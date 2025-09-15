<?php
function moodleAuthRequest(string $username, string $password, string $service = 'moodle_mobile_app'): array
{
    $tokenUrl = 'http://localhost:9000/login/token.php';
    $params = http_build_query([
        'username' => $username,
        'password' => $password,
        'service' => $service
    ]);

    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    /* curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); */ // enable in production for HTTPS
    $response = curl_exec($ch);

    if ($response === false) {
        $err = 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        return [0, $err];
    }
    curl_close($ch);

    $data = json_decode($response, true);

    if (isset($data['token'])) {
        return [1, '']; // success, no error message
    }

    $err = $data['error'] ?? 'Unknown error';
    return [0, $err];
}


// Example usage
/* list($ok, $msg) = moodleAuthRequest("jijo1", "qwewqe!");

if ($ok) {
    echo "✅ Login success\n";
} else {
    echo "❌ Login failed: $msg\n";
} */