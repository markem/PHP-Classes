<?php
#
#	Defines
#
	if( !defined("[]") ){ define( "[]", "array[]" ); }
#
#	Standard error function
#
	set_error_handler(function($errno, $errstring, $errfile, $errline ){
		die( "Error #$errno IN $errfile @$errline\nContent: " . $errstring. "\n" );
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
#	class_misc();
#
#-Description:
#
#	A set of miscellaneous functions.
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
#	Mark Manning			Simulacron I			Tue 01/19/2021 16:15:21.24 
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
#		CLASS_MISC.PHP. A class to handle working with miscellaneous stuff.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_misc
{
################################################################################
#	__construct(). Init function for the class. This does NOT mean it is the
#	ONLY function to do this. Instead, if you don't want to do a NEW each
#	time - use the init() function.
################################################################################
function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
	if( !isset($GLOBALS['class']['misc']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['misc']; }
}
################################################################################
#	init(). Initialization function. This can be called without having to call
#	the __construct() function every time.
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
#	toHex(). Convert an incoming value to be a hex value.
################################################################################
function toHex( $val=null )
{
	if( is_null($val) ){ return false; }

	if( is_array($val) ){
		foreach( $val as $k=>$v ){
			$val[$k] = $this->toHex( $v );
			}
		}
		else {
			$b = gettype( $val );
			switch( $b ){
				case "boolean" :
					$a = (($val === false) ? "01" : "00");
					break;

				case "integer" :
				case "object" :
				case "resource" :
				case "resource (closed)" :
					$a = dec2hex( $val );
					break;

				case "double" :
					$a = floatval( $val );
					$a = $this->toHex( $a );
					break;

				case "string" :
					$a = bin2hex( $val );
					break;

				case "array" :
					$a = $this->toHex( $val );
					break;

				case "NULL" :
				case "unknown type" :
				default :
					$a = "00";
					break;
				}
#
#	If we got an odd length string - add a zero on to the front of it
#	so it is correct.
#
			if( (strlen($a) % 2) > 0 ){ $a = "0$a"; }
#
#	ALWAYS put a "0x" at the front to say THIS IS A HEX VALUE.
#
			$a = "0x$a";
			}

	return $a;
}
################################################################################
#	fromHex(). Take something FROM Hex to whatever it might be.
################################################################################
function fromHex( $val=null )
{
	if( is_null($val) ){ return false; }

	if( preg_match("/^0x/i", $val) ){ $val = substr( $val, 2, strlen($val) ); }
	$a = hexdec( $val );
	$b = hex2bin( $val );
	$c = unpack( "h*", $val );
	$d = unpack( "H*", $val );

	if( hexdec($a) === $val ){ return $a; }
		else if( bin2hex($b) === $val ){ return $b; }
		else if( pack( "h*", $c) === $val ){ return $c; }
		else if( pack( "H*", $d) === $val ){ return $d; }

	return false;
}
################################################################################
#	dump(). A simple function to dump some information.
#	Ex:	$this->dump( "NUM", $num );
################################################################################
function dump( $title=null, $arg=null )
{
	$this->debug->in();
	echo "--->Entering DUMP\n";

	if( is_null($title) ){ return false; }
	if( is_null($arg) ){ return false; }

	$title = trim( $title );
#
#	Get the backtrace
#
	$dbg = debug_backtrace();
#
#	Start a loop
#
	foreach( $dbg as $k=>$v ){
		$a = array_pop( $dbg );

		foreach( $a as $k1=>$v1 ){
			if( !isset($a[$k1]) || is_null($a[$k1]) ){ $a[$k1] = "--NULL--"; }
			}

		$func = $a['function'];
		$line = $a['line'];
		$file = $a['file'];
		$class = $a['class'];
		$obj = $a['object'];
		$type = $a['type'];
		$args = $a['args'];

		echo "$k ---> $title in $class$type$func @ Line : $line =\n";
		foreach( $args as $k1=>$v1 ){
			if( is_array($v1) ){
				foreach( $v1 as $k2=>$v2 ){
					echo "	$k " . str_repeat( '=', $k1 + 3 ) ."> " . $title. "[$k1][$k2] = $v2\n";
					}
				}
				else { echo "	$k " . str_repeat( '=', $k1 + 3 ) . "> " . $title . "[$k1] = $v1\n"; }
			}

#		if( is_array($arg) ){ print_r( $arg ); echo "\n"; }
#			else { echo "ARG = $arg\n"; }
		}

	echo "<---Exiting DUMP\n\n";
	$this->debug->out();
	return true;
}
################################################################################
#	swap(). A SWAP function for PHP.
################################################################################
function swap( $a=null, $b=null ){ return array( $b, $a ); }
################################################################################
#	__destruct(). PHP class destruction function. Last thing called when a
#	class is destroyed.
################################################################################
function __destruct()
{
}

}


	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['misc']) ){
		$GLOBALS['classes']['misc'] = new class_misc();
		}
?>

