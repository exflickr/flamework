<?php

	#
	# $Id$
	#

	#################################################################

	function geo_utils_prepare_coordinate($coord, $collapse=1){

		$coord = geo_utils_trim_coordinate($coord);

		if ($collapse){
			$coord = geo_utils_collapse_coordinate($coord);
		}

		return $coord;
	}

	#################################################################

	function geo_utils_expand_coordinate($coord, $multiplier=1000000){

		return $coord / $multiplier;
	}

	#################################################################

	function geo_utils_collapse_coordinate($coord, $multiplier=1000000){

		return $coord * $multiplier;
	}

	#################################################################

	function geo_utils_trim_coordinate($coord, $offset=6){

		$fmt = "%0{$offset}f";

		return sprintf($fmt, $coord);
	}

	#################################################################

	function geo_utils_is_valid_latitude($lat){

		if (! is_numeric($lat)){
			return 0;
		}

		$lat = floatval($lat);

		if (($lat < -90.) || ($lat > 90.)){
			return 0;
		}

		return 1;
	}

	#################################################################

	function geo_utils_is_valid_longitude($lon){

		if (! is_numeric($lon)){
			return 0;
		}

		$lon = floatval($lon);

		if (($lon < -180.) || ($lont > 180.)){
			return 0;
		}

		return 1;
	}

	#################################################################
?>
