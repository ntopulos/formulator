<?php
namespace ntopulos\formulator\elements;

class ElementInputDatetimelocal extends ElementInputMetaTime
{
    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);
        // type attribute does not follow classical naming
        $this->type = 'datetime-local';

        $this->type_inherited_rules = array_merge(
            $this->type_inherited_rules,
            array('html_datetimelocal' => null)
            );
    }
}
