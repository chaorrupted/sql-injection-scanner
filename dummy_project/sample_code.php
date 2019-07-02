<?php

function clean(string $i){
    $i = $i."qsadfrwed";
    return mysql_escape_string($i);
}


$usr = $_GET['username'];

$usr = clean($usr);
#          X
#
# $usr = $usr."qsadfrwed";
# X = mysql_escape_string($usr)
#     >
#  $usr = X 
#
#
#
