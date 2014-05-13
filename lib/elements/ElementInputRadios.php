<?php
namespace ntopulos\formulator\elements;

class ElementInputRadios extends ElementMultiple
{
    public function render_label($row=0)
    {
        if (count($this->options) > 1) {
            $label_output = NULL;
        } else {
            $label_output = parent::render_label();
        }
    
        return $label_output;
    }

    public function renderElement($row=0)
    {
        $data = parent::renderElement($row);

        // default value correction -> BETTER SOLUTION ?
        if ($data['value'] == $this->default_value AND !empty($data['value'])) {
            $data['value'] = $data['name'].'['.$data['value'].']';
        }

        // case no option because there is only one choice (boolean)
        if (empty($this->options)) {
            $this->options = array($this->name);
        }

        // preparing labels
        foreach($this->options as $option) {
            $labels[] = ( $this->formulator->human_readable_labels ? ucwords(str_replace('_',' ',$option)) : $option);
        }

        // constructing array where key is the label and value the value

        $options = array_combine($labels,$this->options);

        $attributes = array(
            'name'  => $data['name'],
            'id'    => $data['name']
            );

        return \ntopulos\formulator\Render::radios($this->label, $options, $attributes, $data['value']);
    }
}
