<?php

namespace dslibs\clinica\historia\presenters;

use Yii;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use dslibs\clinica\historia\models\ConsentimientoModel;
use dslibs\helpers\Camp;
use yii\base\BaseObject;
use dslibs\helpers\Lista;

class ConsentimientoPresenter extends BaseObject
{
    public function gridConsentimiento ()
    {
        $procedimientos = Lista::lista('ci', 'ci_id', 'ci_procedimiento', '', true, 'ci_procedimiento');
        
    	$query = ConsentimientoModel::find()->where(['pci_epis_id' => Yii::$app->session['e']]);
        $dataProvider = new ActiveDataProvider([
        		'query' => $query,
        		'pagination' => ['pageSize' => 30]
        ]);

        $out = GridView::widget([
        	'dataProvider' => $dataProvider,
            'caption' => Html::tag('h2', 'Consentimientos del Paciente'),
        	'summary' => '',
        	'tableOptions' => ['class' => 'table table-sm'],
        	'showFooter' => true,
        	'footerRowOptions' => ['class' => 'panel panel-info'],
        	'columns' => [
        		['attribute' => 'pci_fdc', 'label' => 'Fecha',
        				'content' => function ($model) { return Yii::$app->formatter->asDate($model->pci_fdc).
        						Html::hiddenInput('pci_id', $model->pci_id); }],
        		['attribute' => 'pci_procedimiento', 'label' => 'Prodecimiento',
        				'content' => function ($model) { return Html::a($model->pci_procedimiento,
        						'/clinica/historia/consentimiento/edit?id='.$model->pci_id); },
        				'footer' => Camp::dropDownList('ci_id', '', $procedimientos)
        		],
        		['attribute' => '', 'label' => 'Ver PDF',
        				'content' => function ($model) { return Html::a('PDF',
        						'/clinica/historia/consentimiento/create-pdf?id='.$model->pci_id); },
        				'footer' => Camp::botonAjax('Crear', 'actualiza_tabla_recarga', '/clinica/historia/consentimiento/edit',
        				        ['class' => 'light'])
        		],
        		['attribute' => '', 'label' => 'Cuardar documento PDF',
        				'content' => function ($model) { return Html::a('Guardar',
        						['/clinica/historia/consentimiento/create-pdf', 'id' => $model->pci_id, 'guardar' => true]); },
        		],
        	]
        ]);
        return $out;
    }

    public function formCinformadoEdit ($id = false)
    {
    	$model = $id ? ConsentimientoModel::findOne($id) : new ConsentimientoModel();
    	$out = 	Html::tag('h2', 'Consentimiento informado para el paciente').
    			Html::beginForm('/clinica/historia/consentimiento/edit?id='.$model->pci_id, 'post').
    			Html::hiddenInput('pci_id', $model->pci_id).
    			Camp::textInput('pci_procedimiento', $model->pci_procedimiento).
    			Camp::ckeditor('pci_texto', $model->pci_texto, '',['height' => '600']).
    			//CKEditor::widget(['name' => 'pci_texto', 'value' => $model->pci_texto, 'clientOptions' => ['height' => '600']]).
    			Camp::botonesNormal('/clinica/historia/consentimiento', $id);
    			Html::endForm();

    	return $out;
    }
}
