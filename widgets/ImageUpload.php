<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package Core
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace Contao;


/**
 * Class Upload
 *
 * Provide methods to use the FileUpload class in a back end widget. The widget
 * will only upload the files to the server. Use a submit_callback to process
 * the files or use the class as base for your own upload widget.
 * 
 * @copyright  Leo Feyer 2005-2013
 * @author     Leo Feyer <https://contao.org>
 * @package    Core
 */
class ImageUpload extends \Widget implements \uploadable
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Add a for attribute
	 * @var boolean
	 */
	protected $blnForAttribute = false;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_image_widget';

	/**
	 * Uploader
	 * @var \FileUpload
	 */
	protected $objUploader;


	/**
	 * Initialize the FileUpload object
	 * @param array
	 */
	public function __construct($arrAttributes=null)
	{
		parent::__construct($arrAttributes);
		$this->import('Input');
		$this->objUploader = new \FileUpload();
		$this->objUploader->setName($this->strName);
		$this->origName = $this->splitFileExt($_FILES[$this->strName]['name'][0]);
	}
	
	private function splitFileExt($fileName){
		if(empty($fileName)){
			return array();
		}
		$fragments = explode(".", $fileName);
		if(count($fragments) == 1){
			return array('name' => $fileName, 'ext' => '');
		}
		$ext = $fragments[count($fragments) - 1];
		unset($fragments[count($fragments) - 1]);
		return array('name' => implode(".", $fragments), 'ext' => $ext);
	}


	/**
	 * Trim values
	 * @param mixed
	 * @return mixed
	 */
	protected function validator($varInput)
	{
		$strUploadTo = 'system/tmp';
		if(!isset($this->origName['name'])){
			if($this->mandatory and $this->Input->post('prev_'.$this->strName) == ''){
				if ($this->strLabel == ''){
					$this->addError($GLOBALS['TL_LANG']['ERR']['mdtryNoLabel']);
				}
				else{
					$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
				}
				return '';
			}
			
			if(isset($_POST['deleteImage'])){
				unlink(TL_ROOT."/".$this->varValue);
				return '';
			}
			$this->blnSubmitInput = false;
			return '';
		}
		// Specify the target folder in the DCA (eval)
		if (isset($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['uploadFolder']))
		{
			$strUploadTo = $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['eval']['uploadFolder'];
		}
		
		while(file_exists($this->getPath($strUploadTo))){
			$_FILES[$this->strName]['name'][0] = $this->origName['name']."_".substr(md5($this->origName.session_id().time().rand(0,1000)), 0, 6).".".$this->origName['ext'];
		}
		$prevImage = $this->varValue;
		$upload = $this->objUploader->uploadTo($strUploadTo);
		if(isset($upload[0])){
			unlink(TL_ROOT."/".$prevImage);
			return $upload[0];
		}
		return '';
		parent::validate();
	}
	
	protected function getPath($strUploadTo){
		return TL_ROOT."/".$strUploadTo."/".$_FILES[$this->strName]['name'][0];
	}

	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$this->deleteImageString = 'Bild löschen?';
		return $this->parse(array('deleteImageString' => 'Bild löschen?'));
	}
}
