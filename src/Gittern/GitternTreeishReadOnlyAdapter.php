<?php

namespace Gittern;

use Gaufrette\Adapter\Base as BaseAdapter;

/**
* @author Magnus Nordlander
**/
class GitternTreeishReadOnlyAdapter extends BaseAdapter
{
  protected $repo;
  protected $tree;

  /**
   * @author Magnus Nordlander
   **/
  public function __construct(Repository $repo, $treeish)
  {
    $this->repo = $repo;

    $object = $repo->getObject($treeish);
    if ($object instanceof GitObject\Commit)
    {
      $object = $object->getTree();
    }

    if ($object instanceof GitObject\Tree)
    {
      $this->tree = $object;
    }
    else
    {
      // throw
    }
  }

  /**
   * Reads the content of the file
   *
   * @param  string $key
   *
   * @return string
   */
  public function read($key)
  {
    $components = explode('/', $key);

    $object = $this->tree;

    foreach ($components as $component) 
    {
      if ($object instanceof GitObject\Tree)
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

    if ($object instanceof GitObject\Blob)
    {
      return $object->getContents();
    }

    throw new \RuntimeException(sprintf('Could not read the \'%s\' file.', $key));
  }

  /**
   * Writes the given content into the file
   *
   * @param  string $key
   * @param  string $content
   *
   * @return integer The number of bytes that were written into the file
   *
   * @throws RuntimeException on failure
   */
  public function write($key, $content, array $metadata = null)
  {
    throw new \RuntimeException("This adapter is read-only.");
  }


  /**
   * Indicates whether the file exists
   *
   * @param  string $key
   *
   * @return boolean
   */
  public function exists($key)
  {
    return array_search($key, $this->keys()) !== false;
  }

  /**
   * Returns an array of all keys matching the specified pattern
   *
   * @return array
   */
  public function keys()
  {
    $iter = new \RecursiveIteratorIterator($this->tree);
    
    return array_keys(iterator_to_array($iter));
  }

  /**
   * Returns the last modified time
   *
   * @param  string $key
   *
   * @return integer An UNIX like timestamp
   */
  public function mtime($key)
  {
    return time();
  }

  /**
   * Returns the checksum of the file
   *
   * @param  string $key
   *
   * @return string
   */
  public function checksum($key)
  {
    $file = $this->read($key);
    return md5($file);
  }

  /**
   * Deletes the file
   *
   * @param  string $key
   *
   * @throws RuntimeException on failure
   */
  public function delete($key)
  {
    throw new \RuntimeException("This adapter is read-only.");
  }

  /**
   * Renames a file
   *
   * @param string $key
   * @param string $new
   *
   * @throws RuntimeException on failure
   */
  public function rename($key, $new)
  {
    throw new \RuntimeException("This adapter is read-only.");
  }

  public function supportsMetadata()
  {
    return false;
  }
}