<?php
namespace ntopulos\formulator\elements;

abstract class ElementSingle extends Element
{

    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);
    }

    protected function renderElement($row = 0)
    {
        $data = parent::renderElement($row);
        $data['id'] = $data['name'];

        return $data;
    }

}
