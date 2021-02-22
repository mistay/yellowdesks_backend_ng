<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Event\Event;

class HostsController extends AppController {
    
    // called by jquery ajax
    public function setposition() {
        $this->autoRender=false;
        if (!$this -> hasAccess([Roles::HOST])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        
        if ($this -> request -> is('post')) {

            $model = TableRegistry::get('Hosts');
            $user = $this->getloggedInUser();
            $row = $model->get($user->id);

            $data = $this->request->getData();
            $model->patchEntity($row, ["lat" => $data["lat"], "lng" => $data["lng"], "lat_loose" => null, "lng_loose" => null] );

            $model->save($row);

            $model->calclatlngloose();
        }
    }

    public function map() {
        if (!$this -> hasAccess([Roles::HOST])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        $model = TableRegistry::get('Hosts');
        $user = $this->getloggedinUser();
        $row = $model->get($user->id);

        $this->set("row", $row);
    }

    public function pictures($host_id = null) {
        if (!$this -> hasAccess([Roles::ADMIN, Roles::COWORKER])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        
        $modelPictures = TableRegistry::get('Pictures');
        
        $user = $this->getloggedInUser();
        
        if ($user->role == Roles::HOST)
            $host_id = $user -> id;
            
        $where = ($host_id == null) ? [] : ['host_id' => $host_id];

        $model = TableRegistry::get('Hosts');
        $row = $model->get($host_id);
        $this->set("row", $row);

        $pictures = $modelPictures->find('all', ['fields' => ["id", "name"]])->where($where);
        $this->set("pictures", $pictures);
    }

    public function cruyd($unsafe_id=null) {
	return $this->cru($unsafe_id);
    }
    public function cru($unsafe_id=null) {
        if (!$this -> hasAccess([Roles::ADMIN, Roles::HOST])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        
        $model = TableRegistry::get('Hosts');
        
        $user = $this->getloggedInUser();
        $this->set("isHost", $user["role"] == Roles::HOST);
        $this->set("isAdmin", $user["role"] == Roles::ADMIN);

        if ($user->role==Roles::ADMIN)
            $id=(int)$unsafe_id;
        
        if ($user->role==Roles::HOST)
            $id = $user -> id;
        
        $row = [];
        if ($id>0) {
            $row = $model->get($id);
        } else {
            $row = $model->newEntity();
        }
        $this->set("row", $row);
        if (!empty($this->request->getData())) {
            $model->patchEntity($row, $this->request->getData());
            
            $row->open_247fixworkers = $this->request->getData("open_247fixworkers") == "on";
            //$row->lat_loose = null;
            //$row->lng_loose = null;
            
            $model->save($row);
            $this -> Flash -> success (__("Successfully saved."));
        }
    }
    
    public function changepass($unsafe_id) {
        if (!$this -> hasAccess([Roles::ADMIN, Roles::HOST])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]);
        
        $user = $this->getloggedInUser();
        
        if ($user->role==Roles::ADMIN)
            $id=(int)$unsafe_id;
        
        if ($user->role==Roles::HOST)
            $id = $user -> id;
        
        $model = TableRegistry::get('Hosts');
        $row = $model->get($unsafe_id);
        $this->set("row", $row);
        
        if (!empty($this->request->getData())) {
            $model->patchEntity($row, $this->request->getData());
            
            $pass1 = $this->request->getData()["password1"];
            $pass2 = $this->request->getData()["password2"];
            
            if ($pass1 == $pass2) {
                
                $row->password = password_hash($pass1, PASSWORD_BCRYPT);
                
                if ($model->save($row)) {
                    $this->Flash->set('Password successfully set.');
                    return $this->redirect(['action' => 'cru', $id]);
                } else {
                    $this->Flash->error(__('Database Error: Could not save data.'));
                }
            } else {
                $this->Flash->error(__('Passwords do not match, please correct.'));
            }
        }
    }
    
    public function delete($unsafe_id) {
        if (!$this -> hasAccess([Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        
        $model = TableRegistry::get('Hosts');
        $row = $model->get($unsafe_id);
        // todo: erst lesen damit man löschen kann? warum nicht return $this->deleteAll(['id' => 1]); https://book.cakephp.org/3.0/en/orm/deleting-data.html
        $result = $model->delete($row);
        
        return $this->redirect(['action' => 'index']);
    }
    
    public function details($host_id = null) {
        $this->viewBuilder()->layout('lightbox');

        $model = TableRegistry::get('Hosts');
        $query = $model->find('all')
                                ->where(["id" => $host_id])
                                ->contain(['Pictures'=> function ($q) {
                                                               return $q
                                                                    ->select(['id', 'host_id']);
                                                            },
                                               'Videos']);
        $row = $query -> first();
        $this->set("row", $row);

        $this->set("user", $this->getloggedinUser());

    }
    
    // todo: request device information (display size) and send imageURL with correct resolution
    // e.g. /pictures/get/311?resolution=100x100&crop=true instead of /pictrues/get/311
    public function index() {
        
        $model = TableRegistry::get('Hosts');
        $query = $model->find('all')	->where(['enableyd' => 1])
					->contain(['Pictures'=> function ($q) {
                                                               return $q
                                                                    ->select(['id', 'host_id']);
                                                            },
                                               'Payments', 'Videos']);
        
        $model = TableRegistry::get('Logs');
        $row = $model->newEntity([]);
        $row->message = print_r($_REQUEST, true) .  print_r($_SERVER, true);

        if ($model->save($row)) {
        }
        
        if (stripos(@$_REQUEST["format"], "json") !== false || stripos(strtolower($_SERVER['HTTP_USER_AGENT']),'android') !== false) {
            $rows = $query->toArray();
            if (@$_REQUEST["format"] == "jsonbrowser") echo "<pre>";
            
            $ret = [];
            foreach ($rows as $row) {
                
                $pictures = [];
                foreach ($row->pictures as $picture) {
                    array_push($pictures, Router::url(['controller' => 'pictures', 'action' => 'get', $picture->id, 'resolution' => '600x400', 'crop' => true], true));
                }
                
                array_push($ret,
                        [   "id" => $row-> id,
                            "host" => $row->nickname,
                            "desks" => $row->desks,
                            "desks_avail" => $row->desks,
                            "picture_id" => $row->picture_id,
                            "imageURL" => ($row->picture_id > 0 ? Router::url(['controller' => 'pictures', 'action' => 'get', $row->picture_id, 'resolution' => '600x'], true) : null),
                            "imageURLs" => $pictures,
                            "details" => $row->details,
                            "extras" => $row->extras,
                            "open_monday_from" => $row->open_monday_from == null ? null : date("H:i:s", strtotime($row->open_monday_from)),
                            "open_monday_till" => $row->open_monday_till == null ? null : date("H:i:s", strtotime($row->open_monday_till)),
                            "open_tuesday_from" => $row->open_tuesday_from == null ? null : date("H:i:s", strtotime($row->open_tuesday_from)),
                            "open_tuesday_till" => $row->open_tuesday_till == null ? null : date("H:i:s", strtotime($row->open_tuesday_till)),
                            "open_wednesday_from" => $row->open_wednesday_from == null ? null : date("H:i:s", strtotime($row->open_wednesday_from)),
                            "open_wednesday_till" => $row->open_wednesday_till == null ? null : date("H:i:s", strtotime($row->open_wednesday_till)),
                            "open_thursday_from" => $row->open_thursday_from == null ? null : date("H:i:s", strtotime($row->open_thursday_from)),
                            "open_thursday_till" => $row->open_thursday_till == null ? null : date("H:i:s", strtotime($row->open_thursday_till)),
                            "open_friday_from" => $row->open_friday_from == null ? null : date("H:i:s", strtotime($row->open_friday_from)),
                            "open_friday_till" => $row->open_friday_till == null ? null : date("H:i:s", strtotime($row->open_friday_till)),
                            "open_saturday_from" => $row->open_saturday_from == null ? null : date("H:i:s", strtotime($row->open_saturday_from)),
                            "open_saturday_till" => $row->open_saturday_till == null ? null : date("H:i:s", strtotime($row->open_saturday_till)),
                            "open_sunday_from" => $row->open_sunday_from == null ? null : date("H:i:s", strtotime($row->open_sunday_from)),
                            "open_sunday_till" => $row->open_sunday_till == null ? null : date("H:i:s", strtotime($row->open_sunday_till)),
                            "open_247fixworkers" => $row->open_247fixworkers,
                            "price_1day" => $row->price_1day,
                            "price_10days" => $row->price_10days,
                            "price_1month" => $row->price_1month,
                            "price_6months" => $row->price_6months,
                            "cancellationscheme" => $row->cancllationscheme,
                            "title" => $row->title,
                            "videoURL" => (sizeof($row->videos) > 0 ? Router::url(['controller' => 'videos', 'action' => '', $row->videos[0]->url], true) : null),
                            
                            "lat" => $row->lat,
                            "lng" => $row->lng,
                        ]);
            }
            
            echo json_encode($ret, JSON_PRETTY_PRINT);
            if (@$_REQUEST["format"] == "jsonbrowser") echo "</pre>";
            exit();
        }
        
        if (!$this -> hasAccess([Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        $this->set("rows", $query);
    }
}
?>
