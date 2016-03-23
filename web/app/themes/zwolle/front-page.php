<?php get_header(); ?>

<?php include('_slideshow.php') ?>

<?php

	$highlight_types = array('best-practice', 'agenda');
	$highlights = array();

	foreach($highlight_types as $highlight_type){
		$args = array(
			'posts_per_page'   => 3,
			'post_type'        => $highlight_type,
		);

		if($highlight_type != 'agenda'){
			$args = array_merge($args, array(
				'posts_per_page'   => 1,
				'meta_key'         => 'on_home',
				'meta_value'       => '1',
			));
		}
		else {
			$args = array_merge($args, array(
				'meta_key' => 'date',
				'orderby' => 'meta_value',
				'order' => 'asc',
				'meta_query'   => array(
					array
					(
						'key'     => 'date',
						'value'   => ((int) date('Ymd')) - 2,
						'type'    => 'NUMERIC',
						'compare' => '>'
					)
				)
			));

//			$q = new WP_Query($args);
//			print_r('<pre>REQUEST:');print_r($q->request);print_r('</pre>');
		}

		$highlights[$highlight_type] = get_posts($args);
	}

?>
<div class="quick_content block full">

	<div class="content_block network">
		<div class="wrap">
			<h3>Op naar de 1000 banen!</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>

			<a href="" class="button medium main">Aanmelden</a>
		</div>
	</div>
	<div class="content_block desks">
		<div class="wrap">
			<h3>Wat betekent dit voor mij?</h3>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

			<a href="/feiten" class="button medium main">Bekijk</a>
		</div>
	</div>
</div>


<div class="quick_content container block full">
	<div class="content_block bestpractices">
		<h3><a href="<?php echo site_url('/regio') ?>">De regio</a></h3>
		<div class="items">
			<?php foreach($highlights['best-practice'] as $item): ?>
				<a class="item" href="<?php echo get_permalink($item->ID) ?>">
					<?php if($image): ?> <img src="<?php echo $image['image']['sizes']['medium'] ?>" /><?php endif; ?>
					<h3><?php echo get_the_title($item->ID); ?></h3>

					<span class="read-more">
						<a href="<?php echo get_permalink($item->ID) ?>">lees meer</a>
					</span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>

	<div class="content_block agenda">
		<h3><a href="<?php echo site_url('/agenda') ?>">Agenda</a></h3>
		<div class="items">
			<?php foreach($highlights['agenda'] as $item): ?>
				<div class="item">
					<a href="<?php echo get_permalink($item->ID) ?>">
						<time datetime=""><?php echo get_field('date', $item->ID); ?></time>
						<?php echo get_the_title($item->ID); ?>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="content_block toolbox">
		<img src="/app/uploads/2016/03/DEMO_Banner-Roadtour-DWWA.png" alt="">
	</div>
</div>


<?php

$args = array(
	'posts_per_page'   => 2,
	'post_type'        => 'nieuws',
);

$news = get_posts($args);

?>
<div class="news block container full archive">
	<div class="inner">
		<h2>Successen</h2>
		<div class="items">
			<?php foreach($news as $item):
				$images = get_field('caroussel', $item->ID);
				$image = count($images) > 0 ? $images[0] : null;
				?>
				<a class="item" href="<?php echo get_permalink($item->ID) ?>">
					<?php if($image): ?> <img src="<?php echo $image['image']['sizes']['medium'] ?>" /><?php endif; ?>
					<h3><?php echo get_the_title($item->ID); ?></h3>

					<span class="read-more">lees meer</span>
				</a>
			<?php endforeach; ?>
		</div>
		<div class="feed">
			<?php if(is_active_sidebar('feed')) dynamic_sidebar('feed'); ?>
		</div>
	</div>
</div>

<?php
$ticker_items = get_field('ticker');

if($ticker_items && count($ticker_items) > 0): ?>
<div class="ticker block full">

	<div class="inner">

		<?php foreach($ticker_items as $item):
			$tag = $item['url'] ? 'a' : 'span';
			?>
			<<?php echo $tag ?> href="<?php echo $item['url'] ?>"><?php echo $item['label'] ?></<?php echo $tag ?>>
		<?php endforeach; ?>
	</div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
