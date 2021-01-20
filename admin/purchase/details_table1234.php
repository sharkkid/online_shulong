<?php
include_once("./func_plant_purchase.php");
$status_mapping = array(0=>'<font color="red">關閉</font>', 1=>'<font color="blue">啟用</font>');
$permissions_mapping = array(
    1=>'<font color="#666666">1.7</font>',
    2=>'<font color="#666666">2.5</font>',
    3=>'<font color="#666666">2.8</font>',
    4=>'<font color="#666666">3.0</font>',
    5=>'<font color="#666666">3.5</font>',
    6=>'<font color="#666666">3.6</font>',
    7=>'<font color="#666666">其他</font>' 
);

//------------------------------------------------------------data
function getSQL($qry) {
    $conn = getDB();
    $result = $conn->query($qry);
    $conn->close();
    return $result;
}


$onadd_part_no = GetParam('onproduct_part_no');
$onadd_growing = GetParam('onproduct_growing');
$onadd_quantity_del = GetParam('onproduct_quantity_del');
$onproduct_sn = GetParam('onproduct_sn');
$onadd_quantity_del = GetParam('onadd_quantity_del');

$product_list = getProductData($onproduct_sn);
$user_list = getExpectedShipByMonth($onadd_quantity_del,$onadd_part_no,$onadd_growing);
$business_data = getBusinessData($onadd_part_no,$onadd_quantity_del);
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
    <script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
    <link rel="stylesheet" href="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
    <script type="text/javascript">
        $(document).ready(function() {
            <?php
                    //  init search parm
            print "$('#search [name=onadd_status] option[value={$onadd_status}]').prop('selected','selected');";
            print "$('#search [name=onadd_growing] option[value={$onadd_growing}]').prop('selected','selected','selected','selected','selected','selected','selected');";
            ?>

        });
        function customer_list(onadd_part_no,year,month,size){
            $('#month_customers_title').html(year+" 年 "+month+" 月 - "+onadd_part_no+" 客戶明細(預計出貨)");
            $('#modal_month_customers').modal();
            $.ajax({
                url: './details_table.php',
                type: 'post',
                dataType: 'json',
                data: {op:"get_customer_list", onadd_part_no:onadd_part_no, year:year, month:month, size:size},
                beforeSend: function(msg) {
                    $("#ajax_loading").show();
                },
                complete: function(XMLHttpRequest, textStatus) {
                    $("#ajax_loading").hide();
                },
                success: function(ret) {
                    $('#month_customers_cotent').html('<div class="col-md-12"><div class="col-sm-10"><label for="addModalInput1" class="col-sm-2 control-label">客戶名稱</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨日期</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨尺寸</label><label for="addModalInput1" class="col-sm-2 control-label">該筆新增日期</label></div></div>');
                    $.each(ret.data, function(key,value){   
                        if(key < ret.data.length){                                      
                            $('#month_customers_cotent').html($('#month_customers_cotent').html()+'<div class="col-md-12"><div class="col-sm-10"><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_client+'</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_quantity+'</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_date+'</label></label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_size+'吋</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_add_date+'</label></div></div>');                             
                        }

                    });
                    
                },
                error: function (xhr, ajaxOptions, thrownError) {
                console.log('ajax error');
                    // console.log(xhr);
                }
            });
            // $('#month_customers_cotent').html($('#month_customers_cotent').html()+'<div class="col-md-12"><div class="col-sm-10"><label for="addModalInput1" class="col-sm-2 control-label">客戶名稱</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨日期</label><label for="addModalInput1" class="col-sm-2 control-label">該筆新增日期</label></div></div>');
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
                    <h4>產品預計出貨明細表</h4>
                </div>
            </div>
        </div>

        <!-- Page Content -->

    <div class="col-md-10">


        <?php
        foreach ($product_list as $row) {
            echo '<h3>'.$onadd_part_no.'</h3>';
            echo '<p>'. '品號(Part no.) : '. $row['onproduct_part_name'].'</p>';
            echo '<p>'. '花色 (Flower Color) : '. $row['onproduct_color'].'</p>';
            echo '<p>'. '花徑 (Flower Size) : '. $row['onproduct_size'].'</p>';
            echo '<p>'. '高度 (Plant Height) : '. $row['onproduct_height'].'</p>';
            echo '<p>'. '適合開花盆徑 (Suitable flowering pot size) : '. $row['onproduct_pot_size'].'</p>';
        }
        ?> 
    </div>

    <!--顯示月份出貨明細----------------------------------------------------------->
        <div id="modal_month_customers" class="modal upd-modal2" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form autocomplete="off" method="post" action="./" id="upd_form2" class="form-horizontal" role="form" data-toggle="validator">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="month_customers_title">出貨</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row" id="month_customers_cotent">
                                <div class="col-md-12">                                 
                                    <div class="col-sm-10">
                                        <label for="addModalInput1" class="col-sm-2 control-label">客戶名稱</label>
                                        <label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量</label>
                                        <label for="addModalInput1" class="col-sm-2 control-label">預計出貨日期</label>
                                        <label for="addModalInput1" class="col-sm-2 control-label">該筆新增日期</label>
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
        <!--顯示月份出貨明細----------------------------------------------------------->

    <!-- container -->
    <div  class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                $href = './details_table.php?onadd_part_no='.$onadd_part_no.'&onadd_growing='.$onadd_growing.'&onadd_quantity_del='.'2020'.'&end='.$end;
                ?>
                <!-- echo '<td><button type="button" class="btn btn-info btn-xs" onclick="location.href=\'./details_table.php?onadd_part_no='.$row['onadd_part_no'].'&onadd_growing='.$row['onadd_growing'].'&onadd_quantity_del='.$row['onadd_quantity_del'].'&start='.$start.'&end='.$end.'\'">查看</button></td>'; -->

                <!-- details_table.php?onadd_part_no=PP-0052&onadd_growing=1&onadd_quantity_del=2019 -->
                <ul class="nav nav-tabs">
                    <?php
                    $font_size = '';
                    // echo GetParam('onadd_quantity_del');
                    for($i=0;$i<5;$i++){
                        $n = (2019+$i);
                        if($n == GetParam('onadd_quantity_del')){
                            echo '<li class="active"><a style="color:#000000;">'.$n.'</a></li>';
                        }
                        else{
                            echo '<li class="active"><a style="color:#23b7e5;" href="'.WT_URL_ROOT.'/admin/purchase/details_table1234.php?onproduct_sn='.GetParam('onproduct_sn').'&onproduct_part_no='.GetParam('onproduct_part_no').'&onproduct_growing='.GetParam('onproduct_growing').'&onadd_quantity_del='.$n.'">'.$n.'</a></li>';
                        }
                    }
                    ?>
                </ul>

                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <table id="table_summary" class="table table-striped table-hover table-condensed table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2">尺寸</th>
                                <th colspan="12" class="tableheader" align="center">月份</th>
                            </tr>
                            <tr>
                                <th>一月</th>
                                <th>二月</th>
                                <th>三月</th>
                                <th>四月</th>
                                <th>五月</th>
                                <th>六月</th>
                                <th>七月</th>
                                <th>八月</th>
                                <th>九月</th>
                                <th>十月</th>
                                <th>十一月</th>
                                <th>十二月</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                for($size_n=0;$size_n<count($business_data);$size_n++){
                                $n = 0;
                                echo '<tbody>';    
                                for($i = 0 ;$i < 12;$i++){                              
                                    if($business_data[$size_n]['onbuda_day'] == ($i+1)){
                                        $team_array[$i]['quantity'] = $business_data[$size_n]['quantity'];
                                        $n++;                                   
                                    }else{
                                        $team_array[$i]['quantity'] = "0";
                                    }
                                }
                                echo '<td>'.$permissions_mapping[$business_data[$size_n]['onbuda_size']].'寸'.'</td>';
                                for($i = 0 ;$i < 12;$i++){
                                    if($team_array[$i]['quantity'] != "0")
                                        echo '<td><a href="javascript: void(0)" onclick="customer_list(\''.$onadd_part_no.'\','.$onadd_quantity_del.','.($i+1).','.$business_data[$size_n]['onbuda_size'].')">'.$team_array[$i]['quantity'].'</a></td>';//品號
                                    else
                                        echo '<td>0</td>';
                                }
                                echo '</tbody>';                            
                            }
                            ?>
                        </tbody>
                    </table>
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
</body>
    </html>?>
