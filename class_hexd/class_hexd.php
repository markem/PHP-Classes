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

	ini_set( 'memory_limit', -1 );
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

#	include_once( "$lib/class_arcs.php" );

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_hexd();
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
class class_hexd
{

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
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
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->debug->out();
}
################################################################################
#	hexd_encode(). Create the hexd stuff
################################################################################
function hexd_encode( $ary=null )
{
	if( is_null($ary) ){ die( "***** ERROR : ARRAY is NULL\n" ); }
	$hexd = $this->hexd_values( $ary );
	$hexd = $this->hexd_keys( $hexd );

	return $hexd;
}
################################################################################
#	hexd_values(). Convert all strings to hexed
################################################################################
function hexd_values( $ary=null )
{
	foreach( $ary as $k=>$v ){
		if( is_array($v) ){ $v = $this->hexd_values( $v ); }
			else { $v = "0x" . bin2hex( $v ); }

		$ary[$k] = $v;
		}

	return $ary;
}
################################################################################
#	hexd_keys(). Convert all keys to being hexed
################################################################################
function hexd_keys( $ary=null )
{
	$str = "";
	foreach( $ary as $k=>$v ){
		if( is_array($v) ){ $v = $this->hexd_keys( $v ); }

		if( $v == "0x" ){ $v = "0x00"; }
		$str .= "[0x" . bin2hex( $k ) . ":$v]";
		}

	return $str;
}
################################################################################
#	hexd_decode(). Restore the array.
################################################################################
function hexd_decode( $str=null )
{
	$dq = '"';
#
#	This is NOT recursive. Instead, we keep track of where we are with a variable
#	called LEVEL. LEVEL is an array which keeps track of where we are in the
#	array we are creating. Each "A:" means ADD on that key. Each "]" means
#	to DECREMENT LEVEL by one level.
#
#	The array to make is ARY
#
	$ary = [];
#
#	Where we are in the levels of the array. This is a pop-up - push-down stack.
#
	$level = [];
#
#	Now split everything up into our parts
#
	$b = explode( '[', $str );
#
#	Check to make sure the first entry is not blank. If so - get rid of it.
#
	if( isset($b[0]) && (strlen($b[0]) < 1) ){ $c = array_shift( $b ); }

	foreach( $b as $k=>$v ){
		$count = 0;
		$v = str_replace( ']', '', $v, $count );
		$a = explode( ':', $v );
#
#	Get rid of the 0x I put there to mark this as a hexadecimal value.
#
		if( preg_match("/^0x/i", $a[0]) ){ $hex = str_replace( "0x", "", $a[0] ); }
			else { $hex = $a[0]; }

		$key = hex2bin( $hex );
		$level[] = $key;
		if( isset($a[1]) && (strlen($a[1]) > 0) ){
#
#	Start the ARY command.
#
			$cmd = "\$ary[";
#
#	Now add in all of the keys
#
			foreach( $level as $k1=>$v1 ){ $cmd .= "'$v1']["; }
			$cmd = substr( $cmd, 0, -1 );
#
#	Now remove the last "[".
#
#print_r( $a ); echo "\n";
			if( preg_match("/^0x/i", $a[1]) ){ $hex = str_replace( "0x", "", $a[1] ); }
				else { $hex = $a[1]; }

#echo "Hex = $hex\n";
			if( preg_match("//", $hex) ){ $hex = "NULL"; }
				else { $val = hex2bin( $hex ); }
#echo "Hex = $hex\n";

			if( $val == " " ){ $val = "NULL"; }
			$val = str_replace( '"', '', $val );

			$cmd = "$cmd = $dq$val$dq;";
#			echo "LINE = " . __LINE__ . " : Command = $cmd\n";
			eval( $cmd );
#
#	Now remove any keys we need to remove
#
			for( $i=0; $i<$count; $i++ ){ array_pop( $level ); }
			}
		}
#
#	Now remove any REAL NULL entries. In other words - not the ones we
#	put the word "NULL" into but an entry that if you say is_null() on
#	it OR if the entry is absolutely empty (ie: "") AND it is the last
#	entry - then get rid of it.
#
	return $ary;
}
################################################################################
#	dump(). A simple function to dump some information.
#	Ex:	$this->dump( "NUM", $num );
################################################################################
function dump( $title=null, $arg=null )
{
	$this->debug->in();
#	echo "--->Entering DUMP\n";

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

#		echo "$k ---> $title in $class$type$func @ Line : $line =\n";
		foreach( $args as $k1=>$v1 ){
			if( is_array($v1) ){
				foreach( $v1 as $k2=>$v2 ){
#					echo "	$k " . str_repeat( '=', $k1 + 3 ) ."> " . $title. "[$k1][$k2] = $v2\n";
					}
				}
				else {
#					echo "	$k " . str_repeat( '=', $k1 + 3 ) . "> " . $title . "[$k1] = $v1\n";
					}
			}

#		if( is_array($arg) ){ print_r( $arg ); echo "\n"; }
#			else { echo "ARG = $arg\n"; }
		}

#	echo "<---Exiting DUMP\n\n";
	$this->debug->out();
	return true;
}
################################################################################
#	dump(). A short function to dump a file.
################################################################################
function dumpfile( $f=null, $l=null )
{
	$this->debug->in();

	if( is_null($f) ){
		$this->debug->msg( "DIE : No file given" );
		return false;
		}

	if( is_null($l) ){ $l = 32; }

	$fh = fopen($f, "r" );
	$r = fread( $fh, 1024 );
	fclose( $fh );

	$this->debug->msg( "Dump File	: " );
	for ($i = 0; $i < $l; $i++) {
		$this->debug->msg( str_pad(dechex(ord($r[$i])), 2, '0', STR_PAD_LEFT) );
		}

	$this->debug->msg( "\nHeader  : " );
	for ($i = 0; $i < 32; $i++) {
		$s = ord( $r[$i] );
		$s = ($s > 127) ? $s - 127 : $s;
		$s = ($s < 32) ? ord(" ") : $s;
		$this->debug->msg( chr( $s ) );
		}

	$this->debug->msg( "\n" );
	$this->debug->out();

	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['hexd']) ){
		$GLOBALS['classes']['hexd'] = new class_hexd();
		}

?>
