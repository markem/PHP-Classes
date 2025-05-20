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

	include_once( "$lib/class_pr.php" );
	include_once( "$lib/class_serial.php" );

	$pr = $GLOBALS['classes']['pr'];
	$cs = $GLOBALS['classes']['serial'];

	$dq = '"';
	$cwd = getcwd();
	$cwd = str_replace( "\\", "/", $cwd );

$pr->pr( $cwd, "CWD =" );

	$dir_images = '$cwd/In';
	$dir_images = str_replace( "\\", "/", $dir_images );

	$stderr = "$cwd/stderr.txt";
#
#	Put whatever file you want to try on your machine. I'm using
#	an image of the Black Lotus from Wizards of the Coast
#	Magic the Gathering game. As you can see, it is a 150 DPI
#	image that I put into Photoshop and did a halftone image.
#	What is a halftone image? It is an image that is only
#	black and white which emulates a greyscale image by how the
#	dots are arranged.
#
	$file = "vma-4-black-lotus-150dpi-halftone-line.bmp";
#
#	Section for the INKEY part. Do NOT use
#
if( false ){
	$cs->cwd( $cwd );
	$cs->bas( "inkey.bas" );
	$cs->makeQBKey();
	exit;
	}
#
#	If you are going to use the FreeBasic program then use the following:
#
if( false ){
	$cs->exe( "inkey32.exe" );
#
#	But if you want to use the QuickBasic program - then use the following:
#
}
else {
	$cs->exe( "inkey.exe" );
}
#
#	Do the up/down of the mouse. We already know the mouse can only go up to zero(0)
#	and down to 1,000.
#
	$a = "n";
	$cs->cwd( $cwd );
	$modes = $cs->modes();
	$cs->set( $modes['con'] );
	$ret = $cs->open();
	$a = explode( "\n", $ret );
	$pr->pr( $a, "A1 = " );
	for( $i=0; $i<100; $i++ ){
		$ret = $cs->read();
		$a = explode( "\n", $ret );
		$pr->pr( $a, "A3 = " );
		}

echo "HERE<br>\n";

