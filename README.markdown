Flamework
=========

Flamework is the semi-fictional framework that powers Flickr.com.
It's less of an actual framework and more of a design philosophy.
None of the code in this project is actually taken from Flickr,
but is rather a reconstruction of the way we built things there and
the way we continue to build things now.


Installation - As a base for a new project
------------------------------------------

The easiest way to use flamework is to just clone it and start hacking directly on the code.

* Copy everything to a web server running Apache with <code>mod_php</code>.
* Enable <code>AllowOverrides all</code> for the root.
* Edit <code>include/config.php</code>.
* That might be it.


Installation - As an external library
-------------------------------------

Another way to use flamework is to clone it into a subfolder and build your project
underneath it. This has the added advantage that you can just pull down updates as
they happen, without having to rebase/merge your project on top of it. The library
isn't set up to easily do this, <b>yet</b>. Once it is, you will be able to point 
to your own config files, libraries, templates, etc. while still using all of the
flamework guts. Aaron is totally working on this.
