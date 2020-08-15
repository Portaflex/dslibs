<?php

/**
 * Creado por Diego Sala en base al código original de Xu Ding.
 * @author Xu Ding
 * @email thedilab@gmail.com
 * @website http://www.StarTutorial.com
 *
 */

namespace dslibs\calendar;

use yii\helpers\Url;
use yii\bootstrap4\Html;
use yii\db\Query;
use Yii;

date_default_timezone_set('Europe/Madrid');

class Calendar
{
    private $dayLabels = ["Lu", "Ma", "Mi", "Ju", "Vi", "Sa", "Do"];
    private $currentYear = 0;
    private $currentMonth = 0;
    private $currentDay = 0;
    private $currentDate = null;
    private $daysInMonth = 0;
    private $naviHref = null;
    private $url = '';
    private $controller = '';
    
    private $tabla;
    private $fecha;
    private $link;
    private $where;
    private $pre = 'c';

    public function __construct ($config = null)
    {
        $this->naviHref = Url::to(); //htmlentities($_SERVER['PHP_SELF']);
        setlocale(LC_ALL, "es_ES");

        if (null != $config)
        {
            foreach ($config as $key => $value)
            {
                $this->$key = $value;
            }
        }
    }

    /**
     * print out the calendar
     */
    public function show ()
    {
        if (isset($_GET[$this->pre.'year'])) Yii::$app->session[$this->pre.'year'] = $_GET[$this->pre.'year'];
        if (! isset($_GET[$this->pre.'year']) && ! isset(Yii::$app->session[$this->pre.'year'])) Yii::$app->session[$this->pre.'year'] = date("Y", time());
        if (isset($_GET[$this->pre.'month'])) Yii::$app->session[$this->pre.'month'] = $_GET[$this->pre.'month'];
        if (! isset($_GET[$this->pre.'month']) && ! isset(Yii::$app->session[$this->pre.'month'])) Yii::$app->session[$this->pre.'month'] = date("m", time());
        if (isset($_GET[$this->pre.'fecha'])) Yii::$app->session[$this->pre.'fecha'] = $_GET[$this->pre.'fecha'];
        if (! isset($_GET[$this->pre.'fecha']) && ! isset(Yii::$app->session[$this->pre.'fecha'])) Yii::$app->session[$this->pre.'fecha'] = date("Y-m-d", time());

        $this->currentYear = Yii::$app->session[$this->pre.'year'];
        $this->currentMonth = Yii::$app->session[$this->pre.'month'];

        $this->daysInMonth = $this->_daysInMonth();
        $weeksInMonth = $this->_weeksInMonth();
        $fechas = $this->_selectFechas();

        $semana = '';

        // Create weeks in a month
        for ($i = 0; $i < $weeksInMonth; $i ++)
        {
        	$dias = "";

        	// Create days in a week
        	for ($j = 1; $j <= 7; $j ++)
        	{
        		$cellContent = $this->_showDay($i * 7 + $j);

        		if (in_array($cellContent, array_column($fechas, $this->pre.'fecha')))
        		{
        			$dias .= Html::tag('td', "<b>".Html::a($cellContent, $this->controller.'?'.$this->pre.'fecha='.$this->currentYear.
        					'-'.$this->currentMonth.'-'.$cellContent)."</b>");
        		}
        		else
        		{
        			$dias .= Html::tag('td', $cellContent);
        		}
        	}

        	$semana .= Html::tag('tr', $dias)."\n";
        }

        $out = "<br> \n". Html::tag('table',
        	   "\n". Html::tag('thead', $this->_createNavi().
               "\n". $this->_createLabels()).
               "\n". Html::tag('tbody', $semana),
        	   ['class' => 'table-calendario', 'width' => '100%']). "<br> \n";

        //$s = $this->_selectFechas();

        return $out;
    }

    /* ******************** PRIVATE ********************
     * create the li element for ul
    */
    
    private function _showDay ($cellNumber)
    {
        if ($this->currentDay == 0)
        {
            $firstDayOfTheWeek = date('N', strtotime( $this->currentYear.'-'.$this->currentMonth.'-01'));

            if (intval($cellNumber) == intval($firstDayOfTheWeek)) $this->currentDay = 1;
        }

        if (($this->currentDay != 0) && ($this->currentDay <= $this->daysInMonth))
        {
            $this->currentDate = date('Y-m-d', strtotime( $this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));
            $cellContent = $this->currentDay;
            $this->currentDay ++;
        }
        else
        {
            $this->currentDate = null;
            $cellContent = null;
        }
        return $cellContent;
    }

    /**
     * create navigation
     */
    private function _createNavi ()
    {
        $nextMonth = $this->currentMonth == 12 ? '01' : $this->currentMonth + 1;
        $nextYear = $this->currentMonth == 12 ? intval($this->currentYear) + 1 : $this->currentYear;
        $preMonth = $this->currentMonth == '01' ? 12 : $this->currentMonth - 1;
        $preYear = $this->currentMonth == '01' ? intval($this->currentYear) - 1 : $this->currentYear;

        if (strlen($preMonth) == 1) $preMonth = "0$preMonth";
        if (strlen($nextMonth) == 1) $nextMonth = "0$nextMonth";

        $monthPre = $this->controller.'?'.$this->pre.'month='.$preMonth.'&'.$this->pre.'year='.$preYear;
        $monthNex = $this->controller.'?'.$this->pre.'month='.$nextMonth.'&'.$this->pre.'year='.$nextYear;

        $out = Html::tag('tr',
        		Html::tag('th', Html::a('<<', $monthPre, ['class' => 'prev'] )).
        		Html::tag('th', date('m \d\e Y', strtotime($this->currentYear.'-'.$this->currentMonth)),
        				['colspan' => 5, 'style' => 'text-align: center;']).
        		Html::tag('th', Html::a('>>', $monthNex, ['class' => 'next'] ))
        	)."\n";

        return $out;
    }

    /**
     * create calendar week labels
     */
    private function _createLabels ()
    {
        $content = '';

        foreach ($this->dayLabels as $label)
        {
            $content .= Html::tag('td', $label);
        }

        return Html::tag('tr', $content)."\n";
    }

    /**
     * calculate number of weeks in a particular month
     */
    private function _weeksInMonth ()
    {
        // find number of days in this month
        $daysInMonths = $this->_daysInMonth();

        $numOfweeks = ($daysInMonths % 7 == 0 ? 0 : 1) +
                 intval($daysInMonths / 7);

        $monthEndingDay = date('N', strtotime($this->currentYear.'-'.$this->currentMonth.'-'.$daysInMonths));
        $monthStartDay = date('N', strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));

        if ($monthEndingDay < $monthStartDay) $numOfweeks ++;

        return $numOfweeks;
    }

    /**
     * calculate number of days in a particular month
     */
    private function _daysInMonth ()
    {
        return date('t', strtotime($this->currentYear.'-'.$this->currentMonth));
    }

    /**
     *  Selecciona las fechas de la base de datos.
     */
    private function _selectFechas()
    {
    	$mes = $this->currentYear.'-'.$this->currentMonth;

    	$q = (new Query())->select(["extract(day from $this->fecha) as ".$this->pre."fecha", "$this->link as link"])
    	->distinct($this->fecha)->from($this->tabla)
    	->andFilterWhere(["to_char($this->fecha, 'YYYY-mm')" => $mes]);
    	if ($this->where) $q->andFilterWhere($this->where);

    	return $q->all();

    }
}