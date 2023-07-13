<?php

namespace bpmj\wpidea\admin\settings\core\entities\fields;

use bpmj\wpidea\helpers\Translator_Static_Helper;

class Paid_Content_Renewal_Table_Field extends Abstract_Setting_Field
{
    public function render_to_string(): string
    {
        if (!$this->is_visible()) {
            return '';
        }
        return $this->get_field_wrapper_start('max-width-renewal-table').
                    $this->get_paid_content_renewal_table_html()
              .$this->get_field_wrapper_end();
    }

    private function get_paid_content_renewal_table_html(): string
    {
        ob_start();

            $add_url = esc_url(admin_url('admin.php?page=wp-idea-add-renewal'));
            $renewal_options = get_option('bmpj_eddpc_renewal');
            ?>
            <table id="edd_paid_content_renewal" class="wp-list-table widefat fixed posts">

                <thead>
                <tr>
                    <th class="type"><?php _e('Reminder type', 'edd-paid-content'); ?></th>
                    <th class=""><?php _e('Subject', 'edd-paid-content'); ?></th>
                    <th class="send-period"><?php _e('Sending period', 'edd-paid-content'); ?></th>
                    <th class="actions"><?php _e('Actions', 'edd-paid-content'); ?></th>
                </tr>
                </thead>

                <?php if (is_array($renewal_options)) { ?>
                    <tbody>
                    <?php
                    foreach ($renewal_options as $key => $option) {
                        $edit_url = esc_url(admin_url('admin.php?page=wp-idea-edit-renewal&renewal-id=' . $key));
                        $delete_url = add_query_arg(
                            ['wpid_action' => 'delete-renewal', 'renewal-id' => $key],
                            admin_url('admin.php?page=' . \bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug::SETTINGS . '&autofocus=messages')
                        );
                        $type = !empty($option['type']) ? $option['type'] : 'renewal';
                        ?>
                        <tr>
                            <td><?php echo $type === 'renewal' ? __('Renewal', 'edd-paid-content') : __('Payment', 'edd-paid-content'); ?></td>
                            <td><?php echo $option['subject']; ?></td>
                            <td><?php echo bpmj_eddpc_renewal_period_description($option['send_period'], $type); ?></td>
                            <td>
                                <a class="bpmj-eddpc-renewal-edit"
                                   href="<?php echo $edit_url; ?>"><?php _e('Edit', 'edd-paid-content'); ?></a> |
                                <a class="bpmj-eddpc-renewal-delete"
                                   href="<?php echo $delete_url; ?>"><?php _e('Delete', 'edd-paid-content'); ?></a>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                <?php } ?>

            </table>
            <p>
                <a href="<?php echo $add_url; ?>" class="redirect-button"
                   id="edd_paid_content_add_renewal"><?php _e('Add a renewal reminder', 'edd-paid-content'); ?></a>
                <?php
                $add_payment_notice_label = __('Add a payment reminder', 'edd-paid-content');
                if (bpmj_eddpc_recurring_payments_possible()):
                    ?>
                    <a href="<?php echo $add_url; ?>&amp;bpmj-renewal-type=payment" class="redirect-button"
                       id="edd_paid_content_add_payment_notice"><?php echo $add_payment_notice_label; ?></a>
                <?php
                else:
                    ?>
                    <button disabled="disabled" class="redirect-button button-no-active"
                            title="<?php esc_attr_e('You cannot add reminders - none of the enabled payment methods supports recurring payments.', 'edd-paid-content'); ?>"><?php echo $add_payment_notice_label; ?></button>

                <?php
                endif;
                ?>
            </p>
           <?php

        return ob_get_clean();
    }
}