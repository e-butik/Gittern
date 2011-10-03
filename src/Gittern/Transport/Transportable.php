<?php

namespace Gittern\Transport;

interface Transportable
{
  public function resolveObject($sha);

  public function resolveTreeish($treeish);
}