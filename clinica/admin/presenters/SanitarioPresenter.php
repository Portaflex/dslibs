<?php

namespace dslibs\clinica\admin\presenters;

use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\base\BaseObject;
use dslibs\clinica\admin\models\SanitarioModel;
use dslibs\helpers\Camp;
use dslibs\helpers\Lista;

class SanitarioPresenter extends BaseObject
{
    public function gridSanitario ()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => SanitarioModel::find()->joinWith(['saniAgenda a', 'saniOpera o']),
            'sort' => [
                'attributes' => [
                    'sani_nombres',
                    'sani_apellido1',
                    'sani_apellido2',
                    'sani_telefono',
                    'sani_ncolegiado',
                    'sani_e_mail',
                    'saniAgenda.nombre' => [
                        'asc' => ['a.m_texto' => SORT_ASC],
                        'desc' => ['a.m_texto' => SORT_DESC]
                    ],
                    'saniOpera.nombre' => [
                          'asc' => ['o.m_texto' => SORT_ASC],
                          'desc' => ['o.m_texto' => SORT_DESC]
                    ]
                ]
            ]
        ]);
        $out = Html::tag('h2', Html::a('Sanitarios del Portal', '/clinica/admin/sanitario'));
        $out .= Html::tag('h4', Html::a('Nuevo Sanitario', '/clinica/admin/sanitario/edit'));
        $out .= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-default'],
                'columns' => [
                        ['content' => function ($model) {
                            return Html::a($model->sani_id, ['/clinica/admin/sanitario/edit', 'id' => $model->sani_id]);
                        }],
                        'sani_nombres',
                        'sani_apellido1',
                        'sani_apellido2',
                        'sani_telefono',
                        'sani_ncolegiado',
                        'sani_e_mail',
                        ['attribute' => 'saniAgenda.nombre', 'label' => 'Tiene agenda'],
                        ['attribute' => 'saniOpera.nombre', 'label' => 'Opera']
                ]
        ]);
        return $out;
    }
    
    public function gridSanitarioDep ($id)
    {
        $dep_id = self::departamento();
        $query = SanitarioModel::findOne($id)->getSanitarioDep();
        $udepProvider = new ActiveDataProvider(['query' => $query]);
        
        $out = Html::tag('h3', 'Departamentos a los que pertenece');
        $out .= GridView::widget([
                'dataProvider' => $udepProvider,
                'tableOptions' => ['class' => 'table table-default'],
                'summary' => '',
                'showFooter' => true,
                'columns' => [
                        ['attribute' => 'rol_id', 'label' => 'Departamentos', 'content' => function ($model) use ($dep_id) {
                            return 	Html::dropDownList('sd_dep_id', $model['sd_dep_id'], $dep_id, ['class' => 'form-control input-sm']).
                            Html::hiddenInput('sd_sani_id', $model['sd_sani_id']).
                            Html::hiddenInput('sd_id', $model['sd_id']); },
                            'footer' => Html::dropDownList('sd_dep_id', '', $dep_id, ['class' => 'form-control input-sm']).
                            Html::hiddenInput('sd_sani_id', $id)
                            ],
                            ['attribute' => null, 'content' => function ($model, $key, $index) {
                                return  Camp::botonAjax('Ok', "actualiza_tabla_recarga",
                                        '/clinica/admin/sanitario/edit-dep', ['class' => 'success', 'action' => 'save']).' '.
                                        Camp::botonAjax('Del', "actualiza_tabla_recarga",
                                        '/clinica/admin/sanitario/edit-dep', ['class' => 'danger', 'action' => 'delete']); },
                               'footer' => Camp::botonAjax('Nuevo', "actualiza_tabla_recarga",
                                        '/clinica/admin/sanitario/edit-dep', ['class' => 'info', 'action' => 'save'])
                            ],
                ]
                ]);
        return $out;
    }
    
    public function formSanitario ($model)
    {
        $agenda = [1 => 'Si', 0 => 'No'];
        
        $out = Html::tag('h2', 'Sanitario').
        Html::beginForm('/clinica/admin/sanitario/edit', 'post').
        Html::errorSummary($model).
        Html::hiddenInput('sani_id', $model->sani_id).
        "<div class='col-sm-6'><p>" . "\n".
        Camp::textInput('sani_nombres', $model->sani_nombres, 'Nombres').
        Camp::textInput('sani_apellido1', $model->sani_apellido1, 'Primer apellido').
        Camp::textInput('sani_apellido2', $model->sani_apellido2, 'Segundo apellido').
        Camp::textInput('sani_especialidad', $model->sani_especialidad, 'Especialidad').
        Camp::textInput('sani_dni', $model->sani_dni, 'DNI').
        "</p></div><div class='col-sm-6'><p>" . "\n".
        Camp::textInput('sani_ncolegiado', $model->sani_ncolegiado, 'Nº Colegiado').
        Camp::textInput('sani_telefono', $model->sani_telefono, 'Teléfono').
        Camp::textInput('sani_e_mail', $model->sani_e_mail, 'E-mail').
        Camp::dropDownList('sani_agenda', $model->sani_agenda, $agenda, 'Tiene agenda').
        Camp::dropDownList('sani_opera', $model->sani_opera, $agenda, 'Opera').
        "</p></div><div class='col-sm-12'>" . "\n".
        Camp::botonesNormal('/clinica/admin/sanitario', $model->sani_id).
        "</dic>".
        Html::endForm();
        return $out;
    }
    
    private function departamento()
    {
        return Lista::lista('menu', 'm_valor', 'm_texto', ['m_ident' => 'departamento'], true, 'm_valor');
    }
}
