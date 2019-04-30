<?php
/**
 * changePassUser
 * @package default
 *
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 *
 * @version    2.2
 *
 */
//Including files
include("../includes/classes/AllClasses.php");
require("../includes/classes/clsLogin.php");

$user_id = $_GET['id'];

mysql_query("DELETE FROM email_verification WHERE user_id = $user_id");

$_SESSION['email'] = $_GET['e'];
header('Location: '.$_SESSION['backURL']);
?> 