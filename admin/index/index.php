<?php
include_once("./func_plant_purchase.php");


$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'search_dayreport':
			// $ret_msg = "test";
			$day = GetParam('day');
			$ret_msg = "搜尋成功!";
			$ret_data = getQuantity_Day($day);
		break;
		case 'get_expected_data':
			// $ret_msg = "test";
			$ret_code = 1;
			$ret_data = getWorkListByMonth();
		break;
	}
	//提醒視窗
	// echo enclode_ret_data($ret_code, $ret_msg, $ret_data);
	// exit;
}
else{
	// search
	if(($onadd_part_no = GetParam('onadd_part_no'))) {
		$search_where[] = "onadd_part_no like '%{$onadd_part_no}%'";
		$search_query_string['onadd_part_no'] = $onadd_part_no;
	}
	if(($onadd_part_name = GetParam('onadd_part_name'))) {
		$search_where[] = "onadd_part_name like '%{$onadd_part_name}%'";
		$search_query_string['onadd_part_name'] = $onadd_part_name;
	}
	if(($onadd_supplier = GetParam('onadd_supplier'))) {
		$search_where[] = "onadd_supplier like '%{$onadd_supplier}%'";
		$search_query_string['onadd_supplier'] = $onadd_supplier;
	}
	if(($onadd_status = GetParam('onadd_status', -1))>=0) {
		$search_where[] = "onadd_status='{$onadd_status}'";
		$search_query_string['onadd_status'] = $onadd_status;
	}
	if(($onadd_growing = GetParam('onadd_growing', -1))>=0) {
		$search_where[] = "onadd_growing='{$onadd_growing}'";
		$search_query_string['onadd_growing'] = $onadd_growing;
	}
	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';

	$size_mapping = array(
		1=>'<font color="#666666">1.7寸</font>',
		2=>'<font color="#666666">2.5寸</font>',
		3=>'<font color="#666666">2.8寸</font>',
		4=>'<font color="#666666">3.0寸</font>',
		5=>'<font color="#666666">3.5寸</font>',
		6=>'<font color="#666666">3.6寸</font>',
		7=>'<font color="#666666">其他</font>' 
	);
	// printr(getWorkListByMonth());
	// exit;
	// printr(getScheduleData());
	// exit;

		// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getUserQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	$product_list = getWorkListByMonth();
	$week_list = getQuantity_Day(GetParam('day'));
	$TotalQty = getTotalQty();

	for($i=0;$i<count($week_list);$i++){
		if($i<count($week_list)-1){
			$str1 .= "'".$week_list[$i]['date1']."',";
			$str2 .= "'".$week_list[$i]['date2']."',";
			$str3 .= "'".$week_list[$i][0]."',";
			$str4 .= "'".$week_list[$i][2]."',";
			$str5 .= "'".$week_list[$i][1]."',";
		}
		else{
			$str1 .= "'".$week_list[$i]['date1']."'";
			$str2 .= "'".$week_list[$i]['date2']."'";
			$str3 .= "'".$week_list[$i][0]."'";
			$str4 .= "'".$week_list[$i][2]."'";
			$str5 .= "'".$week_list[$i][1]."'";
		}
	}

	$SellQuantity = getSellQuantity(GetParam('year'));
	$EliminationQuantity = getEliminationQuantity(GetParam('year'));
	$sell_data = "";
	$elim_data = "";
	$months = "";
	for($i=1;$i<=12;$i++){
		$sell_data .= "'".$SellQuantity[$i]['quantity']."',";
		$elim_data .= "'".$EliminationQuantity[$i]['quantity']."',";
		$months .= "'".$i."月',";
	}	   
	$sell_data = substr($sell_data, 0, -1);
	$elim_data = substr($elim_data, 0, -1);
	$months = substr($months, 0, -1);

	$sum17 = getDetails('1');//計算1.7
	$sum25 = getDetails('2');//計算2.5
	$sum28 = getDetails('3');//計算2.8
	$sum30 = getDetails('4');//計算3.0
	$sum35 = getDetails('5');//計算3.5
	$sum36 = getDetails('6');//計算3.6
	$sum37 = getDetails('7');//計算其他
	$sum38 = getDetails('8');//計算瓶苗開瓶
	$others = $sum28+$sum30+$sum36+$sum37;
	$supplier_list = getAllSupplierData();
	// printr(getWorkListByMonth());
	// exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title><?php echo CN_NAME;?></title>
	<!-- Common plugins -->
	<!-- <link href="./../img/apple-touch-icon.png" rel="apple-touch-icon"> -->
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
	<script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
    <script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.zh-TW.js" charset="UTF-8"></script>
	<link rel="stylesheet" href="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
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

	<script>		
		$(document).ready(function () {
			var chart,chart2;
			var count = 0;
			var flag = 1;
			var plant_price = <?php echo json_encode($plant_price);?>;
			
			<?php 
			if(!empty(GetParam('year')))
				echo '$("#datetimepicker2").val(\''.GetParam('year').'\');';
			if(!empty(GetParam('day')))
				echo '$("#datetimepicker1").val(\''.GetParam('day').'\');';
			?>

	        $(document).on('show.bs.modal', '.modal', function (event) {
	            var zIndex = 1040 + (10 * $('.modal:visible').length);
	            $(this).css('z-index', zIndex);
	            setTimeout(function() {
	                $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
	            }, 0);
	        });

			$(function () {
				$.ajax({
				url: './index.php',
				type: 'post',
				dataType: 'json',
				data: {op:"get_expected_data"},
				beforeSend: function(msg) {
					$("#ajax_loading").show();
				},
				complete: function(XMLHttpRequest, textStatus) {
					$("#ajax_loading").hide();
				},
				success: function(ret) {	
				// console.log("123"+ret);				
					if(ret.code==1) {
				        var data = ret.data;							
						var main_content = document.getElementById("main_content");
						var event = "";
						var btn_type = "";

						if(data[data.length-1].sum > 0){
							var dy_modal = document.createElement("div");
							dy_modal.setAttribute('class', 'modal fade');
							dy_modal.setAttribute('id', 'myModal'+i);
							dy_modal.setAttribute('role', 'dialog');
							dy_modal.setAttribute("style", "z-index: "+i+";");
							dy_modal.innerHTML = "<div class='modal-dialog modal-lg' style='z-index:1;'><div class='modal-content'><div class='modal-body'><div class=\"panel panel-info\"><div class=\"panel-heading\"><h4 class=\"modal-title\">提醒事項</h4></div><div class=\"panel-body\" style=\"font-size: 1.4rem\"><label>您有 "+data[data.length-1].sum+" 項本周待辦事項尚未處理，請點擊以下連結前往處理。</label><br><label></label><a href=\"<?php echo WT_SERVER.'/admin/schedule/plant_re_schedule.php'?>\">點我連結至本周待辦事項！</a><br><br><br><br><br></div></div></div><div class='modal-footer'><button type='button' class='btn btn-default' data-dismiss=\"modal\" id=\"btn_modal\">關閉</button></div></div></div>";		
							main_content.appendChild(dy_modal);	
							// $('#myModal'+i).modal('show');
						}

						for (var i = 0; i < data.length-1; i++) {
							var dy_modal = document.createElement("div");
							dy_modal.setAttribute('class', 'modal fade');
							dy_modal.setAttribute('id', 'myModal'+i);
							dy_modal.setAttribute('role', 'dialog');
							if(data[i]['onadd_planting_date_unix'] >= data[i]['expected_date_unix']){
								if(data[i]['isSell'] == 9){
									expected_title = "預計出貨日：";
									event = "已經超過出貨日期，請安排作業";
									btn_type = "ship";
								}
								else{
									expected_title = "預計成長日：";
									event = "已經超過換盆日期，請安排作業";
									btn_type = "basin";
								}
							  	
							}
							else{
								if(data[i]['isSell'] == 9){
									expected_title = "預計出貨日：";
									event = "即將到達出貨日期";
									btn_type = "ship";
								}
								else{
									expected_title = "預計成長日：";
									event = "即將到達換盆日期";
									btn_type = "basin";
								}
							}

							if(data[i]['onadd_quantity'] > 0){
								dy_modal.innerHTML = "<div class='modal-dialog modal-'><div class='modal-content'><div class='modal-body'><div class=\"panel panel-info\"><div class=\"panel-heading\"><h4 class=\"modal-title\">提醒事項</h4></div><div class=\"panel-body\" style=\"font-size: 1.4rem\"><label>品號："+data[i]['onadd_part_no']+"</label></br><label>品名："+data[i]['onadd_part_name']+"</label></br><label>下種日："+data[i]['onadd_planting_date']+"</label></br><label>"+expected_title+data[i]['expected_date']+"</br><label>數量："+data[i]['onadd_quantity']+"</label></br><label>提醒事項："+event+"</label></div></div></div><div class='modal-footer'><button type='button' class='btn btn-default upd_"+btn_type+"' data-dismiss=\"modal\" data-onadd_sn=\""+data[i]['onadd_sn']+"\">執行</button><button type='button' class='btn btn-default' data-dismiss=\"modal\">關閉</button></div></div></div>";		
								main_content.appendChild(dy_modal);	
								$('#myModal'+i).modal('show');
							}																					
						}

						//換盆-----------------------------------------------------------
						$('button.upd_basin').on('click', function(){
							$('#upd-modal').modal();
							$('#upd_form')[0].reset();
							$.ajax({
								url: '../purchase/plant_purchase.php',
								type: 'post',
								dataType: 'json',
								data: {op:"get", onadd_sn:$(this).data('onadd_sn')},
								beforeSend: function(msg) {
									$("#ajax_loading").show();
								},
								complete: function(XMLHttpRequest, textStatus) {
									$("#ajax_loading").hide();
								},
								success: function(ret) {
							            if(ret.code==1) {
								            var d = ret.data;		
							                $('#upd_form input[name=onadd_sn]').val(d.onadd_sn);
							                $('#upd_form input[name=onadd_ml]').val(d.onadd_ml);
							                $('#upd_form input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
							                $('#upd_form input[name=onadd_AB_sn]').val(d.onadd_AB_sn);
							                $('#upd_form input[name=onadd_level]').val(d.onadd_level);	
							                $('#upd_form input[name=onadd_type]').val(d.onadd_type);

							                $('#upd_form input[name=onadd_part_no]').val(d.onadd_part_no);
							                $('#upd_form input[name=onadd_part_name]').val(d.onadd_part_name);
							                $('#upd_form input[name=onadd_color]').val(d.onadd_color);
							                $('#upd_form input[name=onadd_size]').val(d.onadd_size);
							                $('#upd_form input[name=onadd_height]').val(d.onadd_height);
							                $('#upd_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
							                $('#upd_form input[name=onadd_supplier]').val(d.onadd_supplier);			  
							                $('#upd_form [name=onadd_location] option[value='+d.onadd_location+']').prop('selected','selected');	 
							                $('#upd_form input[name=onadd_sellsize]').val(d.onadd_sellsize);	
							                $('#upd_form input[name=onadd_other_price]').val(d.onadd_other_price);	

							                $('#upd_form input[name=onadd_quantity]').val(d.onadd_quantity);
							                $('#upd_form [name=onadd_cur_size] option[value='+d.onadd_growing+']').prop('selected','selected','selected','selected','selected','selected','selected');
							                $('#upd_form [name=onadd_growing] option[value='+d.onadd_growing+']').prop('selected','selected','selected','selected','selected','selected','selected');		                	
							                $('#upd_form [name=onadd_status] option[value='+d.onadd_status+']').prop('selected','selected');
							            }
						           },
						           error: function (xhr, ajaxOptions, thrownError) {
						            	// console.log('ajax error');
						                // console.log(xhr);
						            }
						        });
						});

						//出貨-----------------------------------------------------------
						$('button.upd_ship').on('click', function(){
							$('#upd-modal2').modal();
							$('#upd_form2')[0].reset();

							$.ajax({
								url: '../purchase/plant_purchase.php',
								type: 'post',
								dataType: 'json',
								data: {op:"get", onadd_sn:$(this).data('onadd_sn')},
								beforeSend: function(msg) {
									$("#ajax_loading").show();
								},
								complete: function(XMLHttpRequest, textStatus) {
									$("#ajax_loading").hide();
								},
								success: function(ret) {
						                if(ret.code==1) {
						                	var d = ret.data;
						                	$('#upd_form2 input[name=onadd_sn]').val(d.onadd_sn);
						                	$('#upd_form2 input[name=onadd_part_name]').val(d.onadd_part_name);
						                	$('#upd_form2 input[name=onadd_part_no]').val(d.onadd_part_no);
						                	$('#upd_form2 input[name=onadd_quantity]').val(d.onadd_quantity);
											$('#upd_form2 input[name=onadd_location]').val(d.onadd_location);
						                	$('#upd_form2 input[name=onadd_ml]').val(d.onadd_ml);
						                	$('#upd_form2 input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
						                	$('#upd_form2 input[name=total_cost_shipment]').val(d.onadd_total);
						                	$('#upd_form2 input[name=total_cost_week]').val(d.onadd_weekday);
						                }
						            },
						            error: function (xhr, ajaxOptions, thrownError) {
					                }
					            });
						});
				//出貨-----------------------------------------------------------
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {	
				    	console.log('ajax error');
				    }
				});

        //page view chart
        <?php 
        //出貨統計表資料
        echo "$('#quantity_title').html(\"".GetParam('year')."年出貨報表\");";
		//廠區使用空間計算
		$UsedQuantity = getUsedQuantity()[0]['add_quantity'] - (getUsedQuantity()[1]['elda_quantity']+getUsedQuantity()[2]['ship_quantity']);
		
        ?>
        //直方圖
        chart2 = c3.generate({
        	bindto: '#stocked',
        	data: {
        		columns: [
        		['出貨數量', <?php echo $sell_data;?>],
        		['損耗數量', <?php echo $elim_data;?>]
        		],
        		colors: {
        			出貨數量: '#23b7e5',
        			損耗數量: '#ddd'
        		},
        		type: 'bar',
        		groups: [
        		['出貨數量', '損耗數量']
        		]
        	},
        	axis: {
		    x: {
		        type: 'category',
		        categories: [<?php echo $months;?>]
		    }
		}
        });
        //日報表
        chart = c3.generate({
        	bindto: '#timeseriesChart',
        	data: {
        		x: 'x',
                xFormat: '%Y%m%d', // 'xFormat' can be used as custom format of 'x'
                columns: [
                	// ['x','2019-10-24','2019-10-23'],
                	// ['x','20191024','20191023'],
                	// ['下種','0','10']
                	// ,['出貨','0','20']
                	// ,['耗損','50','30']    
                <?php 
                	echo "['x',".$str1."],";
	                echo "['x',".$str2."],";
	                // echo "['下種',".$str3."],";
	                echo "['出貨',".$str4."],";
	                echo "['汰除',".$str5."],";
                ?>
                
                // ['x', <?php echo "'".date("Y-m-d",time())."'"; ?>],
                // ['x', <?php echo "'".date("Ymd",time())."'"; ?>],
                // ['下種', <?php echo getQuantity_Day(date("Y/m/d",time()))[0]['add_quantity']; ?>],
                // ['出貨', <?php echo getQuantity_Day(date("Y/m/d",time()))[1]['elda_quantity']; ?>],
                // ['耗損', <?php echo getQuantity_Day(date("Y/m/d",time()))[2]['ship_quantity']; ?>]
                // ],
                ],
                colors: {
                	// 進貨: '#23b7e5',
                	出貨: '#2ECC71 ',
                	汰除: '#C70039 '
                }
            },
            axis: {
            	x: {
            		type: 'timeseries',
            		tick: {
            			format: '%Y/%m/%d'
            		}
            	}
            }
        });

        // setTimeout(function () {
        // 	chart.load({
        // 		columns: [
        // 		['進貨', 30, 200],
        //         ['出貨', 130, 340],
        //         ['耗損', 400, 500]
        // 		]
        // 	});
        // }, 1000);
        //pie chart
        c3.generate({
        	bindto: '#pieChart',
        	data: {
        		columns: [
        		['已使用', <?php echo $UsedQuantity; ?>],
        		['未使用', <?php echo (getSpace()[0]['onsp_space']-(int)$UsedQuantity); ?>]
        		],
        		colors: {
        			Remains: '#F44336',
        			Used: '#50cdf4'
        		},
        		type: 'pie'
        	},
			  pie: {
			    label: {
			      format: function(value, ratio, id) {
			        return value;
			      }
			    }
			  }
        });
    });

	$('.i-checks').iCheck({
		checkboxClass: 'icheckbox_square-blue',
		radioClass: 'iradio_square-blue'
	});

	$('#datetimepicker1, #datetimepicker3, #onshda_add_date').datetimepicker({
		minView: 2,
		language:  'zh-TW',
		format: 'yyyy-mm-dd',
		useCurrent: false
	});

	$('#datetimepicker2').datetimepicker({
		startView: 4,   
		minView: 4, 
		format: 'yyyy',
		language:  'zh-TW',
		useCurrent: false
	});

	$('#change_basin_next_size').change(function () {       
	     var n = $('#change_basin_next_size').val();				        
	     $('#upd_form input[name=onadd_price_per_plant]').val(plant_price[n]);
	 });

	$('#search_yearreport').click(function() {
		var year = $('#datetimepicker2').val();
		var day = $('#datetimepicker1').val();
		var time = "?year="+year+"&day="+day;
		window.location.href = <?php echo "'".WT_SERVER.'/admin/index/index.php'."'+"?>time;
	});

	$('#search_dayreport').click(function() {
		var year = $('#datetimepicker2').val();
		var day = $('#datetimepicker1').val();
		var time = "?year="+year+"&day="+day;
		window.location.href = <?php echo "'".WT_SERVER.'/admin/index/index.php'."'+"?>time;
	});

	//出貨、換盆後動作----------------------------------------------------------
	$('#upd_form, #upd_form2, #eli_form1').validator().on('submit', function(e) {
	if (!e.isDefaultPrevented()) {
		// var files = $("#myFile").get(0).files;   
		e.preventDefault();
		var param = $(this).serializeArray();
		$(this).parents('.modal').modal('hide');
		var quantity = 0;
		// console.log(param);
		$(this)[0].reset();
		 	$.ajax({
		 		url: '../purchase/plant_purchase.php',
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
		 			if(ret.msg != '出貨完成！' && ret.msg != '換盆成功！'){
		 				alert(ret.msg);
		 			}	
		 			else{	
		 				if(ret.msg == '出貨完成！'){
		 					quantity = param[7]['value'] - param[9]['value'];
		 				}
		 				else{
		 					quantity = param[10]['value'] - param[17]['value'];
		 				}

		 				if(quantity > 0){		 			
			 				bootbox.confirm({
			 					message:ret.msg+"，庫存剩餘數量是否汰除？",
			 					buttons: {
							        confirm: {
							            label: '保留',
							            className: 'btn-primary'
							        },
							        cancel: {
							            label: '汰除',
							            className: 'btn-danger'
							        }
							    },
							    callback: function(result) {
									if(result) {																					
										$.ajax({
											url: '../purchase/plant_purchase.php',
											type: 'post',
											dataType: 'json',
											data: {op:"DelayAmonth", onadd_sn:param[1]['value']},
											beforeSend: function(msg) {
												$("#ajax_loading").show();
											},
											complete: function(XMLHttpRequest, textStatus) {
												$("#ajax_loading").hide();
											},
											success: function(ret) {
		        							        alert(ret.msg);
		        							    },
		        							    error: function (xhr, ajaxOptions, thrownError) {

		        							    }
		        						});
									}
									else{
										$('#eli-modal1').modal();
										$.ajax({
											url: '../purchase/plant_purchase.php',
											type: 'post',
											dataType: 'json',
											data: {op:"get", onadd_sn:param[1]['value']},
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
		        							        	$('#eli_form1 input[name=onadd_sn]').val(d.onadd_sn);
		        							        	$('#eli_form1 input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
		        							        	$('#eli_form1 input[name=onadd_part_no]').val(d.onadd_part_no);
		        							        	$('#eli_form1 input[name=onadd_part_name]').val(d.onadd_part_name);
		        							        	$('#eli_form1 input[name=onadd_quantity]').val(d.onadd_quantity);
		        							        	$('#eli_form1 input[name=onriadd_other_item]').val(d.onriadd_other_item);
		        							        	$('#eli_form1 input[name=onadd_other_price]').val(d.onadd_other_price);	
		        							        }
		        							    },
		        							    error: function (xhr, ajaxOptions, thrownError) {

		        							    }
		        						});
									}
								}
							});

			 			}
			 			else if(quantity < 0){
			 				alert("錯誤！輸入數量高於原始數量！");
			 			}
			 			else{
			 				alert(ret.msg);
			 			}
		 			}					            
		 		},
		 		error: function (xhr, ajaxOptions, thrownError) {
		        	console.log('ajax error');
		         //     console.log(thrownError);
		         }
		     });
		 }
	});

	//出貨、換盆後動作----------------------------------------------------------


});
</script>>
</head>

<body>
	
	<?php include('./../htmlModule/nav.php');?>
	<!--main content start-->
	<section class="main-content" id="main_content">



		<!--page header start-->
		<div class="page-header">
			<div class="row">
				<div class="col-sm-6">
					<h4>統計表圖表</h4>
				</div>
			</div>
		</div>

		<!--汰除----------------------------------------------------------->
		<div id="eli-modal1" class="modal upd-modal1" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="eli_form1" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">汰除</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd1">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_newpot_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品號<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品名<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">剩餘數量<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">汰除數量<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity_del" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label">汰除原因<font color="red">*</font></label>
										<div class="col-md-10">
											<select class="form-control" name="onelda_reason">
												<option value="4">其他</option>
												<option value="3">黑頭</option>
												<option value="2">褐斑</option>
												<option selected="selected" value="1">軟腐</option>
											</select>
										</div>
									</div>        								
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-primary keep" data-dismiss="modal">保留</button>
							<button type="submit" class="btn btn-danger">汰除</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--汰除----------------------------------------------------------->

		<!--出貨----------------------------------------------------------->
		<div id="upd-modal2" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form2" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">出貨</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd2">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_ml">
									<input type="hidden" name="onadd_newpot_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品名</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder=""  maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>									
										<label for="addModalInput1" class="col-md-2 control-label">品號<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">放置區<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_location" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>									
										<label for="addModalInput1" class="col-md-2 control-label">可供出貨數量<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 

									<div class="form-group">
										<label class="col-md-2 control-label">出貨日期&nbsp;<font color="red">*</font></label>
										<div class="col-md-4">
											<div class="input-group">
											    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											    <input type="text" class="form-control" id="onshda_add_date" name="onshda_add_date" value="" placeholder="">
											</div>											
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">出貨數量<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_plant_year" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>		
										<!-- <label for="addModalInput1" class="col-md-2 control-label">價格(單棵)<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onshda_price" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div> -->
									</div>  

									<div class="form-group">																	
										<label for="addModalInput1" class="col-md-2 control-label">出貨對象<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onshda_client"  id='onshda_client'>
												<?php
												foreach ($supplier_list as $key => $value) {
													echo '<option value="'.$value['onsd_name'].'">'.$value['onsd_name'].'</option>';
												}
												?>
											</select>
										</div>
										<!-- <label for="addModalInput1" class="col-md-2 control-label">每株代工價格</label>
										<label class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: right;width: 5.5rem;">共種植</label>
										<div class="col-md-1" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 5.5rem;">	
											<input type="text" class="form-control" id="total_cost_week" name="total_cost_week" placeholder=""  minlength="1" maxlength="32"  style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;padding-left: 0px;padding-right: 0px;width: 5.5rem;">
										</div>
										<label class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 5.5rem;">週，共計</label>
										<div class="col-md-1" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 5.5rem;">	
											<input type="text" class="form-control" id="total_cost_shipment" name="total_cost_shipment" placeholder=""  minlength="1" maxlength="32"  style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;padding-left: 0px;padding-right: 0px;">
										</div>									
										<label class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 5.5rem;">元</label> -->
									</div>  


									<!-- <div class="form-group">	
										<div class="col-md-6" ></div>
										<label for="addModalInput1" class="col-md-2 control-label">其他價格</label>
										<div class="col-md-1" style="">	
											<input type="text" class="form-control" id="onriadd_other_item" name="onriadd_other_item" placeholder="" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;padding-left: 0px;padding-right: 0px;">
										</div>
										<label class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 1rem;">，</label>
										<div class="col-md-1" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 5.5rem;">	
											<input type="text" class="form-control" id="onadd_other_price" name="onadd_other_price" placeholder="" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;padding-left: 0px;padding-right: 0px;">										
										</div>
										<label class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 2rem;">元</label>			
									</div>    		 -->						
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">確認出貨</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--出貨----------------------------------------------------------->

		<div id="upd-modal" class="modal upd-modal" tabindex="-1" role="dialog" >
			<div class="modal-dialog modal-lg" style='z-index:999;'>
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">換盆</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_newpot_sn">
									<input type="hidden" name="onadd_ml">
									<input type="hidden" name="onadd_AB_sn">
									<input type="hidden" name="onadd_sellsize">
									<input type="hidden" name="onadd_other_price">
									<input type="hidden" name="bill_mode">
									<input type="hidden" name="onadd_level">
									<input type="hidden" name="onadd_type">	
									
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品號<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>									
										<label for="addModalInput1" class="col-md-2 control-label">品名</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder=""  maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">放置區<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onadd_location">
												<option value="B5">B5</option>
												<option value="B4">B4</option>
												<option value="B3">B3</option>
												<option value="B2">B2</option>
												<option value="B1">B1</option>
												<option value="A5">A5</option>
												<option value="A4">A4</option>
												<option value="A3">A3</option>
												<option value="A2">A2</option>
												<option selected="selected" value="A1">A1</option>
											</select>
										</div>
										<label for="addModalInput1" class="col-md-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">花色</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_color" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">花徑</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_size" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">高度</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_height" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">適合開花盆徑</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_pot_size" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">供應商</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_supplier" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label">換盆日期&nbsp;<font color="red">*</font></label>
										<div class="col-md-4">
											<div class="input-group">
											    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											    <input type="text" class="form-control" id="datetimepicker3" name="onadd_planting_date" value="<?php echo (empty($device['onadd_planting_date'])) ? '' : date('Y-m-d', $device['onadd_planting_date']);?>" placeholder="">
											</div>											
											<div class="help-block with-errors"></div>
										</div>
										<label for="addModalInput1" class="col-md-2 control-label" >換盆數量<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_replant_number" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>	
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label" >即將換盆至<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onadd_cur_size" id='change_basin_next_size'>
												<option value="8">瓶苗開瓶</option>
												<option value="7">其他</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
										<!-- <label class="col-md-2 control-label">下一階段狀態<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onadd_next_status" id='change_basin'>
												<option selected="selected" value="1">換盆</option>
												<option value="2">催花</option>
												<option value="3">出貨</option>
											</select>
										</div> -->
										<label class="col-md-2 control-label">下一階段狀態<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onadd_growing">
												<option value="UrgeFLowers">催花</option>
												<option value="Shipment">出貨</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>	
									<!-- <div class="form-group" id="form_3" >
										<div class="col-md-6">
										</div>
										<label class="col-md-2 control-label">下一階段狀態<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onadd_growing">
												<option value="UrgeFLowers">催花</option>
												<option value="online_shipment_data">出貨</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div> -->
									<div class="form-group" id="form_4" style="display: none;">
										<div class="col-md-6">
										</div>
										<label class="col-md-2 control-label">催花規格<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_foundry_type" placeholder="規格" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;">種植人員</label>
										<div class="col-md-2">
											<input type="text" class="form-control" id="autocomplete_onadd_plant_staff" name="onadd_plant_staff" placeholder="">
											<div class="help-block with-errors"></div>
										</div>

										<label for="addModalInput1" class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: right;">換盆費用</label>
										<div class="col-md-3">											
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">

											<input type="text" class="form-control" name="onadd_price_per_plant" placeholder="費用" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-left: 0px;border-left: 0px;height: 28px;padding-left: 0px;text-align: center;" value="4">
											</div>
											<label for="addModalInput1" class="col-md-4 control-label" style="text-align: left;width: 5rem;padding-left: 0px;">每株</label>											
											<div class="help-block with-errors"></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">確認</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--start page content-->

		<div class="row">
			<div class="col-lg-3 col-md-6 col-sm-12">
				<div class="widget bg-primary padding-0">
					<div class="row row-table">
						<div class="col-xs-4 text-center pv-15 bg-light-dark">
							<em class=" fa-2x">1.7寸</em>
						</div>
						<div class="col-xs-8 pv-15 text-center">
							<?php
							if(empty($sum17))
								echo "<h2 class='mv-0'>".'0'."</h2>" ;
							else
							echo "<h2 class='mv-0'>"."<a style='text-decoration:none;color:white;' href='./../purchase/plant_purchase_details1234.php?onproduct_size=1'>".$sum17."</a>"."</h2>" ;
							?>
						</div>
					</div>
				</div><!--end widget-->
			</div><!--end col-->
			<div class="col-lg-3 col-md-6 col-sm-12">
				<div class="widget bg-teal padding-0">
					<div class="row row-table">
						<div class="col-xs-4 text-center pv-15 bg-light-dark">
							<em class="fa-2x">2.5寸</em>
						</div>
						<div class="col-xs-8 pv-15 text-center">
							<?php
							if(empty($sum25))
								echo "<h2 class='mv-0'>".'0'."</h2>" ;
							else
								echo "<h2 class='mv-0'>"."<a style='text-decoration:none;color:white;' href='./../purchase/plant_purchase_details1234.php?onproduct_size=2'>".$sum25."</a>"."</h2>" ;
							?>
						</div>
					</div>
				</div><!--end widget-->
			</div><!--end col-->

			<div class="col-lg-3 col-md-6 col-sm-12">
				<div class="widget bg-success padding-0">
					<div class="row row-table">
						<div class="col-xs-4 text-center pv-15 bg-light-dark">
							<em class="fa-2x">3.5寸</em>
						</div>
						<div class="col-xs-8 pv-15 text-center">
							<?php
							if(empty($sum35)){
								echo "<h2 class='mv-0'>".'0'."</h2>" ;
							}else{
								echo "<h2 class='mv-0'>"."<a style='text-decoration:none;color:white;' href='./../purchase/plant_purchase_details1234.php?onproduct_size=6'>".$sum35."</a>"."</h2>" ;
							}
							?>
						</div>
					</div>
				</div><!--end widget-->
			</div><!--end col-->

			<div class="col-lg-3 col-md-6 col-sm-12">
				<div class="widget bg-indigo padding-0">
					<div class="row row-table">
						<div class="col-xs-4 text-center pv-15 bg-light-dark">
							<em class="fa-2x">其他</em>
						</div>
						<div class="col-xs-8 pv-15 text-center">
							<h2 class="mv-0">
								<?php 
								// if(empty($sum35))
								// 	echo "<h2 class='mv-0'>0</h2>" ;								
								// else
								if(empty($others)){
									echo "<h2 class='mv-0'>".'0'."</h2>" ;
								}else{
									echo "<h2 class='mv-0'>"."<a style='text-decoration:none;color:white;' href='./../purchase/plant_purchase.php?onadd_growing=7'>".$others."</a>"."</h2>" ;
								}
								?>
									
								</h2>
						</div>
					</div>
				</div><!--end widget-->
			</div><!--end col-->
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="row">							
							<div class="col-lg-3 col-md-6 col-sm-12" style="width: 140px; padding-right: 10px;">
								<input type="text" name="fname" id="datetimepicker2" placeholder="在此輸入年份" />
							</div>
							<div class="col-lg-3 col-md-6 col-sm-12" style="    padding-left: 0px;    padding-right: 0px;    width: 50px;">
								<button id="search_yearreport" class="btn btn-info">搜尋</button>
							</div>
						</div>
					<div class="panel-heading">
						<div id="quantity_title" style="width: 150px;">出貨報表</div> 
					</div>
					<div class="panel-body">
						<div>
							<div id="stocked"></div>
						</div>
					</div>
				</div>
			</div><!--col-md-12-->
			<div class="col-md-6">
				<div class="panel panel-default">
						<div class="row">
							<div class="col-lg-3 col-md-6 col-sm-12" style="width: 140px;padding-right: 0px;">
								<input type="text" name="search_dayreport_start" id="datetimepicker1"  placeholder="輸入日期" />
							</div>

							<div class="col-lg-3 col-md-6 col-sm-12" style="    padding-left: 10px;    padding-right: 0px;    width: 50px;">
								<button id="search_dayreport" class="btn btn-info" >搜尋</button>
							</div>
						</div>
					<div class="panel-heading">
						周報表
					</div>
					<div class="panel-body">
						<div>
							<div id="timeseriesChart"></div>
						</div>

					</div>
				</div>
			</div><!--col-->
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						栽培區
					</div>
					<div class="panel-body">
						<a href="./../map/map.php?area=0001"  target="_blank">
							<img style="width: 100%;height: 100%;object-fit:cover;" src="./../../uploads/map/img/0001-1.jpg">
						</a>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-heading">
						空間統計 <small class="text-muted">剩餘存放空間</small>
					</div>
					<div class="panel-body">
						<div>
							<div id="pieChart"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class='modal fade ' id='myModal' role='dialog'>
			<div class='modal-dialog'>
				<div class='modal-content'>
					<div class='modal-body'>
						<div class="panel panel-info">
					    	<div class="panel-heading">
					    		<h4 class="modal-title">提醒事項</h4>
					    	</div>
					    	<div class="panel-body" style="font-size: 1.4rem">					    	
					    		<label>品號：</label><label id="onadd_part_no"></label>
								</br>
								<label>品名：</label><label id="onadd_part_name"></label>
								</br>
								<label>下種日：</label><label id="onadd_planting_date"></label>
								</br>
								<label>預計成長日：</label><label id="onadd_expected_date"></label>
								</br>
								<label>數量：</label><label id="onadd_quantity"></label>
								</br>
								<label>提醒事項：</label><label id="onadd_content"></label>
					    	</div>
					    </div>
					</div>
					<div class='modal-footer'>
						<button type='button' class='btn btn-default' id="btn_modal">確認</button>
					</div>
				</div>
			</div>
		</div>
	<div class="row">
	<div class="col-md-6">

	</div><!--end row-->

	<!--end page content-->


	<!--Start footer-->
	<footer class="footer">

	<?php
		// printr(getQuantity_Day("2018-04-01"));

	?> 


		<span>Copyright &copy; 2019. Online Plant</span>
	</footer>
	<!--end footer-->

</section>
<!--end main content-->

<!--Common plugins-->
<!-- <script src="./../../js1/jquery.min.js"></script> -->
<!-- <script src="./../../js1/bootstrap.min.js"></script> -->

</body>
</html>?>