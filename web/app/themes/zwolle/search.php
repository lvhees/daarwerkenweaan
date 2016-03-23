<?php get_header(); ?>


<div class="content block extended">

	<div class="inner">
		<h1>Zoekresultaten</h1>

		<?php if ( have_posts() ) : ?>

			<p>Er zijn <?php echo $wp_query->found_posts; ?> <?php echo ($wp_query->found_posts == 1 ? 'resultaat' : 'resultaten'); ?> gevonden voor de term '<?php the_search_query(); ?>'.</p>

			<hr class="divider" />

			<?php
			while ( have_posts() ) : the_post(); ?>

			<header class="entry-header">
				<?php the_title( sprintf( '<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' ); ?>
			</header>

			<div class="entry-summary">
				<?php echo (strlen(get_the_excerpt()) > 0 ? the_excerpt() : 'Er is geen beschrijving voor deze pagina beschikbaar.<br /><br />'); ?>

				<a href="<?php the_permalink(); ?>">Lees meer</a>
			</div>

			<hr class="divider" />

			<?php

			endwhile;

			echo '<br /><br />';

			the_posts_pagination( array(
				'screen_reader_text' => ' ',
				'prev_text'          => '<i class="fa fa-angle-left"></i>',
				'next_text'          => '<i class="fa fa-angle-right"></i>',
			) );

			echo '<br /><br />';

		else :

			?>

				<p>Er zijn geen zoekresultaten gevonden voor deze zoekterm.</p>

			<?php

		endif;
		?>

	</div>
</div>

<?php get_footer(); ?>
