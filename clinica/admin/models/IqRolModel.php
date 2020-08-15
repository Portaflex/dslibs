<?php

/**
 * Esta es la clase implementa el modelo de roles de intervenciones.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

use dslibs\admin\models\MenuModel;

class IqRolModel extends AdminModel
{
    public static function tableName()
    {
        return 'iq_rol';
    }

    public function rules()
    {
        return [
            //[['iqrol_id', 'iqrol_iq_id', 'iqrol_sani_id', 'iqrol_rol_id'], 'required'],
            [['iqrol_id', 'iqrol_iq_id', 'iqrol_sani_id', 'iqrol_rol_id'], 'integer'],
            [['iqrol_precio'], 'number'],
        	[['iqrol_fdc', 'iqrol_fdu', 'iqrol_userlogin'], 'safe'],
            [['iqrol_userlogin'], 'string', 'max' => 45],
        ];
    }

    public function attributeLabels()
    {
        return [
            'iqrol_id' => 'Iqsrol ID',
            'iqrol_iq_id' => 'Iqsrol Iq ID',
            'iqrol_sani_id' => 'Iqsrol Sani ID',
            'iqrol_rol_id' => 'Iqsrol Rol ID',
            'iqrol_precio' => 'Iqsrol Precio',
            'iqrol_fdc' => 'Iqsrol Fdc',
            'iqrol_fdu' => 'Iqsrol Fdu',
            'iqrol_userlogin' => 'Iqsrol Userlogin',
        ];
    }

    public function getIq()
    {
        return $this->hasOne(IqModel::className(), ['iq_id' => 'iqrol_iq_id']);
    }

    public function getSanitario()
    {
        return $this->hasOne(SanitarioModel::className(), ['sani_id' => 'iqrol_sani_id']);
    }

    public function getRol()
    {
        return $this->hasOne(MenuModel::className(), ['m_valor' => 'iqrol_rol_id'])->where(['m_ident' => 'sani_rol']);
    }
}
