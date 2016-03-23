<?php  get_header(); ?>

<?php include('_slideshow.php') ?>

<div class="content block text">

	<div class="inner">
		<h1><?php echo get_the_title() ?></h1>
		<p><?php echo apply_filters( 'the_content', $post->post_content ); ?></p>
	</div>

</div>

<?php get_footer(); ?>
