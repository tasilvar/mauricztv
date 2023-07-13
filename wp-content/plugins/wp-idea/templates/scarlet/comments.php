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
    $tag				 = ( 'div' == $args[ 'style' ] ) ? 'div' : 'li';
    $comment_css_classes = array( 'box' );
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
        <div>
            <div class="zdjecie">
                <?php if ( 0 != $args[ 'avatar_size' ] ) echo get_avatar( $comment, $args[ 'avatar_size' ] ); ?>
            </div>
            <div class="komentarz">
                <p class="author"><?php echo get_comment_author_link( $comment ) ?></p>
                <p class="date" datetime="<?php comment_time( 'c' ); ?>">
                    <?php printf( __( '%1$s at %2$s' ), get_comment_date( '', $comment ), get_comment_time() ); ?>
                    <?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>
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
                <?php if ( '0' == $comment->comment_approved ) : ?>
                    <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
                <?php endif; ?>
                <?php comment_text(); ?>
            </div>
        </div>
    <?php
    endif;
    ?>
    </<?php echo $tag; ?>><!-- #comment-## -->
    <?php
}
?>

<div id="tab_cont_komentarze" class="tab_cont tab_komentarze <?= ! isset($comment_args['is_new_templates_system'] ) ? 'contenter' : '' ?>" style="display: block;">
    <?php if ( have_comments() ) : ?>

        <?php the_comments_navigation(); ?>

        <ul>
            <?php
            wp_list_comments( array(
                                  'callback'		 => $comment_output_function,
                                  'end-callback'	 => function() {
                                      /*
                                       * empty callback, so we prevent displaying
                                       * the closing tag.
                                       * The tag needs to be closed in 'start' callback
                                       */
                                  },
                                  'echo'			 => true,
                                  'style'			 => 'ul',
                                  'short_ping'	 => true,
                                  'avatar_size'	 => 55,
                              ) );
            ?>
        </ul>

        <?php the_comments_navigation(); ?>

    <?php endif; // Check for have_comments().   ?>

    <?php
    // If comments are closed and there are comments, let's leave a little note, shall we?
    if ( !comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
        ?>
        <p class="no-comments"><?php _e( 'Comments are closed.', BPMJ_EDDCM_DOMAIN ); ?></p>
    <?php endif; ?>

    <div class="form-wrapper">
        <?php
        comment_form( array(
                          'title_reply'			 => _x( 'Comment', 'Do comment', BPMJ_EDDCM_DOMAIN ), //'<span>' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', BPMJ_EDDCM_DOMAIN ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</span>',
                          'title_reply_before'	 => '<h3>',
                          'title_reply_after'		 => '</h3>',
                          'comment_field'			 => '<textarea id="comment" name="comment" aria-required="true" required="required"></textarea>',
                          /** This filter is documented in wp-includes/link-template.php */
                          'comment_notes_before'	 => '',
                          'logged_in_as'			 => '',
                          'label_submit'			 => __( 'Send', BPMJ_EDDCM_DOMAIN ),
                          'submit_field'			 => '%1$s %2$s',
                          'class_submit'			 => 'dodaj_notke'
                      ) );
        ?>

    </div>
</div>

