<?php
/**
 * Template part for displaying posts and pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Farm
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	if ( is_single() || is_page() ) {
		if ( has_post_thumbnail() && ! embla_get_meta( 'embla_featured_image_header' ) ) {
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

		<?php embla_posted_on(); ?>

		<div class="entry-content">
			<?php
			if ( get_page_template_slug( get_the_ID() ) === 'staff.php' ) {
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

		<?php
	} else {
		if ( has_post_thumbnail() ) {
			echo '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
			the_post_thumbnail();
			echo '</a>';
		}
		?>
		<header class="entry-header">
		<?php
		the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		?>
		</header><!-- .entry-header -->
		<?php
		if ( get_theme_mod( 'embla_show_meta' ) ) {
			embla_posted_on();
		}
		?>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div>
		<?php
	}
	?>

</article><!-- #post-## -->
