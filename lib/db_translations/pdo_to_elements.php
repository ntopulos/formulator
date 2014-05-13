<?php

/* pdo_to_elements array
 *
 * pdo native_type -> Formulator
 */

return array(

        'BLOB'          => array(
                                'type'      => 'Textarea'
                            ),
        'DATE'          => array(
                                'type'      => 'InputDate',
                                'rules'     => array('date')
                            ),
        'DATETIME'      => array(
                                'type'      => 'InputDatetime',
                                'rules'     => array('date' => 'Y-m-d H:i:s')
                            ),
        'DOUBLE'        => array(
                                'type'      => 'InputNumber',
                                'rules'     => array('numeric')
                            ),
        'FLOAT'         => array(
                                'type'      => 'InputNumber',
                                'rules'     => array('numeric')
                            ),
        'INT24'         => array(
                                'type'      => 'InputNumber',
                                'rules'     => array('numeric')
                            ),
        'INTEGER'       => array(
                                'type'      => 'InputNumber',
                                'rules'     => array('numeric')
                            ),
        'LONG'          => array(
                                'type'      => 'InputNumber',
                                'rules'     => array('numeric')
                            ),
        'LONGLONG'      => array(
                                'type'      => 'InputNumber',
                                'rules'     => array('numeric')
                            ),
        'NEWDECIMAL'    => array(
                                'type'      => 'InputNumber',
                                'rules'     => array('numeric')
                            ),
        'SHORT'         => array(
                                'type'      => 'InputNumber',
                                'rules'     => array('numeric')
                            ),
        'STRING'        => array(
                                'type'      => 'InputText'
                            ),
        'TIME'          => array(
                                'type'      => 'InputTime',
                                'rules'     => array('date' => 'H:i:s')
                            ),
        'TIMESTAMP'     => array(
                                'type'      => 'InputText',
                                'rules'     => array('numeric'),
                                'filters'   => array('date_to_timestamp')
                            )
    );
