<?php

/**
 * Esta clase presenta los datos dinámicos del control de correos de la aplicación
 * en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\presenters;

use Yii;
use yii\grid\GridView;
use dslibs\helpers\Camp;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\base\BaseObject;
use dslibs\admin\models\CorreoModel;

class CorreoPresenter extends BaseObject
{
    public function edit ($data)
    {
        if (isset($data['id']) && $data['id'] != '')
        {
            $model = CorreoModel::findOne($data['id']);
            if ($data['action'] == 'save')
            {
                $model->save();
                $id = $model->id;
            }
            if ($data['action'] == 'delete')
            {
                $model->attributes = $data;
                $model->delete();
                return Yii::$app->response->redirect('/admin/correo');
            }
        }
        else 
        {
            $clave = Yii::$app->getSecurity()->generateRandomString(8);
            $data['password'] = '{SHA512}'.base64_encode(hash('sha512', $clave, TRUE));
            $model = new CorreoModel();
            unset($data['id']);
            $model->attributes = $data;
            if ($data['action'] == 'save')
            {
                self::correoAlta($model, $clave);
                $model->save();
                $id = $model->id;
            }
        }
        return $id;
    }
    
    public static function gridMail ()
    {
        $out = GridView::widget([
            'dataProvider' => new ActiveDataProvider(['query' => CorreoModel::find()]),
            'caption' => Html::tag('h2', 'Control de Correo').Camp::botonReturn('/admin/correo/edit', 'Nuevo'),
            'tableOptions' => ['class' => 'table table-sm'],
            'columns' => [
                  'nombres',
                  'usuario',
                  ['attribute' => 'email', 'content' => function ($model) {
                       return Html::a($model->email, ['/admin/correo/edit', 'id' => $model->id]); }
                  ],
                  'alt_email',
                  'accion',
                  'redirect',
               ]
        ]);
        return $out;
    }
    
    public static function formMail ($id = false)
    {
        $model = $id ? CorreoModel::findOne($id) : new CorreoModel();
        $out = Html::tag('h2', 'Formulario de correo').
        Html::beginForm('/admin/correo/edit', 'post').
        Html::hiddenInput('id', $model->id).
        Camp::textInput('dominio', $model->dominio, 'Dominio').
        Camp::textInput('nombres', $model->nombres, 'Nombres').
        Camp::textInput('usuario', $model->usuario, 'Usuario').
        Camp::textInput('id_portal', $model->id_portal, 'ID del Portal').
        Camp::textInput('email', $model->email, 'Email').
        Camp::textInput('alt_email', $model->alt_email, 'Email alternativo').
        Camp::textInput('accion', $model->accion, 'Acción a realizar').
        Camp::textInput('redirect', $model->redirect, 'Redirect').
        Camp::botonesNormal('/admin/correo', $id).
        Html::endForm();
        return $out;
    }
    
    public static function correoAlta ($model, $pw)
    {
        $objeto = "Correo Clínica de C.O.T.";
        $cuerpo = "Apreciado $model->nombres:<br><br>
        Ha sido dado de alta en el sistema de correo de Clínica de C.O.T.<br><br>
        Su usuario es: <b>$model->email</b><br>
        Su contraseña es: <b>$pw</b><br><br>
        El servidor de correo entrante es:<br>
        imap.clinicadecot.es<br>
        Puerto: 143<br>
        Protocolo: STARTTLS<br><br>
        El servidor de correo saliente es:<br>
        smtp.clinicadecot.es<br>
        Puerto: 25<br>
        Protocolo: STARTTLS<br><br>
        Saludos cordiales<br>
        Clinica de C.O.T.";
        
        Yii::$app->mailer->compose()
        ->setFrom('admin@paragaleno.com')
        ->setTo($model->alt_email)
        ->setSubject($objeto)
        ->setTextBody('Plain text content')
        ->setHtmlBody($cuerpo)
        ->send();
    }
}
