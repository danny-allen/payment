<?php

namespace DannyAllen\Payment\Helpers;

use Exception;

class Validate {

	/**
	 * string
	 *
	 * Checks if the string value exists and is an actual string.
	 * 
	 * @param  string 	$string  	The string to check for.
	 * @param  string 	$context 	The contect to display an error message in.
	 */
	public static function string($string = null, $context = "this") {

		//check the gateway is a string
		if(!isset($string) || !is_string($string)){

			//if it doesnt exist, throw an error
			throw new Exception($context.' needs to be a string.');
		}

		//return value
		return $string;
	}

	public static function int($int = null, $context = "this") {

		//make sure context is a string
		Self::string($context, 'context');

		//check the gateway is a string
		if(!isset($int) || !is_int($int)){

			//if it doesnt exist, throw an error
			throw new Exception($context.' needs to be an int.');
		}

		//return value
		return $int;
	}
}