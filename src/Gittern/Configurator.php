<?php

namespace Gittern;

/**
*
*/
class Configurator
{
  public function defaultConfigure(Repository $repo)
  {
    $repo->setHydrator('commit', new Hydrator\CommitHydrator($repo));
    $repo->setHydrator('tree', new Hydrator\TreeHydrator($repo));
    $repo->setHydrator('blob', new Hydrator\BlobHydrator($repo));
    $repo->setDesiccator('blob', new Desiccator\BlobDesiccator());
    $repo->setDesiccator('tree', new Desiccator\TreeDesiccator());
    $repo->setDesiccator('commit', new Desiccator\CommitDesiccator());
    $repo->setIndexHydrator(new Hydrator\IndexHydrator($repo));
    $repo->setIndexDesiccator(new Desiccator\IndexDesiccator());
  }
}