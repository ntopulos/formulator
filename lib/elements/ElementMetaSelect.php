<?php
namespace ntopulos\formulator\elements;

abstract class ElementMetaSelect extends ElementMultiple
{
    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);
    }

    protected function renderElement($row = 0)
    {
        $data = parent::renderElement($row);
        $data['id'] = $data['name'];
        
        $attributes = array(
            'name'  => $data['name'],
            'id'    => $data['name']
            );

        return array('data' => $data, 'attributes' => $attributes);
    }

}
