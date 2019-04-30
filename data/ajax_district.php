<?php
include("db.php");
//include ("loaddata.php");
include("../application/includes/classes/AllClasses.php");
//$province_id='';
?>
    <?php
if(!empty($_REQUEST['province_id']))
  $province_id=implode(',',$_REQUEST['province_id']);
  ?>
<label for="district" >District:</label>                       
    <select id="district" name="district" style="width:150px" class=" form-control input-sm "  multiple="multiple">
                             <?php 
  if(!empty($province_id)){                       
$query = $conn->query("SELECT
                                                    tbl_locations.PkLocID,
                                                    tbl_locations.LocName
                                                    FROM
                                                    tbl_locations
                                                    WHERE
                                                    tbl_locations.ParentID != 10 
                                                    AND tbl_locations.ParentID IN($province_id)
                                                    ORDER BY tbl_locations.LocName ASC
                                                    ");
//print_r($query);
                                                        while ($row = $query->fetch_assoc()) {
                                                            $pk_id = $row["PkLocID"];
                                                            $dist_name = $row["LocName"];
                                                            ?>
                                                            <option value= <?php echo $pk_id; ?> > <?php echo $dist_name; ?> </option>
                                                        <?php }
                                                        
  }else{
      $query = $conn->query("SELECT
                                                    tbl_locations.PkLocID,
                                                    tbl_locations.LocName
                                                    FROM
                                                    tbl_locations
                                                    WHERE
                                                    tbl_locations.ParentID != 10 
                                                    
                                                    ORDER BY tbl_locations.LocName ASC
                                                    ");
//print_r($query);
                                                        while ($row = $query->fetch_assoc()) {
                                                            $pk_id = $row["PkLocID"];
                                                            $dist_name = $row["LocName"];
                                                            ?>
                                                            <option value= <?php echo $pk_id; ?> > <?php echo $dist_name; ?> </option>
                                                        <?php } 
  }
                                                        
  ?>
</select> 
   <script src="ms.js"></script> 
  <script type="text/javascript">
  $('.multiselect-ui').multiselect({
                                                                includeSelectAllOption: false
                                                            });
  
  </script>


