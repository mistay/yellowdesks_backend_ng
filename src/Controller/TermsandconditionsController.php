<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

use Cake\Event\Event;

class TermsandconditionsController extends AppController {
    public function index() {
        $model = TableRegistry::get('Termsandconditions');

        if ($this -> hasAccess([Roles::ADMIN])) {
            $query = $model->find('all');
            $this->set("rows", $query);
        } else {
            $this->redirect(["action" => "latest"]);
        }
    }

    public function latest() {
        $model = TableRegistry::get('Termsandconditions');
        //$this -> layout = 

        $query = $model->find('all')->order(["version DESC"]);
        $row = $query->first();
        
        $this->set("row", $row);
    }
}
?>