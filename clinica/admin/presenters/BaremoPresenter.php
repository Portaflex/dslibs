<?php

/**
 * Esta es la clase implementa el presentador control de baremos
 * de la aplicación clínica.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\presenters;

use Yii;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use dslibs\helpers\Lista;
use yii\base\BaseObject;
use dslibs\clinica\admin\models\BaremoSearch;
use dslibs\helpers\Camp;

class BaremoPresenter extends BaseObject
{
    public function gridBaremo ()
    {
        $finan = Lista::listaMenu('financiador');
    	$searchModel = new BaremoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
        	'filterModel' => $searchModel,
            'caption' => Html::tag('h2', 'Control de Baremos'),
    		'tableOptions' => ['class' => 'table table-sm'],
            'headerRowOptions' => ['class' => 'thead-light'],
        	'summary' => 'Mostrando {count} de {totalCount}',
        	'showFooter' => true,
        	'columns' => [
        		['attribute' => 'bar_codigo', 'footer' => Html::textInput('bar_codigo', '', ['class' => 'form-control input-sm'])],
        		['attribute' => 'financiadores.finan_empresa', 'footer' => Html::dropDownList('bar_finan_id', '', $finan
        				, ['class' => 'form-control input-sm'])],
        		['attribute' => 'bar_descr', 'footer' => Html::textInput('bar_descr', '', ['class' => 'form-control input-sm'])],
        		['attribute' => 'bar_precio', 'footer' => Html::textInput('bar_precio', '', ['class' => 'form-control input-sm'])],
        		['attribute' => null, 'content' => function ($model, $key, $index) {
        			return Html::hiddenInput('bar_id', $model->bar_id). Camp::botonAjax('Del', 'actualiza', '/clinica/admin/baremo/edit',
        			        ['class' => 'danger', 'action' => 'delete']); },
        		 'footer' => Camp::botonAjax('Nuevo', 'actualiza', '/clinica/admin/baremo/edit', ['class' => 'info'])
        		],
        	]
        ]);
    	return $out;
    }
}
