<?php
session_start();
require_once "functions.php";
edit($_POST, $_GET['id']);
set_flash_massage("success", "Пользавотель обнавлен");
redirect_to('page_profile.php?id='.$_GET['id']);