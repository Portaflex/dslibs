<?php

/**
 * Esta clase es el modelo para implementar los textos de la LOPD de la aplicación
 * en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\models;

class LopdModel extends AdminModel
{
    public static function tableName()
    {
        return 'lopd';
    }

    public function rules()
    {
        return [
            [['pd_ident'], 'string', 'max' => 255],
            [['pd_ident'], 'required'],
            [['pd_id'], 'unique'],
            [['pd_contenido'], 'string'],
            [['pd_publico'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pd_id' => 'ID',
            'pd_ident' => 'Ident',
            'pd_contenido' => 'Contenido',
            'pd_publico' => 'Publico',
        ];
    }
    
    public function getPublico ()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'pd_publico'])->where(['m_ident' => 'booleano']);
    }
}