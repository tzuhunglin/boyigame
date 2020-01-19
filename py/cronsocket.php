<?php
date_default_timezone_set("Asia/Taipei");
error_reporting(E_ALL);
ini_set('display_errors', 1);
$connection = @fsockopen('localhost', '3000');

if (!is_resource($connection))
{
    exec('root node /var/www/html/boyigame/socket.js');
}
?>
