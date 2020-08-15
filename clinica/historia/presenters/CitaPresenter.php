<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use dslibs\helpers\Lista;
use dslibs\helpers\Camp;
use dslibs\clinica\admin\models\CitaModel;
use dslibs\calendar\Calendar;
use yii\base\BaseObject;

class CitaPresenter extends BaseObject
{
    public function gridCita()
    {
    	$estado = Lista::listaMenu('estado_cita');
    	$tipo = Lista::listaMenu('tipo_cita');
    	$sani = Lista::saniDep(Yii::$app->session['d']);
    	$ver_agenda = Yii::$app->session['ee'] == 1 ? Camp::botonReturn('/clinica/historia/cita/select', 'Ver agenda') : '';

    	$dataProvider = (new CitaModel())->searchHistoria();

    	$out = GridView::widget([
    			'dataProvider' => $dataProvider,
    	        'caption' => Html::tag('h2', 'Citas del paciente').$ver_agenda,
    			'tableOptions' => ['class' => 'table table-sm'],
    			'summary' => 'Total de citas: {totalCount}',
    			'showFooter' => Yii::$app->session['ee'] == 1 ? true : false,
    			'footerRowOptions' => ['class' => 'panel panel-info'],
    			'columns' => [
    				['attribute' => 'cita_fecha', 'label' => 'Fecha', 'content' => function ($model) {
    					return Camp::datePicker('cita_fecha', $model['cita_fecha'], '', ['style'=>'width:80px;']).
    						Html::hiddenInput('cita_id', $model['cita_id']).
    						Html::hiddenInput('cita_dep', Yii::$app->session['d']); },
    					'footer' => Camp::datePicker('cita_fecha', '', '', ['style'=>'width:80px;'])
    				],
    				['attribute' => 'cita_hora', 'label' => 'Hora', 'content' => function ($model) {
    					return Camp::textInput('cita_hora', Yii::$app->formatter->asTime($model['cita_hora']), '', ['style'=>'width:50px;']); },
    					'footer' => Camp::textInput('cita_hora', '', '', ['style'=>'width:50px;'])
    				],
    				['attribute' => 'cita_estado', 'label' => 'Estado', 'content' => function ($model) use ($estado) {
    					return Camp::dropDownList('cita_estado', $model['cita_estado'], $estado, '', ['clase' => $model['estado']]); },
    					'footer' => Html::hiddenInput('cita_estado', '1')
    				],
    				['attribute' => 'cita_sani_id', 'label' => 'Sanitario', 'content' => function ($model) use ($sani) {
    					return Camp::dropDownList('cita_sani_id', $model['cita_sani_id'], $sani); },
    					'footer' => Camp::dropDownList('cita_sani_id', '', $sani)
    				],
    				['attribute' => 'cita_tipo', 'label' => 'Tipo de cita', 'content' => function ($model) use ($tipo) {
    					return Camp::dropDownList('cita_tipo', $model['cita_tipo'], $tipo); },
    					'footer' => Camp::dropDownList('cita_tipo', '', $tipo)
    			    ],
    				['attribute' => null, 'content' => function($model){
    					return 	Camp::botonesAjax('/clinica/historia/cita/edit', 'actualiza'); },
    					'footer' => Camp::botonAjax('Nuevo', 'actualiza', '/clinica/historia/cita/edit',
    					    ['class' => 'light'])
	    			]
    			],
    	]);
    	return $out;
    }

    public function sanitarios()
    {
        $saniCita = Yii::$app->session['sani-cita'] ?? '';
        $sani = Lista::saniDep(Yii::$app->session['d']);
        $out = "<div class='panel panel-default'>".
               "<div class='panel-heading' align='center'>Sanitario</div>".
               "<div class='panel-body'>".
               Html::beginForm('/clinica/historia/cita/select', 'post').
               Camp::dropDownList('sani-cita', $saniCita,
                  $sani, '', ['onChange' => 'this.form.submit()']).
               Html::endForm().
               "</div></div>";
       return $out;
    }
    
    public function formAsignaCita ()
    {
        $fecha = Yii::$app->request->get('sfecha');
        
        $model = CitaModel::find()
                    ->where(['cita_sani_id' => Yii::$app->session['sani-cita'],
                             'cita_fecha' => $fecha, 'cita_pac_id' => null,
                             'cita_dep' => Yii::$app->session['d']])
                    ->orderBy('cita_hora asc')->asArray()
                    ->all();
        
        $hora = ArrayHelper::map($model, 'cita_id', function($model) { return Yii::$app->formatter->asTime($model['cita_hora']); });
        
        $tipo = Lista::listaMenu('tipo_cita');
        
        $out = "<div class='panel panel-default'>".
               "<div class='panel-heading' align='center'>Citas para el ".Yii::$app->formatter->asDate($fecha)."</div>".
               "<div class='panel-body'>".
                Html::beginForm('/clinica/historia/cita/edit').
                Html::hiddenInput('cita_pac_id', Yii::$app->session['p']).
                Html::hiddenInput('cita_epis_id', Yii::$app->session['e']).
                Html::hiddenInput('cita_dep', Yii::$app->session['d']).
                Html::hiddenInput('cita_estado', 1).
                Camp::dropDownList('cita_id', '', $hora)."<br>".
                Camp::dropDownList('cita_tipo', '', $tipo)."<br>".
                Camp::botonSave().Camp::botonReturn('/clinica/historia/cita').
                Html::endForm().
               "</div></div>";
        return $out;
    }
    
    public function calendario ()
    {
        $params = [
                'fecha' => 'cita_fecha',
                'link' => 'cita_fecha',
                'controller' => '/clinica/historia/cita/select',
                'tabla' => 'cita',
                'where' => ['cita_sani_id' => Yii::$app->session['sani-cita']],
                'pre' => 's'
        ];
        
        $calendario = new Calendar($params);
        
        $out = "<div class='panel panel-default'>".
               "<div class='panel-heading' align='center'>Calendario</div>".
               "<div class='panel-body'>".
               $calendario->show().
               "</div></div>";
        return $out;
    }
}

// Final del documento.
