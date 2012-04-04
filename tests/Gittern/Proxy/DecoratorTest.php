<?php

namespace Gittern\Proxy;

use Mockery as M;

use ReflectionClass;
use ReflectionMethod;

abstract class DecoratorTest extends \PHPUnit_Framework_TestCase
{
  public function assertClassIsGaplessDecorator($base_class, $decorator_class)
  {
    $base_rc = new ReflectionClass($base_class);
    $decorator_rc = new ReflectionClass($decorator_class);

    $this->assertTrue($decorator_rc->isSubclassOf($base_class));

    $base_methods = $base_rc->getMethods(ReflectionMethod::IS_PUBLIC);
    foreach ($base_methods as $base_method)
    {
      $decorator_method = $decorator_rc->getMethod($base_method->getName());
      if ($decorator_method->getDeclaringClass() != $decorator_rc)
      {
        $this->fail(sprintf("Method %s expected to be redeclared in decorator, but wasn't.", $base_method->getName()));
      }
    }
  }

  public function setupExpectationsOnMockAndGetParams($mock, $method, $return_value = 0xDEADCAFEBABE)
  {
    $params = array();

    $rm = new ReflectionMethod($mock->mockery_getName(), $method);
    foreach ($rm->getParameters() as $rp)
    {
      if ($rp->isArray())
      {
        $params[$rp->getPosition()] = array(uniqid());
      }
      else if ($rc = $rp->getClass())
      {
        $params[$rp->getPosition()] = M::mock($rc->getName());
      }
      else
      {
        $params[$rp->getPosition()] = uniqid();
      }
    }

    $expectation = $mock->shouldReceive($method);

    call_user_func_array(array($expectation, 'with'), $params);

    if ($return_value != 0xDEADCAFEBABE)
    {
      $expectation->andReturn($return_value);
    }

    return $params;
  }
}