<?php

/**
 * clsStockMaster
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
class clsAlerts {
	
    public function count_stockout_alerts() {
        $from_date = date('Y-m-01',strtotime('-1 month'));  
        
	$qry =  "SELECT
                tbl_warehouse.stkid,
                tbl_hf_data.item_id ,
                itminfo_tab.itm_name,
                count(tbl_hf_data.pk_id) as stock_outs
                    FROM
                            tbl_warehouse
                    INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                    INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                    INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                    WHERE
                            stakeholder.lvl = 7
                    AND tbl_warehouse.prov_id = ".$_SESSION['user_province1']."
                    AND tbl_warehouse.dist_id = ".$_SESSION['user_district']."
                    AND tbl_warehouse.stkid = ".$_SESSION['user_stakeholder1']."
                    AND tbl_hf_type_rank.province_id = ".$_SESSION['user_province1']."

                    AND tbl_warehouse.wh_id NOT IN (
                            SELECT
                                    warehouse_status_history.warehouse_id
                            FROM
                                    warehouse_status_history
                            INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
                            WHERE
                                    warehouse_status_history.reporting_month = '".$from_date."'
                            AND warehouse_status_history.`status` = 0

                    )
                    AND tbl_hf_data.reporting_date = '".$from_date."'

                    AND tbl_hf_type.pk_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
                    AND  IFNULL(ROUND( (tbl_hf_data.closing_balance / tbl_hf_data.avg_consumption), 2 ),0) = 0
                    AND itminfo_tab.itm_category = 1
                    AND itminfo_tab.itm_id NOT IN(4,6,10,33)
                
                ORDER BY 
                tbl_warehouse.stkid,
                tbl_hf_data.item_id 
        ";
        //echo $qry;exit;
        $result_set = mysql_query($qry);
		//$n = mysql_num_rows($result_set);
        $row = mysql_fetch_assoc($result_set);
        $n = $row['stock_outs'];
        
        return $n;
    }
   
    public function count_expiry_alerts() {
	$qry = "SELECT
                                       stock_batch.batch_no
                               FROM
                                       stock_batch
                               INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
                               INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                                INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                               WHERE
                                       stock_batch.Qty <> 0 AND
                                       stock_batch.wh_id = '" . $_SESSION['user_warehouse'] . "' AND
                                       tbl_stock_detail.temp = 0
                                       AND stock_batch.batch_expiry > now()
                                        AND stock_batch.batch_expiry <= DATE_ADD(NOW(),INTERVAL 1 YEAR)
                               GROUP BY
                                    stock_batch.item_id,
                                    stock_batch.batch_no
                               ";
        $result_set = mysql_query($qry);
		$n = mysql_num_rows($result_set);
        return $n;
    }
   
    public function count_shipment_alerts() {
        $and= "";
        if(isset($_SESSION['user_level']) && $_SESSION['user_level'] > 1 && isset($_SESSION['user_province1']))
            $and = " AND shipments.procured_by = ".$_SESSION['user_province1']." ";
        
        if(isset($_SESSION['user_level']) && $_SESSION['user_level'] > 1 && isset($_SESSION['user_stakeholder1']))
            $and .= " AND funding_stk_prov.stakeholder_id = ".$_SESSION['user_stakeholder1']." ";
        
	$qry = "SELECT
					shipments.pk_id

				FROM
					shipments
				INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
				INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
				INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
				INNER JOIN funding_stk_prov ON shipments.stk_id = funding_stk_prov.funding_source_id
                                WHERE
					shipments.shipment_date > now() " .$and;
        $result_set = mysql_query($qry);
		$n = mysql_num_rows($result_set);
        return $n;
    }
   
   
   	
    public function get_stockout_alerts() {
    $qry = "SELECT
			itminfo_tab.itm_name,
			itminfo_tab.qty_carton,
			SUM(stock_batch.Qty) AS Vials,
			tbl_itemunits.UnitType
		FROM
			stock_batch
		INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
		INNER JOIN tbl_itemunits ON itminfo_tab.itm_type = tbl_itemunits.UnitType
		WHERE
			stock_batch.`wh_id` = '" . $_SESSION['user_warehouse'] . "'
		GROUP BY
			itminfo_tab.itm_id
		ORDER BY
			itminfo_tab.frmindex";
        $qryRes = mysql_query($qry);
        return $qryRes;
    }
   
    public function get_expiry_alerts() {
	$qry = "SELECT
				   stock_batch.batch_no,
				   stock_batch.batch_id,
				   stock_batch.batch_expiry,
				   stock_batch.item_id,                               
					itminfo_tab.itm_name,
				   SUM(tbl_stock_detail.Qty) as Qty,
				   itminfo_tab.qty_carton,
				   stock_batch.funding_source,
					stock_batch.manufacturer,
					tbl_warehouse.wh_name,
					(select stkname from stakeholder where stock_batch.manufacturer = stakeholder.stkid) AS manufacturer_name,
					tbl_stock_detail.manufacturer,
					stock_batch.`status`
		   FROM
				   stock_batch
		   INNER JOIN tbl_stock_detail ON stock_batch.batch_id = tbl_stock_detail.BatchID
		   INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
			INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
		   WHERE
				   stock_batch.Qty <> 0 AND
				   stock_batch.wh_id = '" . $_SESSION['user_warehouse'] . "' AND
				   tbl_stock_detail.temp = 0
				   AND stock_batch.batch_expiry > now()
					AND stock_batch.batch_expiry <= DATE_ADD(NOW(),INTERVAL 1 YEAR)
		   GROUP BY
				stock_batch.item_id,
				stock_batch.batch_no
		   ORDER BY
				stock_batch.item_id,
				stock_batch.batch_no
                               ";
        //echo $qry;exit;
        $qryRes = mysql_query($qry);
        return $qryRes;
    }
   
    public function get_shipment_alerts() {
        $and= "";
        if(isset($_SESSION['user_level']) && $_SESSION['user_level'] > 1 && isset($_SESSION['user_province1']))
            $and .= " AND shipments.procured_by = ".$_SESSION['user_province1']." ";
        
        if(isset($_SESSION['user_level']) && $_SESSION['user_level'] > 1 && isset($_SESSION['user_stakeholder1']))
            $and .= " AND funding_stk_prov.stakeholder_id = ".$_SESSION['user_stakeholder1']." ";
        
	$qry = "SELECT
                                shipments.pk_id,
                                shipments.item_id,
                                shipments.shipment_date,
                                shipments.shipment_quantity,
                                shipments.stk_id,
                                shipments.procured_by,
                                shipments.`status`,
                                tbl_locations.LocName,
                                tbl_warehouse.wh_name,
                                itminfo_tab.itm_name,
                                itminfo_tab.qty_carton

                            FROM
                                shipments
                            INNER JOIN tbl_locations ON shipments.procured_by = tbl_locations.PkLocID
                            INNER JOIN tbl_warehouse ON shipments.stk_id = tbl_warehouse.wh_id
                            INNER JOIN itminfo_tab ON shipments.item_id = itminfo_tab.itm_id
                            INNER JOIN funding_stk_prov ON shipments.stk_id = funding_stk_prov.funding_source_id

                            WHERE
                                shipments.shipment_date > now() 
                                ".$and;
       $qryRes = mysql_query($qry);
        return $qryRes;
    }
   
}

?>