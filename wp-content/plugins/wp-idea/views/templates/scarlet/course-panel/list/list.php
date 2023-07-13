<?php
/** @var array $lessons */
/** @var \bpmj\wpidea\Course_Progress $progress */
/** @var int $lesson_page_id */
/** @var bool $compact_mode_on */
/** @var bool $show_title */
/** @var \bpmj\wpidea\View $view */

$active_module_id = wp_get_post_parent_id($lesson_page_id);

$mode = $compact_mode_on ? 'compact' : 'default';
$context = ($active_module_id !== 0) ? 'lesson' : 'course-panel';
?>

<div id="course-navigation-section" class="course-navigation-section course-navigation-section--mode-<?= $mode ?> course-navigation-section--context-<?= $context ?>">
<?php if($show_title): ?>
    <h3><?php _e( 'Course Content', BPMJ_EDDCM_DOMAIN ); ?></h3>
<?php endif; ?>

<div class="row">
    <?php
    $lessons_cnt = count( $lessons );
    $i        = 0;
    $parent = 0;
    if ( ! empty( $lessons ) ) {

        foreach ( $lessons as $lesson ) {
            $is_module_currently_viewed = $active_module_id === $lesson->post_parent;
            $module_class = $is_module_currently_viewed ? 'active' : 'other';
            $module_list_properties = 'data-parent-module-id="' . $lesson->post_parent . '" 
                class="module module--' . ($module_class) . '"';

            $column_split_point = (int)($lessons_cnt / 2);
            $is_first_item_after_column_split = $lessons_cnt > 3 && ($i === $column_split_point);
            $is_last_item_before_column_split = $lessons_cnt > 3 && ($i === $column_split_point - 1);

            if( $i == 0 ) {
                echo '<div class="col-sm-6 etapy_kursu">
						<div class="etap_kursu">';
            }
            elseif($is_first_item_after_column_split) {
                echo '</ul></div></div><div class="col-sm-6 etapy_kursu">
                        <div class="etap_kursu"><ul ' . $module_list_properties . '>';
            }
            if( $lesson->post_parent != $parent ) {
                if( $i != 0 ) echo '</ul>';

                echo $view::get('module-title', [
                    'module_id' => $lesson->post_parent,
                    'is_module_currently_viewed' => $is_module_currently_viewed,
                    'should_link' => $compact_mode_on,
                    'show_expand_button' => $compact_mode_on
                ]);

                echo '<ul ' . $module_list_properties . '>';
                $parent = $lesson->post_parent;
            }

            $class_active = '';
            if( $lesson_page_id == $lesson->ID ) {
                $class_active = ' active';
            }

            $decoration_line = '<div class="etap_kursu_kreska fa ' . ($is_last_item_before_column_split ? 'last-item-in-first-column' : '') . '"></div>';

            if ( $lesson->should_be_grayed_out() ) {
                echo '<li' . ( $progress->is_lesson_finished( $lesson->ID ) ? ' class="zakonczony"' : '' ) . '>' . $decoration_line . '<span>' . $lesson->post_title . '</span></li>';
            } else {
                echo '<li' . ( $progress->is_lesson_finished( $lesson->ID ) ? ' class="zakonczony' . $class_active . '"' : ' class="' . $class_active . '"' ) . '>' . $decoration_line . '<a href="' . $lesson->get_permalink() . '">' . $lesson->post_title . '</a></li>';
            }
            $i ++;
        }
        ?>
        </ul>
    <?php } ?>
</div>
</div>
</div>
</div>
