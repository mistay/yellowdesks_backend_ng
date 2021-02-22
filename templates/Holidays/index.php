<h2>Holidays</h2>

<table>
    <tr>
        <th>Countrycode</th>
        <th>Date</th>
        <th>Name</th>
        <th>Comment</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    <?php foreach ($rows as $row): ?>
    <tr>
        <td><?php echo $row->countrycode ?></td>
        <td><?php echo date("d.m.Y", strtotime($row->date)); ?></td>
        <td><?php echo $row->name; ?></td>
        <td><?php echo $row->comment; ?></td>
        
        <td><a href="">Edit</a></td>
        <td><a href="">Delete</a></td>
    </tr>
    <?php endforeach; ?>
</table>