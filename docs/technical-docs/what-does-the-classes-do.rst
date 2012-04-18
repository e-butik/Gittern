===========================================
What does the different kind of classes do?
===========================================

Entities
--------

The Git object model has quite a few entities, and Gittern divides them into two categories. GitObjects and "other". Any kind of entity which is persisted in the Git object store is a GitObject. That means Commits, trees (and their different node types), blobs and annotated tags (not yet supported). The index and it's entries are the current "other" entities.

All of these objects represent a concept present in the Git object model.

Proxy
-----

When you fetch a Git entity (unless it's a blob), it's probably going to have relations to other entities. To make  it easier to work with the objects, Gittern creates proxy objects for these relations. When a method requires data that the proxy object doesn't already have (pretty much anything but the SHA), the proxy lazily loads the data from the repository.

Proxies are decorators of the class they're proxying, and thus will pass a type check.

Hydrator
--------

In a normal git repository, the files are stored according to a certain file specification. The role of the hydrator is to a RawObject (which is basically just the file data and it's sha), and create an entity from it.

Desiccator
----------

A de-hydrator. Where a hydrator takes a RawObject and creates an entity from it, the desiccator takes and entity and creates a RawObject from it.

Transport
---------

In order for Gittern to be modular, the Repository class doesn't actually know how to read and write your RawObjects et c. from/to the disk. Maybe you don't even care that much about keeping compatibility with the git binary, and want to store your objects somewhere else. Maybe you want to cache them in e.g. Redis.

For this reason there's the Transport. The Transport knows all about how to get your objects from the disk, how to resolve references, et c.

Adapters
--------

Due to it's fine representation of the Git object model, Gittern is a breeze to work with. However, sometimes you don't actually care about the Git object model. Sometimes you just want to treat it like a filesystem. Lucky for us the fine folks at KnpLabs have created `Gaufrette <https://github.com/KnpLabs/Gaufrette>`_, a file system abstraction layer. Gittern has two Gaufrette adapters, allowing you to treat a git repository like any other file system.