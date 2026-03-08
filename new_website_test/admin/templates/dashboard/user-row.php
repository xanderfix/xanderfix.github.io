<div class="user text-small">
	<span class="text-semibold"><?php echo $name ?></span>
	<span class="text-light text-center"><?php echo formatDate(DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $timestamp)) ?></span>
	<span class="text-light text-right"><?php echo $ip ?></span>
</div>
