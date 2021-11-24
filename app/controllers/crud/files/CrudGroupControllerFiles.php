<?php
namespace controllers\crud\files;

use Ubiquity\controllers\crud\CRUDFiles;
 /**
  * Class CrudGroupControllerFiles
  */
class CrudGroupControllerFiles extends CRUDFiles{
	public function getViewIndex(){
		return "CrudGroupController/index.html";
	}

	public function getViewForm(){
		return "CrudGroupController/form.html";
	}

	public function getViewDisplay(){
		return "CrudGroupController/display.html";
	}


}
