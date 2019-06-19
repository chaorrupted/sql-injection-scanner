<?php
require "/home/chao/vendor/autoload.php";


use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

$code = <<<'CODE'
<?php
$con=mysqli_connect("localhost","my_user","my_password","my_db");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// escape variables for security
$firstname = mysqli_real_escape_string($con, $_POST['firstname']);
$lastname = mysqli_real_escape_string($con, $_GET['lastname']);
$age = mysqli_real_escape_string($con, $_POST['age']);

$sql="INSERT INTO Persons (FirstName, LastName, Age)
VALUES ('$firstname', '$lastname', '$age')";

if (!mysqli_query($con,$sql)) {
  die('Error: ' . mysqli_error($con));
}
echo "1 record added";

mysqli_close($con);
CODE;

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
try {
    $ast = $parser->parse($code);
} catch (Error $error) {
    echo "Parse error: {$error->getMessage()}\n";
    return;
}


use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};

$traverser = new NodeTraverser;
$traverser->addVisitor(new class extends NodeVisitorAbstract {
    public function enterNode(Node $node) {
        if ($node instanceof Node\Expr\Variable && $node->name == "_POST") { //modify to scream
            echo "found: post node, potential vulnerability\n";
	}
	elseif ($node instanceof Node\Expr\Variable && $node->name == "_GET"){
	    echo "found: get node, potential vulnerability\n";
	}
    }
});


$modifiedStmts = $traverser->traverse($ast);
