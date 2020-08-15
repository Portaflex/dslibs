<?php

/**
 * Esta clase es el modelo para implementar el control de cambios de la aplicación
 * en el apartado de Gestion.
 * 
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\models;

use Yii;
use yii\data\ActiveDataProvider;

class CambioModel extends AdminModel
{
    public static function tableName()
    {
        return 'loggeneral';
    }
    
    public function rules()
    {
        return [
            [['log_action', 'log_tabla', 'log_antes', 'log_despues'], 'string'],
            [['log_id'], 'integer']
        ];
    }

    public function attributeLabels()
    {
        return [
            'log_id' => 'ID',
            'log_action' => 'Acción',
            'log_tabla' => 'Tabla',
            'log_antes' => 'Antes',
            'log_despues' => 'Después',
            'log_fdc' => 'Fecha',
        ];
    }
    
    public function search ()
    {
        $this->load(Yii::$app->request->queryParams);
        
        $query = self::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100
            ],
            'sort' => [
                'attributes' => [
                    'log_id', 'log_action', 'log_tabla', 'log_fdc',
                ],
                'defaultOrder' => ['log_id' => SORT_ASC]
            ],
        ]);
        
        $query->andFilterWhere(['ilike', 'log_antes', $this->log_antes]);
        $query->andFilterWhere(['ilike', 'log_despues', $this->log_despues]);
        $query->andFilterWhere(['log_id' => $this->log_id]);
        
        return $dataProvider;
    }
}
