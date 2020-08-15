<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\grid\GridView;
use yii\bootstrap4\Html;
use yii\db\Query;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use dslibs\clinica\admin\models\FacturaSearch;
use dslibs\clinica\admin\models\FinanciadorModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;
use dslibs\clinica\admin\models\FacturaModel;
use dslibs\clinica\helpers\OpcionClinica;

class FacturaPresenter extends BaseObject
{
    public function gridFactura ()
    {
    	$searchModel = new FacturaSearch();
    	$dataProvider = $searchModel->searchEpisodio(Yii::$app->request->queryParams);
        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
        	//'filterModel' => $searchModel,
        	'caption' => Html::tag('h2', 'Gestor de Facturas').Camp::botonReturn('/clinica/historia/factura/edit', 'Nueva factura'),
        	'summary' => 'Total: {totalCount}',
        	'columns' => [
        		['attribute' => 'fact_fdc', 'format' => 'date', 'content' => function ($model) {
        			return Html::a(Yii::$app->formatter->asDate($model->fact_fdc),
        					['/clinica/historia/factura/edit', 'id' => $model->fact_id]); }
        		],
        		'fact_ano', 'fact_num',
        		[
        		    'attribute' => 'cobro', 'value' => 'estado.m_texto', 'label' => 'Cobro',
        		],
        		[
        		    'attribute' => 'declarado', 'value' => 'presentado.m_texto', 'label' => 'Declarada',
        		],
        		['attribute' => 'fact_pagador', 'content' => function ($model) {
        			return Html::tag('p', $model->fact_pagador); }
        		],
        		['attribute' => 'total', 'content' => function ($model) {
        			return $model->getTotal() . ' €'; }
        	    ],
        		['attribute' => '', 'content' => function ($model) {
        		    
        		    return Html::a('Imprimir', ['/clinica/historia/factura/print', 'id' => $model->fact_id]);  }
        		],
        	]
        ]);
        return $out;
    }

    public function formFactura ($id)
    {
        $model = $id ? FacturaModel::findOne($id) : new FacturaModel();
    	$mem = FinanciadorModel::findOne(Yii::$app->session['f'])->finan_membrete;
    	$membrete = $mem.'<br><b>Paciente: </b>'.Yii::$app->session['paciente'].'<br><b>Expediente: </b>'.
    	           Yii::$app->session['expediente'];
    	$obser = "Cuenta nº " . Yii::$app->params['numero_cuenta_factura'];;
    	$cobro = OpcionClinica::estadoCobroFactura();
    	$presentada = OpcionClinica::estadoPresentado();

    	$out = Html::tag('h2', 'Detalles de la factura '.$model->fact_ano.' - '.$model->fact_num).
    	Html::beginForm(Url::to(), 'post') . Html::hiddenInput('fact_id', $model->fact_id).
    	"<div class='row'><div class='col-sm-6'>" . "\n".
    	Camp::ckeditor('fact_pagador', $model->fact_pagador != '' ? $model->fact_pagador : $membrete,
    	        'Pagador').
    	"</div><div class='col-sm-6'>" . "\n".
    	Camp::ckeditor('fact_observacion', $model->fact_observacion != '' ? $model->fact_observacion : $obser,
    	        'Observaciones').
    	"</div></div>" . "\n".
    	"<div class='row'><div class='col-sm-4'><br>" . "\n".
    	"<h4>Estado: </h4>" . Camp::dropDownList('fact_estado', $model->fact_estado, $cobro) .
    	"<h4>Declarada: </h4>" . Camp::dropDownList('fact_presentada', $model->fact_presentada, $presentada) .
    	"</div></div>" . "\n".
    	Camp::botonesNormal('/clinica/historia/factura', $id) . Html::endForm();
    	return $out;
    }

    public function gridLinea ($id)
    {
    	$query = (new Query())->from('factura_linea')->where(['fl_factura_id' => $id]);

    	$dataProvider = new ActiveDataProvider(['query' => $query]);
    	$out = GridView::widget([
    		'dataProvider' => $dataProvider,
    		'showFooter' => true,
    		'tableOptions' => ['class' => 'table table-sm'],
    	    //'caption' => 'Detalles',
    		'summary' => '',
    		'columns' => [
    			['attribute' => 'fl_concepto', 'label' => 'Concepto', 'content' => function($model) {
    			         return Camp::textInput('fl_concepto', $model['fl_concepto']);
    			     },
    				'footer' => Camp::textInput('fl_concepto')
    			],
    			['attribute' => 'fl_precio', 'label' => 'Precio (€)', 'content' => function ($model) {
    			         return Camp::textInput('fl_precio', $model['fl_precio']).
    			                Html::hiddenInput('fl_albaran_id', $model['fl_albaran_id']);
    			     },
    				'footer' => Camp::textInput('fl_precio'),
    			],
    			['attribute' => '', 'content' => function ($model) {
    				return 	Html::hiddenInput('fl_id', $model['fl_id']).
    						Html::hiddenInput('fl_factura_id', $model['fl_factura_id']).
    						Camp::botonesAjax('/clinica/historia/factura/linea', 'actualiza'); },
    			'footer' => Html::hiddenInput('fl_factura_id', $id).
    						Camp::botonAjax('Insertar', 'actualiza', '/clinica/historia/factura/linea')
    			],
    		]
    	]);
    	return $out;
    }
}
