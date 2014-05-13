<?php

/* mysqli_to_elements array
 *
 * mysqli type ids -> Formulator
 * source of the translation: get_defined_constants(true)
 *
 * driver_type: type defined by mysqli driver
 * type: type of formulator element
 * rules: default set of rules to add to the element
 * filters: set of filters to add to the element
 */

return array(

        0   => array(
                    'driver_type'   => 'DECIMAL',
                    'type'          => 'InputNumber',
                    'rules'         => array('numeric')
                ),
        1   => array(
                    'driver_type'   => 'CHAR',
                    'type'          => 'InputNumber',
                    'rules'         => array('numeric')
                ),
        2   => array(
                    'driver_type'   => 'SHORT',
                    'type'          => 'InputNumber',
                    'rules'         => array('numeric')
                ),
        3   => array(
                    'driver_type'   => 'LONG',
                    'type'          => 'InputNumber',
                    'rules'         => array('numeric')
                ),
        4   => array(
                    'driver_type'   => 'FLOAT',
                    'type'          => 'InputNumber',
                    'rules'         => array('numeric')
                ),
        5   => array(
                    'driver_type'   => 'DOUBLE',
                    'type'          => 'InputNumber',
                    'rules'         => array('numeric')
                ),
        7   => array(  
                    'driver_type'   => 'TIMESTAMP',
                    'type'          => 'InputText',
                    'rules'         => array('numeric'),
                    'filters'       => array('date_to_timestamp'),
                ),
        8   => array(  
                    'driver_type'   => 'LONGLONG',
                    'type'          => 'InputNumber',
                    'rules'         => array('numeric')
                ),
        9   => array(  
                    'driver_type'   => 'INT24',
                    'type'          => 'InputNumber',
                    'rules'         => array('numeric')
                ),
        10  => array(  
                    'driver_type'   => 'DATE',
                    'type'          => 'InputDate',
                    'rules'         => array('date')
                ),
        11  => array(  
                    'driver_type'   => 'TIME',
                    'type'          => 'InputTime',
                    'rules'         => array('date' => 'H:i:s')
                ),
        12  => array(  
                    'driver_type'   => 'DATETIME',
                    'type'          => 'InputDatetime',
                    'rules'         => array('date' => 'Y-m-d H:i:s')
                ),
        13  => array(  
                    'driver_type'   => 'YEAR',
                    'type'          => 'InputNumber',
                    'rules'         => array('date' => 'Y')
                ),
        14  => array(  
                    'driver_type'   => 'NEWDATE',
                    'type'          => 'InputDate',
                    'rules'         => array('date' => 'Y-m-d H:i:s')
                ),
        249 => array(  
                    'driver_type'   => 'TINY_BLOB',
                    'type'          => 'Textarea'
                ),
        250 => array(  
                    'driver_type'   => 'MEDIUM_BLOB',
                    'type'          => 'Textarea'
                ),
        251 => array(  
                    'driver_type'   => 'LONG_BLOB',
                    'type'          => 'Textarea'
                ),
        252 => array(  
                    'driver_type'   => 'BLOB',
                    'type'          => 'Textarea'
                ),
        253 => array(  
                    'driver_type'   => 'VAR_STRING',
                    'type'          => 'InputText'
                ),
        246 => array(  
                    'driver_type'   => 'NEWDECIMAL',
                    'type'          => 'InputNumber',
                    'rules'         => array('numeric')
                )
    );
