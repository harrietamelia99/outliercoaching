<?php
/**
 * Main template fallback (blog / archives). The marketing story lives in page-landing.php.
 *
 * @package Outlier_Collective
 */

get_header();
?>
<main id="main" style="max-width:40rem;margin:3rem auto;padding:0 1.25rem;">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
			<?php
		endwhile;
	else :
		?>
		<p><?php esc_html_e( 'No posts found.', 'outlier-collective' ); ?></p>
		<?php
	endif;
	?>
</main>
<?php
get_footer();
