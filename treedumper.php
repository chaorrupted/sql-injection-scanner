<?php
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

require "/home/chao/vendor/autoload.php";

$code = <<<'CODE'
<?php

class MyClass{
    public function whatevr($in){
        return mysql_real_escape_string($in);
    }

}

$ahmey = new MyClass;

$usr = $_GET['username'];

$usr = $ahmey->whatevr($usr);




CODE;

$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
try {
    $ast = $parser->parse($code);
} catch (Error $error) {
    echo "Parse error: {$error->getMessage()}\n";
    return;
}

$dumper = new NodeDumper;
echo $dumper->dump($ast) . "\n";
