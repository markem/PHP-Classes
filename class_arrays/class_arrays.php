<?php

#
#	$lib is where my libraries are located. Change this to whereever
#	you are keeping them.
#
	$lib = getenv( "my_libs");
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
#
#END DOC
################################################################################
function class_arrays
{

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct(){ $this->init( func_get_args() ); }
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
function init()
{
#
#	Arguments are looked at HERE. Don't put them in!
#
	$args = func_get_args();
	$this->debug = $GLOBALS['classes']['debug'];
	$this->debug->init( $args );
	$this->debug->in();

	$this->debug->out();
}
################################################################################
#	array_search(). Find ANY key (UPPER/lower/whatever)
#	Example : $this->array_search( "MyNeedle.*5", $myArray );
#		Would find "MyNeedle0000055" as a key. Note ANY regular expression
#		could be used.
################################################################################
function array_isearch( $needles=null, $haystack=null )
{
	if( is_null($needles) ){ $this->debug->die( "NEEDLE is null", true ); }
	if( is_null($haystack) ){ $this->debug->die( "HAYSTACK is null", true ); }
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
#	__destruct(). The class destruct function.
################################################################################
function __destruct()
{
	$this->debug->in();
	$this->debug->out();
}
################################################################################
#	dump(). A short function to dump a file.
################################################################################
function dump( $f=null, $l=null )
{
	$this->debug->in();

	if( is_null($f) ){ $this->debug->log( "DIE : No file given", true ); }
	if( is_null($l) ){ $l = 32; }

	$fh = fopen($f, "r" );
	$r = fread( $fh, 1024 );
	fclose( $fh );

	$this->debug->m( "Dump	: " );
	for ($i = 0; $i < $l; $i++) {
		$this->debug->m( str_pad(dechex(ord($r[$i])), 2, '0', STR_PAD_LEFT) );
		}

	$this->debug->m( "\nHeader  : " );
	for ($i = 0; $i < 32; $i++) {
		$s = ord( $r[$i] );
		$s = ($s > 127) ? $s - 127 : $s;
		$s = ($s < 32) ? ord(" ") : $s;
		$this->debug->m( chr( $s ) );
		}

	$this->debug->m( "\n" );

	$this->debug->out();

	return true;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['files']) ){ $GLOBALS['classes']['files'] = new class_files(); }

?>
