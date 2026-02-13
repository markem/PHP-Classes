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
#	class_csv();
#
#-Description:
#
#	Handle read/writing csv files.
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
#	Mark Manning			Simulacron I			Mon 10/05/2020 17:58:02.37 
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
#		CLASS_CSV.PHP. A class to handle working with CSV.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#	Mark Manning			Simulacron I			Wed 05/05/2021 18:09:34.05 
#	---------------------------------------------------------------------------
#	Changed "$this->errmsg" to "$this->debug->die".
#	Changed get_csv and put_csv to fget_csv and fput_csv.
#	Made fget_csv allow for titles.
#	Made fpug_csv allow for titles.
#
#END DOC
################################################################################
class class_csv
{

################################################################################
#	__construct(). Init the class.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['csv']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['csv']; }
}
################################################################################
#	init(). Setting up the new way to initialize everything.
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
#	@link http://gist.github.com/385876
################################################################################
function fget_csv( $filename='', $isTitle=true, $delimiter=',' )
{
    if(!file_exists($filename) || !is_readable($filename)){
		$s = "***** ERROR : " . __FUNCTION__ . "@" . __LINE__ .
			" >>> The filename is Not there.<br>\n";

		die( $s );
		}

	$data = array();

	$fh = fopen( $filename, "r" );
	for($i=0; $a=fgetcsv( $fh, $delimiter ); $i++){
		if( count($a) > 1 ){ $data[] = $a; }
		}

	fclose( $fh );
#
#	In most CSV files - the first line is the TITLE line.
#
	if( $isTitle ){ $title = array_pop( $data ); }
		else { $title = null; }
#
#	Return TITLE and DATA. In this way, if the first line
#	IS NOT a title line - you can just put it back onto the
#	array -OR- set $isTitle to FALSE when you call this routine.
#
	return array( $title, $data );
}
################################################################################
#	Function to write out a CSV file.
#	Notes: $isTitle should be TRUE -IF- your array's keys are named what
#		your columns should be named. If you are providing your column names
#		IN THE ARRAY (and not the NAMES of the columns) - then make $isTitle
#		FALSE and it will NOT WRITE OUT THE COLUMN NAMES.
################################################################################
function fput_csv($filename='', $array=null, $isTitle=true, $delimiter=',')
{
	$c = count( $array );

	if( !isset($array) || is_null($array) || $c < 1 ){
		$s = "***** ERROR : " . __FUNCTION__ . "@" . __LINE__ .
			" >>> The array is blank.<br>\n";

		die( $s );
		}

	$fh = fopen( $filename, "w" );

	if( $fh == false ){
		$s = "***** ERROR : " . __FUNCTION__ . "@" . __LINE__ .
			" >>> The filename is Not there.<br>\n";

		die( $s );
		}

	if( $isTitle ){
		$keys = array_keys( $array );
		fputcsv( $fh, $keys, $delimiter );
		}

	for($i=0; $i<$c; $i++ ){
		fputcsv( $fh, $array[$i], $delimiter );
		}

	fclose( $fh );

	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['csv']) ){
		$GLOBALS['classes']['csv'] = new class_csv();
		}

?>
