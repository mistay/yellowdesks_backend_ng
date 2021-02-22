<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Event\Event;

class LogsController extends AppController {
    
    public function index() {
        if (!$this -> hasAccess([Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        $model = TableRegistry::get('Logs');
        $query = $model->find('all', ['order' => ['ts_logged' => 'DESC']])->limit(100);
        $this->set("rows", $query);
    }
    public function clear() {
        $model = TableRegistry::get('Logs');
        $query = $model->deleteAll([]);
        
        $this->redirect(["action" => "index"]);
    }
}
?>