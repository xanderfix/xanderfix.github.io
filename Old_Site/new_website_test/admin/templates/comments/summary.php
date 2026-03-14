<div class="margin-top margin-bottom" style="line-height: 24px;">
	<span class="text-semibold margin-bottom-phone div-phone">
<?php if ($count > 0): ?>
		<span class="comments-count float-left margin-right-small"><?php echo  $count ?></span>
	    <span class="float-left"><?php echo strtoupper($count > 1 ? l10n('blog_comments') : l10n('blog_comment')) ?></span>
<?php else: ?>
        <span class="float-left"><?php echo l10n('blog_no_comment') ?></span>
<?php endif; ?>
	</span>
<?php if ($count && $hasRating): ?>
    <span class="margin-right">
        <span class="clearfix-phone div-phone"></span>
        <span class="no-phone float-left" style="margin-inline: 5px;">-</span>
        <span class="float-left margin-right-small"><?php echo l10n("blog_average_rating", "Average Vote") ?>:</span>
        <span class="float-left"><?php echo number_format($vote, 1) ?></span>
        <span class="float-left">/</span>
        <span class="float-left">5</span>
    </span>
<?php for ($s = 0; $s < 5; $s++) :?>
    <?php if ($vote >= $s + 1): ?>
    <span class="icon-phone fa icon-large fa-star fore-yellow"></span>
    <?php elseif ($vote >= $s + 0.5): ?>
    <span class="icon-phone fa icon-large fa-star-half fore-yellow"></span><span style="letter-spacing: -2px;" class="fa icon-large fa-star-half fa-flip-horizontal fore-mute-dark"></span>
    <?php else: ?>
    <span class="icon-phone fa icon-large fa-star fore-mute-dark"></span>
    <?php endif; ?>
<?php endfor; ?>
<?php endif; ?>
</div>
