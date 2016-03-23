<?php
/**
 * Template Name: Regio
 */
get_header(); ?>

<div class="content container block text">

	<div class="inner">
		<h1><?php echo get_the_title() ?></h1>
		<p><?php echo apply_filters( 'the_content', $post->post_content ); ?></p>
	</div>
</div>

<div class="container block map">
	<iframe src="https://www.google.com/maps/d/embed?mid=zlC_6ogtUXeg.kyliDrbbPy5E" scrolling="no" style="height: 400px; width: 100%;" frameborder="0"></iframe>

</div>

<div class="container block links">
	<h2>Handige links voor professionals</h2>

	<?php

		$args = array(
			'meta_key' => 'date',
			'orderby' => 'meta_value',
			'order' => 'asc',
			'meta_query'   => array(
				array
				(
					'key'     => 'date',
					'value'   => ((int) date('Ymd')) - 7,
					'type'    => 'NUMERIC',
					'compare' => '>'
				)
			)
		);

		get_posts($args);
		echo '<ul>';
		$i = 0;
		// Start the loop.
		foreach($posts as $post):

			?>

			<li><a href="<?php echo get_field('link_url', $post->ID); ?>" target="_blank" rel="nofollow"><?php echo get_field('text', $post->ID); $i++; ?><?php echo get_the_title($post->ID); ?></a></li>
			<?php if($i == 10 || $i == 20 || $i == 30) echo '</ul><ul>'; ?>

		<?php endforeach; ?>
		<?php echo '</ul>';?>
</div>


<?php get_footer(); ?>
