<?php
$images = isset($images) ? $images : get_field('caroussel');
$image_positioning = isset($image_positioning) ? $image_positioning : 'image-position-top';
if($images && count($images) > 0):
	wp_enqueue_script('slick', get_stylesheet_directory_uri() . '/scripts/vendor/slick.min.js', array('jquery'));
?>
<div class="parallax">
	<div class="caroussel block full <?php echo $image_positioning ?>">
		<?php foreach($images as $image): ?>
			<div class="item" style="background-image: url('<?php echo $image['image']['url'] ?>');">
				<?php if(!empty($image['tagline1']) && !empty($image['tagline2'])): ?>
				<div class="tagline">
					<div class="flag"><?php echo $image['tagline1'] ?></div>
					<div class="flag"><?php echo $image['tagline2'] ?></div>
				</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>
