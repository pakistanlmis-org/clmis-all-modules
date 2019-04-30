<?php
$nstk_id = $_SESSION['user_stakeholder1'];
$nprov_id = $_SESSION['user_province1'];
//echo $_SESSION['user_level'];
$getsrcsqry = "SELECT
        list_detail.pk_id,
	list_detail.list_value
FROM
	stock_sources
INNER JOIN list_detail ON stock_sources.stock_source_id = list_detail.pk_id
WHERE
	stock_sources.stakeholder_id = ".$_SESSION['user_stakeholder1']."
AND stock_sources.province_id = ".$_SESSION['user_province1']." "
        . "AND DATE_FORMAT(stock_sources.created_date,'%Y-%m') <= '$RptDate' AND stock_sources.lvl = ".$wh_lvl." "
        . "ORDER By list_detail.rank";
//query result
//echo $getsrcsqry;exit;
$getsrcRst = mysql_query($getsrcsqry);
$sources = array();
if (mysql_num_rows($getsrcRst) > 0) {
    while ($getsrcdata = mysql_fetch_array($getsrcRst)) {
        $sources[$getsrcdata['pk_id']] = $getsrcdata['list_value'];
    }
}
//echo '<pre>';print_r($sources);exit;
$src_count = count($sources);
//echo 'SSS:'.$src_count;
?>