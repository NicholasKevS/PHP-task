<?php

//app settings
$minNum = 1;
$maxNum = 100;

for($i=$minNum;$i<=$maxNum;$i++) {
	if($i%3 == 0 && $i%5 == 0) {
		echo "foobar,";
	} elseif($i%3 == 0) {
		echo "foo,";
	} elseif($i%5 == 0) {
		echo "bar,";
	} else {
		echo "$i,";
	}
}

?>