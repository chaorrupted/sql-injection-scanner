# sql-injection-scanner

Simple tool to scan a PHP code to look for potential injection vulnerabilities. Made using nikic/PHP-Parser, written in PHP.

to run: `php fscream.php PATH/TO/YOUR_FILE`


currently,  only check variable nodes for sources and 
            only checks function nodes for sanitizers & sinks.

also, does not check globally for tainted-clear inputs (between blocks)
