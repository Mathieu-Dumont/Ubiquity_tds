<?php
namespace controllers;
 use Ajax\php\ubiquity\JsUtils;
 use Ubiquity\attributes\items\router\Get;
 use Ubiquity\attributes\items\router\Post;
 use Ubiquity\controllers\auth\AuthController;
 use Ubiquity\controllers\auth\WithAuthTrait;
 use Ubiquity\controllers\Router;
 use Ubiquity\utils\http\URequest;
 use Ubiquity\utils\http\USession;

 /**
  * Controller TodosController
  * @property JsUtils jquery
  */
class TodosController extends \controllers\ControllerBase{
    use WithAuthTrait;
    const CACHE_KEY = 'datas/lists/';
    const EMPTY_LIST_ID='not saved';
    const LIST_SESSION_KEY='list';
    const ACTIVE_LIST_SESSION_KEY='active-list';

    private function showMessage(string $header, string $message, string $type = '', string $icon = 'info circle',array $buttons=[]) {
        $this->loadView('main/message.html', compact('header', 'type', 'icon', 'message','buttons'));
    }

    #[Get(path: "#/_default/",name: "home")]
    public function index(){
        $list=USession::get('list', []);
        $this->jquery->click('._toEdit', 'let item=$(this).closest("div.item");
                                                            item.find("form").toggle();
                                                            item.find(".checkbox").toggle();');
        $this->jquery->getOnClick('._toDelete',Router::path('Todos.deleteElement'),'._content',['hasLoader'=>'internal', 'attrs'=>'data-available']);
        $this->jquery->getHref('a', parameters: ['hasLoader' =>false, 'historize'=>false]);
        $this->jquery->postOn('submit','.formEdit',Router::path('Todos.editElement'),'{id: $(this).find("input").attrs("id")}','._content',['hasLoader'=>'internal']);
        $this->jquery->renderView('TodosController/index.html', ['list'=>$list]);
    }

    #[Get(path: "todos/add/",name: "Todos.addElement")]
    public function addElement(){
        $this->jquery->postFormOnClick('button',Router::path('Todos.loadListFromForm'), 'frm','._content', ['hasLoader'=>'internal']);
        $this->jquery->renderView('TodosController/addElement.html');

    }


    #[Get(path: "todos/delete/{index}",name: "Todos.deleteElement")]
    public function deleteElement($index){
    $list=USession::get(self::ACTIVE_LIST_SESSION_KEY);
    unset($list[$index]);
    USession::set(self::ACTIVE_LIST_SESSION_KEY,\array_values($list));
    $this->index();
    }


    #[Post(path: "todos/editElement/{index}",name: "Todos.editElement")]
    public function editElement($index){
        $list=USession::get(self::ACTIVE_LIST_SESSION_KEY);
        $list[URequest::get('id')]=URequest::post('._formEdit');
        USession::set(self::ACTIVE_LIST_SESSION_KEY,$list);
        $this->index();

    }


    #[Get(path: "todos/loadList/{uniqid}",name: "Todos.loadList")]
    public function loadList($uniqid){

        $this->loadView('TodosController/loadList.html');

    }


    #[Post(path: "todos/loadListFromForm",name: "Todos.loadListFromForm")]
    public function loadListFromForm(){

        USession::addValueToArray('list', URequest::post('items'));
        echo "listes ajoutées";

    }


    #[Get(path: "List/newList/{force}",name: "Todos.newList")]
    public function newList($force){

        $this->loadView('TodosController/newList.html');

    }


    #[Get(path: "todos/saveList",name: "Todos.saveList")]
    public function saveList(){

        $this->loadView('TodosController/saveList.html');

    }

    protected function getAuthController(): AuthController
    {
        return new MyAuth($this);
    }
}
