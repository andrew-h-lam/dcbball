<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- bootstrap theme -->
    <link href="css/bootstrap-theme.css" rel="stylesheet">
    <!-- font icon -->
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <link href="css/jquery-jvectormap-1.2.2.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <link href="css/xcharts.min.css" rel=" stylesheet">
    <link href="css/jquery-ui-1.10.4.min.css" rel="stylesheet">

    <script type = "text/javascript" src = "http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script type = "text/javascript" src = "js/dcbball.js"></script>

    <style>
        .highlight { background-color:yellow; }
    </style>
</head>

<body>
<!-- container section start -->
<section id="container" class="">

    <header class="header dark-bg">
        <!--logo start-->
        <a href="standings" class="logo">Datacare <span class="lite">Basketball</span></a>
        <!--logo end-->
        <div class="top-nav notification-row"></div>
    </header>
    <!--sidebar start-->
    <aside>
        <div id="sidebar"  class="nav-collapse ">
            <!-- sidebar menu start-->
            <ul class="sidebar-menu">
                <li>
                    <a class="" href="standings">
                        <i class="icon_house_alt"></i>
                        <span>Standings</span>
                    </a>
                </li>

                <li>
                    <a class="" href="games">
                        <i class="icon_document_alt"></i>
                        <span>Game Log</span>
                    </a>
                </li>
                <li>
                    <a class="" href="lineups">
                        <i class="icon_table"></i>
                        <span>3-Man Lineups</span>
                    </a>
                </li>
                <li>
                    <a class="" href="gameform">
                        <i class="icon_pencil"></i>
                        <span>Add Game</span>
                    </a>
                </li>
            </ul>
            <!-- sidebar menu end-->
        </div>
    </aside>
    <!--sidebar end-->

    <!--main content start-->
    <section id="main-content">
        <section class="wrapper">
            <!--overview start-->
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><?php if(isset($title)) echo $title; ?></h3>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-9 col-md-12">
                        <?php if(isset($table)) echo $table; ?><input type="hidden" name="blank" value="">
                </div><!--/col-->
            </div><br><br>
        </section>
    </section>
    <!--main content end-->
</section>
<!-- container section start -->
</body>
</html>
