#!/usr/bin/env python3

import json, sys
import pulp

def main():
    if len(sys.argv) != 3:
        print("Usage: solver.py input.json output.json", file=sys.stderr)
        sys.exit(1)

    # 1) Load input JSON
    with open(sys.argv[1], "r") as f:
        data = json.load(f)

    # 2) Extract discipline IDs and their bounds
    disciplines_data = data["disciplines"]
    # Keep the order stable to build index ↔ ID mappings
    discipline_ids = [d["id"] for d in disciplines_data]
    N = len(discipline_ids)

    # Build maps: disc_id → index (0..N-1), and arrays of min/max
    disc_to_idx = {disc_id: idx for idx, disc_id in enumerate(discipline_ids)}
    N_min = [None] * N
    N_max = [None] * N
    for d in disciplines_data:
        i = disc_to_idx[d["id"]]
        N_min[i] = d["min"]
        N_max[i] = d["max"]

    # 3) Extract student IDs, grades, and desire‐maps
    students_data = data["students"]
    student_ids = [s["id"] for s in students_data]
    S = len(student_ids)
    stud_to_idx = {stu_id: k for k, stu_id in enumerate(student_ids)}

    grades = [None] * S
    # desires[k][i] will hold the rating of student k for discipline i
    desires = [[0]*N for _ in range(S)]

    for s in students_data:
        k = stud_to_idx[s["id"]]
        grades[k] = float(s["grade"])
        # Expect a dict mapping each discipline_id → integer 1..5
        for disc_id, rating in s["desires"].items():
            disc_id = int(disc_id) 
            i = disc_to_idx[disc_id]
            desires[k][i] = int(rating)

    # 4) Build a “score” matrix: score[k][i] = α·grade_k + β·desires[k][i]
    #    You can tweak α and β as needed. Here we use α = 1000, β = 1.
    # Grade-first with desire as a small modulator + tiny tiebreak
    W1 = 10**6       # primary weight
    eps = 0.02       # desire modulates grade by up to ~10% if desire ∈ [0..5]
    W2 = 1           # tiny secondary tiebreak on raw desire
    score = [[W1 * grades[k] * (1 + eps * desires[k][i]) + W2 * desires[k][i] for i in range(N)] for k in range(S)]



    # 5) Create PuLP problem
    prob = pulp.LpProblem("StudentToDiscipline", pulp.LpMaximize)

    # 6) Create binary variables x[k,i]
    x = {}
    for k in range(S):
        for i in range(N):
            x[(k, i)] = pulp.LpVariable(f"x_{k}_{i}", cat="Binary")

    # 7) Each student must be in exactly one discipline
    for k in range(S):
        prob += (pulp.lpSum(x[(k, i)] for i in range(N)) == 1,
                 f"OneDisciplinePerStudent_{k}")

    # 8) Capacity constraints for each discipline
    for i in range(N):
        prob += (pulp.lpSum(x[(k, i)] for k in range(S)) >= N_min[i],
                 f"MinCapacity_{discipline_ids[i]}")
        prob += (pulp.lpSum(x[(k, i)] for k in range(S)) <= N_max[i],
                 f"MaxCapacity_{discipline_ids[i]}")

    # 9) Objective: maximize total score
    obj = pulp.lpSum(score[k][i] * x[(k, i)] for k in range(S) for i in range(N))
    prob += obj, "TotalScore"

    # 10) Solve (silent)
    prob.solve(pulp.PULP_CBC_CMD(msg=False))

    # 11) Extract assignments, mapping student IDs → discipline IDs
    assignments = {}
    for k, stu_id in enumerate(student_ids):
        for i, disc_id in enumerate(discipline_ids):
            if x[(k, i)].value() > 0.5:
                assignments[stu_id] = disc_id
                break

    # 12) Write output JSON
    output = {
        "assignments": assignments,
        "objective_value": pulp.value(prob.objective)
    }
    with open(sys.argv[2], "w") as f:
        json.dump(output, f, indent=2)

if __name__ == "__main__":
    main()
