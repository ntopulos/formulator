<?php
namespace ntopulos\formulator;

use ntopulos\formulator\elements as elements;

/**
 * Formulator (version 2013)
 *
 * This CI driver enables you to easily generate and validate forms.
 *
 * @author      Nikos Topulos
 */
class Formulator
{
    // Form tag basics
    public $name;                               // name of the form (eg: for hidden field)
    public $action = '';
    public $method = 'post';
    public $enctype = null;                     // no default enctype defined

    // Form configuration
    public $rows = 1;
    public $human_readable_labels = true;       // should the labels be more human readable ? (_=>space and camelized)
    public $auto_placeholders = false;          // automatically add labels values as placeholders
    public $submit_button = true;               // automatically add a submit button
    public $validation_messages = array();      // edited validation messages

    // Containers
    public $elements;                           // stdClass with all Element objects
    public $normal_buttons = array();           // array of buttons
    public $final_buttons = array();            // array of forms end buttons
    public $datalists = array();

    // Static
    static $name_increment = 0;                 // for multiple forms

    // Dev
    public $debug_mod = false;



    // ------------------------------------------------------------------------


    /**
     * Constructor, sets the name of the form ; loads libraries ; initializes some variables
     *
     * @param   array   $name       name of the form
     *                  $method     method of the form
     *                  $action 
     * @return  void
    */
    function __construct($params = array())
    {
        // Setting the name of the form
        // each form on the same page needs to have a different name so it is possible to know which form was sent.
        if (isset($params['name'])) {
            // if a name was given, we use that
            $this->name = $params['name'];
        }
        else {
            // if no name was given, we call the first form formulator_1, the second formulator_2, third formulator_3...
            $this->name = 'formulator_'.self::$name_increment;
            self::$name_increment++;
        }

        // Setting other variables
        if (isset($params['action'])) {
            $this->action = $params['action'];
        }
        if (isset($params['method'])) {
            $this->method = $params['method'];
        }
        if (isset($params['enctype'])) {
            $this->enctype = $params['enctype'];
        }

        // Setting up stdClass to store all Elements
        $this->elements = new \stdClass;
    }



    /*************************************************************************/
    /*                          ELEMENTS MANAGEMENT                          */
    /*************************************************************************/
    
    /**
     * Adds element
     *
     * @param   string      name of the element
     * @param   string      type of element (formulator syntax)
     * @param   array       parameters (key as parameter name)
     * @return  void
    */
    public function addElement($name, $type = 'InputText', $default_value = null, $table = 'root', $options = array())
    {
        // Creating element
        $classname = __NAMESPACE__ . '\elements\Element'.$type;
        $element = new $classname($name, $default_value, $table, $options, $this);

        if ($type=='InputFile') {
            $this->enctype = 'multipart/form-data';
        }

        // Adding to array
        if (!isset($this->elements->$table)) {
            $this->elements->$table = new \stdClass;
        }
        $this->elements->$table->$name = $element;
    }

    /**
     * Adds datalist
     *
     * @param   string      id of the list
     * @param   array       option of the list
     * @param   array       attributes
     * @return  void
    */
    public function addDatalist($id, $options, $attributes = array())
    {
        $this->datalists[$id] = array(
                                'options'       => $options,
                                'attributes'    => $attributes
                                );
    }

    /**
     * Adds button
     *
     * @param   string      name of the button
     * @param   string      value to be displayed
     * @param   array       parameters (key as parameter name)
     * @param   string      type (button, submit, reset)
     * @return  void
    */
    public function addButton($name, $value, $attributes=array(), $type='button', $is_final=true)
    {
        $array = ($is_final ? 'final_buttons' : 'normal_buttons');
        $this->{$array}[$name] = array(
                    'type' => $type,
                    'name' => $name,
                    'value' => $value)
                + $attributes;
    }



    /*************************************************************************/
    /*                      ASSISTED INTERACTIONS                            */
    /*************************************************************************/

    /**
     * Adds multiple elements from an array
     * Private class -> the next two are public
     *
     * @param   array       array with all elements
     * @param   array       array with types, rules and attributes
     * @param   string      name of the table
     * @param   boolean     should or not be populated
     * @return  void
    */
    private function addElementsFromArrayMeta($elements, $options, $table, $populate)
    {
        // Getting keys = column names
        // TODO : adapt for laravel too $elements direct without [0]
        $items = array_keys($elements[0]);

        // mono/multi row
        $this->rows = count($elements);

        // Associating types, rules and attributes and instanciating elements
        foreach ($items as $item => $item_value) {

            $column = $item_value;
            // type
            if (isset($options[$table][$column]['type'])) {
                $type = $options[$table][$column]['type'];
            } else {
                $type = 'InputText';
            }

            // value
            if ($populate) {
                // multitple rows
                for ($row = 0; $row < $this->rows; $row++) {
                    $default_value[$row] = $elements[$row][$column];
                }
            } else {
                $default_value = null;
            }

            // attribues
            if (isset($options[$table][$item_value])) {
                $item_options = $options[$table][$item_value];
            } else {
                $item_options = array();
            }

            // instanciating element
            $this->addElement($column, $type, $default_value, $table, $item_options);
        }
    }

    /**
     * Adds multiple elements from an array
     * without populating them
     *
     * @param   array       array with all elements
     * @param   array       array with types, rules and attributes
     * @param   string      name of the table
     * @return  void
    */
    public function addEmptyElementsFromArray($elements, $options = array(), $table = 'root')
    {
        $this->addElementsFromArrayMeta($elements, $options, $table, false);
    }

    /**
     * Adds multiple elements from an array
     * and populates them
     *
     * @param   array       array with all elements
     * @param   array       array with types, rules and attributes
     * @param   string      name of the table
     * @return  void
    */
    public function addElementsFromArray($elements, $options = array(), $table = 'root')
    {
        $this->addElementsFromArrayMeta($elements, $options, $table, true);
    }

    /**
     * Adds multiple elements from a query object
     *
     * @param   array       array with all elements
     * @param   array       array with types, rules and attributes
     * @param   boolean     should or not be populated
     * @return  void
    */
    private function addElementsFromQueryObjectMeta($object, $options, $populate)
    {
        $object_class = get_class($object);

        switch ($object_class) {

            case 'mysqli_result':
                $conversions = require('db_translations/mysqli_to_elements.php');
                $loop_on = $object->fetch_fields();

                // populating
                if ($populate) {
                    while ($row = $object->fetch_assoc()) {
                        $def_values[] = $row;
                    }
                }
               break;

            case 'PDOStatement':
                $conversions = require('db_translations/pdo_to_elements.php');
                $loop_on = range(0, $object->columnCount() - 1);

                // populating
                if ($populate) {
                    while ($row = $object->fetch(\PDO::FETCH_ASSOC)) {
                        $def_values[] = $row;
                    }
                }
                break;
           
            default:
               die('Formulator error: no supported database driver found.');
               break;
        }

        // populate
        if ($populate AND isset($def_values)) {
            $this->rows = count($def_values);
        }

        foreach ($loop_on as $value) {
            // obeject type related
            if ($object_class == 'PDOStatement') {
                $finfo = (object) $object->getColumnMeta($value);
                $finfo->type = (isset($conversions[$finfo->pdo_type]['type']) ? $conversions[$finfo->pdo_type]['type'] : '');
            } else{
                $finfo = $value;
            }

            // populating
            if ($populate AND isset($def_values)) {
                for ($row = 0; $row < $this->rows; $row++) {
                    $default_value[$row] = $def_values[$row][$finfo->name];
                }
            } else {
                $default_value = null;
            }

            // Element operation if not ignored one
            if (!isset($options[$finfo->table][$finfo->name]['ignore'])) {
                // not all conversions exist
                if (isset($conversions[$finfo->type])) {
                    $system_options = $conversions[$finfo->type];
                } else {
                    $system_options = array();
                }
                // not always defined options
                if (!isset($options[$finfo->table][$finfo->name])) {
                    $options[$finfo->table][$finfo->name] = array();
                }
                $merged = $this->mergeElementsOptions($system_options, $options[$finfo->table][$finfo->name]);
                $this->addElement($finfo->name, $merged['type'], $default_value, $finfo->table, $merged['options']);
            }
        }
    }

    /**
     * Adds multiple elements from a query object
     *
     * @param   array       array with all elements
     * @param   array       array with types, rules and attributes
     * @return  void
    */
    public function addElementsFromQueryObject($object, $options = array())
    {
        $this->addElementsFromQueryObjectMeta($object, $options, true);
    }

    /**
     * Adds multiple elements from a query object
     *
     * @param   array       array with all elements
     * @param   array       array with types, rules and attributes
     * @return  void
    */
    public function addEmptyElementsFromQueryObject($object, $options = array())
    {
        $this->addElementsFromQueryObjectMeta($object, $options, false);
    }

    /**
     * Adds multiple elements from a query object
     * will query the MySQL db in this purpose
     *
     * @param   object      Mysqli or PDO-MySQL object
     * @param   dbh         database handler (mysqli or pdo)
     * @param   array       options
     * @return  void
    */
    private function addElementsAdvancedMeta($object, $db_name, $dbh, $options, $populate)
    {
        $tables_columns = array();

        /* 1. Finding tables and columns */
        switch (get_class($object)) {

            case 'mysqli_result':
                $conversions = require('db_translations/mysqli_to_elements.php');
                $finfos = $object->fetch_fields();

                foreach ($finfos as $finfo) {
                    $tables_columns[$finfo->table][] = $finfo->name;
                }

                // populating
                if ($populate) {
                    while ($row = $object->fetch_assoc()) {
                        $def_values[] = $row;
                    }
                }
               break;

            case 'PDOStatement':
                $conversions = require('db_translations/pdo_to_elements.php');

                foreach (range(0, $object->columnCount() - 1) as $column_index) {
                    $finfo = $object->getColumnMeta($column_index);
                    $tables_columns[$finfo['table']][] = $finfo = $finfo['name'];
                }

                // populating
                if ($populate) {
                    while ($row = $object->fetch(\PDO::FETCH_ASSOC)) {
                        $def_values[] = $row;
                    }
                }
                break;
           
            default:
               die('Formulator error: no supported database driver found.');
               break;
        }

        // populate
        if ($populate AND isset($def_values)) {
            $this->rows = count($def_values);
        }


        /* 2. Generating queries, one per table */
        $queries = '';
        foreach ($tables_columns as $table => $columns) {
            $queries .= 'SELECT
                            `TABLE_NAME`,
                            `COLUMN_NAME`,
                            `DATA_TYPE`, `COLUMN_TYPE`,
                            `IS_NULLABLE`,
                            `CHARACTER_MAXIMUM_LENGTH`,
                            `CHARACTER_OCTET_LENGTH`,
                            `NUMERIC_PRECISION`, `NUMERIC_SCALE`,
                            `COLUMN_DEFAULT`
                        FROM
                            `COLUMNS`
                        WHERE 
                            `TABLE_SCHEMA` = \''.$db_name.'\'
                            AND
                            `TABLE_NAME` = \''.$table.'\'
                            AND
                            `COLUMN_NAME` IN (\''.
                                implode('\', \'',$columns).
                                '\');';
        }

        /* 3. Execute a multi-query */
        $db_schema = array();
        switch (get_class($object)) {

            case 'mysqli_result':
                $dbh->multi_query($queries);
                do {
                    $result = $dbh->store_result();
                    while ($row = $result->fetch_assoc()) {
                        $db_schema[$row['TABLE_NAME']][$row['COLUMN_NAME']] = $row;
                    }
                    $result->free();
                } while ($dbh->more_results() and $dbh->next_result());
                break;

            case 'PDOStatement':
                $stmt = $dbh->prepare($queries);
                $stmt->execute();
                while($row = $stmt->fetch(\PDO::FETCH_ASSOC)){
                    $db_schema[$row['TABLE_NAME']][$row['COLUMN_NAME']] = $row;
                }
                break;
        }


        /* 4. Translating and adding Elements */
        $conversions = require('db_translations/mysql_to_elements.php');

        // loop on tables
        foreach ($db_schema as $table => $columns) {
            // loop on columns
            foreach ($columns as $column => $col_schema) {
                if (!isset($options[$table][$column]['ignore'])) {
                    // not all conversions exist
                    $system_options = array();
                    if (isset($conversions[$col_schema['DATA_TYPE']])) {
                        $system_options = $conversions[$col_schema['DATA_TYPE']];
                    }
                    // rule: length_max
                    if (isset($col_schema['CHARACTER_MAXIMUM_LENGTH'])) {
                        $this_rule = array('length_max' => $col_schema['CHARACTER_MAXIMUM_LENGTH']);
                        if (isset($system_options['rules'])) {
                            $system_options['rules'] = array_merge($system_options['rules'], $this_rule);
                        } else {
                            $system_options['rules'] = $this_rule;
                        }
                    }
                    // not always defined options
                    if (!isset($options[$table][$column])) {
                        $options[$table][$column] = array();
                    }

                    // populating
                    if ($populate AND isset($def_values)) {
                        for ($row = 0; $row < $this->rows; $row++) {
                            $default_value[$row] = $def_values[$row][$column];
                        }
                    } else {
                        $default_value = null;
                    }

                    $merged = $this->mergeElementsOptions($system_options, $options[$table][$column]);
                    $this->addElement($column, $merged['type'], $default_value, $table, $merged['options']);
                }
            }
        }
    }

    /**
     * Adds multiple elements from a query object
     * will query the MySQL db in this purpose
     *
     * @param   object      Mysqli or PDO-MySQL object
     * @param   dbh         database handler (mysqli or pdo)
     * @param   array       options
     * @return  void
    */
    public function addElementsAdvanced($object, $db_name, $dbh, $options = array())
    {
        $this->addElementsAdvancedMeta($object, $db_name, $dbh, $options, true);
    }

    /**
     * Adds multiple elements from a query object
     * will query the MySQL db in this purpose
     *
     * @param   object      Mysqli or PDO-MySQL object
     * @param   dbh         database handler (mysqli or pdo)
     * @param   array       options
     * @return  void
    */
    public function addEmptyElementsAdvanced($object, $db_name, $dbh, $options = array())
    {
        $this->addElementsAdvancedMeta($object, $db_name, $dbh, $options, false);
    }

    /**
     * Merge options, defines default type and return type and options
     *
     * @param   array
     * @param   array
     * @return  array
    */
    private function mergeElementsOptions($system_options, $user_options)
    {
        $merged = array_merge($system_options, $user_options);

        // type
        if (isset($merged['type'])) {
            $type = $merged['type'];
            unset($merged['type']); // many others to unset
        } else {
            $type = 'InputText';
        }

        return array('type' => $type, 'options' => $merged);
    }



    /*************************************************************************/
    /*                          FORM SUBMISSION                              */
    /*************************************************************************/
        
    /**
     * Runs the form by populating with POST values (if necessary) and running the validation.
     * Returns true if sent AND valid, otherwise returns false
     *
     * @param   void
     * @return  boolean
    */
    public function compute()
    {
        global ${'_'.strtoupper($this->method)};
        $input = ${'_'.strtoupper($this->method)};

        // if the form is submitted
        if (isset($input[$this->name])) {
            $this->getInputData();
            return $this->validate();
        }
        else {
            return false;
        }
    }

    /**
     * Adds data from $_POST or $_GET
     *
     * @return  void
    */
    private function getInputData()
    {
        global ${'_'.strtoupper($this->method)};
        $input = ${'_'.strtoupper($this->method)};

        // loop on form rows
        for ($row=0; $row < $this->rows; $row++) {
            // Loop on object elements tables
            foreach ($this->elements as $table => $items) {
                // Loop on object elements
                foreach ($items as $item) {
                    if (isset($input['form'][$table][$item->name][$row])) {
                        $value = $input['form'][$table][$item->name][$row];
                    } else {
                        $value = '';

                        // checkboxes
                        if (get_class($item) == 'ElementCheckboxes') {
                            $value = false;
                        }
                    }
                    $this->elements->{$table}->{$item->name}->data[$row]['content'] = $value;
                }
            }
        }
    }

    /**
     * Validates data calling Validation class
     *
     * @return  void
    */
    private function validate()
    {
        global ${'_'.strtoupper($this->method)};
        $input = ${'_'.strtoupper($this->method)};

        // messages
        $validation_msg = require('validation_messages.php');
        if (!empty($this->validation_messages)) {
            $validation_msg = array_merge($validation_msg, $this->validation_messages);
        }

        // loop on form rows
        for ($row=0; $row < $this->rows; $row++) {
            // Loop on object elements tables
            foreach ($this->elements as $table => $items) {
                // Loop on object elements
                foreach ($items as $item) {

                    if (isset($input['form'][$table][$item->name][$row])) {
                        $value = $input['form'][$table][$item->name][$row];
                    } else {
                        $value = '';

                        // checkboxes
                        if (get_class($item) == 'ElementCheckboxes') {
                            $value = false;
                        }
                    }

                    // Validation

                    // validation only in case of: required OR not empty input
                    // otherwise element is valid
                    $is_valid = true;

                    // Merging with system rules before validation
                    $rules = array_merge($item->rules, $item->type_inherited_rules);

                    if (array_key_exists('required', $rules)
                        OR array_key_exists('matches_element', $rules)
                        OR ( is_string($value) && strlen($value) > 0)) {

                        // loop on all rules -> all must bee valid to get valid element
                        foreach ($rules as $rule => $params) {
                            if (is_null($params)) {
                                $is_valid = Rules::$rule($value);
                            } else {
                                $is_valid = Rules::$rule($value, $params);
                            }

                            if (!$is_valid) {
                                $item->data[$row]['error'] = $this->parseValidationMessage($item->label, $validation_msg[$rule], $params);

                                // stoping at the first error
                                break;
                            }
                        }
                    }

                    $item->data[$row]['valid'] = $is_valid;
                }
            }

        }
    }

    /**
     * Parsing validation messages (placeholders of the rules)
     *
     * @param   string  $input
     * @return  bool
    */
    private function parseValidationMessage($attribute, $msg, $rule_params)
    {
        // do rule have parameter(s) ?
        if ($rule_params) {
            if (is_array($rule_params)) {
                $i = 0;
                foreach ($rule_params as $val) {
                    $msg = str_replace('%'.$i, $rule_params[$i], $msg);
                    $i++;
                }
            } else {
                $msg = str_replace('%0', $rule_params, $msg);
            }
        }

        // attribute
        $msg = str_replace('%label', $attribute, $msg);

        return $msg;
    }



    /*************************************************************************/
    /*                          FORM RENDERING                               */
    /*************************************************************************/

    /**
     * Returns the whole form as an view string using the views in views/formulator/
     *
     * @access  public
     * @param   boolean     false: outputs array, true: pseudoTemplate
     * @return  mixed       resulting array or string
    */
    public function render($pseudoTemplate=false)
    {
        // GENERAL 
        $render['rows'] = $this->rows;

        // OPEN
        $form_attr = array(
            'action' => $this->action,
            'method' => $this->method
            );
        if ($this->enctype) {
            $form_attr['enctype'] = $this->enctype;
        }
        $render['open'] = Render::openTag('form', $form_attr);
        
        // ELEMENTS
        $render['elements'] = $this->elements;

        // BUTTONS
        $render['normal_buttons'] = $this->renderNormalButtons();
        $render['final_buttons'] = $this->renderFinalButtons();

        // CLOSE (datalists + close tag)
        $render['close'] = $this->renderDatalists() . '<input type="hidden" name="'.$this->name.'" value="1"></form>';

        // DEBUG
        $render['debug'] = ($this->debug_mod ? $this->debug() : '');

        // VIEW GENERATION
        if ($pseudoTemplate==false) {
            return $render;
        } else {
            $view = ($this->rows > 1 ? 'multirow' : 'monorow');
            $path = '../views/'.$view.'.php';

            if (file_exists($path)) {
                // Into buffer
                ob_start();
                extract($render);
                require '../views/'.$view.'.php';
                $view = ob_get_clean();

                return $view;
            } else {
                return 'File "'.$path.'" not found !<br>';
            }
        }
        
    }

    // buttons for render()
    // in this case we return a string, there is no reason for an array
    private function renderDatalists()
    {
        $datalists = '';

        foreach ($this->datalists as $id => $val) {
            $datalists .= Render::datalist($id, $val);
        }

        return $datalists;
    }

    // buttons for render()
    private function renderFinalButtons()
    {
        $buttons = array();

        foreach ($this->final_buttons as $name => $button_data) {
            $buttons[$name] = Render::button($button_data);
        }

        if ($this->submit_button) {
            $buttons[] = '<input type="submit">';
        }

        return $buttons;
    }

    // buttons for render()
    private function renderNormalButtons()
    {
        $buttons = array();
        foreach ($this->normal_buttons as $button_data => $name) {
            $buttons[$name] = Render::button($button_data);
        }

        return $buttons;
    }



    /*************************************************************************/
    /*                          ! developement !                             */
    /*************************************************************************/

    public function debug() {

        $both_style = 'margin: 0; padding: 20px;';
        $h1_style = $both_style . 'background-color: #272727; color: #fff';
        $pre_style = $both_style . 'line-height: 1.2em; background-color: #333; color: #ccc; margin-bottom: 2em;';

        $result = '<div class="formulator_debug"><h1 style="'.$h1_style.'">Formulator debug</h1><pre style="'.$pre_style.'">'.print_r($this,true).'</pre></div>';

        return $result;
    }

}
