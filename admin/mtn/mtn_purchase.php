<?php
include_once("./func_purchase.php");
$status_mapping = array(0=>'<font color="red">關閉</font>', 1=>'<font color="blue">啟用</font>');
$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'add':
			$emm_sn = GetParam('emm_sn');
			$jsuser_sn = $_SESSION['user']['jsuser_sn'];
			$mtn_status = GetParam('mtn_status');
			
			if(empty($emm_sn)||empty($jsuser_sn)||$mtn_status=='') {
				$ret_msg = "*為必填！";
			} else {
				$now = time();
				$conn = getDB();
				$sql = "INSERT INTO mtn_purchase (mtn_add_date, mtn_mod_date, mtn_status, emm_sn, jsuser_sn) " . 
					"VALUES ('{$now}', '{$now}', '{$mtn_status}', '{$emm_sn}', '{$jsuser_sn}');";
				if($conn->query($sql)) {
					$ret_msg = "新增成功！";
				} else {
					$ret_msg = "新增失敗！";
				}
				$conn->close();
			}
			break;

		case 'get':
			$mtn_sn = GetParam('mtn_sn');
			$ret_data = array();
			if(!empty($mtn_sn)){
				$ret_code = 1;
				$ret_data = getConstLogBySn($mtn_sn);
			} else {
				$ret_code = 0;
			}
				
			break;
				
		case 'upd':
			$mtn_sn = GetParam('mtn_sn');
			$mtn_status = GetParam('mtn_status');
			$emm_sn = GetParam('emm_sn');
			$jsuser_sn = $_SESSION['user']['jsuser_sn'];
			
			if(empty($mtn_sn)||$mtn_status==''||empty($emm_sn)||empty($jsuser_sn)) {
				$ret_msg = "*為必填！";
			} else {
				$now = time();
				$conn = getDB();
				$sql = "UPDATE mtn_purchase SET mtn_status='{$mtn_status}', emm_sn='{$emm_sn}', jsuser_sn='{$jsuser_sn}', mtn_mod_date='{$now}' WHERE mtn_sn='{$mtn_sn}'";
				if($conn->query($sql)) {
					$ret_msg = "修改完成！";
				} else {
					$ret_msg = "修改失敗！";
				}
				$conn->close();
			}
			break;
			
		case 'del':
			$mtn_sn=GetParam('mtn_sn');
			
			if(empty($mtn_sn)){
				$ret_msg = "刪除失敗！";
			}else{
				$now = time();
				$conn = getDB();
				$sql = "UPDATE mtn_purchase SET mtn_status=-1, mtn_mod_date='{$now}' WHERE mtn_sn='{$mtn_sn}'";
				if($conn->query($sql)) {
					$ret_msg = "刪除完成！";
				} else {
					$ret_msg = "刪除失敗！";
				}
				$conn->close();
			}
			break;
			
		case 'show_photo':
			$mtn_sn = GetParam('mtn_sn');
			$ret_data = array();
			if(!empty($mtn_sn)){
				$ret_code = 1;
				$ret_data = getPhoto('mtn_purchase', $mtn_sn);
			} else {
				$ret_code = 0;
			}
			break;
			
		case 'add_photo':
			$mtn_sn = GetParam('mtn_sn');
			if(!empty($mtn_sn)){
				for($i=1; $i<=5; $i++) {
					uploaded_photo('mtn_purchase', $mtn_sn, 'filetoupload'.$i);
				}
				echo "Uploaded successfully. Please go back to the previous page.";
				exit;
			}
			break;

		case 'del_photo':
			$jsphoto_sn=GetParam('jsphoto_sn');
				
			if(empty($jsphoto_sn)) {
				$ret_msg = "刪除失敗！";
			} else {
				$now = time();
				$conn = getDB();
				$sql = "UPDATE js_photo SET jsphoto_status=-1, jsphoto_mod_date='{$now}' WHERE jsphoto_sn='{$jsphoto_sn}'";
				if($conn->query($sql)) {
					$ret_msg = "刪除完成！";
				} else {
					$ret_msg = "刪除失敗！";
				}
				$conn->close();
			}
			break;
			
		default:
			$ret_msg = 'error!';
			break;
	}
	
	echo enclode_ret_data($ret_code, $ret_msg, $ret_data);
	exit;
} else {

	// init define
	define('WEB_PAGE_TITLE', "進料紀錄表單管理");
	define('ADD_MODAT_TITLE', "新增表單");
	define('UPDATE_MODAT_TITLE', "修改表單");
	define('PAGE_FILE_NAME', "mtn_purchase.php");
	
	// search
	if(($emm_id = GetParam('emm_id'))) {
		$search_where[] = "b.emm_id like '%{$emm_id}%'";
		$search_query_string['emm_id'] = $emm_id;
	}

	if(($emm_name = GetParam('emm_name'))) {
		$search_where[] = "b.emm_name like '%{$emm_name}%'";
		$search_query_string['emm_name'] = $emm_name;
	}
	
	if(($jsuser_name = GetParam('jsuser_name'))) {
		$search_where[] = "c.jsuser_name like '%{$jsuser_name}%'";
		$search_query_string['jsuser_name'] = $jsuser_name;
	}
	
	if(($start = GetParam('start',""))) {
		$start_c = str2time($start.":00");
		$search_where[] = "a.mtn_add_date>={$start_c}";
		$search_query_string['start'] = $start;
	}

	if(($end = GetParam('end',""))) {
		$end_c = str2time($end.":59");
		$search_where[] = "a.mtn_add_date<={$end_c}";
		$search_query_string['end'] = $end;
	}
	
	if(($mtn_status = GetParam('mtn_status', -1))>=0) {
		$search_where[] = "a.mtn_status='{$mtn_status}'";
		$search_query_string['mtn_status'] = $mtn_status;
	}
	
	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';
	
	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getConstLogQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows));
	
	$list = getConstLog($search_where, $pg_offset, $pg_rows);
	$em_manage_list = getAllEmList();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    	<?php include('./../htmlModule/head.php');?>
		<script src="./../../lib/jquery.twbsPagination.min.js"></script>
		
		<script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
		<link rel="stylesheet" href="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
		
		<script type="text/javascript">
			$(document).ready(function() {
				<?php 
					//	init search parm
					print "$('#search [name=mtn_status] option[value={$mtn_status}]').prop('selected','selected');";
				?>
				
				$('button.upd').on('click', function(){
					$('#upd-modal').modal();
				  	$('#upd_form')[0].reset();

					$.ajax({
					    url: './<?php echo PAGE_FILE_NAME;?>',
					    type: 'post',
				        dataType: 'json',
					    data: {op:"get", mtn_sn:$(this).data('mtn_sn')},
					    beforeSend: function(msg) {
		    	        	$("#ajax_loading").show();
		                },
		                complete: function(XMLHttpRequest, textStatus) {
		    	        	$("#ajax_loading").hide();
		                },
		                success: function(ret) {
			                // console.log(ret);
			                if(ret.code==1) {
								var d = ret.data;
								$('#upd_form [name=emm_sn] option[value='+d.emm_sn+']').prop('selected','selected');
								$('#upd_form input[name=mtn_sn]').val(d.mtn_sn);
								$('#upd_form input[name=mtn_id]').val(d.mtn_id);
								$('#upd_form input[name=mtn_name]').val(d.mtn_name);
								$('#upd_form [name=mtn_status] option[value='+d.mtn_status+']').prop('selected','selected');
			                }
		                },
		                error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
				    });
				});

				bootbox.setDefaults({
					locale: "zh_TW",
				});
				
				$('button.del').on('click', function(){
					mtn_sn = $(this).data('mtn_sn')
					bootbox.confirm("確認刪除？", function(result) {
						if(result) {
							$.ajax({
							    url: './<?php echo PAGE_FILE_NAME;?>',
							    type: 'post',
						        dataType: 'json',
							    data: {op:"del", mtn_sn:mtn_sn},
							    beforeSend: function(msg) {
				    	        	$("#ajax_loading").show();
				                },
				                complete: function(XMLHttpRequest, textStatus) {
				    	        	$("#ajax_loading").hide();
				                },
				                success: function(ret) {
				                    alert_msg(ret.msg);
				                },
				                error: function (xhr, ajaxOptions, thrownError) {
				                	// console.log('ajax error');
				                }
						    });
						}
			  		}); 
				});
				
				$('#add_form, #upd_form, #del_photo_form').validator().on('submit', function(e) {
					if (!e.isDefaultPrevented()) {
						e.preventDefault();
						var param = $(this).serializeArray();
						
					  	$(this).parents('.modal').modal('hide');
					  	$(this)[0].reset();
					  	
					 // console.table(param);
						
						$.ajax({
						    url: './<?php echo PAGE_FILE_NAME;?>',
						    type: 'post',
					        dataType: 'json',
						    data: param,
						    beforeSend: function(msg) {
			    	        	$("#ajax_loading").show();
			                },
			                complete: function(XMLHttpRequest, textStatus) {
			    	        	$("#ajax_loading").hide();
			                },
			                success: function(ret) {
			                    alert_msg(ret.msg);
			                },
			                error: function (xhr, ajaxOptions, thrownError) {
			                	// console.log('ajax error');
			                    // console.log(xhr);
			                }
					    });
					}
				});

				$('button.show_photo').on('click', function(){
			        var image_html = "";
			        $("#show-photo-modal .image").html("");
			        
					$.ajax({
					    url: './<?php echo PAGE_FILE_NAME;?>',
					    type: 'post',
				        dataType: 'json',
					    data: {op:"show_photo", mtn_sn:$(this).data('mtn_sn')},
					    beforeSend: function(msg) {
		    	        	$("#ajax_loading").show();
		                },
		                complete: function(XMLHttpRequest, textStatus) {
		    	        	$("#ajax_loading").hide();
		                },
		                success: function(ret) {
			                // console.log(ret);
			                if(ret.code==1) {
						        var date = new Date();
						        var unixTimestamp = date.getTime();
						        
								var d = ret.data;
								if(d.length>0) {
									for (var i=0; i<d.length; i++) {
										var src = '<?php echo CN_WEB_URL;?>/uploads' + d[i].jsphoto_path + "?x=" + unixTimestamp;
										image_html += '<a href="' + src + '" target="_blank"><img class="img-rounded" width="100%" src = "'+src+'" alt="載入失敗" /></a>';
										image_html += '<br><br><button type="button" class="btn btn-danger btn-block del_photo" data-jsphoto_sn=' + d[i].jsphoto_sn + '>刪除</button><hr><br>';
									}
								} else {
									image_html += '<center>無照片</center>';
								}

						        $("#show-photo-modal .image").html(image_html);

								$('button.del_photo').on('click', function(){
									$('#del_photo_form input[name=jsphoto_sn]').val($(this).data('jsphoto_sn'));
									$('#del-photo-modal').modal();
								});
			                }
		                },
		                error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
				    });
				    
					$('#show-photo-modal').modal();
				});

				$('button.add_photo').on('click', function(){
					$('#add_photo_form input[name=mtn_sn]').val($(this).data('mtn_sn'));
					$('#add-photo-modal').modal();
				});

		        $('#datetimepicker1').datetimepicker({
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd hh:ii',
		            useCurrent: false //Important! See issue #1075
		        });
		        
		        $('#datetimepicker2').datetimepicker({
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd hh:ii',
		            useCurrent: false //Important! See issue #1075
		        });
			});
		</script>
    </head>

    <body>
    	<?php include('./../htmlModule/nav.php');?>
    	
    	<!-- modal -->
		<div id="add-modal" class="modal add-modal" tabindex="-1" role="dialog">
		    <div class="modal-dialog modal-lg">
		        <div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="add_form" class="form-horizontal" role="form" data-toggle="validator">
			            <div class="modal-header">
			                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			                <h4 class="modal-title"><?php echo ADD_MODAT_TITLE;?></h4>
			            </div>
			            <div class="modal-body">
			            	<div class="row">
								<div class="col-md-12">
					        		<input type="hidden" name="op" value="add">
									<div class="form-group">
										<label class="col-sm-2 control-label">工程<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="emm_sn">
								            <?php 
								            foreach ($em_manage_list as $v) {
												echo '<option value="'.$v['emm_sn'].'">'.$v['emm_id'].'-'.$v['emm_name'].'</option>';
											}
								            ?>
									        </select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">狀態<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="mtn_status">
									            <option value="0">關閉</option>
									            <option selected="selected" value="1">啟用</option>
									        </select>
										</div>
									</div>
								</div>
							</div>
			            </div>
						<div class="modal-footer">
			            	<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			            	<button type="reset" class="btn btn-default">清空</button>
			            	<button type="submit" class="btn btn-primary">新增</button>
						</div>
					</form>
		        </div>
		    </div>
		</div>
		
		<div id="upd-modal" class="modal upd-modal" tabindex="-1" role="dialog">
		    <div class="modal-dialog modal-lg">
		        <div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form" class="form-horizontal" role="form" data-toggle="validator">
			            <div class="modal-header">
			                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			                <h4 class="modal-title"><?php echo UPDATE_MODAT_TITLE;?></h4>
			            </div>
			            <div class="modal-body">
			            	<div class="row">
								<div class="col-md-12">
					        		<input type="hidden" name="op" value="upd">
					        		<input type="hidden" name="mtn_sn">
									<div class="form-group">
										<label class="col-sm-2 control-label">工程<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="emm_sn">
								            <?php 
								            foreach ($em_manage_list as $v) {
												echo '<option value="'.$v['emm_sn'].'">'.$v['emm_id'].'-'.$v['emm_name'].'</option>';
											}
								            ?>
									        </select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">狀態<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="mtn_status">
									            <option value="0">關閉</option>
									            <option selected="selected" value="1">啟用</option>
									        </select>
										</div>
									</div>
								</div>
							</div>
			            </div>
						<div class="modal-footer">
			            	<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			            	<button type="submit" class="btn btn-primary">更新</button>
						</div>
					</form>
		        </div>
		    </div>
		</div>
		
		<div id="show-photo-modal" class="modal fade" role="dialog">
			<div class="modal-dialog modal-lg" style="width: 700px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title modal_header_title">照片</h4>
				</div>
				<div class="modal-body">
					<div class="container-fluid">
						<div class="image"></div>
					</div>     		
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
				</div>
			</div>
		  </div>
		</div>
		
		<div id="add-photo-modal" class="modal add-photo-modal" tabindex="-1" role="dialog">
		    <div class="modal-dialog modal-lg">
		        <div class="modal-content">
					<form autocomplete="off" method="post" action="./<?php echo PAGE_FILE_NAME;?>" id="add_photo_form" class="form-horizontal" role="form" data-toggle="validator" enctype="multipart/form-data">
			            <div class="modal-header">
			                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			                <h4 class="modal-title">新增照片</h4>
			            </div>
			            <div class="modal-body">
			            	<div class="row">
								<div class="col-md-12">
					        		<input type="hidden" name="op" value="add_photo">
					        		<input type="hidden" name="mtn_sn">
									<div class="form-group">
										<label class="col-sm-2 control-label">照片<font color="red">*</font></label>
										<div class="col-sm-10">
											<h6></h6>
											<input type="file" id="filetoupload1" name="filetoupload1"/>
											<h6></h6>
											<input type="file" id="filetoupload2" name="filetoupload2"/>
											<h6></h6>
											<input type="file" id="filetoupload3" name="filetoupload3"/>
											<h6></h6>
											<input type="file" id="filetoupload4" name="filetoupload4"/>
											<h6></h6>
											<input type="file" id="filetoupload5" name="filetoupload5"/>
											<div class="help-block">檔案大小限制10MB</div>
										</div>
									</div>
								</div>
							</div>
			            </div>
						<div class="modal-footer">
			            	<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			            	<button type="reset" class="btn btn-default">清空</button>
			            	<button type="submit" class="btn btn-primary">上傳</button>
						</div>
					</form>
		        </div>
		    </div>
		</div>
		
		<div id="del-photo-modal" class="modal del-photo-modal" tabindex="-1" role="dialog">
		    <div class="modal-dialog modal-xs">
		        <div class="modal-content">
					<form autocomplete="off" method="post" action="./<?php echo PAGE_FILE_NAME;?>" id="del_photo_form" class="form-horizontal" role="form" data-toggle="validator">
		        		<input type="hidden" name="op" value="del_photo">
		        		<input type="hidden" name="jsphoto_sn">
			            <div class="modal-body">
			            	確認刪除此照片
			            </div>
						<div class="modal-footer">
			            	<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			            	<button type="submit" class="btn btn-danger">確認刪除</button>
						</div>
					</form>
		        </div>
		    </div>
		</div>
		
		
    	<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
				
    				<!-- title -->
					<h3 class="text-center wt-block-title"><?php echo WEB_PAGE_TITLE;?></h3>
					
    				<!-- nav toolbar -->
					<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
					    <ul class="nav nav-pills pull-right toolbar">
					    	<li><button data-parent="#toolbar" data-toggle="modal" data-target=".add-modal" class="accordion-toggle btn btn-primary"><i class="glyphicon glyphicon-plus"></i> 新增</button></li>
					    </ul>
					</div>
					
    				<!-- search -->
    				<div id="search" style="clear:both;">
					    <form autocomplete="off" method="get" action="./<?php echo PAGE_FILE_NAME;?>" id="search_form" class="form-inline alert alert-info" role="form">
							<div class="row">
							    <div class="col-md-12">
								    <div class="form-group">
								        <label>工程編號</label>
								        <input type="text" class="form-control" name="emm_id" value="<?php echo $emm_id;?>" placeholder="">
								    </div>
								    <div class="form-group">
								        <label>工程名稱</label>
								        <input type="text" class="form-control" name="emm_name" value="<?php echo $emm_name;?>" placeholder="">
								    </div>
								    <div class="form-group">
								        <label>填表人</label>
								        <input type="text" class="form-control" name="jsuser_name" value="<?php echo $jsuser_name;?>" placeholder="">
								    </div>
								    
								    <div class="form-group">
								        <label for="datetimepicker1">新增時間</label>
								        <input type="text" class="form-control" id="datetimepicker1" name="start" value="<?php echo $start;?>" placeholder="">
								    </div>
								    
								    <div class="form-group">
								        <label for="datetimepicker2">~</label>
								        <input type="text" class="form-control" id="datetimepicker2" name="end" value="<?php echo $end;?>" placeholder="">
								    </div>
								    
								    <div class="form-group">
								        <label>狀態</label>
								        <select class="form-control" name="mtn_status">
								            <option selected="selected" value="-1">全部</option>
								            <?php 
								            foreach ($status_mapping as $k=>$v) {
												echo '<option value="'.$k.'">'.$v.'</option>';
											}
								            ?>
								        </select>
								    </div>
								    <button type="submit" class="btn btn-info" op="search">搜尋</button>
								</div>
							</div>
						</form>
					</div>
					
    				<!-- content -->
					<table class="table table-striped table-hover table-condensed tablesorter">
						<thead>
							<tr>
								<th>操作</th>
								<th>工程編號</th>
								<th>工程名稱</th>
								<th>填表人</th>
								<th>照片</th>
								<th width="180px">新增時間</th>
								<th width="180px">最近修改時間</th>
								<th width="55px">狀態</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($list as $row) {
									echo '<tr>';
									echo '<td>';
									echo '<button type="button" class="btn btn-primary btn-xs upd" data-mtn_sn="'.$row['mtn_sn'].'">修改</button>&nbsp;';
									echo '<button type="button" class="btn btn-danger btn-xs del" data-mtn_sn="'.$row['mtn_sn'].'">刪除</button>';
									echo '</td>';
									echo '<td>'.$row['emm_id'].'</td>';
									echo '<td>'.$row['emm_name'].'</td>';
									echo '<td>'.$row['jsuser_name'].'</td>';
									echo '<td>';
									echo '<button type="button" class="btn btn-primary btn-xs show_photo" data-mtn_sn="'.$row['mtn_sn'].'">查看照片</button>&nbsp;';
									echo '<button type="button" class="btn btn-primary btn-xs add_photo" data-mtn_sn="'.$row['mtn_sn'].'">上傳照片</button>';
									echo '</td>';
									echo '<td>'.date('Y-m-d H:i', $row['mtn_add_date']).'</td>';
									echo '<td>'.date('Y-m-d H:i', $row['mtn_mod_date']).'</td>';
									echo '<td>'.$status_mapping[$row['mtn_status']].'</td>';
									echo '</tr>';
								}
							?>
						</tbody>
					</table>
					
    				<?php include('./../htmlModule/page.php');?>
					
				</div>
			</div>
		</div>
    </body>
</html>