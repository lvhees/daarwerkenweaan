<?php
/**
 * Template Name: Archief
 */

get_header(); ?>

<div class="content block container text">

	<div class="inner">
		<h1><?php echo get_the_title() ?></h1>
		<p><?php echo apply_filters( 'the_content', $post->post_content ); ?></p>
	</div>

</div>


<div class="content block container archive">

	<div class="inner">

		<div class="items">

		<?php
		if($post->post_name == 'successen'){
			$post_type = 'nieuws';
		} elseif($post->post_name == 'cijfers'){
			$post_type = 'facts';
		} else {
			$post_type = $post->post_name;
		}

		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => -1,
		);

		if($post->post_name == 'agenda'){
			$args = array_merge($args, array(
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
			));
		}

		if($post->post_name == 'cijfers'){
			$args = array_merge($args, array(
				'posts_per_page' => 5,
			));
		}

		$posts = get_posts($args);

		// Start the loop.
		foreach($posts as $post):

			$images = get_field('caroussel', $post->ID);
			$image = count($images) > 0 ? $images[0] : null;
		?>

			<a class="item" href="<?php echo get_permalink($post->ID) ?>">
				<?php if($image): ?> <img src="<?php echo $image['image']['sizes']['medium'] ?>" alt="<?php echo get_the_title($post->ID); ?>" /><?php endif; ?>
				<h3><?php echo get_the_title($post->ID); ?></h3>
				<!--<div class="excerpt">
					<?php echo $post->post_excerpt; ?>
				</div>-->
				<span class="read-more">lees meer</span>
			</a>


		<?php endforeach; ?>
			<?php
			if($post_type == 'facts'){
				?>
				<a class="ite" href="">
					<img src="/app/uploads/2016/03/DEMO_Banner-Toolbox-DWWA.png" alt="" />
				</a>
				<?php
			}
			?>
		</div>

		<?php

		// Previous/next page navigation.
		the_posts_pagination( array(
			'prev_text'          => __( 'Previous page', 'twentyfifteen' ),
			'next_text'          => __( 'Next page', 'twentyfifteen' ),
			'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'twentyfifteen' ) . ' </span>',
		) );

		?>
	</div>

</div>

<?php get_footer(); ?>
