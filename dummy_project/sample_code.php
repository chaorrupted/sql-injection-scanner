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

$usr = $_GET['username'];
$pass = $_GET['password'];

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
