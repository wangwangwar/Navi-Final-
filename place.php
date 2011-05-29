<?php
class Place {
	var $data = array( 'placeNum' => 0, 'placeName' => '', 'placeInfo' => '' );
	// 
	function getPlace( MySQLi $con, $num ) {
		if ( !$con ) {
			die( '不能连接数据库: '. mysql_error() );
		}
		$get_place_sql = "SELECT * FROM place WHERE placeNum = $num;";
		
		$place = $con->query($get_place_sql)->fetch_assoc();
		echo $row['placeNum'];
		echo $row['placeName'];
		echo htmlspecialchars(stripslashes($row['placeInfo']));
	}
	function setPlace( $Num, $Name, $Info ) {
		$this->data['placeNum'] = $Num;
		$this->data['placeName'] = $Name;
		$this->data['placeInfo'] = $Info;
	}
	// 存入数据库
	function insertDB( MySQLi $con ) {
		if ( !$con ) {
			die( '不能连接数据库: '. mysql_error() );
		}
		if ( !$this->data['placeNum'] ) {
			die( '缺少地点信息，请重新检查' );
		}
		$Num = $this->data['placeNum'];
		$Name = $this->data['placeName'];
		$Info = $this->data['placeInfo'];
		$insert_place_sql = "INSERT INTO place VALUES( $Num, '$Name', '$Info' );";
		$con->query( $insert_place_sql );
	}
}
?>