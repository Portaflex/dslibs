<?php

namespace dslibs\helpers;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\db\Query;
use Yii;

class Access
{
	private $campoLogin;
	private $campoPw;
	private $campoRol;
	private $campoActivo = FALSE;
	private $tablaUsuario;
	private $tablaSesion = 'session';
	private $datosSesion;
	private $campoEstado;

	public function __construct ($params = false)
	{
		if ($params)
		{
			foreach ($params as $k => $v)
			{
				$this->$k = $v;
			}
		}
	}
	
	public function permiso ($ruta)
	{
	    if (isset(Yii::$app->session[$this->campoRol]))
	    {
	        $a = [];
	        $a['r_ruta'] = $ruta;
	        $a['rol_id'] = Yii::$app->session[$this->campoRol];
	        
	        $permiso = (new Query())->select(['r.r_ruta', 'rr.rol_id'])
	        ->from('recurso_rol rr')->leftJoin('recurso r', 'rr.ruta_id = r.r_id')
	        ->where($a)->exists();
	        return ($permiso > 0) OR false;
	    }
	    else return false;
	}

	public function acceder ($url = 'login')
	{
	    if ($post = Yii::$app->request->post())
	    {
	        $login = $post['login'];
	        $pw = $post['pw'];
	        
	        if ($login == '' OR $pw == '')
	        {
	            Yii::$app->session->setFlash('error', 'Los campos usuario y contraseña son obligatorios');
	        }
	        else 
	        {
    	        $usuario = $this->compruebaUsuario($login);
        		
        		if ($usuario)
        		{
        		    if ($this->compruebaSesion($login))
        		    {
        		        Yii::$app->db->createCommand()->delete($this->tablaSesion,
        		            ['login' => $login])->execute();
        		    }
        		    
        	        if ($this->compruebaPw($login, $pw))
        	        {
        	            Yii::$app->db->createCommand()
        	            ->update('usuario', ['user_intentos' => 0], ['user_id' => $usuario['user_id']])
        	            ->execute();
        	            
        	            $this->datosSesion($usuario);
        	            
        	            $fechaPw = new \DateTime($usuario['user_fechapw']);
        	            $hoy = new \DateTime('now');
        	            $interval = $fechaPw->diff($hoy)->format('%r%a');
        	            
        	            if ($interval > 180)
        	            {
        	                Yii::$app->session->setFlash('error', 'Su contraseña ha caducado. Por favor '.Html::a('cambiela',
        	                    Url::toRoute('admin/su-usuario')));
        	            }
        	        }
        	        else 
        	        {
        	            if ($usuario['user_intentos'] < 3)
        	            {
        	                Yii::$app->db->createCommand()
        	                ->update('usuario', ['user_intentos' => $usuario['user_intentos'] + 1], ['user_id' => $usuario['user_id']])
        	                ->execute();
        	                
        	                Yii::$app->session->setFlash('error', 'Contraseña incorrecta');
        	            }
        	            elseif ($usuario['user_intentos'] == '3')
        	            {
        	                Yii::$app->session->setFlash('error', 'Ha superado el número de intentos fallidos de acceso.<br>
                                Su usuario ha sido bloqueado.<br>Póngase en contacto con el administrador. Gracias.');
        	            }
        	        }
        		}
        		else
        		{
        		    Yii::$app->session->setFlash('error', 'El usuario introducido no existe');
        		}
    		}
	    }
		
		return $this->muestraError().$this->formAcceso($url);
	}
	
	public function salir ()
	{
	    Yii::$app->db->createCommand()->delete($this->tablaSesion, ['id' => Yii::$app->session->getId()])->execute();
	}
	
	private function compruebaUsuario ($login)
	{
	    $out = (new Query())->from($this->tablaUsuario)
	               ->where([$this->campoLogin => $login, $this->campoActivo => 1])->one();
	    
	    return $out;
	}
	
	private function compruebaSesion ($login)
	{
	    $out = (new Query())->from($this->tablaSesion)->where(['login' => $login])->exists();
	    
	    return $out;
	}

	private function compruebaPw ($login, $pw)
	{
		$usuario = (new Query())->from($this->tablaUsuario)
		              ->where([$this->campoLogin => $login])
		              ->one();
		
		if (Yii::$app->getSecurity()->validatePassword($pw, $usuario[$this->campoPw]))
		{
			$this->datosSesion($usuario);
			return true;
		}
		else
		{
			return false;
		}
	}

	private function datosSesion ($usuario = false)
	{   
	    foreach ($this->datosSesion as $k => $v)
		{
			if (is_array($v))
			{
				foreach ($v as $u)
				{
					$f[] = $usuario[$u];
				}
				Yii::$app->session[$k] = implode(' ', $f);
			}
			else
			{
			    Yii::$app->session[$k] = $usuario[$v];
			}
		}
		
		Yii::$app->db->createCommand()
		   ->update($this->tablaSesion,['login' => $usuario['user_login']], ['id' => session_id()])
		   ->execute();
	}
	
	private function muestraError ()
	{
	    return Html::tag('h3', Yii::$app->session->getFlash('error'), ['style' => 'color:red']);
	}

	private function formAcceso ($url = 'login')
	{
	    if (! isset(Yii::$app->session['userId']))
	    {
	        $out = Html::tag('h2', 'Formulario de acceso').
	        Html::beginForm("/$url", 'post').
    		Camp::textInput('login', '', 'Usuario').
    		Camp::pwInput('pw', '', 'Contraseña').
    		Camp::botonSend('Entrar');
    		Html::endForm();
    		return $out;
	    }
	}
}
