<?php
################################################################################
#	is_64bit(). Taken from StackOverflow. Written by 
################################################################################
function is_64bit()
{
    $int = "9223372036854775807";
    $int = intval($int);
    if ($int == 9223372036854775807) {
        /* 64bit */
        return true;
    } elseif ($int == 2147483647) {
        /* 32bit */
        return false;
    } else {
        /* error */
        return "error";
    }
}

?>

