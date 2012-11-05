# The Flamework database model

Flamework assumes a federated model with all the various user data spread across a series of databases, 
or "clusters". For each cluster there are a series of corresponding helper functions defined in `lib_db.php`.

**By default Flamework does not require that it be run under a fully-federated
  database system.** It takes advantage of the ability to run in "poor man's
  federated" mode which causes the database libraries to act as though there are
  multiple database clusters when there's only really one. Specifically, all the
  various databases are treated as though they live in the `db_main`
  cluster. The goal is to enable (and ensure) that when a given installation of
  a Flamework project outgrows a simple one or two machine setup that it can easily 
  be migrated to a more robust system with a minimum of fuss.

As of this writing Flamework defines/expects the following clusters:

+ **db_main**

This is the database cluster where user accounts and other lookup-style database tables live.

+ **db_users**

These are the federated tables, sometimes called "shards". This is where the bulk of the data in Dotspotting 
is stored because it can be spread out, in smaller chunks, across a whole bunch of databases rather than a 
single monolithic monster database that becomes a single point of failure and it just generally a nuisance 
to maintain.

+ **db_tickets**

One of the things about storing federated user data is that from time to time you may need to "re-balance" 
your shards, for example moving all of a user's data from shard #5 to shard #23. That means you can no longer 
rely on an individual database to generate auto-incrementing unique IDs because each database shard creates 
those IDs in isolation and if you try to move a dot, for example, with ID `123` to a shard with another dot 
that already has the same ID everything will break and there will be tears.

The way around this is to use "ticketing" servers whose only job is to sit around and assign unique IDs. 
A discussion of ticketing servers is outside the scope of this document but [Kellan wrote a good blog post 
about the subject](http://code.flickr.com/blog/2010/02/08/ticket-servers-distributed-unique-primary-keys-on-the-cheap/) 
if you're interested in learning more. Which is a long way of saying: Flamework uses tickets and they come 
from the `db_tickets` cluster.
