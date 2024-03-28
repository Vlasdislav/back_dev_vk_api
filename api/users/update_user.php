<?php

header("Access-Control-Allow-Origin: http://budru.com.ru/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Требуется для кодирования JWT
require "../libs/vendor/autoload.php";
include_once "../Config/Core.php";
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key; 

// Файлы, необходимые для подключения к базе данных
include_once "../Config/Database.php";
include_once "../Objects/User.php";

// Получаем соединение с БД
$database = new Database();
$db = $database->getConnection();

// Создание объекта "User"
$user = new User($db);

// Получаем данные
$data = json_decode(file_get_contents("php://input"));

// Получаем jwt
$jwt = isset($data->jwt) ? $data->jwt : "";

// Если JWT не пуст
if ($jwt) {
    // Если декодирование выполнено успешно, показать данные пользователя
    try {
        // Декодирование jwt
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));

        // Нам нужно установить отправленные данные (через форму HTML) в свойствах объекта пользователя
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->email = $data->email;
        $user->password = $data->password;
        $user->id = $decoded->data->id;

        // Создание пользователя
        if ($user->update()) {
            // Нам нужно заново сгенерировать JWT, потому что данные пользователя могут отличаться
            $token = [
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "data" => [
                    "id" => $user->id,
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "email" => $user->email
                ]
            ];
            
            $jwt = JWT::encode($token, $key, 'HS256');
            
            http_response_code(200);
            $res = [
                "status" => true,
                "jwr" => $jwt,
                "message" => "Данные о пользователе обновлены"
            ];
            echo json_encode($res);
        } else {
            http_response_code(401);
            $res = [
                "status" => false,
                "message" => "Невозможно обновить данные пользователя"
            ];
            echo json_encode($res);
        }
    }
    // Если декодирование не удалось, это означает, что JWT является недействительным
    catch (Exception $e) {
        http_response_code(401);
        $res = [
            "status" => false,
            "error" => $e->getMessage(),
            "message" => "Доступ закрыт"
        ];
        echo json_encode($res);
    }
} else {
    http_response_code(401);
    $res = [
        "status" => false,
        "message" => "Доступ закрыт"
    ];
    echo json_encode($res);
}
