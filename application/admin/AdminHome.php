<?php
/**
 * Admin Home page
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include $_SESSION['menu']; ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <h3 class="heading">
                    <?php
                    echo "<h1>Welcome: " . $_SESSION['user_name'] . " </h1>";
                    ?>
                </h3>
            </div>
        </div>
    </div>
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>
</body>
</html>