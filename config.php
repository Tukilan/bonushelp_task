<?

/* Файл для хранения глобальных переменных и подключения необходимых для работы классов*/

/* Для глобалок */
$server['root'] = $_SERVER['DOCUMENT_ROOT'].'/';
$server['upload_dir'] = $server['root'].'files/';






include_once($server['root'].'database.php');



?>