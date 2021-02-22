<?php
namespace App\Model\Table;
use Cake\ORM\Table;

class HolidaysTable extends Table {
    public function initialize(array $config): void {
        //$this->belongsTo('Hosts');
    }
    
    public function getworkingdays($unsafe_begin, $unsafe_end) {
        $ret = [];
        
        $count = 0;
        $ret["count"] = $count;
        $ret["details"] = "";
        $ret["calendardays"] = 0;
        
        $query = $this->find('all');
        
        
        $begin = new \Datetime($unsafe_begin);
        $end = new \Datetime($unsafe_end);
        $end = $end->modify( '+1 day' ); 
        $ret["begin"] = $begin->format("Y-m-d");
        $ret["end"] = $end->format("Y-m-d");
        
        $interval = new \DateInterval('P1D');
        $daterange = new \DatePeriod($begin, $interval ,$end);

        
        foreach($daterange as $date){
            $ret["calendardays"]++;
            
            $ret["details"] .= $date->format("Y-m-d: ") . $date->format("Ymd") . ": ";

            if (date('N', $date->getTimestamp()) >= 6) {
                $ret["details"] .= "weekend\n";
                continue;
            }
            
            foreach ($query as $row) {
                if ($date->format("Ymd") == date ("Ymd", strtotime($row->date))) {
                    // holiday
                    $ret["details"] .= "holiday\n";
                    continue 2;
                }
            }
            $ret["count"]++;
            $ret["details"] .= "working day\n";
        }
        
        return $ret;
    }
}

?>