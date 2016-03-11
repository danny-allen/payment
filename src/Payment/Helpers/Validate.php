<?php

namespace Dao\Payment\Helpers;

use Exception;

class Validate {

	/**
	 * boolean
	 *
	 * Checks if the boolean value exists and is an actual boolean.
	 * 
	 * @param  boolean 	$boolean  	The boolean to check for.
	 * @param  string 	$context 	The contect to display an error message in.
	 * @return boolean  				The value validated.
	 */
	public static function boolean($boolean = null, $context = "this") {

		//check the gateway is a boolean
		if(!isset($boolean) || !is_bool($boolean)){

			//if it doesnt exist, throw an error
			throw new Exception($context.' needs to be a boolean value.');
		}

		//return value
		return $boolean;
	}


	/**
	 * string
	 *
	 * Checks if the string value exists and is an actual string.
	 * 
	 * @param  string 	$string  	The string to check for.
	 * @param  string 	$context 	The contect to display an error message in.
	 * @return string  				The value validated.
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


	/**
	 * int
	 *
	 * Checks if the int value exists and is an actual integer.
	 * 
	 * @param  int 		$int  		The integer to check for.
	 * @param  string 	$context 	The contect to display an error message in.
	 * @return int  				The value validated.
	 */
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


	/**
	 * ip
	 *
	 * Checks if the ip value exists and is an actual ip address.
	 * 
	 * @param  string 	$ip  		The ip address to check.
	 * @param  string 	$context 	The contect to display an error message in.
	 * @return string  				The value validated.
	 */
	public static function ip($ip = null, $context) {

		//make sure context is a string
		Self::string($context, 'context');

		//check the gateway is a string
		if(!isset($ip) || filter_var($ip, FILTER_VALIDATE_IP) === false){

			//if it doesnt exist, throw an error
			throw new Exception($context.' needs to be an ip address.');
		}

		//return value
		return $ip;

	}


	/**
	 * dom
	 *
	 * Checks if the dom value exists and is an actual dom object.
	 * 
	 * @param  object 	$dom  		The dom object to check.
	 * @param  string 	$context 	The contect to display an error message in.
	 * @return object  		 		The value validated.
	 */
	public static function dom($dom = null, $context = "this"){

		//check for DOMDocument object
		if(!isset($dom) || !is_a($dom, "DOMDocument")){

			//if it doesnt exist, throw an error
			throw new Exception($context.' needs to be a DOMDocument object.');
		}

		//return value
		return $dom;
	}
}