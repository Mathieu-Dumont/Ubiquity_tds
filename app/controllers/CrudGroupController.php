<?php
namespace controllers;
use Ajax\php\ubiquity\JsUtils;
use controllers\crud\datas\CrudGroupControllerDatas;
use models\Group;
use models\Organization;
use Ubiquity\attributes\items\router\Get;
use Ubiquity\attributes\items\router\Post;
use Ubiquity\controllers\crud\CRUDDatas;
use controllers\crud\viewers\CrudGroupControllerViewer;
use Ubiquity\controllers\crud\viewers\ModelViewer;
use controllers\crud\events\CrudGroupControllerEvents;
use Ubiquity\controllers\crud\CRUDEvents;
use controllers\crud\files\CrudGroupControllerFiles;
use Ubiquity\controllers\crud\CRUDFiles;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\orm\DAO;
use Ubiquity\utils\http\URequest;
use Ubiquity\utils\http\USession;
use services\ui\UIGroups;

#[Route(path: "/group/",inherited: true,automated: true)]
class CrudGroupController extends \Ubiquity\controllers\crud\CRUDController{

    public function initialize() {
        $this->ui=new UIGroups($this);
        parent::initialize();
    }

	public function __construct(){
		parent::__construct();
		\Ubiquity\orm\DAO::start();
		$this->model='models\\Group';
		$this->style='';
	}

    #[Get('new/group', name: 'new.group')]
    public function newGroup(){
        $name='frm-group';
        $this->ui->newGroup($name);
        $this->jquery->renderView('main/vForm.html',['formName'=>$name]);
    }

    #[Post('new/group', name: 'new.groupPost')]
    public function newGroupPost(){
        $orga=DAO::getById(Organization::class,'1',false);
        $group=new Group();
        URequest::setValuesToObject($group);
        $group->setOrganization($orga);
        if(DAO::insert($group)){
            $count=DAO::count(Group::class,'idOrganization= ?',[1]);
            $this->jquery->execAtLast('$("#group-count").html("'.$count.'")');
            $this->showMessage("Ajout du groupe","Le groupe $group a été ajouté à l'organisation.",'success','check square outline');
        }else{
            $this->showMessage("Ajout du groupe","Aucun groupe n'a été ajouté",'error','warning circle');
        }
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
