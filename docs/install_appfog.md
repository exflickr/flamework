# Installation - AppFog.com

Installation on AppFog.com is pretty simple and offers the advantage of being able to easily scale your app and move between infrastructures and data centers. 

1) To begin, simply log in to your AppFog account and create a PHP App. You can select any available infrastructure. Wait for your app to launch and then add a MySQL service and bind to it via the control panel. This is also fairly trivial to do from the command line.

2) Once your app is running be sure you have the following installed:

	$ sudo gem install af
	$ sudo gem install caldecott

3) In your www/include/config.php file, add the following:

	$services = getenv("VCAP_SERVICES");
	$services_json = json_decode($services,true);
	$mysql_config = $services_json["mysql-5.1"][0]["credentials"];

and replace this 

	$GLOBALS['cfg']['db_main'] = array(
		'host'	=> 'localhost',
		'user'	=> 'root',
		'pass'	=> 'root',
		'name'	=> 'flamework',
		'auto_connect' => 0,
		);

with this 

	$GLOBALS['cfg']['db_main'] = array(
		'host'	=> $mysql_config["hostname"],
		'user'	=> $mysql_config["user"],
		'pass'	=> $mysql_config["password"],
		'name'	=> $mysql_config["name"],
		'auto_connect' => 0,
		);
		
4) Push the www directory to your new app by doing the following:

	$ cd /yourflameworkfolder/www
	$ af login
	$ af push myApp
	
5) Flamework is now installed on your AppFog app. You Should be able to see its basic home-page by going to your app's URL. To add the DB schema do the following.

	$ af tunnel myApp

This will open a MySQL connection to your AppFog database. You can then use standard MySQL commands to copy in the schema files found in /yourflameworkfolder/schema. Once this is done, you should be able to create user accounts, etc on your AppFog/Flaework app.

Enjoy.