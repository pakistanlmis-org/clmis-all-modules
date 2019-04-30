<?php 
/**
 * cCms
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
/*********************************************************
	Author:				Syed Aun Irtaza
	Created Date:		26/09/2009
	Purpose:			Class for cms table
	*********************************************************/
	 class cCms
	 {
	 	private $id;
		private $title;
		private $description;
		private $ministry; 
			
		/*********************************************************
			Purpose:	For Assigning default values
		*********************************************************/
		function __construct(){				
			
		$this->id=0;
		$this->title="";
		$this->description=""; 
		$this->ministry="";
						
		}
		/////////       Setter n Getter     ///////////////////
		/**
                 * fnSetId
                 * 
                 * @param type $var
                 */
		function fnSetId($var)
		{
			$this->id=$var;	
		}
                /**
                 * fnGetId
                 * 
                 * @return type
                 */
		function fnGetId()
		{
			return $this->id;	
		}
		/**
                 * fnSetTitle
                 * 
                 * @param type $var
                 */
		function fnSetTitle($var)
		{
			$this->title=$var;	
		}
                /**
                 * fnGetTitle
                 * 
                 * @return type
                 */
		function fnGetTitle()
		{
			return $this->title;	
		}
		/**
                 * fnSetDescription
                 * 
                 * @param type $var
                 */
		function fnSetDescription($var)
		{
			$this->description=$var;	
		}
                /**
                 * fnGetDescription
                 * 
                 * @return type
                 */
		function fnGetDescription()
		{
			return $this->description;	
		}
                /**
                 * fnSetMinistry
                 * 
                 * @param type $var
                 */
		function fnSetMinistry($var)
		{
			$this->ministry=$var;	
		}
		
		////////////////////////////////////////////////
		
		///// validation/////
                /**
                 * validate
                 * 
                 * @return string
                 */
		function validate(){
			if(empty($this->title)){
				$error = "&bull;&nbsp;&nbsp;Title for content is required.";
			}
			if(empty($this->description)){
				$error .= "<br />&bull;&nbsp;&nbsp;Description about content is required.";
			}
			return $error;
		}
		
		////////////  DML Functions   //////////////////
                /**
                 * Add
                 * 
                 * @param type $table
                 * @return string
                 */
		function Add($table){
			$sql = "INSERT INTO ".$table." SET
					title			='".mysql_real_escape_string($this->title)."',
					stkid			='".mysql_real_escape_string($this->ministry)."',
					description		='".htmlspecialchars($this->description)."'";	
			return $sql;	
			
		}
                
		/**
                 * Update
                 * 
                 * @param type $table
                 * @param type $id
                 * @return string
                 */
		function Update($table,$id){
			 	$sql = "Update ".$table." SET
					title			='".mysql_real_escape_string($this->title)."',
					stkid			='".mysql_real_escape_string($this->ministry)."',
					description		='".htmlspecialchars($this->description)."'
					Where id		= ".mysql_real_escape_string($id).";";
			return $sql;
			
		}
		
		/**
                 * SqlCount
                 * 
                 * @param type $table
                 * @param type $whr_cls
                 * @return string
                 */
		function SqlCount($table,$whr_cls=""){
			$sql="SELECT 1 FROM ".$table." Where 1=1 ".$whr_cls.";";
			return $sql;
		}
		/**
                 * Select
                 * 
                 * @param type $table
                 * @param type $col
                 * @param type $whr_cls
                 * @return string
                 */
		function Select($table,$col,$whr_cls=""){
			$sql="SELECT ".$col." FROM ".$table." Where 1=1 ".$whr_cls;
			return $sql;
		}
		/**
                 * Delete
                 * 
                 * @param type $table
                 * @param type $whr_cls
                 * @return string
                 */
		function Delete($table,$whr_cls){
			$sql="DELETE FROM ".$table." Where 1=1 ".$whr_cls.";";
			return $sql;
		}
		

}	
?>