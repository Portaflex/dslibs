<?php

/**
 * Esta es la clase implementa el modelo baremos de compañías.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\models;

class BaremoModel extends AdminModel
{
    public static function tableName()
    {
        return 'baremo';
    }

    public function attributes()
    {
    	return array_merge(parent::attributes(), ['financiadores.finan_empresa']);
    }

    public function rules()
    {
        return [
            //[['bar_finan_id'], 'required'],
            [['bar_id', 'bar_finan_id', 'bar_ident'], 'integer'],
            [['bar_precio'], 'number'],
        		[['bar_fdc', 'bar_fdu', 'bar_userlogin'], 'safe'],
            [['bar_codigo', 'bar_userlogin'], 'string', 'max' => 45],
            [['bar_descr'], 'string', 'max' => 255],
            [['bar_id'], 'unique'],
            [['bar_finan_id'], 'exist', 'skipOnError' => true, 'targetClass' => FinanciadorModel::className(), 'targetAttribute' => ['bar_finan_id' => 'finan_id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'bar_id' => 'ID',
            'bar_finan_id' => 'Financiador',
            'bar_ident' => 'Identificador',
            'bar_codigo' => 'Código',
            'bar_descr' => 'Descripción',
            'bar_precio' => 'Precio',
            'bar_fdc' => 'Fdc',
            'bar_fdu' => 'Fdu',
            'bar_userlogin' => 'Userlogin',
        ];
    }

    public function getFinanciadores()
    {
        return $this->hasOne(FinanciadorModel::className(), ['finan_id' => 'bar_finan_id']);
    }
}
