<?php

header("Access-Control-Allow-Origin: http://budru.com.ru/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Подключение к БД
include_once "../Config/Database.php";
include_once "../Objects/User.php";

// Получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// Создание объекта "User"
$user = new User($db);
 
// Получаем данные
$data = json_decode(file_get_contents("php://input"));
 
// Устанавливаем значения
$user->firstname = $data->firstname;
$user->lastname = $data->lastname;
$user->email = $data->email;
$user->password = $data->password;

// Поверка на существование e-mail в БД
// $email_exists = $user->emailExists();
 
// Создание пользователя
if (
    !empty($user->firstname) &&
    !empty($user->lastname) &&
    !empty($user->email) &&
    // $email_exists == 0 &&
    !empty($user->password) &&
    $user->create()
) {
    http_response_code(200);
    $res = [
        "status" => true,
        "user_id" => $db->lastInsertId(),
        "message" => "Пользователь был создан"
    ];
    echo json_encode($res);
} else {
    http_response_code(400);
    $res = [
        "status" => false,
        "message" => "Невозможно создать пользователя"
    ];
    echo json_encode($res);
}
