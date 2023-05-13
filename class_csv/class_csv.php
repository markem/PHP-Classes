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
	private $debug = null;

################################################################################
#	__construct(). Init the class.
################################################################################
function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
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
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->debug->out();
}
################################################################################
#	@link http://gist.github.com/385876
################################################################################
function fget_csv($filename='', $isTitle=true, $delimiter=',')
{
	$this->debug->in();

    if(!file_exists($filename) || !is_readable($filename)){
		$this->debug->die( __FUNCTION__, __LINE__, " >>> The filename is Not there.<br>\n" );
		}

	$data = array();

	$fh = fopen( $filename, "r" );
	for($i=0; $data[]=fgetcsv( $fh, $delimiter ); ++$i){}
	fclose( $fh );
#
#	In more CSV files - the first line is the TITLE line.
#
	if( $isTitle ){ $title = array_pop( $data ); }
		else { $title = null; }

	$this->debug->out();
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
	$this->debug->in();

	$c = count( $array );

	if( !isset($array) || is_null($array) || $c < 1 ){
		$this->debug->die( __FUNCTION__, __LINE__, " >>> The array is blank.<br>\n" );
		}

	$fh = fopen( $filename, "w" );

	if( $fh == false ){
		$this->debug->die( __FUNCTION__, __LINE__, " >>> The filename is Not there.<br>\n" );
		}

	if( $isTitle ){
		$keys = array_keys( $array );
		fputcsv( $fh, $keys, $delimiter );
		}

	for($i=0; $i<$c; $i++ ){
		fputcsv( $fh, $array[$i], $delimiter );
		}

	fclose( $fh );

	$this->debug->out();

	return true;
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
	if( !isset($GLOBALS['classes']['csv']) ){
		$GLOBALS['classes']['csv'] = new class_csv();
		}

?>
