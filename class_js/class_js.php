<?php

	if( file_exists("../class_debug.php") ){
		include_once( "../class_debug.php" );
		}
		else if( !isset($GLOBALS['classes']['debug']) ){
			die( "Can not load CLASS_DEBUG" );
			}
		else {
			die( "Can not load CLASS_DEBUG" );
			}

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_js;
#
#-Description:
#
#	This is the javascript Class.
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
#	Name					Company						Date
#	----------------------------------------------------------------------
#	Mark Manning			Simulacron I				Sat 06/14/2008 21:34:38.65
#		Original Program
#
#	Mark Manning			Simulacron I			Sat 11/21/2009 14:51:42.95 
#		These classes are now under the MIT License.  Any and all works
#		whether derivatives or whatever must be sent back to markem@sim1.us as
#		per the MIT License.  In this way, anything that makes these
#		routines better can and will be incorporated into them for the greater
#		good of mankind.  All additions and who made them should be noted here
#		in this file OR in a separate file to be called the HISTORY.DAT file
#		since, at some point in the future, this list will get to be too big
#		to store within the class itself.  See the MIT license file for details
#		on the MIT license.  If you do not agree with the license - then do NOT
#		use these routines in any way, shape, or form.  Failure to do so or using
#		these routines in whole or in part - constitutes a violation of the MIT
#		licensing terms and can and will result in prosecution under the law.
#
#	Legal Statement follows:
#
#		class_js. A PHP class to handle Javascript.
#		Copyright (C) 2001.  Mark Manning
#
#		This program is free software: you can redistribute it
#		and/or modify it under the terms of the MIT License.
#
#		This program is distributed in the hope that it will be useful,
#		but WITHOUT ANY WARRANTY; without even the implied warranty of
#		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
#
#	Mark Manning			Simulacron I			Sat 04/18/2015 18:45:32.85 
#	---------------------------------------------------------------------------
#		All new.  Wiped and started again.  Much simpler now.
#		Adding in Javascript routines.
#
#END DOC
################################################################################
class class_js
{
	private $debug_flag = false;

	private $base_path = null;
	private $base_file = null;
	private $jsCode = null;
	private $jsVars = null;
	private	$jsc = null;

################################################################################
#	__construct(). Construction function.
#	Mark Manning			Simulacron I			Sat 06/14/2008 21:29:55.85
#	---------------------------------------------------------------------------
#	BIG Change. NOW what you do is to put in the commands as you need them.
#	So for the path - you put in "path=<Path>" and so forth. See the arguments.
#
#	Arguments:
#
#		path	:	The path to the email file. Usage="path=<PATH>".
#		file	:	The file to read. Usage="file=<FILE>".
#		pf		:	The PATH/FILE to use (combined). Usage="pf=<PATH/FILE>".
#		d(ebug)	:	Turn on debug
#		s(ave)	:	Save debug info into a debug file.
#
#	Notes	:	You can ONLY use either Path & File -OR- PF. Not both.
#
################################################################################
function __construct()
{
	$args = func_get_args();
	$this->debug = $GLOBALS['classes']['debug'];
	$this->debug->init( func_get_args() );
	$this->debug->in();
#
#	PATH will have slashes in it (ie: "C:/..." or "A/B"... - BUT - IT MUST HAVE AT LEAST ONE SLASH IN IT!
#	FILE will have a NAME.TYPE set up (ie: "A.PDF", "B.BMP", whatever)
#	DEBUG will be a Boolean (ie: True/False)
#
	$path = null;
	$file = null;
	foreach( $args as $k=>$v ){
		$a = explode( '=', $v );
		if( preg_match("/path/i", $a[0]) ){ $path = $a[1]; }
			elseif( preg_match("/file/i", $a[0]) ){ $file = $a[1]; }
			elseif( preg_match("/pf/i", $a[0]) ){
				$path = dirname( $a[1] );
				$file = basename( $a[1] );
				}
		}

	$this->jsc = "jsc";
	$this->jsCode = "";
	$this->jsVars = "";

	if( !is_null($path) ){ $this->base_path = $path; }
		else { $this->base_path = dirname(realpath(__FILE__)); }

	if( !is_null($file) ){ $this->base_file = $file; }
		else { $this->base_file = "scripts.js"; }

	$this->init( "jsc" );

	$this->debug->out();

	return true;
}
################################################################################
#	init().  Set up all of the basic stuff.
################################################################################
function init( $site_href, $libName="jsc" )
{
	$this->debug->in();

	$this->jsc = $jsc = $libName;

	$this->jsVars = <<<END_OF_JAVASCRIPT

////////////////////////////////////////////////////////////////////////////////
//	Add in our namespace object.
////////////////////////////////////////////////////////////////////////////////
	var $jsc = {};

	$jsc.vars = new Array();	//	All variables go in here.
	$jsc.vars['dq'] = '"';	//	Double Quotes
	$jsc.vars['sq'] = "'";	//	Single Quotes
	$jsc.vars['ca'] = String.fromCharCode(1);	//	Control-A
	$jsc.vars['cb'] = String.fromCharCode(2);	//	Control-B

////////////////////////////////////////////////////////////////////////////////
//	Example: Here is how you test to see if something is there or not.
////////////////////////////////////////////////////////////////////////////////
	$jsc.vars['hasDIV'] = document.div ? true : false;
	$jsc.vars['hasALL'] = document.all ? true : false;
	$jsc.vars['hasDM'] = document.designMode ? true : false;
	$jsc.vars['hasGEBI'] = document.getElementById ? true : false;
	$jsc.vars['hasGEBN'] = document.getElementsByName ? true : false;
	$jsc.vars['hasImg'] = document.images ? true : false;
	$jsc.vars['hasLayers'] = document.layers ? true : false;

	$jsc.vars['hascE'] = navigator.cookieEnabled ? true : false;

	$jsc.vars['hasAXO'] = window.ActiveXObject ? true : false;
	$jsc.vars['hasFocus'] = window.focus ? true : false;
	$jsc.vars['hasPrint'] = window.print ? true : false;
	$jsc.vars['hasXMLHR'] = window.XMLHttpRequest ? true : false;\n
END_OF_JAVASCRIPT;

	$this->jsCode = <<<END_OF_JAVASCRIPT
////////////////////////////////////////////////////////////////////////////////
//	Cursor Functions.
////////////////////////////////////////////////////////////////////////////////
$jsc.wait = function(){ \$("body").css('cursor','wait'); }
$jsc.auto = function(){ \$("body").css('cursor','auto'); }
////////////////////////////////////////////////////////////////////////////////
//	doesExist.  A function to detect whether something exists or not.
////////////////////////////////////////////////////////////////////////////////
$jsc.doesExist = function(e)
{
	if( (typeof(e) != "object") || (e == "") || (e == null) ){ return false; }

	return true;
}
////////////////////////////////////////////////////////////////////////////////
//	Simple cross-browser function to get an element.
//	by Jason D. Agostoni @ jason ATNOSPAM agostoni DOTNOSPAM net
//	Modified by Mark Manning @ www-DOT-sim1-DOT-us
//	REALLY modified by Mark Marnning - complete re-write.
////////////////////////////////////////////////////////////////////////////////
$jsc.getElement = function(psID)
{
	var myArray = null;
	var myObject = null;
//
//	Is this browser too old?
//
	if( !vars['hasALL'] && !vars['hasGEBI'] && !vars['hasGEBN'] && !vars['hasLayers'] ){
		alert( "Your browser is too old.  Please upgrade your browser. Thank you." );
		return null;
		}
//
//	Did they send us a pre-existing object instead of the object's NAME?
//
	if( doesExist(psID) ){ return psID; }
//
//	Try the getElementById method.
//
	if( vars['hasGEBI'] ){
		myObject = document.getElementById( psID );
		if( myObject != null ){ return myObject; }
		}
//
//	Try the getElementsByName method.
//
	if( vars['hasGEBN'] ){
		myObject = document.getElementsByName( psID );
		if( (myObject != null) && (myObject.constructor != null) ){
			if( myObject.constructor.toString().indexOf("Array") > -1 ){
				myArray = document.getElementsByName( psID ).item(0);
				myObject = myArray[0];
				}
			}
			else { myObject = null; }

		if( myObject != null ){ return myObject; }
		}
//
//	Try the document.all method.
//
	if( vars['hasALL'] ){
		myObject = document.all[psID];
		if( myObject != null ){ return myObject; }
		}
//
//	Does it have layers?  Are we that far back?
//
	if( vars['hasLayers'] ){
		for( var iLayer = 1; iLayer < document.layers.length; iLayer++ ){
			if( document.layers[iLayer].id == psID ){
				return document.layers[iLayer];
				}
			}
		}
//
//	We could, at this point, cycle through the entire child and sibling lists
//	until we find the psID - but why bother?  One of the above methods should
//	have found what we were looking for.  If not - there's a reason why.  Like
//	maybe they misspelled something?  In any case - just return NULL and let them
//	deal with it.
//
	return null;
}
////////////////////////////////////////////////////////////////////////////////
//	toHex().  Convert an ASCII string to hexadecimal.
//	Copyrighted (c) 1992-2015 by Mark Manning (markem@sim1.us)
//	Used with permission
////////////////////////////////////////////////////////////////////////////////
$jsc.toHex = function(s)
{
	if( s.substr(0,2).toLowerCase() == "0x" ){ return s; }

	var l = "0123456789ABCDEF";
	var o = "";

	if( typeof s != "string" ){ s = s.toString(); }
	for( var i=0; i<s.length; i++ ){
		var c = s.charCodeAt(i);

		o = o + l.substr((c>>4),1) + l.substr((c & 0x0f),1);
		}

	return "0x" + o;
}
////////////////////////////////////////////////////////////////////////////////
//	fromHex().  Convert a hex string to ascii text.
//	Copyrighted (c) 1992-2015 by Mark Manning (markem@sim1.us)
//	Used with permission
////////////////////////////////////////////////////////////////////////////////
$jsc.fromHex = function(s)
{
	var start = 0;
	var o = "";

	if( typeof s != "string" ){ s = s.toString(); }
	if( s.substr(0,2) == "0x" ){ start = 2; }

	for( var i=start; i<s.length; i+=2 ){
		var c = s.substr( i, 2 );

		o = o + String.fromCharCode( parseInt(c, 16) );
		}

	return o;
}
////////////////////////////////////////////////////////////////////////////////
//	getParams().  Gets all of the inputs from a given webpage.
////////////////////////////////////////////////////////////////////////////////
$jsc.getParams = function()
{
	var s = "";
	var num = 0;
	var re = new RegExp( /help/i );
//
//	If there is an argument - then it is the file to call.
//
	if( arguments.length > 0 ){ s = "file=" + arguments[0]; }

	\$('input, input:hidden, select, textarea').each( function(index){
		var input = \$(this);
		var v = null;
		if( input.attr('type') == 'radio' ){
			if( input.checked ){ v = $jsc.escapeHtmlEntities( $jsc.trim(input.val()) ); }
				else { v = ""; }
			}
			else { v = $jsc.escapeHtmlEntities( $jsc.trim(input.val()) ); }

		var n = $jsc.trim( input.attr('name') );
//
//	We send all input values as hex. EVEN IF IT IS BLANK! <- IMPORTANT!
//
		num++;
		s = s + "&v" + num + "=" + $jsc.toHex(n + $jsc.vars['cb'] + v);
		});
//
//	Now get all of the global variables and send them.
//
	for( var key in $jsc.vars ){
		if( !re.test(key) ){
			num++;
			s = s + "&v" + num + "=" + $jsc.toHex( key + $jsc.vars['cb'] + $jsc.vars[key] );
			}
		}

	return s;
	}
////////////////////////////////////////////////////////////////////////////////
//	loadPage().  Loads in and executes the next page.
////////////////////////////////////////////////////////////////////////////////
$jsc.loadPage = function()
{
	var arg = arguments[0];

	\$.ajax({
		type: "POST",
		async: false,
		url: "$site_href/index.php",
		dataType: "html",
		data: arg,
//		success: function(data){ document.write( data ); document.close(); },
		success: function(data){ \$(document.body).html( data ); },

		error: function(jqXHR, textStatus, errorThrown){
			alert( "FAILED: " + textStatus + " - " + errorThrown );
			}
		});

	return;
	}
////////////////////////////////////////////////////////////////////////////////
//	noop().  A function that does nothing.
////////////////////////////////////////////////////////////////////////////////
$jsc.noop = function(){ return false; }
////////////////////////////////////////////////////////////////////////////////
//	trim().  Trim whitespace.
////////////////////////////////////////////////////////////////////////////////
$jsc.trim = function(str)
{
	if( typeof(str) == "undefined" ){ return ""; }
	return str.replace( /^\s+|\s+$/g, '' );
}
//------------------------------------------------------------------------------
//	The following comes from
//
//		https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/
//			Global_Objects/String/Trim
//------------------------------------------------------------------------------
	if( !String.prototype.trim ){
		String.prototype.trim = function(){
			return this.replace( /^\s+|\s+$/g, '' );
			};
		}\n
////////////////////////////////////////////////////////////////////////////////
//	escapeHtmlEntities().  Replace all non-alphanumeric symbols with &#<ORD>;.
////////////////////////////////////////////////////////////////////////////////
$jsc.escapeHtmlEntities = function(text)
{
	var s = "";
	var c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	for( var i=0; i<text.length; i++ ){
		var v = text.charCodeAt(i);
		var h = (v & 0xff00) >> 16;
		var l = v & 0x00ff;
//
//	For now only deal with the lower part.
//
		if( c.indexOf(l) > -1 ){ s = s + String.fromCharCode(l); }
			else { s = s + "&#" + l + ";"; }
		}

	return s;
}
////////////////////////////////////////////////////////////////////////////////
//	cookies().  A function to test if cookies are enabled.  If not - set up the
//				hidden form element.
//	by tauren and Mark Manning
//	https://github.com/Modernizr/Modernizr/issues/191
////////////////////////////////////////////////////////////////////////////////
$jsc.cookies = function(cookie_dough)
{
//
// Quick test if browser has cookieEnabled host property
//
	if (navigator.cookieEnabled) return true;
//
// Create cookie
//
	document.cookie = "cookietest=1";
	var ret = document.cookie.indexOf("cookietest=") != -1;
//
// Delete cookie
//
	document.cookie = "cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT";
//
//	Add in the data
//
	if( ret == false ){
		if( \$('#COOKIE_DOUGH').length > 0 ){
			\$('#COOKIE_DOUGH').val(cookie_dough);
			}
			else {
				\$('<input>').attr({
					type: 'hidden',
					id: 'COOKIE_DOUGH',
					name: 'COOKIE_DOUGH'
					}).val(cookie_dough).appendTo( 'form' );
				}
		}

	return ret;
}

END_OF_JAVASCRIPT;

	$this->debug->out();

	return true;
}
################################################################################
#	checkEmail().  Check to see if they gave us a good e-mail address.
################################################################################
function checkEmail()
{
	$this->debug->in();

	$jsc = $this->jsc;

	$this->jsCode .= <<<END_OF_JAVASCRIPT
////////////////////////////////////////////////////////////////////////////////
//	checkEmail(). Check to make sure the e-mail address is valid.
////////////////////////////////////////////////////////////////////////////////
$jsc.checkEmail = function(id)
{
	var s = "";
	var v = \$("#"+id).val();
	var re = new RegExp( "/^\w+\@\w+\.\w+$/" );

	if( !re.test(s) ){
		s = "The email field is invalid.\\nPlease try this again.\\nThank You.";
		alert( s );
		return false;
		}

	return true;
}\n
END_OF_JAVASCRIPT;

	$this->debug->out();

	return true;
}
################################################################################
#	isAlnum().  Check for alpha-numeric string
################################################################################
function isAlnum()
{
	$this->debug->in();

	$jsc = $this->jsc;

	$this->jsCode .= <<<END_OF_JAVASCRIPT
////////////////////////////////////////////////////////////////////////////////
//	isAlnum().  Check to see if this is an alpha-numeric string
////////////////////////////////////////////////////////////////////////////////
$jsc.isAlnum = function(id)
{
	var t = typeof( id );
	var re = new RegExp( "/undefined|function|xml|object/i" );

	if( re.test(t) ){ return false; }

	var v = \$("#"+id).val();
	re = new RegExp( "/\W/" );

	if( re.test(v) ){ return false; }
	return true;
}
END_OF_JAVASCRIPT;

	$this->debug->out();

	return true;
}
################################################################################
#	isAlpha().  Check for alpha-numeric string
################################################################################
function isAlpha()
{
	$this->debug->in();

	$jsc = $this->jsc;

	global $tcf;

	$this->jsCode .= <<<END_OF_JAVASCRIPT
////////////////////////////////////////////////////////////////////////////////
//	isAlpha().  Check to see if this is an alpha string
////////////////////////////////////////////////////////////////////////////////
$jsc.isAlpha = function(id)
{
	if( typeof(id) != "string" ){ return false; }
	return true;
}
////////////////////////////////////////////////////////////////////////////////
//	Taken from Stack Overflow.  Original program by montss
//http://stackoverflow.com/questions/21797258/getcomputedstyle-like-javascript-function-for-ie8
////////////////////////////////////////////////////////////////////////////////
$jsc.getStyleProperty = function(el, prop)
{
	var computedStyle;

	if( document.defaultView && document.defaultView.getComputedStyle ){ // standard (includes ie9)
		computedStyle = document.defaultView.getComputedStyle(el, null)[prop];
		}
		else if( el.currentStyle ){ // IE older
			computedStyle = el.currentStyle[prop];
			}
		else { // inline style
			computedStyle = el.style[prop];
			}

        return computedStyle;

};
////////////////////////////////////////////////////////////////////////////////
//	init_resize().  Run through everything and add in the resize information.
////////////////////////////////////////////////////////////////////////////////
$jsc.init_resize=function()
{
	$("*").map( function(){
		var obj = $(this);
//
//	Get all of the classes.  Make sure they are not just inheriting from a parent.
//
		var cl = obj.classes();
		if( cl != null ){
			for( key in cl ){
				var a = $("."+cl[key]).css("font-size");
				var b = $("."+cl[key]).parent().css("font-size");

				if( a != null && a != b ){ cl[key] = cl[key] + '=' + parseInt(a,10); }
					else { delete cl[key]; }
				}

			cl = cl.join('.');
			}

		obj.data( "ow", obj.width() );
		obj.data( "oh", obj.height() );
		obj.data( "cl",cl);
		});
//
//	Get all of the input types
//
	$('input, input:hidden, select, textarea').each( function(index){
		var obj = $(this);
		var id = obj.attr("id");
		var name = obj.attr("name");
		var cl = obj.classes();

		if( typeof name == "undefined" && typeof id == "undefined" ){
			id = name = jsc.uniqid( "inp_" );
			obj.attr( "id", id );
			obj.attr( "name", name );
			}
			else if( typeof name == "undefined" ){
				name = id;
				obj.attr( "name", name );
				}
			else if( typeof id == "undefined" ){
				id = name;
				obj.attr( "id", id );
				}

		var fs = null;
		var stylearray = jsc.getStyleProperty( obj[0], null );

		for( var key in stylearray ){
			if( key == "font-size" ){
				fs = parseInt( obj.css("font-size") );
				}
			}
//
//	If there wasn't a class already for this object - make one.
//
		if( cl == null ){
			cl = jsc.uniqid( "css_" );
			if( obj.css('font-size') != null ){
				fs = (fs == null) ? parseInt( obj.css("font-size") ) : fs;
				}

			var w = parseInt(obj.width());
			var h = parseInt(obj.height()) + 10;
			var style = "<style>" + cl + "{font-size:" + fs + "px;";
				style = style + "width:" + w + "px;height:" + h + "px;}</style>";
			$('html > head').append(style);
			obj.addClass( cl );
			obj.data( "cl", "." + cl + "=" + fs );
			}

		if( obj.attr("type") == "text" ){
			obj.data( "input_size", obj.attr("Size") );
			}
		});
}
////////////////////////////////////////////////////////////////////////////////
//	uniqid().  Taken from csharptest.net's post on Stack Overflow
//
//	http://stackoverflow.com/questions/1349404/generate-a-string-of-5-random-characters-in-javascript
//
//	Notes: I changed his routine to be like the PHP routine.  You can pass
//		in a base text that will be stuck on the front of the returning
//		string.
////////////////////////////////////////////////////////////////////////////////
$jsc.uniqid = function()
{
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

	if( arguments.length > 0 ){ text = arguments[0]; }

    for( var i=0; i < 20; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

END_OF_JAVASCRIPT;

	$this->debug->out();

	return true;
}
################################################################################
#	add().  Adds in more javascript.
#	Mark Manning		Simulacron I				Sat 06/14/2008 21:31:26.25
################################################################################
function add( $script=null, $vars=null )
{
	$this->debug->in();

	$jsc = $this->jsc;

	$this->jsCode .= !is_null($script) ? $script : "";
	$this->jsVars .= !is_null($vars) ? $vars : "";

	$this->debug->out();

	return true;
}

################################################################################
#	show(). Return the javascript we have.
#	Mark Manning		Simulacron I				Sat 06/14/2008 21:31:26.25
################################################################################
function show()
{
	$this->debug->in();

	$jsc = $this->jsc;

	$scripts = $this->jsVars;
	$scripts .= $this->jsCode;
	$noscripts = <<<END_OF_HTML
<noscript>
<center>
<table width='50%' border='0' cellspacing='0' cellpadding='0'>
<tr><td align='center'><span style='font:12pt arial;'>
If you are seeing this, then your browser either has
Javascript turned off or it doesn't support Javascript. In
either case you need to either enable Javascript or upgrade
your browser so it can handle Javascript. Otherwise you
will not be able to use this program. Remember that
<b>JavaSCRIPT is not JAVA</b>. Javascript was created
by Netscape and JAVA was created by Sun Microsystems.
Javascript is a part of the browser while Java is an add-on
product. <b><u>It is Java that <i>USED</i> to have
security problems.</u></b> Those are fixed now.<p><b><span
style='font:16pt arial;'>Javascript has <u>never</u> had
any security problems.</font></b><br><i>(So you need to
turn it on or enable it)</i></td></tr>

<tr><td height="40px">&nbsp;</td><tr><td align="center" style='font:12pt arial;'><p>

The current version of FireFox can be found <a href="http://www.mozilla.org">here</a>.<p>

The current version of Internet Explorer can be found <a href="http://www.microsoft.com">here</a>.<p>

The current version of Safari can be found <a href="http://www.apple.com">here</a>.<p>

The current version of Opera can be found <a href="http://www.opera.com">here</a>.<p>

The current version of Chrome can be found <a href="http://www.google.com">here</a>.<p>

Thank you.</span><p>
</td></tr></table>
</center>
</noscript>\n
END_OF_HTML;

	$this->debug->out();

	return array( $scripts, $noscripts);
}

################################################################################
#	save().	Save the javascript to the path/file.
#	Mark Manning			Simulacron I			Thu 07/26/2012 10:40:31.75 
################################################################################
function save( $js=null )
{
	$this->debug->in();

	$jsc = $this->jsc;

	file_put_contents( "$base_path/$base_file", $this->show() );

	$this->debug->out();

	return "<script type='text/javascript' language='javascript' src='$base_path/$base_file'></script>";
}

}

if( !isset($GLOBALS['classes']) ){ global $classes; }
if( !isset($GLOBALS['classes']['js']) ){ $GLOBALS['classes']['js'] = new class_js(); }

?>
