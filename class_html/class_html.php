<?php
#
#	Defines
#

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
#
#	Set up LIBS so we can check whereever the class is located
#	Put in the standard stuff
#
		$libs = [];
		$libs[] = ".";
		$libs[] = "..";
#
#	Now get the environment information - IF it is there
#
		$env = getenv( "my_libs" );
		if( !is_null($env) ){
			$libs[] = $env;
			}
#
#	Now insert all of the other locations to look in
#
		$libs[] = "C:/xampp/php/usr/fpdf186";
		$libs[] = "C:/xampp/php/usr/setasign";
		$libs[] = "C:/xampp/php/usr";

		$flag = true;
		foreach( $libs as $k=>$v ){
			if( file_exists("$v/$class") ) { $lib = $v; $flag = false; }
			}

		if( $flag ){ die( "Can't find $class - aborting\n" ); }

		include_once "$lib/$class";
		});

################################################################################
#BEGIN DOC
#
#-Calling Sequence:
#
#	class_html();
#
#-Description:
#
#	A class to handle my html.
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
#	Mark Manning			Simulacron I			Sun 07/07/2019 15:33:42.40 
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
#		CLASS_HTML.PHP. A class to handle working with html.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_html
{

	var $pr = null;
	var $chex = null;
	var $pathinfo = null;	#	Path to current file.
	var $html_cmds = null;	#	An array of all of the HTML commands

	var $all_htmls = null;	#	HTML array to hold all of the html files
	var $all_links = null;	#	All links in the web page.
	var $all_base64 = null;
	var $all_images = null;
	var $all_sources = null;
	var $all_scripts = null;

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
#
#	Reset everything
#
	$this->pr = null;
	$this->chex = null;
	$this->path = null;
	$this->file = null;
	$this->htmls = [];	#	HTML array.
	$this->all_links = [];	#	All links in the web page.
	$this->all_base64 = [];
	$this->all_images = [];
	$this->all_scripts = [];
	$this->all_sources = [];
	$this->pathinfo = null;	#	Path to current file.

	$this->pr = new class_pr();
	$this->chex = new class_hex();

	$img_cmds =
		"gif|gd|gd2|jpg|jpeg|exif|jfif|jfi|wbmp|xbm|xpm|png|web|webp|" .
		"tif|tiff|x-icon|svg+xml";

	$this->img_cmds = explode( "|", $img_cmds );

	$html_cmds =
	 	"DOCTYPE|abbreviation|acronym|address|anchor|applet|area|article|" .
		"aside|audio|base|basefont|bdi|bdo|bgsound|big|blockquote|body|" .
		"bold|break|button|caption|canvas|center|cite|code|colgroup|col|" .
		"comment|data|datalist|dd|define|delete|details|dialog|dir|div|" .
		"dl|dt|embed|fieldset|figcaption|figure|font|footer|form|frame|" .
		"frameset|head|header|heading|hgroup|hr|html|iframe|image|input|" .
		"ins|isindex|italic|kbd|keygen|label|legend|link|list|main|mark|" .
		"marquee|menuitem|meta|meter|nav|nobreak|noembed|noscript|object|" .
		"optgroup|option|output|paragraph|param|em|pre|progress|q|rp|" .
		"rt|ruby|s|samp|script|section|small|source|spacer|span|strike|" .
		"strong|style|sub|sup|summary|svg|table|tbody|td|template|tfoot|" .
		"th|thead|time|title|tr|track|tt|underline|var|video|wbr|xmp|" .
		"!--|!DOCTYPE|a|abbr|acronym|address|applet|area|" .
		"article|aside|audio|b|base|basefont|bdi|bdo|big|blockquote|" .
		"body|br|button|canvas|caption|center|cite|code|col|" .
		"colgroup|data|datalist|dd|del|details|dfn|dialog|dir|" .
		"div|dl|dt|em|embed|fieldset|figcaption|figure|font|" .
		"footer|form|frame|frameset|h1|head|header|hgroup|" .
		"hr|html|i|iframe|img|input|ins|kbd|label|legend|" .
		"li|link|main|map|mark|menu|meta|meter|nav|noframes|" .
		"noscript|object|ol|optgroup|option|output|p|param|" .
		"picture|pre|progress|q|rp|rt|ruby|s|samp|script|" .
		"search|section|select|small|source|span|strike|strong|" .
		"style|sub|summary|sup|svg|table|tbody|td|template|" .
		"textarea|tfoot|th|thead|time|title|tr|track|tt|" .
		"u|ul|var|video|wbr|!DOCTYPE";

		$this->html_cmds = explode( "|", $html_cmds );

	$style_cmds =
		"alignContent|alignItems|alignSelf|animation|animationDelay|".
		"animationDirection|animationDuration|animationFillMode|".
		"animationIterationCount|animationName|animationTimingFunction|".
		"animationPlayState|background|backgroundAttachment|".
		"backgroundColor|backgroundImage|backgroundPosition|".
		"backgroundRepeat|backgroundClip|backgroundOrigin|backgroundSize|".
		"backfaceVisibility|border|borderBottom|borderBottomColor|".
		"borderBottomLeftRadius|borderBottomRightRadius|borderBottomStyle|".
		"borderBottomWidth|borderCollapse|borderColor|borderImage|".
		"borderImageOutset|borderImageRepeat|borderImageSlice|".
		"borderImageSource|borderImageWidth|borderLeft|borderLeftColor|".
		"borderLeftStyle|borderLeftWidth|borderRadius|borderRight|".
		"borderRightColor|borderRightStyle|borderRightWidth|borderSpacing|".
		"borderStyle|borderTop|borderTopColor|borderTopLeftRadius|".
		"borderTopRightRadius|borderTopStyle|borderTopWidth|borderWidth|".
		"bottom|boxDecorationBreak|boxShadow|boxSizing|captionSide|".
		"caretColor|clear|clip|color|columnCount|columnFill|".
		"columnGap|columnRule|columnRuleColor|columnRuleStyle|".
		"columnRuleWidth|columns|columnSpan|columnWidth|content|".
		"counterIncrement|counterReset|cursor|direction|display|".
		"emptyCells|filter|flex|flexBasis|flexDirection|flexFlow|".
		"flexGrow|flexShrink|flexWrap|cssFloat|font|fontFamily|".
		"fontSize|fontStyle|fontVariant|fontWeight|fontSizeAdjust|".
		"fontStretch|hangingPunctuation|height|hyphens|icon|imageOrientation|".
		"isolation|justifyContent|left|letterSpacing|lineHeight|".
		"listStyle|listStyleImage|listStylePosition|listStyleType|".
		"margin|marginBottom|marginLeft|marginRight|marginTop|".
		"maxHeight|maxWidth|minHeight|minWidth|navDown|navIndex|".
		"navLeft|navRight|navUp|objectFit|objectPosition|opacity|".
		"order|orphans|outline|outlineColor|outlineOffset|outlineStyle|".
		"outlineWidth|overflow|overflowX|overflowY|padding|".
		"paddingBottom|paddingLeft|paddingRight|paddingTop|pageBreakAfter|".
		"pageBreakBefore|pageBreakInside|perspective|perspectiveOrigin|".
		"position|quotes|resize|right|scrollBehavior|tableLayout|".
		"tabSize|textAlign|textAlignLast|textDecoration|textDecorationColor|".
		"textDecorationLine|textDecorationStyle|textIndent|textJustify|".
		"textOverflow|textShadow|textTransform|top|transform|".
		"transformOrigin|transformStyle|transition|transitionProperty|".
		"transitionDuration|transitionTimingFunction|transitionDelay|".
		"unicodeBidi|userSelect|verticalAlign|visibility|whiteSpace|".
		"width|wordBreak|wordSpacing|wordWrap|widows|zIndex";

	$this->style_cmds = explode( "|", $style_cmds );
#
#	HTML Commands
#
	$a =
		"charset|hash|host|hostname|href|hreflang|origin|name".
		"password|pathname|port|protocol|rel|rev|search|target|".
		"text|type|username";

	$table =
		"align|background|bgColor|border|caption|cellPadding|" .
		"cellSpacing|frame|height|rules|summary|tFoot|tHead|width|";
#
#	Standard Elements
#
	$elements = 
		"accessKey|addEventListener()|after()|append()|appendChild()|".
		"attributes|before()|blur()|childElementCount|childNodes|".
		"children|classList|className|click()|clientHeight|clientLeft|".
		"clientTop|clientWidth|cloneNode()|closest()|compareDocumentPosition()|".
		"contains()|contentEditable|dir|firstChild|firstElementChild|".
		"focus()|getAttribute()|getAttributeNode()|getBoundingClientRect()|".
		"getElementsByClassName()|getElementsByTagName()|hasAttribute()|".
		"hasAttributes()|hasChildNodes()|id|innerHTML|innerText|".
		"insertAdjacentElement()|insertAdjacentHTML()|insertAdjacentText()|".
		"insertBefore()|isContentEditable|isDefaultNamespace()|".
		"isEqualNode()|isSameNode()|isSupported()|lang|lastChild|".
		"lastElementChild|matches()|namespaceURI|nextSibling|".
		"nextElementSibling|nodeName|nodeType|nodeValue|normalize()|".
		"offsetHeight|offsetWidth|offsetLeft|offsetParent|".
		"offsetTop|outerHTML|outerText|ownerDocument|parentNode|".
		"parentElement|previousSibling|previousElementSibling|".
		"querySelector()|querySelectorAll()|remove()|removeAttribute()|".
		"removeAttributeNode()|removeChild()|removeEventListener()|".
		"replaceChild()|scrollHeight|scrollIntoView()|scrollLeft|".
		"scrollTop|scrollWidth|setAttribute()|setAttributeNode()|".
		"style|tabIndex|tagName|textContent|title|toString()";

	$this->elements = explode( "|", $elements );
}
################################################################################
#	open(). Set what the file is so we can open it later on.
#	NOTE: Originally I passed in the path and the file but since my routine
#		get_files() returns each entry as a complete path/file - I'm now
#		just sending over the entry and putting it in to pathinfo.
################################################################################
function open( $file )
{
	$this->pathinfo = pathinfo( $file );

	return true;
}
################################################################################
#	get_html_link().  Use the following function to extract all of the
#		links from a HTML file.
#
#	Taken from :
#
#		https://www.hashbangcode.com/article/extract-links-html-file-php
#
################################################################################
function get_html_link( $html )
{
	$links = [];
	if( preg_match_all('/<a\s+.*?href=[\"\']?([^\"\' >]*)[\"\']?[^>]*>(.*?)<\/a>/i',
		$html, $matches, PREG_SET_ORDER)){
		foreach( $matches as $match ){
			$links[] = $match;
			}
		}

	return $links;
}
################################################################################
#	parse_page(). Separate out each part and store into an HTML.
################################################################################
function get_html()
{
	$path = $this->pathinfo['dirname'];
	$file = $this->pathinfo['basename'];
	$html_cmds = $this->html_cmds;
	$filename = $this->pathinfo['filename'];

	$pr = $this->pr;
#
#	Now get the page
#
	$html = file_get_contents( "$path/$file" );
#
#	Ok - first change all occurances of each HTML command so we can
#	split up the document.
#
	foreach( $html_cmds as $k=>$v ){
		$html = str_replace( "<$v ", "\r<$v ", $html );
		$html = str_replace( "<$v>", "\r<$v>", $html );
		$html = str_replace( "</$v>", "\r</$v>", $html );
		}
#
#	Now split up the document based upon the \r< part
#
	$html = explode( "\r<", $html );
	array_shift( $html );

	foreach( $html as $k=>$v ){
		$html[$k] = "<$v";
		$html[$k] = preg_replace( "/$/", "", $html[$k] );
		$html[$k] = "$html[$k]\n";
		$html[$k] = str_replace( "\n\n", "\n", $html[$k] );
		if( $html[$k] == "<\n" ){ unset( $html[$k] ); continue; }
		if( strlen(trim($html[$k])) < 1 ){ unset( $html[$k] ); }
		$html[$k] = preg_replace( "/\xa\s*\xa/", "\xa", $html[$k] );
		if( preg_match( "/^\s*\xa/", $html[$k]) ){ unset( $html[$k] ); }
		}

	$html = array_reverse( $html );
	$html = array_reverse( $html );
#
#	To keep track of everything, I'm now usimg the filename
#	as the entry into the HTML array.
#
	$this->all_htmls[$filename] = $html;
	return true;
}
################################################################################
#	get_scripts(). Get all of the script commands, put them into an array, and
#		save them to the hard drive.
################################################################################
function get_scripts()
{
	$filename = $this->pathinfo['filename'];

	$html = $this->all_htmls[$filename];	#	Get the current HTML info

	$scripts = [];
	$html_cnt = count( $html ) - 1;
#
#	Find all of the new "<script" tags and save them.
#
	foreach( $html as $k=>$v ){
		if( preg_match("/<script/i", $v) ){
			$scripts[] = $v;
			}
		}
#
#	Now check the scripts against themselves
#
	foreach( $scripts as $k=>$v ){
		for( $i=$k-1; $i>=0; $i-- ){
			if( $v === $scripts[$i] ){ $scripts[$k] = "$filename:$i;"; }
			}
		}
#
#	Look to see if we already have these scripts.
#
	foreach( $this->all_scripts as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
			foreach( $scripts as $k2=>$v2 ){
#
#	If this is a duplicate - then put the id of where it is located
#	into the scripts array. Remember: FILENAME:Level 1: Level
#
#	Example: For #1 = 5, For #2 = 98. So the 98th line in file #5.
#
				if( $v1 === $v2 ){ $scripts[$k2] = "$filename:$k1:$k;"; }
				}
			}
		}
#
#	Now save it
#
	$this->all_scripts[$filename] = $scripts;
	return true;
}
################################################################################
#	get_links(). Get all of the link commands, put them into an array, and
#		save them to the hard drive.
################################################################################
function get_links()
{
	$pr = $this->pr;
	$pathinfo = $this->pathinfo;
	$filename = $pathinfo['filename'];
	$html = $this->all_htmls[$filename];

	$links = [];
#
#	Now find all links in this document
#
	foreach( $html as $k=>$v ){
		if( preg_match("/<link/i", $v) ){
			$links[] = $v;
#			$pr->pr( $v, "LINK = " );
			}
		}
#
#	Now check the scripts against themselves
#
	foreach( $links as $k=>$v ){
		for( $i=$k-1; $i>=0; $i-- ){
			if( $v === $links[$i] ){ $links[$k] = "$filename:$i;"; }
			}
		}
#
#	Look to see if we already have this link.
#
	foreach( $this->all_links as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
			foreach( $links as $k2=>$v2 ){
				if( $v1 === $v2 ){ $links[$k] = "$filename:$k1:$k;"; }
				}
			}
		}

	$this->all_links[$filename] = $links;
	return true;
}
################################################################################
#	get_images(). Get all of the image commands, put them into an array, and
#		save them to the hard drive.
################################################################################
function get_images()
{
	$pathinfo = $this->pathinfo;
	$filename = $pathinfo['filename'];
	$html = $this->all_htmls[$filename];

	$images = [];
#
#	Now look through the document to get all images
#
	foreach( $html as $k=>$v ){
		if( preg_match("/<img/i", $v) ){
			$images[] = $v;
			}
		}
#
#	Now check the images against themselves
#
	foreach( $images as $k=>$v ){
		for( $i=$k-1; $i>=0; $i-- ){
			if( $v === $images[$i] ){ $images[$k] = "$filename:$i;"; }
			}
		}
#
#	Look to see if we already have this image.
#
	$id = null;
	foreach( $this->all_images as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
		  foreach( $images as $k2=>$v2 ){
				if( $v1 === $v2 ){ $images[$k2] = "$filename:$k1:$k;"; }
				}
			}
		}

	$this->all_images[$filename] = $images;
	return true;
}
################################################################################
#	get_sources(). Get all of the source commands, put them into an array, and
#		save them to the hard drive.
################################################################################
function get_sources()
{
	$pr = $this->pr;

	$pathinfo = $this->pathinfo;
	$filename = $pathinfo['filename'];
	$html = $this->all_htmls[$filename];

	$sources = [];
#
#	We are trying to find
	foreach( $html as $k=>$v ){	#	Get the current html document
		if( is_array($v) ){
			foreach( $v as $k1=>$v1 ){			#	Go through the html code
				if( preg_match("/<source/i", $v1) ){
					$sources[] = $v;
					}
				}
			}
			else {
#				$pr->pr( $v, "V = " );
				}
		}
#
#	Now check the sources against themselves
#
	foreach( $sources as $k=>$v ){
		for( $i=$k-1; $i>=0; $i-- ){
			if( $v === $sources[$i] ){ $sources[$k] = "$filename:$i;"; }
			}
		}
#
#	Look to see if we already have this source.
#
	$id = null;
	foreach( $this->all_sources as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
			foreach( $sources as $k2=>$v2 ){
				if( $v1 === $v2 ){ $sources[$k2] = "$filename:$k1:$k;"; }
				}
			}
		}

	$this->all_sources[$filename] = $sources;
	return true;
}
################################################################################
#	get_base64(). Find, convert, and save all base64 areas.
################################################################################
function get_base64()
{
$pr = $this->pr;
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	$pathinfo = $this->pathinfo;
	$filename = $pathinfo['filename'];
	$html = $this->all_htmls[$filename];

	$dq = '"';
	$num_base64 = 0;

	foreach( $html as $k=>$v ){
		if( preg_match("/base64/i", $v) ){
#
#	First we have to break up the HTML command
#
			$a = explode( " ", $v );
			$cnt = count( $a );
#
#	Now look for the base64 line
#
			for( $i=0; $i<$cnt; $i++ ){
				$type = "NONE";
				if( preg_match(";text/css;i", $a[$i]) ){ $type = "css"; }
					else if( preg_match(";image/x-icon;i", $a[$i]) ){ $type = "ico"; }
					else if( preg_match(";image/svg\+xml;i", $a[$i]) ){ $type = "svg"; }
					else if( preg_match(";image/png;i", $a[$i]) ){ $type = "png"; }
					else if( preg_match(";image/gif;i", $a[$i]) ){ $type = "gif"; }
					else if( preg_match(";image/jpg;i", $a[$i]) ){ $type = "jpg"; }
					else if( preg_match(";image/jpeg;i", $a[$i]) ){ $type = "jpg"; }
					else if( preg_match(";image/bmp;i", $a[$i]) ){ $type = "bmp"; }
					else if( preg_match(";image/webp;i", $a[$i]) ){ $type = "webp"; }

				if( $type == "NONE" ){ continue; }

				if( preg_match("/base64/i", $a[$i]) ){
					$b = explode( "base64", $a[$i] );
					array_shift( $b );	#	Get rid of the first line
					if( preg_match("/$dq>$/", $b[0]) ){
						$b = str_replace( "$dq>", "", $b[0] );
						}
						else if( preg_match("/$dq$/", $b[0]) ){
							$b = str_replace( "$dq", "", $b[0] );
							}
						else { $b = $b[0]; }

					$b = base64_decode( $b );
					$base64[$num_base64] = [];
					$base64[$num_base64]['type'] = $type;
					$base64[$num_base64++]['image'] = $b;
				}
			}
		}
	}
#
#	Now check the base64s against themselves
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	foreach( $base64 as $k=>$v ){
		for( $i=$k-1; $i>=0; $i-- ){
			if( $v === $base64[$i] ){ $base64[$k] = "$filename:$i;"; }
			}
		}
#
#	Look to see if we already have this source.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	$id = null;
	foreach( $this->all_base64 as $k=>$v ){
		foreach( $v as $k1=>$v1 ){
			foreach( $base64 as $k2=>$v2 ){
				if( $v1 === $v2 ){ $base64[$k2] = "$filename:$k1:$k;"; }
				}
			}
		}

	$this->all_base64[$filename] = $base64;
	return true;
}
################################################################################
#	save_page(). Save the web page and get rid of it.
################################################################################
function save_page()
{
$pr = $this->pr;
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	$path = $this->pathinfo['dirname'];
	$basename = $this->pathinfo['basename'];
	$filename = $this->pathinfo['filename'];
	$ext = $this->pathinfo['extension'];
#
#	Save the web page. It is taking up too much space and is not being checked.
#
	$html_path = "$path/html";
	if( !file_exists($html_path) ){ mkdir( $html_path, 0777 ); }
	file_put_contents( "$html_path/$basename", $this->all_htmls[$filename] );
	unset( $this->all_htmls[$filename] );
	return true;
}
################################################################################
#	save_all(). Save all of the information we got (or print it out).
################################################################################
function save_all( $files )
{
	$pr = $this->pr;
#$pr->pr( $this->all_base64, "all_base64 = " );
#
#	Save all files we looked at
#
	foreach( $files as $k=>$v ){
		$pathinfo = pathinfo( $v );
		$path = $pathinfo['dirname'];
		$file = $pathinfo['basename'];
		$filename = $pathinfo['filename'];
		$ext = $pathinfo['extension'];

		$html_path = "$path/html";
		$script_path = "$html_path/scripts";
		$script_cnt = count( $this->all_scripts[$filename] );
		if( !file_exists($script_path) ){ mkdir( $script_path, 0777 ); }

		file_put_contents( "$script_path/$filename-scripts.htm", $this->all_scripts[$filename] );

		$link_path = "$html_path/links";
		$link_cnt = count( $this->all_links[$filename] );
		if( !file_exists($link_path) ){ mkdir( $link_path, 0777 ); }

		file_put_contents( "$link_path/$filename-links.htm", $this->all_links[$filename] );
#
#	Write out the HTML code we found for images.
#
		$image = $this->all_images[$filename];
		$image_path = "$html_path/html-images";
		$image_cnt = count( $image );
		if( !file_exists($image_path) ){ mkdir( $image_path, 0777 ); }

		$loc = "$image_path/$filename-images.htm";
		file_put_contents( $loc, $image );

		$source_path = "$html_path/source";
		$source_cnt = count( $this->all_sources[$filename] );
		if( !file_exists($source_path) ){ mkdir( $source_path, 0777 ); }

		file_put_contents( "$source_path/$filename-source.htm", $this->all_sources[$filename] );

		$base64 = $this->all_base64[$filename];
		$base64_cnt = count( $base64 );
		$base64_path = "$html_path/images";
		if( !file_exists($base64_path) ){ mkdir( $base64_path, 0777 ); }

		for( $i=0; $i<$base64_cnt; $i++ ){
			if( is_array($base64[$i]) ){
				$image = $base64[$i]['image'];
				$type = $base64[$i]['type'];
				$file = "$base64_path/$filename-image-$i.$type";
				file_put_contents( $file, $image );
				}
				else {
					$file = "$base64_path/$filename-image-$i.dup";
					file_put_contents( $file, $base64[$i] );
					}
			}
		}
}
################################################################################
#	set_hexed_directory(). Set where we write the HEXED information.
################################################################################
function set_hexed_directory( $dir=null )
{
	if( is_null($dir) ){ return false; }
	$this->hexed_dir = $dir;
	if( !file_exists($dir) ){ mkdir( $dir, 0777 ); }
	return true;
}
################################################################################
#	save_files(). Save all of the current information we have.
################################################################################
function save_files()
{
$pr = $this->pr;
#$pr->pr( count($this->all_base64), "Count of base64 = " );

	$chex = $this->chex;
	$dir = $this->hexed_dir;
	if( is_null($this->hexed_dir) ){
		die( "***** ERROR : HEXED Directory is not set\n" );
		}
#
#	Write out the SCRIPTS we already have.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	if( count($this->all_scripts) ){
		$hexed = $chex->encode( $this->all_scripts );
		file_put_contents( "$dir/scripts.hex", $hexed );
		}
#
#	Write out the LINKS we already have.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	if( count($this->all_links) ){
		$hexed = $chex->encode( $this->all_links );
		file_put_contents( "$dir/links.hex", $hexed );
		}
#
#	Write out the IMAGES we already have.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	if( count($this->all_images) ){
		$hexed = $chex->encode( $this->all_images );
		file_put_contents( "$dir/images.hex", $hexed );
		}
#
#	Write out the SOURCES we already have.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	if( count($this->all_sources) ){
		$hexed = $chex->encode( $this->all_sources );
		file_put_contents( "$dir/sources.hex", $hexed );
		}
#
#	Write out the base64 we already have.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	if( count($this->all_base64) ){
#$pr->pr( count($this->all_base64), "Count of base64 = " );
		$hexed = $chex->encode( $this->all_base64 );
#$pr->pr( $hexed, "HEXED = " );
#$pr->pr( count($this->all_base64), "Count of base64 = " );
		file_put_contents( "$dir/base64.hex", $hexed );
#$pr->pr( count($this->all_base64), "Count of base64 = " );
		}
		else {
			die( "***** ERROR : \$all_base64 is EMPTY!\n" );
			}

	return true;
}
################################################################################
#	load_files(). Load in any/all HEXED files we may have written out.
################################################################################
function load_files()
{
$pr = $this->pr;
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	$chex = $this->chex;
	$dir = $this->hexed_dir;
	if( is_null($dir) ){
		die( "***** ERROR : HEXED Directory is not set\n" );
		}
#
#	Write out the SCRIPTS we already have.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	if( file_exists("$dir/scripts.hex") ){
		$hexed = file_get_contents( "$dir/scripts.hex" );
		$this->all_scripts = $chex->decode( $hexed );
		}
#
#	Write out the LINKS we already have.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	if( file_exists("$dir/links.hex") ){
		$hexed = file_get_contents( "$dir/links.hex" );
		$this->all_links = $chex->decode( $hexed );
		}
#
#	Write out the IMAGES we already have.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	if( file_exists("$dir/images.hex") ){
		$hexed = file_get_contents( "$dir/images.hex" );
		$this->all_images = $chex->decode( $hexed );
		}
#
#	Write out the SOURCES we already have.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	if( file_exists("$dir/sources.hex") ){
		$hexed = file_get_contents( "$dir/sources.hex" );
		$this->all_sources = $chex->decode( $hexed );
		}
#
#	Write out the base64 we already have.
#
#$pr->pr( count($this->all_base64), "Count of base64 = " );
	if( file_exists("$dir/base64.hex") ){
		$hexed = file_get_contents( "$dir/base64.hex" );
		$this->all_base64 = $chex->decode( $hexed );
		}

	return true;
}
################################################################################
#	__destruct(). Get rid of everything and close the class.
################################################################################
function __destruct()
{
}

}

?>
