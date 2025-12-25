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

	define( "INT_SIZE", PHP_INT_SIZE * 8 );
	define( "BIT_NOT", 1 );
	define( "BIT_ZERO", 2 );
	define( "BIT_ONE", 3 );
	define( "BIT_XOR", 4 );
	define( "BIT_AND", 5 );
	define( "BIT_OR", 6 );
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

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_bits();
#
#-Description:
#
#	Handle my HEXD code.
#
#	Notes:
#		1. PHP_INT_SIZE = Number of bytes per word (8 = 64, 4 = 32)
#		2. PHP_INT_MAX = Largest number possible. (2GB = 32, 2PB = 64)
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
#	Mark Manning			Simulacron I			Tue 03/04/2025 14:13:01.31
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
class class_bits
{
	private $bit_length = null;	#	How long is each variable/register

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
	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->bit_length = INT_SIZE;
}
################################################################################
#	set_length(). Change the length you want to work with. Done in BITS.
################################################################################
function set_length( $len=32 )
{
	$this->bit_length = $len;
	return true;
}
################################################################################
#	get_length(). Gets how long our variables are
################################################################################
function get_length()
{
	return $this->bit_length;
}
################################################################################
#	doit(). Do whatever it is we want to do.
#	NOTES:
#		var_1 is what we are testing against
#		var_2 is what we are going to test against
#		start is where to start testing
#		end is where to stop testing (remember the computer is zero based)
#		opt is optional parameters
#		ans is the answer. The answer has to be as large as possible.
################################################################################
function bit_test( $var_1=null, $start=0, $end=INT_SIZE, $opt=BIT_NOT, $var_2=null )
{
	if( is_null($var_1) ){ die( "***** ERROR : Variable #1 is NULL\n" ); }

	$buf_1 = [];
	$format = "%0" . $this->bit_length . "b";
	$bin = sprintf( $format, $var_1 );
#
#	First, we have to get the entire variable because otherwise we would muck
#	up the variable.
#
	for( $i=0; $i<$this->bit_length; $i++ ){ $buf_1[$i] = substr( $bin, $i, 1 ); }
#
#	Now do the second variable
#
	$buf_2 = [];
	$bin = sprintf( $format, $var_2 );
#
#	First, we have to get the entire variable because otherwise we would muck
#	up the variable.
#
	for( $i=0; $i<$this->bit_length; $i++ ){ $buf_2[$i] = substr( $bin, $i, 1 ); }
#
#	Now do the test
#
	for( $i=$start; $i<$end; $i++ ){
		switch( $opt ){
			case BIT_NOT: !$buf_1[$i]; break;
			case BIT_ZERO: $buf_1[$i] = 0; break;
			case BIT_ONE: $buf_1[$i] = 1; break;
			case BIT_XOR: $buf_1[$i] xor $buf_2[$i]; break;
			case BIT_AND: $buf_1[$i] and $buf_2[$i]; break;
			case BIT_OR: $buf_1[$i] or $buf_2[$i]; break;
			default: die( "***** ERROR : No operation given\n" ); break;
			}
		}
#
#	Put it all back together
#
	return bindec( implode('', $buf_1) );
}
################################################################################
#	not(). Not bits.
################################################################################
function not( $var=null, $start=0, $end=INT_SIZE, $opt=BIT_NORMAL, $bit=null )
{
	return $this->bit_test( $var, $start, $end, BIT_NOT );
}
################################################################################
#	zeros(). Zero out all or part of a variable.
################################################################################
function zeros( $var=null, $start=0, $end=INT_SIZE )
{
	return $this->bit_test( $var, $start, $end, BIT_ZERO );
}
################################################################################
#	ones(). Set all or part to ones
################################################################################
function ones( $var=null, $start=0, $end=INT_SIZE )
{
	return $this->bit_test( $var, $start, $end, BIT_ONE );
}
################################################################################
#	xor(). Set all or part to be exclusively OR'd.
#		Take the $BIT and XOR it against the bits given.
#	NOTES : REMEMBER! The $BIT variable is applied against ALL of the bits
#		given by $start -> to -> $end!!!! So if $start is 13 and $end is 27
#		BUT $bits is just 1 - you are really going to test against ZERO(0)
#		because $bits is 00000000000000000000000000000001!!!!!
#		So if you want to XOR against a one(1). Then FIRST do the following:
#
#		#	Create a variable that contains all ones(1).
#
#			$xor = $class_bits->ones( 0 );
#
#		THEN you can do the XOR command with the new variable.
#
#			$ans = $class_bits->xor( $var, $xor );
################################################################################
function xor( $var=null, $bit=0, $start=0, $end=INT_SIZE )
{
	return $this->bit_test( $var, $start, $end, BIT_ONE, $bit );
}
################################################################################
#	and(). Set all or part to be AND'd together.
################################################################################
function and( $var=null, $bit=0, $start=0, $end=INT_SIZE )
{
	$this->fb( $var, $start, $end, BIT_AND, $bit );
}
################################################################################
#	or(). Set all or part to be OR'd together.
################################################################################
function or( $var=null, $bit=0, $start=0, $end=INT_SIZE )
{
	$this->fb( $var, $start, $end, BIT_OR, $bit );
}
################################################################################
#	ls(). Left shift. Returns shifted variable AND what was popped off of the
#		the end of the variable. The $BIT variable is pushed onto the variable.
################################################################################
function ls( $var=null, $bit=0, $start=0; $end=INT_SIZE )
{
	if( is_null($var) ){ die( "***** ERROR : VARiable is NULL\n" ); }
	if( strlen($bit) > 1 ){
		die( "***** ERROR : BIT is larger than a single digit - $bit\n" );
		}

	$ans = [];
	$format = "%0" . INT_SIZE . "b";
	$bin = sprintf( $format, $var );
#
#	First, we have to get the entire variable because otherwise we would muck
#	up the variable.
#
	for( $i=0; $i<INT_SIZE; $i++ ){ $ans[$i] = substr( $bin, $i, 1 ); }
#
#	Get the segment of the variable we are going to work with.
#
	$seg = [];
	for( $i=$start; $i<$end; $i++ ){
		$seg[] = $ans[$i];
		}
#
#	Now do the shift
#
	array_push( $seg, $bit );
	$bit = array_shift( $seg );
#
#	Put it all back
#
	$cnt = count( $seg );
	for( $i=0; $i<$cnt; $i++ ){
		$ans[$start+$i] = $seg[$i];
		}
#
#	Put it all back together
#
	return array( bindec(implode('', $ans)), $bit );
}
################################################################################
#	rs(). Right shift. Returns shifted variable AND what was popped off of the
#		the end of the variable. The $BIT variable is pushed onto the variable.
################################################################################
function rs( $var=null, $bit=0, $start=0; $end=INT_SIZE )
{
	if( is_null($var) ){ die( "***** ERROR : VARiable is NULL\n" ); }
	if( strlen($bit) > 1 ){
		die( "***** ERROR : BIT is larger than a single digit - $bit\n" );
		}

	$ans = [];
	$format = "%0" . INT_SIZE . "b";
	$bin = sprintf( $format, $var );
#
#	First, we have to get the entire variable because otherwise we would muck
#	up the variable.
#
	for( $i=0; $i<INT_SIZE; $i++ ){ $ans[$i] = substr( $bin, $i, 1 ); }
#
#	Get the segment of the variable we are going to work with.
#
	$seg = [];
	for( $i=$start; $i<$end; $i++ ){
		$seg[] = $ans[$i];
		}
#
#	Move things around
#
	array_unshift( $seg, $bit );
	$bit = array_pop( $seg );
#
#	Now move everything back to the answer ($ANS).
#
	$cnt = count( $seg );
	for( $i=0; $i<$cnt; $i++ ){
		$ans[$start+$i] = $seg[$i];
		}
#
#	Put it all back together
#
	return array( bindec(implode('', $ans)), $bit );
}
################################################################################
#	lcs(). Left circular shift.
#	NOTES:	This does a circular shift ON the variable. So if you had 1100 and
#		you called this routine - then you'd get back 1001.
################################################################################
function lcs( $var=null, $start=0; $end=INT_SIZE )
{
	if( is_null($var) ){ die( "***** ERROR : VARiable is NULL\n" ); }

	$ans = [];
	$format = "%0" . INT_SIZE . "b";
	$bin = sprintf( $format, $var );
#
#	First, we have to get the entire variable because otherwise we would muck
#	up the variable.
#
	for( $i=0; $i<INT_SIZE; $i++ ){ $ans[$i] = substr( $bin, $i, 1 ); }
#
#	Get the segment of the variable we are going to work with.
#
	$seg = [];
	for( $i=$start; $i<$end; $i++ ){
		$seg[] = $ans[$i];
		}
#
#	Do the left circular shift
#
	array_push( $seg, array_shift($seg) );
#
#	Now move everything back to the answer ($ANS).
#
	$cnt = count( $seg );
	for( $i=0; $i<$cnt; $i++ ){
		$ans[$start+$i] = $seg[$i];
		}
#
#	Put it all back together
#
	return bindec( implode('', $ans) );
}
################################################################################
#	rcs(). Right circular shift.
#	NOTES:	This does a circular shift ON the variable. So if you had 1100 and
#		you called this routine - then you'd get back 0110.
################################################################################
function rcs( $var=null, $start=0; $end=INT_SIZE )
{
	if( is_null($var) ){ die( "***** ERROR : VARiable is NULL\n" ); }
	if( strlen($bit) > 1 ){
		die( "***** ERROR : BIT is larger than a single digit - $bit\n" );
		}

	$ans = [];
	$format = "%0" . INT_SIZE . "b";
	$bin = sprintf( $format, $var );
#
#	First, we have to get the entire variable because otherwise we would muck
#	up the variable.
#
	for( $i=0; $i<INT_SIZE; $i++ ){ $ans[$i] = substr( $bin, $i, 1 ); }
#
#	Get the segment of the variable we are going to work with.
#
	$seg = [];
	for( $i=$start; $i<$end; $i++ ){
		$seg[] = $ans[$i];
		}
#
#	Do the right circular shift
#
	array_unshift( $seg, array_pop($seg) );
#
#	Now move everything back to the answer ($ANS).
#
	$cnt = count( $seg );
	for( $i=0; $i<$cnt; $i++ ){
		$ans[$start+$i] = $seg[$i];
		}
#
#	Put it all back together
#
	return array( bindec(implode('', $ans)), $bit );
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['bits']) ){
		$GLOBALS['classes']['bits'] = new class_bits();
		}

?>
