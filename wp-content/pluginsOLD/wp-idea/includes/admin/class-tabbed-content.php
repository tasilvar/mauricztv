<?php

/**
 *
 * The class responsible for edit course page
 *
 */

// Exit if accessed directly
namespace bpmj\wpidea\admin;

use WP_Post;

if (!defined('ABSPATH')) {
    exit;
}

class Tabbed_Content
{
    protected $sections = array();

    /**
     * @param string $name
     *
     * @return bool|string
     */
    protected function calculate_id($name)
    {
        return substr(sha1($name), 0, 8);
    }

    /**
     * @param string $name
     * @param callable $callback
     * @param string $icon
     * @param string $id
     *
     * @return $this
     */
    public function add_section($name, $callback, $icon = '', $id = '')
    {
        if (!$id) {
            $id = $this->calculate_id($name);
        }
        $this->sections[$id] = array(
            'name' => $name,
            'callback' => $callback,
            'icon' => $icon,
        );

        return $this;
    }

    /**
     * @param $id_or_name
     *
     * @return $this
     */
    public function remove_section($id_or_name)
    {
        if (!isset($this->sections[$id_or_name])) {
            $id_or_name = $this->calculate_id($id_or_name);
        }

        unset($this->sections[$id_or_name]);

        return $this;
    }

    /**
     * @param WP_Post $post
     * @param string $box
     */
    public function render($post, $box)
    {
        ?>
        <div class="cs-framework cs-metabox-framework">
            <div class="cs-body">
                <div class="cs-nav">
                    <ul>
                        <?php
                        $first_section = true;
                        foreach ($this->sections as $section_id => $section):
                            ?>
                            <li>
                                <a class="<?php echo $first_section ? 'cs-section-active' : ''; ?>"
                                   href="#" data-section="bpmj-eddcm-<?php echo $section_id; ?>-tab">
                                    <?php if (!empty($section['icon'])): ?>
                                        <span class="dashicons <?php echo $section['icon']; ?>"
                                              title="<?php echo esc_attr($section['name']); ?>"></span>
                                    <?php endif; ?>
                                    <span class="title">
									    <?php echo esc_html($section['name']); ?>
                                    </span>
                                </a>
                            </li>
                            <?php
                            if ($first_section) {
                                $first_section = false;
                            }
                        endforeach;
                        ?>
                    </ul>
                </div>
                <div class="cs-content">
                    <div class="cs-sections">
                        <?php
                        $first_section = true;
                        foreach ($this->sections as $section_id => $section):
                            ?>
                            <div id="cs-tab-bpmj-eddcm-<?php echo $section_id; ?>-tab" class="cs-section"
                                 style="<?php echo $first_section ? 'display: block;' : ''; ?>">
                                <div class="bpmj-eddcm-cs-section-body">
                                    <div class="heading inverted narrow-margin">
                                        <?php if (!empty($section['icon'])): ?>
                                            <span class="dashicons <?php echo $section['icon']; ?>"></span>
                                        <?php endif; ?>
                                        <?php echo esc_html($section['name']); ?>
                                    </div>
                                    <?php call_user_func($section['callback'], $post, $box, $section); ?>
                                </div>
                            </div>
                            <?php
                            if ($first_section) {
                                $first_section = false;
                            }
                        endforeach;
                        ?>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="cs-nav-background"></div>
                <div class="clear"></div>
            </div>
        </div>
        <?php
    }
}