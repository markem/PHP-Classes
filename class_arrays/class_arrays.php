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
#	class_arrays();
#
#-Description:
#
#	A class to add to the PHP functions. For instance - find ANY key.
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
#	Mark Manning			Simulacron I			Fri 02/05/2021 14:21:56.65 
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
#		CLASS_ARRAYS.PHP. A class to handle working with arrays.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_arrays
{

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['arrays']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['arrays']; }
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
}
################################################################################
#	array_search(). Find ANY key (UPPER/lower/whatever)
#	Example : $this->array_search( "MyNeedle.*5", $myArray );
#		Would find "MyNeedle0000055" as a key. Note ANY regular expression
#		could be used.
################################################################################
function array_isearch( $needles=null, $haystack=null )
{
	if( is_null($needles) ){ die( "NEEDLE is null\n" ); }
	if( is_null($haystack) ){ die( "HAYSTACK is null\n" ); }
#
#	If no needle is sent over - return all of them.
#
	if( !is_array($needles) && strlen(trim($needles)) < 1 ){
		return array_keys( $haystack);
		}

	$ary = [];
	$flag = false;
	$a = array_keys( $haystack );
	foreach( $a as $k=>$v ){
		if( is_array($needles) ){
			foreach( $needles as $k1=>$v1 ){
				if( preg_match("/$v1/i", $v) ){
					$ary[] = $v;
					$flag = true;
					}
				}

			if( $flag ){ return $ary; }
			}
			else if( preg_match("/$needles/i", $v) ){
				$ary[] = $v;
				$flag = true;
				}
		}

	if( $flag ){ return $ary; }

	return fales;
}
################################################################################
#	make_array(). This function will create an array.
#	NOTES:	$options is a TWO DIMENSIONAL ARRAY. It goes like this:
#
#		Each entry is made like this:
#
#			0 = Where to start the array
#			1 = How far this part of the array goes
#			2 = The value to put into the array. (NOTE : ONLY on the last array)
#
#		For each entry - the array adds another array to the maximum of three
#		levels.
#
#		array( 0, 5, '*' );	array[]
#		array( array(0,5), array(0,36,'+') );	array[][]
#		array( array(0,5), array(0,42), array(0,5,'=') ); Array[][][]
################################################################################
function make_array( $options=null )
{
	$cnt = count( $options );
	if( $cnt == 1 ){
		return $this->array_fill( $options[0], $options[1], $options[2] );
		}
		else if( $cnt == 2 ){
			$ary_1 = $this->array_fill( $options[0][0], $options[0][1], array() );
			for( $i=$options[0][0]; $i<$options[0][1]; $i++ ){
				$ary_1[$i] =
					$this->array_fill( $options[1][0], $options[1][1], $options[1][2] );
				}
			}
		else if( $cnt == 3 ){
			$ary_1 = $this->array_fill( $options[0][0], $options[0][1], array() );
			for( $i=$options[0][0]; $i<$options[0][1]; $i++ ){
				$ary_1[$i] =
					$this->array_fill( $options[1][0], $options[1][1], array() );
				for( $j=$options[1][0]; $j<$options[1][1]; $j++ ){
					$ary_1[$i][$j] =
						$this->array_fill( $options[2][0], $options[2][1], $options[2][2] );
					}
				}
			}
		else {
			die( "***** ERROR : Too many arrays to make - aborting.\n" );
			}

	return $ary_1;
}
################################################################################
#	array_fill(). Fill a ONE dimensional array with something
################################################################################
function array_fill( $start=null, $end=null, $mixed=null )
{
	$ary = [];
	for( $i=$start, $i<$end, $i++ ){
		$ary[$i] = $mixed;
		}

	return $ary;
}
################################################################################
#	__destruct(). The class destruct function.
################################################################################
function __destruct()
{
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['arrays']) ){
		$GLOBALS['classes']['arrays'] = new class_arrays();
		}

?>
