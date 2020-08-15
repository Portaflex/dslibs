<?php

/**
 * Esta es la clase implementa el modelo de linea de factura.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

class FacturaLineaModel extends AdminModel
{
    public static function tableName()
    {
        return 'factura_linea';
    }

    public function rules()
    {
        return [
            [['fl_factura_id', 'fl_albaran_id'], 'integer'],
            [['fl_precio'], 'number'],
            [['fl_fdc'], 'safe'],
            [['fl_concepto', 'fl_userlogin'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fl_id' => 'Fl ID',
            'fl_factura_id' => 'Fl Factura ID',
            'fl_concepto' => 'Fl Concepto',
            'fl_precio' => 'Fl Precio',
            'fl_userlogin' => 'Fl Userlogin',
            'fl_fdc' => 'Fl Fdc',
            'fl_albaran_id' => 'Fl Albaran ID',
        ];
    }
}
