<?php


namespace JRest\Helpers;



/**
 * JDate is a class that stores a date and provides logic to manipulate
 * and render that date in a variety of formats.
 *
 * @method  Date|bool  add(\DateInterval $interval)  Adds an amount of days, months, years, hours, minutes and seconds to a JDate object.
 * @method  Date|bool  sub(\DateInterval $interval)  Subtracts an amount of days, months, years, hours, minutes and seconds from a JDate object.
 * @method  Date|bool  modify(string $modify)       Alter the timestamp of this object by incre/decre-menting in a format accepted by strtotime().
 *
 * @property-read  string   $daysinmonth   t - Number of days in the given month.
 * @property-read  string   $dayofweek     N - ISO-8601 numeric representation of the day of the week.
 * @property-read  string   $dayofyear     z - The day of the year (starting from 0).
 * @property-read  boolean  $isleapyear    L - Whether it's a leap year.
 * @property-read  string   $day           d - Day of the month, 2 digits with leading zeros.
 * @property-read  string   $hour          H - 24-hour format of an hour with leading zeros.
 * @property-read  string   $minute        i - Minutes with leading zeros.
 * @property-read  string   $second        s - Seconds with leading zeros.
 * @property-read  string   $microsecond   u - Microseconds with leading zeros.
 * @property-read  string   $month         m - Numeric representation of a month, with leading zeros.
 * @property-read  string   $ordinal       S - English ordinal suffix for the day of the month, 2 characters.
 * @property-read  string   $week          W - ISO-8601 week number of year, weeks starting on Monday.
 * @property-read  string   $year          Y - A full numeric representation of a year, 4 digits.
 *
 * @since  1.7.0
 */
class JDate extends \DateTime
{
	const DAY_ABBR = "\x021\x03";
	const DAY_NAME = "\x022\x03";
	const MONTH_ABBR = "\x023\x03";
	const MONTH_NAME = "\x024\x03";

	/**
	 * The format string to be applied when using the __toString() magic method.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	public static $format = 'Y-m-d H:i:s';

	/**
	 * Placeholder for a \DateTimeZone object with GMT as the time zone.
	 *
	 * @var    object
	 * @since  1.7.0
	 */
	protected static $gmt;

	/**
	 * Placeholder for a \DateTimeZone object with the default server
	 * time zone as the time zone.
	 *
	 * @var    object
	 * @since  1.7.0
	 */
	protected static $stz;

	/**
	 * The \DateTimeZone object for usage in rending dates as strings.
	 *
	 * @var    \DateTimeZone
	 * @since  3.0.0
	 */
	protected $tz;

	/**
	 * Constructor.
	 *
	 * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
	 * @param   mixed   $tz    Time zone to be used for the date. Might be a string or a DateTimeZone object.
	 *
	 * @since   1.7.0
	 */
	public function __construct($date = 'now', $tz = null)
	{
		// Create the base GMT and server time zone objects.
		if (empty(self::$gmt) || empty(self::$stz))
		{
		    
			self::$gmt = new \DateTimeZone('GMT');
			self::$stz = new \DateTimeZone(@date_default_timezone_get());
		}

		// If the time zone object is not set, attempt to build it.
		if (!($tz instanceof \DateTimeZone))
		{
			if ($tz === null)
			{
				$tz = self::$gmt;
			}
			elseif (is_string($tz))
			{
				$tz = new \DateTimeZone($tz);
			}
		}

		// If the date is numeric assume a unix timestamp and convert it.
		date_default_timezone_set('UTC');
		$date = is_numeric($date) ? date('c', $date) : $date;

		// Call the DateTime constructor.
		parent::__construct($date, $tz);

		// Reset the timezone for 3rd party libraries/extension that does not use JDate
		date_default_timezone_set(self::$stz->getName());

		// Set the timezone object for access later.
		$this->tz = $tz;
	}

	/**
	 * Magic method to access properties of the date given by class to the format method.
	 *
	 * @param   string  $name  The name of the property.
	 *
	 * @return  mixed   A value if the property name is valid, null otherwise.
	 *
	 * @since   1.7.0
	 */
	public function __get($name)
	{
		$value = null;

		switch ($name)
		{
			case 'daysinmonth':
				$value = $this->format('t', true);
				break;

			case 'dayofweek':
				$value = $this->format('N', true);
				break;

			case 'dayofyear':
				$value = $this->format('z', true);
				break;

			case 'isleapyear':
				$value = (boolean) $this->format('L', true);
				break;

			case 'day':
				$value = $this->format('d', true);
				break;

			case 'hour':
				$value = $this->format('H', true);
				break;

			case 'minute':
				$value = $this->format('i', true);
				break;

			case 'second':
				$value = $this->format('s', true);
				break;

			case 'month':
				$value = $this->format('m', true);
				break;

			case 'ordinal':
				$value = $this->format('S', true);
				break;

			case 'week':
				$value = $this->format('W', true);
				break;

			case 'year':
				$value = $this->format('Y', true);
				break;

			default:
				$trace = debug_backtrace();
				trigger_error(
					'Undefined property via __get(): ' . $name . ' in ' . $trace[0]['file'] . ' on line ' . $trace[0]['line'],
					E_USER_NOTICE
				);
		}

		return $value;
	}

	/**
	 * Magic method to render the date object in the format specified in the public
	 * static member Date::$format.
	 *
	 * @return  string  The date as a formatted string.
	 *
	 * @since   1.7.0
	 */
	public function __toString()
	{
		return (string) parent::format(self::$format);
	}

	/**
	 * Proxy for new JDate().
	 *
	 * @param   string  $date  String in a format accepted by strtotime(), defaults to "now".
	 * @param   mixed   $tz    Time zone to be used for the date.
	 *
	 * @return  JDate
	 *
	 * @since   1.7.3
	 */
	public static function getInstance($date = 'now', $tz = null)
	{
		return new JDate($date, $tz);
	}

	

	/**
	 * Gets the date as a formatted string in a local calendar.
	 *
	 * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
	 * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
	 * @param   boolean  $translate  True to translate localised strings
	 *
	 * @return  string   The date string in the specified format format.
	 *
	 * @since   1.7.0
	 */
	public function calendar($format, $local = false, $translate = true)
	{
		return $this->format($format, $local, $translate);
	}

	/**
	 * Gets the date as a formatted string.
	 *
	 * @param   string   $format     The date format specification string (see {@link PHP_MANUAL#date})
	 * @param   boolean  $local      True to return the date string in the local time zone, false to return it in GMT.
	 * @param   boolean  $translate  True to translate localised strings
	 *
	 * @return  string   The date string in the specified format format.
	 *
	 * @since   1.7.0
	 */
	public function format($format, $local = false, $translate = true)
	{
		

		// If the returned time should not be local use GMT.
		if ($local == false && !empty(self::$gmt))
		{
			parent::setTimezone(self::$gmt);
		}

		// Format the date.
		$return = parent::format($format);

		

		

		return $return;
	}

	/**
	 * Get the time offset from GMT in hours or seconds.
	 *
	 * @param   boolean  $hours  True to return the value in hours.
	 *
	 * @return  float  The time offset from GMT either in hours or in seconds.
	 *
	 * @since   1.7.0
	 */
	public function getOffsetFromGmt($hours = false)
	{
		return (float) $hours ? ($this->tz->getOffset($this) / 3600) : $this->tz->getOffset($this);
	}

	
	/**
	 * Method to wrap the setTimezone() function and set the internal time zone object.
	 *
	 * @param   \DateTimeZone  $tz  The new \DateTimeZone object.
	 *
	 * @return  JDate
	 *
	 * @since   1.7.0
	 * @note    This method can't be type hinted due to a PHP bug: https://bugs.php.net/bug.php?id=61483
	 */
	public function setTimezone($tz)
	{
		$this->tz = $tz;

		return parent::setTimezone($tz);
	}

	/**
	 * Gets the date as an ISO 8601 string.  IETF RFC 3339 defines the ISO 8601 format
	 * and it can be found at the IETF Web site.
	 *
	 * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
	 *
	 * @return  string  The date string in ISO 8601 format.
	 *
	 * @link    http://www.ietf.org/rfc/rfc3339.txt
	 * @since   1.7.0
	 */
	public function toISO8601($local = false)
	{
		return $this->format(\DateTime::RFC3339, $local, false);
	}

	

	/**
	 * Gets the date as an RFC 822 string.  IETF RFC 2822 supercedes RFC 822 and its definition
	 * can be found at the IETF Web site.
	 *
	 * @param   boolean  $local  True to return the date string in the local time zone, false to return it in GMT.
	 *
	 * @return  string   The date string in RFC 822 format.
	 *
	 * @link    http://www.ietf.org/rfc/rfc2822.txt
	 * @since   1.7.0
	 */
	public function toRFC822($local = false)
	{
		return $this->format(\DateTime::RFC2822, $local, false);
	}

	/**
	 * Gets the date as UNIX time stamp.
	 *
	 * @return  integer  The date as a UNIX timestamp.
	 *
	 * @since   1.7.0
	 */
	public function toUnix()
	{
		return (int) parent::format('U');
	}
	
	public function toSql()
	{
	    return 'Y-m-d H:i:s';
	}

	/**
	 * convert number to month
	 * 
	 */
	const JANUARY = ['display' => 'January', 'value' => '1','code'=>'JAN'];
	const FEBUARY = ['display' => 'Febuary', 'value' => '2','code'=>'FEB'];
	const MARCH = ['display' => 'March', 'value' => '3','code'=>'MAR'];
	const APRIL = ['display' => 'April', 'value' => '4','code'=>'APR'];
	const MAY = ['display' => 'May', 'value' => '5','code'=>'MAY'];
	const JUNE = ['display' => 'June', 'value' => '6','code'=>'JUN'];
	const JULY = ['display' => 'July', 'value' => '7','code'=>'JUL'];
	const AUGUST = ['display' => 'August', 'value' => '8','code'=>'AUG'];
	const SEPTEMBER = ['display' => 'September', 'value' => '9','code'=>'SEP'];
	const OCTOBER = ['display' => 'October', 'value' => '10','code'=>'OCT'];
	const NOVEMBER = ['display' => 'November', 'value' => '11','code'=>'NOV'];
	const DECEMBER = ['display' => 'December', 'value' => '12','code'=>'DEC'];
	public static function getValueOfMonth($display)
    {
        if (isset($display)) {
            $oClass = new \ReflectionClass(__CLASS__);
			$constants = $oClass->getConstants();
			// var_dump($constants);
            foreach ($constants as $item) {
                if (is_array($item) && $item['display'] == $display) return $item['value'];
            }
        }
        return false;
	}
	public static function getDisplayOfMonth($value)
    {
        if (isset($value)) {
            $oClass = new \ReflectionClass(__CLASS__);
            $constants = $oClass->getConstants();
            foreach ($constants as $item) {
                if (is_array($item) && $item['value'] == $value) return $item['display'];
            }
        }
        return false;
	}
	public static function getInstanceOfMonth($value)
    {
        if (isset($value)) {
            $oClass = new \ReflectionClass(__CLASS__);
            $constants = $oClass->getConstants();
            foreach ($constants as $item) {
                if (is_array($item) && $item['value'] == $value) return $item;
            }
        }
        return false;
	}
	public static function isDate($value) 
	{
		if (!$value) {
			return false;
		}
	
		try {
			new \DateTime($value);
			return true;
		} catch (\Exception $e) {
			return false;
		}
	}
}
