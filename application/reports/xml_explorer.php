<?php

/**
 * xml_explorer
 * @package reports
 * 
 * @author     Ajmal Hussain 
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Query for explorer
//This query returns
//wh_name,
//hf_type_id,
//stkname,
//stakeholder.lvl,
//itm_name,
//itm_category,
//frmindex,
//wh_obl_a,
//wh_received,
//wh_issue_up,
//wh_adja,
//wh_adjb,
//wh_cbl_a,
//last_update,
//wh_rank,
//hf_type_rank,
//district,
//Province.PkLocID,
//province,

$query_xmlw = "SELECT
					*
				FROM
					(
						SELECT
							tbl_warehouse.wh_name,
							tbl_warehouse.hf_type_id,
							stakeholder.stkname,
							stakeholder.lvl,
							itminfo_tab.itm_name,
							itminfo_tab.itm_category,
							itminfo_tab.frmindex,
							tbl_wh_data.wh_obl_a,
							tbl_wh_data.wh_received,
							tbl_wh_data.wh_issue_up,
							tbl_wh_data.wh_adja,
							tbl_wh_data.wh_adjb,
							tbl_wh_data.wh_cbl_a,
							tbl_wh_data.last_update,
							tbl_warehouse.wh_rank,
							tbl_hf_type_rank.hf_type_rank,
							District.LocName AS district,
							Province.PkLocID,
							Province.LocName AS province,
							0 AS new,
							0 AS old,
                                                        tbl_product_category.ItemCategoryName
						FROM
							tbl_wh_data
						INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
						INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
						LEFT JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
						INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
						INNER JOIN tbl_locations AS District ON tbl_warehouse.dist_id = District.PkLocID
						INNER JOIN tbl_locations AS Province ON tbl_warehouse.prov_id = Province.PkLocID
                                                INNER JOIN tbl_product_category ON itminfo_tab.itm_category = tbl_product_category.PKItemCategoryID
						WHERE
							$where
						UNION
							SELECT
								tbl_warehouse.wh_name,
								tbl_warehouse.hf_type_id,
								stakeholder.stkname,
								stakeholder.lvl,
								itminfo_tab.itm_name,
								itminfo_tab.itm_category,
								itminfo_tab.frmindex,
								tbl_hf_data.opening_balance AS wh_obl_a,
								tbl_hf_data.received_balance AS wh_received,
								tbl_hf_data.issue_balance AS wh_issue_up,
								tbl_hf_data.adjustment_positive AS wh_adja,
								tbl_hf_data.adjustment_negative AS wh_adjb,
								tbl_hf_data.closing_balance AS wh_cbl_a,
								tbl_hf_data.last_update,
								tbl_warehouse.wh_rank,
								tbl_hf_type_rank.hf_type_rank,
								District.LocName AS district,
								Province.PkLocID,
								Province.LocName AS province,
								tbl_hf_data.new,
								tbl_hf_data.old,
                                                                tbl_product_category.ItemCategoryName
							FROM
								tbl_hf_data
							INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
							INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
							INNER JOIN tbl_hf_type_rank ON tbl_warehouse.hf_type_id = tbl_hf_type_rank.hf_type_id
							INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
							INNER JOIN tbl_locations AS District ON tbl_warehouse.dist_id = District.PkLocID
							INNER JOIN tbl_locations AS Province ON tbl_warehouse.prov_id = Province.PkLocID
                                                        INNER JOIN tbl_product_category ON itminfo_tab.itm_category = tbl_product_category.PKItemCategoryID
							WHERE
								$where1
								
					) A
				ORDER BY
					A.PkLocID,
					A.district,
					IF (A.wh_rank = '' OR A.wh_rank IS NULL, 1, 0),
					A.wh_rank,
				  	A.hf_type_rank ASC,
					A.frmindex ASC,
					A.wh_name ASC";
//echo $query_xmlw;exit;
//Qury result
$result_xmlw = mysql_query($query_xmlw);
//xml
$xmlstore = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xmlstore .= "<rows>";
//refCases
$refCases = 0;
$num = 1;
//numOfRows
$numOfRows = mysql_num_rows($result_xmlw);
//Checking if record exists
if ($numOfRows > 0) {
    //Populate xml
    while ($row_xmlw = mysql_fetch_array($result_xmlw)) {
        $lastModified = (empty($row_xmlw['last_update'])) ? '' : date('d/m/Y h:i A', strtotime($row_xmlw['last_update']));
        //PkLocID
        $province = $row_xmlw['PkLocID'];
        //Checking itm_category
        if ($row_xmlw['itm_category'] == 2) {
            $refCases += $row_xmlw['wh_issue_up'];
        }
        //Checking itm_category
        if ($row_xmlw['itm_category'] == 1 || $row_xmlw['itm_category'] == 5) {
            $xmlstore .= "<row>";
            $xmlstore .= "<cell>" . $num++ . "</cell>";
            //province
            $xmlstore .= "<cell><![CDATA[" . $row_xmlw['province'] . "]]></cell>";
            //district
            $xmlstore .= "<cell><![CDATA[" . $row_xmlw['district'] . "]]></cell>";
            //stkname
            $xmlstore .= "<cell><![CDATA[" . $row_xmlw['stkname'] . "]]></cell>";
            //wh_name
            $xmlstore .= "<cell><![CDATA[" . $row_xmlw['wh_name'] . "]]></cell>";
            //itm_name
            $xmlstore .= "<cell><![CDATA[" . $row_xmlw['itm_name'] . "]]></cell>";
            $xmlstore .= "<cell><![CDATA[" . $row_xmlw['ItemCategoryName'] . "]]></cell>";
            //wh_obl_a
            $xmlstore .= "<cell>" . number_format($row_xmlw['wh_obl_a']) . "</cell>";
            //wh_received
            $xmlstore .= "<cell>" . number_format($row_xmlw['wh_received']) . "</cell>";
            //wh_issue_up
            $xmlstore .= "<cell>" . number_format($row_xmlw['wh_issue_up']) . "</cell>";
            //wh_adja
            $xmlstore .= "<cell>" . number_format($row_xmlw['wh_adja']) . "</cell>";
            //wh_adjb
            $xmlstore .= "<cell>" . number_format($row_xmlw['wh_adjb']) . "</cell>";
            //wh_cbl_a
            $xmlstore .= "<cell>" . number_format($row_xmlw['wh_cbl_a']) . "</cell>";

            $lvl = $row_xmlw['lvl'];
            $type = $row_xmlw['hf_type_id'];
            if ($lvl == 7 || empty($sel_wh)) {
                $xmlstore .= "<cell>" . number_format($row_xmlw['new']) . "</cell>";
                $xmlstore .= "<cell>" . number_format($row_xmlw['old']) . "</cell>";
                $colspan = ',#cspan,#cspan';
                $header = ",<div style='text-align:center;'>Clients</div>,#cspan";
                $header1 = ",<div style='text-align:center;'>New</div>,<div style='text-align:center;'>Old</div>";
                $header2 = ',,';
                $width = ',*,*';
                $colAlign = ',right,right';
                $colType = ',ro,ro';
            } else {
                $colspan = '';
                $header = '';
                $header1 = '';
                $header2 = '';
                $width = '';
                $colAlign = '';
                $colType = '';
            }
            $xmlstore .= "<cell>" . $lastModified . "</cell>";
            $xmlstore .= "</row>";
        }
    }
}
if ($lvl == 7 && in_array($type, array(4, 5))) {
    // Get surgery Cases
    //This guery gets 
    //CS_Done
    //ante_natal
    //post_natal
    //ailment_children
    //ailment_adults
    //general_ailment
    $csQry = "SELECT
				SUM(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS CS_Done,
				(tbl_hf_mother_care.pre_natal_new + tbl_hf_mother_care.pre_natal_old) AS ante_natal,
				(tbl_hf_mother_care.post_natal_new + tbl_hf_mother_care.post_natal_old) AS post_natal,
				tbl_hf_mother_care.ailment_children,
				tbl_hf_mother_care.ailment_adults,
				tbl_hf_mother_care.general_ailment
			FROM
				tbl_hf_data
			INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
			INNER JOIN tbl_hf_mother_care ON tbl_hf_data.warehouse_id = tbl_hf_mother_care.warehouse_id
			AND tbl_hf_data.reporting_date = tbl_hf_mother_care.reporting_date
			WHERE
				tbl_hf_data.reporting_date = '" . $sel_year . "-" . $sel_month . "-01'
			AND tbl_hf_data.warehouse_id = $sel_wh";
    $csRows = mysql_query($csQry);
    $row = mysql_fetch_array($csRows);
    $CS_Done = !empty($row['CS_Done']) ? $row['CS_Done'] : 0;
    $ante_natal = !empty($row['ante_natal']) ? $row['ante_natal'] : 0;
    $post_natal = !empty($row['post_natal']) ? $row['post_natal'] : 0;
    $ailment_children = !empty($row['ailment_children']) ? $row['ailment_children'] : 0;
    $ailment_adults = !empty($row['ailment_adults']) ? $row['ailment_adults'] : 0;
    $general_ailment = !empty($row['general_ailment']) ? $row['general_ailment'] : 0;

    if ($province == 3) {
        $xmlstore1 = "Surgery Cases(Reffered): $refCases  Surgery Cases(Performed): $CS_Done Ante-natal: $ante_natal Post-natal: $post_natal Children: $ailment_children Adults: $ailment_adults";
    } else {
        $xmlstore1 = "Surgery Cases(Reffered): $refCases  Surgery Cases(Performed): $CS_Done Ante-natal: $ante_natal Post-natal: $post_natal Children: " . ($ailment_children + $ailment_adults) . " General Ailment: $general_ailment";
    }
}
//End xml
$xmlstore .= "</rows>";
?>