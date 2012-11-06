# Flamework Design Philosophy - Statement(s) of Bias

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
