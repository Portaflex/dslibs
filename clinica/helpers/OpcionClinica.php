<?php

namespace dslibs\clinica\helpers;

use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\base\Behavior;
use dslibs\helpers\Lista;

class OpcionClinica extends Behavior
{   
    public static function antecedente ()
    {
        return Lista::lista('antecedente', 'antec_id', 'antec_desc', '', true, 'antec_desc');
    }
    
    public static function booleano ()
    {
        return self::listaMenuSimple('booleano');
    }
    
    public static function consentimiento ()
    {
        return Lista::lista('ci', 'ci_id', 'ci_procedimiento', '', true, 'ci_procedimiento');
    }
    
    public static function departamento ()
    {
        return self::listaMenu('departamento');
    }
    
    public static function diagnostico ()
    {
        return Lista::lista('dx', 'dx_id', 'dx_descripcion', ['dx_parent' => null], true, 'dx_descripcion');
    }
    
    public static function diagnosticoSub ($parent = '') 
    {
        return Lista::lista('dx', 'dx_id', 'dx_descripcion', ['dx_parent' => $parent]);
    }
    
    public static function estadoCita ()
    {
        return self::listaMenu('estado_cita');
    }
    
    public static function estadoCobro ()
    {
        return self::listaMenu('estado_cobro');
    }
    
    public static function estadoCobroFactura ()
    {
        return self::listaMenuSimple('estado_cobro');
    }
    
    public static function estadoEpisodio ()
    {
        return self::listaMenuSimple('estado_episodio');
    }
    
    public static function estadoPresentado ()
    {
        return self::listaMenuSimple('estado_presentado');
    }
    
    public static function estadoIq ()
    {
        return self::listaMenu('estado_iq');
    }
    
    public static function estadoPago ()
    {
        return self::listaMenu('estado_pago');
    }
    
    public static function financiador ()
    {
        return Lista::lista('financiador', 'finan_id', 'finan_empresa', ['finan_activo' => 1], true, 'finan_empresa');
    }
    
    public static function quirofano ()
    {
        return self::listaMenu('quirofano');
    }
    
    public static function motivoAlta ()
    {
        return self::listaMenu('motivo_alta');
    }
    
    public static function recomAlta ()
    {
        return Lista::lista('recomen', 'recom_id', 'recom_descrip', ['recom_tipo' => 1]);
    }
    
    public static function recomTratamiento ()
    {
        return Lista::lista('recomen', 'recom_id', 'recom_descrip', ['recom_tipo' => 2]);
    }
       
    public static function remitente ()
    {
        return self::listaMenu('remitente');
    }
    
    public static function saniDep ($dep = '')
    {
        $query = (new Query())->select([
            's.sani_id', "concat_ws(' ', s.sani_nombres, s.sani_apellido1, s.sani_apellido2) as nombre"])
        ->from('sanitario s')
        ->leftJoin('sanitario_dep sd', 's.sani_id = sd.sd_sani_id')
        ->leftJoin('menu m', 'sd.sd_dep_id = m_valor and m_parent = 364')
        ->where(['sd.sd_dep_id' => $dep, 's.sani_agenda' => 1])
        ->all();
        
        return ArrayHelper::map($query, 'sani_id', 'nombre') + ['' => 'Seleccionar'];
    }
    
    public static function saniCita ()
    {
        return Lista::lista('sanitario', 'sani_id', ['sani_nombres', 'sani_apellido1', 'sani_apellido2'],
            ['sani_agenda' => 1]);
    }
    
    public static function saniOpera ()
    {
        return Lista::lista('sanitario', 'sani_id', ['sani_nombres', 'sani_apellido1', 'sani_apellido2'],
            ['sani_opera' => 1]);
    }
    
    public static function saniRol ()
    {
        return self::listaMenuSimple('sani_rol');
    }
    
    public static function tipoAlbaran ()
    {
        return self::listaMenuSimple('tipo_albaran');
    }
    
    public static function tipoCita ()
    {
        return self::listaMenuSimple('tipo_cita');
    }
    
    public static function tipoIngreso ()
    {
        return self::listaMenuSimple('tipo_ingreso');
    }
    
    public static function tipoRecomen ()
    {
        return self::listaMenu('tipo_recomen');
    }
    
    private function listaMenu($grupo = '', $orden = 'm_valor')
    {
        return Lista::lista('menu', 'm_valor', 'm_texto', ['m_ident' => $grupo], true, $orden);
    }
    
    private function listaMenuSimple ($grupo = '', $orden = 'm_valor')
    {
        return Lista::lista('menu', 'm_valor', 'm_texto', ['m_ident' => $grupo], false, $orden);
    }
}