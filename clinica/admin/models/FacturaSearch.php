<?php

/**
 * Esta es la clase implementa el modelo de búsqueda de facturas.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use yii\data\ActiveDataProvider;
use Yii;

class FacturaSearch extends FacturaModel
{
    public function rules()
    {
        return [
            [['fact_id', 'fact_ano', 'fact_num', 'fact_epis_id', 'fact_pac_id', 'fact_empresa_id', 'fact_estado'], 'integer'],
            [['fact_pagador', 'fact_fecha', 'fact_cobro', 'fact_fdc', 'fact_fdu', 'fact_userlogin', 'fact_observacion',
            		'fact_userlogin', 'fact_epis_id', 'fact_pac_id', 'cobro', 'declarado', 'total'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = FacturaModel::find()->joinWith(['estado e', 'presentado p']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['fact_fdc' => SORT_DESC],
                'attributes' => [
                    'cobro' => ['asc' => ['e.m_texto' => SORT_ASC], 'desc' => ['e.m_texto' => SORT_DESC]],
                    'declarado' => ['asc' => ['p.m_texto' => SORT_ASC], 'desc' => ['p.m_texto' => SORT_DESC]],
                    'fact_fdc', 'fact_ano', 'fact_num', 'fact_pac_id', 'fact_pagador',    
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'fact_id' => $this->fact_id,
            'fact_ano' => $this->fact_ano,
            'fact_num' => $this->fact_num,
            'fact_epis_id' => $this->fact_epis_id,
            'fact_pac_id' => $this->fact_pac_id,
            'fact_empresa_id' => $this->fact_empresa_id,
            'fact_estado' => $this->fact_estado,
            'fact_fecha' => $this->fact_fecha,
            'fact_cobro' => $this->fact_cobro,
            'fact_fdc' => $this->fact_fdc,
            'fact_fdu' => $this->fact_fdu,
        ]);

        $query->andFilterWhere(['ilike', 'fact_pagador', $this->fact_pagador])
        ->andFilterWhere(['ilike', 'fact_userlogin', $this->fact_userlogin])
        ->andFilterWhere(['ilike', 'fact_observacion', $this->fact_observacion])
        ->andFilterWhere(['ilike', 'e.m_texto', $this->cobro])
        ->andFilterWhere(['ilike', 'p.m_texto', $this->declarado]);

        return $dataProvider;
    }

    public function searchEpisodio($params)
    {
    	$query = FacturaModel::find()
    	->joinWith(['estado e', 'presentado p'])
    	->where(['fact_epis_id' => Yii::$app->session['e']]);

    	// add conditions that should always apply here

    	$dataProvider = new ActiveDataProvider([
    		'query' => $query,
    	    'sort' => [
    	        'defaultOrder' => ['fact_fdc' => SORT_DESC],
    	        'attributes' => [
    	            'cobro' => ['asc' => ['e.m_texto' => SORT_ASC], 'desc' => ['e.m_texto' => SORT_DESC]],
    	            'declarado' => ['asc' => ['p.m_texto' => SORT_ASC], 'desc' => ['p.m_texto' => SORT_DESC]],
    	            'fact_fdc', 'fact_ano', 'fact_num', 'fact_pac_id', 'fact_pagador',
    	        ],
    	    ],
    	]);

    	$this->load($params);

    	if (!$this->validate()) {
    		// uncomment the following line if you do not want to return any records when validation fails
    		// $query->where('0=1');
    		return $dataProvider;
    	}

    	// grid filtering conditions
    	$query->andFilterWhere([
    			'fact_id' => $this->fact_id,
    			'fact_ano' => $this->fact_ano,
    			'fact_num' => $this->fact_num,
    			'fact_epis_id' => $this->fact_epis_id,
    			'fact_pac_id' => $this->fact_pac_id,
    			'fact_empresa_id' => $this->fact_empresa_id,
    			'fact_estado' => $this->fact_estado,
    			'fact_fecha' => $this->fact_fecha,
    			'fact_cobro' => $this->fact_cobro,
    			'fact_fdc' => $this->fact_fdc,
    			'fact_fdu' => $this->fact_fdu,
    	]);

    	$query->andFilterWhere(['like', 'fact_pagador', $this->fact_pagador])
    	->andFilterWhere(['like', 'fact_userlogin', $this->fact_userlogin])
    	->andFilterWhere(['like', 'fact_observacion', $this->fact_observacion])
    	->andFilterWhere(['ilike', 'e.m_texto', $this->cobro])
    	->andFilterWhere(['ilike', 'p.m_texto', $this->declarado]);
        
        $dataProvider->sort->defaultOrder = ['fact_fdc' => SORT_DESC];

    	return $dataProvider;
    }
}
