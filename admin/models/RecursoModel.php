<?php

/**
 * Esta clase es el modelo para implementar el control de acceso a resursos
 * de la aplicación en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\models;

use Yii;
use yii\data\ActiveDataProvider;

class RecursoModel extends AdminModel
{
    public static function tableName()
    {
        return 'recurso';
    }

    public function rules()
    {
        return [
            [['r_ruta'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'r_id' => 'ID',
            'r_ruta' => 'RecursoModel',
        ];
    }

    public function getRoles ()
    {
    	return $this->hasMany(MenuModel::className(), ['m_valor' => 'rol_id'])
    	->viaTable('recurso_rol', ['ruta_id' => 'r_id'])->where(['m_parent' => 374]);
    }
    
    public function search()
    {
        if ($data = Yii::$app->request->get())
        {
            if (isset($data['r_ruta'])) Yii::$app->session['ruta'] = $data['r_ruta'];
        }
        
        $params = [];
        $params['r_ruta'] = Yii::$app->session['ruta'];
        
        $query = RecursoModel::find()->with('roles')->orderBy('r_ruta asc');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        
        $this->attributes = $params;
        
        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([ 'r_id' => $this->r_id ]);
        $query->andFilterWhere(['like', 'r_ruta', $this->r_ruta]);
        
        return $dataProvider;
    }
}
