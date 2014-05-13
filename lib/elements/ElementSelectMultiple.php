<?php
namespace ntopulos\formulator\elements;

class ElementSelectMultiple extends ElementMetaSelect
{
    public function renderElement($row=0)
    {
        $var = parent::renderElement($row);
        $var['attributes']['name'] = $var['attributes']['name'].'[]';
        $var['attributes']['multiple'] = 'multiple';

        return form_select($var['attributes'], $this->options, $var['data']['value']);
    }
}
