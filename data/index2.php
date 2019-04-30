<?php
include("db.php");
//include ("loaddata.php");
include("../application/includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
?>
<!DOCTYPE html>


<html>
    <head>  
         <link rel="stylesheet" type="text/css" href="select2.css"/>
        <style>
            span.multiselect-native-select {
                position: relative
            }
            span.multiselect-native-select select {
                border: 0!important;
                clip: rect(0 0 0 0)!important;
                height: 1px!important;
                margin: -1px -1px -1px -3px!important;
                overflow: hidden!important;
                padding: 0!important;
                position: absolute!important;
                width: 1px!important;
                left: 50%;
                top: 30px
            }
            .multiselect-container {
                position: absolute;
                list-style-type: none;
                margin: 0;
                padding: 0
            }
            .multiselect-container .input-group {
                margin: 5px
            }
            .multiselect-container>li {
                padding: 0
            }
            .multiselect-container>li>a.multiselect-all label {
                font-weight: 700
            }
            .multiselect-container>li.multiselect-group label {
                margin: 0;
                padding: 3px 20px 3px 20px;
                height: 100%;
                font-weight: 700
            }
            .multiselect-container>li.multiselect-group-clickable label {
                cursor: pointer
            }
            .multiselect-container>li>a {
                padding: 0
            }
            .multiselect-container>li>a>label {
                margin: 0;
                height: 100%;
                cursor: pointer;
                font-weight: 400;
                padding: 3px 0 3px 30px
            }
            .multiselect-container>li>a>label.radio, .multiselect-container>li>a>label.checkbox {
                margin: 0
            }
            .multiselect-container>li>a>label>input[type=checkbox] {
                margin-bottom: 5px
            }
            .btn-group>.btn-group:nth-child(2)>.multiselect.btn {
                border-top-left-radius: 4px;
                border-bottom-left-radius: 4px
            }
            .form-inline .multiselect-container label.checkbox, .form-inline .multiselect-container label.radio {
                padding: 3px 20px 3px 40px
            }
            .form-inline .multiselect-container li a label.checkbox input[type=checkbox], .form-inline .multiselect-container li a label.radio input[type=radio] {
                margin-left: -20px;
                margin-right: 0
            }
            table#myTable{width: 96% !important; margin-left:2% !important; margin-right: 2% !important;}
            table#myTable{margin-top:0px !important; color: #000;}
            table#myTable{margin-top:20px;border-collapse: collapse;border-spacing: 0; border:1px solid #999;}
            table#myTable tr td{font-size:11px;padding:3px; text-align:left; border:1px solid #999; color: #000;}
            table#myTable tr th{font-size:11px;padding:3px; text-align:center; border:1px solid #999; color: #000;}
            table#myTable tr td.TAR{text-align:right; padding:5px;width:50px !important;}
            .sb1NormalFont {color: #444444; font-size: 11px; font-weight: bold; text-decoration: none;}
            p{margin-bottom:5px; font-size:11px !important; line-height:1 !important; padding:0 !important; color: #000;}
            table#headerTable tr td{ font-size:11px; color: #000;}
            h4{margin:0; color: #000; font-size:14px;}
            h5{margin:15px 0 5px 0; color: #000;}
            h6{margin:0; color: #000; font-size:12px;}
            .right{text-align:right !important;}
            .center{text-align:center !important;}

            /* Print styles */
            @media only print
            {
                table#myTable{margin-top:0px !important;}
                table#myTable tr th{font-size:8px;padding:3px !important; text-align:center; border:1px solid #999; color: #000;}
                table#myTable tr td{font-size:8px;padding:3px !important; text-align:left; border:1px solid #999; color: #000;}
                #doNotPrint{display: none !important;}
                h4{margin:0; color: #000;}
                h5{margin:0; color: #000;}
                h6{margin:0; color: #000;}
                p{margin-bottom:5px; font-size:11px !important; line-height:1 !important; padding:0 !important; color: #000;}    
            }
        </style>
        <script>
            function printContents() {
                var w = 900;
                var h = screen.height;
                var left = Number((screen.width / 2) - (w / 2));
                var top = Number((screen.height / 2) - (h / 2));
                var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes,left=" + left + ",top=" + top + ",width=" + w + ",height=" + h;
                var printingContents = document.getElementById("export").innerHTML;
                var docprint = window.open("", "", dispSetting);
                docprint.document.open();
                docprint.document.write('<html><head>');
                docprint.document.write('</head><body onLoad="self.print();self.close();"><center>');
                docprint.document.write(printingContents);
                docprint.document.write('</center></body></html>');
                docprint.document.close();
                docprint.focus();
            }

        </script>
        <style>
            .loader {
                border: 16px solid #f3f3f3;
                border-radius: 50%;
                border-top: 16px solid blue;
                border-bottom: 16px solid blue;
                width: 60px;
                height: 60px;
                -webkit-animation: spin 2s linear infinite;
                animation: spin 2s linear infinite;
            }

            @-webkit-keyframes spin {
                0% { -webkit-transform: rotate(0deg); }
                100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
        <script type="text/javascript">
//                       function myNewFunction(sel)
//                        {
//                            alert(sel.options[sel.selectedIndex].text);
//                        }

            function loaddata()
            {
                // var submit=document.getElementById( "submit" );

                var start = $('#datepicker2').val();
                var end = $('#datepicker1').val();
                var province = $('#province').val();

                var stakeholder = $('#stakeholder').val();
                var product = $('#product').val();
                var mos = $('#mos').val();
                var soh = $('#soh').val();
                //  var product = $('#product').val();
                var district=$("#district").val();

                if (start)
                {
                    $('#display_info').html('');
                    $('#loader').show();

                    $.ajax({
                        type: 'post',
                        url: 'loaddata2.php',
                        data: {
                            // user_name:submit,
                            start_date: start,
                            end_date: end,
                            province: province,
                            product: product,
                            stakeholder: stakeholder,
                            mos: mos,
                            soh: soh,
                            district:district
                        },
                        success: function (response) {
                            // We get the element having id of display_info and put the response inside it
                            $('#display_info').html(response);
                            $('#loader').hide();
                        }
                    });

                } else
                {
                    $('#display_info').html("No Data Exist");
                }

            }

        </script>       


    </head>


    <body >        
        <div class="page-container" >
            <?php
//include top
            include PUBLIC_PATH . "html/top.php";
//include top_im
            include PUBLIC_PATH . "html/top_im.php";
            ?>
            <div style="margin-top:10px !important;">
                <form action="" method="post" >
                    <div class="page-content-wrapper">
                        <div class="page-content">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="widget">
                                        <div class="widget-head">
                                            Facility Wise Stock Analysis 
                                        </div>
                                        <div class="widget-body">
                                            <div class="row">

                                                <div class="form-group col-xs-2">
                                                    <label for="start_date">Start Date:</label>
                                                    <input type="text" class="form-control"  name="start_date" value="<?php if (isset($_POST['start_date'])) echo $_POST['start_date']; ?>" id="datepicker2"> 
                                                </div>
                                                <div class="form-group col-xs-2">
                                                    <label for="end_date">End Date:</label>
                                                    <input type="text"  class="form-control" name="end_date" value="<?php if (isset($_POST['end_date'])) echo $_POST['end_date']; ?>" id="datepicker1">
                                                </div>
                                                <div class="form-group col-xs-2" >
                                                    <label for="province">Province:</label>                       
                                                    <select name="province" class="multiselect-ui form-control" multiple="multiple" id="province">

                                                        <?php
                                                        $query = $conn->query("SELECT
                                                    tbl_locations.PkLocID,
                                                    tbl_locations.LocName
                                                    FROM
                                                    tbl_locations
                                                    WHERE
                                                    tbl_locations.ParentID = 10 AND
                                                    tbl_locations.LocLvl = 2 AND
                                                    tbl_locations.LocType = 2");
                                                        while ($row = $query->fetch_assoc()) {
                                                            $pk_id = $row["PkLocID"];
                                                            $province_name = $row["LocName"];
                                                            ?>
                                                            <option value= <?php echo $pk_id; ?> > <?php echo $province_name; ?> </option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-xs-2" name="dstrict_div" id="district_div" >
                                                    <label for="district" >District:</label>                       
                                                    <select id="district" name="district" class="multiselect-ui form-control" multiple="multiple"></select>
                                                </div>
                                                <div class="form-group col-xs-2">
                                                    <label for="Product">Product:</label>
                                                    <select id="product" class="multiselect-ui form-control" multiple="multiple">
                                                        <?php
                                                        $query = $conn->query("SELECT
                                                    itminfo_tab.itmrec_id,
                                                    itminfo_tab.itm_name
                                                    FROM
                                                    itminfo_tab
                                                    WHERE
                                                    itminfo_tab.itm_category = 1");
                                                        while ($row = $query->fetch_assoc()) {
                                                            // $id=$row["id_expense"];  
                                                            $pk_id = $row["itmrec_id"];
                                                            $product_name = $row["itm_name"];
                                                            ?>
                                                            <option value= <?php echo $pk_id; ?> ><?php echo $product_name; ?> </option>
                                                        <?php }
                                                        ?>

                                                    </select>

                                                </div>

                                                <div class="form-group col-xs-2">
                                                    <label for="stakeholder">Stakeholder:</label>
                                                    <!--<select name="stakeholder" id="stakeholder" class="form-control">-->
                                                    <select id="stakeholder" class="multiselect-ui form-control" multiple="multiple">
                                                        <?php
                                                        $query = $conn->query("SELECT
                                                    stakeholder.stkid,
                                                    stakeholder.stkname,
                                                    stakeholder.MainStakeholder
                                                    FROM
                                                    stakeholder
                                                    WHERE
                                                    stakeholder.lvl = 7 AND
                                                    stakeholder.stk_type_id = 0");
                                                        while ($row = $query->fetch_assoc()) {
                                                            // $id=$row["id_expense"];  
                                                            $pk_id = $row["stkid"];
                                                            $stakeholder_name = $row["stkname"];
                                                            ?>
                                                            <option value= <?php echo $pk_id; ?> > <?php echo $stakeholder_name ?> </option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-xs-1">
                                                    <label for="start_date">SOH:</label>
                                                    <input type="text" class="form-control"  name="soh" value="" id="soh"> 
                                                </div>
                                                <div class="form-group col-xs-1">
                                                    <label for="start_date">MOS:</label>
                                                    <input type="text" class="form-control"  name="mos" value="" id="mos"> 
                                                </div>
                                                <div class="form-group col-xs-1">

                                                    <p style="margin-top:23px !important; "><button type="button" class="btn btn-primary" onclick="loaddata();" name="submit" id="submit">Search</button></p>
                                                </div>
                                                <div class="form-group col-xs-4 pull-right"> <p style="margin-top:23px !important; "><a href="index.php" class="btn  default"><i class="fa fa-external-link  black" style="color:black !important;"></i> Goto district wise stock analysis</a></p></div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

<!--                <div id="wait" style="display:none;width:69px;height:89px;border:1px solid black;position:absolute;top:50%;left:50%;padding:2px;"><img src='Llgbv_s-200x150.gif' width="64" height="64" /><br>Loading....</div>-->
                            <div class="row">

                                <div class="loader" id="loader"></div>
                                <div id="display_info" >
                                </div>
                            </div>


                        </div>
                    </div>
                </form>   




            </div>
        </div>
        <?php
//include footer
        include PUBLIC_PATH . "/html/footer.php";
//include combos
//include ('combos.php');
        ?>
        <script src="ms.js"></script>
        <script src="select2.min.js"></script>
        <script type="text/javascript">
                                                        $(function () {
                                                            $('#datepicker1').datepicker({dateFormat: 'yy-mm-dd'});
                                                            $('#datepicker2').datepicker({dateFormat: 'yy-mm-dd'});
                                                            $('#loader').hide();
                                                            $('.multiselect-ui').multiselect({
                                                                includeSelectAllOption: true
                                                            });

                                                        });
                                                        $('#province').change(function(){
    var province_id=$(this).val();
    $.ajax({

                        type: "POST",
                        url: 'ajax_district.php',
                        data: {province_id:province_id},
                        dataType: 'html',
                        success: function (data) {
                            console.log(data)
                            $('#district_div').html(data);
                           $('#district').select2();
                    $('#district').removeClass('form-control').addClass('select2me');
                     
                              
                        }

                    });
});
        </script>

    </body>
</html>


