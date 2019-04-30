<?php

/**
 * index
 * @package default
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including files
require("../includes/classes/Configuration.inc.php");
require(APP_PATH . "includes/classes/clsConfiguration.php");
require(APP_PATH . "includes/classes/clsDatabaseManager.php");
require(APP_PATH . "includes/classes/db.php");
require(APP_PATH . "includes/classes/clsLogin.php");
$strMsg = NULL;
$objLogin = new clsLogin();
if (isset($_REQUEST['newrole']) && !empty($_REQUEST['newrole'])) {
    $newrole = $_REQUEST['newrole'];
    $r = $objLogin->ChangeRole($newrole);
    $_REQUEST['login'] = $r[0];
    $_REQUEST['pass'] = $r[1];
    $_REQUEST['t'] = 'true';
}

if (isset($_REQUEST['login']) && !empty($_REQUEST['login'])) {
    //Getting pass
    if (isset($_REQUEST['pass']) && !empty($_REQUEST['pass'])) {
        //$objLogin = new clsLogin();
        // t variable for LMIS support admin
        if (isset($_REQUEST['t']) && !empty($_REQUEST['t'])) {
            $objLogin->m_strPass = mysql_real_escape_string($_REQUEST['pass']);
        } else {
            $objLogin->m_strPass = md5(mysql_real_escape_string($_REQUEST['pass']));
        }
        $objLogin->m_login = mysql_real_escape_string($_REQUEST['login']);
        $user = $objLogin->Login();

        if ($user['numOfRec'] > 0) {
            $ip = $_SERVER['REMOTE_ADDR'];
            //   $_SERVER['HTTP_X_FORWARDED_FOR']; 
            $qry = "INSERT INTO tbl_user_login_log
				SET
					user_id = " . $user['UserID'] . ",
					ip_address = '" . $ip . "',					
					login_time = NOW() ";
            mysql_query($qry);

            $user_role = $user['sysusr_type'];
            $_SESSION['user_login_log_id'] = mysql_insert_id();
            //UserID
            $_SESSION['user_id'] = $user['UserID'];
            //sysusr_type
            $_SESSION['user_role'] = $user['sysusr_type'];
            //sysusr_name
            $_SESSION['user_name'] = $user['sysusr_name'];
            //wh_id
            $_SESSION['user_warehouse'] = $user['wh_id'];
            //stkid
            $_SESSION['user_stakeholder'] = $user['stkid'];
            //stkid
            $_SESSION['user_stakeholder_office'] = $user['stkofficeid'];
            //user_level
            $_SESSION['user_level'] = $user['lvl'];
            // User Province
            $_SESSION['user_province'] = $user['prov_id'];
            //User district
            $_SESSION['user_district'] = $user['dist_id'];
            //IM Access
            if ($user['UserID'] == 8904 || $user['UserID'] == 9780) {
                $_SESSION['is_allowed_im'] = 0;
            } else {
                $_SESSION['is_allowed_im'] = $user['is_allowed_im'];
            }
            $_SESSION['im_start_month'] = $user['im_start_month'];
            //stk_type_id
            $_SESSION['user_stakeholder_type'] = $user['stk_type_id'];
            //user_province
            $_SESSION['user_province1'] = $user['user_province'];
            //user_stk
            $_SESSION['user_stakeholder1'] = $user['user_stk'];
            //landing_page
            $_SESSION['landing_page'] = $user['landing_page'];

            // Special cases for global user
            if (isset($_REQUEST['newrole']) && !empty($_REQUEST['newrole'])) {
                switch ($user['role_level']) {
                    case 1:
                        $_SESSION['user_province1'] = 10;
                        $_SESSION['user_stakeholder1'] = 1;
                        $_SESSION['user_district'] = '';
                        $_SESSION['user_warehouse'] = 123;
                        break;
                    case 2:
                        $_SESSION['user_province1'] = 1;
                        $_SESSION['user_stakeholder1'] = 1;
                        $_SESSION['user_district'] = '';
                        $_SESSION['user_warehouse'] = 123;
                        break;
                    case 3:
                        $_SESSION['user_province1'] = 1;
                        $_SESSION['user_stakeholder1'] = 1;
                        $_SESSION['user_district'] = 102;
                        $_SESSION['user_warehouse'] = 170;
                        break;
                    default:
                        $_SESSION['user_province1'] = 10;
                        $_SESSION['user_stakeholder1'] = 1;
                        $_SESSION['user_district'] = '';
                        $_SESSION['user_warehouse'] = 123;
                        break;
                }
            }

            if ($user_role == 1) {
                $_SESSION['menu'] = PUBLIC_PATH . 'html/menu_superadmin.php';
            } else {
                $_SESSION['menu'] = PUBLIC_PATH . 'html/top.php';
            }

            $url = SITE_URL . $user['landing_page'];
            echo "<script>window.location='$url'</script>";
            exit;
        } else {
            $_SESSION['err'] = 'Username/Password is incorrect.';
            echo "<script>window.location='$url'</script>";
            exit;
        }
    } else {
        $_SESSION['err'] = 'Please enter Login Details';
        $url = SITE_URL . 'index.php';
        echo "<script>window.location='$url'</script>";
    }
} else {
    //Setting error message
    $_SESSION['err'] = 'Please enter username';
    $url = SITE_URL . 'index.php';
    echo "<script>window.location='$url'</script>";
}