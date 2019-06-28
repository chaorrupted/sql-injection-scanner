<?php

require "/home/chao/vendor/autoload.php";

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};

#TESTING PUSH

#maybe add these to _globals
$tainted = [];
$clear = [];
$flag = 0;

function climb_up(Node $node){
	global $flag;
	if ($flag == 1){
	    echo "short-circuiting climb...\n";
	    $flag = 0;
	    return true;
	}elseif ($flag == -1){
		echo "found.\n";
		$flag = 0;
		return false;
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
	    #TAINTED variable: add to global list
	    return false;
	}
}

function is_safe(Node $func_node){
    include '/home/chao/sql-injection-scanner/SSS.php';
    echo "checking if ".$func_node->getType()." is in sanitizers list...\n";
    $fname = $func_node->name;
    echo "NAME : ".$fname."\n";
    if (in_array($fname, $sanitizers)){
        echo "function is in sanitizers list. input is sanitized.\n";
        return 1;
    }
    elseif (in_array($fname, $sinks)){
    	 echo "function is a sink! vulnerability ";
         return -1;
    }else{
	    echo "function call is not sanitizer function\n";
	    echo "continue climb..\n";
       return 0;
    }
}

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

class Screamer extends NodeVisitorAbstract {
	
    public function enterNode(Node $node) {
	include "/home/chao/sql-injection-scanner/SSS.php";

        if ($node instanceof Node\Expr\Variable && in_array("$".$node->name, $sources)) {
		
		echo "found source: ".$node->name." at line ".$node->getLine().", potential vulnerability\n";
		if($node->hasAttribute('parent')){
		    $parent = $node->getAttribute('parent');
		    $parent_type = $parent->getType();
		    echo $node->name." node has parent node with type: '$parent_type'\n";
	    
		    if (!climb_up($parent)){
		        echo "VULNERABILITY: input from ".$node->name." without sanitization call at line ".$node->getLine()."\n";
		    }
		    echo "----------\n";
		}
	}
    }
}

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

$traverser = new NodeTraverser;
$traverser->addVisitor(new Screamer);

$pretraverser = new NodeTraverser;
$pretraverser->addVisitor(new ParentConnector);



if (!isset($argv[1])){
    exit("no target specified. halt execution\n");
}
else{


    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($argv[1]));

    $files = array(); 

    foreach ($rii as $file) {

        if ($file->isDir()){ 
            continue;
        }

        $files[] = $file->getPathname(); 

    }

    foreach($files as $file) {

	if (strpos($file, "/.")){
	    #echo "skipping ".$file."\n";
	    continue;
	}

	echo "reading: ".$file."\n";
        $code = file_get_contents($file);
        if($code == FALSE){
            echo "failed to read file.\n";
        }

        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }

        $ast = $pretraverser->traverse($ast);
        $traverser->traverse($ast);

    }
}


