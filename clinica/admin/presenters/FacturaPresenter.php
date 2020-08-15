<?php

/**
 * Esta es la clase implementa el presentador control de albaranes de facturas
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
use yii\grid\GridView;
use yii\bootstrap4\Html;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use dslibs\clinica\admin\models\FacturaModel;
use dslibs\helpers\Camp;
use dslibs\clinica\admin\models\FacturaSearch;
use yii\base\Behavior;
use dslibs\clinica\helpers\OpcionClinica;

class FacturaPresenter extends Behavior
{
    public function gridFactura ()
    {
    	$searchModel = new FacturaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
        	'filterModel' => $searchModel,
        	'caption' => Html::tag('h2', 'Gestor de Facturas').Camp::botonReturn('/clinica/admin/factura/edit', 'Nueva factura'),
        	'summary' => 'Total: {totalCount}',
        	'columns' => [
        		['attribute' => 'fact_fdc', 'format' => 'date', 'content' => function ($model) {
        			return Html::a(Yii::$app->formatter->asDate($model->fact_fdc),
        					'/clinica/admin/factura/edit?id='.$model->fact_id); }
        		],
        		'fact_ano', 'fact_num',
        		[
        		    'attribute' => 'cobro', 'value' => 'estado.m_texto', 'label' => 'Cobro',
        		],
        		[
        		    'attribute' => 'declarado', 'value' => 'presentado.m_texto', 'label' => 'Declarada',
        		],
        		['attribute' => 'fact_pagador', 'content' => function ($model) {
        			return Html::tag('p', $model->fact_pagador) .
        			       Html::hiddenInput('fact_userlogin', Yii::$app->session['userLogin']); }
        	    ],
        	    ['attribute' => 'fact_pac_id', 'label' => 'Paciente','content' => function ($model) {
        		    return Html::a($model->fact_pac_id, ['/clinica/historia/episodio/', 'p' => $model->fact_pac_id]); }
        		],
        		['attribute' => 'total', 'content' => function ($model) {
        		    return $model->getTotal() . ' €'; }
        		],
        		['attribute' => '', 'label' => '', 'content' => function ($model) {
        		    return Html::a('Imprimir', ['/clinica/admin/factura/print', 'id' => $model->fact_id]); }
        		],
        		
        	]
        ]);
        return $out;
    }
    
    public function formFactura ($id)
    {
        $model = $id ? FacturaModel::findOne($id) : new FacturaModel();
    	$obser = "Cuenta bancaria: ".Yii::$app->params['numero_cuenta_factura'];
    	$cobro = OpcionClinica::estadoCobro();
    	$presentada = OpcionClinica::estadoPresentado();
        
        $out = Html::tag('h2', 'Detalles de la factura '.$model->fact_ano.' - '.$model->fact_num).
        Html::beginForm(Url::to(), 'post') . Html::hiddenInput('fact_id', $model->fact_id).
        "<div class='row'><div class='col-sm-6'>" . "\n".
        Camp::ckeditor('fact_pagador', $model->fact_pagador, 'Pagador', ['preset' => 'Factura']).
        "</div><div class='col-sm-6'>" . "\n".
        Camp::ckeditor('fact_observacion', $model->fact_observacion ? $model->fact_observacion : $obser, 'Observaciones', ['preset' => 'Factura']).
        "</div></div>" . "\n".
        "<div class='row'><div class='col-sm-4'><br>" . "\n".
        "<h4>Estado: </h4>" . Camp::dropDownList('fact_estado', $model->fact_estado, $cobro) .
        "<h4>Declarada: </h4>" . Camp::dropDownList('fact_presentada', $model->fact_presentada, $presentada) .
        "</div></div>" . "\n".
        Camp::botonesNormal('/clinica/admin/factura', $id) . Html::endForm();
        return $out;
    }

    public function gridLinea ($id)
    {
    	$query = (new Query())->from('factura_linea')->where(['fl_factura_id' => $id]);

    	$dataProvider = new ActiveDataProvider(['query' => $query]);
    	$out = GridView::widget([
    		'dataProvider' => $dataProvider,
    		'showFooter' => true,
    		'tableOptions' => ['class' => 'table table-default'],
    	    //'caption' => 'Detalles',
    		'summary' => '',
    		'columns' => [
    			['attribute' => 'fl_concepto', 'label' => 'Concepto', 'content' => function($model) {
    			         return Camp::textInput('fl_concepto', $model['fl_concepto']);
    			     },
    				'footer' => Html::beginForm('/clinica/admin/factura/linea', 'post').Camp::textInput('fl_concepto')
    			],
    			['attribute' => 'fl_precio', 'label' => 'Precio', 'content' => function ($model) {
    			         return Camp::textInput('fl_precio', Yii::$app->formatter->asCurrency($model['fl_precio']));
    			     },
    				'footer' => Camp::textInput('fl_precio'),
    			],
    			['attribute' => '', 'content' => function ($model) {
    				return 	Html::hiddenInput('fl_id', $model['fl_id']).
    						Html::hiddenInput('fl_factura_id', $model['fl_factura_id']).
    						Camp::botonesAjax('/clinica/admin/factura/linea', 'actualiza'); },
    			'footer' => Html::hiddenInput('fl_factura_id', $id).
    						Camp::botonSave().Html::endForm()
    			],
    		]
    	]);
    	return $out;
    }
}
