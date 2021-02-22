<h2>Configs</h2>

<a href="<?php echo $this->Url->build(["action" => "cru"]); ?>">Add</a>

<table>
    <tr>
        <th>Configkey</th>
        <th>Configvalue</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    <?php foreach ($rows as $row): ?>
    <tr>
        <td><?= $row->configkey ?></td>
        <td><?= nl2br($row->configvalue) ?></td>
        <td><a href="<?php echo $this->Url->build(["action" => "cru", $row->id]); ?>">Edit</a></td>
        <td><a onclick="return confirm('are you sure?')" href="<?php echo $this->Url->build(["action" => "delete", $row->id]); ?>">Delete</a></td>
    </tr>
    <?php endforeach; ?>
</table>