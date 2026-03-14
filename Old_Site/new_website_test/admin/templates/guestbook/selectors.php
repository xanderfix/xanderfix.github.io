<script>
function showGb( obj ) {
	var val = $( obj ).val();
	if (val !== "")
		window.location.href = "guestbook.php?id=" + val;
	else
		window.location.href = "guestbook.php";
}
</script>
<div class="margin-bottom-2">
	<label for="category"><?php echo l10n("admin_guestbook_select") ?></label>
	<select class="margin-left border border-mute-light background-transparent" name="category" id="category" onchange="showGb(this)">
	<option value=""><?php echo l10n("admin_all_objects") ?></option>
<?php foreach($guestbooks as $gbid => $gb): ?>
	<option value="<?php echo $gbid?>"<?php echo ($gbid == $id ? " selected" : "") ?>><?php echo l10n("cmn_subject") . " " . $gb['objectnumber'] . " - " . $gb['pagetitle']?></option>
<?php endforeach; ?>
	</select>
</div>
