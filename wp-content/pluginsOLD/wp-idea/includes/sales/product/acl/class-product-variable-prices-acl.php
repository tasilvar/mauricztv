<?php

namespace bpmj\wpidea\sales\product\acl;

use bpmj\wpidea\Courses;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\modules\cart\api\Cart_API;
use bpmj\wpidea\sales\product\api\Interface_Product_API;
use bpmj\wpidea\sales\product\model\Product_ID;
use bpmj\wpidea\sales\product\repository\Interface_Product_Repository;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;
use bpmj\wpidea\sales\price_history\core\model\Price_History;
use bpmj\wpidea\sales\product\core\event\Event_Name;
use bpmj\wpidea\events\Interface_Events;

class Product_Variable_Prices_ACL implements Interface_Product_Variable_Prices_ACL
{
    private const RECURRING_PAYMENTS_ENABLED = 'recurring_payments_enabled';
    private const AMOUNT = 'amount';
    private const SALE_PRICE = 'sale_price';
    private const PURCHASE_LIMIT_ITEMS_LEFT = 'bpmj_eddcm_purchase_limit_items_left';
    private const PURCHASE_LIMIT = 'bpmj_eddcm_purchase_limit';
    private const PURCHASE_LIMIT_PREV = 'bpmj_eddcm_purchase_limit_prev';
    private const PRODUCTS_EDITOR_EDIT_VARIABLE_PRICES = '/products/editor/edit-variable-prices';

    private Interface_Product_API $product_api;
    private Interface_View_Provider $view_provider;
    private Interface_Product_Repository $product_repository;
    private Interface_Translator $translator;
    private Cart_API $cart_api;
    private Interface_Actions $actions;
    private Interface_Filters $filters;
    private Interface_Events $events;

    public function __construct(
        Interface_Product_API $product_api,
        Interface_View_Provider $view_provider,
        Interface_Product_Repository $product_repository,
        Interface_Translator $translator,
        Cart_API $cart_api,
        Interface_Actions $actions,
        Interface_Filters $filters,
        Interface_Events $events
    ) {
        $this->product_api = $product_api;
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
                $new_default_price_id = 1;

                if (isset($variable_field)) {
                    $new_default_price_id = (!empty($variable_field) && is_numeric($variable_field)) || (0 === (int)$variable_field) ? (int)$variable_field : 1;
                }

                $this->product_repository->update_meta(new Product_ID($product_id), $field, $new_default_price_id);
            } else {
                if (!empty($variable_field)) {
                    if ('edd_variable_prices' === $field) {
                        $this->sort_variable_field($variable_field);
                        $variable_field = $this->set_changed_in_variable_field($variable_field);
                    }

                    $new = $this->filters->apply('edd_metabox_save_' . $field, $variable_field);

                    $this->events->emit(Event_Name::VARIABLE_PRICES_UPDATED, $product_id, $new);

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

        $result = [
            'variable_prices_summary_html' => $this->get_variable_prices_html($price_variants->variable_prices),
            'no_variable_prices' => empty($price_variants->variable_prices),
            'message' => !empty($price_variants->variable_prices) ? $this->translator->translate('product_editor.sections.general.variable_prices.edit.table.message.success') : $this->translator->translate('product_editor.sections.general.variable_prices.edit.table.message.error')
        ];

        $this->actions->do('wpi_after_save_variable_prices');

        return $result;
    }

    public function get_variable_prices(int $post_id): string
    {
        $this->actions->add('edd_download_price_table_head', [$this, 'render_recurring_payments_interval_variable_head']);
        $this->actions->add('edd_download_price_table_row', [$this, 'render_recurring_payments_interval_variable_row']);

        $this->actions->add('edd_download_price_table_head', [$this, 'render_variable_prices_purchase_limit_head']);
        $this->actions->add('edd_download_price_table_row', [$this, 'render_variable_prices_purchase_limit_row'], 10, 3);
        $this->filters->add('edd_price_row_args', [$this, 'variable_prices_purchase_limit_row_args'], 10, 2);

        $this->filters->remove('edd_get_variable_prices', [EDD_Sale_Price()->price, 'maybe_display_variable_sale_prices']);
        $this->actions->remove('edd_download_price_table_head', [EDD_Sale_Price()->admin_product, 'add_variable_sale_price_header'], 5);
        $this->actions->remove('edd_download_price_table_row', [EDD_Sale_Price()->admin_product, 'variable_sale_price_field'], 5);

        $this->actions->add('edd_download_price_table_head', [EDD_Sale_Price()->admin_product, 'add_variable_sale_price_header'], 5);
        $this->actions->add('edd_download_price_table_row', [$this, 'variable_sale_price_field'], 5, 3);


        return $this->view_provider->get_admin(self::PRODUCTS_EDITOR_EDIT_VARIABLE_PRICES, [
            'product_id' => $post_id,
            'translator' => $this->translator
        ]);
    }

    public function render_variable_prices_purchase_limit_head(): void
    {
        ?>
        <th><?= $this->translator->translate('product_editor.sections.general.variable_prices.edit.table.purchase_limit_items_left') ?></th>
        <th><?= $this->translator->translate('product_editor.sections.general.variable_prices.edit.table.purchase_limit') ?></th>
        <?php
    }

    public function render_variable_prices_purchase_limit_row(int $post_id, string $key, array $args): void
    {
        ?>
        <td><input type="number" name="edd_variable_prices[<?php
            echo $key; ?>][bpmj_eddcm_purchase_limit_items_left]"
                   value="<?php
                   echo isset($args[self::PURCHASE_LIMIT_ITEMS_LEFT]) ? esc_attr($args[self::PURCHASE_LIMIT_ITEMS_LEFT]) : ''; ?>"
                   title="<?= $this->translator->translate('product_editor.sections.general.variable_prices.edit.table.purchase_limit_items_left') ?>"
                   style="width: 50px;" min="0" oninput="validity.valid||(value='');"/>
        </td>
        <td><input type="number" name="edd_variable_prices[<?php
            echo $key; ?>][bpmj_eddcm_purchase_limit]"
                   value="<?php
                   echo isset($args[self::PURCHASE_LIMIT]) ? esc_attr($args[self::PURCHASE_LIMIT]) : ''; ?>"
                   title="<?= $this->translator->translate('product_editor.sections.general.variable_prices.edit.table.purchase_limit') ?>"
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
        $args[self::PURCHASE_LIMIT] = $value[self::PURCHASE_LIMIT] ?? '';
        $args[self::PURCHASE_LIMIT_ITEMS_LEFT] = $value[self::PURCHASE_LIMIT_ITEMS_LEFT] ?? '';

        return $args;
    }

    public function variable_sale_price_field(int $post_id, int $key, array $args): void
    {
        $defaults = array(
            'sale_price' => null,
        );
        $args = wp_parse_args($args, $defaults);

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

            $recurring_payments_enabled = ($variable_price[self::RECURRING_PAYMENTS_ENABLED] ?? null)
                ? $this->translator->translate('product_editor.sections.general.variable_prices.table.body.'.self::RECURRING_PAYMENTS_ENABLED.'.yes')
                : $this->translator->translate('product_editor.sections.general.variable_prices.table.body.'.self::RECURRING_PAYMENTS_ENABLED.'.no');

            $html .= '<tr>
                           <td>' . $price_id . '</td>
                           <td>' . $variable_price['name'] . '</td>
                           <td>' . $price . '</td>
                           <td>' . $purchase_limit_items_left . ' / ' . $purchase_limit . '</td>
                           <td>' . $recurring_payments_enabled . '</td>
                    </tr>';
        }


        if (!$html) {
            $html = '<tr><td colspan="6" style="text-align:center;">' . $this->translator->translate(
                    'product_editor.sections.general.variable_prices.table.body.set_price_variants'
                ) . '</td></tr>';
        }

        return $html;
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

    private function get_course_meta(int $id, string $key)
    {
        return get_post_meta($id, $key, true);
    }

    private function sort_variable_field(array $variable_field): void
    {
        uasort($variable_field, function ($a, $b) {
            if (empty($a['index']) || empty($b['index'])) {
                return 0;
            }

            $a_int = (int)$a['index'];
            $b_int = (int)$b['index'];

            return $a_int <=> $b_int;
        });
    }

    private function set_changed_in_variable_field(array $variable_field): array
    {
        foreach ($variable_field as $id => $field) {
            if (is_array($field)) {
                $variable_field[$id]['changed'] = 1;
            }
        }
        return $variable_field;
    }

    public function render_recurring_payments_interval_variable_head()
    {
        ?>
        <th><?= $this->translator->translate('product_editor.sections.general.variable_prices.table.head.interval')?></th>
        <?php
    }

    public function render_recurring_payments_interval_variable_row($download_id = 0, $price_id = null)
    {
        $recurring_payments_interval = edd_recurring_get_interval($download_id, $price_id);
        $recurring_payments_possible = edd_recurring_payments_possible_for_download($download_id, $price_id);
        ?>
        <td class="interval_column">
            <label>
                <select name="edd_variable_prices[<?php echo $price_id; ?>][recurring_payments_interval_number]" <?php disabled(!$recurring_payments_possible); ?>>
                    <?php foreach (range(1, 30) as $number): ?>
                        <option value="<?php echo $number; ?>" <?php selected($number, false !== $recurring_payments_interval ? $recurring_payments_interval['number'] : false); ?>>
                            +<?php echo $number; ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                <select name="edd_variable_prices[<?php echo $price_id; ?>][recurring_payments_interval_unit]" <?php disabled(!$recurring_payments_possible); ?>>
                    <?php foreach (edd_recurring_get_interval_units() as $unit => $unit_name): ?>
                        <option value="<?php echo $unit; ?>" <?php selected($unit, false !== $recurring_payments_interval ? $recurring_payments_interval['unit'] : 'months'); ?>>
                            <?php echo $unit_name; ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </td>
        <?php
    }
}