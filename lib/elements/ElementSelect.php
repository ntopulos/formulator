<?php
namespace ntopulos\formulator\elements;

class ElementSelect extends ElementMetaSelect
{
    public function renderElement($row=0)
    {
        $var = parent::renderElement($row);
        return \ntopulos\formulator\Render::select($var['attributes'], $this->options, $var['data']['value']);
    }
}
