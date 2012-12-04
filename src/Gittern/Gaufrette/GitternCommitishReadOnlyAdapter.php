<?php

namespace Gittern\Gaufrette;

use Gittern\Repository;
use Gittern\Entity\GitObject\Blob;
use Gittern\Entity\GitObject\Commit;
use Gittern\Entity\GitObject\Tree;

use Gaufrette\Adapter as AdapterInterface;
use Gaufrette\Adapter\ChecksumCalculator;

/**
* @author Magnus Nordlander
**/
class GitternCommitishReadOnlyAdapter implements AdapterInterface, ChecksumCalculator
{
  protected $repo;
  protected $commit;
  protected $tree;

  public function __construct(Repository $repo, $commitish)
  {
    $this->repo = $repo;

    $object = $repo->getObject($commitish);
    if ($object instanceof Commit)
    {
      $this->commit = $object;
      $this->tree = $object->getTree();
    }
    else
    {
      throw new \RuntimeException("Could not resolve commitish to a commit.");
    }
  }

  protected function getGitObjectForKey($key)
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

    if ($object)
    {
      return $object;
    }

    return null;
  }

  public function read($key)
  {
    $object = $this->getGitObjectForKey($key);

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
    return $this->getGitObjectForKey($key) instanceOf Blob;
  }

  public function keys()
  {
    $iter = new \RecursiveIteratorIterator($this->tree);

    return array_keys(iterator_to_array($iter));
  }

  public function mtime($key)
  {
    return $this->commit->getCommitTime()->format('U');
  }

  public function checksum($key)
  {
    $object = $this->getGitObjectForKey($key);

    if ($object instanceof Blob)
    {
      return $object->getSha();
    }

    throw new \RuntimeException(sprintf('Could not read the \'%s\' file.', $key));
  }

  public function delete($key)
  {
    throw new \RuntimeException("This adapter is read-only.");
  }

  public function rename($key, $new)
  {
    throw new \RuntimeException("This adapter is read-only.");
  }

  public function isDirectory($key)
  {
    return $this->getGitObjectForKey($key) instanceof Tree;
  }

  public function supportsMetadata()
  {
    return false;
  }
}