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

Installation - As a base for a new project
------------------------------------------

* Copy everything to a web server running Apache with <code>mod_php</code> and <code>php5-mcrypt</code>.
* Enable <code>AllowOverrides all</code> for the root.
* Copy <code>include/config.php.example</code> to <code>include/config.php</code> and edit it.
* Ensure that the <code>templates_c</code> directory can be written to by your webserver.
* Load the schema into mysql: <code>mysql -uwww -Dflamework -p < schema/db_main.schema</code>

That might be it.

Installation - As an external library
-------------------------------------

Another way to use flamework is to clone it into a subfolder and build
your project underneath it. This has the added advantage that you can
just pull down updates as they happen, without having to rebase/merge
your project on top of it. The library isn't set up to easily do this,
automagically <b>yet</b>. Once it is, you will be able to point to your
own config files, libraries, templates, etc. while still using all of
the flamework guts.

In the meantime here's one way to use flamework in a separate project.
It's not actually that complicated, just a bit boring. (Aaron promises
to write a shell script to automate as much of this as possible Real
Soon Now (tm).)

To start, imagine you've got two folders:

1) The copy of exflickr/flamework you've cloned from GitHub. For example:

	/var/flamework

2) The project you're working on. For example:

	/var/your-project/

Your project might contain the following sub-folders, depending on how you set
things up:

	/var/your-project/www
	/var/your-project/www/include

In this example, we'll assume that they are. The first sub-folder is
your application's directory root (e.g. the thing the interweb sees). The second is
where you store your application's shared libraries.

The first thing you should do is copy the default <code>.htaccess</code> files for
the same folders (application root and shared libraries) from flamework:

	cp /var/flamework/.htaccess /var/your-project/www/
	cp /var/flamework/include/.htaccess /var/your-project/www/include/

(Don't forget that you'll still need to set up your templates and templates cache
directories for Smarty. You may want to copy over the <code>templates</code> and
<code>templates_c</code> from the flamework trunk but those details are still left
up to you, for the time being.)

Next, edit <code>/var/your-project/www/.htaccess</code> to include a php <code>include_path</code>
config. Basically, all you're doing is telling PHP to look for libraries and other shared
code in your application *first* and then to fall back on flamework. For example:

	php_value include_path "/var/your-project/www/include:/var/flamework/include:."

The second <code>.htaccess</code> file you've copied over from flamework shouldn't need
to be changed. The default configuration prevents anyone from reading its
contents.

Now copy the default flamework config file in to your application's include
directory:

	cp /var/flamework/include/config.php /var/your-project/www/include/

Update the (newer) file accordingly with your application's configuration and be
sure to add the following line. This does what it sounds like:

	$GLOBALS['cfg']['flamework_skip_init_config'] = 1;

You're almost done. No, really. You just need to create an init.php file for your
application. For example:

	/var/your-project/www/include/init.php

Here's the minimum amount of stuff you'll need to add in order for your application
to use flamework:

	define('URPROJECT_FLAMEWORK_DIR', '/var/flamework');
	define('URPROJECT_ROOT_DIR', dirname(dirname(__FILE__)));

	include(URPROJECT_FLAMEWORK_DIR . '/include/config.php');
	include(URPROJECT_ROOT_DIR . "/include/config.php");

	include_once(URPROJECT_FLAMEWORK_DIR . '/include/init.php');

Here's what going on:

1) You are defining constants to indicate where your application and flamework
are located.

2) Loading first the flamework <code>config.php</code> file followed by your application's
<code>config.php</code>. Remember, the order that these files are loaded is
important. You want to first load the default flamework configs (many of which
you won't need to change or worry about) but then overwrites you ones you do care
about, not the other way around.

3) Finally loading flamework's <code>init.php</code> file!

Depending on your how your application is set up this may be all you need to do
in order to use flamework. Once you've finished you can start using it all in your
code, like this:

	<?php
		include("include/init.php");
		# your code goes here
	?>

Profit!

Style guide
-----------

The coding style is idiosyncratic and will stay that way. There are no
spaces between closing parentheses and opening braces. We indent with
tabs. All functions in a library must start with the library name,
globals too. We don't (often) use constants. An underscore at the
start of a function means it's library-private, same with
globals. Function names are all lowercase, split with underscores. We
don't use objects.

We turn on E_ALL & E_STRICT, but ignore most E_NOTICEs because they're
dumb. We do quote all hash keys, but we don't care about undefined
keys or variables - isset() is vary rarely used.

If you submit patches that don't conform to the weird standards,
they'll get reformatted. It's not you, it's me.
