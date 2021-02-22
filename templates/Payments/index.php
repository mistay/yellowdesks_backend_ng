<h2>Payments</h2>
Payments from Yellowdesks to Hosts

<?php
// toto: globalize me

setlocale(LC_MONETARY, 'de_DE');

?>


<table>
    <tr>
        <th>Date</th>
        <th>Bankaccount</th>
        <th>Amount</th>
    </tr>
    <?php foreach ($rows as $row): ?>
    <tr>
        <td><?php echo date("d.m.Y H:i", strtotime($row->dt_inserted)); ?></td>
        <td><?php echo $row->bankaccount->iban; ?></td>
        
        <td><?php echo money_format("%i", $row->amount); ?></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="2"><?php foreach($row->bookings as $booking) {
            echo($booking->dt_inserted) ." " . $booking->coworker->companyname . " " . $booking->coworker->firstname . " " . $booking->coworker->lastname . "<br />"; 
        }
        ?></td>
    </tr>
    <?php endforeach; ?>
</table>