<?php

/**
 * Esta es la clase implementa el modelo de departamentos del sanitario.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use yii\db\Query;

class SanitarioDepModel extends AdminModel
{
    public static function tableName()
    {
        return 'sanitario_dep';
    }

    public function rules()
    {
        return [
            [['sd_sani_id', 'sd_dep_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'sd_id' => 'Sd ID',
            'sd_sani_id' => 'Sd Sani ID',
            'sd_dep_id' => 'Sd Dep ID',
        ];
    }
    
    public function getSanitarios()
    {
        return $this->hasMany(SanitarioModel::className(), ['sani_id' => 'sd_sani_id']);
    }
    
    public function v_sanitario()
    {
        $q = (new Query())
        ->select(['sd.*', 's.sani_id as sani_id', 's.sani_agenda',
        "concat_ws(' ', s.sani_nombres, s.sani_apellido1, s.sani_apellido2) as nombre"])
        ->from('sanitario_dep sd')
        ->leftJoin('sanitario s', 'sd.sd_sani_id = s.sani_id');
        return $q;
    }
}
