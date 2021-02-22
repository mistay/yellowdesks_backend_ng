<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class VideosController extends AppController {

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    
    public $paginate = [
        'limit' => 100,
        'order' => [
            'Host.id' => 'asc'
        ]
    ];
    
    public function index() {
        if (!$this -> hasAccess([Roles::HOST, Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        $model = TableRegistry::get('Videos');
        
        $user = $this->getloggedInUser();
        if ($user->role == Roles::HOST)
            $host_id = $user -> id;
        if ($user->role == Roles::ADMIN)
            $host_id = null;

        $where = $host_id == null ? [] : ['host_id' => $host_id]; 

        $query = $model->find('all')->where($where)->contain(['Hosts']);
        $this->set("rows", $this->paginate($query));
        
        
        // e.g. http://localhost:8888/yellowdesks/videos?host_id=5&format=jsonbrowser
        /*
        [
            {
                "id": 1,
                "name": "standdesk",
                "mime": "image\/jpeg",
                "data": "<base64string...>"
            },
            {
                "id": 2,
                "name": "restroom",
                "mime": "image\/jpeg",
                "data": "<base64string...>"
           }
        ]
        */
        if (stripos(@$_REQUEST["format"], "json") !== false || stripos(strtolower($_SERVER['HTTP_USER_AGENT']),'android') !== false) {
            $rows = $query->toArray();
            if (@$_REQUEST["format"] == "jsonbrowser") echo "<pre>";
            $ret = [];
            foreach ($rows as $row) {
                array_push($ret,
                        [   "id" => $row->id,
                            "name" => $row->name,
                            "mime" => $row->mime,
                            "url" => "https://yellowdesks.com/videos/" . $row->url,
                        ]);
                
            }
            $this->autoRender = false;
            $this->response->type('application/json');
            $this->response->body(json_encode($rets, JSON_PRETTY_PRINT));
        }
    }
    
    public function get($unsafe_id) {
       if (!$this -> hasAccess([Roles::COWORKER, Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
       $this->autoRender=false;
        
        $id = (int) $unsafe_id;
        
        $model = TableRegistry::get('Videos');
        $query = $model->get($id);
        //$data = stream_get_contents($query->data);
        
        header("Content-Type: video/mp4");
        echo $data;

        exit(0);
    }
}
?>