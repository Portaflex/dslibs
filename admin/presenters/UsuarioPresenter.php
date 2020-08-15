<?php

/**
 * Esta clase presenta los datos dinámicos del control de usuarios de la aplicación
 * en el apartado de Gestion.
 *
 * @author: Diego Sala
 * @link http://paragaleno.com/
 * @copyright Copyright (c) 2020 Paragaleno
 * @license BSD
 */

namespace dslibs\admin\presenters;

use dslibs\helpers\Camp;
use dslibs\helpers\Lista;
use dslibs\admin\models\UsuarioModel;
use dslibs\admin\models\UsuarioDepModel;
use Yii;
use yii\bootstrap4\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\base\BaseObject;

class UsuarioPresenter extends BaseObject
{
    public static function edit ($model)
    {
    	if ($data = Yii::$app->request->post())
    	{
            if (isset($data['user_id']) && $data['user_id'] != '')
            {
                $model->attributes = $data;
        	    
        	    if ($data['action'] == 'save')
        	    {
        	        if ($model->save())
        	        {
        	            return $model;
        	        }
        	    }
        	    if ($data['action'] == 'delete')
        	    {
        	        if ($model->delete()) return 'delete';
        	    }
        	}
        	elseif ($data['user_id'] == '')
        	{
        	    // Generar y cifrar una contraseña aleatoria.
        	    $pw = Yii::$app->getSecurity()->generateRandomString(8);
        	    $data['user_pw'] = Yii::$app->getSecurity()->generatePasswordHash($pw);
        	    
        	    unset($data['user_id']);
        	    $model->attributes = $data;
        	    
        	    if ($model->save())
        	    {
        	        self::correoUsuarioAlta($model, $pw);
        	    }
        	    return $model;
        	}
    	}
    }

    public static function editDep ()
    {
    	if ($data = Yii::$app->request->post())
    	{
    		if (isset($data['action']) && $data['action'] == 'save')
    		{
    			if (! isset($data['ud_id']))
    			{
    			    $model = new UsuarioDepModel();
    				$model->attributes = $data;
    			}
    			else
    			{
    			    $model = UsuarioDepModel::findOne($data['ud_id']);
    				$model->attributes = $data;
    			}
    			$model->save();

    		}
    		if (isset($data['action']) && $data['action'] == 'delete')
    		{
    		    $model = UsuarioDepModel::findOne($data['ud_id']);
    			$model->delete();
    		}
    		return $model->ud_uid;
    	}
    }
    
    public static function reset ($id)
    {
        // Generar y cifrar una contraseña aleatoria.
        $pw = Yii::$app->getSecurity()->generateRandomString(8);
        $nueva_clave = Yii::$app->getSecurity()->generatePasswordHash($pw);
        $model = UsuarioModel::findOne($id);
        $model->user_pw = $nueva_clave;
        $model->user_fechapw = date('yy-m-d');
        $model->user_intentos = 0;
        if ($model->save())
        {
            self::correoUsuarioCambioClave($model, $pw);
            Yii::$app->session->setFlash('usuario',
                'Se ha cambiado la clave del usuario y se le ha enviado un e-mail. Gracias');
        }
    }

    public static function gridUsuario ()
    {
    	$dataProvider = new ActiveDataProvider([
    	        'query' => UsuarioModel::find()->joinWith(['activo a', 'recibe r', 'erecibe e'])->orderBy('user_id'),
    	        'sort' => [
    	                'attributes' => [
    	                        'user_id', 'user_login', 'user_nom', 'user_apell1', 'user_apell2', 'user_email',
    	                        'user_fechapw', 'user_intentos',
    	                        'activo.nombre' => [
    	                                'asc' => ['a.m_texto' => SORT_ASC],
    	                                'desc' => ['a.m_texto' => SORT_DESC],
    	                        ],
    	                        'recibe.nombre' => [
    	                                'asc' => ['r.m_texto' => SORT_ASC],
    	                                'desc' => ['r.m_texto' => SORT_DESC]
    	                        ],
    	                        'erecibe.nombre' => [
    	                               'asc' => ['e.m_texto' => SORT_ASC],
    	                               'desc' => ['e.m_texto' => SORT_DESC]
    	                        ]
    	                ]
    	        ]
    	]);
    	
    	$out = Html::tag('h2', Html::a('Usuarios del Portal', '/admin/usuario'));
    	$out .= Html::tag('h4', Html::a('Nuevo usuario', '/admin/usuario/edit'));
    	$out .= GridView::widget(
    	[
    		'dataProvider' => $dataProvider,
    		//'filterModel' => $searchModel,
    	    'summary' => '',
    	    'tableOptions' => ['class' => 'table table-sm'],
    		'columns' => [
    			['attribute' => 'user_id', 'label' => 'ID', 'content' => function($model) {
    				return Html::a($model->user_id, '/admin/usuario/edit?id='.$model->user_id);
    			}],
    			'user_login', 'user_nom', 'user_apell1', 'user_apell2', 'user_email',
    			['attribute' => 'activo.nombre', 'label' => 'Activo'],
    			//['attribute' => 'recibe.nombre', 'label' => 'Recibe'],
    			//['attribute' => 'erecibe.nombre', 'label' => 'E-Recibe'],
    			['attribute' => 'user_fechapw', 'format' => 'date'],
    			'user_intentos'
    		]
    	]);
    	return $out;
    }

    public static function gridUsuarioDep ($user_id)
    {
    	$dep_id = self::lista('departamento');
    	
    	$query = UsuarioModel::findOne($user_id)->getUsuarioDep();
    	$udepProvider = new ActiveDataProvider(['query' => $query]);

    	$out = Html::tag('h3', 'Departamentos');
    	$out .= GridView::widget([
    		'dataProvider' => $udepProvider,
    		'tableOptions' => ['class' => 'table table-sm'],
    		'summary' => '',
    		'showFooter' => true,
    		'columns' => [
    			['attribute' => 'rol_id', 'label' => 'Departamentos', 'content' => function ($model) use ($dep_id) {
    				return 	Html::dropDownList('ud_depid', $model['ud_depid'], $dep_id, ['class' => 'form-control input-sm']).
    						Html::hiddenInput('ud_uid', $model['ud_uid']).
    						Html::hiddenInput('ud_id', $model['ud_id']); },
    				'footer' => Html::dropDownList('ud_depid', '', $dep_id, ['class' => 'form-control input-sm']).
    						Html::hiddenInput('ud_uid', $user_id)
    			],
    			['attribute' => null, 'content' => function ($model, $key, $index) {
    				return  Camp::botonAjax('Ok', "actualiza_tabla_recarga",
    							'/admin/usuario/edit-dep', ['class' => 'success', 'action' => 'save']).' '.
    						Camp::botonAjax('Del', "actualiza_tabla_recarga",
    							'/admin/usuario/edit-dep', ['class' => 'danger', 'action' => 'delete']); },
    				'footer' => Camp::botonAjax('Nuevo', "actualiza_tabla_recarga",
    							'/admin/usuario/edit-dep', ['class' => 'info', 'action' => 'save'])
    			],
    		]
    	]);
    	return $out;
    }

    public static function formUsuario ($model = false)
    {
    	$rol = self::lista('usuario_rol');
    	$booleano =  [1 => 'Si', 0 => 'No'];
    	$out = Html::tag('h2', Html::a('Datos del usuario', '/admin/usuario')).
    	    "<div class='row'><div class='col-sm-4'>" . "\n".
    	    Html::beginForm('/admin/usuario/edit', 'post').
    	    Html::errorSummary($model).
	    	Html::hiddenInput('user_id', $model->user_id).
	    	Camp::textInput('user_login', $model->user_login,'Login').
	    	Camp::textInput('user_nom', $model->user_nom, 'Nombre').
	    	Camp::textInput('user_apell1', $model->user_apell1, 'Primer apellido').
	    	Camp::textInput('user_apell2', $model->user_apell2, 'Segundo apellido').
	    	//Camp::textInput('user_ncol', $model->user_ncol, 'Colegiado').
	    	//Camp::textInput('user_grado', $model->user_grado, 'Grado').
	    	"</div><div class='col-sm-4'>"."\n".
	    	Camp::textInput('user_espec', $model->user_espec, 'Especialidad').
	    	Camp::textInput('user_email', $model->user_email, 'E-mail').
	    	//Camp::textInput('user_email_portal', $model->user_email_portal, 'E-mail del portal').
	    	Camp::textInput('user_movil', $model->user_movil, 'Teléfono móvil').
	    	"</div><div class='col-sm-4'>"."\n".
	    	Camp::dropDownList('user_group', $model->user_group, $rol, 'Grupo').
	    	//Camp::dropDownList('user_recibe', $model->user_recibe, $booleano, 'Recibe comunicaciones del Foro').
	    	//Camp::dropDownList('user_erecibe', $model->user_erecibe, $booleano, 'Recibe comunicaciones del Foro Enfermería').
	    	Camp::dropDownList('user_activo', $model->user_activo, $booleano, 'Activo').
	    	Camp::textInput('user_intentos', $model->user_intentos, 'Fallos de login').
	    	Camp::botonesNormal('/admin/usuario', $model->user_id).
	    	Html::endForm().
	    	Html::tag('h3', Html::a('Reset de clave', ['/admin/usuario/reset', 'id' => $model->user_id])).
	    	Yii::$app->session->getFlash('usuario').
	    	"</div></div>"."\n";
    	return $out;
    }
    
    public static function correoUsuarioCambioClave ($model, $pw)
    {
        $nombre = Yii::$app->params['nombre'];
        $url = Yii::$app->params['url'];
        $objeto = "Cambio de contraseña en $nombre";
        $cuerpo = "Apreciado $model->user_nom $model->user_apell1:<br><br>
        Se ha cambiado tu contraseña de acceso para entrar en $nombre<br><br>
        Tu usuario es: $model->user_login<br>
        Tu nueva clave es: $pw<br><br>
        Entra en el portal $url con tu nueva contraseña.<br><br>
        Saludos cordiales<br>
        $nombre";
        
        Yii::$app->mailer->compose()
        ->setFrom(Yii::$app->params['adminEmail'])
        ->setTo($model->user_email)
        ->setSubject($objeto)
        ->setTextBody('Plain text content')
        ->setHtmlBody($cuerpo)
        ->send();
    }
    
    public static function correoUsuarioAlta ($model, $pw = '')
    {
        $nombre = Yii::$app->params['nombre'];
        $url = Yii::$app->params['url'];
        $objeto = "Alta en el portal $nombre";
        $contenido = "Apreciado $model->user_nom $model->user_apell1:<br><br>
    	Has sido dado de alta en el portal $nombre<br><br>
    	Tu usuario es: $model->user_login <br>
        Tu clave es $pw<br><br>
        Entra en el portal $url con tus nuevas credenciales<br><br>
        Saludos cordiales<br>
        $nombre";
        
        Yii::$app->mailer->compose()
        ->setFrom(Yii::$app->params['adminEmail'])
        ->setTo($model->user_email)
        ->setSubject($objeto)
        ->setTextBody('Plain text content')
        ->setHtmlBody($contenido)
        ->send();
    }
    
    private function lista($ident)
    {
        return Lista::lista('menu', 'm_valor', 'm_texto', ['m_ident' => $ident], true, 'm_valor');
    }
}
