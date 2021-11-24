<?php
namespace controllers;
use controllers\crud\datas\CrudGroupControllerDatas;
use Ubiquity\controllers\crud\CRUDDatas;
use controllers\crud\viewers\CrudGroupControllerViewer;
use Ubiquity\controllers\crud\viewers\ModelViewer;
use controllers\crud\events\CrudGroupControllerEvents;
use Ubiquity\controllers\crud\CRUDEvents;
use controllers\crud\files\CrudGroupControllerFiles;
use Ubiquity\controllers\crud\CRUDFiles;
use Ubiquity\attributes\items\router\Route;

/**
 * Controller TodosController
 * @property JsUtils $jquery
 */

#[Route(path: "/group/",inherited: true,automated: true)]
class CrudGroupController extends \Ubiquity\controllers\crud\CRUDController{

	public function __construct(){
		parent::__construct();
		\Ubiquity\orm\DAO::start();
		$this->model='models\\Group';
		$this->style='';
	}

	public function _getBaseRoute() {
		return '/group/';
	}
	
	protected function getAdminData(): CRUDDatas{
		return new CrudGroupControllerDatas($this);
	}

	protected function getModelViewer(): ModelViewer{
		return new CrudGroupControllerViewer($this,$this->style);
	}

	protected function getEvents(): CRUDEvents{
		return new CrudGroupControllerEvents($this);
	}

	protected function getFiles(): CRUDFiles{
		return new CrudGroupControllerFiles();
	}


}
