<?php

namespace dslibs\admin\helpers;

use yii\helpers\ArrayHelper;
use Yii;
use yii\base\Behavior;
use dslibs\helpers\Lista;
use dslibs\admin\models\UsuarioModel;

class OpcionAdmin extends Behavior
{
    
    public static function booleano ()
    {
        return self::listaMenu('booleano');
    }
    
    public static function usuarioRol ()
    {
        return self::listaMenu('usuario_rol');
    }
    
    public static function usuarioDep ()
    {
        $query = UsuarioModel::findOne(Yii::$app->session['userId']);
        return ArrayHelper::map($query->departamentos, 'm_valor', 'm_texto');
    }
    
    private function listaMenu($grupo = '', $orden = 'm_valor')
    {
        return Lista::lista('menu', 'm_valor', 'm_texto', ['m_ident' => $grupo], true, $orden);
    }
}