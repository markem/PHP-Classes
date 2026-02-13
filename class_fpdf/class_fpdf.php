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
#	class_fpdf();
#
#-Description:
#
#	A class to extend the FPDF file.
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
#	Mark Manning			Simulacron I			Tue 12/29/2020 22:57:10.44 
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
#		CLASS_FPDF.PHP. A class to handle working with PDF files.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_fpdf
{
	private $pdf = null;
	private $htm = null;
	private $html = null;
	private $file = null;
	private	$fpdf = null;
	private $funcs = null;

################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['fpdf']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['fpdf']; }
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

	$this->get_html();
	$this->fpdf = new FPDF();
	$this->make_pdf( $args );
}
################################################################################
#	get_html(). Get all of the HTML commands possible.
#
#	Notes:	Taken from https://www.w3schools.com/tags/
#				All commands are from the w3school website.
#
#			Note that ALL HTML commands are here - even if no longer in HTML5
#				because who knows WHAT version of HTML the person is using.
################################################################################
private function get_html()
{
	$this->htm = [];	#	List of HTML commands
	$this->html = [];
	$this->funcs = [];	#	Functions to call.

	$cmds = <<<EOD
<!-- ... -->	Defines a comment
<!DOCTYPE>  	Defines the document type
<a> 	Defines a hyperlink
<abbr> 	Defines an abbreviation or an acronym
<acronym> 	Not supported in HTML5. Use <abbr> instead.  Defines an acronym
<address> 	Defines contact information for the author/owner of a document
<applet> 	Not supported in HTML5. Use <embed> or <object> instead.  Defines an embedded applet
<area> 	Defines an area inside an image map
<article> 	Defines an article
<aside> 	Defines content aside from the page content
<audio> 	Defines embedded sound content
<b> 	Defines bold text
<base> 	Specifies the base URL/target for all relative URLs in a document
<basefont> 	Not supported in HTML5. Use CSS instead.  Specifies a default color, size, and font for all text in a document
<bdi> 	Isolates a part of text that might be formatted in a different direction from other text outside it
<bdo> 	Overrides the current text direction
<big> 	Not supported in HTML5. Use CSS instead.  Defines big text
<blockquote> 	Defines a section that is quoted from another source
<body> 	Defines the document's body
<br> 	Defines a single line break
<button> 	Defines a clickable button
<canvas> 	Used to draw graphics, on the fly, via scripting (usually JavaScript)
<caption> 	Defines a table caption
<center> 	Not supported in HTML5. Use CSS instead.  Defines centered text
<cite> 	Defines the title of a work
<code> 	Defines a piece of computer code
<col> 	Specifies column properties for each column within a <colgroup> element 
<colgroup> 	Specifies a group of one or more columns in a table for formatting
<data> 	Adds a machine-readable translation of a given content
<datalist> 	Specifies a list of pre-defined options for input controls
<dd> 	Defines a description/value of a term in a description list
<del> 	Defines text that has been deleted from a document
<details> 	Defines additional details that the user can view or hide
<dfn> 	Specifies a term that is going to be defined within the content
<dialog> 	Defines a dialog box or window
<dir> 	Not supported in HTML5. Use <ul> instead.  Defines a directory list
<div> 	Defines a section in a document
<dl> 	Defines a description list
<dt> 	Defines a term/name in a description list
<em> 	Defines emphasized text 
<embed> 	Defines a container for an external application
<fieldset> 	Groups related elements in a form
<figcaption> 	Defines a caption for a <figure> element
<figure> 	Specifies self-contained content
<font> 	Not supported in HTML5. Use CSS instead.  Defines font, color, and size for text
<footer> 	Defines a footer for a document or section
<form> 	Defines an HTML form for user input
<frame> 	Not supported in HTML5.  Defines a window (a frame) in a frameset
<frameset> 	Not supported in HTML5.  Defines a set of frames
<h1> 	Defines HTML headings
<h2> 	Defines HTML headings
<h3> 	Defines HTML headings
<h4> 	Defines HTML headings
<h5> 	Defines HTML headings
<h6> 	Defines HTML headings
<head> 	Contains metadata/information for the document
<header> 	Defines a header for a document or section
<hr> 	Defines a thematic change in the content
<html> 	Defines the root of an HTML document
<i> 	Defines a part of text in an alternate voice or mood
<iframe> 	Defines an inline frame
<img> 	Defines an image
<input> 	Defines an input control
<ins> 	Defines a text that has been inserted into a document
<kbd> 	Defines keyboard input
<label> 	Defines a label for an <input> element
<legend> 	Defines a caption for a <fieldset> element
<li> 	Defines a list item
<link> 	Defines the relationship between a document and an external resource (most used to link to style sheets)
<main> 	Specifies the main content of a document
<map> 	Defines an image map
<mark> 	Defines marked/highlighted text
<meta> 	Defines metadata about an HTML document
<meter> 	Defines a scalar measurement within a known range (a gauge)
<nav> 	Defines navigation links
<noframes> 	Not supported in HTML5.  Defines an alternate content for users that do not support frames
<noscript> 	Defines an alternate content for users that do not support client-side scripts
<object> 	Defines a container for an external application
<ol> 	Defines an ordered list
<optgroup> 	Defines a group of related options in a drop-down list
<option> 	Defines an option in a drop-down list
<output> 	Defines the result of a calculation
<p> 	Defines a paragraph
<param> 	Defines a parameter for an object
<picture> 	Defines a container for multiple image resources
<pre> 	Defines preformatted text
<progress> 	Represents the progress of a task
<q> 	Defines a short quotation
<rp> 	Defines what to show in browsers that do not support ruby annotations
<rt> 	Defines an explanation/pronunciation of characters (for East Asian typography)
<ruby> 	Defines a ruby annotation (for East Asian typography)
<s> 	Defines text that is no longer correct
<samp> 	Defines sample output from a computer program
<script> 	Defines a client-side script
<section> 	Defines a section in a document
<select> 	Defines a drop-down list
<small> 	Defines smaller text
<source> 	Defines multiple media resources for media elements (<video> and <audio>)
<span> 	Defines a section in a document
<strike> 	Not supported in HTML5. Use <del> or <s> instead.  Defines strikethrough text
<strong> 	Defines important text
<style> 	Defines style information for a document
<sub> 	Defines subscripted text
<summary> 	Defines a visible heading for a <details> element
<sup> 	Defines superscripted text
<svg> 	Defines a container for SVG graphics
<table> 	Defines a table
<tbody> 	Groups the body content in a table
<td> 	Defines a cell in a table
<template> 	Defines a container for content that should be hidden when the page loads
<textarea> 	Defines a multiline input control (text area)
<tfoot> 	Groups the footer content in a table
<th> 	Defines a header cell in a table
<thead> 	Groups the header content in a table
<time> 	Defines a specific time (or datetime)
<title> 	Defines a title for the document
<tr> 	Defines a row in a table
<track> 	Defines text tracks for media elements (<video> and <audio>)
<tt> 	Not supported in HTML5. Use CSS instead.  Defines teletype text
<u> 	Defines some text that is unarticulated and styled differently from normal text
<ul> 	Defines an unordered list
<var> 	Defines a variable
<video> 	Defines embedded video content
<wbr> 	Defines a possible line-break
EOD;

	$this->htm = [];
	$a = explode( "\n", $cmds );

	foreach( $a as $k=>$v ){
		$b = preg_split( "/\s+/", $v );
		$b[0] = str_replace( "<", "", $b[0] );
		$b[0] = str_replace( ">", "", $b[0] );
		$b[0] = preg_replace( "/!\s*doctype/i", "doctype", $b[0] );

		$this->htm[] = $b[0];
		$this->funcs[] = "do_$b[0]";
		}
}
################################################################################
#	do_doctype(). Handle the DOCTYPE comand.
#-------------------------------------------------------------------------------
#	HTML 4.01:
#	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
#		"http://www.w3.org/TR/html4/loose.dtd">
#
#	XHTML 1.1:
#	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
#		"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
#
################################################################################
private function do_doctype( $info )
{
#
#	We have to move "A" to "B" because we are taking things out of "A".
#
	$b = [];
	$info = trim( $info );
	$a = preg_split( "/\s+/", $info );
	for( $i=0; $i<count($a); $i++ ){
		if( preg_match('/"/', $a[$i]) ){
			$j = $i + 1;
			while( !preg_match('/".*"/', $a[$j]) ){
				$a[$i] .= " $a[$j]";
				unset( $a[$j] );
				}

			$i = $j;
			}

		$b[] = $a[$i];
		}
#
#	Ok, now "B" has everything put back together.
#	If this is an earlier HTML file, we should be able to find either "Strict",
#		"Traditional", or "Frameset".
#
	foreach( $b as $k=>$v ){
		if( preg_match("/\sstrict/i", $v) ){ $this->html['type'] = "Strict"; }
			else if( preg_match("/\straditional/i", $v) ){ $this->html['type'] = "Traditional"; }
			else if( preg_match("/\sframeset/i", $v) ){ $this->html['type'] = "Frameset"; }

		if( preg_match("/\s+html\s+\d\.\d+/i", $v) ){
			$this->html['version'] = preg_replace( "/\s+html\s+(\d+\.\d+)/i", "$1", $v );
			}
			else if( preg_match("/\s+xhtml\s+\d\.\d+/i", $v) ){
				$this->html['version'] = preg_replace( "/\s+xhtml\s+(\d+\.\d+)/i", "$1", $v );
				}
		}
}
################################################################################
#	do_html(). Handle the HTML command
################################################################################
private function do_html( $info )
{
	$info = trim( $info );
	$this->html['html'] = str_replace( "<", "", $info );
}
################################################################################
#	make_pdf(). Changes an HTML file to a PDF file.
################################################################################
function make_pdf( $html )
{
#
#	Separate lines based on all HTML commands are inside of "<" and ">".
#
	$a = explode( "<", $html );
	$b = [];
	foreach( $a as $k=>$v ){
		$c = explode( ">", $v );
		if( strlen(trim($c[0])) > 0 ){ $b[] = "<" . trim( $c[0] ) . ">"; }
		if( count($c) > 1 && strlen($c[1]) > 0 ){ $b[] = trim( $c[1] ); }
			else { $b[] = ""; }
		}
#
#	Remove blank lines.
#
	$a = $b;
	$b = [];
	foreach( $a as $k=>$v ){
		if( strlen(trim($v)) > 0 ){ $b[] = $v; }
		}
#
#	Put everything back into the $a array.
#
	$a = $b;
	$b = "";
	print_r( $a );
	print_r( $this->htm );
	foreach( $a as $k=>$v ){
		$b = preg_split( "/\s+/", $v );
		$b[0] = str_replace( "<", "", $b[0] );
		$b[0] = str_replace( "!", "", $b[0] );
		if( function_exists($this->funcs[$b[0]]) ){
			$this->funcs[$b[0]]( $v );
			}
		}
}
################################################################################
#	px2mm().  Conversion pixel -> millimeter at 72 dpi
#
#	Taken from:
#
#	HTML2PDF by Clément Lavoillotte
#	ac.lavoillotte@noos.fr
#	webmaster@streetpc.tk
#	http://www.streetpc.tk
#
################################################################################
function px2mm($px)
{
	return number_format( $px * 25.4 / 72, 2 );
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['fpdf']) ){
		$GLOBALS['classes']['fpdf'] = new class_fpdf();
		}

?>
