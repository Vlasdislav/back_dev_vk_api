<?php

class User {
    // Подключение к БД таблице "users"
    private $conn;
    private $table_name = "users";

    // Свойства
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $password;

    // Конструктор класса User
    public function __construct($db) {
        $this->conn = $db;
    }

    // Метод для создания нового пользователя
    function create() {
        // Запрос для добавления нового пользователя в БД
        $query = "insert into " . $this->table_name . "
                set
                    firstname = :firstname,
                    lastname  = :lastname,
                    email     = :email,
                    password  = :password";

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);

        // Инъекция
        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->lastname = htmlspecialchars(strip_tags($this->lastname));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Привязываем значения
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":email", $this->email);

        // Хешируем пароль перед сохранением в базу данных
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);

        // Если выполнение успешно, то информация о пользователе будет сохранена в базе данных
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Проверка, существует ли электронная почта в нашей базе данных
    function emailExists() {
        // Запрос, чтобы проверить, существует ли электронная почта
        $query = "select id, firstname, lastname, password
                from " . $this->table_name . "
                where email = ?
                LIMIT 0,1";
    
        // Подготовка запроса
        $stmt = $this->conn->prepare($query);
    
        // Инъекция
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // Привязываем значение e-mail
        $stmt->bindParam(1, $this->email);
    
        // Выполняем запрос
        $stmt->execute();
    
        // Получаем количество строк
        $num = $stmt->rowCount();
    
        // Если почта существует, присвоим значения свойствам объекта
        if ($num > 0) {
            // Получаем значения
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Присвоим значения свойствам объекта
            $this->id = $row["id"];
            $this->firstname = $row["firstname"];
            $this->lastname = $row["lastname"];
            $this->password = $row["password"];

            return true;
        }

        return false;
    }
    
    // Обновить запись пользователя
    public function update() {
        // Если в HTML-форме был введен пароль (необходимо обновить пароль)
        $password_set =! empty($this->password) ? ", password = :password" : "";
    
        // Если не введен пароль - не обновлять пароль
        $query = "update " . $this->table_name . "
                set
                    firstname = :firstname,
                    lastname  = :lastname,
                    email     = :email
                    {$password_set}
                where id = :id";
    
        // Подготовка запроса
        $stmt = $this->conn->prepare($query);
    
        // Инъекция (очистка)
        $this->firstname=htmlspecialchars(strip_tags($this->firstname));
        $this->lastname=htmlspecialchars(strip_tags($this->lastname));
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // Привязываем значения с HTML формы
        $stmt->bindParam(":firstname", $this->firstname);
        $stmt->bindParam(":lastname", $this->lastname);
        $stmt->bindParam(":email", $this->email);
    
        // Метод password_hash () для защиты пароля пользователя в базе данных
        if(!empty($this->password)) {
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(":password", $password_hash);
        }
    
        // Уникальный идентификатор записи для редактирования
        $stmt->bindParam(":id", $this->id);
    
        // Если выполнение успешно, то информация о пользователе будет сохранена в базе данных
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
