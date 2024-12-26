<?php
require_once 'config/db.php';
require_once 'vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

$jwt_secret_key = "token_secret_key"; //ambil token dari login.php dengan metode post

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

function sendResponse($success, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

function validateToken($secret_key) {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? '';

    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $jwt = $matches[1];
        try {
            $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            sendResponse(false, "Token tidak valid: " . $e->getMessage(), null, 401);
        }
    } else {
        sendResponse(false, "Token tidak ditemukan", null, 401);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS') {
    $decoded_token = validateToken($jwt_secret_key);
}

try {
    $dsn = "mysql:host=$host;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    sendResponse(false, "Koneksi database gagal: " . $e->getMessage(), null, 500);
}

// Endpoint API

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['id'])) {
    try {
        $status = $_GET['status'] ?? null;
        $due_date = $_GET['due_date'] ?? null;
        $page = $_GET['page'] ?? 1;
        $limit = $_GET['limit'] ?? 10;
        $sort_by = $_GET['sort_by'] ?? 'id';
        $order = $_GET['order'] ?? 'asc';

        $allowed_sort_columns = ['id', 'due_date', 'status'];
        if (!in_array($sort_by, $allowed_sort_columns)) {
            $sort_by = 'id';
        }

        $offset = ($page - 1) * $limit;

        $query = "SELECT * FROM tasks WHERE 1=1";
        if ($status) $query .= " AND status = :status";
        if ($due_date) $query .= " AND due_date = :due_date";
        $query .= " ORDER BY $sort_by $order LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($query);
        if ($status) $stmt->bindParam(':status', $status);
        if ($due_date) $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        sendResponse(true, "Data tugas berhasil diambil", $tasks);
    } catch (PDOException $e) {
        sendResponse(false, "Gagal mendapatkan tugas: " . $e->getMessage(), null, 500);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['name'], $data['description'], $data['due_date'])) {
        $name = trim($data['name']);
        $description = trim($data['description']);
        $due_date = trim($data['due_date']);

        $date = DateTime::createFromFormat('Y-m-d', $due_date);
        if (!$date || $date->format('Y-m-d') !== $due_date) {
            sendResponse(false, "Tanggal tenggat tidak valid! Format harus YYYY-MM-DD.", null, 400);
        }

        try {
            $query = "INSERT INTO tasks (name, description, due_date) VALUES (:name, :description, :due_date)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':due_date', $due_date);
            $stmt->execute();

            sendResponse(true, "Tugas berhasil ditambahkan!");
        } catch (PDOException $e) {
            sendResponse(false, "Gagal menambahkan tugas: " . $e->getMessage(), null, 500);
        }
    } else {
        sendResponse(false, "Input tidak valid!", null, 400);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $query = "DELETE FROM tasks WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        sendResponse(true, "Tugas berhasil dihapus!");
    } catch (PDOException $e) {
        sendResponse(false, "Gagal menghapus tugas: " . $e->getMessage(), null, 500);
    }
} else {
    sendResponse(false, "Metode request tidak valid!", null, 405);
}