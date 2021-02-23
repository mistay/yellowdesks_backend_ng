<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class ConfigsController extends AppController {
    public function index() {
        if (!$this -> hasAccess([Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 

        $model = TableRegistry::get('Configs');
        $query = $model->find('all');
        $this->set("rows", $query);
    }

    public function cru($id=null) {
        if (!$this -> hasAccess([Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        
        $model = TableRegistry::get('Configs');
        
        $row = [];
        if ($id>0) {
            $row = $model->get($id);
        } else {
            $row = $model->newEntity([]);
        }
        $this->set("row", $row);
        if (!empty($this->request->getData())) {
            $model->patchEntity($row, $this->request->getData());
            
            $model->save($row);
            $this->Flash->set('Successfully saved.');

            //return $this->redirect(['action' => 'cru']);
        }
    }

    public function delete($id) {
        if (!$this -> hasAccess([Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        
        $model = TableRegistry::get('Configs');
        $row = $model->get($id);
        $result = $model->delete($row);
        
        return $this->redirect(['action' => 'index']);
    }

}
?>