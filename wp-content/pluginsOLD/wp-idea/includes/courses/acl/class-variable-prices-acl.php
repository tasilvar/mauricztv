<?php

declare(strict_types=1);

namespace bpmj\wpidea\courses\acl;

use bpmj\wpidea\Courses;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\core\event\Event_Name as Product_Event_Name;
use bpmj\wpidea\events\Interface_Events;

class Variable_Prices_ACL implements Interface_Variable_Prices_ACL
{
    private const RECURRING_PAYMENTS_ENABLED = 'recurring_payments_enabled';
    private const AMOUNT = 'amount';
    private const SALE_PRICE = 'sale_price';
    private const ACCESS_TIME = 'access_time';
    private const ACCESS_TIME_UNIT = 'access_time_unit';
    private const PURCHASE_LIMIT_ITEMS_LEFT = 'bpmj_eddcm_purchase_limit_items_left';
    private const PURCHASE_LIMIT = 'bpmj_eddcm_purchase_limit';
    private const PURCHASE_LIMIT_PREV = 'bpmj_eddcm_purchase_limit_prev';
    private const RESTRICTED_TO = '_bpmj_eddpc_restricted_to';


    private Interface_Product_API $product_api;
    private Courses $courses;
    private Interface_View_Provider $view_provider;
    private Interface_Product_Repository $product_repository;
    private Interface_Translator $translator;
    private Cart_API $cart_api;
    private Interface_Actions $actions;
    private Interface_Filters $filters;
    private Interface_Events $events;

    public function __construct(
        Interface_Product_API $product_api,
        Courses $courses,
        Interface_View_Provider $view_provider,
        Interface_Product_Repository $product_repository,
        Interface_Translator $translator,
        Cart_API $cart_api,
        Interface_Actions $actions,
        Interface_Filters $filters,
        Interface_Events $events
    ) {
        $this->product_api = $product_api;
        $this->courses = $courses;
        $this->view_provider = $view_provider;
        $this->product_repository = $product_repository;
        $this->translator = $translator;
        $this->cart_api = $cart_api;
        $this->actions = $actions;
        $this->filters = $filters;
        $this->events = $events;
    }

    public function save(int $product_id, array $fields): array
    {
        $this->filters->add('edd_sanitize_amount_amount', [$this, 'fix_variable_prices_comma_separated_decimals_in_amount']);
        $this->filters->add('sanitize_post_meta_edd_variable_prices', [$this, 'sanitize_variable_prices']);

        if ($this->recurring_payments_interval_save_variable_function_exists()) {
            $this->filters->add('edd_metabox_save_edd_variable_prices', 'edd_meta_box_recurring_payments_interval_save_variable');
        }

        foreach ($fields as $field => $variable_field) {
            if ('_edd_default_price_id' === $field && edd_has_variable_prices($product_id)) {
                if (isset($variable_field)) {
                    $new_default_price_id = (!empty($variable_field) && is_numeric($variable_field)) || (0 === (int)$variable_field) ? (int)$variable_field : 1;
                } else {
                    $new_default_price_id = 1;
                }

                $this->product_repository->update_meta(new Product_ID($product_id), $field, $new_default_price_id);
            } else {
                if (!empty($variable_field)) {
                    if ('edd_variable_prices' === $field) {
                        uasort($variable_field, function ($a, $b) {
                            if (empty($a['index']) || empty($b['index'])) {
                                return 0;
                            }

                            if ('' === $a['index'] || '' === $b['index']) {
                                if ($a['index'] === $b['index']) {
                                    return 0;
                                }

                                return '' === $a['index'] ? 1 : -1;
                            }
                            $a_int = (int)$a['index'];
                            $b_int = (int)$b['index'];
                            if ($a_int === $b_int) {
                                return 0;
                            }

                            return $a_int < $b_int ? -1 : 1;
                        });

                        foreach ($variable_field as $id => $f) {
                            if (is_array($variable_field[$id])) {
                                $variable_field[$id]['changed'] = 1;
                            }
                        }

                        $new = $this->filters->apply('edd_metabox_save_' . $field, $variable_field);
                        $this->update_lessons_to_prices($product_id, $new);

                        $this->events->emit(Product_Event_Name::VARIABLE_PRICES_UPDATED, $product_id, $new);
                    } else {
                        $new = $this->filters->apply('edd_metabox_save_' . $field, $variable_field);
                    }

                    $this->product_repository->update_meta(new Product_ID($product_id), $field, $new);
                } else {
                    delete_post_meta($product_id, $field);
                }
            }
        }

        if (edd_has_variable_prices($product_id)) {
            $lowest = edd_get_lowest_price_option($product_id);
            $this->product_repository->update_meta(new Product_ID($product_id), 'edd_price', $lowest);
        }

        $price_variants = $this->product_api->get_price_variants($product_id);

        bpmj_eddcm_set_overall_purchase_limits($product_id, $price_variants->variable_prices);

        $this->do_extra_variable_prices_operations($product_id);

        $result = [
            'variable_prices_summary_html' => $this->get_variable_prices_html($price_variants->variable_prices),
            'no_variable_prices' => empty($price_variants->variable_prices),
            'message' => !empty($price_variants->variable_prices) ? $this->translator->translate('course_editor.sections.general.variable_prices.edit.table.message.success') : $this->translator->translate('course_editor.sections.general.variable_prices.edit.table.message.error')
        ];

        $this->actions->do('wpi_after_save_variable_prices');

        return $result;
    }

    public function get_variable_prices(int $post_id): string
    {
        if ('download' === $this->get_post_type($post_id)) {
            $product_id = $post_id;
            $course_id = null;
            /*
             * These functions were removed in EDD Paid Content - we need to reenable them, but only for normal products
             * and bundles
             */
            if ($this->recurring_payments_interval_variable_head_function_exists()) {
                $this->actions->add('edd_download_price_table_head', 'edd_meta_box_recurring_payments_interval_variable_head');
            }

            if ($this->recurring_payments_interval_variable_row_function_exists()) {
                $this->actions->add('edd_download_price_table_row', 'edd_meta_box_recurring_payments_interval_variable_row', 10, 2);
            }

        } else {
            $course_id = $post_id;
            $product_id = $this->courses->get_product_by_course($course_id);
        }

        $this->actions->add('edd_download_price_table_head', [$this, 'render_variable_prices_purchase_limit_head']);
        $this->actions->add('edd_download_price_table_row', [$this, 'render_variable_prices_purchase_limit_row'], 10, 3);
        $this->filters->add('edd_price_row_args', [$this, 'variable_prices_purchase_limit_row_args'], 10, 2);

        $this->filters->remove('edd_get_variable_prices', [EDD_Sale_Price()->price, 'maybe_display_variable_sale_prices']);
        $this->actions->remove('edd_download_price_table_head', [EDD_Sale_Price()->admin_product, 'add_variable_sale_price_header'], 5);
        $this->actions->remove('edd_download_price_table_row', [EDD_Sale_Price()->admin_product, 'variable_sale_price_field'], 5);

        $this->actions->add('edd_download_price_table_head', [EDD_Sale_Price()->admin_product, 'add_variable_sale_price_header'], 5);
        $this->actions->add('edd_download_price_table_row', [$this, 'variable_sale_price_field'], 5, 3);


        return $this->view_provider->get_admin('/course/edit-variable-prices', [
            'product_id' => $product_id,
            'translator' => $this->translator
        ]);
    }

    public function render_variable_prices_purchase_limit_head(): void
    {
        ?>
        <th><?= $this->translator->translate('course_editor.sections.general.variable_prices.edit.table.purchase_limit_items_left') ?></th>
        <th><?= $this->translator->translate('course_editor.sections.general.variable_prices.edit.table.purchase_limit') ?></th>
        <?php
    }

    public function render_variable_prices_purchase_limit_row(int $post_id, string $key, array $args): void
    {
        ?>
        <td><input type="number" name="edd_variable_prices[<?php
            echo $key; ?>][bpmj_eddcm_purchase_limit_items_left]"
                   value="<?php
                   echo isset($args[self::PURCHASE_LIMIT_ITEMS_LEFT]) ? esc_attr($args[self::PURCHASE_LIMIT_ITEMS_LEFT]) : ''; ?>"
                   title="<?= $this->translator->translate('course_editor.sections.general.variable_prices.edit.table.purchase_limit_items_left') ?>"
                   style="width: 50px;" min="0" oninput="validity.valid||(value='');"/>
        </td>
        <td><input type="number" name="edd_variable_prices[<?php
            echo $key; ?>][bpmj_eddcm_purchase_limit]"
                   value="<?php
                   echo isset($args[self::PURCHASE_LIMIT]) ? esc_attr($args[self::PURCHASE_LIMIT]) : ''; ?>"
                   title="<?= $this->translator->translate('course_editor.sections.general.variable_prices.edit.table.purchase_limit') ?>"
                   style="width: 50px;" min="0" oninput="validity.valid||(value='');"/>
            <input type="hidden"
                   name="edd_variable_prices[<?php
                   echo $key; ?>][bpmj_eddcm_purchase_limit_prev]"
                   value="<?php
                   echo isset($args[self::PURCHASE_LIMIT]) ? esc_attr($args[self::PURCHASE_LIMIT]) : ''; ?>"/>
        </td>
        <?php



    }

    public function variable_prices_purchase_limit_row_args(array $args, array $value): array
    {
        $args[self::PURCHASE_LIMIT] = isset($value[self::PURCHASE_LIMIT]) ? $value[self::PURCHASE_LIMIT] : '';
        $args[self::PURCHASE_LIMIT_ITEMS_LEFT] = isset($value[self::PURCHASE_LIMIT_ITEMS_LEFT]) ? $value[self::PURCHASE_LIMIT_ITEMS_LEFT] : '';

        return $args;
    }

    public function variable_sale_price_field(int $post_id, int $key, array $args): void
    {
        $defaults = array(
            'sale_price' => null,
        );
        $args = wp_parse_args($args, $defaults);

        $course = $this->courses->get_course_by_product($post_id);

        $variable_prices = $this->get_course_meta((int)$course->ID, 'variable_prices');

        if (!empty($variable_prices[$key]['tmp_sale_price'])) {
            $args['sale_price'] = $variable_prices[$key]['tmp_sale_price'];
        } else {
            if (!empty($variable_prices[$key]['sale_price'])) {
                $args['sale_price'] = $variable_prices[$key]['sale_price'];
            }
        }

        ?>
        <td><?php

        $price_args = array(
            'name' => 'edd_variable_prices[' . $key . '][sale_price]',
            'value' => !empty($args['sale_price']) ? $args['sale_price'] : '',
            'class' => 'edd-price-field edd-sale-price-field'
        );

        $currency_position = edd_get_option('currency_position');
        if (empty($currency_position) || $currency_position == 'before') :
            ?>
            <span><?php
            echo '<span>' . edd_currency_filter('') . ' ' . EDD()->html->text($price_args); ?></span><?php
        else :
            ?><span>
            <?php
            echo EDD()->html->text($price_args); ?>
            </span>
        <?php

        endif;

        ?></td><?php
    }

    public function sanitize_variable_prices($prices): array
    {
        foreach ($prices as $id => $price) {
            if (!empty($price[self::SALE_PRICE]) || $price[self::SALE_PRICE] === "0") {
                $price[self::SALE_PRICE] = number_format((float)str_replace(',', '.', preg_replace('/\s+/', '', (string)$price[self::SALE_PRICE])), 2, '.', '');
            }

            $purchase_limit = isset($price[self::PURCHASE_LIMIT]) ? (int)$price[self::PURCHASE_LIMIT] : '';
            $purchase_limit_prev = isset($price[self::PURCHASE_LIMIT_PREV]) ? (int)$price[self::PURCHASE_LIMIT_PREV] : '';
            $items_left = isset($price[self::PURCHASE_LIMIT_ITEMS_LEFT]) ? (int)$price[self::PURCHASE_LIMIT_ITEMS_LEFT] : '';
            if (empty($purchase_limit)) {
                $price[self::PURCHASE_LIMIT] = '';
                $price[self::PURCHASE_LIMIT_ITEMS_LEFT] = '';
            } else {
                $price[self::PURCHASE_LIMIT] = $purchase_limit;
                if (0 === $purchase_limit_prev && 0 === $items_left) {
                    $price[self::PURCHASE_LIMIT_ITEMS_LEFT] = $purchase_limit;
                } else {
                    $price[self::PURCHASE_LIMIT_ITEMS_LEFT] = min($items_left, $purchase_limit);
                }
            }
            $prices[$id] = $price;
        }

        return $prices;
    }

    private function update_lessons_to_prices($product_id, $new_prices): array
    {
        $price_variants = $this->product_api->get_price_variants($product_id);

        $old_prices = $price_variants->variable_prices;

        $new_price_ids = array_values(array_diff(array_keys(array_filter($new_prices)), array_keys($old_prices)));
        $product_id = (int)$product_id;
        if (!empty($new_price_ids)) {
            $course = $this->courses->get_course_by_product($product_id);
            if (!$course) {
                return $new_price_ids;
            }
            $course_id = $course->ID;
            $add_price_ids = function ($post_id) use ($new_price_ids, $product_id) {
                if (!$post_id) {
                    return;
                }

                $restricted_to = $this->get_page_meta((int)$post_id,self::RESTRICTED_TO);

                if (!is_array($restricted_to)) {
                    $restricted_to = array();
                }

                // clear 'all' price_id
                foreach ($restricted_to as $restriction_key => $restriction_rule) {
                    if ((int)$restriction_rule['download'] === $product_id && ('all' === $restriction_rule['price_id'] || in_array(
                                (int)$restriction_rule['price_id'],
                                $new_price_ids
                            ))) {
                        unset($restricted_to[$restriction_key]);
                    }
                }

                foreach ($new_price_ids as $price_id) {
                    $restricted_to[] = array(
                        'download' => $product_id,
                        'price_id' => $price_id,
                    );
                }
                $this->update_page_meta((int)$post_id, self::RESTRICTED_TO, $restricted_to);
            };

            $modules = $this->get_course_meta((int)$course_id, 'module');

            if (is_array($modules)) {
                foreach ($modules as $module) {
                    $module_id = isset($module['id']) ? $module['id'] : false;
                    $lessons = isset($module['module']) ? $module['module'] : false;
                    if (is_array($lessons)) {
                        foreach ($lessons as $lesson) {
                            $lesson_id = isset($lesson['id']) ? $lesson['id'] : false;
                            $add_price_ids($lesson_id);
                        }
                    }
                    $add_price_ids($module_id);
                }
            }
        }

        return $new_price_ids;
    }

    private function get_variable_prices_html(array $variable_prices): string
    {
        $html = '';

        foreach ($variable_prices as $price_id => $variable_price) {
            $price = !empty($variable_price[self::SALE_PRICE])
                ? $this->cart_api->get_formatted_price_with_currency(
                    $variable_price[self::SALE_PRICE]
                ) . ' / <s>' . $this->cart_api->get_formatted_price_with_currency($variable_price[self::AMOUNT]) . '</s>'
                : $this->cart_api->get_formatted_price_with_currency($variable_price[self::AMOUNT]);

            $purchase_limit_items_left = !empty($variable_price[self::PURCHASE_LIMIT_ITEMS_LEFT])
                ? $variable_price[self::PURCHASE_LIMIT_ITEMS_LEFT] : '-';

            $purchase_limit = !empty($variable_price[self::PURCHASE_LIMIT])
                ? $variable_price[self::PURCHASE_LIMIT] : '-';

            $access_time = $variable_price[self::ACCESS_TIME]
                ? $variable_price[self::ACCESS_TIME] . ' ' . $this->translator->translate(
                    'course_editor.sections.general.access_time_unit.option.' . $variable_price[self::ACCESS_TIME_UNIT]
                ) : '-';

            $recurring_payments_enabled = ($variable_price[self::RECURRING_PAYMENTS_ENABLED] ?? null)
                ? $this->translator->translate('course_editor.sections.general.variable_prices.table.body.'.self::RECURRING_PAYMENTS_ENABLED.'.yes')
                : $this->translator->translate('course_editor.sections.general.variable_prices.table.body.'.self::RECURRING_PAYMENTS_ENABLED.'.no');

            $html .= '<tr>
                           <td>' . $price_id . '</td>
                           <td>' . $variable_price['name'] . '</td>
                           <td>' . $price . '</td>
                           <td>' . $purchase_limit_items_left . ' / ' . $purchase_limit . '</td>
                           <td>' . $access_time . '</td>
                           <td>' . $recurring_payments_enabled . '</td>
                    </tr>';
        }


        if (!$html) {
            $html = '<tr><td colspan="6" style="text-align:center;">' . $this->translator->translate(
                    'course_editor.sections.general.variable_prices.table.body.set_price_variants'
                ) . '</td></tr>';
        }

        return $html;
    }

    private function do_extra_variable_prices_operations(int $product_id): void
    {
        $course = $this->courses->get_course_by_product($product_id);

        if (!$course) {
            return;
        }

        $variable_pricing = $this->get_course_meta((int)$course->ID, 'variable_pricing');
        $variable_sale_price_from_date = $this->get_course_meta((int)$course->ID, 'variable_sale_price_from_date');
        $variable_sale_price_to_date = $this->get_course_meta((int)$course->ID, 'variable_sale_price_to_date');

        $variable_prices_update = $this->get_course_meta((int)$course->ID, 'variable_prices');

        $price_variants = $this->product_api->get_price_variants($product_id);
        $variable_prices_clean = $price_variants->variable_prices;

        $changed = false;
        foreach ($variable_prices_clean as $key => $variable_price) {
            if (!empty($variable_price['changed'])) {
                unset($variable_prices_clean[$key]['changed']);

                $changed = true;
            }
        }

        if ($changed) {
            $variable_prices_update = $variable_prices_clean;
            $this->product_repository->update_meta(new Product_ID($product_id), 'edd_variable_prices', $variable_prices_clean);
        }

        if (!empty($variable_pricing)) {
            $edd_variable_prices = $variable_prices_update;
            if (!empty($variable_sale_price_from_date)) {
                foreach ($edd_variable_prices as $key => $variable_price) {
                    $edd_variable_prices[$key]['tmp_sale_price'] = $edd_variable_prices[$key]['sale_price'];
                    $edd_variable_prices[$key]['sale_price'] = '';
                }
            }

            if(empty($variable_sale_price_from_date) && empty($variable_sale_price_to_date)) {
                $this->events->emit(Product_Event_Name::VARIABLE_PRICES_UPDATED, $product_id, $edd_variable_prices);
            }

            $this->product_repository->update_meta(new Product_ID($product_id), 'edd_variable_prices', $edd_variable_prices);
        }

        $this->update_course_meta($course->ID, 'variable_prices', $variable_prices_update);
    }

    public function fix_variable_prices_comma_separated_decimals_in_amount($amount): string
    {
        return number_format(
            (float)str_replace(',', '.', preg_replace('/\s+/', '', (string)$amount)),
            2,
            '.',
            ''
        );
    }

    private function recurring_payments_interval_save_variable_function_exists(): bool
    {
        return (function_exists('edd_meta_box_recurring_payments_interval_save_variable') && !has_filter(
                'edd_metabox_save_edd_variable_prices',
                'edd_meta_box_recurring_payments_interval_save_variable'
            ));
    }

    private function recurring_payments_interval_variable_head_function_exists(): bool
    {
        return function_exists('edd_meta_box_recurring_payments_interval_variable_head');
    }

    private function recurring_payments_interval_variable_row_function_exists(): bool
    {
        return function_exists('edd_meta_box_recurring_payments_interval_variable_row');
    }

    private function get_post_type(int $id): string
    {
        return get_post_type($id);
    }

    private function get_course_meta(int $id, string $key)
    {
        return get_post_meta($id, $key, true);
    }

    private function update_course_meta(int $id, string $key, $value): void
    {
        update_post_meta($id, $key, $value);
    }

    private function get_page_meta(int $id, string $key)
    {
        return get_post_meta($id, $key, true);
    }

    private function update_page_meta(int $id, string $key, $value): void
    {
        update_post_meta($id, $key, $value);
    }
}