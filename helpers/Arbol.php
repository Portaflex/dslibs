<?php

namespace dslibs\helpers;

use yii\db\Query;
use yii\helpers\Url;

class Arbol
{
	public function genArbol($raiz, $config = [])
	{
		$config['order'] = $config['order'] ?? $config['nombre'];
		// Se recuperan los datos de la tabla...
		$query = new Query();

		if (isset($config['donde'])) $query->where($config['donde']);

		if ($raiz != 0)
		{
			$query->andWhere(['=', $config['id'], $raiz]);
			$query->orWhere(['=', $config['parent'], $raiz]);
		}
		if (isset($config['group_by'])) $query->addGroupBy($config['group_by']);
		$query->addOrderBy($config['order']);
		$items = $query->from($config['tabla'])->all();

		// Se pinta la representación de arbol...

		$t = isset($config['titulo']) ? $config['titulo'] : FALSE;
		$n = isset($config['nuevo']) ? $config['nuevo'] : '';
		if (! empty ($config['titulo'])) $this->out = '<h2><a href='.Url::to($config['ref']).'>'.$t."</a></h2>
				<b><a href='".Url::to($config['ref_insert'].'?parent='.$raiz)."'>".$n.'</a></b>';
		$this->out .= "<div style='column-count:auto; column-width:20em;'>";
		$this->createTreeView($items, $raiz, $config);
		$this->out .= "</div>";
		return $this->out;
	}

	public function createTreeView($items, $raiz, $config = [], $CL = 0, $PL = -1)
	{
		foreach ($items as $item)
		{
			if ($raiz == $item[$config['parent']])
			{
				$link = $item[$config['id_link']] ?? $item[$config['id']];
			    $id_link = "<li><a href='".Url::to($config['ref_edit']."?id=".$item[$config['id']]).
				"'><b>".$link.'</b> - </a>';

				if ($CL > $PL) $this->out .= "<ul>"."\n";
				if ($CL == $PL) $this->out .= "</li>"."\n";

				$this->out .= $id_link."<a href='".Url::to($config['ref_insert'])."?parent=".
						$item[$config['id']]."'>".$item[$config['nombre']]."</a>";

						if ($CL > $PL) $PL = $CL;

						$CL++;
						$this->createTreeView ($items, $item[$config['id']], $config, $CL, $PL);
						$CL--;
			}
		}
		if ($CL == $PL)
		{
			$this->out .= "</li></ul>"."\n";
		}
	}
}
// fin de documento