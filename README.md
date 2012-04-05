# Gittern
Making Git like music for PHP's ears.

Version 0.8-dev

## What is Gittern?
Gittern is a PHP library for reading from and writing to Git repositories. It doesn't depend on the ```git``` binary, it directly acccesses the repo files.

Gittern provides several interfaces for interacting with your Git repos. Firstly, there's a low level interface, where you manually create blobs, trees and commits. This can however become cumbersome very quickly. As such, there's also two different Gaufrette adapters included. 

The first one, ```GitternTreeishReadOnlyAdapter``` is pretty much what it sounds like. It's an adapter to which you supply a treeish, and from which you may read the files associated with the treeish.

The second one, ```GitternIndexAdapter``` allows you to read from and write to the Git index. The Git index is the staging area in which you stage new changes for a commit. Then, when you're ready, creating a commit from the index is quite simple. Just get the tree from the index, and use it to create your commit.

In addition to all of this functionality, Gittern is created with extensibility in mind. How Gittern reads data from the repository on disk is cleanly separated using the adapter pattern, so that if you have the need you could easily implement e.g. an adapter capable of maintaining multiple indexes, or Git object caching in something like MongoDB or Redis (in fact, if you don't care about accessing the repo from the git binary, you could just use this as a fast, distributed backend).

## How do I install Gittern?
Use Composer.

## How do I use Gittern?
To be written.

### Part the first, the GitternTreeishReadOnlyAdapter

### Part the second, the GitternIndexAdapter

### Part the third, the low level interface
The low level interface is where the magic happens. This is also where you'll absolutely need to be familiar with the git model of blobs, trees, commits, and how the fit together. Seriously. If you don't e.g. know what a tree is (in the context of git, of course), probably won't understand how to use this, and theoretically you might be able to break your repos when using the low level interface. Proceed with caution.

If you want to learn how git works, there's plenty of resources. I'd suggest the following, in order:

* [Think like (a) Git](http://think-like-a-git.net/)
* The Internals and Plumbing chapters in the [Git Community Book](http://book.git-scm.com/index.html)

## Kinda-sorta bugs
* The index flags field (see http://opensource.apple.com/source/Git/Git-26/src/git-htmldocs/technical/index-format.txt) contains an assume-valid flag that's not represented
* The file name length of the flags field should only be written in full if it's less than 0xFFF.
* The stage flag isn't desiccated properly.
* Indexes in conflict (i.e. with stage > 0, see http://opensource.apple.com/source/Git/Git-26/src/git-htmldocs/technical/index-format.txt) should be handled somehow, even if just by an exception.

## Planned features
There are several planned features, which didn't make it in to version 0.8. 

* Subclassing Repository with a git binary dependent subclass to allow some advanced commands,
* Packfile creation (currently packfiles can only be read)
* Support for resolving lightweight tags
* Support for reading annotated tags