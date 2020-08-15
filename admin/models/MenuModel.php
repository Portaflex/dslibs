<?php

/**
 * Esta clase es el modelo para implementar el control de menús de la aplicación
 * en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\models;

class MenuModel extends AdminModel
{
    public static function tableName()
    {
        return 'menu';
    }
    
    public function rules ()
    {
        return [
                //[['m_id'], 'required'],
                [['m_id', 'm_parent', 'm_orden', 'm_valor'], 'integer'],
                [['m_ident'], 'string', 'max' => 200],
                [['m_grupo'], 'string', 'max' => 45],
                [['m_texto', 'm_url', 'm_curl'], 'string', 'max' => 255],
                [['m_id'], 'unique']];
    }

    public function attributeLabels ()
    {
        return [
                'm_id' => 'ID',
                'm_parent' => 'Parent',
                'm_ident' => 'Ident',
                'm_grupo' => 'Grupo',
                'm_orden' => 'Orden',
                'm_texto' => 'Texto',
                'm_url' => 'Url',
                'm_valor' => 'Valor',
                'm_curl' => 'Curl'
        ];
    }
    
    public function getNombre ()
    {
        return $this->m_texto;
    }
}
