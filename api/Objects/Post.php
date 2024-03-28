<?php

class User {
    // Подключение к БД таблице "posts"
    private $conn;
    private $table_name = "posts";

    // Свойства
    public $id;
    public $title;
    public $text;
    public $img;
    public $price;
    public $author;

    // Конструктор класса Post
    public function __construct($db) {
        $this->conn = $db;
    }

    // Метод для создания нового объявления
    function create() {
        // Запрос для добавления нового объявления в БД
        $query = "insert into " . $this->table_name . "
                set
                    title  = :title,
                    text   = :text,
                    img    = :img,
                    price  = :price,
                    author = :autor";

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);

        // Инъекция
        $this->title = htmlspecialchars(strip_tags($this->text));
        $this->text = htmlspecialchars(strip_tags($this->text));
        $this->img = htmlspecialchars(strip_tags($this->img));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->author = htmlspecialchars(strip_tags($this->author));

        // Привязываем значения
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":text", $this->text);
        $stmt->bindParam(":img", $this->img);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":author", $this->author);

        // Если выполнение успешно, то информация об объявлениии будет сохранена в БД
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Метод для получения объявления
    public function get() {
        // Запрос для получения объявления из БД
        $query = "select * from " . $this->table_name . " where id = :id";

        // Подготовка запроса
        $stmt = $this->conn->prepare($query);

        // Инъекция
        $this->title = htmlspecialchars(strip_tags($this->text));
        $this->text = htmlspecialchars(strip_tags($this->text));
        $this->img = htmlspecialchars(strip_tags($this->img));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->author = htmlspecialchars(strip_tags($this->author));

        // Привязываем значения
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":text", $this->text);
        $stmt->bindParam(":img", $this->img);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":author", $this->author);

        // Если выполнение успешно, то информация об объявлениии будет сохранена в БД
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Обновить запись объявления
    public function update() {
        $query = "update " . $this->table_name . "
                set
                    title  = :title,
                    text   = :text,
                    img    = :img,
                    price  = :price,
                    author = :autor
                where id = :id";
    
        // Подготовка запроса
        $stmt = $this->conn->prepare($query);
    
        // Инъекция (очистка)
        $this->title = htmlspecialchars(strip_tags($this->text));
        $this->text = htmlspecialchars(strip_tags($this->text));
        $this->img = htmlspecialchars(strip_tags($this->img));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->author = htmlspecialchars(strip_tags($this->author));
    
        // Привязываем значения с HTML формы
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":text", $this->text);
        $stmt->bindParam(":img", $this->img);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":author", $this->author);
    
        // Уникальный идентификатор записи для редактирования
        $stmt->bindParam(":id", $this->id);
    
        // Если выполнение успешно, то информация об объявлении будет сохранена в БД
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

        // Обновить запись объявления
        public function delete() {
            $query = "delete from " . $this->table_name . " where id = :id";
        
            // Подготовка запроса
            $stmt = $this->conn->prepare($query);
        
            // Инъекция (очистка)
            $this->title = htmlspecialchars(strip_tags($this->text));
            $this->text = htmlspecialchars(strip_tags($this->text));
            $this->img = htmlspecialchars(strip_tags($this->img));
            $this->price = htmlspecialchars(strip_tags($this->price));
            $this->author = htmlspecialchars(strip_tags($this->author));
        
            // Привязываем значения с HTML формы
            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":text", $this->text);
            $stmt->bindParam(":img", $this->img);
            $stmt->bindParam(":price", $this->price);
            $stmt->bindParam(":author", $this->author);
        
            // Уникальный идентификатор записи для редактирования
            $stmt->bindParam(":id", $this->id);
        
            // Если выполнение успешно, то информация об объявлении будет сохранена в БД
            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
}
