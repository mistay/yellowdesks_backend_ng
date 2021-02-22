<?php
namespace App\Model\Table;
use Cake\ORM\Table;

class HostsTable extends Table {
    public function initialize(array $config): void {
        $this->hasMany('Pictures');
        $this->hasMany('Payments');
        $this->hasMany('Videos');
    }

    public function calclatlngloose() {
        $query = $this->find('all')->where(['lat_loose is' => null, 'lng_loose is' => null,]);

        foreach ($query as $row) {
            $row->lat_loose = $row->lat + (mt_rand(-1000,1000) / 1000000.0);
            $row->lng_loose = $row->lng + (mt_rand(-1000,1000) / 1000000.0);
            
            $this->save($row);
        }
    }
}


?>