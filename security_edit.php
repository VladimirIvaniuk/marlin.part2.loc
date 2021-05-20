<?php
session_start();
require_once "functions.php";
is_not_logged();
$data=[
    "email"=> $_POST['email'],
    "password"=>password_hash($_POST['password'], PASSWORD_DEFAULT),
    ];
if(edit($data, $_GET['id'])){
    $_SESSION["login"]=$_POST['email'];
    set_flash_massage('success', 'профиль успешно обнавлен');
    redirect_to('page_profile.php?id='.$_GET['id']);
}
