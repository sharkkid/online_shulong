<div class="navbar-collapse collapse" style="margin-bottom: 10px;">
    <div class="btn-group btn-group-lg">
		<?php if(in_array($_SESSION['user']['jsuser_admin_permit'], array(0,1,2,4))) {?>
			<button type="button" class="btn btn-default go_page" data-href="<?php echo WT_SERVER;?>/admin/device/device_manage_add.php"><span class="glyphicon glyphicon-file"></sapn></button>
		<?php }?>
		
		<button type="button" class="btn btn-default go_page" data-href="<?php echo WT_SERVER;?>/admin/device/device_manage_advanced_search.php"><span class="glyphicon glyphicon-search"></sapn></button>
		
		<?php if(in_array($_SESSION['user']['jsuser_admin_permit'], array(0,1,2,4))) {?>
			<button type="button" class="btn btn-default go_page" data-href="<?php echo WT_SERVER;?>/admin/device/device_manage_edit_menu.php"><span class="glyphicon glyphicon-edit"></sapn></button>
		<?php }?>
		
		<?php if(in_array($_SESSION['user']['jsuser_admin_permit'], array(1,4))) {?>
			<button type="button" class="btn btn-default go_page" data-href="<?php echo WT_SERVER;?>/admin/device/device_manage_general_search.php?o=del"><span class="glyphicon glyphicon-trash"></sapn></button>
		<?php }?>

		<button type="button" class="btn btn-default go_page" data-href="<?php echo WT_SERVER;?>/admin/device/device_manage_export.php"><span class="glyphicon glyphicon-export"></sapn></button>
		<button type="button" class="btn btn-default page_first"><span class="glyphicon glyphicon-backward"></sapn></button>
		<button type="button" class="btn btn-default page_prev"><span class="glyphicon glyphicon-play" style="transform:rotate(180deg);"></sapn></button>
		<button type="button" class="btn btn-default page_next"><span class="glyphicon glyphicon-play"></sapn></button>
		<button type="button" class="btn btn-default page_last"><span class="glyphicon glyphicon-forward"></sapn></button>
    </div>
</div>