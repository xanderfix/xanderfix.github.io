<div class="comment text-small">
	<?php if (isset($url)) { echo '<a href="' . $url . '" class="fore-color-inherit">'; } ?>
		<div class="row-1">
			<span class="text-semibold w-perc-32"><?php echo $name ?></span>
			<span class="w-perc-40"><?php echo $title ?></span>
			<span class="text-light text-right w-perc-24"><?php echo formatDate(DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $timestamp)) ?></span>
		</div>
		<div class="row-2"><?php echo $body ?></div>
	<?php if (isset($url)) { echo '</a>'; }?>
</div>
