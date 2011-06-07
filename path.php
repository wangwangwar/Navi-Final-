<?php 
header("Content-Type:text/html; charset=utf8");
$postPlaceNum 	= $_POST['pathStart'];
$postPlaceName	= $_POST['pathEnd'];
$postPlaceInfo	= $_POST['pathLength'];

$postSearchStart	= $_POST['searchStart'];
$postSearchEnd		= $_POST['searchEnd'];
require 'path-class.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<title>路径信息</title>
</head>

<body>
<p><strong>添加新路径</strong></p>
<!-- A form to add a new path-->
<form action='path.php' method='post'>
<label for="pathStart">地点A编号</label>
<textarea id="pathStart" name='pathStart' rows='1' cols='10'></textarea>
<label for="pathEnd">地点B编号</label>
<textarea id="pathEnd" name='pathEnd' rows="1" cols="10"></textarea>
<label for="pathLength">路径长度</label>
<textarea id="pathLength" name='pathLength' rows="1" cols="10"></textarea>
<input type="submit" value="添加!" />
</form>

<p><strong>搜索路径</strong></p>
<!-- A form to add a new path-->
<form action='path.php' method='post'>
<label for="searchStart">地点A编号</label>
<textarea id="searchStart" name='searchStart' rows='1' cols='10'></textarea>
<label for="searchEnd">地点B编号</label>
<textarea id="searchEnd" name='searchEnd' rows="1" cols="10"></textarea>
<input type="submit" value="搜索!" />
</form>
<?php 
$p = new Path( $postPlaceNum, $postPlaceName, $postPlaceInfo );
if ( $postPlaceNum && $postPlaceName && $postPlaceInfo ) {
	$p->insertPath();
}

$p->getPathMatrix();
//$p->printPathMatrix();
if ( $postSearchStart != null && $postSearchEnd != null ) {
	$countPlace = count( $p->pathMatrix );
	if ( ( $postSearchStart <= 0 ) || ( $postSearchStart > $countPlace ) 
		||	( $postSearchEnd <= 0 ) || ( $postSearchEnd > $countPlace ) ) {
			echo "您要到的地方太神秘了，我找不到啦<br/>";
			echo "我们这里地点编号范围： 0 ~ ". $countPlace. "<br/>";
			}
	else if ( $postSearchStart == $postSearchEnd ) {
		echo "原地转三圈就到啦～<br/>";
	}
	else {
		echo "上车吧 --> ";
		$p->findPath( $postSearchStart, $postSearchEnd );
	}
}
else {
	echo "您还没有告诉我到哪儿呢。<br/>";
}

?>

</body>
</html>