<?php if (isset($margin) && $margin): ?>
<div class="subtitle"></div>
<?php endif; ?>
<div class="summary<?php echo isset($cssClass) ? " " . $cssClass : "" ?>">
	<span class="icon fa <?php echo $icon ?> <?php echo $value > 0 ? $iconColoredClass : $iconEmptyClass ?> fore-white"></span>
	<span class="value"><?php echo $value ?></span>
	<?php if (isset($url)) { echo '<a href="' . $url . '" class="fore-color-inherit">'; } ?>
	<span class="caption text-right"><?php echo $caption ?></span>
	<?php if (isset($url)) { echo '</a>'; }?>
</div>
