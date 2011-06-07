<?php 
header("Content-Type:text/html; charset=utf8");
$postPlaceNum 	= $_POST['placeNum'];
$postPlaceName	= $_POST['placeName'];
$postPlaceInfo	= $_POST['placeInfo'];
require 'place-class.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf8">
<title>地点信息</title>
</head>

<body>
<p><strong>添加新地点</strong></p>
<!-- A form to add a new path-->
<form action='place.php' method='post'>
<label for="placeNum">地点编号</label>
<textarea id="placeNum" name='placeNum' rows='1' cols='10'></textarea>
<br></br>
<label for="placeName">地点名称</label>
<textarea id="placeName" name='placeName' rows="1" cols="10"></textarea>
<br></br>
<label for="placeInfo">写一点简介吧</label>
<textarea id="placeInfo" name='placeInfo' rows="5" cols="20"></textarea>
<br></br>
<input type="submit" value="OK!">
</form>
<br></br>

<?php 
$p = new Place( $postPlaceNum, $postPlaceName, $postPlaceInfo );
if( $postPlaceNum && $postPlaceName && $postPlaceInfo ) {
	$p->insertPlace();
}
?>

</body>
</html>