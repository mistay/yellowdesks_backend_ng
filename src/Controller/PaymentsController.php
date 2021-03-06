<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

use Cake\Event\Event;

class PaymentsController extends AppController {
    
    public function index() {
        if (!$this -> hasAccess([Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 

        $model = TableRegistry::get('Payments');
        $query = $model->find('all')->contain(['Bankaccounts', 'Bookings.Coworkers']);
        $this->set("rows", $query);
    }
}
?>