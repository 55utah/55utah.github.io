<?php
	error_reporting(E_ALL^E_NOTICE^E_WARNING);
	class devData{
		var $id;
		var $name;
		var $type;
		var $value;
	}
	class opResult{
		var $result;
		var $id;	//出错时保存错误码
		var $name;	//出错时保存出错源
		var $type;	//出错时保存出错类型
		var $value;	//出错时保存错误详细说明
	}

	$input = file_get_contents("php://input");

	echo $input;
	$ret = new opResult;
	$op = json_decode($input);	
	
	//echo "value";
	//echo $op->{"value"};

	header("Content-type: text/html; charset=utf-8");
	
	$dbname = "u653029240_maci";
	$dbpass = "M1234560";
	$dbhost = "mysql.hostinger.com.hk";
	$dbdatabase = "u653029240_maci";
 
    //生成一个连接
    $db_connect= new mysqli($dbhost,$dbname,$dbpass,$dbdatabase);
    if ($db_connect->connect_errno){
    	echo "connect error";
    }
    echo "connect success"
    $strsql="INSERT INTO mytable (`id`,`name`,`type`,`value`) VALUES (".$op->{"id"}.",'".$op->{"name"}."','".$op->{"type"}."','".$op->{"value"}."')";
	$result=$db_connect->query($strsql);
	if ($db_connect->errno != 0){echo "query error";}
	$row=mysqli_fetch_assoc($result);
	$ret->id = $row["id"];
	$ret->name = $row["name"];
	$ret->type = $row["type"];				
	$ret->value = json_decode($row["value"]);
	echo json_encode($ret,JSON_UNESCAPED_UNICODE);	
	if( is_object($result){
		$result->close();
	}

    $db_connect->close();
?>