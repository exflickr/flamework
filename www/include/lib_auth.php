<?php
	#
	# This library handles administrative authentication for the application,
	# which is separated from user-authentication.
	#
	# By default, we simply treat all users as admins when the environment is
	# set to dev, or calls are being made from the command line.
	#
	# This is an apporpriate place to plug in something like GodAuth
	# to handle actual role-based authentication.
	# https://github.com/exflickr/GodAuth/
 	#

	auth_init();

	########################################################################

	function auth_init(){

		$GLOBALS['cfg']['auth_roles'] = array();

		if ($GLOBALS['cfg']['environment'] == 'dev'){

			$GLOBALS['cfg']['auth_roles']['staff'] = 1;
		}

		if ($GLOBALS['this_is_shell']){

			$GLOBALS['cfg']['auth_roles']['staff'] = 1;
		}

	}

	########################################################################

	function auth_has_role($role){

		return !!$GLOBALS['cfg']['auth_roles'][$role];
	}

	########################################################################
