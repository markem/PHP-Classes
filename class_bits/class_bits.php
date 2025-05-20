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

	ini_set( 'memory_limit', -1 );
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
#	class_bits();
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
#	Mark Manning			Simulacron I			Tue 03/04/2025 14:13:01.31
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
class class_bits
{
	private $reg_a = null;		#	The first register
	private $reg_b = null;		#	The second register
	private $bit_length = null;	#	How long is each variable/register

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
	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$this->vars = [];
	$this->regs = [];
	$this->bit_length = 32;
}
################################################################################
#	set_length(). Set how large the variables/registers are
################################################################################
function set_length( $len=32 )
{
	$this->bit_length = $len;
	return true;
}
################################################################################
#	get_length(). Gets how long our variables are
################################################################################
function get_lenght()
{
	return $this->bit_length;
}
################################################################################
#	puta(). Store the value into the A registry
################################################################################
function puta( $val=null, $opt=false )
{
	if( is_null($val) ){ $this->dump("***** ERROR : VAL is NULL", true); }
#
#	Get the incoming value as a binary value.
#
	$str = "0-";
	$s = decbin( $val );
	$len = $this->bit_length;
#
#	Now convert it to "#-" so we have our binary value
#	A string will look like this: #-#-#-#-...#-#-#
#	This means we have a BLANK at both the last and first
#	entry. This is so when we shift something I can get the
#	bit that popped off.
#
	for( $i=0; $i<$len; $i++ ){
		$str .= substr( $s, $i, 1 ) . "-";
		}

	$str .= "0";
#
#	Now save it
#
	if( $opt ){ $this->reg_b = $val; }
		else { $this->reg_a = $val; }

	return true;
}
################################################################################
#	putb(). Store a value into the B registry
################################################################################
function putb( $val=null )
{
	$this->puta( $val, true );
}
################################################################################
#	add(). Add the two registry's. The result is just returned. YOU must then
#		store it whereever you want it to go.
################################################################################
function add()
{
	$a = $this->reg_a;
	$b = $this->reg_b;
	$len = $this->bit_length;

	$a = explode( '-', $a );
	$b = explode( '-', $b );

	$ans = [];

	for( $i=0; $i<$len; $i++ ){
		$ans[$i] = $a[$i] + $b[$i];
		}

	for( $i=($len-1); $i>0; $i++ ){
		if( $ans[$i] > 1 ){
			$ans[$i] = 0;
			$ans[$i-1] += 1;
			}
		}
#
#	Change the number back in to a regular variable
#
	$sign = array_shift( $ans );
	$last = array_pop( $ans );

	$ans = implode( '-', $ans );
	$dec = bindec( $ans );

	return array( $sign, $dec, $last );
}
#################################################################################
#	or(). Do an OR on the two registerys
#################################################################################
function or()
{
	$a = $this->reg_a;
	$b = $this->reg_b;
	$len = $this->bit_length;

	$a = explode( '-', $a );
	$b = explode( '-', $b );

	$ans = [];

	for( $i=0; $i<$len; $i++ ){
		$ans[$i] = $a[$i] | $b[$i];
		}
#
#	Change the number back in to a regular variable
#
	$sign = array_shift( $ans );
	$last = array_pop( $ans );

	$ans = implode( '-', $ans );
	$dec = bindec( $ans );

	return array( $sign, $dec, $last );
}
#################################################################################
#	and(). Do an OR on the two registerys
#################################################################################
function and()
{
	$a = $this->reg_a;
	$b = $this->reg_b;
	$len = $this->bit_length;

	$a = explode( '-', $a );
	$b = explode( '-', $b );

	$ans = [];

	for( $i=0; $i<$len; $i++ ){
		$ans[$i] = $a[$i] & $b[$i];
		}
#
#	Change the number back in to a regular variable
#
	$sign = array_shift( $ans );
	$last = array_pop( $ans );

	$ans = implode( '-', $ans );
	$dec = bindec( $ans );

	return array( $sign, $dec, $last );
}
#################################################################################
#	xor(). Do an OR on the two registerys
#################################################################################
function xor()
{
	$a = $this->reg_a;
	$b = $this->reg_b;
	$len = $this->bit_length;

	$a = explode( '-', $a );
	$b = explode( '-', $b );

	$ans = [];

	for( $i=0; $i<$len; $i++ ){
		$ans[$i] = $a[$i] ^ $b[$i];
		}
#
#	Change the number back in to a regular variable
#
	$sign = array_shift( $ans );
	$last = array_pop( $ans );

	$ans = implode( '-', $ans );
	$dec = bindec( $ans );

	return array( $sign, $dec, $last );
}
#################################################################################
#	flip(). Do an OR on the two registerys
#################################################################################
function flipa( $opt=false )
{
	if( $opt ){ $var = $this->reg_b; }
		else { $var = $this->reg_a; }

	$var = explode( '-', $var );
	$len = $this->bit_length;

	$ans = [];

	for( $i=0; $i<$len; $i++ ){
		$ans[$i] = ($var[$i] + 1) % 2;
		}
#
#	Change the number back in to a regular variable
#
	$sign = array_shift( $ans );
	$last = array_pop( $ans );

	$ans = implode( '-', $ans );
	$dec = bindec( $ans );

	return array( $sign, $dec, $last );
}
#################################################################################
#	csr(). Do a Circular Shift Right for the A registry
#	NOTE : You can turn off saving the new value back into the A registry
#		by setting $SAVE to FALSE. Default is TRUE.
#
#	Example:
#				Original Number Store in $reg_a
#				L-#-#-#-#-R
#				0-0-1-0-1-0
#
#				Number shifted to the RIGHT
#				>->->->->->
#				L-#-#-#-#-R
#				0-0-0-1-0-1
#
#				Then we move the RIGHT bit to the lowest number (ie: L+1 location)
#				L-#-#-#-#-R
#				0-1-0-1-0-1
#
#				Finally we clear the RIGHT bit
#				L-#-#-#-#-R
#				0-1-0-1-0-0
#
#	NOTES:
#		Note that the LEFT bit does NOT change. This is because we are going
#		that (->) way with the bits.
#
#		Note also that the RIGHT bit is ALWAYS set to zero(0) AFTER we are
#		through doing the CSR.
#
#		Last, but not least, the "L" stands for LEFT and the "R" stands for RIGHT.
#
#################################################################################
function csra( $save=true, $opt=false )
{
	if( $opt ){ $var = $this->reg_b; }
		else { $var = $this->reg_a; }

	$var = explode( '-', $var );
	$len = $this->bit_length;

	$ans = [];
#
#	First slide everything over by one to the right.
#
	for( $i=$len-1; $i>1; $i-- ){
		$ans[$i] = $var[$i-1];
		}
#
#	Now put whatever is in the last position to the first
#	position in the answer and clear the last position.
#
	$ans[1] = $var[$len-2];
#
#	Clear out the RIGHT bit
#
	$ans[$len-1] = 0;
#
#	Now save the answer into the correct registry area
#
	$var = implode( '-', $ans );
	if( $opt ){ $this->reg_b = $var; }
		else { $this->reg_a = $var; }
#
#	Change the number back in to a regular variable
#
	$left_bit = array_shift( $ans );
	$right_bit = array_pop( $ans );

	$dec = implode( '', $ans );
	$dec = bindec( $dec );

	return array( $left_bit, $dec, $right_bit );
}
#################################################################################
#	csrb(). Do a Circular Shift Right for the B registry
#################################################################################
function csrb( $save=true )
{
	$this->csra( $save, true );
}
#################################################################################
#	csl(). Do a Circular Shift Left for the A registry
#
#	Example:
#				Original Number Store in $reg_a
#				L-#-#-#-#-R
#				0-1-0-1-0-0
#
#				Number shifted to the left
#				L-#-#-#-#-R
#				1-0-1-0-0-0
#
#				Then we move the L)eft bit to the $LEN-2 position.
#				L-#-#-#-#-R
#				1-0-1-0-1-0 <--$LEN
#						^ ^
#						| +-> $LEN-1
#						+---> $LEN-2
#
#	NOTES	:	PHP array numbers start at ZERO. The COUNT() function returns
#		however many entries there are in the array counting from one(1).
#		This might seem stupid but is actually very smart.
#
#		Note that the SIGN bit stays however it winds up from this operation.
#		That is because we are going that (<-) way. So the SIGN bit changes
#		over time and can make your resulting DECIMAL number be negative.
#
#		Also note that whatever the SIGN bit WAS - it is lost as the second
#		bit writes over it.
#
#		Last, but not least, the "L" stands for LEFT and the "R" stands for RIGHT.
#
#################################################################################
function csla( $save=true, $opt=false )
{
	if( $opt ){ $var = $this->reg_b; }
		else { $var = $this->reg_a; }

	$var = explode( '-', $var );
	$len = $this->bit_length;

	$ans = [];
#
#	First slide everything over by one to the left.
#
	for( $i=0; $i<$len-1; $i++ ){
		$ans[$i] = $var[$i+1];
		}
#
#	Now get the bit that slid off the end and put it back on.
#
	$ans[$len-2] = $ans[0];
#
#	Clear the RIGHT bit
#
	$ans[$len-1] = 0;
#
#	Now save the answer into the correct registry area
#
	$var = implode( '-', $ans );
	if( $opt ){ $this->reg_b = $var; }
		else { $this->reg_a = $var; }
#
#	Change the number back in to a regular variable
#
	$left_bit = array_shift( $ans );
	$right_bit = array_pop( $ans );

	$dec = implode( '', $ans );
	$dec = bindec( $dec );

	return array( $left_bit, $dec, $right_bit );
}
#################################################################################
#	cslb(). Do a Circular Shift Left for the B registry
#################################################################################
function cslb( $save=true )
{
	$this->csla( $save, true );
}
#################################################################################
#	multi_csla(). Call the csla() function multiple times.
#################################################################################
function multi_csla( $num=null, $save=true, $opt=false )
{
	if( is_null($num) ){ $num = 1; }

	for( $i=0; $i<$num; $i++ ){
		$ret = $this->csla( $save, $opt );
		}

	return $ret;
}
#################################################################################
#	multi_cslb(). Call the csla() function multiple times.
#################################################################################
function multi_cslb( $num=null, $save=true )
{
	return $this->multi_csla( $num, $save, true );
}
#################################################################################
#	multi_csra(). Call the csra() function multiple times.
#################################################################################
function multi_csra( $num=null, $save=true, $opt=false )
{
	if( is_null($num) ){ $num = 1; }

	for( $i=0; $i<$num; $i++ ){
		$ret = $this->csra( $save, $opt );
		}

	return $ret;
}
#################################################################################
#	multi_csrb(). Call the csra() function multiple times.
#################################################################################
function multi_csrb( $num=null, $save=true )
{
	return $this->multi_csra( $num, $save, true );
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['bits']) ){
		$GLOBALS['classes']['bits'] = new class_bits();
		}

?>
