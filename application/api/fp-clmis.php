<?php

set_time_limit(0);
/**
 * Family Planning Dashboard cLMIS Web Services
 * @package api
 *
 * @author     Ahmad Saib
 * @email <ahmad.saib@outlook.com>
 *
 * @version    2.2
 *
 */
//Including required files
include("../../application/includes/classes/Configuration.inc.php");
//Including db file
include(APP_PATH . "includes/classes/db.php");
require_once("../../application/includes/classes/clsFp.php");

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $auth = mysql_real_escape_string($_GET['token']);

    if ($auth == 'c7c0f284ab537c3961836b6fc2f8960e') {

        $shared_key = 'dhisapi';

        $key = md5($shared_key);



        $province_id = '1';


        if ($key == $auth) {
            if (isset($_GET['indicator']) && !empty($_GET['indicator'])) {
                $indicator = $_GET['indicator'];
            } else {
                $indicator = '';
            }
            if (isset($_GET['commodities']) && !empty($_GET['commodities'])) {
                $commodities = $_GET['commodities'];
            } else {
                $commodities = '';
            }
            if (isset($_GET['fromyear']) && !empty($_GET['fromyear'])) {
                $fromyear = $_GET['fromyear'];
            } else {
                $fromyear = '';
            }
            if (isset($_GET['frommonth']) && !empty($_GET['frommonth'])) {
                $frommonth = $_GET['frommonth'];
            } else {
                $frommonth = '';
            }
            if (isset($_GET['toyear']) && !empty($_GET['toyear'])) {
                $toyear = $_GET['toyear'];
            } else {
                $toyear = '';
            }
            if (isset($_GET['tomonth']) && !empty($_GET['tomonth'])) {
                $tomonth = $_GET['tomonth'];
            } else {
                $tomonth = '';
            }

            if (isset($_GET['facilitytype']) && !empty($_GET['facilitytype'])) {
                $facilitytype = $_GET['facilitytype'];
            } else {
                $facilitytype = '';
            }
            if (isset($_GET['dist_id']) && !empty($_GET['dist_id'])) {
                $dist_id = $_GET['dist_id'];
            } else {
                $dist_id = '';
            }
            if (isset($_GET['stakeholder']) && !empty($_GET['stakeholder'])) {
                $stakeholder = $_GET['stakeholder'];
            } else {
                $stakeholder = '';
            }
            if (isset($_GET['datasource']) && !empty($_GET['datasource'])) {
                $datasource = $_GET['datasource'];
            } else {
                $datasource = '';
            }
            if (isset($_GET['tier']) && !empty($_GET['tier'])) {
                $tier = $_GET['tier'];
            } else {
                $tier = '';
            }


            $from_date = $fromyear . '-' . str_pad($frommonth, 2, "0", STR_PAD_LEFT);
            $to_date = $toyear . '-' . str_pad($tomonth, 2, "0", STR_PAD_LEFT);

            $to_date = $toyear . "-" . $tomonth;
            if ($indicator == 'clmiscompliance') {
                $month = $_GET['month'];
                $year = $_GET['year'];
                //creatting object of clsFp
                $fp = new clsFp();
                $result = $fp->getComplainceReportSubmission($indicator, $month, $year, $stakeholder, $datasource, $province_id);
                echo $result;
            } else if ($tier == 'province') {

                //creatting object of clsFp
                $fp = new clsFp();
                $result = $fp->getProvinceLevelAggregate($indicator, $from_date, $to_date, $commodities, $facilitytype, $datasource, $tier, $dist_id, $province_id);
                echo $result;
            } else if ($tier == 'district') {
                //creatting object of clsFp
                $fp = new clsFp();
                $result = $fp->getDistrictWiseAggregateData($indicator, $from_date, $to_date, $commodities, $datasource, $tier, $dist_id, $province_id);
                echo $result;
            } else if ($tier == 'facility') {
                //creatting object of clsFp
                $fp = new clsFp();
                $result = $fp->getFacilityWiseAggregate($indicator, $from_date, $to_date, $commodities, $datasource, $tier, $dist_id, $province_id);
                echo $result;
            }
        } else {
            //If authentication fails
            print json_encode(array('message' => 'Authentication failed.'));
            exit;
        }
    }
    // else for sinidh
    else {
        
        $shared_key = 'userfp';
        $date_current = date('Y-m-d');
        $key = md5($shared_key . $date_current);

       
            $province_id = '2';
        

        if ($key == $auth) {
            if (isset($_GET['indicator']) && !empty($_GET['indicator'])) {
                $indicator = $_GET['indicator'];
            } else {
                $indicator = '';
            }
            if (isset($_GET['commodities']) && !empty($_GET['commodities'])) {
                $commodities = $_GET['commodities'];
            } else {
                $commodities = '';
            }
            if (isset($_GET['fromyear']) && !empty($_GET['fromyear'])) {
                $fromyear = $_GET['fromyear'];
            } else {
                $fromyear = '';
            }
            if (isset($_GET['frommonth']) && !empty($_GET['frommonth'])) {
                $frommonth = $_GET['frommonth'];
            } else {
                $frommonth = '';
            }
            if (isset($_GET['toyear']) && !empty($_GET['toyear'])) {
                $toyear = $_GET['toyear'];
            } else {
                $toyear = '';
            }
            if (isset($_GET['tomonth']) && !empty($_GET['tomonth'])) {
                $tomonth = $_GET['tomonth'];
            } else {
                $tomonth = '';
            }

            if (isset($_GET['facilitytype']) && !empty($_GET['facilitytype'])) {
                $facilitytype = $_GET['facilitytype'];
            } else {
                $facilitytype = '';
            }
            if (isset($_GET['dist_id']) && !empty($_GET['dist_id'])) {
                $dist_id = $_GET['dist_id'];
            } else {
                $dist_id = '';
            }
            if (isset($_GET['stakeholder']) && !empty($_GET['stakeholder'])) {
                $stakeholder = $_GET['stakeholder'];
            } else {
                $stakeholder = '';
            }
            if (isset($_GET['datasource']) && !empty($_GET['datasource'])) {
                $datasource = $_GET['datasource'];
            } else {
                $datasource = '';
            }
            if (isset($_GET['tier']) && !empty($_GET['tier'])) {
                $tier = $_GET['tier'];
            } else {
                $tier = '';
            }


            $from_date = $fromyear . '-' . str_pad($frommonth, 2, "0", STR_PAD_LEFT);
            $to_date = $toyear . '-' . str_pad($tomonth, 2, "0", STR_PAD_LEFT);

            $to_date = $toyear . "-" . $tomonth;
            if ($indicator == 'clmiscompliance') {
                $month = $_GET['month'];
                $year = $_GET['year'];
                //creatting object of clsFp
                $fp = new clsFp();
                $result = $fp->getComplainceReportSubmission($indicator, $month, $year, $stakeholder, $datasource, $province_id);
                echo $result;
            } else if ($tier == 'province') {

                //creatting object of clsFp
                $fp = new clsFp();
                $result = $fp->getProvinceLevelAggregate($indicator, $from_date, $to_date, $commodities, $facilitytype, $datasource, $tier, $dist_id, $province_id);
                echo $result;
            } else if ($tier == 'district') {
                //creatting object of clsFp
                $fp = new clsFp();
                $result = $fp->getDistrictWiseAggregateData($indicator, $from_date, $to_date, $commodities, $datasource, $tier, $dist_id, $province_id);
                echo $result;
            } else if ($tier == 'facility') {
                //creatting object of clsFp
                $fp = new clsFp();
                $result = $fp->getFacilityWiseAggregate($indicator, $from_date, $to_date, $commodities, $datasource, $tier, $dist_id, $province_id);
                echo $result;
            }
        } else {
            //If authentication fails
            print json_encode(array('message' => 'Authentication failed.'));
            exit;
        }
    }
} else {
    //If authentication fails
    print json_encode(array('message' => 'Authentication failed.'));
    exit;
}
?>