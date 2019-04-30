<?php

include("../includes/classes/AllClasses.php");
//include FunctionLib

//include header
include(PUBLIC_PATH . "html/header.php");

?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
    <?php 
    //include top
    include PUBLIC_PATH."html/top.php";?>
    <?php 
    //include top_im
    include PUBLIC_PATH."html/top_im.php";?>
        <div class="page-content-wrapper">
            <div class="page-content">
            <div class="well well-dark">
                
                <div class="row">
                    <div class="col-md-12 center">
                        <div style="">
                            <img src="<?=PUBLIC_URL?>/images/maintenance2.png"></img>
                        </div>
                    </div>
                </div> 
                
                <div class="row">
                    <div class="col-md-12 center">
                        <h2>We are working to improve this page. <br/>Sorry for inconvenience !</h2>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
    
<?php 
//include footer
include PUBLIC_PATH."/html/footer.php";?>
</body>
</html>