<? session_start();
require_once "functions.php";
is_not_logged();
edit(['status'=>$_POST['status']], $_GET['id']);
redirect_to('page_profile.php?id='.$_GET['id']);