<?php
namespace services\ui;

 use Ajax\bootstrap\html\HtmlForm;
 use Ajax\php\ubiquity\UIService;
 use Ajax\semantic\widgets\dataform\DataForm;
 use eventListener\GetOneEventListener;
 use models\Group;
 use models\User;
 use Ubiquity\controllers\Controller;
 use Ubiquity\controllers\Router;
 use Ubiquity\orm\DAO;
 use Ubiquity\utils\http\URequest;
 use Ubiquity\utils\models\UArrayModels;

 /**
  * Class UIGroups
  */
 class UIGroups extends UIService {
     public function __construct(Controller $controller) {
         parent::__construct($controller);
         if(!URequest::isAjax()) {
             $this->jquery->getHref('a[data-target]', '', ['hasLoader' => 'internal', 'historize' => false,'listenerOn'=>'body']);
         }
     }


     private function addFormBehavior(string $formName,HtmlForm|DataForm $frm,string $responseElement,string $postUrlName){
         $frm->setValidationParams(["on"=>"blur","inline"=>true]);
         $this->jquery->click("#$formName-div ._validate",'$("#'.$formName.'").form("submit");');
         $this->jquery->click("#$formName-div ._cancel",'$("#'.$formName.'-div").hide();');
         $frm->setSubmitParams(Router::path($postUrlName),'#'.$responseElement,['hasLoader'=>'internal']);
     }



     public function newGroup($formName){
         $frm=$this->semantic->dataForm($formName,new Group());
         $frm->addClass('inline');
         $frm->setFields(['name','email','aliases']);
         $frm->setCaptions(['nom','Email','aliases']);
         $frm->fieldAsLabeledInput('name',['rules'=>'empty']);
         $frm->fieldAsLabeledInput('email',['rules'=>'empty']);
         $this->addFormBehavior($formName,$frm,'new-group','new.groupPost');
     }

     private function semantic() {
         return $this->jquery->semantic();
         //
     }

     public function addUser(){
         $dd=$this->semantic()->htmlDropdown('dd-groupes','Ajoutez des utilisateur ..',UArrayModels::asKeyValues(DAO::getAll(Group::class)));
         $dd->asButton();
         $dd->setIcon('users');

         $users=new User();
         $grp=new Group();
         $ids=\array_map(function ($user){
             return $user->getId();
         },$grp->getUsers());
         $grp->userIds=implode(',',$ids);
         $frm=$this->jquery->semantic()->dataForm('frm-user',$users);
         $frm->setActionTarget(Router::path('user.addPost'),'');
         $frm->setProperty('method','post');
         $frm->setFields(['userIds','submit']);
         $frm->fieldAsSubmit('submit','green','');
         $frm->fieldAsDropDown('userIds',UArrayModels::asKeyValues(DAO::getAll(User::class),'getId'),'true');
     }
 }
