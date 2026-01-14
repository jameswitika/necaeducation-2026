<?php
include("../wp-load.php");

// Get Form Entries
$form_id = CPD_FORM_ID;
$form = GFAPI::get_form($form_id);
$entrys = GFAPI::get_entries( $form_id, array(), null, array( 'offset' => 0, 'page_size' => 100 ));

?>

<html>
<head>
	<title>Re-Process CPD Form Entry</title>
</head>
<body>
	<table border="1" cellpadding="5" cellspacing="5">
		<tr>
			<td>Date</td>
			<td>Course Scope Code</td>
			<td>Course Number</td>
			<td>Name</td>
			<td>Party ID</td>
			<td>Action</td>
		</tr>
		<?php foreach($entrys as $entry): 
			$link = 'cpd-reprocess-entry.php?entry_id=' . $entry['id'];
		?>
		<tr>
			<td><?php echo $entry['date_created']; ?></td>
			<td><?php echo $entry['22']; ?></td>
			<td><?php echo $entry['23']?></td>
			<td><?php echo $entry['9'] . ' ' . $entry['8']; ?></td>
			<td><?php echo $entry['125']?></td>
			<td><a href="<?php echo $link; ?>">REPROCESS</a>
		</tr>
		<?php endforeach; ?>
	</table>
</body>
</html>