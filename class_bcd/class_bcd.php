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
#	class_bcd();
#
#-Description:
#
#	I'm making this class because I want to be able to handle more than
#	what the standard PHP BCD class can handle.
#
#	The way I am doing this is to have a bunch of bytes in a row. Each of
#	these bytes represents ONE(1) digit. In actuality though, if you need
#	to double the amount of space a single digit takes up, you could use
#	the high/low nibbles of a byte to do the numbers 0-9. But then you
#	always have to separate all of them out and then put them back in again.
#	Since I'm not trying to compute PI (and thus need hundreds of thousands
#	of digits) - I think just using one byte per letter is the best way
#	of doing this.
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
#	Mark Manning			Simulacron I			Thu 08/05/2021 17:01:12.03 
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
#		CLASS_BCD.PHP. A class to handle working with BCD variables.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_bcd
{
	private	$vars = null;

################################################################################
#	__construct(). Function to initialize this class.
################################################################################
function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
	if( !isset($GLOBALS['class']['bcd']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['bcd']; }
}
################################################################################
#	init(). Initialization function. Done so you can call it to reset everything
#		if you need to do so.
################################################################################
function init()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->vars = [];

	$this->debug->out();
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

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['bcd']) ){
		$GLOBALS['classes']['bcd'] = new class_bcd();
		}

?>
