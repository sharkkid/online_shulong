<?php
include_once("./func_plant_user.php");
// printr(qr_download(6));
// exit();
$status_mapping = array(0=>'<font color="red">關閉</font>', 1=>'<font color="blue">啟用</font>');
$DEVICE_SYSTEM = array(
		1=>"1.7",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他",
		8=>"瓶苗開瓶",
		9=>"出貨"
		// 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
);
$permissions_mapping = array(
    1=>'<font color="#666666">1.7</font>',
    2=>'<font color="#666666">2.5</font>',
    3=>'<font color="#666666">2.8</font>',
    4=>'<font color="#666666">3.0</font>',
    5=>'<font color="#666666">3.5</font>',
    6=>'<font color="#666666">3.6</font>',
    7=>'<font color="#666666">其他</font>',
    8=>'<font color="#666666">瓶苗開瓶</font>' 
);
$permmsion = $_SESSION['user']['jsuser_admin_permit'];
$permmsion_option = $_SESSION['user']['jsuser_option'];

$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'add':
		$permmsion_account=GetParam('permmsion_account');
		$permmsion_password=GetParam('permmsion_password');
		$permmsion_name=GetParam('permmsion_name');
		$permmsion_mail=GetParam('permmsion_mail');
		$permmsion_status=GetParam('permmsion_status');
		$permmsion_level=GetParam('permmsion_level');
		$permmsion_add=GetParam('permmsion_add');
		$permmsion_modify=GetParam('permmsion_modify');
		$permmsion_remove=GetParam('permmsion_remove');
		$permmsion_ship=GetParam('permmsion_ship');
		$permmsion_image=GetParam('permmsion_image');
		$permmsion_move=GetParam('permmsion_move');
		if(empty($permmsion_account)||empty($permmsion_password)||empty($permmsion_name)){
			$ret_msg = "*為必填！";
		} else {
			$option = "";
			if(!empty($permmsion_add)){
				$option .= "1,";
			}
			if(!empty($permmsion_modify)){
				$option .= "3,";
			}
			if(!empty($permmsion_remove)){
				$option .= "2,";
			}
			if(!empty($permmsion_ship)){
				$option .= "5,";
			}
			if(!empty($permmsion_image)){
				$option .= "4,";
			}
			if(!empty($permmsion_move)){
				$option .= "6,";
			}
			$option = substr($option, 0, -1);;
			$now = time();
			$conn = getDB();
				$sql = "INSERT INTO `js_user`(`jsuser_account`, `jsuser_password`, `jsuser_name`, `jsuser_email`, `jsuser_add_date`, `jsuser_mod_date`, `jsuser_status`, `jsuser_admin_permit`, `jsuser_option`) VALUES ('{$permmsion_account}','{$permmsion_password}','{$permmsion_name}','{$permmsion_mail}','{$now}','{$now}','{$permmsion_status}','{$permmsion_level}','{$option}')";
				if($conn->query($sql)) {
					$ret_msg = "新增成功！";				
				} else {
					$ret_msg = "新增失敗！";
				}
			$conn->close();
		}
		break;

		case 'get':
		$jsuser_sn=GetParam('jsuser_sn');
		$ret_data = array();
		if(!empty($jsuser_sn)){
			$ret_code = 1;
			$ret_data = getUserBySn($jsuser_sn);
		} else {
			$ret_code = 0;
		}

		break;

		case 'modify':
		$jsuser_sn=GetParam('jsuser_sn');

		$permmsion_account=GetParam('permmsion_account');
		$permmsion_password=GetParam('permmsion_password');
		$permmsion_name=GetParam('permmsion_name');
		$permmsion_mail=GetParam('permmsion_mail');
		$permmsion_status=GetParam('permmsion_status');
		$permmsion_level=GetParam('permmsion_level');
		$permmsion_add=GetParam('permmsion_add');
		$permmsion_modify=GetParam('permmsion_modify');
		$permmsion_remove=GetParam('permmsion_remove');
		$permmsion_ship=GetParam('permmsion_ship');
		$permmsion_image=GetParam('permmsion_image');
		$permmsion_move=GetParam('permmsion_move');

		if(empty($permmsion_account)||empty($permmsion_password)||empty($permmsion_name)){
			$ret_msg = "*為必填！".$permmsion_level;
		} else {
			$option = "";
			if(!empty($permmsion_add)){
				$option .= "1,";
			}
			if(!empty($permmsion_modify)){
				$option .= "3,";
			}
			if(!empty($permmsion_remove)){
				$option .= "2,";
			}
			if(!empty($permmsion_ship)){
				$option .= "5,";
			}
			if(!empty($permmsion_image)){
				$option .= "4,";
			}
			if(!empty($permmsion_move)){
				$option .= "6,";
			}
			$option = substr($option, 0, -1);;
			$now = time();
			$conn = getDB();
				$sql = "UPDATE `js_user` SET 
				`jsuser_account`='{$permmsion_account}',
				`jsuser_password`='{$permmsion_password}',
				`jsuser_name`='{$permmsion_name}',
				`jsuser_email`='{$permmsion_mail}',
				`jsuser_add_date`='{$now}',
				`jsuser_mod_date`='{$now}',
				`jsuser_status`='{$permmsion_status}',
				`jsuser_admin_permit`='{$permmsion_level}',
				`jsuser_option`='{$option}'
				WHERE jsuser_sn = '{$jsuser_sn}'";
				if($conn->query($sql)) {
					$ret_msg = "修改成功！";				
				} else {
					$ret_msg = "修改失敗！".$sql;
				}
			$conn->close();
		}
		break;

		case 'del':
		$jsuser_sn=GetParam('jsuser_sn');

		if(empty($jsuser_sn)){
			$ret_msg = "刪除失敗！";
		}else{
			$now = time();
			$conn = getDB();
			$sql = "DELETE FROM js_user WHERE jsuser_sn='{$jsuser_sn}'";
			if($conn->query($sql)) {
				$ret_msg = "刪除完成！";
			} else {
				$ret_msg = "刪除失敗！";
			}
			$conn->close();
		}
		break;

		case 'download':
		$onadd_sn=GetParam('onadd_sn');
		$ret_data = array();
		if(!empty($onadd_sn)){
			$ret_code = 1;
			$ret_data = qr_download($onadd_sn);
		} else {
			$ret_code = 0;
		}

		//產品履歷---------------------------------------------
		case 'get_history_list':
		$onadd_sn = GetParam('onadd_sn');

		if(empty($onadd_sn)){
			$ret_msg = "查詢失敗！";
		} else {
			$ret_data = getHistory_List($onadd_sn);
		}
		break;
		//產品履歷---------------------------------------------

		default:
		$ret_msg = 'error!';
		break;
	}

	echo enclode_ret_data($ret_code, $ret_msg, $ret_data);
	exit;
} else {
	if(($permmsion_account = GetParam('permmsion_account'))) {
		$search_where[] = "jsuser_account like '%{$permmsion_account}%'";
		$search_query_string['jsuser_account'] = $permmsion_account;
	}
	if(($permmsion_mail = GetParam('permmsion_mail'))) {
		$search_where[] = "jsuser_email like '%{$permmsion_mail}%'";
		$search_query_string['jsuser_email'] = $permmsion_mail;
	}
	if(($permmsion_name = GetParam('permmsion_name'))) {
		$search_where[] = "jsuser_name like '%{$permmsion_name}%'";
		$search_query_string['jsuser_name'] = $permmsion_name;
	}

	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';
	// printr($search_where);
	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getUserQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	$user_list = getUser($search_where, $pg_offset, $pg_rows);
	// printr($user_list);
	// exit;
	// echo "<hr><hr><hr><hr><hr>";printr($ex_P);printr($ex_p);printr($ex_);printr($search_where);

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
	<style>
	* {
	  box-sizing: border-box;
	}

	/*the container must be positioned relative:*/
	.autocomplete {
	  position: relative;
	  display: inline-block;
	}

	input {
	  border: 1px solid transparent;
	  background-color: #f1f1f1;
	  padding: 10px;
	  font-size: 16px;
	}

	input[type=submit] {
	  background-color: DodgerBlue;
	  color: #fff;
	  cursor: pointer;
	}

	.autocomplete-items {
	 /* position: absolute;*/
	  border: 1px solid #d4d4d4;
	  border-bottom: none;
	  border-top: none;
	  z-index: 99;
	  /*position the autocomplete items to be the same width as the container:*/
	  top: 100%;

	}

	.autocomplete-items div {
	  padding: 10px;
	  cursor: pointer;
	  background-color: #fff; 
	  border-bottom: 1px solid #d4d4d4; 
	}

	/*when hovering an item:*/
	.autocomplete-items div:hover {
	  background-color: #e9e9e9; 
	}

	/*when navigating through the items using the arrow keys:*/
	.autocomplete-active {
	  background-color: DodgerBlue !important; 
	  color: #ffffff; 
	}
	</style>
	<script type="text/javascript">
		var all_part_no = null;
		var all_part_name = null;
		$(document).ready(function() {
			<?php
					//	init search parm
			// print "$('#search [name=onadd_status] option[value={$onadd_status}]').prop('selected','selected');";
			?>

			$("body").on("change", ".upl", function (){
		        preview(this);
		        var files = $("#myFile").get(0).files;   		     
		        var formData = new FormData();   
    			formData.append("myFile", files[0]); 
    			formData.append("onproduct_type", "4"); 
    			$.ajax({   
			        url: './../purchase/upload_image.php',   
			        data: formData,    
			        dataType: "json",   
			        type: "POST",   
			        cache: false,   
			        contentType: false,   
			        processData: false,   
			        error: function(xhr) {   
			        },   
			        success: function(json) {   
			            
			        },   
			        complete: function(json){
			        	$('#img_newName').html(json.responseText);   
			        }   
			    });  
		    });

			function autocomplete(inp, arr) {

		  /*the autocomplete function takes two arguments,
		  the text field element and an array of possible autocompleted values:*/
		  var currentFocus;
		  /*execute a function when someone writes in the text field:*/
		  inp.addEventListener("input", function(e) {
		      var a, b, i, val = this.value;
		      /*close any already open lists of autocompleted values*/
		      closeAllLists();
		      // if (!val) { return false;}
		      currentFocus = -1;
		      /*create a DIV element that will contain the items (values):*/
		      a = document.createElement("DIV");
		      a.setAttribute("id", this.id + "autocomplete-list");
		      a.setAttribute("class", "autocomplete-items");
		      /*append the DIV element as a child of the autocomplete container:*/
		      this.parentNode.appendChild(a);
		      /*for each item in the array...*/
		      for (i = 0; i < arr.length; i++) {
		        /*check if the item starts with the same letters as the text field value:*/
		        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
		          /*create a DIV element for each matching element:*/
		          b = document.createElement("DIV");
		          /*make the matching letters bold:*/
		          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
		          b.innerHTML += arr[i].substr(val.length);
		          /*insert a input field that will hold the current array item's value:*/
		          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
		          /*execute a function when someone clicks on the item value (DIV element):*/
		          b.addEventListener("click", function(e) {
		              /*insert the value for the autocomplete text field:*/
		            inp.value = this.getElementsByTagName("input")[0].value;
						$.ajax({
							url: './plant_purchase_addflask.php',
							type: 'post',
							dataType: 'json',
							data: {op:"getProductByPartNo",onproduct_part_no:inp.value},
							beforeSend: function(msg) {
								$("#ajax_loading").show();
							},
							complete: function(XMLHttpRequest, textStatus) {
								$("#ajax_loading").hide();
							},
							success: function(ret) {
									var data = ret.data;
									// console.log(data.onproduct_pic_url);
							        if(ret.code==1) {
							        	var img = "./../purchase"+data.onproduct_pic_url.substring(1,data.onproduct_pic_url.length);
							        	$('#img_newName').html((data.onproduct_pic_url != "") ? data.onproduct_pic_url : "");
							        	document.getElementById('preview').setAttribute("src",((data.onproduct_pic_url != "") ? img : "./../purchase/images/nopic.png"));
							        	document.getElementById('dropdown_onadd_part_name').value = (data.onproduct_part_name != "") ? data.onproduct_part_name : "";
							        	document.getElementById('dropdown_onadd_color').value = (data.onproduct_color!= "") ? data.onproduct_color : "";
							        	document.getElementById('dropdown_onadd_size').value = (data.onproduct_size != "") ? data.onproduct_size : "";
							        	document.getElementById('dropdown_onadd_height').value = (data.onproduct_height != "") ? data.onproduct_height : "";
							        	document.getElementById('dropdown_onadd_location').value = (data.onproduct_location != "") ? data.onproduct_location : "";
							        	document.getElementById('dropdown_onadd_pot_size').value = (data.onproduct_pot_size != "") ? data.onproduct_pot_size : "";
							        	document.getElementById('dropdown_onadd_supplier').value = (data.onproduct_supplier != "") ? data.onproduct_supplier : "";
							        	document.getElementById('dropdown_onadd_growing').value = data.onproduct_growing;
							        }
							    },
							    error: function (xhr, ajaxOptions, thrownError) {
							    	// console.log('ajax error');
							     //    console.log(xhr);
							    }
							});
		              /*close the list of autocompleted values,
		              (or any other open lists of autocompleted values:*/
		              closeAllLists();
		          });
		          a.appendChild(b);
		        }
		      }
		  });
		  /*execute a function presses a key on the keyboard:*/
		  inp.addEventListener("keydown", function(e) {
		      var x = document.getElementById(this.id + "autocomplete-list");
		      if (x) x = x.getElementsByTagName("div");
		      if (e.keyCode == 40) {
		        /*If the arrow DOWN key is pressed,
		        increase the currentFocus variable:*/
		        currentFocus++;
		        /*and and make the current item more visible:*/
		        addActive(x);
		      } else if (e.keyCode == 38) { //up
		        /*If the arrow UP key is pressed,
		        decrease the currentFocus variable:*/
		        currentFocus--;
		        /*and and make the current item more visible:*/
		        addActive(x);
		      } else if (e.keyCode == 13) {
		        /*If the ENTER key is pressed, prevent the form from being submitted,*/
		        e.preventDefault();
		        if (currentFocus > -1) {
		          /*and simulate a click on the "active" item:*/
		          if (x) x[currentFocus].click();
		        }
		      }
		  });

		  function addActive(x) {
		    /*a function to classify an item as "active":*/
		    if (!x) return false;
		    /*start by removing the "active" class on all items:*/
		    removeActive(x);
		    if (currentFocus >= x.length) currentFocus = 0;
		    if (currentFocus < 0) currentFocus = (x.length - 1);
		    /*add class "autocomplete-active":*/
		    x[currentFocus].classList.add("autocomplete-active");
		  }
		  function removeActive(x) {
		    /*a function to remove the "active" class from all autocomplete items:*/
		    for (var i = 0; i < x.length; i++) {
		      x[i].classList.remove("autocomplete-active");
		    }
		  }
		  function closeAllLists(elmnt) {
		    /*close all autocomplete lists in the document,
		    except the one passed as an argument:*/
		    var x = document.getElementsByClassName("autocomplete-items");
		    for (var i = 0; i < x.length; i++) {
		      if (elmnt != x[i] && elmnt != inp) {
		        x[i].parentNode.removeChild(x[i]);
		      }
		    }
		  }
		  /*execute a function when someone clicks in the document:*/
		  document.addEventListener("click", function (e) {
		      closeAllLists(e.target);
		  });
  		}	
		
		$.ajax({
			url: './plant_purchase_addflask.php',
			type: 'post',
			dataType: 'json',
			data: {op:"get_all_product"},
			beforeSend: function(msg) {
				$("#ajax_loading").show();
			},
			complete: function(XMLHttpRequest, textStatus) {
				$("#ajax_loading").hide();
			},
			success: function(ret) {
			        if(ret.code==1) {
			        	all_part_no = ret.data;	
			        	/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
						autocomplete(document.getElementById('dropdown_onadd_part_no'), all_part_no[0]);

			        }
			    },
			    error: function (xhr, ajaxOptions, thrownError) {
		        	// console.log('ajax error');
		            // console.log(xhr);
		        }
		    });

			$('button.upd').on('click', function(){
				$('#upd-modal').modal();
				$('#upd_form')[0].reset();

				$.ajax({
					url: './plant_flask.php',
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
			                // console.log(ret);
			                if(ret.code==1) {
			                	var d = ret.data;
			                	$('#upd_form input[name=onadd_sn]').val(d.onadd_sn);
			                	if(d.onadd_newpot_sn == 0){	                	
				                	$('#upd_form input[name=onadd_newpot_sn]').val(d.onadd_sn);
				                }
				                else{
				                	$('#upd_form input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
				                }
			                	$('#upd_form input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd_form input[name=onadd_part_name]').val(d.onadd_part_name);
			                	$('#upd_form input[name=onadd_color]').val(d.onadd_color);
			                	$('#upd_form input[name=onadd_size]').val(d.onadd_size);
			                	$('#upd_form input[name=onadd_height]').val(d.onadd_height);
			                	$('#upd_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
			                	$('#upd_form input[name=onadd_supplier]').val(d.onadd_supplier);
			                	$('#upd_form input[name=onadd_quantity]').val(d.onadd_quantity);
			                	$('#upd_form [name=onadd_status] option[value='+d.onadd_status+']').prop('selected','selected');
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});

			//修改-----------------------------------------------------------
			$('button.user_upd').on('click', function(){
				$('#modify-modal').modal();
				$('#modify_form')[0].reset();

					$.ajax({
					url: './person_setting.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", jsuser_sn:$(this).data('jsuser_sn')},
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
			                	// console.log(d);
			                	$('#modify-modal input[name=jsuser_sn]').val(d.jsuser_sn);
			                	$('#modify-modal input[name=permmsion_account]').val(d.jsuser_account);
			                	$('#modify-modal input[name=permmsion_password]').val(d.jsuser_password);
			                	$('#modify-modal input[name=permmsion_name]').val(d.jsuser_name);
			                	$('#modify-modal input[name=permmsion_mail]').val(d.jsuser_email);
			                	$('#modify-modal [name=permmsion_status]').val(d.jsuser_status);
			                	$('#modify-modal [name=permmsion_level]').val(d.jsuser_admin_permit);
			                	if(d.jsuser_option != ''){
			                		console.log('123');
			                		if(d.jsuser_option.includes("1")){
			                			$('#modify-modal [name=permmsion_add]').prop('checked', true);
			                		}
			                		if(d.jsuser_option.includes("2")){
			                			$('#modify-modal [name=permmsion_remove]').prop('checked', true);
			                		}
			                		if(d.jsuser_option.includes("3")){
			                			$('#modify-modal [name=permmsion_modify]').prop('checked', true);
			                		}
			                		if(d.jsuser_option.includes("4")){
			                			$('#modify-modal [name=permmsion_image]').prop('checked', true);
			                		}
			                		if(d.jsuser_option.includes("5")){
			                			$('#modify-modal [name=permmsion_ship]').prop('checked', true);
			                		}
			                		if(d.jsuser_option.includes("6")){
			                			$('#modify-modal [name=permmsion_move]').prop('checked', true);
			                		}

			                	}
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                 //    console.log(thrownError);
		                }
		            });
			});			
			//修改-----------------------------------------------------------

			bootbox.setDefaults({
				locale: "zh_TW",
			});

			$('button.del').on('click', function(){
				jsuser_sn = $(this).data('jsuser_sn')
				bootbox.confirm("確認刪除？", function(result) {
					if(result) {
						$.ajax({
							url: './person_setting.php',
							type: 'post',
							dataType: 'json',
							data: {op:"del", jsuser_sn:jsuser_sn},
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


			$('#add_form, #upd_form1, #upd_form2, #upd3_form, #eli_form1, #modify_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();					
					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();

					 	$.ajax({
					 		url: './person_setting.php',
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
			                 //     console.log(thrownError);
			                 }
			             });
					 }
				});

				bootbox.setDefaults({
					locale: "zh_TW",
				});

				$('#datetimepicker1').datetimepicker({
		        	minView: 2,
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd',
		            useCurrent: false
		        });
		        
		        $('#datetimepicker2').datetimepicker({
		        	minView: 2,
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd',
		            useCurrent: false
		        });
		        $('#datetimepicker3').datetimepicker({
		        	minView: 2,
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd',
		            useCurrent: false
		        });
		        $('button.cancel').on('click', function() {
					location.href = "./../";
				});
		});
	</script>
</head>

<body>
	<?php include('./../htmlModule/nav.php');?>
	<!--main content start-->
	<section class="main-content">



		<!--page header start-->
		<div class="page-header">
			<div class="row">
				<div class="col-sm-6">
					<h4>使用者管理</h4>
				</div>
			</div>
		</div>

		

		<div id="upd-modal" class="modal upd-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">下種</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_newpot_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品名<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花色</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_color" placeholder="" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_size" placeholder=""  maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">高度</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_height" placeholder="" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">適合開花盆徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_pot_size" placeholder="" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_supplier" placeholder="" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>									
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">日期<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="datetimepicker3" name="onadd_planting_date" value="<?php echo (empty($device['onadd_planting_date'])) ? '' : date('Y-m-d', $device['onadd_planting_date']);?>" placeholder="" required minlength="1">
											<div class="help-block with-errors"></div>
										</div>
									</div>        								
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label" >下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_plant_day" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>	
									<div class="form-group">
										<label class="col-sm-2 control-label">預計成長大小<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onadd_cur_size" >
												<option selected="selected" value="8">瓶苗開瓶</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">預計成長大小<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onadd_growing">
												<option value="7">其他</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>
<!-- 									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label" >剩餘數量是否汰除? (沒勾選擇保留庫存)</label>
										<div class="col-sm-10">
											<input type="checkbox" class="form-control" name="isKept" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div> -->
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">下種</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!--汰除----------------------------------------------------------->
		<div id="upd-modal1" class="modal upd-modal1" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form1" class="form-horizontal" role="form" data-toggle="validator">
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
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">汰除數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity_del" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">汰除原因<font color="red">*</font></label>
										<div class="col-sm-10">
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
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
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
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">出貨數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_plant_year" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">出貨對象<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onshda_client" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
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
		<!--出貨----------------------------------------------------------->

		<!--苗種履歷----------------------------------------------------------->
		<div id="history_modal" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="history_title">品號 - 品名 - 產品履歷</h4>
						</div>
						<div class="row">
							<div class="row" id="history_cotent">
								<div class="col-md-12">									
									<div class="col-sm-12">
										<label for="addModalInput1" class="col-sm-2 control-label">操作日期</label>
										<label for="addModalInput1" class="col-sm-2 control-label">下種日期(數量)</label>
										<label for="addModalInput1" class="col-sm-2 control-label">換盆日期(數量)</label>
										<label for="addModalInput1" class="col-sm-2 control-label">出貨日期(數量)</label>
										<label for="addModalInput1" class="col-sm-2 control-label">汰除日期(數量)</label>
									</div>	
								</div>

							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--苗種履歷----------------------------------------------------------->

		<!--QR Code產生Modal----------------------------------------------------------->
		<div id="qr_modal" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog mw-100 w-75">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="history_title">二維條碼</h4>
						</div>
						<div class="row" id="qr_container">
							<div class="row" id="qr_cotent">
								<div class="col-sm-8" id="qr_sec_cotent">
									<!-- <input type="hidden" id="temp_onadd_sn">
									<img id="qr_img_example" style="margin-left: 20px;padding-left: 10px;display:none;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_purchase.php?">
									<img id="qr_img" style="margin-left: 20px;padding-left: 10px;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_purchase.php?">	 -->
								</div>
								<div class="col-md-8" id="qr_sec_cotent2" style="border-left-width: 20px; margin-left: 30px;">
<!-- 									<div id="qr_sn" style="font-size: 20px;font-weight:bold;">產品編號：</div>
									<div id="qr_part_no" style="font-size: 20px;font-weight:bold;">品號：</div>
									<div id="qr_part_name" style="font-size: 20px;font-weight:bold;">品名：</div>
									<div id="qr_plant_date" style="font-size: 20px;font-weight:bold;">下種日期：</div>
									<div id="qr_location" style="font-size: 20px;font-weight:bold;">位置：</div>
									<div id="qr_part_number" style="font-size: 20px;font-weight:bold;">數量：</div>		
									<img id="qr_sticker_img"style="width: 400px;height: 280px;" src=""> -->
									<img id="qr_product_img"style="width: 565px;height: 392px;margin-top: 15px;" src="">
									<div id="qr_sn" style="margin-top: 5px;font-size: 20px;"></div>
									<div id="qr_part_no" style="font-size: 20px;"></div>
									<div id="qr_part_name" style="font-size: 20px;"></div>
									<div id="qr_plant_date" style="font-size: 20px;"></div>
									<div id="qr_location" style="font-size: 20px;"></div>								
								</div>
								<div class="col-sm-8" id="qr_sec_cotent">
									<input type="hidden" id="temp_onadd_sn">
									<img id="qr_img_example" style="margin-left: 20px;padding-left: 10px;display:none;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/flask/plant_flask.php?">
									<img id="qr_img" style="margin-left: 0px;padding-left: 430px;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/flask/plant_flask.php?">	
								</div>
							</div>
							<div id="qr_cotent_recover" >
								
							</div>
						</div>

						<div class="modal-footer">
							<button id="qr_download" type="button" class="btn btn-primary qr_download">下載二維條碼</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--QR Code產生Modal----------------------------------------------------------->

		<!-- modal -->
		<div id="add-modal" class="modal add-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="add_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">使用者建立</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="add">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">帳號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="permmsion_account" name="permmsion_account" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">密碼<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="permmsion_password" name="permmsion_password" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">名稱<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="permmsion_name" name="permmsion_name" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">MAIL</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="permmsion_mail" name="permmsion_mail" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">啟用狀態<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" id="permmsion_status" name="permmsion_status">
												<option value="0">未啟用</option>
												<option selected="selected" value="1">啟用</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">權限等級<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" id="permmsion_level" name="permmsion_level">
												<option value="0">系統管理員</option>
												<option value="1">老闆</option>
												<option selected="selected" value="2">員工</option>
												<option value="3">業務</option>
												<option value="4">訪客</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">其他權限</label>
										<div class="col-sm-10">											
											<input type="checkbox" class="custom-control-input" id="permmsion_add" name="permmsion_add">
											<label class="custom-control-label" for="defaultChecked2">新增</label>
											<input type="checkbox" class="custom-control-input" id="permmsion_modify" name="permmsion_modify">
											<label class="custom-control-label" for="defaultChecked2">修改</label>
											<input type="checkbox" class="custom-control-input" id="permmsion_remove" name="permmsion_remove">
											<label class="custom-control-label" for="defaultChecked2">汰除</label>
											<input type="checkbox" class="custom-control-input" id="permmsion_ship" name="permmsion_ship">
											<label class="custom-control-label" for="defaultChecked2">出貨</label>
											<input type="checkbox" class="custom-control-input" id="permmsion_image" name="permmsion_image">
											<label class="custom-control-label" for="defaultChecked2">圖片上傳</label>
											<input type="checkbox" class="custom-control-input" id="permmsion_move" name="permmsion_move">
											<label class="custom-control-label" for="defaultChecked2">移倉</label>
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

		<div id="modify-modal" class="modal modify-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="modify_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">使用者資料修改</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="modify">
									<input type="hidden" name="jsuser_sn" value="jsuser_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">帳號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="permmsion_account" name="permmsion_account" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">密碼<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="permmsion_password" name="permmsion_password" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">名稱<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="permmsion_name" name="permmsion_name" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">MAIL</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="permmsion_mail" name="permmsion_mail" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">啟用狀態<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" id="permmsion_status" name="permmsion_status">
												<option value="0">未啟用</option>
												<option selected="selected" value="1">啟用</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">權限等級<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" id="permmsion_level" name="permmsion_level">
												<option value="0">系統管理員</option>
												<option value="1">老闆</option>
												<option selected="selected" value="2">員工</option>
												<option value="3">業務</option>
												<option value="4">訪客</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">其他權限</label>
										<div class="col-sm-10">											
											<input type="checkbox" class="custom-control-input" id="permmsion_add" name="permmsion_add">
											<label class="custom-control-label" for="defaultChecked2">新增</label>
											<input type="checkbox" class="custom-control-input" id="permmsion_modify" name="permmsion_modify">
											<label class="custom-control-label" for="defaultChecked2">修改</label>
											<input type="checkbox" class="custom-control-input" id="permmsion_remove" name="permmsion_remove">
											<label class="custom-control-label" for="defaultChecked2">汰除</label>
											<input type="checkbox" class="custom-control-input" id="permmsion_ship" name="permmsion_ship">
											<label class="custom-control-label" for="defaultChecked2">出貨</label>
											<input type="checkbox" class="custom-control-input" id="permmsion_image" name="permmsion_image">
											<label class="custom-control-label" for="defaultChecked2">圖片上傳</label>
											<input type="checkbox" class="custom-control-input" id="permmsion_move" name="permmsion_move">
											<label class="custom-control-label" for="defaultChecked2">移倉</label>
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
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">出貨數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_plant_year" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">出貨對象<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onshda_client" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
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
		<!--出貨----------------------------------------------------------->

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
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品名<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">剩餘數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">汰除數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity_del" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">汰除原因<font color="red">*</font></label>
										<div class="col-sm-10">
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
							<button type="button" class="btn btn-primary" data-dismiss="modal">保留</button>
							<button type="submit" class="btn btn-danger">汰除</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--汰除----------------------------------------------------------->

		<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">

					<!-- nav toolbar -->
					<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
						<ul class="nav nav-pills pull-right toolbar">
							<?php if($permmsion == '0' || strpos($permmsion_option, '1') !== false){ ?>
								<li><button data-parent="#toolbar" data-toggle="modal" data-target=".add-modal" class="accordion-toggle btn btn-primary"><i class="glyphicon glyphicon-plus"></i> 使用者建立</button></li>
							<?php } ?>
							<!-- <li><button data-parent="#toolbar" class="accordion-toggle btn btn-primary" onclick="javascript:location.href='./plant_purchase_addflask.php'"><i class="glyphicon glyphicon-plus"></i>新品項建立</button></li> -->
							<!-- <li><button data-parent="#toolbar" class="accordion-toggle btn btn-warning" onclick="javascript:location.href='./plant_purchase_addflask.php'"></i> 返回瓶苗資料建立</button></li> -->
						</ul>
					</div>
					<!-- search -->
					<div id="search" style="clear:both;">
						<form autocomplete="off" method="get" action="./person_setting.php" id="search_form" class="form-inline alert alert-info" role="form">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="searchInput0">帳號</label>
										<input type="text" class="form-control" id="permmsion_account" name="permmsion_account" value="<?php echo $permmsion_account;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput1">姓名</label>
										<input type="text" class="form-control" id="permmsion_name" name="permmsion_name" value="<?php echo $permmsion_name;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput4">MAIL</label>
										<input type="text" class="form-control" id="searchInput4" name="permmsion_mail" value="<?php echo $permmsion_mail;?>" placeholder="">
									</div>

									<button type="submit" class="btn btn-info" op="search">搜尋</button>
								</div>
							</div>
						</form>
					</div>

					<div id="qr_sticker" style="width: 410px; height: 720px;text-align:center;display:none;">
						<img id="qr_sticker_img"style="width: 400px;height: 280px;" src="">
						<div id="qr_sticker_sn" style="text-align:left;font-size: 30px;height: 40px;margin-top: 10px;"></div>
						<div id="qr_sticker_part_no" style="text-align:left;font-size: 30px;height: 40px;"></div>
						<div id="qr_sticker_part_name" style="text-align:left;font-size: 30px;height: 40px;"></div>
						<div id="qr_sticker_date" style="text-align:left;font-size: 30px;height: 40px;"></div>
						<div id="qr_sticker_location" style="text-align:left;font-size: 30px;height: 40px;"></div>
						<div style="text-align:right;">
							<img id="qr_sticker_qrcode" style="width: 150px;" src="">
						</div>	
					</div>

					<!-- content -->
					<table class="table table-striped table-hover table-condensed tablesorter">
						<thead>
							<tr style="font-size: 1.1em">
								<th style="text-align: center;">帳號</th>
								<th style="text-align: center;">密碼</th>
								<th style="text-align: center;">名稱</th>
								<th style="text-align: center;">E-Mail</th>
								<th style="text-align: center;">啟用狀態</th>
								<th style="text-align: center;">權限等級</th>
								<th style="text-align: center;">其他權限</th>
							</tr>
						</thead>
						<tbody>
							<?php
							// printr($user_list);
        					foreach ($user_list as $row) {
								echo '<tr>';									
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['jsuser_account'].'</td>';//帳號
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['jsuser_password'].'</td>';//密碼							
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['jsuser_name'].'</td>';//名稱  
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['jsuser_email'].'</td>';//MAIL
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.(($row['jsuser_status']==1)?'<span style="color:#00DD00;">啟用</span>':'<span style="color:#CC0000;">未啟用</span>').'</td>';//啟用狀態
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.permission_level($row['jsuser_admin_permit']).'</td>';//權限等級
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.permission_option($row['jsuser_option']).'</td>';//其他權限							
	        						echo '</td>';
	        						echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;"><button data-parent="#toolbar" data-toggle="modal" class="btn btn-primary btn-xs user_upd" data-jsuser_sn="'.$row['jsuser_sn'].'">修改</button> <button data-parent="#toolbar" data-toggle="modal" class="btn btn-danger btn-xs del" data-jsuser_sn="'.$row['jsuser_sn'].'">刪除</button></td>';
	        									
	        						echo '</td>';
        						echo '</tr>';
        					}
        					?>
        					</tbody>
        				</table>

        				<?php include('./../htmlModule/page.php');?>

        			</div>
        		</div>
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
        <script src="./../../lib/dom-to-image.js"></script>
        <script src="./../../lib/FileSaver.js"></script>
    </body>
    </html>?>