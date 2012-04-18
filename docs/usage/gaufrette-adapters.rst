=========================================
Using Gittern with the Gaufrette Adapters
=========================================

GitternCommitishReadOnlyAdapter
-------------------------------

Code speaks louder than words:

.. code-block:: php

    <?php

    use Gittern\Repository,
        Gittern\Transport\NativeTransport,
        Gittern\Configurator,
        Gittern\Gaufrette\GitternCommitishReadOnlyAdapter;

    use Gaufrette\Filesystem;

    $repo = new Repository;
    $repo->setTransport(new NativeTransport($repo_path));

    $configurator = new Configurator;
    $configurator->defaultConfigure($repo);

    $filesystem = new Filesystem(new GitternCommitishReadOnlyAdapter($repo, "master"));

After this, you can use the filesystem like any other Gaufrette Filesystem. Just bear in mind that it's read-only, and will throw exceptions if you try to modify it.

GitternIndexAdapter
-------------------

Again, code speaks louder than words:

.. code-block:: php

    <?php

    use Gittern\Repository,
        Gittern\Transport\NativeTransport,
        Gittern\Configurator,
        Gittern\Gaufrette\GitternIndexAdapter;

    use Gaufrette\Filesystem;

    $repo = new Repository;
    $repo->setTransport(new NativeTransport($repo_path));

    $configurator = new Configurator;
    $configurator->defaultConfigure($repo);

    $filesystem = new Filesystem(new GitternIndexAdapter($repo));


After this, you can use the filesystem like any other Gaufrette Filesystem.

Committing
++++++++++

The Git Index contains everything necessary to create a tree. Once you have a tree, creating a commit is a fairly straight-forward deal, but additional convenience is under consideration.

.. code-block:: php

    <?php

    use Gittern\Entity\GitObject\Commit,
        Gittern\Entity\GitObject\User;

    use DateTime;

    $parent = $repo->getObject('master');

    $tree = $repo->getIndex()->createTree();
    $commit = new Commit;
    $commit->setTree($tree);
    $commit->addParent($parent);
    $commit->setMessage("Added another file");
    $commit->setAuthor(new User("Tessie Testson", "tessie.testson@example.com"));
    $commit->setCommitter(new User("Tessie Testson", "tessie.testson@example.com"));
    $commit->setAuthorTime(new DateTime);
    $commit->setCommitTime(new DateTime);

    $repo->desiccateGitObject($commit);
    $repo->setBranch('master', $commit);

    $repo->flush();
