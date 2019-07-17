<?php


class MyClass{

    private $dollar = 3;

    public function whatevr($in){
        return mysql_real_escape_string($in);
    }

}

function clean(&$inp){
    $inp = mysql_escape_string($inp);
}



$ahmey = new MyClass;

mysqli_query($_GET['somethnfdghg']);

$usr = $_GET['username'];
$pass = $_GET['password'];

mysqli_query($usr);

$usr = $ahmey->whatevr($usr);

clean($pass);

#          X
#
# $usr = $usr."qsadfrwed";
# X = mysql_escape_string($usr)
#     >
#  $usr = X 
#
#
#
