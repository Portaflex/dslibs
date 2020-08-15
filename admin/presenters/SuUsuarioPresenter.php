<?php

/**
 * Esta clase presenta los datos dinámicos del control de su usuario de la aplicación
 * en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\presenters;

use Yii;
use yii\bootstrap4\Html;
use dslibs\helpers\Camp;
use yii\base\BaseObject;
use dslibs\admin\models\UsuarioModel;

class SuUsuarioPresenter extends BaseObject
{
    public static function formSuUsuario ()
    {
        $model = UsuarioModel::findOne(Yii::$app->session['userId']);
        $booleano = ['Si' => 1, 'No' => 0];
        $out = Html::tag('h2', Html::a('Datos del usuario', '/admin/usuario')).
            "<div class='row'><div class='col-sm-6'><p>" . "\n".
            Html::beginForm('/admin/su-usuario', 'post').
            Html::hiddenInput('user_id', $model['user_id']).
            Html::tag('h3', 'Login: '.Html::label($model['user_login'])).
            Camp::textInput('user_nom', $model['user_nom'], 'Nombre').
            Camp::textInput('user_apell1', $model['user_apell1'], 'Primer apellido').
            Camp::textInput('user_apell2', $model['user_apell2'], 'Segundo apellido').
            Camp::textInput('user_ncol', $model['user_ncol'], 'Colegiado').
            Camp::botonesNormal('/admin/su-usuario').
            "</p></div><div class='col-sm-6'><p>"."\n".
            Camp::textInput('user_grado', $model['user_grado'], 'Grado').
            Camp::textInput('user_espec', $model['user_espec'], 'Especialidad').
            Camp::textInput('user_email', $model['user_email'], 'E-mail').
            Camp::textInput('user_movil', $model['user_movil'], 'Teléfono móvil').
            Camp::dropDownList('user_recibe', $model['user_recibe'], $booleano, 'Recibe comunicados del Foro').
            Html::endForm().
            "</p></div></div>"."\n".
            "<div class='row'><div class='col-sm-6'>"."\n".
            Html::tag('h2', 'Cambiar contraseña').
            Html::beginForm('/admin/su-usuario', 'post').
            Html::hiddenInput('user_id', $model['user_id']).
            Camp::textInput('user_pw', '', 'Nueva contraseña').
            Camp::botonesNormal('/admin/su-usuario').
            Html::endForm().
            "</div>"."\n";
        if (isset($model['user_email_portal']))
        {
            $out .= "<div class='col-sm-6'>"."\n".
            Html::tag('h2', 'Cambiar contraseña de correo').
            Html::beginForm('/admin/su-usuario', 'post').
            Html::hiddenInput('user_id', $model['user_id']).
            Camp::textInput('clave', '', 'Nueva contraseña de correo').
            Camp::botonesNormal('/admin/su-usuario').
            Html::endForm().
            "</div>"."\n";
        }
        $out .= "</div>"."\n";
            
        return $out;
    }
}
