<?php
class Place {
	// 存储地点数据
	var $place = array( 'placeNum' => 0, 'placeName' => '', 'placeInfo' => '' );
	
	// 构造函数
	function __construct( $placeNum, $placeName, $placeInfo ) {
		if ( $placeNum && $placeName && $placeInfo ) {
			if ( $placeNum <= 0 || $placeNum > 100
				 ) {
				echo '<p>您输入的地点信息有误。<br/>'
	  			. '地点编号范围为 1 ～ 100<br/></p>';
				exit();
			}
			$this->setPlace( $placeNum, $placeName, $placeInfo );
		}
		else {
			$this->setPlace( 0, null, null );
		}
	}
	
	function getPlace( $num = 0 ) {
		$con = $this->connectDB();
		$get_place_sql = "SELECT * FROM place WHERE placeNum = $num;";
		$place = $con->query($get_place_sql)->fetch_assoc();
		if ( $place ) {
			$this->setPlace( $place['placeNum'], $place['placeName'], $place['placeInfo'] );
			
		}
		else {
			echo "地点编号 ". $num." 不存在.";
		}
		$con->close();
	}
	
	function connectDB() {
		$con = new mysqli( "localhost", "navi", "123456", "navi" );
		if ( !$con ) {
			die( '不能连接数据库: '. mysql_error() );
		}
		return $con;
	}
	
	function setPlace( $Num, $Name, $Info ) {
		$this->place['placeNum'] = $Num;
		$this->place['placeName'] = $Name;
		$this->place['placeInfo'] = $Info;
	}
	
	function insertPlace() {
		if ( !$this->place['placeNum'] || !$this->place['placeName'] || !$this->place['placeInfo'] ) {
			die( '无法加入数据库：缺少地点信息，请重新检查' );
		}
		$Num = $this->place['placeNum'];
		$Name = $this->place['placeName'];
		$Info = $this->place['placeInfo'];
		$insert_place_sql = "INSERT INTO place VALUES( $Num, '$Name', '$Info' );";
		$con = $this->connectDB();
		if ( !$con->query( $insert_place_sql ) ) {
    		printf("Errormessage: %s\n", $con->error);
    		exit();
		}
		echo "本条地点信息成功加入数据库！<br/>";
		$con->close();
	}
}

?>