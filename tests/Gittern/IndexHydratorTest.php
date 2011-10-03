<?php

namespace Gittern;

/**
* @author Magnus Nordlander
**/
class IndexHydratorTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @author Magnus Nordlander
   **/
  public function testHydrateIndex()
  {
    $data = file_get_contents('/Users/magnus/Developer/KlarnaPHP/.git/index');

    $hydrator = new Hydrator\IndexHydrator($this->getMock('Gittern\Repository', array(), array(), '', false));

    $index = $hydrator->hydrate($data);

    //var_dump($index);
  }
}