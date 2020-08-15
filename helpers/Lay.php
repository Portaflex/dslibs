<?php

namespace dslibs\helpers;

class Lay
{
    static public function prep_container ($data)
    {
        $salida = '';
        if (! is_array($data))
        {
            $salida .= $data;
        }
        else
        {
            foreach ($data as $a => $b)
            {
                if (is_string($a))
                    $salida .= "<div class='$a'>" . "\n";
                if (is_array($b))
                {
                    ksort($b);
                    $grupo = '';
                    foreach ($b as $tipo => $cont)
                    {
                        if (is_string($tipo) && substr_count($tipo, '.') === 1)
                        {
                            $w = explode('.', $tipo);
                            if ($w[0] !== $grupo)
                                $salida .= "<div class='col-md-$w[1]'>" . "\n";
                            $salida .= self::prep_container($cont);
                            $grupo = $w[0];
                            $salida .= "</div>" . "\n";
                        }
                        elseif (is_integer($tipo))
                        {
                            $salida .= self::prep_container($cont);
                        }
                        else
                        {
                            $salida .= self::prep_container($b);
                        }
                    }
                }
                else
                    $salida .= self::prep_container($b);
                if (is_string($a))
                    $salida .= "</div>" . "\n";
            }
        }
        return $salida;
    }
}
// Fin del documento.