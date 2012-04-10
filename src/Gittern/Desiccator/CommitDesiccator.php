<?php

namespace Gittern\Desiccator;

use Gittern\Entity\GitObject\Commit;
use Gittern\Entity\GitObject\Tree;
use Gittern\Entity\GitObject\User;

use Gittern\Transport\RawObject;

use Iodophor\Io\StringWriter;
use Iodophor\Io\Writer;

/**
* @author Magnus Nordlander
**/
class CommitDesiccator
{
  protected function writeTree(Tree $tree, Writer $writer)
  {
    $sha = $tree->getSha();

    if (strlen($sha) != 40)
    {
      throw new \RuntimeException("Tree referred to by commit is not persisted yet.");
    }

    $writer->writeString8("tree ");
    $writer->writeString8($sha);
    $writer->writeString8("\n");
  }

  protected function writeParent(Commit $commit, Writer $writer)
  {
    $sha = $commit->getSha();

    if (strlen($sha) != 40)
    {
      throw new \RuntimeException("Parent referred to by commit is not persisted yet.");
    }

    $writer->writeString8("parent ");
    $writer->writeString8($sha);
    $writer->writeString8("\n");
  }

  protected function writeAuthor(User $author, \DateTime $authorTime, Writer $writer)
  {
    $writer->writeString8("author ");
    $this->writeUser($author, $authorTime, $writer);
    $writer->writeString8("\n");
  }

  protected function writeCommitter(User $committer, \DateTime $committerTime, Writer $writer)
  {
    $writer->writeString8("committer ");
    $this->writeUser($committer, $committerTime, $writer);
    $writer->writeString8("\n");
  }

  protected function writeUser(User $author, \DateTime $datetime, Writer $writer)
  {
    $writer->writeString8($author->getName());
    $writer->writeString8(" <");
    $writer->writeString8($author->getEmail());
    $writer->writeString8("> ");
    $writer->writeString8($datetime->format('U O'));
  }

  /**
   * @author Magnus Nordlander
   **/
  public function desiccate(Commit $commit)
  {
    $writer = new StringWriter();

    $this->writeTree($commit->getTree(), $writer);
    foreach ($commit->getParents() as $parent) 
    {
      $this->writeParent($parent, $writer);
    }

    $this->writeAuthor($commit->getAuthor(), $commit->getAuthorTime(), $writer);
    $this->writeCommitter($commit->getCommitter(), $commit->getCommitTime(), $writer);
    $writer->writeString8("\n");
    $writer->writeString8($commit->getMessage());

    return new RawObject('commit', $writer->toString());
  }
}