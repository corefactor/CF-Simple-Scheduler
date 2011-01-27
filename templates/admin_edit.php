<form method="post" id="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>"> 
	
	<input type="hidden" name="cfss_id" value="<?php echo $_POST['cfss_id'] ?>" />

	<ul>
		<li>
			<label for="cfss_name">Name (identifier)</label>
			<input type="text" name="cfss_name" id="cfss_name" value="<?php echo $_POST['cfss_name'] ?>" />
			<p>You will be able to add this code to a post or a page. ex: <strong>[cfss_time_slots name="identifier"]</strong></p>
		</li>
		<li>
			<label for="cfss_date">Date</label>
			<input type="text" name="cfss_date" id="cfss_date" value="<?php echo $_POST['cfss_date'] ?>" />
		</li>
		<li>
			<label for="cfss_time_start">Time the session starts</label>
			<input type="text" name="cfss_time_start" id="cfss_time_start" value="<?php echo $_POST['cfss_time_start'] ?>" />
		</li>
		<li>
			<label for="cfss_time_end">Time the session ends</label>
			<input type="text" name="cfss_time_end" id="cfss_time_end" value="<?php echo $_POST['cfss_time_end'] ?>" />
		</li>
		<li>
			<p>Optionally you can add more info</p>	
			<label for="cfss_notes">Extra notes</label>
			<textarea name="cfss_notes" ID="cfss_notes"><?php echo $_POST['cfss_notes'] ?></textarea>
		</li>		
		<li>
			<input type="submit" value="Save time slot" class="button-primary" />
		</li>
	</ul>
	
</form>