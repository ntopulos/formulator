<?php
namespace ntopulos\formulator\elements;

abstract class ElementInput extends ElementSingle
{
    protected $type;        // html input type

    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);
    }

    public function renderElement($row = 0)
    {
        $data = parent::renderElement($row);
        
        // merging to put type at the beginning
        $html_type = strtolower(substr($this->type,5));
        $data = array_merge(array('type' => $html_type), $data);

        // attributes
        $data = array_merge($data, $this->attributes);

        // no value backsend for these
        if (in_array($data['type'], array('password', 'file'))) {
            unset($data['value']);
        }

        return \ntopulos\formulator\Render::input($data);
    }
}
