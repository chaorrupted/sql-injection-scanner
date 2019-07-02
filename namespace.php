<?php

require "/home/chao/vendor/autoload.php";

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeTraverser, NodeVisitorAbstract};

class InspectorGadget extends NodeVisitorAbstract {
    
    public function enterNode(Node $node){
        
    }
}
