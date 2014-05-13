<?php
namespace ntopulos\formulator\elements;

class ElementInputColor extends ElementInput
{
    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);

        // Placeholder unset
        if (isset($this->attributes['placeholder'])) {
            unset($this->attributes['placeholder']);
        }

        $this->type_inherited_rules = array_merge(
            $this->type_inherited_rules,
            array('color' => null)
            );
    }
}
