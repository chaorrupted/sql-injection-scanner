<?php

require "/home/chao/vendor/autoload.php";

require_once "/home/chao/sql-injection-scanner/fscream.php";

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};



$tainted;
$root;
function is_tainted(Node $node){
    #traverse given tree using Screamer from fscream
    #somehow return tainted or safe ??

    global $root;
    global $tainted;

    $root = $node->getAttribute('parent');
    $tainted = false;

    $traverser = new NodeTraverser;
    $traverser->addVisitor(new Screamer);
    
    $traverser->traverse(array($node));
    return $tainted;

}


$CLEAR = new \Ds\Set();
$DIRTY = new \Ds\Set();


class Snail extends NodeVisitorAbstract {
    
    public function enterNode(Node $node){
        if ($node instanceof Node\Expr\Assign){
            global $CLEAR;
            global $DIRTY;

            echo 'found an assignment at line '.$node->getLine()."\n";
            echo 'var is: '.$node->var->name."\n";
            echo 'calling is_tainted on: '.$node->expr->getType()."\n";
            
            $vname = $node->var->name;
            if (is_tainted($node->expr)){
                
                if ($CLEAR->contains($vname)){
                    $CLEAR->remove($vname);
                    echo "removed $vname from clear\n";
                }
                if (!$DIRTY->contains($vname)){
                    $DIRTY->add($vname);
                    echo "added $vname to dirty\n";
                }

            }
            else{
                
                if ($DIRTY->contains($vname)){
                    $DIRTY->remove($vname);
                    echo "removed $vname from dirty\n";
                }
                if (!$CLEAR->contains($vname)){
                    $CLEAR->add($vname);
                    echo "added $vname to clear\n";
                }


            }
            

        }
        #else -sink check here   --leaveNode may be the place    
    
    }
}


