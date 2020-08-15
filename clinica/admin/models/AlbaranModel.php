<?php

/**
 * Esta es la clase implementa el modelo albarán. Está ubicada en la
 * sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use dslibs\admin\models\MenuModel;
use yii\db\Query;

class AlbaranModel extends AdminModel
{
    public static function tableName()
    {
        return 'albaran';
    }

    public function formName()
    {
    	return '';
    }

    public function attributes()
    {
    	// add related fields to searchable attributes
    	return array_merge(parent::attributes(), [
    			'paciente', 'financiador', 'finan_id', 'sanitario', 'sani_id',
    			'estado', 'acto', 'pago', 'fa_1', 'fa_2', 'iq_diagnostico', 'rol_tipo',
    			]);
    }

    public function rules()
    {
        return [
            //[['a_fact_id', 'a_pac_id', 'a_epis_id'], 'required'],
            [['a_id', 'a_fact_id', 'a_pac_id', 'a_epis_id', 'a_acto', 'a_cita_id',
                'a_iq_id', 'a_estado', 'a_pago'], 'integer'],
            [['a_presentado', 'a_fecha_acto', 'a_fdc', 'a_fdu', 'a_pac_id', 'a_epis_id',
                'a_userlogin', 'a_precio', 'a_facturado', 'a_transaccion'], 'safe'],
            //[['a_precio'], 'number'],
            [['a_concepto', 'a_transaccion'], 'string', 'max' => 255],
            [['a_userlogin'], 'string', 'max' => 45],
        	[['paciente', 'finandiador', 'sanitario'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'a_id' => 'Fd ID',
            'a_fact_id' => 'Factura',
            'a_pac_id' => 'Paciente ID',
            'a_epis_id' => 'Episodio ID',
            'a_acto' => 'Acto',
            'a_cita_id' => 'Cita ID',
            'a_iq_id' => 'Iq ID',
            'a_concepto' => 'Concepto',
            'a_presentado' => 'Presentado',
            'a_estado' => 'Cobro',
            'a_pago' => 'Pago',
            'a_precio' => 'Precio (€)',
            'a_fecha_acto' => 'Fecha',
            'a_fdc' => 'Fdc',
            'a_fdu' => 'Fdu',
            'a_userlogin' => 'Userlogin',
        	'sanitario' => 'Sanitario',
        	'acto' => 'Acto',
        	'paciente' => 'Paciente',
        	'financiador' => 'Financiador',
        	'iq_diagnostico' => 'Diagnóstico',
            'a_facturado' => 'Facturado',
            'a_transaccion' => 'Transacción'
        ];
    }

    public static function v_albaran()
    {
    	$q = (new Query())->select([
    		"a.*", 'e.*',
    		"fn.finan_empresa as financiador", 'fn.finan_id',
    		"concat_ws(' ', p.pac_nom,p.pac_apell1, p.pac_apell2) AS paciente", "p.pac_id",
    		'm1.m_texto as estado', 'm2.m_texto as pago', 'm3.m_texto as acto',
    		"s.sani_apellido1 as sanitario", 's.sani_id'])
    		->from('albaran a')
    		->leftJoin('episodio e', 'a.a_epis_id = e.epis_id')
    		->leftJoin('financiador fn', 'e.epis_finan_id = fn.finan_id')
    		->leftJoin('paciente p', 'a.a_pac_id = p.pac_id')
    		->leftJoin('menu m1', "a.a_estado = m1.m_valor and m1.m_ident = 'estado_cobro'")
    		->leftJoin('menu m2', "a.a_pago = m2.m_valor and m2.m_ident = 'estado_pago'")
    		->leftJoin('menu m3', "a.a_acto = m3.m_valor and m3.m_ident = 'tipo_albaran'")
    		->leftJoin('cita c', 'a.a_cita_id = c.cita_id')
    		->leftJoin('sanitario s', 'c.cita_sani_id = s.sani_id')
    		->andWhere(['>', 'a_fecha_acto', '01-01-2018'])
    		->andWhere(['IS NOT', 'a_cita_id', null]);
    	return $q;
    }



    public static function v_albaran_iq()
    {
    	$q = (new Query())->select([
    		"a.*", 'iq.*', 'e.*',
    		"fn.finan_empresa as financiador", 'fn.finan_id', 'il.iqrol_precio',
    		"concat_ws(' ', s.sani_nombres, s.sani_apellido1) as sanitario", 'm4.m_texto as rol',
    		"concat_ws(' ', p.pac_nom,p.pac_apell1, p.pac_apell2) AS paciente", "p.pac_id",
    		'm1.m_texto as estado', 'm2.m_texto as pago', 'm3.m_texto as acto'])
    		->from('albaran a')
    		->leftJoin('episodio e', 'a.a_epis_id = e.epis_id')
    		->leftJoin('financiador fn', 'e.epis_finan_id = fn.finan_id')
    		->leftJoin('paciente p', 'a.a_pac_id = p.pac_id')
    		->leftJoin('menu m1', "a.a_estado = m1.m_valor and m1.m_ident = 'estado_cobro'")
    		->leftJoin('menu m2', "a.a_pago = m2.m_valor and m2.m_ident = 'estado_pago'")
    		->leftJoin('menu m3', "a.a_acto = m3.m_valor and m3.m_ident = 'tipo_albaran'")
    		->leftJoin('iq iq', 'a.a_iq_id = iq.iq_id')
    		->leftJoin('iq_rol il', 'iq.iq_id = il.iqrol_iq_id')
    		->leftJoin('sanitario s', 'il.iqrol_sani_id = s.sani_id')
    		->leftJoin('menu m4', "il.iqrol_rol_id = m4.m_valor and m4.m_ident = 'sani_rol'")
    		->andWhere(['>', 'a_fecha_acto', '01-01-2018'])
    		->andwhere(['IS NOT', 'a_iq_id', null]);
    	return $q;
    }

    public function getCita()
    {
    	return $this->hasOne(CitaModel::className(), ['cita_id' => 'a_cita_id']);
    }

    public function getEpisodio()
    {
    	return $this->hasOne(EpisodioModel::className(), ['epis_id' => 'a_epis_id']);
    }

    public function getFinanciador()
    {
    	return $this->hasOne(FinanciadorModel::className(), ['finan_id' => 'epis_finan_id'])->via('episodio');
    }

    public function getIq()
    {
    	return $this->hasOne(IqModel::className(), ['iq_id' => 'a_iq_id']);
    }

    public function getActo()
    {
    	return $this->hasOne(MenuModel::className(), ['m_valor' => 'a_acto'])->where(['m_ident' => 'tipo_albaran']);
    }

    public function getEstado()
    {
    	return $this->hasOne(MenuModel::className(), ['m_id' => 'a_estado']);
    }

    public function getPago()
    {
    	return $this->hasOne(MenuModel::className(), ['m_id' => 'a_pago']);
    }

    public function getPaciente()
    {
    	return $this->hasOne(PacienteModel::className(), ['pac_id' => 'a_pac_id']);
    }

    public function getSanitario()
    {
    	return $this->hasOne(SanitarioModel::className(), ['sani_id' => 'cita_sani_id'])->via('cita');
    }

    public function getSaniIq()
    {
    	return $this->hasOne(SanitarioModel::className(), ['sani_id' => 'cita_sani_id'])->via('iq');
    }
}
