<?php
namespace ntopulos\formulator\elements;

class ElementInputNumber extends ElementInput
{
    function __construct($name, $default_value, $table, $options, $formulator)
    {
        parent::__construct($name, $default_value, $table, $options, $formulator);

        $this->type_inherited_rules = array_merge(
            $this->type_inherited_rules,
            array('numeric' => null)
            );
    }

    public function computeSystemAttributes()
    {
        parent::computeSystemAttributes();

        // auto attibutes: between, min, max
        if (array_key_exists('between', $this->rules)) {
            $new_attr = array(
                'min' => $this->rules['between'][0],
                'max' => $this->rules['between'][1],
                );
            $this->system_attributes = array_merge($new_attr, $this->system_attributes);
        }

        if (array_key_exists('min', $this->rules)) {
            $new_attr = array('min' => $this->rules['min']);
            $this->system_attributes = array_merge($new_attr, $this->system_attributes);
        }

        if (array_key_exists('max', $this->rules)) {
            $new_attr = array('max' => $this->rules['max']);
            $this->system_attributes = array_merge($new_attr, $this->system_attributes);
        }
    }
}
