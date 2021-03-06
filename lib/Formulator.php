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
    public $action = '#';
    public $method = 'post';
    public $enctype = null;                     // no default enctype defined

    // Form configuration
    public $rows = 1;
    public $human_readable_labels = true;       // should the labels be more human readable ? (_=>space and camelized)
    public $auto_placeholders = false;          // automatically add labels values as placeholders
    public $submit_button = true;               // automatically add a submit button
    public $global_validation = false;          // one global validation message
    public $validation_messages = array();      // edited validation messages
    public $validation_extended = array();      // user registered rules

    // Containers
    public $elements;                           // stdClass with all Element objects
    public $buttons = array();                  // array of buttons
    public $datalists = array();

    // From status
    private $is_valid = true;

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
    public function addButton($name, $value, $attributes=array(), $type='button')
    {
        $this->buttons[$name] = array(
                    'type' => $type,
                    'name' => $name,
                    'value' => $value)
                + $attributes;
    }



    /*************************************************************************/
    /*                      AUTOMATIC INTERACTIONS                           */
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
    private function addElementsAdvancedMeta($object, $dbh, $options, $populate)
    {
        $tables_columns = array();

        /* 1. Finding db, tables and columns */
        switch (get_class($object)) {

            case 'mysqli_result':
                /* current database */
                $result = $dbh->query('SELECT DATABASE()');
                $db_name = $result->fetch_row()[0];
                $result->close();
                
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
                /* current database */
                $stmt = $dbh->query('SELECT DATABASE()');
                $db_name = $stmt->fetch(\PDO::FETCH_NUM)[0];

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
                // switch db
                $dbh->select_db('information_schema');

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
                $dbh->query('use information_schema');
                $stmt = $dbh->query($queries);
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
    public function addElementsAdvanced($object, $dbh, $options = array())
    {
        $this->addElementsAdvancedMeta($object, $dbh, $options, true);
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
    public function addEmptyElementsAdvanced($object, $dbh, $options = array())
    {
        $this->addElementsAdvancedMeta($object, $dbh, $options, false);
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
    /*                              EXTENDING                                */
    /*************************************************************************/

    /**
     * Adds custom rule to the instance.
     *
     * @param   void
     * @return  boolean
    */
    public function registerRule($name, $closure, $message = null)
    {
        $this->validation_extended[$name] = $closure;
        if (!is_null($message)) {
            $this->validation_messages[$name] = $message;
        }
    }



    /*************************************************************************/
    /*                          FORM SUBMISSION                              */
    /*************************************************************************/

    /**
     * Checks if the form has been submitted
     *
     * @param   void
     * @return  boolean
    */
    public function submitted()
    {
        global ${'_'.strtoupper($this->method)};
        $input = ${'_'.strtoupper($this->method)};

        if (isset($input[$this->name])) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Populates the elements with data from POST or GET
     * Returns true if sent AND valid, otherwise returns false
     *
     * @param   void
     * @return  boolean
    */
    public function validate()
    {
        if ($this->submitted()) {
            $this->getInputData();
            return $this->validateGlobal();
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
     * Validates all data calling Validation class
     *
     * @return  void
    */
    private function validateGlobal()
    {
        global ${'_'.strtoupper($this->method)};
        $input = ${'_'.strtoupper($this->method)};

        // messages
        $validation_msg = require('validation_messages.php');
        $this->validation_messages = array_merge($validation_msg, $this->validation_messages);

        // loop on form rows
        $valid_form = true;
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
                            $is_valid = $this->validateSingleItem($value, $rule, $params);

                            if (!$is_valid && !$this->global_validation) {
                                $item->data[$row]['error'] = $this->parseValidationMessage($item->label, $this->validation_messages[$rule], $params);
                                break;  // stoping at the first error
                            }
                        }
                    }

                    $item->data[$row]['valid'] = $is_valid;

                    if (!$is_valid) {
                        $valid_form = false;
                    }
                }
            }

        }

        $this->is_valid = $valid_form;
        return $valid_form;
    }

    /**
     * Validates single item with extend and normal rules.
     *
     * @param   string
     * @param   string
     * @param   array   ?
     * @return  bool
    */
    private function validateSingleItem($value, $rule, $params)
    {
        if (array_key_exists($rule, $this->validation_extended)) {
            $call = $this->validation_extended[$rule];
        } else {
            $call = __NAMESPACE__.'\Rules::'.$rule;
        }

        if (is_null($params)) {
            $is_valid = call_user_func($call, $value);
        } else {
            $is_valid = call_user_func_array($call, array($value, $params));
        }

        return $is_valid;
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
     * Returns the whole form as arrays
     *
     * @access  public
     * @param   boolean     false: outputs array, true: pseudoTemplate
     * @return  mixed       resulting array or string
    */
    public function render()
    {
        // GENERAL 
        $render['rows'] = $this->rows;

        // GLOBAL VALIDATION
        $render['global_validation'] = $this->global_validation;
        if (!$this->is_valid && $this->global_validation) {
            $render['global_error'] = $this->validation_messages['global'];
        } else {
            $render['global_error'] = false;
        }

        // OPEN (+ datalists + hidden form name)
        $form_attr = array(
            'action' => $this->action,
            'method' => $this->method
            );
        if ($this->enctype) {
            $form_attr['enctype'] = $this->enctype;
        }

        $render['open'] = Render::openTag('form', $form_attr) .
                        $this->renderDatalists() .
                        '<input type="hidden" name="'.$this->name.'" value="1">';
        
        // ELEMENTS
        $render['elements'] = $this->elements;

        // BUTTONS
        $render['buttons'] = $this->renderButtons();

        // DEBUG
        $render['debug'] = ($this->debug_mod ? $this->debug() : '');

        // OUTPUT
        return $render;
    }

    /**
     * Returns the whole form as a view string
     * using the views in views/formulator/
     *
     * @access  public
     * @param   boolean     false: outputs array, true: pseudoTemplate
     * @return  mixed       resulting array or string
    */
    public function renderTemplate()
    {
        $view = ($this->rows > 1 ? 'multirow' : 'monorow');
        $path = __DIR__.'/../views/'.$view.'.php';

        if (file_exists($path)) {
            // Into buffer
            ob_start();
            extract($this->render());
            require $path;
            $view = ob_get_clean();

            return $view;
        } else {
            return 'File "'.$path.'" not found !<br>';
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
    private function renderButtons()
    {
        $buttons = array();

        foreach ($this->buttons as $name => $button_data) {
            $buttons[$name] = Render::button($button_data);
        }

        if ($this->submit_button) {
            $buttons[] = '<input type="submit">';
        }

        return $buttons;
    }



    /*************************************************************************/
    /*                          ! development !                              */
    /*************************************************************************/

    public function debug() {

        $both_style = 'margin: 0; padding: 20px;';
        $h1_style = $both_style . 'background-color: #272727; color: #fff';
        $pre_style = $both_style . 'line-height: 1.2em; background-color: #333; color: #ccc; margin-bottom: 2em;';

        $result = '<div class="formulator_debug"><h1 style="'.$h1_style.'">Formulator debug</h1><pre style="'.$pre_style.'">'.print_r($this,true).'</pre></div>';

        return $result;
    }

}
