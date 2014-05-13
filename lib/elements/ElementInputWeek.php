<?php
namespace ntopulos\formulator\elements;

class ElementInputWeek extends ElementInputMetaTime
{
    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);

        $this->type_inherited_rules = array_merge(
            $this->type_inherited_rules,
            array('html_week' => null)
            );
    }
}
