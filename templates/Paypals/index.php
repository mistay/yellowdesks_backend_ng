<h2>Paypal IPNs</h2>

<table>
    <tr>
        <th>ID</th>
        <th>inserted</th>
        <th>issandbox</th>
        <th>rawrequest</th>
    </tr>
    <tr>
    <?php foreach($rows as $row) { ?>
    <tr>
        <td><?= $row["id"]; ?></td>
        <td><?= $row["ts_inserted"]; ?></td>
        <td><?= $row["issandbox"] ? "y" : "n"; ?></td>
        <td><?= $row["rawrequest"]; ?></td>
    </tr>
    <?php } ?>
</table>