<?php

/**
 * clsvvmTypes
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */

// If it's going to need the database, then it's 
// probably smart to require it before we start.
class clsvvmTypes {

    protected static $table_name = "vvm_types";
    protected static $db_fields = array('id', 'type');
    public $id;
    public $type;

    /**
     * Get All Types
     * 
     * @return boolean
     */
    function getAllTypes(){
		$strSql = "SELECT
			vvm_types.type,
			vvm_types.id
			FROM
			vvm_types ORDER BY vvm_types.id DESC";
		$rsSql = mysql_query($strSql) or die("Error getAllTypes");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
	}

}

?>