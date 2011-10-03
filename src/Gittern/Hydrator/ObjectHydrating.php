<?php

namespace Gittern\Hydrator;

interface ObjectHydrating
{
  public function hydrate($object_sha, $data);
}