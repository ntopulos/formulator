<?php

/* pdo_to_elements array
 *
 * pdo native_type -> Formulator
 */

return array(

    'BLOB'          => [
                        'type'      => 'Textarea'
                       ],
    'DATE'          => [
                        'type'      => 'InputDate',
                        'rules'     => ['date']
                       ],
    'DATETIME'      => [
                        'type'      => 'InputDatetime',
                        'rules'     => ['date' => 'Y-m-d H:i:s']
                       ],
    'DOUBLE'        => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'FLOAT'         => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'INT24'         => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'INTEGER'       => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'LONG'          => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'LONGLONG'      => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'NEWDECIMAL'    => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'SHORT'         => [
                        'type'      => 'InputNumber',
                        'rules'     => ['numeric']
                       ],
    'STRING'        => [
                        'type'      => 'InputText'
                       ],
    'TIME'          => [
                        'type'      => 'InputTime',
                        'rules'     => ['date' => 'H:i:s']
                       ],
    'TIMESTAMP'     => [
                        'type'      => 'InputText',
                        'rules'     => ['numeric'],
                        'filters'   => ['date_to_timestamp']
                       ]

);
