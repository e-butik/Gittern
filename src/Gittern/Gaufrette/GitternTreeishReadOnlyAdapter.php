<?php

namespace Gittern\Gaufrette;

use Gittern\Repository;
use Gittern\Entity\GitObject\Blob;
use Gittern\Entity\GitObject\Commit;
use Gittern\Entity\GitObject\Tree;

use Gaufrette\Adapter\Base as BaseAdapter;

/**
* @author Magnus Nordlander
**/
class GitternTreeishReadOnlyAdapter extends BaseAdapter
{
  protected $repo;
  protected $tree;

  public function __construct(Repository $repo, $treeish)
  {
    $this->repo = $repo;

    $object = $repo->getObject($treeish);
    if ($object instanceof Commit)
    {
      $object = $object->getTree();
    }

    if ($object instanceof Tree)
    {
      $this->tree = $object;
    }
    else
    {
      throw new \RuntimeException("Could not resolve treeish to a tree.");
    }
  }

  public function read($key)
  {
    $components = explode('/', $key);

    $object = $this->tree;

    foreach ($components as $component)
    {
      if ($object instanceof Tree)
      {
        $node = $object->getNodeNamed($component);

        if ($node)
        {
          $object = $node->getRelatedObject();
          continue;
        }
      }

      break;
    }

    if ($object instanceof Blob)
    {
      return $object->getContents();
    }

    throw new \RuntimeException(sprintf('Could not read the \'%s\' file.', $key));
  }

  public function write($key, $content, array $metadata = null)
  {
    throw new \RuntimeException("This adapter is read-only.");
  }

  public function exists($key)
  {
    return array_search($key, $this->keys()) !== false;
  }

  public function keys()
  {
    $iter = new \RecursiveIteratorIterator($this->tree);

    return array_keys(iterator_to_array($iter));
  }

  public function mtime($key)
  {
    return time();
  }

  public function checksum($key)
  {
    $file = $this->read($key);
    return md5($file);
  }

  public function delete($key)
  {
    throw new \RuntimeException("This adapter is read-only.");
  }

  public function rename($key, $new)
  {
    throw new \RuntimeException("This adapter is read-only.");
  }

  public function supportsMetadata()
  {
    return false;
  }
}