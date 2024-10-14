<?php
/**
 * The template for displaying comments
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

$comment_output_function = function ($comment, $args, $depth) {
	/* @var $comment WP_Comment */
	$tag			 = ( 'div' == $args[ 'style' ] ) ? 'div' : 'li';
	$comment_css_classes	 = array( 'box' );
	if ( $depth > 1 ) {
		$comment_css_classes[] = 'reply-' . min( array( $depth - 1, 3 ) );
	}
	if ( !$comment->get_children() ) {
		$comment_css_classes[] = 'no-children';
	}
	?>
	<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( $comment_css_classes, $comment ); ?>>
	<?php
	if ( ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) && $args[ 'short_ping' ] ) :
		?>
		<div class="content">
			<?php _e( 'Pingback:' ); ?> <?php comment_author_link( $comment ); ?> <?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>
		</div>
		<?php
	else:
		?>
		<div class="left">
			<div class="avatar">
				<?php if ( 0 != $args[ 'avatar_size' ] ) echo get_avatar( $comment, $args[ 'avatar_size' ] ); ?>
			</div>
			<div class="meta">
				<p class="author"><?php echo get_comment_author_link( $comment ) ?></p>
				<p class="date" datetime="<?php comment_time( 'c' ); ?>">
					<?php printf( __( '%1$s at %2$s' ), get_comment_date( '', $comment ), get_comment_time() ); ?>
					<?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>
				</p>
			</div>
		</div>
		<div class="right">
			<p class="screen">
				<?php
				comment_reply_link( array_merge( $args, array(
				    'add_below'	 => 'div-comment',
				    'depth'		 => $depth,
				    'max_depth'	 => $args[ 'max_depth' ],
				    'before'	 => '',
				    'after'		 => ''
				) ) );
				?>
			</p>
			<p class="media"><a href="#"><span class="arrow">&rsaquo;</span></a></p>
		</div>
		<div id="div-comment-<?php comment_ID(); ?>" class="content">
			<?php if ( '0' == $comment->comment_approved ) : ?>
				<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
			<?php endif; ?>
			<?php comment_text(); ?>
		</div>
	<?php
	endif;
	?>
	</<?php echo $tag; ?>><!-- #comment-## -->
	<?php
}
?>
<section class="comments bg">
	<div class="wrapper">
		<?php if ( have_comments() ) : ?>
			<h2 class="bg center">
				<?php
				$comments_number = get_comments_number();
				if ( 1 === $comments_number ) {
					/* translators: %s: post title */
					printf( _x( 'One thought on &ldquo;%s&rdquo;', 'comments title', BPMJ_EDDCM_DOMAIN ), get_the_title() );
				} else {
					printf(
						/* translators: 1: number of comments, 2: post title */
						_nx(
							'%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $comments_number, 'comments title', BPMJ_EDDCM_DOMAIN
						), number_format_i18n( $comments_number ), get_the_title()
					);
				}
				?>
			</h2>

			<?php the_comments_navigation(); ?>

			<?php
			wp_list_comments( array(
			    'callback'	 => $comment_output_function,
			    'end-callback'	 => function() {
				    /*
				     * empty callback, so we prevent displaying
				     * the closing tag.
				     * The tag needs to be closed in 'start' callback
				     */
			    },
			    'echo'		 => true,
			    'style'		 => 'div',
			    'short_ping'	 => true,
			    'avatar_size'	 => 55,
			) );
			?>

			<?php the_comments_navigation(); ?>

		<?php else: ?>
			<h2 class="bg center"><?php _e( 'Comments', BPMJ_EDDCM_DOMAIN ); ?></h2>
		<?php endif; // Check for have_comments().   ?>

		<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( !comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
			?>
			<p class="no-comments"><?php _e( 'Comments are closed.', BPMJ_EDDCM_DOMAIN ); ?></p>
		<?php endif; ?>

		<div class="form-wrapper">
			<?php
			add_action( 'comment_form_after', function () {
				echo '<p>* ' . __( 'required fields', BPMJ_EDDCM_DOMAIN ) . '</p>';
			} );
			comment_form( array(
			    'title_reply'		 => _x( 'Comment', 'Do comment', BPMJ_EDDCM_DOMAIN ) . ':',
			    'title_reply_before'	 => '<h3>',
			    'title_reply_after'	 => '</h3>',
			    'comment_field'		 => '<textarea placeholder="' . __( 'Comment', BPMJ_EDDCM_DOMAIN ) . ' *" id="comment" name="comment" aria-required="true" required="required"></textarea>',
			    /** This filter is documented in wp-includes/link-template.php */
			    'comment_notes_before'	 => '',
			    'label_submit'		 => __( 'Send', BPMJ_EDDCM_DOMAIN ),
			    'submit_field'		 => '%1$s %2$s',
			) );
			?>

                </div>
	</div>
</section>
