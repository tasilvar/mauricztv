<?php

/*
 * Wszystkie filtry użyte we wtyczce
 */

// Zakoncz, jeżeli plik jest załadowany bezpośrednio
if (!defined('ABSPATH'))
    exit;


// Dodanie niestandardowych częstotliwości do harmonogramu CRON

add_filter('cron_schedules', 'bpmj_wpifirma_custom_intervals');

function bpmj_wpifirma_custom_intervals($schedules) {

    $schedules['bpmj_wpifirma_min'] = array(
        'interval' => 60,
        'display' => __('Co minutę')
    );

    return $schedules;
}


/*
 * Dodanie kolumny ze statusem i informacją
 */

add_filter( 'manage_edit-bpmj_wp_ifirma_columns', 'bpmj_wpifirma_set_columns' );
add_action( 'manage_bpmj_wp_ifirma_posts_custom_column' , 'bpmj_wpifirma_custom_column', 10, 2 );

function bpmj_wpifirma_set_columns($columns) {
    unset( $columns['author'] );
    unset( $columns['date'] );
    $columns['ifirma_status'] = __( 'Status', 'bpmj_wpifirma' );
    $columns['ifirma_note'] = __( 'Logi', 'bpmj_wpifirma' );
    $columns['ifirma_date'] = __( 'Data', 'bpmj_wpifirma' );

    return $columns;
}

function bpmj_wpifirma_custom_column( $column, $post_id ) {
    switch ( $column ) {

        case 'ifirma_status' :
            
            $status = get_post_meta($post_id, 'ifirma_status', true);
            
            echo $status;
            
            break;

        case 'ifirma_note' :
            
            $note = get_post_meta($post_id, 'ifirma_note', true);
            
            echo $note;
            
            break;
        
        case 'ifirma_date' :
            echo get_the_date( 'G:i - d.m.Y', $post_id );
            break;

    }
}

