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
* If some of your rewrite rules like <code>^account/password$</code> aren't working, try <code>Options -MultiViews</code>.
* Copy <code>include/config.php.example</code> to <code>include/config.php</code> and edit it.
* Ensure that the <code>templates_c</code> directory can be written to by your webserver.
* Load the schema into mysql: <code>mysql -uwww -Dflamework -p < schema/db_main.schema</code>

That's it.

For a longer version, read the <a href="/docs/install_base.md">installation guide</a>.

If you'd like to use Flamework as an external library, <a href="/docs/install_external.md">read this</a>.

To install Flamework on AppFog.com, <a href="/docs/install_appfog.md">read this</a>.


## Global Variables

Flamework uses and assigns global PHP variables on the grounds that it's really just not that big a 
deal. A non-exhaustive list of global variables that Flameworks assigns is:

* `$GLOBALS['cfg']` -- A great big hash that contains all the various site configs and runtime user authentication info.

* `$GLOBALS['smarty']` -- A [Smarty](http://www.smarty.net/) templating object.

* `$GLOBALS['timings']` & `$GLOBALS['timing_keys']` -- Hashs used to store site performance metrics.

Some libraries use their own globals internally, usually prefixed with `LIBRARYNAME_` or `_LIBRARYNAME_`.


## Other documentation

* <a href="/docs/troubleshooting.md">Troubleshooting</a>
* <a href="/docs/philosophy.md">Design Philosophy</a>
* <a href="/docs/database_model.md">Database Model</a>
* <a href="/docs/style_guide.md">Style guide</a>


## Libraries & Tools

There are several drop-in external libraries for common tasks:

* <a href="https://github.com/straup/flamework-geo">flamework-geo</a> - Geo libraries and helper functions
* <a href="https://github.com/straup/flamework-aws">flamework-aws</a> - S3 upload library
* <a href="https://github.com/straup/flamework-api">flamework-api</a> - Add an external API
* <a href="https://github.com/straup/flamework-invitecodes">flamework-invitecodes</a> - Generate invite codes
* <a href="https://github.com/iamcal/flamework-useragent">flamework-useragent</a> - Parse useragent strings
* <a href="https://github.com/iamcal/flamework-JSON">flamework-JSON</a> - Parse invalid JSON
* <a href="https://github.com/micahwalter/flamework-sendgrid">flamework-sendgrid</a> - Use the SendGrid SMTP Service

<a href="https://github.com/straup/">Aaron</a> has created several starter configurations for using delegated auth:

* <a href="https://github.com/straup/flamework-flickrapp">flamework-flickrapp</a> - Authenticate using Flickr
* <a href="https://github.com/straup/flamework-twitterapp">flamework-twitterapp</a> -  Authenticate using Twitter
* <a href="https://github.com/straup/flamework-foursquareapp">flamework-foursquareapp</a> - Authenticate using foursquare
* <a href="https://github.com/straup/flamework-osmapp">flamework-osmapp</a> - Authenticate using OpenStreetMap
* <a href="https://github.com/micahwalter/flamework-tumblrapp">flamework-tumblrapp</a> - Authenticate using Tumblr

And some random odds and ends:

* <a href="https://github.com/straup/flamework">flamework-tools</a> - Automation scripts


## Tests

If you have `make` and and recent `perl` installed (you almost certainly do, or if not see [Vagrant](#vagrant) and [Docker](#docker) sections below), you can run the tests using:

    make test

If you also have `xdebug` and `PHP_CodeCoverage` installed, you can generate test coverage information:

    make cover

Test coverage needs some serious improvement.
 

## Vagrant

If you don't want to mess with your local development environment, you can run the tests under Vagrant by doing:

    vagrant up
    vagrant ssh
    cd /vagrant
    make test

## Docker

Similarly, Docker is an option for both local development and test running, but is not suitable for production use (really, REALLY don't use it for prod -- we (intentionally) do not have this configured securely). To build and run:

    docker build -t flamework .
    docker run -ti -p80:8081 -p443:4331 -v ~/dev/flamework:/mnt/flamework --name=flamework --rm flamework

Your local flamework copy should now be listening on ports `8081` and `4331`. Use `docker ps` to verify them. You'll need to edit <code>include/config.php</code> as usual. Since you mounted your local dev flamework directory into the container, any code changes you make should be reflected immediately.

Once the container is running, to run tests you can do:

    docker exec -ti flamework make test

And to tail the error logs:

    docker exec -ti flamework tail -F /var/log/apache2/error.log

When killing the container using either `CTRL+C` or `docker stop flamework`, the container will be removed and all data will be reset next run. This is useful for running tests.