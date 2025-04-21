<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //if req is POST
    if (isset($_POST["action"]) && $_POST["action"] == "loginUser") {

        // 1. Grab form input (sanitize in real code!)
        $username = $_POST['username'];
        $password = $_POST['pass'];

        // 2. Build the token request
        $tokenUrl = 'https://fpmi.bg/moodle/login/token.php';
        $params = http_build_query([
            'username' => $username,
            'password' => $password,
            'service' => 'moodle_mobile_app'
        ]);

        // 3. Send the POST request
        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // **always** verify SSL!
        $response = curl_exec($ch);
        if ($response === false) {
            die('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        // 4. Decode Moodle’s response
        $data = json_decode($response, true);
        if (isset($data['token'])) {
            // Success!
            $token = $data['token'];
            // You can now call other Moodle WS endpoints with this token,
            // or simply treat this as proof the user exists and has correct credentials.
            $_SESSION['moodle_token'] = $token;
            $_SESSION['moodle_user'] = $username;

            echo "Moodle log in success :)";
            
        } else {
            // Failed — Moodle sends {"error":"...","errorcode":"..."}
            $err = htmlspecialchars($data['error'] ?? 'Unknown error');
            echo "<p>Invalid credentials: $err</p>";
        }

    } else {
        echo "Unrecognized access :(";
    }
} else {
    echo "Only POST allowed :(";
}