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
#	MicroDateTime();
#
#-Description:
#
#	A class to extend what the TIME function can do.
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
#	Mark Manning			Simulacron I			Sun 01/24/2021 23:35:54.73 
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
#		CLASS_TIME.PHP. A class to handle working with time.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_MicroDateTime extends DateTime
{
    public $microseconds = 0;

################################################################################
#	__construct(). Used to start the class.
################################################################################
public function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
	if( !isset($GLOBALS['class']['gd']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['gd']; }
}
################################################################################
#	init(). A function so you can re-init this class.
################################################################################
public function init()
{

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$time = $args;
	if ($time == 'now' || is_array($args) || is_null($args) ){ $time = microtime(true); }
#
# "+ 0" implicitly converts $time to a numeric value
#
	if( is_float($time + 0) ){
		list($ts, $ms) = explode('.', $time);
		parent::__construct(date('Y-m-d H:i:s.', $ts).$ms);
		$this->microseconds = $time - (int)$time;
		}
		else {
			throw new Exception('Incorrect value for time "' . print_r($time, true) . '"');
			}
}

################################################################################
#	setTimestamp(). Sets the current time stamp.
################################################################################
public function setTimestamp($timestamp)
{
	parent::setTimestamp($timestamp);
	$this->microseconds = $timestamp - (int)$timestamp;
}

################################################################################
#	getTimestamp(). Returns the current time stamp.
################################################################################
public function getTimestamp()
{
	return parent::getTimestamp() + $this->microseconds;
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
	if( !isset($GLOBALS['classes']['MicroDateTime']) ){
		$GLOBALS['classes']['MicroDateTime'] = new class_MicroDateTime();
		}

?>
