<?php
namespace ntopulos\formulator\elements;

/*************************************************************************/
/*                          CLASS ELEMENT                                */
/*************************************************************************/

/* Part of - Formulator library - */
/* This class defines all the commons of all the formulator fields */
abstract class Element
{
    
    protected $formulator;                  // Main Formulator object

    public $name;                           // syntax: table.col    -> output name in monorow forms only
    public $table;
    public $column;
    public $default_value;
    public $disabled = false;
    public $hidden = false;

    public $label;
    public $description;

    public $attributes = array();           // attributes of the html tag
    public $system_attributes = array();    // automatically added attr. in relation with rules

    public $data = array();                 // data object (row by row values) with: value, valid, error

    public $rules = array();                // list of validation rules (names of corresponding rules in Validation class)
    public $type_inherited_rules = array(); // values related to field types


    function __construct($name, $default_value, $table, $options, $formulator)
    {
        $this->name = $name;
        $this->table = $table;

        // element type extracted from class name
        $classname = get_class($this);
        if (preg_match('@\\\\([\w]+)$@', $classname, $matches)) {
            $classname = $matches[1];
        }
        $this->type = substr($classname,7);

        // default value
        if (!is_null($default_value)) {
            $this->default_value = $default_value;
        }

        // options
        if (isset($options['rules'])) {
            $this->addRules($options['rules']);
        }
        if (isset($options['attributes'])) {
            $this->attributes = array_merge($this->attributes, $options['attributes']);
        }

        $this->formulator = $formulator;

        if ($formulator->human_readable_labels) {
            $this->label = ucwords(str_replace('_',' ',$this->name));
        }
        else {
            $this->label = $this->name;
        }

        // Placeholder
        // elements without placeholder should unset it in their constructor
        if ($formulator->auto_placeholders) {
            $this->addAttribute(array('placeholder' => $this->label));
        }

    }



    /*************************************************************************/
    /*                          ELEMENT MANAGEMENT                          */
    /*************************************************************************/

    /**
     * Adds rule to the element
     *
     * @param   string      rule to add
     * @return  object
     */
    public function addRule($rule)
    {
        if (!is_array($rule)) {
            $rule = $this->formatStringRule($rule);
        }

        $this->rules = array_merge($this->rules, $rule);
        $this->computeSystemAttributes();
        return $this;
    }

    /**
     * Adds rules to the element
     *
     * @param   array       rules to add
     * @return  void
     */
    public function addRules($rules)
    {   
        // which array: values OR keys & values 
        if (isset($rules[0])) {
            $formated_rules = array();
            foreach($rules as $rule) {
                $formated_rules = array_merge($formated_rules, $this->formatStringRule($rule));
            }
        } else {
            $formated_rules = $rules;
        }

        $this->rules = array_merge($this->rules, $formated_rules);
        $this->computeSystemAttributes();
        return $this;
    }

    /**
     * Defines rule of the element (delete existing ones)
     *
     * @param   string      rule to set
     * @return  object
     */
    public function setRule($rule)
    {
        $this->rules = array();
        $this->addRule($rule);
        return $this;
    }

    /**
     * Defines rules of the element (delete existing ones)
     *
     * @param   array       rules to set
     * @return  void
     */
    public function setRules($rules)
    {
        $formated_rules = array();
        $this->addRules($rules);
        return $this;
    }

    /**
     * Deletes all rules of the element
     *
     * @return object
     */
    public function deleteRules()
    {
        $this->rules = array();
        $this->computeSystemAttributes();
        return $this;
    }

    /**
     * Formats the rule in case of a rule with parameters
     *
     * @return object
     */
    private function formatStringRule($rule)
    {
        // ignoring ',' for some rules
        $ignore = array('date');

        // do rule have parameter(s) ?
        $exp = explode(":", $rule, 2);

        if (!isset($exp[1])) {
            $rule_name = $rule;
            $rule_parameter = null;
        } else {
            $rule_name = $exp[0];
            $rule_parameter = $exp[1];

            // parameter array if ',' found
            if (strpos($rule_parameter, ',') AND !in_array($rule_name, $ignore)) {
                $rule_parameter = explode(",", $rule_parameter);
            }
        }

        return array($rule_name => $rule_parameter);
    }

    /**
     * Adds attribute to the element
     *
     * @param   mixed      attribute with(out) paramater
     * @return  object
     */
    public function addAttribute($attribute)
    {
        if(!is_array($attribute)) {
            if (preg_match('/\A\w+=".+"\z/', $attribute)) {
                preg_match('/\A\w+/', $attribute, $name);
                preg_match('/".+"\z/',$attribute, $value);
                $value = str_replace('"','', $value[0]);
                $attribute = array($name[0] => $value);
            } else {
                $attribute = array($attribute => null);
            }
        }
        $this->attributes = array_merge($this->attributes, $attribute);
        return $this;
    }

    /**
     * Defines attibutes of the element (delete existing ones)
     *
     * @param   array      attribute with paramater
     * @return  object
     */
    public function setAttribute($attribute)
    {
        $this->attributes = array();
        $this->addAttribute($attribute);
        return $this;
    }

    /**
     * Adds attribute to the element
     *
     * @param   array      attribute with paramater
     * @return  object
     */
    public function computeSystemAttributes()
    {
        // 1. attributes from object settings
        if ($this->disabled) {
            $new_attr = array('disabled' => null);
            $this->system_attributes = array_merge($new_attr, $this->system_attributes);
        }

        // 2. attributes from rules
        // required
        if (array_key_exists('required', $this->rules)) {
            $new_attr = array('required' => null);
            $this->system_attributes = array_merge($new_attr, $this->system_attributes);
        }

        // auto attibutes: lenght_between
        if (array_key_exists('length_between', $this->rules)) {
            $new_attr = array('maxlength' => $this->rules['length_between'][1]);
            $this->system_attributes = array_merge($new_attr, $this->system_attributes);
        }

        // auto attibutes: lenght_max
        if (array_key_exists('length_max', $this->rules)) {
            $new_attr = array('maxlength' => $this->rules['length_max']);
            $this->system_attributes = array_merge($new_attr, $this->system_attributes);
        }
    }



    /*************************************************************************/
    /*                            DATA STORAGE                               */
    /*************************************************************************/
    
    public function dataContent($row=0)
    {
        if (isset($this->data[$row]['content'])) {
            return $this->data[$row]['content'];
        }
        else {
            return false;
        }
    }

    public function dataValid($row=0)
    {
        if (isset($this->data[$row]['valid'])) {
            $result = $this->data[$row]['valid'];
        } else {
            $result = true;
        }

        return $result;
    }

    public function dataError($row=0)
    {
        if (isset($this->data[$row]['error'])) {
            $result = $this->data[$row]['error'];
        } else {
            $result = '';
        }

        return $result;
    }



    /*************************************************************************/
    /*                              Render                                   */
    /*************************************************************************/
    
    protected function renderElement($row = 0)
    {
        // attributes merging - user values override sys ones
        $this->attributes = array_merge($this->system_attributes, $this->attributes);

        $name = 'form['.$this->table.']['.$this->name.']['.$row.']';
        
        // value
        if ($this->dataContent($row)) {
            $value = $this->dataContent($row);
        } else {
            // default values in multirows -> string
            if (is_array($this->default_value)) {
                if (isset($this->default_value[$row])) {
                    $value = $this->default_value[$row];
                } else {
                    $value = '';
                }
            } else {
                $value = $this->default_value;
            }
        }

        return array('name' => $name, 'value' => $value);
    }

    // render operation, only label for now
    // $row is the row in the form)
    public function renderLabel($row = 0)
    {
        // Label processing
        $name = 'form['.$this->table.']['.$this->name.']['.$row.']';
        $output_label = '<label for="'.$name.'">'.$this->label.'</label>';

        return $output_label;
    }
}
