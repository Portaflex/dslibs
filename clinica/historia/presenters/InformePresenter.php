<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use dslibs\clinica\historia\models\InformeModel;
use dslibs\clinica\historia\models\VisitaModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;

class InformePresenter extends BaseObject
{
    public function gridInforme ()
    {
        $query = InformeModel::find()->where(['info_epis_id' => Yii::$app->session['e']]);
        $dataProvider = new ActiveDataProvider([
        		'query' => $query,
        		'pagination' => ['pageSize' => 30]
        ]);

        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
        	'summary' => '',
            'caption' => Html::tag('h2', 'Informes del Paciente'),
        	'tableOptions' => ['class' => 'table table-sm'],
        	'showFooter' => true,
        	'footerRowOptions' => ['class' => 'panel panel-info'],
        	'columns' => [
        		['attribute' => 'info_fdc', 'content' => function ($model) {
        			return Html::a(Yii::$app->formatter->asDate($model->info_fdc),
        						'/clinica/historia/informe/edit?id='.$model->info_id).
        				Html::hiddenInput('info_id', $model->info_id); },
        		 'footer' => Camp::botonReturn('/clinica/historia/informe/edit', 'Nuevo informe')
        		],
        		['attribute' => 'info_destino'],
        		['attribute' => '', 'label' => 'Ver PDF',
        				'content' => function ($model) { return Html::a('PDF',
        						'/clinica/historia/informe/create-pdf?id='.$model->info_id); }
        		],
        		['attribute' => '', 'label' => 'Cuardar PDF',
        				'content' => function ($model) { return Html::a('Guardar',
        						['/clinica/historia/informe/create-pdf', 'id' => $model->info_id, 'guardar' => true]); }
        		],
        	]
        ]);
        return $out;
    }

    public function formInforme ($id = false)
    {
    	$notas = VisitaModel::notas();
    	$model = $id ? InformeModel::findOne($id) : new InformeModel();
    	$out = 	Html::tag('h2', 'Informe para el paciente').
    			Html::beginForm('/clinica/historia/informe/edit', 'post').
    			Html::hiddenInput('info_id', $model->info_id).
    			Camp::textInput('info_destino', $model->info_destino, 'Destinatario').
    			Camp::ckeditor('info_texto', isset($model->info_texto) ? $model->info_texto : $notas, 'Texto del informe', ['height' => '400']).
    			Camp::botonesNormal('/clinica/historia/informe', $id);
    			Html::endForm();

    	return $out;
    }
}
