<?php

require "/home/chao/vendor/autoload.php";

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};

#will turn to file read in the future
$code = <<<'CODE'
<?php
$con=mysqli_connect("localhost","my_user","my_password","my_db");

if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$firstname = mysqli_real_escape_string($con, $_POST['firstname']);
$lastname = mysqli_real_escape_string($con, strval($_POST['lastname']));

$location = strval($_POST['location']);

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



#-----------LINK-PARENTS-------------------
class ParentConnector extends NodeVisitorAbstract {
    private $stack;
    public function beforeTraverse(array $nodes) {
        $this->stack = [];
    }
    public function enterNode(Node $node) {
        if (!empty($this->stack)) {
            $node->setAttribute('parent', $this->stack[count($this->stack)-1]);
        }
        $this->stack[] = $node;
    }
    public function leaveNode(Node $node) {
        array_pop($this->stack);
    }
}
$pretraverser = new NodeTraverser;
$pretraverser->addVisitor(new ParentConnector);
#------------------------------


$flag = 0;
function climb_up(Node $node){
	global $flag;
	if ($flag == 1){
	    echo "short-circuiting climb...\n";
	    $flag = 0;
	    return true;
	}
        if($node->hasAttribute('parent')){
            $parent = $node->getAttribute('parent');
	    $parent_type = $parent->getType();
	    $node_type = $node->getType();
	    echo $node_type." node has parent node with type: '$parent_type'\n";

	    if($node_type == 'Expr_FuncCall'){
	    	echo "caught function node. ";
		$flag = is_safe($node);
	    }
	    return climb_up($parent);
	}
	else{
	    $node_type = $node->getType();
	    echo "node ".$node_type." does not have a parent\n";
	    return false;
	}
}

function is_safe(Node $func_node){
    echo "inspecting ".$func_node->getType()." for mysqli_real_escape_string...\n";
    $fname = $func_node->name;
    echo "NAME : ".$fname."\n";
    if($fname == "mysqli_real_escape_string"){
        echo "input is sanitized.\n";
        return 1;
    }
    else{
	    echo "function call is not sanitizer function\n";
	    echo "continue climb..\n";
        return 0;
    }
}

$traverser = new NodeTraverser;
$traverser->addVisitor(new class extends NodeVisitorAbstract {
	
    public function enterNode(Node $node) {
        if ($node instanceof Node\Expr\Variable && $node->name == "_POST") {
		
		echo "found: '_POST' at line ".$node->getLine().", potential vulnerability\n";
		if($node->hasAttribute('parent')){
		    $parent = $node->getAttribute('parent');
		    $parent_type = $parent->getType();
		    echo "post node has parent node with type: '$parent_type'\n";

		    
		    if(!climb_up($parent)){
		        echo "VULNERABILITY: input from _POST without escape_string call at line ".$node->getLine()."\n";
		    }
		    echo "----------\n";
		}
	}
	#elseif ($node instanceof Node\Expr\Variable && $node->name == "_GET"){
	#    echo "found: get node, potential vulnerability\n";
	#}
    }
});

$ast = $pretraverser->traverse($ast);
$stmt = $traverser->traverse($ast);


$dumper = new NodeDumper;
echo $dumper->dump($stmt) . "\n";
