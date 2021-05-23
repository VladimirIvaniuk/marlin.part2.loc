<? session_start();
require_once "functions.php";
is_not_logged();
deleteUser($_GET['id']);
