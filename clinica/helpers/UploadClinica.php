<?php

namespace dslibs\clinica\helpers;

use yii\base\Model;
use yii\web\UploadedFile;
use Yii;
use dslibs\clinica\historia\models\DocsPacienteModel;
use dslibs\clinica\historia\models\ImagenModel;

class UploadClinica extends Model
{
	/**
	 * @var UploadedFile
	 */

	public $doc_titulo;
	public $doc_nombre;
	private $doc_subido;

	public $image_nombre;
	public $image_imagen;
	private $imagen_subida;

	public function rules()
	{
		return [
				//[['doc_nombre'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, doc, pdf, txt'],
				//[['image_imagen'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif, pdf, bmp']
		];
	}

	public function attributeLabels()
	{
		return [
				'doc_nombre' => 'Archivo cargado',
				'doc_titulo' => 'Título del documento',
				'image_imagen' => 'Imagen cargada',
				'image_nombre' => 'Título de la imagen'
		];
	}

	public function uploadImage()
	{
	    if ($this->validate())
	    {
	        $info = new \SplFileInfo($this->image_imagen['name']);
	        $this->imagen_subida = basename($this->image_imagen['name'], '.'.$info->getExtension()) . '-' .
	   	        Yii::$app->session['p'] . '-' .
	   	        date('H-i-s') . '.' .
	   	        $info->getExtension();
	   	        
	   	        $this->registraImagen();
	   	        
	   	        return move_uploaded_file($this->image_imagen['tmp_name'],
	   	            Yii::$app->basePath . '/imagenes/' . $this->imagen_subida);
	    }
	    else return false;
	}

	public function uploadDocument()
	{
		if ($this->validate())
		{
		    $info = new \SplFileInfo($this->doc_nombre['name']);
		    $this->doc_subido = basename($this->doc_nombre['name'], '.'.$info->getExtension()) . '-' . 
		          Yii::$app->session['p'] . '-' . 
		          date('H-i-s') . '.' . 
		          $info->getExtension();
			
		    $this->registraDocumento();
			
		    return move_uploaded_file($this->doc_nombre['tmp_name'],
		        Yii::$app->basePath . '/documentos/' . $this->doc_subido);
		}
		else return false;
	}

	public function registraDocumento ()
	{
		$db = new DocsPacienteModel();
		$db->doc_titulo = $this->doc_titulo;
		$db->doc_nombre = $this->doc_subido;
		$db->doc_pac_id = Yii::$app->session['p'];
		$db->doc_epis_id = Yii::$app->session['e'];
		$db->doc_userlogin = Yii::$app->session['userLogin'];
		$db->save();
	}

	public function registraImagen ()
	{
		$db = new ImagenModel();
		$db->image_nombre = $this->image_nombre;
		$db->image_imagen = $this->imagen_subida;
		$db->image_pac_id = Yii::$app->session['p'];
		$db->image_epis_id = Yii::$app->session['e'];
		$db->image_userlogin = Yii::$app->session['userLogin'];
		$db->save();
	}
}