<?php

namespace bpmj\wpidea\admin\pages\product_editor\core\fields;

use bpmj\wpidea\admin\settings\core\entities\fields\Abstract_Setting_Field;
use bpmj\wpidea\helpers\Translator_Static_Helper;
use bpmj\wpidea\modules\cart\api\Cart_API_Static_Helper;

class Product_Variable_Prices_Field extends Abstract_Setting_Field
{
    private const RECURRING_PAYMENTS_ENABLED = 'recurring_payments_enabled';
    private const AMOUNT = 'amount';
    private const SALE_PRICE = 'sale_price';
    private const ACCESS_TIME = 'access_time';
    private const ACCESS_TIME_UNIT = 'access_time_unit';
    private const PURCHASE_LIMIT_ITEMS_LEFT = 'bpmj_eddcm_purchase_limit_items_left';
    private const PURCHASE_LIMIT = 'bpmj_eddcm_purchase_limit';

    private int $id;

    public function __construct(
        string $name,
        ?string $label = null,
        ?string $description = null,
        ?string $tooltip = null,
        $value = null
    ) {
        parent::__construct($name, $label, $description, $tooltip, null, $value);
    }

    public function set_id(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }

        return $this->get_field_wrapper_start()
            . $this->field_variable_pricing_table($this->id, $this->get_value() ?? [])
            . $this->get_field_wrapper_end();
    }

    private function field_variable_pricing_table(int $post_id, array $variable_prices): string
    {
        ob_start();
        ?>
        <style>
            .table_variable_prices{
                width:100%;
                border-collapse: collapse;
            }
            .table_variable_prices th {
                text-align:left;
                background:#F8F8F9;
            }
            .table_variable_prices tr {
                border-bottom: 1px solid #E7EAEA;
            }
            .table_variable_prices td, th {
                padding: 10px;
            }
        </style>

        <div style="width:100%;">
            <table class="table_variable_prices">
                <thead>
                <tr>
                    <th><?= Translator_Static_Helper::translate('product_editor.sections.general.variable_prices.table.head.id') ?></th>
                    <th><?= Translator_Static_Helper::translate('product_editor.sections.general.variable_prices.table.head.name') ?></th>
                    <th><?= Translator_Static_Helper::translate('product_editor.sections.general.variable_prices.table.head.price') ?></th>
                    <th><?= Translator_Static_Helper::translate('product_editor.sections.general.variable_prices.table.head.availability') ?></th>
                    <th><?= Translator_Static_Helper::translate('product_editor.sections.general.variable_prices.table.head.access_on') ?></th>
                    <th><?= Translator_Static_Helper::translate('product_editor.sections.general.variable_prices.table.head.recurring_payments') ?></th>
                </tr>
                </thead>
                <tbody id="bpmj-eddcm-variable-prices" >
                <?= $this->get_variable_prices_html($variable_prices) ?>
                </tbody>
            </table>
        </div>
        <div style="width:100%; clear:both;">
            <br />
            <button type="button" data-action="edit-product-variable-prices" data-post-id="<?php echo $post_id; ?>" class="wpi-button wpi-button--clean configuration-button" style="float:right">
                <?= Translator_Static_Helper::translate('product_editor.sections.general.variable_prices.edit_variants') ?>
            </button>
        </div>
        <?php

        return ob_get_clean();
    }

    protected function get_variable_prices_html(array $variable_prices): string
    {
        $html = '';

        foreach ( $variable_prices as $price_id => $variable_price ) {

            $price = !empty($variable_price[self::SALE_PRICE])
                ? Cart_API_Static_Helper::get_formatted_price_with_currency( $variable_price[self::SALE_PRICE] ) .' / <s>'. Cart_API_Static_Helper::get_formatted_price_with_currency( $variable_price[self::AMOUNT] ).'</s>'
                : Cart_API_Static_Helper::get_formatted_price_with_currency( $variable_price[self::AMOUNT]);

            $purchase_limit_items_left = !empty($variable_price[self::PURCHASE_LIMIT_ITEMS_LEFT])
                ? $variable_price[self::PURCHASE_LIMIT_ITEMS_LEFT] : '-';

            $purchase_limit = !empty($variable_price[self::PURCHASE_LIMIT])
                ? $variable_price[self::PURCHASE_LIMIT] : '-';

            $access_time = $variable_price[self::ACCESS_TIME]
                ? $variable_price[self::ACCESS_TIME] .' '. Translator_Static_Helper::translate('product_editor.sections.general.access_time_unit.option.'.$variable_price[self::ACCESS_TIME_UNIT]) : '-';

            $recurring_payments_enabled = $variable_price[self::RECURRING_PAYMENTS_ENABLED]
                ? Translator_Static_Helper::translate('product_editor.sections.general.variable_prices.table.body.'.self::RECURRING_PAYMENTS_ENABLED.'.yes')
                : Translator_Static_Helper::translate('product_editor.sections.general.variable_prices.table.body.'.self::RECURRING_PAYMENTS_ENABLED.'.no');

            $html .= '<tr>
                           <td>'. $price_id .'</td>
                           <td>'. $variable_price['name'] .'</td>
                           <td>'. $price . '</td>
                           <td>'. $purchase_limit_items_left . ' / '. $purchase_limit .'</td>
                           <td>'. $access_time .'</td>
                           <td>'. $recurring_payments_enabled .'</td>
                    </tr>';
        }

        if (!$html) {
            $html = '<tr><td colspan="6" style="text-align:center;">'. Translator_Static_Helper::translate('product_editor.sections.general.variable_prices.table.body.set_price_variants') .'</td></tr>';
        }

        return $html;
    }
}
