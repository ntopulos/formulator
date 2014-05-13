<?php
namespace ntopulos\formulator;

/*************************************************************************/
/*                          Render CLASS                                 */
/*************************************************************************/

/* Part of - Formulator library - */
/* This class contains all the functions that actually generate HTML elements */
class Render
{


    /*************************************************************************/
    /*                               MAIN METHODS                            */
    /*************************************************************************/
    
    /**
     * Creates a start tag, or mono tag
     *
     * @param   string      Name of the tag, ie: input
     * @param   array       All parameters of the tag, type and name should be the first elements of the array
     * @return  string
     */
    public static function openTag($tag_name, $attributes)
    {
        $html_attributes = '';

        // attributes that do not need value
        $single_attributes = array(
            'autofocus',
            'checked',
            'disabled',
            'formnovalidate',
            'multiple',
            'readonly',
            'required'
            );

        foreach ($attributes as $key => $val)   {
            // no value="", it can disturb browsers autocomplete
            if (!($key == 'value' AND $val == '')) {
                if (in_array($key, $single_attributes)) {
                    $html_attributes .= ' '.$key;
                } else {
                    $html_attributes .= ' '.$key.'="'.$val.'"';
                }
            }
        }

        return '<'.$tag_name.$html_attributes.'>';
    }



    /*************************************************************************/
    /*                              FORM ELEMENTS                            */
    /*************************************************************************/

    /**
     * Creates a button
     *
     * @param   array       All parameters of the tag, type and name should be the first elements of the array
     * @return  string
     */

    public static function button($attributes)
    {
        $value = $attributes['value'];
        unset($attributes['value']);

        $button = static::openTag('button', $attributes);
        $button .= $value.'</button>';

        return $button;
    }

    /**
     * Creates an input
     *
     * @param   array       All parameters of the tag, type and name should be the first elements of the array
     * @return  string
     */

    public static function input($attributes)
    {
        return static::openTag('input', $attributes);
    }

    /**
     * Creates a keygen (HTML5 element)
     *
     * @param   array       All parameters of the tag, type and name should be the first elements of the array
     * @return  string
     */

    public static function keygen($attributes)
    {
        return static::openTag('keygen', $attributes);
    }

    /**
     * Creates a output
     *
     * @param   array       All parameters of the tag, type and name should be the first elements of the array
     * @return  string
     */

    public static function output($attributes, $default_value='')
    {
        $element = static::openTag('output', $attributes);
        $element .= $default_value.'</output>';

        return $element;
    }

    /**
     * Creates a textarea
     *
     * @param   array       All parameters of the tag, type and name should be the first elements of the array
     * @return  string
     */

    public static function textarea($attributes, $default_value='')
    {
        $element = static::openTag('textarea', $attributes);
        $element .= $default_value.'</textarea>';

        return $element;
    }

    /**
     * Creates a datalist
     *
     * @param   array       All parameters of the tag, type and name should be the first elements of the array
     * @param   array       Options of the select
     * @param   various     Selected values : string, array, or false
     * @return  string
     */
    public static function datalist($id, $val)
    {
        $attributes = array_merge($val['attributes'], array('id' => $id));

        $element = static::openTag('datalist', $attributes);

        foreach($val['options'] as $option) {
            $element .= '<option value="'.$option.'">';
        }

        $element .= '</datalist>';

        return $element;
    }

    /**
     * Creates a select
     *
     * @param   array       All parameters of the tag, type and name should be the first elements of the array
     * @param   array       Options of the select
     * @param   various     Selected values : string, array, or false
     * @return  string
     */
    public static function select($attributes, $options, $selected=false)
    {
        $element = static::openTag('select', $attributes);

        foreach($options as $option) {
            
            if(is_string($selected)) {
                $is_selected = ( $option == $selected ? ' selected="selected"' : '');
            } elseif(is_array($selected)) {
                $is_selected = '';

                foreach($selected as $value) {
                    if($value == $option) {
                        $is_selected = ' selected="selected"';

                        break;
                    }
                }
            } else {
                $is_selected = '';
            }

            $element .= '<option value="'.$option.'"'.$is_selected.'>'.$option.'</option>';
        }

        $element .= '</select>';

        return $element;
    }

    /**
     * Creates one or a group of checkboxes
     *
     * @param   array       Options of the select where key is the label and value the value
     * @param   array       All parameters of the tag, type and name should be the first elements of the array
     * @param   various     Selected values : string, array, or false
     * @return  string
     */

    public static function checkboxes($global_label, $options, $attributes, $selected=false)
    {
        // declaring checkbox type
        $attributes = array_merge($attributes, array('type' => 'checkbox'));

        $result = '';
        foreach($options as $label => $value) {

            // adapting attributes to multiple choices
            $attributes_i = $attributes;
            $attributes_i['name'] = (count($options)>1 ? $attributes_i['name'].'['.$value.']' : $attributes_i['name']);
            $attributes_i['id'] = $attributes_i['name'];
            $attributes_i['value'] = $attributes_i['name'];

            // selected
            if(is_string($selected)) {
                if($selected == $attributes_i['value']) {$attributes_i['checked'] = 'checked';}
            } elseif(is_array($selected)) {
                if(in_array($attributes_i['value'],$selected)) {
                    $attributes_i['checked'] = 'checked';
                }
            }

            // multiple or single checkbox ?
            $label_i = (count($options)>1 ? '<label for="'.$attributes_i['id'].'">'.$label.'</label>' : '');

            $result .= $label_i . static::openTag('input', $attributes_i);
        }

        // fieldset if multiple checkboxes
        if(count($options)>1) {
            $result = '<fieldset><legend>'.$global_label.'</legend>' . $result . '</fieldset>';
        }

        return $result;
    }

    /**
     * Creates one or a group of radios
     *
     * @param   array       Options of the select where key is the label and value the value
     * @param   array       All parameters of the tag, type and name should be the first elements of the array
     * @param   various     Selected values : string, array, or false
     * @return  string
     */

    public static function radios($global_label, $options, $attributes, $selected=false)
    {
        // declaring checkbox type
        $attributes = array_merge($attributes, array('type' => 'radio'));

        $result = '';
        foreach($options as $label => $value) {

            // adapting attributes to multiple choices
            $attributes_i = $attributes;
            $attributes_i['name'] = (count($options)>1 ? $attributes_i['name'] : $attributes_i['name']);
            $attributes_i['value'] = (count($options)>1 ? $attributes_i['name'].'['.$value.']' : $attributes_i['name']);
            $attributes_i['id'] = $attributes_i['value'];

            // selected
            if(is_string($selected)) {
                if($selected == $attributes_i['value']) {
                    $attributes_i['checked'] = 'checked';
                }
            } elseif(is_array($selected)) {
                if(in_array($attributes_i['value'],$selected)) {
                    $attributes_i['checked'] = 'checked';
                }
            }

            // multiple or single checkbox ?
            $label_i = (count($options)>1 ? '<label for="'.$attributes_i['id'].'">'.$label.'</label>' : '');

            $result .= $label_i . static::openTag('input', $attributes_i);
        }

        // fieldset if multiple checkboxes
        if(count($options)>1) {
            $result = '<fieldset><legend>'.$global_label.'</legend>' . $result . '</fieldset>';
        }

        return $result;
    }

}
