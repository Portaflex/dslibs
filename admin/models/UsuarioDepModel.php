<?php

/**
 * Esta clase es el modelo para implementar el control de los departamentos
 * a los que pertenecen los usuarios de la aplicación. Está en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\models;

use yii\db\Query;

class UsuarioDepModel extends AdminModel
{
    public static function tableName()
    {
        return 'usuario_dep';
    }

    public function rules()
    {
        return [
            [['ud_uid', 'ud_depid'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'ud_id' => 'ID',
            'ud_uid' => 'Usuario ID',
            'ud_depid' => 'Departamento ID',
        ];
    }
    
    public function v_dep()
    {
        $q = (new Query())->select(['ud.ud_uid', 'ud.ud_depid',
            'd.m_valor as valor', 'd.m_texto as nombre'])->from('usuario_dep ud')
            ->leftJoin('menu d', 'ud.ud_depid = d.m_valor and d.m_ident = departamento');
        return $q;
    }
}
