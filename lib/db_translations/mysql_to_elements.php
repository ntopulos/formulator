<?php

/* mysql_to_elements array
 *
 * mysql data type -> Formulator
 *
 */

return array(

    // Numeric data types
    'int'           => array(
                            'type'      => 'InputNumber',
                            'rules'     => array('integer')
                        ),
    'tinyint'       => array(
                            'type'      => 'InputNumber',
                            'rules'     => array('integer')
                        ),
    'smallint'      => array(
                            'type'      => 'InputNumber',
                            'rules'     => array('integer')
                        ),
    'mediumint'     => array(
                            'type'      => 'InputNumber',
                            'rules'     => array('integer')
                        ),
    'bigint'        => array(
                            'type'      => 'InputNumber',
                            'rules'     => array('numeric')
                        ),
    'float'         => array(
                            'type'      => 'InputNumber',
                            'rules'     => array('numeric')
                        ),
    'double'        => array(
                            'type'      => 'InputNumber',
                            'rules'     => array('numeric')
                        ),
    'decimal'       => array(
                            'type'      => 'InputNumber',
                            'rules'     => array('numeric')
                        ),

    // Date and time types
    'date'          => array(
                            'type'      => 'InputDate',
                            'rules'     => array('date')
                        ),
    'datetime'      => array(
                            'type'      => 'InputDatetime',
                            'rules'     => array('date' => 'Y-m-d H:i:s')
                        ),
    'timestamp'     => array(
                            'type'      => 'InputText',
                            'rules'     => array('numeric'),
                            'filters'   => array('date_to_timestamp'),
                        ),
    'time'          => array(
                            'type'      => 'InputTime',
                            'rules'     => array('date' => 'H:i:s')
                        ),
    'year'          => array(
                            'type'      => 'InputNumber',
                            'rules'     => array('date' => 'Y')
                        ),

    // String types
    'char'          => array(
                            'type'      => 'InputText',
                        ),
    'varchar'       => array(
                            'type'      => 'InputText',
                        ),
    'blob'          => array(
                            'type'      => 'Textarea',
                        ),
    'tinyblob'      => array(
                            'type'      => 'Textarea',
                        ),
    'mediumblob'    => array(
                            'type'      => 'Textarea',
                        ),
    'longblob'      => array(
                            'type'      => 'Textarea',
                        ),
    'text'          => array(
                            'type'      => 'Textarea',
                        ),
    'tinytext'      => array(
                            'type'      => 'Textarea',
                        ),
    'mediumtext'    => array(
                            'type'      => 'Textarea',
                        ),
    'longtext'      => array(
                            'type'      => 'Textarea',
                        ),
    'enum'          => array(
                            'type'      => 'InputText',
                        ),
    // set (multiple enum)

    );