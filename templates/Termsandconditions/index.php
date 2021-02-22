<h2>Terms and Conditions</h2>

<table>
    <tr>
        <th>Version</th>
        <th>Text</th>
        <th>Delete</th>
    </tr>
    <?php foreach ($rows as $row): ?>
    <tr>
        <td><?php echo $row->version; ?></td>
        <td><?php echo nl2br($row->tac); ?></td>
        <td><a onclick="return confirm('are you sure?')" href="<?php echo $this->Url->build(["action" => "delete", $row->id]); ?>">Delete</a></td>
    </tr>
    <?php endforeach; ?>
</table>
