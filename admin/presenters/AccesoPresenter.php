<?php

/**
 * Esta clase presenta los datos dinámicos del control de accesos a la aplicación
 * en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\presenters;

use yii\grid\GridView;
use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\base\BaseObject;
use dslibs\admin\models\AccesoModel;

class AccesoPresenter extends BaseObject
{
    public static function gridLog ()
    {
    	$log = AccesoModel::find()->orderBy('la_fecha desc');
    	$dataProvider = new ActiveDataProvider([
    		'query' => $log,
    		'pagination' => [
    			'pageSize' => 100
    		]
    	]);

    	$out = Html::tag('h2', 'Control de accesos a la aplicación');
    	$out .= GridView::widget([
    	    'dataProvider' => $dataProvider,
    	    'tableOptions' => ['class' => 'table table-sm'],
	        'columns' => [
	                //'la_id',
	                'la_pagina',
	                //'la_tabla',
	                'la_pac_id',
	                'la_epis_id',
	                'la_userlogin',
	                ['attribute' => 'la_fecha', 'format' => 'date'],
	                ['attribute' => 'la_fecha', 'format' => 'time', 'label' => 'Hora']
	                //'la_url',
	        ]
    	]);
    	return $out;
    }
}
