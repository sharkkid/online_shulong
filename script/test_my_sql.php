<?php
include_once(dirname(__FILE__).'/config.php');

$conn = getDB();
printr(mysqli_error($conn));

?>