<?
/**
 * DateTime
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
function USA_DtTm()
	{
		$ctime=time();		
		$ctime=round($ctime);//7
		$BST_current_dt=date('Y-m-d',$ctime); //0
		$BST_current_time1=date('h:i:s A',$ctime);//1
		$BST_current_time2=date('H:i:s',$ctime);//2
		$BST_current_dt1=date ("jS F, Y",$ctime);//3
		$BST_id_d=date('Ymd',$ctime);//4
		$BST_id_d2= date("F j, Y",$ctime);//5
		$BST_unix_time=time();//6
		return array($BST_current_dt,$BST_current_time1,$BST_current_time2,$BST_current_dt1,$BST_id_d,$BST_id_d2,$BST_unix_time,$ctime);
	}

function GMT_DtTm()
	{
		$ctime=time();
		$calc=(60*60*6);
		$ctime=round($ctime-$calc);//7
		$GMT_current_dt=date('Y-m-d',$ctime); //0
		$GMT_current_time1=date('h:i:s A',$ctime);//1
		$GMT_current_time2=date('H:i:s',$ctime);//2
		$GMT_current_dt1=date ("jS F, Y",$ctime);//3
		$GMT_id_d=date('Ymd',$ctime);//4
		$GMT_id_d2= date("F j, Y",$ctime);//5
		$GMT_unix_time=time();//6
		return array($GMT_current_dt,$GMT_current_time1,$GMT_current_time2,$GMT_current_dt1,$GMT_id_d,$GMT_id_d2,$GMT_unix_time,$ctime);
	}

function BST_DtTm()
	{
		$ctime=time();
		$calc=(60*60*11);
		$ctime=round($ctime+$calc);//7
		$USA_current_dt=date('Y-m-d',$ctime); //0
		$USA_current_time1=date('h:i:s A',$ctime);//1
		$USA_current_time2=date('H:i:s',$ctime);//2
		$USA_current_dt1=date ("jS F, Y",$ctime);//3
		$USA_id_d=date('Ymd',$ctime);//4
		$USA_id_d2= date("F j, Y",$ctime);//5
		$USA_unix_time=time();//6
		return array($USA_current_dt,$USA_current_time1,$USA_current_time2,$USA_current_dt1,$USA_id_d,$USA_id_d2,$USA_unix_time,$ctime);
	}
?>