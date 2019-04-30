<?php

/**
 * clsReports
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class ClsReports {

    //wh id
    var $wh_id;
    //province id
    var $province_id;
    //district id
    var $district_id;
    //stakeholder
    var $stk;

    /**
     * editableMonths
     * @return type
     */
    function editableMonths() {
        //select query
        //gets
        //editable data entry months
        $qry = "SELECT
					tbl_warehouse.editable_data_entry_months
				FROM
					tbl_warehouse
				WHERE
					tbl_warehouse.wh_id = " . $this->wh_id;
        //query result
        $qryRes = mysql_fetch_array(mysql_query($qry));
        if (!empty($qryRes['editable_data_entry_months'])) {
            $months = $qryRes['editable_data_entry_months'];
        }
        return (isset($_SESSION['LIMIT']) ? $_SESSION['LIMIT'] : $months);
    }

    /**
     * getReportingStartMonth
     * @return type
     */
    function getReportingStartMonth() {
        //select query
        //gets
        //reporting start month
        $qry = "SELECT
					ADDDATE(tbl_warehouse.reporting_start_month,INTERVAL -1 MONTH) AS reporting_start_month
				FROM
					tbl_warehouse
				WHERE
					tbl_warehouse.wh_id = " . $this->wh_id;
        //query result
        $firstMonth = mysql_fetch_array(mysql_query($qry));
        if (!empty($firstMonth['reporting_start_month'])) {
            $NewDate = $firstMonth['reporting_start_month'];
        }
        return $NewDate;
    }

    /**
     * getReportingStartMonth
     * @return type
     */
    function getReportingStartMonthSatellite() {
        //select query
        //gets
        //reporting start month
        $qry = "SELECT
					ADDDATE(tbl_hf_satellite_rep_start_date.reporting_start_date,INTERVAL -1 MONTH) AS reporting_start_month
				FROM
					tbl_hf_satellite_rep_start_date
				WHERE
					tbl_hf_satellite_rep_start_date.warehouse_id = " . $this->wh_id;
        //query result
        $firstMonth = mysql_fetch_array(mysql_query($qry));
        if (!empty($firstMonth['reporting_start_month'])) {
            $NewDate = $firstMonth['reporting_start_month'];
            return $NewDate;
        }
        return false;
    }

    /**
     * GetLastReportDate
     * @return type
     */
    function GetLastReportDate() {
        $d = "2014-12-01";
        //select query
        $query = "SELECT max(RptDate) as MaxDate FROM tbl_wh_data WHERE wh_id=" . $this->wh_id;
        //query result
        $rs = mysql_fetch_object(mysql_query($query));
        if (!empty($rs->MaxDate)) {
            $d = $rs->MaxDate;
        } else if (empty($rs->MaxDate)) {
            //getReportingStartMonth
            $d = $this->getReportingStartMonth();
        }
        return $d;
    }

    /**
     * GetLastReportDateHF
     * @return type
     */
    function GetLastReportDateHF() {
        $d = '2015-01-01';
        //select query
        $query = "SELECT max(reporting_date) as MaxDate FROM tbl_hf_data WHERE warehouse_id=" . $this->wh_id;
        //query result
        $rs = mysql_fetch_object(mysql_query($query));
        if (!empty($rs->MaxDate)) {
            $d = $rs->MaxDate;
        } else if (empty($rs->MaxDate)) {
            //getReportingStartMonth
            $d = $this->getReportingStartMonth();
        }
        return $d;
    }

    /**
     * GetLastReportDateHFSatellite
     * @return type
     */
    function GetLastReportDateHFSatellite() {
        $d = '2014-12-01';
        //select query
        $query = "SELECT max(reporting_date) as MaxDate FROM tbl_hf_satellite_data WHERE warehouse_id=" . $this->wh_id;
        //query result
        $rs = mysql_fetch_object(mysql_query($query));
        if (!empty($rs->MaxDate)) {
            $d = $rs->MaxDate;
        } else if (empty($rs->MaxDate)) {
            //getReportingStartMonth
            $d = $this->getReportingStartMonthSatellite();
        }
        return $d;
    }

    /**
     * GetLastReportDateHFType
     * @return type
     */
    function GetLastReportDateHFType() {
        $d = "2014-12-01";
        //select query
        $query = "SELECT max(reporting_date) as MaxDate FROM tbl_hf_type_data WHERE facility_type_id=" . $this->wh_id . " AND district_id = " . $_SESSION['dist_id'];
        //query result
        $rs = mysql_query($query) or die(print mysql_error());
        while ($r = mysql_fetch_object($rs)) {
            if (!empty($r->MaxDate)) {
                $d = $r->MaxDate;
            }
        }
        return $d;
    }

    /**
     * GetLast3Months
     * @return type
     */
    function GetLast3Months() {
        $limit = $this->editableMonths();
        $last3Months = array();
        if ($limit > 0) {
            //select query
            $query = "SELECT DATE_FORMAT(RptDate,'%Y-%m-%d') as MaxDate FROM tbl_wh_data WHERE wh_id=" . $this->wh_id . " GROUP BY MaxDate ORDER BY MaxDate DESC LIMIT " . $limit;
            //query result
            $rs = mysql_query($query) or die(print mysql_error());

            while ($r = mysql_fetch_object($rs)) {
                $last3Months[] = $r->MaxDate;
            }
        } else {
            //select query
            $query = "SELECT DATE_FORMAT(RptDate,'%Y-%m-%d') as CurrentDate FROM tbl_wh_data WHERE wh_id=" . $this->wh_id . " GROUP BY CurrentDate ORDER BY CurrentDate DESC LIMIT 1";
            //query result
            $rs = mysql_query($query) or die(print mysql_error());
            $curr_dates[0] = date("Y-m-01");
            $curr_dates[1] = date('Y-m-01', strtotime($curr_dates[0] . "-1month"));

            while ($r = mysql_fetch_object($rs)) {
                if (in_array($r->CurrentDate, $curr_dates)) {
                    $last3Months[] = $r->CurrentDate;
                }
            }
        }
        return $last3Months;
    }

    /**
     * GetLast3MonthsHF
     * @return type
     */
    function GetLast3MonthsHF() {
        $limit = $this->editableMonths();
        $last3Months = array();
        if ($limit > 0) {
            //select query
            $query = "SELECT DATE_FORMAT(reporting_date,'%Y-%m-%d') as MaxDate FROM tbl_hf_data WHERE warehouse_id=" . $this->wh_id . " GROUP BY MaxDate ORDER BY MaxDate DESC LIMIT " . $limit;
            //query result
            $rs = mysql_query($query) or die(print mysql_error());

            while ($r = mysql_fetch_object($rs)) {
                $last3Months[] = $r->MaxDate;
            }
        } else {
            //select query
            $query = "SELECT DATE_FORMAT(reporting_date,'%Y-%m-%d') as CurrentDate FROM tbl_hf_data WHERE warehouse_id=" . $this->wh_id . " GROUP BY CurrentDate ORDER BY CurrentDate DESC LIMIT 1";
            //query result
            $rs = mysql_query($query) or die(print mysql_error());
            $curr_dates[0] = date("Y-m-01");
            $curr_dates[1] = date('Y-m-01', strtotime($curr_dates[0] . "-1month"));
            while ($r = mysql_fetch_object($rs)) {
                if (in_array($r->CurrentDate, $curr_dates)) {
                    $last3Months[] = $r->CurrentDate;
                }
            }
        }

        return $last3Months;
    }

    /**
     * GetLast3MonthsHFSatellite
     * @return type
     */
    function GetLast3MonthsHFSatellite() {
        $limit = $this->editableMonths();
        $start_date = $this->GetReportingStartMonthHFSatellite();
        $where = "";
        if ($start_date) {
            $where = " reporting_date >= $start_date AND ";
        }

        $last3Months = array();
        //select query
        $query = "SELECT DATE_FORMAT(reporting_date,'%Y-%m-%d') as MaxDate FROM tbl_hf_satellite_data WHERE $where warehouse_id=" . $this->wh_id . " GROUP BY MaxDate ORDER BY MaxDate DESC LIMIT " . $limit;
        //query result
        $rs = mysql_query($query) or die(print mysql_error());
        //fetch result
        while ($r = mysql_fetch_object($rs)) {
            $last3Months[] = $r->MaxDate;
        }
        return $last3Months;
    }

    /**
     * GetLast3MonthsHFSatellite
     * @return type
     */
    function GetReportingStartMonthHFSatellite() {
        //select query
        $query = "SELECT reporting_start_date FROM tbl_hf_satellite_rep_start_date WHERE warehouse_id=" . $this->wh_id;
        //query result
        $rs = mysql_query($query) or die(print mysql_error());
        //fetch result
        $numrows = mysql_num_rows($rs);
        if ($numrows > 0) {
            $r = mysql_fetch_object($rs);
            return $r->reporting_start_date;
        }

        return false;
    }

    /**
     * GetLast3MonthsHFType
     * @return type
     */
    function GetLast3MonthsHFType() {
        $limit = $this->editableMonths();
        $last3Months = array();
        //select query
        $query = "SELECT DATE_FORMAT(reporting_date,'%Y-%m-%d') as MaxDate FROM tbl_hf_type_data WHERE facility_type_id=" . $this->wh_id . " AND district_id = " . $_SESSION['dist_id'] . " GROUP BY MaxDate ORDER BY MaxDate DESC LIMIT " . $limit;
        //query result
        $rs = mysql_query($query) or die(print mysql_error());
        //fetch result
        while ($r = mysql_fetch_object($rs)) {
            $last3Months[] = $r->MaxDate;
        }
        return $last3Months;
    }

    /**
     * GetPendingReportMonth
     * @return string
     */
    function GetPendingReportMonth() {
        //GetLastReportDate
        $LRM = $this->GetLastReportDate();

        $NewDatetemp = $this->add($LRM, 2);
        $NewDate = $NewDatetemp->format('Y-m-d');
        $NewDatetemp2 = date('Y-m-d', strtotime('-1 day', strtotime($NewDatetemp->format('Y-m-d'))));
        $NewMonth_dt = new DateTime($NewDatetemp2);

        $today = date("Y-m-d");
        $today_dt = new DateTime($today);

        if ($NewMonth_dt < $today_dt) {
            return $this->add($LRM, 1)->format('Y-m-d');
        } else {
            return "";
        }
    }

    /**
     * GetPendingReportMonthHF
     * @return string
     */
    function GetPendingReportMonthHF() {
        $LRM = $this->GetLastReportDateHF();
        $NewDatetemp = $this->add($LRM, 2);
        $NewDate = $NewDatetemp->format('Y-m-d');
        
        
        $NewDatetemp2 = date('Y-m-d', strtotime('-1 day', strtotime($NewDatetemp->format('Y-m-d'))));
        $NewMonth_dt = new DateTime($NewDatetemp2);

//        echo '>>>>>>> LRM:';print_r($LRM);echo ', NewDate Temp:'; print_r($NewDatetemp); echo ' , NewDATE:'; print_r($NewDate);
        
        $today = date("Y-m-d");
        $curr_month = date("Y-m-01");
        $today_dt = new DateTime($today);

        $expected_new_month  = $this->add($LRM, 1)->format('Y-m-d');
//        echo ' ,RETURN: '; print_r($expected_new_month);
        //echo ' ,today_dt: '; print_r($today_dt);
        //echo ' ,NewMonth_dt: '; print_r($NewMonth_dt);
        //echo $curr_month;
        
        $wh_history = array();
        $query = "SELECT
                        distinct warehouse_status_history.`status`,
                        warehouse_status_history.warehouse_id,
                        warehouse_status_history.reporting_month
                    FROM
                        warehouse_status_history
                    WHERE
                        warehouse_status_history.warehouse_id = '".$this->wh_id."'
                    ORDER BY 
                        warehouse_status_history.reporting_month"  ;
        $rs = mysql_query($query) or die(print mysql_error());
        
        while ($r = mysql_fetch_assoc($rs)) {
            @$wh_history[$r['reporting_month']] += $r['status'];
        }
        //echo '<pre>';print_r($wh_history);exit;

        while($expected_new_month < $curr_month)
        {
            //if status is NOT ZERO. break and return
            if(!isset($wh_history[$expected_new_month]) || $wh_history[$expected_new_month]>=1)
            {
                //break while loop. to return this month value
                break;
            }
           //expected new month ++
            $expected_new_month = date("Y-m-d", strtotime("+1 month", strtotime($expected_new_month)));
        }

        
        //if ($NewMonth_dt < $today_dt) 
        if ($expected_new_month < $curr_month) {
//            return $this->add($LRM, 1)->format('Y-m-d');
            return $expected_new_month;
        } else {
            return "";
        }
    }

    /**
     * GetPendingReportMonthHFSatellite
     * @return string
     */
    function GetPendingReportMonthHFSatellite() {
        //GetLastReportDateHFSatellite
        $LRM = $this->GetLastReportDateHFSatellite();

        $NewDatetemp = $this->add($LRM, 2);
        $NewDate = $NewDatetemp->format('Y-m-d');
        $NewDatetemp2 = date('Y-m-d', strtotime('-1 day', strtotime($NewDatetemp->format('Y-m-d'))));
        $NewMonth_dt = new DateTime($NewDatetemp2);

        $today = date("Y-m-d");
        $today_dt = new DateTime($today);

        if ($NewMonth_dt < $today_dt) {
            return $this->add($LRM, 1)->format('Y-m-d');
        } else {
            return "";
        }
    }

    /**
     * GetPendingReportMonthHFType
     * @return string
     */
    function GetPendingReportMonthHFType() {
        //GetLastReportDateHFType
        $LRM = $this->GetLastReportDateHFType();

        $NewDatetemp = $this->add($LRM, 2);
        $NewDate = $NewDatetemp->format('Y-m-d');
        $NewDatetemp2 = date('Y-m-d', strtotime('-1 day', strtotime($NewDatetemp->format('Y-m-d'))));
        $NewMonth_dt = new DateTime($NewDatetemp2);

        $today = date("Y-m-d");
        $today_dt = new DateTime($today);

        if ($NewMonth_dt < $today_dt) {
            return $this->add($LRM, 1)->format('Y-m-d');
        } else {
            return "";
        }
    }

    /**
     * GetThisMonthReportDate
     * @return type
     */
    function GetThisMonthReportDate() {
        $LRM = date("Y-m-d");
        $NewDatetemp = $this->add($LRM, -1);
        $NewDate = $NewDatetemp->format('Y-m-d');
        return $NewDate;
    }

    /**
     * GetPreviousMonthReportDate
     * @param type $thismonth
     * @return type
     */
    function GetPreviousMonthReportDate($thismonth) {
        $NewDatetemp = $this->add($thismonth, -1);
        $NewDate = $NewDatetemp->format('Y-m-d');
        return $NewDate;
    }

    /**
     * add
     * @param type $date_str
     * @param type $months
     * @return \DateTime
     */
    function add($date_str, $months) {
        $date = new DateTime($date_str);
        $start_day = $date->format('j');

        $date->modify("+{$months} month");
        $end_day = $date->format('j');

        if ($start_day != $end_day) {
            $date->modify('last day of last month');
        }

        return $date;
    }

    /////// Function for KPK Tank district & FATA disticts to start off with provided date.
    /**
     * GetAllMonthsTillDate
     * @param type $date
     * @param type $year
     * @return type
     */
    function GetAllMonthsTillDate($date, $year) {
        $date1 = $date;
        $yr1 = $year;
        $yr2 = '2014';
        $date2 = date('Y-m-d');
        $time1 = strtotime($date1);
        $time2 = strtotime($date2);
        $my = date('mY', $time2);

        $months = array(date($yr1 . '-m-d', $time1));

        while ($time1 < $time2) {
            $time1 = strtotime(date('Y-m-d', $time1) . ' +1 month');
            if (date('mY', $time1) != $my && ($time1 < $time2)) {
                if (count($months) >= 12) {
                    $yr1 = $yr2;
                }

                $months[] = date($yr1 . '-m-d', $time1);
            }
        }

        return $months;
    }

}
