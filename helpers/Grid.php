<?php

namespace dslibs\helpers;

use yii\grid\GridView;

class Grid extends GridView
{
	public $groupColumn;
	
	/**
	 * Esta función está modificada por Diego Sala para mostrar tablas agrupadas.
	 * @return string the rendering result.
	 */
	public function renderTableBody()
	{
		$models = array_values($this->dataProvider->getModels());
		$keys = $this->dataProvider->getKeys();
		$rows = [];
		$groups = [];
		$claves = [];

		if ($this->groupColumn)
		{
			foreach ($models as $index => $model)
			{
				$key = $keys[$index];

				if ($this->beforeRow !== null) {
					$row = call_user_func($this->beforeRow, $model, $key, $index, $this);
					if (!empty($row)) {
						$rows[] = $row;
					}
				}

				if (! in_array($model[$this->groupColumn], $groups))
				{
					$rows[] = $this->renderTableRow($model, $key, $index);
					$g = $model[$this->groupColumn];
					$groups[] = $g;
				}

				foreach ($models as $index => $model)
				{
					$key = $keys[$index];
					if ($this->afterRow !== null && $model[$this->groupColumn] == $g
							&& ! in_array($index, $claves))
					{
						$row = call_user_func($this->afterRow, $model, $key, $index, $this);
						if (!empty($row))
						{
							$rows[] = $row;
						}
						$claves[] = $index;
					}
				}
			}
		}
		else
		{
			foreach ($models as $index => $model)
			{
				$key = $keys[$index];
				if ($this->beforeRow !== null) {
					$row = call_user_func($this->beforeRow, $model, $key, $index, $this);
					if (!empty($row)) {
						$rows[] = $row;
					}
				}

				$rows[] = $this->renderTableRow($model, $key, $index);

				if ($this->afterRow !== null) {
					$row = call_user_func($this->afterRow, $model, $key, $index, $this);
					if (!empty($row)) {
						$rows[] = $row;
					}
				}
			}
		}

		if (empty($rows)) {
			$colspan = count($this->columns);

			return "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
		} else {
			return "<tbody>\n" . implode("\n", $rows) . "\n</tbody>";
		}
	}
}
// Fin de documento