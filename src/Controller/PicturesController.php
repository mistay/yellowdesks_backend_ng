<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class PicturesController extends AppController {
    
    public $paginate = [
        'limit' => 100,
        'order' => [
            'Host.id' => 'asc'
        ]
    ];
    
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    
    public function delete($unsafe_id) {
        if (!$this -> hasAccess([Roles::ADMIN, Roles::HOST, Roles::COWORKER])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        
        $model = TableRegistry::get('Pictures');
        $row = $model->get($unsafe_id);

        $user = $this->getloggedInUser();
        if ($user -> role == Roles::ADMIN)
            $result = $model->delete($row);
        if ($user -> role == Roles::HOST) {
            if ( $row -> host_id == $user -> id ) {
                $result = $model->delete($row);
            }
        }
        if ($user -> role == Roles::COWORKER) {
            if ( $row -> coworker_id == $user -> id ) {
                $result = $model->delete($row);
            }
        }
        
        return $this->redirect(['action' => 'index']);
    }

    public function cru() {
        if (!$this -> hasAccess([Roles::ADMIN, Roles::HOST, Roles::COWORKER])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        
        $user = $this->getloggedInUser();

        $model = TableRegistry::get('Hosts');
        $hosts = $model->find('all');
        $this->set("hosts", $hosts);

        $model = TableRegistry::get('Coworkers');
        $coworkers = $model->find('all');
        $this->set("coworkers", $coworkers);

        
        $data = $this->request->getData();

        $uploads = 0;
        if (!empty($data)) {
            foreach ($data["files"] as $file) {
                $model2 = TableRegistry::get('Pictures');
                $row = $model2->newEntity();
        
                $row->mime=$file["type"];
                $row->name="";
                
                if ($user -> role == Roles::ADMIN) {
                    if ( $data["host_id"] > 0)
                        // user will für host bilder raufladen
                        $row -> host_id = $data["host_id"];
                    if ( $data["coworker_id"] > 0)
                        // user will für coworker bilder raufladen
                        $row -> coworker_id = $data["coworker_id"];
                }
                elseif ($user -> role == Roles::HOST)
                    $row -> host_id = $user -> id;
                elseif ($user -> role == Roles::COWORKER)
                    $row -> coworker_id = $user -> id;

                if (!isset($file["tmp_name"]) || $file["tmp_name"] == "" ) {
                    $this -> Flash -> success (__("No file specified. Please choose at least one file to upload."));
                    continue;
                } else {

                    $row->data = file_get_contents($file["tmp_name"]);
                    $succ = $model2->save($row);

                    // assign host this picture if not done yet.
                    if ($row -> host_id > 0) {
                        foreach ($hosts as $host) {
                            if ($host -> id == $row -> host_id) {
                                // found
                                if ($host -> picture_id == null) {
                                    $data = [];
                                    $data["picture_id"] = $row -> id;
                                    $model -> patchEntity($host, $data);
                                    $model -> save($host);
                                }
                                break;
                            }
                        }
                    }
                    if ($row -> coworker_id > 0) {
                        foreach ($coworkers as $coworker) {
                            if ($coworker -> id == $row -> coworker_id) {
                                // found
                                if ($coworker -> picture_id == null) {
                                    $data = [];
                                    $data["picture_id"] = $row -> id;
                                    $model -> patchEntity($coworker, $data);
                                    $model -> save($coworker);
                                }
                                break;
                            }
                        }
                    }   
                }
                $uploads++;
            }
            if ($uploads>0) {
                $this -> Flash -> success (__("Successfully uploaded {0} picture(s).", $uploads));
    
                // prevent DUPes by browser reload
                $this->redirect(["action" => "cru"]);
            }

        }
    }

    public function index() {
        if (!$this -> hasAccess([Roles::ADMIN, Roles::HOST, Roles::COWORKER])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        $modelPictures = TableRegistry::get('Pictures');
        
        $user = $this->getloggedInUser();
        if ($user->role == Roles::HOST) {
            $host_id = $user -> id;
            $where = ['host_id' => $host_id]; 
            $model = TableRegistry::get('Hosts');
            $host = $model->get($host_id);
            $this->set("host", $host);
        }
        elseif ($user->role == Roles::COWORKER) {
            $coworker_id = $user -> id;
            $where = ['coworker_id' => $coworker_id]; 
            $model = TableRegistry::get('Coworkers');
            $coworker = $model->get($coworker_id);
            $this->set("coworker", $coworker);
        }
        elseif ($user->role == Roles::ADMIN) {
            $host_id = null;
            $where = [];
        }

        $rows = $modelPictures->find('all', ['fields' => ["id", "name"]])->where($where);
        $this->set("rows", $rows);

        
        // e.g. http://localhost:8888/yellowdesks/pictures?host_id=5&format=jsonbrowser
        /*
        [
            {
                "id": 1,
                "name": "standdesk",
                "mime": "image\/jpeg",
            },
            {
                "id": 2,
                "name": "restroom",
                "mime": "image\/jpeg",
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
                        ]);
                
            }
            $this->autoRender = false;
            $this->response->type('application/json');
            $this->response->body(json_encode($rets, JSON_PRETTY_PRINT));
        }
    }
    
    public function get($unsafe_id) {
        // kein hasAccess() weil coworker sowieso alle pictures sehen darf
        // todo: überlegn ob viell doch hasAccess() damit kein anyonmer (weder cowoker noch host noch admin) lesen darf?
        // aber dann is auth() nötig und bei fb-login wäre für jedes bild ein request zu fb nötig; oder ein fb-login cache?

        $this->autoRender=false;
        
        $id = (int) $unsafe_id;
        
        $model = TableRegistry::get('Pictures');
        $query = $model->get($id);
        
        $data = stream_get_contents($query->data);

        $virtualfilename = 'data://text/plain;base64,' . base64_encode($data);
        
        $exif = null;
        $ort = 0;
        try {
            // kann zB bei pngs exif_read_data(H+VE2DH4J30BAAAAAElFTkSuQmCC): File not supported 
            // werfen. deshalb mit @ .. todo: sauberer lösen?
            $exif = @exif_read_data ( $virtualfilename );
        } catch (Exception $e) {
        }
        
        //$ort = $exif['IFD0']['Orientation'];
        $ort = ($exif != null && isset($exif['Orientation'])) ? $exif['Orientation'] : 0;
        
        $degrees=0;
        $flip=0;
        switch($ort)
        {
            case 1: /*nothing */ break;
            case 2: $flip = IMG_FLIP_HORIZONTAL;                    break;
            case 3:                                 $degrees = 180; break;
            case 4: $flip = IMG_FLIP_VERTICAL;                      break;
            case 5: $flip = IMG_FLIP_VERTICAL;      $degrees = -90; break;
            case 6:                                 $degrees = -90; break;
            case 7: $flip = IMG_FLIP_HORIZONTAL;    $degrees = -90; break;
            case 8:                                 $degrees = 90;  break;
        }
            
            
        // e.g. /pictures/get?resolution=320x240
        if (isset($_REQUEST["resolution"])) {
            $src_img = imagecreatefromstring($data);
            $src_w = imagesx($src_img);
            $src_h = imagesy($src_img);
            $ratio = $src_w / $src_h;
            
            list($dst_w, $dst_h) = explode("x", $_REQUEST["resolution"]);
            
            
            // http://stackoverflow.com/questions/6594089/calculating-image-size-ratio-for-resizing
            $srcX = $srcY = 0;
            if (isset($_REQUEST["crop"])) {
                // http://polyetilen.lt/en/resize-and-crop-image-from-center-with-php
                $width_new = $src_h * $dst_w / $dst_h;
                $height_new = $src_w * $dst_h / $dst_w;
                //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
                if($width_new > $src_w){
                    //cut point by height
                    $h_point = (($src_h - $height_new) / 2);
                    //copy image

                    $dst_image = imagecreatetruecolor($dst_w, $dst_h);
                    imagecopyresampled($dst_image, $src_img, 0, 0, 0, $h_point, $dst_w, $dst_h, $src_w, $height_new);
                }else{
                    //cut point by width
                    $w_point = (($src_w - $width_new) / 2);

                    $dst_image = imagecreatetruecolor($dst_w, $dst_h);
                    imagecopyresampled($dst_image, $src_img, 0, 0, $w_point, 0, $dst_w, $dst_h, $width_new, $src_h);
                }
            } else {
                $dst_w = $dst_h = min(max($dst_w, $dst_h), max($src_w, $src_h));
                if ($ratio < 1)     $dst_w = $dst_h * $ratio;
                else                $dst_h = $dst_w / $ratio;

                $dst_image = imagecreatetruecolor($dst_w, $dst_h);
                imagecopyresampled($dst_image, $src_img , 0, 0, $srcX, $srcY, $dst_w, $dst_h , $src_w, $src_h);
            }

            
            if ($degrees != 0)
                $dst_image = imagerotate($dst_image, $degrees, 0);
            
            if ($flip > 0)
                imageflip ( $image, $flip );
            
            header("Content-Type: image/jpeg");
            imagejpeg ($dst_image);
        } else {
            header("Content-Type: " . $query->mime);
            print_r($data);
            
        }
        exit(0);
    }
}
?>