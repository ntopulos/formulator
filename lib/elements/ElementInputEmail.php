<?php
namespace ntopulos\formulator\elements;

class ElementInputEmail extends ElementInput
{
    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);

        $this->type_inherited_rules = array_merge(
            $this->type_inherited_rules,
            array('email' => null)
            );
    }
}
