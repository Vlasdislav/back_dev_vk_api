<?php

// Используем для подключения к базе данных MySQL
class Database {
    // Учётные данные базы данных
    private $host = "h807240245.mysql";
    private $db_name = "h807240245_back_dev_vk_api";
    private $username = "h807240245_mysql";
    private $password = "MpSPkb:6";
    public $conn;

    // Получаем соединение с базой данных
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . "; dbname=" . $this->db_name, $this->username, $this->password);
        } catch (PDOException $exception) {
            echo "Ошибка соединения с БД: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
