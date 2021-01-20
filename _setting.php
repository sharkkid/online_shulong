<?php
// DB
// define('DB_SERVER', "demo.wholetech.com.tw");
define('DB_SERVER', "localhost");
// define('DB_SERVER', "148.72.232.169:3306");
define('DB_USERNAME', "online_shulong");
define('DB_PASSWORD', "2YR4A47Vc1tmtwTE");
define('DB_DATABASE', "online_shulong");

define("SYS_DEBUG_MODE", false); // true: test, false: prod
if(SYS_DEBUG_MODE) {
	define("CN_NAME", "Test模式-Online_Web");
	define("CN_SHORT_NAME", "Test模式-二次配圖面文件管理");
	define("CN_WEB_URL", "http://localhost/online_shulong/");
	// define("CN_WEB_URL", "http://182.50.151.86/online_shulong/");
} else {
	define("CN_NAME", "Online_shulong");
	define("CN_SHORT_NAME", "Online_shulong");
	define("CN_WEB_URL", "http://localhost/online_shulong/");
	// define("CN_WEB_URL", "http://onlineplantweb.com.tw/online_shulong/");
}

define('WT_PATH_ROOT', dirname(__FILE__)); // # ROOT PATH
define('WT_URL_ROOT', '/online_shulong');
define('WT_SERVER', 'http://localhost' . WT_URL_ROOT);
// define('WT_SERVER', 'http://onlineplantweb.com.tw' . WT_URL_ROOT);
// define('IP', 'http://onlineplantweb.com.tw');

define("SYS_EMAIL_SENDER","service@wholetech.com.tw");
date_default_timezone_set('Asia/Taipei');

$SellSize = array(
	1=>"1.7寸",
	10=>"2.0寸",
	2=>"2.5寸",
	3=>"2.8寸",
	4=>"3.0寸",
	5=>"3.5寸",
	6=>"3.6寸",
	7=>"成花",
);

$Size = array(
		1=>"1.7寸",
		2=>"2.5寸",
		6=>"3.6寸",
		7=>"其他",
);

$DEVICE_SYSTEM = array(
		1=>"1.7",
		10=>"2.0",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他"
		// 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
);

//種植費用
$plant_price = array(
		1=>"4",
		2=>"8",
		3=>"",
		4=>"",
		5=>"",
		6=>"12",
		7=>""
);

function inject_check($sql_str) {
	return mb_eregi('select|insert|update|delete|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile', $sql_str); // iLo
}

function GetParam($pname, $default='') {
	if(isset($_GET[$pname]) || isset($_POST[$pname])) {
		$retstr= trim ((isset($_POST[$pname]))?@$_POST[$pname]:@$_GET[$pname]);

		if(inject_check($retstr)) {
			return '';
		} else {
			return $retstr;
		}
	} else {
		return $default;
	}
}

function printr($expression, $return=false) {
	if($return)
		return print_r($expression, true);
	print('<pre>' . print_r($expression, true) . '</pre>');
}

function &get_col(&$list, $value_field, $key_field=null, $caseting=null)
{
	$col = array();
	if(!$list)
		return $col;

	foreach($list as &$r) {
		if($key_field)
			$col[$r[$key_field]] = $caseting ? $caseting($r[$value_field]) : $r[$value_field];
		else
			$col[] = $caseting ? $caseting($r[$value_field]) : $r[$value_field];
	}

	return $col;
}

function str2time($date_time)
{
	if(!$date_time)
		return 0;

	/* yyyy-mm-dd hh:mm:ss OR yyyy/mm/dd hh:mm:ss */
	if(preg_match("/^(\d{4})[-\/](0[1-9]|1[012])[-\/](0[1-9]|[12][0-9]|3[01]) (2[0-3]|[01]\d):([0-5]\d):([0-5]\d)$/", $date_time, $m)) {
		return mktime($m[4], $m[5], $m[6], $m[2], $m[3], $m[1]);
	}
	/* dd-mm-yyyy hh:mm:ss OR dd/mm/yyyy hh:mm:ss */
	else if(preg_match("/^(0[1-9]|[12][0-9]|3[01])[-\/]?(0[1-9]|1[012])[-\/]?(\d{4}) (2[0-3]|[01]\d):([0-5]\d):([0-5]\d)$/", $date_time, $m)) {
		return mktime($m[4], $m[5], $m[6], $m[2], $m[1], $m[3]);
	}
	/* yyyy-mm-dd hh:mm OR yyyy/mm/dd hh:mm */
	else if(preg_match("/^(\d{4})[-\/](0[1-9]|1[012])[-\/](0[1-9]|[12][0-9]|3[01]) (2[0-3]|[01]\d):([0-5]\d)$/", $date_time, $m)) {
		return mktime($m[4], $m[5], 0, $m[2], $m[3], $m[1]);
	}
	/* yyyy-mm-dd OR yyyy/mm/dd */
	else if(preg_match("/^(\d{4})[-\/]?(0[1-9]|1[012])[-\/]?(0[1-9]|[12][0-9]|3[01])$/", $date_time, $m)) {
		return mktime(0, 0, 0, $m[2], $m[3], $m[1]);
	}
	/* dd-mm-yyyy OR dd/mm/yyyy */
	else if(preg_match("/^(0[1-9]|[12][0-9]|3[01])[-\/]?(0[1-9]|1[012])[-\/]?(\d{4})$/", $date_time, $m)) {
		return mktime(0, 0, 0, $m[2], $m[1], $m[3]);
	}
	throw new Exception("Failed to parse date/time string, $date_time !", 100);
}

function get_ip()
{
	if(empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	} else {
		$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$ip = $ip[0];
	}
	return $ip;
}

function setConfig($key, $val)
{
	$ret = false;
	$conn = getDB();
	$sql = "UPDATE js_config SET jsc_value='{$val}' WHERE jsc_key='{$key}'";
	if($conn->query($sql))
		$ret = true;
	$conn->close();
	return $ret;
}

function getConfig($key)
{
	$ret_data = '';
	$conn = getDB();
	$sql = "select jsc_value from js_config where jsc_key='{$key}'";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		if($row = $qresult->fetch_assoc()) {
			$ret_data = $row['jsc_value'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getDB_oem(){
	$servername = DB_SERVER;
	$username = "onlineoem_sql";
	$password = "2C2NhGfr1tuDNloL";
	$database = "onlineoem_sql";
	$conn = mysqli_connect($servername, $username, $password, $database);
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		exit;
	}
	mysqli_set_charset($conn, "UTF8");
	return $conn;
}

/*
 * $subject max length 512 words
 * $content max length 4GB
 * $mail_to max length 1024 words
 * $files array('name'=>xxx.png, 'path'=>'./xxx.png')
 */
function sendMail($subject, $content, $mail_to, $files=array()) {
	$now = time();
	$conn = getDB();
	$files = empty($files) ? '' : urldecode(json_encode($files));
	$sql = <<<SQL
INSERT INTO js_mail (jsmail_add_date, jsmail_subject, jsmail_content, jsmail_mail_to, jsmail_files)
VALUES ('{$now}', '{$subject}', '{$content}', '{$mail_to}', '{$files}');
SQL
;
	if($conn->query($sql)) {
		$conn->close();
		return true;
	} else {
		$conn->close();
		return false;
	}
}

/*
 * $sys_type		0: sys, 1: daily, 2: leader, 3: ir, 4: temp, 5: sop
 * $sys_type_desc	max length 50 words
 * $op_type			0: otrers, 1:add, 2:update, 3:delete, 4:backend login
 * $op_desc			max length 255 words
 */
function addHistory($sys_type, $sys_type_desc, $op_type, $op_desc) {
	$now = time();
	$ip = get_ip();
	$user = empty($_SESSION['user']['jsuser_sn']) ? 0 : $_SESSION['user']['jsuser_sn'];
	$conn = getDB();
	$sql = <<<SQL
INSERT INTO js_history (jshist_add_date, jshist_sys_type, jshist_sys_type_desc, jshist_user, jshist_op_type, jshist_op_desc, jshist_ip)
VALUES ('{$now}', '{$sys_type}', '{$sys_type_desc}', '{$user}', '{$op_type}', '{$op_desc}', '{$ip}');
SQL
	;
	if($conn->query($sql)) {
	$conn->close();
	return true;
	} else {
	$conn->close();
	return false;
	}
}

function getDB(){
	$servername = DB_SERVER;
	$username = DB_USERNAME;
	$password = DB_PASSWORD;
	$database = DB_DATABASE;
	$conn = mysqli_connect($servername, $username, $password, $database);
	if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
		exit;
	}
	mysqli_set_charset($conn, "UTF8");
	return $conn;
}

function enclode_ret_data($code=1, $msg='', $data=array()) {
	$ret['code'] = $code;
	$ret['msg'] = $msg;
	$ret['data'] = $data;
	return json_encode($ret);
}

function &i18n() {
	require_once dirname(__FILE__) . '/lang/php-i18n/i18n.class.php';
	$i18n = new i18n(dirname(__FILE__) . '/lang/php-i18n/lang/lang_{LANGUAGE}.json', dirname(__FILE__) . '/lang/php-i18n/langcache/', 'tw');
	if(empty($_GET['lang']) && empty($_SESSION['lang']) && empty($_GET['ln']) && empty($_POST['ln']))
		$_GET['lang'] = 'tw';
	$i18n->init();

	return $i18n;
}
?>