<?php
namespace ntopulos\formulator\elements;

class ElementTextarea extends ElementSingle
{
    public function renderElement($row=0)
    {
        $data = parent::renderElement($row);
        
        $default_value = $data['value'];
        unset($data['value']);

        // Placeholder unset
        if (isset($this->attributes['placeholder'])) {
            unset($this->attributes['placeholder']);
        }

        return \ntopulos\formulator\Render::textarea($data, $default_value);
    }
}
