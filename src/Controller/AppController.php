<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\ORM\TableRegistry;
use Cake\Mailer\TransportFactory;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppController extends CrumbsController
{

    var $appconfigs = [];


    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following component for recommended CakePHP form protection settings.
         * see https://book.cakephp.org/4/en/controllers/components/form-protection.html
         */
        //$this->loadComponent('FormProtection');
        //$this->response = $this->response->withHeader('X-API-Level', '1');

        $model = TableRegistry::get('Configs');
        $query = $model->find('all');
        foreach ($query as $tmp) {
            $this -> appconfigs [$tmp["configkey"]] = $tmp["configvalue"];
        }

        TransportFactory::setConfig('lqh', [
            'host' => $this -> appconfigs['emailhost'],
            'port' => 587,
            'username' => $this -> appconfigs['emailusername'],
            'password' => $this -> appconfigs['emailpassword'],
            'className' => 'Smtp',
            'tls' => true,
        ]);
    }

    public function timegmt() {
        return time() - (int)substr(date('O'),0,3)*60*60;
    }

    public function cleanupbookings() {
        // delete all reserverations that are 15 days old
        // zu heiß. es können jetzt auch die hosts bis zu tage später buchungen akzeptieren
        return;

        // todo: ueberlegen wie wir cleanup loesen koennen
        $model = TableRegistry::get('Bookings');
        $time = date("Y-m-d H:i:s",  $this->timegmt() - (15 * 60));
        $query = $model->deleteAll(["dt_inserted < " => $time]);
    }
    
}
