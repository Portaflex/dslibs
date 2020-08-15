<?php

/**
 * Esta es la clase implementa el modelo de facturas.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 * 
 * @var $total integer
 */

namespace dslibs\clinica\admin\models;

use dslibs\admin\models\MenuModel;
use yii\db\Query;

class FacturaModel extends AdminModel
{
    public $total;
    public $cobro;
    public $declarado;
    
    const SCENARIO_ADMIN = 'admin';
    
    public static function tableName()
    {
        return 'factura';
    }
    
    /* public function attributes()
    {
        return array_merge(parent::attributes(), ['estado']);
    } */

    public function rules()
    {
        return [
            [['fact_ano', 'fact_num', 'fact_epis_id', 'fact_pac_id', 'fact_empresa_id', 'fact_estado'], 'integer'],
            [['fact_pagador', 'fact_observacion'], 'string'],
            [['fact_fecha', 'fact_cobro','fact_epis_id', 'fact_pac_id', 'fact_userlogin', 'cobro', 'fact_presentada',
                'declarado'], 'safe'],
            [['fact_userlogin'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fact_id' => 'Fact ID',
            'fact_ano' => 'Año',
            'fact_num' => 'Número',
            'fact_epis_id' => 'Fact Epis ID',
            'fact_pac_id' => 'Fact Pac ID',
            'fact_pagador' => 'Pagador',
            'fact_empresa_id' => 'Empresa que emite la factura',
            'fact_estado' => 'Estado',
            'fact_fecha' => 'Fecha de la factura',
            'fact_cobro' => 'Indica si está cobrada o no',
            'fact_fdc' => 'Fecha',
            'fact_fdu' => 'Fact Fdu',
            'fact_userlogin' => 'Fact Userlogin',
            'fact_observacion' => 'Fact Observacion',
            'fact_presentada' => 'Fact Presentada',
            'cobro' => 'Cobro',
            'declarado' => 'Declarado'
        ];
    }
    
    public function getLineas ()
    {
        return (new Query())->from('factura_linea')->where(['fl_factura_id' => $this->fact_id])->all();
    }
    
    public function getTotal ()
    {
        foreach ($this->getLineas() as $linea)
        {
            $this->total += $linea['fl_precio'];
        }
        return $this->total;
    }
    
    public function getEstado ()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'fact_estado'])->where(['e.m_ident' => 'estado_cobro']);
    }
    
    public function getPresentado ()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'fact_presentada'])->where(['p.m_ident' => 'estado_presentado']);
    }
}
