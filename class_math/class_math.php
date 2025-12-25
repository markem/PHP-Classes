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
#	class_math();
#
#-Description:
#
#	A class to handle math functions.
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
#	Mark Manning			Simulacron I			Sun 01/24/2021 23:31:26.13 
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
#		CLASS_MATH.PHP. A class to handle working with math.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_math
{
	private $pi = null;
#
#	The following is taken from Wikipedia at
#	https://en.wikipedia.org/wiki/Fine-structure_constant
#
#	e is the elementary charge (1.602176634×10−19 C[5]);
#
	private $elementary_charge = null;
	private $e = null;
#
#	h is the Planck constant (6.62607015×10−34 J⋅Hz−1[6]);
#
	private $planck_constant = null;
	private $h = null;
#
#	ħ is the reduced Planck constant, ħ = h/2π (1.054571817...×10−34 J⋅s[7])
#
	private $reduced_planck_constant = null;
	private $rh = null;
#
#	c is the speed of light (299792458 m⋅s−1[8]);
#
	private $speed_of_light = null;
#	private $c = null;
#
#	ε0 is the electric constant (8.8541878188(14)×10−12 F⋅m−1[9])
#
	private $electric_constant = null;
	private $e0 = null;
#
#	The fine-structure constant, also known as the Sommerfeld constant.
#	0.0072973525643 ~ (1 / 137.035999177), with a relative uncertainty of 1.6×10−10.[1]
#
	private $fine_structure_constant = null;
	private $a = null;
	private $sommerfeld_constant = null;
#
#	See the webpage for more details on the following.
#	In the electrostatic CGS system, a = exp(2)/(hc) - where "h" is "h" with a line over it.
#	In a nondimensionalised system, a = exp(2)/(4 * $pi).
#	In the system of atomic units, a = 1/c;
#
#	The CODATA recommended value of α is
#
	private $codata_a = null;
#
#	This value for α gives µ0 = 4π × 0.99999999987(16)×10−7 H⋅m−1
#
	private $codata_mu = null;
#
#	The reciprocal of the fine-structure constant by CODATA is
#
	private $codata_reciprocal_constant = null;
	private $codata_r = null;
#
#	The 2020 value with a relative accuracy of 8.1×10−11
#
	private $codata_2020_r = null;
#
#	History of Measurement. Latest at 2023
#	Fan et al. (2023)
#
	private $codata_2023_a = null;
	private $codata_2023_r = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['math']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['math']; }
}
################################################################################
#	init(). A way to re-init the class.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}

	$pi = 3.14159265358979323846;
#
#	The following is taken from Wikipedia at
#	https://en.wikipedia.org/wiki/Fine-structure_constant
#
#	e is the elementary charge (1.602176634×10−19 C[5]);
#
	$elementary_charge = 1.602176634 * (10 ** -19);
	$e = $elementary_charge;
#
#	h is the Planck constant (6.62607015×10−34 J⋅Hz−1[6]);
#
	$planck_constant = 6.62607015 * (10 ** -34);
	$h = $planck_constant;
#
#	ħ is the reduced Planck constant, ħ = h/2π (1.054571817...×10−34 J⋅s[7])
#
	$reduced_planck_constant = 1.054571817 * (10 ** -34);
	$rh = $reduced_planck_constant;
#
#	c is the speed of light (299792458 m⋅s−1[8]);
#
	$speed_of_light = 299792458;	#	Meters per Second (ms)
#	private $c = $speed_of_light;
#
#	ε0 is the electric constant (8.8541878188(14)×10−12 F⋅m−1[9])
#
	$electric_constant = 8.854187818814 * (10 ** -12);
	$e0 = $electric_constant;
#
#	The fine-structure constant, also known as the Sommerfeld constant.
#	0.0072973525643 ~ (1 / 137.035999177), with a relative uncertainty of 1.6×10−10.[1]
#
	$fine_structure_constant = 0.0072973525643;
	$a = $fine_structure_constant;
	$sommerfeld_constant = $fine_structure_constant;
#
#	See the webpage for more details on the following.
#	In the electrostatic CGS system, a = exp(2)/(hc) - where "h" is "h" with a line over it.
#	In a nondimensionalised system, a = exp(2)/(4 * $pi).
#	In the system of atomic units, a = 1/c;
#
#	The CODATA recommended value of α is
#
	$codata_a = 0.0072973525643;
#
#	This value for α gives µ0 = 4π × 0.99999999987(16)×10−7 H⋅m−1
#
	$codata_mu = (4 * $pi) * (0.99999999987 * (10 ** -7));
#
#	The reciprocal of the fine-structure constant by CODATA is
#
	$codata_reciprocal_constant = 137.035999177;
	$codata_r = $codata_reciprocal_constant;
#
#	The 2020 value with a relative accuracy of 8.1×10−11
#
	$codata_2020_r = 137.035999206;
#
#	History of Measurement. Latest at 2023
#	Fan et al. (2023)
#
	$codata_2023_a = 0.0072973525649;
	$codata_2023_r = 137.035999166;
}
################################################################################
#	area_square(). computes the area of the square.
################################################################################
function area_square( $w=null, $h=null )
{
	if( is_null($w) ){ die( "Area of a square - no side given\n" ); }
	if( is_null($h) ){ $h = $w; }

	return ($w * $h);
}
################################################################################
#	area_rectangle(). Compute the area of a rectangle.
################################################################################
function area_rectangle( $w=null, $h=null )
{
	return $this->area_square( $w, $h );
}
################################################################################
#	area_triangle(). Compute the area of a triangle.
################################################################################
function area_triangle( $b=null, $h=null )
{
	return $this->area_square($b, $h) / 2.0;
}
################################################################################
#	area_rhombus(). Compute the area of a rhombus (diamond)
#	Notes:	D is BIG diagonal, d is small diagonal
################################################################################
function area_rhombus( $D=null, $d=null )
{
	if( is_null($D) ){ die( "Area of a rhombus - D not given\n" ); }
	if( is_null($d) ){ die( "Area of a rhombus - d not given\n" ); }

	return (($D * $d) / 2.0);
}
################################################################################
#	area_trapezoid(). Compute the area of a trapezoid
################################################################################
function area_trapezoid( $B=null, $b=null, $h=null )
{
	if( is_null($B) ){ die( "Area of a trapezoid - B not given\n" ); }
	if( is_null($b) ){ die( "Area of a trapezoid - b not given\n" ); }
	if( is_null($h) ){ die( "Area of a trapezoid - h not given\n" ); }

	return (($B + $b) / 2.0) * $h;
}
################################################################################
#	area_polygon(). Compute the area of a polygon.
#	Notes: P is perimeter, a is apothem
################################################################################
function area_polygon( $P=null, $a=null )
{
	if( is_null($P) ){ die( "Area of a polygon - P not given\n" ); }
	if( is_null($a) ){ die( "Area of a polygon - a not given\n" ); }

	return ($P / 2.0) * $a;
}
################################################################################
#	area_circle(). Computes the area of the circle.
################################################################################
function area_circle( $r=null )
{
	if( is_null($r) ){ die( "Area of a circle - r not given\n" ); }

	return (($r * $r) * $this->PI );
}
################################################################################
#	area_perimeter(). Computes the perimeter (circumferance)
################################################################################
function area_perimeter( $r=null )
{
	if( is_null($r) ){ die( "Area of a circles perimeter - r not given\n" ); }

	return (2.0 * $this->PI * $r);
}
################################################################################
#	area_cone(). Computes the area of a cone
################################################################################
function area_cone( $r=null, $s=null )
{
	if( is_null($r) ){ die( "Area of a cone - r not given\n" ); }
	if( is_null($s) ){ die( "Area of a cone - s not given\n" ); }

	return ($this->PI * $r) * $s;
}
################################################################################
#	area_sphere(). Computes the area of a sphere.
################################################################################
function area_sphere( $r )
{
	if( is_null($r) ){ die( "Area of a sphere - r not given\n" ); }

	return (4.0 * $this->PI * ($r * $r));
}
################################################################################
#	vol_cube(). computes the volume of a cube
################################################################################
function vol_cube( $w=null, $h=null )
{
	if( is_null($w) ){ die( "Volume of a cube - w not given\n" ); }
	if( is_null($h) ){ $h = $w; }

	return ( $w * $h * $w );
}
################################################################################
#	vol_parallelpiped(). Comput the volume of a parallelpiped
################################################################################
function vol_parallelepiped( $l=null, $w=null, $h=null )
{
	if( is_null($l) ){ die( "Volume of a parallelepiped - l not given\n" ); }
	if( is_null($w) ){ die( "Volume of a parallelepiped - w not given\n" ); }
	if( is_null($h) ){ die( "Volume of a parallelepiped - h not given\n" ); }

	return ($l * $w * $h);
}
################################################################################
#	vol_ppp(). An easier call to vol_parallelepiped.
################################################################################
function vol_ppp( $l=null, $w=null, $h=null )
{
	return vol_parallelepiped( $l, $w, $h );
}
################################################################################
#	volume_prism(). Computes the volume of a prism.
################################################################################
function vol_prism( $b=null, $h=null )
{
	if( is_null($b) ){ die( "Volume of a prism - b not given\n" ); }
	if( is_null($h) ){ die( "Volume of a prism - h not given\n" ); }

	return ($b * $h);
}
################################################################################
#	volume_cylinder(). Computes the volume of a cylinder
################################################################################
function volume_cylinder( $r=null, $h=null )
{
	if( is_null($r) ){ die( "Volume of a cylinder - r not given\n" ); }
	if( is_null($h) ){ die( "Volume of a cylinder - h not given\n" ); }

	return ($this->PI * ($r * $r)) * $h;
}
################################################################################
#	volume_cone(). Compute the volume of a cone.
################################################################################
function volume_cone( $b=null, $h=null )
{
	if( is_null($b) ){ die( "Volume of a cone - b not given\n" ); }
	if( is_null($h) ){ die( "Volume of a cone - h not given\n" ); }

	return ($b * $h) / 3.0;
}
################################################################################
#	volume_sphere(). Computes the volume of a sphere.
################################################################################
function volume_sphere( $r=null )
{
	if( is_null($r) ){ die( "Volume of a sphere - r not given\n" ); }

	return (($r * $r * $r) * $this->PI) * (4.0 / 3.0);
}
################################################################################
#	Eq_dp(). Determine if three numbers are directly proportional.
################################################################################
function eq_dp( $x=null, $y=null, $k=null )
{
	if( is_null($x) ){ die( "Equation - Directly Proportional - x not given\n" ); }
	if( is_null($y) ){ die( "Equation - Directly Proportional - y not given\n" ); }
	if( is_null($k) ){ die( "Equation - Directly Proportional - k not given\n" ); }

	if( ($y == ($k * $x)) && ($k == ($y / $x)) ){ return true; }

	return false;
}
################################################################################
#	eq_ip(). Determine if three numbers are inversely proportional
################################################################################
function eq_ip( $x=null, $y=null, $k=null )
{
	if( is_null($x) ){ die( "Equation - Inversely Proportional - x not given\n" ); }
	if( is_null($y) ){ die( "Equation - Inversely Proportional - y not given\n" ); }
	if( is_null($k) ){ die( "Equation - Inversely Proportional - k not given\n" ); }

	if( ($y == ($k / $x)) && ($k = ($y * $x)) ){ return true; }

	return false;
}
################################################################################
#	eq_quadratic(). Computes the quadratic equaion and returns TWO answers.
################################################################################
function eq_quadratic( $a=null, $b=null, $c=null )
{
	if( is_null($a) ){ die( "Equation - Quadratic formula - a not given\n" ); }
	if( is_null($b) ){ die( "Equation - Quadratic formula - b not given\n" ); }
	if( is_null($c) ){ die( "Equation - Quadratic formula - c not given\n" ); }

	if( $a !== 0.0 ){
		$d = sqrt(($b * $b) - (4.0 * $a * $c));
		$r1 = ((-$b) + $d) / (2.0 * $a);
		$r2 = ((-$b) - $d) / (2.0 * $a);
		return array( $r1, $r2 );
		}

	return false;
}
################################################################################
#	eq_line(). Get a line's equation. Returns Y
################################################################################
function eq_line( $a=null, $b=null, $c=null, $x=null )
{
	if( is_null($a) ){ die( "Equation of a line - a not given\n" ); }
	if( is_null($b) ){ die( "Equation of a line - b not given\n" ); }
	if( is_null($c) ){ die( "Equation of a line - c not given\n" ); }
	if( is_null($x) ){ die( "Equation of a line - x not given\n" ); }

	return ($a * ($x * $x)) + ($b * $x) + $c;
}
################################################################################
#	eq_concavity(). Computes if a line is concave or not.
#	NOTES:	ax^2 + bx + c = 0
#			ax^2 + bx = -c
#			x(ax + b) = -c
#			x = (-c) / (ax + b)
#			IF x is null then X becomes one(1)
################################################################################
function eq_concavity( $a=null, $b=null, $c=null, $x=null )
{
	if( is_null($a) ){ die( "Equation - Concavity - a not given\n" ); }
	if( is_null($b) ){ die( "Equation - Concavity - b not given\n" ); }
	if( is_null($c) ){ die( "Equation - Concavity - c not given\n" ); }

	if( $a > 0.0 ){ return "up"; }
		else if( $a < 0.0 ){ return "down"; }

	return false;
}
################################################################################
#	eq_discriminant(). Computes the discriminant (delta)
################################################################################
function eq_discriminant( $a=null, $b=null, $c=null )
{
	if( is_null($a) ){ die( "Equation - Discriminant - a not given\n" ); }
	if( is_null($b) ){ die( "Equation - Discriminant - b not given\n" ); }
	if( is_null($c) ){ die( "Equation - Discriminant - c not given\n" ); }

	return (($b * $b) - (4.0 * $a * $c));
}
################################################################################
#	eq_vertex_parabola(). Computes the vertex of a parabola.
#	NOTES:	REQUIRES c because it computes the discriminant.
################################################################################
function eq_vertex_parabola( $a=null, $b=null, $c=null )
{
	if( is_null($a) ){ die( "Equation - Vertex of a parabola - a not given\n" ); }
	if( is_null($b) ){ die( "Equation - Vertex of a parabola - b not given\n" ); }
	if( is_null($c) ){ die( "Equation - Vertex of a parabola - c not given\n" ); }

	$delta = $this->eq_discriminant( $a, $b, $c );
	$v1 = (-$b) / (2.0 * $a);
	$v2 = (-$delta) / (4.0 * $a );

	return array( $v1, $v2 );
}
################################################################################
#	eq_parabola(). Computes a parabola.
#	NOTES:	If X is not given - it is assumed to be one(1).
################################################################################
function eq_parabola( $a=null, $h=null, $k=null, $x=null )
{
	if( is_null($a) ){ die( "Equation - parabola - a not given\n" ); }
	if( is_null($h) ){ die( "Equation - parabola - h not given\n" ); }
	if( is_null($k) ){ die( "Equation - parabola - k not given\n" ); }
	if( is_null($x) ){ $x = 1.0; }

	return ($a * ($x - ($h * $h))) + $k;
}
################################################################################
#	vertex_parabola(). Calls the eq_vertex_parabola() function.
################################################################################
function vertex_parabola( $h=null, $k=null )
{
	if( is_null($h) ){ die( "Equation - vertex of parabola - h not given\n" ); }
	if( is_null($k) ){ die( "Equation - vertex of parabola - k not given\n" ); }

	return $this->eq_vertex_parabola( $h, $k );
}
################################################################################
#	eq_diff2sqr(). Computes the differences between two squares.
################################################################################
function eq_diff2sqr( $a=null, $b=null )
{
	if( is_null($a) ){ die( "Equation - Difference of two squares - a not given\n" ); }
	if( is_null($b) ){ die( "Equation - Difference of two squares - b not given\n" ); }

	return ($a * $a) - ($b * $b);
}
################################################################################
#	eq_pst(). Computes the Perfect Square Trinomial
################################################################################
function eq_pst( $a=null, $b=null )
{
	if( is_null($a) ){ die( "Equation - Perfect square trinomial - a not given\n" ); }
	if( is_null($b) ){ die( "Equation - Perfect square trinomial - b not given\n" ); }

	return ($a * $a) + (2.0 * $a * $b) + ($b * $b);
}
################################################################################
#	eq_binomial_theorem(). Computes the binomial theorem
################################################################################
function eq_binominal_theorem( $x=null, $y=null, $n=null )
{
	if( is_null($x) ){ die( "Equation - Binomial Theorem - x not given\n" ); }
	if( is_null($y) ){ die( "Equation - Binomial Theorem - y not given\n" ); }

	$c = 0;
	for( $i=0; $i<=$k; $i++ ){
		$a = $x ** ($n - $i);
		$b = $y ** $i;
		$c += ( $a * $b );
		}

	return $c;
}
################################################################################
#	eq_bith(). Easier to call function.
################################################################################
function eq_bith( $x=null, $y=null, $n=null )
{
	return $this->eq_binomial_theorem( $x, $y, $n );
}
################################################################################
#	do_prod1(). Calculate a^m * a^n as a^(m+n)
################################################################################
function do_prod1( $a=null, $m=null, $n=null )
{
	if( is_null($a) ){ die( "Equation - Do Product #1 - a not given\n" ); }
	if( is_null($m) ){ die( "Equation - Do Product #1 - m not given\n" ); }
	if( is_null($n) ){ die( "Equation - Do Product #1 - n not given\n" ); }

	return ($a ** ($m + $n));
}
################################################################################
#	do_prod2(). Calculate a^m * b^m as (a * b) ^ m
################################################################################
function do_prod2( $a=null, $b=null, $m=null )
{
	if( is_null($a) ){ die( "Equation - Do Product #2 - a not given\n" ); }
	if( is_null($b) ){ die( "Equation - Do Product #2 - b not given\n" ); }
	if( is_null($m) ){ die( "Equation - Do Product #2 - m not given\n" ); }

	return ($a * $b) ** $m;
}
################################################################################
#	do_quot1(). Calculate a^m / a^n as a^(m-n)
################################################################################
function do_quot1( $a=null, $m=null, $n=null )
{
	if( is_null($a) ){ die( "Equation - Do Quotient #1 - a not given\n" ); }
	if( is_null($m) ){ die( "Equation - Do Quotient #1 - m not given\n" ); }
	if( is_null($n) ){ die( "Equation - Do Quotient #1 - n not given\n" ); }

	return ($a ** ($m - $n));
}
################################################################################
#	do_quot2(). Calculate a^m / b^m as (a / b) ^ m
################################################################################
function do_quot2( $a=null, $b=null, $m=null )
{
	if( is_null($a) ){ die( "Equation - Do Quotient #2 - a not given\n" ); }
	if( is_null($b) ){ die( "Equation - Do Quotient #2 - b not given\n" ); }
	if( is_null($m) ){ die( "Equation - Do Quotient #2 - m not given\n" ); }

	if( abs($b) > 0.0 ){ return ($a / $b) ** $m; }

	return false;
}
################################################################################
#	do_pop(). Calculate (a^m)^p as a^(m*p)
################################################################################
function do_pop( $a=null, $m=null, $p=null )
{
	if( is_null($a) ){ die( "Equation - Do Power of Power - a not given\n" ); }
	if( is_null($m) ){ die( "Equation - Do Power of Power - m not given\n" ); }
	if( is_null($p) ){ die( "Equation - Do Power of Power - p not given\n" ); }

	return $a ** ($m * $p);
}
################################################################################
#	do_nexp(). Calculate a^(-n) as (1/a)^n
################################################################################
function do_nexp( $a=null, $n=null )
{
	if( is_null($a) ){ die( "Equation - Do Negative Exponents - a not given\n" ); }
	if( is_null($n) ){ die( "Equation - Do Negative Exponents - n not given\n" ); }

	return (1.0 / $a) ** $n;
}
################################################################################
#	do_fexp(). Calculate a^(p/q) as (a^p)^(1/q)
################################################################################
function do_fexp( $a=null, $p=null, $q=null )
{
	if( is_null($a) ){ die( "Equation - Do Fractional Exponents - a not given\n" ); }
	if( is_null($p) ){ die( "Equation - Do Fractional Exponents - p not given\n" ); }
	if( is_null($q) ){ die( "Equation - Do Fractional Exponents - q not given\n" ); }

	return ($a ** $p) ** (1.0 / $q);
}
################################################################################
#	magic_square1(). Do the math for a magic square - type 1
################################################################################
function magic_square1( $n=null )
{
	if( is_null($n) ){ die( "Equation - Magic Square #1 - n not given\n" ); }

	return (0.5 * $n)(($n * $n) + 1.0);
}
################################################################################
#	magic_square2(). Do the math for a magic square - type 2
################################################################################
function magic_square2( $n=null, $a=null, $d=null )
{
	if( is_null($n) ){ die( "Equation - Magic Square #2 - n not given\n" ); }
	if( is_null($a) ){ die( "Equation - Magic Square #2 - a not given\n" ); }
	if( is_null($d) ){ die( "Equation - Magic Square #2 - d not given\n" ); }

	return ($n * 0.5)(($a * 2.0) + ($d * (($n * $n) - 1.0)));
}
################################################################################
#	d2r(). Change degrees to radians
################################################################################
function d2r( $n=null )
{
	if( is_null($n) ){ die( "Degrees to Radians - n not given\n" ); }
	if( preg_match("/(\.|\d)+d/", $n) || is_numeric($n) ){ $n = deg2rad( substr($n, 0, -1) ); }
	if( preg_match("/(\.|\d)+r/", $n) ){ $n = substr( $n, 0, -1 ); }

	return $n;
}
################################################################################
#	law_cos(). Law of cosine.
#	NOTES:	"A" MUST be a radian. If you want to use degrees, make it "Ad"
#		where the "d" means degrees.
################################################################################
function law_cos( $b=null, $c=null, $a=null )
{
	if( is_null($b) ){ die( "Law of Cosine - b not given\n" ); }
	if( is_null($c) ){ die( "Law of Cosine - c not given\n" ); }
	if( is_null($a) ){ die( "Law of Cosine - a not given\n" ); }

	$a = $this->d2r( $a );

	return (($b * $b) + ($c * $c) - (2.0 * $b * $c * cos($a)));
}
################################################################################
#	heron_formula(). A never heard of formula.
################################################################################
function heron_formula( $s=null, $a=null, $b=null, $c=null )
{
	if( is_null($s) ){ die( "Heron's formula - s not given\n" ); }
	if( is_null($a) ){ die( "Heron's formula - a not given\n" ); }
	if( is_null($b) ){ die( "Heron's formula - b not given\n" ); }
	if( is_null($c) ){ die( "Heron's formula - c not given\n" ); }

	return sqrt($s * ($s - $a) * ($s - $b) * ($s - $c));
}
################################################################################
#	sina_b(). Compute sin(a+b)
################################################################################
function sina_b( $a=null, $b=null )
{
	if( is_null($a) ){ die( "sin(a+b) - a not given\n" ); }
	if( is_null($b) ){ die( "sin(a+b) - b not given\n" ); }

	return ((sin($a) * cos($b)) + (sin($b) * cos($a)));
}
################################################################################
#	cosa_b(). compute cos(a+b)
################################################################################
function cosa_b( $a=null, $b=null )
{
	if( is_null($a) ){ die( "cos(a+b) - a not given\n" ); }
	if( is_null($b) ){ die( "cos(a+b) - b not given\n" ); }

	return ((cos($a) * cos($b)) - (sin($a) * sin($b)));
}
################################################################################
#	tana_b(). Compute tan(a+b)
################################################################################
function tana_b( $a=null, $b=null )
{
	if( is_null($a) ){ die( "tan(a+b) - a not given\n" ); }
	if( is_null($b) ){ die( "tan(a+b) - b not given\n" ); }

	$t1 = tan($a) - tan($b);
	$t2 = 1 + (tab($a) * tab($b));

	if( abs($t2) > 0.0 ){ return $t1 / $t2; }

	return false;
}
################################################################################
#	sin2a(). Compute sin(2a)
################################################################################
function sin2a( $a=null )
{
	if( is_null($a) ){ die( "sin(2a) - a not given\n" ); }

	return (2.0 * sin($a) * cos($a));
}
################################################################################
#	cos2a(). Compute cos(2a)
################################################################################
function cos2a( $a=null )
{
	if( is_null($a) ){ die( "cos(2a) - a not given\n" ); }

	return ((cos(a) * cos(a)) - (sin(a) * sin(a)));
}
################################################################################
#	tan2a(). Compute tan(2a)
################################################################################
function tan2a( $a=null )
{
	if( is_null($a) ){ die( "tan(2a) - a not given\n" ); }

	$t1 = 2.0 * tan($a);
	$t2 = 1.0 - (tan($a) * tan($a));
	if( abs($t2) > 0.0 ){ return $t1 / $t2; }

	return false;
}
################################################################################
#	sum_angles(). Compute the sum of interior angles of a polygon.
#	NOTES:	It returns DEGREES
################################################################################
function sum_angles( $n=null )
{
	if( is_null($n) ){ die( "Sum of inerior angles of a polygon - n not given\n" ); }

	return ($n * 2.0) * 180;
}
################################################################################
#	p_t(). Do the Pythagorean theorem
################################################################################
function p_t( $a=null, $b=null, $c=null )
{
	if( is_null($a) ){ die( "Pythagorean theorem - a not given\n" ); }
	if( is_null($b) ){ die( "Pythagorean theorem - b not given\n" ); }
	if( is_null($c) ){ die( "Pythagorean theorem - c not given\n" ); }

	if( $a >= $b ){
		$s1 = $b;
		if( $a >= $c ){ $s2 = $c; $s3 = $a; }
			else { $s2 = $a; $s3 = $c; }
		}
		else {
			$s1 = $a;
			if( $b >= $c ){ $s2 = $c; $s3 = $b; }
				else { $s2 = $b; $s3 = $c; }
			}

	if( (($s1 * $s1) + ($s2 * $s2)) == ($s3 * $s3) ){ return true; }

	return false;
}
################################################################################
#	dist(). Calculate the distance between two points
################################################################################
function dist( $x1=null, $x2=null, $y1=null, $y2=null )
{
	if( is_null($x1) ){ die( "Distance - x1 not given\n" ); }
	if( is_null($x2) ){ die( "Distance - x2 not given\n" ); }
	if( is_null($y1) ){ die( "Distance - y1 not given\n" ); }
	if( is_null($y2) ){ die( "Distance - y2 not given\n" ); }

	return sqrt((($x1 - $x2) * ($x1 - $x2)) + (($y1 - $y2) * ($y1 - $y2)));
}
################################################################################
#	midpoint(). Calculate the midpoint of a line.
################################################################################
function midpoint( $x1=null, $x2=null, $y1=null, $y2=null )
{
	if( is_null($x1) ){ die( "Distance - x1 not given\n" ); }
	if( is_null($x2) ){ die( "Distance - x2 not given\n" ); }
	if( is_null($y1) ){ die( "Distance - y1 not given\n" ); }
	if( is_null($y2) ){ die( "Distance - y2 not given\n" ); }

	return array((($x1 + $x2) / 2.0), (($y1 + $y2) / 2.0) );
}
################################################################################
#	slope(). Calculate the slope of a line
################################################################################
function slope( $x=null, $m=null, $b=null )
{
	if( is_null($x) ){ die( "Distance - x not given\n" ); }
	if( is_null($b) ){ die( "Distance - b not given\n" ); }
	if( is_null($m) ){ die( "Distance - m not given\n" ); }

	return (($m * $x) + $b);
}
################################################################################
#	eq_plane() . Compute a plane. It will return an X, y, and z answer.
#	ARGS :	nx, ny, nz = Multiplier times where the circle starts.
#			sx, sy, sz = Start of where x, y, or z is located.
#			ex, ey, ez = End of where x, y, or z is located.
#			dx, dy, dz = Distance from the origin the circle is located.
#			ax, ay, az = Angle the circle is set at
#				(normally called pitch, yaw, and roll.)
#	NOTES:	Send everything in an array. with the above letters.
#			No angles are applied yet.
################################################################################
function eq_plane( $d )
{
	foreach( $d as $k=>$v ){
		if( is_null($v) ){ die( "Distance - $k not given\n" ); }
		}

	$x = ( $d['nx'] * ($d['sx'] - $d['ex']) ) + $d['dx'];
	$y = ( $d['ny'] * ($d['sy'] - $d['ey']) ) + $d['dy'];
	$z = ( $d['nz'] * ($d['sz'] - $d['ez']) ) + $d['dz'];

	return array( $x, $y, $z );
}
################################################################################
#	eq_circle() . Compute a plane. It will return an X, y, and z answer.
#	ARGS :	sx, sy, sz = Start of where x, y, or z is located.
#			ex, ey, ez = End of where x, y, or z is located.
#			dx, dy, dz = Distance from the origin the circle is located.
#			ax, ay, az = Angle the circle is set at
#				(normally called pitch, yaw, and roll.)
#	NOTES:	Send everything in an array. with the above letters.
#			No angles are applied yet.
#			In a CIRCLE - you only use two things like x&y, or x&z, or y&z
#	Return:	IF there is a problem - return NULL for that value.
################################################################################
function eq_circle( $d )
{
	foreach( $d as $k=>$v ){
		if( is_null($v) ){ die( "Distance - $k not given" ); }
		}

	if( !is_null($d['sx']) ){
		$x = (($d['sx'] - $d['ex']) * ($d['sx'] - $d['ex'])) + $d['dx'];
		}
		else { $x = null; }

	if( !is_null($d['sy']) ){
		$y = (($d['sy'] - $d['ey']) * ($d['sy'] - $d['ey'])) + $d['dy'];
		}
		else { $y = null; }

	if( !is_null($d['sz']) ){
		$z = (($d['sz'] - $d['ez']) * ($d['sz'] - $d['ez'])) + $d['dz'];
		}
		else { $z = null; }

	return array( $x, $y, $z );
}
################################################################################
#	eq_sphere() . Compute a plane. It will return an X, y, and z answer.
#	ARGS :	sx, sy, sz = Start of where x, y, or z is located.
#			ex, ey, ez = End of where x, y, or z is located.
#			dx, dy, dz = Distance from the origin the circle is located.
#			ax, ay, az = Angle the circle is set at
#				(normally called pitch, yaw, and roll.)
#	NOTES:	Send everything in an array. with the above letters.
#			No angles are applied yet.
#			Radius is implied by the equation.
################################################################################
function eq_sphere( $d )
{
	foreach( $d as $k=>$v ){
		if( is_null($v) ){ die( "Distance - $k not given\n" ); }
		}

	$x = (($d['sx'] - $d['ex']) * ($d['sx'] - $d['ex'])) + $d['dx'];
	$y = (($d['sy'] - $d['ey']) * ($d['sy'] - $d['ey'])) + $d['dy'];
	$z = (($d['sz'] - $d['ez']) * ($d['sz'] - $d['ez'])) + $d['dz'];

	return array( $x, $y, $z );
}
################################################################################
#	eq_ellipse() . Compute a plane. It will return an X, y, and z answer.
#	ARGS :	x, y, z = Start of where x, y, or z is located.
#			h, k, l, a, b, c = other parts of the equation
#			dx, dy, dz = Distance from the origin the circle is located.
#			ax, ay, az = Angle the circle is set at
#				(normally called pitch, yaw, and roll.)
#	NOTES:	Send everything in an array. with the above letters.
#			No angles are applied yet.
#			Only two of these is used for a 2D ellipse (ie: x&y, x&z, or y&z)
################################################################################
function eq_ellipse( $d )
{
	foreach( $d as $k=>$v ){
		if( is_null($v) ){ die( "Distance - $k not given" ); }
		}

	if( !is_null($d['x']) ){
		$x = (($d['x'] - $d['h']) / $a);
		$x = $x * $x;
		}
		else { $x = null; }

	if( !is_null($d['y']) ){
		$y = (($d['y'] - $d['k']) / $b);
		$y = $y * $y;
		}
		else { $y = null; }

	if( !is_null($d['z']) ){
		$z = (($d['z'] - $d['l']) / $c);
		$z = $z * $z;
		}
		else { $z = null; }

	return array( $x, $y, $z );
}
################################################################################
#	marks_square(). Just a hodge-podge magic square
################################################################################
function marks_square( $x=null, $y=null, $n=null )
{
	if( is_null($x) ){ die( "Mark's Square - x not given\n" ); }
	if( is_null($y) ){ die( "Mark's Square - y not given\n" ); }
	if( is_null($n) ){ die( "Mark's Square - n not given\n" ); }

	$b = [];
#
#	Clear and create array.
#
	for( $i=0; $i<$x; $i++ ){
		for( $j=0; $j<$y; $j++ ){
			$b[$i][$j] = abs(random_int( PHP_INT_MIN, PHP_INT_MAX ) ) % $n;
			}
		}

	return $b;
}
################################################################################
#	is_even(). Determines if a number is even or odd.
#	Returns:	TRUE if it is even, FALSE if it is not.
################################################################################
function is_even( $num=null )
{
	if( is_null($num) ){ return -1; }

	if( abs($num) % 2 > 0 ){ return false; }
		else { return true; }
}
################################################################################
#	collatz(). Do the Collatz Conjecture only ONCE and return.
#	NOTES:	Part of the fractal software.
################################################################################
function collatz( $num=null )
{
	if( is_null($num) ){ return -1; }

	if( ($num % 2) > 0 ){ $num = $num * 3 + 1; }
		else { $num = ($num / 2); }

	return $num;
}
################################################################################
#	hailstones(). Do the collatz_1 until we reach ONE.
#	NOTES:	Part of the fractal software.
################################################################################
function hailstones( $num=null )
{
	if( is_null($num) ){ return -1; }
		else if( $num < 0 ){ return -1; }

	$a =[];
	$b = 0;

	$b++;
	$a[] = $num;
	while( (($c = $this->collatz($num)) !== 1) && ($c > 0.001) ){
		$b++;
		$a[] = $c;
		$num = $c;
		}

	$b++;
	if( $num === 1 ){ $a[] = 1; }
		else { $a[] = 0; }

	return array( $a, $b );
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['math']) ){ $GLOBALS['classes']['math'] = new class_math(); }
?>
