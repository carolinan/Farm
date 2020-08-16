<?php
/**
 * Template Name: Staff
 * Description: A Page Template that displays your selected number of users.
 *
 * @package Farm
 */

get_header(); ?>
<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail();
			}
			?>
			<header class="entry-header">
			<?php
			if ( is_front_page() ) {
				the_title( '<h2 class="entry-title">', '</h2>' );
			} else {
				the_title( '<h1 class="entry-title">', '</h1>' );
			}
			?>
			</header><!-- .entry-header -->
			<?php if ( function_exists( 'jetpack_breadcrumbs' ) ) { ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Breadcrumb Navigation', 'farm' ); ?></span>
				<div class="breadcrumb-area"><?php jetpack_breadcrumbs(); ?></div><!-- .breadcrumb-area -->
			<?php } ?>
			<div class="entry-content">
				<?php
				for ( $i = 1; $i < 9; $i++ ) {
					$staffmember = get_user_by( 'ID', get_theme_mod( 'farm_staff_member' . $i ) );
					if ( ! empty( $staffmember ) ) {
						echo '<div class="staff-member">';
						echo get_avatar( $staffmember->user_email ) . '<br>';
						if ( count_user_posts( $staffmember->ID ) ) {
							echo '<a href="' . esc_url( get_author_posts_url( $staffmember->ID ) ) . '">' . $staffmember->display_name . '</a>';
						} elseif ( $staffmember->user_url ) {
								echo '<a href="' . esc_url( $staffmember->user_url ) . '">' . $staffmember->display_name . '</a>';
						} else {
							echo $staffmember->display_name;
						}
						echo '<br>';
						echo '<span class="staff-description">' . get_user_meta( $staffmember->ID, 'description', true ) . '</span>';
						echo '</div>';
					}
				}

				the_content();
				wp_link_pages(
					array(
						'before'      => '<div class="page-links">' . __( 'Pages:', 'farm' ),
						'after'       => '</div>',
						'link_before' => '<span class="page-number">',
						'link_after'  => '</span>',
					)
				);

				?>
			</div><!-- .entry-content -->
			<footer class="entry-footer">
				<?php embla_entry_footer(); ?>
			</footer><!-- .entry-footer -->
			</article><!-- #post-## -->
			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		endwhile; // End of the loop.
	?>
</main><!-- #main -->
<?php
get_footer();
