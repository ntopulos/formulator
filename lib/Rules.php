<?php
namespace ntopulos\formulator;

/*************************************************************************/
/*                          RULES CLASS                                  */
/*************************************************************************/

/* Part of - Formulator library - */
/* This class contains the validation rules */
class Rules {

    /**
     * Alpha
     *
     * @param   string  $input
     * @return  bool
    */
    public static function alpha($input)
    {
        return ctype_alpha($input);
    }

    /**
     * Alpha-numeric
     *
     * @param   string  $input
     * @return  bool
    */
    public static function alpha_numeric($input)
    {
        return ctype_alnum($input);
    }

    /**
     * Between
     *
     * @param   string      $input
     * @param   array       $between
     * @return  bool
    */
    public static function between($input, $between)
    {
        return (static::numeric($input) AND $input >= $between[0] AND $input <= $between[1]);
    }

    /**
     * Color
     *
     * @param   string      $input
     * @return  bool
    */
    public static function color($input)
    {
        $result = false;

        if (strlen($input) === 7 AND $input[0] === '#') {
            if (ctype_xdigit(substr($input, 1))) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Date
     *
     * @param   string      $input
     * @param   array       $format
     * @return  bool
    */
    public static function date($input, $format = 'Y-m-d')
    {
        // $format can contain more than one format, seperated by ||
        // comply to one is sufficient
        $formats = explode('||', $format);
        if (!isset($formats[1])) {
            $d = \DateTime::createFromFormat($format, $input);
            return $d && $d->format($format) == $input;
        } else {
            foreach ($formats as $format) {
                $d = \DateTime::createFromFormat($format, $input);
                $result = $d && $d->format($format) == $input;
                if ($result) {
                    return $result;
                }
            }
            // if no rule valid
            return false;
        }
    }

    /**
     * Equal
     *
     * @param   string      $input
     * @param   string      $value
     * @return  bool
    */
    public static function equal($input, $value)
    {
        return $input === $value;
    }

    /**
     * Email
     *
     * @param   string  $input
     * @return  bool
    */
    public static function email($input)
    {
        return filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Float
     *
     * @param   string  $input
     * @return  bool
    */
    public static function float($input)
    {
        return is_float($input);
    }

    /**
     * Datetime validation according to HTML5 W3C specifications
     * http://www.w3.org/TR/html5/infrastructure.html#valid-normalized-forced-utc-global-date-and-time-string
     *
     * @param   string  $input
     * @return  bool
    */
    public static function html_datetime($input)
    {
        if (static::date($input, 'Y-m-d\TH:i\Z||Y-m-d\TH:i:s\Z')) {
            return true;
        } elseif (preg_match('/\A\d{4}-\d{2}-\d{2}\T\d{2}:\d{2}:\d{2}\.\d{1,3}Z\z/', $input)) {
            $datetime = explode('.', $input);
            if (static::date($datetime[0], 'Y-m-d\TH:i:s')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Datetime-local validation according to HTML5 W3C specifications
     * http://www.w3.org/TR/html5/infrastructure.html#valid-normalized-forced-utc-global-date-and-time-string
     *
     * @param   string  $input
     * @return  bool
    */
    public static function html_datetimelocal($input)
    {
        if (static::date($input, 'Y-m-d\TH:i||Y-m-d\TH:i:s')) {
            return true;
        } elseif (preg_match('/\A\d{4}-\d{2}-\d{2}\T\d{2}:\d{2}:\d{2}\.\d{1,3}\z/', $input)) {
            $datetime = explode('.', $input);
            if (static::date($datetime[0], 'Y-m-d\TH:i:s')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Time validation according to HTML5 W3C specifications
     * http://www.w3.org/TR/html5/infrastructure.html#valid-time-string
     *
     * @param   string  $input
     * @return  bool
    */
    public static function html_time($input)
    {
        if (static::date($input, 'H:i||H:i:s')) {
            return true;
        } elseif (preg_match('/\A\d{2}:\d{2}:\d{2}\.\d{1,3}\z/', $input)) {
            $exp = explode('.', $input);
            if (static::date($exp[0], 'H:i:s')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Week validation according to HTML5 W3C specifications
     * http://www.w3.org/TR/html5/forms.html#week-state-(type=week)
     *
     * @param   string  $input
     * @return  bool
    */
    public static function html_week($input)
    {
        if (preg_match('/\A\d{4}-W\d{2}\z/', $input)) {

            $exp = explode('-W', $input);
            $y = $exp[0];
            $w = $exp[1];

            if (static::date($y, 'Y')) {
                // number of weeks that year
                $max_week = date("W", mktime(0,0,0,12,28, $y));
                
                if ($w >= 1 AND $w <= $max_week) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Integer
     *
     * @param   string  $input
     * @return  bool
    */
    public static function integer($input)
    {
        return preg_match('/^-\d+|\d+$/', $input);
    }

    /**
     * Ip
     *
     * @param   string  $input
     * @return  bool
    */
    public static function ip($input)
    {
        return filter_var($input, FILTER_VALIDATE_IP);
    }

    /**
     * Length between
     *
     * @param   integer  $input
     * @param   array  $between
     * @return  bool
    */
    public static function length_between($input, $between)
    {
        return (strlen($input) >= $between[0] AND strlen($input) <= $between[1]);
    }

    /**
     * Length max
     *
     * @param   integer  $input
     * @return  bool
    */
    public static function length_max($input, $max)
    {
        return (strlen($input) <= $max);
    }

    /**
     * Length min
     *
     * @param   integer  $input
     * @return  bool
    */
    public static function length_min($input, $min)
    {
        return (strlen($input) >= $min);
    }

    /**
     * Max
     *
     * @param   numeric     $max
     * @param   string      $input
     * @return  bool
    */
    public static function max($input, $max)
    {
        return (static::numeric($input) AND $input <= $max);
    }

    /**
     * Min
     *
     * @param   numeric     $min
     * @param   string      $input
     * @return  bool
    */
    public static function min($input, $min)
    {
        return (static::numeric($input) AND $input >= $min);
    }

    /**
     * Numeric
     *
     * @param   string  $input
     * @return  bool
    */
    public static function numeric($input)
    {
        return is_numeric($input);
    }

    /**
     * Required
     *
     * @param   string  $input
     * @return  bool
    */
    public static function required($input)
    {
        return ( (is_string($input) && strlen($input) > 0) OR is_array($input) ? true : false);
    }

    /**
     * Time after
     *
     * @param   string      $input
     * @return  bool
    */
    public static function time_after($input, $after)
    {
        if (static::compareTimesDates($input, $after) === 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Time before
     *
     * @param   string      $input
     * @return  bool
    */
    public static function time_before($input, $before)
    {
        if (static::compareTimesDates($input, $before) === -1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Time between
     *
     * @param   string      $input
     * @return  bool
    */
    public static function time_between($input, $between)
    {
        $cp1 = static::compareTimesDates($input, $between[0]);
        $cp2 = static::compareTimesDates($input, $between[1]);
        if (($cp1 === 1 OR $cp1 === 0) AND ($cp2 === -1 OR $cp2 === 0)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Time max
     *
     * @param   string      $input
     * @return  bool
    */
    public static function time_max($input, $max)
    {
        $cp = static::compareTimesDates($input, $max);
        if ($cp === -1 OR $cp === 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Time min
     *
     * @param   string      $input
     * @return  bool
    */
    public static function time_min($input, $min)
    {
        $cp = static::compareTimesDates($input, $min);
        if ($cp === 1 OR $cp === 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Url
     *
     * @param   string  $input
     * @return  bool
    */
    public static function url($input)
    {
        return filter_var($input, FILTER_VALIDATE_URL);
    }



    /*************************************************************************/
    /*                         PRIVATE METHODS                               */
    /*************************************************************************/

    /**
     * Compare two dates
     * says if the first is, relatively to the second:
     * 1: bigger
     * 0: equal
     * -1: smaller
     *
     * @param   string      $input
     * @return  integer
    */
    private static function compareDates($first, $second, $format = 'Y-m-d')
    {
        $result = false;

        $date_f = new \DateTime($first);
        $date_f->createFromFormat($format, $first);

        $date_s = new \DateTime($second);
        $date_s->createFromFormat($format, $second);

        if ($date_f->format('U') > $date_s->format('U')) {
            return 1;
        } elseif ($date_f->format('U') < $date_s->format('U')) {
            return -1;
        } else {
            return 0;
        }  
    }

    /**
     * Compare two times (html format)
     * says if the first is, relatively to the second:
     * 1: bigger
     * 0: equal
     * -1: smaller
     *
     * @param   string
     * @param   string
     * @return  integer
    */
    private static function compareTimes($first, $second)
    {
        $ft = explode(':', $first);
        $ft[2] = (isset($ft[2]) ? $ft[2] : '00');
        $ft_s = explode('.', $ft[2]);
        $sd = explode(':', $second);
        $sd[2] = (isset($sd[2]) ? $sd[2] : '00');
        $sd_s = explode('.', $sd[2]);

        $time_ft = mktime($ft[0], $ft[1], $ft_s[0], 1, 1, 1980);
        $time_sd = mktime($sd[0], $sd[1], $sd_s[0], 1, 1, 1980);

        if ($time_ft > $time_sd) {
            return 1;
        } elseif ($time_ft < $time_sd) {
            return -1;
        } else {
            // equal case
            if (isset($ft_s[1]) AND isset($sd_s[1])) {
                if ($ft_s[1] > $sd_s[1]) {
                    return 1;
                } elseif ($ft_s[1] < $sd_s[1]) {
                    return -1;
                } else {
                    return 0;
                }
            } else {
                return 0;
            }
        }
    }

    /**
     * Meta method to compare dates and times
     * says if the first is, relatively to the second:
     * 1: bigger
     * 0: equal
     * -1: smaller
     * false: incorrect input
     *
     * @param   string      $input
     * @param   string      $second
     * @return  mixed
    */
    private static function compareTimesDates($input, $second)
    {
        $detected = static::detectTimeFormat($second);

        switch ($detected['rule']) {
            case 'date':
                if (static::date($input, $detected['format'])) {
                    return static::compareDates($input, $second, $detected['format']);
                }
                break;

            case 'html_datetime':
            case 'html_datetimelocal':
                if (static::{$detected['rule']}($input)) {
                    $in = explode('T', $input);
                    $aft = explode('T', $second);
                    $cp = static::compareDates($in[0], $aft[0], $detected['format']);

                    if ($cp == 0) {
                        $in[1] = str_replace("Z", "", $in[1]);
                        $aft[1] = str_replace("Z", "", $aft[1]);
                        return static::compareTimes($in[1], $aft[1]);
                    }
                    else {
                        return $cp;
                    }
                }
                break;
                
            case 'html_time':
                if (static::html_time($input)) {
                    return static::compareTimes($input, $second);
                }
                break;

            case 'html_week':
                $in = explode('-W', $input);
                $aft = explode('-W', $second);

                if ($in[0] > $aft[0]) {
                    return 1;
                } elseif ($in[0] < $aft[0]) {
                    return -1;
                } else {
                    if ($in[1] > $aft[1]) {
                        return 1;
                    } elseif ($in[1] < $aft[1]) {
                        return -1;
                    } else {
                        return 0;
                    }
                }
                break;
        }

        return false;
    }

    /**
     * Detecting time/date format
     *
     * @param   string      $input
     * @return  arrra       
    */
    private static function detectTimeFormat($input)
    {
        // not all formats need this parameter
        $format = NULL;

        if (preg_match('/\A\d{4}-\d{2}-\d{2}\z/', $input)) {
            $rule = 'date';         // input=date
            $format = 'Y-d-m';
        } elseif (preg_match('/\A\d{4}-\d{2}\z/', $input)) {
            $rule = 'date';         // input=month
            $format = 'Y-m';
        }
        elseif (static::html_datetime($input)) {
            $rule = 'html_datetime';
        } elseif (static::html_datetimelocal($input)) {
            $rule = 'html_datetimelocal';
        } elseif (static::html_time($input)) {
            $rule = 'html_time';
        } elseif (static::html_week($input)) {
            $rule = 'html_week';
        } else {
            $format = 'Y-d-m';
        }

        return array('rule' => $rule, 'format' => $format);
    }
}
