<?php

/**
 * clsFiscalYear
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsFiscalYear {
    /**
     * Get Fiscal Year
     * 
     * @return type
     */
    public function getFiscalYear() {
        $current_year = date("Y");
        $current_month = date("m");
        if ($current_month < 7) {
            $from_date = ($current_year - 1) . "-06-30";
            $to_date = $current_year . "-07-30";
        } else {
            $from_date = $current_year . "-06-30";
            $to_date = ($current_year + 1) . "-07-30";
        }

        return array(
            'from_date' => $from_date,
            'to_date' => $to_date
        );
    }

}

?>