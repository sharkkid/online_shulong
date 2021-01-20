<?php
include_once("./func_plant_purchase.php");
$qr_sn = "";
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
$permissions_mapping = getMapping_size();

$permmsion = $_SESSION['user']['jsuser_admin_permit'];
$permmsion_option = $_SESSION['user']['jsuser_option'];

$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {	
		case 'get':
		$onadd_sn=GetParam('onadd_sn');
		setcookie("onadd_sn", $onadd_sn);
		setcookie("qr_sn", GetParam('qr_sn'));
		setcookie("plant_sn", GetParam('plant_sn'));
		$ret_data = array();
		if(!empty($onadd_sn)){
			$ret_code = 1;
			$ret_data = getUserBySn($onadd_sn);
		} else {
			$ret_code = 0;
		}

		break;

		//搜尋用的資料---------------------------------------------
		case 'get_all_product':
		$ret_code = 1;
		$ret_data = getAllProductsNo();

		break;

		case 'getProductByPartNo':
		$onproduct_part_no=GetParam('onproduct_part_no');
		if(!empty($onproduct_part_no)){
			$ret_code = 1;
			$ret_data = getProductByPartNo($onproduct_part_no);
		}
		else{
			$ret_code = 0;
		}

		break;

		default:
		$ret_msg = 'error!';
		break;
	}

	echo enclode_ret_data($ret_code, $ret_msg, $ret_data);
	exit;
} else {
	// search
	if(($onadd_sn = GetParam('onadd_sn'))) {
		// 檢查搜尋條件是否有包含大小寫的 P、p
		$ex_P = explode("P", $onadd_sn);
		$ex_p = explode("p", $onadd_sn);	
		if (count($ex_P) == 2  and $ex_P[0] == "") {
			if (isset($ex_P[1])) {
				$ex_P[1] = explode("-", $ex_P[1]);	
				$search_where[] = "onadd_isbought = 1 and FROM_UNIXTIME(onadd_planting_date,'%Y') like '%{$ex_P[1][0]}%'";
			}else{
				$search_where[] = "onadd_isbought = 1";
			}			
		}elseif(count($ex_p) == 2 and $ex_p[0] == ""){
			if (isset($ex_p[1])) {
				$ex_p[1] = explode("-", $ex_p[1]);	
				$search_where[] = "onadd_isbought = 1 and FROM_UNIXTIME(onadd_planting_date,'%Y') like '%{$ex_p[1][0]}%'";
			}else{
				$search_where[] = "onadd_isbought = 1";
			}	
		}elseif(count($ex_P) == 1 and $ex_P[0] !=""){
			if (strpos($ex_P[0],'P')) {
				$search_where[] = "onadd_isbought = 1";
			}elseif (strpos($ex_P[0],'-')) {
				$ex_ = explode("-", $ex_P[0]);
				printr();
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn like '%{$onadd_sn}%' or onadd_sn like '%{$onadd_sn}%') or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_[0]%'";
			}elseif($ex_P[0] == '-'){
				$search_where[] = "";
			}else{
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn like '%{$onadd_sn}%' or onadd_sn like '%{$onadd_sn}%') or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_P[0]%'";
			}
		}elseif(count($ex_p) == 1 and $ex_p[0] !=""){
			if (strpos($ex_p[0],'p')) {
				$search_where[] = "onadd_isbought = 1";
			}elseif (strpos($ex_p[0],'-')) {
				$ex_ = explode("-", $ex_p[0]);
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn like '%{$onadd_sn}%' or onadd_sn like '%{$onadd_sn}%') or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_[0]%'";
			}elseif($ex_P[0] == '-'){
				$search_where[] = "";
			}
			else{
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn like '%{$onadd_sn}%' or onadd_sn like '%{$onadd_sn}%') or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_p[0]%'";
			}
		}
		$search_query_string['onadd_sn'] = $onadd_sn;
	}	
	if(($onadd_part_no = GetParam('onadd_part_no'))) {
		$search_where[] = "onadd_part_no like '%{$onadd_part_no}%'";
		$search_query_string['onadd_part_no'] = $onadd_part_no;
	}
	if(($onadd_part_name = GetParam('onadd_part_name'))) {
		$search_where[] = "onadd_part_name like '%{$onadd_part_name}%'";
		$search_query_string['onadd_part_name'] = $onadd_part_name;
	}
	if(($onadd_location = GetParam('onadd_location'))) {
		$search_where[] = "onadd_location like '%{$onadd_location}%'";
		$search_query_string['onadd_location'] = $onadd_location;
	}
	if(($onadd_cur_size = GetParam('onadd_growing'))) {
		$search_where[] = "onadd_cur_size = {$onadd_cur_size}";
		$search_query_string['onadd_growing'] = $onadd_cur_size;
	}

	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';
	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getUserQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	$product_list = getPlantData($search_where, $pg_offset, $pg_rows,$onadd_sn);
	// echo "<hr><hr><hr><hr><hr>";printr($ex_P);printr($ex_p);printr($ex_);printr($search_where);
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
		$(document).ready(function() {
			var all_part_no = null;
			var all_part_name = null;
			var all_produce_img = null;


		    $("body").on("change", ".upl", function (){
		        preview(this);
		        var files = $("#myFile").get(0).files;   		     
		        var formData = new FormData();   
    			formData.append("myFile", files[0]); 
    			formData.append("onproduct_type", "4"); 
    			$.ajax({   
			        url: './upload_image.php',   
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

			<?php
			//	init search parm
			// print "$('#search [name=onadd_status] option[value={$onadd_status}]').prop('selected','selected');";
			// print "$('#search [name=onadd_growing] option[value={$onadd_growing}]').prop('selected','selected','selected','selected','selected','selected','selected');";
			?>
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
							url: './plant_cost_add.php',
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
									console.log(data);
							        if(ret.code==1) {							        	
							        	$('#img_newName').html((data.onproduct_pic_url != "") ? data.onproduct_pic_url : "");
							        	document.getElementById('preview').setAttribute("src",((data.onproduct_pic_url != null) ? data.onproduct_pic_url : "./images/nopic.png"));
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
			url: './plant_cost_add.php',
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
					url: './plant_cost.php',
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
			                console.log(ret);
			                if(ret.code==1) {
			                	var d = ret.data;		
			                	$('#upd_form input[name=onadd_sn]').val(d.onadd_sn);
			                	if(d.onadd_newpot_sn == 0){	
			                		if(d.onadd_ml == 0)                	
				                		$('#upd_form input[name=onadd_newpot_sn]').val(d.onadd_sn);
				                	else
				                		$('#upd_form input[name=onadd_newpot_sn]').val(d.onadd_ml);
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
			                	$('#upd_form input[name=onadd_location]').val(d.onadd_location);	              	
			                	// $('#upd_form input[name=onadd_planting_date]').val(d.onadd_planting_date);
			                	$('#upd_form input[name=onadd_quantity]').val(d.onadd_quantity);
			                	$('#upd_form [name=onadd_cur_size] option[value='+d.onadd_growing+']').prop('selected','selected','selected','selected','selected','selected','selected');
			                	$('#upd_form [name=onadd_growing] option[value='+d.onadd_growing+']').prop('selected','selected','selected','selected','selected','selected','selected');	
			                	if(d.onadd_sellsize != ""){
			                		$('#upd_form [name=onadd_sellsize] option[value='+d.onadd_sellsize+']').prop('selected','selected','selected','selected','selected','selected','selected');	
			                	}	                	
			                	$('#upd_form [name=onadd_status] option[value='+d.onadd_status+']').prop('selected','selected');
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});

			//汰除-----------------------------------------------------------
			$('button.upd1').on('click', function(){
				$('#upd-modal1').modal();
				$('#upd_form1')[0].reset();

				$.ajax({
					url: './plant_cost.php',
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
			                	$('#upd_form1 input[name=onadd_sn]').val(d.onadd_sn);
			                	$('#upd_form1 input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd_form1 input[name=onadd_quantity]').val(d.onadd_quantity);
			                	if(d.onadd_newpot_sn == "0"){
				                	$('#upd_form1 input[name=onadd_newpot_sn]').val(d.onadd_sn);
				                }
				                else{
				                	$('#upd_form1 input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
				                }
			                	
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});
			//汰除-----------------------------------------------------------

			//出貨-----------------------------------------------------------
			$('button.upd2').on('click', function(){
				$('#upd-modal2').modal();
				$('#upd_form2')[0].reset();

				$.ajax({
					url: './plant_cost.php',
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
			                	$('#upd_form2 input[name=onadd_sn]').val(d.onadd_sn);
			                	$('#upd_form2 input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd_form2 input[name=onadd_quantity]').val(d.onadd_quantity);
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});
			//出貨-----------------------------------------------------------

			//修改-----------------------------------------------------------
			$('button.upd3').on('click', function(){
				$('#upd-modal3').modal();
				$('#upd3_form')[0].reset();
								$.ajax({
					url: './plant_cost.php',
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
			                console.log(ret);
			                if(ret.code==1) {
			                	var d = ret.data;
			                	$('#upd3_form input[name=onadd_sn]').val(d.onadd_sn);
			                	$('#upd3_form input[name=onadd_ml]').val(d.onadd_ml);
			                	$('#upd3_form input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
			                	$('#upd3_form input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd3_form input[name=onadd_part_name]').val(d.onadd_part_name);
			                	$('#upd3_form input[name=onadd_color]').val(d.onadd_color);
			                	$('#upd3_form input[name=onadd_size]').val(d.onadd_size);
			                	$('#upd3_form input[name=onadd_height]').val(d.onadd_height);
			                	$('#upd3_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
			                	$('#upd3_form input[name=onadd_location]').val(d.onadd_location);
			                	$('#upd3_form input[name=onadd_supplier]').val(d.onadd_supplier);
			                	$('#dropdown_onadd_cur_size').val(d.onadd_cur_size);

			                	$('#upd3_form input[name=onadd_planting_date]').val(d.onadd_planting_date);
			                	$('#upd3_form input[name=onadd_quantity]').val(d.onadd_quantity);
			                	$('#upd3_form input[name=onadd_growing]').val(d.onadd_growing);

			                	$('#upd3_form [name=onadd_growing] option[value='+d.onadd_growing+']').prop('selected','selected','selected','selected','selected','selected','selected');	
			                	if(d.onadd_sellsize != ""){		
			                		$('#upd3_form [name=onadd_sellsize] option[value='+d.onadd_sellsize+']').prop('selected','selected','selected','selected','selected','selected','selected');	
			                	}		
			                	$('#upd3_form [name=onadd_status] option[value='+d.onadd_status+']').prop('selected','selected');
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
				onadd_sn = $(this).data('onadd_sn')
				bootbox.confirm("確認刪除？", function(result) {
					if(result) {
						$.ajax({
							url: './plant_cost.php',
							type: 'post',
							dataType: 'json',
							data: {op:"del", onadd_sn:onadd_sn},
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
			//產生QR Code-------------------------------------------------------
			$('button.qr').on('click', function(){
				$('#qr_modal').modal();
				var qr_sn = $(this).data('qr_sn');
				$.ajax({
					url: './plant_cost.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onadd_sn:$(this).data('onadd_sn'), qr_sn: qr_sn, plant_sn:"1"},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
			                console.log(ret);
			                if(ret.code==1) {
			                	var d = ret.data;
			                	console.log(d);
			                	$('#temp_onadd_sn').val(d.onadd_sn);

			                	if(d.img_url != "")
			                		$('#qr_product_img').attr("src",d.img_url);
			                	else
			                		$('#qr_product_img').attr("src","./images/nopic.png");

			     //            	if($('#qr_product_img').width() > 565)
			     //            		$('#qr_product_img').width(550);
								// if($('#qr_product_img').height() > 392)
			     //            		$('#qr_product_img').height(392);
 
			                	$('#qr_sn').html("產品編號："+qr_sn);
			                	$('#qr_part_no').html("品號："+d.onadd_part_no);
			                	$('#qr_part_name').html("品名："+d.onadd_part_name);
			                	$('#qr_plant_date').html("下種日期："+d.onadd_planting_date);
			                	$('#qr_part_number').html("數量："+d.onadd_quantity);
								$('#qr_location').html("位置："+d.onadd_location);
			                	var src = $('#qr_img_example').attr('src');
			                	$('#qr_img').attr('src',src+"onadd_sn="+d.onadd_sn);
			                	// document.getElementById('qr_cotent_recover').appendChild(document.getElementById('qr_cotent').cloneNode(true));
			                	// $('#qr_cotent_recover').attr('style','display:none');
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});
			//下載QR Code-------------------------------------------------------
			$('button.qr_download').on('click', function(){
				var qr_sn = $('#qr_sn').html();
				var qr_part_no = $('#qr_part_no').html();
				var qr_part_name = $('#qr_part_name').html();
				var qr_plant_date = $('#qr_plant_date').html();
				var qr_location = $('#qr_location').html();
				var img_src = $('#qr_product_img').attr("src");
				var qr_src = $('#qr_img').attr("src");
				var data = "?qr_sn="+qr_sn+"&qr_part_no="+qr_part_no+"&qr_part_name="+qr_part_name+"&qr_plant_date="+qr_plant_date+"&qr_location="+qr_location+"&img_src="+img_src+"&qr_src="+qr_src;
				window.open(
				 '<?php echo WT_SERVER;?>/admin/purchase/test.php'+data,
				  '_blank' // <- This is what makes it open in a new window.
				);
				// $('#qr_modal').modal();
				// var onadd_sn = $('#temp_onadd_sn').val();
				// $.ajax({
				// 	url: './plant_cost.php',
				// 	type: 'post',
				// 	dataType: 'json',
				// 	data: {op:"download", onadd_sn:onadd_sn},
				// 	beforeSend: function(msg) {
				// 		$("#ajax_loading").show();
				// 	},
				// 	complete: function(XMLHttpRequest, textStatus) {
				// 		$("#ajax_loading").hide();
				// 	},
				// 	success: function(ret) {
			 //                console.log(onadd_sn);
			 //                if(ret.code==1) {
			 //                	$('#qr_sticker_img').removeAttr("src");
			 //                	$('#qr_sticker_img').attr("src",ret.data['img_url']);
				// 				$('#qr_sticker_sn').html($('#qr_sn').html());
				// 				$('#qr_sticker_part_no').html($('#qr_part_no').html());
				// 				$('#qr_sticker_part_name').html($('#qr_part_name').html());
				// 				$('#qr_sticker_date').html($('#qr_plant_date').html());
				// 				$('#qr_sticker_location').html($('#qr_location').html());
				// 				$('#qr_sticker_qrcode').attr("src",($('#qr_img').attr("src")));
			 //                	PrintElem('qr_sticker');

			 //                	setTimeout(
				// 				    function() {		
				// 				    	document.getElementById('qr_sticker').setAttribute("style", "width: 410px; height: 720px;display:none;");
				// 				    }, 500);			                	
			 //                }
			 //            },
			 //            error: function (xhr, ajaxOptions, thrownError) {
		  //               	// console.log('ajax error');
		  //                   // console.log(xhr);
		  //               }
		  //           });
			});

			$('#add_form, #upd_form, #upd_form1, #upd_form2, #upd3_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					// var files = $("#myFile").get(0).files;   
					e.preventDefault();
					var param = $(this).serializeArray();
					var onproduct_pic_url = {name:"onproduct_pic_url",value:$('#img_newName').html().substring(1,$('#img_newName').html().length)};
					param.push(onproduct_pic_url);
					console.log(param);
					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();
					 	$.ajax({
					 		url: './plant_cost.php',
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

			//產品履歷----------------------------------------------------------
			function history(onadd_part_no,onadd_name,onadd_sn){
				$('#history_title').html(onadd_part_no+" - "+onadd_name+" 苗種履歷");
				$('#history_modal').modal();
				$.ajax({
					url: './plant_cost.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get_history_list", onadd_sn:onadd_sn},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
						console.log(ret);
						$('#history_cotent').html('<div class="col-md-12"><div class="col-sm-12"><table class="table table-hover"><thead><tr><th style="text-align:center;">操作日期</th><th style="text-align:center;">下種日期(數量)</th><th style="text-align:center;">換盆日期(數量)</th><th style="text-align:center;">出貨日期(數量)</th><th style="text-align:center;">汰除日期(數量)</th></tr></thead>');
						$.each(ret.data, function(key,value){	
							if(key < ret.data.length){
								var temp = "";
								switch(value.flag){
									case 0:
										temp ='<label for="addModalInput1" class="col-sm-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>';
									break;
									case 1:
										temp ='<label for="addModalInput1" class="col-sm-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>';
									break;
									case 2:
										temp ='<label for="addModalInput1" class="col-sm-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>';
									break;
									case 3:
										temp ='<label for="addModalInput1" class="col-sm-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>';
									break;
								}
																		
								$('#history_cotent').html($('#history_cotent').html()+'<div class="col-md-12"><div class="col-sm-12">'+temp+'</div></div>');								
							}

						});
						
					},
					error: function (xhr, ajaxOptions, thrownError) {
				   	console.log('ajax error');
				        // console.log(xhr);
				    }
				});
			}
			//產品履歷----------------------------------------------------------
			function PrintElem(elem)
			{
			    var mywindow = window.open('', 'PRINT', 'height=1160,width=820');

			    mywindow.document.write('<html><head><title>' + document.title  + '</title>');
			    mywindow.document.write('</head><body >');
			    mywindow.document.write('<h1>' + document.title  + '</h1>');
			    document.getElementById(elem).setAttribute("style", "width: 820px; height: 1160px;");
			    mywindow.document.write(document.getElementById(elem).innerHTML);
			    mywindow.document.write('</body></html>');

			    mywindow.document.close(); // necessary for IE >= 10
			    mywindow.focus(); // necessary for IE >= 10*/

			    domtoimage.toBlob(document.getElementById(elem))
				    .then(function(blob) {
				      window.saveAs(blob, $('#qr_part_no').html());
				    });
			    mywindow.close();
			    return true;
			}

			function insert(str, index, value) {
			    return str.substr(0, index) + value + str.substr(index);
			}
			function downloadAsImg( el, filename, scale ){
			    if( scale!=undefined ) var props = {
			        width: el.clientWidth*scale*1.412,
			        height: el.clientHeight*scale,
			        style: {
			            'transform': 'scale('+scale+')',
			            'transform-origin': 'top left'
			        }
			    }
			    domtoimage.toBlob( el, props==undefined ? {} : props).then(function (blob) {
			        window.saveAs(blob, filename==undefined ? 'image.png' : filename);
			    });
			}

			/**
			 * 預覽圖
			 * @param   input 輸入 input[type=file] 的 this
			 */
			function preview(input) {
			 
			    // 若有選取檔案
			    if (input.files && input.files[0]) {
			 
			        // 建立一個物件，使用 Web APIs 的檔案讀取器(FileReader 物件) 來讀取使用者選取電腦中的檔案
			        var reader = new FileReader();
			 
			        // 事先定義好，當讀取成功後會觸發的事情
			        reader.onload = function (e) {
			            
			            console.log(e);
			 
			            // 這裡看到的 e.target.result 物件，是使用者的檔案被 FileReader 轉換成 base64 的字串格式，
			            // 在這裡我們選取圖檔，所以轉換出來的，會是如 『data:image/jpeg;base64,.....』這樣的字串樣式。
			            // 我們用它當作圖片路徑就對了。
			            $('.preview').attr('src', e.target.result);
			 
			            // 檔案大小，把 Bytes 轉換為 KB
			            var KB = format_float(e.total / 1024, 2);
			            $('.size').text("檔案大小：" + KB + " KB");
			        }
			 
			        // 因為上面定義好讀取成功的事情，所以這裡可以放心讀取檔案
			        reader.readAsDataURL(input.files[0]);
			    }
			}
 
			/**
			 * 格式化
			 * @param   num 要轉換的數字
			 * @param   pos 指定小數第幾位做四捨五入
			 */
			function format_float(num, pos)
			{
			    var size = Math.pow(10, pos);
			    return Math.round(num * size) / size;
			}
 

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
					<h4>苗株成本總表</h4>
				</div>
			</div>
		</div>		

		<div id="upd-modal" class="modal upd-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
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
											<input type="text" class="form-control" id="addModalInput1" name="onadd_color" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_size" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">高度</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_height" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">放置區<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_location" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">適合開花盆徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_pot_size" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_supplier" placeholder="" readonly="readonly">
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
										<label class="col-sm-2 control-label">換盆日期&nbsp;<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="datetimepicker2" name="onadd_planting_date" value="<?php echo (empty($device['onadd_planting_date'])) ? '' : date('Y-m-d', $device['onadd_planting_date']);?>" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>        								
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label" >換盆數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_replant_number" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>	
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label" >換盆尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onadd_cur_size">
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
									</div>	
									<div class="form-group">
										<label class="col-sm-2 control-label">下階段換盆尺寸<font color="red">*</font></label>
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
									<div class="form-group">
										<label class="col-sm-2 control-label">預計出貨尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onadd_sellsize">
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

		<!--修改----------------------------------------------------------->
		<div id="upd-modal3" class="modal upd-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd3_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">移倉</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd5">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_ml">
									<input type="hidden" name="onadd_newpot_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品名<font color="red">*</font></label>
										<div class="col-sm-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花色</label>
										<div class="col-sm-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_color" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花徑</label>
										<div class="col-sm-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_size" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">高度</label>
										<div class="col-sm-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_height" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">放置區<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_location" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">移倉數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_ml_amount" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">適合開花盆徑</label>
										<div class="col-sm-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_pot_size" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商</label>
										<div class="col-sm-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_supplier" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">換盆日期&nbsp;</label>
										<div class="col-sm-10">
											<input readonly="readonly" type="text" class="form-control" id="datetimepicker3" name="onadd_planting_date" value="<?php echo (empty($device['onadd_planting_date'])) ? '' : date('Y-m-d', $device['onadd_planting_date']);?>" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>   
									<div class="form-group">
										<label class="col-sm-2 control-label">目前尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select readonly="readonly" class="form-control" id="dropdown_onadd_cur_size" name="onadd_cur_size">
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
									</div>     								
									<div class="form-group">
										<label class="col-sm-2 control-label">下階段換盆尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select readonly="readonly" class="form-control" name="onadd_growing">
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
									<div class="form-group">
										<label class="col-sm-2 control-label">預計出貨尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onadd_sellsize">
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
				<div class="modal-content" style="width: 1002px;">
					<form autocomplete="off" method="post" action="./" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="history_title">品號 - 品名 - 產品履歷</h4>
						</div>
						<div class="row">
							<div class="row" id="history_cotent">
								<div class="col-md-12">									
									<div class="col-sm-12">
										<label for="addModalInput1" class="col-sm-2 control-label">資料建立日期</label>
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
						<div class="row" id="qr_container" >
							<div class="row" id="qr_cotent">
								<!-- <div class="col-sm-8" id="qr_sec_cotent"> -->
									<!-- <input type="hidden" id="temp_onadd_sn">
									<img id="qr_img_example" style="margin-left: 20px;padding-left: 10px;display:none;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_cost.php?">
									<img id="qr_img" style="margin-left: 20px;padding-left: 10px;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_cost.php?">	 -->
								<!-- </div> -->
								<div class="col-md-12" id="qr_sec_cotent2" style="text-align: center;">
<!-- 								<div id="qr_sn" style="font-size: 20px;font-weight:bold;">產品編號：</div>
									<div id="qr_part_no" style="font-size: 20px;font-weight:bold;">品號：</div>
									<div id="qr_part_name" style="font-size: 20px;font-weight:bold;">品名：</div>
									<div id="qr_plant_date" style="font-size: 20px;font-weight:bold;">下種日期：</div>
									<div id="qr_location" style="font-size: 20px;font-weight:bold;">位置：</div>
									<div id="qr_part_number" style="font-size: 20px;font-weight:bold;">數量：</div>		
									<img id="qr_sticker_img"style="width: 400px;height: 280px;" src=""> -->									
									<img id="qr_product_img" class="img-thumbnail" style="max-height: 32vh;max-width: 32vw;text-align: center;" src="">
								</div>
								<p style="margin: 10px">
								<div class="col-md-12" id="qr_sec_cotent2" style="text-align: center;">
									<div class="col-md-6" style="float: left;">
										<span id="qr_sn" style="font-size: 20px;"></span><br>
										<span id="qr_part_no" style="font-size: 20px;"></span><br>
										<span id="qr_part_name" style="font-size: 20px;"></span><br>
										<span id="qr_plant_date" style="font-size: 20px;"></span><br>
										<span id="qr_location" style="font-size: 20px;"></span><br>
									</div>
									<div class="col-sm-6" id="qr_sec_cotent" style="float: left;">
										<input type="hidden" id="temp_onadd_sn">
										<img id="qr_img"  style="margin-left: 0px;" 
											 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_cost.php?">
										<img id="qr_img_example" style="display:none;" 
											 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_cost.php?">											
									</div>
								</div>
							</div>
							<!-- <div id="qr_cotent_recover" >
								
							</div> -->
						</div>

						<div class="modal-footer">
							<button id="qr_download" type="button" class="btn btn-primary qr_download">列印二維條碼</button>
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
							<h4 class="modal-title">新品項資料建立</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="add">
									<input type="hidden" id="IsUploadImg" value="">
									<div class="form-group">
										<?php if(strpos($permmsion_option, "4") !== false || $permmsion == 0){ ?>
										<label class="col-sm-2 control-label">產品圖片</label>
										<div class="col-sm-10">
											<div style="display: none;" id="img_newName"></div>
										    <input type="file" class="upl" id="myFile" name="myFile" accept="image/jpeg,image/jpg,image/gif,image/png">

										    <img class="preview" id="preview" style="max-width: 500px; max-height: 500px;">
										    <div class="size" id="preview_size"></div>
										    
									    </div>
										<?php } ?>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">苗種來源<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" id="addModalInput1" name="onadd_isbought" placeholder="" required minlength="1" maxlength="32">
											　<option value="0">自種苗</option>
											　<option value="1">外購苗</option>
											</select>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_part_no" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品名<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_part_name" name="onadd_part_name" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花色</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_color" name="onadd_color" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_size" name="onadd_size" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">高度</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_height" name="onadd_height" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">放置區<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_location" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">適合開花盆徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_pot_size" name="onadd_pot_size" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_supplier" name="onadd_supplier" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">下種日期&nbsp;<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="datetimepicker1" name="onadd_planting_date" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									
									<div class="form-group">
										<label class="col-sm-2 control-label">目前尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" id="dropdown_onadd_cur_size" name="onadd_cur_size">
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

									<div class="form-group">
										<label class="col-sm-2 control-label">下階段換盆尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" id="dropdown_onadd_growing" name="onadd_growing">
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

									<div class="form-group">
										<label class="col-sm-2 control-label">預計出貨尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" id="dropdown_onadd_sell" name="onadd_sellsize">
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

		<!--圖片上傳Modal----------------------------------------------------------->
		<div id="upload_img_modal" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog mw-100 w-75">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<h4 class="modal-title" id="history_title">提示視窗！</h4>							
						</div>		
						<div style="text-align: center;font-size: 15px">確認上傳此圖片嗎？</div>				
						<div class="modal-footer">
							<button id="upload_img_yes" type="button" class="btn btn-primary">確認</button>
							<button id="upload_img_no" type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--圖片上傳Modal----------------------------------------------------------->

		<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">

				<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
						<ul class="nav nav-pills pull-right toolbar">
							
							<!-- <li><button data-parent="#toolbar" class="accordion-toggle btn btn-primary" onclick="javascript:location.href='./plant_cost_add.php'"><i class="glyphicon glyphicon-plus"></i> 新品項建立</button></li> -->
							<!-- <li><button data-parent="#toolbar" class="accordion-toggle btn btn-warning" onclick="javascript:location.href='./plant_cost_add.php'"></i> 返回苗種資料建立</button></li> -->
						</ul>
					</div>

					<!-- search -->
					<div id="search" style="clear:both;">
						<form autocomplete="off" method="get" action="./plant_cost.php" id="search_form" class="form-inline alert alert-info" role="form">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="searchInput0">產品編號</label>
										<input type="text" class="form-control" id="searchInput0" name="onadd_sn" value="<?php echo $onadd_sn;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput1">品號</label>
										<input type="text" class="form-control" id="searchInput1" name="onadd_part_no" value="<?php echo $onadd_part_no;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput4">品名</label>
										<input type="text" class="form-control" id="searchInput4" name="onadd_part_name" value="<?php echo $onadd_part_name;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput2">放置區位置</label>
										<input type="text" class="form-control" id="searchInput2" name="onadd_location" value="<?php echo $onadd_location;?>" placeholder="">
									</div>

									<button type="submit" class="btn btn-info" op="search">搜尋</button>
								</div>
							</div>
						</form>
					</div>

					<div id="qr_sticker" style="width: 410px; height: 720px;text-align:center;display:none;">
						<img id="qr_sticker_img"style="width: 400px;height: 280px;" src="">
						<div id="qr_sticker_sn" style="text-align:left;font-size: 30px;height: 40px;margin-top: 10px;">產品編號:2019-17</div>
						<div id="qr_sticker_part_no" style="text-align:left;font-size: 30px;height: 40px;">品號:P1015</div>
						<div id="qr_sticker_part_name" style="text-align:left;font-size: 30px;height: 40px;">品名:維維安Vivian (2號)</div>
						<div id="qr_sticker_date" style="text-align:left;font-size: 30px;height: 40px;">下種日期:2019-08-01</div>
						<div id="qr_sticker_location" style="text-align:left;font-size: 30px;height: 40px;">位置:A5</div>
						<div style="text-align:left;">
							<img id="qr_sticker_qrcode" style="width: 150px;" src="">
						</div>	
					</div>

					<!-- content -->
					<table class="table table-striped table-hover table-condensed tablesorter">
						<thead>
							<tr style="font-size: 1.1em">
								<th style="text-align: center;">產品編號</th>
								<th style="text-align: center;">品號</th>
								<th style="text-align: center;">品名</th>
								<th style="text-align: center;">下種日期</th>
								<th style="text-align: center;">下種數量</th>
								<th style="text-align: center;">苗株費用</th>
								<th style="text-align: center;">軟杯</th>
								<th style="text-align: center;">水草</th>
								<th style="text-align: center;">人工</th>								
								<th style="text-align: center;">種植費用</th>
								<th style="text-align: center;">單株成本</th>
								<th style="text-align: center;">總成本</th>
							</tr>
						</thead>
						<tbody >
							<?php
							foreach ($product_list as $row) {								
								echo '<tr>';//產品編號
									if($row['onadd_isbought'] == 0){
										if($row['onadd_newpot_sn'] == 0){
											if($row['onadd_ml'] == 0){
												echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.date(	'Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'].'</td>';//產品編號
	        									$qr_sn = date('Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'];
											}else{											
	        									echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.date(	'Y',$row['onadd_planting_date']).'-'.$row['onadd_ml'].'</td>';//產品編號
	        									$qr_sn = date('Y',$row['onadd_planting_date']).'-'.$row['onadd_ml'];
	        								}
	        							}
	        							else{
	        								echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_newpot_sn'].'</td>';//產品編號
	        								$qr_sn = date('Y',$row['onadd_planting_date']).'-'.$row['onadd_newpot_sn'];
	        							}
									}else{
										if($row['onadd_newpot_sn'] == 0){
											if($row['onadd_ml'] == 0){
												echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">P'.date(	'Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'].'</td>';//產品編號
	        									$qr_sn = date('Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'];
											}else{											
	        									echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">P'.date(	'Y',$row['onadd_planting_date']).'-'.$row['onadd_ml'].'</td>';//產品編號
	        									$qr_sn = date('Y',$row['onadd_planting_date']).'-'.$row['onadd_ml'];
	        								}
										}
										else{
											echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">P'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_newpot_sn'].'</td>';//產品編號
											$qr_sn = "P".date('Y',$row['onadd_planting_date']).'-'.$row['onadd_newpot_sn'];
										}
									}
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_part_no'].'</td>';//品號
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_part_name'].'</td>';//品名  							
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.date('Y-m-d',$row['onadd_planting_date']).'</td>';//下種日期        							
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_quantity'].'</td>';//數量
        							$total_perOne = $row['onadd_cost_plant']+$row['onadd_cost_cup']+$row['onadd_cost_grass']+$row['onadd_cost_labour']+$row['onadd_cost_month'];
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.number_format($row['onadd_cost_plant'],2,".",",").'</td>';//苗株費用
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.number_format($row['onadd_cost_cup'],2,".",",").'</td>';//軟杯費用
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.number_format($row['onadd_cost_grass'],2,".",",").'</td>';//水草費用
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.number_format($row['onadd_cost_labour'],2,".",",").'</td>';//人工費用
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.number_format($row['onadd_cost_month'],2,".",",").'</td>';//每月固定費用
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.number_format($total_perOne,2,".",",").' NT$ </td>';//單株總費用
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.number_format(($total_perOne*$row['onadd_quantity']),2,".",",").' NT$ </td>';//整批總費用

        							
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
        <!-- <script src="./../../js1/bootstrap-datepicker.js"></script> -->
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
    </html>