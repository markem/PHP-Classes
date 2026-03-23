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
#	class_hexd();
#
#-Description:
#
#	Handle my HEXD code.
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
#	Mark Manning			Simulacron I			Sat 02/15/2025 17:14:36.37
#		Original Program.
#
#	Mark Manning			Simulacron I			Sat 07/17/2021 14:56:52.53 
#	---------------------------------------------------------------------------
#		REMEMBER! We are now following the PHP code of NOT killing the program
#		but instead always setting a DEBUG MESSAGE and returning FALSE. So I'm
#		getting rid of all of the DIE() calls.
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
#		CLASS_FILES.PHP. A class to handle working with files.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_hexd
{
	private $base64 = null;
	private $hex = null;
	private $pr = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['files']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['files']; }
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

	$this->pr = new class_pr();
#
#	Get our base 62 info
#
	$base64 = "0123456789";
	for( $i=0; $i<26; $i++ ){ $base64 .= chr(ord("A")+$i); }
	for( $i=0; $i<26; $i++ ){ $base64 .= chr(ord("a")+$i); }
	$base64 .= "=+";

	$this->base64 = $base64;
#
#	Set up $hex
#
	$this->hex = "0123456789ABCDEF";
}
################################################################################
#	encode(). Create the hexd stuff
#	NOTES : The $encrypt value is either a TRUE or FALSE.
################################################################################
function encode( $ary=null, $encrypt=false )
{
	$pr = $this->pr;
	if( is_null($ary) ){ die( "***** ERROR : VALUE is NULL\n" ); }

	$pr->pr( "Calling VALUES()" );
	$hexd = $this->values( $ary, $encrypt );
	$pr->pr( "Calling KEYS()" );
	$hexd = $this->keys( $hexd, $encrypt );

	return $hexd;
}
################################################################################
#	values(). Convert all strings to hexed
################################################################################
function values( $val=null, $encrypt=false )
{
	$pr = $this->pr;
	$pr->pr( $val, "VAL #1 = " );

	if( is_array($val) ){
		foreach( $val as $k=>$v ){
			$pr->pr( "Here" );
			if( is_array($v) ){ $v = $this->values( $v ); }
				else { $v = $this->encrypt( $v, $encrypt ); }

			$pr->pr( $v, "v = " );
			$val[$k] = $v;
			}
		}
		else { $val = $this->encrypt( $val, $encrypt ); }

	$pr->pr( $val, "VAL #2 = " );
	return $val;
}
################################################################################
#	keys(). Convert all keys to being hexed
################################################################################
function keys( $ary=null, $encrypt=false )
{
	$str = "";
	if( is_array($ary) ){
		foreach( $ary as $k=>$v ){
			if( is_array($v) ){ $v = $this->keys( $v ); }

			if( $v == "00x" ){ $v = "00x00"; }
			$str = "[" . $this->encrypt( $k, $encrypt ) . ":$v]";
			}
		}
		else {
			$str = "[" . $this->encrypt( "", $encrypt ) . ":$ary]";
			}

	return $str;
}
################################################################################
#	decode(). Restore the array.
################################################################################
function decode( $str=null )
{
	$dq = '"';
	$pr = $this->pr;
#
#	This is NOT recursive. Instead, we keep track of where we are with a variable
#	called LEVEL. LEVEL is an array which keeps track of where we are in the
#	array we are creating. Each "A:" means ADD on that key. Each "]" means
#	to DECREMENT LEVEL by one level.
#
#	The array to make is ARY
#
	$ary = [];
#
#	Where we are in the levels of the array. This is a pop-up - push-down stack.
#
	$level = [];
#
#	Now split everything up into our parts
#
	$b = explode( '[', $str );
#
#	Check to make sure the first entry is not blank. If so - get rid of it.
#
	if( isset($b[0]) && (strlen($b[0]) < 1) ){ $c = array_shift( $b ); }

	foreach( $b as $k=>$v ){
		$count = 0;
		$v = str_replace( ']', '', $v, $count );
		$a = explode( ':', $v );
#
#	Get rid of the ##x I put there to mark this as a hexadecimal value.
#	Because the two digits are hexadecimal - they can be 0-f. So I am using just
#	the dot(.) to say there is a character in that position.
#
		if( preg_match("/^..x/i", $a[0]) ){ $hex = $ch->decrypt( $a[0] ); }
			else { $hex = $a[0]; }

		$pr->pr( $hex, "HEX =" );

		if( strlen($hex) < 2 ){ $hex = "00"; }
			else if( (strlen($hex) % 2) > 0 ){ $hex = "0$hex"; }

		$key = hex2bin( $hex );
		$level[] = $key;
		if( isset($a[1]) && (strlen($a[1]) > 0) ){
#
#	Start the ARY command.
#
			$cmd = "\$ary[";
#
#	Now add in all of the keys
#
			foreach( $level as $k1=>$v1 ){ $cmd .= "'$v1']["; }
			$cmd = substr( $cmd, 0, -1 );
#
#	Now remove the last "[".
#
#print_r( $a ); echo "\n";
			if( preg_match("/^..x/i", $a[1]) ){ $hex = $ch->decrypt( $a[1] ); }
				else { $hex = $a[1]; }

#echo "Hex = $hex\n";
			if( preg_match("//", $hex) ){ $hex = "NULL"; }
				else { $val = hex2bin( $hex ); }

			$pr->pr( $val, "VAL = " );
			if( $val == " " ){ $val = "NULL"; }
			$val = str_replace( '"', '', $val );

			$cmd = "$cmd = $dq$val$dq;";
#			echo "LINE = " . __LINE__ . " : Command = $cmd\n";
			eval( $cmd );
#
#	Now remove any keys we need to remove
#
			for( $i=0; $i<$count; $i++ ){ array_pop( $level ); }
			}
		}
#
#	Now remove any REAL NULL entries. In other words - not the ones we
#	put the word "NULL" into but an entry that if you say is_null() on
#	it OR if the entry is absolutely empty (ie: "") AND it is the last
#	entry - then get rid of it.
#
	return $ary;
}
################################################################################
#	encrypt(). Encrypts incoming strings.
#
#	NOTES	:	Here is what we do:
#		Front	:
#			1.	Get two random numbers between zero(0) and three(3). Use the number
#				gotten times whatever the value is for the high/low bytes.
#			2.	Get a randdom number between zero(0) and base64. E = Do nothing
#				O = Switch high/low bytes.
#		Back	:
#			1.	
################################################################################
function encrypt( $str=null, $encrypt=false )
{
	$pr = $this->pr;
	$hex = $this->hex;
	$base64 = $this->base64;
	$pr->pr( "HERE" );
#
#	If they do not want it to be encrypted then just send that back.
#
	if( is_null($str) || (strlen($str) < 1) ){ $str = ""; }
	if( $encrypt === false ){ $str = "0x" . bin2hex( $str ) ."00"; }
#
#	Now we need to get a truly random number to encrypt the incoming
#	information.
#
	$rnd1 = mt_rand( 0, 3 ) * 16;
	$rnd2 = mt_rand( 0, 3 ) * 16;
#
#	Set up the original value. Just like above.
#
	$s = bin2hex( $str );
#
#	Get the new length of the string.
#
	$len = strlen( $s );
#
#	Now change all of the numbers to the new values
#
	$new = "";
	for( $i=0; $i<$len; $i+=2 ){
		$new .= substr( $base64, stripos($hex, substr($s, $i, 1)) + $rnd1, 1 );
		$new .= substr( $base64, stripos($hex, substr($s, $i+1, 1)) + $rnd2, 1 );
		}
#
#	Save the above
#
	$s = $new;
#
#	Now figure out if we should switch things around.
#
	$new = "";
	$rnd1 = mt_rand( 1, 62 );
	if( ($rnd1 % 2) > 0 ){
		for( $i=0; $i<$len; $i+=2 ){
			$n1 = substr( $s, $i, 1 );
			$n2 = substr( $s, $i+1, 1 );
			$new .= $n2 . $n1;
			}
		}

	$opt1 = substr( $base64, $rnd1, 1);
#
#	Now determine which column should be subtracted from 64.
#	0 = neither, 1=first, 2=second, 3=both
#
	$new = "";
	$rnd1 = mt_rand( 0, 63 ) + 1;
	if( ($rnd1 % 4) < 1 ){ $new = $s; }
		else if( ($rnd1 % 4) < 2 ){
			for( $i=0; $i<$len; $i+=2 ){
				$n1 = 64 - ord( substr($s, $i, 1) );
				$n2 = substr( $s, $i+1, 1 );
				$new .= $n1 . $n2;
				}
			}
		else if( ($rnd1 % 4) < 3 ){
			for( $i=0; $i<$len; $i+=2 ){
				$n1 = substr( $s, $i, 1 );
				$n2 = 64 - ord( substr($s, $i+1, 1) );
				$new .= $n1 . $n2;
				}
			}
		else {
			for( $i=0; $i<$len; $i+=2 ){
				$n1 = 64 - ord( substr($s, $i, 1) );
				$n2 = 64 - ord( substr($s, $i+1, 1) );
				$new .= $n1 . $n2;
				}
			}

	$opt2 = substr( $base64, $rnd1, 1);

	$s = "0x" . $new . "x" . $opt1 . $opt2;

	return $s;
}
################################################################################
#	decrypt(). Decrypts incoming strings.
################################################################################
function decrypt( $str=null )
{
	$base64 = $this->base64;
#
#	Remove the "0x" from in front of the string.
#
	$str = substr( $str, 2, strlen($str) );
#
#	Now remove the two digits at the end. These are our options.
#
	$opts = substr( $str, 0, -2 );
	$opt1 = substr( $opts, 0, 1 );
	$opt2 = substr( $opts, 1, 1 );
#
#	Undo the 64 - x
#
	$new = "";
	$opt2 = strpos( $base64, $opt2 );
	if( ($opt2 % 4) < 1 ){ $new = $s; }
		else if( ($opt2 % 4) < 2 ){
			for( $i=0; $i<$len; $i+=2 ){
				$n1 = 64 - ord( substr($s, $i, 1) );
				$n2 = substr( $s, $i+1, 1 );
				$new .= $n1 . $n2;
				}
			}
		else if( ($opt2 % 4) < 3 ){
			for( $i=0; $i<$len; $i+=2 ){
				$n1 = substr( $s, $i, 1 );
				$n2 = 64 - ord( substr($s, $i+1, 1) );
				$new .= $n1 . $n2;
				}
			}
		else {
			for( $i=0; $i<$len; $i+=2 ){
				$n1 = 64 - ord( substr($s, $i, 1) );
				$n2 = 64 - ord( substr($s, $i+1, 1) );
				$new .= $n1 . $n2;
				}
			}
#
#	Save the above
#
	$s = $new;
#
#	Now figure out if we should switch things around.
#
	$new = "";
	$opt1 = strpos( $base64, $opt1 );
	if( ($opt1 % 2) > 0 ){
		for( $i=0; $i<$len; $i+=2 ){
			$n1 = substr( $s, $i, 1 );
			$n2 = substr( $s, $i+1, 1 );
			$new .= $n2 . $n1;
			}
		}
#
#	Save the above
#
	$s = $new;
#
#	Last conversion
#
	$new = "";
	for( $i=0; $i<$len; $i++ ){
		$new .= strpos( $base64, substr($s, $i, 1) ) % 16;
		}

	return $s;
}

}

?>
