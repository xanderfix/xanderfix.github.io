<script>
function showCategory( obj ) {
	var cat = $( obj ).val();
	if ( cat !== "" )
		window.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>?category=' + encodeURIComponent(cat.replace(/ /g, '_'));
	else
		window.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>';
}

function showProduct( obj, objcat ) {
	var product = $( obj ).val(),
		cat = $( objcat ).val();
	if ( product !== "" && cat !== "" )
		window.location.href = '<?php echo basename($_SERVER['PHP_SELF']) ?>?category=' + encodeURIComponent(cat.replace(/ /g, '_')) + '&product=' + product;
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
		<?php foreach($categories as $cat => $c): ?>
				<option value="<?php echo $c["id"] ?>"<?php echo  $c["id"] == $selectedCategory  ? " selected" : "" ?>><?php echo $c["name"] ?></option>
		<?php endforeach; ?>
			</select>
		</div>
		
		<?php if (strlen($selectedCategory)): ?>
		<div class="box box-grid margin-bottom-2">
			<label for="post"><?php echo l10n("admin_product_select") ?></label>
		</div>
		<div class="box box-grid margin-bottom-2">
			<select class="margin-left border border-mute-light background-transparent" name="post" id="post" onchange="showProduct(this, '#category')">
				<option value=""><?php echo l10n("admin_all_products") ?></option>
		<?php foreach($products as $prod): ?>
				<option value="<?php echo $prod["id"] ?>"<?php echo $prod["id"] == $selectedProduct ? " selected" : "" ?>>
					<?php
						echo $prod["label"];
					?>
				</option>
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
	<?php foreach($categories as $cat => $c): ?>
			<option value="<?php echo $c["id"] ?>"<?php echo  $c["id"] == $selectedCategory  ? " selected" : "" ?>><?php echo $c["name"] ?></option>
	<?php endforeach; ?>
		</select>
	</div>
	
	<?php if (strlen($selectedCategory)): ?>
	<div class="box margin-bottom">
		<label for="product"><?php echo l10n("admin_product_select") ?></label>
	</div>
	<div class="box margin-bottom-2">
		<select class="border border-mute-light background-transparent" name="product" id="post" onchange="showProduct(this, '#category')">
			<option value=""><?php echo "" ?></option>
			<?php foreach($products as $prod): ?>
				<option value="<?php echo $prod["id"] ?>"<?php echo $prod["id"] == $selectedProduct ? " selected" : "" ?>>
					<?php echo $prod["label"]; ?>
				</option>
		<?php endforeach; ?>
		</select>
	</div>
	<?php endif; ?>
</div>
