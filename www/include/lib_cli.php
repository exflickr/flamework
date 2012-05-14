<?php

	# This isn't finished yet (20120513/straup)

	#################################################################

	# $spec = array(
	# 	"i" => array("name" => "input", "required" => 1, "help" => "yer input"),
	# 	"o" => array("name" => "output", "required" => 1, "help" => "yer output"),
	# 	"u" => array("name" => "username", "required" => 1, "help" => "a username"),
	# 	"y" => array("name" => "year", "required" => 1, "help" => "what time is it?"),
	# );

	function cli_getopts($spec, $more=array()){

		$defaults = array(
			'include_help' => 1
		);

		$more = array_merge($defaults, $more);

		if ($more['include_help']){

			$spec['h'] = array(
				"name" => "help",
				"help" => "print this message",
				"boolean" => 1,
			);
		}

		$short_opts = array();
		$long_opts = array();
		$names = array();

		foreach ($spec as $key => $details){

			$extras = "::";
			$name = $key;

			if ($details['required']){
				$extras = ":";
			}

			else if (isset($details['boolean'])){
				$extras = "";
			}

			else {}

			$short_opts[] = "{$key}{$extras}";

			if (isset($details['name'])){
				$name = $details['name'];
			}

			$names[$name] = $key;
			$long_opts[] = "{$name}{$extras}";
		}

		$short_opts = implode("", $short_opts);

		$opts = getopt($short_opts, $long_opts);

		$help = ((isset($opts['h'])) || (isset($opts['help']))) ? 1 : 0;

		if (($help) && ($more['include_help'])){
			cli_help($spec);
			return;
		}

		foreach ($opts as $k => $stuff){

			if (isset($spec[$k])){

				if ($name = $spec[$k]['name']){
					$opts[$name] = $stuff;
				}
			}

			else {
				$opts[$names[$k]] = $stuff;
			}
		}

		foreach ($spec as $key => $details){

			if (! $details['required']){
				continue;
			}

			if (! isset($opts[$key])){
				cli_help($spec, "Required parameter ({$spec[$key]['name']}) missing");
			}		
		}

		return $opts;
	}

	#################################################################

	function cli_help($spec, $msg=''){

		if ($msg){
			echo "{$msg}\n\n";
		}

		echo "Usage:\n\n";
		echo "   $>php -q {$GLOBALS['argv'][0]}";

		if (count($spec)){
			echo " --options\n\n";
			echo "Valid options are:\n";
		}

		echo "\n";

		foreach ($spec as $key => $details){

			echo "-{$key} ";

			if (isset($details['name'])){
				echo "--{$details['name']} ";
			}

			if ($details['required']){
				echo "(required)";
			}

			if (isset($details['help']) && ($details['help'])){
				echo "\n";

				# chunk_split is the quick and dirty way of doing
				# this; it does not account for splitting on words
				# or multibyte strings (20120514/straup)

				$chunks = chunk_split($details['help'], 80);
				$chunks = rtrim($chunks);

				foreach (explode("\n", $chunks) as $chunk){
					$chunk = trim($chunk);
					echo "   {$chunk}\n";
				}
			}

			echo "\n";
		}

		echo "\n";
		exit();
	}

	#################################################################
?>
