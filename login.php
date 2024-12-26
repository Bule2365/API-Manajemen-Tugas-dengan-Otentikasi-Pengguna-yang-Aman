<?php
require_once 'config/db.php';
require_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if (!isset($pdo) || !isset($secret_key)) {
    echo json_encode([
        "success" => false,
        "message" => "Server configuration error."
    ]);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            echo json_encode([
                "success" => false,
                "message" => "Username dan password harus diisi."
            ]);
            exit;
        }

        $username = $data['username'];
        $password = $data['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $payload = [
                "iss" => "your_issuer",
                "iat" => time(),
                "exp" => time() + (86400 * 7), // Token berlaku 7 hari
                "data" => [
                    "id" => $user['id'],
                    "username" => $user['username']
                ]
            ];

            // Encode JWT
            $jwt = JWT::encode($payload, $secret_key, 'HS256');

            echo json_encode([
                "success" => true,
                "token" => $jwt
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Username atau password salah."
            ]);
        }
    } else {
        // Jika metode HTTP bukan POST
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Metode request tidak valid."
        ]);
    }
}