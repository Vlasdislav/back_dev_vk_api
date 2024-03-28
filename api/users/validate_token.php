<?php

header("Access-Control-Allow-Origin: http://budru.com.ru/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// Требуется для декодирования JWT
require "../libs/vendor/autoload.php";
include_once "../Config/Core.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key; 
 
// Получаем значение веб-токена JSON
$data = json_decode(file_get_contents("php://input"));

// Получаем JWT
$jwt = isset($data->jwt) ? $data->jwt : "";

// Если JWT не пуст
if ($jwt) {
    // Если декодирование выполнено успешно, показать данные пользователя
    try {
        // Декодирование jwt
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        http_response_code(200);
        $res = [
            "status" => true,
            "data" => $decoded->data,
            "message" => "Доступ разрешен"
        ];
        echo json_encode($res);
    }
    // Если декодирование не удалось, это означает, что JWT является недействительным
    catch (Exception $e) {
        http_response_code(401);
        $res = [
            "status" => false,
            "error" => $e->getMessage(),
            "message" => "Доступ запрещен"
        ];
        echo json_encode($res);
    }
} else {
    http_response_code(401);
    $res = [
        "status" => false,
        "message" => "Доступ запрещен"
    ];
    echo json_encode($res);
}
