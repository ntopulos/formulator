<?php

/* mysql_to_elements array
 *
 * mysql data type -> Formulator
 *
 */

return array(

    // Numeric data types
    'int'           => [
                        'type'      => 'InputNumber',
                        'rules'     => ['integer']
                       ],
    'tinyint'       => [
                        'type'      => 'InputNumber',
                        'rules'     => ['integer']
                       ],
    'smallint'      => [
                        'type'      => 'InputNumber',
                        'rules'     => ['integer']
                       ],
    'mediumint'     => [
                        'type'      => 'InputNumber',
                        'rules'     => ['integer']
                       ],
    'bigint'        => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'float'         => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'double'        => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'decimal'       => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],

    // Date and time types
    'date'          => [
                        'type'      => 'InputDate',
                        'rules'     => ['date']
                       ],
    'datetime'      => [
                        'type'      => 'InputDatetime',
                        'rules'     => ['date' => 'Y-m-d H:i:s']
                       ],
    'timestamp'     => [
                        'type'      => 'InputText',
                        'rules'     => ['numeric'],
                        'filters'   => ['date_to_timestamp'],
                       ],
    'time'          => [
                        'type'      => 'InputTime',
                        'rules'     => ['date' => 'H:i:s']
                       ],
    'year'          => [
                        'type'      => 'InputNumber',
                        'rules'     => ['date' => 'Y']
                       ],

    // String types
    'char'          => [
                        'type'      => 'InputText',
                       ],
    'varchar'       => [
                        'type'      => 'InputText',
                       ],
    'blob'          => [
                        'type'      => 'Textarea',
                       ],
    'tinyblob'      => [
                        'type'      => 'Textarea',
                       ],
    'mediumblob'    => [
                        'type'      => 'Textarea',
                       ],
    'longblob'      => [
                        'type'      => 'Textarea',
                       ],
    'text'          => [
                        'type'      => 'Textarea',
                       ],
    'tinytext'      => [
                        'type'      => 'Textarea',
                       ],
    'mediumtext'    => [
                        'type'      => 'Textarea',
                       ],
    'longtext'      => [
                        'type'      => 'Textarea',
                       ],
    'enum'          => [
                        'type'      => 'InputText',
                       ],
    // set (multiple enum)

);
