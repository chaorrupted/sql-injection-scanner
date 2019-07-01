<?php

require "/home/chao/vendor/autoload.php";

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};


class Taint extends NodeVisitorAbstract {
	
	public function enterNode(Node $node){
		echo 'visiting: '.$node->getType()."\n";

	}
}




function is_tainted(Node $node){
	#traverse given tree using Screamer from fscream
	#return tainted or safe
	$traverser = new NodeTraverser;
	$traverser->addVisitor(new Taint);
	$traverser->traverse(array($node));
	
	

}


#IMPORTANT: use set for tainted&clear


class Snail extends NodeVisitorAbstract {
    
	public function enterNode(Node $node){
		if ($node instanceof Node\Expr\Assign){

			echo 'found an assignment at line '.$node->getLine()."\n";
			echo 'var is: '.$node->var->name."\n";
			echo 'calling is_tainted on: '.$node->expr->getType()."\n";
			
			$vname = $node->var->name;
			if (is_tainted($node->expr)){
				
				if (($key = array_search($vname, $CLEAR)) !== false){
					unset($CLEAR[$key]); #sets may be better.
				}
				if (array_search($vname, $TAINTED) !== false){
					array_push($TAINTED, $vname);
				}

			}
			else{
				#then remove from tainted add to clear
			}
			

		}	
	
	}
}


