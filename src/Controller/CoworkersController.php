<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class CoworkersController extends AppController {
    public function profile($unsafe_id) {
	if (!$this -> hasAccess([Roles::ADMIN, Roles::COWORKER, Roles::HOST])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]);

        $user = $this->getloggedInUser();

	$this->set("isAdmin", $user->role==Roles::ADMIN);
	$this->set("isCoworker", $user->role==Roles::COWORKER);
	$this->set("isHost", $user->role==Roles::HOST);

	$id = 0;
        if ($user->role==Roles::ADMIN)
            $id=(int)$unsafe_id;

        if ($user->role==Roles::COWORKER)
            $id = $user -> id;

	if ($user->role==Roles::HOST) {
		// check if host is allowed to see coworker's profile
		$modelbookings = TableRegistry::get('Bookings');
		$query = $modelbookings->find('all')->where(["paypalipn_id >" => "0", "host_id" => $user->id, "coworker_id" => (int)$unsafe_id])->contain(['Hosts', 'Coworkers']);
		if ($query->count() > 0)
			$id=(int)$unsafe_id;
		else
			return;
			
	}
        if ($id>0) {
		$model = TableRegistry::get('Coworkers');

            $row = $model->get($id);
	    $this->set("row", $row);
	}



	
	
    }

    public function index() {
        if (!$this -> hasAccess([Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 

        $model = TableRegistry::get('Coworkers');
        $query = $model->find('all'); //->contain(['Pictures']);
        $this->set("rows", $query);
        
        if (stripos(@$_REQUEST["format"], "json") !== false || stripos(strtolower($_SERVER['HTTP_USER_AGENT']),'android') !== false) {
            $rows = $query->toArray();
            if (@$_REQUEST["format"] == "jsonbrowser") echo "<pre>";
            $ret = [];
            foreach ($rows as $row) {
                
                $pictures = [];
                foreach ($row->pictures as $picture) {
                    array_push($pictures, Router::url(['controller' => 'pictures', 'action' => 'get', $picture->id], true));
                }
                
                array_push($ret, [
                        "id" => $row-> id,
                        "host" => $row->nickname,
                        "desks" => $row->desks,
                        "desks_avail" => $row->desks,
                        "imageURL" => ($row->picture_id > 0 ? Router::url(['controller' => 'pictures', 'action' => 'get', $row->picture_id], true) : null),
                        "images" => $pictures,
                        "details" => $row->details,
                        "title" => $row->title,
                        "lat" => $row->lat + (mt_rand(-1000,1000) / 1000000.0),
                        "lng" => $row->lng + (mt_rand(-1000,1000) / 1000000.0),
                ]);
            }
            
            $this->autoRender = false;
            $this->response->type('application/json');
            $this->response->body(json_encode($rets, JSON_PRETTY_PRINT));
        }
    }
    
    public function cru($unsafe_id=null) {
        if (!$this -> hasAccess([Roles::ADMIN, Roles::COWORKER])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        
        $model = TableRegistry::get('Coworkers');
        
        $user = $this->getloggedInUser();
        
        if ($user->role==Roles::ADMIN)
            $id=(int)$unsafe_id;
        
        if ($user->role==Roles::COWORKER)
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
            
            $model->save($row);
            $this->Flash->set('Successfully saved.');

            // redirect auf index funktioniert für coworkers nicht, die haben keinen zugriff auf ['action' => 'index']
            //return $this->redirect(['action' => 'cru']);
        }
    }
    
    public function changepass($unsafe_id=null) {
        if (!$this -> hasAccess([Roles::ADMIN, Roles::COWORKER])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]);
        
        $user = $this->getloggedInUser();
        
        if ($user->role==Roles::ADMIN)
            $id=(int)$unsafe_id;
        
        if ($user->role==Roles::COWORKER)
            $id = $user -> id;
        
        $model = TableRegistry::get('Coworkers');
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
        
        $model = TableRegistry::get('Coworkers');
        $row = $model->get($unsafe_id);
        $result = $model->delete($row);
        
        return $this->redirect(['action' => 'index']);
    }

    public function register() {
        $this -> autoRender = false;

        $ret=[];
        $ret["error"] = "unknown error";

        $jsondata = $_REQUEST["data"];
        $data = json_decode($jsondata, true);

        $model = TableRegistry::get('Coworkers');
        $row = $model->newEntity();

        if (is_array($data)) {
            $row -> emailconfirmed = false;
            $model->patchEntity($row, $data);

            $row -> password = password_hash($data["password"], PASSWORD_BCRYPT);

            try {
                $model->save($row);
                $ret["error"] = "";
                $ret["coworker"] = [];
                $ret["coworker"]["id"] = $row -> id;
                $ret["coworker"]["username"] = $row -> username;
            } catch (\Exception $e) {
                $ret["error"] = "could not save data. duplicate username? e: " . $e;
            }
        } else 
            $ret["error"] = "could not read data from request. this api method requires data in http request body.";

        $this->autoRender = false;
        $this->response->type('application/json');
        $this->response->body(json_encode($rets, JSON_PRETTY_PRINT));
    }
}
?>
