<?php

/**
 * Esta clase presenta los datos dinámicos del control de cambios de la aplicación
 * en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\presenters;

use yii\grid\GridView;
use yii\helpers\Html;
use yii\base\BaseObject;
use yii\widgets\DetailView;
use dslibs\admin\models\CambioModel;

class CambioPresenter extends BaseObject
{
    public static function gridCambio ()
    {
    	$searchModel = new CambioModel();
    	$dataProvider = $searchModel->search();

    	$out = Html::tag('h2', 'Control de cambios');
    	$out .= GridView::widget([
    	    'dataProvider' => $dataProvider,
    	    'filterModel' => $searchModel,
	        'columns' => [
	            ['attribute' => 'log_id', 'content' => function ($model) {
	                return Html::a($model->log_id, ['/clinica/admin/cambio/vista', 'id' => $model->log_id]);
	            }],
	            'log_action',
	            'log_tabla',
	            'log_antes',
	            'log_despues',
	            'log_fdc',
	        ]
    	]);
    	return $out;
    }
    
    public static function vistaCambio ($id)
    {
        $model = CambioPresenter::findOne($id);
        $out = Html::tag('h2', 'Cambios para el registro '. $model->log_id);
        $out .= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'log_action',
                'log_tabla',
                'log_antes',
                'log_despues',
                'log_fdc'
            ]
        ]);
        $out .= Html::a('Volver', '/clinica/admin/cambio');
        
        return $out;
    }
}
