<?php
namespace controllers;
use models\Organization;
use models\User;
use Ubiquity\attributes\items\router\Post;
use Ajax\JsUtils;
use Ubiquity\attributes\items\router\Get;
use models\Groupe;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\Router;
use Ubiquity\orm\DAO;
use Ubiquity\orm\repositories\ViewRepository;
use Ubiquity\utils\http\URequest;
use Ubiquity\utils\models\UArrayModels;

/**
 * Controller GroupController
 * @property JsUtils $jquery
 */
class GroupController extends \controllers\ControllerBase{

    private ViewRepository $repo;

    public function initialize() {
        parent::initialize();
        $this->repo??=new ViewRepository($this,Groupe::class);
    }

    #[Route('/Group')]
    public function index(){
        $this->repo->all();
        $this->loadView("GroupController/index.html");
    }

    #[Get(path: "Group/addGroupe",name: "group.addGroupe")]
    public function addGroupe(){
        $grp=new Groupe();
        $ids = \array_map(function ($user){
            return $user->getId ();
        }, $grp->getUsers());
        $grp->userIds=implode(',',$ids);
        $frm=$this->jquery->semantic()->dataForm('frm-grp',$grp);
        $frm->setActionTarget(Router::path('group.postGroupe'), '');
        $frm->setProperty('method','post');
        $frm->setFields(['name','email', 'aliases', 'organization','usersIds', 'submit']);
        $frm->fieldAsDropDown('organization', UArrayModels::asKeyValues(DAO::getAll(Organization::class),'getId'));
        $frm->fieldAsDropDown('users', UArrayModels::asKeyValues(DAO::getAll(User::class), 'getId'), true);
        $frm->fieldAsSubmit('submit', 'green','');
        $this->jquery->renderView('GroupController/addGroupe.html');

    }


    #[Post(path: "Group/resultPost",name: "group.postGroupe")]
    public function postGroupe(){
        $grp= new Groupe();
        if ($grp){
            URequest::setValuesToObject($grp);
            $users=DAO::getAllByIds( User::class, explode(',',URequest::post('users')));
            var_dump($users)  ;die();
            $grp->setUsers($users);
            $this->repo->insert($grp,true);
        }
        $this->index();

    }

}