<?php

/**
 * clsDatabaseManager
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
/* * ******************************************************************************
 * Date 30-01-2006																*
 * owner DIGITALSPINNERS 														*
 * Address House No 13, Street 48, F-7/4, Islamabad, Pakistan					*
 * Phone No. +9251 2274909,2872546,2874983 Fax:2879134							*
 * Author: Yasir Abbasi (Project Manager)											*
 * version: 1.0.0.0																*
 * ******************************************************************************* */

//---------------------  START CLASS FOR Database Manager -------------------------
class clsDatabaseManager {

    var $strTableName;
    var $strColumns;
    var $strWhere;
    var $strOrderBy;
    var $strGroupBy;
    var $nLimit;
    var $nLimitEnd;

    /**
     * Used to select a table given columns, table name, and where clause.
     * 
     * @param strTableName
     * @param strColumns    Columns which need to be selected, by default strColumns='*'
     * @param strWhere   Where clause, default is null
     */
    function SelectTable($strTableName, $strColumns = ' * ', $strWhere = '', $strOrderBy = '', $strGroupBy = '', $nLimit = "", $nLimitEnd = "") {
        if ($strWhere != '') {
            $strQry = 'SELECT DISTINCT ' . $strColumns . ' FROM ' . $strTableName . ' WHERE ' . $strWhere;
        } else {
            $strQry = 'SELECT DISTINCT ' . $strColumns . ' FROM ' . $strTableName;
        }

        if ($strGroupBy != '') {
            $strQry .= ' Group BY ' . $strGroupBy;
        }

        if ($strOrderBy != '') {
            $strQry .= ' ORDER BY ' . $strOrderBy;
        }

        if ($nLimitEnd != "") {
            $strQry .= ' LIMIT ' . $nLimit . ',' . $nLimitEnd;
        } elseif ($nLimit != "") {
            $strQry .= ' LIMIT ' . $nLimit;
        }

        $result = mysql_query($strQry) or die("Unable to select, Error: " . mysql_error());
        return $result;
    }

    /**
     * SelectTable1
     * 
     * @param type $strTableName
     * @param type $strColumns
     * @param type $strWhere
     * @param type $strOrderBy
     * @param type $strGroupBy
     * @param type $nLimit
     * @param type $nLimitEnd
     * @return type
     */
    function SelectTable1($strTableName, $strColumns = ' * ', $strWhere = '', $strOrderBy = '', $strGroupBy = '', $nLimit = "", $nLimitEnd = "") {
        if ($strWhere != '') {
            $strQry = 'SELECT DISTINCT ' . $strColumns . ' FROM ' . $strTableName . ' WHERE ' . $strWhere;
        } else {
            $strQry = 'SELECT DISTINCT ' . $strColumns . ' FROM ' . $strTableName;
        }

        if ($strGroupBy != '') {
            $strQry .= ' Group BY ' . $strGroupBy;
        }

        if ($strOrderBy != '') {
            $strQry .= ' ORDER BY ' . $strOrderBy;
        }

        if ($nLimitEnd != "") {
            $strQry .= ' LIMIT ' . $nLimit . ',' . $nLimitEnd;
        } elseif ($nLimit != "") {
            $strQry .= ' LIMIT ' . $nLimit;
        }
        echo "||||" . $strQry . "||||";

        $result = mysql_query($strQry) or die("Unable to select, Error: " . mysql_error());
        return $result;
    }

    /**
     * Used to delete a row from table.
     * 
     * @param strTableName
     * @param strWhere   Where clause, default is null
     */

    /**
     * DeleteTable
     * 
     * @param type $strTableName
     * @param type $strWhere
     * @return type
     */
    function DeleteTable($strTableName, $strWhere = '') { //return number of rows affected
        if ($strWhere == '') {
            die('Cannot delete all rows in the table');
        }

        $strQry = 'DELETE FROM ' . $strTableName . ' WHERE ' . $strWhere;
        mysql_query($strQry) or die("Unable to delete, Error: " . mysql_error());
        return mysql_affected_rows();
    }

    /**
     * Used to delete a row from table.
     * 
     * @param strTableName
     * @param strWhere   Where clause, default is null
     */

    /**
     * DeleteAllTable
     * 
     * @param type $strTableName
     * @return type
     */
    function DeleteAllTable($strTableName) { //return number of rows affected
        $strQry = 'DELETE FROM ' . $strTableName;
        mysql_query($strQry) or die("Unable to delete, Error: " . mysql_error());
        return mysql_affected_rows();
    }

    /**
     * Used to update a table.
     * 
     * @param strTableName
     * @param strUpdateData  
     * @param strWhere   Where clause, default is null
     */

    /**
     * UpdateTable
     * 
     * @param type $strTableName
     * @param type $strUpdateData
     * @param type $strWhere
     * @return type
     */
    function UpdateTable($strTableName, $strUpdateData = '', $strWhere = '') { //returns number of rows affected
        if ($strWhere == '') {
            die('Cannot update all rows in the table');
        }
        if ($strUpdateData == '') {
            die('What to update?');
        }
        $strQry = 'UPDATE ' . $strTableName . ' SET ' . $strUpdateData . ' WHERE ' . $strWhere;
        mysql_query($strQry) or die("Unable to update, Error: " . mysql_error());
        return mysql_affected_rows();
    }

    /**
     * Used to insert a row into table.
     * 
     * @param strTableName
     * @param strColumns  
     * @param strValues
     */

    /**
     * InsertTable
     * 
     * @param type $strTableName
     * @param type $strColumns
     * @param type $strValues
     * @return type
     */
    function InsertTable($strTableName, $strColumns = '', $strValues = '') { //returns number of rows affected
        if ($strValues == '') {
            die('What to insert?');
        }
        if ($strColumns == '') {
            $strQry = 'INSERT INTO ' . $strTableName . ' VALUES(' . $strValues . ')';
        } else {
            $strQry = 'INSERT INTO ' . $strTableName . '(' . $strColumns . ') VALUES(' . $strValues . ')';
        }
        mysql_query($strQry) or die("Unable to insert, Error: " . mysql_error());
        return mysql_insert_id();
    }

    /**
     * InsertTable1
     * 
     * @param type $strTableName
     * @param type $strColumns
     * @param type $strValues
     * @return type
     */
    function InsertTable1($strTableName, $strColumns = '', $strValues = '') { //returns number of rows affected
        if ($strValues == '') {
            die('What to insert?');
        }
        if ($strColumns == '') {
            $strQry = 'INSERT INTO ' . $strTableName . ' VALUES(' . $strValues . ')';
        } else {
            $strQry = 'INSERT INTO ' . $strTableName . '(' . $strColumns . ') VALUES(' . $strValues . ')';
        }
        echo $strQry . "<br><hr>";
        exit;
        mysql_query($strQry) or die("Unable to insert, Error: " . mysql_error());
        return mysql_insert_id();
    }

    /**
     * InsertSelect
     * 
     * @param type $strTableName
     * @param type $strColumns
     * @param type $strQuery
     * @return type
     */
    function InsertSelect($strTableName, $strColumns = '', $strQuery = '') { //returns number of rows affected
        if ($strQuery == '') {
            error('What to insert?');
        }
        if ($strQuery == '') {
            error('Nothing to select!');
        }
        if ($strColumns == '') {
            $strQry = 'INSERT INTO ' . $strTableName . ' ' . $strQuery;
        } else {
            $strQry = 'INSERT INTO ' . $strTableName . '(' . $strColumns . ') ' . $strQuery;
        }


        mysql_query($strQry) or die("Unable to insert, Error: " . mysql_error());
        return mysql_affected_rows();
    }

    /**
     * InsertAutoTable
     * 
     * @param type $strTableName
     * @param type $strColumns
     * @param type $strValues
     * @return type
     */
    function InsertAutoTable($strTableName, $strColumns = '', $strValues = '') { //returns autoincremented value
        if ($strValues == '') {
            die('What to insert?');
        }
        if ($strColumns == '') {
            $strQry = 'INSERT INTO ' . $strTableName . ' VALUES(' . $strValues . ')';
        } else {
            $strQry = 'INSERT INTO ' . $strTableName . '(' . $strColumns . ') VALUES(' . $strValues . ')';
        }

        mysql_query($strQry) or die("Unable to insert auto, Error: " . mysql_error());
        return mysql_insert_id();
    }

    /**
     * GetTableFields
     * 
     * @param type $strTableName
     * @return boolean
     */
    function GetTableFields($strTableName) {
        $strFields = "";
        $strQry = 'SHOW COLUMNS FROM ' . $strTableName;

        $rsTableFields = mysql_query($strQry) or die("Unable to select fields Error: " . mysql_error());
        if (mysql_num_rows($rsTableFields) > 0) {
            while ($objTableFields = mysql_fetch_object($rsTableFields)) {
                $strFields = $strFields . $objTableFields->Field . ",";
            }
            $strFields = substr($strFields, 0, strlen($strFields) - 1);
            return $strFields;
        } else {
            return false;
        }
    }

    /**
     * GetTableData
     * 
     * @param type $TabelName
     * @param type $SortField
     * @param type $SordOrder
     * @return boolean
     */
    function GetTableData($TabelName, $SortField, $SordOrder = 'ASC') {
        $rsSql = mysql_query("SELECT * FROM `" . $TabelName . "` ORDER BY `" . $SortField . "` " . $SordOrder . "");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return false;
        }
    }

}

?>