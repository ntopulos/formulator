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

    0   => [
            'driver_type'   => 'DECIMAL',
            'type'          => 'InputNumber',
            'rules'         => ['numeric']
           ],
    1   => [
            'driver_type'   => 'CHAR',
            'type'          => 'InputNumber',
            'rules'         => ['numeric']
           ],
    2   => [
            'driver_type'   => 'SHORT',
            'type'          => 'InputNumber',
            'rules'         => ['numeric']
           ],
    3   => [
            'driver_type'   => 'LONG',
            'type'          => 'InputNumber',
            'rules'         => ['numeric']
           ],
    4   => [
            'driver_type'   => 'FLOAT',
            'type'          => 'InputNumber',
            'rules'         => ['numeric']
           ],
    5   => [
            'driver_type'   => 'DOUBLE',
            'type'          => 'InputNumber',
            'rules'         => ['numeric']
           ],
    7   => [  
            'driver_type'   => 'TIMESTAMP',
            'type'          => 'InputText',
            'rules'         => ['numeric'],
            'filters'       => ['date_to_timestamp'],
           ],
    8   => [  
            'driver_type'   => 'LONGLONG',
            'type'          => 'InputNumber',
            'rules'         => ['numeric']
           ],
    9   => [  
            'driver_type'   => 'INT24',
            'type'          => 'InputNumber',
            'rules'         => ['numeric']
           ],
    10  => [  
            'driver_type'   => 'DATE',
            'type'          => 'InputDate',
            'rules'         => ['date']
           ],
    11  => [  
            'driver_type'   => 'TIME',
            'type'          => 'InputTime',
            'rules'         => ['date' => 'H:i:s']
           ],
    12  => [  
            'driver_type'   => 'DATETIME',
            'type'          => 'InputDatetime',
            'rules'         => ['date' => 'Y-m-d H:i:s']
           ],
    13  => [  
            'driver_type'   => 'YEAR',
            'type'          => 'InputNumber',
            'rules'         => ['date' => 'Y']
           ],
    14  => [  
            'driver_type'   => 'NEWDATE',
            'type'          => 'InputDate',
            'rules'         => ['date' => 'Y-m-d H:i:s']
           ],
    249 => [  
            'driver_type'   => 'TINY_BLOB',
            'type'          => 'Textarea'
           ],
    250 => [  
            'driver_type'   => 'MEDIUM_BLOB',
            'type'          => 'Textarea'
           ],
    251 => [  
            'driver_type'   => 'LONG_BLOB',
            'type'          => 'Textarea'
           ],
    252 => [  
            'driver_type'   => 'BLOB',
            'type'          => 'Textarea'
           ],
    253 => [  
            'driver_type'   => 'VAR_STRING',
            'type'          => 'InputText'
           ],
    246 => [  
            'driver_type'   => 'NEWDECIMAL',
            'type'          => 'InputNumber',
            'rules'         => ['numeric']
           ]

);
