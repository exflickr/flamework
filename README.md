Flamework
=========

Flamework is the semi-fictional framework that powers Flickr.com. It's
less of an actual framework and more of a design philosophy. None of
the code in this project is actually taken from Flickr, but is rather
a reconstruction of the way we built things there and the way we
continue to build things now.

<b>This library is a work in progress</b>. While it basically works,
it's lacking lots of the bits it really needs. As we pull these parts
from other projects (and I've built most parts 10 times over by now),
it'll start to take better shape. If you have stuff you want to add,
fork, commit and file a pull-request.

## Installation - As a base for a new project

* Copy everything in <code>www</code> to a web server running Apache with <code>mod_php</code> and <code>php5-mcrypt</code>.
* Enable <code>AllowOverrides all</code> for the root.
* Copy <code>include/config.php.example</code> to <code>include/config.php</code> and edit it.
* Ensure that the <code>templates_c</code> directory can be written to by your webserver.
* Load the schema into mysql: <code>mysql -uwww -Dflamework -p < schema/db_main.schema</code>

That might be it.

If you'd like to use Flamework as an external library, <a href="/exflickr/flamework/blob/master/docs/install_external.md">read this</a>.

To install Flamework on AppFog.com, <a href="/exflickr/flamework/blob/master/docs/install_appfog.md">read this</a>.

## Installation - The longer, slightly hand holding, version

Get the [code from GitHub](https://github.com/straup/flamework).

Decide on whether you'll host this on a sub-domain (something alone the lines of `flame.example.com`) or on a subdirectory 
(maybe something like `www.example.com/flame`).

The rest of this section will assume the following:

* That you'll be hosting on a sub-domain called *flame* on a domain called *example.com*, or, to put it another way, 
`flame.example.com`. Just mentally substitute your domain and sub-domain when reading, and physically substitute your 
domain and sub-domain during the installation process. Unless you actually own the example.com.
* That you want the URL for Flamework to be `flame.example.com` and not `flame.example.com/www`
* That `<root>` is the path on your webserver where your web server has been configured to find the sub-domain.
* That you have shell access (probably via SSH) to your web server.

Now ... upload the code, plus all sub-directories to your web-server; don't forget the (hidden) `.htaccess` file in the 
root of the code's distribution.

Copy `<root>/www/include/config.php.example` to `<root>/www/include/config.php` and edit this new file.

Change the site name to reflect your sub-domain name and whether you're running in a production or development environment

	$GLOBALS['cfg']['site_name'] = 'flame';
	$GLOBALS['cfg']['environment'] = 'prod';

Set up your database name, database user and database password. Copy and paste these into ...

	$GLOBALS['cfg']['db_main'] = array(
		'host' => 'localhost',
		'name' => 'my-database-name',
		'user' => 'my-database-user',
		'pass' => 'my-database-users-password',
		'auto_connect' => 0,
	);

Setup your encryption secrets secrets. SSH to your host and run `php <root>/bin/generate_secret.php`, 3 times. Copy and paste each secret into 

	$GLOBALS['cfg']['crypto_cookie_secret'] = 'first-secret-here';
	$GLOBALS['cfg']['crypto_password_secret'] = 'third-secret-here';
	$GLOBALS['cfg']['crypto_crumb_secret'] = 'second-secret-here';

(If you don't have shell access to your web-server, you can run this command from the shell on a local machine)

Create the database tables. Load `<root>/schema/db_main.schema` and `<root>/schema/db_users.schema` into the database. 
You can do this either via phpMyAdmin and the import option or via `mysql` on the shell's command line.

Browse to http://flame.example.com

If you get errors in your Apache error log such as ...

	www/.htaccess: Invalid command 'php_value', perhaps misspelled or defined by a module not included in the server configuration

... then your host is probably running PHP as a CGI and not as a module so you'll want to comment out any line in 
`<root>/www/.htaccess` that starts with `php_value` or `php_flag` and put these values into a new file, 
`<root>/www/php.ini`, without the leading `php_value` or `php_flag`.

Click on *Sign In* and setup your user account.

That's it. Or should be. If I've forgotten something please let me know or submit a pull request.


## Statement(s) of Bias

*"Working on the crumbly edge of future-proofing." -- [Heather Champ](http://www.hchamp.com/)*

If you've never watched [Cal Henderson's](http://www.iamcal.com) "Why I Hate Django" presentation now is probably 
as good a time as any. It will help you understand a lot about why things were done they were at Flickr and why 
those of us who've left prefer to keep doing them that way:

+ [http://www.youtube.com/watch?v=i6Fr65PFqfk](http://www.youtube.com/watch?v=i6Fr65PFqfk "Why I Hate Django")

Flamework is not really a framework, at least not by most people's standards. All software development is 
basically pain management and Flamework assumes that the most important thing is *the speed with which the code 
running an application can be re-arranged, in order to adapt to circumstances*, even if it's at the cost of 
"doing things twice" or "repeating ourselves".

(Also, in fairness to the Django kids a lot has changed and gotten better since Cal's talk way back when.)

**Flamework is basically two things:**

1. A set of common libraries and functions.
2. A series of social conventions for how code is arranged.

**Flamework also takes the following for granted:**

* It uses [Smarty](http://www.smarty.net "Smarty") for templating.
* It uses global variables. Not many of them but it also doesn't make a fuss about the idea of using them.
* It does not use objects or "protected" variables.
* It breaks it own rules occasionally and uses objects but only rarely and generally when they are defined 
by third-party libraries (like [Smarty](http://www.smarty.net/)).
* That ["normalized data is for sissies"](http://kottke.org/04/10/normalized-data).

**For all intents and purposes, Flamework *is* a model-view-controller (MVC) system:**

* There are shared libraries (the model)
* There are PHP files (the controller)
* There are templates (the view)

Here is a simple bare-bones example of how it all fits together:

	# /include/lib_example.php

	<?php
		function example_foo(&$user){
			$max = ($user['id']) ? $user['id'] : 1000;
			return range(0, rand(0, $max));
		}
	?>

	# /example.php
	#
	# note how we're importing lib_example.php (above)
	# and squirting everything out to page_example.txt (below)

	<?php
		include("include/init.php");
		loadlib("example");

		$foo = example_foo($GLOBALS['cfg']['user']);

		$GLOBALS['smarty']->assign_by_ref("foo", $foo);
		$GLOBALS['smarty']->display("page_example.txt");
		exit();
	?>

	# /templates/page_example.txt

	{include file="inc_head.txt" page_title="example page title"}

	<p>{if $cfg.user.id}Hello, {$cfg.user.username|escape}!{else}Hello, stranger!{/if}</p>
	<p>foo is: {','|@implode:$foo|escape}</p>

	{include file="inc_foot.txt"}

The only "rules" here are:

1. Making sure you load `include/init.php`
2. The part where `init.php` handles authentication checking and assigns logged in users to the 
global `$cfg` variable (it also creates and assigns a global `$smarty` object)
3. The naming conventions for shared libraries, specifically: `lib_SOMETHING.php` which is 
imported as `loadlib("SOMETHING")`.
4. Functions defined in libraries are essentially "namespaced".

Page template names and all that other stuff is, ultimately, your business.

Global Variables
--

Flamework uses and assigns global PHP variables on the grounds that it's really just not that big a 
deal. A non-exhaustive list of global variables that Flameworks assigns is:

* `$GLOBALS['cfg']` -- A great big hash that contains all the various site configs and runtime user authentication info.

* `$GLOBALS['smarty']` -- A [Smarty](http://www.smarty.net/) templating object.

* `$GLOBALS['timings']` -- A hash used to store site performance metrics.

Some libraries use their own globals internally, usually prefixed with `LIBRARYNAME_` or `_LIBRARYNAME_`.
Some libraries define globals with other random names, which is something we should probably fix.


The database model
--

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
	   
## Other documentation

* <a href="/exflickr/flamework/blob/master/docs/troubleshooting.md">Troubleshooting</a>
* <a href="/exflickr/flamework/blob/master/docs/style_guide.md">Style guide</a>


## Libraries & Tools

There are several drop-in external libraries for common tasks:

* <a href="https://github.com/straup/flamework-geo">flamework-geo</a> - Geo libraries and helper functions
* <a href="https://github.com/straup/flamework-aws">flamework-aws</a> - S3 upload library
* <a href="https://github.com/straup/flamework-api">flamework-api</a> - Add an external API
* <a href="https://github.com/straup/flamework-invitecodes">flamework-invitecodes</a> - Generate invite codes
* <a href="https://github.com/iamcal/flamework-useragent">flamework-useragent</a> - Parse useragent strings
* <a href="https://github.com/iamcal/flamework-JSON">flamework-JSON</a> - Parse invalid JSON

<a href="https://github.com/straup/">Aaron</a> has created several starter configurations for using delegated auth:

* <a href="https://github.com/straup/flamework-flickrapp">flamework-flickrapp</a> - Authenticate using Flickr
* <a href="https://github.com/straup/flamework-twitterapp">flamework-twitterapp</a> -  Authenticate using Twitter
* <a href="https://github.com/straup/flamework-foursquareapp">flamework-foursquareapp</a> - Authenticate using foursquare
* <a href="https://github.com/straup/flamework-osmapp">flamework-osmapp</a> - Authenticate using OpenStreetMap

And some random odds and ends:

* <a href="https://github.com/straup/flamework">flamework-tools</a> - Automation scripts
