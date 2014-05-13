<?php
namespace ntopulos\formulator\elements;

class ElementInputHidden extends ElementInput
{
    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);

        // Placeholder unset
        if (isset($this->attributes['placeholder'])) {
            unset($this->attributes['placeholder']);
        }
        
        $this->hidden = true;
    }
}
