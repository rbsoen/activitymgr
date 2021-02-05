<?php
	require_once "include/config.php";
	require_once "include/db.php";
	
	$cur_date = date("Y/m/d");
	$cur_date_hyphen = date("Y-m-d");

	$database = new DB($db_host,$db_name,$db_user,$db_pass);
	
	// POST new activity
	if( !empty($_POST['summary']) && !empty($_POST['description']) ){
		$database->add_activity(
			filter_var($_POST['summary'], FILTER_SANITIZE_STRING),
			filter_var($_POST['description'], FILTER_SANITIZE_STRING)
		);
	}
	
	$daylog = $database->list_activities("$cur_date_hyphen 00:00:00", "$cur_date_hyphen 23:59:59");
?>
<html>
	<head></head>
	<body>
		<h1>Activity Logger and Manager</h1>
		<!-- TODO -->
		<!--ul>
			<li><a href="#">View complete log</a></li>
			<li><a href="#">View statistics</a></li>
		</ul-->
		<h2>Activities logged today (<?php echo $cur_date;?>)</h2>
		<ul>
		<?php
		foreach ($daylog as $log){
			$time = date( 'h:i', strtotime($log['time']) );
			$subj = $log['subject'];
			$desc = $log['description'];
			echo "<li><details><summary>$time -> $subj</summary><pre><code>$desc</code></pre></details></li>";
		}
		?>
		</ul>
		<form action="" method="post">
		<h2>Log a new activity</h2>
		<fieldset>
			<label for="log_abstract">Summary:</label>
			<input type="text" name="summary" id="log_abstract">
			<br>
			<label for="log_description">Description:</label>
			<textarea name="description" id="log_description" rows="6"></textarea>
		</fieldset>
		<input type="submit" value="Add activity for today">
		<input type="hidden" name="hash" value="random hash">
		</form>
	</body>
</html>
