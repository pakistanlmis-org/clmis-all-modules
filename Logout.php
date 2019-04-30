<?php
session_start();
session_destroy();

if($_SERVER['SERVER_ADDR'] == '::1' || $_SERVER['SERVER_ADDR'] == 'localhost'  || $_SERVER['SERVER_ADDR'] == '127.0.0.1') {
    header('Location:index.php');
} else {
    header('Location:http://lmis.gov.pk/index.php');
}
exit;
?>