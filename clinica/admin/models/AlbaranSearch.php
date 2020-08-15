<?php

/**
 * Esta es la clase implementa el modelo búsqueda de agenda. Está ubicada en la
 * sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Yii;

class AlbaranSearch extends AlbaranModel
{
    public function rules()
    {
        return [
            [['a_id', 'a_fact_id', 'a_pac_id', 'a_epis_id', 'a_acto', 'a_cita_id',
            		'a_iq_id', 'a_estado', 'a_pago', 'sani_id', 'finan_id'], 'integer'],
            [['a_concepto', 'a_presentado', 'a_fecha_acto', 'a_fdc', 'a_fdu',
            		'a_userlogin', 'financiador', 'paciente', 'sanitario', 'acto', 'fa_1',
            		'iq_diagnostico', 'fa_2', 'a_pac_id', 'a_epis_id', 'a_transaccion'], 'safe'],
            [['a_precio'], 'number'],
        ];
    }

    public function search()
    {
        if ($data = Yii::$app->request->get()) Yii::$app->session['ap'] = $data;
        
        $params = Yii::$app->session['ap'];
        $page = isset($params['page']) ? $params['page'] - 1 : '';
        
        $query = AlbaranModel::v_albaran();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                    'pageSize' => 30,
                    'page' => $page,
            ],
            'sort' => [
                'attributes' => [
                    'a_fecha_acto', 'paciente', 'financiador', 'acto',
                    'sanitario', 'a_estado', 'a_pago', 'a_precio', 'a_transaccion'
                ],
                'defaultOrder' => ['a_fecha_acto' => SORT_DESC]
            ]
        ]);

        $this->attributes = $params;

        if (!$this->validate()) return $dataProvider;

        $query->andFilterWhere([
            'a_acto' => $this->a_acto,
            'a_estado' => $this->a_estado,
            'a_pago' => $this->a_pago,
            'a_precio' => $this->a_precio,
            'a_fecha_acto' => $this->a_fecha_acto,
        	's.sani_id' => $this->sani_id,
        	'fn.finan_id' => $this->finan_id,
        ]);

        $query->andFilterWhere(['ilike', "concat_ws(' ',p.pac_nom, p.pac_apell1, p.pac_apell2)", $this->paciente])
        ->andFilterWhere(['ilike', 'fn.finan_empresa', $this->financiador])
    	->andFilterWhere(['>=', 'a_fecha_acto', $this->fa_1])
    	->andFilterWhere(['<=', 'a_fecha_acto', $this->fa_2])
    	->andFilterWhere(['ilike', 's.sani_apellido1', $this->sanitario])
    	->andFilterWhere(['=', 'a_transaccion', $this->a_transaccion]);

        $a = $query->all();
        $a = ArrayHelper::index($a, 'a_id');
        $suma = array_column($a, 'a_precio');
        $dataProvider->totalCount = count($suma);
        $count = array_sum($suma);
    	
    	$out['monto'] = $count;
    	$out['dataProvider'] = $dataProvider;

    	return $out;
    }

    public function searchIq()
    {
        if ($data = Yii::$app->request->get()) Yii::$app->session['api'] = $data;
        
        $params = Yii::$app->session['api'];
        $page = isset($params['page']) ? $params['page'] - 1 : '';
        
        $query = AlbaranModel::v_albaran_iq();

    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    			'pagination' => [
    				'pageSize' => 50,
    			    'page' => $page
    			],
    	       'sort' => [
    	           'attributes' => [
    	               'a_fecha_acto', 'paciente', 'financiador', 'acto', 'sanitario',
    	               'a_estado', 'a_pago', 'a_precio', 'a_diagnostico', 'diagnostico', 'a_transaccion'
    	           ]
    	       ]
    	]);

    	$this->attributes = $params;

    	if (!$this->validate()) return $dataProvider;

    	$dataProvider->sort->defaultOrder = ['a_fecha_acto' => SORT_DESC];

    	$query->andFilterWhere([
    			'a_acto' => $this->a_acto,
    			'a_estado' => $this->a_estado,
    			'a_pago' => $this->a_pago,
    			'a_precio' => $this->a_precio,
    			'a_fecha_acto' => $this->a_fecha_acto,
    			's.sani_id' => $this->sani_id,
    			'fn.finan_id' => $this->finan_id,
    	        'a_transaccion' => $this->a_transaccion,
    	]);

    	$query->andFilterWhere(['ilike', "concat_ws(' ',p.pac_nom, p.pac_apell1, p.pac_apell2)", $this->paciente])
    	->andFilterWhere(['ilike', 'fn.finan_empresa', $this->financiador])
    	->andFilterWhere(['>=', 'a_fecha_acto', $this->fa_1])
    	->andFilterWhere(['<=', 'a_fecha_acto', $this->fa_2])
    	->andFilterWhere(['ilike', 's.sani_apellido1', $this->sanitario]);
    	
    	$a = $query->all();
    	$a = ArrayHelper::index($a, 'a_id');
        $suma = array_column($a, 'a_precio');
        $dataProvider->totalCount = count($suma);
    	$count = array_sum($suma);
    	
    	$out['monto'] = $count;
    	$out['dataProvider'] = $dataProvider;

    	return $out;
    }

    public function searchPaciente($params)
    {
    	$query = AlbaranModel::v_albaran()->where(['a_epis_id' => Yii::$app->session['e']]);

    	$dataProvider = new ActiveDataProvider([
    			'query' => $query,
    	]);

    	$this->load($params);

    	if (!$this->validate()) return $dataProvider;

    	$query->andFilterWhere([
    			'a_id' => $this->a_id,
    			'a_fact_id' => $this->a_fact_id,
    			'a_pac_id' => $this->a_pac_id,
    			'a_epis_id' => $this->a_epis_id,
    			'a_acto' => $this->a_acto,
    			'a_cita_id' => $this->a_cita_id,
    			'a_iq_id' => $this->a_iq_id,
    			'a_presentado' => $this->a_presentado,
    			'a_estado' => $this->a_estado,
    			'a_pago' => $this->a_pago,
    			'a_precio' => $this->a_precio,
    			'a_fecha_acto' => $this->a_fecha_acto,
    			'a_fdc' => $this->a_fdc,
    			'a_fdu' => $this->a_fdu,
    	]);

    	$query->andFilterWhere(['ilike', 'a_concepto', $this->a_concepto])
    	->andFilterWhere(['ilike', 'a_userlogin', $this->a_userlogin]);
    	
    	$dataProvider->sort->defaultOrder = ['a_fecha_acto' => SORT_DESC];

    	return $dataProvider;
    }
}
