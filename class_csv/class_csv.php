<?php

	if( file_exists("../class_debug.php") ){
		include_once( "../class_debug.php" );
		}
		else if( !isset($GLOBALS['classes']['debug']) ){
			die( "Can not load CLASS_DEBUG" );
			}
		else {
			die( "Can not load CLASS_DEBUG" );
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
#	Mark Manning			Simulacron I			Sun 01/24/2021 23:24:52.28 
#	---------------------------------------------------------------------------
#	Now under the MIT License.
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
	$this->debug->init( func_get_args() );
	$this->debug->in();
	$this->debug->out();
}
################################################################################
#	@link http://gist.github.com/385876
################################################################################
function get_csv($filename='', $delimiter=',')
{
	$this->debug->in();

    if(!file_exists($filename) || !is_readable($filename)){
		$this->errmsg( __FUNCTION__, __LINE__, " >>> The filename is Not there.<br>\n" );
		}

	$data = array();

	$fh = fopen( $filename, "r" );
	for($i=0; $data[]=fgetcsv( $fh, $delimiter ); ++$i){}
	fclose( $fh );

	array_pop( $data );

	$this->debug->out();

	return $data;
}
################################################################################
#	Function to write out a CSV file
################################################################################
function put_csv($filename='', $array=null, $delimiter=',')
{
	$this->debug->in();

	$c = count( $array );

	if( !isset($array) || is_null($array) || $c < 1 ){
		$this->errmsg( __FUNCTION__, __LINE__, " >>> The array is blank.<br>\n" );
		}

	$fh = fopen( $filename, "w" );

	if( $fh == false ){
		$this->errmsg( __FUNCTION__, __LINE__, " >>> The filename is Not there.<br>\n" );
		}

	for($i=0; $i<$c; $i++ ){
		fputcsv( $fh, $array[$i], $delimiter );
		}

	fclose( $fh );

	$this->debug->out();

	return $data;
}

}

?>
