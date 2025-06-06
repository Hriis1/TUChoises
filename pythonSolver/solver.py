#!/usr/bin/env python3
"""
assign_students.py

Reads input JSON from sys.argv[1], solves assignment,
writes output JSON to sys.argv[2].

Input JSON format (all arrays are 0-indexed):
{
  "N": 10,
  "N_min": [5, 3, 4, ..., up to length N],
  "N_max": [15, 12, 8, ..., length N],

  "S": 100,
  "grades": [5, 6, 4, 6, 2, ...],          # length = S, each in {2..6}
  "desires": [                            # array of length S, each inner list length = N
      [3,5,2,1,4,4,3,2,5,1],
      [4,4,3,2,5,1,2,3,4,2],
      ...
  ]
}

Output JSON format:
{
  "assignments": { "0": 3, "1": 5, "2": 0, ... },
  "objective_value": 12345
}
where “k”: i means student k is assigned to discipline i.
"""

import json
import sys
import pulp

def main():
    if len(sys.argv) != 3:
        print("Usage: assign_students.py input.json output.json", file=sys.stderr)
        sys.exit(1)

    with open(sys.argv[1], "r") as f:
        data = json.load(f)

    N = data["N"]
    N_min = data["N_min"]
    N_max = data["N_max"]

    S = data["S"]
    grades = data["grades"]
    desires = data["desires"]

    # 1) Build scores:
    #    We choose weights: alpha = 100 (grade), beta = 1 (desire).
    score = [[100 * grades[k] + desires[k][i] for i in range(N)] for k in range(S)]

    # 2) Define the problem
    prob = pulp.LpProblem("StudentAssignment", pulp.LpMaximize)

    # 3) Variables x[k,i] ∈ {0,1}
    x = {}
    for k in range(S):
        for i in range(N):
            x[(k, i)] = pulp.LpVariable(f"x_{k}_{i}", cat="Binary")

    # 4) One discipline per student
    for k in range(S):
        prob += (pulp.lpSum(x[(k, i)] for i in range(N)) == 1, f"OneDiscipline_Student_{k}")

    # 5) Capacity constraints per discipline
    for i in range(N):
        prob += (pulp.lpSum(x[(k, i)] for k in range(S)) >= N_min[i], f"MinCap_Disc_{i}")
        prob += (pulp.lpSum(x[(k, i)] for k in range(S)) <= N_max[i], f"MaxCap_Disc_{i}")

    # 6) Objective: maximize total score
    prob += pulp.lpSum(score[k][i] * x[(k, i)] for k in range(S) for i in range(N)), "TotalScore"

    # 7) Solve (silence solver output)
    prob.solve(pulp.PULP_CBC_CMD(msg=False, timeLimit=10))

    # 8) Extract assignments
    assignments = {}
    for k in range(S):
        for i in range(N):
            if x[(k, i)].value() > 0.5:
                assignments[str(k)] = i
                break

    out = {
        "assignments": assignments,
        "objective_value": pulp.value(prob.objective),
    }

    with open(sys.argv[2], "w") as f:
        json.dump(out, f)

if __name__ == "__main__":
    main()
