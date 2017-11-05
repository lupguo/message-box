<?php
/**
 * rpc-protobuf 调试
 * User: Terry
 * Date: 2017/11/5
 * Time: 22:42
 */

$autoloader = require '../vendor/autoload.php';
$autoloader->addPsr4('', ['../php_out']);

$esSearch = new Search\ESearch\SearchRequest();

$esSearch->setCorpus(3);
$esSearch->setQuery('HelloWorld');
$esSearch->setPageNumber(5);
$esSearch->setResultPerPage(10);

var_dump($esSearch->serializeToString());


//$from = new Foo();
//$from->serializeToString(1);
//$from->setString('a');
//$from->getRepeatedInt32()[] = 1;
//$from->getMapInt32Int32()[1] = 1;
//$data = $from->serializeToString();
//
//var_dump($data);


//try {
//    $to->mergeFromString($data);
//} catch (Exception $e) {
//    // Handle parsing error from invalid data.
//}


