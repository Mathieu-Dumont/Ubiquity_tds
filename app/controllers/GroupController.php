<?php
namespace controllers;
 use Ajax\php\ubiquity\JsUtils;
 use models\Group;
 use models\Organization;
 use models\User;
 use services\ui\UIGroups;
 use Ubiquity\attributes\items\router\Get;
 use Ubiquity\attributes\items\router\Post;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\controllers\Router;
 use Ubiquity\orm\DAO;
 use Ubiquity\orm\repositories\ViewRepository;
 use Ubiquity\utils\http\URequest;
 use Ubiquity\utils\models\UArrayModels;

 /**
  * Controller GroupController
  * @property JsUtils $jquery
  *
  */
class GroupController extends \controllers\ControllerBase{



    #[Route('/group')]
	public function index(){
        $this->ui->addUser();
        $this->jquery->getOnClick('dd-groupes','','','')
		$this->jquery->renderView("GroupController/index.html");
	}

    #[Post(path: "Group/resultPost",name: "group.addPost")]
    public function postGroupe(){
        $grp=new Group();
        if ($grp){
            URequest::setValuesToObject($grp);
            $users=DAO::getById(User::class,explode(',',URequest::post()));
            $grp->setUsers($users);
            $this->repo->insert($grp,true);
        }
        $this->index();
    }

    private function showMessage($title,$text,$type,$icon){
        $this->loadView('GroupController/showMessage.html',compact('icon','title','type','text'));
    }

    public function initialize() {
        $this->ui=new UIGroups($this);
        parent::initialize();
        $this->repo??=new ViewRepository($this, Group::class);
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


}
