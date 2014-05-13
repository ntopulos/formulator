<?php
namespace ntopulos\formulator\elements;

/* Abstract class for:
 * ElementInputDate
 * ElementInputDatetime
 * ElementInputDatetimelocal
 * ElementInputMonth
 * ElementInputTime
 * ElementInputWeek
 *
 * Sets the system attributes for these elements.
*/
abstract class ElementInputMetaTime extends ElementInput
{
    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);
    }

    public function computeSystemAttributes()
    {
        parent::computeSystemAttributes();

        // auto attibutes: between, min, max
        $new_attr = array();

        if (array_key_exists('time_between', $this->rules)) {
            $new_attr = array(
                'min' => $this->rules['time_between'][0],
                'max' => $this->rules['time_between'][1],
                );
            $this->system_attributes = array_merge($new_attr, $this->system_attributes);
        }

        if (array_key_exists('time_min', $this->rules)) {
            $new_attr = array('min' => $this->rules['time_min']);
            $this->system_attributes = array_merge($new_attr, $this->system_attributes);
        }

        if (array_key_exists('time_max', $this->rules)) {
            $new_attr = array('max' => $this->rules['time_max']);
            $this->system_attributes = array_merge($new_attr, $this->system_attributes);
        }
    }
}
