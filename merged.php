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
        include "/home/chao/sql-injection-scanner/SSS.php";
        
        global $CLEAR;
        global $DIRTY;
        global $INSTANCES;




        if ($node instanceof Node\Stmt\Class_){#methods reqrd
            #echo "\nskipping over class decleration\n";
            #echo "on line ".$node->getLine()." with name ".$node->name."\n";

            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }elseif ($node instanceof Node\Stmt\Function_){
            #echo "\nskipping over a function decleration\n";
            #echo "on line ".$node->getLine()." with name ".$node->name."\n";

            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }elseif ($node instanceof Node\Expr\Assign && $node->expr instanceof Node\Expr\New_){
            #echo "\nfound assingment with call to 'new' on line :".$node->getLine()."\n";
            #echo $node->var->name." is an instance of ".$node->expr->class."\n\n";

            $INSTANCES[$node->var->name] = $node->expr->class->parts[0];

        }elseif ($node instanceof Node\Expr\Assign && $node->var instanceof Node\Expr\ArrayDimFetch){
            #echo "found an array FETCH on line".$node->getLine()."\n";
            #echo "array name is: ".$node->var->var->name."\n";
            #echo 'calling is_tainted on: '.$node->expr->getType()."\n";
            
            $vname = $node->var->var->name;


            if (is_tainted($node->expr)){
                
                if ($CLEAR->contains($vname)){
                    $CLEAR->remove($vname);
                    #echo "removed $vname from clear\n\n";
                }
                if (!$DIRTY->contains($vname)){
                    $DIRTY->add($vname);
                    #echo "added $vname to dirty\n\n";
                }

            }else{
                
                if ($DIRTY->contains($vname)){
                    $DIRTY->remove($vname);
                    #echo "removed $vname from dirty\n\n";
                }
                if (!$CLEAR->contains($vname)){
                    $CLEAR->add($vname);
                    #echo "added $vname to clear\n\n";
                }
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }
    

        }elseif ($node instanceof Node\Expr\Assign){


            #echo 'found an assignment at line '.$node->getLine()."\n";
            #echo 'var is: '.$node->var->name."\n";

            #echo 'calling is_tainted on: '.$node->expr->getType()."\n";
            
            $vname = $node->var->name;


            if (is_tainted($node->expr)){
                
                if ($CLEAR->contains($vname)){
                    $CLEAR->remove($vname);
                    #echo "removed $vname from clear\n\n";
                }
                if (!$DIRTY->contains($vname)){
                    $DIRTY->add($vname);
                    #echo "added $vname to dirty\n\n";
                }

            }else{
                
                if ($DIRTY->contains($vname)){
                    $DIRTY->remove($vname);
                    #echo "removed $vname from dirty\n\n";
                }
                if (!$CLEAR->contains($vname)){
                    $CLEAR->add($vname);
                    #echo "added $vname to clear\n\n";
                }
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }
        }elseif ($node instanceof Node\Expr\FuncCall && in_array($node->name, $sinks) ) {

            #echo "SINK FUNCTION CALL:\n"."NAME: $node->name \n"."LINE: ".$node->getLine()."\n";
            $i = 0;
            $i_t = 0;
            foreach ($node->args as $ar){
                $t = is_tainted($ar);
                #$x = $t ? "tainted" : "safe";
                #echo "arg #$i : ".$x."\n";
                if ($t) { $i_t++; }
                $i++;
            }
            #echo "$i_t of total $i arguments are tainted.\n";

            if ($i_t) {
                global $file;
                echo $file.':'.$node->getLine().':breach'."\n";

                #echo "------------------------------------------\n";
                #echo "ALERT: tainted input given to sink function(".$node->name.") on line ".$node->getLine()."!\n";
                #echo "------------------------------------------\n";
            }
        } #elseif ($node instanceof Node\Expr\FuncCall && is user defind)
    }
}

class Screamer extends NodeVisitorAbstract { #WHAT
    
    public function enterNode(Node $node) {
        include "/home/chao/sql-injection-scanner/SSS.php";
        global $DIRTY;
        global $CLEAR;

        if ($node instanceof Node\Expr\Variable && in_array('$'.$node->name, $sources)) {            
            #echo "found source: ".$node->name." at line ".$node->getLine().", potential vulnerability\n";
            if (!climb_up($node)){
                #echo "Input from ".$node->name." without sanitization at line ".$node->getLine()."\n";
            }
        }elseif ($node instanceof Node\Expr\Variable && $DIRTY->contains($node->name)) {
            #echo "found a tainted variable on line ".$node->getLine()."\n";
            if (!climb_up($node)){
                #echo "VLN: tainted variable persists and infects\n";
            }
        }
    }
}

class Librarian extends NodeVisitorAbstract { #Pretty critical BUG: same variable name used in different scopes gets mixed up
                                              #fix by renaming 
    public function enterNode(Node $node){
        global $CLASSES;
        global $FUNCTIONS;
        global $METHODS;

        if ($node instanceof Node\Stmt\Class_){#methods reqrd
            #echo "\n################################################################################\n";
            #echo "found a class declaration on line ".$node->getLine()." with name ".$node->name."\n";
            #echo "################################################################################\n\n";

            $CLASSES[($node->name->name)] = $node;
        }elseif ($node instanceof Node\Stmt\Function_){
            #echo "\n################################################################################\n";
            #echo "found a function declaration on line ".$node->getLine()." with name ".$node->name."\n";
            #echo "################################################################################\n\n";

            $FUNCTIONS[$node->name->name] = $node;
        }elseif ($node instanceof Node\Stmt\ClassMethod){
            #echo "\n################################################################################\n";
            #echo "found a class method declaration on line ".$node->getLine()." with name ".$node->name."\n";
            #echo "################################################################################\n\n";

            $METHODS[$node->name->name] = $node;
        }

    }
}

###############################################################################

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

###############################################################################


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
        #echo "short-circuiting climb...\n";
        $flag = 0;
        return true;
    }

    if($node->hasAttribute('parent') && $node !== $root){
        $parent = $node->getAttribute('parent');
        $parent_type = $parent->getType();
        $node_type = $node->getType();
        #echo $node_type." node has parent node with type: '$parent_type'\n";

        if($node_type == 'Expr_FuncCall'){
            #echo "caught function node. ";
            $flag = is_safe($node);
        }
        return climb_up($parent);
    
    }elseif ($node === $root ){
        #echo "reached root, stopping climb \n";
        $tainted = true;
        return false;
    }else{
        $node_type = $node->getType();
        #echo "node ".$node_type." does not have a parent\n";
        $tainted = true;
        return false;
    }
}

function is_safe(Node $func_node){
    include '/home/chao/sql-injection-scanner/SSS.php';
    
    #echo "checking if ".$func_node->getType()." is in sanitizers list...\n";
    $fname = $func_node->name;
    #echo "NAME : ".$fname."\n";
    
    if (in_array($fname, $sanitizers)){
        #echo "function is in sanitizers list. input is (probably) sanitized.\n";
        return 1;
    }else{
        #echo "function call is not sanitizer function\n";
	    #echo "continue climb..\n";
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
        $classfinder->traverse($ast);
        $traverser->traverse($ast);

#        $dumper = new NodeDumper;
#        foreach ($METHODS as $name => $tree){
#            echo "\ntree for ".$name." looks like this:\n";
#            echo $dumper->dump($tree)."\n";
#        }
#        foreach ($FUNCTIONS as $name => $tree){
#            echo "\ntree for ".$name." looks like this:\n";
#            echo $dumper->dump($tree)."\n";
#        }
#        foreach ($CLASSES as $name => $tree){
#            echo "\ntree for ".$name." looks like this:\n";
#            echo $dumper->dump($tree)."\n";
#        }
#        
#        echo "instances: ";
#        print_r($INSTANCES);

    }
}

