<?php
namespace ntopulos\formulator\elements;

class ElementKeygen extends ElementSingle
{
    public function renderElement($row=0)
    {
        $data = parent::renderElement($row);
        unset($data['value']);
        return \ntopulos\formulator\Render::keygen($data);
    }
}
