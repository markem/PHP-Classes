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
#	Mark Manning			Simulacron I			Sat 04/18/2015 18:45:32.85 
#	---------------------------------------------------------------------------
#		All new.  Wiped and started again.  Much simpler now.
#		Adding in Javascript routines.
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
#		CLASS_JS.PHP. A class to handle working with javascript.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_js
{
	private $base_path = null;
	private $base_file = null;
	private $jsCode = null;
	private $jsVars = null;
	private	$jsc = null;
	private $ds = '$';

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
	if( !isset($GLOBALS['class']['gd']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['gd']; }
}
################################################################################
#	init().  Set up all of the basic stuff.
################################################################################
function init()
{
	$args = func_get_args();
	while( is_array($args) && (count($args) < 2) ){
		$args = array_pop( $args );
		}
#
#	PATH will have slashes in it (ie: "C:/..." or "A/B"... - BUT -
#		IT MUST HAVE AT LEAST ONE SLASH IN IT!
#	FILE will have a NAME.TYPE set up (ie: "A.PDF", "B.BMP", whatever)
#	DEBUG will be a Boolean (ie: True/False)
#
	$path = null;
	$file = null;
	if( is_array($args) ){
		foreach( $args as $k=>$v ){
			$a = explode( '=', $v );
			if( preg_match("/^path/i", $a[0]) ){ $path = $a[1]; }
				elseif( preg_match("/^file/i", $a[0]) ){ $file = $a[1]; }
				elseif( preg_match("/^pf/i", $a[0]) ){
					$path = dirname( $a[1] );
					$file = basename( $a[1] );
					}
			}
		}

	$this->jsc = "jsc";
	$this->jsCode = "";
	$this->jsVars = "";

	if( !is_null($path) ){ $this->base_path = $path; }
		else { $this->base_path = dirname(realpath(__FILE__)); }

	if( !is_null($file) ){ $this->base_file = $file; }
		else { $this->base_file = "scripts.js"; }
#
#	I do not like paths with backslashes in them - convert them to forward slashes.
#
	$this->base_path = str_replace( "\\", "/", $this->base_path );
	$this->base_file = str_replace( "\\", "/", $this->base_file );

	$jsc = $this->jsc;
	$site_href = '$site_href';
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

	$ds = $this->ds;
	$this->jsCode = <<<END_OF_JAVASCRIPT
////////////////////////////////////////////////////////////////////////////////
//	Cursor Functions.
////////////////////////////////////////////////////////////////////////////////
$jsc.wait = function(){ $ds("body").css('cursor','wait'); }
$jsc.auto = function(){ $ds("body").css('cursor','auto'); }
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
//	isHex(). Is this a hex string/value?
//	Arguments	:	0	=	Item to test
//					1	=	V(alue) or S(tring). Default is STRING.
////////////////////////////////////////////////////////////////////////////////
$jsc.isHex = function()
{
	var p = 0;
	var re1 = /(\n|\r)+/g;
	var re2 = /[\Wg-zG-Z]/;
	var re3 = /v/i;
//
//	Make sure the string is really a string.
//
	var s = arguments[0];
	if( typeof s != "string" ){ s = s.toString(); }
//
//	If you want to check if this is a value hex VALUE
//	and NOT a hex STRING - you can also use this:
//
	var opt = arguments[1];
	if( re3.test(opt) && s.length % 2 > 0 ){ return false; }
//
//	Remove any returns. BinHex files can be megabytes in length with 80
//	column information. So we have to remove all returns first.
//
	s.replace( re1, "" );
//
//	IF they send us something with the universal "0x" or the HTML "#" on the
//	front of it - we have to FIRST move where we look at the string.
//
	if( s.substr(0,1) == "#" ){ p = 1; }
		else if( s.substr(0,2).toLowerCase() == "0x" ){ p = 2; }

	if( re2.test(s.substr(p,s.length)) ){ return false; }

	return true;
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
	if( !isHex(s) ){ return false; }
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

	$ds('input, input:hidden, select, textarea').each( function(index){
		var input = $ds(this);
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

	$ds.ajax({
		type: "POST",
		async: false,
		url: "$site_href/index.php",
		dataType: "html",
		data: arg,
//		success: function(data){ document.write( data ); document.close(); },
		success: function(data){ $ds(document.body).html( data ); },

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
		if( $ds('#COOKIE_DOUGH').length > 0 ){
			$ds('#COOKIE_DOUGH').val(cookie_dough);
			}
			else {
				$ds('<input>').attr({
					type: 'hidden',
					id: 'COOKIE_DOUGH',
					name: 'COOKIE_DOUGH'
					}).val(cookie_dough).appendTo( 'form' );
				}
		}

	return ret;
}

END_OF_JAVASCRIPT;

	return true;
}
################################################################################
#	checkEmail().  Check to see if they gave us a good e-mail address.
################################################################################
function checkEmail()
{
	$jsc = $this->jsc;

	$this->jsCode .= <<<END_OF_JAVASCRIPT
////////////////////////////////////////////////////////////////////////////////
//	checkEmail(). Check to make sure the e-mail address is valid.
////////////////////////////////////////////////////////////////////////////////
$jsc.checkEmail = function(id)
{
	var s = "";
	var v = $ds("#"+id).val();
	var re = new RegExp( "/^\w+\@\w+\.\w+$/" );

	if( !re.test(s) ){
		s = "The email field is invalid.\\nPlease try this again.\\nThank You.";
		alert( s );
		return false;
		}

	return true;
}\n
END_OF_JAVASCRIPT;

	return true;
}
################################################################################
#	isAlnum().  Check for alpha-numeric string
################################################################################
function isAlnum()
{
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

	var v = $ds("#"+id).val();
	re = new RegExp( "/\W/" );

	if( re.test(v) ){ return false; }
	return true;
}
END_OF_JAVASCRIPT;

	return true;
}
################################################################################
#	isAlpha().  Check for alpha-numeric string
################################################################################
function isAlpha()
{
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

	return true;
}
################################################################################
#	add().  Adds in more javascript.
#	Mark Manning		Simulacron I				Sat 06/14/2008 21:31:26.25
################################################################################
function add( $script=null, $vars=null )
{
	$jsc = $this->jsc;

	$this->jsCode .= !is_null($script) ? $script : "";
	$this->jsVars .= !is_null($vars) ? $vars : "";

	return true;
}

################################################################################
#	show(). Return the javascript we have.
#	Mark Manning		Simulacron I				Sat 06/14/2008 21:31:26.25
################################################################################
function show()
{
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

	return array( $scripts, $noscripts);
}

################################################################################
#	save().	Save the javascript to the path/file.
#	Mark Manning			Simulacron I			Thu 07/26/2012 10:40:31.75 
################################################################################
function save( $js=null )
{
	$jsc = $this->jsc;

	file_put_contents( "$base_path/$base_file", $this->show() );

	return "<script type='text/javascript' language='javascript' src='$base_path/$base_file'></script>";
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['js']) ){
		$GLOBALS['classes']['js'] = new class_js();
		}

?>
