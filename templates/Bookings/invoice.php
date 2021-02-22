<?php echo $row->coworker->companyname; ?>
<br />
<?php echo $row->coworker->firstname . " " . $row->coworker->lastname; ?>
<br />
<?php echo $row->coworker->address; ?><br />
<?php echo $row->coworker->email; ?>

<h2>Invoice</h2><a href="<?= $this->Url->build(["controller" => "bookings", "action" => "pdfinvoice", $row->id]); ?>">Download PDF</a>
<br />
<br />
<table>
    <tr>
        <th>Date</th>
        <td><?php echo date("d.m.Y", strtotime($row -> dt_inserted)); ?></td>
    </tr>
    <tr>
        <th>Host</th>
        <td><?= $row -> host -> name; ?></td>
    </tr>
</table>

<table>
    <tr>
        <th>Posititon</th>
        <th>Quantity</th>
        <th>Description</th>
        <th>Unit Cost</th>
        <th>VAT</th>
        <th>Total</th>
    </tr>
    <tr>
        <td>1</td>
        <td>1</td>
        <td><?= $row->description; ?></td>
        <td><?= money_format('%i', $row->price); ?></td>
        <td><?= money_format('%i', $row->vat); ?></td>
        <td><?php $total = $row->price + $row->vat; echo $total; ?></td>
    </tr>
</table>
