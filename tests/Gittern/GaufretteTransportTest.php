<?php

namespace Gittern;

use Gaufrette\Filesystem;
use Gaufrette\Adapter\Local;

/**
* @author Magnus Nordlander
**/
class GaufretteTransportTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @author Magnus Nordlander
   **/
  public function testGetRegularObject()
  {
    $adapter = new Local('/Users/magnus/Developer/KlarnaPHP/.git');
    $filesystem = new Filesystem($adapter);

    $resolver = new Transport\GaufretteTransport($filesystem);

    $resolver->resolveObject('c3c8d149d63338c1215e0b06c839768cdf1933d5');
  }
}