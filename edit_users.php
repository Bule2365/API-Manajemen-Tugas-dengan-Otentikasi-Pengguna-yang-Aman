<?php
require_once 'config/db.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $id = intval($_GET['id']);
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['username']) || isset($data['password'])) {
        $username = isset($data['username']) ? trim($data['username']) : null;
        $password = isset($data['password']) ? trim($data['password']) : null;

        try {
            $query = "UPDATE users SET ";
            $params = [];

            if ($username) {
                if (empty($username)) {
                    http_response_code(400);
                    echo json_encode(["success" => false, "message" => "Username tidak boleh kosong."]);
                    exit;
                }

                $checkQuery = "SELECT id FROM users WHERE username = :username AND id != :id";
                $checkStmt = $pdo->prepare($checkQuery);
                $checkStmt->bindParam(':username', $username);
                $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
                $checkStmt->execute();

                if ($checkStmt->rowCount() > 0) {
                    http_response_code(400);
                    echo json_encode(["success" => false, "message" => "Username sudah digunakan oleh user lain."]);
                    exit;
                }

                $query .= "username = :username, ";
                $params[':username'] = $username;
            }

            if ($password) {
                if (empty($password)) {
                    http_response_code(400);
                    echo json_encode(["success" => false, "message" => "Password tidak boleh kosong."]);
                    exit;
                }

                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $query .= "password_hash = :password_hash, ";
                $params[':password_hash'] = $passwordHash;
            }
            $query = rtrim($query, ", ");
            $query .= " WHERE id = :id";
            $params[':id'] = $id;

            $stmt = $pdo->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            echo json_encode(["success" => true, "message" => "User berhasil diperbarui."]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Gagal memperbarui user: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Input tidak valid."]);
    }
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Metode request tidak valid."]);
}