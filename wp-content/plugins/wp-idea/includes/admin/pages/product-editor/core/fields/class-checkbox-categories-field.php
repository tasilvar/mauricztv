<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;

class Checkbox_Categories_Field extends Abstract_Setting_Field
{
    private array $categories_list = [];

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null
    ) {
        parent::__construct($name, $label, $description, $tooltip, $additional_settings_fields = null, $value = null);
    }

    public function set_categories_list(array $categories_list): self
    {
        $this->categories_list = $categories_list;

        return $this;
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }
        return $this->get_field_wrapper_start()
             .  $this->display_category_input()
             . $this->get_field_wrapper_end();
    }

    private function display_category_input($i = 0)
    {
        ob_start();

        foreach ($this->categories_list as $category) :
            if ($i !== $category->parent) {
                continue;
            }

            $checked = is_array($this->get_value()) && ! empty(array_filter($this->get_value(), function ($cat) use ($category) {
                return $cat->slug === $category->slug;
            })) ? 'checked="checked"' : '';
            ?>

            <div style="<?php echo 0 === $i ? '' : 'padding-left: 20px;'; ?> width:100%; display:block;">
                <label for="<?php echo $this->get_name() . '_' . $category->cat_ID; ?>">
                    <input
                        type="checkbox" value="<?php echo $category->cat_ID; ?>"
                        name="<?php echo $this->get_name() . '[]'; ?>"
                        class="single-field checkbox-double"
                        style="margin-bottom:9px;"
                        id="<?php echo $this->get_name() . '_' . $category->cat_ID; ?>"
                        <?php echo $checked; ?>
                    />
                    <?php echo $category->name; ?>
                </label>
                <div>
                    <?php echo $this->display_category_input($category->cat_ID); ?>
                </div>
            </div>

        <?php
        endforeach;

        return ob_get_clean();
    }
}