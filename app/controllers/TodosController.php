<?php
namespace controllers;
use Ajax\JsUtils;
use Ubiquity\attributes\items\router\Get;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\attributes\items\router\Post;
use Ubiquity\controllers\auth\AuthController;
use Ubiquity\controllers\auth\WithAuthTrait;
use Ubiquity\controllers\Router;
use Ubiquity\utils\http\URequest;
use Ubiquity\utils\http\USession;

/**
 * Controller TodosController
 * @property JsUtils $jquery
 */
class TodosController extends \controllers\ControllerBase{

    use WithAuthTrait{
        initialize as initAuth;
    }

    public function initialize()
    {
        $this->initAuth();
        if(!URequest::isAjax()) {
            $this->jquery->getOnClick('._toDelete', Router::path('todos.deleteElement'), '._content', ['hasLoader' => 'internal', 'attr' => 'data-value','listenerOn'=>'body']);
        }
    }

    const CACHE_KEY = 'datas/lists/';
    const EMPTY_LIST_ID='not saved';
    const LIST_SESSION_KEY='list';
    const ACTIVE_LIST_SESSION_KEY='active-list';

    public function __construct()
    {
        parent::__construct();

    }

    private function showMessage(string $header, string $message, string $type = '', string $icon = 'info circle',array $buttons=[]) {
        $this->loadView('main/message.html', compact('header', 'type', 'icon', 'message','buttons'));
    }

    #[Get(path: "#/_default/",name: "home")]
    public function index(){
        $list=USession::get(self::ACTIVE_LIST_SESSION_KEY, []);
        $this->jquery->click('._toEdit', 'let item=$(this).closest("div.item");
                                                            item.find("form").toggle();
                                                            item.find(".checkbox").toggle();');
        $this->jquery->getHref('a', parameters: ['hasLoader' =>false, 'historize'=>false]);
        $this->jquery->postOn('submit','._formEdit',Router::path('todos.editElement'), '{id: $(this).find("input").attr("id"), element: $(this).find("input").val()}','._content', ['hasLoader'=>'internal']);
        $this->jquery->renderView('TodosController/index.html', ['list'=>$list]);
    }

    #[Get(path: "todos/add/",name: "todos.addElement")]
    public function addElement(){
        $this->jquery->postFormOnClick('button',Router::path('todos.loadListFromForm'), 'frm','._content', ['hasLoader'=>'internal']);
        $this->jquery->renderView('TodosController/addElement.html');
    }


    #[Get(path: "todos/delete/{index}",name: "todos.deleteElement")]
    public function deleteElement($index=0){
        $list=USession::get(self::ACTIVE_LIST_SESSION_KEY, []);
        unset($list[$index]);
        USession::set(self::ACTIVE_LIST_SESSION_KEY,\array_values($list));
        $this->index();
    }


    #[Post(path: "todos/edit/{index}",name: "todos.editElement")]
    public function editElement($index=0){
        $list=USession::get(self::ACTIVE_LIST_SESSION_KEY, []);
        $list[URequest::post('id')]=URequest::post('element');
        USession::set(self::ACTIVE_LIST_SESSION_KEY,\array_values($list));
        $this->index();
        echo "edit effectuée";


    }


    #[Get(path: "/todos/loadList/{uniquid}",name: "todos.loadList")]
    public function loadList($uniquid){

        $this->loadView('TodosController/loadList.html');

    }


    #[Post(path: "/todos/loadList",name: "todos.loadListFromForm")]
    public function loadListFromForm(){

        USession::addValueToArray(self::ACTIVE_LIST_SESSION_KEY, URequest::post('items'));
        echo "listes ajoutées";

    }


    #[Get(path: "/todos/new/{force}",name: "todos.newList")]
    public function newList($force){

        $this->loadView('TodosController/newList.html');

    }


    #[Get(path: "/todos/saveList",name: "todos.saveList")]
    public function saveList(){

        $list=USession::get(self::ACTIVE_LIST_SESSION_KEY, []);
        $id=uniqid('',true);
        CacheManager::$cache->store(self::CACHE_KEY . $id, $list);
        $this->loadView('TodosController/saveList.html');

    }

    protected function getAuthController(): AuthController
    {
        return new MyAuth($this);
    }
}