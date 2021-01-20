<div class="navbar-collapse collapse" style="margin-bottom: 10px;">
    <div class="btn-group btn-group-lg">
		<?php if(in_array($_SESSION['user']['jsuser_admin_permit'], array(0,1,2,4))) {?>
			<button type="button" class="btn btn-default add"><span class="glyphicon glyphicon-file"></sapn></button>
		<?php }?>
		
		<?php if(in_array($_SESSION['user']['jsuser_admin_permit'], array(1,2,4))) {?>
			<button type="button" class="btn btn-default turn_version"><span class="glyphicon glyphicon-share-alt"></sapn></button>
		<?php }?>
		
		<button type="button" class="btn btn-default go_page" data-href="<?php echo WT_SERVER;?>/admin/device/device_manage_history_version.php?dema_sn=<?php echo $dema_sn;?>"><span class="glyphicon glyphicon-book"></sapn></button>
    </div>
</div>