<?php

$another = ($con=mysqli_connect("localhost","my_user","my_password","my_db"));

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$firstname = mysqli_real_escape_string($con, mysqli_query($_POST['firstname']));
$lastname = mysqli_real_escape_string($con, strval($_GET['lastname']));

$location = strval($_POST['location']);

$sql="INSERT INTO Persons (FirstName, LastName, Age)
VALUES ('$firstname', '$lastname', '$age')";

if (!mysqli_query($con,$sql)) {
  die('Error: ' . mysqli_error($con));
}
echo "1 record added";

mysqli_close($con);

