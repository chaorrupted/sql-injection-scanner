<?php

require "/home/chao/vendor/autoload.php";

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};


$CLEAR = new \Ds\Set();
$DIRTY = new \Ds\Set();

$CLASSES = [];
$FUNCTIONS = [];
$INSTANCES = [];
$METHODS = [];

class Snail extends NodeVisitorAbstract {
    
    public function enterNode(Node $node){
        include "./SSS.php";
        
        global $CLEAR;
        global $DIRTY;
        global $INSTANCES;

        if ($node instanceof Node\Stmt\Class_){ #methods reqrd
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }elseif ($node instanceof Node\Stmt\Function_){
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }elseif ($node instanceof Node\Expr\Assign && $node->expr instanceof Node\Expr\New_){
            
            $INSTANCES[$node->var->name] = $node->expr->class->parts[0];

        }elseif ($node instanceof Node\Expr\Assign && $node->var instanceof Node\Expr\ArrayDimFetch){
            #needs fixing
            
            $vname = $node->var->var->name;
            if (is_tainted($node->expr)){
                if ($CLEAR->contains($vname)){
                    $CLEAR->remove($vname);
                }
                if (!$DIRTY->contains($vname)){
                    $DIRTY->add($vname);
                }
            }else{
                if ($DIRTY->contains($vname)){
                    $DIRTY->remove($vname);
                }
                if (!$CLEAR->contains($vname)){
                    $CLEAR->add($vname);
                }
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }

        }elseif ($node instanceof Node\Expr\Assign){

            $vname = $node->var->name;


            if (is_tainted($node->expr)){
                if ($CLEAR->contains($vname)){
                    $CLEAR->remove($vname);
                }
                if (!$DIRTY->contains($vname)){
                    $DIRTY->add($vname);
                }
            }else{
                if ($DIRTY->contains($vname)){
                    $DIRTY->remove($vname);
                }
                if (!$CLEAR->contains($vname)){
                    $CLEAR->add($vname);
                }
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }
        }elseif ($node instanceof Node\Expr\FuncCall && in_array($node->name, $sinks) ) {
            
            $i = 0;
            $i_t = 0;
            foreach ($node->args as $ar){
                $t = is_tainted($ar);
                if ($t) { $i_t++; }
                $i++;
            }
            
            if ($i_t) {
                global $file;
                echo '  '.$file.':'.$node->getLine().':breach'."\n";
            }
        } 
    }
}

class Screamer extends NodeVisitorAbstract { 
    
    public function enterNode(Node $node) {
        include "./SSS.php";
        global $DIRTY;
        global $CLEAR;

        if ($node instanceof Node\Expr\Variable && in_array('$'.$node->name, $sources)) {            
            climb_up($node);
        }elseif ($node instanceof Node\Expr\Variable && $DIRTY->contains($node->name)) {
            climb_up($node);
        }
    }
}

class Librarian extends NodeVisitorAbstract { #Pretty critical BUG: same variable name used in different scopes gets mixed up
                                              
    public function enterNode(Node $node){
        global $CLASSES;
        global $FUNCTIONS;
        global $METHODS;

        if ($node instanceof Node\Stmt\Class_){
            $CLASSES[($node->name->name)] = $node;
        }elseif ($node instanceof Node\Stmt\Function_){
            $FUNCTIONS[$node->name->name] = $node;
        }elseif ($node instanceof Node\Stmt\ClassMethod){
            $METHODS[$node->name->name] = $node;
        }

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

#################################################


$tainted;
$root;
function is_tainted(Node $node){
    
    global $root;
    global $tainted;

    $root = $node->getAttribute('parent');
    $tainted = false;

    $traverser = new NodeTraverser;
    $traverser->addVisitor(new Screamer);
    
    $traverser->traverse(array($node));
    return $tainted;

}


$flag = 0;
function climb_up(Node $node){
    global $flag;
    global $tainted;
    global $root;

    if ($flag == 1){
        
        $flag = 0;
        return true;
    }

    if($node->hasAttribute('parent') && $node !== $root){
        $parent = $node->getAttribute('parent');
        $parent_type = $parent->getType();
        $node_type = $node->getType();
        
        if($node_type == 'Expr_FuncCall'){
            
            $flag = is_safe($node);
        }
        return climb_up($parent);
    
    }elseif ($node === $root ){
        $tainted = true;
        return false;
    }else{
        $node_type = $node->getType();
        $tainted = true;
        return false;
    }
}

function is_safe(Node $func_node){
    include './SSS.php';
    
    $fname = $func_node->name;
    
    if (in_array($fname, $sanitizers)){
        return 1;
    }else{
        return 0;
    }
}


$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

$traverser = new NodeTraverser;
$traverser->addVisitor(new Snail);

$classfinder = new NodeTraverser;
$classfinder->addVisitor(new Librarian);

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
        $classfinder->traverse($ast);
        $traverser->traverse($ast);

    }
}

