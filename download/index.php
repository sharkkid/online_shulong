<?php
include_once("./func.php");
$app = getApp();
$CN_WEB_URL = CN_WEB_URL;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="Wholetech">
				
		<title><?php echo CN_NAME;?></title>

		<script type="text/javascript" src="<?php echo WT_SERVER;?>/lib/jquery-1.11.3.min.js"></script>
		<script type="text/javascript" src="<?php echo WT_SERVER;?>/lib/jquery.tablesorter.js"></script>
				
		<!-- bootstrap -->
		<link rel="stylesheet" href="<?php echo WT_SERVER;?>/lib/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="<?php echo WT_SERVER;?>/lib/bootstrap/3.3.5/css/bootstrap-theme.min.css">
		<script src="<?php echo WT_SERVER;?>/lib/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<!--[if lt IE 9]>
			<script src="./../../lib/html5shiv/3.7.0/html5.js" type="text/javascript"></script>
			<script src="./../../lib/respond/1.4.2/respond.min.js" type="text/javascript"></script>
		<![endif]-->
		<script type="text/javascript">
			$(document).ready(function() {
			});
		</script>
    </head>

    <body>
    	<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
				
    				<!-- title -->
					<h3 class="text-center wt-block-title">Download</h3>

					<!-- content -->
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>名稱</th>
								<th>Version code</th>
								<th>Version name</th>
								<th>檔案名稱</th>
								<th>描述</th>
								<th>下載</th>								
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($app as $row) {
									echo '<tr>';
									echo '<td>'.$row['jsapp_name'].'</td>';
									echo '<td>'.$row['jsapp_version_code'].'</td>';
									echo '<td>'.$row['jsapp_version_name'].'</td>';
									echo '<td>'.$row['jsapp_file_name'].'</td>';
									echo '<td>'.$row['jsapp_desc'].'</td>';
									echo '<td><a href="'.$CN_WEB_URL.'download/'.$row['jsapp_sys'].'/'.$row['jsapp_file_name'].'">下載</td>';
									echo '</td></tr>';
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
    </body>
</html>