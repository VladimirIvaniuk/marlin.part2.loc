<?php
session_start();
/**
 * @param string $email
 * description: поиск пользователя по эл. адресу
 * @return mixed
 */
function get_user_by_email($email)
{
    $pdo = new PDO('mysql:host=localhost; dbname=marlin_part_2;', "root", "root");
    $sql = "SELECT * FROM users WHERE email = :email";
    $statement = $pdo->prepare($sql);
    $statement->execute(["email" => $email]);
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}

/**
 * @param string $email
 * @description: добавить пользователя в базу
 * @param string $password
 * @return int
 */
function add_users($email, $password)
{
    $pdo = new PDO('mysql:host=localhost; dbname=marlin_part_2;', "root", "root");
    $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
    $statement = $pdo->prepare($sql);
    $result = $statement->execute(["email" => $email, "password" => password_hash($password, PASSWORD_DEFAULT)]);
    return $result;
}

/**
 * @param string $name (key)
 * @param string $message (значние, текст сообщения)
 * @description: подготовить флеш сообщение
 * @return value: null
 */
function set_flash_massage($name, $message)
{
    $_SESSION[$name] = $message;
    return null;
}

/**
 * @param string $name (ключ)
 * @description: вывести флеш сообщение
 * // * @return value: null
 */
function display_flash_message($name)
{
    if (isset($_SESSION[$name])) {
        echo "<div class=\"alert alert-{$name} text-dark\" role=\"alert\">{$_SESSION[$name]}</div>";
    }
    unset($_SESSION[$name]);
}

/**
 * @param string $path (путь куда переадресовывать)
 */
function redirect_to($path)
{
    header('Location: ' . $path);
    exit;
}

function register($data)
{
    $email = $data['email'];
    $password = $data['password'];
    $user = get_user_by_email($email);
    if (!$user) {
        if (add_users($email, $password)) {
            set_flash_massage("success", "Успешная регистрация");
            redirect_to("page_login.php");
        }
    } else {
        set_flash_massage('danger', "Этот эл. адрес уже занят другим пользователем.");
        redirect_to("/page_register.php");
    }
}

function login($data)
{
    $email = $data['email'];
    $password = $data['password'];
    if (!empty($email)) {
        $user = get_user_by_email($email);
        $result = password_verify($password, $user['password']);
        if ($result) {
            $_SESSION["is_auth"] = true; //Делаем пользователя авторизованным
            $_SESSION["login"] = $email; //Записываем в сессию логин пользователя
            redirect_to("page_profile.html");
        } else {
            set_flash_massage('danger', "Неверный пароль");
            redirect_to("page_login.php");
        }
        $_SESSION["is_auth"] = false;
    }
    $_SESSION["is_auth"] = false;
    set_flash_massage('danger', "Неверный логин");
    redirect_to("page_login.php");
}