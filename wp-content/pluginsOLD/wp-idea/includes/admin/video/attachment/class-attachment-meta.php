<?php

namespace bpmj\wpidea\admin\video\attachment;

/**
 * Attachment Meta
 */

class Attachment_Meta
{
    //Holds Media Fields
    private $media_fields = [];

    //Default arguments.
    private static $default_item_args = [
        'application' => 'video', //add to video attachments only
        'exclusions'  => [],
        'input'       => 'readonly'
    ];

    /**
     * Factory method
     * 
     * @param array $args
     */
    public static function create( $args ){
    	new Attachment_Meta( $args );
    }

    /**
     * @param array $args
     */
    function __construct( $args )
    {
        $this->media_fields = $args;

        add_filter( 'attachment_fields_to_edit', [ $this, 'render' ], 9, 2 );
    }

    /**
     * Method responsible for generating fields in the Media Editor.
     *
     * @param $form_fields
     * @param $post
     * @return mixed
     */
    public function render($form_fields, $post = null)
    {
        foreach ( $this->media_fields as $field => $values )
        {
            $values = wp_parse_args( $values, self::$default_item_args );

            // If the field matches the current attachment mime type
            // and is not one of the exclusions
            if(
                false !== strpos( $post->post_mime_type, $values['application'] )  &&
                ! in_array( $post->post_mime_type, $values['exclusions'] ) )
            {
                $meta = get_post_meta( $post->ID, "_" . $field, true );

                switch ($values['input'])
                {
                    case 'readonly':
                        $values['input'] = "html";
                        $html = '<input type="text" value="' . $meta . '" name="attachments[' . $post->ID . '][' . $field . ']" id="attachments-' . $post->ID . '-' . $field . '" readonly />';
                        $values['html'] = $html;
                        break;

                    default:
                        break;
                }

                // And set it to the field before building it
                $values['value'] = $meta;

                // We add our field into the $form_fields array
                $form_fields[$field] = $values;
            }
        }

        // We return the completed $form_fields array
        return $form_fields;
    }
}