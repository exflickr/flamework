<?php

	# Note: this is not stock flamework code (20130618/straup)

	########################################################################

	function auth_has_role($role, $who=0){

		$who = ($who) ? $who : $GLOBALS['cfg']['user']['id'];

		if ((! $who) && ($role == "staff") && (features_is_enabled("auth_roles_autopromote_staff"))){
			
			if (($GLOBALS['cfg']['environment'] == 'dev') && (features_is_enabled("auth_roles_autopromote_staff_dev"))){
				return 1;
			}

			if (($GLOBALS['this_is_shell']) && (features_is_enabled("auth_roles_autopromote_staff_shell"))){
				return 1;
			}
		}

		if (! $who){
			return 0;
		}

		if (! isset($GLOBALS['cfg']['auth_users'][$who])){
			return 0;
		}

		$details = $GLOBALS['cfg']['auth_users'][$who];
		$roles = $details['roles'];

		return (in_array($role, $roles)) ? 1 : 0;
	}

	########################################################################

	function auth_has_role_any($roles, $who=0){

		if (! is_array($roles)){
			return 0;
		}

		foreach ($roles as $role){

			if (auth_has_role($role, $who)){
				return 1;
			}
		}

		return 0;
	}

	########################################################################

	function auth_has_role_all($roles, $who=0){

		if (! is_array($roles)){
			return 0;
		}

		foreach ($roles as $role){

			if (! auth_has_role($role, $who)){
				return 0;
			}
		}

		return 1;
	}

	########################################################################

	# the end
