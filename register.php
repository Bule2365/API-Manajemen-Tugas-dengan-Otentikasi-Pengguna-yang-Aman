<?php
require_once 'config/db.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['username'], $data['password'])) {
        $username = trim($data['username']);
        $password = trim($data['password']);

        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Username dan password tidak boleh kosong."]);
            exit;
        }

        try {
            $checkQuery = "SELECT id FROM users WHERE username = :username";
            $checkStmt = $pdo->prepare($checkQuery);
            $checkStmt->bindParam(':username', $username);
            $checkStmt->execute();

            if ($checkStmt->rowCount() > 0) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "Username sudah digunakan."]);
                exit;
            }
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)";
            $stmt = $pdo->prepare($insertQuery);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password_hash', $passwordHash);
            $stmt->execute();

            echo json_encode(["success" => true, "message" => "User berhasil didaftarkan."]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Gagal mendaftarkan user: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Input tidak valid."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Metode request tidak valid."]);
}