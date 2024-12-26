<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function jwtMiddleware() {
    $headers = getallheaders();

    if (isset($headers['Authorization'])) {
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        
        try {
            $secretKey = 'my_very_secret_key_12345';
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));  
            if (isset($decoded->id)) {
                return $decoded;
            } else {
                throw new Exception('ID not found in token');
            }
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Unauthorized: " . $e->getMessage()]);
            exit();
        }
    } else {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Token not provided"]);
        exit();
    }
}