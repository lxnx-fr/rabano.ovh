<?php
echo "DAMM ";
require_once 'functions.inc.php';
require_once 'dbh.inc.php';

$code = createCode($conn);
echo "</br>code: ". $code;