Flamework
=========

[![Build Status](https://secure.travis-ci.org/exflickr/flamework.png)](http://travis-ci.org/exflickr/flamework)

Flamework is a PHP web-application framework, born out of the processes
and house-style developed at Flickr.com.

<b>This library is a work in progress</b>. It is immediately usable,
but lacks many of the components needed to create a fully featured modern 
application. These pieces are being added as the individual contributors
find a need to in their personal projects. If you've written a missing
piece of the puzzle, please send a pull-request.


## Installation - As a base for a new project

* Copy everything in <code>www</code> to a web server running Apache with <code>mod_php</code> and <code>php5-mcrypt</code>.
* Enable <code>AllowOverrides all</code> for the root.
* Copy <code>include/config.php.example</code> to <code>include/config.php</code> and edit it.
* Ensure that the <code>templates_c</code> directory can be written to by your webserver.
* Load the schema into mysql: <code>mysql -uwww -Dflamework -p < schema/db_main.schema</code>

That's it.

For a longer version, read the <a href="/exflickr/flamework/blob/master/docs/install_base.md">intsallation guide</a>.

If you'd like to use Flamework as an external library, <a href="/exflickr/flamework/blob/master/docs/install_external.md">read this</a>.

To install Flamework on AppFog.com, <a href="/exflickr/flamework/blob/master/docs/install_appfog.md">read this</a>.


##Global Variables

Flamework uses and assigns global PHP variables on the grounds that it's really just not that big a 
deal. A non-exhaustive list of global variables that Flameworks assigns is:

* `$GLOBALS['cfg']` -- A great big hash that contains all the various site configs and runtime user authentication info.

* `$GLOBALS['smarty']` -- A [Smarty](http://www.smarty.net/) templating object.

* `$GLOBALS['timings']` & `$GLOBALS['timing_keys']` -- Hashs used to store site performance metrics.

Some libraries use their own globals internally, usually prefixed with `LIBRARYNAME_` or `_LIBRARYNAME_`.


## Other documentation

* <a href="/exflickr/flamework/blob/master/docs/troubleshooting.md">Troubleshooting</a>
* <a href="/exflickr/flamework/blob/master/docs/philosophy.md">Design Philosophy</a>
* <a herf="/exflickr/flamework/blob/master/docs/database_model.md">Database Model</a>
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


## Tests

If you have perl's <a href="http://search.cpan.org/dist/Test-Harness/">Test::Harness</a> installed (you almost certainly do), 
you can run the tests using:

    prove --exec 'php' tests/*.t

Test coverage is in great need of improving.
 
