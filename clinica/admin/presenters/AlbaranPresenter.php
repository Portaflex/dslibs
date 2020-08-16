<?php

/**
 * Esta es la clase implementa el presentador control de albaranes
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
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap4\Tabs;
use yii\base\BaseObject;
use dslibs\clinica\admin\models\AlbaranSearch;
use dslibs\helpers\Camp;
use dslibs\clinica\helpers\OpcionClinica;
use yii2tech\spreadsheet\Spreadsheet;

class AlbaranPresenter extends BaseObject
{
    private $sani;
    private $financiador;
    private $cobro;
    private $pago;

    
    public function tabsAlbaran()
    {
        $out = Html::tag('h2', 'Albaranes de citas');
        $out .= Tabs::widget([
            'items' => [
                ['label' => 'Albaranes de citas',
                    'content' => "<p>".$this->gridAlbaran()."</p>",
                ],
                ['label' => 'Buscar un albaran',
                    'content' => $this->formAlbaranBusca(),
                    'options' => ['tag' => 'div'],
                    'headerOptions' => ['class' => 'my-class'],
                ],
                [ 'label' => 'Cargar pagos',
                    'content' => $this->formCargaPagos(),
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
        $o  = $searchModel->search();
        $dataProvider = $o['dataProvider'];
        $amount = $o['monto'];
        
        $cobro = OpcionClinica::estadoCobro();
        $pago = OpcionClinica::estadoPago();
        
        $out = GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'summary' => 'Total albaranes: {totalCount}',
            'showFooter' => true,
            'tableOptions' => ['class' => 'table table-sm table-hover'],
            'headerRowOptions' => ['class' => 'thead-light'],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['attribute' => 'a_fecha_acto', 'value' => function ($model) {
                  return Yii::$app->formatter->asDate($model['a_fecha_acto']); },
                  'filter' => Camp::datePicker('a_fecha_acto', ['ap', 'a_fecha_acto']),
                ],
                ['attribute' => 'paciente', 'content' => function ($model) {
                   return Html::a($model['paciente'], ['/clinica/historia/visita/', 'e' => $model['a_epis_id'],
                   'p' => $model['a_pac_id'], 'f' => $model['finan_id']]);},
                   'filter' => Camp::textInput('paciente', ['ap', 'paciente']),
                ],
                ['attribute' => 'financiador',
                   'filter' => Camp::textInput('financiador', ['ap', 'finandiador'])],
                ['attribute' => 'acto',
                   'filter' => Camp::textInput('acto', ['ap', 'acto'])],
                ['attribute' => 'sanitario',
                   'filter' => Camp::textInput('sanitario', ['ap', 'sanitario'])],
                ['attribute' => 'a_transaccion', 'content' => function ($model, $key, $index) {
                       return Camp::textInput('a_transaccion', $model['a_transaccion'], '', ['onChange' => "actualiza_recarga($index)",
                           'url' => '/clinica/admin/albaran/edit', 'id' => $index, 'style'=>'width:100px;']); },
                    'filter' => Camp::textInput('a_transaccion', ['ap', 'a_transaccion'])
                ],
                ['attribute' => 'a_estado', 'content' => function ($model, $key, $index) use ($cobro) {
                   return Html::dropDownList('a_estado', $model['a_estado'], $cobro, ['class' => 'form-control input-sm '.
                   $model['estado'],'onChange' => "actualiza('e_$index')",
                   'url' => '/clinica/admin/albaran/edit', 'id' => 'e_'.$index]); },
                   'filter' => $cobro,],
                ['attribute' => 'a_pago', 'content' => function ($model, $key, $index) use ($pago) {
                   return Html::dropDownList('a_pago', $model['a_pago'], $pago, ['class' => 'form-control input-sm '.
                   $model['pago'], 'onChange' => "actualiza('p_$index')",
                   'url' => '/clinica/admin/albaran/edit', 'id' => 'p_'.$index]); },
                   'filter' => $pago, 'footer' => '<b>Total:</b>'],
                ['attribute' => 'a_precio', 'content' => function ($model, $key, $index) {
                   return Camp::textInput('a_precio', $model['a_precio'], '', ['onChange' => "actualiza_recarga($index)",
                   'url' => '/clinica/admin/albaran/edit', 'id' => $index, 'style'=>'width:80px;']) . Html::hiddenInput('a_id', $model['a_id']); },
                   'filter' => false, 'footer' => '<b>'.Yii::$app->formatter->asCurrency($amount).'</b>'],
             ],
        ]);
        return $out;
    }
    
    public function formAlbaranBusca ()
    {
        $sani = OpcionClinica::saniCita();
        $financiador = OpcionClinica::financiador();
        $cobro = OpcionClinica::estadoCobro();
        $pago = OpcionClinica::estadoPago();
        
        $out = Html::tag('h3', 'Buscar un albarán').
        "<div class='col-sm-6'>" . "\n".
        Html::beginForm('/clinica/admin/albaran', 'get').
        Camp::textInput('paciente', Yii::$app->request->get('paciente'), 'Paciente').
        Camp::dropDownList('sani_id',  Yii::$app->request->get('sani_id'), $sani, 'Sanitario').
        Camp::dropDownList('finan_id', Yii::$app->request->get('finan_id'), $financiador, 'Financiador').
        "</div><div class='col-sm-6'>"."\n".
        Camp::datePicker('fa_1', Yii::$app->request->get('fa_1'),'Desde fecha').
        Camp::datePicker('fa_2', Yii::$app->request->get('fa_2'), 'Hasta fecha').
        Camp::dropDownList('a_estado', Yii::$app->request->get('a_estado'), $cobro, 'Cobro').
        Camp::dropDownList('a_pago', Yii::$app->request->get('a_pago'), $pago, 'Pago').
        "</div><div class='col-sm-10'><br>".
        Html::submitButton('Buscar', ['class' => 'btn btn-xs btn-primary']).
        Html::endForm()."</div>";
        return $out;
    }
    
    public function formCargaPagos ()
    {
        $out = Html::tag('h3', 'Cargar archivo csv de pagos').
        Html::beginForm('/clinica/admin/albaran/carga-pagos', 'post', ['enctype' => 'multipart/form-data']).
        Camp::fileInput('archivo', '', 'Seleccionar', ['class' => 'file']).
        Camp::botonSend().
        Html::endForm();
        
        return $out;
    }
    
    public function excelExport ()
    {
        $searchModel = new AlbaranSearch();
        $o  = $searchModel->search();
        $dataProvider = $o['dataProvider'];
        
        $exporter = new Spreadsheet([
            'dataProvider' => $dataProvider,
            'title' => 'Albaranes de cita',
            'columns' => [
                ['attribute' => 'Fecha', 'content' => function ($model) {
                    return Yii::$app->formatter->asDate($model['a_fecha_acto']);
                }],
                ['attribute' => 'financiador'],
                ['attribute' => 'paciente'],
                ['attribute' => 'financiador'],
                ['attribute' => 'sanitario'],
                ['attribute' => 'estado'],
                ['attribute' => 'pago'],
                ['attribute' => 'a_precio', 'label' => 'Precio']
            ]
        ]);
        
        return $exporter->send('albaranes.xls');
    } 
}

// Fin del documento
