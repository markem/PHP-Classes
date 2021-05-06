<?php

#
#	$lib is where my libraries are located. Change this to whereever
#	you are keeping them.
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
#	Mark Manning			Simulacron I			Sun 01/24/2021 23:36:12.95 
#	---------------------------------------------------------------------------
#	This code is now under the MIT License.
#
#END DOC
################################################################################
class MicroDateTime extends DateTime
{
    public $microseconds = 0;

public function __construct($time = 'now')
{
	$this->debug = $GLOBALS['classes']['debug'];
	$this->debug->init( func_get_args() );

	if ($time == 'now')
		$time = microtime(true);
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

public function setTimestamp($timestamp)
{
	parent::setTimestamp($timestamp);
	$this->microseconds = $timestamp - (int)$timestamp;
}

public function getTimestamp()
{
	return parent::getTimestamp() + $this->microseconds;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['time']) ){ $GLOBALS['classes']['time'] = new class_time(); }

?>
