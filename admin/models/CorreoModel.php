<?php

/**
 * Esta clase es el modelo para implementar el control correos de la aplicación
 * en el apartado de Gestion.
 * Utiliza una base de datos distinta a la aplicación, la del servidor de correo.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\models;

use Yii;
use yii\db\ActiveRecord;

class CorreoModel extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->get('mail');
    }
    
    public static function tableName()
    {
        return 'virtual_users';
    }

    public function rules()
    {
        return [
            [['id_portal', 'redirect'], 'integer'],
            [['usuario', 'password', 'alt_email', 'accion', 'dominio', 'nombres'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario' => 'Usuario',
            'id_portal' => 'Id Portal',
            'password' => 'Password',
            'email' => 'Email',
            'alt_email' => 'Alt Email',
            'accion' => 'Acción',
            'redirect' => 'Redirect',
            'dominio' => 'Dominio',
            'nombres' => 'Nombres',
        ];
    }
}
