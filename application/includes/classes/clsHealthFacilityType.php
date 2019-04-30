<?php
/**
 * clsHealthFacilityType
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
class clsHealthFacilityType {
//npkId
    var $m_npkId;
    //StakeholderTypeID
    var $m_StakeholderTypeID;
    //StakeholderTypeDescription
    var $m_StakeholderTypeDescription;
    //HealthFacilityTypeID
    var $m_HealthFacilityTypeID;
    //HealthFacilityTypeDescription
    var $m_HealthFacilityTypeDescription;
    //HealthFacilityRank
    var $m_HealthFacilityRank;

    /**
     * AddHealthFacilityType
     * 
     * @return int
     */
    function AddHealthFacilityType() {

        if ($this->m_StakeholderTypeID == '') {
            $this->m_StakeholderTypeID = 'NULL';
        }
        if ($this->m_StakeholderTypeDescription == '') {
            $this->m_StakeholderTypeDescription = 'NULL';
        }
        if ($this->m_HealthFacilityRank == '') {
            $this->m_HealthFacilityRank = 'NULL';
        }
        //add query
        $strSql = "INSERT INTO  tbl_hf_type(hf_type, stakeholder_id, hf_rank) VALUES(" . "'" . $this->m_HealthFacilityTypeDescription . "'" . ", " . $this->m_StakeholderTypeID . ", " . $this->m_HealthFacilityRank . ")";
        //query result
        $rsSql = mysql_query($strSql) or die("Error AddHealthFacilityType");
        if (mysql_insert_id() > 0) {
            return mysql_insert_id();
        } else {
            return 0;
        }
    }
/**
 * EditHealthFacilityType
 * 
 * @return boolean
 */
    function EditHealthFacilityType() {
        //update query
        $strSql = "UPDATE tbl_hf_type SET ";

        $HealthFacilityTypeName = "hf_type='" . $this->m_HealthFacilityTypeDescription . "'";
        if ($this->m_HealthFacilityTypeDescription != '') {
            $strSql .=$HealthFacilityTypeName;
        }
        $StakeholderID = ",stakeholder_id='" . $this->m_StakeholderTypeID . "'";
        if ($this->m_StakeholderTypeID != '') {
            $strSql .=$StakeholderID;
        }
        $HealthFacilityRank = ",hf_rank='" . $this->m_HealthFacilityRank . "'";
        if ($this->m_StakeholderTypeID != '') {
            $strSql .=$HealthFacilityRank;
        }

        $strSql .=" WHERE pk_id=" . $this->m_npkId;

        $rsSql = mysql_query($strSql) or die("Error EditHealthFacilityType");
        if (mysql_affected_rows()) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * DeleteHealthFacilityType
 * 
 * @return boolean
 */
    function DeleteHealthFacilityType() {
        //delete query
        $strSql = "DELETE FROM  tbl_hf_type WHERE pk_id=" . $this->m_npkId;
//query result
        $rsSql = mysql_query($strSql) or die("Error DeleteHealthFacilityType");
        if (mysql_affected_rows()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
/**
 * GetAllHealthFacilityType
 * 
 * @return boolean
 */
    function GetAllHealthFacilityType() {
        //Get All Health Facility Type query
        //gets
        //pk id
        //health_facility_type
        //stakeholder_id
        //health_facility_rank
        //stakeholder_name
        $strSql = " SELECT
                        tbl_hf_type.pk_id,
                        tbl_hf_type.hf_type AS health_facility_type,
                        tbl_hf_type.stakeholder_id,
                        tbl_hf_type.hf_rank AS health_facility_rank,
                        stakeholder.stkname AS stakeholder_name
                    FROM
                        tbl_hf_type
                        INNER JOIN stakeholder ON tbl_hf_type.stakeholder_id = stakeholder.stkid
                        WHERE stakeholder.stk_type_id = 0 AND tbl_hf_type.stakeholder_id = ". $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetAllHealthFacilityType");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetHealthFacilityById
 * 
 * @return boolean
 */
    function GetHealthFacilityById() {
        //Get Health Facility By Id
        //pk id
        //health_facility_type
        //stakeholder_id
        //health_facility_rank
        //stakeholder_name
        $strSql = " SELECT
                        tbl_hf_type.pk_id,
                        tbl_hf_type.hf_type AS health_facility_type,
                        tbl_hf_type.stakeholder_id,
                        tbl_hf_type.hf_rank AS health_facility_rank,
                        stakeholder.stkname AS stakeholder_name
                    FROM
                        tbl_hf_type
                        INNER JOIN stakeholder ON tbl_hf_type.stakeholder_id = stakeholder.stkid
                    WHERE
                        tbl_hf_type.pk_id = " . $this->m_npkId;
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetHealthFacilityById");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }
/**
 * GetNewRank
 * 
 * @return boolean
 */
    function GetNewRank() {
        $strSql = " SELECT
                        MAX(tbl_hf_type.hf_rank) + 1 AS rank
                    FROM
                        tbl_hf_type
                    WHERE
                        tbl_hf_type.pk_id <> 13";
        //query result
        $rsSql = mysql_query($strSql) or die("Error GetNewRank");
        if (mysql_num_rows($rsSql) > 0) {
            return $rsSql;
        } else {
            return FALSE;
        }
    }

}

?>
