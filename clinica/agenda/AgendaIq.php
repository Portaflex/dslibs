<?php

/**
 * Esta es la clase implementa el presentador control de intervenciones
 * de la aplicación clínica.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\agenda;

use Yii;
use yii\base\BaseObject;
use yii\grid\GridView;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use dslibs\clinica\admin\models\IqModel;
use dslibs\helpers\Camp;
use dslibs\clinica\helpers\OpcionClinica;

class AgendaIq extends BaseObject
{
    public function gridIq()
    {
    	$dataProvider = new ActiveDataProvider(['query' => IqModel::find()
    	        ->where(['iq_fecha' => Yii::$app->session['iqfecha']])
    	        ->with(['paciente', 'tipo', 'estado', 'episodio'])
    	        ->orderBy('iq_norden asc')]);

    	$out = GridView::widget([
    			'dataProvider' => $dataProvider,
    			'caption' => Html::tag('h2', 'Intervenciones del '.Yii::$app->formatter->format(Yii::$app->session['iqfecha'], 'date')),
    			'summary' => 'Intervenciones: {totalCount}',
    	        'showHeader' => FALSE,
    			'tableOptions' => ['class' => 'table'],
    			'columns' => [
    			        ['value' => 'iq_norden'],
    			        ['content' => function ($model) {
    			            return Html::tag('h4', Html::a($model->paciente['nombre'], ['/clinica/historia/iq/edit',
    			                    'id' => $model->iq_id, 'p' => $model->iq_pac_id, 'e' => $model->iq_epis_id,
    			                    'f' => $model->financiador['finan_id'], 'd' => $model->iq_dep,
    			                    'ee' => $model->episodio['epis_estado']])) .
    			                   Html::label('Edad:') . ' ' . $model->paciente['edad'] . ' años' . "<br>" .
    			                   Html::label('Código Iq:') . ' ' . $model->iq_codigo . "<br>" .
    			     			   Html::label('Financiador:') . ' '. $model->financiador['finan_empresa'] . "<br>" ;
    			        }],
    			        ['content' => function ($model) {
    			            return Html::label('Diagnóstico:') . ' '. $model->iq_diagnostico . "<br>" .
    			     			   Html::label('Procedimiento:') . ' '. $model->iq_procedimiento . '<br>' .
    			     			   Html::label('Observaciones:') . ' '. $model->iq_observ . '<br>' .
    			     			   Html::label('Tipo:') . ' '. $model->tipo['m_texto'];
    			        }],
    			        ['content' => function ($model, $key, $index) {
    			            return Html::label('Hora:') . ' '. Yii::$app->formatter->asTime($model->iq_hora) . "<br>" .
    			     			   Html::label('Estado:') . ' '. Camp::dropDownList('iq_estado', $model->iq_estado, OpcionClinica::estadoIq(), '',
    			     			           ['id' => 'i_'.$index, 'onChange' => "actualiza('i_$index')",
    			     			             'url' => '/clinica/agenda/iq/cambia-estado', 'clase' => $model->estado['m_texto']]) .
    			     			             Html::hiddenInput('iq_id', $model->iq_id);
    			        }],
    			]
    	]);
    	return $out;
    }
    
    public function gridIqPdf()
    {
        $dataProvider = new ActiveDataProvider(['query' => IqModel::find()
                ->where(['iq_fecha' => Yii::$app->session['iqfecha'], 'iq_estado' => 1])
                ->with(['paciente', 'tipo', 'estado'])
                ->orderBy('iq_norden asc')]);
        
        $out = GridView::widget([
                'dataProvider' => $dataProvider,
                'caption' => Html::tag('h3', 'Intervenciones del '.Yii::$app->formatter->format(Yii::$app->session['iqfecha'], 'date')) . "<hr>",
                'summary' => 'Intervenciones: {totalCount}',
                'tableOptions' => ['class' => 'table'],
                'columns' => [
                        //['value' => 'iq_norden'],
                        ['content' => function ($model) {
                            return  Html::tag('h4', $model->iq_norden. '. '.$model->paciente['nombre']) . "<br>" .
                                    '<i>Edad:</i> ' . $model->paciente['edad'] . ' años' . "<br>" .
                                    '<i>Financiador:</i> '. $model->financiador['finan_empresa'];
                        }],
                        ['content' => function ($model) {
                            return  '<i>Diagnóstico:</i> '. $model->iq_diagnostico . "<br><br>" .
                                    '<i>Procedimiento:</i> '. $model->iq_procedimiento;
                        }],
                        ['content' => function ($model) {
                            return  '<i>Tipo de ingreso:</i> '. $model->tipo['m_texto'];
                        }],
                ],
                'afterRow' => function () { return "<tr><td colspan=3><hr></td></tr>"; },
        ]);
        return $out;
    }
}
