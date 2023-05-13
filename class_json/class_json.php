<?php
#
#	Standard error function
#
	set_error_handler(function($errno, $errstring, $errfile, $errline ){
		echo "Error #$errno IN $errfile @$errline\nContent: " . $errstring. "\n";
		});

	date_default_timezone_set( "UTC" );
#
#	$lib is where my libraries are located.
#	>I< have all of my libraries in one directory called "<NAME>/PHP/libs"
#	because of my UNIX background. So I used the following to find them
#	no matter where I was. I created an environment variable called "my_libs"
#	and then it could find my classes. IF YOU SET THINGS UP DIFFERENTLY then
#	you will have to modify the following.
#
	$lib = getenv( "my_libs" );
	$lib = str_replace( "\\", "/", $lib );
	if( !file_exists($lib) ){ $lib = ".."; }

	if( file_exists("$lib/class_debug.php") ){
		include_once( "$lib/class_debug.php" );
		}
		else if( !isset($GLOBALS['classes']['debug']) ){
			die( __FILE__ . ": Can not load CLASS_DEBUG" );
			}

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
	public $debug = false;

################################################################################
#	__construct(). Make the class.
################################################################################
function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
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
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->debug->out();
}
################################################################################
#	html_getJSON().  A function to get all of the HTML JSON code.
#	Notes: Old JSON function to get any JSON information coming in from the
#		browser.
################################################################################
function html_getJSON( $json='json' )
{
	$this->debug->in();
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

	$this->debug->out();

	return $params;
}
################################################################################
#	put_json(). Convert an array to a hex'd json string.
################################################################################
function put_json( $array )
{
	$this->debug->in();

	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = put_json($v); }
			else {
				$hex = bin2hex( $v );
				$hex = (((strlen($hex) % 2) < 1) ? "" : "0") . $hex;
				$array[$k] = "0x" . $hex;
				}
		}

	$this->debug->out();

	return json_encode( $array );
}
################################################################################
#	get_json(). Convert a hex'd json to a regular array.
################################################################################
function get_json( $json )
{
	$this->debug->in();

	static $first = true;
	if( $first ){ $array = json_decode( $json ); $first = false; }

	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = get_json($v); }
			else { $array[$k] = hex2bin( substr($v, 2, strlen($v)) ); }
		}

	$this->debug->out();

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
	$this->debug->in();

	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = key_encode( $v ); }
		$hex = bin2hex( $k );
		$hex = (((strlen($hex) % 2) < 1) ? "" : "0") . $hex;
		$array[$hex] = $v;
		unset( $array[$k] );
		}

	$this->debug->out();

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
	$this->debug->in();

	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = key_decode( $v ); }
		$bin = hex2bin( substr($k,2,strlen($k)) );
		$array[$bin] = $v;
		unset( $array[$k] );
		}

	$this->debug->out();

	return $array;
}
################################################################################
#	errmsg(). Print a message.
################################################################################
function errmsg( $func, $line, $msg )
{
	$this->debug->in();
	$this->debug->msg( $msg );
	$this->debug->out();
}
################################################################################
#	dump(). A simple function to dump some information.
#	Ex:	$this->dump( "NUM", __LINE__, $num );
################################################################################
function dump( $title=null, $line=null, $arg=null )
{
	$this->debug->in();

	if( is_null($title) ){ return false; }
	if( is_null($line) ){ return false; }
	if( is_null($arg) ){ return false; }

	if( is_array($arg) ){
		echo "$title @ Line : $line =\n";
		print_r( $arg );
		echo "\n";
		}
		else {
			echo "$title @ Line : $line = $arg\n";
			}

	$this->debug->out();
	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['json']) ){
		$GLOBALS['classes']['json'] = new class_json();
		}

?>
