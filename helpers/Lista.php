<?php

namespace dslibs\helpers;

use yii\db\Query;
use yii\helpers\ArrayHelper;

class Lista
{

    public static function lista ($tabla, $id, $nombre, $cond = false, $selec = true, $order = false)
    {
        $opcion = array();

    	if (is_array($nombre))
        {
        	$nombre = "concat_ws(' ', ".implode(', ', $nombre).") as nombre";
        }
        else
        {
        	$nombre = "$nombre as nombre";
        }

    	$q = (new Query())->select([$id,$nombre])->from($tabla);

        if ($cond)
        {
            foreach ($cond as $c => $d)
            {
                if (is_array($d))
                {
                    $q->where($d);
                }
                else 
                {
                    $q->where([$c => $d]);
                }
            }
        }
        
        $order = $order ?? 'nombre';
        
        $query = $q->orderBy($order)->all();

        if ($selec) $opcion[''] = 'Seleccionar';

        foreach ($query as $row)
        {
        	$opcion[$row[$id]] = $row['nombre'];
        }

        return $opcion;
    }

    public static function listaSanitario ()
    {
    	$q = (new Query())
    	->select(['sani_id', "concat_ws(' ', sani_nombres, sani_apellido1) as sanitario"])
    	->from('sanitario')->where(['sani_agenda' => 1])
    	->indexBy('sani_id')->all();
    	return ArrayHelper::map($q, 'sani_id', 'sanitario');
    }
    
    public static function listaMenu($grupo = '', $orden = 'm_valor')
    {
        return self::lista('menu', 'm_valor', 'm_texto', ['m_ident' => $grupo], TRUE, $orden);
    }
    
    public static function listaSimple($grupo = '', $orden = 'm_valor')
    {
        return self::lista('menu', 'm_valor', 'm_texto', ['m_ident' => $grupo], FALSE, $orden);
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
}
// Fin de documento
