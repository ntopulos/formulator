<?php
namespace ntopulos\formulator\elements;

abstract class ElementMultiple extends Element
{

    protected $type;        // generated value (todo: maybe should be solved in another way to avoid this duplicate...)
    public $options = array();


    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);

        // Placeholder unset
        if (isset($this->attributes['placeholder'])) {
            unset($this->attributes['placeholder']);
        }
    }


    protected function renderElement($row = 0)
    {
        $data = parent::renderElement();

        return $data;
    }

}
