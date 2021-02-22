<table>
    <tr>
        <th>inserted</th>
        <th>actualrecipients</th>
        <th>originalrecipients</th>
        <th>sender</th>
        <th>subject</th>
        <th>message</th>
    </tr>
    <tr>
    <?php foreach($rows as $row) { ?>
    <tr>
        <td><?= $row["ts_inserted"]; ?></td>
        <td><?= $row["actualrecipients"]; ?></td>
        <td><?= $row["originalrecipients"]; ?></td>
        <td><?= $row["sender"]; ?></td>
        <td><?= $row["subject"]; ?></td>
        <td><?= $row["message"]; ?></td>
    </tr>
    <?php } ?>
</table>