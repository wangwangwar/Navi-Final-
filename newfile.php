<?php
$placeQ = array ( 1, 2, 3, 4, 5, 6 );
unset( $placeQ[1] );
unset( $placeQ[2] );
echo count( $placeQ );	

define( "MAX_DEST", 10000 );
echo MAX_DEST;