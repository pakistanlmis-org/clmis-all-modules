<?php

set_time_limit(0);

/**
 * clsDistrictlevel
 * @package includes/class
 *
 * @author     Ahmad Saib
 * @email <ahmad.saib@outlook.com>
 *
 * @version    2.2
 *
 */
class clsFp {

    /**
     *
     *
     * @return int
     */
    function getProvinceLevelAggregate($indicator, $from_date, $to_date, $commodities, $facilitytype, $datasource, $tier, $dist_id, $province_id) {
        if (!empty($dist_id)) {
            $where_district = "AND tbl_warehouse.dist_id = $dist_id";
            $where_district2 = "AND tbl_locations.PkLocID = $dist_id";
            $where_district3 = " AND summary_district.district_id = $dist_id";
        } else {
            $where_district = "";
            $where_district2 = "";
            $where_district3 = "";
        }


        if ($datasource == 'pwd') {
            $stk = "tbl_warehouse.stkid = '1' AND ";
            $stk1 = "summary_province.stakeholder_id = '1' AND ";
        } else if ($datasource == 'lhw') {
            $stk = "tbl_warehouse.stkid = '2' AND ";
            $stk1 = "summary_province.stakeholder_id = '2' AND ";
        } else if ($datasource == 'dohhf') {
            $stk = "tbl_warehouse.stkid = '7' AND ";
            $stk1 = "summary_province.stakeholder_id = '7' AND ";
        } else if ($datasource == 'pphihf') {
            $stk = "tbl_warehouse.stkid = '9' AND ";
            $stk1 = "summary_province.stakeholder_id = '9' AND ";
        } else if ($datasource == 'cmw') {
            $stk = "tbl_warehouse.stkid = '111' AND ";
            $stk1 = "summary_province.stakeholder_id = '111' AND ";
        } else if ($datasource == 'all') {
            $stk = "tbl_warehouse.stkid IN ('1','2','7','9','111')  AND ";
            $stk1 = "summary_province.stakeholder_id IN ('1','2','7','9','111')  AND ";
        } else {
            $stk = "";
            $stk1 = "";
        }

        if ($commodities == 'condom') {
            $commodities_where = "AND summary_district.item_id = 'IT-001'";
            $commodities_where1 = "AND tbl_wh_data.item_id = 'IT-001'";
            $commodities_where2 = "AND tbl_hf_data.item_id = '1'";
            $commodities_where3 = "AND summary_province.item_id = 'IT-001'";
        } else if ($commodities == 'oralpills') {
            $commodities_where = "AND summary_district.item_id IN ('IT-002', 'IT-009')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-002', 'IT-009')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('2', '9')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-002', 'IT-009')";
        } else if ($commodities == 'ecp') {
            $commodities_where = "AND summary_district.item_id = 'IT-003'";
            $commodities_where1 = "AND tbl_wh_data.item_id = 'IT-003'";
            $commodities_where2 = "AND tbl_hf_data.item_id = '3'";
            $commodities_where3 = "AND summary_province.item_id = 'IT-003'";
        } else if ($commodities == 'iucd') {
            $commodities_where = "AND summary_district.item_id IN ('IT-004', 'IT-005')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-004', 'IT-005')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('4', '5')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-004', 'IT-005')";
        } else if ($commodities == 'injectables') {
            $commodities_where = "AND summary_district.item_id IN ('IT-006', 'IT-007')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-006', 'IT-007')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('6', '7')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-006', 'IT-007')";
        } else if ($commodities == 'implants') {
            $commodities_where = "AND summary_district.item_id IN ('IT-008', 'IT-013')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-008', 'IT-013')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('8', '13')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-008', 'IT-013')";
        }

        if ($indicator == "rdc") {

            $query = "SELECT
        A.itm_name,
	SUM(A.issue_balance) AS distributed,
	SUM(A.received_balance) AS received,
	B.consumption AS consumption
        FROM
	(
		SELECT
			SUM(tbl_hf_data.issue_balance) AS `issue_balance`,
			SUM(
				tbl_hf_data.received_balance
			) AS `received_balance`,
			itminfo_tab.itm_name,
			itminfo_tab.itm_id
		FROM
			tbl_hf_data
		INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
		INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
		WHERE
                $stk
		tbl_warehouse.prov_id = '$province_id'
		AND DATE_FORMAT(
			tbl_hf_data.reporting_date,
			'%Y-%m'
		) BETWEEN '$from_date'
		AND '$to_date'
		AND itminfo_tab.itm_category = 1
		GROUP BY
			itminfo_tab.itm_id
		UNION
			SELECT
				SUM(tbl_wh_data.wh_issue_up) AS `issue_balance`,
				SUM(tbl_wh_data.wh_received) AS `received_balance`,
				itminfo_tab.itm_name,
				itminfo_tab.itm_id
			FROM
				tbl_wh_data
			INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
			INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
			WHERE
                        $stk
				tbl_warehouse.prov_id = '$province_id'

			AND DATE_FORMAT(
				tbl_wh_data.RptDate,
				'%Y-%m'
			) BETWEEN '$from_date'
			AND '$to_date'
			AND itminfo_tab.itm_category = 1
			GROUP BY
				itminfo_tab.itm_id
	) A
        JOIN (
	SELECT
		SUM(
			summary_province.consumption
		) AS `consumption`,
		itminfo_tab.itm_id
	FROM
		summary_province
	INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
	INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
	WHERE
        $stk1
		summary_province.province_id = '$province_id'
	AND DATE_FORMAT(
		summary_province.reporting_date,
		'%Y-%m'
	) BETWEEN '$from_date'
	AND '$to_date'
	AND itminfo_tab.itm_category = 1
	GROUP BY
		itminfo_tab.itm_id
        ) B ON A.itm_id = B.itm_id
        GROUP BY
	A.itm_id";
        } else if ($indicator == "commoditiesdistributed") {

            $query = "SELECT SUM(A.value) as `value` from (SELECT
	    SUM(tbl_hf_data.issue_balance) as `value`
            FROM
            tbl_hf_data
            INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
            WHERE
                    $stk
            tbl_warehouse.prov_id = '$province_id'
            AND DATE_FORMAT(
                    tbl_hf_data.reporting_date,
                    '%Y-%m'
            ) BETWEEN  '$from_date'
                    AND '$to_date'

                    $commodities_where2
            GROUP BY
                    tbl_warehouse.prov_id UNION  "
                    . "SELECT
	    SUM(tbl_wh_data.wh_issue_up) as `value`
            FROM
                    tbl_wh_data
            INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
            WHERE
                    $stk
             tbl_warehouse.prov_id = '$province_id'
            AND DATE_FORMAT(
                    tbl_wh_data.RptDate,
                    '%Y-%m'
            ) BETWEEN  '$from_date'
                    AND '$to_date'
                    $commodities_where1
            GROUP BY
                    tbl_warehouse.prov_id) A";
        } else if ($indicator == "commoditiesdistibutedfpclients") {

            $query = "SELECT
	             IFNULL(A.value1,0) as `value`
                     FROM
                    (
                    SELECT
                    tbl_locations.PkLocID AS dist_id,
                    tbl_locations.LocName AS district,
                    SUM(tbl_wh_data.wh_issue_up) AS value1
                    FROM
                    tbl_wh_data
                    INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    WHERE
                    tbl_warehouse.stkid = 1 AND
                    tbl_warehouse.prov_id = '$province_id'
                    AND tbl_warehouse.stkofficeid = 17
                    $commodities_where1
                    AND DATE_FORMAT(
                    tbl_wh_data.RptDate,
                    '%Y-%m'
                    ) BETWEEN '$from_date'
                    AND '$to_date'
                    GROUP BY
                    tbl_warehouse.prov_id
            ) A";
        } else if ($indicator == "commoditiesconsumed") {
            $query = "SELECT
                        IFNULL(A.value1, 0) AS `value`
                        FROM
                        (
                        SELECT

                        SUM(
                        summary_province.consumption
                        ) AS `value1`
                        FROM
                        summary_province
                        INNER JOIN itminfo_tab ON summary_province.item_id = itminfo_tab.itmrec_id
                        INNER JOIN stakeholder ON summary_province.stakeholder_id = stakeholder.stkid
                        WHERE
                        $stk1
                        summary_province.province_id = '$province_id'
                        AND DATE_FORMAT(
                        summary_province.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        $commodities_where3
                        ) A";
        } else if ($indicator == 'commoditiesreceived') {

            $query = "SELECT SUM(A.value) as `value` from (SELECT
                        SUM(tbl_hf_data.received_balance) as `value`
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        WHERE
                         $stk
                        tbl_warehouse.prov_id = '$province_id'
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'

                        $commodities_where2
                        GROUP BY
                        tbl_warehouse.prov_id UNION "
                    . "SELECT
                        SUM(tbl_wh_data.wh_received) as `value`
                        FROM
                        tbl_wh_data
                        INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                        WHERE
                       $stk
                         tbl_warehouse.prov_id = '$province_id'
                        AND DATE_FORMAT(
                        tbl_wh_data.RptDate,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        $commodities_where1
                        GROUP BY
                        tbl_warehouse.prov_id) A";
        } else if ($indicator == 'fpclientsatpwd') {

            $query = "SELECT

            Sum(IFNULL(tbl_hf_data.new, 0) + IFNULL(tbl_hf_data.old, 0)) AS `value`

            FROM
            tbl_hf_data
            INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id

            WHERE
                    tbl_warehouse.prov_id = '$province_id'
            AND tbl_warehouse.stkid = 1

            AND DATE_FORMAT(
                    tbl_hf_data.reporting_date,
                    '%Y-%m'
            ) BETWEEN '$from_date'
            AND '$to_date'
            GROUP BY
                    tbl_warehouse.prov_id";
        } else if ($indicator == 'surgeriesreferred') {

            $query = "SELECT
                        SUM(
                        IF (
                        tbl_warehouse.hf_type_id IN (4)
                        AND itminfo_tab.itm_category = 2,
                        (
                        IF (
                        tbl_hf_data_reffered_by.hf_type_id IN (4),
                        tbl_hf_data_reffered_by.ref_surgeries,
                        0
                        )
                        ),
                        tbl_hf_data.issue_balance
                        )
                        ) AS `value`
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                        INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data_reffered_by.hf_data_id = tbl_hf_data.pk_id
                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                        WHERE
                        tbl_warehouse.prov_id = '$province_id' AND
                        tbl_warehouse.stkid = 1 AND
                        tbl_warehouse.hf_type_id = 4
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        GROUP BY
                        tbl_warehouse.prov_id";
        } else if ($indicator == 'surgeriesperformed') {

            $query = "SELECT
                        
                        SUM(tbl_hf_data_reffered_by.static) + SUM(tbl_hf_data_reffered_by.camp) AS `value`
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                        INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data_reffered_by.hf_data_id = tbl_hf_data.pk_id
                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                        WHERE
                        tbl_warehouse.prov_id = '$province_id' AND
                        tbl_warehouse.stkid = 1 AND
                        tbl_warehouse.hf_type_id = 4
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        GROUP BY
                        tbl_warehouse.prov_id";
        }


        $rs = mysql_query($query) or die(print mysql_error());
        $rows = array();
        while ($r = mysql_fetch_assoc($rs)) {
            $rows[] = $r;
        }
        print json_encode($rows
        );
    }

    function getDistrictWiseAggregateData($indicator, $from_date, $to_date, $commodities, $datasource, $tier, $dist_id, $province_id) {
        if (!empty($dist_id)) {
            $where_district = "AND tbl_warehouse.dist_id = $dist_id";
            $where_district2 = "AND tbl_locations.PkLocID = $dist_id";
            $where_district3 = " AND summary_district.district_id = $dist_id";
        } else {
            $where_district = "";
            $where_district2 = "";
            $where_district3 = "";
        }

        if ($datasource == 'pwd') {
            $stk = "tbl_warehouse.stkid = '1' AND ";
            $stk1 = "summary_district.stakeholder_id = '1' AND ";
            $stkoffice = "tbl_warehouse.stkofficeid = 17 AND";
        } else if ($datasource == 'lhw') {
            $stk = "tbl_warehouse.stkid = '2' AND ";
            $stkoffice = " tbl_warehouse.stkofficeid = 20 AND";
            $stk1 = "summary_district.stakeholder_id = '2' AND ";
        } else if ($datasource == 'dohhf') {
            $stk = "tbl_warehouse.stkid = '7' AND ";
            $stkoffice = " tbl_warehouse.stkofficeid = 32 AND";
            $stk1 = "summary_district.stakeholder_id = '7' AND ";
        } else if ($datasource == 'pphihf') {
            $stk = "tbl_warehouse.stkid = '9' AND ";
            $stkoffice = " tbl_warehouse.stkofficeid = 71 AND";
            $stk1 = "summary_district.stakeholder_id = '9' AND ";
        } else if ($datasource == 'cmw') {
            $stk = "tbl_warehouse.stkid = '111' AND ";
            $stk1 = "summary_district.stakeholder_id = '111' AND ";
            $stkoffice = "";
        } else if ($datasource == 'all') {
            $stk = "tbl_warehouse.stkid IN ('1','2','7','9','111')  AND ";
            $stkoffice = " tbl_warehouse.stkofficeid IN ('17','20','32','71') AND";
            $stk1 = "summary_district.stakeholder_id IN ('1','2','7','9','111')  AND ";
        } else {
            $stk = "";
            $stkoffice = "";
            $stk1 = "";
        }
        if ($commodities == 'condom') {
            $commodities_where = "AND summary_district.item_id = 'IT-001'";
            $commodities_where1 = "AND tbl_wh_data.item_id = 'IT-001'";
            $commodities_where2 = "AND tbl_hf_data.item_id = '1'";
            $commodities_where3 = "AND summary_province.item_id = 'IT-001'";
        } else if ($commodities == 'oralpills') {
            $commodities_where = "AND summary_district.item_id IN ('IT-002', 'IT-009')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-002', 'IT-009')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('2', '9')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-002', 'IT-009')";
        } else if ($commodities == 'ecp') {
            $commodities_where = "AND summary_district.item_id = 'IT-003'";
            $commodities_where1 = "AND tbl_wh_data.item_id = 'IT-003'";
            $commodities_where2 = "AND tbl_hf_data.item_id = '3'";
            $commodities_where3 = "AND summary_province.item_id = 'IT-003'";
        } else if ($commodities == 'iucd') {
            $commodities_where = "AND summary_district.item_id IN ('IT-004', 'IT-005')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-004', 'IT-005')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('4', '5')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-004', 'IT-005')";
        } else if ($commodities == 'injectables') {
            $commodities_where = "AND summary_district.item_id IN ('IT-006', 'IT-007')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-006', 'IT-007')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('6', '7')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-006', 'IT-007')";
        } else if ($commodities == 'implants') {
            $commodities_where = "AND summary_district.item_id IN ('IT-008', 'IT-013')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-008', 'IT-013')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('8', '13')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-008', 'IT-013')";
        }



        if ($indicator == "commoditiesdistributed") {

            $query = "SELECT
	B.PkLocID AS dist_id,
	B.LocName AS district,
	IFNULL(A.value1,0) as `value`

            FROM
                    (
                    SELECT
                            tbl_locations.PkLocID AS dist_id,
                            tbl_locations.LocName AS district,
                            SUM(tbl_wh_data.wh_issue_up) AS value1
                    FROM
                            tbl_wh_data
                    INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    WHERE
                            $stk
                                $stkoffice
                     tbl_warehouse.prov_id = '$province_id'
                     
                     $where_district
                    $commodities_where1
                    AND DATE_FORMAT(
                            tbl_wh_data.RptDate,
                            '%Y-%m'
                    ) BETWEEN '$from_date'
        AND '$to_date'
                    GROUP BY
                            tbl_locations.PkLocID
            ) A
        RIGHT JOIN (
                SELECT
                        tbl_locations.PkLocID,
                        tbl_locations.LocName
                FROM
                        tbl_locations
                WHERE
                        tbl_locations.LocLvl = 3
                        $where_district2
                AND tbl_locations.ParentID = '$province_id'
        ) B ON A.dist_id = B.pkLocID ";
        } else if ($indicator == "commoditiesdistibutedfpclients") {

            $query = "SELECT
	B.PkLocID AS dist_id,
	B.LocName AS district,
	IFNULL(A.value1,0) as `value`

            FROM
                    (
                    SELECT
                            tbl_locations.PkLocID AS dist_id,
                            tbl_locations.LocName AS district,
                            SUM(tbl_wh_data.wh_issue_up) AS value1
                    FROM
                            tbl_wh_data
                    INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    WHERE
                            tbl_warehouse.stkid = 1 AND
                   tbl_warehouse.prov_id = '$province_id'
                    AND tbl_warehouse.stkofficeid = 17
                    $where_district

                    $commodities_where1
                    AND DATE_FORMAT(
                            tbl_wh_data.RptDate,
                            '%Y-%m'
                    ) BETWEEN '$from_date'
        AND '$to_date'
                    GROUP BY
                            tbl_locations.PkLocID
            ) A
        RIGHT JOIN (
                SELECT
                        tbl_locations.PkLocID,
                        tbl_locations.LocName
                FROM
                        tbl_locations
                WHERE
                        tbl_locations.LocLvl = 3
                AND tbl_locations.ParentID = '$province_id'
                $where_district2
        ) B ON A.dist_id = B.pkLocID";
        } else if ($indicator == "commoditiesconsumed") {

            $query = "SELECT
                        B.PkLocID AS dist_id,
                        B.LocName AS district,
                        IFNULL(A.value1, 0) AS `value`
                        FROM
                        (
                        SELECT
                        summary_district.district_id as dist_id,
                        SUM(
                        summary_district.consumption
                        ) AS `value1`

                        FROM
                        summary_district
                        INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
                        INNER JOIN tbl_locations ON summary_district.province_id = tbl_locations.PkLocID
                        INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
                        WHERE
$stk1
                        summary_district.province_id = '$province_id'
                        AND DATE_FORMAT(summary_district.reporting_date, '%Y-%m') BETWEEN '$from_date'
                        AND '$to_date'
                        $commodities_where
                        $where_district3

                        GROUP BY
                        summary_district.district_id
                        ) A
                        RIGHT JOIN (
                        SELECT
                        tbl_locations.PkLocID,
                        tbl_locations.LocName
                        FROM
                        tbl_locations
                        WHERE
                        tbl_locations.LocLvl = 3
                        AND tbl_locations.ParentID = '$province_id'
                        $where_district2
                        ) B ON A.dist_id = B.pkLocID ";
        } else if ($indicator == 'commoditiesreceived') {

            $query = "SELECT
                        B.PkLocID AS dist_id,
                        B.LocName AS district,
                        IFNULL(A.value1, 0) as `value`

                        FROM
                        (
                        SELECT
                        tbl_locations.PkLocID AS dist_id,
                        tbl_locations.LocName AS district,
                        SUM(tbl_wh_data.wh_received) AS value1
                        FROM
                        tbl_wh_data
                        INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                        WHERE
                        $stk
                            $stkoffice
                         tbl_warehouse.prov_id = '$province_id'
                        
                        $where_district
                        $commodities_where1
                        AND DATE_FORMAT(
                        tbl_wh_data.RptDate,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        GROUP BY
                        tbl_locations.PkLocID
                        ) A
                        RIGHT JOIN (
                        SELECT
                        tbl_locations.PkLocID,
                        tbl_locations.LocName
                        FROM
                        tbl_locations
                        WHERE
                        tbl_locations.LocLvl = 3
                        AND tbl_locations.ParentID = '$province_id'
                        $where_district2
                        ) B ON A.dist_id = B.pkLocID ";
        } else if ($indicator == 'fpclientsatpwd') {

            $query = "SELECT
                tbl_locations.PkLocID as dist_id,
                tbl_locations.LocName as district,
                Sum(IFNULL(tbl_hf_data.new, 0) + IFNULL(tbl_hf_data.old, 0)) AS `value`

                FROM
                tbl_hf_data
                INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                WHERE
                        tbl_warehouse.prov_id = '$province_id'
                AND tbl_warehouse.stkid = 1
               $where_district
                AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                ) BETWEEN '$from_date'
                AND '$to_date'
                GROUP BY
                        tbl_warehouse.dist_id";
        } else if ($indicator == 'surgeriesreferred') {

            $query = "SELECT
                        tbl_locations.PkLocID as dist_id,
                        tbl_locations.LocName as district,
                        SUM(

                        IF (
                        tbl_warehouse.hf_type_id IN (4)
                        AND itminfo_tab.itm_category = 2,
                        (

                        IF (
                        tbl_hf_data_reffered_by.hf_type_id IN (4),
                        tbl_hf_data_reffered_by.ref_surgeries,
                        0
                        )
                        ),
                        tbl_hf_data.issue_balance
                        )
                        ) AS `value`
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                        INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data_reffered_by.hf_data_id = tbl_hf_data.pk_id
                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                        WHERE
                        tbl_warehouse.prov_id = '$province_id' AND
                        tbl_warehouse.stkid = 1 AND
                        tbl_warehouse.hf_type_id = 4
                        $where_district
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        GROUP BY
                        tbl_warehouse.dist_id";
        } else if ($indicator == 'surgeriesperformed') {

            $query = "SELECT
                        tbl_locations.PkLocID as dist_id,
                        tbl_locations.LocName as district,
                        SUM(tbl_hf_data_reffered_by.static) + SUM(tbl_hf_data_reffered_by.camp) AS `value`
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                        INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data_reffered_by.hf_data_id = tbl_hf_data.pk_id
                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                        WHERE
                        tbl_warehouse.prov_id = '$province_id' AND
                        tbl_warehouse.stkid = 1 AND
                        tbl_warehouse.hf_type_id = 4
                        $where_district
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        GROUP BY
                        tbl_warehouse.dist_id";
        }

        $rs = mysql_query($query) or die(print mysql_error());
        $rows = array();
        while ($r = mysql_fetch_assoc($rs)) {
            $rows[] = $r;
        }
        print json_encode($rows
        );
    }

    function getFacilityWiseAggregate($indicator, $from_date, $to_date, $commodities, $datasource, $tier, $dist_id, $province_id) {
        if (!empty($dist_id)) {
            $where_district = "AND tbl_warehouse.dist_id = $dist_id";
            $where_district2 = "AND tbl_locations.PkLocID = $dist_id";
            $where_district3 = " AND summary_district.district_id = $dist_id";
        } else {
            $where_district = "";
            $where_district2 = "";
            $where_district3 = "";
        }


        if ($datasource == 'pwd') {
            $stk = "tbl_warehouse.stkid = '1' AND ";
            $stk1 = "summary_province.stakeholder_id = '1' AND ";
        } else if ($datasource == 'lhw') {
            $stk = "tbl_warehouse.stkid = '2' AND ";
            $stk1 = "summary_province.stakeholder_id = '2' AND ";
        } else if ($datasource == 'dohhf') {
            $stk = "tbl_warehouse.stkid = '7' AND ";
            $stk1 = "summary_province.stakeholder_id = '7' AND ";
        } else if ($datasource == 'pphihf') {
            $stk = "tbl_warehouse.stkid = '9' AND ";
            $stk1 = "summary_province.stakeholder_id = '9' AND ";
        } else if ($datasource == 'cmw') {
            $stk = "tbl_warehouse.stkid = '111' AND ";
            $stk1 = "summary_province.stakeholder_id = '111' AND ";
        } else if ($datasource == 'all') {
            $stk = "tbl_warehouse.stkid IN ('1','2','7','9','111')  AND ";
            $stk1 = "summary_province.stakeholder_id IN ('1','2','7','9','111')  AND ";
        } else {
            $stk = "";
            $stk1 = "";
        }

        if ($commodities == 'condom') {
            $commodities_where = "AND summary_district.item_id = 'IT-001'";
            $commodities_where1 = "AND tbl_wh_data.item_id = 'IT-001'";
            $commodities_where2 = "AND tbl_hf_data.item_id = '1'";
            $commodities_where3 = "AND summary_province.item_id = 'IT-001'";
        } else if ($commodities == 'oralpills') {
            $commodities_where = "AND summary_district.item_id IN ('IT-002', 'IT-009')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-002', 'IT-009')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('2', '9')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-002', 'IT-009')";
        } else if ($commodities == 'ecp') {
            $commodities_where = "AND summary_district.item_id = 'IT-003'";
            $commodities_where1 = "AND tbl_wh_data.item_id = 'IT-003'";
            $commodities_where2 = "AND tbl_hf_data.item_id = '3'";
            $commodities_where3 = "AND summary_province.item_id = 'IT-003'";
        } else if ($commodities == 'iucd') {
            $commodities_where = "AND summary_district.item_id IN ('IT-004', 'IT-005')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-004', 'IT-005')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('4', '5')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-004', 'IT-005')";
        } else if ($commodities == 'injectables') {
            $commodities_where = "AND summary_district.item_id IN ('IT-006', 'IT-007')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-006', 'IT-007')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('6', '7')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-006', 'IT-007')";
        } else if ($commodities == 'implants') {
            $commodities_where = "AND summary_district.item_id IN ('IT-008', 'IT-013')";
            $commodities_where1 = "AND tbl_wh_data.item_id IN ('IT-008', 'IT-013')";
            $commodities_where2 = "AND tbl_hf_data.item_id IN ('8', '13')";
            $commodities_where3 = "AND summary_province.item_id IN ('IT-008', 'IT-013')";
        }

        if ($indicator == "commoditiesdistributed") {

            $query = "SELECT
                    tbl_warehouse.wh_id as facility_id,
                    tbl_warehouse.wh_name as facility,
                    SUM(tbl_hf_data.issue_balance) as value
                    FROM
                    tbl_hf_data
                    INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                    WHERE
                    $stk
                     tbl_warehouse.prov_id = '$province_id'
                    $where_district
                    AND DATE_FORMAT(
                    tbl_hf_data.reporting_date,
                    '%Y-%m'
                    ) BETWEEN '$from_date'
                    AND '$to_date'
                    $commodities_where2
                    GROUP BY
                    tbl_warehouse.wh_id";
        } else if ($indicator == "commoditiesdistibutedfpclients") {

            $query = "SELECT

                        tbl_warehouse.wh_id as facility_id,
                        tbl_warehouse.wh_name as facility,
                        SUM(tbl_hf_data.received_balance) as value
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        WHERE
                        tbl_warehouse.stkid = '1' AND 
                         tbl_warehouse.prov_id = '$province_id'
                        $where_district
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        $commodities_where2
                        GROUP BY
                        tbl_warehouse.wh_id";
        } else if ($indicator == "commoditiesconsumed") {

            $query = "SELECT
                        tbl_warehouse.wh_id as facility_id,
                        tbl_warehouse.wh_name as facility,
                        SUM(tbl_hf_data.issue_balance) as value
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        WHERE
                        $stk
                         tbl_warehouse.prov_id = '$province_id'
                        $where_district
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        $commodities_where2
                        GROUP BY
                        tbl_warehouse.wh_id";
        } if ($indicator == 'commoditiesreceived') {

            $query = "SELECT

                        tbl_warehouse.wh_id as facility_id,
                        tbl_warehouse.wh_name as facility,
                        SUM(tbl_hf_data.received_balance) as value
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        WHERE
                        $stk
                         tbl_warehouse.prov_id = '$province_id'
                        $where_district
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        $commodities_where2
                        GROUP BY
                        tbl_warehouse.wh_id";
        } else if ($indicator == 'fpclientsatpwd') {


            $query = "SELECT
                        tbl_warehouse.wh_id as facility_id,
                        tbl_warehouse.wh_name as facility,
                        SUM(
                        IFNULL(tbl_hf_data.new, 0) + IFNULL(tbl_hf_data.old, 0)
                        ) AS `value`
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        WHERE
                        tbl_warehouse.prov_id = '$province_id' AND
                        tbl_warehouse.stkid = 1
                        $where_district
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        GROUP BY
                        tbl_warehouse.wh_id";
        } else if ($indicator == 'surgeriesreferred') {

            $query = "SELECT
                        tbl_warehouse.wh_id as facility_id,
                        tbl_warehouse.wh_name as facility,
                        SUM(
                         IF (
                        tbl_warehouse.hf_type_id IN (4)
                        AND itminfo_tab.itm_category = 2,
                        (

                        IF (
                        tbl_hf_data_reffered_by.hf_type_id IN (4),
                        tbl_hf_data_reffered_by.ref_surgeries,
                        0
                        )
                        ),
                        tbl_hf_data.issue_balance
                        )
                        ) AS `value`
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                        INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data_reffered_by.hf_data_id = tbl_hf_data.pk_id
                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                        WHERE
                        tbl_warehouse.prov_id = '$province_id' AND
                        tbl_warehouse.stkid = 1 AND
                        tbl_warehouse.hf_type_id = 4
                        $where_district
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        GROUP BY
                        tbl_warehouse.wh_id";
        } else if ($indicator == 'surgeriesperformed') {

            $query = "SELECT
                        tbl_warehouse.wh_id as facility_id,
                        tbl_warehouse.wh_name as facility,
                        SUM(tbl_hf_data_reffered_by.static) + SUM(tbl_hf_data_reffered_by.camp) AS `value`
                        FROM
                        tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                        INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data_reffered_by.hf_data_id = tbl_hf_data.pk_id
                        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                        WHERE
                        tbl_warehouse.prov_id = '$province_id' AND
                        tbl_warehouse.stkid = 1 AND
                        tbl_warehouse.hf_type_id = 4
                        $where_district
                        AND DATE_FORMAT(
                        tbl_hf_data.reporting_date,
                        '%Y-%m'
                        ) BETWEEN '$from_date'
                        AND '$to_date'
                        GROUP BY
                        tbl_warehouse.wh_id";
        }

        $rs = mysql_query($query) or die(print mysql_error());
        $rows = array();
        while ($r = mysql_fetch_assoc($rs)) {
            $rows[] = $r;
        }
        print json_encode($rows
        );
    }

    public function getComplainceReportSubmission($indicator, $month, $year, $stakeholder, $datasource, $province_id) {
        $rpt_date = $year . '-' . str_pad($month, 2, "0", STR_PAD_LEFT) . '-' . '01';


        $query = "SELECT   B.provinceId,
                        B.province,
                        B.districtId,
                        B.district,
                        B.stkMain,
                        B.stkOffice,
                        B.wh_id,
                        B.wh_name,
                        B.wh_rank,
                        DATE_FORMAT(A.add_date, '%Y-%m-%d') AS add_date,
                        CONCAT(
                        DATE_FORMAT(A.last_update, '%d/%m/%Y'),
                        ' ',
                        TIME_FORMAT(
                        A.last_update,
                        '%h:%i:%s %p'
                        )
                        ) AS last_update,
                        A.ip_address
                        FROM
                        (
                        SELECT
                        tbl_warehouse.wh_id,
                        tbl_warehouse.wh_name,
                        tbl_warehouse.dist_id,
                        tbl_warehouse.prov_id,
                        tbl_warehouse.stkid,
                        tbl_warehouse.stkofficeid,
                        tbl_warehouse.wh_rank,
                        tbl_wh_data.add_date,
                        tbl_wh_data.last_update,
                        tbl_wh_data.ip_address
                        FROM
                        tbl_warehouse
                        INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                        INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                        WHERE
                        tbl_wh_data.report_month = '$month'
                        AND tbl_wh_data.report_year = '$year'
                        AND tbl_wh_data.item_id = 'IT-001'
                        AND tbl_warehouse.prov_id = '$province_id'
                        AND stakeholder.lvl IN (3, 4, 7)
						AND stakeholder.stk_type_id = 0
                        GROUP BY
                        tbl_warehouse.wh_id
                        UNION
                        SELECT
                        tbl_warehouse.wh_id,
                        tbl_warehouse.wh_name,
                        tbl_warehouse.dist_id,
                        tbl_warehouse.prov_id,
                        tbl_warehouse.stkid,
                        tbl_warehouse.stkofficeid,
                        tbl_warehouse.wh_rank,
                        tbl_hf_data.created_date AS add_date,
                        tbl_hf_data.last_update,
                        tbl_hf_data.ip_address
                        FROM
                        tbl_warehouse
                        INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                        INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                        WHERE
                        MONTH (tbl_hf_data.reporting_date) = '$month'
                        AND YEAR (tbl_hf_data.reporting_date) = '$year'
                        AND tbl_hf_data.item_id = 1
                        AND tbl_warehouse.prov_id = '$province_id'
                        AND stakeholder.lvl IN (3, 4, 7)
						AND stakeholder.stk_type_id = 0
                        GROUP BY
                        tbl_warehouse.wh_id
                        ) A
                        RIGHT JOIN (
                        SELECT DISTINCT
                        tbl_warehouse.wh_id,
                        tbl_warehouse.wh_name,
                        tbl_warehouse.dist_id,
                        tbl_warehouse.prov_id,
                        tbl_warehouse.stkid,
                        tbl_warehouse.stkofficeid,
                        tbl_warehouse.wh_rank,
                        MainStk.stkorder,
                        MainStk.stkname AS stkMain,
                        stakeholder.stkname AS stkOffice,
                        District.PkLocID AS districtId,
                        District.LocName AS district,
                        Province.PkLocID AS provinceId,
                        Province.LocName AS province
                        FROM
                        tbl_warehouse
                        INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                        INNER JOIN stakeholder AS MainStk ON tbl_warehouse.stkid = MainStk.stkid
                        INNER JOIN tbl_locations AS District ON tbl_warehouse.dist_id = District.PkLocID
                        INNER JOIN tbl_locations AS Province ON tbl_warehouse.prov_id = Province.PkLocID
                        WHERE
                        tbl_warehouse.wh_id NOT IN (
                        SELECT
                        warehouse_status_history.warehouse_id
                        FROM
                        warehouse_status_history
                        INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                        WHERE
                        warehouse_status_history.reporting_month = '$rpt_date'
                        AND warehouse_status_history.`status` = 0
                        )
                        
                        AND tbl_warehouse.prov_id = '$province_id'
                        AND stakeholder.lvl IN (3, 4, 7)
						AND stakeholder.stk_type_id = 0
                        ) B ON A.wh_id = B.wh_id
                        AND A.prov_id = B.prov_id
                        AND A.dist_id = B.dist_id
                        AND A.stkid = B.stkid
                        AND A.stkofficeid = B.stkofficeid
                        ORDER BY
                        B.provinceId ASC,
                        B.district ASC,
                        B.stkorder ASC,
                        IF (
                        A.wh_rank = ''
                        OR A.wh_rank IS NULL,
                        1,
                        0
                        ),
                        A.wh_rank,
                        A.wh_name ASC";

        $totalWH = array();


        $reportedAll = array();

        $nonReported = array();

        $rs = mysql_query($query) or die(print mysql_error());
        $rows = array();
        while ($row = mysql_fetch_assoc($rs)) {

            $rows[$row['districtId']]['dist_id'] = $row['districtId'];
            $rows[$row['districtId']]['district'] = $row['district'];

            $rows[$row['districtId']]['denominator'] = ++$totalWH[$row['districtId']];

            if (!empty($row['add_date'])) {
                $rows[$row['districtId']]['numerator'] = ++$reportedAll[$row['districtId']];
            } //else {
                //$rows[$row['districtId']]['numerator'] = 0;
            //}
            $rows[$row['districtId']]['compliance'] = round(($rows[$row['districtId']]['numerator'] / $rows[$row['districtId']]['denominator']) * 100, 2);
        }
		
        $result = array();
        foreach ($rows as $dis_id => $sub_arr) {
            $result[] = $sub_arr;
        }
        print json_encode($result
        );
    }

    function getAllDistricts() {
        $query = "SELECT
                        dis.LocName as province,
                        tbl_locations.PkLocID as dist_id,
                        tbl_locations.LocName as district

                        FROM
                        tbl_locations AS dis
                        INNER JOIN tbl_locations ON tbl_locations.ParentID = dis.PkLocID
                        WHERE
                        tbl_locations.LocLvl = 3
                        ORDER BY province, district";

        $rs = mysql_query($query) or die(print mysql_error());
        $rows = array();
        while ($r = mysql_fetch_assoc($rs)) {
            $rows[] = $r;
        }
        print json_encode($rows);
    }

}

?>
