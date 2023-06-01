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
#	class_business_statistics();
#
#-Description:
#
#	A class to handle all business statistics. Taken from the book series:
#
#		Core Business Program
#			Business Statistics
#			by Wilfried R. Vanhonacker, Ph. D.
#				Assistant Professor
#				Graduate School of Business
#				Columbia University
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
#	Mark Manning			Simulacron I			Wed 09/15/2021 16:37:34.79 
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
#		CLASS_BUSINESS.PHP. A class to handle working with business things.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_business_statistics
{
	private $debug = null;			#	Debug information.
	private $temp_path = null;		#	A temporary file path if needed.

	private $Gdata = null;	#	If we need a global data area - here it is.
	private $Ldata = null;		#	If we need a local data area - here it is.

	private $Gquests = null;	#	Place for global questions
	private $Lquests = null;	#	Place for local questions
################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	$this->debug = $GLOBALS['classes']['debug'];
	if( !isset($GLOBALS['class']['business_statistics']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['business_statistics']; }
}
################################################################################
#	init(). Used instead of __construct() so you can re-init() if necessary.
################################################################################
function init()
{
	$this->debug->in();

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$lib = getenv( "my_libs" );
	$lib = str_replace( "\\", "/", $lib );
	if( !file_exists($lib) ){ $lib = ".."; }

	$this->temp_path = "c:/temp/business_statistics";
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

	$this->debug->out();
}
################################################################################
#	am(). Arithmetic mean. You can send multiple ams to be computed.
#	Call:	$cbs->am( <data> );
#	Return:	An array which will contain all ams.
################################################################################
function am()
{
	$args = func_get_args();
	$argv = func_num_args();
	$this->debug->in();

print_r( $args );
print_r( $argv );

	$ams = [];
	for( $av=0; $av<$argv; $av++ ){
		if( is_array($args[$av]) ){
			$s = 0;
			$ary = $args[$av];
			$c = count( $ary );
			foreach( $ary as $k=>$v ){
				$s += $v + 0;
				}

			$ams[] = $s / $c;
			}
		}

	$this->debug->out();
	return $ams;
}
################################################################################
#	wm(). Weighted mean.
#	Call:	wm( <Data>, <Weights>, [<data>, <weights>...] )
#
#	Notes:	RETURNS TWO ANSWERS. The first one is the NUMERIC answer (ie: The
#		weighted amount divided by HOW MANY ENTRIES THERE ARE. THIS answer is
#		less accurate than the second method. The SECOND ANSWER is more
#		accurate than the first one - but you get BOTH. Always get this
#		via the :
#
#		Also note - this function returns ARRAYS.
#
#	Ex:		list( $wmn, $wmm ) = $cbs->wm( <DATA>, <WEIGHTS> );
#
################################################################################
function wm()
{
	$args = func_get_args();
	$argv = func_num_args();
	$this->debug->in();

	$wm = 0;
	$all_w = 0;
	$num_w = 0;

	$weighted_mean_num = [];
	$weighted_mean_all = [];

	for( $av=0; $av<$argv; $av+=2 ){
		if( is_array($args[$av]) ){
			$d = args[$av];	#	Data
			$w = args[$av+1];	#	Weights

			foreach( $w as $k=>$v ){
				$wm += $d[$k] * $v;
				$all_w += $v;
				}

			$num_w = count( $w );

			$weighted_mean_num[$av] = $wm / $num_w;
			$weighted_mean_all[$av] = $wm / $all_w;
			}
		}

	$this->debug->out();

	return array( $weighted_mean_num, $weighted_mean_all );
}
################################################################################
#	gm(). Geometric mean. This does JUST the Geometric mean.
#	Call:	$cbs->gm( array(1,2,3,4), array(3,2,4,1) );
#	Return:	Geometric mean $gm.
################################################################################
function gm()
{
	$args = func_get_args();
	$argv = func_num_args();
	$this->debug->in();

	$gms = [];
	$num = 0;

	for( $i=0; $i<$argv; $i++ ){
		if( is_array($args[$i]) ){
			$g = null;
			foreach( $args[$i] as $k=>$v ){
				if( is_null($g) ){ $g = $v; }
					else { $g *= $v; }
				}

			$c = count( $args[$i] );
			$gms[$k] = pow( $g, (1/$c) );
			}
		}

	$this->debug->out();

	return $gms;
}
################################################################################
#	wgm(). Weighted Geometric Mean.
#	Call:	$cbs->wgm( <data>, <weights> );
#	Notes:	You can send multiple pairs of data+weight.
#	Return:	Array $ans
################################################################################
function wgm()
{
	$args = func_get_args();
	$argv = func_num_args();
	$this->debug->in();

	$ans = [];
	for( $i=0; $i<$argv; $i+=2 ){
		if( is_array($args[$i]) ){
			$d = $args[$args[$i]];
			$w = $args[$args[$i+1]];
			$n = count( $d );
			$sum = 0;
			foreach( $d as $k=>$v ){
				if( ($d[$k] <= 0) || ($w[$k] <= 0) ){
					$this->debug->die( "Geometric Mean : Value is zero or less : D=$d[$k] - W=$w[$k]" );
					}

				$sum = pow( $d[$k], $w[$k] );
				}

			$ans[] = pow( $sum, (1/$n) );
			}
		}

	$this->debug->out();
	return $ans;
}
################################################################################
#	lgm(). Logarithmic Mean.
#	Notes:	All inputs MUST be arrays. You can have multiple arrays given in
#		pairs. Example: $cbs->lgm( <D1>[, <D2>(,...)] );
#
#		Note also that the book incorrectly states the formula as
#			do((log x)/n)
################################################################################
function lgm()
{
	$args = func_get_args();
	$argv = func_num_args();
	$this->debug->in();

	$ans = [];
	for( $i=0; $i<$argv; $i+=2 ){
		if( is_array($args[$i]) ){
			$d = $args[$args[$i]];
			$n = count( $d );
			$sum = 0;
			foreach( $d as $k=>$v ){
				$sum = log( $v );
				}

			$ans[] = $sum / $n;
			}
		}

	$this->debug->out();
	return $ans;
}
################################################################################
#	index(). Does the Index of Number on page 28.
#	Notes:	The equation is index = (sum(index #1) + sum(index #2)) * 100
################################################################################
function index()
{
	$args = func_get_args();
	$argv = func_num_args();
	$this->debug->in();

	$ans = [];
	for( $i=0; $i<$argv; $i+=2 ){
		if( is_array($args[$i]) ){
			$sum = 0;
			foreach( $args[$i] as $k=>$v ){ $sum += $v; }
			$pn = $sum;

			$sum = 0;
			foreach( $args[$i] as $k=>$v ){ $sum += $v; }
			$po = $sum;

			$ans[] = ($pn / $po) * 100;
			}
		}

	$this->debug->out();
	return $ans;
}
################################################################################
#	weighted_index(). Performs the weighted index from page 29.
#	Notes:	You must pass in the three arrays as A1[], B1[], C1[]
#		OR you can pass in an array WITH the above three items in
#		that array. IE: Array(A[],B[],C[]).
#
#		Also - you can pass in as many arrays as you wish.
#
#		Note also that I believe the equation on page 29 is wrong. I believe
#		that the equation is supposed to be : (sum(Pn * Qo) / sum(Po * Qo)) * 100
#		I believe the extra 'n' above the Pn is incorrect but I will include a
#		second weighted_index function USING the 'n' so the equation should be:
#			(sum((Pn^n)*Qo) / sum(Po * Qo)) * 100
################################################################################
function weighted_index()
{
	$args = func_get_args();
	$argv = func_num_args();
	$this->debug->in();
#
#	First, we need to go through and separate out the arrays.
#	We just check to see if there is just an array (ie: A[])
#	or an array of arrays (ie: ARRAY(A[], B[], C[]) ). Which
#	is pretty early to do.
#
	$a = [];
	$b = [];
	$c = [];
#
#	Ok. It is either "A[],B[],C[]" or "ARRAY(A[], B[], C[])".
#
	for( $i=0; $i<$argv; $i++ ){
#
#	Get the first item in the array
#
		$ary = $args[$i];
#
#	Now - we have no idea how the person set up the array
#	so we do a FOREACH which gets this for us. Then we just
#	need to look at the first item in THIS array. It will
#	either just be an array OR it will have multiple arrays
#	inside of it.
#
		$loop_count = 0;
		foreach( $ary as $k=>$v ){
#
#	Remember! $V is currently pointing at an argument and this argument
#	will either be just ITEM[] or it is ITEM(A[],B[],C[])!
#
			$loop_count += 1;
			$cnt = count( $v );
#
#	Ok, if the count is one - then each array is a single array.
#
			if( $cnt > 2 ){
				$loop_count = 0;
				$a[] = $v[0];
				$b[] = $v[1];
				$c[] = $v[2];
				}
#
#	Otherwise, they sent us multiple arrays
#
				else {
					if( $loop_count == 1 ){ $a[] = $v; }
						else if( $loop_count == 2 ){ $b[] = $v; }
						else if( $loop_count == 3 ){ $c[] = $v; }
					}
			}
		}

	$ans = [];
	for( $i=0; $i<$argv; $i+=2 ){
		if( is_array($args[$i]) ){
			$sum = 0;
			foreach( $args[$i] as $k=>$v ){ $sum += $v; }
			$pn = $sum;

			$sum = 0;
			foreach( $args[$i] as $k=>$v ){ $sum += $v; }
			$po = $sum;

			$ans[] = ($pn / $po) * 100;
			}
		}

	return $ans;
	$this->debug->out();
}
################################################################################
#	moving_average(). Takes an array, combines them into a second array by
#		using the $LEN variable (for how long or how to group the array) and
#		then does a moving average on the NEW array.
#
#	Variables:
#		Array($ary) is the array of values.
#		Length($len) is how many items from the array to use at a time.
#
#	Return : This function not only returns the MOVING AVERAGE but it also
#		returns TRUE or FALSE. TRUE if there were left-over values which were
#		not used. Otherwise, it returns FALSE (no left-overs).
################################################################################
function moving_average( $ary=null, $len=null )
{
	$this->debug->in();

	if( is_null($ary) || !is_array($ary) ){ return false; }
	if( is_null($len) ){ return false; }

	$a = 0;
	$b = [];
	$flag = false;
	foreach( $ary as $k=>$v ){
		$a += $v;
		if( $k % $len ){
			$b[] = $a;
			$a = 0;
			}
		}
#
#	Are there left-overs? In other words - does $LEN
#	divide equally into the numbers we have?
#
	if( ($k % $len) > 0 ){ $flag = true; }
#
#	Calculate the MOVING AVERAGE.
#
	foreach( $b as $k=>$v ){
		$b[$k] = $v / $len;
		}

	$this->debug->out();

	return array( $b, $flag );
}
################################################################################
#	mean_diviation(). Gets the MEAN DIVIATION. This is found by FIRST getting
#		the ARITHMETIC MEAN. (The AM() function above.) Then subtracting all of
#		the values in the array FROM the AM. Finally, the MEAN DIVIATION is
#		calculated by dividing by the number of entries you have. To do all of
#		this - just send in an array of values.
################################################################################
function mean_diviation( $ary=null )
{
	$this->debug->in();

	if( is_null($ary) || !is_array($ary) ){ return false; }

	$num = count( $ary );
	$am = $this->am( $ary );

	$a = 0;
	foreach( $arg as $k=>$v ){
		$a = ( $v - $am );
		}

	$a = $a / $num;
	$this->debug->out();

	return $a;
}
################################################################################
#	range(). Get the range from the array.
################################################################################
function range( $ary=null )
{
	$this->debug->in();

	if( is_null($ary) || !is_array($ary) ){ return false; }

	$low = PHP_INT_MAX;
	$high = PHP_INT_MIN;
	foreach( $ary as $k=>$v ){
		if( $v < $low ){ $low = $v; }
		if( $v > $high ){ $high = $v; }
		}

	$this->debug->out();
	return array( $low, $high );
}
################################################################################
#	mnd(). Figure out the mean of an array and computes the deviation.
#	NOTES:	Returns the MEAN and an array of the deviations.
################################################################################
function mnd( $ary=null )
{
	$this->debug->in();

	if( is_null($ary) || !is_array($ary) ){ return false; }

	$num = count( $ary );

	$n = 0;
	foreach( $ary as $k=>$v ){
		$n += $v;
		}

	$mean = $n / $num;

	$dev = [];
	foreach( $ary as $k=>$v ){
		$dev[$k] = $ary[$k] - $mean;
		}

	$this->debug->out();
	return array( $mean, $dev );
}
################################################################################
#	standard_deviation(). Computes the standard deviation.
#	NOTES:	Send all values and everything will be calculated.
################################################################################
function standard_deviation( $ary=null )
{
	$this->debug->in();

	if( is_null($ary) || !is_array($ary) ){ return false; }
#
#	First - get the mean and deviation.
#
	list( $mean, $dev ) = $this->mnd( $ary );
#
#	Square the deviation numbers
#
	$a = [];
	$num = count( $dev );
	foreach( $dev as $k=>$v ){
		$a[$k] = $v * $v;
		}
#
#	Add up all of the deviations.
#
	$b = 0;
	foreach( $a as $k=>$v ){
		$b += $v;
		}
#
#	Divide this by how many there are
#
	$c = $b / $num;
#
#	And take the square root of that number.
#
	$sd = sqrt( $c );

	$this->debug->out();
	return $sd;
}
################################################################################
#	covariance().	Calculates the covariance. Needs TWO arrays.
#		1/n( sum( deviation(array #1) * deviation(array #2) ) )
################################################################################
function covariance( $a1=null, $a2=null )
{
	$this->debug->in();

	if( is_null($a1) || !is_array($a1) ){ return false; }
	if( is_null($a2) || !is_array($a2) ){ return false; }
#
#	First - get the mean and deviation of array #1.
#
	list( $m1, $d1 ) = $this->mnd( $a1 );
#
#	First - get the mean and deviation of array #2.
#
	list( $m2, $d2 ) = $this->mnd( $a2 );
#
#	Since we DO NOT KNOW if the arrays are the same length -
#	we have to find which one is smaller and only go THAT FAR.
#
	$c1 = count( $d1 );
	$c2 = count( $d2 );

	if( $c2 < $c1 ){
		$c1 = $c2;
		$info = "Fi-" . __FILE__ . ";C-" . __CLASS__ .
			";M-" . __METHOD__ . ";Fu-" .  __FUNCTION__ . "L-" . __LINE__;

		echo "\n***ATTENTION*** : $info\n" .
			"Array #1 is NOT THE SAME LENGTH as Array #2\n";
		}

	$b = [];
	for( $i=0; $i<$c1; $i++ ){
		$b[$i] = $a1[$i] * $a2[$i];
		}

	$sd = 0;
	foreach( $b as $k=>$v ){ $sd += $v; }

	$sd = $sd / $c1;

	$this->debug->out();
	return $sd;
}
################################################################################
#	eval_r(). Taken from the book. You have to send BOTH the X & Y arrays
################################################################################
function eval_r( $a1=null, $a2=null )
{
	$this->debug->in();

	if( is_null($a1) || !is_array($a1) ){ return false; }
	if( is_null($a2) || !is_array($a2) ){ return false; }
#
#	Get the two standard deviations and then the covariance.
#
	$sx = $this->standard_deviation( $d1 );
	$sy = $this->standard_deviation( $d2 );
	$sxy = $this->covariance( $a1, $a2 );
#
#	Now computer the R value.
#
	$r = ( $sxy / ($sx * $sy) );

	$this->debug->out();
	return $r;
}
################################################################################
#	reliabilty_r(). Checks the reliability of r
################################################################################
function reliability_r( $r=null )
{
	$this->debug->in();

	if( is_null($r) || !is_array($r) ){ return false; }

	$v = 0.5 * log( (1 + $r) / (1 - $r) );

	$this->debug->out();
	return $v;
}
################################################################################
#	rank_correlation(). Ranks array information.
################################################################################
function rank_correlation( $a1=null, $a2=null )
{
	$this->debug->in();

	if( is_null($a1) || !is_array($a1) ){ return false; }
	if( is_null($a2) || !is_array($a2) ){ return false; }

	$c1 = count( $a1 );
	$c2 = count( $a2 );
	if( $c2 < $c1 ){
		$c1 = $c2;
		$info = "Fi-" . __FILE__ . ";C-" . __CLASS__ .
			";M-" . __METHOD__ . ";Fu-" .  __FUNCTION__ . "L-" . __LINE__;

		echo "\n***ATTENTION*** : $info\n" .
			"Array #1 is NOT THE SAME LENGTH as Array #2\n";
		}

	$b = [];
	for( $i=0; $i<$c1; $i++ ){
		$b[$i] = $a1[$i] - $a2[$i];
		}

	$s = 0;
	foreach( $b as $k=>$v ){
		$s += $v * $v;
		}

	$r = 1 - ( (6 * $s) / (($c1 * $c1 * $c1) - $c1) );

	$this->debug->out();
	return $r;
}
################################################################################
#	regression_coefficient(). Presented on Page 92.
################################################################################
function regression_coefficient( $a1=null, $a2=null )
{
	$this->debug->in();

	if( is_null($a1) || !is_array($a1) ){ return false; }
	if( is_null($a2) || !is_array($a2) ){ return false; }
#
#	Get the two standard deviations and then the covariance.
#
	$sx = $this->standard_deviation( $d1 );
	$sy = $this->standard_deviation( $d2 );
	$sxy = $this->covariance( $a1, $a2 );

	$r = $sxy / ($sx * $sx);

	$this->debug->out();
	return $r;
}
################################################################################
#	frequency(). Presented on Page 96. This can be done in two ways:
#		#1	=	Send THREE SINGLE columned lines of information. (ie: a[], b[], c[])
#		#2	=	Send ONE 3x3 array.
################################################################################
function frequency()
{
	$this->debug->in();
#
#	First, we have to figure out if they sent a[], b[], c[] or a[3,3]
#
	$args = func_get_args();
	if( count($args) < 3 ){
		$a = $args[0];
		$b = null;
		$c = null;
		}
		else {
			$a = $args[0];
			$b = $args[1];
			$c = $args[2];
			}

	
	if( is_null($a1) || !is_array($a1) ){ return false; }
	if( is_null($a2) || !is_array($a2) ){ return false; }

	$this->debug->out();
	return $r;
}
################################################################################
#	sd_ep(). Calculates the Stanard Deviation of RANDOM values but the EQUAL
#		probability.
#	NOTES:	$X is an array while $P is just ONE probability.
################################################################################
function sd_ep( $x=null, $p=null )
{
	$this->debug->in();

	if( is_null($x) || !is_array($x) ){ return false; }
	if( is_null($p) ){ return false; }
#
#	Get Mu
#
	$Mu = 0;
	$num = count( $x );
	foreach( $x as $k=>$v ){
		$Mu += $v;
		}

	$Mu = $Mu / $num;
#
#	Get Theta
#
	$Theta = 0;
	foreach( $x as $k=>$v ){
		$Theta = ( ($v - $Mu) * ($v - $Mu) );
		}

	$Theta = sqrt( ($Theta / $num) );

	$this->debug->out();
	return $Theta;
}
################################################################################
#	sd_dp(). Calculates the Stanard Deviation of RANDOM values but DIFFERENT
#		probability.
#	NOTES:	$X is an array while $P is just ONE probability.
################################################################################
function sd_dp( $x=null, $p=null )
{
	$this->debug->in();

	if( is_null($x) || !is_array($x) ){ return false; }
	if( is_null($p) || !is_array($p) ){ return false; }
#
#	First get Mu
#
	$Mu = 0;
	foreach( $x as $k=>$v ){
		$Mu += $v * $p[$k];
		}
#
#	Now calculate Theta
#
	$Theta = 0;
	$num = count( $x );
	foreach( $x as $k=>$v ){
		$Theta += $p[$k] * ( ($v - $Mu) * ($v - $Mu) );
		}

	$Theta = sqrt( $Theta );

	$this->debug->out();
	return $Theta;
}
################################################################################
#	us_sd(). The UNCORRECTED SAMPLE STANDARD DEVIATION function.
#	NOTES:	 $X is an array.
################################################################################
function us_sd( $x=null )
{
	$this->debug->in();

	if( is_null($x) || !is_array($x) ){ return false; }
#
#	First - get the mean and deviation.
#
	list( $mean, $dev ) = $this->mnd( $ary );
#
#	Then get the SN.
#
	$sn = 0;
	$num = count( $x );
	foreach( $x as $k=>$v ){
		$sn += ($v * $mean) * ($v * $mean);
		}

	$sn = sqrt( ($sn / $num) );

	$this->debug->out();
	return $sn;
}
################################################################################
#	cs_sd(). The CORRECTED SAMPLE STANDARD DEVIATION function.
#	NOTES:	 $X is an array.
################################################################################
function cs_sd( $x=null )
{
	$this->debug->in();

	if( is_null($x) || !is_array($x) ){ return false; }
#
#	First - get the mean and deviation.
#
	list( $mean, $dev ) = $this->mnd( $ary );
#
#	Then get the SN.
#
	$sn = 0;
	$num = count( $x ) - 1;
	foreach( $x as $k=>$v ){
		$sn += ($v * $mean) * ($v * $mean);
		}

	if( $num != 0 ){ $sn = sqrt( ($sn / $num) ); }
		else {
			$this->debug->out();
			return false;
			}

	$this->debug->out();
	return $sn;
}
################################################################################
#	ubs_sd(). The UNBIASED SAMPLE STANDARD DEVIATION function.
#	NOTES:	 $X is an array. $Y2 is the EXCESS KURTOSIS which can be left out.
################################################################################
function ubs_sd( $x=null, $y2=null )
{
	$this->debug->in();

	if( is_null($x) || !is_array($x) ){ return false; }
	if( is_null($y2) ){ $y2 = 0; }
#
#	First - get the mean and deviation.
#
	list( $mean, $dev ) = $this->mnd( $ary );
#
#	Then get the SN.
#
	$sn = 0;
	$num = count( $x ) - 1.5 - ($y2 * (0.25));
	foreach( $x as $k=>$v ){
		$sn += ($v * $mean) * ($v * $mean);
		}

	if( $num != 0 ){ $sn = sqrt( ($sn / $num) ); }
		else {
			$this->debug->out();
			return false;
			}

	$this->debug->out();
	return $sn;
}
################################################################################
#	fp_ep(). FINITE POPULATION with EQUAL PROBABILITIES
#	NOTES:	$ARY is an array.
################################################################################
function fp_ep( $ary=null )
{
	$this->debug->in();

	if( is_null($ary) || !is_array($ary) ){ return false; }

	$a = 0;
	$num = count( $ary );
	foreach( $ary as $k=>$v ){
		$a += $v;
		}

	$a = ($a * $a) / ($num * $num);

	$b = 0;
	foreach( $ary as $k=>$v ){
		$b += ($v * $v);
		}

	$b = $b / $num;

	$c = sqrt( $b - $a );

	$this->debug->out();
	return $c;
}
################################################################################
#	permutation(). Does a permutation of N!/(N-R)!
#	NOTES:	ONLY DOES POSITIVE NUMBERS
################################################################################
function permutation( $n=null, $r=null )
{
	$this->debug->in();

	if( is_null($n) ){ return false; }
	if( is_null($r) ){ return false; }

	$n = abs( $n );
	$r = abs( $r );

	if( $n > 0 ){
		$a = 1;
		for( $i=1; $i<=$n; $i++ ){ $a *= $i; }

		$b = $n - $r;
		$c = 1;
		for( $i=1; $i<=$b; $i++ ){ $c *= $i; }

		$ret = $a / $c;
		}
		else { $ret = 1; }

	$this->debug->out();
	return $ret;
}
################################################################################
#	combination(). Does a combination using N!/(R! * (N-R)!).
#	NOTES:	ONLY DOES POSITIVE NUMBERS
################################################################################
function combination( $n=null, $r=null )
{
	$this->debug->in();

	if( is_null($n) ){ return false; }
	if( is_null($r) ){ return false; }

	$n = abs( $n );
	$r = abs( $r );

	if( $n > 0 ){
		$a = 1;
		for( $i=1; $i<=$n; $i++ ){ $a *= $i; }

		$b = 1;
		for( $i=1; $i<=$r; $i++ ){ $b *= $i; }

		$d = $n - $r;
		$c = 1;
		for( $i=1; $i<=$d; $i++ ){ $c *= $i; }

		$ret = $a / ($b * $c);
		}
		else { $ret = 1; }

	$this->debug->out();
	return $ret;
}
################################################################################
#	poisson_distribution(). Does the Poisson Distribution.
#	NOTES:	Mu is a number. X is HOW MANY
################################################################################
function poisson_distribution( $Mu=null, $x=null )
{
	$this->debug->in();

	if( is_null($Mu) ){ return false; }
	if( is_null($x) ){ return false; }

	$e = 2.7182;
	$mu_e = pow( $e, $Mu );

	$p = [];
	for( $i=0; $i<=$x; $i++ ){
		$pow_mu = pow( $Mu, $i );
		$n = 1;
		for( $j=1; $j<=$i; $j++ ){
			$n *= $j;
			}

		$p[] = $mu_e * ($pow_mu / $n);
		}

	$this->debug->out();
	return $p;
}
################################################################################
#	correlation_coefficient(). Computers the correlation coefficient which
#		requires you to send two arrays, which must be the same length (number
#		of entries), and The program computes the correlation coefficient and
#		the rest of the items.
################################################################################
function correlation_coefficient( $x=null, $y=null, $opt=false )
{
	$this->debug->in();

	if( is_null($x) ){ return false; }
	if( is_null($y) ){ return false; }

	$nx = count( $x );
	$ny = count( $y );
#
#	Computer X^2
#
	$x2 = [];
	foreach( $x as $k=>$v ){ $x2[$k] = $v * $v; }
#
#	Compute y^2
#
	$y2 = [];
	foreach( $y as $k=>$v ){ $y2[$k] = $v * $v; }
#
#	Compute X * Y
#
	$xy = [];
	foreach( $x as $k=>$v ){ $xy[$k] = $v * $y[$k]; }
#
#	Sum X
#
	$sx = 0;
	foreach( $x as $k=>$v ){ $sx += $v; }
#
#	Sum Y
#
	$sy = 0;
	foreach( $y as $k=>$v ){ $sy += $v; }
#
#	Compute the Mean of X
#
	$mx = $sx / $nx;
#
#	Compute the Mean of Y
#
	$my = $sy / $ny;
#
#	Compute Deviation of X
#
	$dx = [];
	foreach( $x as $k=>$v ){ $dx[$k] = $v - sx; }
#
#	Compute Deviation of Y
#
	$dy = [];
	foreach( $y as $k=>$v ){ $dy[$k] = $v - sy; }
#
#	Compute Deviation of X squared
#
	$dx2 = 0;
	foreach( $dx as $k=>$v ){ $dx2 += $v * $v; }
#
#	Compute Deviation of Y squared
#
	$dy2 = 0;
	foreach( $dy as $k=>$v ){ $dy2 += $v * $v; }
#
#	Compute Standard Deviation of X
#
	$sdx = sqrt( ($dx2 / $nx) );
#
#	Compute Standard Deviation of Y
#
	$sdy = sqrt( ($dy2 / $ny) );
#
#	Summation of dx*dy
#
	$sxy = 0;
	foreach( $dx as $k=>$v ){ $sxy += $v * $dy[$k]; }
	$sxy = $sxy / $nx;

	$r = $sxy / ($sx * $sy);

	$this->debug->out();
	if( $opt ){
		$ret = [];
		$ret['nx'] = $nx;
		$ret['ny'] = $ny;
		$ret['x2'] = $x2;
		$ret['y2'] = $y2;
		$ret['xy'] = $xy;
		$ret['sx'] = $sx;
		$ret['sy'] = $sy;
		$ret['mx'] = $mx;
		$ret['my'] = $my;
		$ret['dx'] = $dx;
		$ret['dy'] = $dy;
		$ret['dx2'] = $dx2;
		$ret['dy2'] = $dy2;
		$ret['sdx'] = $sdx;
		$ret['sdy'] = $sdy;
		$ret['sxy'] = $sxy;
		$ret['r'] = $r;

		return $ret;
		}
		else { return $r; }
}
################################################################################
#	pd(). Easy function call to the Poisson Distribution function.
################################################################################
function pd( $Mu=null, $x=null ){ $this->poisson_distrubution( $Mu, $x ); }
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
################################################################################
#	End of the class
################################################################################
}
################################################################################
#	Add the class to the GLOBALS['classes'] variable so everyone can use it.
################################################################################

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['business_statistics']) ){
		$GLOBALS['classes']['business_statistics'] = new class_business_statistics();
		}

?>
