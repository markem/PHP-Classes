<?php
#
#	Defines
#
	if( !defined("[]") ){ define( "[]", "array[]" ); }
#
#	  Standard error function
#
	set_error_handler(function($errno, $errstring, $errfile, $errline ){
#		throw new ErrorException($errstring, $errno, 0, $errfile, $errline);
		die( "Error #$errno IN $errfile @$errline\nContent: " . $errstring. "\n"
		); });

	ini_set( 'memory_limit', -1 );
	date_default_timezone_set( "UTC" );
#
#	$libs is where my libraries are located.
#	>I< have all of my libraries in one directory called "<NAME>/PHP/libs"
#	because of my UNIX background. So I used the following to find them
#	no matter where I was. I created an environment variable called "my_libs"
#	and then it could find my classes. IF YOU SET THINGS UP DIFFERENTLY then
#	you will have to modify the following.
#
	spl_autoload_register(function ($class){
#
#	This might seem stupid but it works. If X is there - get rid of it and then put
#	X onto the string. If X is not there - just put it onto the string. Get it?
#
		$class = str_ireplace( ".php", "", $class ) . ".php";

		$libs = getenv( "my_libs" );
		$libs = str_replace( "\\", "/", $libs );

		if( file_exists("./$class") ){ $libs = "."; }
			else if( file_exists("../$class") ){ $libs = ".."; }
			else if( !file_exists("$libs/$class") ){
				die( "Can't find $libs/$class - aborting\n" );
				}

		include "$libs/$class";
		});

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_json();
#
#-Description:
#
#	A class to handle not only the JSON stuff but to also change keys into hex
#	values. It is VERY IMPORTANT to convert the keys also.
#
#-Inputs:
#
#	None.
#
#-Outputs:
#
#	None.
#
#-Revisions:
#
#	Name					Company					Date
#	---------------------------------------------------------------------------
#	Mark Manning			Simulacron I			Mon 05/27/2019 19:03:25.71 
#		Original Program.
#
#	Mark Manning			Simulacron I			Sat 05/13/2023 17:34:57.07 
#	---------------------------------------------------------------------------
#		This is now under the BSD Three Clauses Plus Patents License.
#		See the BSD-3-Patent.txt file.
#
#	Mark Manning			Simulacron I			Wed 05/05/2021 16:37:40.51 
#	---------------------------------------------------------------------------
#	Please note that _MY_ Legal notice _HERE_ is as follows:
#
#		CLASS_JSON.PHP. A class to handle working with json.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_json
{

################################################################################
#	__construct(). Make the class.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['gd']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['gd']; }
}
################################################################################
#	init(). A function to allow someone to start over for some reason.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}
}
################################################################################
#	html_getJSON().  A function to get all of the HTML JSON code.
#	Notes: Old JSON function to get any JSON information coming in from the
#		browser.
################################################################################
function html_getJSON( $json='json' )
{
//
//	grab JSON data if there...
//
	$params = null;
	if( isset($_REQUEST[$json]) ){
		$params =  json_decode( stripslashes($_REQUEST[$json]) );
		}
		else {
//
//	<-- Have to jump through hoops to get PUT data $raw  = '';
//
			$raw = "";
			$httpContent = fopen( 'php://input', 'r' );
			while( $kb = fread($httpContent, 1024) ){ $raw .= $kb; }
			fclose( $httpContent );

			$p = array();
			parse_str( $raw, $p );

			if( isset($p['data']) ){ $params =  json_decode( stripslashes($p['data']) ); }
				else {
					$p = json_decode( stripslashes($raw) );
					if( $p ){
						if( isset($p->data) ){ $params = $p->data; }
							else { $params = $p; }
						}
					}
			}

	if( is_null($params) ){ $params = array(); }

	return $params;
}
################################################################################
#	put_json(). Convert an array to a hex'd json string.
################################################################################
function put_json( $array )
{
	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = $this->put_json($v); }
			else { $hex = bin2hex( $v ); }

		$hex = (((strlen($hex) % 2) < 1) ? "" : "0") . $hex;
		$array[$k] = "0x" . $hex;
		}

	return json_encode( $array );
}
################################################################################
#	get_json(). Convert a hex'd json to a regular array.
################################################################################
function get_json( $json )
{
	static $first = true;
	if( $first ){ $array = json_decode( $json ); $first = false; }

	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = get_json($v); }
			else { $array[$k] = gzdecode( $v ); }
#			else { $array[$k] = hex2bin( substr($v, 2, strlen($v)) ); }
		}

	return $array;
}
################################################################################
#	key_encode(). Convert all keys to their hex value.
#	Notes: CALL THIS FUNCTION >>>FIRST<<< so the keys are converted.
#	Notes: You can NOT just array_flip and change the keys because
#		some array entries might be an array and you can NOT have an array
#		be a key.
################################################################################
function key_encode( $array )
{
	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = key_encode( $v ); }
		$hex = bin2hex( $k );
		$hex = (((strlen($hex) % 2) < 1) ? "" : "0") . $hex;
		$array[$hex] = $v;
		unset( $array[$k] );
		}

	return $array;
}
################################################################################
#	key_decode(). Convert all keys back to their actual value.
#	Notes: CALL THIS FUNCTION >>>FIRST<<< Before unconverting the array.
#	Notes: You can NOT just array_flip and change the keys because
#		some array entries might be an array and you can NOT have an array
#		be a key.
################################################################################
function key_decode( $array )
{
	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = key_decode( $v ); }
		$bin = hex2bin( substr($k,2,strlen($k)) );
		$array[$bin] = $v;
		unset( $array[$k] );
		}

	return $array;
}
################################################################################
#	str2hex(). Convert a string into a hexadecimal value
################################################################################
function str2hex( $str=null )
{
	if( is_null($str) ){ $str = ""; }

	$hex = pack( "H*", $str );
	return $hex;
}
################################################################################
#	hex2str(). Convert a hex value into a string
################################################################################
function hex2str( $hex=null )
{
	if( is_null($hex) ){ $hex = 0x00; }

	$str = unpack( "H*", $hex );
	return $str;
}
################################################################################
#	errmsg(). Print a message.
################################################################################
function errmsg( $func, $line, $msg )
{
	echo "$msg\n";
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['json']) ){
		$GLOBALS['classes']['json'] = new class_json();
		}

?>
