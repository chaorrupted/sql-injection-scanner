<?php

require "/home/chao/vendor/autoload.php";

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};

require_once "./snail.php";

$flag = 0;
function climb_up(Node $node){
    global $flag;
    global $tainted;
    global $root;

    if ($flag == 1){
        echo "short-circuiting climb...\n";
        $flag = 0;
        return true;
    }

    if($node->hasAttribute('parent') && $node !== $root){
        $parent = $node->getAttribute('parent');
        $parent_type = $parent->getType();
        $node_type = $node->getType();
        echo $node_type." node has parent node with type: '$parent_type'\n";

        if($node_type == 'Expr_FuncCall'){
            echo "caught function node. ";
            $flag = is_safe($node);
        }
        return climb_up($parent);
    
    }elseif ($node === $root ){
        echo "reached root, stopping climb \n";
        $tainted = true;
        return false;
    }else{
        $node_type = $node->getType();
        echo "node ".$node_type." does not have a parent\n";
        #TAINTED variable: add to global list
        $tainted = true;
        return false;
    }
}

function is_safe(Node $func_node){
    include '/home/chao/sql-injection-scanner/SSS.php';
    
    echo "checking if ".$func_node->getType()." is in sanitizers list...\n";
    $fname = $func_node->name;
    echo "NAME : ".$fname."\n";
    
    if (in_array($fname, $sanitizers)){
        echo "function is in sanitizers list. input is (probably) sanitized.\n";
        return 1;
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
        global $DIRTY;
        global $CLEAR;

        if ($node instanceof Node\Expr\Variable && in_array('$'.$node->name, $sources)) {            
            echo "found source: ".$node->name." at line ".$node->getLine().", potential vulnerability\n";
            if (!climb_up($node)){
                echo "Input from ".$node->name." without sanitization at line ".$node->getLine()."\n";
            }
        }elseif ($node instanceof Node\Expr\Variable && $DIRTY->contains($node->name)) {
            echo "found a tainted variable on line ".$node->getLine()."\n";
            if (!climb_up($node)){
                echo "VLN: tainted variable persists and infects\n";
            }
        }
    }
}

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

$traverser = new NodeTraverser;
#$traverser->addVisitor(new Screamer);
$traverser->addVisitor(new Snail);

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


