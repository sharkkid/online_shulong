<?php
include_once('func.php');
set_time_limit(600);

$op = GetParam('op');

switch ($op) {
	case 'get_app_info':
		get_update_info();
		break;

	default:
		break;
}

function enclose_data($op='', $code=-1, $msg='', $data=array()) {
	$ret['op'] = $op;
	$ret['code'] = $code;
	$ret['msg'] = $msg;
	$ret['data'] = $data;
	echo json_encode($ret);
}

function get_update_info() {
	$sys = GetParam('sys');
	$app = getAppBySys($sys);
	if(!empty($app)) {
		$ret['sys'] = $app['jsapp_sys'];
		$ret['version_code'] = $app['jsapp_version_code'];
		$ret['version_name'] = $app['jsapp_version_name'];
		$ret['file_name'] = $app['jsapp_file_name'];
		$ret['desc'] = $app['jsapp_desc'];
		$ret['file_url'] = WT_SERVER . '/download/' . $app['jsapp_sys'] . '/' . $app['jsapp_file_name'];
	}
	
	return enclose_data('get_update_info', 1, 'success', $ret);
}
?>