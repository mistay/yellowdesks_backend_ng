<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Event\Event;

require_once('3rdparty/TCPDF/tcpdf.php');

class MYPDF extends \TCPDF {
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
	$tmp ="Yellowdesks GmbH :: Jakob-Haringer-Str. 3 c/o COWORKINGSALZBURG :: 5020 Salzburg :: Fax: +43 662 890000-9 :: E-Mail: hello@yellowdesks.com";

        $this->Cell(0, 10, $tmp, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

class BookingsController extends AppController {

	public function pdfinvoice($id) {
		if (!$this -> hasAccess([Roles::COWORKER, Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]);

		$model = TableRegistry::get('Bookings');
		$query = $model->get($id, [
			'contain' => ['Hosts', 'Coworkers']
		]);

		$row = $query -> toArray();

		$this -> autoRender = false;

		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Yellowdesks GmbH');
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->AddPage();


		$pdf->SetXY(130, 10);
		$pdf->SetFont('helvetica', 'I', 20);
		$pdf->Cell(0, 0, 'Yellowdesks GmbH', 0, 1, 'L', 0, '', 0);
		$pdf->SetFont('helvetica', 'I', 7);
		$pdf->setX(130);
		$pdf->Cell(0, 0, 'Jakob-Haringer-Str. 3', 0, 1, 'L', 0, '', 0);
		$pdf->setX(130);
		$pdf->Cell(0, 0, 'c/o COWORKINGSALZBURG', 0, 1, 'L', 0, '', 0);
		$pdf->setX(130);
		$pdf->Cell(0, 0, '5020 Salzburg', 0, 1, 'L', 0, '', 0);
		$pdf->setX(130);
		$pdf->Cell(0, 0, 'Austria', 0, 1, 'L', 0, '', 0);
		$pdf->setX(130);
		$pdf->Cell(0, 0, 'billing@yellowdesks.com', 0, 1, 'L', 0, '', 0);
		$pdf->setX(130);
		$pdf->Cell(0, 0, 'Our VATID: ATU1234567890', 0, 1, 'L', 0, '', 0);
		$pdf->setX(130);
		$pdf->Cell(0, 0, 'Your VATID: ' . $row["coworker"]["vatid"], 0, 1, 'L', 0, '', 0);
		$pdf->setX(130);
		$pdf->Cell(0, 0, 'Invoice No: ' . $row["id"], 0, 1, 'L', 0, '', 0);
		$pdf->setX(130);
		$pdf->Cell(0, 0, 'Invoice Date: ' . date("Y-m-d", strtotime($row["dt_inserted"])), 0, 1, 'L', 0, '', 0);
		$pdf->setX(130);
		$pdf->Cell(0, 10, 'Page '.$pdf->getAliasNumPage().' of '.$pdf->getAliasNbPages(), 0, 1, 'L', 0, '', 0, false, 'T', 'M');

		$pdf->SetXY(10,45);
		$pdf->SetFont('helvetica', 'I', 6);
		$pdf->Cell(0, 0, 'Yellowdesks GmbH :: Jakob-Haringer-Str. 3 c/o coworkingsalzburg :: 5020 Salzburg :: Austria', 0, 1, 'L', 0, '', 0);
		$pdf->Ln(3);
		$pdf->SetFont('helvetica', 'I', 10);
		$pdf->Cell(0, 0, $row["coworker"]["companyname"], 0, 1, 'L', 0, '', 0);
		$pdf->Cell(0, 0, $row["coworker"]["firstname"] . " " .$row["coworker"]["lastname"], 0, 1, 'L', 0, '', 0);
		$pdf->Cell(0, 0, $row["coworker"]["address"], 0, 1, 'L', 0, '', 0);
		$pdf->Cell(0, 0, $row["coworker"]["postal_code"] . " " . $row["coworker"]["city"], 0, 1, 'L', 0, '', 0);

		$pdf->SetXY(10,90);
		$pdf->SetFont('helvetica', 'I', 20);
		$pdf->Cell(0, 0, "Invoice", 0, 1, 'L', 0, '', 0);

		$pdf->SetFont('helvetica', 'I', 10);

		$pdf->Cell(0, 0, $row["description"] . ': ' . $row["price"] . 'EUR + ' . $row["vat"] . ' EUR VAT' , 0, 1, 'L', 0, '', 0);

		$pdf->SetFont('helvetica', 'I', 8);
		$pdf->Cell(0, 0, "Sale was exempted from VAT", 0, 1, 'L', 0, '', 0);

		$pdf->Ln(3);
		$legal = "Place of jurisdiction: Salzburg. 8% default interest. Please have a look at our Terms & Conditions";
		$pdf->Cell(0, 0, $legal, 0, 1, 'L', 0, '', 0);




		$pdf->Output('example_001.pdf', 'I');




	
	}
    
    public function index() {
        if (!$this -> hasAccess([Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 

        $this->cleanupBookings();
        
        $model = TableRegistry::get('Bookings');

        $user = $this -> getLoggedinUser();

        $query = $model->find('all')->order(["dt_inserted DESC"])->where()->contain(['Hosts', 'Coworkers']);
        $this->set("rows", $query);
    }

    public function mybookings() {
        if (!$this -> hasAccess([Roles::COWORKER])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 

        $this->cleanupBookings();
        
        $model = TableRegistry::get('Bookings');

        $user = $this -> getLoggedinUser();

        $query = $model->find('all')->order(["dt_inserted DESC"])->where(["coworker_id" => $user -> id])->contain(['Hosts', 'Coworkers']);
        $this->set("rows", $query);
    }
    
    public function invoice($id) {
        if (!$this -> hasAccess([Roles::COWORKER, Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 

        $model = TableRegistry::get('Bookings');
        $query = $model->get($id, [
            'contain' => ['Hosts', 'Coworkers']
        ]);

        // todo: security: check if user is permitted to request this invoice
        
        $this->set("row", $query);
    }
    
    public function host($year = null, $month = null) {
        if (!$this -> hasAccess([Roles::HOST])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
 
        if ($year == null || !is_numeric($year) || $year < 1990 || $year > 3000)
            $year = date("Y");

        if ($month == null || !is_numeric($month) || $month < 1 || $month > 12)
            $month = date("m");
         
        $this->set("year", $year);
        $this->set("month", $month);
  
        $user = $this -> getloggedinUser();

        $model = TableRegistry::get('Bookings');
        $query = $model->find('all')->contain(['Coworkers'])->where(['paypalipn_id IS NOT' => null, 'host_id' => $user->id]);

        $hosts = TableRegistry::get('Hosts');
        $this->set("host", $hosts->get( $user->id));

        // todo: security: check if user is permitted to request this invoice
        $this->set("rows", $query);

        
    }
    
    public function preparebookingrequest() {
        if (!$this -> hasAccess([Roles::COWORKER])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
    }
    

    /*  see: AGBs 
        $str_date1 $str_date2, e.g. '2017-02-01'
    */
    private function calculate_timespan($str_date_begin, $str_date_end) {
        $date1 = new \DateTime($str_date_begin);
        $date2 = new \DateTime($str_date_end); 
        $date2->add(new \DateInterval('P1D')); // end date is included as of definition

        $diff = $date1->diff($date2);
        //printf('%u year(s), %u month(s), %u day(s)', $diff->y, $diff->m, $diff->d);

        return ["years" => $diff->y, "months" => $diff->m, "days" => $diff->d];
    }

    /*  see: AGBs 
        $str_date1 $str_date2, e.g. '2017-02-01'
    */
    private function calculate_workingdays($host_id, $str_date_begin, $str_date_end) {

        $from = strtotime($str_date_begin);
        $to = strtotime($str_date_end);

        $hosts = TableRegistry::get('Hosts');
        $host = $hosts->get($host_id);

        $modelholidays = TableRegistry::get('Holidays');
        $holidays = $modelholidays->find('all'); // todo: auf land/zeitraum? einschränken?

        $days = 0;
        $workingdays = [];
        do {
            $test_date = mktime(date("H", $from), date("i", $from), date("s", $from), date("m", $from), date("d", $from) + $days, date("Y", $from));
            $days++;

            // 1. continue with next day if day is public holiday
            $found=false;
            foreach ($holidays as $holiday) {
                if (date("Y-m-d", $test_date) == date("Y-m-d", strtotime($holiday->date))) {
                    $found = true;
                    break;
                }
            }
            if ($found)
                // zu testendender tag ist ein feiertag, kein coworking möglich
                continue;

            // 2. add day to list of workingdays if host is open at that day, else continue with next day
            switch (date('N', $test_date)) {
                case 1: //monday
                    if ($host->open_monday_from != null && $host->open_monday_till != null) {
                        array_push($workingdays, date("Y-m-d", $test_date)); break; }
                    break;
                case 2:
                    if ($host->open_tuesday_from != null && $host->open_tuesday_till != null) {
                        array_push($workingdays, date("Y-m-d", $test_date)); break; }
                    break;
                case 3:
                    if ($host->open_wednesday_from != null && $host->open_wednesday_till != null) {
                        array_push($workingdays, date("Y-m-d", $test_date)); break; }
                    break;
                case 4:
                    if ($host->open_thursday_from != null && $host->open_thursday_till != null) {
                        array_push($workingdays, date("Y-m-d", $test_date)); break; }
                    break;
                case 5:
                    if ($host->open_friday_from != null && $host->open_friday_till != null) {
                        array_push($workingdays, date("Y-m-d", $test_date)); break; }
                    break;
                case 6:
                    if ($host->open_saturday_from != null && $host->open_saturday_till != null) {
                        array_push($workingdays, date("Y-m-d", $test_date)); break; }
                    break;
                case 7:
                    if ($host->open_sunday_from != null && $host->open_sunday_till != null) {
                        array_push($workingdays, date("Y-m-d", $test_date)); break; }
                    break;
            }

            // we assume host is open mo-fr if no opening hours set
            if ($host->open_monday_from == null && $host->open_monday_till == null &&
                $host->open_tuesday_from == null && $host->open_tuesday_till == null &&
                $host->open_wednesday_from == null && $host->open_wednesday_till == null &&
                $host->open_thursday_from == null && $host->open_thursday_till == null &&
                $host->open_friday_from == null && $host->open_friday_till == null &&
                $host->open_saturday_from == null && $host->open_saturday_till == null &&
                $host->open_sunday_from == null && $host->open_sunday_till == null)

                if (date('N', $test_date) >= 1 && date('N', $test_date) <= 5) {
                    // mo - fr
                    array_push($workingdays, date("Y-m-d", $test_date)); continue;
                }
        } while($test_date < $to);
        return $workingdays;
    }

/*
Auszug aus YD-AGBs:

preisberechnung
===============
die reihenfolge der untenstehenden regeln ist relevant. sobald eine regelbedingung erfüllt ist, wird nur diese regel (und keine andere) angewendet.

1. regel: bei buchungen > 1monat: monatspreis für anzahl der monate, letzter monat aliquot.
z.B: coworker bucht von 1.2. bis 7.3. (=1,22 monate da 1 februarmonat + 7 tage im märz), monatspreis: 309EUR
     kosten für coworker: 1,22 Monate * 309EUR = 376,98 EUR

2. regel: bei buchungen > 10 tage: kosten für coworker: 10-tagespreis * anzahl werktage / 10
z.B: 13 Tage (10er-Tagespreis: 215 EUR) kosten 13 Tage * 215EUR / 10 = 259,50EUR

3. regel: bei buchungen > 1 tag: kosten einzelticketpreis * anzahl der tage
z.B: 3 Tage ju je 25EUR kosten 3 * 25EUR = 75EUR

day of rest ("ruhetag")
=======================
definition
  1. all public holidays in austria, listed in wikipedia (https://en.wikipedia.org/wiki/List_of_holidays_by_country)
  2. all days in hosts's profile where no opening-hours are set (at the time of booking)

example: coworker books from 31.10.2017 to 7.11.2017 at host "coworkingsalzburg" (assuming opening hours on monday till saturday)
   tue 31.10.2017 = working day ("arbeitstag"), will be charged
   wed 1.11.2017 = public holiday as of wikipedia, day of rest, no charge
   thu 2.11.2017 = working day ("arbeitstag"), will be charged
   fri 3.11.2017 = working day ("arbeitstag"), will be charged
   sat 4.11.2017 = working day ("arbeitstag"), will be charged
   sun 5.11.2017 = day of rest, no charge
   mon 6.11.2017 = working day ("arbeitstag"), will be charged
   tue 7.11.2017 = working day ("arbeitstag"), will be charged
*/
    public function prepare($hostid, $begin, $end, $requestOffer=false) {
        if ($requestOffer !== false) {
            // login if user would like to book, otherwise it's just a price calculation (no login needed)
            if (!$this -> hasAccess([Roles::COWORKER])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 
        }
        $rets = [];
        
        $user = $this->getloggedinUser();

        $model = TableRegistry::get('Bookings');

        $model_hosts = TableRegistry::get('Hosts');
        $host = $model_hosts->get($hostid);

        $model_holidays = TableRegistry::get('Holidays');
        $holidays = $query = $model_holidays->find('all');

        $from = strtotime($begin);
        $to = strtotime($end);

        if ($to < $from) {
            // todo: was machen mit dem fall?
            $rets["debug_invalid date"] = "to < from, darf ned sein";

            $to = $from;
        }

        // todo: 2h-ticket (wie?)
        $total = 0;
        $timespan = $this->calculate_timespan($begin, $end);
        $rets["num_months"] = $timespan["months"];
        $rets["num_days"] = $timespan["days"];

        if ($timespan["months"] >= 6) {
            // 1st rule
            $rets["debug_rule"] = 1;
            $total = ($timespan["months"] + ($timespan["days"] / 30)) * $host -> price_6months / 6;
            $total *= 1 + $host -> servicefee;
        } elseif ($timespan["months"] >= 1 && $host -> price_1month > 0) {
            // 2nd rule
            // zieht nur wenn host einen monats-satz hinterlegt hat

            $rets["debug_rule"] = 2;
            $total = ($timespan["months"] + ($timespan["days"] / 30)) * $host -> price_1month;
            $total *= 1 + $host -> servicefee;
        } else {
            $workingdays = $this -> calculate_workingdays($hostid, $begin, $end);
            $rets["workingdays"] = $workingdays;
            $rets["num_workingdays"] = sizeof($workingdays);

            if (sizeof($workingdays) >= 10 && $host -> price_10days > 0)  {
                // 3rd rule
                $rets["debug_rule"] = 3;
                $total = sizeof($workingdays) * $host -> price_10days / 10;
                $total *= 1 + $host -> servicefee;
            } elseif (sizeof($workingdays) >= 1)  {
                // 4th rule
                $rets["debug_rule"] = 4;
                $total = sizeof($workingdays) * $host -> price_1day;
                $total *= 1 + $host -> servicefee;
            }
        }

        // https://de.wikipedia.org/wiki/Rundung
        // kaufmaennisch gerundet: stelle die wegfaellt 0,1,2,3 od 4: abrunden, sonst: aufrunden.
        $total = round($total, 2, PHP_ROUND_HALF_UP);

        
        $booking = [
            "type" => "Yellowdesk Ticket",
            "begin" => date("Y-m-d", $from),
            "end" => date("Y-m-d", $to),  
            "price" => $total,
        ];

        // todo: collision check

        $total_bookings = 0;
        
        $row = $this -> Bookings -> newEntity([]);
        if ($requestOffer !== false)
            $row -> coworker_id = $user -> id;
        $row -> payment_id = null;
        $row -> host_id = $hostid;
        $row -> description = $booking[ "type" ];
        $row -> price = $booking[ "price" ];
        $row -> servicefee_host = $booking[ "price" ] * $host -> servicefee; // 20% to YD
		
	// gueltige, nicht .at-uid nummer hinterlegt?
	$row -> vat = ($user-> vatid_successfully_checked != null && strpos(strtolower($host -> vatid), "at") === false) ? 
		0 : $row -> vat = round(($booking[ "price" ] / 100 * 20), 2, PHP_ROUND_HALF_UP);

        $row -> amount_host = $row -> price; // - $row -> servicefee_host;
	$row -> vat_host = ($host -> vatid_successfully_checked != null && strpos(strtolower($host -> vatid), "at") === false) ?
		0 : round(($row -> amount_host / 100 * 20), 2, PHP_ROUND_HALF_UP);

        $row -> begin = date("Y-m-d", strtotime($booking[ "begin" ]));
        $row -> end = date("Y-m-d", strtotime($booking[ "end" ]));

        $ret = [
            "nickname" => $host -> nickname,
            "host_id" => $host -> id,
            "title" => $host -> title,
            "price" => $row -> price,
            "vat" => $row -> vat,
            "description" => $booking[ "type" ],
            "begin" => date("Y-m-d", strtotime($booking[ "begin" ])),
            "end" => date("Y-m-d", strtotime($booking[ "end" ])),
        ];
        
        if ($requestOffer !== false) {
            $this -> Bookings -> save($row);
            $rets[$row->id] = $ret;
        }
        
        $total_bookings += $row -> price + $row -> vat;
        
        $rets["price"] = $row -> price;
        $rets["vat"] = $row -> vat;
        $rets["total"] = $total_bookings;

        if (@$_REQUEST["jsonbrowser"]) echo "<pre>";

        $this->autoRender = false;
        $this->response->type('application/json');
        $this->response->body(json_encode($rets, JSON_PRETTY_PRINT));
    }
 
}
?>
