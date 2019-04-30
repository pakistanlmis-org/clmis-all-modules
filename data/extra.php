
    
    
     <?php
                      $start_date=NULL;$end_date=NULL;
                           $start_date=$_POST['start_date'];
                           $end_date=$_POST['end_date'];
                           
                            $query=$conn->query("SELECT * FROM `data` WHERE (Reporting_Date >='$start_date' AND Reporting_Date<='$end_date' AND )");
                             $rowcount=mysqli_num_rows($query);
                            if($rowcount>1)
                            {
                              ?>
    <div class="row">
                     
                    <button type="button" style="margin-bottom:10px !important;  " class="btn btn-default pull-right" onClick="tableToExcel('export', 'sheet 1', '<?php echo 'Data'; ?>')" alt="Excel" style="cursor:pointer;">Save Excel</button><br>
                </div>
                            
                                   <div  id="export">
                                    <table  id="myTable" >
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Year</th>
                                                <th>Month</th>
                                                <th>Reporting Date</th>
                                                <th>Stakeholder</th>
                                                <th>Province</th>
                                                <th>District</th>
                                                <th>Health Facility</th>
                                                <th>Item</th>
                                                <th>MOS</th>

                                           </tr>
                                        </thead>


                                        <tbody>


                                                       <?php     $no=1;

                                                      while($row =$query->fetch_assoc()){
                                                      ?>
                                                      <tr>
                                                          <td style="text-align: right;"><?php echo $no++?></td>
                                                        <td style="text-align:right;"><?php echo $row['Year']?></td>
                                                        <td style="text-align: right;"><?php echo $row['Month']?></td>
                                                        <td style="text-align: center;"><?php echo $row['Reporting_Date']?></td>
                                                        <td style="text-align: center;"><?php echo $row['Stakeholder']?></td>
                                                        <td style="text-align: center;"><?php echo $row['Province']?></td>
                                                        <td style="text-align: center;"><?php echo $row['District']; ?></td>
                                                        <td style="text-align: center;"><?php echo $row['Health_Facility']; ?></td>
                                                        <td style="text-align: center;"><?php echo $row['Item']; ?></td>
                                                        <td style="text-align: right;"><?php echo $row['MOS']; ?></td>
                                                      </tr>
                                                      <?php 
                                                      }
                                                      ?>


                                        </tbody>
                                    </table>
                                        </div>
                                  
                                  
                             <?php  
                            } 
                            ?>
    