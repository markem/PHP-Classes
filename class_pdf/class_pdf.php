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
#	class_pdf();
#
#-Description:
#
#	A class to deal with PDF files
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
#	Mark Manning			Simulacron I			Mon 08/05/2019 13:52:01.25 
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
#		CLASS_PDF.PHP. A class to handle working with PDFs.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_pdf
{
################################################################################
#	__construct(). Constructor
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['pdf']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['pdf']; }
}
################################################################################
#	init(). A function to allow for re-initialization of this class.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}
}
################################################################################
#	pdf_info(). Calls on the program pdfinfo to get the information about a pdf
#		file.
################################################################################
function pdf_info( $file=null )
{
#
#	Get a file
#
	$dq = '"';
	$cmd = "pdfinfo $dq$file$dq";
#	echo "Command : $cmd\n";
#
#	Get the information
#
	$fp = popen( $cmd, "r" );
#	echo gettype( $fp ) . "\n";
	$out = fread( $fp, 2048 );
	pclose( $fp );

	$out = explode( "\n", $out );
	array_pop( $out );

	$b = [];
	$b['title'] = null;
	$b['subject'] = null;
	$b['keywords'] = null;
	$b['author'] = null;
	$b['creator'] = null;
	$b['producer'] = null;
	$b['creationdate'] = null;
	$b['moddate'] = null;
	$b['tagged'] = null;
	$b['form'] = null;
	$b['pages'] = null;
	$b['encrypted'] = null;
	$b['Page size'] = null;
	$b['file size'] = null;
	$b['optimized'] = null;
	$b['pdf version'] = null;
	$b['w'] = null;
	$b['h'] = null;

	foreach( $out as $k=>$v ){
#		echo "Line : $k = $v\n";
#
#	          1         2         3         4
#	01234567890123456789012345678901234567890123456789
#	Title:          City of Splendors: Waterdeep
#	Subject:        Scanned by Rob
#	Keywords:       
#	Author:         Eric L. Boyd
#	Creator:        CanoScan D660U
#	Producer:       PDFScanLib v1.2.2 in Adobe Acrobat 7.0
#	CreationDate:   Thu Oct  6 03:03:31 2005
#	ModDate:        Sat May  6 10:49:50 2006
#	Tagged:         no
#	Form:           AcroForm
#	Pages:          183
#	Encrypted:      no
#	Page size:      610.56 x 802.56 pts (rotated 0 degrees)
#	File size:      18592769 bytes
#	Optimized:      yes
#	PDF version:    1.6
#
		$t = trim( substr($v, 0, 16) );
		$t = strtolower( substr( $t, 0, -1 ) );
		$d = strtolower( trim( substr( $v, 16, strlen($v) ) ) );
		$b[$t] = $d;

		if( preg_match("/^page size/i", $v) ){
			$v = preg_replace( "/\s+/", " ", $v );
			$a = explode( " ", $v );
			$b['w'] = $a[2];
			$b['h'] = $a[4];
			}
		}

	return $b;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['pdf']) ){
		$GLOBALS['classes']['pdf'] = new class_pdf();
		}

?>
