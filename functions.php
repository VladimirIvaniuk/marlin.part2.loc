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

function get_users(){
    $pdo = new PDO('mysql:host=localhost; dbname=marlin_part_2;', "root", "root");
    $sql = "SELECT * FROM users";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
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
    $pdo = new PDO('mysql:host=localhost;dbname=marlin_part_2;', "root", "root");
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
        redirect_to("page_register.php");
    }
}

/**
 * @param $data
 */
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
            redirect_to("users.php");
        }
    }
    set_flash_massage('danger', "Неверный логин или пароль");
    $_SESSION["is_auth"] = false;
    redirect_to("page_login.php");
}
function is_not_logged(){
    if($_SESSION["is_auth"]==false){
        redirect_to("page_login.php");
    }
}
function logout(){
    $_SESSION["is_auth"]=false;
    redirect_to("page_login.php");
}
function getRole(){
    $user = get_user_by_email($_SESSION["login"]);
    if($user["role"]=="admin"){
        $_SESSION['user']="admin";
    }else{
        $_SESSION['user']="user";
    }
    return $_SESSION['user'];
}
function is_admin(){
    $user = getRole();
    if($user=="admin"){
        return true;
    }
    return false;
}
function getUser(){
    $user = get_user_by_email($_SESSION["login"]);
    return $user;
}

function add_user_admin(){
    $user=add_users();
    if($user){
        edit();
    }

}

/**
 * @param $username string
 * @param $job_title string
 * @param $phone string
 * @param $address string
 * @param $user_id int
 * Description: редактировать профиль
 * Return value: null
 */
function edit($username, $job_title, $phone, $address, $user_id){

}

/**
 * @param $status
 * Description: установить статус
 * @return null
 */
function set_status($status){
    return null;
}

/**
 * @param array $image
 * Description: загрузить аватар
 */
function upload_avatar(array $image){

}



function dump($arr, $var_dump = false)
{
    echo "<pre style='background: #222;color: silver; font-weight: 800; padding: 20px; border: 10px double blue;'>";
    if ($var_dump){
        var_dump($arr);
    }
    else{
        print_r($arr);
    }
    echo "</pre>";
}