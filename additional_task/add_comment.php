<?php
include_once "functions.php";
$obj=new DB();
$comments=$obj->add($_POST);
redirect_to('/additional_task');
