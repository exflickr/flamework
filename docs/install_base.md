# Installation - As a base for a new project

Get the [code from GitHub](https://github.com/exflickr/flamework).

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

That's it.
