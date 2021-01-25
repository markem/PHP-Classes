<?php
class MicroDateTime extends DateTime
{
    public $microseconds = 0;

public function __construct($time = 'now')
{
	if ($time == 'now')
		$time = microtime(true);
#
# "+ 0" implicitly converts $time to a numeric value
#
	if( is_float($time + 0) ){
		list($ts, $ms) = explode('.', $time);
		parent::__construct(date('Y-m-d H:i:s.', $ts).$ms);
		$this->microseconds = $time - (int)$time;
		}
		else {
			throw new Exception('Incorrect value for time "' . print_r($time, true) . '"');
			}
}

public function setTimestamp($timestamp)
{
	parent::setTimestamp($timestamp);
	$this->microseconds = $timestamp - (int)$timestamp;
}

public function getTimestamp()
{
	return parent::getTimestamp() + $this->microseconds;
}

}

	if( !isset($GLOBALS['classes']) ){ global $classes; }
	if( !isset($GLOBALS['classes']['time']) ){ $GLOBALS['classes']['time'] = new class_time(); }

?>
