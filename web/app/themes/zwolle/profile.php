<?php
/**
 * Template Name: Profiel
 */

get_header();

if(is_user_logged_in()){

	$image_positioning = 'image-position-center';
	$images = array(
		array(
			'image' => array(
				'url' => get_avatar_url(get_current_user_id(), array('size' => 1920))
			)
		)
	);
}

include('_slideshow.php');

?>

<div class="content block text">

	<div class="inner">
		<h1><?php echo get_the_title() ?></h1>
		<p><?php echo apply_filters( 'the_content', $post->post_content); ?></p>

		<?php if(is_user_logged_in()): ?>
			<?php acf_form_head(); ?>
			<?php acf_form(array(
				'post_id' => 'user_' . $current_user->ID,
				'field_groups' => array('group_5602786b20c8e'),
				'submit_value' => 'Opslaan',
				'return' => false
			)); ?>
		<?php else: ?>
			Om je profiel te bewerken moet je ingelogd zijn.
		<?php endif; ?>

	</div>

</div>

<?php get_footer(); ?>
