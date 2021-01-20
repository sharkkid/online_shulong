<?php
include_once("./func.php");
$area=GetParam('area');
$onadd_part_no=GetParam('onadd_part_no');

if(empty($area) && empty($onadd_part_no)) {
    $onadd_sn=GetParam('onadd_sn');
    $manage = getmanageLogBySn($onadd_sn);
    $area = $LOCATION_DB_AREA_MAPPING[$manage['dema_site_loc'].$manage['dema_floor_loc']];
    $onadd_part_no = $manage['onadd_part_no'];
}

$op=GetParam('op');
if(!empty($op)) {
    $ret_code = 1;
    $ret_msg = '';
    $ret_data = array();
    switch ($op) {
        case 'get':
            $onadd_sn = GetParam('onadd_sn');
            $ret_data = array();
            if(!empty($dema_sn)){
                $ret_code = 1;
                $ret_data = getmanageLogBySn($onadd_sn);
            } else {
                $ret_code = 0;
            }
            break;
            
        case 'setting':
            $area = GetParam('area');
            $position = GetParam('position');
            file_put_contents('./../../uploads/map/setting/'.$area, $position);
            if(!empty($_FILES['file']['name'])) {
                $upload_file_ret = uploadFile('./../../uploads/map/img/', $area, 'file');
                if($upload_file_ret['result']) {
                    $ret_msg = "設定成功.";
                } else {
                    $ret_code = -1;
                    $ret_msg = $upload_file_ret['msg'];
                    unlink('./../../uploads/map/img/'.$area);
                }
            } else {
                $ret_msg = "設定成功！";
            }
            
            $_SESSION['sys_map_setting_upload_data_result'] = $ret_msg;
            header('Location: map.php?area='.$area);
            die();
            break;
            
        case 'reset':
            $area = GetParam('area');
            file_put_contents('./../../uploads/map/setting/'.$area, '');
            echo 'reset success.';
            exit;
            
        default:
            $ret_msg = 'error!';
            break;
    }
    
    echo enclode_ret_data($ret_code, $ret_msg, $ret_data);
    exit;
} else {
    define('WEB_PAGE_TITLE', "A區圖");
    define('PAGE_FILE_NAME', "map.php");
    
    $o = GetParam('o');
    if($o == "") {
    	$o = 'o';
    }
    
    if(file_exists('./../../uploads/map/setting/'.$area)) {
        $handle = fopen('./../../uploads/map/setting/'.$area, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $positions[] = $line;
            }
            
            fclose($handle);
            $position_data = file_get_contents('./../../uploads/map/setting/'.$area);
        }
    } else {
        $position_data = '';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
    	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo CN_NAME;?></title>
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
    	<?php include('./../htmlModule2/head.php');?>
		<script src="./../../lib/jquery.twbsPagination.min.js"></script>
		
		<script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
		<link rel="stylesheet" href="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
		
		<style>
			#map_img {
	　			position:absolute;
	　			z-index:1;
			}
			
			.device {
				position:absolute;
				background-color:#6DA9E7;
				z-index:2;
				opacity:0.3;
			}
		</style>
		
		<script type="text/javascript">
			$(document).ready(function() {
				<?php 
    				if(!empty($_SESSION['sys_map_setting_upload_data_result'])) {
    				    echo "bootbox.alert('".$_SESSION['sys_map_setting_upload_data_result']."', function(){});";
    				    $_SESSION['sys_map_setting_upload_data_result'] = '';
    				}
				?>
				
				// mousemove
				tops = [];
				lefts = [];
				<?php if($o=='edit') {?>
    			    $('#map_img').click(function (e) {
    			        var posX = $(this).offset().left,
    			            posY = $(this).offset().top;
    			        var top = (e.pageY - posY);
    			        var left = (e.pageX - posX);
    			        tops.push(top);
    			        lefts.push(left);
    			        if(tops.length%2==0) {
    			        	var height = Math.round(tops[tops.length-1]-tops[tops.length-2]);
    			        	var width = Math.round(lefts[lefts.length-1]-lefts[lefts.length-2]);
    				        console.log('{top: '+Math.round(tops[tops.length-2])+'+80, left: '+Math.round(lefts[lefts.length-2])+'+15, height:'+height+', width:'+width+'}');
    				        console.log('---');
    			        } else {
    			        }
    			    });
			    <?php } ?>
			    
			    $(".device").click(function() {
			    	var id = $(this).attr('id');
			    	if(id.indexOf('-')!=-1) {
				    	var id_array = id.split('-');
				    	var url = "./../device/device_manage.php?map=1&dema_device_num="+(id_array[0].substring(1));
			    	} else {
			    		var url = "./../device/device_manage.php?map=1&dema_device_num="+($(this).attr('id').substring(1));
			    	}
		    		$('#device_modal').modal('show').find('.modal-body').load(url);
				});

		        var zoom_level = 100;
		        var size = 60;
    			var image = document.getElementById("map_img");
				var o = '<?php echo $o;?>';
				var onadd_part_no = '<?php echo $onadd_part_no;?>';
    			var count = 0;
    			if(onadd_part_no=="" && o!='edit') {
    				count = 19;
    			}
			    $("#zoom_in").click(function() {
			    	if(count>=-5) {
			    		<?php
						    if(!empty($positions)) {
					            foreach($positions as $k =>$v) {
					                if(empty($v)||$v==''||strpos($v, '@')===false) {
					                	continue;
					                }
					                $data = explode('@', $v);
					                $data = str_replace('-', "", $data);
					                echo 'var _'.trim($data[0]).'= document.getElementById("_'.trim($data[0]).'");';
					                echo '_'.trim($data[0]).'.style.width=(parseInt(_'.trim($data[0]).'.style.width)*1.1+1)+"px";';
					                echo '_'.trim($data[0]).'.style.height=(parseInt(_'.trim($data[0]).'.style.height)*1.1+1)+"px";';
					                echo '_'.trim($data[0]).'.style.top=(parseInt(_'.trim($data[0]).'.style.top)*1.1-7.8)+"px";';
					                echo '_'.trim($data[0]).'.style.left=(parseInt(_'.trim($data[0]).'.style.left)*1.1-1.2)+"px";';
					        	}
					        }
				        ?>
		        		image.style.width = image.width*1.1+'px';
		        		count--;
			    	}	
				});

			    $("#zoom_out").click(function() {
			    	if(count <= 22) {
						<?php
						    if(!empty($positions)) {
					            foreach($positions as $v) {
					                if(empty($v)||$v==''||strpos($v, '@')===false) {
					                	continue;
					                }
					                $data = explode('@', $v);
					                $data = str_replace('-', "", $data);
					                echo 'var _'.trim($data[0]).'= document.getElementById("_'.trim($data[0]).'");';
					                echo '_'.trim($data[0]).'.style.width=(parseInt(_'.trim($data[0]).'.style.width)/1.1)+"px";';
					                echo '_'.trim($data[0]).'.style.height=(parseInt(_'.trim($data[0]).'.style.height)/1.1)+"px";';
					                echo '_'.trim($data[0]).'.style.top=(parseInt(_'.trim($data[0]).'.style.top)/1.1+8)+"px";';
					                echo '_'.trim($data[0]).'.style.left=(parseInt(_'.trim($data[0]).'.style.left)/1.1+2)+"px";';
					        	}
					        }
				        ?>
	       				image.style.width=image.width/1.1+'px';
	       				count++;
			    	}
				});

				//滾輪事件
				function MouseWheel (e) {
					e = e || window.event;
					if(e.wheelDelta <= 0 && o!='edit' && count <= 22) {
						<?php
							if(!empty($positions)) {
						        foreach($positions as $v) {
						            if(empty($v)||$v==''||strpos($v, '@')===false) {
						                continue;
						            }
						            $data = explode('@', $v);
						            $data = str_replace('-', "", $data);
						            echo 'var _'.trim($data[0]).'= document.getElementById("_'.trim($data[0]).'");';
						            echo '_'.trim($data[0]).'.style.width=(parseInt(_'.trim($data[0]).'.style.width)/1.1)+"px";';
						            echo '_'.trim($data[0]).'.style.height=(parseInt(_'.trim($data[0]).'.style.height)/1.1)+"px";';
						            echo '_'.trim($data[0]).'.style.top=(parseInt(_'.trim($data[0]).'.style.top)/1.1+8)+"px";';
						            echo '_'.trim($data[0]).'.style.left=(parseInt(_'.trim($data[0]).'.style.left)/1.1+2)+"px";';
						      	}
						    }
				        ?>
	       				image.style.width=image.width/1.1+'px';
	       				count++;
					} else if(e.wheelDelta > 0 && o!='edit' && count >-5) {
						<?php
							if(!empty($positions)) {
						        foreach($positions as $k =>$v) {
						            if(empty($v)||$v==''||strpos($v, '@')===false) {
						                continue;
						            }
						            $data = explode('@', $v);
						            $data = str_replace('-', "", $data);
						            echo 'var _'.trim($data[0]).'= document.getElementById("_'.trim($data[0]).'");';
						            echo '_'.trim($data[0]).'.style.width=(parseInt(_'.trim($data[0]).'.style.width)*1.1+1)+"px";';
						            echo '_'.trim($data[0]).'.style.height=(parseInt(_'.trim($data[0]).'.style.height)*1.1+1)+"px";';
						            echo '_'.trim($data[0]).'.style.top=(parseInt(_'.trim($data[0]).'.style.top)*1.1-7.8)+"px";';
						            echo '_'.trim($data[0]).'.style.left=(parseInt(_'.trim($data[0]).'.style.left)*1.1-1.2)+"px";';
						        }
						    }
				        ?>
		        		image.style.width = image.width*1.1+'px';
		        		count--;
					}
					return false;
				}

				//網頁相容性
				if ('onmousewheel' in window) {//chrome,IE9up
				  window.onmousewheel = MouseWheel;
				} else if ('onmousewheel' in document) {//IE8
				  document.onmousewheel = MouseWheel;
				}

			    $("#setting_map").click(function() {
					$('#setting-modal').modal();
				});

				$('#setting_form [name=position]').on('keyup', function() {
					var content = $(this).val();
					var content_row = content.split("\n")
					var device_list = [];
					var device_repeat = [];
					for(var i in content_row) {
						var content_row_split = content_row[i].split("@")
						if(device_list.indexOf(content_row_split[0])!=-1) {
							device_repeat.push(content_row_split[0]);
						} else {
							device_list.push(content_row_split[0]);
						}
					}
					device_repeat = $.unique(device_repeat);
					var repeat_info = '';
					for(var i in device_repeat) {
						repeat_info += device_repeat[i] + ', ';
					}
					if(repeat_info.length>0) {
						repeat_info = repeat_info.substring(0, repeat_info.length-2);
						$('#device_repeat_info').html("機台名稱重複: " + repeat_info);
					}
				});
				
				<?php
    				if(!empty($positions)) {
        				foreach($positions as $v) {
        				    if(empty($v)||$v==''||strpos($v, '@')===false) {
        				        continue;
        				    }
        				    $data = explode('@', $v);
        				    $data = str_replace('-', "", $data);
        				    echo '$("#_'.trim($data[0]).'").css('.trim($data[1]).');';
        				}
    				}

    				if(empty($onadd_part_no) && $o!='edit') {
        				for($i=0;$i<=18;$i++) {
        					if(!empty($positions)) {
    				            foreach($positions as $v) {
    				                if(empty($v)||$v==''||strpos($v, '@')===false) {
    				                	continue;
    				                }
    				                $data = explode('@', $v);
    				                $data = str_replace('-', "", $data);
    				                echo 'var _'.trim($data[0]).'= document.getElementById("_'.trim($data[0]).'");';
    				                echo '_'.trim($data[0]).'.style.width=parseInt(_'.trim($data[0]).'.style.width)/1.1+"px";';
    				                echo '_'.trim($data[0]).'.style.height=parseInt(_'.trim($data[0]).'.style.height)/1.1+"px";';
    				                echo '_'.trim($data[0]).'.style.top=(parseInt(_'.trim($data[0]).'.style.top)/1.1+8)+"px";';
    				                echo '_'.trim($data[0]).'.style.left=(parseInt(_'.trim($data[0]).'.style.left)/1.1+2)+"px";';
    				        	}
    				        }
    				        
        					echo 'image.style.width=image.width/1.1+"px";';
        				}
    				}
    				
				?>

			    <?php if(!empty($onadd_part_no)) {?>
				    var onadd_part_no = "<?php echo $onadd_part_no;?>";
				    if($("#_"+onadd_part_no).length==0) {
				    	onadd_part_no = onadd_part_no + '-1';
				    }
				    if($("#_"+onadd_part_no).length>0) {
    					$("html, body").animate({ scrollTop: $("#_"+onadd_part_no).offset().top-300,  scrollLeft: $("#_"+onadd_part_no).offset().left-300}, 100);
    					$("#_"+onadd_part_no).css({"background-color":"#FF3333"});
    					$("#_"+onadd_part_no).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300).fadeOut(300).fadeIn(300);
				    }
			    <?php }?>


			    $(".edit_mode").click(function() {
					location.href = "./map.php?area=<?php echo $area;?>&o=edit";
				});
			});
		</script>
    </head>
    
    <body>
    	<!--top bar start-->
	<?php include('./../htmlModule/nav.php');?>

        <!--main content start-->
        <section class="main-content">



        	<!--page header start-->
        	<div class="page-header">
        		<div class="row">
        			<div class="col-sm-6">
        				<h4>區域管理</h4>
        			</div>
        		</div>
        	</div>
		<!-- container -->
    	<div id="device_modal" class="modal fade">
			<div class="modal-dialog modal-lg">
			    <div class="modal-content">
			            <div class="modal-body">
			                <p>Loading...</p>
			            </div>
			            <div class="modal-footer">
			                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			            </div>
			    </div>
			</div>
		</div>

    	<?php include('./../htmlModule/nav.php');?>
    	
    	<!-- modal -->
		<div id="setting-modal" class="modal setting-modal" tabindex="-1" role="dialog">
		    <div class="modal-dialog modal-lg">
		        <div class="modal-content">
					<form autocomplete="off" method="post" action="./map.php" id="setting_form" class="form-horizontal" role="form" enctype="multipart/form-data">
			            <div class="modal-header">
			                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			                <h4 class="modal-title">設定地圖</h4>
			            </div>
			            <div class="modal-body">
			            	<div class="row">
				        		<input type="hidden" name="op" value="setting">
				        		<input type="hidden" name="area" value="<?php echo $area;?>">
								<div class="form-group">
									<label for="addModalInput1" class="col-sm-2 control-label">位置</label>
									<div class="col-sm-9">
										<textarea class="form-control" rows="15" name="position"><?php echo $position_data;?></textarea>
										<div class="help-block" style="color: red" id="device_repeat_info"></div>
										<div class="help-block"><ul>
										</ul></div>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label">上傳地圖圖檔</label>
									<div class="col-sm-10">
										<div class="form-control-static">
											<input type="file" name="file">
											<div class="help-block">未更新不用上傳</div>
										</div>
									</div>
								</div>
							</div>
			            </div>
						<div class="modal-footer">
			            	<button type="button" class="btn btn-default edit_mode">進入抓取模式</button>
			            	<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
			            	<button type="submit" class="btn btn-primary">儲存</button>
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
					<?php $x = date('YmdH', time());?>
					<img id="map_img" src="./../../uploads/map/img/<?php echo $area;?>.jpg?x=<?php echo $x?>">
					
    				<?php
        				if(!empty($positions)) {
            				foreach($positions as $v) {
            				    if(empty($v)||$v==''||strpos($v, '@')===false) {
            				        continue;
            				    }
            				    $data = explode('@', $v);
            				    $data = str_replace('-', "", $data);
            				    echo '<div class="device" id="_'.trim($data[0]).'"></div>';
            				}
        				}
    				?>
				</div>
			</div>
		</div>
		
		<?php if(in_array($_SESSION['user']['jsuser_admin_permit'], array(0,1,2,4))) {?>
    		<div id="setting_map" style="position: fixed; bottom: 145px; right: 15px; display: block; z-index: 100;">
    			<span class="glyphicon glyphicon-info-sign" style="font-size:60px; color:#e77268;"></span>
    		</div>
		<?php }?>
		<div id="zoom_in" style="position: fixed; bottom: 75px; right: 15px; display: block; z-index: 100;">
			<span class="glyphicon glyphicon-plus-sign" style="font-size:60px; color:#e77268;"></span>
		</div>
		<div id="zoom_out" style="position: fixed; bottom: 5px; right: 15px; display: block; z-index: 100;">
			<span class="glyphicon glyphicon-minus-sign" style="font-size:60px; color:#e77268;"></span>
		</div>

		<!--Start footer-->
        	<footer class="footer">
        		<span>Copyright &copy; 2019. Online Plant</span>
        	</footer>
        	<!--end footer-->

        </section>
        <!--end main content-->

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