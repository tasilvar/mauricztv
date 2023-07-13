<?php
namespace bpmj\wpidea;

class Metabox
{
    const METABOXES_ACTION = 'bpmj_metabox_save';

    private $name;

    private $title;

    private $screen;

    private $context;

    private $elements = [];

    private $inputs = [];
    
    public function __construct($name, $title, $screen = 'page', $context = 'side') {
        $this->name = $name;
        $this->title = $title;
        $this->screen = $screen;
        $this->context = $context;
    }

    /**
     * @param array $options Should be in format: $options = [ ['value' => 'option_value', 'label' => 'option_label'] ]
     */
    public function add_select(string $name, string $label = '', array $options = [], bool $allow_empty = true): self
    {
        $this->inputs[] = $name;
        $select = '';

        $select .= "<label class='bpmj_wpi_select_label' for={$name}>{$label}</label>";
        $select .= "<select id={$name} name={$name}>";
        
        if($allow_empty) $select .= "<option value='0'>" . __( 'No option selected', BPMJ_EDDCM_DOMAIN ) . "</option>";

        foreach ($options as $key => $option) {
            $selected = !empty($_GET['post']) ? Post_Meta::get($_GET['post'], $name) == $option['value'] ? 'selected=true' : '' : '';
            
            $select .= "<option value={$option['value']} {$selected}>{$option['label']}</option>";
        }
        $select .= "</select>";

        $this->elements[] = $select;

        return $this;
    }

    public function render(): void
    {
		add_action('add_meta_boxes', function()
        {   
            add_meta_box($this->name, $this->title, function()
            {
                echo $this->get_nonce();
                echo $this->get_content();
            }, $this->screen, $this->context, 'default' );
        });
    
        add_action( 'save_post', array( $this, 'save_metabox_inputs' ), 10, 2 );
    }

    public function save_metabox_inputs($post_id, $post): void
    {
        if(empty($_POST)) return;

        if (!current_user_can('edit_post', $post_id)) return;
        
        foreach ($this->inputs as $key => $name) {
            if ( isset( $_POST[$name] ) ) {
                check_admin_referer(self::METABOXES_ACTION, $this->name); //die() on fail

                Post_Meta::set($post_id, $name, $_POST[$name]);
            }
        }
    }

    private function get_content(): string
    {
        $content = '';

        foreach ($this->elements as $key => $element) {
            $content .= $element;
        }

        return $content;
    }

    private function get_nonce(): string
    {
        return wp_nonce_field( self::METABOXES_ACTION, $this->name, $referrer = true, $echo = false );
    }
}