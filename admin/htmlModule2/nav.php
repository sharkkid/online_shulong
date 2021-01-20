<!-- nav -->
<style>
.navbar-default .navbar-nav>li>a, .navbar-default .navbar-nav>li>a:focus, .navbar-default .navbar-nav>li>a:hover {
	color: #ffffff;
	font-size: 20px;
	font-weight: bolder;
} 

.dropdown-menu>li>a:focus, .dropdown-menu>li>a, .dropdown-menu>li>a:focus, .dropdown-menu>li>a:hover {
	font-size: 20px;
}

.navbar-header>a, .navbar-header>a:focus, .navbar-header>a:hover, .navbar-default .navbar-brand, .navbar-default .navbar-brand:hover {
	color: #ffffff;
	font-size: 30px;
	font-weight: bolder;
	margin-left:30px;
}

.navbar-default .title{
	color: #ffffff;
	font-size: 30px;
	font-weight: bolder;
	margin-left:30px;
}

.navbar .navbar-nav {
    display: inline-block;
    float: none;
}

.navbar .navbar-collapse {
    text-align: center;
}

</style>
<nav class="navbar navbar-default">
	<div class="container-fluid" style="background-color: rgb(17, 85, 148);">
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav navbar-left">
				<li class="dropdown"><a href="<?php echo WT_SERVER;?>/admin/summary/device_summary.php">機台總表</a></li>
				
				<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">全區圖 <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="<?php echo WT_SERVER;?>/admin/map/map.php?area=6a2f">6A 2F</a></li>
						<li><a href="<?php echo WT_SERVER;?>/admin/map/map.php?area=6a3f">6A 3F</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="<?php echo WT_SERVER;?>/admin/map/map.php?area=6b2f">6B 2F</a></li>
						<li><a href="<?php echo WT_SERVER;?>/admin/map/map.php?area=6b3f">6B 3F</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="<?php echo WT_SERVER;?>/admin/map/map.php?area=6c1f">6C 1F</a></li>
						<li><a href="<?php echo WT_SERVER;?>/admin/map/map.php?area=6c2f">6C 2F</a></li>
						<li><a href="<?php echo WT_SERVER;?>/admin/map/map.php?area=6c3f">6C 3F</a></li>
					</ul>
				</li>
			</ul>
			
			<ul class="nav navbar-nav">
				<span class="title"><a style="text-decoration:none; color:#ffffff;" href="<?php echo WT_SERVER;?>/admin/"><?php echo CN_SHORT_NAME;?></a></span>
			</ul>
				
			<ul class="nav navbar-nav navbar-right">
				<?php if($_SESSION['user']['jsuser_admin_permit']==1) {?>
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">管理員專區 <span class="caret"></span></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo WT_SERVER;?>/admin/sys/sys_user.php">使用者管理</a></li>
						<li role="separator" class="divider"></li>
							<li><a href="<?php echo WT_SERVER;?>/admin/sys/sys_history.php">系統操作歷史紀錄</a></li>
							<li><a href="<?php echo WT_SERVER;?>/admin/sys/sys_history_online.php">所有帳號個別上線時間</a></li>
							<li><a href="<?php echo WT_SERVER;?>/admin/sys/sys_history_edit.php">所有帳號執行修改次數</a></li>
						</ul>
					</li>
				<?php }?>
				
				<li class="dropdown"><a href="#" class="dropdown-toggle"
					data-toggle="dropdown" role="button" aria-haspopup="true"
					aria-expanded="false">Hello, <?php session_start(); echo $_SESSION['user']['jsuser_name'];?> <span class="caret"></span>
				</a>
					<ul class="dropdown-menu">
						<li><a href="<?php echo WT_SERVER;?>/admin/sys/sys_passwd.php">變更密碼</a></li>
						<li><a href="<?php echo WT_SERVER;?>/admin/sys/sys_login.php?op=logout">登出</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</nav>

<!-- ajax loading  -->
<div id="ajax_loading" style="display:none">
	<div class="ajax_overlay blue-loader" style="opacity: 0.5; width: 100%; height: 100%; position: absolute; top: 0px; left: 0px; z-index: 99999; background-color: rgb(0, 0, 0);">
	    <div class="ajax_loader"></div>
	</div>
</div>

<!-- alert modal -->
<div id="alert-modal" class="modal alert-modal" tabindex="-1" role="dialog" style="z-index: 99999;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">通知</h4>
            </div>
            <div class="modal-body">
				<p class="msg"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">確定</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('button.go_page').click(function(e){
			e.preventDefault();
			var href = $(this).data('href');
			location.href = href;
		});
	});
</script>