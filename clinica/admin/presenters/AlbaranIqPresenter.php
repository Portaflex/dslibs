<?php

/**
 * Esta es la clase implementa el presentador control de albaranes de citas
 * de la aplicación clínica.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 **/

namespace dslibs\clinica\admin\presenters;

use Yii;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap4\Tabs;
use yii\data\ActiveDataProvider;
use yii\base\BaseObject;
use dslibs\helpers\Grid;
use dslibs\helpers\Lista;
use dslibs\helpers\Camp;
use dslibs\clinica\admin\models\IqRolModel;
use dslibs\clinica\admin\models\AlbaranSearch;

class AlbaranIqPresenter extends BaseObject
{
    private $cobro;
    private $pago;
    private $sani;
    private $financiador;

	public function init()
    {
    	parent::init();
    	$this->cobro = Lista::listaMenu('estado_cobro');
    	$this->pago = Lista::listaMenu('estado_pago');
    	$this->sani = Lista::lista('sanitario', 'sani_id', ['sani_nombres', 'sani_apellido1', 'sani_apellido2'],
    	    ['sani_opera' => 1]);
    	$this->financiador = Lista::lista('financiador', 'finan_id', 'finan_empresa', '', true, 'finan_empresa');
    }

    public function tabsAlbaran()
    {
    	$out = Html::tag('h2', 'Albaranes de cirugías').
    	Tabs::widget([
    			'items' => [
    				['label' => 'Albaranes de citas',
    				 'content' => "<p>".$this->gridAlbaran()."</p>",
    				],
    				['label' => 'Buscar un albaran',
    				'content' => $this->formBusca(),
    				'options' => ['tag' => 'div'],
    				'headerOptions' => ['class' => 'my-class'],
    				],
    			],
    			'options' => ['tag' => 'div'],
    			'itemOptions' => ['tag' => 'div'],
    			'headerOptions' => ['class' => 'my-class'],
    			'clientOptions' => ['collapsible' => false],
    	]);

    	return $out;
    }

    public function gridAlbaran()
    {
    	$searchModel = new AlbaranSearch();
    	$o  = $searchModel->searchIq();
    	$dataProvider = $o['dataProvider'];
    	$amount = $o['monto'];
    	
    	$cobro = $this->cobro;
    	$pago = $this->pago;

    	$out = Grid::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
    	'summary' => 'Total albaranes: {totalCount}',
    	'tableOptions' => ['class' => 'table table-sm'],
    	'rowOptions' => ['class' => 'info'],
    	'groupColumn' => 'a_id',
    	'showFooter' => true,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'a_fecha_acto', 'label' => 'Fecha', 'content' => function ($model) {
            	return Html::a(Yii::$app->formatter->asDate($model['a_fecha_acto']), ['/clinica/admin/albaran-iq/edit-albaran',
            	            'id' => $model['a_id']]).
            		Html::hiddenInput('a_id', $model['a_id']); },
            	'filter' => Camp::datePicker('a_fecha_acto'),
            ],
            ['attribute' => 'paciente', 'content' => function ($model) {
            	return Html::a($model['paciente'], ['/clinica/historia/visita/', 'e' => $model['a_epis_id'], 
            	            'p' => $model['a_pac_id'], 'f' => $model['finan_id'],
            	            'd' => $model['iq_dep']]); },
            	'filter' => Camp::textInput('paciente'),
            ],
            ['attribute' => 'financiador', 'value' => 'financiador', 'format' => 'text',
            	'filter' => Camp::textInput('financiador'),
            ],
            ['attribute' => 'a_transaccion', 'label' => 'Transaccion',
                'filter' => Camp::textInput('a_transaccion', ['ap', 'a_transaccion'])
            ],
            ['attribute' => 'iq_diagnostico', 'value' => 'iq_diagnostico', 'label' => 'Diagnóstico',
            	'filter' => Camp::textInput('sanitario'),
            ],
    		['attribute' => 'a_estado', 'label' => 'Cobro', 'content' => function ($model, $key, $index) use ($cobro) {
    			return Html::dropDownList('a_estado', $model['a_estado'], $this->cobro,
    					['class' => 'form-control input-sm '. $model['estado'],
    					 'onChange' => "actualiza('e_$index')", 'url' => '/clinica/admin/albaran-iq/edit-linea',
    					 'id' => 'e_'.$index]); },
            	'filter' => $cobro,
    		],
   			['attribute' => 'a_pago', 'label' => 'Pago', 'content' => function ($model, $key, $index) use ($pago) {
    			return Html::dropDownList('a_pago', $model['a_pago'], $pago,
    					['class' => 'form-control input-sm '. $model['pago'],
    					 'onChange' => "actualiza('p_$index')", 'url' => '/clinica/admin/albaran-iq/edit-linea',
    					 'id' => 'p_'.$index]); },
            	'filter' => $pago,
            	'footer' => '<b>Total:</b>'
    		],
    		['attribute' => 'a_precio', 'label' => 'Precio(€)', 'content' => function ($model, $key, $index) {
    			return Camp::textInput('a_precio', $model['a_precio'], '',
    					['onChange' => "actualiza_recarga($index)", 'url' => '/clinica/admin/albaran-iq/edit-linea',
    					 'id' => $index]); },
            	'filter' => false,
            	'footer' => '<b>'.Yii::$app->formatter->asCurrency($amount).'</b>'
    		],
        ],
        'afterRow' => function ($model, $key, $index){
        	$precio = (! empty ($model['iqrol_precio'])) ? ' --> '.$model['iqrol_precio'].' €' : '';
         	return Html::tag('tr', "<td colspan=7>".
         			Html::ul([$model['rol'].': '.$model['sanitario'].' '.$precio]).
         			"</td><td></td>"); }
    	]);
    	return $out;
    }

    public function formBusca()
    {
    	$out = Html::tag('h3', 'Buscar un albarán')."<div class='col-sm-6'>" . "\n".
    	 		Html::beginForm('/clinica/admin/albaran-iq', 'get').
    	 		Camp::textInput('paciente', Yii::$app->request->get('paciente'), 'Paciente').
    	 		Camp::dropDownList('sani_id', Yii::$app->request->get('sani_id'), $this->sani, 'Sanitario').
    	 		Camp::dropDownList('finan_id', Yii::$app->request->get('finan_id'), $this->financiador, 'Financiador').
    	 				"</div><div class='col-sm-6'>"."\n".
    	 		Camp::datePicker('fa_1', Yii::$app->request->get('fa_1'), 'Fecha inicial').
    	 		Camp::datePicker('fa_2', Yii::$app->request->get('fa_2'), 'Fecha final').
    	 		Camp::dropDownList('a_estado', Yii::$app->request->get('a_estado'), $this->cobro, 'Cobro').
    	 		Camp::dropDownList('a_pago', Yii::$app->request->get('a_pago'), $this->pago, 'Pago').
    	 				"</div><div class='col-sm-10'><br>".
    			Html::submitButton('Buscar', ['class' => 'btn btn-primary']).
    			Html::endForm()."</div>";
    	return $out;
    }

    public function formAlbaran($model)
    {
    	$out = Html::tag('h2', 'Editar Albarán').
    		Html::beginForm('/clinica/admin/albaran-iq/edit-albaran', 'post').
    		Html::tag('h3', 'Paciente: '.$model['paciente']).
    		"<div class='col-sm-12'><p>" . "\n".
    		Html::hiddenInput('a_id', $model['a_id']).
    		Camp::textInput('iq_diagnostico', $model['iq_diagnostico'], 'Diagnóstico').
    		"</p></div><div class='col-sm-4'>"."\n".
    		Camp::dropDownList('a_estado', $model['a_estado'], $this->cobro, 'Cobro',
    		        ['class' => 'form-control input-sm '. $model['estado']]).
    		"</p></div><div class='col-sm-4'><p>" . "\n".
    		Camp::dropDownList('a_pago', $model['a_pago'], $this->pago, 'Pago',
    		        ['class' => 'form-control input-sm '. $model['pago']]).
    		"</p></div><div class='col-sm-4'><p>" . "\n".
    		Camp::textInput('a_precio', $model['a_precio'], 'Precio').
    		"</p></div><div class='col-sm-12'><p>" . "\n".
    		Camp::botonesNormal('/clinica/admin/albaran-iq').
    		"</div>"."\n".
    		Html::endForm();
    	return $out;
    }

    public function gridSanitario($iq_id)
    {
    	$query = IqRolModel::find()->where('iqrol_iq_id ='.$iq_id);
    	$dataProvider = new ActiveDataProvider(['query' => $query]);
    	$roles = Lista::listaMenu('sani_rol');
    	$sani = $this->sani;

    	$out = GridView::widget([
    			'dataProvider' => $dataProvider,
    			'tableOptions' => ['class' => 'table'],
    			'showFooter' => false,
    			'footerRowOptions' => ['class' => 'panel panel-info'],
    			'summary' => '',
    			'columns' => [
    				['attribute' => 'iqsrol_rol_id', 'label' => 'Rol',
    				 'content' => function ($model) use ($roles) {
    					return Camp::dropDownList('iqrol_rol_id', $model['iqrol_rol_id'], $roles).
    					       Html::hiddenInput('iqrol_iq_id', $model['iqrol_iq_id']).
    					       Html::hiddenInput('iqrol_id', $model['iqrol_id']); },
    				 'footer' => Camp::dropDownList('iqrol_rol_id', '', $roles)
    				],
    				['attribute' => 'iqrol_sani_id', 'label' => 'Sanitario',
    				 'content' => function ($model) use ($sani) {
    					return Camp::dropDownList('iqrol_sani_id', $model['iqrol_sani_id'], $sani); },
    				 'footer' => Camp::dropDownList('iqrol_sani_id', '', $sani)
    				],
    				['attribute' => 'iqrol_precio', 'label' => 'Precio',
    				 'content' => function ($model) {
    					return Camp::textInput('iqrol_precio', $model['iqrol_precio']); },
    				 'footer' => Camp::textInput('iqrol_precio')
    				],
    				['attribute' => null, 'content' => function($model){
    					return 	Camp::botonesAjax('/clinica/admin/albaran-iq/edit-albaran', 'actualiza'); },
	    			 'footer' => Camp::botonAjax('Nuevo', 'actualiza', '/admin/albaran-iq/edit-albaran',
	    			         ['class' => 'info'])
	    			]
    			]
    	]);
    	return $out;
    }
}

// Final del documento.
