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
#	class_rnd();
#
#-Description:
#
#	Do class_rnd numbers.
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
#	Mark Manning			Simulacron I			Sat 05/04/2019  0:58:43.83 
#		Original Program.
#
#
#END DOC
################################################################################
class class_rnd
{
	private $debug_flag = false;

	private $rnd = null;
	private $mult = 5432109876.0;

################################################################################
#	__construct(). Init the functions.
#
#	Arguments:
#
#		d(ebug)	:	Turn on debug
#		s(ave)	:	Turn on saving the debug info
#		seed	:	Send a seed over. Usage : seed=<Number>
################################################################################
public function __construct()
{
	$args = func_get_args();
	$this->debug = $GLOBALS['classes']['debug'];
	$this->debug->init( func_get_args() );
	$this->debug->in();

	foreach( $args as $k=>$v ){
		$a = explode( '=', $v );
		if( preg_match("/seed/i", $a[0]) ){ $seed = $a[1]; }
		}

	if( (is_null($seed) || is_NAN($seed) || strlen(trim($seed)) < 1) && is_null($this->rnd) ){
		$this->rnd = microtime( true );
		}
		else if( !is_null($seed) ){ $this->rnd = $seed; }

	$this->debug->out();
	return true;
}
################################################################################
#	rand($seed). Get a pseudo random number.
################################################################################
public function rand()
{
	$this->debug->in();

	$this->rnd = abs( sin($this->rnd) * $this->mult );
	$this->rnd = abs( log10($this->rnd) * $this->mult );
	$this->rnd = abs( cos($this->rnd) * $this->mult );
	$this->rnd = abs( log($this->rnd) );

	$this->rnd = abs( $this->rnd - floor($this->rnd) );

	$this->debug->out();
	return $this->rnd;
}
################################################################################
#	rnd($low,$high). A random number generator.
################################################################################
public function rnd( $n=null, $low=null, $high=null )
{
	$this->debug->in();

	if( is_null($low) ){ $low = PHP_INT_MIN; }
	if( is_null($high) ){ $high = PHP_INT_MAX; }
	if( is_null($n) || (!is_null($low) && !is_null($high)) ){ $n = 1; }

	$this->debug->out();
	return abs(random_int( $low, $high ) % $n);
}
################################################################################
#	irnd($n1,$n2,$n3). An integer random number generator.
################################################################################
public function irnd( $n1=null, $n2=null, $n3=null )
{
	$this->debug->in();

	if( is_null($n1) ){ $n = abs(random_int( PHP_INT_MIN, PHP_INT_MAX ) ); }
		else if( is_null($n2) ){ $n = abs(random_int( PHP_INT_MIN, PHP_INT_MAX ) % $n1 ); }
		else if( is_null($n3) ){ $n = abs(random_int( PHP_INT_MIN, PHP_INT_MAX ) % $n1 ) + $n2; }
		else { $n = (abs(random_int( PHP_INT_MIN, PHP_INT_MAX ) % $n1 ) + $n2) * $n3; }

	$this->debug->out();
	return $n;
}
################################################################################
#	frnd($n1,$n2). A floating point random number generator.
################################################################################
public function frnd( $n1=null, $n2=null )
{
	$this->debug->in();
#
#	Gives basic 0.0-1.0 number.
#
	if( is_null($n1) ){
		$n = (float)(abs((float)random_int(PHP_INT_MIN, PHP_INT_MAX ) / (float)PHP_INT_MAX) );
		}
#
#	Gives (0.0-1.0) * N1
#
		else if( is_null($n2) ){
			$n = (float)(abs((float)random_int(PHP_INT_MIN, PHP_INT_MAX ) / (float)PHP_INT_MAX) * (float)$n1 );
			}
#
#	Gives 0.0-1.0 * abs(N1-N2) + min(N1, N2).
#
		else {
			$n = (float)(abs((float)random_int(PHP_INT_MIN, PHP_INT_MAX )) / (float)PHP_INT_MAX);
			$m = $n * (abs($n1) - abs($n2) );
			if( $n1 > $n2 ){ $n = $m + $n2; }
				else { $n = $m + $n1; }
			}

	$this->debug->out();
	return $n;
}
#
#	Taken from the PHP documentation website.
#
#	Kristof_Polleunis at yahoo dot com
#
#	A guid function that works in all php versions:
#	MEM 3/30/2015 : Modified the function to allow someone
#		to specify whether or not they want the curly
#		braces on the GUID.
#
#	Set $opt to true/false as your default way to do this.
#
################################################################################
#	guid(). Create a GUID.
################################################################################
public function guid( $opt = false )
{
	$this->debug->in();
	if( function_exists('com_create_guid') ){
		if( $opt ){
			$this->debug->out();
			return com_create_guid();
			}
			else {
				$this->debug->out();
				return trim( com_create_guid(), '{}' );
				}
		}
		else {
			mt_srand( (double)microtime() * 10000 );	// optional for php 4.2.0 and up.
			$charid = strtoupper( md5(uniqid($this->rnd(), true)) );
			$hyphen = chr( 45 );	// "-"
			$left_curly = $opt ? chr(123) : "";		//	"{"
			$right_curly = $opt ? chr(125) : "";	//	"}"
			$uuid = $left_curly
				. substr( $charid, 0, 8 ) . $hyphen
				. substr( $charid, 8, 4 ) . $hyphen
				. substr( $charid, 12, 4 ) . $hyphen
				. substr( $charid, 16, 4 ) . $hyphen
				. substr( $charid, 20, 12 )
				. $right_curly;

			$this->debug->out();
			return $uuid;
			}

	$this->debug->out();
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['rnd']) ){ $GLOBALS['classes']['rnd'] = new class_rnd(); }

?>

