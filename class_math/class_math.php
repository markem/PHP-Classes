<?php

	if( file_exists("../class_debug.php") ){
		include_once( "../class_debug.php" );
		}
		else if( !isset($GLOBALS['classes']['debug']) ){
			die( __FILE__ . ": Can not load CLASS_DEBUG" );
			}

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
#	Mark Manning			Simulacron I			Sun 01/24/2021 23:31:44.65 
#	---------------------------------------------------------------------------
#	This code is under the MIT License.
#
#END DOC
################################################################################
class class_math
{
	public $debug = false;
	private $pi = 3.14159265358979323846;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	$args = func_get_args();
	$this->debug = $GLOBALS['classes']['debug'];
	$this->debug->init( func_get_args() );
	$this->debug->in();
	$this->debug->out();
}
################################################################################
#	area_square(). computes the area of the square.
################################################################################
function area_square( $w=null, $h=null )
{
	$this->debug->in();
	if( is_null($w) ){ $this->debug->m( "Area of a square - no side given", true ); }
	if( is_null($h) ){ $h = $w; }
	$this->debug->out();

	return ($w * $h);
}
################################################################################
#	area_rectangle(). Compute the area of a rectangle.
################################################################################
function area_rectangle( $w=null, $h=null )
{
	$this->debug->in();
	$this->debug->out();
	return $this->area_square( $w, $h );
}
################################################################################
#	area_triangle(). Compute the area of a triangle.
################################################################################
function area_triangle( $b=null, $h=null )
{
	$this->debug->in();
	$this->debug->out();
	return $this->area_square($b, $h) / 2.0;
}
################################################################################
#	area_rhombus(). Compute the area of a rhombus (diamond)
#	Notes:	D is BIG diagonal, d is small diagonal
################################################################################
function area_rhombus( $D=null, $d=null )
{
	$this->debug->in();
	if( is_null($D) ){ $this->debug->m( "Area of a rhombus - D not given", true ); }
	if( is_null($d) ){ $this->debug->m( "Area of a rhombus - d not given", true ); }
	$this->debug->out();

	return (($D * $d) / 2.0);
}
################################################################################
#	area_trapezoid(). Compute the area of a trapezoid
################################################################################
function area_trapezoid( $B=null, $b=null, $h=null )
{
	$this->debug->in();
	if( is_null($B) ){ $this->debug->m( "Area of a trapezoid - B not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Area of a trapezoid - b not given", true ); }
	if( is_null($h) ){ $this->debug->m( "Area of a trapezoid - h not given", true ); }
	$this->debug->out();

	return (($B + $b) / 2.0) * $h;
}
################################################################################
#	area_polygon(). Compute the area of a polygon.
#	Notes: P is perimeter, a is apothem
################################################################################
function area_polygon( $P=null, $a=null )
{
	$this->debug->in();
	if( is_null($P) ){ $this->debug->m( "Area of a polygon - P not given", true ); }
	if( is_null($a) ){ $this->debug->m( "Area of a polygon - a not given", true ); }
	$this->debug->out();

	return ($P / 2.0) * $a;
}
################################################################################
#	area_circle(). Computes the area of the circle.
################################################################################
function area_circle( $r=null )
{
	$this->debug->in();
	if( is_null($r) ){ $this->debug->m( "Area of a circle - r not given", true ); }
	$this->debug->out();

	return (($r * $r) * $this->PI );
}
################################################################################
#	area_perimeter(). Computes the perimeter (circumferance)
################################################################################
function area_perimeter( $r=null )
{
	$this->debug->in();
	if( is_null($r) ){ $this->debug->m( "Area of a circles perimeter - r not given", true ); }
	$this->debug->out();

	return (2.0 * $this->PI * $r);
}
################################################################################
#	area_cone(). Computes the area of a cone
################################################################################
function area_cone( $r=null, $s=null )
{
	$this->debug->in();
	if( is_null($r) ){ $this->debug->m( "Area of a cone - r not given", true ); }
	if( is_null($s) ){ $this->debug->m( "Area of a cone - s not given", true ); }
	$this->debug->out();

	return ($this->PI * $r) * $s;
}
################################################################################
#	area_sphere(). Computes the area of a sphere.
################################################################################
function area_sphere( $r )
{
	$this->debug->in();
	if( is_null($r) ){ $this->debug->m( "Area of a sphere - r not given", true ); }
	$this->debug->out();

	return (4.0 * $this->PI * ($r * $r));
}
################################################################################
#	vol_cube(). computes the volume of a cube
################################################################################
function vol_cube( $w=null, $h=null )
{
	$this->debug->in();
	if( is_null($w) ){ $this->debug->m( "Volume of a cube - w not given", true ); }
	if( is_null($h) ){ $h = $w; }
	$this->debug->out();

	return ( $w * $h * $w );
}
################################################################################
#	vol_parallelpiped(). Comput the volume of a parallelpiped
################################################################################
function vol_parallelepiped( $l=null, $w=null, $h=null )
{
	$this->debug->in();
	if( is_null($l) ){ $this->debug->m( "Volume of a parallelepiped - l not given", true ); }
	if( is_null($w) ){ $this->debug->m( "Volume of a parallelepiped - w not given", true ); }
	if( is_null($h) ){ $this->debug->m( "Volume of a parallelepiped - h not given", true ); }
	$this->debug->out();

	return ($l * $w * $h);
}
################################################################################
#	vol_ppp(). An easier call to vol_parallelepiped.
################################################################################
function vol_ppp( $l=null, $w=null, $h=null )
{
	$this->debug->in();
	$this->debug->out();
	return vol_parallelepiped( $l, $w, $h );
}
################################################################################
#	volume_prism(). Computes the volume of a prism.
################################################################################
function vol_prism( $b=null, $h=null )
{
	$this->debug->in();
	if( is_null($b) ){ $this->debug->m( "Volume of a prism - b not given", true ); }
	if( is_null($h) ){ $this->debug->m( "Volume of a prism - h not given", true ); }
	$this->debug->out();

	return ($b * $h);
}
################################################################################
#	volume_cylinder(). Computes the volume of a cylinder
################################################################################
function volume_cylinder( $r=null, $h=null )
{
	$this->debug->in();
	if( is_null($r) ){ $this->debug->m( "Volume of a cylinder - r not given", true ); }
	if( is_null($h) ){ $this->debug->m( "Volume of a cylinder - h not given", true ); }
	$this->debug->out();

	return ($this->PI * ($r * $r)) * $h;
}
################################################################################
#	volume_cone(). Compute the volume of a cone.
################################################################################
function volume_cone( $b=null, $h=null )
{
	$this->debug->in();
	if( is_null($b) ){ $this->debug->m( "Volume of a cone - b not given", true ); }
	if( is_null($h) ){ $this->debug->m( "Volume of a cone - h not given", true ); }
	$this->debug->out();

	return ($b * $h) / 3.0;
}
################################################################################
#	volume_sphere(). Computes the volume of a sphere.
################################################################################
function volume_sphere( $r=null )
{
	$this->debug->in();
	if( is_null($r) ){ $this->debug->m( "Volume of a sphere - r not given", true ); }
	$this->debug->out();

	return (($r * $r * $r) * $this->PI) * (4.0 / 3.0);
}
################################################################################
#	Eq_dp(). Determine if three numbers are directly proportional.
################################################################################
function eq_dp( $x=null, $y=null, $k=null )
{
	$this->debug->in();
	if( is_null($x) ){ $this->debug->m( "Equation - Directly Proportional - x not given", true ); }
	if( is_null($y) ){ $this->debug->m( "Equation - Directly Proportional - y not given", true ); }
	if( is_null($k) ){ $this->debug->m( "Equation - Directly Proportional - k not given", true ); }

	if( ($y == ($k * $x)) && ($k == ($y / $x)) ){ return true; }
	$this->debug->out();

	return false;
}
################################################################################
#	eq_ip(). Determine if three numbers are inversely proportional
################################################################################
function eq_ip( $x=null, $y=null, $k=null )
{
	$this->debug->in();
	if( is_null($x) ){ $this->debug->m( "Equation - Inversely Proportional - x not given", true ); }
	if( is_null($y) ){ $this->debug->m( "Equation - Inversely Proportional - y not given", true ); }
	if( is_null($k) ){ $this->debug->m( "Equation - Inversely Proportional - k not given", true ); }

	if( ($y == ($k / $x)) && ($k = ($y * $x)) ){ return true; }
	$this->debug->out();

	return false;
}
################################################################################
#	eq_quadratic(). Computes the quadratic equaion and returns TWO answers.
################################################################################
function eq_quadratic( $a=null, $b=null, $c=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Quadratic formula - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Equation - Quadratic formula - b not given", true ); }
	if( is_null($c) ){ $this->debug->m( "Equation - Quadratic formula - c not given", true ); }

	if( $a !== 0.0 ){
		$d = sqrt(($b * $b) - (4.0 * $a * $c));
		$r1 = ((-$b) + $d) / (2.0 * $a);
		$r2 = ((-$b) - $d) / (2.0 * $a);
		return array( $r1, $r2 );
		}

	$this->debug->out();

	return false;
}
################################################################################
#	eq_line(). Get a line's equation. Returns Y
################################################################################
function eq_line( $a=null, $b=null, $c=null, $x=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation of a line - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Equation of a line - b not given", true ); }
	if( is_null($c) ){ $this->debug->m( "Equation of a line - c not given", true ); }
	if( is_null($x) ){ $this->debug->m( "Equation of a line - x not given", true ); }
	$this->debug->out();

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
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Concavity - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Equation - Concavity - b not given", true ); }
	if( is_null($c) ){ $this->debug->m( "Equation - Concavity - c not given", true ); }

	if( $a > 0.0 ){ return "up"; }
		else if( $a < 0.0 ){ return "down"; }

	$this->debug->out();

	return false;
}
################################################################################
#	eq_discriminant(). Computes the discriminant (delta)
################################################################################
function eq_discriminant( $a=null, $b=null, $c=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Discriminant - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Equation - Discriminant - b not given", true ); }
	if( is_null($c) ){ $this->debug->m( "Equation - Discriminant - c not given", true ); }
	$this->debug->out();

	return (($b * $b) - (4.0 * $a * $c));
}
################################################################################
#	eq_vertex_parabola(). Computes the vertex of a parabola.
#	NOTES:	REQUIRES c because it computes the discriminant.
################################################################################
function eq_vertex_parabola( $a=null, $b=null, $c=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Vertex of a parabola - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Equation - Vertex of a parabola - b not given", true ); }
	if( is_null($c) ){ $this->debug->m( "Equation - Vertex of a parabola - c not given", true ); }

	$delta = $this->eq_discriminant( $a, $b, $c );
	$v1 = (-$b) / (2.0 * $a);
	$v2 = (-$delta) / (4.0 * $a );
	$this->debug->out();

	return array( $v1, $v2 );
}
################################################################################
#	eq_parabola(). Computes a parabola.
#	NOTES:	If X is not given - it is assumed to be one(1).
################################################################################
function eq_parabola( $a=null, $h=null, $k=null, $x=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - parabola - a not given", true ); }
	if( is_null($h) ){ $this->debug->m( "Equation - parabola - h not given", true ); }
	if( is_null($k) ){ $this->debug->m( "Equation - parabola - k not given", true ); }
	if( is_null($x) ){ $x = 1.0; }
	$this->debug->out();

	return ($a * ($x - ($h * $h))) + $k;
}
################################################################################
#	vertex_parabola(). Calls the eq_vertex_parabola() function.
################################################################################
function vertex_parabola( $h=null, $k=null )
{
	$this->debug->in();
	if( is_null($h) ){ $this->debug->m( "Equation - vertex of parabola - h not given", true ); }
	if( is_null($k) ){ $this->debug->m( "Equation - vertex of parabola - k not given", true ); }
	$this->debug->out();

	return $this->eq_vertex_parabola( $h, $k );
}
################################################################################
#	eq_diff2sqr(). Computes the differences between two squares.
################################################################################
function eq_diff2sqr( $a=null, $b=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Difference of two squares - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Equation - Difference of two squares - b not given", true ); }
	$this->debug->out();

	return ($a * $a) - ($b * $b);
}
################################################################################
#	eq_pst(). Computes the Perfect Square Trinomial
################################################################################
function eq_pst( $a=null, $b=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Perfect square trinomial - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Equation - Perfect square trinomial - b not given", true ); }
	$this->debug->out();

	return ($a * $a) + (2.0 * $a * $b) + ($b * $b);
}
################################################################################
#	eq_binomial_theorem(). Computes the binomial theorem
################################################################################
function eq_binominal_theorem( $x=null, $y=null, $n=null )
{
	$this->debug->in();
	if( is_null($x) ){ $this->debug->m( "Equation - Binomial Theorem - x not given", true ); }
	if( is_null($y) ){ $this->debug->m( "Equation - Binomial Theorem - y not given", true ); }

	$c = 0;
	for( $i=0; $i<=$k; $i++ ){
		$a = $x ** ($n - $i);
		$b = $y ** $i;
		$c += ( $a * $b );
		}

	$this->debug->out();

	return $c;
}
################################################################################
#	eq_bith(). Easier to call function.
################################################################################
function eq_bith( $x=null, $y=null, $n=null )
{
	$this->debug->in();
	$this->debug->out();
	return $this->eq_binomial_theorem( $x, $y, $n );
}
################################################################################
#	do_prod1(). Calculate a^m * a^n as a^(m+n)
################################################################################
function do_prod1( $a=null, $m=null, $n=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Do Product #1 - a not given", true ); }
	if( is_null($m) ){ $this->debug->m( "Equation - Do Product #1 - m not given", true ); }
	if( is_null($n) ){ $this->debug->m( "Equation - Do Product #1 - n not given", true ); }
	$this->debug->out();

	return ($a ** ($m + $n));
}
################################################################################
#	do_prod2(). Calculate a^m * b^m as (a * b) ^ m
################################################################################
function do_prod2( $a=null, $b=null, $m=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Do Product #2 - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Equation - Do Product #2 - b not given", true ); }
	if( is_null($m) ){ $this->debug->m( "Equation - Do Product #2 - m not given", true ); }
	$this->debug->out();

	return ($a * $b) ** $m;
}
################################################################################
#	do_quot1(). Calculate a^m / a^n as a^(m-n)
################################################################################
function do_quot1( $a=null, $m=null, $n=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Do Quotient #1 - a not given", true ); }
	if( is_null($m) ){ $this->debug->m( "Equation - Do Quotient #1 - m not given", true ); }
	if( is_null($n) ){ $this->debug->m( "Equation - Do Quotient #1 - n not given", true ); }
	$this->debug->out();

	return ($a ** ($m - $n));
}
################################################################################
#	do_quot2(). Calculate a^m / b^m as (a / b) ^ m
################################################################################
function do_quot2( $a=null, $b=null, $m=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Do Quotient #2 - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Equation - Do Quotient #2 - b not given", true ); }
	if( is_null($m) ){ $this->debug->m( "Equation - Do Quotient #2 - m not given", true ); }

	if( abs($b) > 0.0 ){ return ($a / $b) ** $m; }
	$this->debug->out();

	return false;
}
################################################################################
#	do_pop(). Calculate (a^m)^p as a^(m*p)
################################################################################
function do_pop( $a=null, $m=null, $p=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Do Power of Power - a not given", true ); }
	if( is_null($m) ){ $this->debug->m( "Equation - Do Power of Power - m not given", true ); }
	if( is_null($p) ){ $this->debug->m( "Equation - Do Power of Power - p not given", true ); }
	$this->debug->out();

	return $a ** ($m * $p);
}
################################################################################
#	do_nexp(). Calculate a^(-n) as (1/a)^n
################################################################################
function do_nexp( $a=null, $n=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Do Negative Exponents - a not given", true ); }
	if( is_null($n) ){ $this->debug->m( "Equation - Do Negative Exponents - n not given", true ); }
	$this->debug->out();

	return (1.0 / $a) ** $n;
}
################################################################################
#	do_fexp(). Calculate a^(p/q) as (a^p)^(1/q)
################################################################################
function do_fexp( $a=null, $p=null, $q=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Equation - Do Fractional Exponents - a not given", true ); }
	if( is_null($p) ){ $this->debug->m( "Equation - Do Fractional Exponents - p not given", true ); }
	if( is_null($q) ){ $this->debug->m( "Equation - Do Fractional Exponents - q not given", true ); }
	$this->debug->out();

	return ($a ** $p) ** (1.0 / $q);
}
################################################################################
#	magic_square1(). Do the math for a magic square - type 1
################################################################################
function magic_square1( $n=null )
{
	$this->debug->in();
	if( is_null($n) ){ $this->debug->m( "Equation - Magic Square #1 - n not given", true ); }
	$this->debug->out();

	return (0.5 * $n)(($n * $n) + 1.0);
}
################################################################################
#	magic_square2(). Do the math for a magic square - type 2
################################################################################
function magic_square2( $n=null, $a=null, $d=null )
{
	$this->debug->in();
	if( is_null($n) ){ $this->debug->m( "Equation - Magic Square #2 - n not given", true ); }
	if( is_null($a) ){ $this->debug->m( "Equation - Magic Square #2 - a not given", true ); }
	if( is_null($d) ){ $this->debug->m( "Equation - Magic Square #2 - d not given", true ); }
	$this->debug->out();

	return ($n * 0.5)(($a * 2.0) + ($d * (($n * $n) - 1.0)));
}
################################################################################
#	d2r(). Change degrees to radians
################################################################################
function d2r( $n=null )
{
	$this->debug->in();
	if( is_null($n) ){ $this->debug->m( "Degrees to Radians - n not given", true ); }
	if( preg_match("/\d+d/", $n) || ($n > $this->PI) ){ $n = deg2rad( substr($n, 0, -1) ); }
	if( preg_match("/\d+r/", $n) ){ $n = substr( $n, 0, -1 ); }
	$this->debug->out();

	return $n;
}
################################################################################
#	law_cos(). Law of cosine.
#	NOTES:	"A" MUST be a radian. If you want to use degrees, make it "Ad"
#		where the "d" means degrees.
################################################################################
function law_cos( $b=null, $c=null, $a=null )
{
	$this->debug->in();
	if( is_null($b) ){ $this->debug->m( "Law of Cosine - b not given", true ); }
	if( is_null($c) ){ $this->debug->m( "Law of Cosine - c not given", true ); }
	if( is_null($a) ){ $this->debug->m( "Law of Cosine - a not given", true ); }

	$a = $this->d2r( $a );
	$this->debug->out();

	return (($b * $b) + ($c * $c) - (2.0 * $b * $c * cos($a)));
}
################################################################################
#	heron_formula(). A never heard of formula.
################################################################################
function heron_formula( $s=null, $a=null, $b=null, $c=null )
{
	$this->debug->in();
	if( is_null($s) ){ $this->debug->m( "Heron's formula - s not given", true ); }
	if( is_null($a) ){ $this->debug->m( "Heron's formula - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Heron's formula - b not given", true ); }
	if( is_null($c) ){ $this->debug->m( "Heron's formula - c not given", true ); }

	return sqrt($s * ($s - $a) * ($s - $b) * ($s - $c));
}
################################################################################
#	sina_b(). Compute sin(a+b)
################################################################################
function sina_b( $a=null, $b=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "sin(a+b) - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "sin(a+b) - b not given", true ); }
	$this->debug->out();

	return ((sin($a) * cos($b)) + (sin($b) * cos($a)));
}
################################################################################
#	cosa_b(). compute cos(a+b)
################################################################################
function cosa_b( $a=null, $b=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "cos(a+b) - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "cos(a+b) - b not given", true ); }
	$this->debug->out();

	return ((cos($a) * cos($b)) - (sin($a) * sin($b)));
}
################################################################################
#	tana_b(). Compute tan(a+b)
################################################################################
function tana_b( $a=null, $b=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "tan(a+b) - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "tan(a+b) - b not given", true ); }

	$t1 = tan($a) - tan($b);
	$t2 = 1 + (tab($a) * tab($b));

	if( abs($t2) > 0.0 ){ return $t1 / $t2; }
	$this->debug->out();

	return false;
}
################################################################################
#	sin2a(). Compute sin(2a)
################################################################################
function sin2a( $a=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "sin(2a) - a not given", true ); }
	$this->debug->out();

	return (2.0 * sin($a) * cos($a));
}
################################################################################
#	cos2a(). Compute cos(2a)
################################################################################
function cos2a( $a=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "cos(2a) - a not given", true ); }
	$this->debug->out();

	return ((cos(a) * cos(a)) - (sin(a) * sin(a)));
}
################################################################################
#	tan2a(). Compute tan(2a)
################################################################################
function tan2a( $a=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "tan(2a) - a not given", true ); }

	$t1 = 2.0 * tan($a);
	$t2 = 1.0 - (tan($a) * tan($a));
	if( abs($t2) > 0.0 ){ return $t1 / $t2; }
	$this->debug->out();

	return false;
}
################################################################################
#	sum_angles(). Compute the sum of interior angles of a polygon.
#	NOTES:	It returns DEGREES
################################################################################
function sum_angles( $n=null )
{
	$this->debug->in();
	if( is_null($n) ){ $this->debug->m( "Sum of inerior angles of a polygon - n not given", true ); }
	$this->debug->out();

	return ($n * 2.0) * 180;
}
################################################################################
#	p_t(). Do the Pythagorean theorem
################################################################################
function p_t( $a=null, $b=null, $c=null )
{
	$this->debug->in();
	if( is_null($a) ){ $this->debug->m( "Pythagorean theorem - a not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Pythagorean theorem - b not given", true ); }
	if( is_null($c) ){ $this->debug->m( "Pythagorean theorem - c not given", true ); }

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
	$this->debug->out();

	return false;
}
################################################################################
#	dist(). Calculate the distance between two points
################################################################################
function dist( $x1=null, $x2=null, $y1=null, $y2=null )
{
	$this->debug->in();
	if( is_null($x1) ){ $this->debug->m( "Distance - x1 not given", true ); }
	if( is_null($x2) ){ $this->debug->m( "Distance - x2 not given", true ); }
	if( is_null($y1) ){ $this->debug->m( "Distance - y1 not given", true ); }
	if( is_null($y2) ){ $this->debug->m( "Distance - y2 not given", true ); }
	$this->debug->out();

	return sqrt((($x1 - $x2) * ($x1 - $x2)) + (($y1 - $y2) * ($y1 - $y2)));
}
################################################################################
#	midpoint(). Calculate the midpoint of a line.
################################################################################
function midpoint( $x1=null, $x2=null, $y1=null, $y2=null )
{
	$this->debug->in();
	if( is_null($x1) ){ $this->debug->m( "Distance - x1 not given", true ); }
	if( is_null($x2) ){ $this->debug->m( "Distance - x2 not given", true ); }
	if( is_null($y1) ){ $this->debug->m( "Distance - y1 not given", true ); }
	if( is_null($y2) ){ $this->debug->m( "Distance - y2 not given", true ); }
	$this->debug->out();

	return array((($x1 + $x2) / 2.0), (($y1 + $y2) / 2.0) );
}
################################################################################
#	slope(). Calculate the slope of a line
################################################################################
function slope( $x=null, $m=null, $b=null )
{
	$this->debug->in();
	if( is_null($x) ){ $this->debug->m( "Distance - x not given", true ); }
	if( is_null($b) ){ $this->debug->m( "Distance - b not given", true ); }
	if( is_null($m) ){ $this->debug->m( "Distance - m not given", true ); }
	$this->debug->out();

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
	$this->debug->in();
	foreach( $d as $k=>$v ){
		if( is_null($v) ){ $this->debug->m( "Distance - $k not given", true ); }
		}

	$a = [];
	$a['x'] = ( $d['nx'] * ($d['sx'] - $d['ex']) ) + $d['dx'];
	$a['y'] = ( $d['ny'] * ($d['sy'] - $d['ey']) ) + $d['dy'];
	$a['z'] = ( $d['nz'] * ($d['sz'] - $d['ez']) ) + $d['dz'];
	$this->debug->out();

	return $a;
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
################################################################################
function eq_circle( $d )
{
	$this->debug->in();
	foreach( $d as $k=>$v ){
		if( is_null($v) ){ $this->debug->m( "Distance - $k not given" ); }
		}

	$a = [];

	if( !is_null($d['sx']) ){
		$a['x'] = (($d['sx'] - $d['ex']) * ($d['sx'] - $d['ex'])) + $d['dx'];
		}

	if( !is_null($d['sy']) ){
		$a['y'] = (($d['sy'] - $d['ey']) * ($d['sy'] - $d['ey'])) + $d['dy'];
		}

	if( !is_null($d['sz']) ){
		$a['z'] = (($d['sz'] - $d['ez']) * ($d['sz'] - $d['ez'])) + $d['dz'];
		}

	$this->debug->out();

	return $a;
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
	$this->debug->in();
	foreach( $d as $k=>$v ){
		if( is_null($v) ){ $this->debug->m( "Distance - $k not given", true ); }
		}

	$a = [];
	$a['x'] = (($d['sx'] - $d['ex']) * ($d['sx'] - $d['ex'])) + $d['dx'];
	$a['y'] = (($d['sy'] - $d['ey']) * ($d['sy'] - $d['ey'])) + $d['dy'];
	$a['z'] = (($d['sz'] - $d['ez']) * ($d['sz'] - $d['ez'])) + $d['dz'];
	$this->debug->out();

	return $a;
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
	$this->debug->in();
	foreach( $d as $k=>$v ){
		if( is_null($v) ){ $this->debug->m( "Distance - $k not given" ); }
		}

	$a = [];

	if( !is_null($d['x']) ){
		$x = (($d['x'] - $d['h']) / $a);
		$a['x'] = $x * $x;
		}

	if( !is_null($d['y']) ){
		$y = (($d['y'] - $d['k']) / $b);
		$a['y'] = $y * $y;
		}

	if( !is_null($d['z']) ){
		$z = (($d['z'] - $d['l']) / $c);
		$a['z'] = $z * $z;
		}

	$this->debug->out();

	return $a;
}
################################################################################
#	marks_square(). Just a hodge-podge magic square
################################################################################
function marks_square( $x=null, $y=null, $n=null )
{
	$this->debug->in();
	if( is_null($x) ){ $this->debug->m( "Mark's Square - x not given", true ); }
	if( is_null($y) ){ $this->debug->m( "Mark's Square - y not given", true ); }
	if( is_null($n) ){ $this->debug->m( "Mark's Square - n not given", true ); }

	$b = [];
#
#	Clear and create array.
#
	for( $i=0; $i<$x; $i++ ){
		for( $j=0; $j<$y; $j++ ){
			$b[$i][$j] = abs(random_int( PHP_INT_MIN, PHP_INT_MAX ) ) % $n;
			}
		}

	$this->debug->out();

	return $b;
}

}

?>
