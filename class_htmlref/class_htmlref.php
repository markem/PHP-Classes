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
#	class_htmlref();
#
#-Description:
#
#	A class to handle my html references.
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
#	Mark Manning			Simulacron I			Sun 12/07/2019 15:33:42.40 
#		Original Program.
#
#	Mark Manning			Simulacron I			Sat 12/17/2021 14:56:52.53 
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
#	Mark Manning			Simulacron I			Wed 12/07/2022 13:20:00.87 
#	---------------------------------------------------------------------------
#	Note that all lines saying "Taken from" means THOSE people really are the
#	authors of the following information. Just so you don't think I'm just
#	stealing information from people!
#
#	Mark Manning			Simulacron I			Wed 12/07/2022 13:32:27.07 
#	---------------------------------------------------------------------------
#	This code is using the Dynamic HTML book by Danny Goodman as the basis.
#
#	Mark Manning			Simulacron I			Wed 05/05/2021 16:37:40.51 
#	---------------------------------------------------------------------------
#	Please note that _MY_ Legal notice _HERE_ is as follows:
#
#		CLASS_HTMLREF.PHP. A class to handle all of the HTML references.
#		Copyright (C) 2001-NOW.  Mark Manning. All rights reserved
#		except for those given by the BSD License.
#
#	Please place _YOUR_ legal notices _HERE_. Thank you.
#
#END DOC
################################################################################
class class_htmlref
{
	private	$html_cmds = null;
	private	$html_global_attributes = null;
	private	$html_window_events = null;
	private	$html_form_events = null;
	private	$html_mouse_events = null;
	private	$html_keyboard_events = null;
	private	$html_clipboard_events = null;
	private	$html_media_events = null;
	private	$css_cmds = null;
	private	$css_props = null;
	private	$css_atrules = null;
	private	$css_aural = null;
	private	$css_units = null;
	private	$fonts_safe = null;
################################################################################
#	__construct(). Constructor.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['htmlref']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['htmlref']; }
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
#	Taken from :
#		Danny Goodman's Dynamic HTML Book
#		https://way2tutorial.com/html/tag/index.php
#		https://www.tutorialrepublic.com/html-reference/html5-tags.php
#
	$html_cmds = [
#		[ "Start Tag|End Tag", "Description", "Version", "True=USED/False=REMOVED", "true= <xxx/>, false=null" ]
		[ "!--|--", "Describe a comment text in the source code", 1, true, false ],
		[ "!doctype", "Defines a document type", 1, true, true ],
		[ "a", "Specific a anchor (Hyperlink) Use for link in internal/external web documents.", 1, true, false ],
		[ "abbr", "Describes an abbreviation (acronyms)", 1, true, true ],
		[ "acronym", "Describes an acronyms", 5, false, true ],
		[ "address", "Describes an address information", 1, true, true ],
		[ "applet", "Embedding an applet in HTML document", 4, false, true ],
		[ "area", "Defines an area in an image map", 1, true, true ],
		[ "article", "Defines an article", 5, true, true ],
		[ "aside", "Describes contain set(or write) on aside place in page contain", 5, true, true ],
		[ "audio", "Specific audio content", 5, true, true ],
		[ "b", "Specific text weight bold", 1, true, true ],
		[ "base", "Define a base URL for all the links with in a web page", 1, true, true ],
		[ "basefont", "Describes a default font color, size, face in a document", 4, false, true ],
		[ "bdi", "Represents text that is isolated from its surrounding for the purposes of " .
			"bidirectional text formatting.", 5, true, true ],
		[ "bdo", "Specific direction of text display", 1, true, true ],
		[ "big", "Defines a big text", 5, false, true ],
		[ "blockquote", "Specifies a long quotation", 1, true, true ],
		[ "body", "Defines a main section(body) part in HTML document", 1, true, true ],
		[ "br", "Specific a single line break", 1, true, false ],
		[ "button", "Specifies a press/push button", 1, true, true ],
		[ "canvas", "Specifies the display graphics on HTML web documment", 5, true, true ],
		[ "caption", "Define a table caption", 1, true, true ],
		[ "center", "Specifies a text is display in center align", 4, false, true ],
		[ "cite", "Specifies a text citation", 1, true, true ],
		[ "code", "Specifies computer code text", 1, true, true ],
		[ "col", "Specifies a each column within a <colgroup> element in table", 1, true, true ],
		[ "colgroup", "Defines a group of one or more columns inside table", 1, true, true ],
		[ "command", "Define a command button, invoke as per user action", 5, false, true ],
		[ "data", "Links a piece of content with a machine-readable translation.", 5, true, true ],
		[ "datagrid", "Define a represent data in datagrid either list wise or tree wise", 5, false, true ],
		[ "datalist", "Define a list of pre-defined options surrounding <input> tag", 5, true, true ],
		[ "dd", "Defines a definition description in a definition list", 1, true, true ],
		[ "del", "Specific text deleted in web document", 1, true, true ],
		[ "details", "Define a additional details hide or show as per user action", 5, true, true ],
		[ "dfn", "Define a definition team", 1, true, true ],
		[ "dialog", "Define a chat conversation between one or more person", 5, true, true ],
		[ "dir", "Define a directory list", 4, false, true ],
		[ "div", "Define a division part", 1, true, true ],
		[ "dl", "Define a definition list", 1, true, true ],
		[ "dt", "Define a definition team", 1, true, true ],
		[ "em", "Define a text is emphasize format", 1, true, true ],
		[ "embed", "Define a embedding external application using a relative plug-in", 5, true, true ],
		[ "eventsource", "Defines a source of event generates to remote server", 5, false, true ],
		[ "fieldset", "Defines a grouping of related form elements", 1, true, true ],
		[ "figcaption", "Represents a caption text corresponding with a figure element", 5, true, true ],
		[ "figure", "Represents self-contained content corresponding with a <figcaption> element", 5, true, true ],
		[ "font", "Defines a font size, font face and font color for its text", 4, false, true ],
		[ "footer", "Defines a footer section containing details about the author, copyright, " .
			"contact us, sitemap, or links to related documents.", 5, true, true ],
		[ "form", "Defines a form section that having interactive input controls to submit form information to a server.",
			1, true, true ],
		[ "frame", "Defines frame window.", 2, false, true ],
		[ "frameset", "Used to holds one or more <frame> elements.", 2, false, true ],
		[ "h1", "Defines a Headings level from 1 to 6 different sizes.", 1, true, true ],
		[ "h2", "Defines a Headings level from 1 to 6 different sizes.", 1, true, true ],
		[ "h3", "Defines a Headings level from 1 to 6 different sizes.", 1, true, true ],
		[ "h4", "Defines a Headings level from 1 to 6 different sizes.", 1, true, true ],
		[ "h5", "Defines a Headings level from 1 to 6 different sizes.", 1, true, true ],
		[ "h6", "Defines a Headings level from 1 to 6 different sizes.", 1, true, true ],
		[ "head", "Defines header section of HTML document.", 1, true, true ],
		[ "header", "Defines as a container that hold introductory content or navigation links.", 5, true, true ],
		[ "hgroup", "Defines the heading of a section that hold the h1 to h6 tags.", 5, true, true ],
		[ "hr", "Represent a thematic break between paragraph-level tags. It is typically draw horizontal line.",
			1, true, false ],
		[ "html", "Define a document is a HTML markup language", 1, true, true ],
		[ "i", "Defines a italic format text", 1, true, true ],
		[ "iframe", "Defines a inline frame that embedded external content into current web document.", 1, true, true ],
		[ "img", "Used to insert image into a web document.", 1, true, true ],
		[ "input", "Define a get information in selected input", 1, true, true ],
		[ "ins", "Used to indicate text that is inserted into a page and indicates changes to a document.",
			1, true, true ],
		[ "isindex", "Used to create a single line search prompt for querying the contents of the document.",
			4, false, true ],
		[ "kbd", "Used to identify text that are represents keyboard input.", 1, true, true ],
		[ "keygen", "Used to generate signed certificate, which is used to authenticate to services.", 5, true, true ],
		[ "label", "Used to caption a text label with a form <input> element.", 1, true, true ],
		[ "legend", "Used to add a caption (title) to a group of related form elements that are " .
			"grouped together into the <fieldset> tag.", 1, true, true ],
		[ "li", "Define a list item either ordered list or unordered list.", 1, true, true ],
		[ "listing", "Define a list item either ordered list or unordered list.", 4, false, true ],
		[ "link", "Used to load an external stylesheets into HTML document.", 1, true, true ],
		[ "main", "Represents the main or dominant content of the document.", 5, true, true ],
		[ "map", "Defines an clickable image map.", 1, true, true ],
		[ "mark", "Used to highlighted (marked) specific text.", 5, true, true ],
		[ "menu", "Used to display a unordered list of items/menu of commands.", 4, false, true ],
		[ "menuitem", "Defines a list (or menuitem) of commands that a user can perform.", 5, true, true ],
		[ "meta", "Used to provide structured metadata about a web page.", 1, true, true ],
		[ "meter", "Used to measure data within a given range.", 5, true, true ],
		[ "nav", "Used to defines group of navigation links.", 5, true, true ],
		[ "noframes", "Used to provide a fallback content to the browser that does not support the <frame> element.",
			5, false, true ],
		[ "noscript", "Used to provide an fall-back content to the browser that does not support the JavaScript.",
			1, true, true ],
		[ "object", "Used to embedded objects such as images, audio, videos, Java applets, and Flash animations.",
			1, true, true ],
		[ "ol", "Defines an ordered list of items.", 1, true, true ],
		[ "optgroup", "Used to create a grouping of options, the related options are grouped under specific headings.",
			1, true, true ],
		[ "option", "Represents option items within a <select>, <optgroup> or <datalist> element.", 1, true, true ],
		[ "output", "Used for representing the result of a calculation.", 5, true, true ],
		[ "p", "Used to represents a paragraph text.", 1, true, true ],
		[ "param", "Provides parameters for embedded object element.", 1, true, true ],
		[ "picture", "Defines a container for multiple image sources.", 5, true, true ],
		[ "plaintext", "Use plain text.", 4, false, true ],
		[ "pre", "Used to represents preformatted text.", 1, true, true ],
		[ "progress", "Represents the progress of a task.", 5, true, true ],
		[ "q", "Represents the short quotation.", 1, true, true ],
		[ "rp", "Used to provide parentheses around fall-back content to the browser that " .
			"does not support the ruby annotations.", 5, true, true ],
		[ "rt", "Specifies the ruby text of ruby annotation.", 5, true, true ],
		[ "ruby", "Used to represents a ruby annotation.", 5, true, true ],
		[ "s", "Text display in strikethrough style.", 4, false, true ],
		[ "samp", "Represents text that should be interpreted as sample output from a computer program.", 1, true, true ],
		[ "script", "Defines client-side JavaScript.", 1, true, true ],
		[ "section", "Used to divide a document into number of different generic section.", 5, true, true ],
		[ "select", "Used to create a drop-down list.", 1, true, true ],
		[ "small", "Used to makes the text one size smaller.", 1, true, true ],
		[ "source", "Used to specifies multiple media resources.", 5, true, true ],
		[ "span", "Used to grouping and applying styles to inline elements.", 1, true, true ],
		[ "strike", "Represents strikethrough text.", 4, false, true ],
		[ "strong", "Represents strong emphasis greater important text.", 1, true, true ],
		[ "style", "Used to add CSS style to an HTML document.", 1, true, true ],
		[ "sub", "Represents inline subscript text.", 1, true, true ],
		[ "summary", "Defines a summary for the <details> element.", 5, true, true ],
		[ "sup", "Represents inline superscript text.", 1, true, true ],
		[ "svg", "Embed SVG (Scalable Vector Graphics) content in an HTML document.", 5, true, true ],
		[ "table", "Used to defines a table in an HTML document.", 1, true, true ],
		[ "tbody", "Used for grouping table rows.", 1, true, true ],
		[ "td", "Used for creates standard data cell in HTML table.", 1, true, true ],
		[ "template", "Defines the fragments of HTML that should be hidden when the page " .
			"is loaded, but can be cloned and inserted in the document by JavaScript.", 5, true, true ],
		[ "textarea", "Create multi-line text input.", 1, true, true ],
		[ "tfoot", "Used to adding a footer to a table that containing summary of the table data.", 1, true, true ],
		[ "th", "Used for creates header of a group of cell in HTML table.", 1, true, true ],
		[ "thead", "Used to adding a header to a table that containing header information of the table.", 1, true, true ],
		[ "time", "Represents the date and/or time in an HTML document.", 5, true, true ],
		[ "title", "Represents title to an HTML document.", 1, true, true ],
		[ "tr", "Defines a row of cells in a table.", 1, true, true ],
		[ "track", "Represents text tracks for both the <audio> and <video> tags.", 5, true, true ],
		[ "tt", "Represents teletype text.", 5, false, true ],
		[ "u", "Represents underlined text.", 1, true, true ],
		[ "ul", "Defines an unordered list of items.", 1, true, true ],
		[ "var", "Represents a variable in a computer program or mathematical equation.", 1, true, true ],
		[ "video", "Used to embed video content.", 5, true, true ],
		[ "wbr", "Defines a word break opportunity in a long string of text.", 5, true, true ],
		[ "xmp", "The XMP element displays its content in a monospace font as a block element, " .
			"as in computer code listings rendered 80 columns wide.", 4, false, true ],
		];
#
#	Taken from :	https://www.tutorialrepublic.com/html-reference/html5-global-attributes.php
#
	$html_global_attributes = [
#		[ "Attribute", "Value", "Description", Version, Used ],
		[ "accesskey", "shortcut key", "Specifies a keyboard shortcut to activate or focus the element.", 1, true ],
		[ "class", "classname", "Assigns a class name or space-separated list of class names to an element.", 1, true ],
		[ "contenteditable", "true", 1, true ],
		[ "false", "Indicates whether the content of an element is editable by the user or not.", 1, true ],
		[ "contextmenu", "menu-id", "Specifies a context menu for an element. A context menu is a " .
			"menu that appears when the user clicks the right mouse button on the element.", 1, true ],
		[ "data-*", "data", "Specified on any HTML element, to store custom data specific to the page.", 1, true ],
		[ "dir", "ltr", 1, true ],
		[ "rtl", "Specifies the base direction of directionality of the element's text.", 1, true ],
		[ "draggable", "true", 1, true ],
		[ "false", "Specifies whether an element is draggable or not.", 1, true ],
		[ "dropzone", "copy", 1, true ],
		[ "move", 1, true ],
		[ "link", "Specifies whether the dragged data is copied, moved, or linked, when dropped.", 1, true ],
		[ "hidden", "hidden", "Indicates that the element is not yet, or is no longer, relevant.", 1, true ],
		[ "id", "name", "Specifies a unique identifier (ID) for an element which must be unique in the " .
			"whole document.", 1, true ],
		[ "lang", "language-code", "Specifies the primary language for the element's text content.", 1, true ],
		[ "language", "language-code", "Sets the scripting language (and switches on the desired scripting " .
			"eng1ne) for script statements defined in the eletnent (such as event handler script stateme nts " .
			"in the tag).", 5, false ],
		[ "spellcheck", "true", 1, true ],
		[ "false", "Specifies whether the element may be checked for spelling errors or not.", 1, true ],
		[ "style", "style", "Specifies inline style information for an element.", 1, true ],
		[ "tabindex", "number", "Specifies the tabbing order of an element.", 1, true ],
		[ "title", "text", "Provides advisory information related to the element. It would be " .
			"appropriate for a tooltip.", 1, true ],
		[ "translate", "yes", 1, true ],
		[ "no", "Specifies whether the text content of an element should be translated or not.", 1, true ],
		[ "xml:lang", "language-code", "Specifies the primary language for the element's text content, " .
			"in XHTML documents.", 1, true ],
		];
#
#	Taken from : https://www.tutorialrepublic.com/html-reference/html5-event-attributes.php
#
	$html_window_events = [
#		[ "Attribute", "Value", "Description", Version, Used ],
		[ "onafterprint", "script", "Fires after the associated document is printed.", 1, true ],
		[ "onbeforeprint", "script", "Fires before the associated document is printed.", 1, true ],
		[ "onbeforeunload", "script", "Fires before a document being unloaded.", 1, true ],
		[ "onerror", "script", "Fires when document errors occur.", 1, true ],
		[ "onhashchange", "script", "Fires when the fragment identifier part of " .
			"the document's URL i.e. the portion of a URL that follows the sign (#) changes.", 1, true ],
		[ "onload", "script", "Fires when the document has finished loading.", 1, true ],
		[ "onmessage", "script", "Fires when the message event occurs i.e. when user sends " .
			"a cross-document message or a message is sent from a worker with postMessage() " .
			"method. See HTML5 Web Workers.", 1, true ],
		[ "onoffline", "script", "Fires when the network connection fails and the browser " .
			"starts working offline.", 1, true ],
		[ "ononline", "script", "Fires when the network connections returns and the browser " .
			"starts working online.", 1, true ],
		[ "onpagehide", "script", "Fires when the page is hidden, such as when a user is " .
			"moving to another webpage.", 1, true ],
		[ "onpageshow", "script", "Fires when the page is shown, such as when a user " .
			"navigates to a webpage.", 1, true ],
		[ "onpopstate", "script", "Fires when changes are made to the active history.", 1, true ],
		[ "onresize", "script", "Fires when the browser window is resized.", 1, true ],
		[ "onstorage", "script", "Fires when a Web Storage area is updated.", 1, true ],
		[ "onunload", "script", "Fires immediately before the document is unloaded or " .
			"the browser window is closed.", 1, true ]
		];
#
#	Taken from : https://www.tutorialrepublic.com/html-reference/html5-event-attributes.php
#
	$html_form_events = [
#		[ "Attribute", "Value", "Description", Version, Used ],
		[ "onblur", "script", "Fires when an element loses focus.", 1, true ],
		[ "onchange", "script", "Fires when the value or state of the element is changed.", 1, true ],
		[ "onfocus", "script", "Fires when the element receives focus.", 1, true ],
		[ "oninput", "script", "Fires when the value of an element is changed by the user.", 1, true ],
		[ "oninvalid", "script", "Fires when a submittable element do not satisfy their constraints " .
			"during form validation.", 1, true ],
		[ "onreset", "script", "Fires when the user resets a form.", 1, true ],
		[ "onselect", "script", "Fires when some text is being selected or the current selection is " .
			"changed by the user.", 1, true ],
		[ "onsearch", "script", "Fires when the user writes something in a search input field.", 1, true ],
		[ "onsubmit", "script", "Fires when a form is submitted.", 1, true ]
		];
#
#	Taken from : https://www.tutorialrepublic.com/html-reference/html5-event-attributes.php
#
	$html_mouse_events = [
#		[ "Attribute", "Value", "Description", Version, Used ],
		[ "onclick", "script", "Fires when the user clicks the left mouse button on the element.", 1, true ],
		[ "ondblclick", "script", "Fires when the user double-clicks on the element.", 1, true ],
		[ "oncontextmenu", "script", "Fires when a context menu is triggered by the user through " .
			"right-click on the element.", 1, true ],
		[ "ondrag", "script", "Fires when the user drags an element. The ondrag event fires " .
			"throughout the drag operation.", 1, true ],
		[ "ondragend", "script", "Fires when the user releases the mouse button at the " .
			"end of a drag operation.", 1, true ],
		[ "ondragenter", "script", "Fires when the user drags an element to a valid drop target.", 1, true ],
		[ "ondragleave", "script", "Fires when an element leaves a valid drop target " .
			"during a drag operation.", 1, true ],
		[ "ondragover", "script", "Fires when an element is being dragged over a valid drop target.", 1, true ],
		[ "ondragstart", "script", "Fires when the user starts to drag a text " .
			"selection or selected element.", 1, true ],
		[ "ondrop", "script", "Fires when the mouse button is released during a drag-and-drop " .
			"operation i.e. when dragged element is being dropped.", 1, true ],
		[ "onmousedown", "script", "Fires when the mouse button is pressed over an element.", 1, true ],
		[ "onmousemove", "script", "Fires when the user moves the mouse pointer over an element.", 1, true ],
		[ "onmouseout", "script", "Fires when the user moves the mouse pointer outside the " .
			"boundaries of an element.", 1, true ],
		[ "onmouseover", "script", "Fires when the user moves the mouse pointer onto an element.", 1, true ],
		[ "onmouseup", "script", "Fires when the user releases the mouse button while " .
			"the mouse is over an element.", 1, true ],
		[ "onmousewheel", "script", "Deprecated Use the onwheel attribute instead.", 1, true ],
		[ "onscroll", "script", "Fires when the user scrolls the contents of an element by " .
			"scrolling the element's scrollbar.", 1, true ],
		[ "onshow", "script", "Fires when a contextmenu event was fired onto an element that " .
			"has a contextmenu attribute.", 1, true ],
		[ "ontoggle", "script", "Fires when the user opens or closes the <details> element.", 1, true ],
		[ "onwheel", "script", "Fires when the user scrolls the contents of an element by rolling " .
			"the mouse wheel up or down over an element.", 1, true ]
		];
#
#	Taken from : https://www.tutorialrepublic.com/html-reference/html5-event-attributes.php
#
	$html_keyboard_events = [
#		[ "Attribute", "Value", "Description", Version, Used ],
		[ "onkeydown", "script", "Fires when the user presses a key.", 1, true ],
		[ "onkeypress", "script", "Fires when the user presses an alphanumeric key.", 1, true ],
		[ "onkeyup", "script", "Fires when the user releases a key.", 1, true ]
		];
#
#	Taken from : https://www.tutorialrepublic.com/html-reference/html5-event-attributes.php
#
	$html_clipboard_events = [
#		[ "Attribute", "Value", "Description", Version, Used ],
		[ "oncopy", "script", "Fires when the user copies the element or selection, " .
			"adding it to the system clipboard.", 1, true ],
		[ "oncut", "script", "Fires when the element or selection is removed from " .
			"the document and added to the system clipboard.", 1, true ],
		[ "onpaste", "script", "Fires when the user pastes data, transferring the " .
			"data from the system clipboard to the document.", 1, true ]
		];
#
#	Taken from : https://www.tutorialrepublic.com/html-reference/html5-event-attributes.php
#
	$html_media_events = [
#		[ "Attribute", "Value", "Description", Version, Used ],
		[ "onabort", "script", "Fires when playback is aborted, but not due to an error.", 1, true ],
		[ "oncanplay", "script", "Fires when enough data is available to play the media, " .
			"at least for a couple of frames, but would require further buffering.", 1, true ],
		[ "oncanplaythrough", "script", "Fires when entire media can be played to the end " .
			"without requiring to stop for further buffering.", 1, true ],
		[ "oncuechange", "script", "Fires when the text track cue in a <track> element changes.", 1, true ],
		[ "ondurationchange", "script", "Fires when the duration of the media changes.", 1, true ],
		[ "onemptied", "script", "Fires when the media element is reset to its initial state, " .
			"either because of a fatal error during load, or because the load() method is called to reload it.", 1, true ],
		[ "onended", "script", "Fires when the end of playback is reached.", 1, true ],
		[ "onerror", "script", "Fires when an error occurs while fetching the media data.", 1, true ],
		[ "onloadeddata", "script", "Fires when media data is loaded at the current playback position.", 1, true ],
		[ "onloadedmetadata", "script", "Fires when metadata of the media (like duration and " .
			"dimensions) has finished loading.", 1, true ],
		[ "onloadstart", "script", "Fires when loading of the media begins.", 1, true ],
		[ "onpause", "script", "Fires when playback is paused, either by the user or programmatically.", 1, true ],
		[ "onplay", "script", "Fires when playback of the media starts after having been paused i.e. " .
			"when the play() method is requested.", 1, true ],
		[ "onplaying", "script", "Fires when the audio or video has started playing.", 1, true ],
		[ "onprogress", "script", "Fires periodically to indicate the progress while downloading " .
			"the media data.", 1, true ],
		[ "onratechange", "script", "Fires when the playback rate or speed is increased or decreased, " .
			"like slow motion or fast forward mode.", 1, true ],
		[ "onseeked", "script", "Fires when the seek operation ends.", 1, true ],
		[ "onseeking", "script", "Fires when the current playback position is moved.", 1, true ],
		[ "onstalled", "script", "Fires when the download has stopped unexpectedly.", 1, true ],
		[ "onsuspend", "script", "Fires when the loading of the media is intentionally stopped.", 1, true ],
		[ "ontimeupdate", "script", "Fires when the playback position changed, like when the user " .
			"fast forwards to a different playback position.", 1, true ],
		[ "onvolumechange", "script", "Fires when the volume is changed, or playback is muted or unmuted.", 1, true ],
		[ "onwaiting", "script", "Fires when playback stops because the next frame of a video " .
			"resource is not available.", 1, true ]
		];
#
#	Taken from : https://www.tutorialrepublic.com/css-reference/css3-properties.php
#
	$css_cmds = [
#		[ "Tag", "Descrition", "Version created/removed", "True=USED/False=REMOVED", "Elements removed from" ]
		[ "Property", "Description", 1, true, "" ],
		[ "align", "Text-align and vertical align style attributes", 4, false,
			"CAPTION, IFRAME, OBJECT, HR, DIV, APPLET, IMG, INPUT, LEGEND, TABLE, H1H6, P" ],
		[ "align-content", "Specifies the alignment of flexible container's items within " .
			"the flex container.", 1, true, "" ],
		[ "align-items", "Specifies the default alignment for items within the flex container.", 1, true, "" ],
		[ "align-self", "Specifies the alignment for selected items within the flex container.", 1, true, "" ],
		[ "alink", "A:active {color:}", 4, false, "BODY" ],
		[ "alt", "OBJECT element TITLE attribute", 4, false, "APPLET" ],
		[ "animation", "Specifies the keyframe-based animations.", 1, true, "" ],
		[ "animation-delay", "Specifies when the animation will start.", 1, true, "" ],
		[ "animation-direction", "Specifies whether the animation should play in reverse " .
			"on alternate cycles or not.", 1, true, "" ],
		[ "animation-duration",
			"Specifies the number of seconds or milliseconds an animation should take to " .
			"complete one cycle.", 1, true, "" ],
		[ "animation-fill-mode",
			"Specifies how a CSS animation should apply styles to its target before and " .
			"after it is executing.", 1, true, "" ],
		[ "animation-iteration-count",
			"Specifies the number of times an animation cycle should be played before stopping.", 1, true, "" ],
		[ "animation-name",
			"Specifies the name of @keyframes defined animations that should be applied " .
			"to the selected element.", 1, true, "" ],
		[ "animation-play-state", "Specifies whether the animation is running or paused.", 1, true, "" ],
		[ "animation-timing-function",
			"Specifies how a CSS animation should progress over the duration of each cycle.", 1, true, "" ],
		[ "archive", "OBJECT element ARCHIVE attribute", 4, false, "APPLET" ],
		[ "backface-visibility",
			"Specifies whether or not the 'back' side of a transformed element is visible " .
			"when facing the user.", 1, true, "" ],
		[ "background", "Defines a variety of background properties within one declaration.", 1, true, "" ],
		[ "background", "background style attribute", 4, false, "BODY" ],
		[ "background-attachment", "Specify whether the background image is fixed in the " .
			"viewport or scrolls.", 1, true, "" ],
		[ "background-clip", "Specifies the painting area of the background.", 1, true, "" ],
		[ "background-color", "Defines an element's background color.", 1, true, "" ],
		[ "background-image", "Defines an element's background image.", 1, true, "" ],
		[ "background-origin", "Specifies the positioning area of the background images.", 1, true, "" ],
		[ "background-position", "Defines the origin of a background image.", 1, true, "" ],
		[ "background-repeat", "Specify whether/how the background image is tiled.", 1, true, "" ],
		[ "background-size", "Specifies the size of the background images.", 1, true, "" ],
		[ "bgcolor", "background-color style attribute", 4, false, "BODY TABLE TD TH TR" ],
		[ "border", "Sets the width, style, and color for all four sides of an element's border.", 1, true, "" ],
		[ "border", "border-width style attributes", 4, false, "IMG OBJECT" ],
		[ "border-bottom", "Sets the width, style, and color of the bottom border of an element.", 1, true, "" ],
		[ "border-bottom-color", "Sets the color of the bottom border of an element.", 1, true, "" ],
		[ "border-bottom-left-radius", "Defines the shape of the bottom-left border corner of an element.", 1, true, "" ],
		[ "border-bottom-right-radius", "Defines the shape of the bottom-right border corner of an element.", 1, true, "" ],
		[ "border-bottom-style", "Sets the style of the bottom border of an element.", 1, true, "" ],
		[ "border-bottom-width", "Sets the width of the bottom border of an element.", 1, true, "" ],
		[ "border-collapse", "Specifies whether table cell borders are connected or separated.", 1, true, "" ],
		[ "border-color", "Sets the color of the border on all the four sides of an element.", 1, true, "" ],
		[ "border-image", "Specifies how an image is to be used in place of the border styles.", 1, true, "" ],
		[ "border-image-outset", "Specifies the amount by which the border image area extends " .
			"beyond the border box.", 1, true, "" ],
		[ "border-image-repeat", "Specifies whether the image-border should be repeated, " .
			"rounded or stretched.", 1, true, "" ],
		[ "border-image-slice", "Specifies the inward offsets of the image-border.", 1, true, "" ],
		[ "border-image-source", "Specifies the location of the image to be used as a border.", 1, true, "" ],
		[ "border-image-width", "Specifies the width of the image-border.", 1, true, "" ],
		[ "border-left", "Sets the width, style, and color of the left border of an element.", 1, true, "" ],
		[ "border-left-color", "Sets the color of the left border of an element.", 1, true, "" ],
		[ "border-left-style", "Sets the style of the left border of an element.", 1, true, "" ],
		[ "border-left-width", "Sets the width of the left border of an element.", 1, true, "" ],
		[ "border-radius", "Defines the shape of the border corners of an element.", 1, true, "" ],
		[ "border-right", "Sets the width, style, and color of the right border of an element.", 1, true, "" ],
		[ "border-right-color", "Sets the color of the right border of an element.", 1, true, "" ],
		[ "border-right-style", "Sets the style of the right border of an element.", 1, true, "" ],
		[ "border-right-width", "Sets the width of the right border of an element.", 1, true, "" ],
		[ "border-spacing", "Sets the spacing between the borders of adjacent table cells.", 1, true, "" ],
		[ "border-style", "Sets the style of the border on all the four sides of an element.", 1, true, "" ],
		[ "border-top", "Sets the width, style, and color of the top border of an element.", 1, true, "" ],
		[ "border-top-color", "Sets the color of the top border of an element.", 1, true, "" ],
		[ "border-top-left-radius", "Defines the shape of the top-left border corner of an element.", 1, true, "" ],
		[ "border-top-right-radius", "Defines the shape of the top-right border corner of an element.", 1, true, "" ],
		[ "border-top-style", "Sets the style of the top border of an element.", 1, true, "" ],
		[ "border-top-width", "Sets the width of the top border of an element.", 1, true, "" ],
		[ "border-width", "Sets the width of the border on all the four sides of an element.", 1, true, "" ],
		[ "bottom", "Specify the location of the bottom edge of the positioned element.", 1, true, "" ],
		[ "box-shadow", "Applies one or more drop-shadows to the element's box.", 1, true, "" ],
		[ "box-sizing", "Alter the default CSS box model.", 1, true, "" ],
		[ "caption-side", "Specify the position of table's caption.", 1, true, "" ],
		[ "clear", "Specifies the placement of an element in relation to floating elements.", 1, true, "" ],
		[ "clear", "clear style attribute", 4, false, "BR" ],
		[ "clip", "Defines the clipping region.", 1, true, "" ],
		[ "code", "OBJECT element CLASSID attribute", 4, false, "APPLET" ],
		[ "CODEBASE", "OBJECT element CODEBASE attribute", 4, false, "DIR, DL, MENU, OL, UL" ],
		[ "color", "Specify the color of the text of an element.", 1, true, "" ],
		[ "color", "color style attribute", 4, false, "BASEFONT, FONT" ],
		[ "column-count", "Specifies the number of columns in a multi-column element.", 1, true, "" ],
		[ "column-fill", "Specifies how columns will be filled.", 1, true, "" ],
		[ "column-gap", "Specifies the gap between the columns in a multi-column element.", 1, true, "" ],
		[ "column-rule",
			"Specifies a straight line, or 'rule', to be drawn between each column " .
			"in a multi-column element.", 1, true, "" ],
		[ "column-rule-color", "Specifies the color of the rules drawn between columns " .
			"in a multi-column layout.", 1, true, "" ],
		[ "column-rule-style", "Specifies the style of the rule drawn between the columns " .
			"in a multi-column layout.", 1, true, "" ],
		[ "column-rule-width", "Specifies the width of the rule drawn between the columns " .
			"in a multi-column layout.", 1, true, "" ],
		[ "column-span", "Specifies how many columns an element spans across in a multi-column layout.", 1, true, "" ],
		[ "column-width", "Specifies the optimal width of the columns in a multi-column element.", 1, true, "" ],
		[ "columns", "A shorthand property for setting column-width and column-count properties.", 1, true, "" ],
		[ "COMPACT", "{display:compact}", 4, false, "BASEFONT, FONT" ],
		[ "content", "Inserts generated content.", 1, true, "" ],
		[ "counter-increment", "Increments one or more counter values.", 1, true, "" ],
		[ "counter-reset", "Creates or resets one or more counters.", 1, true, "" ],
		[ "cursor", "Specify the type of cursor.", 1, true, "" ],
		[ "direction", "Define the text direction/writing direction.", 1, true, "" ],
		[ "display", "Specifies how an element is displayed onscreen.", 1, true, "" ],
		[ "empty-cells", "Show or hide borders and backgrounds of empty table cells.", 1, true, "" ],
		[ "FACE", "font-face style attribute", 4, false, "BASEFONT, FONT" ],
		[ "flex", "Specifies the components of a flexible length.", 1, true, "" ],
		[ "flex-basis", "Specifies the initial main size of the flex item.", 1, true, "" ],
		[ "flex-direction", "Specifies the direction of the flexible items.", 1, true, "" ],
		[ "flex-flow", "A shorthand property for the flex-direction and the flex-wrap properties.", 1, true, "" ],
		[ "flex-grow",
			"Specifies how the flex item will grow relative to the other items inside " .
			"the flex container.", 1, true, "" ],
		[ "flex-shrink",
			"Specifies how the flex item will shrink relative to the other items inside " .
			"the flex container.", 1, true, "" ],
		[ "flex-wrap", "Specifies whether the flexible items should wrap or not.", 1, true, "" ],
		[ "float", "Specifies whether or not a box should float.", 1, true, "" ],
		[ "font", "Defines a variety of font properties within one declaration.", 1, true, "" ],
		[ "font-family", "Defines a list of fonts for element.", 1, true, "" ],
		[ "font-size", "Defines the font size for the text.", 1, true, "" ],
		[ "font-size-adjust", "Preserves the readability of text when font fallback occurs.", 1, true, "" ],
		[ "font-stretch", "Selects a normal, condensed, or expanded face from a font.", 1, true, "" ],
		[ "font-style", "Defines the font style for the text.", 1, true, "" ],
		[ "font-variant", "Specify the font variant.", 1, true, "" ],
		[ "font-weight", "Specify the font weight of the text.", 1, true, "" ],
		[ "height", "Specify the height of an element.", 1, true, "" ],
		[ "HEIGHT", "OBJECT element HEIGHT attribute", 4, false, "APPLET" ],
		[ "HEIGHT", "height positioning style attribute", 4, false, "APPLET" ],
		[ "HSPACE", "left positioning style attribute", 4, false, "IMG, OBJECT" ],
		[ "justify-content",
			"Specifies how flex items are aligned along the main axis of the flex container " .
			"after any flexible lengths and auto margins have been resolved.", 1, true, "" ],
		[ "left", "Specify the location of the left edge of the positioned element.", 1, true, "" ],
		[ "letter-spacing", "Sets the extra spacing between letters.", 1, true, "" ],
		[ "line-height", "Sets the height between lines of text.", 1, true, "" ],
		[ "LINK", "A:link {color:}", 4, false, "BODY" ],
		[ "list-style", "Defines the display style for a list and list elements.", 1, true, "" ],
		[ "list-style-image", "Specifies the image to be used as a list-item marker.", 1, true, "" ],
		[ "list-style-position", "Specifies the position of the list-item marker.", 1, true, "" ],
		[ "list-style-type", "Specifies the marker style for a list-item.", 1, true, "" ],
		[ "margin", "Sets the margin on all four sides of the element.", 1, true, "" ],
		[ "margin-bottom", "Sets the bottom margin of the element.", 1, true, "" ],
		[ "margin-left", "Sets the left margin of the element.", 1, true, "" ],
		[ "margin-right", "Sets the right margin of the element.", 1, true, "" ],
		[ "margin-top", "Sets the top margin of the element.", 1, true, "" ],
		[ "max-height", "Specify the maximum height of an element.", 1, true, "" ],
		[ "max-width", "Specify the maximum width of an element.", 1, true, "" ],
		[ "min-height", "Specify the minimum height of an element.", 1, true, "" ],
		[ "min-width", "Specify the minimum width of an element.", 1, true, "" ],
		[ "NAME", "OBJECT element NAME attribute", 4, false, "APPLET" ],
		[ "NOSHADE", "", 4, false, "HR" ],
		[ "NOWRAP", "white-space style attribute", 4, false, "TD, TH" ],
		[ "OBJECT", "OBJECT element CLASSID attribute", 4, false, "APPLET" ],
		[ "opacity", "Specifies the transparency of an element.", 1, true, "" ],
		[ "order",
			"Specifies the order in which a flex items are displayed and laid out " .
			"within a flex container.", 1, true, "" ],
		[ "outline", "Sets the width, style, and color for all four sides of an element's outline.", 1, true, "" ],
		[ "outline-color", "Sets the color of the outline.", 1, true, "" ],
		[ "outline-offset", "Set the space between an outline and the border edge of an element.", 1, true, "" ],
		[ "outline-style", "Sets a style for an outline.", 1, true, "" ],
		[ "outline-width", "Sets the width of the outline.", 1, true, "" ],
		[ "overflow", "Specifies the treatment of content that overflows the element's box.", 1, true, "" ],
		[ "overflow-x", "Specifies the treatment of content that overflows the " .
			"element's box horizontally.", 1, true, "" ],
		[ "overflow-y", "Specifies the treatment of content that overflows the " .
			"element's box vertically.", 1, true, "" ],
		[ "padding", "Sets the padding on all four sides of the element.", 1, true, "" ],
		[ "padding-bottom", "Sets the padding to the bottom side of an element.", 1, true, "" ],
		[ "padding-left", "Sets the padding to the left side of an element.", 1, true, "" ],
		[ "padding-right", "Sets the padding to the right side of an element.", 1, true, "" ],
		[ "padding-top", "Sets the padding to the top side of an element.", 1, true, "" ],
		[ "page-break-after", "Insert a page breaks after an element.", 1, true, "" ],
		[ "page-break-before", "Insert a page breaks before an element.", 1, true, "" ],
		[ "page-break-inside", "Insert a page breaks inside an element.", 1, true, "" ],
		[ "perspective", "Defines the perspective from which all child elements of the " .
			"object are viewed.", 1, true, "" ],
		[ "perspective-origin",
			"Defines the origin (the vanishing point for the 3D space) for the perspective property.", 1, true, "" ],
		[ "position", "Specifies how an element is positioned.", 1, true, "" ],
		[ "PROMPT", "LABEL element", 4, false, "ISINDEX" ],
		[ "quotes", "Specifies quotation marks for embedded quotations.", 1, true, "" ],
		[ "resize", "Specifies whether or not an element is resizable by the user.", 1, true, "" ],
		[ "right", "Specify the location of the right edge of the positioned element.", 1, true, "" ],
		[ "SIZE", "width positioning style attribute", 4, false, "HR" ],
		[ "SIZE", "font-size style attrib ute", 4, false, "FONT, BASEFONT" ],
		[ "START", "To be determined in CSS2", 4, false, "OL" ],
		[ "tab-size", "Specifies the length of the tab character.", 1, true, "" ],
		[ "table-layout", "Specifies a table layout algorithm.", 1, true, "" ],
		[ "TEXT", "color style attribute", 4, false, "BODY" ],
		[ "text-align", "Sets the horizontal alignment of inline content.", 1, true, "" ],
		[ "text-align-last",
			"Specifies how the last line of a block or a line right before a " .
			"forced line break is aligned when text-align is justify.", 1, true, "" ],
		[ "text-decoration", "Specifies the decoration added to text.", 1, true, "" ],
		[ "text-decoration-color", "Specifies the color of the text-decoration-line.", 1, true, "" ],
		[ "text-decoration-line", "Specifies what kind of line decorations are added to the element.", 1, true, "" ],
		[ "text-decoration-style",
			"Specifies the style of the lines specified by the text-decoration-line property", 1, true, "" ],
		[ "text-indent", "Indent the first line of text.", 1, true, "" ],
		[ "text-justify", "Specifies the justification method to use when the " .
			"text-align property is set to justify.", 1, true, "" ],
		[ "text-overflow",
			"Specifies how the text content will be displayed, when it overflows the block containers.", 1, true, "" ],
		[ "text-shadow", "Applies one or more shadows to the text content of an element.", 1, true, "" ],
		[ "text-transform", "Transforms the case of the text.", 1, true, "" ],
		[ "top", "Specify the location of the top edge of the positioned element.", 1, true, "" ],
		[ "transform", "Applies a 2D or 3D transformation to an element.", 1, true, "" ],
		[ "transform-origin", "Defines the origin of transformation for an element.", 1, true, "" ],
		[ "transform-style", "Specifies how nested elements are rendered in 3D space.", 1, true, "" ],
		[ "transition", "Defines the transition between two states of an element.", 1, true, "" ],
		[ "transition-delay", "Specifies when the transition effect will start.", 1, true, "" ],
		[ "transition-duration",
			"Specifies the number of seconds or milliseconds a transition effect should take to complete.", 1, true, "" ],
		[ "transition-property",
			"Specifies the names of the CSS properties to which a transition effect should be applied.", 1, true, "" ],
		[ "transition-timing-function", "Specifies the speed curve of the transition effect.", 1, true, "" ],
		[ "TYPE", "list-style-type style attribute", 4, false, "LI, OL, UL" ],
		[ "VALUE", "To be determined in CSS2", 4, false, "LI" ],
		[ "VERSION", "Built into the DTD for HTML 4.0", 4, false, "HTML" ],
		[ "vertical-align", "Sets the vertical positioning of an element relative to the " .
			"current text baseline.", 1, true, "" ],
		[ "visibility", "Specifies whether or not an element is visible.", 1, true, "" ],
		[ "VLINK", "A:visited {color: }", 4, false, "BODY" ],
		[ "VSPACE", "top positioning style attribute", 4, false, "IMG, OBJECT" ],
		[ "white-space", "Specifies how white space inside the element is handled.", 1, true, "" ],
		[ "width", "Specify the width of an element.", 1, true, "" ],
		[ "WIDTH", "width positioning style attribute", 4, false, "HR" ],
		[ "WIDTH", "OBJECT element WIDTH attribute", 4, false, "APPLET" ],
		[ "WIDTH", "COLGROUP element WIDTH attribute", 4, false, "TD, TH" ],
		[ "word-break", "Specifies how to break lines within words.", 1, true, "" ],
		[ "word-spacing", "Sets the spacing between words.", 1, true, "" ],
		[ "word-wrap",
			"Specifies whether to break words when the content overflows the " .
			"boundaries of its container.", 1, true, "" ],
		[ "z-index", "Specifies a layering or stacking order for positioned elements.", 1, true, "" ]
		];
#
#	Taken from : https://www.tutorialrepublic.com/css-reference/css3-properties.php
#
	$css_props = [
#		["Property", "Description" ]
		[ "align-content ", "Specifies the alignment of flexible container's items within the flex container." ],
		[ "align-items ", "Specifies the default alignment for items within the flex container." ],
		[ "align-self ", "Specifies the alignment for selected items within the flex container." ],
		[ "animation ", "Specifies the keyframe-based animations." ],
		[ "animation-delay ", "Specifies when the animation will start." ],
		[ "animation-direction ", "Specifies whether the animation should play in " .
			"reverse on alternate cycles or not." ],
		[ "animation-duration ", "Specifies the number of seconds or milliseconds " .
			"an animation should take to complete one cycle." ],
		[ "animation-fill-mode ", "Specifies how a CSS animation should apply styles " .
			"to its target before and after it is executing." ],
		[ "animation-iteration-count ", "Specifies the number of times an animation " .
			"cycle should be played before stopping." ],
		[ "animation-name ", "Specifies the name of @keyframes defined animations " .
			"that should be applied to the selected element." ],
		[ "animation-play-state ", "Specifies whether the animation is running or paused." ],
		[ "animation-timing-function ", "Specifies how a CSS animation should progress over the duration of each cycle." ],
		[ "backface-visibility ", "Specifies whether or not the 'back' side of a transformed " .
			"element is visible when facing the user." ],
		[ "background", "Defines a variety of background properties within one declaration." ],
		[ "background-attachment", "Specify whether the background image is fixed in the viewport or scrolls." ],
		[ "background-clip ", "Specifies the painting area of the background." ],
		[ "background-color", "Defines an element's background color." ],
		[ "background-image", "Defines an element's background image." ],
		[ "background-origin ", "Specifies the positioning area of the background images." ],
		[ "background-position", "Defines the origin of a background image." ],
		[ "background-repeat", "Specify whether/how the background image is tiled." ],
		[ "background-size ", "Specifies the size of the background images." ],
		[ "border", "Sets the width, style, and color for all four sides of an element's border." ],
		[ "border-bottom", "Sets the width, style, and color of the bottom border of an element." ],
		[ "border-bottom-color", "Sets the color of the bottom border of an element." ],
		[ "border-bottom-left-radius ", "Defines the shape of the bottom-left border corner of an element." ],
		[ "border-bottom-right-radius ", "Defines the shape of the bottom-right border corner of an element." ],
		[ "border-bottom-style", "Sets the style of the bottom border of an element." ],
		[ "border-bottom-width", "Sets the width of the bottom border of an element." ],
		[ "border-collapse", "Specifies whether table cell borders are connected or separated." ],
		[ "border-color", "Sets the color of the border on all the four sides of an element." ],
		[ "border-image ", "Specifies how an image is to be used in place of the border styles." ],
		[ "border-image-outset ", "Specifies the amount by which the border image area extends beyond the border box." ],
		[ "border-image-repeat ", "Specifies whether the image-border should be repeated, rounded or stretched." ],
		[ "border-image-slice ", "Specifies the inward offsets of the image-border." ],
		[ "border-image-source ", "Specifies the location of the image to be used as a border." ],
		[ "border-image-width ", "Specifies the width of the image-border." ],
		[ "border-left", "Sets the width, style, and color of the left border of an element." ],
		[ "border-left-color", "Sets the color of the left border of an element." ],
		[ "border-left-style", "Sets the style of the left border of an element." ],
		[ "border-left-width", "Sets the width of the left border of an element." ],
		[ "border-radius ", "Defines the shape of the border corners of an element." ],
		[ "border-right", "Sets the width, style, and color of the right border of an element." ],
		[ "border-right-color", "Sets the color of the right border of an element." ],
		[ "border-right-style", "Sets the style of the right border of an element." ],
		[ "border-right-width", "Sets the width of the right border of an element." ],
		[ "border-spacing", "Sets the spacing between the borders of adjacent table cells." ],
		[ "border-style", "Sets the style of the border on all the four sides of an element." ],
		[ "border-top", "Sets the width, style, and color of the top border of an element." ],
		[ "border-top-color", "Sets the color of the top border of an element." ],
		[ "border-top-left-radius ", "Defines the shape of the top-left border corner of an element." ],
		[ "border-top-right-radius ", "Defines the shape of the top-right border corner of an element." ],
		[ "border-top-style", "Sets the style of the top border of an element." ],
		[ "border-top-width", "Sets the width of the top border of an element." ],
		[ "border-width", "Sets the width of the border on all the four sides of an element." ],
		[ "bottom", "Specify the location of the bottom edge of the positioned element." ],
		[ "box-shadow ", "Applies one or more drop-shadows to the element's box." ],
		[ "box-sizing ", "Alter the default CSS box model." ],
		[ "caption-side", "Specify the position of table's caption." ],
		[ "clear", "Specifies the placement of an element in relation to floating elements." ],
		[ "clip", "Defines the clipping region." ],
		[ "color", "Specify the color of the text of an element." ],
		[ "column-count ", "Specifies the number of columns in a multi-column element." ],
		[ "column-fill ", "Specifies how columns will be filled." ],
		[ "column-gap ", "Specifies the gap between the columns in a multi-column element." ],
		[ "column-rule ", "Specifies a straight line, or 'rule', to be drawn between each " .
			"column in a multi-column element." ],
		[ "column-rule-color ", "Specifies the color of the rules drawn between columns in a multi-column layout." ],
		[ "column-rule-style ", "Specifies the style of the rule drawn between the columns in a multi-column layout." ],
		[ "column-rule-width ", "Specifies the width of the rule drawn between the columns in a multi-column layout." ],
		[ "column-span ", "Specifies how many columns an element spans across in a multi-column layout." ],
		[ "column-width ", "Specifies the optimal width of the columns in a multi-column element." ],
		[ "columns ", "A shorthand property for setting column-width and column-count properties." ],
		[ "content", "Inserts generated content." ],
		[ "counter-increment", "Increments one or more counter values." ],
		[ "counter-reset", "Creates or resets one or more counters." ],
		[ "cursor", "Specify the type of cursor." ],
		[ "direction", "Define the text direction/writing direction." ],
		[ "display", "Specifies how an element is displayed onscreen." ],
		[ "empty-cells", "Show or hide borders and backgrounds of empty table cells." ],
		[ "flex ", "Specifies the components of a flexible length." ],
		[ "flex-basis ", "Specifies the initial main size of the flex item." ],
		[ "flex-direction ", "Specifies the direction of the flexible items." ],
		[ "flex-flow ", "A shorthand property for the flex-direction and the flex-wrap properties." ],
		[ "flex-grow ", "Specifies how the flex item will grow relative to the other " .
			"items inside the flex container." ],
		[ "flex-shrink ", "Specifies how the flex item will shrink relative to the other " .
			"items inside the flex container." ],
		[ "flex-wrap ", "Specifies whether the flexible items should wrap or not." ],
		[ "float", "Specifies whether or not a box should float." ],
		[ "font", "Defines a variety of font properties within one declaration." ],
		[ "font-family", "Defines a list of fonts for element." ],
		[ "font-size", "Defines the font size for the text." ],
		[ "font-size-adjust ", "Preserves the readability of text when font fallback occurs." ],
		[ "font-stretch ", "Selects a normal, condensed, or expanded face from a font." ],
		[ "font-style", "Defines the font style for the text." ],
		[ "font-variant", "Specify the font variant." ],
		[ "font-weight", "Specify the font weight of the text." ],
		[ "height", "Specify the height of an element." ],
		[ "justify-content ", "Specifies how flex items are aligned along the main axis " .
			"of the flex container after any flexible lengths and auto margins have been resolved." ],
		[ "left", "Specify the location of the left edge of the positioned element." ],
		[ "letter-spacing", "Sets the extra spacing between letters." ],
		[ "line-height", "Sets the height between lines of text." ],
		[ "list-style", "Defines the display style for a list and list elements." ],
		[ "list-style-image", "Specifies the image to be used as a list-item marker." ],
		[ "list-style-position", "Specifies the position of the list-item marker." ],
		[ "list-style-type", "Specifies the marker style for a list-item." ],
		[ "margin", "Sets the margin on all four sides of the element." ],
		[ "margin-bottom", "Sets the bottom margin of the element." ],
		[ "margin-left", "Sets the left margin of the element." ],
		[ "margin-right", "Sets the right margin of the element." ],
		[ "margin-top", "Sets the top margin of the element." ],
		[ "max-height", "Specify the maximum height of an element." ],
		[ "max-width", "Specify the maximum width of an element." ],
		[ "min-height", "Specify the minimum height of an element." ],
		[ "min-width", "Specify the minimum width of an element." ],
		[ "opacity ", "Specifies the transparency of an element." ],
		[ "order ", "Specifies the order in which a flex items are displayed " .
			"and laid out within a flex container." ],
		[ "outline", "Sets the width, style, and color for all four sides of an element's outline." ],
		[ "outline-color", "Sets the color of the outline." ],
		[ "outline-offset ", "Set the space between an outline and the border edge of an element." ],
		[ "outline-style", "Sets a style for an outline." ],
		[ "outline-width", "Sets the width of the outline." ],
		[ "overflow", "Specifies the treatment of content that overflows the element's box." ],
		[ "overflow-x ", "Specifies the treatment of content that overflows the element's box horizontally." ],
		[ "overflow-y ", "Specifies the treatment of content that overflows the element's box vertically." ],
		[ "padding", "Sets the padding on all four sides of the element." ],
		[ "padding-bottom", "Sets the padding to the bottom side of an element." ],
		[ "padding-left", "Sets the padding to the left side of an element." ],
		[ "padding-right", "Sets the padding to the right side of an element." ],
		[ "padding-top", "Sets the padding to the top side of an element." ],
		[ "page-break-after", "Insert a page breaks after an element." ],
		[ "page-break-before", "Insert a page breaks before an element." ],
		[ "page-break-inside", "Insert a page breaks inside an element." ],
		[ "perspective ", "Defines the perspective from which all child elements of the object are viewed." ],
		[ "perspective-origin ", "Defines the origin (the vanishing point for " .
			"the 3D space) for the perspective property." ],
		[ "position", "Specifies how an element is positioned." ],
		[ "quotes", "Specifies quotation marks for embedded quotations." ],
		[ "resize ", "Specifies whether or not an element is resizable by the user." ],
		[ "right", "Specify the location of the right edge of the positioned element." ],
		[ "tab-size ", "Specifies the length of the tab character." ],
		[ "table-layout", "Specifies a table layout algorithm." ],
		[ "text-align", "Sets the horizontal alignment of inline content." ],
		[ "text-align-last ", "Specifies how the last line of a block or a line " .
			"right before a forced line break is aligned when text-align is justify." ],
		[ "text-decoration", "Specifies the decoration added to text." ],
		[ "text-decoration-color ", "Specifies the color of the text-decoration-line." ],
		[ "text-decoration-line ", "Specifies what kind of line decorations are added to the element." ],
		[ "text-decoration-style ", "Specifies the style of the lines specified " .
			"by the text-decoration-line property" ],
		[ "text-indent", "Indent the first line of text." ],
		[ "text-justify ", "Specifies the justification method to use when the " .
			"text-align property is set to justify." ],
		[ "text-overflow ", "Specifies how the text content will be displayed, " .
			"when it overflows the block containers." ],
		[ "text-shadow ", "Applies one or more shadows to the text content of an element." ],
		[ "text-transform", "Transforms the case of the text." ],
		[ "top", "Specify the location of the top edge of the positioned element." ],
		[ "transform ", "Applies a 2D or 3D transformation to an element." ],
		[ "transform-origin ", "Defines the origin of transformation for an element." ],
		[ "transform-style ", "Specifies how nested elements are rendered in 3D space." ],
		[ "transition ", "Defines the transition between two states of an element." ],
		[ "transition-delay ", "Specifies when the transition effect will start." ],
		[ "transition-duration ", "Specifies the number of seconds or milliseconds a " .
			"transition effect should take to complete." ],
		[ "transition-property ", "Specifies the names of the CSS properties to which a " .
			"transition effect should be applied." ],
		[ "transition-timing-function ", "Specifies the speed curve of the transition effect." ],
		[ "vertical-align", "Sets the vertical positioning of an element relative to the current text baseline." ],
		[ "visibility", "Specifies whether or not an element is visible." ],
		[ "white-space", "Specifies how white space inside the element is handled." ],
		[ "width", "Specify the width of an element." ],
		[ "word-break ", "Specifies how to break lines within words." ],
		[ "word-spacing", "Sets the spacing between words." ],
		[ "word-wrap ", "Specifies whether to break words when the content overflows the boundaries of its container." ],
		[ "z-index", "Specifies a layering or stacking order for positioned elements." ],
		];

	$css_atrules = [
#		[ "Rule", "Description" ]
		[ "@charset", "Specifies the character encoding of an external style sheet." ],
		[ "@font-face", "Enables the use of downloadable web fonts." ],
		[ "@import", "Imports an external style sheet." ],
		[ "@keyframes", "Specifies the values for the animating properties at various points during the animation." ],
		[ "@media", "Sets the media types for a set of rules in a style sheet." ],
		[ "@page", "Defines the dimensions, orientation, and margins of a page box for printing environments." ],
		];
#
#	Taken from : https://www.tutorialrepublic.com/css-reference/css-aural-properties.php
#
	$css_aural = [
#		[ "Property", "Values", "Description" ],
		[ "azimuth", "angle, left-side, far-left, left, center-left, center, center-right, " .
			"right, far-right, right-side, behind, leftwards, rightwards, inherit",
			"Sets where the sound should come from horizontally." ],
		[ "cue", "cue-before, cue-after, inherit",
			"Shorthand for setting the cue properties (i.e. cue-before and cue-after) in one declaration." ],
		[ "cue-after", "none, url, inherit",
			"Specifies a sound to be played after speaking an element's content to delimit it from other." ],
		[ "cue-before", "none, url, inherit",
			"Specifies a sound to be played before speaking an element's content to delimit it from other." ],
		[ "elevation", "angle, below, level, above, higher, lower, inherit",
			"Sets where the sound should come from vertically." ],
		[ "pause", "pause-before, pause-after, inherit",
			"Shorthand for setting the pause properties (i.e. pause-before and pause-after) in one declaration." ],
		[ "pause-after", "time, %, inherit", "Specify a pause to be observed after speaking an element's content." ],
		[ "pause-before", "time, %, inherit", "Specify a pause to be observed before speaking an element's content." ],
		[ "pitch", "frequency, x-low, low, medium, high, x-high, inherit",
			"Specifies the average pitch (a frequency) of the speaking voice. " .
			"The average pitch of a voice depends on the voice family." ],
		[ "pitch-range", "number, inherit", "Specifies variation in average pitch." ],
		[ "play-during", "auto, none, url, mix, repeat, inherit",
			"Specifies a sound to be played as a background while an element's content is spoken." ],
		[ "richness", "number, inherit", "Specifies the richness of the speaking voice." ],
		[ "speak", "normal, none, spell-out, inherit",
			"Specifies whether text will be rendered aurally and if so, in what manner." ],
		[ "speak-header", "always, once, inherit",
			"Specifies whether table headers are spoken before every cell, or only before " .
			"a cell when that cell is associated with a different header than the previous cell." ],
		[ "speak-numeral", "digits, continuous, inherit", "Specifies how numerals are spoken." ],
		[ "speak-punctuation", "none, code, inherit", "Specifies how punctuation characters are spoken." ],
		[ "speech-rate", "number, x-slow, slow, medium, fast, x-fast, faster, slower, inherit",
			"Specifies the speaking rate i.e. number of words spoken per minute." ],
		[ "Stress", "number, inherit", "Specifies the .stress. in the speaking voice." ],
		[ "voice-family", "specific-voice, generic-voice, inherit",
			"Specifies a comma-separated, prioritized list of voice family names." ],
		[ "volume", "number, %, silent, x-soft, soft, medium, loud, x-loud, inherit",
			"Specifies the volume of the speaking voice." ]
		];
#
#	Taken from : https://www.tutorialrepublic.com/css-tutorial/css-units.php
#
	$css_units = [
#		[ "Unit", "Description", "Type:P)rint,W)eb" ],
		[ "em", "the current font-size", "pw" ],
		[ "ex", "the x-height of the current font", "pw" ],
		[ "in", "inches – 1in is equal to 2.54cm.", "pw" ],
		[ "cm", "centimeters.", "p" ],
		[ "mm", "millimeters.", "p" ],
		[ "pt", "points – In CSS, one point is defined as 1/72 inch (0.353mm).", "pw" ],
		[ "pc", "picas – 1pc is equal to 12pt.", "pw" ],
		[ "px", "pixel units – 1px is equal to 0.75pt.", "pw" ]
		];
#
#	Taken from : https://www.tutorialrepublic.com/css-reference/css-web-safe-fonts.php
#
#	NOTES	: What you DO is to put the font NAME first or earlier in order to use THAT
#		font. Later fonts (such as sans-serif) means they are the LAST choice (or later
#		choice in case the other ones fail).
#
	$fonts_safe = [
#		[ "font-family", "Type" ],
		[ "Arial, Helvetica, 'Arial Black', Gadget, Impact, Charcoal, " .
			"Tahoma, Geneva, 'Trebuchet MS', Helvetica, Verdana, sans-serif", "sans-serif" ],
		[ "'Times New Roman', Times, Georgia, 'Palatino Linotype', Palatino, 'Book Antiqua', serif", "serif" ],
		[ "'Courier New', Courier, 'Lucida Console', Monaco, monospace", "monospace" ],
		];

	$this->html_cmds = $html_cmds;
	$this->html_global_attributes = $html_global_attributes;
	$this->html_window_events = $html_window_events;
	$this->html_form_events = $html_form_events;
	$this->html_mouse_events = $html_mouse_events;
	$this->html_keyboard_events = $html_keyboard_events;
	$this->html_clipboard_events = $html_clipboard_events;
	$this->html_media_events = $html_media_events;
	$this->css_cmds = $css_cmds;
	$this->css_props = $css_props;
	$this->css_atrules = $css_atrules;
	$this->css_aural = $css_aural;
	$this->css_units = $css_units;
	$this->fonts_safe = $fonts_safe;
}
################################################################################
#	__get(). Gets one of the above arrays and returns it to the calling program.
################################################################################
function __get( $name )
{
	if( isset(${$name}) ){
		return ${$name};
		}

	return false;
}
################################################################################
#	list(). Used to get the list of arrays of information.
################################################################################
function list()
{
	$a = [];
	$a[] = "html_cmds";
	$a[] = "html_global_attributes";
	$a[] = "html_window_events";
	$a[] = "html_form_events";
	$a[] = "html_mouse_events";
	$a[] = "html_keyboard_events";
	$a[] = "html_clipboard_events";
	$a[] = "html_media_events";
	$a[] = "css_cmds";
	$a[] = "css_props";
	$a[] = "css_atrules";
	$a[] = "css_aural";
	$a[] = "css_units";
	$a[] = "fonts_safe";

	return $a;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['htmlref']) ){
		$GLOBALS['classes']['htmlref'] = new class_htmlref();
		}

?>

