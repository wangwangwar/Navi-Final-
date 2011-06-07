<?php 
define( "MAX_DEST", 10000 );

class Path {
	var $path = array( 'pathStart' => 0, 'pathEnd' => 0, 'pathLength' => 0 );
	var $pathMatrix;
	
	// 构造函数
	function __construct( $pathStart, $pathEnd, $pathLength ) {
		if ( $pathStart && $pathEnd && $pathLength ) {
			if ( 	$pathStart <= 0 	|| $pathEnd <= 0 
	  			|| 	$pathStart > 100 || $pathEnd > 100
	  			||	$pathStart == $pathEnd
	  			||	$pathLength <= 0 ) {
	  			echo '<p>您输入的路径信息有误。<br/>'
	  			. '地点编号范围为 1 ～ 100 且地点 A 与 B 编号不能相同。<br/>'
	  			. '路径长度应大于 0 m。</br></p>';
	  			exit();
			}
			$this->setPath( $pathStart, $pathEnd, $pathLength );
		}
		else {
			$this->setPath( 0, 0, 0 );
		}
	}

	// 获取路径
	function getPath() {
		return $this->path;
	}
	
	// 设置路径
	function setPath( $pathStart, $pathEnd, $pathLength ) {
		$this->path['pathStart'] = $pathStart;
		$this->path['pathEnd'] = $pathEnd;
		$this->path['pathLength'] = $pathLength;
	}
	
	// 连接数据库
	function connectDB() {
	    $con = new mysqli( "localhost", "navi", "123456", "navi" );
		if ( $con->connect_errno ) {
    		printf( "Connect failed: %s\n", $con->connect_error );
    		exit();
		}
		return $con;
	}
	
	// 从数据库中获得路径矩阵
	function getPathMatrix() {
		$con = $this->connectDB();
		$get_path_sql = "SELECT * FROM path;";
		if ( !( $paths = $con->query( $get_path_sql ) ) ) {
    		printf("Errormessage: %s\n", $con->error);
    		exit();
		}
		while( $path = $paths->fetch_assoc() ) {
			$this->pathMatrix[ $path['pathStart'] ][ $path['pathEnd'] ] = $path['pathLength'];
		}
		$con->close();
	}
	
	// 打印路径矩阵
	function printPathMatrix() {
		echo "<table border='0'>";
		echo '<tr bgcolor='. '#ff0000>';
		echo '<td bgcolor='. '#ffffff></td>';
		$placeNum = count( $this->pathMatrix );
		for ( $i = 1; $i <= $placeNum; $i++ ) {
			echo "<td bgcolor=". "#ff0000". ">$i</td>";
		}
		for( $i = 1; $i <= $placeNum; $i++ ) {
			echo '<tr bgcolor='. '#cccccc>';
			echo "<td bgcolor=". "#ff0000". ">$i</td>";
			for( $j = 1; $j <= $placeNum; $j++ ) {
				if ( $length = $this->pathMatrix[$i][$j] ) {
					echo "<td bgcolor=". "#ffffff". ">$length</td>";
				}
				else {
					echo "<td>*</td>";
				}
			}
			echo '</tr>';
		}
		echo '</table>';
	}
	
	// 将当前路径信息加入数据库
	function insertPath() {
		
		$pathStart = $this->path['pathStart'];
		$pathEnd = $this->path['pathEnd'];
		$pathLength = $this->path['pathLength'];
		if ( !$pathStart || !pathEnd || !pathLength ) {
			echo "路径信息不完整，无法添加。请返回检查。". "<br/>";
			exit();
		}
		$insert_path_sql = "INSERT INTO path VALUES ( $pathStart, $pathEnd, $pathLength );";
		$con = $this->connectDB();
		if ( !$con->query( $insert_path_sql ) ) {
			printf("Errormessage: %s\n", $con->error);
			$con->close();
    		exit();
		}
			
		$insert_path_sql = "INSERT INTO path VALUES ( $pathEnd, $pathStart, $pathLength );";
		print( $insert_path_sql );
		if ( !$con->query( $insert_path_sql ) ) {
			printf("Errormessage: %s\n", $con->error);
			$con->close();
    		exit();
		}
		echo "<p>路径：'地点$pathStart ---- $pathLength ---- 地点$pathEnd' 已保存。</p>";
		$con->close();
	}
	
	// ****************** Dijkstra 算法 **********************************
	// 两点寻路，Dijkstra 算法。 解决有向图 G = ( V, E ) 上带权的单源最短路径问题。
	// 要求所有边的权值非负。地图路径符合这一要求。
	// 算法伪码如下，摘自《算法导论2e》
	// DIJKSTRA( G, w, s)
	// 1	INITIALIZE-SINGLE-SOURCE( G, s )
	// 2	S <- 空集
	// 3 	Q <- V[G]			/* 有向图 G 中所有顶点 */
	// 4	while Q != 空集		
	// 5		do 	u <- EXTRACT-MIN( Q )	/* 弹出集合 Q 中 d 值（距源 s 的距离）最小的那个顶点 */
	// 6			S <- S U { u }			/* 将 u 插入 S 中 */
	// 7			for each vertex v 属于 Adj[u]		/* 对 u 的每一个邻接顶点进行松弛 */
	// 8 				do RELAX( u, v, w )				/* 松弛， w 为 u->v 有向边的权 */
	// 我们这里依葫芦画瓢实现算法。
	
	function findPath( $startPlace, $endPlace ) {
		$placeS = array();
		$placeQ = $this->initializeSingleSource( $startPlace );
		// 对 placeQ 集合中所有顶点进行处理
		while ( count( $placeQ ) != 0 ) {
			$min = $this->extractMin( $placeQ );
			//print_r( $min ); echo "<br/>";
			$placeS[$min['placeNum']] = $min;
			// 
			$i = count( $this->pathMatrix );
			while ( $i != 0 ) {
				// 最小顶点的邻接点, 所有与当前最小点邻接的点都要进行松弛
				// 首先，第一个语句判断顶点 $i 是否为邻接点，
				// 第二个语句判断这个邻接点是否在 placeQ 集合中（未处理），在的话就要处理
				if ( $this->pathMatrix[ $min['placeNum'] ][ $i ] && $placeQ[$i] ) {		
					$this->relax( $min, $placeQ[$i] );
				}
				$i--;
			}
		}
		// 处理完毕，得到处理后的集合 placeS，输出
		$this->print_path( $placeS, $startPlace, $endPlace );
		echo "--!还不下车、意犹未尽？<br/>";
	}
	
	// Dijkstra 算法私有函数 ***************************
	// 初始化地点信息数组 placeArray
	function initializeSingleSource( $sourcePlaceNum ) {
		$con = $this->connectDB();
		$get_place_sql ="SELECT * FROM place;";
		if ( !( $result = $con->query( $get_place_sql ) ) ) {
			printf("Errormessage: %s\n", $con->error);
		}
		while ( $row = $result->fetch_assoc() ) {
			$placeArray[ $row['placeNum'] ] = array(	'placeNum' => $row['placeNum'],
														'placeName' => $row['placeName'],
														'placeDist' => MAX_DEST,
														'placePrev' => -1 );
		}
		$placeArray[ $sourcePlaceNum ]['placeDist'] = 0;
		$con->close();
		return $placeArray;
	}
	
	// 弹出数组中 placeDist 值最小的那个顶点
	function extractMin( &$placeQ ) {
		$min = array(	'placeNum' => 0,
						'placeName' => null,
						'placeDist' => MAX_DEST,
						'placePrev' => -1 );
		foreach ( $placeQ as $u ) {
			if ( $min['placeDist'] > $u['placeDist'] ) {
				$min = $u;
			}
		}
		unset( $placeQ[$min['placeNum']] );		// 从 $placeQ 数组中删去最小顶点
		return $min;
	}
	
	// 松弛
	function relax( $u, &$v ) {
		$length = $u['placeDist'] + $this->pathMatrix[ $u['placeNum'] ][ $v['placeNum'] ];
		//echo $length. " ";
		if ( $v['placeDist'] > $length ) {
			$v['placeDist'] = $length;
			$v['placePrev'] = $u['placeNum'];
		}
	}
	
	// 打印路径
	function print_path( $placeS, $startPlace, $endPlace ) {
		if ( $endPlace == $startPlace  ) {
			echo "地点". $startPlace. " (". $placeS[$startPlace]['placeName']. ") --> ";
		}
		else if ( $placeS[$endPlace]['placePrev'] != -1 ) {
			$this->print_path($placeS, $startPlace, $placeS[$endPlace]['placePrev'] );
			echo "地点". $endPlace." (". $placeS[$endPlace]['placeName']. ") --> ";
		}
		else {
			echo "地点". $startPlace. ": ". $placeS[$startPlace]['placeName']. " -- "
			. "地点". $endPlace. " (". $placeS[$endPlace]['placeName']. ") 的路径不存在。";
		}
	}
	// ************************* end of Dijkstra 算法 **********************************
}
?>