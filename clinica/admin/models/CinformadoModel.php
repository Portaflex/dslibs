<?php

/**
 * Esta es la clase implementa el modelo de consentimientos informados.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

class CinformadoModel extends AdminModel
{
    public static function tableName ()
    {
        return 'ci';
    }

    public function rules ()
    {
        return [
        		//[['ci_procedimiento'],'required'],
                [['ci_id'],'integer'],
                [['ci_texto'],'string'],
        		[['ci_fdc','ci_fdu','ci_userlogin'],'safe'],
                [['ci_procedimiento','ci_userlogin'],'string','max' => 255]];
    }

    public function attributeLabels ()
    {
        return [
                'ci_id' => 'ID',
                'ci_procedimiento' => 'Procedimiento',
                'ci_texto' => 'Texto',
                'ci_fdc' => 'Fdc',
                'ci_fdu' => 'Fdu',
                'ci_userlogin' => 'Userlogin'];
    }
}
