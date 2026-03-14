<script>
function showCategory( obj ) {
	var cat = $( obj ).val();
	if ( cat !== "" )
		window.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>?category=' + encodeURIComponent(cat.replace(/ /g, '_'));
	else
		window.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>';
}

function showPost( obj, objcat ) {
	var post = $( obj ).val(),
		cat = $( objcat ).val();
	if ( post !== "" && cat !== "" )
		window.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>?category=' + encodeURIComponent(cat.replace(/ /g, '_')) + '&post=' + post;
	else if ( cat !== "" )
		window.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>?category=' + encodeURIComponent(cat.replace(/ /g, '_'));
	else
		window.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>';	
}
</script>
<!-- Desktop Row -->
<div class="no-phone">
	<div class="select-container-grid">
		<div class="box box-grid margin-bottom">
			<label for="category"><?php echo l10n("admin_category_select") ?></label>
		</div>
		<div class="box box-grid margin-bottom">
			<select class="margin-left border border-mute-light background-transparent" name="category" id="category" onchange="showCategory(this)">
				<option value=""><?php echo l10n("admin_all_category") ?></option>
		<?php foreach($categories as $category => $post): ?>
				<option value="<?php echo $category ?>"<?php echo str_replace(" ", "_", $category) == $selectedCategory ? " selected" : "" ?>><?php echo str_replace("_", " ", $category) ?></option>
		<?php endforeach; ?>
			</select>
		</div>
		
		<?php if (strlen($selectedCategory)): ?>
		<div class="box box-grid margin-bottom-2">
			<label for="post"><?php echo l10n("admin_post_select") ?></label>
		</div>
		<div class="box box-grid margin-bottom-2">
			<select class="margin-left border border-mute-light background-transparent" name="post" id="post" onchange="showPost(this, '#category')">
				<option value=""><?php echo l10n("admin_all_articles") ?></option>
		<?php foreach($categoryPosts as $post): ?>
				<option value="<?php echo $post ?>"<?php echo $post == $selectedPost ? " selected" : "" ?>><?php echo $posts[$post]['title'] ?></option>
		<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>
	</div>
</div>

<!-- Phone Row -->
<div class="no-tablet no-desktop">
	<div class="box margin-bottom">
		<label for="category"><?php echo l10n("admin_category_select") ?></label>
	</div>
	<div class="box margin-bottom">
		<select class="border border-mute-light background-transparent" name="category" id="category" onchange="showCategory(this)">
			<option value=""><?php echo l10n("admin_all_category") ?></option>
	<?php foreach($categories as $category => $post): ?>
			<option value="<?php echo $category ?>"<?php echo str_replace(" ", "_", $category) == $selectedCategory ? " selected" : "" ?>><?php echo str_replace("_", " ", $category) ?></option>
	<?php endforeach; ?>
		</select>
	</div>
	
	<?php if (strlen($selectedCategory)): ?>
	<div class="box margin-bottom">
		<label for="post"><?php echo l10n("admin_post_select") ?></label>
	</div>
	<div class="box margin-bottom-2">
		<select class="border border-mute-light background-transparent" name="post" id="post" onchange="showPost(this, '#category')">
			<option value=""><?php echo "" ?></option>
	<?php foreach($categoryPosts as $post): ?>
			<option value="<?php echo $post ?>"<?php echo $post == $selectedPost ? " selected" : "" ?>><?php echo $posts[$post]['title'] ?></option>
	<?php endforeach; ?>
		</select>
	</div>
	<?php endif; ?>
</div>
