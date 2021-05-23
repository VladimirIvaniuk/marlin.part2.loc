<? session_start();
require_once "functions.php";
is_not_logged();

if(edit($_FILES, $_GET['id'])){
    set_flash_massage('success', 'аватар успешно обнавлен');
    redirect_to('page_profile.php?id='.$_GET['id']);
}
