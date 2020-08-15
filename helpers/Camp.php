<?php

namespace dslibs\helpers;

use yii\bootstrap4\Html;
use yii\jui\DatePicker;
use Yii;

class Camp
{
	//-------------------------------------------------------------------------
	// Funciones de campos
	//-------------------------------------------------------------------------

	public static function datePicker ($name, $value = '', $etiqueta = '', $opciones = [])
	{
		$op = [];
	    $op['name'] = $name;
		$op['options'] = ['class' => 'form-control input-sm'] + $opciones;
		if ($value !== '')
		{
		    if (is_array($value)) $op['value'] = self::sorter($value);
		    else $op['value'] = $value;
		}
		//$op['dateFormat'] = 'dd-MM-yyyy';
		$op['language'] = 'es';

		$out = $etiqueta !== '' ? Html::label($etiqueta) : '';
		$out .= DatePicker::widget($op)."\n";
		return $out;
	}

	public static function dropDownList ($nombre, $valor = '', $lista, $etiqueta = '', $op = [])
	{
		if (! isset($op['class'])) $op['class'] = 'form-control input-sm';
		if (isset($op['clase'])) $op['class'] = 'form-control input-sm ' . $op['clase'];
		if (is_array($valor)) $valor = self::sorter($valor);
		$out = $etiqueta !== '' ? Html::label($etiqueta) : ''."\n";
		$out .= Html::dropDownList($nombre, $valor, $lista, $op)."\n";
		return $out;
	}

	public static function textInput ($nombre, $valor = '', $etiqueta = '' , $op = [])
	{
		$op['class'] = 'form-control input-sm';
		$op['placeholder'] = $etiqueta;
		if (is_array($valor)) $valor = self::sorter($valor);
		$out = $etiqueta !== '' ? Html::label($etiqueta) : ''."\n";
		$out .= Html::textInput($nombre, $valor, $op)."\n";
		return $out;
	}

	public static function pwInput ($nombre, $valor = '', $etiqueta = '' , $op = [])
	{
		$op['class'] = 'form-control input-sm';
		$op['placeholder'] = $etiqueta;
		$out = $etiqueta !== '' ? Html::label($etiqueta) : ''."\n";
		$out .= Html::passwordInput($nombre, $valor, $op)."\n";
		return $out;
	}

	public static function textArea ($nombre, $valor = '', $etiqueta = '' , $op = [])
	{
		$op['class'] = 'form-control input-sm';
		$op['placeholder'] = $etiqueta;
		$out = $etiqueta !== '' ? Html::label($etiqueta) : ''."\n";
		$out .= Html::textarea($nombre, $valor, $op)."\n";
		return $out;
	}

	/* public static function ckeditor ($name, $value = '', $etiqueta = '', $opciones = [])
	{
		$op['name'] = $name;
		$op['clientOptions'] = ['class' => 'form-control input-sm'] + $opciones;
		if ($value !== '') $op['value'] = $value;

		$out = $etiqueta !== '' ? Html::label($etiqueta)."\n": '';
		$out .= CKEditor::widget($op)."\n";
		return $out;
	} */
	
	public static function ckeditor ($name, $value = '', $etiqueta = '', $op = [])
	{
	    $op['id'] = 'editor_'.rand(0, 1000);
	    $op['class'] = 'form-control input-sm';
	    $preset = isset($op['preset']) ? $op['preset'] : 'Consulta';
	    $height = isset($op['height']) ? $op['height'] : 200;
	    $width = isset($op['width']) ? $op['width'] : '100%';
	    unset($op['preset'], $op['height'], $op['width']);
	    
	    $out = $etiqueta !== '' ? Html::label($etiqueta)."\n": ''."\n";
	    $out .= Html::textarea($name, $value, $op)."\n";
	    $out .= "<script>CKEDITOR.replace(".$op['id'].", {toolbar: '$preset', height: $height, width: '$width'})</script>"."\n";
	    $out .= '<script>CKEDITOR.dtd.$removeEmpty["span"] = false;</script>';
	    return $out;
	}

	public static function fileInput ($nombre, $valor = '', $etiqueta = '' , $op = [])
	{
		$out = "<div class='input-group input-group-sm'><p>"."\n";
		$out .= $etiqueta !== '' ? Html::label($etiqueta)."\n": '';
		$out .= Html::fileInput($nombre, $valor, $op)."\n";
		$out .= "</p></div>";
		return $out;
	}

	//-------------------------------------------------------------------------
	// Funciones de Botones.
	//-------------------------------------------------------------------------

	public static function botonesNormal ($url, $id = false)
	{
		$out = "<br>". self::botonSave().' ';
		if ($id) $out .= self::botonDelete().' ';
		$out .= self::botonReturn($url).' ';
		return Html::tag('h3', $out)."<br> \n";
	}

	public static function botonesAjax ($url, $function)
	{
		$out = self::botonAjax('Ok', $function, $url, ['class' => 'btn btn-outline-primary btn-xs']).' '."\n";
		$out .= self::botonAjax('Del', $function, $url, ['class' => 'btn btn-outline-danger btn-xs', 'action' => 'delete' ])."<br> \n";
		return $out;
	}

	public static function boton ($name, $action = 'save', $class = 'default', $options = [])
	{
	    $op = array_merge(['class' => 'btn btn-sm btn-'.$class,
	            'name' => 'action', 'value' => $action], $options);
	    return Html::submitButton($name, $op)."\n";
	}

	public static function botonSend ()
	{
		return "<br>". Html::tag('h2', Html::submitButton('Enviar', ['class' => 'btn btn-sm btn-outline-primary', 'name' => 'action', 'value' => 'send']))."\n";
	}

	public static function botonSave ()
	{
		return Html::submitButton('Guardar', ['class' => 'btn btn-sm btn-outline-primary', 'name' => 'action', 'value' => 'save'])."\n";
	}

	public static function botonDelete ()
	{
		return Html::submitButton('Eliminar', ['class' => 'btn btn-sm btn-outline-danger', 'name' => 'action',  'value' => 'delete',
				'onclick' => "return confirm('Desea eliminar permanentemente este registro?')"])."\n";
	}

	public static function botonReturn ($url, $nombre = 'Volver')
	{
		return Html::a($nombre, $url , ['class' => 'btn btn-sm btn-outline-secondary']);
	}

	public static function botonAjax ($name, $function, $url, $options = false)
	{
		$id = 'boton_'.rand(0, 1000);
		$op = ['class' => 'light', 'action' => 'save'];

		if ($options)
		{
	   		if (is_array($options))
	   		{
	   			foreach ($options as $k => $v)
	   			{
	   				$op[$k] = $v;
	   			}
	   		}
	   		else
	   		{
	   			$op['class'] = $options;
	   		}
		}
   		return 	Html::button($name, ['class' => "btn btn-sm btn-".$op['class'], 'onClick' => "$function('$id')",
   				'url' => "$url", 'id' => $id, 'action' => $op['action']])."\n";
   }
   
   public static function sorter ($valor)
   {
       return !empty(Yii::$app->session[$valor[0]][$valor[1]]) ? Yii::$app->session[$valor[0]][$valor[1]] : '';
   }
}

// Fin del archivo.