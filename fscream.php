<?php

require "/home/chao/vendor/autoload.php";

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};

#will turn to file read in the future
#

#turn to multiple file or automate with python
if(! isset($argv[1])){
    exit("no file specified. halt execution\n");
}
else{
    $filename = $argv[1];
    echo "reading: "."$filename"."\n";
    $code = file_get_contents("$filename");
    if($code == FALSE){
        echo "failed to read file.\n";
    }
}
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
$traverser->traverse($ast);
