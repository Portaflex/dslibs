<?php

/**
 * Esta es la clase implementa el modelo de búsqueda de baremos.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use yii\data\ActiveDataProvider;

class BaremoSearch extends BaremoModel
{
    public function rules()
    {
        return [
            [['bar_id', 'bar_finan_id', 'bar_ident'], 'integer'],
            [['bar_codigo', 'bar_descr', 'bar_fdc', 'bar_fdu', 'bar_userlogin', 'financiadores.finan_empresa'], 'safe'],
            [['bar_precio'], 'number'],
        ];
    }

    public function search($params)
    {
        $query = BaremoModel::find()->joinWith(['financiadores f']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => [
        			'pageSize' => 40
        	]
        ]);

        $dataProvider->sort->attributes['financiadores.finan_empresa'] = [
        		'asc' => ['f.finan_empresa' => SORT_ASC],
        		'desc' => ['f.finan_empresa' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'bar_id' => $this->bar_id,
            'bar_finan_id' => $this->bar_finan_id,
            'bar_ident' => $this->bar_ident,
            'bar_precio' => $this->bar_precio,
            'bar_fdc' => $this->bar_fdc,
            'bar_fdu' => $this->bar_fdu,
        ]);

        $query->andFilterWhere(['ilike', 'bar_codigo', $this->bar_codigo])
            ->andFilterWhere(['ilike', 'bar_descr', $this->bar_descr])
            ->andFilterWhere(['ilike', 'f.finan_empresa', $this->getAttribute('financiadores.finan_empresa')])
            ->andFilterWhere(['ilike', 'bar_userlogin', $this->bar_userlogin]);

        return $dataProvider;
    }
}
