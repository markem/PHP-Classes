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
#	xml_json();
#
#-Description:
#
#	A class to handle not only the JSON stuff but to also change keys into hex
#	values. It is VERY IMPORTANT to convert the keys also.
#
#	Adding in XML stuff also. Renaming this to xml_json.php for json and xml.
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
#	Mark Manning			Simulacron I			Mon 05/27/2019 19:03:25.71 
#		Original Program.
#
#
#END DOC
################################################################################
class xml_json
{
	public $xml_exception = null;
	public $xml_dir = null;
	public $xml_fname = null;
	public $fp = null;

################################################################################
#	__construct(). Make the class.
################################################################################
function __construct()
{
	if( !isset($GLOBALS['class']['gd']) ){
		return $this->init( func_get_args() );
		}
		else { return $GLOBALS['class']['gd']; }
}
################################################################################
#	init(). A function to start the entire thing over again.
################################################################################
function init()
{
	static $newInstance = 0;

	if( $newInstance++ > 1 ){ return; }

	$args = func_get_args();
	while( is_array($args) && (count($args) > 2) ){
		$args = array_pop( $args );
		}

	$this->cf = $this->get_class( 'files' );

	return true;
}
################################################################################
#	getJSON().  A function to get all of the HTML JSON code.
#	Notes: Old JSON function to get any JSON information coming in from the
#		browser.
################################################################################
function getJSON( $json='json' )
{
#
#	grab JSON data if there...
#
	$params = null;
	if( isset($_REQUEST[$json]) ){
		$params =  json_decode( stripslashes($_REQUEST[$json]) );
		}
		else {
#
#	<-- Have to jump through hoops to get PUT data $raw  = '';
#
			$raw = "";
			$httpContent = fopen( 'php://input', 'r' );
			while( $kb = fread($httpContent, 1024) ){ $raw .= $kb; }
			fclose( $httpContent );

			$p = array();
			parse_str( $raw, $p );

			if( isset($p['data']) ){ $params =  json_decode( stripslashes($p['data']) ); }
				else {
					$p = json_decode( stripslashes($raw) );
					if( $p ){
						if( isset($p->data) ){ $params = $p->data; }
							else { $params = $p; }
						}
					}
			}

	if( is_null($params) ){ $params = array(); }

	return $params;
}
################################################################################
#	hex_json(). Convert an array to a hex'd json string.
################################################################################
function hex_json( $array )
{
	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = hex_json($v); }
			else {
				$hex = bin2hex( $v );
				$hex = (((strlen($hex) % 2) < 1) ? "" : "0") . $hex;
				$array[$k] = "0x" . $hex;
				}
		}

	return json_encode( $array );
}
################################################################################
#	unhex_json(). Convert a hex'd json to a regular array.
################################################################################
function unhex_json( $json )
{
	static $first = true;
	if( $first ){ $array = json_decode( $json ); $first = false; }

	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = unhex_json($v); }
			else { $array[$k] = hex2bin( substr($v, 2, strlen($v)) ); }
		}

	return $array;
}
################################################################################
#	key_encode(). Convert all keys to their hex value.
#	Notes: CALL THIS FUNCTION >>>FIRST<<< so the keys are converted.
#	Notes: You can NOT just array_flip and change the keys because
#		some array entries might be an array and you can NOT have an array
#		be a key.
################################################################################
function key_encode( $array )
{
	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = key_encode( $v ); }
		$hex = hin2hex( $k );
		$hex = (((strlen($hex) % 2) < 1) ? "" : "0") . $hex;
		$array[$hex] = $v;
		unset( $array[$k] );
		}

	return $array;
}
################################################################################
#	key_decode(). Convert all keys back to their actual value.
#	Notes: CALL THIS FUNCTION >>>FIRST<<< Before unconverting the array.
#	Notes: You can NOT just array_flip and change the keys because
#		some array entries might be an array and you can NOT have an array
#		be a key.
################################################################################
function key_decode( $array )
{
	foreach( $array as $k=>$v ){
		if( is_array($v) ){ $array[$k] = key_decode( $v ); }
		$bin = hex2bin( substr($k,2,strlen($k)) );
		$array[$bin] = $v;
		unset( $array[$k] );
		}

	return $array;
}
################################################################################
#	is_base64(). Checks to see if the string is a base64 string.
#	Notes: Taken from the PHP documentation and @morgangalpin att gmail dotty com
#		Made only two changes: 1)Sending the data in, and 2)If not true ALWAYS
#			send back FALSE;
################################################################################
function is_base64( $data=null )
{
	if( preg_match(";^[a-zA-Z0-9/+]*=[0,2]$;", $data) ){ return true; }

	return false;
}
################################################################################
#	fget_xml(). Get an xml document BUT DO NOT DO ANYTHING WITH IT!
################################################################################
function fget_xml( $file=null )
{
	if( !file_exists($file) ){ die( "DIE : No such file - $file\n" ); }

	try{ $xml = new SimpleXmlIterator( $file, null, true ); }
	catch( exception $e ){
		$this->xml_exception = $e;
		return false;
		}

	$this->xml_exception = null;

    return $xml;
}
################################################################################
#	fput_xml(). Take an array and save it as an xml file.
################################################################################
function fput_xml( $file=null, $array=null )
{
	if( is_null($file) ){ return false; }
	if( is_null($array) ){ die( "DIE : Array is NULL\n" ); }

	$xml = $this->xml_encode( "<root>", $array );

	$fp = fopen( $file, "w" );
	fwrite( $fp, $xml );
	fclose( $fp );

	return true;
}
################################################################################
#	xml_decode(). Changes an XML string to an array.
################################################################################
function xml_decode( $xml=null, $opt=null )
{
	if( is_null($xml) ){ return false; }
	if( is_null($opt) ){ $opt = false; }

    static $c = 0;

    $a = [];
    for( $xml->rewind(); $xml->valid(); $xml->next() ){
        if( !array_key_exists($xml->key(), $a) ){
            $a[$xml->key()] = [];
            }

        if( $xml->hasChildren() ){
            $a[$xml->key()][] = $this->xml_decode( $xml->current() );
            }
            else {
				$var = $xml->current();
				if( $opt && $this->is_base64($var) ){
					$var = base64_decode( $var );
					}
					else {
						$var = $xml->current();
						$v1 = @convert_uudecode( $var );
						if( $v1 === false ){ echo "Not uuencoded\n"; }
						$v2 = @gzdecode( $var );
						if( $v2 === false ){ echo "Not gzip encoded\n"; }
						$v3 = @gzuncompress( $var );
						if( $v3 === false ){ echo "Not gzcompressed\n"; }
						$v4 = @gzinflate( $var );
						if( $v4 === false ){ echo "Not gzip deflated\n"; }
						$v5 = @zlib_decode( $var );
						if( $v5 === false ){ echo "Not zlib encoded\n"; }

						$key = "QJZX-D6WT-QFLW-PDMC-JILW";
						$var = $this->ssl_decrypt( $var, $key );
						}

				$a[$xml->key()][] = $var;
                }
        }

    return $a;
}
################################################################################
#	xml_encode(). Encode an array to an XML setup.
#	Notes:	Taken from https://gist.github.com/randomekek/4677686
#		...and modified. For unknown reasons the '<' and '>' are acting
#		weird. So I replaced them with two variables. $lt and $gt.
################################################################################
function xml_encode( $root=null, $array=null )
{
	if( is_null($root) ){ return false; }
	if( is_null($array) ){ die( "DIE : Array is NULL\n" ); }

	$lt = '<';
	$gt = '>';
	$s = "$lt?xml version='1.0' encoding='utf-8'?$gt$lt$root/$gt";

	$xml = $this->xml_add_children(new SimpleXMLElement($s), $array)->asXML();

	return $xml;
}
################################################################################
#	xml_add_children(). Adds another child to the list.
################################################################################
function xml_add_children( $xml=null, $array=null )
{
	if( is_null($xml) ){ return false; }
	if( is_null($array) ){ die( "DIE : Array is NULL\n" ); }

    foreach( $array as $key => $value ){
        if( $key[0] == '@' ){
            $xml->addAttribute( substr($key, 1), $value );
			}
			else if( !is_array($value) && !is_object($value) ){
				$xml->addChild( $key, htmlspecialchars($value) );
				}
			else {
				$this->xml_add_children( $xml->addChild($key), $value );
				}
		}

    return $xml;
}
################################################################################
#	fsave_images(). Convert images to files
#	Notes: If you DO NOT send a directory - this will make the directory
#		called "IMAGES". You have been warned!
#		ALSO!  Remember you are sending an ARRAY. Not an XML object. Do the
#		transfer FIRST and then call this function.
#		fname = Only the first part of a filename (like "image")
################################################################################
function fsave_images( $array=null, $dir=null, $fname=null )
{
	if( is_null($dir) ){ $dir = "./images"; }
	if( !is_dir($dir) ){  mkdir( $dir ); }
	if( is_null($fname) ){ return false; }
	if( is_null($array) ){ return false; }

	$this->xml_dir = $dir;
	$this->xml_fname = $fname;

	return $this->fsave_images_child( $array );
}
################################################################################
#	fsave_images_child(). Work on children in an array to make images.
################################################################################
function fsave_images_child( $array=null )
{
	static $c = 0;
	$dir = $this->xml_dir;
	$fname = $this->xml_fname;

	if( is_null($array) ){ return false; }

	foreach( $array as $k=>$v ){
		if( is_array($v) ){
			$this->fsave_images_child( $v );
			}
			elseif( $this->is_base64($v) || preg_match("/aaaa/i", $v) ){
				$image = base64_decode( $v );
				$ext = $this->get_ftype( $image );

				$file = "$dir/$fname-$c.$ext";
				$fp = fopen( $file, "w" );
				fwrite( $fp, $image );
				fclose( $fp );
				$c++;
				}
		}

	return true;
}
################################################################################
#	get_ftype(). Get which type of file I am looking at.
#	Notes: Duplicate of the function in class_files.php.
################################################################################
function get_ftype( $image )
{
#	First, we need to get the first 1024 bytes from the file.
#	Nothing else is needed at this point.
#
	$r = substr( $image, 0, 1024 );
#
#	png file format header
#
	$id = preg_match( "/png/i", substr($r, 1, 3) );
	if( $id ){ return "png"; }
#
#	gif file format header
#
	$id = preg_match( "/gif/i", substr($r, 0, 3) );
	if( $id ){ return "gif"; }
#
#	BMP file format header
#
	$id = preg_match( "/(bm|ba|ci|cp|ic|pt)/i", substr($r, 0, 2) );
	if( $id ){ return "bmp"; }
#
#	jpg file format header
#
	$id = preg_match( "/(exif|jfif|jfi|jpg|jpeg)/i", substr($r, 6, 4) );
	if( $id ){ return "jpg"; }
#
#	tiff file format header
#
	$tiff1 = "49492a00";
	$tiff2 = "4040002a";
	$hex = str_pad( bin2hex(substr($r,0,4)), 8, '0', STR_PAD_LEFT );
	$id = preg_match( "/($tiff1|$tiff2)/i", $hex );
	if( $id ){ return "tif"; }
#
#	Webp file format header
#
	$id = ( preg_match("/riff/i", substr($r, 0, 4)) || preg_match("/webp/i", substr($r, 8, 4)) );
	if( $id ){ return "webp"; }
#
#	psd file format header
#
	$id = preg_match( "/8bps/i", substr($r, 0, 4) );
	if( $id ){ return "psd"; }
#
#	Computer Eyes file format header
#
	$id = preg_match( "/eyes/i", substr($r, 0, 4) );
	if( $id ){ return "flm"; }

	$id = preg_match( "/(fedbh|fedch)/i", substr($r, 0, 5) );
	if( $id ){ return "seq"; }
#
#	Imagic Film Picture file format header
#
	$id = preg_match( "/imdc/i", substr($r, 0, 4) );
	if( $id ){ return "flm"; }
#
#	STAD file format header
#
	$id = preg_match( "/(pm86|pm85)/i", substr($r, 0, 4) );
	if( $id ){ return "stad"; }
#
#	AuotCAD DXF file format header
#
	$hs = chr( 0x0d ) . chr( 0x0a ) . chr( 0x1a ) . chr( 0x00 );
	$len = strlen( "/autocad binary dxf/i" ) + 4;
	$id = @preg_match( "/autocad binary dxf$hs/i", substr($r, 0, $len) );
	if( $id ){ return "dxf"; }
#
#	AuotCAD DXB file format header
#
	$hs = chr( 0x0d ) . chr( 0x0a ) . chr( 0x1a ) . chr( 0x00 );
	$len = strlen( "/AutoCAD DXB 1.0/i" ) + 4;
	$id = @preg_match( "/AutoCAD DXB 1.0$hs/i", substr($r, 0, $len) );
	if( $id ){ return "dxf"; }
#
#	BDF file format header
#
	$id = preg_match( "/startfont/i", substr($r, 0, 9) );
	if( $id ){ return "stad"; }
#
#	DPX file format header
#
	$id = preg_match( "/(sdpx|xpds)/i", substr($r, 0, 4) );
	if( $id ){ return "dpx"; }
#
#	Dr. Halo PAL file format header
#
	$id = preg_match( "/ah/i", substr($r, 0, 2) );
	if( $id ){ return "pal"; }
#
#	DVM file format header
#
	$id = preg_match( "/dvm/i", substr($r, 0, 3) );
	if( $id ){ return "dvm"; }
#
#	EPS v2.0 file format header
#
	$str = "%!PS-Adobe-2.0 EPSF-1.2";
	$len = strlen( $str );
	$id = preg_match( "/$str/i", substr($r, 0, $len) );
	if( $id ){ return "eps"; }
#
#	EPS v3.0 file format header
#
	$str = "%!PS-Adobe-3.0 EPSF-3.0";
	$len = strlen( $str );
	$id = preg_match( "/$str/i", substr($r, 0, $len) );
	if( $id ){ return "eps"; }
#
#	FLI file format header
#
	$hs = 0xaf11;
	$id = preg_match( "/$hs/i", substr($r, 0, 2) );
	if( $id ){ return "fli"; }
#
#	FLC file format header
#
	$hs = 0xaf12;
	$id = preg_match( "/$hs/i", substr($r, 0, 2) );
	if( $id ){ return "flc"; }
#
#	GEM VDI file format header
#
	$hs = 0xffff;
	$id = preg_match( "/$hs/i", substr($r, 0, 2) );
	if( $id ){ return "vdi"; }
#
#	DVI file format header
#
	$hs = 0x56445649;
	$id = preg_match( "/$hs/i", substr($r, 0, 4) );
	if( $id ){ return "dvi"; }
#
#	AVL file format header
#
	$hs = 0x41565353;
	$id = preg_match( "/$hs/i", substr($r, 0, 4) );
	if( $id ){ return "avl"; }
#
#	AUDI file format header
#
	$hs = 0x41554449;
	$id = preg_match( "/$hs/i", substr($r, 0, 4) );
	if( $id ){ return "audi"; }
#
#	CMIG file format header
#
	$id = preg_match( "/cmig/i", substr($r, 0, 4) );
	if( $id ){ return "cmig"; }
#
#	YCC file format header
#
	$hs = 0x5965600;
	$id = preg_match( "/$hs/i", substr($r, 0, 4) );
	if( $id ){ return "ycc"; }
#
#	Microsoft WmF file format header
#
	$id = preg_match( "/ EMF/i", substr($r, 40, 4) );
	if( $id ){ return "wmf"; }
#
#	If we can't figure it out - call it an icon.
#
	return "ico";
}
################################################################################
#	xml_dump(). Dump out the xml file.
################################################################################
function xml_dump( $xml, $title=null )
{
	if( is_null($title) ){ $title = "Array"; }

	$this->fp = fopen( "./xml.dat", "w" );
	$this->xml_dump_child( $xml, $title );
	fclose( $this->fp );
}
################################################################################
#	xml_dump_child(). Cyclic function
################################################################################
function xml_dump_child( $xml, $title=null )
{
	$fp = $this->fp;

	foreach( $xml as $k=>$v ){
		$s = "[$k]";
		if( is_array($v) ){
			$this->xml_dump_child( $v, "$title$s" );
			continue;
			}

		if( $this->is_base64($v) || preg_match("/aaaa/i", $v) ){
			fwrite( $fp, "$title$s-><image>\n" );
			}
			else { fwrite( $fp, "$title$s->$v\n" ); }
		}

	return true;
}
################################################################################
#	ssl_encrypt(). Do an openssl encryption.
################################################################################
function ssl_encrypt( $token=null, $key=null )
{
	if( is_null($token) ){ die( "TOKEN is null\n" ); }
	if( is_null($key) ){ die( "KEY is null\n" ); }

	$cipher_method = 'aes-128-ctr';
	$enc_key = openssl_digest( $key, 'SHA256', TRUE );
	$enc_iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length($cipher_method) );
	$crypted_token = openssl_encrypt( $token, $cipher_method, $enc_key, 0, $enc_iv ) .
		"::" . bin2hex( $enc_iv );

	unset( $token, $cipher_method, $enc_key, $enc_iv );

	return $crypted_token;
}
################################################################################
#	ssl_decrypt(). Do an openssl encryption.
################################################################################
function ssl_decrypt( $crypted_token=null, $key=null )
{
	if( is_null($crypted_token) ){ die( "CRYPTED_TOKEN is null\n" ); }

	if( is_null($key) ){
		list( $crypted_token, $enc_iv ) = explode( "::", $crypted_token );
		$enc_iv = hex2bin( $enc_iv );
		}

	if( !is_null($key) && preg_match("/-/", $key) ){
		$enc_iv = preg_replace( "/-/", "", $key );
		}
		else if( !is_null($key) ){ $enc_iv = $key; }

	$cipher_method = 'aes-128-ctr';
	$enc_key = openssl_digest( php_uname(), 'SHA256', TRUE );
	$token = openssl_decrypt( $crypted_token, $cipher_method, $enc_key, 0, $enc_iv );

	unset( $crypted_token, $cipher_method, $enc_key, $enc_iv );

	return $token;
}
################################################################################
#	get_class(). Returns a class specified on the call line.
#	Notes:	This is being done because I have too many re-entrant calls to my
#		classes. So now - you have to make sure you put include the class in
#		YOUR program so these can work properly.
################################################################################
function get_class( $name=null )
{
	if( is_null($name) ){
		die( "***** ERROR : Name is not given at " . __LINE__ . "\n" );
		}

	$lib = getenv( "my_libs" );
	$lib = str_replace( "\\", "/", $lib );

	if( isset($GLOBALS['classes'][$name]) ){ return $GLOBALS['classes'][$name]; }
		else { die( "***** ERROR : You need to include $lib/class_rgb.php\n" ); }
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['xml_json']) ){
		$GLOBALS['classes']['xml_json'] = new xml_json();
		}

?>
