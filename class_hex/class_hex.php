<?php
#
#	Defines
#

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
#
#	Set up LIBS so we can check whereever the class is located
#	Put in the standard stuff
#
		$libs = [];
		$libs[] = ".";
		$libs[] = "..";
#
#	Now get the environment information - IF it is there
#
		$env = getenv( "my_libs" );
		if( !is_null($env) ){
			$libs[] = $env;
			}
#
#	Now insert all of the other locations to look in
#
		$libs[] = "C:/xampp/php/usr/fpdf186";
		$libs[] = "C:/xampp/php/usr/setasign";
		$libs[] = "C:/xampp/php/usr";

		$flag = true;
		foreach( $libs as $k=>$v ){
			if( file_exists("$v/$class") ) { $lib = $v; $flag = false; }
			}

		if( $flag ){ die( "Can't find $class - aborting\n" ); }

		include_once "$lib/$class";
		});

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_hex();
#
#-Description:
#
#	Handle my HEXD code.
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
#	Mark Manning			Simulacron I			Sat 02/15/2025 17:14:36.37
#		Original Program.
#
#	Mark Manning			Simulacron I			Sat 07/17/2021 14:56:52.53 
#	---------------------------------------------------------------------------
#		REMEMBER! We are now following the PHP code of NOT killing the program
#		but instead always setting a DEBUG MESSAGE and returning FALSE. So I'm
#		getting rid of all of the DIE() calls.
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
#		CLASS_FILES.PHP. A class to handle working with files.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_hex
{
	private $base64 = null;
	private $hex = null;
	private $pr = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['files']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['files']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->pr = new class_pr();
#
#	Get our base 64 info
#
	$base64 = "0123456789";
	for( $i=0; $i<26; $i++ ){ $base64 .= chr(ord("A")+$i); }
	for( $i=0; $i<26; $i++ ){ $base64 .= chr(ord("a")+$i); }
	$base64 .= "=+";

	$this->base64 = $base64;
#
#	Set up $hex
#
	$this->hex = "0123456789ABCDEF";
}
################################################################################
#	encode(). A simple "convert to hex" routine. Handles vars and arrays
#	NOTE : Don't know if it will work with an object. But I don't think so.
################################################################################
function encode( $var=null, $key=null )
{
	if( is_null($var) ){
		die( "***** ERROR : Call line variable is NULL - Aborting\n" );
		}

	$str = "";
	if( is_array($var) ){
		foreach( $var as $k=>$v ){
			if( is_array($v) ){
				$key = bin2hex( $k );
				$key = ((strlen($key) % 2) < 1) ? "$key" : "0$key";
				$str .= "[$key:" . $this->encode( $v ) . "]";
				}
				else {
					$key = bin2hex( $k );
					$key = ((strlen($key) % 2) < 1) ? "$key" : "0$key";
					$val = bin2hex( $v );
					$val = ((strlen($val) % 2) < 1) ? "$val" : "0$val";
					$str .= "<$key=$val>";
					}
			}
		}
		else {
			if( is_null($key) ){ $key = "00"; }
				else {
					$key = bin2hex( $key );
					$key = ((strlen($key) % 2) < 1) ? "$key" : "0$key";
					}

			$val = bin2hex( $var );
			$val = ((strlen($val) % 2) < 1) ? "$val" : "0$val";
			$str .= "<$key=$val>";
			}

	return $str;
}
################################################################################
#	decode(). Takes the string apart and re-creates a variable or an array.
################################################################################
function decode()
{
	$args = func_get_args();
	$args_cnt = func_num_args();

	$string = $args[0];
	$current_array = array();
	$string_len = strlen( $string );

	if( $args_cnt > 1 ){ $start = $args[1]; }
		else{ $start = 0; }

	if( is_null($string) ){
		die( "***** ERROR : Call line variable is NULL - Aborting\n" );
		}

	$key_cnt = 0;
	for( $i=$start; $i<$string_len; $i++ ){
		$a = substr( $string, $i++, 1 );
#	 	=><30=54686973206973206120746573740a><31=54686973206973206120746573740a>
#	 	=>[32:<30=546573742074686973206973><31=546573742074686973206973><32=5465
#	 	=>73742074686973206973><33=546573742074686973206973>]<33=546869732069732
#	 	=>06120746573740a><34=54686973206973206120746573740a>
		if( $a == "<" ){
			$str = "";
			while( $a != "=" ){
				$a = substr( $string, $i++, 1 );
				$str .= $a;
				}

			$key = substr( $str, 0, -1 );
			$key = hex2bin( $key );
#
#	Now get the value
#
			$str = "";
			$a = substr( $string, $i++, 1 );
			while( $a != ">" ){
				$str .= $a;
				$a = substr( $string, $i++, 1 );
				}

			$val = hex2bin( $str );

			$current_array[$key] = $val;
			$i--;
			}
			else if( $a == '[' ){
				$str = "";
				while( $a != ':' ){
					$a = substr( $string, $i++, 1 );
					$str .= $a;
					}

				$key = substr( $str, 0, -1 );
				$key = hex2bin( $key );
				list( $i, $current_array[$key] ) = $this->decode( $string, $i );
				}
			else if( $a == ']' ){
				return array( --$i, $current_array );
				}
		}

	return( $current_array );
}

}

?>
