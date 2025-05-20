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

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_statistics();
#
#-Description:
#
#	A class to handle all statistics. Taken from the Excel and one heck of a
#	lot of Googling in order to understand them.
#
#	Adapted to this PHP script by Mark E. Manning. See notes below.
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
#	Mark Manning			Simulacron I			Wed 10/16/2023 16:37:34.79 
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
#		CLASS_STATISTICS.PHP. A class to handle working with statistics.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_statistics
{
	private $temp_path = null;		#	A temporary file path if needed.

	private $Gdata = null;		#	If we need a global data area - here it is.
	private $Ldata = null;		#	If we need a local data area - here it is.

	private $Gquests = null;	#	Place for global questions
	private $Lquests = null;	#	Place for local questions
################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['statistics']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['statistics']; }
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

	$lib = getenv( "my_libs" );
	$lib = str_replace( "\\", "/", $lib );
	if( !file_exists($lib) ){ $lib = ".."; }

	$this->temp_path = "c:/temp/statistics";
	if( !file_exists($this->temp_path) ){
		mkdir( $this->temp_path, 0777, true );
		}

	if( file_exists("$lib/class_files.php") ){
		include_once( "$lib/class_files.php" );
		}
		else if( !isset($GLOBALS['classes']['files']) ){
			$this->debug->msg( __FILE__ . ": Can not load CLASS_FILES" );
			return false;
			}
}
################################################################################
#	average(). Add everything up and divide by how many entries there are.
#	NOTE :	NON-NUMERIC numbers are NOT used.
################################################################################
function average()
{
	$args = func_get_args();
	$argv = func_num_args();

	$cnt = 0;
	$avg = 0;
	for( $i=0; $i<$argv; $i++ ){
		$cv = $args[$i];
		if( is_array($cv) ){
			$ary = $cv;
			foreach( $ary as $k=>$v ){
				if( is_numeric($v) ){
					$cnt++;
					$avg += $v;
					}
				}
			}
			elseif( !is_null($cv) ){
				if( is_numeric($cv) ){
					$cnt++;
					$avg += $cv;
					}
				}
			else {}
		}

	return $avg / $cnt;
}
################################################################################
#	avedev(). Returns the average of the absolute deviations of data
#	points from their mean (see average() function). AVEDEV is a measure of the
#	variability in a data set.
#	NOTE :	NON-NUMERIC numbers are NOT used.
################################################################################
function avedev()
{
	$args = func_get_args();
	$argv = func_num_args();
#
#	First - we have to call the average() function to get the mean.
#
	$mean = $this->average( $args );

	$cnt = 0;
	$avg = 0;
	for( $i=0; $i<$argv; $i++ ){
		$cv = $args[$i];
		if( is_array($cv) ){
			$ary = $cv;
			foreach( $ary as $k=>$v ){
				if( is_numeric($v) ){
					$cnt++;
					$avg += abs( $v - $mean );
					}
				}
			}
			elseif( !is_null($cv) ){
				if( is_numeric($cv) ){
					$cnt++;
					$avg += abs( $cv- $mean );
					}
				}
			else {}

		}

	return $avg / $cnt;
}
################################################################################
#	count(). Returns how many items there are.
#	NOTE : This routine returns and ARRAY. First is the numeric count and second
#		is the NON-numeric count.
################################################################################
function count()
{
	$args = func_get_args();
	$argv = func_num_args();

	$a = 0;
	$b = 0;
	for( $i=0; $i<$argv; $i++ ){
		$cv = $args[$i];
		if( is_array($cv) ){
			$ary = $cv;
			foreach( $ary as $k=>$v ){
				if( is_numeric($v) ){ $a++; }
					else { $b++; }
				}
			}
			elseif( !is_null($cv) ){
				if( is_numeric($v) ){ $a++; }
					else { $b++; }
				}
			else { $b++; }

		}

	return array( $a, $b );
}
################################################################################
#	max(). Returns the maximum NUMERIC value.
#	NOTE : NON-numeric values causes a FALSE value return.
################################################################################
function max()
{
	$args = func_get_args();
	$argv = func_num_args();

	$a = null;
	for( $i=0; $i<$argv; $i++ ){
		$cv = $args[$i];
		if( is_array($cv) ){
			$ary = $cv;
			foreach( $ary as $k=>$v ){
				if( is_numeric($v) ){
					if( is_null($a) || ($a < $cv) ){ $a = $cv; }
					}
					else { return false; }
				}
			}
			elseif( !is_null($cv) ){
				if( is_numeric($cv) ){
					if( is_null($a) || ($a < $cv) ){ $a = $cv; }
					}
					else { return false; }
				}
			else { return false; }

		}

	return $a;
}
################################################################################
#	min(). Returns the minimum NUMERIC value.
#	NOTE : NON-numeric values causes a FALSE value return.
################################################################################
function min()
{
	$args = func_get_args();
	$argv = func_num_args();

	$a = null;
	for( $i=0; $i<$argv; $i++ ){
		$cv = $args[$i];
		if( is_array($cv) ){
			$ary = $cv;
			foreach( $ary as $k=>$v ){
				if( is_numeric($v) ){
					if( is_null($a) || ($a > $cv) ){ $a = $cv; }
					}
					else { return false; }
				}
			}
			elseif( !is_null($cv) ){
				if( is_numeric($cv) ){
					if( is_null($a) || ($a > $cv) ){ $a = $cv; }
					}
					else { return false; }
				}
			else { return false; }

		}

	return $a;
}
################################################################################
#	test(). Test out all functions.
################################################################################
function test()
{
	echo "Average (1,2,3...) = " . $this->average( 99,33,4,null,56,98 ) . "\n";
	echo "Average Deviation = " . $this->avedev( 5,99, 2, 23, 41, 67 ) . "\n";
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['statistics']) ){
		$GLOBALS['classes']['statistics'] = new class_statistics();
		}

?>


