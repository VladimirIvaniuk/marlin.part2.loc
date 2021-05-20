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
function getUserById($id){
    $pdo = new PDO('mysql:host=localhost; dbname=marlin_part_2;', "root", "root");
    $sql = "SELECT * FROM users WHERE id = :id";
    $statement = $pdo->prepare($sql);
    $statement->execute(["id" => $id]);
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result;
}
function get_users()
{
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
    $query = $pdo->query("SELECT LAST_INSERT_ID()");
    $id = $query->fetchColumn();
    return $id;
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

function is_not_logged()
{
    if ($_SESSION["is_auth"] == false) {
        redirect_to("page_login.php");
    }
}

function logout()
{
    $_SESSION["is_auth"] = false;
    redirect_to("page_login.php");
}

function getRole()
{
    $user = get_user_by_email($_SESSION["login"]);
    if ($user["role"] == "admin") {
        $_SESSION['user'] = "admin";
    } else {
        $_SESSION['user'] = "user";
    }
    return $_SESSION['user'];
}

function is_admin()
{
    $user = getRole();
    if ($user == "admin") {
        return true;
    }
    return false;
}

function getUser()
{
    $user = get_user_by_email($_SESSION["login"]);
    return $user;
}

function add_user_admin($data, $file)
{
    $email = $data['email'];
    $password = $data['password'];
    $user = get_user_by_email($email);
    if($file){
        $data=$data+$file;
    }
    if (!$user) {
        $id_user = add_users($email, $password);
        edit($data, $id_user);
        set_flash_massage('success', "Добавлен новый пользователь");
        redirect_to("users.php");
    } else {
        set_flash_massage('danger', "Этот эл. адрес уже занят другим пользователем.");
        redirect_to("create_user.php");
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
function edit($data, $user_id)
{
    $pdo = new PDO('mysql:host=localhost;dbname=marlin_part_2;', "root", "root");
    if(isset($data["avatar"])){
        $data['image'] = upload_avatar($data["avatar"]);
        unset($data['avatar']);
        unset($data['password']);
    }
    $fields = '';
    foreach($data as $key => $value) {
        $fields .= $key . "=:" . $key . ",";
    }
    $fields = rtrim($fields, ',');

    $sql = "UPDATE users SET $fields WHERE id=$user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    return true;
}
/**
 * @param $status
 * Description: установить статус
 * @return null
 */
function set_status()
{

}
function getStatus(){
    $table_name="users";
    $column_name="status";
    $pdo = new PDO('mysql:host=localhost;dbname=marlin_part_2;', "root", "root");
    $sql = 'SHOW COLUMNS FROM '.$table_name.' WHERE field="'.$column_name.'"';
    $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    return $row['Type'];
}
/**
 * @param array $image
 * Description: загрузить аватар
 */
function upload_avatar(array $image)
{
    $from = $image["tmp_name"];
    $filename = $image["name"];
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $filename = uniqid() . "." . $extension;
    $to = "images/" . $filename;
    move_uploaded_file($from, $to);
    return $filename;
}
//function add_social_link($data){
//    $array_social_links=[
//        'vk'=>$data['vk'],
//        'telegram'=>$data['telegram'],
//        'instagram'=>$data['instagram'],
//    ];
//    return $array_social_links;
//}
function dump($arr, $var_dump = false)
{
    echo "<pre style='background: #222;color: silver; font-weight: 800; padding: 20px; border: 10px double blue;'>";
    if ($var_dump) {
        var_dump($arr);
    } else {
        print_r($arr);
    }
    echo "</pre>";
}