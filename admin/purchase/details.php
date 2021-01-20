<?php
include_once("./func_plant_purchase.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo CN_NAME;?></title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/modern-business.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="./../../images/favicon.png" rel="icon">
    <link href="./../../css1/bootstrap.min.css" rel="stylesheet">
    <link href="./../../css1/simple-line-icons.css" rel="stylesheet">
    <link href="./../../css1/font-awesome.min.css" rel="stylesheet">
    <link href="./../../css1/pace.css" rel="stylesheet">
    <link href="./../../css1/jasny-bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./../../css1/nanoscroller.css">
    <link rel="stylesheet" href="./../../css1/metismenu.min.css">
    <link href="./../../css1/c3.min.css" rel="stylesheet">
    <link href="./../../css1/blue.css" rel="stylesheet">
    <!-- dataTables -->
    <link href="./../../css1/jquery.datatables.min.css" rel="stylesheet" type="text/css">
    <link href="./../../css1/responsive.bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- <link href="./../css1/jquery.toast.min.css" rel="stylesheet"> -->
    <!--template css-->
    <link href="./../../css1/style.css" rel="stylesheet">
    <?php include('./../htmlModule/head.php');?>
    <script src="./../../lib/jquery.twbsPagination.min.js"></script>
    <script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!--top bar start-->
    <div class="top-bar light-top-bar"><!--by default top bar is dark, add .light-top-bar class to make it light-->
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-6">
                    <a href="" class="admin-logo">
                        <h1><img src="./../../picture/logo-dark.png" alt=""></h1>
                    </a>
                    <div class="left-nav-toggle visible-xs visible-sm">
                        <a href="">
                            <i class="glyphicon glyphicon-menu-hamburger"></i>
                        </a>
                    </div><!--end nav toggle icon-->
                    <!--start search form-->
                    <div class="search-form hidden-xs">
                            <!-- <form>
                                <input type="text" class="form-control" placeholder="Search for...">
                                <button type="button" class="btn-search"><i class="fa fa-search"></i></button>
                            </form> -->
                        </div>
                        <!--end search form-->
                    </div>
                    <div class="col-xs-6">
                        <ul class="list-inline top-right-nav">
                            <li class="dropdown hidden-xs icon-dropdown">
                                <a href="" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="glyphicon glyphicon-bell"></i>
                                    <span class="badge badge-danger">2</span>
                                </a>
                                <ul class="dropdown-menu top-dropdown lg-dropdown notification-dropdown">
                                    <li>
                                        <div class="dropdown-header"><a href="" class="pull-right text-muted"><small></small></a> 提醒事項 </div>
                                        <div class="scrollDiv">
                                            <div class="notification-list">
                                                <a href="javascript: void(0);" class="clearfix">
                                                    <span class="notification-icon"><i class="icon-info text-primary"></i></span>                                                 
                                                    <span class="notification-title">澆水</span>
                                                    <span class="notification-description">品號 : PP-0052</span>
                                                    <span class="notification-description">品名 : Snow Elf(小白)</span>
                                                    <span class="notification-time">兩日後需澆水</span>
                                                </a>
                                                <a href="javascript: void(0);" class="clearfix">
                                                    <span class="notification-icon"><i class="icon-info text-primary"></i></span>                                                 
                                                    <span class="notification-title">施肥</span>
                                                    <span class="notification-description">品號 : PA2</span>
                                                    <span class="notification-description">品名 : Pink Girl 粉紅女孩</span>
                                                    <span class="notification-time">三日後需施肥</span>
                                                </a>
                                            </div>
                                        </div>
                                    </li>

                                </ul>
                            </li>
                            <li class="dropdown avtar-dropdown">
                                <a href="" class="dropdown-toggle" data-toggle="dropdown">
                                    <img src="./../../picture/avtar-1.jpg" class="img-circle" width="30" alt="">

                                </a>
                                <ul class="dropdown-menu top-dropdown">
                                    <li class="divider"></li>
                                    <!-- <li><a href="javascript: void(0);"><i class="icon-logout"></i> Logout</a></li> -->
                                    <li><a href="<?php echo WT_SERVER;?>/admin/sys/sys_login.php?op=logout">Logout</a></li>
                                </ul>
                            </li>

                        </ul> 
                    </div>
                </div>
            </div>
        </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!--left navigation start-->
        <aside class="float-navigation light-navigation">
            <div class="nano">
                <div class="nano-content">
                    <ul class="metisMenu nav" id="menu">
                        <li class="nav-heading"><span>主要項目</span></li>
                        <li class="active">
                            <a href="javascript: void(0);" aria-expanded="true"><i class="icon-home"></i> 庫存管理 <span class="fa arrow"></span></a>
                            <ul class="nav-second-level nav" aria-expanded="true">
                                <li><a href="<?php echo WT_SERVER;?>/admin/purchase/plant_purchase.php">進貨管理</a></li>
                                <li><a href="<?php echo WT_SERVER;?>/admin/purchase/plant_shipment.php">出貨管理</a></li>

                            </ul>
                        </li>
                        <li>
                            <a href="javascript: void(0);" aria-expanded="true"><i class="icon-grid"></i> 植床區域管理 <span class="fa arrow"></span></a>
                            <ul class="nav-second-level nav" aria-expanded="true">
                                <li><a href="<?php echo WT_SERVER;?>/admin/map/map.php?area=6a3f">A區</a></li>
                                <li><a href="layout-sidebar-colored.html">B區</a></li>
                                <li><a href="layout-horizontal.html">冷房</a></li>
                            </ul>
                        </li>

                        <li><a href="<?php echo WT_SERVER;?>/admin/index.php">"<i class="icon-bar-chart"></i> 統計圖表 </a></li>

                        <li class="nav-heading"><span>管理員項目</span></li>
                        <li><a href="<?php echo WT_SERVER;?>/admin/sys/sys_user.php"><i class="icon-user"></i> 帳號管理</a></li>
                        <li><a href="javascript: void(0);"><i class="icon-settings"></i> 語言設定</a></li>

                        <!--<li><a href="landing/index.html" target="_blank" class="bg-primary"><i class="icon-star"></i>Landing page</a></li>-->
                    </ul>
                </div><!--nano content-->
            </div><!--nano scroll end-->
        </aside>
        <!--left navigation end-->

        <!--main content start-->
        <section class="main-content">



            <!--page header start-->
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h4>產品明細</h4>
                    </div>
                </div>
            </div>

    <!-- Page Content -->
    <div class="container">
        <!-- /.row -->

        <!-- Portfolio Item Row -->
        <div class="row">

            <div class="col-md-8">
                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                        <!--<li data-target="#carousel-example-generic" data-slide-to="2"></li>-->
                    </ol>

                    <!-- Wrapper for slides -->
                    <div class="carousel-inner">
                        <div class="item active">
                            <img class="img-responsive" src="images/6 CM POT/White with pink/圖片 1.png" alt="banana ball python">
                            <div class="carousel-caption">
                                
                            </div>
                        </div>
                        <div class="item">
                            <img class="img-responsive" src="images/6 CM POT/White with pink/圖片 2.png" alt="banana clown ball python">
                            <div class="carousel-caption">
                               
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>
            </div>

            <div class="col-md-4">
                <h3>Snow Elf(小白)</h3>
                <p>品號(Part no.) : PP-0052</p>
				<p>花色 (Flower Color) : whith</p>
				<p>花徑 (Flower Size) : 4.5cm</p>
				<p>高度 (Plant Height) : 15-20cm</p>
				<p>適合開花盆徑 (Suitable flowering pot size) : 6 cm pot/ 1.7 inch pot</p>
				<p>	<table border="2" >
		<tr>
			<td colspan="8" align="center" >2018</td>
			

			
			<td colspan="12" align="center" rowspan="1">2019</td>
			
			
		<tr>
			<td>Month</td>
			<td>June</td>
			<td>July</td>
			<td>Aug</td>
			<td>Sep</td>
			<td>Oct</td>
			<td>Nov</td>
			<td>Dec</td>
			
			
			<td>Jan</td>
			<td>Feb</td>
			<td>March</td>
			<td>April</td>
			<td>May</td>
			<td>June</td>
			<td>July</td>
			<td>Aug</td>
			<td>Sep</td>
			<td>Oct</td>
			<td>Nov</td>
			<td>Dec</td>

		</tr>
		
		
		<tr>
			<td>Flask</td>
			<td></td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			
			
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>
			<td>5000</td>

		</tr>
		
		<tr>
			<td>1.7”</td>
			<td>10000</td>
			<td>5000</td>
			<td>5000</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			
			
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>

		</tr>
		
		<tr>
			<td>2.5”</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			
			
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>

		</tr>
		
		
		

		</tr>


		
		
		
		
	
	</table></p>
                
            </div>
			
			

        </div>
 
        <!-- /.row -->

        
        <hr>

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    
                </div>
            </div>
        </footer>

    </div>
    <!-- /.container -->
    <footer class="footer">
                <span>Copyright &copy; 2019. Online Plant</span>
            </footer>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!--Common plugins-->
        <!-- <script src="./../../js1/jquery.min.js"></script> -->
        <!-- <script src="./../../js1/bootstrap.min.js"></script> -->
        <script src="./../../js1/pace.min.js"></script>
        <script src="./../../js1/jasny-bootstrap.min.js"></script>
        <script src="./../../js1/jquery.slimscroll.min.js"></script>
        <script src="./../../js1/jquery.nanoscroller.min.js"></script>
        <script src="./../../js1/metismenu.min.js"></script>
        <script src="./../../js1/float-custom.js"></script>
         <!--page script-->
        <script src="./../../js1/d3.min.js"></script>
        <script src="./../../js1/c3.min.js"></script>
        <!-- iCheck for radio and checkboxes -->
        <script src="./../../js1/icheck.min.js"></script>
        <!-- Datatables-->
        <script src="./../../js1/jquery.datatables.min.js"></script>
        <script src="./../../js1/datatables.responsive.min.js"></script>
        <script src="./../../js1/jquery.toast.min.js"></script>
        <script src="./../../js1/dashboard-alpha.js"></script>

</body>

</html>
