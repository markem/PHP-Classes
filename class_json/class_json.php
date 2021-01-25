<?php

	include_once( "../class_debug.php" );
################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_json();
#
#-Description:
#
#	A class to handle not only the JSON stuff but to also change keys into hex
#	values. It is VERY IMPORTANT to convert the keys also.
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
#	Mark Manning			Simulacron I			Mon 05/27/2019 19:03:25.71 
#		Original Program.
#
#	Mark Manning			Simulacron I			Sun 01/24/2021 23:30:46.28 
#	---------------------------------------------------------------------------
#	This code is now under the MIT License.
#
#END DOC
################################################################################
class class_json
{
	public $debug = false;

################################################################################
#	__construct(). Make the class.
################################################################################
function __construct()
{
	$args = func_get_args();
	$this->debug = new class_debug( func_get_args() );
	$this->debug->in();
	$this->debug->out();
}
################################################################################
#	html_getJSON().  A function to get all of the HTML JSON code.
#	Notes: Old JSON function to get any JSON information coming in from the
#		browser.
################################################################################
function html_getJSON( $json='json' )
{
	$this->debug->in();

//
//	grab JSON data if there...
//
	$params = null;
	if( isset($_REQUEST[$json]) ){
		$params =  json_decode( stripslashes($_REQUEST[$json]) );
		}
		else {
//
//	<-- Have to jump through hoops to get PUT data $raw  = '';
//
			$raw = "";
			$httpContent = fopen( 'php://input', 'r' );
			while( $kb = fread($httpContent, 1024) ){ $raw .= $kb; }
			fclose( $httpContent );

			$p = array();
			parse_str( $raw, $p );

			if( isset($p['data']) ){ $params =  json_decode( stripslashes($p['data']) ); }
				else {
					$p = json_decode( stripslashes($raw) );
					if( $p ){
						if( isset($p->data) ){ $params = $p->data; }
							else { $params = $p; }
						}
					}
			}

	if( is_null($params) ){ $params = array(); }

	$this->debug->out();

	return $params;
}
################################################################################
#	put_json(). Convert an array to a hex'd json string.
################################################################################
function put_json( $array )
{
	$this->debug->in();

	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = put_json($v); }
			else {
				$hex = bin2hex( $v );
				$hex = (((strlen($hex) % 2) < 1) ? "" : "0") . $hex;
				$array[$k] = "0x" . $hex;
				}
		}

	$this->debug->out();

	return json_encode( $array );
}
################################################################################
#	get_json(). Convert a hex'd json to a regular array.
################################################################################
function get_json( $json )
{
	$this->debug->in();

	static $first = true;
	if( $first ){ $array = json_decode( $json ); $first = false; }

	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = get_json($v); }
			else { $array[$k] = hex2bin( substr($v, 2, strlen($v)) ); }
		}

	$this->debug->out();

	return $array;
}
################################################################################
#	key_encode(). Convert all keys to their hex value.
#	Notes: CALL THIS FUNCTION >>>FIRST<<< so the keys are converted.
#	Notes: You can NOT just array_flip and change the keys because
#		some array entries might be an array and you can NOT have an array
#		be a key.
################################################################################
function key_encode( $array )
{
	$this->debug->in();

	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = key_encode( $v ); }
		$hex = bin2hex( $k );
		$hex = (((strlen($hex) % 2) < 1) ? "" : "0") . $hex;
		$array[$hex] = $v;
		unset( $array[$k] );
		}

	$this->debug->out();

	return $array;
}
################################################################################
#	key_decode(). Convert all keys back to their actual value.
#	Notes: CALL THIS FUNCTION >>>FIRST<<< Before unconverting the array.
#	Notes: You can NOT just array_flip and change the keys because
#		some array entries might be an array and you can NOT have an array
#		be a key.
################################################################################
function key_decode( $array )
{
	$this->debug->in();

	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = key_decode( $v ); }
		$bin = hex2bin( substr($k,2,strlen($k)) );
		$array[$bin] = $v;
		unset( $array[$k] );
		}

	$this->debug->out();

	return $array;
}
################################################################################
#	errmsg(). Print a message.
################################################################################
function errmsg( $func, $line, $msg )
{
	$this->debug->in();
	$this->debug->m( $msg );
	$this->debug->out();
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['json']) ){ $GLOBALS['classes']['json'] = new class_json(); }

?>
