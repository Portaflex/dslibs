<?php

/**
 * Esta clase es el modelo para implementar el control de usuarios de la aplicación
 * en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\models;

class UsuarioModel extends AdminModel
{
	public static function tableName ()
    {
        return 'usuario';
    }

    public function rules ()
    {
        return [
           //[['user_login'], 'required'],
           [['user_id', 'user_recibe', 'user_erecibe', 'user_activo', 'user_group', 'user_intentos'], 'integer'],
           [['user_fdc', 'user_fdu', 'user_fechapw'], 'safe'],
           [['user_login', 'user_dr', 'user_nom', 'user_apell1', 'user_apell2', 'user_ncol'], 'string', 'max' => 45],
           [['user_pw', 'user_email', 'user_telefono', 'user_movil', 'user_direccion', 'user_avatar', 'user_email_portal',
               'user_fechapw'], 'string', 'max' => 255],
           [['user_grado', 'user_espec'], 'string', 'max' => 200],
           ['user_email', 'email']
        ];
    }

    public function attributeLabels ()
    {
        return [
           'user_id' => 'User ID',
           'user_login' => 'Login',
           'user_pw' => 'User Pw',
           'user_dr' => 'Dr',
           'user_nom' => 'Nombre',
           'user_apell1' => 'Primer Apellido',
           'user_apell2' => 'Segundo Apellido',
           'user_ncol' => 'Nº Col',
           'user_grado' => 'Grado',
           'user_espec' => 'Especialidad',
           'user_email' => 'Email',
           'user_telefono' => 'Telefono',
           'user_movil' => 'Movil',
           'user_direccion' => 'Dirección',
           'user_recibe' => 'Recibe comunicados del Foro',
           'user_erecibe' => 'Recibe comunicados de Foro de Enfermería',
           'user_fdc' => 'User Fdc',
           'user_fdu' => 'User Fdu',
           'user_activo' => 'Activo',
           'user_avatar' => 'Avatar',
           'user_group' => 'Grupo',
           'user_email_portal' => 'Email del Portal',
           'user_fechapw' => 'Fecha de contraseña',
           'user_intentos' => 'Intentos'
        ];
    }

    public function getUsuarioDep ()
    {
    	return $this->hasMany(UsuarioDepModel::className(), ['ud_uid' => 'user_id']);
    }
    
    public function getNombres ()
    {
        return $this->user_nom.' '.$this->user_apell1.' '.$this->user_apell2;
    }
    
    public function getDepartamentos ()
    {
        return $this->hasMany(MenuModel::className(), ['m_valor' => 'ud_depid'])
            ->viaTable('usuario_dep', ['ud_uid' => 'user_id'])->where(['m_ident' => 'departamento']);
    }
    
    public function getActivo ()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'user_activo'])->where(['a.m_ident' => 'booleano']);
    }
    
    public function getRecibe ()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'user_recibe'])->where(['r.m_ident' => 'booleano']);
    }
    
    public function getErecibe ()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'user_erecibe'])->where(['e.m_ident' => 'booleano']);
    }
}
