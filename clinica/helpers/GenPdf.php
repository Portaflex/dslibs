<?php

namespace dslibs\clinica\helpers;

use Mpdf\Mpdf;
use Yii;

class GenPdf extends Mpdf
{
     private function doctores ()
	{
	    $doctores = Yii::$app->params['doctores'];
	    $out = '<b>Clínica de C.O.T.</b><br>';
	    foreach ($doctores as $d)
	    {
	        $out .= $d.'<br>';
	    }
	    $out .= '<i>Especialistas en C.O.T.</i>';
	    return $out;
	}
    
    public function genIq ($iq, $episodio)
	{
		$paciente = $episodio->paciente;
		$edad = date_diff(date_create($paciente->pac_fnac), date_create('today'))->y;
		$financiador = $episodio->financiador;
		$doctores = $this->doctores();
		$this->SetHTMLHeader("<table style='width: 100%;'><tr>
			<td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 30pt;'>
			<img src='images/logo_casa.png' /><br>Protocolo Quirúrgico</td>
			<td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 25pt;'>
			$doctores</td>
			<td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 30pt;'>
			<b>$paciente->pac_nom $paciente->pac_apell1 $paciente->pac_apell2</b><br>Edad: $edad años<br>
			$financiador->finan_empresa</td>
			</tr></table>");

		$this->SetFooter("<table style='width: 100%;'><tr>
			<td style='width: 100%; text-align: left; padding: 20px; font-size: 25pt;'>
			<b>Médico responsable:</b><br>".Yii::$app->session['usuario']."<br><br><br>
			Colegiado Nº: ".Yii::$app->session['userNcol']."</td>
			<td style='width: 100%; text-align: center; padding: 20px; font-size: 25pt;'></td>
			<td style='width: 100%; text-align: left; padding: 20px; font-size: 25pt;'></td>
			</tr></table>");

		$this->setAutoTopMargin = 'stretch';
		$this->autoMarginPadding = '25';
		$this->setAutoBottomMargin = 'stretch';
		return $this;
	}

	public function genConsentimiento ()
	{
	    $doctores = $this->doctores();
	    $this->SetHTMLHeader("<table style='width: 100%;'><tr>
	            <td style='width: 100%; text-align: center; border: 0px solid #000000; padding: 20px; font-size: 18pt;'>
	            <img src='images/logo_casa.png' /><br><br><h1>Clínica de C.O.T.</h1>
	            <i style='font-size:22pt;'>Especialistas en Cirugía Ortopédica y Traumatología</i><br>CIF: B-97969620
	            </td>
	            <td style='width: 100%; text-align: center; border: 0px solid #000000; padding: 20px; font-size: 35pt;'>
	            </td>
	            <td style='width: 100%; text-align: center; border: 0px solid #000000; padding: 20px; font-size: 30pt;'>
	            $doctores</td>
	            </tr></table>");
	    
	    $this->SetFooter("<table style='width: 100%;'><tr>
	            <td style='text-align: left; font-size: 8pt;'>avda. Manuel Candela 41 - 46021 Valencia</td>
	            <td style='text-align: center; font-size: 8pt;'></td>
	            <td style='text-align: right; font-size: 8pt;'>E-mail: admin@clinicadecot.es</td>
	            </tr></table>");
	    
	    $this->setAutoTopMargin = 'stretch';
	    $this->autoMarginPadding = '35';
	    $this->setAutoBottomMargin = 'stretch';
	    return $this;
	}

	public function genEpicrisis ($episodio, $epicrisis)
	{
	    $doctores = $this->doctores();
		$paciente = $episodio->paciente;
		$edad = date_diff(date_create($paciente->pac_fnac), date_create('today'))->y;
		$financiador = $episodio->financiador;
		$motivoalta = isset($epicrisis->motivoalta['m_texto']) ? $epicrisis->motivoalta['m_texto'] : '';
		$this->SetHTMLHeader("<table style='width: 100%;'><tr>
			<td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 30pt;'>
			<img src='images/logo_casa.png' /><br>Informe de Alta Hospitalaria</td>
			<td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 25pt;'>
			$doctores</td>
			<td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 30pt;'>
			<b>$paciente->pac_nom $paciente->pac_apell1 $paciente->pac_apell2</b><br>Edad: $edad años<br>
			$financiador->finan_empresa</td>
			</tr></table>");

		$this->SetFooter("<table style='width: 100%;'><tr>
			<td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 25pt;'>
			<b>Motivo del alta:</b><br>$motivoalta<br></td>
			<td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 25pt;'>
			<b>Fecha de ingreso:</b><br>".Yii::$app->formatter->asDate($epicrisis->epic_fechaingreso)."<br><b>Fecha de alta:</b>
		    <br>".Yii::$app->formatter->asDate($epicrisis->epic_fechaalta)."</td>
			<td style='width: 100%; text-align: left; border: 2px solid #000000; padding: 20px; font-size: 25pt;'>
			<b>Médico responsable del alta:</b><br>".Yii::$app->session['usuario']."<br><br><br>
			Colegiado Nº: ".Yii::$app->session['userNcol']."</td>
			</tr></table>");

		$this->setAutoTopMargin = 'stretch';
		$this->autoMarginPadding = '30';
		$this->setAutoBottomMargin = 'stretch';
		return $this;
	}
	
	public function genParteIq ()
	{
	    $doctores = $this->doctores();
	    $this->SetHTMLHeader("<table style='width: 100%;'><tr>
	            <td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 30pt;'>
	            <img src='images/logo_casa.png' /><br>Parte de Quirófano</td>
	            <td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 35pt;'>
	            <b>Clínica de C.O.T.</b></td>
	            <td style='width: 100%; text-align: center; border: 2px solid #000000; padding: 20px; font-size: 30pt;'>
	            $doctores</td>
	            </tr></table>");
	            
	   $this->SetFooter("<table style='width: 100%;'><tr><td></td></tr></table>");
	            
	            $this->setAutoTopMargin = 'stretch';
	            $this->autoMarginPadding = '30';
	            $this->setAutoBottomMargin = 'stretch';
	            return $this;
	}

	public function genFactura ()
	{
	    $this->SetHTMLHeader("<table style='width: 100%;'><tr>
	            <td style='width: 100%; text-align: center; border: 0px solid #000000; padding: 20px; font-size: 18pt;'>
	            <img src='images/logo_clinica.png' height='60' /><br><br><h1>Clínica Ortopédica y Traumatológica Dr. Sala SLP</h1>
	            <br><h3>CIF: B-97969620</h3>
	            </td>
	            <td style='width: 100%; text-align: center; border: 0px solid #000000; padding: 20px; font-size: 35pt;'>
	            </td>
	            <td style='width: 100%; text-align: center; border: 0px solid #000000; padding: 20px; font-size: 30pt;'>
	            <h1>Factura</h1>
	            </td>
	            </tr></table>");
	    
	    $this->SetFooter("<table style='width: 100%;'><tr>
	            <td style='text-align: left; font-size: 8pt;'>".Yii::$app->params['direccion']."</td>
	            <td style='text-align: center; font-size: 8pt;'></td>
	            <td style='text-align: right; font-size: 8pt;'>E-mail: ".Yii::$app->params['adminEmail']."</td>
	            </tr></table>");
	    
	    $this->setAutoTopMargin = 'stretch';
	    $this->autoMarginPadding = '30';
	    $this->setAutoBottomMargin = 'stretch';
	    return $this;
	}
}
