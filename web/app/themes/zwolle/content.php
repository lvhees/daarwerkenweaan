<?php
$images = get_field('caroussel', $post->ID);
$image = count($images) > 0 ? $images[0] : null;
?>
<a class="item" href="<?php echo get_permalink($post->ID) ?>">
	<h3><?php echo get_the_title($post->ID); ?></h3>
	<?php if($image): ?> <img src="<?php echo $image['image']['sizes']['medium'] ?>" /><?php endif; ?>
	<div class="excerpt">
		<?php echo $post->post_excerpt; ?>
	</div>
	<span class="read-more">lees meer</span>
</a>
