<?php

namespace dslibs\helpers;

use yii\db\Query;
use yii\helpers\Url;

class Menus
{

    public function menu ($p)
    {
        if ($data = apc_fetch(implode('.', $p)))
        {
            $q = new Query();
            $q->select(
                    [
                            $p['id'],
                            $p['nombre'],
                            $p['url']])->from($p['tabla']);
            if ($p['donde'] != '')
            {
                foreach ($p['donde'] as $donde)
                {
                    $q->where($donde);
                }
            }
            $data = $q->orderBy($p['nombre'])->all();
            apc_add(implode('.', $p), $data);
        }

        $conf = [
                'menu_tipo' => $p['tipo'] == '' ? 'vmenu' : 'hmenu',
                'url' => $p['url'] != '' ? $p['url'] : '',
                'nombre' => $p['nombre']];

        return $this->gen_menu($data, $conf);
    }
    
    function gen_menu ($items = '', $config = '')
    {
        $out = '<ul class="' . $config['menu_tipo'] . '">' . "\n";
        foreach ($items as $item)
        {
            if (is_array($config['url']))
            {
                $url = $config['url'][0] . "/" . $item[$config['url'][1]];
            }
            else
            {
                $url = $item[$config['url']];
            }
            $out .= "<li><a href=" . Url::to($url) . "><b>" .
                $item[$config['nombre']] . "</b></a></li>" . "\n";
        }
        $out .= "</ul>" . "\n";
        return $out;
    }
}
