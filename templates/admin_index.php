<h3>Time Slots</h3>

<table class="widefat">
<thead>
    <tr>
        <th>Name</th>
        <th>Ocurrs</th>
		<th>Time Start</th>
		<th>Time End</th>
		<th>Notes</th>
		<th>Actions</th>
    </tr>
</thead>
<tfoot>
    <tr>
        <th>Name</th>
        <th>Ocurrs</th>
		<th>Time Start</th>
		<th>Time End</th>
		<th>Notes</th>
		<th>Actions</th>
    </tr>
</tfoot>
<tbody>
	<?php foreach($timeslots as $slot): ?>
		<tr>
			<td><?php echo $slot->name ?></td>
			<td><?php echo empty($slot->week_day) ? $slot->date : $slot->week_day ; ?></td>
			<td><?php echo date('H:i', strtotime($slot->time_start)); ?></td>
			<td><?php echo date('H:i', strtotime($slot->time_end)); ?></td>
			<td><?php echo $slot->notes ?></td>
			<td>
				<a href="admin.php?page=cf_simple_schedule_edit&id=<?php echo $slot->id ?>">Edit</a> | 
				<a href="admin.php?page=cf_simple_schedule_index&action=delete&id=<?php echo $slot->id ?>">Delete</a>
			</td>
		</tr>
	<?php endforeach; ?>
</tbody>
</table>