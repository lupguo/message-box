<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 2017/11/6
 * Time: 22:43
 */

namespace MyProtobuf;


use Search\ESearch\SearchRequest;

class EsSearchClient
{
    public function run() {

        //protobuf test
        $esSearch = new SearchRequest();

        $esSearch->setCorpus(3);
        $esSearch->setQuery('HelloWorld');
        $esSearch->setPageNumber(5);
        $esSearch->setResultPerPage(10);

        var_dump($esSearch->serializeToString());
    }
}