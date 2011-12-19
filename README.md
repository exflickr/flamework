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


## Other documentation

* <a href="/exflickr/flamework/blob/master/docs/troubleshooting.md">Troubleshooting</a>
* <a href="/exflickr/flamework/blob/master/docs/style_guide.md">Style guide</a>
