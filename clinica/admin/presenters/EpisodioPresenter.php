<?php

/**
 * Esta es la clase implementa el presentador control de episodios
 * de pacientes de la aplicación clínica.
 * Está ubicada en la sección Gestión de la aplicación clínica.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\clinica\admin\presenters;

use Yii;
use yii\base\BaseObject;
use yii\grid\GridView;
use yii\helpers\Html;
use dslibs\clinica\admin\models\EpisodioModel;
use dslibs\helpers\Camp;

class EpisodioPresenter extends BaseObject
{
   
    public static function gridEpisodio()
    {
        $searchModel = new EpisodioModel();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        $out = GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'caption' => Html::tag('h2', 'Control de Episodios'),
            'tableOptions' => ['class' => 'table table-sm table-hover'],
            'columns' => [
                ['attribute' => 'epis_pac_id', 'content' => function ($model) {
                    return  Html::hiddenInput('epis_id', $model->epis_id).
                    Camp::textInput('epis_pac_id', $model->epis_pac_id);
                }, 'options' => ['style'=>'width:80px;']],
                ['attribute' => 'epis_id', 'content' => function ($model) {
                    return Html::a($model->epis_id, ['/clinica/historia/visita', 'e' => $model->epis_id,
                        'p' => $model->epis_pac_id, 'f' => $model->epis_finan_id, 'd' => $model->epis_dep,
                         'ee' => $model->epis_estado]); }, 'options' => ['style'=>'width:80px;']],
                ['attribute' => 'financiador', 'value' => 'financiador.finan_empresa', 'options' => ['style'=>'width:100px;']],
                ['attribute' => 'epis_expediente', 'value' => 'epis_expediente', 'options' => ['style'=>'width:80px;']],
                /* ['attribute' => 'pac', 'label' => 'Paciente', 'value' => function ($model) {
                    return $model->paciente['pac_nom'].' '.$model->paciente['pac_apell1'].' '.$model->paciente['pac_apell2'];
                }], */
                ['attribute' => 'pac_nom', 'value' => 'paciente.pac_nom', 'label' => 'Nombre'],
                ['attribute' => 'pac_apell1', 'value' => 'paciente.pac_apell1', 'label' => 'Apellido 1'],
                ['attribute' => 'pac_apell2', 'value' => 'paciente.pac_apell2', 'label' => 'Apellido 2'],
                ['attribute' => 'epis_fdc', 'value' => 'epis_fdc', 'format' => 'date',
                    'options' => ['style'=>'width:100px; text-align:right;']],
                ['attribute' => 'citas', 'content' => function ($model) {
                    return count($model->citas);
                }],
                ['options' => ['style'=>'width:100px;'], 'content' => function ($model) {
                    return 	Camp::botonesAjax('/clinica/admin/episodio/edit', 'actualiza');
                }]
            ]
        ]);
        return $out;
    }
}
