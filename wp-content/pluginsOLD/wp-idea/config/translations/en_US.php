<?php

return [
    'action_does_not_exist' => 'Action does not exist!',
    'certificate_not_found' => 'Certificate not found',
    'certificate_template_not_found' => 'Certificate template not found!',
    'incorrect_return_params' => 'Incorrect return params',
    'invalid_token' => 'Invalid token!',
    'method_not_allowed' => 'Method not allowed!',
    'name_exist' => 'Name exist!',
    'no_permission_for_course' => 'You do not have permission for this course!',
    'no_permission_for_run_action' => 'You do not have permission to run this action!',
    'no_required_variables' => 'No required variables!',
    'quiz_does_not_exist' => 'The quiz does not exist!',
    'payment_error' => 'Payment error. Notify the administrator.',
    'payment_error_details' => 'Details',
    'understand' => 'I understand',
    'no_limit' => 'No limit',

    'course.settings.certificate_number.explanation.title' => 'Explanation for "Numbering pattern":',

    'course.settings.certificate_number.explanation.text' => '
                                              <p>
                                                Configure the certificate numbering pattern according to your preferences. It will be automatically
                                                printed on the generated certificate, if the certificate template includes the element named "Certificate Number".
                                             </p>
                                             <p>
                                                <strong>Example:</strong><br>
                                                ZM / X / YYYY pattern - will be replaced on the certificate with e.g. ZM/172/2021, if
                                                the next certificate number is 172 and the current year is 2021.
                                             </p>
                                              <p>
                                                <strong>The following are allowed:</strong>
                                                     <ul>
                                                      <li>uppercase and lowercase letters,</li>
                                                      <li>numbers,</li>
                                                      <li>characters: / - _ and spaces.</li>
                                                     </ul>
                                             </p>
                                             <p>
                                               <strong>Variables available:</strong><br>
                                                X - will be replaced by the digit in the "Next generated certificate field will get the number".<br>
                                                YY - will be replaced by the two-digit number for the current year.<br>
                                                YYYY - will be replaced with the four-digit number for the current year.
                                            </p>',

    'dynamic_table.results_per_page' => 'Results per page',
    'dynamic_table.data_types' => 'Data types',
    'dynamic_table.data_types.hint' => 'Select which columns should be visible in the table.',
    'dynamic_table.results.showing' => 'Displaying',
    'dynamic_table.results.to' => 'to',
    'dynamic_table.results.of' => 'of',
    'dynamic_table.results.results' => 'results',
    'dynamic_table.pagination.item_x_of' => 'of',
    'dynamic_table.pagination.prev' => 'Previous',
    'dynamic_table.pagination.next' => 'Next',
    'dynamic_table.loading' => 'Loading',
    'dynamic_table.refresh' => 'Refresh data',
    'dynamic_table.filters.select' => 'Select',
    'dynamic_table.filters.show' => 'Show filters',
    'dynamic_table.filters.hide' => 'Hide filters',
    'dynamic_table.filters.clear' => 'Clear filters',
    'dynamic_table.filters.clear_one' => 'Clear filter',
    'dynamic_table.filters.active_count' => 'Number of active filters',
    'dynamic_table.filters.type' => 'Type to search',
    'dynamic_table.filters.select_date' => 'Select a date',
    'dynamic_table.filters.select_date.today' => 'Today',
    'dynamic_table.filters.select_date.yesterday' => 'Yesterday',
    'dynamic_table.filters.select_date.this_week' => 'This week',
    'dynamic_table.filters.select_date.last_week' => 'Last week',
    'dynamic_table.filters.select_date.this_month' => 'This month',
    'dynamic_table.filters.select_date.last_month' => 'Last month',
    'dynamic_table.filters.select_date.from_the_start' => 'From the start',
    'dynamic_table.filters.select_date.to_the_end' => 'To the end',
    'dynamic_table.filters.select_date.apply' => 'Apply',
    'dynamic_table.filters.select_date.custom_range' => 'Range',
    'dynamic_table.filters.select_date.custom_range.days' => 'days',
    'dynamic_table.filters.select_date.cancel' => 'Cancel',
    'dynamic_table.filters.number_range.to' => 'to',
    'dynamic_table.export' => 'Export to CSV',
    'dynamic_table.export.loading' => 'Generating CSV file...',
    'dynamic_table.cell_content.read_more' => 'expand',
    'dynamic_table.cell_content.read_less' => 'collapse',
    'dynamic_table.bulk_actions' => 'Bulk actions',

    'notice.pixel_caffeine' => '%s plugin has a negative impact on the operation of WP Idea - we recommend removing and connecting Pixel through the built-in mechanism: %s',

    'logs.page_title' => 'Logs',
    'logs.menu_title' => 'Logs',
    'logs.level.100' => 'Debug',
    'logs.level.200' => 'Info',
    'logs.level.250' => 'Notice',
    'logs.level.300' => 'Warning',
    'logs.level.400' => 'Error',
    'logs.level.500' => 'Critical',
    'logs.level.550' => 'Alert',
    'logs.level.600' => 'Emergency',
    'logs.delete_all' => 'Delete all logs',
    'logs.source.wpi_default' => 'System',
    'logs.source.wpi_invoices.fakturownia' => 'Integration with Fakturownia',
    'logs.source.wpi_invoices.ifirma' => 'Integration with iFirma',
    'logs.source.wpi_invoices.wfirma' => 'Integration with wFirma',
    'logs.source.wpi_invoices.infakt' => 'Integration with Infakt',
    'logs.source.wpi_invoices.taxe' => 'Integration with Taxe',
    'logs.source.orders_source' => 'Orders',
    'logs.source.communication' => 'Communication',
    'logs.column.id' => 'ID',
    'logs.column.created_at' => 'Date',
    'logs.column.level' => 'Level',
    'logs.column.source' => 'Source',
    'logs.column.message' => 'Message',

    'logs.log_message.user_logged_in' => 'User {login} ({email}) has just logged in from IP: {ip}.',
    'logs.log_message.user_login_failed' => 'Failed login for {login} from IP: {ip}.',
    'logs.log_message.user_registred_by_admin' => 'New user registered: {user_register_login} - {user_register_role}. Entered by: {current_user_login}',
    'logs.log_message.user_registred_during_checkout' => 'New user registered: {user_register_login} - {user_register_role}.',
    'logs.log_message.user_changed_permissions' => 'Changing user permissions: {edit_user_login} - {edit_user_role}. Entered by: {current_user_login}',
    'logs.log_message.order_created' => 'Order no {order_id} for user {email} with the amount {amount} has been just created. ',
    'logs.log_message.order_completed' => 'Order no {order_id} for user {email} with the amount {amount} has been just completed.',
    'logs.log_message.triggering_an_webhook_event' => 'Webhook event: "{type_of_webhook}". The data has been sent to the following address: {url}. Returned response: {request}.',

    'logs.log_message.exceeding_active_sessions_limit' => 'When logging into the {email} account, it was detected that the number of concurrently active sessions was exceeded. Previous sessions have been deleted. Number of ended sessions: {destroyed_sessions}',

    'logs.invoices.queued' => 'An invoice has been queued to be issued for order: %s',
    'logs.invoices.error' => 'An error occurred while trying to issue an invoice: %s',
    'logs.invoices.success' => 'Invoice "%s" has been issued correctly for order: %s',
    'logs.invoices.api_error' => 'Error communicating with API: %s',

    'webhooks.page_title' => 'Webhooks',
    'webhooks.menu_title' => 'Webhooks',
    'webhooks.event.order_paid' => 'Order paid',
    'webhooks.event.quiz_finished' => 'Quiz finished',
    'webhooks.event.certificate_issued' => 'The certificate has been issued',
    'webhooks.event.student_enrolled_in_course' => 'The student has been enrolled in the course',
    'webhooks.event.course_completed' => 'Course completed',
    'webhooks.column.id' => 'ID',
    'webhooks.column.type_of_event' => 'Type of event',
    'webhooks.column.url' => 'URL address',
    'webhooks.column.status' => 'Status',
    'webhooks.status.active' => 'Active',
    'webhooks.status.suspended' => 'Suspended',
    'webhooks.actions.add_webhook' => 'Add new',
    'webhooks.actions.documentation' => 'Documentation',
    'webhooks.actions.edit' => 'Edit',
    'webhooks.actions.edit.loading' => 'Editing...',
    'webhooks.actions.delete' => 'Delete',
    'webhooks.actions.delete.loading' => 'Deleting...',
    'webhooks.actions.status.loading' => 'I change...',
    'webhooks.actions.status.active' => 'Activate',
    'webhooks.actions.status.inactive' => 'Pause',
    'webhooks.actions.delete.confirm' => 'Are you sure you want to delete this webhook? This action is irreversible.',
    'webhooks.form.add' => 'Add a new webhook',
    'webhooks.form.edit' => 'Edit webhook',
    'webhooks.form.save' => 'Save',
    'webhooks.form.cancel' => 'Cancel',
    'webhooks.form.return' => 'Return',
    'webhooks.form.select_option' => 'select an option',
    'webhooks.documentation.title' => 'Webhooks documentation',
    'webhooks.documentation.heading' => 'Format sending data for event "%s"',
    'webhooks.documentation.description' => 'Description of individual fields',
    'webhooks.render_page_wrong_plan' => 'To use the webhook management panel, you need to upgrade your license to level: ',

    'orders.page_title' => 'Orders',
    'orders.menu_title' => 'Orders',
    'orders.column.id' => 'ID',
    'orders.column.full_name' => 'Full name',
    'orders.column.email' => 'Email',
    'orders.column.phone_no' => 'Phone number',
    'orders.column.date' => 'Date',
    'orders.column.amount' => 'Amount',
    'orders.column.value' => 'Value',
    'orders.column.status' => 'Status',
    'orders.column.increasing_sales_offer_type' => 'Increasing sales',
    'orders.column.discount_code' => 'Discount code',
    'orders.column.first_checkbox' => 'Checkbox 1',
    'orders.column.second_checkbox' => 'Checkbox 2',
    'orders.column.delivery_address' => 'Delivery address',
    'orders.column.additional_checkbox.yes' => 'yes',
    'orders.column.additional_checkbox.no' => 'no',
    'orders.column.products' => 'Products',
    'orders.column.country' => 'Country',
    'orders.column.nip' => 'NIP',
    'orders.column.company_name' => 'Company name',
    'orders.column.details' => 'Details',
    'orders.column.payment_method' => 'Payment method',
    'orders.column.recurring_payments' => 'Recurring payment',
    'orders.actions.add_payment' => 'Add order',
    'orders.actions.delete' => 'Delete order',
    'orders.actions.delete.bulk' => 'Delete orders',
    'orders.actions.delete.loading' => 'Deleting...',
    'orders.actions.delete.confirm' => 'Are you sure you want to delete this order? This action is irreversible.',
    'orders.actions.delete.success' => 'The order has been successfully deleted!',
    'orders.actions.delete.bulk.confirm' => 'Are you sure you want to delete these orders? This action is irreversible.',
    'orders.actions.delete.bulk.success' => 'Orders have been successfully deleted!',
    'orders.actions.see_details' => 'See order details',
    'orders.actions.resend' => 'Resend email',
    'orders.actions.resend.loading' => 'Sending email...',
    'orders.actions.resend.success' => 'The email has been successfully sent!',
    'orders.actions.resend.bulk' => 'Resend email for selected',
    'orders.actions.resend.bulk.loading' => 'Sending emails...',
    'orders.actions.resend.bulk.success' => 'Emails have been successfully sent!',
    'orders.actions.payment.secure_ssl' => 'This is a secure SSL encrypted payment.',
    'orders.actions.payment.credit_card_info' => 'Credit Card Info',
    'orders.actions.payment.name_on_the_card' => 'Name on the Card',
    'orders.actions.payment.full_name' => 'Full Name',
    'orders.actions.payment.credit_card' => 'Credit Card',
    'orders.actions.payment.billing_details' => 'Billing Details',
    'orders.actions.payment.billing_country' => 'Billing Country',
    'orders.actions.payment.billing_zip' => 'Billing Zip / Postal Code',
    'orders.actions.payment.postal_code' => 'Zip / Postal Code',
    'orders.status.payu_recurrent' => 'Pending (PayU recurrent payment)',
    'orders.status.tpay_recurrent' => 'Pending (Tpay recurrent payment)',
    'orders.unknown_email' => 'Unknown email',
    'orders.invoice_data.full_name' => 'First and last name',
    'orders.invoice_data.street' => 'Street',
    'orders.invoice_data.building_number' => 'Building number',
    'orders.invoice_data.apartment_number' => 'Apartment number',
    'orders.recurring_payment.no' => 'No',
    'orders.recurring_payment.manual' => 'Yes - semi-automatic',
    'orders.recurring_payment.automatic' => 'Yes - automatic',

    'affiliate_program.page_title' => 'Affiliate program commissions',
    'affiliate_program.menu_title' => 'Affiliate program',
    'affiliate_program.column.id' => 'ID',
    'affiliate_program.column.partner_id' => 'Partner ID',
    'affiliate_program.column.partner_email' => 'Partner e-mail',
    'affiliate_program.column.partner_link' => 'Partner link',
    'affiliate_program.column.name' => 'Name',
    'affiliate_program.column.email' => 'Buyer e-mail',
    'affiliate_program.column.sale_date' => 'Sale date',
    'affiliate_program.column.purchased_products' => 'Purchased products',
    'affiliate_program.column.sales_amount' => 'Sales amount',
    'affiliate_program.column.commission_percentage' => 'Commission percentage',
    'affiliate_program.column.commission_amount' => 'Commission amount',
    'affiliate_program.column.status' => 'Status',
    'affiliate_program.actions.change_status' => 'Change of status',
    'affiliate_program.actions.change_status.confirm' => 'Are you sure you want to change the status (settled/unsettled)?',
    'affiliate_program.actions.change_status.loading' => 'I change ...',
    'affiliate_program.actions.change_status.bulk' => 'Change of statuses',
    'affiliate_program.actions.change_status.bulk.confirm' => 'Are you sure you want to change the statuses (settled/unsettled) for selected partners?',
    'affiliate_program.actions.delete' => 'Delete',
    'affiliate_program.actions.delete.confirm' => 'Are you sure you want to delete the selected items? This action is irreversible.',
    'affiliate_program.actions.delete.loading' => 'Deleting...',
    'affiliate_program.actions.delete.bulk' => 'Delete selected',
    'affiliate_program.actions.delete.bulk.confirm' => 'Are you sure you want to delete the selected items? This action is irreversible.',
    'affiliate_program.actions.add_partner' => 'Add a partner',
    'affiliate_program.actions.add_partner.success' => 'Partner created!',
    'affiliate_program.status.settled' => 'Settled',
    'affiliate_program.status.unsettled' => 'Unsettled',
    'affiliate_program.participants.page_title' => 'Affiliate program',
    'affiliate_program.participants.no_information' => 'No Information',
    'affiliate_program.participants.id' => 'Partner ID',
    'affiliate_program.participants.link' => 'Partner link',
    'affiliate_program.participants.status' => 'Status',
    'affiliate_program.participants.status.active' => 'Active',
    'affiliate_program.participants.status.inactive' => 'Inactive',
    'affiliate_program.order_details.note' => 'Purchased on recommendation ',
    'settings.affiliate_program' => 'Affiliate program',
    'settings.affiliate_program.commission_amount' => 'The amount of the commission',
    'settings.affiliate_program.licence_notice' => 'Change package: To use the affiliate program you need to change your license to PRO.',

    'affiliate_program_redirections.page_title' => 'Affiliate link generator',
    'affiliate_program_redirections.menu_title' => 'Link generator',
    'affiliate_program_redirections.actions.add' => 'Add link',
    'affiliate_program_redirections.column.id' => 'ID',
    'affiliate_program_redirections.column.product' => 'Product',
    'affiliate_program_redirections.column.url' => 'External URL',
    'affiliate_program_redirections.actions.delete' => 'Delete',
    'affiliate_program_redirections.actions.delete.confirm' => 'Are you sure you want to delete this item?',
    'affiliate_program_redirections.actions.delete.loading' => 'Deleting...',
    'affiliate_program_redirections.actions.add.page_title' => 'Add link',
    'affiliate_program_redirections.actions.add.select_product' => 'Select a product',
    'affiliate_program_redirections.actions.add.save' => 'Save',
    'affiliate_program_redirections.actions.add.cancel' => 'Back',

    'customers.page_title' => 'Customers',
    'customers.menu_title' => 'Customers',
    'customers.column.id' => 'ID',
    'customers.column.name' => 'Name',
    'customers.column.email' => 'E-mail',
    'customers.column.purchases' => 'Purchases',
    'customers.column.total_spent' => 'Total Spent',
    'customers.column.date_created' => 'Customer since',
    'customers.actions.delete' => __('Delete order', BPMJ_EDDCM_DOMAIN),
    'customers.actions.delete.bulk' => __('Delete orders', BPMJ_EDDCM_DOMAIN),
    'customers.actions.delete.loading' => __('Deleting...', BPMJ_EDDCM_DOMAIN),
    'customers.actions.delete.confirm' => __('Are you sure you want to delete this order? This action is irreversible.',
        BPMJ_EDDCM_DOMAIN),
    'customers.actions.delete.success' => __('The order has been successfully deleted!', BPMJ_EDDCM_DOMAIN),
    'customers.actions.delete.bulk.confirm' => __('Are you sure you want to delete these orders? This action is irreversible.',
        BPMJ_EDDCM_DOMAIN),
    'customers.actions.delete.bulk.success' => __('Orders have been successfully deleted!', BPMJ_EDDCM_DOMAIN),
    'customers.actions.data' => 'View buyer details',

    'students.column.id' => 'ID',
    'students.column.username' => 'Username',
    'students.column.name' => 'Name',
    'students.column.courses' => 'Courses',
    'students.column.email' => 'E-mail',
    'students.column.user_login' => 'User login',
    'students.page_title' => 'Students',
    'students.menu_title' => 'Students',
    'students.edit' => 'Edit',
    'logs.emails.send_attempt' => 'Sending email: ',
    'logs.emails.send_failed' => 'Sending failed: ',
    'logs.emails.to' => 'Recipient: ',
    'logs.emails.subject' => 'Subject: ',
    'logs.emails.source' => 'Communication',
    'courses.participants' => 'Participants',

    'quizzes.page_title' => 'Solved quizzes',
    'quizzes.menu_title' => 'Quizzes',
    'quizzes.not_rated_quizzes' => 'Not rated quizzes',
    'quizzes.column.id' => 'Id',
    'quizzes.column.course' => 'Course',
    'quizzes.column.title' => 'Quiz title',
    'quizzes.column.email' => 'E-mail',
    'quizzes.column.full_name' => 'Full name',
    'quizzes.column.points' => 'Points',
    'quizzes.column.result' => 'Result',
    'quizzes.column.date' => 'Completed at',
    'quizzes.actions.edit_quiz' => 'Edit quiz',
    'quizzes.actions.show_answers' => 'Show quiz answers',
    'quizzes.actions.show_student_profile' => 'Show student profile',

    'quiz.result.not_rated_yet' => 'Test not rated yet',
    'quiz.result.passed' => 'Test passed',
    'quiz.result.failed' => 'Test failed',

    'admin_menu_mode.switch_to_wp_menu' => 'Back to Dashboard',
    'admin_menu_mode.switch_to_lms_menu' => 'WP Idea',

    'admin_bar.bpmj_main' => 'WP Idea',
    'admin_bar.support' => 'Support',
    'admin_bar.license_info' => 'You are a %1$s package user',
    'admin_bar.free_space' => 'The current consumption of the available space for files other than videos is %s out of %s GB.',
    'admin_bar.free_video_space' => 'The current consumption of the available space for videos is %s out of %s.',
    'admin_bar.free_video_storage_space_and_traffic' => 'The current usage of available disk space for video files is %s of %s. Also used %s of %s available video transfer for the current month.',

    'certificates.page_title' => 'Certificates',
    'certificates.column.id' => 'Id',
    'certificates.column.course' => 'Course',
    'certificates.column.full_name' => 'Name',
    'certificates.column.email' => 'Email',
    'certificates.column.certificate_number' => 'Certificate number',
    'certificates.column.created' => 'Created',
    'certificates.regenerate' => 'Regenerate',
    'certificates.download' => 'Download',

    'edit_courses.certificate_numbering.enable' => 'Enable certificate numbering',
    'edit_courses.certificate_numbering.pattern' => 'Numbering pattern',
    'edit_courses.certificate_numbering.error' => 'Attention! The given by the numbering pattern does not contain the "X" parameter which is intended for you to generate the numbering.',
    'edit_courses.max_input_vars.error' => 'Attention! During the last attempt to save the course structure, an error occurred due to the low value of max_input_vars in PHP. Please contact your host provider.',

    'resources.type.course' => 'Course',
    'resources.type.digital_product' => 'Digital product',
    'resources.type.service' => 'Service',

    'digital_products.name' => 'Digital product name',
    'digital_products.mailer.desc' => 'Select the lists that the buyer will be subscribed to when he pays for access to the digital product',

    'media.limit_checker.title' => 'The file was not transferred',
    'media.limit_checker.error' => 'Due to the limit being exceeded, your current WP Idea package is not able to handle more files of the transferred type. Please contact WP Idea Technical Support to present possible solutions.',

    'media.video_format_blocker.title' => 'File format not allowed',
    'media.video_format_blocker.error' => 'If you want to upload video files, use the dedicated %1$s Video subpage %2$s.',


    'notifications.cron_not_working_correctly' => 'Our system has detected that the CRON on your server may not be working properly. Contact your site administrator or maintainer to check if it works properly.',

    'role.content_manager' => 'LMS Content Manager',
    'role.partner' => 'Partner',

    'discount_codes.page_title' => 'Discount codes',
    'discount_codes.column.id' => 'ID',
    'discount_codes.column.name' => 'Discount name',
    'discount_codes.column.code' => 'Code',
    'discount_codes.column.amount' => 'Amount',
    'discount_codes.column.uses' => 'Uses',
    'discount_codes.column.start_date' => 'Start date',
    'discount_codes.column.end_date' => 'Expires',
    'discount_codes.column.status' => 'Status',
    'discount_codes.column.status.active' => 'Active',
    'discount_codes.column.status.inactive' => 'Inactive',
    'discount_codes.column.status.expired' => 'Expired',
    'discount_codes.actions.add' => 'Add discount code',
    'discount_codes.actions.add.success' => 'Discount code added!',
    'discount_codes.actions.generate' => 'Generate many codes',
    'discount_codes.actions.edit' => 'Edit',
    'discount_codes.actions.edit.success' => 'Discount code updated!',
    'discount_codes.actions.delete' => 'Delete',
    'discount_codes.actions.delete.confirm' => 'Are you sure you want to delete this code? This action is irreversible.',
    'discount_codes.actions.delete.loading' => 'Deleting...',
    'discount_codes.actions.delete.success' => 'The code has been successfully deleted!',
    'discount_codes.plan_error.title' => 'Discount Code Generator',
    'discount_codes.plan_error.message' => 'To use discount code generator, you need to upgrade your license to level: %s or %s.',

    'admin_courses.cant_remove_bundled_course' => 'You cannot delete this course because it is tied to at least one package. Remove it from all packages, then try the removal again.',
    'admin_courses.cant_remove_bundled_course.bulk' => 'Some courses have been skipped because they are tied to at least one package.',
    'admin_courses.participants' => 'Participants',
    'invoices.vat_rate.is_vat_payer' => 'Is the seller an active VAT payer?',
    'invoices.vat_rate.is_vat_payer.yes' => 'Yes',
    'invoices.vat_rate.is_vat_payer.no' => 'No',
    'invoices.vat_rate.default_vat_rate' => 'Default VAT rate',
    'invoices.vat_rate.default_vat_rate.desc' => 'In percents. Applies only to active VAT payers. The VAT rate can also be set separately for each product.',
    'invoices.vat_rate' => 'VAT rate',
    'invoices.vat_rate.empty' => 'Leave empty to use the default rate.',
    'invoices.flat_rate_tax_symbol' => 'Flat rate tax symbol',
    'invoices.no_flat_rate_tax' => 'No flat rate tax',
    'invoices.warning_flat_rate_tax_not_supported' => 'Warning! Flat rate tax is not supported for: Taxe',

    'user_account.account_settings' => 'Account settings',
    'user_account.account_settings.details' => 'Basic user data, password change',
    'user_account.my_courses' => 'My courses',
    'user_account.my_courses.details' => 'Course list, subscription management',
    'user_account.my_digital_products' => 'My digital products',
    'user_account.my_digital_products.details' => 'Digital product list',
    'user_account.my_services' => 'My services',
    'user_account.my_services.details' => 'Service list, subscription management',
    'user_account.my_certificates' => 'My certificates',
    'user_account.my_certificates.details' => 'Generated certificate list',
    'user_account.my_certificates.product_name' => 'Product name',
    'user_account.my_certificates.download_certificate' => 'Download certificate',
    'user_account.my_certificates.download' => 'Download',
    'user_account.orders' => 'Transaction history',
    'user_account.orders.details' => 'List and details of the transaction',
    'user_account.partner_program' => 'Partner program',
    'user_account.partner_program.title' => 'Basic Information',
    'user_account.partner_program.details' => 'Basic Information, Affiliate links',
    'user_account.orders.send_invoice_again.done' => 'The request to resend the sales document has been transferred to the invoicing system.',
    'user_account.orders.send_invoice_again.something_went_wrong' => 'Oops! Something went wrong, please contact the platform administrator.',
    'user_account.orders.invoice' => 'Invoice',
    'user_account.orders.send_on_email' => 'Send on email',
    'user_account.history_transaction.login_page' => 'You need to login to see history transactions.',
    'user_account.opinions' => 'Add opinion',
    'user_account.opinions.details' => 'Rate courses and products you used',
    'user_account.opinions.add.save' => 'Add opinion',
    'user_account.opinions.add.label.reviewer_name' => 'Name (you can change it %shere%s)',
    'user_account.opinions.add.label.reviewed_product' => 'Product',
    'user_account.opinions.add.label.opinion_content' => 'Opinion content',
    'user_account.opinions.add.label.rating' => 'Rating',
    'user_account.opinions.add.select.select_product' => 'Select product',
    'user_account.opinions.add.add_opinion_info' => 'By adding an opinion, you accept <a href="%s" target="_blank">the rules</a>.',
    'user_account.opinions.add.saving_error' => 'An error occurred while saving.',
    'user_account.opinions.add.no_product_to_review' => 'You have no products or all of them have been reviewed',

    'user_account.my_partner_profile.external_landing_links.title' => 'Links to external promotional sites (created by the administrator)',
    'user_account.my_partner_profile.external_landing_links.id' => 'ID',
    'user_account.my_partner_profile.external_landing_links.product' => 'Product',
    'user_account.my_partner_profile.external_landing_links.link' => 'Affiliate link',
    'user_account.my_partner_profile.external_landing_links.info' => 'No links.',
    'user_account.my_commissions.info' => 'No commission!',

    'user_account.my_partner_profile.campaign.title' => 'How do I check which sources are converting?',
    'user_account.my_partner_profile.campaign.info' => 'If you use different communication channels during the promotion and want to know which ones are effective and generate sales under the affiliate program, see this entry in the Publigo knowledge base:  <a href="https://poznaj.publigo.pl/articles/224298-jak-ledzi-kampanie-w-programie-partnerskim" target="_blank">https://poznaj.publigo.pl/articles/224298-jak-ledzi-kampanie-w-programie-partnerskim</a>',

    'user_account.affiliate_program.commissions' => 'Affiliate Program - Commissions',
    'user_account.my_commissions.id' => 'ID',
    'user_account.my_commissions.campaign' => 'Campaign',
    'user_account.my_commissions.sales_amount' => 'Sales amount',
    'user_account.my_commissions.commission_amount' => 'Commission amount',
    'user_account.my_commissions.sale_date' => 'Sale Date',
    'user_account.my_commissions.status' => 'Status',

    'purchase_redirections.menu_title' => 'Redirects',
    'purchase_redirections.page_title' => 'Post-purchase redirects',
    'purchase_redirects.purchased_product' => 'Purchased product name',
    'purchase_redirects.redirect_url' => 'Post-purchase redirection URL',
    'purchase_redirects.priority' => 'Priority',
    'purchase_redirects.active_rules' => 'Active redirect rules',
    'purchase_redirects.no_active_rules' => 'No redirect rules have been defined',
    'purchase_redirects.new_rule' => 'New redirect',
    'purchase_redirects.new_condition' => 'Add condition',
    'purchase_redirects.remove_condition' => 'Remove condition',
    'purchase_redirects.condition.and' => 'and purchased',
    'purchase_redirects.condition.or' => 'or purchased',
    'purchase_redirects.select_product' => 'Select product',
    'purchase_redirects.enter_url' => 'Enter URL address',
    'purchase_redirects.save' => 'Save',
    'purchase_redirects.saving' => 'Saving...',
    'purchase_redirects.be_careful' => 'Be careful',
    'purchase_redirects.you_have_unsaved_changes' => 'you have unsaved changes',
    'purchase_redirects.reset_changes' => 'Undo changes',
    'purchase_redirects.save_success' => 'Saved successfully!',
    'purchase_redirects.save_error' => 'An error occurred while saving. Please contact the site administrator.',
    'purchase_redirects.remove_rule' => 'Remove redirect',
    'purchase_redirects.wrong_plan' => 'To use the redirects management panel, you need to upgrade your license to level: ',

    'services.services' => 'Services',
    'services.add_service' => 'Add a service',
    'services.edit_service' => 'Edit the service',
    'services.your_services' => 'Your services',
    'services.your_services.you_do_not_have_any_yet' => 'You have not created any services yet!',
    'services.your_services.create_one_in_creator' => 'Create a service with the help of a wizard',
    'services.your_services.view_service' => 'View the service',
    'services.your_services.create' => 'Create a new service',
    'services.your_services.service_name' => 'Name of service',
    'services.creator.title' => 'Create a service',
    'services.creator.saving_in_progress' => 'Your service is save...',
    'services.creator.saving_in_progress.title' => 'Saving the service',
    'services.creator.save.success' => 'The service has been saved!',
    'services.creator.step_button.configure_integration' => 'Set up integrations',
    'services.creator.step_button.save_service' => 'Save the service',
    'services.creator.step_name.details' => 'Service details',
    'services.creator.step_name.integrations' => 'Integrations',
    'services.creator.enter_service_name_here' => 'Enter the name of your service here.',
    'services.creator.how_much_costs' => 'How much does the service cost? Enter 0 if you want to make the product available for free.',
    'services.settings.enable_services' => 'Enable services functionality',
    'services.settings.enable_services.desc' => 'Once enabled, you will be able to sell services.',
    'services.editor.banner' => ' Service banner',
    'services.editor.enable_recurring_payments' => 'Enable recurring payments for this service',
    'services.editor.access_time_hint' => 'How long should the user be able to access the service? Leave blank so as not to limit access time.',
    'services.editor.no_access_to_access_time_message' => 'To be able to limit the access time to the service, you must have at least a package: %s.',
    'services.mailer.desc' => 'Select the lists to which the buyer will be subscribed when he pays for access to the service',
    'services.editor.promote_product' => 'Promote this product on the home page',
    'services.editor.promote_service' => 'Promote this service on the home page',

    'services.page_title' => 'Services',
    'services.column.id' => 'ID',
    'services.column.name' => 'Service name',
    'services.column.show' => 'Show',
    'services.column.sales' => 'Sales',

    'services.sales.status.enabled' => 'Enabled',
    'services.sales.status.disabled' => 'Disabled',

    'services.actions.create_service' => 'Create a new service',
    'services.actions.edit' => 'Edit',
    'services.actions.duplicate' => 'Duplicate',
    'services.actions.duplicate.not_available_in_your_package' => 'Course duplication is only available %s.',
    'services.actions.delete' => 'Delete',
    'services.actions.delete.success' => 'The service has been successfully removed!',
    'services.actions.delete.loading' => 'Deleting ...',
    'services.actions.delete.confirm' => 'Are you sure you want to delete the selected service? This action is irreversible.',
    'services.actions.sales.bulk' => 'Enable / disable sales',
    'services.actions.sales.active' => 'Enable sales',
    'services.actions.sales.inactive' => 'Disable sales',
    'services.actions.sales.loading' => 'I change...',
    'services.actions.delete.error' => 'An error occurred while deleting. Please contact the site administrator.',
    'services.actions.delete.info' => 'You cannot remove this service because it is assigned to at least one package. Remove it from all packages, then try the removal again.',
    'services.buttons.service_panel.tooltip' => 'View service',

    'services.popup.close' => 'Close',
    'services.popup.purchase_links.title' => 'Shopping Links',
    'services.buttons.purchase_links.tooltip' => 'Shopping Links',
    'services.buttons.digital_product_panel.tooltip' => 'Service panel',

    'templates_system.classic.undeveloped' => '(undeveloped)',
    'templates_system.scarlet.activation_warning' => 'Attention! The Classic template is no longer being developed. If you switch to the Scarlet template, you won\'t be able to go back to the Classic template anymore. Do you want to continue?',
    'templates_system.scarlet.settings.products_list_page' => 'Page with products list',

    'edit_course.start_date_warning' => 'Attention! Changing the date will only affect new students. Those who made a purchase before changing it will get access according to the original settings.',

    'settings.active_sessions_limiter' => 'Enable account login limit functionality',
    'settings.active_sessions_limiter.desc' => 'When this option is enabled, you will have the option to set the limit of users who can log into the same account at the same time.',
    'settings.max_active_sessions_number' => 'User limit',
    'settings.active_sessions_limiter.license' => 'Change package: To use the login limit you must change your license to PLUS or PRO.',
    'settings.main.payment_methods' => 'Payment methods',
    'settings.main.payment_methods.traditional_transfer' => 'Traditional transfer',
    'settings.main.payment_methods.traditional_transfer.name' => 'Company / Name',
    'settings.main.payment_methods.traditional_transfer.address' => 'Address',
    'settings.main.payment_methods.traditional_transfer.account_number' => 'Account Number',
    'settings.main.payment_methods.traditional_transfer.transfer_details' => 'Bank transfer details',
    'settings.main.payment_methods.traditional_transfer.empty' => 'In order to make a payment for purchased products, please contact the seller.',

    'settings.messages.purchase_subject' => 'Enter the title of the message sent to the buyer after receiving the payment for the product.',
    'settings.messages.purchase_heading' => 'Enter the header of the message sent to the buyer after the product payment is posted.',

    'color_settings.scarlet.header.general_settings' => '%s General settings %s',
    'color_settings.scarlet.bg_color' => 'Main background',
    'color_settings.scarlet.generic_white_color' => 'Name of the product promoted on the home page / %1$s Amount in the list of products / %1$s Number of products in the basket / %1$s Text on the navigation buttons in the lesson',
    'color_settings.scarlet.default_img_bg_color' => 'The background of the image distinguishing the product on the list / %s Login page background',
    'color_settings.scarlet.content_header_color' => 'Headings in content',
    'color_settings.scarlet.main_color' => 'Buttons color / %s Icons in the course menu',
    'color_settings.scarlet.main_color_hover' => 'Color of buttons on hover',
    'color_settings.scarlet.inactive_border_color' => 'Bottom border of buttons',
    'color_settings.scarlet.breadcrumbs_color' => 'Breadcrumbs',
    'color_settings.scarlet.tab_alt_text_color' => 'Other links / %1$s  Order page headers (cart) / %1$s My account headers / %1$s My account menu',
    'color_settings.scarlet.price_bg_color' => 'The background under the prices',

    'color_settings.scarlet.header.the_menu_bar' => '%s The menu bar %s',
    'color_settings.scarlet.content_header_bg_color' => 'Menu bar color',
    'color_settings.scarlet.link_color' => 'Links',
    'color_settings.scarlet.menu_link_bg_color' => 'Background of links in the submenu',
    'color_settings.scarlet.menu_bg_color' => 'Background of links in the submenu - mobile devices',
    'color_settings.scarlet.menu_link_hover_color' => 'Background of links in the submenu on hover',
    'color_settings.scarlet.menu_link_border_color' => 'Border around links in a submenu',
    'color_settings.scarlet.menu_border_color' => 'Border around links in the submenu - mobile devices',

    'color_settings.scarlet.header.footer' => '%s Footer %s',
    'color_settings.scarlet.footer_bg_color' => 'Footer color',
    'color_settings.scarlet.footer_text_color' => 'Footer text',

    'color_settings.scarlet.header.forms_order_page' => '%s Forms / Order page (cart) %s',
    'color_settings.scarlet.main_text_color' => 'Main content',
    'color_settings.scarlet.main_border_color' => 'Frames in forms',
    'color_settings.scarlet.placeholder_color' => 'Tooltip text in form fields',
    'color_settings.scarlet.form_text_color' => 'The text "required field"',
    'color_settings.scarlet.cart_border_color' => 'Frames in the shopping cart',
    'color_settings.scarlet.cart_promotion_price_color' => 'Regular price',
    'color_settings.scarlet.cart_summary_price_color' => 'Total amount in the order form',
    'color_settings.scarlet.discount_code_color' => 'Item Discount code',

    'color_settings.scarlet.header.login_page' => '%s Login page %s',
    'color_settings.scarlet.login_input_placeholder' => 'Hint text in the fields of the login form',
    'color_settings.scarlet.login_label_color' => 'The content of the checkboxes',

    'color_settings.scarlet.header.list_of_products' => '%s Product list (home page) %s',
    'color_settings.scarlet.main_box_border_color' => 'Frame around the products',
    'color_settings.scarlet.default_img_color' => 'Color of the picture icon that distinguishes the product on the list',
    'color_settings.scarlet.price_available_color' => 'The inscription "available" after purchase (instead of the price)',
    'color_settings.scarlet.promotion_price_color' => 'Regular price background with promotional price turned on',
    'color_settings.scarlet.category_link_color' => 'Category link',
    'color_settings.scarlet.display_mode_color' => 'The inscription "view" when changing the display mode',
    'color_settings.scarlet.display_mode_icon_color' => 'Display mode - inactive icons',
    'color_settings.scarlet.display_mode_active_icon_color' => 'Display mode - active icon',

    'color_settings.scarlet.header.course_pages' => '%s Course pages %s',
    'color_settings.scarlet.stage_text_color' => 'Link active and hovering over in the course menu',
    'color_settings.scarlet.lesson_completed_link_color' => 'Active link of the lesson in the course menu',
    'color_settings.scarlet.stage_bg_color' => 'Background of links when hovering over the course menu',
    'color_settings.scarlet.box_border_color' => 'Frame in the course menu',
    'color_settings.scarlet.stage_text_border_color' => 'Inner frames in the course menu',
    'color_settings.scarlet.course_stage_line_color' => 'Vertical lines in the course menu',
    'color_settings.scarlet.lesson_text_color' => 'Descriptions of the modules in the list',
    'color_settings.scarlet.completed_lesson_input_border' => 'A frame for selecting a completed lesson',
    'color_settings.scarlet.lesson_top_bg_color' => 'Information bar color in lessons',
    'color_settings.scarlet.lesson_top_text_color' => 'Text on the information bar in lessons',
    'color_settings.scarlet.lesson_icon_color' => 'Color of quiz buttons',
    'color_settings.scarlet.quiz_summary_img_bg_color' => 'The cup icon in the quiz summary - background',
    'color_settings.scarlet.quiz_summary_img_frame_color' => 'The cup icon in the quiz summary - smaller frame',
    'color_settings.scarlet.quiz_summary_img_border_color' => 'The cup icon in the quiz summary - larger frame',

    'color_settings.scarlet.header.comments' => '%s Comments %s',
    'color_settings.scarlet.comments_third_color' => 'Comments and moderation messages',

    'videos.menu_title' => 'Video',
    'videos.page_title' => 'Video',
    'videos.column.id' => 'ID',
    'videos.column.name' => 'Name',
    'videos.column.file_size' => 'File size',
    'videos.column.length' => 'Length',
    'videos.column.date_created' => 'Date added',
    'videos.column.actions.add_video' => 'Add a video',
    'videos.column.actions.delete' => 'Delete',
    'videos.actions.delete.confirm' => 'Are you sure you want to delete the selected video?',
    'videos.actions.delete.loading' => 'Deleting...',
    'videos.actions.delete.bulk' => 'Delete selected',
    'videos.actions.delete.bulk.confirm' => 'Are you sure you want to delete the selected video files?',
    'videos.column.actions.processing' => 'Processing...',
    'videos.column.actions.edit_settings' => 'Edit the settings',

    'media.submenu.other_media.title' => 'Other media',

    'packages.packages' => 'Packages',
    'packages.add_package' => 'Add a package',

    'courses.edit_course' => 'Edit course',
    'courses.edit_module' => 'Edit module',
    'courses.edit_lesson' => 'Edit lesson',
    'courses.edit_test' => 'Edit quiz',
    'courses.settings.courses_enable' => 'Enable course functionality',
    'courses.settings.courses_enable.desc' => 'Once enabled, you will be able to sell courses.',
    'courses.settings.courses_enable.is_courses' => 'To disable this functionality, delete all courses.',

    'digital_products.settings.digital_products_enable' => 'Enable the functionality of digital products',
    'digital_products.digital_products_enable.desc' => 'When this option is enabled, you will be able to sell files such as e-books, audiobooks etc.',
    'digital_products.digital_products_enable.disable_notice' => 'To disable this functionality, delete all digital products.',

    'breadcrumbs.list_courses' => 'List of courses',
    'breadcrumbs.list_products' => 'List of products',

    'template_list.items.list_products' => 'List of products',

    'blocks.products.title' => 'List of products',
    'blocks.products.items_page' => 'Items of page',
    'blocks.products.items_page.desc' => 'How many products should be displayed on one page of the catalog.',

    'blocks.products_slider.title' => 'Promoted products (slider)',
    'blocks.products_slider.front.title' => 'Promoted products',

    'blocks.notes.block_name' => 'Private notes / Comments',
    'blocks.notes.block_name.no_access_to_notes' => 'Private notes (PLUS/PRO) / Comments',
    'blocks.notes.tab_title' => 'Notes',
    'blocks.notes.delete_note_prompt' => 'Are you sure you want to delete this note?',
    'blocks.notes.note_content' => 'Note content',
    'blocks.notes.save_note' => 'Save note',
    'blocks.notes.edit_note' => 'Edit note',
    'blocks.notes.delete_note' => 'Delete note',

    'blocks.opinions.title' => 'Opinions',
    'blocks.opinions.items_page' => 'Number of opinions per page',
    'blocks.opinions.items_page.desc' => 'How many opinions to display on page.',
    'blocks.opinions.items_page.min' => 'The minimum number of opinions per page is 1.',
    'blocks.opinions.empty' => 'No reviews left',
    'blocks.opinions.empty.user' => 'Client',
    'blocks.opinions.column.label' => 'Show reviews in',
    'blocks.opinions.column.desc' => 'The opinion list can be divided into one or two columns',
    'blocks.opinions.column.options1' => ' 1 column',
    'blocks.opinions.column.options2' => '2 columns',

    'templates.checkout_cart.price' => 'Price',
    'templates.checkout_cart.price.gross' => 'Gross price',
    'templates.checkout_cart.price.net' => 'Net price',

    'templates.checkout_cart.products_in_cart' => 'Products in cart',
    'templates.checkout_cart.remove_from_cart' => 'Delete',
    'templates.checkout_cart.net' => 'Net',
    'templates.checkout_cart.vat' => 'VAT',
    'templates.checkout_cart.delivery' => 'Delivery price',

    'templates.checkout_confirmation.course_panel' => 'Course Panel',

    'notifications.page_title' => 'Notifications',
    'notifications.menu_title' => 'Notifications',

    'notifications.form.allow_user_notice' => 'Enable student notifications',
    'notifications.form.allow_user_notice.desc' => 'Enables the notification system for logged in users of the platform.',
    'notifications.form.allow_user_notice.notice' => 'Warning! Any modification of the content or disabling and re-enabling this functionality will make each user see the notification again (even if they have already closed it).',
    'notifications.form.allow_user_notice.content' => 'Content of the notification',
    'notifications.form.allow_user_notice.show_close_button' => 'Allow it to close',
    'notifications.form.allow_user_notice.show_close_button.desc' => 'When this option is enabled, the user has the option to close the notification. A closed notification will not reappear until you change its content or disable and re-enable the notification system.',
    'notifications.form.save' => 'Save',
    'notifications.form.while_saving' => 'Saving...',
    'notifications.form.wrong_license_msg' => 'To use notifications, you need to upgrade your license to level: %s or %s.',

    'settings.menu_title' => 'Settings',
    'settings.page_title' => 'Settings',
    'settings.sections.general' => 'Basic',
    'settings.sections.accounting' => 'Accountants',
    'settings.sections.payment' => 'Payment methods',
    'settings.sections.design' => 'Appearance',
    'settings.sections.integrations' => 'Integrations',
    'settings.sections.cart' => 'Shopping cart',
    'settings.sections.messages' => 'Messages',
    'settings.sections.gift' => 'Shopping for a gift',
    'settings.sections.certificate' => 'Certificates',
    'settings.sections.analytics' => 'Analytics and scripts',
    'settings.sections.modules' => 'Enable modules',
    'settings.sections.advanced' => 'Advanced',

    'settings.field.button.save' => 'Save',
    'settings.field.button.saving' => 'Saving...',
    'settings.field.button.saved' => 'Saved!',
    'settings.field.button.cancel' => 'Cancel',
    'settings.field.button.media' => 'Choose',

    'settings.field.validation.must_be_int' => 'The entered value must be a number greater than 0',
    'settings.field.validation.cant_be_empty' => 'The given value cannot be empty',
    'settings.field.validation.invalid_url' => 'Invalid url format',
    'settings.field.validation.invalid_extension' => 'Invalid file extension',
    'settings.field.validation.invalid_email' => 'Invalid email address format',
    'settings.field.validation.invalid_default_vat_rate' => 'The entered value must be a number. The maximum number of characters is 2',
    'settings.field.validation.difference_must_be_at_least_5_hours' => 'The minimum interval is 5 hours.',
    'settings.field.validation.fakturownia.invalid_apikey' => 'For proper integration, a Token with a prefix is required, e.g.: op7iHoQK4vHpbPYWQ2/my-account',

    'settings.popup.button.configure' => 'Configure',
    'settings.popup.button.save' => 'Save',
    'settings.popup.button.close' => 'Close',
    'settings.popup.button.cancel' => 'Cancel',
    'settings.popup.button.saving' => 'Saving...',
    'settings.field.select.choose' => 'Choose...',
    'settings.popup.saved' => 'The settings have been saved!',
    'settings.field.button.set' => 'Set',
    'settings.popup.button.add_new_variant' => 'Add new variant',

    'settings.messages.an_error_occurred' => 'An error occurred while saving. Please contact the administrator.',
    'settings.messages.unsaved_data_error' => "Attention! You have unsaved changes. \n Are you sure you want to leave this tab?",

    'settings.sections.general.fieldset.service' => 'Service',

    'settings.sections.general.blog_name' => 'Site title',
    'settings.sections.general.blog_name.tooltip' => 'Add a title that will be visible, among others in the website tab and search results. ',
    'settings.sections.general.blog_name.desc' => '',

    'settings.sections.general.blog_description' => 'Service description',
    'settings.sections.general.blog_description.tooltip' => 'Add a short description to be visible, incl. in the website tab and search results. ',
    'settings.sections.general.blog_description.desc' => '',

    'settings.sections.general.fieldset.license' => 'License',

    'settings.sections.general.license_key' => 'License key',
    'settings.sections.general.license_key.tooltip' => 'Tooltip text',
    'settings.sections.general.license_key.desc' => '',

    'settings.sections.general.fieldset.branding' => 'Branding',

    'settings.sections.general.logo' => 'Platform logo',
    'settings.sections.general.logo.desc' => 'Optimal size is 165px by 68 px (or 330px by 136px for retina screens).',

    'settings.sections.general.favicon' => 'Favicon',
    'settings.sections.general.favicon.desc' => 'The optimal size is 16px by 16px.',

    'settings.sections.general.fieldset.functional_pages' => 'Functional Pages',

    'settings.sections.general.page_on_front' => 'Home',
    'settings.sections.general.page_on_front.tooltip' => 'Select the page to be displayed as the home page.',

    'settings.sections.general.my_account' => 'My Account page',

    'settings.sections.general.after_logging_in' => 'After logging in',

    'settings.sections.general.contact_page' => 'Contact page / reCAPTCHA',
    'settings.sections.general.contact_page.tooltip' => 'Select the page where the contact form will appear.',

    'settings.sections.general.contact_page.popup.title' => 'Contact Page / reCAPTCHA',
    'settings.sections.general.recaptcha.popup.contact_page.additional_information' => 'ReCAPTCHA is required for the contact form to work properly',
    'settings.sections.general.recaptcha.popup.additional_information' => 'To properly configure reCAPTCHA, you must complete both fields below.',
    'settings.sections.general.recaptcha.site_key' => 'Site Key',
    'settings.sections.general.recaptcha.site_key.empty' => 'The "Site Key" field must be filled in!',
    'settings.sections.general.recaptcha.secret_key' => 'Secret key',
    'settings.sections.general.recaptcha.secret_key.empty' => 'The "Secret key" must be filled in!',

    'settings.sections.general.fieldset.comment_management' => 'Comment management',

    'settings.sections.general.comment_management' => 'Comments',
    'settings.sections.general.comment_management.tooltip' => 'If you want comments to appear in your course, please use the configurator.',

    'settings.sections.general.comment_management.popup.title' => 'Comments',

    'settings.sections.general.comments_notify' => 'Notify of new comments',
    'settings.sections.general.comments_notify.desc' => 'Check if you want to be notified every time someone adds a comment',
    'settings.sections.general.comments_notify.tooltip' => 'Check this if you want to receive notifications when someone adds a new comment.',

    'settings.sections.general.moderation_notify' => 'Notify of new comments pending moderation',
    'settings.sections.general.moderation_notify.desc' => 'Check if you want to be notified every time a new comment is awaiting moderation',
    'settings.sections.general.moderation_notify.tooltip' => 'Check this if you want to be notified when a new comment awaits moderation.',

    'settings.sections.general.comment_moderation' => 'Moderation of comments before their publication',
    'settings.sections.general.comment_moderation.desc' => 'Check if the comment must be approved manually before it appears on the website',
    'settings.sections.general.comment_moderation.tooltip' => 'Check this if you want to approve comments manually before publishing.',

    'settings.sections.general.comment_previously_approved' => 'Allow comments from trusted authors',
    'settings.sections.general.comment_previously_approved.desc' => 'Check if another comment by this author has to be manually approved for the next one to appear automatically on the website',
    'settings.sections.general.comment_previously_approved.tooltip' => 'Select if another comment by this author has to be confirmed manually for the next one to appear automatically on the page.',

    'settings.sections.general.fieldset.email' => 'Administrative',

    'settings.sections.general.contact_email' => 'Contact e-mail',

    'settings.sections.general.fieldset.footer' => 'Footer',
    'settings.sections.general.footer' => 'Footer content',

    'settings.sections.general.footer.popup.title' => 'Footer content',
    'settings.sections.general.footer.popup.footer_html' => 'Contents',

    'settings.sections.general.fieldset.cookie_bar' => 'GDPR',
    'settings.sections.general.cookie_bar' => 'Cookie bar',

    'settings.sections.general.cookie_bar.popup.title' => 'Cookie bar',

    'settings.sections.general.cookie_bar.popup.privacy_policy' => 'Privacy Policy',
    'settings.sections.general.cookie_bar.popup.privacy_policy.desc' => 'Select the page that contains the content of your Privacy Policy',

    'settings.sections.general.cookie_bar.popup.content' => 'The content of the cookie bar',

    'settings.sections.general.cookie_bar.popup.button_text' => 'The button on the cookie bar',

    'settings.sections.general.cookie_bar.popup.accept_button' => 'Accept button',

    'settings.sections.general.fieldset.new_sale_notifications' => 'Sale',
    'settings.sections.general.new_sale_notifications' => 'New sale notifications',
    'settings.sections.general.new_sale_notifications.tooltip' => 'If you want to receive notifications about new sales, use the configurator.',

    'settings.sections.general.new_sale_notifications.popup.title' => 'New sale notifications',

    'settings.sections.general.new_sale_notifications.popup.admin_notice_policy' => 'Notifications',
    'settings.sections.general.new_sale_notifications.popup.admin_notice_policy.tooltip' => 'Define when the system should send notifications about new sales.',

    'settings.sections.general.fieldset.delivery' => 'Delivery',
    'settings.sections.general.delivery_price' => 'Delivery price',
    'settings.sections.general.delivery_price.tooltip' => '',
    'settings.sections.general.delivery_price.popup.title' => 'Delivery price',

    'settings.sections.general.delivery_price.popup.delivery_price' => 'Delivery price',
    'settings.sections.general.delivery_price.popup.delivery_price.desc' => '',
    'settings.sections.general.delivery_price.popup.delivery_price.tooltip' => 'The amount entered in this field refers to the delivery containing at least one physical product, regardless of their quantity in the basket.',

    'settings.sections.general.delivery_price.popup.delivery_price.validation' => 'The given value should be between 0 and 9999',

    'settings.sections.general.delivery_price.popup.delivery_provider' => 'Provider',
    'settings.sections.general.delivery_price.popup.delivery_provider.desc' => '',
    'settings.sections.general.delivery_price.popup.delivery_provider.tooltip' => 'In this field, you can optionally enter the name of the courier company',

    'settings.sections.general.option.admin_notice_policy.disabled' => 'Disabled',
    'settings.sections.general.option.admin_notice_policy.comments' => 'Only orders with comments',
    'settings.sections.general.option.admin_notice_policy.all' => 'All orders',

    'settings.sections.general.new_sale_notifications.popup.admin_notice_emails' => 'E-mail addresses for sales notifications',
    'settings.sections.general.new_sale_notifications.popup.admin_notice_emails.desc' => 'Enter the address or email addresses to which sales notifications will be sent. Each address must be on a separate line.',

    'settings.sections.accounting.fieldset.currency' => 'Currency',

    'settings.sections.accounting.currency' => 'Currency',
    'settings.sections.accounting.currency.tooltip' => 'Select the currency in which the orders will be paid.',

    'settings.sections.accounting.thousands_separator' => 'Thousands separator',
    'settings.sections.accounting.thousands_separator.tooltip' => 'Select the type of thousands separator.',

    'settings.sections.accounting.decimal_separator' => 'Decimal separator',
    'settings.sections.accounting.decimal_separator.tooltip' => 'Select the type of decimal separator.',

    'settings.sections.accounting.option.separator.comma' => 'Comma',
    'settings.sections.accounting.option.separator.dot' => 'Dot',
    'settings.sections.accounting.option.separator.space' => 'Space',
    'settings.sections.accounting.option.separator.disabled' => 'Disabled',

    'settings.sections.accounting.fieldset.invoicing' => 'Invoicing',

    'settings.sections.accounting.enable_invoices' => 'Enable invoices',
    'settings.sections.accounting.enable_invoices.tooltip' => 'When this option is selected, additional fields will appear in the order form allowing the buyer to enter data on the invoice',


    'settings.sections.accounting.invoices_is_vat_payer' => 'Is the seller an active VAT payer?',

    'settings.sections.accounting.invoices_is_vat_payer.popup.title' => 'Is the seller an active VAT payer?',

    'settings.sections.accounting.invoices_is_vat_payer.popup.invoices_default_vat_rate' => 'Default VAT rate',
    'settings.sections.accounting.invoices_is_vat_payer.popup.invoices_default_vat_rate.desc' => 'In percents. It only applies to active VAT payers. The rate can also be set separately for each product.',

    'settings.sections.accounting.edd_id_force' => 'Require invoice details',
    'settings.sections.accounting.edd_id_force.tooltip' => 'When this option is active, providing invoice details when placing the order will be mandatory.',

    'settings.sections.accounting.edd_id_person' => 'Invoices for individuals',
    'settings.sections.accounting.edd_id_person.tooltip' => 'When this option is active, the invoice details will also be in the screens (first name, last name and address).',

    'settings.sections.accounting.edd_id_disable_tax_id_verification' => 'Disable NIP verification',
    'settings.sections.accounting.edd_id_disable_tax_id_verification.tooltip' => 'When this option is active, the VAT number field in the order form will be verified',

    'settings.sections.accounting.nip_for_receipts' => 'NIP for private persons',
    'settings.sections.accounting.nip_for_receipts.tooltip' => 'When this option is active, an additional field will appear in the order form allowing the private person to enter the tax identification number.',

    'settings.sections.accounting.edd_id_enable_vat_moss' => 'Enable simplified overseas sales (beta)',
    'settings.sections.accounting.edd_id_enable_vat_moss.tooltip' => 'When this option is active, an additional checkbox for you will appear in the invoice data in the order. Specifying a country other than Poland will result in the introduction of modifications to the invoice issued (in accordance with the rules of MOSS VAT exemption up to the indicated amount limit).',

    'settings.sections.accounting.enable_flat_rate_tax_symbol' => 'Enable flat tax rate',

    'settings.sections.accounting.flat_rate_tax_symbol' => 'Flat rate tax symbol',
    'settings.sections.accounting.flat_rate_tax_symbol.tooltip' => 'Activate if you pay lump-sum tax on recorded income. You will be able to indicate the flat rate at which to record the sale of your products. You can define the flat rate in the settings of a given product. ',

    'settings.sections.accounting.fieldset.gus' => 'GUS API',
    'settings.sections.accounting.gus' => 'Enable service',
    'settings.sections.accounting.gus.tooltip' => 'Enabling this functionality will allow you to quickly fill out the fields of the order form after entering the company tax identification number.',
    'settings.sections.accounting.enable_gus.notice' => 'Change package: To use the functionality of downloading data from the GUS database, you must change your license to PLUS or PRO.',

    'settings.sections.payments.fieldset.configuration_and_tests' => 'Configuration and tests',
    'settings.sections.payments.payment_settings' => 'Payment settings',
    'settings.sections.payments.payment_settings.tooltip' => 'Here you can configure your payment settings.',


    'settings.sections.payments.test_mode' => 'Test mode',
    'settings.sections.payments.test_mode.tooltip' => 'While in test mode, no live transactions are processed. To fully use the test mode, you must have a sandbox (test) account for the payment gateway you are testing.',

    'settings.sections.payments.default_gateway' => 'Default payment method',
    'settings.sections.payments.default_gateway.desc' => 'This payment gate will be used as the default.',
    'settings.sections.payments.default_gateway.tooltip' => 'Select the payment gateway that will appear in the order form by default .',

    'settings.sections.payments.display_payment_methods_as_icons' => 'Display payment methods as icons on checkout',
    'settings.sections.payments.display_payment_methods_as_icons.tooltip' => 'When you select this option, small logos of payment methods will be displayed instead of radio buttons during checkout.',

    'settings.sections.payments.test_payment_gate' => 'Payment test',
    'settings.sections.payments.test_payment_gate.tooltip' => 'Check to activate test mode. It allows you to test payment gateways without having to make real payments. The test mode works only with gateways that provide the so-called sandbox account . ',

    'settings.sections.payments.section.payment_gates' => 'Available payment gates',

    'settings.sections.payments.fieldset.bank_and_recurring_payments' => 'Bank and recurring payments',

    'settings.sections.payments.checkout_label' => 'Payment method name',
    'settings.sections.payments.checkout_label.tooltip' => 'Please enter a name for this payment method.',

    'settings.sections.payments.tpay_payment_gate' => 'Tpay.com',
    'settings.sections.payments.tpay_payment_gate.tooltip' => 'Here you can configure payments <a href ="http://tpay.com"> Tpay.com </a>',

    'settings.sections.payments.tpay.tpay_id' => 'Tpay.com ID',
    'settings.sections.payments.tpay.tpay_id.desc' => 'Enter your tpay.com ID',

    'settings.sections.payments.tpay.tpay_pin' => 'Tpay.com security code',
    'settings.sections.payments.tpay.tpay_pin.desc' => 'Enter your security code (confirming)',

    'settings.sections.payments.tpay.tpay_cards_api_key' => 'API key for payment cards (optional)',
    'settings.sections.payments.tpay.tpay_cards_api_key.desc' => 'Enter your API key for payment cards. Entering the key and password allows you to enable recurring (subscription) payments',
    'settings.sections.payments.tpay.tpay_cards_api_key.tooltip' => 'Enter your API key for payment cards. Entering the key and password allows you to enable recurring (subscription) payments. The API key can be found in the Tpay panel by going to <b> Card payments> API </b>. ',

    'settings.sections.payments.tpay.tpay_cards_api_password' => 'API password for payment cards (optional)',
    'settings.sections.payments.tpay.tpay_cards_api_password.desc' => 'Enter your API password for payment cards',
    'settings.sections.payments.tpay.tpay_cards_api_password.tooltip' => 'Enter your API password for payment cards. You can find the API password by going to <b> Card payments> API </b> ',

    'settings.sections.payments.tpay.tpay_cards_verification_code' => 'The verification code for the payment card API',
    'settings.sections.payments.tpay.tpay_cards_verification_code.desc' => 'Enter your verification code into the API for payment cards',
    'settings.sections.payments.tpay.tpay_cards_verification_code.tooltip' => 'Enter your verification code into the payment card API. You can find the verification code by going to <b> Card payments> API </b> ',

    'settings.sections.payments.tpay.tpay_recurrence_allow_standard_payments' => 'Enable standard payment methods for recurrent orders',
    'settings.sections.payments.tpay.tpay_recurrence_allow_standard_payments.tooltip' => 'When enabled, customers will be able to choose non-card payment methods to pay for recurrent products. The system will automatically generate payments for consecutive periods, but the customer has to be informed and make the payment manually. Automatic charging is possible only with credit card payments.',

    'settings.sections.payments.payu_payment_gate' => 'PayU',
    'settings.sections.payments.payu_payment_gate.tooltip' => 'Here you can configure PayU payments',

    'settings.sections.payments.payu.payu_pos_id' => 'Point of payment id (pos_id)',
    'settings.sections.payments.payu.payu_pos_id.desc' => '',
    'settings.sections.payments.payu.payu_pos_id.tooltip' => 'Enter the ID of the point of payment ( pos_id ). You will find it in the administration panel <b> Pay U> Electronic payments> My shops> Payment points. </b> ',

    'settings.sections.payments.payu.payu_pos_auth_key' => 'Payment authorization key (pos_auth_key)',
    'settings.sections.payments.payu.payu_pos_auth_key.desc' => '',

    'settings.sections.payments.payu.payu_key1' => 'Key (MD5)',
    'settings.sections.payments.payu.payu_key1.desc' => '',

    'settings.sections.payments.payu.payu_key2' => 'Second key (MD5)',
    'settings.sections.payments.payu.payu_key2.desc' => '',
    'settings.sections.payments.payu.payu_key2.tooltip' => 'Enter the second key (MD5). You will find it in the administration panel <b> Pay U> Electronic payments> My shops> Payment points. </b> ',

    'settings.sections.payments.payu.payu_api_type' => 'Type API PayU',
    'settings.sections.payments.payu.payu_api_type.desc' => '',
    'settings.sections.payments.payu.payu_api_type.tooltip' => 'Select API Pay U type. We recommend choosing the newer and drop-down REST type.',

    'settings.sections.payments.option.payu_api_type.rest' => 'REST (Checkout - Express Payment)',
    'settings.sections.payments.option.payu_api_type.classic' => 'Classic (Express Payment)',

    'settings.sections.payments.payu.payu_return_url_failure' => '',
    'settings.sections.payments.payu.payu_return_url_failure.desc' => 'Copy and paste this url into your PayU payment point settings',

    'settings.sections.payments.payu.payu_return_url_success' => '',
    'settings.sections.payments.payu.payu_return_url_success.desc' => 'Copy and paste this url into your PayU payment point settings',

    'settings.sections.payments.payu.payu_return_url_reports' => '',
    'settings.sections.payments.payu.payu_return_url_reports.desc' => 'Copy and paste this url into your PayU payment point settings',

    'settings.sections.payments.payu.payu_api_environment' => 'Environment PayU API',
    'settings.sections.payments.payu.payu_api_environment.desc' => '',
    'settings.sections.payments.payu.payu_api_environment.tooltip' => 'Select the PayU API environment. Remember that each environment has its own keys that need to be changed. ',

    'settings.sections.payments.option.payu_api_environment.secure' => 'Secure (default)',
    'settings.sections.payments.option.payu_api_environment.sandbox' => 'Sandbox (for testing)',

    'settings.sections.payments.payu.payu_recurrence_allow_standard_payments' => 'Enable standard payment methods for recurring purchases',
    'settings.sections.payments.payu.payu_recurrence_allow_standard_payments.tooltip' => 'After selecting this option, the buyer will be able to choose standard payment channels to pay for recurring products. The system will automatically generate payments for subsequent billing periods, but the customer will have to be informed and make the payment manually. Automatic debiting of the customers account is only possible when paying by credit card.',

    'settings.sections.payments.payu.payu_enable_debug' => 'Enable diagnostic mode',
    'settings.sections.payments.payu.payu_enable_debug.tooltip' => 'After selecting this option, additional diagnostic information will be collected regarding transactions carried out by PayU.',

    'settings.sections.payments.payu.payu_checkout_label' => 'Payment method name',
    'settings.sections.payments.payu.payu_checkout_label.tooltip' => 'Please enter a name for this payment method.',

    'settings.sections.payments.fieldset.bank_payments' => 'Bank payments',

    'settings.sections.payments.przelewy24_payment_gate' => 'Przelewy24',
    'settings.sections.payments.przelewy24_payment_gate.tooltip' => 'Here you can configure Przelewy24 payments.',

    'settings.sections.payments.przelewy24.przelewy24_id' => 'Przelewy24 ID',
    'settings.sections.payments.przelewy24.przelewy24_id.desc' => 'Your Przelewy24 account ID',
    'settings.sections.payments.przelewy24.przelewy24_id.tooltip' => 'Enter the account ID in Przelewy24.',

    'settings.sections.payments.przelewy24.przelewy24_pin' => 'Przelewy24 CRC',
    'settings.sections.payments.przelewy24.przelewy24_pin.desc' => 'You can find this CRC code on <a href="http://przelewy24.pl">Przelewy24.pl</a>: <b>Moje dane / Klucz do CRC</b>',
    'settings.sections.payments.przelewy24.przelewy24_pin.tooltip' => 'Enter the CRC key. You will find it in the administration panel <b> Przelewy24> My data> CRC key. </b> ',

    'settings.sections.payments.przelewy24.przelewy24_checkout_label' => 'Payment method name',
    'settings.sections.payments.przelewy24.przelewy24_checkout_label.tooltip' => 'Please enter a name for this payment method.',

    'settings.sections.payments.dotpay_payment_gate' => 'Dotpay',
    'settings.sections.payments.dotpay_payment_gate.tooltip' => 'Here you can configure Dotpay payments .',

    'settings.sections.payments.dotpay.info_message' => "Before start using this payment gate, please go to <a href='http://www.dotpay.pl/'>Dotpay.pl</a> admin panel: <b>Ustawienia / Konfiguracja URLC / Edycja[JD5]</b> and uncheck the fields <b>Blokuj zewnętrzne urlc[JD6]</b> and <b>HTTPS verify</b>.<br>That will helps with making payments through this plugin.",

    'settings.sections.payments.dotpay.dotpay_id' => 'Dotpay ID',
    'settings.sections.payments.dotpay.dotpay_id.desc' => 'Your Dotpay account ID',
    'settings.sections.payments.dotpay.dotpay_id.tooltip' => 'Please enter your Dotpay account ID .',

    'settings.sections.payments.dotpay.dotpay_pin' => 'Dotpay PIN',
    'settings.sections.payments.dotpay.dotpay_pin.desc' => "It's a string that you have to prepare on <a href='http://www.dotpay.pl/'>Dotpay.pl</a>: <b>Ustawienia / parametry URLC</b>",
    'settings.sections.payments.dotpay.dotpay_pin.tooltip' => 'Enter your PIN in Dotpay . It should be put in <a href ="http://www.dotpay.pl/"> Dotpay.pl </a> <b>> Settings> ParametersURLC </b> ',

    'settings.sections.payments.dotpay.dotpay_onlinetransfer' => 'Realtime payments',
    'settings.sections.payments.dotpay.dotpay_onlinetransfer.desc' => 'Check if you accept only realtime payments',
    'settings.sections.payments.dotpay.dotpay_onlinetransfer.tooltip' => 'Check this option if you only want to accept real-time payments.',

    'settings.sections.payments.dotpay.dotpay_checkout_label' => 'Payment method name',
    'settings.sections.payments.dotpay.dotpay_checkout_label.tooltip' => 'Please enter a name for this payment method.',

    'settings.sections.payments.paynow_payment_gate' => 'Paynow',
    'settings.sections.payments.paynow_payment_gate.tooltip' => 'Here you can configure Paynow payments.',

    'settings.sections.payments.paynow.paynow_access_key' => 'Access key to API',
    'settings.sections.payments.paynow.paynow_access_key.tooltip' => 'Please enter your API key. You will find it in the Paynow admin panel.',

    'settings.sections.payments.paynow.paynow_signature_key' => 'Signature key to API',
    'settings.sections.payments.paynow.paynow_signature_key.tooltip' => 'Please enter your API key signature. You will find the signature in the Paynow administration panel under the name <b> Signature calculation key. </b> ',

    'settings.sections.payments.paynow.paynow_environment' => 'Paynow environment',
    'settings.sections.payments.paynow.paynow_environment.desc' => 'Select Paynow environment',
    'settings.sections.payments.paynow.paynow_environment.option_production' => 'Production',
    'settings.sections.payments.paynow.paynow_environment.option_sandbox' => 'Sandbox (for testing)',

    'settings.sections.payments.paynow.paynow_environment.tooltip' => 'Select the Paynow environment . Remember that each sandbox environment has a different address. ',
    'settings.sections.payments.paynow.paynow_checkout_label' => 'Payment method name',
    'settings.sections.payments.paynow.paynow_checkout_label.tooltip' => 'Please enter a name for this payment method.',

    'settings.sections.payments.stripe_payment_gate' => 'Stripe',
    'settings.sections.payments.stripe_payment_gate.tooltip' => 'Here you can configure Stripe payments .',

    'settings.sections.payments.stripe.test_secret_key' => 'Test secret',
    'settings.sections.payments.stripe.test_secret_key.desc' => 'Enter your test secret key. You will find it in your Stripe account in the Developers -> API keys tab (test mode on).',
    'settings.sections.payments.stripe.test_secret_key.tooltip' => 'Enter your test secret (test secret key ). You will find it in your Stripe account under <b> Developers -> API keys </b> (test mode enabled). ',

    'settings.sections.payments.stripe.test_publishable_key' => 'Test public key',
    'settings.sections.payments.stripe.test_publishable_key.desc' => 'Enter your test publishable key. You will find it in your Stripe account in the Developers -> API keys tab (test mode on).',
    'settings.sections.payments.stripe.test_publishable_key.tooltip' => 'Enter your test public key (test publishable key ). You will find it in your Stripe account under <b> Developers -> API keys </b> (test mode enabled). ',

    'settings.sections.payments.stripe.live_secret_key' => 'Production secret',
    'settings.sections.payments.stripe.live_secret_key.desc' => 'Enter your production live secret key). You will find it in your Stripe account in the Developers -> API keys tab (test mode turned off).',
    'settings.sections.payments.stripe.live_secret_key.tooltip' => 'Enter your live secret production secret key ). You will find it in your Stripe account under <b> Developers -> API keys </b> ( test mode turned off ). ',

    'settings.sections.payments.stripe.live_publishable_key' => 'Production public key',
    'settings.sections.payments.stripe.live_publishable_key.desc' => 'Enter your live publishable key. You will find it on your Stripe account in the Developers -> API keys tab (test mode off).',
    'settings.sections.payments.stripe.live_publishable_key.tooltip' => 'Enter your production public key (live publishable key ). You will find it in your Stripe account under <b> Developers -> API keys </b> (test mode turned off). ',

    'settings.sections.payments.stripe.stripe_checkout_label' => 'Payment method name',
    'settings.sections.payments.stripe.stripe_checkout_label.tooltip' => 'Please enter a name for this payment method.',

    'settings.sections.payments.fieldset.other_payments' => 'Other types of payments',

    'settings.sections.payments.paypal_payment_gate' => 'PayPal',
    'settings.sections.payments.paypal_payment_gate.tooltip' => 'Here you can configure PayPal payments .',

    'settings.sections.payments.paypal.paypal_email' => 'PayPal Email',
    'settings.sections.payments.paypal.paypal_email.desc' => 'Enter the email address of your PayPal account',
    'settings.sections.payments.paypal.paypal_email.tooltip' => 'Enter the email address of your PayPal account .',

    'settings.sections.payments.paypal.paypal_page_style' => 'PayPal payment page style',
    'settings.sections.payments.paypal.paypal_page_style.desc' => 'Enter the name of the page style you want to use or leave blank for the default settings.',
    'settings.sections.payments.paypal.paypal_page_style.tooltip' => 'Enter the name of the page style you want to use or leave blank for the default settings.',

    'settings.sections.payments.paypal.disable_paypal_verification' => 'Disable IPN verification',
    'settings.sections.payments.paypal.disable_paypal_verification.desc' => 'Check if the order status does not change to Completed. This option changes the payment verification method to a slightly less secure one.',
    'settings.sections.payments.paypal.disable_paypal_verification.tooltip' => 'Check if the order status does not change to Completed. This option changes the payment verification method to a slightly less secure. ',

    'settings.sections.payments.paypal.paypal_checkout_label' => 'Payment method name',
    'settings.sections.payments.paypal.paypal_checkout_label.tooltip' => 'Please enter a name for this payment method.',

    'settings.sections.payments.coinbase_payment_gate' => 'Coinbase',
    'settings.sections.payments.coinbase_payment_gate.tooltip' => 'Here you can configure Coinbase payments .',
    'settings.sections.payments.coinbase.edd_coinbase_api_key.tooltip' => 'Enter your API key from Coinbase .',
    'settings.sections.payments.coinbase.coinbase_checkout_label.tooltip' => 'Please enter a name for this payment method.',
    'settings.sections.payments.coinbase.coinbase_checkout_label' => 'Please enter a name for this payment method',
    'settings.sections.payments.coinbase.info_message' => 'For this payment method to function fully, you need to configure webhook. To do this, go to <a href="https://commerce.coinbase.com/dashboard/settings" target="_blank">your Coinbase account page now</a>. Then, add a webhook pointing to the URL listed below.<br> Webhook URL: %s<br>See the <a href="https://docs.easydigitaldownloads.com/article/314-coinbase-payment-gateway-setup-documentation">Coinbase documentation</a> if you need more information.',
    'settings.sections.payments.coinbase.edd_coinbase_api_key' => 'API Key',
    'settings.sections.payments.coinbase.edd_coinbase_api_key.desc' => 'Enter your API key from Coinbase',

    'settings.sections.payments.transfers_payment_gate' => 'Traditional transfer',

    'settings.sections.payments.transfers.edd_przelewy_name' => 'Company / Name and surname',
    'settings.sections.payments.transfers.edd_przelewy_name.desc' => '',

    'settings.sections.payments.transfers.edd_przelewy_address' => 'Address',
    'settings.sections.payments.transfers.edd_przelewy_address.desc' => 'ul. Street name 123, 11-123 Warsaw',

    'settings.sections.payments.transfers.edd_przelewy_account_number' => 'Account number',
    'settings.sections.payments.transfers.edd_przelewy_account_number.desc' => '',

    'settings.sections.payments.transfers.przelewy_checkout_label' => 'Payment method name',

    'settings.sections.advanced.fieldset' => 'Advanced',
    'settings.sections.advanced.allow_inline_file_download' => 'Opening files',
    'settings.sections.advanced.allow_inline_file_download.tooltip' => 'Choose how to open files when the user clicks on them.',
    'settings.sections.advanced.allow_inline_file_download.desc' => '',
    'settings.sections.advanced.allow_inline_file_download.option.inline' => 'Open in browser when possible',
    'settings.sections.advanced.allow_inline_file_download.option.attachment' => 'Force download to disk',

    'settings.sections.advanced.enable_logo_in_courses_to_home_page' => 'Directing the logo to the home page',
    'settings.sections.advanced.enable_logo_in_courses_to_home_page.desc' => '',
    'settings.sections.advanced.enable_logo_in_courses_to_home_page.tooltip' => 'When this option is active, the logo in the header will always be directed to the home page of the website (instead of to the panel of a given course when inside its structure).',
    'settings.sections.advanced.enable_logo_in_courses_to_home_page.popup.title' => 'settings.sections.advanced.enable_logo_in_courses_to_home_page.popup.title',

    'settings.sections.advanced.enable_active_sessions_limiter' => 'Account login limit functionality',
    'settings.sections.advanced.enable_active_sessions_limiter.desc' => '',
    'settings.sections.advanced.enable_active_sessions_limiter.tooltip' => 'When this option is active, you will be able to set a limit of users who can log in to the same account at the same time.',
    'settings.sections.advanced.enable_active_sessions_limiter.notice' => 'Change package: To use the login limit functionality you must change your license to PLUS lub PRO.',
    'settings.sections.advanced.enable_active_sessions_limiter.popup.title' => 'Account login limit functionality',
    'settings.sections.advanced.enable_active_sessions_limiter.max_active_sessions_number' => 'Session limit',
    'settings.sections.advanced.enable_active_sessions_limiter.max_active_sessions_number.desc' => '',
    'settings.sections.advanced.enable_active_sessions_limiter.max_active_sessions_number.tooltip' => '',
    
    'settings.sections.advanced.enable_payment_reminders' => 'Lost order recovery',
    'settings.sections.advanced.enable_payment_reminders.desc' => '',
    'settings.sections.advanced.enable_payment_reminders.tooltip' => '',
    'settings.sections.advanced.enable_payment_reminders.notice' => 'Change package: To use the lost cart recovery functionality you must change your license to PRO.',

    'settings.sections.advanced.enable_payment_reminders.popup.title' => 'Lost order recovery',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_number_days' => 'Number of days after which the message is sent in case of non-payment',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_number_days.desc' => '',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_number_days.tooltip' => '',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject' => 'Subject of the message sent in case of non-payment',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject.desc' => '',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject.tooltip' => '',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject.value' => 'You have an unpaid order number: {payment_id}',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_content' => 'The content of the message sent in the absence of payment',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_content.desc' => '',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_content.tooltip' => '',

    'settings.sections.advanced.enable_sell_discounts' => 'Generating discounts',
    'settings.sections.advanced.enable_sell_discounts.desc' => '',
    'settings.sections.advanced.enable_sell_discounts.tooltip' => 'Activating this option will allow you to generate a discount with each purchase (e.g. to provide a unique discount code for your next purchase).',

    'settings.sections.advanced.purchase_limit_behaviour' => 'Sale limit control',
    'settings.sections.advanced.purchase_limit_behaviour.tooltip' => 'This option allows you to choose how to control the sales limit. It is possible to count the placed or only paid orders (in the second case the limit may be exceeded due to the incoming payments to the placed orders). ',
    'settings.sections.advanced.purchase_limit_behaviour.desc' => '',
    'settings.sections.advanced.purchase_limit_behaviour.option.begin_payment' => 'When placing an order',
    'settings.sections.advanced.purchase_limit_behaviour.option.complete_payment' => 'When payment is posted',

    'settings.sections.advanced.partner_program' => 'Partner Program',
    'settings.sections.advanced.partner_program.tooltip' => '',
    'settings.sections.advanced.partner_program.desc' => '',

    'settings.sections.advanced.partner_program.notice' => 'Change package: To use the affiliate program you need to change your license to PRO.',

    'settings.sections.advanced.partner_program_commission' => 'The amount of the commission',
    'settings.sections.advanced.partner_program_commission.tooltip' => '',
    'settings.sections.advanced.partner_program_commission.desc' => '',

    'settings.sections.advanced.quiz_settings' => 'Quiz Settings',
    'settings.sections.advanced.right_click_blocking_quiz' => 'Block right-click, select and paste',

    'settings.sections.integrations.fieldset.mailing_systems' => 'Mailing systems',

    'settings.sections.integrations.fieldset.mailing_systems.action' => 'Actions',

    'settings.sections.integrations.fieldset.mailing_systems.sync' => 'Data synchronization',
    'settings.sections.integrations.fieldset.mailing_systems.sync.tooltip' => '',
    'settings.sections.integrations.fieldset.mailing_systems.sync.button' => 'Sync now',

    'settings.sections.integrations.fieldset.mailing_systems.pl' => 'Polish-speaking',

    'settings.sections.integrations.fieldset.mailing_systems.en' => 'English-speaking',

    'settings.sections.integrations.getresponse_integration' => 'GetResponse',

    'settings.sections.integrations.getresponse.bpmj_eddres_token' => 'API key',
    'settings.sections.integrations.getresponse.bpmj_eddres_token.desc' => 'Enter your GetResponse API key',

    'settings.sections.integrations.getresponse.bpmj_eddres_show_checkout_signup' => 'Show Signup on Checkout',
    'settings.sections.integrations.getresponse.bpmj_eddres_show_checkout_signup.tooltip' => 'Allow customers to signup for the list selected below during checkout?',

    'settings.sections.integrations.getresponse.bpmj_eddres_list' => 'Choose a list',
    'settings.sections.integrations.getresponse.bpmj_eddres_list.desc' => 'Select the list you wish to subscribe buyers to',

    'settings.sections.integrations.getresponse.bpmj_eddres_list_unsubscribe' => 'Choose a unsubscribe list',
    'settings.sections.integrations.getresponse.bpmj_eddres_list_unsubscribe.desc' => 'Select the list you wish to unsubscribe buyers from',

    'settings.sections.integrations.getresponse.bpmj_eddres_label' => 'Checkout Label',
    'settings.sections.integrations.getresponse.bpmj_eddres_label.desc' => 'This is the text shown next to the signup option',

    'settings.sections.integrations.freshmail_integration' => 'FreshMail',

    'settings.sections.integrations.freshmail.bpmj_eddfm_api' => 'API key',
    'settings.sections.integrations.freshmail.bpmj_eddfm_api.desc' => 'Enter your FreshMail API key',

    'settings.sections.integrations.freshmail.bpmj_eddfm_api_secret' => 'API secret',
    'settings.sections.integrations.freshmail.bpmj_eddfm_api_secret.desc' => 'Enter your FreshMail API secret',

    'settings.sections.integrations.freshmail.bpmj_eddfm_show_checkout_signup' => 'Show Signup on Checkout',
    'settings.sections.integrations.freshmail.bpmj_eddfm_show_checkout_signup.tooltip' => 'Allow customers to signup for the list selected below during checkout?',

    'settings.sections.integrations.freshmail.bpmj_eddfm_group' => 'Choose a list',
    'settings.sections.integrations.freshmail.bpmj_eddfm_group.desc' => 'Select the list you wish to subscribe buyers to',

    'settings.sections.integrations.freshmail.bpmj_eddfm_group_unsubscribe' => 'Choose a unsubscribe list',
    'settings.sections.integrations.freshmail.bpmj_eddfm_group_unsubscribe.desc' => 'Select the list you wish to unsubscribe buyers from',

    'settings.sections.integrations.freshmail.bpmj_eddfm_label' => 'Checkout Label',
    'settings.sections.integrations.freshmail.bpmj_eddfm_label.desc' => 'This is the text shown next to the signup option',

    'settings.sections.integrations.freshmail.bpmj_eddfm_double_opt_in' => 'Double Opt-In',
    'settings.sections.integrations.freshmail.bpmj_eddfm_double_opt_in.tooltip' => 'When checked, users will be sent a confirmation email after signing up, and will only be added once they have confirmed the subscription.',

    'settings.sections.integrations.salesmanago_integration' => 'SalesManago',

    'settings.sections.integrations.salesmanago.salesmanago_owner' => 'SALESmanago account email address',
    'settings.sections.integrations.salesmanago.salesmanago_owner.desc' => 'Email address to which your SAELESmanago account is registered.',

    'settings.sections.integrations.salesmanago.salesmanago_endpoint' => 'Endpoint',
    'settings.sections.integrations.salesmanago.salesmanago_endpoint.desc' => 'Your server identifier (endpoint) from the SALESmanago panel (Settings-> Integration).',

    'settings.sections.integrations.salesmanago.salesmanago_client_id' => 'Client ID',
    'settings.sections.integrations.salesmanago.salesmanago_client_id.desc' => 'Your Client ID from the SALESmanago panel (Settings-> Integration).',

    'settings.sections.integrations.salesmanago.salesmanago_api_secret' => 'API Secret',
    'settings.sections.integrations.salesmanago.salesmanago_api_secret.desc' => 'The Secret API code from the SALESmanago panel (Settings-> Integration).',

    'settings.sections.integrations.salesmanago.salesmanago_tracking_code' => 'Tracking code',
    'settings.sections.integrations.salesmanago.salesmanago_tracking_code.tooltip' => 'Check to include tracking code.',

    'settings.sections.integrations.salesmanago.salesmanago_checkout_mode' => 'Record field',
    'settings.sections.integrations.salesmanago.salesmanago_checkout_mode.tooltip' => 'Check to have the save field shown.',

    'settings.sections.integrations.salesmanago.salesmanago_checkout_label' => 'Description of the write field',
    'settings.sections.integrations.salesmanago.salesmanago_checkout_label.desc' => 'This text will be displayed next to the save option in the cart summary.',

    'settings.sections.integrations.salesmanago.bpmj_eddsm_salesmanago_tags' => 'Tags appended to the user',
    'settings.sections.integrations.salesmanago.bpmj_eddsm_salesmanago_tags.desc' => 'Enter the tags (separated with a comma) to be added to the contact in the SALESmanago panel after each purchase. These tags will only be added if the save box is displayed and checked in the basket summary. Product tags will be added independently.',
    'settings.sections.integrations.salesmanago.bpmj_eddsm_salesmanago_tags.placeholder' => 'Add a tag',

    'settings.sections.integrations.ipresso_integration' => 'iPresso',

    'settings.sections.integrations.ipresso.bpmj_eddip_api_endpoint' => 'Panel URL',
    'settings.sections.integrations.ipresso.bpmj_eddip_api_endpoint.desc' => 'Enter your iPresso panel URL (eg. yourcompany.ipresso.com)',

    'settings.sections.integrations.ipresso.bpmj_eddip_api' => 'API key',
    'settings.sections.integrations.ipresso.bpmj_eddip_api.desc' => 'Enter your iPresso API key',

    'settings.sections.integrations.ipresso.bpmj_eddip_api_login' => 'API login',
    'settings.sections.integrations.ipresso.bpmj_eddip_api_login.desc' => 'Enter your iPresso API login',

    'settings.sections.integrations.ipresso.bpmj_eddip_api_password' => 'API password',
    'settings.sections.integrations.ipresso.bpmj_eddip_api_password.desc' => 'Enter your iPresso API password',

    'settings.sections.integrations.ipresso.bpmj_eddip_show_checkout_signup' => 'Display the opt-in option on the checkout page',
    'settings.sections.integrations.ipresso.bpmj_eddip_show_checkout_signup.tooltip' => 'When this option is selected, customers have the option to subscribe to the selected list when placing an order',

    'settings.sections.integrations.ipresso.bpmj_eddip_tracking_code' => 'Tracking code',
    'settings.sections.integrations.ipresso.bpmj_eddip_tracking_code.desc' => 'Provide iPresso tracking code for this site',

    'settings.sections.integrations.mailchimp_integration' => 'MailChimp',

    'settings.sections.integrations.mailchimp.eddmc_api' => 'API key',
    'settings.sections.integrations.mailchimp.eddmc_api.desc' => 'Enter your API key from the MailChimp system',

    'settings.sections.integrations.mailchimp.eddmc_list' => 'Select a list',
    'settings.sections.integrations.mailchimp.eddmc_list.desc' => 'Select the list to which the buyer should be subscribed when he ticks the checkbox',

    'settings.sections.integrations.mailchimp.eddmc_show_checkout_signup' => 'Display the opt-in option on the checkout page',
    'settings.sections.integrations.mailchimp.eddmc_show_checkout_signup.tooltip' => 'When this option is selected, customers have the option to subscribe to the selected list when placing an order',

    'settings.sections.integrations.mailchimp.eddmc_label' => 'Description of the save options',
    'settings.sections.integrations.mailchimp.eddmc_label.desc' => 'This text will appear next to the option that allows the customer to subscribe to the mailing list in the order form',

    'settings.sections.integrations.mailchimp.eddmc_double_opt_in' => 'Record with confirmation',
    'settings.sections.integrations.mailchimp.eddmc_double_opt_in.tooltip' => 'When this option is selected, a message will be sent to users subscribing to the list asking them to confirm their email address',

    'settings.sections.integrations.mailerlite_integration' => 'MailerLite',

    'settings.sections.integrations.mailerlite.bpmj_edd_ml_api' => 'API Key',
    'settings.sections.integrations.mailerlite.bpmj_edd_ml_api.desc' => 'Enter your API key from MailerLite',

    'settings.sections.integrations.mailerlite.bpmj_edd_ml_group' => 'Select group',
    'settings.sections.integrations.mailerlite.bpmj_edd_ml_group.desc' => 'Select the list to which the buyer should be saved when checkbox is checked',

    'settings.sections.integrations.mailerlite.bpmj_edd_ml_show_checkout_signup' => 'Display the opt-in option on the checkout page',
    'settings.sections.integrations.mailerlite.bpmj_edd_ml_show_checkout_signup.tooltip' => 'When this option is selected, customers have the option to subscribe to the selected list when placing an order',

    'settings.sections.integrations.mailerlite.bpmj_edd_ml_label' => 'Description of saving options',
    'settings.sections.integrations.mailerlite.bpmj_edd_ml_label.tooltip' => 'This text will appear next to the option that allows the customer to subscribe to the mailing list in the order form',

    'settings.sections.integrations.mailerlite.bpmj_edd_ml_double_opt_in' => 'Save with confirmation',
    'settings.sections.integrations.mailerlite.bpmj_edd_ml_double_opt_in.tooltip' => 'When this option is selected, a message will be sent to users subscribing to the list asking them to confirm their email address',

    'settings.sections.integrations.interspire_integration' => 'Interspire',

    'settings.sections.integrations.interspire.bpmj_edd_in_username' => 'Interspire username',
    'settings.sections.integrations.interspire.bpmj_edd_in_username.desc' => 'Enter your Interspire username',

    'settings.sections.integrations.interspire.bpmj_edd_in_token' => 'Token Interspire',
    'settings.sections.integrations.interspire.bpmj_edd_in_token.desc' => 'Enter Interspire token',

    'settings.sections.integrations.interspire.bpmj_edd_in_xmlEndpoint' => 'XML Interspire path',
    'settings.sections.integrations.interspire.bpmj_edd_in_xmlEndpoint.desc' => 'Enter the full XML Interspire path',

    'settings.sections.integrations.interspire.bpmj_edd_in_contact_list' => 'Select contact list',
    'settings.sections.integrations.interspire.bpmj_edd_in_contact_list.desc' => 'Select the list to which the buyer should be saved when he selects the checkbox',

    'settings.sections.integrations.interspire.bpmj_edd_in_show_checkout_signup' => 'Display save to list option on order page',
    'settings.sections.integrations.interspire.bpmj_edd_in_show_checkout_signup.tooltip' => 'When this option is selected, customers can subscribe to the selected list when placing an order',

    'settings.sections.integrations.interspire.bpmj_edd_in_label' => 'Description of saving options',
    'settings.sections.integrations.interspire.bpmj_edd_in_label.desc' => 'This text will appear next to the option that allows the customer to subscribe to the mailing list in the order form',

    'settings.sections.integrations.interspire.bpmj_edd_in_double_opt_in' => 'Save with confirmation',
    'settings.sections.integrations.interspire.bpmj_edd_in_double_opt_in.tooltip' => 'When this option is selected, a message will be sent to users subscribing to the list asking them to confirm their email address',

    'settings.sections.integrations.activecampaign_integration' => 'ActiveCampaign',

    'settings.sections.integrations.activecampaign.bpmj_eddact_api_url' => 'API URL',
    'settings.sections.integrations.activecampaign.bpmj_eddact_api_url.desc' => 'Enter your ActiveCampaign API URL',

    'settings.sections.integrations.activecampaign.bpmj_eddact_api_token' => 'API token',
    'settings.sections.integrations.activecampaign.bpmj_eddact_api_token.desc' => 'Enter your ActiveCampaign API token',

    'settings.sections.integrations.activecampaign.bpmj_eddact_show_checkout_signup' => 'Show Signup on Checkout',
    'settings.sections.integrations.activecampaign.bpmj_eddact_show_checkout_signup.tooltip' => 'Allow customers to signup for the list selected below during checkout?',

    'settings.sections.integrations.activecampaign.bpmj_eddact_list' => 'Choose a list',
    'settings.sections.integrations.activecampaign.bpmj_eddact_list.desc' => 'Select the list you wish to subscribe buyers to',

    'settings.sections.integrations.activecampaign.bpmj_eddact_list_unsubscribe' => 'Choose a unsubscribe list',
    'settings.sections.integrations.activecampaign.bpmj_eddact_list_unsubscribe.desc' => 'Select the list you wish to unsubscribe buyers from',

    'settings.sections.integrations.activecampaign.bpmj_eddact_tag' => 'Choose a tag',
    'settings.sections.integrations.activecampaign.bpmj_eddact_tag.desc' => 'Select the tag you wish to add buyers to',
    'settings.sections.integrations.activecampaign.bpmj_eddact_tag.placeholder' => 'Add a tag',

    'settings.sections.integrations.activecampaign.bpmj_eddact_tag_unsubscribe' => 'Choose a unsubscribe tag',
    'settings.sections.integrations.activecampaign.bpmj_eddact_tag_unsubscribe.desc' => 'Select the tag you wish to remove buyers from',
    'settings.sections.integrations.activecampaign.bpmj_eddact_tag_unsubscribe.placeholder' => 'Add a tag',

    'settings.sections.integrations.activecampaign.bpmj_eddact_label' => 'Checkout Label',
    'settings.sections.integrations.activecampaign.bpmj_eddact_label.desc' => 'This is the text shown next to the signup option',

    'settings.sections.integrations.activecampaign.bpmj_eddact_form_id' => 'Confirmation form',
    'settings.sections.integrations.activecampaign.bpmj_eddact_form_id.desc' => 'Select subscription confirmation form. By doing so you can enable double opt-in for subscribing a user (recommended). Forms are created in administration panel at <b>ActiveCampaign.com / Apps / Add form</b>.',

    'settings.sections.analytics.fieldset.google' => 'Google',
    'settings.sections.analytics.fieldset.facebook' => 'Facebook',
    'settings.sections.analytics.fieldset.additional_scripts' => 'Additional scripts',

    'settings.sections.analytics.ga4_id' => 'Google Analytics 4 ID',
    'settings.sections.analytics.ga4_id.desc' => '',
    'settings.sections.analytics.ga4_id.tooltip' => '',

    'settings.sections.analytics.enable_debug_view_ga4' => 'Enable debug mode',

    'settings.sections.analytics.ga_id' => 'Universal Analytics ID',
    'settings.sections.analytics.ga_id.desc' => '',
    'settings.sections.analytics.ga_id.tooltip' => '',

    'settings.sections.analytics.gtm_id' => 'Google Tag Manager ID',
    'settings.sections.analytics.gtm_id.desc' => '',
    'settings.sections.analytics.gtm_id.tooltip' => '',

    'settings.sections.analytics.pixel_fb_id' => 'Facebook Pixel ID',
    'settings.sections.analytics.pixel_fb_id.desc' => '',
    'settings.sections.analytics.pixel_fb_id.tooltip' => '',

    'settings.sections.analytics.pixel_meta' => 'Pixel Meta',
    'settings.sections.analytics.pixel_meta.desc' => '',
    'settings.sections.analytics.pixel_meta.tooltip' => '',

    'settings.sections.analytics.pixel_meta.popup.title' => 'Pixel Meta',
    'settings.sections.analytics.pixel_meta.popup.additional_information' => 'In order to configure the Conversion API correctly both of the fields below must be filled.',

    'settings.sections.analytics.pixel_meta.access_token' => 'Access token',
    'settings.sections.analytics.pixel_meta.access_token.desc' => '',
    'settings.sections.analytics.pixel_meta.access_token.tooltip' => '',

    'settings.sections.analytics.before_end_head' => 'Scripts before &lt;/head&gt;',
    'settings.sections.analytics.before_end_head.desc' => '',
    'settings.sections.analytics.before_end_head.tooltip' => '',
    'settings.sections.analytics.before_end_head.popup.title' => 'Scripts before &lt;/head&gt;',
    'settings.sections.analytics.before_end_head_additional' => 'Scripts before &lt;/head&gt;',
    'settings.sections.analytics.before_end_head_additional.desc' => "E.g. &lt;script&gt;alert('Example alert')&lt;/script&gt;",
    'settings.sections.analytics.before_end_head_additional.tooltip' => '',

    'settings.sections.analytics.after_begin_body' => 'Scripts after &lt;body&gt;',
    'settings.sections.analytics.after_begin_body.desc' => '',
    'settings.sections.analytics.after_begin_body.tooltip' => '',
    'settings.sections.analytics.after_begin_body.popup.title' => 'Scripts after &lt;body&gt;',
    'settings.sections.analytics.after_begin_body_additional' => 'Scripts after &lt;body&gt;',
    'settings.sections.analytics.after_begin_body_additional.desc' => "E.g. &lt;script&gt;alert('Example alert')&lt;/script&gt;",
    'settings.sections.analytics.after_begin_body_additional.tooltip' => '',

    'settings.sections.analytics.before_end_body' => 'Scripts before &lt;/body&gt;',
    'settings.sections.analytics.before_end_body.desc' => '',
    'settings.sections.analytics.before_end_body.tooltip' => '',
    'settings.sections.analytics.before_end_body.popup.title' => 'Scripts before &lt;/body&gt;',
    'settings.sections.analytics.before_end_body_additional' => 'Scripts before &lt;/body&gt;',
    'settings.sections.analytics.before_end_body_additional.desc' => "E.g. &lt;script&gt;alert('Example alert')&lt;/script&gt;",
    'settings.sections.analytics.before_end_body_additional.tooltip' => '',

    'settings.sections.integrations.convertkit_integration' => 'ConvertKit',

    'settings.sections.integrations.convertkit.edd_convertkit_api' => 'API key',
    'settings.sections.integrations.convertkit.edd_convertkit_api.desc' => 'Enter your ConvertKit API key',

    'settings.sections.integrations.convertkit.edd_convertkit_api_secret' => 'API secret',
    'settings.sections.integrations.convertkit.edd_convertkit_api_secret.desc' => 'Enter your ConvertKit API secret',

    'settings.sections.integrations.convertkit.edd_convertkit_show_checkout_signup' => 'Allow customers to subscribe to a selected list',
    'settings.sections.integrations.convertkit.edd_convertkit_show_checkout_signup.tooltip' => 'Allow customers to signup for the list selected below during checkout?',

    'settings.sections.integrations.convertkit.edd_convertkit_list' => 'Select a list',
    'settings.sections.integrations.convertkit.edd_convertkit_list.desc' => 'Select the form you wish to subscribe buyers to. The form can also be selected on a per-product basis from the product edit screen',

    'settings.sections.integrations.convertkit.edd_convertkit_label' => 'Description of the save options',
    'settings.sections.integrations.convertkit.edd_convertkit_label.desc' => 'This is the text shown next to the signup option',

    'settings.sections.integrations.fieldset.invoicing_systems' => 'Invoicing systems',

    'settings.sections.integrations.fakturownia_integration' => 'Fakturownia',

    'settings.sections.integrations.fakturownia.apikey' => 'API key / Account name',
    'settings.sections.integrations.fakturownia.apikey.desc' => 'Fakturownia.pl -> Account settings -> Integration -> See API Tokens -> Add New Token -> Add Token with prefix',

    'settings.sections.integrations.fakturownia.departments_id' => 'Company ID',
    'settings.sections.integrations.fakturownia.departments_id.desc' => 'In Fakturownia.pl -> Settings -> Company details, click on the company / department and the department ID will appear in the URL. If this field is left blank, your company default data will be inserted ',

    'settings.sections.integrations.fakturownia.auto_sent' => 'Automatic invoice sending',
    'settings.sections.integrations.fakturownia.auto_sent.desc' => 'Select if invoices are to be sent automatically by e-mail to the client. Full activation of the Fakturownia.pl system is required',

    'settings.sections.integrations.fakturownia.auto_sent_receipt' => 'Automatic dispatch of receipts',
    'settings.sections.integrations.fakturownia.auto_sent_receipt.desc' => 'Check if the receipts are to be sent automatically by e-mail to the client. Full activation of the Fakturownia.pl system is required',

    'settings.sections.integrations.fakturownia.receipt' => 'Also issue receipts',

    'settings.sections.integrations.fakturownia.vat_exemption' => 'Text to be inserted in the invoice (invoice without VAT)',
    'settings.sections.integrations.fakturownia.vat_exemption.default' => 'Basis for VAT exemption: art. 43 paragraph. 1 p. 28',

    'settings.sections.integrations.ifirma_integration' => 'iFirma',

    'settings.sections.integrations.ifirma.ifirma_ifirma_email' => 'Email from iFirma system',
    'settings.sections.integrations.ifirma.ifirma_ifirma_email.desc' => 'Enter the email you use to log in to the iFirma system panel',

    'settings.sections.integrations.ifirma.ifirma_ifirma_invoice_key' => 'Invoice API key',
    'settings.sections.integrations.ifirma.ifirma_ifirma_invoice_key.desc' => 'iFirma.pl -> Tools -> API',

    'settings.sections.integrations.ifirma.ifirma_ifirma_subscriber_key' => 'subscriber API key',
    'settings.sections.integrations.ifirma.ifirma_ifirma_subscriber_key.desc' => 'iFirma.pl -> Tools -> API',

    'settings.sections.integrations.ifirma.ifirma_vat_exemption' => 'Basis for VAT exemption',
    'settings.sections.integrations.ifirma.ifirma_vat_exemption.value' => 'Art. 113 paragraph. 1 ',

    'settings.sections.integrations.ifirma.ifirma_auto_sent' => 'Autoship',
    'settings.sections.integrations.ifirma.ifirma_auto_sent.desc' => 'Select, if the sales documents are to be sent automatically by e-mail to the customer',

    'settings.sections.integrations.wfirma_integration' => 'wFirma',

    'settings.sections.integrations.wfirma.wfirma_auth_type' => 'Authorization type',
    'settings.sections.integrations.wfirma.wfirma_auth_type.desc' => '',
    'settings.sections.integrations.wfirma.wfirma_auth_type.tooltip' => 'Select  type. OAuth2 is recommended, because Basic authorization will not be suppored after 2023-06.',

    'settings.sections.integrations.wfirma.option.wfirma_auth_type.basic' => 'Basic (supported up to end of 2023-06)',
    'settings.sections.integrations.wfirma.option.wfirma_auth_type.oauth2' => 'OAuth2',

    'settings.sections.integrations.wfirma.wfirma_wf_login' => 'Login',
    'settings.sections.integrations.wfirma.wfirma_wf_login.desc' => 'Login (email address) to the wfirma.pl system',

    'settings.sections.integrations.wfirma.wfirma_wf_pass' => 'Password',
    'settings.sections.integrations.wfirma.wfirma_wf_pass.desc' => 'Password to the wfirma.pl system',

    'settings.sections.integrations.wfirma.oauth2_message1' => 'In order to generate the following data, necessary to integrate Publigo with the wFirma system, please read the <a href="https://poznaj.publigo.pl/articles/229095-konfiguracja-integracji-z-wfirma" target="_blank">article in our knowledge base</a><br/>Adres zwrotny - <b>{url}</b>, adres IP - <b>{ip}</b>.',
    'settings.sections.integrations.wfirma.oauth2_message1.ip_error' => '[Error getting IP, refresh page]',

	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_client_id' => 'Client ID',
	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_client_id.desc' => '',

	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_client_secret' => 'Client password',
	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_client_secret.desc' => '',

    'settings.sections.integrations.wfirma.oauth2_message2' => 'Click the button below to generate an Authorization code. Attention! Publigo will redirect you to the wFirma system. This is an essential part of the process.',

    'settings.sections.integrations.wfirma.oauth2_button_redir' => 'Authorization code generation',
    'settings.sections.integrations.wfirma.oauth2_button_redir.value' => 'Generate',

	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_authorization_code' => 'Authorization code',
	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_authorization_code.desc' => '',

    'settings.sections.integrations.wfirma.wfirma_wf_company_id' => 'Company ID',
    'settings.sections.integrations.wfirma.wfirma_wf_company_id.desc' => 'Leave the field blank when you have one company in wFirma',

    'settings.sections.integrations.wfirma.wfirma_receipt' => 'Also issue receipts',

    'settings.sections.integrations.wfirma.wfirma_auto_sent' => 'Automatic invoice dispatch',
    'settings.sections.integrations.wfirma.wfirma_auto_sent.desc' => 'Select, if invoices and bills are to be sent automatically by e-mail to the customer',

    'settings.sections.integrations.wfirma.wfirma_auto_sent_receipt' => 'Automatic receipt delivery',
    'settings.sections.integrations.wfirma.wfirma_auto_sent_receipt.desc' => 'Select, if the receipts are to be sent automatically by e-mail to the client',

    'settings.sections.integrations.taxe_integration' => 'Taxe',

    'settings.sections.integrations.taxe.taxe_taxe_login' => 'Login',

    'settings.sections.integrations.taxe.taxe_taxe_api_key' => 'API Key',
    'settings.sections.integrations.taxe.taxe_taxe_api_key.desc' => 'CRM -> API services',

    'settings.sections.integrations.taxe.taxe_vat_exemption' => 'Basis for VAT exemption',
    'settings.sections.integrations.taxe.taxe_vat_exemption.value' => 'Art. 113 paragraph. 1 ',
    'settings.sections.integrations.taxe.taxe_vat_exemption.default' => 'Art. 113 paragraph 1 or paragraph 9',
    
    'settings.sections.integrations.taxe.taxe_receipt' => 'Also issue receipts',

    'settings.sections.integrations.taxe.taxe_auto_sent' => 'Automatic invoice sending',
    'settings.sections.integrations.taxe.taxe_auto_sent.desc' => 'Select if invoices and bills are to be sent automatically by e-mail to the client. <br /> <strong> Note: </strong> make sure you have <a href="https://panel.taxe.com/email-template/"> Email templates </a> set default template for the activity "Send document in e-mail"',

    'settings.sections.integrations.taxe.taxe_auto_sent_receipt' => 'Automatic dispatch of receipts',
    'settings.sections.integrations.taxe.taxe_auto_sent_receipt.desc' => 'Check if the receipts are to be sent automatically by e - mail to the client . <br /> <strong > Note: </strong > make sure you have < a href = "https://panel.taxe.com/email-template/"> Email templates </a > set default template for the activity "Send document in e-mail"',

    'settings.sections.integrations.infakt_integration' => 'Infakt',

    'settings.sections.integrations.infakt.infakt_infakt_api_key' => 'API Key',
    'settings.sections.integrations.infakt.infakt_infakt_api_key.desc' => 'Settings -> Other options -> API',

    'settings.sections.integrations.infakt.infakt_vat_exemption' => 'Basis for VAT exemption',
    'settings.sections.integrations.infakt.infakt_vat_exemption.value' => 'Art. 113 paragraph. 1 ',

    'settings.sections.integrations.infakt.infakt_auto_sent' => 'Automatic invoice dispatch',
    'settings.sections.integrations.infakt.infakt_auto_sent.desc' => 'Select, if the sales documents are to be sent automatically by e-mail to the customer',

    'settings.sections.design.list_excerpt.label' => 'Show short description',
    'settings.sections.design.list_excerpt.label.tooltip' => 'Activate to have a short description appear next to products. It will be visible before purchase. You can set the description by going to <b> Courses> Edit the selected course> Product> Edit product description> Enter the description and save the changes. </b> ',
    'settings.sections.design.list_buy_button.label' => 'Show purchase button',
    'settings.sections.design.list_buy_button.label.tooltip' => 'Activate to have a purchase button next to the product.',
    'settings.sections.design.list_pagination.label' => 'Show Pagination',
    'settings.sections.design.list_pagination.label.tooltip' => 'Activate to have pagination at the bottom of the page. You can define how many courses are to appear on one page by going to <b> Settings> Templates> Edit Active Template> Edit "Course List" template> Click "Course List"> Specify the number of products per page. </b> ',
    'settings.sections.design.display_categories.label' => 'Show categories',
    'settings.sections.design.display_categories.label.tooltip' => 'Activate to display categories under the course title.',
    'settings.sections.design.show_available_quantities.label' => 'Show available quantities',
    'settings.sections.design.show_available_quantities.tooltip' => 'Activate to show the number of available products on the home page',
    'settings.sections.design.show_available_quantities.package_notice' => 'Upgrade to PLUS or PRO to get this functionality.',

    'settings.sections.design.available_quantities_format.label' => 'Format',
    'settings.sections.design.available_quantities_format.format_x_of_y' => 'Available: X of Y',
    'settings.sections.design.available_quantities_format.format_x' => 'Available quantities: X',

    'settings.sections.design.display_tags.label' => 'Show tags',
    'settings.sections.design.display_tags.label.tooltip' => 'Activate to display tags under the course title .',
    'settings.sections.design.default_view.label' => 'Default View',
    'settings.sections.design.default_view.label.tooltip' => 'Select the view for the page: product catalog.',
    'settings.sections.design.default_view.grid' => 'Grid',
    'settings.sections.design.default_view.grid_small' => 'Small Grid',
    'settings.sections.design.default_view.list' => 'List',
    'settings.sections.design.progress_tracking.label' => 'Tracking your progress',
    'settings.sections.design.progress_tracking.label.tooltip' => 'Activate to enable the course tracker. If the student clicks " Completed " after completing the lesson, it will be recorded in the statistics. This option can be overridden individually for each course in its settings. ',
    'settings.sections.design.auto_progress.label' => 'Automatic progress',
    'settings.sections.design.auto_progress.label.tooltip' => 'Activate to enable automatic progress . The lessons will be automatically marked as completed when the user clicks the "Next lesson" button. ',
    'settings.sections.design.display_author_info.label' => 'Course author',
    'settings.sections.design.display_author_info.label.tooltip' => 'Activate to display the course author in the course panel.',
    'settings.sections.design.responsive_video.label' => 'Responsive video',
    'settings.sections.design.responsive_video.label.tooltip' => 'Activate to enable responsive video.',
    'settings.sections.design.progress_forced.label' => 'Progressive Access',
    'settings.sections.design.progress_forced.label.tooltip' => 'Activate to enable progressive access. The student will have to mark the lesson as processed in order to proceed to the next one. This option can be overridden individually for each course in its settings. ',
    'enabled' => 'Enabled',
    'disabled' => 'Disabled',
    'asc' => 'Growing',
    'desc' => 'Descending',
    'settings.sections.design.fieldset.course_view_settings' => 'Course view settings',
    'settings.sections.design.fieldset.directory_settings' => 'Directory settings',
    'settings.sections.design.list_price.label' => 'Show the price.',
    'settings.sections.design.list_price.label.tooltip' => 'Activate to show the price in the product catalog.',
    'settings.sections.design.inaccessible_lesson_display.label' => 'Displaying unavailable lessons',
    'settings.sections.design.inaccessible_lesson_display.label.tooltip' => 'Define how unavailable lessons should be displayed.',
    'settings.sections.design.inaccessible_lesson_display.visible' => 'Always visible',
    'settings.sections.design.inaccessible_lesson_display.grayed' => 'Visible, dimmed',
    'settings.sections.design.inaccessible_lesson_display.hidden' => 'Hidden',
    'settings.sections.design.navigation_next_lesson_label.label' => 'Label for the next lesson',
    'settings.sections.design.navigation_next_lesson_label.label.tooltip' => 'Select a label for the next lesson.',
    'settings.sections.design.navigation_next_lesson_label.text_previous' => '"Next lesson" text',
    'settings.sections.design.navigation_next_lesson_label.title_previous' => 'Next lesson title',
    'settings.sections.design.navigation_next_lesson_label.text_next' => 'Text "Previous lesson"',
    'settings.sections.design.navigation_next_lesson_label.title_next' => 'Previous lesson title',
    'settings.sections.design.navigation_previous_lesson_label.label' => 'Previous lesson label',
    'settings.sections.design.navigation_previous_lesson_label.label.tooltip' => 'Select a label for the previous lesson.',
    'settings.sections.design.popup.label' => 'Label settings for the next and previous lesson',
    'settings.sections.design.popup.label.tooltip' => 'Here you can configure labels for the next and the previous lesson.',
    'settings.sections.design.course_view_settings.label' => 'Course View Settings',
    'settings.sections.design.list_sort_type.label' => 'Sort type',
    'settings.sections.design.list_orderby.label' => 'Sort By',
    'settings.sections.design.list_orderby.label.tooltip' => 'Choose how to sort the products on the page.',
    'settings.sections.design.list_orderby.post_date' => 'Course publication date',
    'settings.sections.design.list_sort_type.label.tooltip' => 'Select sort type.',
    'settings.sections.design.list_orderby.id' => 'Course ID',
    'settings.sections.design.list_orderby.title' => 'Course name',
    'settings.sections.design.list_orderby.price' => 'Course price',
    'settings.sections.design.list_orderby.random' => 'Random',
    'settings.sections.design.list_orderby.custom' => 'Custom order',
    'settings.sections.design.list_orderby.custom.no_access' => 'Custom order (available in PLUS/PRO)',
    'settings.sections.design.list_details_button.label' => 'Show full description button',
    'settings.sections.design.list_details_button.label.tooltip' => 'Activate to have the "Read more" button displayed next to the course description.',

    'settings.sections.design.custom_order_table.column.priority' => 'Order',
    'settings.sections.design.custom_order_table.column.product_name' => 'Product',
    'settings.sections.design.custom_order_table.column.actions' => 'Actions',
    'settings.sections.design.custom_order_table.column.select_product' => 'Select product',
    'settings.sections.design.custom_order_table.button.add_product' => 'Add product',
    'settings.sections.design.custom_order_table.button.save' => 'Save',
    'settings.sections.design.custom_order_table.message.saving' => 'Saving ...',
    'settings.sections.design.custom_order_table.button.cancel' => 'Cancel',
    'settings.sections.design.custom_order_table.message.you_have_unsaved_changes' => 'you have unsaved changes!',
    'settings.sections.design.custom_order_table.message.be_careful' => 'Watch out',
    'settings.sections.design.custom_order_table.message.selected_products' => 'Selected products',
    'settings.sections.design.custom_order_table.message.no_selected_products' => 'No products selected',
    'settings.sections.design.custom_order_table.message.save_success' =>'The settings have been saved!',
    'settings.sections.design.custom_order_table.message.save_error' => 'An error occurred while saving. Please contact the administrator.',
    'settings.sections.design.custom_order_table.message.product_id' => 'Product ID',
    'settings.sections.design.custom_order_table.message.move_up' => 'Move up',
    'settings.sections.design.custom_order_table.message.move_down' => 'Move down',
    'settings.sections.design.custom_order_table.message.move_to_top' => 'Move to top',
    'settings.sections.design.custom_order_table.message.move_to_bottom' => 'Move to bottom',

    'settings.sections.cart.data_in_form' => 'Data in the form',
    'settings.sections.cart.show_email2_on_checkout' => 'Email verification',
    'settings.sections.cart.show_email2_on_checkout.tooltip' => 'Activate to enable email verification. An additional field will appear to enter your email address. ',
    'settings.sections.cart.edd_id_hide_fname' => 'Hide the surname field',
    'settings.sections.cart.additional_checkboxes' => 'Additional checkboxes',
    'settings.sections.cart.sidebar' => 'Sidebar',
    'settings.sections.cart.show_comment_field' => 'Enable the field for additional comment',
    'settings.sections.cart.show_comment_field.tooltip' => 'Activate to enable the field for additional comment.',
    'settings.sections.cart.fieldset.statute' => 'Statute',
    'settings.sections.cart.agree_label' => 'Description of the acceptance checkbox ',
    'settings.sections.cart.agree_label.tooltip' => 'Add your own description or leave a default description ("I accept the terms of purchase (necessary to place an order")','settings.sections.cart.agree_label' => 'Description of the rules acceptance field',
    'settings.sections.cart.info_1_title' => 'Title of field 1 of additional information',
    'settings.sections.cart.info_1_title.tooltip' => 'Add the title of the first additional information field.',
    'settings.sections.cart.info_1_desc' => 'Description of field 1 for additional information',
    'settings.sections.cart.info_1_desc.tooltip' => 'Add a description of the first additional information field.',
    'settings.sections.cart.cart_popup_2' => 'First section of additional information',
    'settings.sections.cart.cart_popup_2.tooltip' => 'Here you will configure the first section of additional information. You can add here e.g. a Satisfaction Guarantee. ',
    'settings' => 'Settings',
    'settings.sections.cart.info_2_title' => 'Title of field 2 of additional information',
    'settings.sections.cart.info_2_title.tooltip' => 'Add a title for the second additional information field.',
    'settings.sections.cart.info_2_desc' => 'Description of field 2 for additional information',
    'settings.sections.cart.info_2_desc.tooltip' => 'Add a description of the second additional information field.',
    'settings.sections.cart.cart_popup_3' => 'Second section of additional information',
    'settings.sections.cart.cart_popup_3.tooltip' => 'Here you will configure the second section of additional information. You can add here e.g. Safe connection ',
    'settings.sections.cart.acd' => 'Description of an additional checkbox',
    'settings.sections.cart.acd.tooltip' => 'Add checkbox description .',
    'settings.sections.cart.acdr' => 'Additional checkbox required',
    'settings.sections.cart.acdr.tooltip' => 'Activate, if the checkbox is to be obligatory to be accepted.',
    'settings.sections.cart.acd2' => 'Description of an additional checkbox',
    'settings.sections.cart.acd2.tooltip' => 'Add checkbox description .',
    'settings.sections.cart.acdr2' => 'Additional checkbox required',
    'settings.sections.cart.acdr2.tooltip' => 'Activate, if the checkbox is to be obligatory to be accepted.',
    'settings.sections.cart.ac' => 'Checkbox 1',
    'settings.sections.cart.ac.tooltip' => 'Here you can configure the first checkbox .',
    'settings.sections.cart.ac2' => 'Checkbox 2',
    'settings.sections.cart.ac2.tooltip' => 'Here you can configure the second checkbox .',
    'settings.sections.cart.last_name_required' => 'Require surname',
    'settings.sections.cart.hide_fname' => 'Hide the name field',
    'settings.sections.cart.hide_fname.tooltip' => 'Activate to hide the name field.',
    'settings.sections.cart.hide_lname' => 'Hide the surname field',
    'settings.sections.cart.hide_lname.tooltip' => 'Activate to hide the surname field',
    'settings.sections.cart.enable_field_phone' => 'Enable the phone number field',
    'settings.sections.cart.enable_field_phone.tooltip' => 'Activate to enable an additional field for the phone number.',
    'settings.sections.cart.phone_required' => 'Phone number required',

    'settings.sections.messages.external_news' => 'External messages',

    'settings.sections.messages.fieldset.sender' => 'Sender',

    'settings.sections.messages.sender.from_name' => 'Sender name',
    'settings.sections.messages.sender.from_name.tooltip' => 'Your first and last name, website or platform name that will appear in the sender field in the messages you send.',

    'settings.sections.messages.sender.from_email' => 'Sender email',
    'settings.sections.messages.sender.from_email.tooltip' => 'Email address from which messages will be sent. Note that in order for the messages to be sent to the recipients, this address should be in the same domain as the store domain! Additionally, it is worth considering integrating the platform with the emaillabs.pl system.',

    'settings.sections.messages.sender_info' => 'Warning! If you change the e-mail address below, be sure to report it to us by sending us a message (zapytaj@publigo.pl) from the e-mail address you use to contact us. Otherwise, messages coming from your platform will stop working.',

    'settings.sections.messages.fieldset.message_after_purchase' => ' Message sent after purchase',

    'settings.sections.messages.message_after_purchase.purchase_subject' => 'Message subject',
    'settings.sections.messages.message_after_purchase.purchase_subject.tooltip' => 'Enter the title of the message sent to the buyer after the payment for the course has been credited.',

    'settings.sections.messages.message_after_purchase.purchase_heading' => 'Message header',
    'settings.sections.messages.message_after_purchase.purchase_heading.tooltip' => 'Enter the header of the message sent to the buyer after the payment for the course has been credited.',

    'settings.sections.messages.message_after_purchase.purchase_receipt_popup' => 'Message content',
    'settings.sections.messages.message_after_purchase.purchase_receipt_popup.tooltip' => 'Here you can configure the content of the message sent after the payment for the course has been posted.',

    'settings.sections.messages.message_after_purchase.purchase_receipt' => 'Content',
    'settings.sections.messages.message_after_purchase.purchase_receipt.tooltip' => 'Enter the text of the message sent after the payment for the course has been booked. HTML tags and tags may be used in the content.',
    'settings.sections.messages.message_after_purchase.purchase_receipt.desc' => 'Enter the text that is sent as purchase receipt email to users after completion of a successful purchase. HTML is accepted. Available template tags:<br><code>{download_list}</code> - A list of download links for each download purchased<br><code>{file_urls}</code> - A plain-text list of download URLs for each download purchased<br><code>{name}</code> - The buyer first name<br><code>{fullname}</code> - The buyer full name, first and last<br><code>{username}</code> - The buyer user name on the site, if they registered an account<br><code>{user_email}</code> - The buyer email address<br><code>{billing_address}</code> - The buyer billing address<br><code>{date}</code> - The date of the purchase<br><code>{subtotal}</code> - The price of the purchase before taxes<br><code>{tax}</code> - The taxed amount of the purchase<br><code>{price}</code> - The total price of the purchase<br><code>{payment_id}</code> - The unique ID number for this purchase<br><code>{receipt_id}</code> - The unique ID number for this purchase receipt<br><code>{payment_method}</code> - The method of payment used for this purchase<br><code>{sitename}</code> - Your site name<br><code>{receipt_link}</code> - Adds a link so users can view their receipt directly on your website if they are unable to view it in the browser correctly.<br><code>{discount_codes}</code> - Adds a list of any discount codes applied to this purchase<br><code>{ip_address}</code> - The buyer IP Address<br><code>{generated_discount_codes_details}</code> - Displays list of generated discount codes with details<br><code>{generated_discount_codes}</code> - Displays list of generated discount codes separated with comma<br>',

    'settings.sections.messages.fieldset.message_after_creating_account' => 'Message sent after account creation',

    'settings.sections.messages.message_after_creating_account.bpmj_edd_arc_subject' => 'Message subject',
    'settings.sections.messages.message_after_creating_account.bpmj_edd_arc_subject.tooltip' => 'Add subject of message sent after account creation.',

    'settings.sections.messages.message_after_creating_account.edd_arc_content_popup' => 'Message content',
    'settings.sections.messages.message_after_creating_account.edd_arc_content_popup.tooltip' => 'Here you can configure the content of the message sent after creating an account.',

    'settings.sections.messages.message_after_creating_account.bpmj_edd_arc_content' => 'Message content',
    'settings.sections.messages.message_after_creating_account.bpmj_edd_arc_content.tooltip' => 'Enter the text of the message sent after creating an account.',
	'settings.sections.messages.message_after_creating_account.bpmj_edd_arc_content.desc' => 'Available template tags:<br><code>{firstname}</code>- The buyer first name<br><code>{login}</code>- The buyer user name on the site, if they registered an account<br><<code>{password}</code>- The buyer default password<br><code>{password_reset_link}</code>- Password reset link<br></p>',

    'settings.sections.messages.messages_subscription' => 'Messages for subscription',

    'settings.sections.messages.fieldset.discount_codes' => 'Discount codes',

    'settings.sections.messages.discount_codes.bpmj_renewal_discount' => 'Enable discount codes in the message',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount.tooltip' => 'Activate this option if you want discount codes to be generated. This code can be added to the content of the reminder. ',

    'settings.sections.messages.discount_codes.bpmj_renewal_discount_value' => 'Discount code value.',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_value.tooltip' => 'Select the value of the discount code.',

    'settings.sections.messages.discount_codes.bpmj_renewal_discount_type' => 'Discount code type.',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_type.tooltip' => 'Select the type of discount code - percentage or amount.',

    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time' => 'The period of validity of the discount code',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.tooltip' => 'Select how long the discount code should be valid (counted from the moment it was generated).',

    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.one_day' => 'One day',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.two_days' => 'Two days',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.three_days' => 'Three days',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.five_days' => 'Five days',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.week' => 'One Week',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.two_weeks' => 'Two weeks',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.month' => 'One Month',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.no_limit' => 'No limit time',

    'settings.sections.messages.fieldset.reports' => 'Reports',

    'settings.sections.messages.reports.bpmj_expired_access_report_email' => 'Report e-mail',
    'settings.sections.messages.reports.bpmj_expired_access_report_email.tooltip' => 'A report about expired user subscriptions will be sent to this email every day. Leave blank if you do not want to receive such a report. ',

    'settings.sections.messages.fieldset.reminders' => 'Reminders',

    'settings.sections.messages.reminders.paid_content_renewal' => 'Reminder',
    'settings.sections.messages.reminders.paid_content_renewal.tooltip' => 'Set reminders for content expiration time by clicking on the <b> Add Renewal Reminder button. </b> You can design a renewal reminder message there. After saving, all data will appear in the adjacent table. The reminder can be set for courses that have a specific access time. ',

    'settings.sections.messages.reminder_hours.reminder_hours' => 'Reminder hours',
    'settings.sections.messages.reminder_hours.reminder_hours.tooltip' => 'At what times should notifications be sent? The minimum interval is 5 hours. ',

    'settings.sections.messages.reminder_hours.bpmj_renewals_start' => 'From',
    'settings.sections.messages.reminder_hours.bpmj_renewals_start.tooltip' => 'Select the hourly interval in which renewal reminder messages will be sent.',

    'settings.sections.messages.reminder_hours.bpmj_renewals_end' => 'To',
    'settings.sections.messages.reminder_hours.bpmj_renewals_end.tooltip' => 'Select the hourly interval in which renewal reminder messages will be sent.',

    'settings.sections.messages.admin_sale_notification.title.default' => 'Order no #{payment_id}',
    'settings.sections.messages.admin_sale_notification.message.default' => 'Hello!
    
A new order has just been paid.
    
Below are the details of the transaction.
    
Ordered products:

{download_list}

Full name: {fullname}
E-mail: {user_email}
Amount: {price}
Order no: {payment_id}
Date: {date}
Payment method: {payment_method}

Buyer comment: {comment}

-- 
Powered by Publigo',

    'settings.sections.modules.fieldset.product_types' => 'Product types',
    'settings.sections.modules.fieldset.sales_and_marketing' => 'Sales and marketing',

    'settings.sections.modules.courses_enable' => 'Courses',
    'settings.sections.modules.courses_enable.tooltip' => 'When enabled, you will be able to sell courses.',
    'settings.sections.modules.courses_enable.notice' => 'To disable this functionality, delete all courses.',

    
    'settings.sections.modules.enable_digital_products' => 'Digital products',
    'settings.sections.modules.enable_digital_products.tooltip' => 'After enabling you will be able to sell files such as e-books, audiobooks, etc.',
    'settings.sections.modules.enable_digital_products.disable_notice' => 'To disable this functionality, you need to delete all digital products.',
    
    'settings.sections.modules.enable_physical_products' => 'Physical products',
    'settings.sections.modules.enable_physical_products.tooltip' => 'After enabling you will be able to sell physical products.',
    'settings.sections.modules.enable_physical_products.disable_notice' => 'To disable this functionality, you need to delete all physical products.',

    'settings.sections.modules.services_enabled' => 'Services',
    'settings.sections.modules.services_enabled.tooltip' => 'After enabling you will be able to sell services.',
    'settings.sections.modules.services_enabled.disable_notice' => 'To disable this functionality, delete all services.',

    'settings.sections.modules.increasing_sales_enabled' => 'Increasing sales',
    'settings.sections.modules.increasing_sales_enabled.tooltip' => 'When enabled, you will be able to create sales-increasing marketing offers.',
    'settings.sections.modules.increasing_sales_enabled.notice' => 'To use this functionality, you need to upgrade your license to level:% s',

    'settings.sections.modules.fieldset.communication' => 'Communication',

    'settings.sections.modules.enable_opinions' => 'Opinions',
    'settings.sections.modules.enable_opinions.tooltip' => 'Once enabled, customers will be able to leave feedback for the purchased course.',

    'settings.sections.modules.opinions_rules' => 'Rule URL',
    'settings.sections.modules.opinions_rules.attention' => 'In order to fully activate the review system, it is necessary to enter the URL to the review rules in Configure.',
    'settings.sections.modules.opinions_rules.info' => 'Do not have an opinion policy? Go to safe.biz and generate it in a few minutes.
                                                         See the special offer only for Publigo customers! Link: <a href="https://bit.ly/bbiz-pbg-reviews" target="_blank">https://bit.ly/bbiz-pbg-reviews</a>',

    'settings.sections.certificate.fieldset.certificate' => 'Certificates',

    'settings.sections.certificate.enable_certificates' => 'Certificates',

    'settings.sections.certificate.new_certificate.notice' => 'Certificates are available in %s.',

    'settings.sections.certificate.enable_new_version_certificates_popup.notice' => 'Attention! You are using an old version of certificates that is no longer being developed. Switch to new certificates to be able to visually edit patterns, create multiple templates, numbering certificates etc. ',

    'settings.sections.certificate.enable_new_version_certificates_popup' => 'Switch to new certificates',

    'settings.sections.certificate.certificate_templates' => 'Certificate Templates',
    'settings.sections.gifts.enable' => "Gift purchases",
    'settings.sections.gifts.email_body' => 'Message sent after purchase',
    'settings.sections.gifts.expiration_number' => '',
    'settings.sections.gifts.fieldset.voucher_as_pdf' => 'Voucher as PDF',
    'settings.sections.gifts.generate_pdf' => 'Generate the voucher as a PDF file',
    'settings.sections.gifts.voucher_bg' => 'Voucher background',
    'settings.sections.gifts.voucher_orientation' => 'Voucher orientation',
    'portrait' => 'Vertical',
    'landscape' => 'Horizontal',
    'settings.sections.gifts.voucher_template' => 'PDF voucher template',
    'settings.sections.gifts.voucher_template.desc' => 'Define the template on the basis of which the PDF voucher will be generated. You can use HTML tags in your content and use the following tags: <br> <strong> <code> {voucher_code} </code> - Voucher code </strong> <br> <strong> <code> {voucher_expiration_date} </ code > - Voucher expiry date </strong> <br> <strong> <code> {redeem_link} </code> - Link leading directly to the order form with the voucher entered </strong> <br> <strong> <code> {product_name } </code> - Product name </strong> <br> <code> {name} </code> - Buyer name <br> <code> {fullname} </code> - Buyer name and surname <br> < code> {username} </code> - Username assigned to the buyer, if registered. <br> <code> {user_email} </code> - Buyer email address <br> <code> {date} </ code > - Order date <br> <code> {payment_id} </code> - Unique order number <br> <code> {receipt_id} </code> - Unique invoice number <br> <code> {sitename} </ code > - Your site name <br> <code> {generated_discount_codes_details} </code> - Displays a list of discount codes purchased with all information <br> <code > {generated_discount_codes} </code> - Displays purchased discount codes separated by a comma <br> <code> {invoice_type} </code> - Invoice type <br> <code> {invoice_person_name} </code> - Name <br> < code> {invoice_company_name} </code> - Name <br> <code> {invoice_buyer_name} </code> - Name <br> <code> {invoice_nip} </code> - Tax ID: <code> {invoice_street} </code> - Street <br> <code> {invoice_postcode} </code> - Postal code <br> <code> {invoice_city} </code> - City',
    'settings.sections.gifts.voucher_css' => 'PDF voucher css style',
    'settings.sections.gifts.voucher_css.desc' => 'Here you can define CSS styles for the voucher. For example, you can define a graphic that will appear in the background. ',
    'hours' => 'Hours',
    'days' => 'Days',
    'weeks' => 'Tygody',
    'months' => 'Months',
    'years' => 'Years',
    'settings.sections.gifts.expiration' => 'Default voucher validity time',
    'settings.sections.gifts.unit' => 'Unit',
    'settings.sections.gifts.period' => 'Period',

    'settings.sections.gifts.period.validation' => 'The given value cannot be empty and less than 0',

    'settings.sections.cart.cart_view' => 'Cart view',
    'settings.sections.cart.cart_view.standard' => 'Standard',
    'settings.sections.cart.cart_view.experimental' => 'Experimental',
    'settings.sections.cart.cart_view.go_back_button' => 'Custom button (experimental view)',
    'settings.sections.cart.cart_view.go_back_button_text' => 'Custom Button text',
    'settings.sections.cart.cart_view.go_back_button_url' => 'Custom Button url',

    'settings.package_notice.pro' => 'To use this functionality, you need to upgrade your license to PRO package.',

    'help.diagnostics.memory_limit' => 'memory_limit',
    'help.diagnostics.memory_limit.fix_hint' => 'Change your php.ini configuration',
    'help.diagnostics.memory_limit.solve_hint' => 'Make sure memory limit value is set to at least 256MB.',

    'template_name.search_results' => 'Search results page',
    'template_name.search_results.description' => 'The template of the page where the search engine block is located and the search results are displayed',
    'template_name.search_page' => 'Search results page',
    'template_name.search_page.description' => 'The template of the page where the search engine block is located and the search results are displayed',
    'template_name.cart_page' => 'Cart Page Template',
    'template_name.cart_page.description' => 'A shopping cart page template, where, among other things, an order form is also displayed',
	'template_name.experimental_cart_page' => 'Cart Page Template',
	'template_name.experimental_cart_page.description' => 'A shopping cart page template, where, among other things, an order form is also displayed (experimental view)',
	'template_name.category_page' => 'Category Page Template',
    'template_name.category_page.description' => 'Category page template, which displays all products assigned to a given category',
    'template_name.course_lesson_page' => 'Course Lesson Template',
    'template_name.course_lesson_page.description' => 'A website template for any of the lessons of the course',
    'template_name.course_module_page' => 'Course Module Template',
    'template_name.course_module_page.description' => 'A website template for any of the course modules',
    'template_name.course_offer_page' => 'Course Offer Page Template',
    'template_name.course_offer_page.description' => 'Product description page template visible before purchase - for users who are not logged in',
    'template_name.course_panel_page' => 'Course Panel Template',
    'template_name.course_panel_page.description' => 'Template of the homepage of a given course with a list of available modules / lessons',
    'template_name.course_quiz_page' => 'Course Quiz Template',
    'template_name.course_quiz_page.description' => 'Template of the page where the quiz / tests block is located',
    'template_name.products_page' => 'List of products',
    'template_name.products_page.description' => 'Template of the default home page of the platform, compiling the list of products currently on sale',
    'template_name.tag_page' => 'Tag Page Template',
    'template_name.tag_page.description' => 'Tag page template that displays all products with a given tag',
    'template_name.user_account_page' => 'User Account Template',
    'template_name.user_account_page.description' => 'The template of the My Account page, which collects information important from the perspective of the user / customer',

    'templates_list.page_title' => 'Templates list',
    'templates_list.popup_title' => 'Templates settings',
    'templates_list.column.layout_type' => 'Layout type',
    'templates_list.column.layout_description' => 'Description',
    'templates_list.actions.edit' => 'Edit',
    'templates_list.actions.colors_settings' => 'Colors settings',
    'templates_list.actions.settings' => 'Settings',
    'templates_list.actions.restore.active' => 'Restore',
    'templates_list.actions.restore.confirm_message' => 'Are you sure you want to restore this layout?',

    'search_results.block_name' => 'Search',
    'search_results.page_title' => 'Searching',
    'search_results.type_to_search' => 'Enter the phrase you want to search for',
    'search_results.search' => 'Search',
    'search_results.you_will_see_results_here' => 'Your search results will appear here.',
    'search_results.no_search_results' => 'No search results.',
    'search_results.no_search_results.try_other_phrase' => 'Try to enter a different phrase',
    'search_results.results_count' => 'The number of results that match the criteria',
    'alert.account_deleted' => 'Account deleted.',

    'course_list.page_title' => 'Courses',
    'course_list.column.id' => 'ID',
    'course_list.column.name' => 'Name',
    'course_list.column.show' => 'Show',
    'course_list.column.sales' => 'Sales',
    'course_list.sales.status.enabled' => 'Enabled',
    'course_list.sales.status.disabled' => 'Disabled',
    'course_list.column.sales_limit_status' => 'Sales limit',
    'course_list.sales_limit_status.available' => 'Available: %s',

    'course_list.actions.create_course' => 'Create a new course',
    'course_list.actions.edit' => 'Edit',
    'course_list.actions.duplicate' => 'Duplicate',
    'course_list.actions.duplicate.loading' => 'Duplicating...',
    'course_list.column.delete' => 'Delete',
    'course_list.actions.delete.loading' => 'Deleting ...',
    'course_list.actions.delete.confirm' => 'Are you sure you want to delete the selected course? This action is irreversible.',
    'course_list.actions.delete.bulk' => 'Delete',
    'course_list.actions.delete.bulk.confirm' => 'Are you sure you want to delete selected courses? This action is irreversible.',
    'course_list.actions.sales.bulk' => 'Enable / disable sales',
    'course_list.actions.sales.active' => 'Enable sales',
    'course_list.actions.sales.inactive' => 'Disable sales',
    'course_list.actions.sales.loading' => 'I change...',

    'course_list.popup.close' => 'Close',
    'course_list.popup.purchase_links.title' => 'Purchase Links',
    'course_list.popup.course_stats.title' => 'Statistics',

    'course_list.buttons.course_panel.tooltip' => 'Course panel',
    'course_list.buttons.course_stats.tooltip' => 'Statistics',
    'course_list.buttons.course_students.tooltip' => 'Participants',
    'course_list.buttons.purchase_links.tooltip' => 'Purchase Links',
    'course_list.buttons.expiring_customers.tooltip' => 'Ending Users',

    'course_list.stats.lesson' => 'Lesson',
    'course_list.stats.passed' => 'Passed',

    'expiring_customers.page_title' => 'Expiring access',
    'expiring_customers.column.name' => 'Name',
    'expiring_customers.column.email' => 'E-mail',
    'expiring_customers.column.access_to' => 'Access to',
    'expiring_customers.column.course' => 'Course',
    'expiring_customers.access_to.unlimited' => 'Unlimited',

    'digital_products_list.page_title' => 'Digital products',
    'digital_products_list.column.id' => 'ID',
    'digital_products_list.column.name' => 'Digital product name',
    'digital_products_list.column.show' => 'Show',
    'digital_products_list.column.sales' => 'Sales',

    'digital_products_list.actions.create_digital_product' => 'Create a new digital product',
    'digital_products_list.actions.edit' => 'Edit',
    'digital_products_list.actions.duplicate' => 'Duplicate',
    'digital_products_list.actions.delete' => 'Delete',
    'digital_products_list.actions.delete.success' => 'The digital product has been successfully removed!',
    'digital_products_list.actions.delete.loading' => 'Deleting ...',
    'digital_products_list.actions.delete.confirm' => 'Are you sure you want to delete the selected digital product? This action is irreversible.',
    'digital_products_list.actions.sales.bulk' => 'Enable / disable sales',
    'digital_products_list.actions.sales.active' => 'Enable sales',
    'digital_products_list.actions.sales.inactive' => 'Disable sales',
    'digital_products_list.actions.sales.loading' => 'I change...',
    'digital_products_list.actions.delete.error' => 'An error occurred while deleting. Please contact the site administrator.',
    'digital_products_list.actions.delete.info' => 'You cannot remove this product because it is assigned to at least one package. Please remove it from all packages, then try the removal again. ',
    'digital_products_list.buttons.digital_product_panel.tooltip' => 'View digital product',

    'digital_products_list.popup.close' => 'Close',
    'digital_products_list.popup.save' => 'Save',
    'digital_products_list.popup.purchase_links.title' => 'Shopping Links',
    'digital_products_list.buttons.purchase_links.tooltip' => 'Shopping Links',

    'digital_products_list.sales.status.enabled' => 'Enabled',
    'digital_products_list.sales.status.disabled' => 'Disabled',

    'users.page_title' => 'Users',
    'users.menu_title' => 'Users',
    'users.column.id' => 'ID',
    'users.column.name' => 'Username',
    'users.column.full_name' => 'Name',
    'users.column.email' => 'E-mail',
    'users.column.roles' => 'Role',

    'users.column.role.administrator' => 'Administrator',
    'users.column.role.lms_admin' => 'Publigo Manager',
    'users.column.role.lms_support' => 'Publigo Support',
    'users.column.role.editor' => 'Editor',
    'users.column.role.author' => 'Author',
    'users.column.role.contributor' => 'Contributor',
    'users.column.role.subscriber' => 'Subscriber',
    'users.column.role.lms_accountant' => 'Publigo Accountant',
    'users.column.role.lms_content_manager' => 'Publigo content manager',
    'users.column.role.lms_partner' => 'Publigo Partner',
    'users.column.role.lms_assistant' => 'Publigo Testing and Certification Assistant',

    'users.actions.edit' => 'Edit',
    'users.actions.delete' => 'Delete',
    'users.actions.send_link' => 'Send password reset link',
    'users.actions.added_user' => 'Add a new user',
    'users.actions.loading' => 'Please wait...',

    'users.actions.send_link.many_users.success' => 'Password reset link has been sent to% s users.',
    'users.actions.send_link.success' => 'Password reset link sent.',
    'users.actions.added_user.success' => 'New user account has been successfully created.',
    'users.actions.delete.success' => 'Account has been deleted.',

    'product_editor.sections.general.price.validation' => 'The given value cannot be empty and less than 0.',
    'product_editor.sections.general.sale_price.validation' => 'The given value cannot be empty and less than 0. Enter 0 if you want to disable the promotional price.',
    'product_editor.sections.general.purchase_limit.validation' => 'The given value cannot be empty and less than 0. Enter 0 to disable the limit',
    'product_editor.sections.general.purchase_limit_items_left.validation' => 'The given value cannot be empty and less than 0.',
    'product_editor.sections.general.featured_image.attachment_must_exist' => 'URL must be an existing attachment address',
    'product_editor.sections.invoices.vat_rate.validation' => 'The given value must be greater than or equal to 0. The maximum number of characters is 2',
    'product_editor.sections.invoices.vat_rate.validation.max_length' => 'The given value is incorrect.',
    'product_editor.product_does_not_exist' => 'The product does not exist or the given ID is incorrect.',

    'service_editor.add_service' => 'Add service',
    'service_editor.page_title' => 'Service Edit',
    'service_editor.preview_button' => 'Preview service',

    'service_editor.sections.general' => 'Basic',

    'service_editor.sections.general.fieldset.name' => 'Name and description',

    'service_editor.sections.general.name' => 'Service Name',

    'service_editor.sections.general.description' => 'Description',
    'service_editor.sections.general.description.desc' => 'Configure the product description, categories, tags, URL extension and featured image.',

    'service_editor.sections.general.short_description' => 'Short description',

    'service_editor.sections.general.categories' => 'Categories',
    'service_editor.sections.general.select_categories' => 'Select categories',

    'service_editor.sections.general.url' => 'URL extension',
    'service_editor.sections.general.tags' => 'Tags',
    'service_editor.sections.general.add_tags' => 'Add new tag',
    'service_editor.sections.general.add_tags.desc' => 'Separate with commas or enter.',

    'service_editor.sections.general.fieldset.price' => 'Price',
    'service_editor.sections.general.price.tooltip' => 'How much does it cost to access the course? Enter 0 if you want to share the course for free. ',

    'service_editor.sections.general.fieldset.location' => 'Location',

    'service_editor.sections.general.special_offer' => 'Promotion',

    'service_editor.sections.general.sale_price' => 'Promotion price',
    'service_editor.sections.general.sale_price.tooltip' => 'What is the promotional price for this course? Enter 0 if you want to disable the promotional price.',

    'service_editor.sections.general.sale_price_date_from' => 'Promotion start',
    'service_editor.sections.general.sale_price_date_from.tooltip' => 'The moment of activating and deactivating the promotion may in practice be delayed up to 5 minutes compared to the set time',

    'service_editor.sections.general.sale_price_date_to' => 'End of promotion',
    'service_editor.sections.general.sale_price_date_to.tooltip' => 'The moment of activating and deactivating the promotion may in practice be delayed up to 5 minutes compared to the set time',

    'service_editor.sections.general.fieldset.quantities_available' => 'Quantities available',
    'service_editor.sections.general.purchase_limit' => 'Total number of items to be purchased',
    'service_editor.sections.general.purchase_limit.desc' => 'Enter 0 or leave it blank to disable the limit',

    'service_editor.sections.general.purchase_limit_items_left' => 'Pcs left',

    'service_editor.sections.general.fieldset.graphics' => 'Graphics',

    'service_editor.sections.general.banner' => 'Banner',
    'service_editor.sections.general.featured_image' => 'Product image',

    'service_editor.sections.general.fieldset.sale' => 'Sale',

    'service_editor.sections.general.sales_disabled' => 'Turn on sale',
    'service_editor.sections.general.sales_disabled.tooltip' => 'By checking this option you will enable customers to purchase this product',

    'service_editor.sections.general.hide_from_lists' => 'Show the service in the directory',
    'service_editor.sections.general.hide_purchase_button' => 'Show purchase button',
    'service_editor.sections.general.hide_purchase_button.tooltip' => 'This option will show a buy button on the product page',

    'service_editor.sections.general.promote_course' => 'Promote service on home page',
    'service_editor.sections.general.recurring_payments_enabled' => 'Enable Recurring Payments',
    'service_editor.sections.general.recurring_payments' => 'Recurring payments',
    'service_editor.sections.general.recurring_payments_interval' => 'Time interval',
    'service_editor.sections.general.recurring_payments_interval.desc' => 'Set the time interval between recurring payments for this item.',

    'service_editor.sections.general.payments_unit.option.days' => 'Days',
    'service_editor.sections.general.payments_unit.option.weeks' => 'Weeks',
    'service_editor.sections.general.payments_unit.option.months' => 'Months',
    'service_editor.sections.general.payments_unit.option.years' => 'Years',

    'service_editor.sections.link_generator.message_1' => 'Using the link generator, you can prepare a link that not only immediately adds the product to the cart, but also applies a discount code or activates the gift purchase option. You can place such a link anywhere, e.g. on the sales page under the Buy Now button.',

    'service_editor.sections.invoices.fieldset.general' => 'Accounting settings',

    'service_editor.sections.invoices.no_gtu' => 'No GTU code',
    'service_editor.sections.invoices.gtu' => 'GTU code',
    'service_editor.sections.invoices.gtu.not_supported_for' => 'Warning! GTU via API is not supported for:',
    'service_editor.sections.invoices.flat_rate_tax_symbol.not_supported_for' => 'Attention! Flat rate tax is not supported by the system:',

    'service_editor.sections.invoices.flat_rate_tax_symbol' => 'Flat rate tax symbol',
    'service_editor.sections.invoices.no_tax_symbol' => 'No flat rate tax',

    'service_editor.sections.invoices.vat_rate' => 'VAT rate',

    'service_editor.sections.link_generator.fieldset.general' => 'Links generator',
    'service_editor.sections.link_generator.link_generator' => 'Links generator',

    'service_editor.sections.link_generator.variable_prices.price' => 'Price',
    'service_editor.sections.link_generator.variable_prices.copy' => 'Copy',
    'service_editor.sections.link_generator.variable_prices.copied' => 'Copied',

    'service_editor.sections.link_generator' => 'Link generator',
    'service_editor.sections.invoices' => 'Invoices',
    'service_editor.sections.mailings' => 'Mailing systems',
    'service_editor.sections.discount_code' => 'Discount code',

    'service_editor.sections.mailings.fieldset.popup.mailings' => 'Mailing systems',

    'service_editor.sections.mailings.empty_lists' => 'Incorrect configuration or missing lists.',

    'service_editor.sections.mailings.mailchimp' => 'MailChimp',
    'service_editor.sections.mailings.popup.mailchimp' => 'Select the lists',
    'service_editor.sections.mailings.popup.mailchimp.desc' => 'Select lists to which the buyer should be subscribed when he pays for access to the service',

    'service_editor.sections.mailings.mailerlite' => 'MailerLite',
    'service_editor.sections.mailings.popup.mailerlite' => 'Select lists',
    'service_editor.sections.mailings.popup.mailerlite.desc' => 'Select lists to which the buyer should be subscribed when he pays for access to the service',

    'service_editor.sections.mailings.freshmail' => 'FreshMail',
    'service_editor.sections.mailings.popup.freshmail' => 'Select lists',
    'service_editor.sections.mailings.popup.freshmail.desc' => 'Select lists to which the buyer should be saved when he pays for access to the service',

    'service_editor.sections.mailings.ipresso' => 'ActiveCampaign',
    'service_editor.sections.mailings.popup.ipresso_tags' => 'Add tags',
    'service_editor.sections.mailings.popup.ipresso_tags.desc' => 'Add tags (separated by commas) which will be <strong> added </strong> to your iPresso contacts after the purchase is completed.',

    'service_editor.sections.mailings.popup.ipresso_tags_unsubscribe' => 'Add tags',
    'service_editor.sections.mailings.popup.ipresso_tags_unsubscribe.desc' => 'Add tags (separated by commas) which will be <strong> removed </strong> from contacts in iPresso after purchase.',

    'service_editor.sections.mailings.activecampaign' => 'ActiveCampaign',
    'service_editor.sections.mailings.popup.activecampaign' => 'Select Lists',
    'service_editor.sections.mailings.popup.activecampaign.desc' => 'Select the lists that the buyer should <strong> subscribe </strong> to when they pay for access to the service.',

    'service_editor.sections.mailings.popup.activecampaign_unsubscribe' => 'Select Lists',
    'service_editor.sections.mailings.popup.activecampaign_unsubscribe.desc' => 'Select the lists from which the buyer is to be <strong>unsubscribed</strong> when he pays for access to the service.',

    'service_editor.sections.mailings.popup.activecampaign_tags' => 'Add tags',
    'service_editor.sections.mailings.popup.activecampaign_tags.desc' => 'Add tags (separated by commas) to be <strong> added </strong> to your ActiveCampaign contacts after purchase is complete.',

    'service_editor.sections.mailings.popup.activecampaign_tags_unsubscribe' => 'Add tags',
    'service_editor.sections.mailings.popup.activecampaign_tags_unsubscribe.desc' => 'Add tags (separated by commas) to be <strong> removed </strong> from ActiveCampaign contacts after purchase is complete.',

    'service_editor.sections.mailings.getresponse' => 'GetResponse',
    'service_editor.sections.mailings.popup.getresponse' => 'Select lists',
    'service_editor.sections.mailings.popup.getresponse.desc' => 'Select the lists to which the buyer should <strong> subscribe </strong> when he pays for access to the service.',

    'service_editor.sections.mailings.popup.getresponse_unsubscribe' => 'Select lists',
    'service_editor.sections.mailings.popup.getresponse_unsubscribe.desc' => 'Select the lists from which the buyer should <strong> unsubscribe </strong> when he pays for access to the service.',

    'service_editor.sections.mailings.popup.getresponse_tags' => 'Select tags',
    'service_editor.sections.mailings.popup.getresponse_tags.desc' => 'Select tags to which buyers should be added when shopping.',

    'service_editor.sections.mailings.salesmanago' => 'SalesManago',
    'service_editor.sections.mailings.popup.salesmanago_tags' => 'Add tags',
    'service_editor.sections.mailings.popup.salesmanago_tags.desc' => 'Enter tags (separating them with a comma) to be added to the contact in the SALESmanago panel after purchasing this product.',

    'service_editor.sections.mailings.interspire' => 'Interspire',
    'service_editor.sections.mailings.popup.interspire' => 'Select Lists',
    'service_editor.sections.mailings.popup.interspire.desc' => 'Select the lists to which the buyer should be subscribed when he pays for access to the service',

    'service_editor.sections.mailings.convertkit' => 'ConvertKit',
    'service_editor.sections.mailings.popup.convertkit' => 'Select Lists',
    'service_editor.sections.mailings.popup.convertkit.desc' => 'Select the lists to which the buyer should be subscribed when he pays for access to the service',

    'service_editor.sections.mailings.popup.convertkit_tags' => 'Select tags',
    'service_editor.sections.mailings.popup.convertkit_tags.desc' => 'Select tags to which buyers should be <strong> added </strong> when shopping.',

    'service_editor.sections.mailings.popup.convertkit_tags_unsubscribe' => 'Select tags',
    'service_editor.sections.mailings.popup.convertkit_tags_unsubscribe.desc' => 'Select tags from which buyers should be <strong> removed </strong> when shopping.',

    'service_editor.sections.mailings.select_list' => 'Select list or group',
    'service_editor.sections.mailings.add_next' => 'Add next',

    'service_editor.sections.discount_code.message' => 'Together with this product you can sell a discount code which will be generated on the basis of the previously created one. You can set its expiry date. ',
    'service_editor.sections.discount_code.fieldset.discount_code' => 'Discount codes',

    'service_editor.sections.discount_code.code_pattern' => 'Select a pattern code',
    'service_editor.sections.discount_code.code_pattern.desc' => 'Based on it, we will generate a new code after paying for the order.',

    'service_editor.sections.discount_code.code_time' => 'Validity period',
    'service_editor.sections.discount_code.code_time.desc' => 'This parameter is optional. By default, the discount code never expires. ',
    'service_editor.sections.discount_code.code_time.validation' => 'The given value cannot be less than 0.',
    'service_editor.sections.discount_code.code_time.validation.must_be_a_number' => 'The given value must be a number.',
    'service_editor.sections.discount_code.code_time.validation.must_not_be_empty' => 'You must select one of the options in the field above.',

    'service_editor.sections.discount_code.code_pattern.no_code.label' => 'No coupons to use',

    'service_editor.sections.discount_code.code_type.option.duration' => 'Duration',
    'service_editor.sections.discount_code.code_type.option.days' => 'Days',
    'service_editor.sections.discount_code.code_type.option.weeks' => 'Weeks',
    'service_editor.sections.discount_code.code_type.option.months' => 'Months',

    'product_editor.popup.button.cancel' => 'Cancel',
    'product_editor.popup.button.save' => 'Create and edit',
    'product_editor.popup.button.saving' => 'Saving ...',

    'product_editor.popup.field.error.empty' => 'The field cannot be left blank.',
    'product_editor.popup.field.error.price' => 'The given value cannot be empty and less than 0.',

    'product_editor.popup.save.error' => 'An error occurred while saving. Please contact the administrator.',
    'product_editor.sections.general.variable_pricing' => 'Pricing variants',
    'product_editor.sections.general.variable_pricing_single_price' => 'Various variants in cart',
    'product_editor.sections.general.variable_pricing_single_price.tooltip' => 'Enable multiple options. It allows you to add to the cart various price options for the product in one order.',

    'product_editor.sections.general.variable_prices' => 'Pricing variants',
    'product_editor.sections.general.variable_prices.edit_variants' => 'Edit variants',

    'product_editor.sections.general.variable_prices.table.head.id' => 'ID',
    'product_editor.sections.general.variable_prices.table.head.name' => 'Variant name',
    'product_editor.sections.general.variable_prices.table.head.price' => 'Price (PLN)',
    'product_editor.sections.general.variable_prices.table.head.availability' => 'Availability (pcs)',
    'product_editor.sections.general.variable_prices.table.head.access_on' => 'Access to',
    'product_editor.sections.general.variable_prices.table.head.recurring_payments' => 'Recurring payments',
    'product_editor.sections.general.variable_prices.table.head.interval' => 'Interval',

    'product_editor.sections.general.variable_prices.table.body.recurring_payments_enabled.yes' => 'Yes',
    'product_editor.sections.general.variable_prices.table.body.recurring_payments_enabled.no' => 'No',

    'product_editor.sections.general.variable_prices.table.body.set_price_variants' => 'Set price variants by clicking the button below',
    'product_editor.sections.general.variable_prices.edit.table.purchase_limit_items_left' => 'Pieces left',
    'product_editor.sections.general.variable_prices.edit.table.purchase_limit' => 'Total items available',

    'product_editor.sections.general.variable_prices.edit.table.message.success' => 'Price variants have been successfully saved.',
    'product_editor.sections.general.variable_prices.edit.table.message.error' => 'The price field must be filled in!',

    'product_editor.sections.general.access_time_unit.option.minutes' => 'Minutes',
    'product_editor.sections.general.access_time_unit.option.hours' => 'Hours',
    'product_editor.sections.general.access_time_unit.option.days' => 'Days',
    'product_editor.sections.general.access_time_unit.option.months' => 'Months',
    'product_editor.sections.general.access_time_unit.option.years' => 'Years',

    'service_popup_editor.popup.field.name' => 'Service Name',
    'service_popup_editor.popup.field.name.placeholder' => 'Enter service name',

    'service_popup_editor.popup.field.price' => 'Price',
    'service_popup_editor.popup.field.price.placeholder' => 'Enter service price',
    'service_popup_editor.popup.field.price.tooltip' => 'How much does it cost to access the service? Enter 0 if you want to provide the service for free. ',

    'support.rules.before_contacting' => 'Before contacting support, make sure that the answer you are looking for is not in one of the places indicated below, and that none of the following sources did solve the issue you were interested in.',

    'digital_products.actions.create_digital_product' => 'Create a new digital product',

    'digital_products_popup_editor.popup.field.name' => 'Digital product name',
    'digital_products_popup_editor.popup.field.name.placeholder' => 'Enter digital product name',
    'digital_products_popup_editor.popup.field.price.tooltip' => 'How much does it cost to access a digital product? Enter 0 if you want to make the digital product available for free. ',

    'digital_products_popup_editor.popup.field.price' => 'Price',
    'digital_products_popup_editor.popup.field.price.placeholder' => 'Enter digital product price',

    'digital_product_editor.add_digital_product' => 'Add a digital product',
    'digital_product_editor.page_title' => 'Editing a digital product',
    'digital_product_editor.preview_button' => 'View digital product',

    /* digital product editor - general tab */
    'digital_product_editor.sections.general' => 'Basic',

    'digital_product_editor.sections.general.fieldset.name' => 'Name and description',

    'digital_product_editor.sections.general.name' => 'Digital product name',

    'digital_product_editor.sections.general.description' => 'Description',
    'digital_product_editor.sections.general.description.desc' => 'Configure the product description, categories, tags, URL extension and featured image.',

    'digital_product_editor.sections.general.short_description' => 'Short description',

    'digital_product_editor.sections.general.categories' => 'Categories',
    'digital_product_editor.sections.general.select_categories' => 'Select categories',

    'digital_product_editor.sections.general.url' => 'URL extension',
    'digital_product_editor.sections.general.tags' => 'Tags',
    'digital_product_editor.sections.general.add_tags' => 'Add new tag',
    'digital_product_editor.sections.general.add_tags.desc' => 'Separate with commas or enter.',

    'digital_product_editor.sections.general.fieldset.price' => 'Price',
    'digital_product_editor.sections.general.price.validation' => 'The given value cannot be empty and cannot be less than 0. Enter 0 if you want to provide the digital product for free.',
    'digital_product_editor.sections.general.price.tooltip' => 'How much does it cost to access a digital product? Enter 0 if you want to make the digital product available for free. ',

    'digital_product_editor.sections.general.fieldset.location' => 'Location',

    'digital_product_editor.sections.general.special_offer' => 'Promotion',

    'digital_product_editor.sections.general.sale_price' => 'Promotion price',
    'digital_product_editor.sections.general.sale_price.tooltip' => 'What is the promotional price for this course? Enter 0 if you want to disable the promotional price.',
    'digital_product_editor.sections.general.sale_price.validation' => 'The given value cannot be empty and less than 0. Enter 0 if you want to disable the promotional price.',

    'digital_product_editor.sections.general.sale_price_date_from' => 'Promotion start',
    'digital_product_editor.sections.general.sale_price_date_from.tooltip' => 'The moment of activating and deactivating the promotion may in practice be delayed up to 5 minutes compared to the set time',

    'digital_product_editor.sections.general.sale_price_date_to' => 'End of promotion',
    'digital_product_editor.sections.general.sale_price_date_to.tooltip' => 'The moment of activating and deactivating the promotion may in practice be delayed up to 5 minutes compared to the set time',

    'digital_product_editor.sections.general.fieldset.quantities_available' => 'Quantities available',
    'digital_product_editor.sections.general.purchase_limit' => 'Total number of items to be purchased',
    'digital_product_editor.sections.general.purchase_limit.desc' => 'Enter 0 or leave it blank to disable the limit',
    'digital_product_editor.sections.general.purchase_limit.validation' => 'The given value cannot be empty and less than 0. Enter 0 to disable the limit',

    'digital_product_editor.sections.general.purchase_limit_items_left' => 'Pcs left',
    'digital_product_editor.sections.general.purchase_limit_items_left.validation' => 'The given value cannot be empty and less than 0.',

    'digital_product_editor.sections.general.fieldset.graphics' => 'Graphics',

    'digital_product_editor.sections.general.banner' => 'Banner',
    'digital_product_editor.sections.general.featured_image' => 'Product image',
    'digital_product_editor.sections.general.featured_image.attachment_must_exist' => 'The URL must be the address of an existing attachment',

    'digital_product_editor.sections.general.fieldset.sale' => 'Sale',

    'digital_product_editor.sections.general.sales_disabled' => 'Turn on sale',
    'digital_product_editor.sections.general.sales_disabled.tooltip' => 'By checking this option you enable customers to purchase this product',

    'digital_product_editor.sections.general.hide_from_lists' => 'Show the digital product in the directory',
    'digital_product_editor.sections.general.hide_purchase_button' => 'Show purchase button',
    'digital_product_editor.sections.general.hide_purchase_button.tooltip' => 'This option will show a buy button on the product page',

    'digital_product_editor.sections.general.promote_course' => 'Promote digital product on home page',
    'digital_product_editor.sections.general.recurring_payments_enabled' => 'Enable Recurring Payments',
    'digital_product_editor.sections.general.recurring_payments' => 'Recurring payments',
    'digital_product_editor.sections.general.recurring_payments_interval' => 'Time interval',
    'digital_product_editor.sections.general.recurring_payments_interval.desc' => 'Set the time interval between recurring payments for this item.',

    'digital_product_editor.sections.general.payments_unit.option.days' => 'Days',
    'digital_product_editor.sections.general.payments_unit.option.weeks' => 'Weeks',
    'digital_product_editor.sections.general.payments_unit.option.months' => 'Months',
    'digital_product_editor.sections.general.payments_unit.option.years' => 'Years',

    /* digital product editor - mailings tab */
    'digital_product_editor.sections.mailings.fieldset.popup.mailings' => 'Mailing systems',

    'digital_product_editor.sections.mailings.empty_lists' => 'Incorrect configuration or missing lists.',

    'digital_product_editor.sections.mailings.mailchimp' => 'MailChimp',
    'digital_product_editor.sections.mailings.popup.mailchimp' => 'Select the lists',
    'digital_product_editor.sections.mailings.popup.mailchimp.desc' => 'Select lists to which the buyer should be subscribed when he pays for access to the digital product',

    'digital_product_editor.sections.mailings.mailerlite' => 'MailerLite',
    'digital_product_editor.sections.mailings.popup.mailerlite' => 'Select lists',
    'digital_product_editor.sections.mailings.popup.mailerlite.desc' => 'Select lists to which the buyer should be subscribed when he pays for access to the digital product',

    'digital_product_editor.sections.mailings.freshmail' => 'FreshMail',
    'digital_product_editor.sections.mailings.popup.freshmail' => 'Select lists',
    'digital_product_editor.sections.mailings.popup.freshmail.desc' => 'Select lists to which the buyer should be saved when he pays for access to the digital product',

    'digital_product_editor.sections.mailings.ipresso' => 'ActiveCampaign',
    'digital_product_editor.sections.mailings.popup.ipresso_tags' => 'Add tags',
    'digital_product_editor.sections.mailings.popup.ipresso_tags.desc' => 'Add tags (separated by commas) which will be <strong> added </strong> to your iPresso contacts after the purchase is completed.',

    'digital_product_editor.sections.mailings.popup.ipresso_tags_unsubscribe' => 'Add tags',
    'digital_product_editor.sections.mailings.popup.ipresso_tags_unsubscribe.desc' => 'Add tags (separated by commas) which will be <strong> removed </strong> from contacts in iPresso after purchase.',

    'digital_product_editor.sections.mailings.activecampaign' => 'ActiveCampaign',
    'digital_product_editor.sections.mailings.popup.activecampaign' => 'Select Lists',
    'digital_product_editor.sections.mailings.popup.activecampaign.desc' => 'Select the lists that the buyer should <strong> subscribe </strong> to when they pay for access to the digital product.',

    'digital_product_editor.sections.mailings.popup.activecampaign_unsubscribe' => 'Select Lists',
    'digital_product_editor.sections.mailings.popup.activecampaign_unsubscribe.desc' => 'Select the lists from which the buyer is to be <strong>unsubscribed</strong> when he pays for access to the digital product.',

    'digital_product_editor.sections.mailings.popup.activecampaign_tags' => 'Add tags',
    'digital_product_editor.sections.mailings.popup.activecampaign_tags.desc' => 'Add tags (separated by commas) to be <strong> added </strong> to your ActiveCampaign contacts after purchase is complete.',

    'digital_product_editor.sections.mailings.popup.activecampaign_tags_unsubscribe' => 'Add tags',
    'digital_product_editor.sections.mailings.popup.activecampaign_tags_unsubscribe.desc' => 'Add tags (separated by commas) to be <strong> removed </strong> from ActiveCampaign contacts after purchase is complete.',

    'digital_product_editor.sections.mailings.getresponse' => 'GetResponse',
    'digital_product_editor.sections.mailings.popup.getresponse' => 'Select lists',
    'digital_product_editor.sections.mailings.popup.getresponse.desc' => 'Select the lists to which the buyer should <strong> subscribe </strong> when he pays for access to the digital product.',

    'digital_product_editor.sections.mailings.popup.getresponse_unsubscribe' => 'Select lists',
    'digital_product_editor.sections.mailings.popup.getresponse_unsubscribe.desc' => 'Select the lists from which the buyer should <strong> unsubscribe </strong> when he pays for access to the digital product.',

    'digital_product_editor.sections.mailings.popup.getresponse_tags' => 'Select tags',
    'digital_product_editor.sections.mailings.popup.getresponse_tags.desc' => 'Select tags to which buyers should be added when shopping.',

    'digital_product_editor.sections.mailings.salesmanago' => 'SalesManago',
    'digital_product_editor.sections.mailings.popup.salesmanago_tags' => 'Add tags',
    'digital_product_editor.sections.mailings.popup.salesmanago_tags.desc' => 'Enter tags (separating them with a comma) to be added to the contact in the SALESmanago panel after purchasing this product.',

    'digital_product_editor.sections.mailings.interspire' => 'Interspire',
    'digital_product_editor.sections.mailings.popup.interspire' => 'Select Lists',
    'digital_product_editor.sections.mailings.popup.interspire.desc' => 'Select the lists to which the buyer should be subscribed when he pays for access to the digital product',

    'digital_product_editor.sections.mailings.convertkit' => 'ConvertKit',
    'digital_product_editor.sections.mailings.popup.convertkit' => 'Select Lists',
    'digital_product_editor.sections.mailings.popup.convertkit.desc' => 'Select the lists to which the buyer should be subscribed when he pays for access to the digital product',

    'digital_product_editor.sections.mailings.popup.convertkit_tags' => 'Select tags',
    'digital_product_editor.sections.mailings.popup.convertkit_tags.desc' => 'Select tags to which buyers should be <strong> added </strong> when shopping.',

    'digital_product_editor.sections.mailings.popup.convertkit_tags_unsubscribe' => 'Select tags',
    'digital_product_editor.sections.mailings.popup.convertkit_tags_unsubscribe.desc' => 'Select tags from which buyers should be <strong> removed </strong> when shopping.',

    'digital_product_editor.sections.mailings.select_list' => 'Select list or group',
    'digital_product_editor.sections.mailings.add_next' => 'Add next',

    /* digital product editor - files tab */
    'digital_product_editor.sections.files' => 'Files',
    'digital_product_editor.sections.files.info' => 'Below you can add various types of files (pdf, audio, documents, sheets, graphics, etc.) that will be made available to the customer after the purchase. It is also possible to define unique names for them and arrange them in a specific order.',

    'digital_product_editor.sections.files.table.column.priority' => 'Order',
    'digital_product_editor.sections.files.table.column.file_name' => 'File name',
    'digital_product_editor.sections.files.table.column.file_url' => 'File URL',
    'digital_product_editor.sections.files.table.button.browse_media' => 'Select file',
    'digital_product_editor.sections.files.table.button.add_file' => 'Add File',
    'digital_product_editor.sections.files.table.button.save' => 'Save',
    'digital_product_editor.sections.files.table.message.saving' => 'Saving ...',
    'digital_product_editor.sections.files.table.button.cancel' => 'Cancel',
    'digital_product_editor.sections.files.table.message.you_have_unsaved_changes' => 'You have unsaved changes!',
    'digital_product_editor.sections.files.table.message.be_careful' => 'Watch out',
    'digital_product_editor.sections.files.table.message.active_files' => 'Uploaded files',
    'digital_product_editor.sections.files.table.message.no_active_files' => 'No files uploaded',
    'digital_product_editor.sections.files.table.message.save_success' =>'The settings have been saved!',
    'digital_product_editor.sections.files.table.message.save_error' => 'An error occurred while saving. Please contact the administrator.',

    'products.add_to_cart_button.sold_out' => 'Sold out',
    'products.add_to_cart_button.sales_disabled' => 'Sales disabled',

    'my_account.my_digital_products.free_video_storage_space' => 'Payment status: %s',

    'courses.duplicated_course_suffix' => '(copy)',

    'course_editor.page_title' => 'Course Editing',
    'course_editor.preview_button.course' => 'View course',
    'course_editor.preview_button.course_panel' => 'View course panel',
    'course_editor.course_does_not_exist' => 'The course does not exist or the ID provided is incorrect.',

    'course_editor.sections.general' => 'Basic',
    'course_editor.sections.structure' => 'Structure',
    'course_editor.sections.link_generator' => 'Link Generator',
    'course_editor.sections.invoices' => 'Invoices',
    'course_editor.sections.mailings' => 'Mailing systems',
    'course_editor.sections.discount_code' => 'Discount code',

    'courses_popup_editor.popup.field.name' => 'Course Name',
    'courses_popup_editor.popup.field.name.placeholder' => 'Enter course name',

    'courses_popup_editor.popup.field.price' => 'Price',
    'courses_popup_editor.popup.field.price.placeholder' => 'Enter course price',
    'courses_popup_editor.popup.field.price.tooltip' => 'How much does it cost to access the course? Enter 0 if you want to share the course for free. ',

    'course_editor.sections.general.fieldset.name' => 'Name and description',

    'course_editor.sections.general.name' => 'Product Name',

    'course_editor.sections.general.description' => 'Full description of the course offer',
    'course_editor.sections.general.description.desc' => 'Configure the course description, categories, tags, URL extension and highlighted image.',

    'course_editor.sections.general.short_description' => 'Short description of the course offer',
    'course_editor.sections.general.welcome' => 'Welcome / description in the student panel',

    'course_editor.sections.general.categories' => 'Categories',
    'course_editor.sections.general.select_categories' => 'Select categories',

    'course_editor.sections.general.fieldset.location' => 'Location',

    'course_editor.sections.general.url' => 'URL extension',

    'course_editor.sections.general.tags' => 'Tags',
    'course_editor.sections.general.add_tags' => 'Add a new tag',
    'course_editor.sections.general.add_tags.desc' => 'Separate with commas or enter.',

    'course_editor.sections.general.fieldset.graphics' => 'Graphic',

    'course_editor.sections.general.fieldset.view' => 'View',

    'course_editor.sections.general.view_options' => 'View options',

    'course_editor.sections.general.navigation_next_lesson_label.label' => 'Next lesson label',
    'course_editor.sections.general.navigation_next_lesson_label.label.tooltip' => 'Select a label for the next lesson.',

    'course_editor.sections.general.view_options.default' => 'Default (%s)',
    'course_editor.sections.general.navigation_next_lesson_label.lesson' => 'Text "Next Lesson"',
    'course_editor.sections.general.navigation_next_lesson_label.lesson_title' => 'Title of next lesson',
    'course_editor.sections.general.navigation_next_lesson_label.text_next' => 'Previous Lesson Text',
    'course_editor.sections.general.navigation_next_lesson_label.title_next' => 'Title of previous lesson',
    'course_editor.sections.general.navigation_previous_lesson_label.label' => 'Previous lesson label',
    'course_editor.sections.general.navigation_previous_lesson_label.label.tooltip' => 'Select a label for a previous lesson.',

    'course_editor.sections.general.inaccessible_lesson_display.visible' => 'Always visible',
    'course_editor.sections.general.inaccessible_lesson_display.grayed' => 'Visible, greyed out',
    'course_editor.sections.general.inaccessible_lesson_display.hidden' => 'Hidden',

    'course_editor.sections.general.progress_tracking' => 'Progress tracking',

    'course_editor.sections.general.progress_forced.label' => 'Progressive access',

    'course_editor.sections.general.progress_forced.enabled' => 'Enabled',
    'course_editor.sections.general.progress_forced.disabled' => 'Disabled',

    'course_editor.sections.general.progress_tracking.option.on' => 'Enabled',
    'course_editor.sections.general.progress_tracking.option.off' => 'Disabled',

    'course_editor.sections.general.featured_image' => 'Course thumbnail',
    'course_editor.sections.general.logo' => 'Course logo',
    'course_editor.sections.general.banner' => 'Course banner',

    'course_editor.sections.general.fieldset.certification' => 'Certification',

    'course_editor.sections.general.disable_certificates' => 'Certificates',
    'course_editor.sections.general.disable_certificates.tooltip' => 'Selecting this option activates certificates only for this course',
    'course_editor.sections.general.disable_certificates.popup.title' => 'Certificates',

    'course_editor.sections.general.fieldset.sale' => 'Sale',

    'course_editor.sections.general.sales_disabled' => 'Enable sales',
    'course_editor.sections.general.sales_disabled.tooltip' => 'By selecting this option, you will allow customers to purchase this course',

    'course_editor.sections.general.hide_from_list' => 'Show course in directory',

    'course_editor.sections.general.hide_purchase_button' => 'Show purchase button',
    'course_editor.sections.general.hide_purchase_button.tooltip' => 'This option will show the buy button on the course page',

    'course_editor.sections.general.promote_course' => 'Promote a course on home page',

    'course_editor.sections.general.fieldset.no_authorization' => 'No authorization',

    'course_editor.sections.general.redirect_page' => 'Redirect unauthorized users',
    'course_editor.sections.general.redirect_page.tooltip' => 'If the user does not have access to the page, redirect them to:',
    'course_editor.sections.general.redirect_page.select.choose' => 'Select page...',

    'course_editor.sections.general.redirect_url' => 'or URL',

    'course_editor.sections.general.certificate_template_id' => 'Select a certificate template',
    'course_editor.sections.general.certificate_template_id.tooltip' => 'You can choose which template should be used to generate the certificate after completing this course.',
    'course_editor.sections.general.certificate_template_id.select.default' => 'Default',

    'course_editor.sections.general.enable_certificate_numbering' => 'Enable certificate numbering',

    'course_editor.sections.general.certificate_numbering_pattern' => 'Numbering pattern',

    'course_editor.sections.general.disable_email_subscription' => 'Subscription notifications',

    'course_editor.sections.general.fieldset.price' => 'Price',

    'course_editor.sections.general.variable_pricing' => 'Pricing variants',
    'course_editor.sections.general.variable_pricing_single_price' => 'Various variants in cart',

    'course_editor.sections.general.variable_pricing.notice' => 'Enabling or disabling variants will disable course sales. <br>You can turn it back on at any time.',
    'course_editor.sections.general.sales_disabled.notice' => 'To enable sales, add at least one price variant or disable the variant module.',

    'course_editor.sections.general.variable_pricing_single_price.tooltip' => 'Enable multiple options. It allows you to add to the cart various price options for the product in one order.',

    'course_editor.sections.general.variable_prices' => 'Pricing variants',
    'course_editor.sections.general.variable_prices.edit_variants' => 'Edit variants',

    'course_editor.sections.general.variable_prices.table.head.id' => 'ID',
    'course_editor.sections.general.variable_prices.table.head.name' => 'Variant name',
    'course_editor.sections.general.variable_prices.table.head.price' => 'Price (PLN)',
    'course_editor.sections.general.variable_prices.table.head.availability' => 'Availability (pcs)',
    'course_editor.sections.general.variable_prices.table.head.access_on' => 'Access to',
    'course_editor.sections.general.variable_prices.table.head.recurring_payments' => 'Recurring payments',

    'course_editor.sections.general.variable_prices.table.body.recurring_payments_enabled.yes' => 'Yes',
    'course_editor.sections.general.variable_prices.table.body.recurring_payments_enabled.no' => 'No',

    'course_editor.sections.general.variable_prices.table.body.set_price_variants' => 'Set price variants by clicking the button below',

    'course_editor.sections.general.variable_prices.edit.table.purchase_limit_items_left' => 'Pieces left',
    'course_editor.sections.general.variable_prices.edit.table.purchase_limit' => 'Total items available',

    'course_editor.sections.general.variable_prices.edit.table.message.success' => 'Price variants have been successfully saved.',
    'course_editor.sections.general.variable_prices.edit.table.message.error' => 'The price field must be filled in!',

    'course_editor.sections.general.variable_prices.edit.table.message.one_price_min' => 'You must have at least one price variant',

    'course_editor.sections.general.variable_prices.edit.table.message.one_field_min' => 'You must have at least one completed field',

    'course_editor.sections.general.price' => 'Price',
    'course_editor.sections.general.price.tooltip' => 'How much does it cost to access the course? Enter 0 if you want to share the course for free.',

    'course_editor.sections.general.special_offer' => 'Special offer',
    'course_editor.sections.general.variable_prices_special_offer' => 'Promotion planning',

    'course_editor.sections.general.sale_price' => 'Special price',
    'course_editor.sections.general.sale_price.tooltip' => 'What is the sale price for this course? Enter 0 if you want to disable the promotional price.',

    'course_editor.sections.general.sale_price_date_from' => 'Promotion start',
    'course_editor.sections.general.sale_price_date_from.tooltip' => 'Activation and deactivation of the promotion may be delayed up to 5 minutes from the set time in practice',

    'course_editor.sections.general.sale_price_date_to' => 'Promotion ends',
    'course_editor.sections.general.sale_price_date_to.tooltip' => 'Activation and deactivation of the promotion may be delayed up to 5 minutes from the set time in practice',

    'course_editor.sections.general.fieldset.quantities_available' => 'Quantities available',

    'course_editor.sections.general.purchase_limit' => 'Total number of items to purchase',
    'course_editor.sections.general.purchase_limit.desc' => 'Enter 0 or leave blank to disable limit',

    'course_editor.sections.general.purchase_limit_items_left' => 'Items left:',

    'course_editor.sections.general.fieldset.course_start' => 'Course start and length of access',

    'course_editor.sections.discount_code.access_time' => 'Access to',
    'course_editor.sections.discount_code.access_time.tooltip' => 'For how long will the student have access to the purchased course? Leave the field blank to disable the time limit (untimed access).',

    'course_editor.sections.general.access_time' => 'Access to',
    'course_editor.sections.general.access_time.tooltip' => 'For how long will the student have access to the purchased course? Leave the field blank to disable the time limit (untimed access).',

    'course_editor.sections.general.code_time.validation' => 'Entered value cannot be less than 0.',
    'course_editor.sections.general.code_time.validation.must_be_a_number' => 'The value entered must be a number.',
    'course_editor.sections.general.code_time.validation.must_not_be_empty' => 'You must select one of the options in the box above.',

    'course_editor.sections.general.access_time_unit.option.minutes' => 'Minutes',
    'course_editor.sections.general.access_time_unit.option.hours' => 'Hours',
    'course_editor.sections.general.access_time_unit.option.days' => 'Days',
    'course_editor.sections.general.access_time_unit.option.months' => 'Months',
    'course_editor.sections.general.access_time_unit.option.years' => 'Years',

    'course_editor.sections.general.access_start' => 'Course start date',
    'course_editor.sections.general.access_start.desc' => 'Attention! The date change will only affect new students. Those who made a purchase before the change will have access as originally set up.',
    'course_editor.sections.general.access_start.tooltip' => 'Use this option to specify when course content will be available to students. Leave the field blank to disable the start date.',
    'course_editor.sections.general.access_start.at' => 'at',

    'course_editor.sections.general.recurring_payments_enabled' => 'Enable recurring payments',
    'course_editor.sections.general.recurring_payments' => 'Recurring payments',
    'course_editor.sections.general.recurring_payments_interval' => 'Interval',
    'course_editor.sections.general.recurring_payments_interval.desc' => 'Set the time interval between recurring payments for this item.',

    'course_editor.sections.general.payments_unit.option.days' => 'Days',
    'course_editor.sections.general.payments_unit.option.weeks' => 'Weeks',
    'course_editor.sections.general.payments_unit.option.months' => 'Months',
    'course_editor.sections.general.payments_unit.option.years' => 'Years',

    'course_editor.sections.general.custom_purchase_link' => 'Link to external offer',
    'course_editor.sections.general.custom_purchase_link.tooltip' => 'The url address to which redirection is to take place after clicking the Order button. Leave the field empty if the button is to work in a standard way.',

    'course_editor.sections.mailings.fieldset.mailings' => 'Mailing systems',

    'course_editor.sections.mailings.empty_lists' => 'Incorrect configuration or missing lists.',

    'course_editor.sections.mailings.mailchimp' => 'MailChimp',
    'course_editor.sections.mailings.popup.mailchimp' => 'Select the lists',
    'course_editor.sections.mailings.popup.mailchimp.desc' => 'Select lists to which the buyer should be subscribed when he pays for access to the service',

    'course_editor.sections.mailings.mailerlite' => 'MailerLite',
    'course_editor.sections.mailings.popup.mailerlite' => 'Select lists',
    'course_editor.sections.mailings.popup.mailerlite.desc' => 'Select lists to which the buyer should be subscribed when he pays for access to the service',

    'course_editor.sections.mailings.freshmail' => 'FreshMail',
    'course_editor.sections.mailings.popup.freshmail' => 'Select lists',
    'course_editor.sections.mailings.popup.freshmail.desc' => 'Select lists to which the buyer should be saved when he pays for access to the service',

    'course_editor.sections.mailings.ipresso' => 'ActiveCampaign',
    'course_editor.sections.mailings.popup.ipresso_tags' => 'Add tags',
    'course_editor.sections.mailings.popup.ipresso_tags.desc' => 'Add tags (separated by commas) which will be <strong> added </strong> to your iPresso contacts after the purchase is completed.',

    'course_editor.sections.mailings.popup.ipresso_tags_unsubscribe' => 'Add tags',
    'course_editor.sections.mailings.popup.ipresso_tags_unsubscribe.desc' => 'Add tags (separated by commas) which will be <strong> removed </strong> from contacts in iPresso after purchase.',

    'course_editor.sections.mailings.activecampaign' => 'ActiveCampaign',
    'course_editor.sections.mailings.popup.activecampaign' => 'Select Lists',
    'course_editor.sections.mailings.popup.activecampaign.desc' => 'Select the lists that the buyer should <strong> subscribe </strong> to when they pay for access to the service.',

    'course_editor.sections.mailings.popup.activecampaign_unsubscribe' => 'Select Lists',
    'course_editor.sections.mailings.popup.activecampaign_unsubscribe.desc' => 'Select the lists from which the buyer is to be <strong>unsubscribed</strong> when he pays for access to the service.',

    'course_editor.sections.mailings.popup.activecampaign_tags' => 'Add tags',
    'course_editor.sections.mailings.popup.activecampaign_tags.desc' => 'Add tags (separated by commas) to be <strong> added </strong> to your ActiveCampaign contacts after purchase is complete.',

    'course_editor.sections.mailings.popup.activecampaign_tags_unsubscribe' => 'Add tags',
    'course_editor.sections.mailings.popup.activecampaign_tags_unsubscribe.desc' => 'Add tags (separated by commas) to be <strong> removed </strong> from ActiveCampaign contacts after purchase is complete.',

    'course_editor.sections.mailings.getresponse' => 'GetResponse',
    'course_editor.sections.mailings.popup.getresponse' => 'Select lists',
    'course_editor.sections.mailings.popup.getresponse.desc' => 'Select the lists to which the buyer should <strong> subscribe </strong> when he pays for access to the service.',

    'course_editor.sections.mailings.popup.getresponse_unsubscribe' => 'Select lists',
    'course_editor.sections.mailings.popup.getresponse_unsubscribe.desc' => 'Select the lists from which the buyer should <strong> unsubscribe </strong> when he pays for access to the service.',

    'course_editor.sections.mailings.popup.getresponse_tags' => 'Select tags',
    'course_editor.sections.mailings.popup.getresponse_tags.desc' => 'Select tags to which buyers should be added when shopping.',

    'course_editor.sections.mailings.salesmanago' => 'SalesManago',
    'course_editor.sections.mailings.popup.salesmanago_tags' => 'Add tags',
    'course_editor.sections.mailings.popup.salesmanago_tags.desc' => 'Enter tags (separating them with a comma) to be added to the contact in the SALESmanago panel after purchasing this product.',

    'course_editor.sections.mailings.interspire' => 'Interspire',
    'course_editor.sections.mailings.popup.interspire' => 'Select Lists',
    'course_editor.sections.mailings.popup.interspire.desc' => 'Select the lists to which the buyer should be subscribed when he pays for access to the service',

    'course_editor.sections.mailings.convertkit' => 'ConvertKit',
    'course_editor.sections.mailings.popup.convertkit' => 'Select Lists',
    'course_editor.sections.mailings.popup.convertkit.desc' => 'Select the lists to which the buyer should be subscribed when he pays for access to the service',

    'course_editor.sections.mailings.popup.convertkit_tags' => 'Select tags',
    'course_editor.sections.mailings.popup.convertkit_tags.desc' => 'Select tags to which buyers should be <strong> added </strong> when shopping.',

    'course_editor.sections.mailings.popup.convertkit_tags_unsubscribe' => 'Select tags',
    'course_editor.sections.mailings.popup.convertkit_tags_unsubscribe.desc' => 'Select tags from which buyers should be <strong> removed </strong> when shopping.',

    'course_editor.sections.mailings.select_list' => 'Select list or group',
    'course_editor.sections.mailings.add_next' => 'Add next',

    'user_security.user_banned.forever' => 'You have been banned forever',
    'user_security.user_banned.temporarily' => '<strong>You are banned!</strong> Wait ',
    'user_security.user_banned.just_a_moment' => 'just a moment',

    'product.lowest_price_information' => 'The lowest price in the last %d days before discount was %.2f %s',

    'course_editor.sections.structure.success_message' => 'The structure was successfully saved.',
    'course_editor.sections.structure.error_message' => 'An error occurred while saving. Please contact the site administrator.',
    'course_editor.sections.structure.validation.empty_structure' => 'Please add structure to your course before saving.',
    'course_editor.sections.structure.validation.error_message' => 'The data entered in the structure is invalid.',

    'course_editor.sections.structure.info' => 'You can add a structure for your course below. Depending on your needs, you can choose from modules, lessons and quizzes. A pencil icon next to each item will open a new tab in the browser where you can edit individual content.',

    'course_editor.sections.structure.fieldset.module.disabled' => 'You must save your changes to be able to edit this content.',

    'course_editor.sections.structure.fieldset.module_availability' => 'Module and Lesson Availability',

    'course_editor.sections.structure.default_drip_value' => 'Change sharing delay',

    'course_editor.sections.structure.fieldset.drip_value' => 'Sharing delay for modules and lessons',
    'course_editor.sections.structure.fieldset.drip_value.tooltip' => 'Ex. to share the content of one lesson per day, set it to 1 day. Leave the field blank to make all lessons available immediately after purchasing the course.',

    'course_editor.sections.structure.fieldset.drip_unit' => 'Delay unit',

    'course_editor.sections.structure.set_modules_lessons' => 'Set for modules/lessons',
    'course_editor.sections.structure.change_drip_unit' => 'Change drip unit',
    'course_editor.sections.structure.set_drip_unit' => 'Set drip unit',

    'course_editor.sections.structure.delay' => 'In order to drip courses, you need to upgrade your license to level: %s',
    'course_editor.sections.structure.delay.info' => 'Set, for example, <strong>1 day</strong>, to release one lesson every day.<br>Leave blank to release all lessons immediately after purchase.',
    'course_editor.sections.structure.upgrade_needed' => 'Upgrade needed',

    'course_editor.sections.structure.before_save.info1' => 'To change the content of new lessons, please save the course.',
    'course_editor.sections.structure.before_save.info2' => 'You can move modules or lessons and change their order.',

    'course_editor.sections.structure.button.add_module' => 'Add module',
    'course_editor.sections.structure.button.add_lesson' => 'Add lesson',
    'course_editor.sections.structure.button.add_quiz' => 'Add quiz',

    'course_editor.sections.structure.quiz.upgrade_needed' => 'In order to add a quiz, you need to upgrade your license to level: %s',

    'course_editor.sections.structure.quiz.number_test_attempts' => 'Number of test attempts',
    'course_editor.sections.structure.quiz.number_test_attempts.desc' => 'Specifies how many attempts the trainee has to pass the test.',

    'course_editor.sections.structure.quiz.number_test_attempts.limit.wrong' => 'Unfortunately, you answered all the questions wrong. <br><br><strong>The number of attempts to approach the test has been used.</strong>',
    'course_editor.sections.structure.quiz.number_test_attempts.limit.few_points' => 'Unfortunately, you scored too few points. <br><br><strong>The number of attempts to approach the test has been used.</strong>',
    'course_editor.sections.structure.quiz.number_test_attempts.limit' => '<strong>The number of test attempts has been used up.</strong>',
    'course_editor.sections.structure.quiz.number_test_attempts.attempts_left_info' => 'The number of remaining attempts at the test: %d',

    'quiz_editor.page_title' => 'Editing a quiz',
    'quiz_editor.quiz_does_not_exist' => 'The quiz does not exist or the ID provided is incorrect.',
    'quiz_editor.sections.general' => 'Basic',
    'quiz_editor.sections.structure' => 'Structure',
    'quiz_editor.sections.files' => 'Attachments',

    'quiz_editor.sections.general.fieldset.name_description' => 'Name and description',
    'quiz_editor.sections.general.fieldset.location' => 'Location',
    'quiz_editor.sections.general.fieldset.graphic' => 'Graphic',
    'quiz_editor.sections.general.fieldset.quiz_settings' => 'Quiz Settings',


    'quiz_editor.sections.general.name' => 'Name',
    'quiz_editor.sections.general.description' => 'Quiz instructions',
    'quiz_editor.sections.general.description.tooltip' => '',

    'quiz_editor.sections.general.subtitle' => 'Quiz short description',
    'quiz_editor.sections.general.subtitle.tooltip' => 'Optional, additional description displayed below the quiz title (on the quiz page, module page and panel).',

    'quiz_editor.sections.general.additional_info' => 'Additional information',
    'quiz_editor.sections.general.additional_info.tooltip' => '',

    'quiz_editor.sections.general.url' => 'Extension for URL',

    'quiz_editor.sections.general.featured_image' => 'Quiz thumbnail',

    'quiz_editor.sections.general.level' => 'Difficulty level',
    'quiz_editor.sections.general.level.tooltip' => 'The difficulty level of this material.',

    'quiz_editor.sections.general.duration' => 'Duration',
    'quiz_editor.sections.general.duration.tooltip' => 'Estimated time needed to complete this material.',

    'quiz_editor.sections.general.time' => 'Quiz time',
    'quiz_editor.sections.general.time.tooltip' => 'Time (in minutes) to complete the quiz.',

    'quiz_editor.sections.general.time.validation' => 'The given value cannot be empty, less than or equal to 0.',

    'quiz_editor.sections.general.number_test_attempts' => 'Number of test attempts',
    'quiz_editor.sections.general.number_test_attempts.tooltip' => 'Specifies how many attempts the trainee has to pass the test.',
    'quiz_editor.sections.general.number_test_attempts.validation' => 'The given value cannot be empty, less than or equal to 0.',

    'quiz_editor.sections.general.evaluated_by_admin_mode' => 'Quiz moderation',
    'quiz_editor.sections.general.evaluated_by_admin_mode.tooltip' => 'Activating this option will make the passing of the test dependent on its evaluation by the administrator.',

    'quiz_editor.sections.general.randomizing_and_limiting_questions' => 'Randomizing the order of the questions',
    'quiz_editor.sections.general.randomize_question_order.tooltip' => 'Enable this option to randomize the order of questions.',

    'quiz_editor.sections.general.randomize_answer_order' => 'Randomize answers',
    'quiz_editor.sections.general.randomize_answer_order.tooltip' => 'Activating this option will cause the answers to the questions to appear in random order.',
    'quiz_editor.sections.general.answers_preview' => 'Preview of the answers',
    'quiz_editor.sections.general.answers_preview.tooltip' => 'Enable this option to allow the user to check their answers after completing the quiz.',
    'quiz_editor.sections.general.also_show_correct_answers' => 'Also show correct answers and comments',
    'quiz_editor.sections.general.also_show_correct_answers.tooltip' => 'Enable this option to show the user the correct answers to the questions they answered incorrectly. In addition, if comments are added to the question structure, they will also be displayed.',

    'quiz_editor.sections.structure.success_message' => 'The structure was successfully saved.',
    'quiz_editor.sections.structure.error_message' => 'An error occurred while saving. Please contact the site administrator.',
    'quiz_editor.sections.structure.validation.empty_structure' => 'Please add structure to your quiz before saving.',
    'quiz_editor.sections.structure.validation.error_message' => 'The data entered in the structure is invalid.',
    'quiz_editor.sections.structure.info' => 'You can add a structure for your quiz below. Depending on your needs, you can choose from different types of questions. You can also define the number of points necessary to pass the test.',

    'bundles_list.notice' => 'Change package: To use the functionality of packages, you must change your license to PLUS or PRO.',

    'bundles_list.page_title' => 'Bundles',
    'bundles_list.column.id' => 'ID',
    'bundles_list.column.name' => 'Bundle name',
    'bundles_list.column.products' => 'Products',
    'bundles_list.column.show' => 'Show',
    'bundles_list.column.sales' => 'Sales',

    'bundles_list.actions.create_bundle' => 'Add Bundle',
    'bundles_list.actions.edit' => 'Edit',
    'bundles_list.actions.delete' => 'Delete',
    'bundles_list.actions.delete.success' => 'Bundle was successfully removed!',
    'bundles_list.actions.delete.loading' => 'Deleting...',
    'bundles_list.actions.delete.confirm' => 'Are you sure you want to delete the selected bundle? This action is irreversible.',
    'bundles_list.actions.sales.bulk' => 'Enable / disable sales',
    'bundles_list.actions.sales.active' => 'Enable sales',
    'bundles_list.actions.sales.inactive' => 'Disable sales',
    'bundles_list.actions.sales.loading' => 'Changing...',
    'bundles_list.actions.delete.error' => 'An error occurred while deleting. Please contact the site administrator.',

    'bundles_list.popup.close' => 'Close',
    'bundles_list.popup.save' => 'Save',
    'bundles_list.popup.purchase_links.title' => 'Shopping Links',
    'bundles_list.buttons.purchase_links.tooltip' => 'Shopping links',

    'bundles_list.sales.status.enabled' => 'Enabled',
    'bundles_list.sales.status.disabled' => 'Disabled',

    'bundles_popup_editor.title' => 'Create a new bundle',
    'bundles_popup_editor.popup.field.name' => 'Bundle name',
    'bundles_popup_editor.popup.field.name.placeholder' => 'Enter a package name',

    'bundles_popup_editor.popup.field.price' => 'Price',
    'bundles_popup_editor.popup.field.price.placeholder' => 'Enter bundle price',
    'bundles_popup_editor.popup.field.price.tooltip' => 'How much does access to the package cost? Enter 0 if you want to provide the service for free.',

    'gateways.manual_purchases' => 'Manual purchase',

    'bundle_editor.page_title' => 'Edit Bundle',
    'bundle_editor.preview_button' => 'View Bundle',

    'bundle_editor.sections.general' => 'General',
    'bundle_editor.sections.package_contents' => 'Bundle content',
    'bundle_editor.sections.invoices' => 'Invoices',
    'bundle_editor.sections.mailings' => 'Mailing systems',

    'bundle_editor.sections.general.fieldset.name' => 'Name and description',

    'bundle_editor.sections.general.name' => 'Bundle name',

    'bundle_editor.sections.general.description' => 'Bundle Offer Full Description',
    'bundle_editor.sections.general.description.desc' => 'Configure bundle description',
    'bundle_editor.sections.general.short_description' => 'Bundle offer summary',

    'bundle_editor.sections.general.categories' => 'Categories',
    'bundle_editor.sections.general.select_categories' => 'Select categories',

    'bundle_editor.sections.general.fieldset.location' => 'Location',

    'bundle_editor.sections.general.url' => 'Extension for URL',

    'bundle_editor.sections.general.tags' => 'Tags',
    'bundle_editor.sections.general.add_tags' => 'Add new tag',
    'bundle_editor.sections.general.add_tags.desc' => 'Separate with commas or enters.',

    'bundle_editor.sections.general.fieldset.graphics' => 'Graphic',

    'bundle_editor.sections.general.featured_image' => 'Bundle thumbnail',
    'bundle_editor.sections.general.banner' => 'Bundle banner',

    'bundle_editor.sections.general.fieldset.sale' => 'Sale',

    'bundle_editor.sections.general.sales_disabled' => 'Enable sales',
    'bundle_editor.sections.general.sales_disabled.tooltip' => 'By selecting this option, you will allow customers to purchase this bundle',

    'bundle_editor.sections.general.hide_from_list' => 'Show bundle in directory',

    'bundle_editor.sections.general.hide_purchase_button' => 'Show purchase button',
    'bundle_editor.sections.general.hide_purchase_button.tooltip' => 'This option will show the buy button on the course page',

    'bundle_editor.sections.general.variable_prices' => 'Price variants',
    'bundle_editor.sections.general.variable_prices.edit_variants' => 'Edit variants',

    'bundle_editor.sections.general.variable_pricing.notice' => 'Enabling or disabling variants will disable the sale of the package. <br>You can turn it back on at any time.',
    'bundle_editor.sections.general.sales_disabled.notice' => 'To enable sales, add at least one price variant or disable the variant module.',

    'bundle_editor.sections.general.variable_prices.edit.table.purchase_limit_items_left' => 'Pieces left',
    'bundle_editor.sections.general.variable_prices.edit.table.purchase_limit' => 'Total items available',

    'bundle_editor.sections.general.fieldset.price' => 'Price',

    'bundle_editor.sections.general.variable_pricing' => 'Pricing variants',

    'bundle_editor.sections.general.price' => 'Price',
    'bundle_editor.sections.general.price.tooltip' => 'How much does it cost to access the bundle? Enter 0 if you want to share the package for free.',

    'bundle_editor.sections.general.special_offer' => 'Promotion',

    'bundle_editor.sections.general.sale_price' => 'Special price',
    'bundle_editor.sections.general.sale_price.tooltip' => 'What is the sale price of this bundle? Enter 0 if you want to disable the promotional price.',

    'bundle_editor.sections.general.sale_price_date_from' => 'Promotion start',
    'bundle_editor.sections.general.sale_price_date_from.tooltip' => 'Activation and deactivation of the promotion may be delayed up to 5 minutes from the set time in practice',

    'bundle_editor.sections.general.sale_price_date_to' => 'Promotion ends',
    'bundle_editor.sections.general.sale_price_date_to.tooltip' => 'Activation and deactivation of the promotion may be delayed up to 5 minutes from the set time in practice',

    'bundle_editor.sections.general.recurring_payments_enabled' => 'Enable recurring payments',
    'bundle_editor.sections.general.recurring_payments' => 'Recurring payments',
    'bundle_editor.sections.general.recurring_payments_interval' => 'Interval',
    'bundle_editor.sections.general.recurring_payments_interval.desc' => 'Set the interval between recurring payments for this item.',

    'bundle_editor.sections.general.payments_unit.option.days' => 'Days',
    'bundle_editor.sections.general.payments_unit.option.weeks' => 'Weeks',
    'bundle_editor.sections.general.payments_unit.option.months' => 'Months',
    'bundle_editor.sections.general.payments_unit.option.years' => 'Years',
    'bundle_editor.sections.mailings.fieldset.mailings' => 'Mailing systems',

    'bundle_editor.sections.bundle_content.info' => 'Below you can manage the list of products and services that will be included in the edited package. Add any number of elements and arrange them in the desired order.',

    'bundle_editor.sections.bundle_content.column.priority' => 'Order',
    'bundle_editor.sections.bundle_content.column.product_name' => 'Product',
    'bundle_editor.sections.bundle_content.column.select_product' => 'Select product',
    'bundle_editor.sections.bundle_content.button.add_product' => 'Add product',
    'bundle_editor.sections.bundle_content.button.save' => 'Save',
    'bundle_editor.sections.bundle_content.message.saving' => 'Saving ...',
    'bundle_editor.sections.bundle_content.button.cancel' => 'Cancel',
    'bundle_editor.sections.bundle_content.message.you_have_unsaved_changes' => 'You have unsaved changes!',
    'bundle_editor.sections.bundle_content.message.be_careful' => 'Watch out',
    'bundle_editor.sections.bundle_content.message.selected_products' => 'Selected products',
    'bundle_editor.sections.bundle_content.message.no_selected_products' => 'No products selected',
    'bundle_editor.sections.bundle_content.message.save_success' =>'The settings have been saved!',
    'bundle_editor.sections.bundle_content.message.save_error' => 'An error occurred while saving. Please contact the administrator.',

    'contact_form.captcha.spam_detected' => 'Sorry, spam was detected!',

    'price_history.menu_title' => 'Price history',
    'price_history.page_title' => 'Price history',
    'price_history.column.id' => 'ID',
    'price_history.column.product_name' => 'Product name',
    'price_history.column.price' => 'Price',
    'price_history.column.type_of_price' => 'Type of price',
    'price_history.column.date_of_change' => 'Date of change',
    'price_history.column.price.promotion_disabled' => 'Disabled',

    'price_history.type_of_price.regular' => 'Regular',
    'price_history.type_of_price.promo' => 'Promotional',

    'app_view.go_to' => 'Go to',
    'app_view.course_panel' => 'Course panel',

    'lesson_editor.short_description' => 'Short description',

    'newsletter.sign_up' => 'Signup for the newsletter',

    'tpay.checkout_button.text' => 'Purchase and pay by card',

    'physical_products.physical_products' => 'Physical products',
    'physical_products.page_title' => 'Physical products',

    'physical_products.column.id' => 'ID',
    'physical_products.column.name' => 'Product name',
    'physical_products.column.show' => 'Show',
    'physical_products.column.sales' => 'Sales',

    'physical_products.sales.status.enabled' => 'Enabled',
    'physical_products.sales.status.disabled' => 'Disabled',

    'physical_products.actions.create_product' => 'Add a physical product',
    'physical_products.actions.edit' => 'Edit',
    'physical_products.actions.delete' => 'Delete',
    'physical_products.actions.delete.success' => 'The product was successfully deleted!',
    'physical_products.actions.delete.info' => 'You cannot delete this product because it is tied to at least one package. Remove it from all packages, then try the removal again.',
    'physical_products.actions.delete.loading' => 'Deleting...',
    'physical_products.actions.delete.confirm' => 'Are you sure you want to delete the selected product? This action is irreversible.',
    'physical_products.actions.sales.bulk' => 'Enable / Disable Sales',
    'physical_products.actions.sales.active' => 'Enable sales',
    'physical_products.actions.sales.inactive' => 'Disable sales',
    'physical_products.actions.sales.loading' => 'I am changing...',
    'physical_products.actions.delete.error' => 'An error occurred while deleting. Please contact the site administrator.',

    'physical_products.popup.close' => 'Close',
    'physical_products.popup.purchase_links.title' => 'Shopping Links',
    'physical_products.buttons.purchase_links.tooltip' => 'Purchase links',
    'physical_products.buttons.product_panel.tooltip' => 'View product',

    'physical_products_popup_editor.popup.field.name' => 'Product name',
    'physical_products_popup_editor.popup.field.name.placeholder' => 'Enter product name',

    'physical_products_popup_editor.popup.field.price' => 'Price',
    'physical_products_popup_editor.popup.field.price.placeholder' => 'Enter product price',
    'physical_products_popup_editor.popup.field.price.tooltip' => 'How much does the product cost? Enter 0 if you want the product to be free.',

    'physical_product_editor.add_service' => 'Add product',
    'physical_product_editor.page_title' => 'Product Edit',
    'physical_product_editor.preview_button' => 'Preview product',

    'physical_product_editor.sections.general' => 'Basic',

    'physical_product_editor.sections.general.fieldset.name' => 'Name and description',

    'physical_product_editor.sections.general.name' => 'Product Name',

    'physical_product_editor.sections.general.description' => 'Description',
    'physical_product_editor.sections.general.description.desc' => 'Configure the product description, categories, tags, URL extension and featured image.',

    'physical_product_editor.sections.general.short_description' => 'Short description',

    'physical_product_editor.sections.general.categories' => 'Categories',
    'physical_product_editor.sections.general.select_categories' => 'Select categories',

    'physical_product_editor.sections.general.url' => 'URL extension',
    'physical_product_editor.sections.general.tags' => 'Tags',
    'physical_product_editor.sections.general.add_tags' => 'Add new tag',
    'physical_product_editor.sections.general.add_tags.desc' => 'Separate with commas or enter.',

    'physical_product_editor.sections.general.fieldset.price' => 'Price',
    'physical_product_editor.sections.general.delivery_price_info' => 'Please note that shipping costs are currently not included. To change this, configure it in Publigo Settings in Basic tab.',
    'physical_product_editor.sections.general.price.tooltip' => 'How much does the product it cost? Enter 0 if you want to share it for free. ',

    'physical_product_editor.sections.general.fieldset.location' => 'Location',

    'physical_product_editor.sections.general.special_offer' => 'Promotion',

    'physical_product_editor.sections.general.sale_price' => 'Promotion price',
    'physical_product_editor.sections.general.sale_price.tooltip' => 'What is the promotional price for this item? Enter 0 if you want to disable the promotional price.',

    'physical_product_editor.sections.general.sale_price_date_from' => 'Promotion start',
    'physical_product_editor.sections.general.sale_price_date_from.tooltip' => 'The moment of activating and deactivating the promotion may in practice be delayed up to 5 minutes compared to the set time',

    'physical_product_editor.sections.general.sale_price_date_to' => 'End of promotion',
    'physical_product_editor.sections.general.sale_price_date_to.tooltip' => 'The moment of activating and deactivating the promotion may in practice be delayed up to 5 minutes compared to the set time',

    'physical_product_editor.sections.general.fieldset.quantities_available' => 'Quantities available',
    'physical_product_editor.sections.general.purchase_limit' => 'Total number of items to be purchased',
    'physical_product_editor.sections.general.purchase_limit.desc' => 'Enter 0 or leave it blank to disable the limit',

    'physical_product_editor.sections.general.purchase_limit_items_left' => 'Pcs left',

    'physical_product_editor.sections.general.fieldset.graphics' => 'Graphics',

    'physical_product_editor.sections.general.banner' => 'Banner',
    'physical_product_editor.sections.general.featured_image' => 'Product image',

    'physical_product_editor.sections.general.fieldset.sale' => 'Sale',

    'physical_product_editor.sections.general.sales_disabled' => 'Turn on sale',
    'physical_product_editor.sections.general.sales_disabled.tooltip' => 'By checking this option you will enable customers to purchase this product',

    'physical_product_editor.sections.general.hide_from_lists' => 'Show item in the directory',
    'physical_product_editor.sections.general.hide_purchase_button' => 'Show purchase button',
    'physical_product_editor.sections.general.hide_purchase_button.tooltip' => 'This option will show a buy button on the product page',

    'physical_product_editor.sections.general.promote_course' => 'Promote product on home page',
    'physical_product_editor.sections.general.recurring_payments_enabled' => 'Enable Recurring Payments',
    'physical_product_editor.sections.general.recurring_payments' => 'Recurring payments',
    'physical_product_editor.sections.general.recurring_payments_interval' => 'Time interval',
    'physical_product_editor.sections.general.recurring_payments_interval.desc' => 'Set the time interval between recurring payments for this item.',

    'physical_product_editor.sections.general.payments_unit.option.days' => 'Days',
    'physical_product_editor.sections.general.payments_unit.option.weeks' => 'Weeks',
    'physical_product_editor.sections.general.payments_unit.option.months' => 'Months',
    'physical_product_editor.sections.general.payments_unit.option.years' => 'Years',

    'physical_product_editor.sections.link_generator.message_1' => 'Using the link generator, you can prepare a link that not only immediately adds the product to the cart, but also applies a discount code or activates the gift purchase option. You can place such a link anywhere, e.g. on the sales page under the Buy Now button.',

    'physical_product_editor.sections.invoices.fieldset.general' => 'Accounting settings',

    'physical_product_editor.sections.invoices.no_gtu' => 'No GTU code',
    'physical_product_editor.sections.invoices.gtu' => 'GTU code',
    'physical_product_editor.sections.invoices.gtu.not_supported_for' => 'Warning! GTU via API is not supported for:',
    'physical_product_editor.sections.invoices.flat_rate_tax_symbol.not_supported_for' => 'Attention! Flat rate tax is not supported by the system:',

    'physical_product_editor.sections.invoices.flat_rate_tax_symbol' => 'Flat rate tax symbol',
    'physical_product_editor.sections.invoices.no_tax_symbol' => 'No flat rate tax',

    'physical_product_editor.sections.invoices.vat_rate' => 'VAT rate',

    'physical_product_editor.sections.link_generator.fieldset.general' => 'Links generator',
    'physical_product_editor.sections.link_generator.link_generator' => 'Links generator',

    'physical_product_editor.sections.link_generator.variable_prices.price' => 'Price',
    'physical_product_editor.sections.link_generator.variable_prices.copy' => 'Copy',
    'physical_product_editor.sections.link_generator.variable_prices.copied' => 'Copied',

    'physical_product_editor.sections.link_generator' => 'Link generator',
    'physical_product_editor.sections.invoices' => 'Invoices',
    'physical_product_editor.sections.mailings' => 'Mailing systems',
    'physical_product_editor.sections.discount_code' => 'Discount code',

    'physical_product_editor.sections.mailings.fieldset.popup.mailings' => 'Mailing systems',

    'physical_product_editor.sections.mailings.empty_lists' => 'Incorrect configuration or missing lists.',

    'physical_product_editor.sections.mailings.mailchimp' => 'MailChimp',
    'physical_product_editor.sections.mailings.popup.mailchimp' => 'Select the lists',
    'physical_product_editor.sections.mailings.popup.mailchimp.desc' => 'Select lists to which the buyer should be subscribed when he pays for access to the service',

    'physical_product_editor.sections.mailings.mailerlite' => 'MailerLite',
    'physical_product_editor.sections.mailings.popup.mailerlite' => 'Select lists',
    'physical_product_editor.sections.mailings.popup.mailerlite.desc' => 'Select lists to which the buyer should be subscribed when he pays for access to the service',

    'physical_product_editor.sections.mailings.freshmail' => 'FreshMail',
    'physical_product_editor.sections.mailings.popup.freshmail' => 'Select lists',
    'physical_product_editor.sections.mailings.popup.freshmail.desc' => 'Select lists to which the buyer should be saved when he pays for access to the service',

    'physical_product_editor.sections.mailings.ipresso' => 'ActiveCampaign',
    'physical_product_editor.sections.mailings.popup.ipresso_tags' => 'Add tags',
    'physical_product_editor.sections.mailings.popup.ipresso_tags.desc' => 'Add tags (separated by commas) which will be <strong> added </strong> to your iPresso contacts after the purchase is completed.',

    'physical_product_editor.sections.mailings.popup.ipresso_tags_unsubscribe' => 'Add tags',
    'physical_product_editor.sections.mailings.popup.ipresso_tags_unsubscribe.desc' => 'Add tags (separated by commas) which will be <strong> removed </strong> from contacts in iPresso after purchase.',

    'physical_product_editor.sections.mailings.activecampaign' => 'ActiveCampaign',
    'physical_product_editor.sections.mailings.popup.activecampaign' => 'Select Lists',
    'physical_product_editor.sections.mailings.popup.activecampaign.desc' => 'Select the lists that the buyer should <strong> subscribe </strong> to when they pay for access to the service.',

    'physical_product_editor.sections.mailings.popup.activecampaign_unsubscribe' => 'Select Lists',
    'physical_product_editor.sections.mailings.popup.activecampaign_unsubscribe.desc' => 'Select the lists from which the buyer is to be <strong>unsubscribed</strong> when he pays for access to the service.',

    'physical_product_editor.sections.mailings.popup.activecampaign_tags' => 'Add tags',
    'physical_product_editor.sections.mailings.popup.activecampaign_tags.desc' => 'Add tags (separated by commas) to be <strong> added </strong> to your ActiveCampaign contacts after purchase is complete.',

    'physical_product_editor.sections.mailings.popup.activecampaign_tags_unsubscribe' => 'Add tags',
    'physical_product_editor.sections.mailings.popup.activecampaign_tags_unsubscribe.desc' => 'Add tags (separated by commas) to be <strong> removed </strong> from ActiveCampaign contacts after purchase is complete.',

    'physical_product_editor.sections.mailings.getresponse' => 'GetResponse',
    'physical_product_editor.sections.mailings.popup.getresponse' => 'Select lists',
    'physical_product_editor.sections.mailings.popup.getresponse.desc' => 'Select the lists to which the buyer should <strong> subscribe </strong> when he pays for access to the service.',

    'physical_product_editor.sections.mailings.popup.getresponse_unsubscribe' => 'Select lists',
    'physical_product_editor.sections.mailings.popup.getresponse_unsubscribe.desc' => 'Select the lists from which the buyer should <strong> unsubscribe </strong> when he pays for access to the service.',

    'physical_product_editor.sections.mailings.popup.getresponse_tags' => 'Select tags',
    'physical_product_editor.sections.mailings.popup.getresponse_tags.desc' => 'Select tags to which buyers should be added when shopping.',

    'physical_product_editor.sections.mailings.salesmanago' => 'SalesManago',
    'physical_product_editor.sections.mailings.popup.salesmanago_tags' => 'Add tags',
    'physical_product_editor.sections.mailings.popup.salesmanago_tags.desc' => 'Enter tags (separating them with a comma) to be added to the contact in the SALESmanago panel after purchasing this product.',

    'physical_product_editor.sections.mailings.interspire' => 'Interspire',
    'physical_product_editor.sections.mailings.popup.interspire' => 'Select Lists',
    'physical_product_editor.sections.mailings.popup.interspire.desc' => 'Select the lists to which the buyer should be subscribed when he pays for access to the service',

    'physical_product_editor.sections.mailings.convertkit' => 'ConvertKit',
    'physical_product_editor.sections.mailings.popup.convertkit' => 'Select Lists',
    'physical_product_editor.sections.mailings.popup.convertkit.desc' => 'Select the lists to which the buyer should be subscribed when he pays for access to the service',

    'physical_product_editor.sections.mailings.popup.convertkit_tags' => 'Select tags',
    'physical_product_editor.sections.mailings.popup.convertkit_tags.desc' => 'Select tags to which buyers should be <strong> added </strong> when shopping.',

    'physical_product_editor.sections.mailings.popup.convertkit_tags_unsubscribe' => 'Select tags',
    'physical_product_editor.sections.mailings.popup.convertkit_tags_unsubscribe.desc' => 'Select tags from which buyers should be <strong> removed </strong> when shopping.',

    'physical_product_editor.sections.mailings.select_list' => 'Select list or group',
    'physical_product_editor.sections.mailings.add_next' => 'Add next',

    'physical_product_editor.sections.discount_code.message' => 'Together with this product you can sell a discount code which will be generated on the basis of the previously created one. You can set its expiry date. ',
    'physical_product_editor.sections.discount_code.fieldset.discount_code' => 'Discount codes',

    'physical_product_editor.sections.discount_code.code_pattern' => 'Select a pattern code',
    'physical_product_editor.sections.discount_code.code_pattern.desc' => 'Based on it, we will generate a new code after paying for the order.',

    'physical_product_editor.sections.discount_code.code_time' => 'Validity period',
    'physical_product_editor.sections.discount_code.code_time.desc' => 'This parameter is optional. By default, the discount code never expires. ',
    'physical_product_editor.sections.discount_code.code_time.validation' => 'The given value cannot be less than 0.',
    'physical_product_editor.sections.discount_code.code_time.validation.must_be_a_number' => 'The given value must be a number.',
    'physical_product_editor.sections.discount_code.code_time.validation.must_not_be_empty' => 'You must select one of the options in the field above.',

    'physical_product_editor.sections.discount_code.code_pattern.no_code.label' => 'No coupons to use',

    'physical_product_editor.sections.discount_code.code_type.option.duration' => 'Duration',
    'physical_product_editor.sections.discount_code.code_type.option.days' => 'Days',
    'physical_product_editor.sections.discount_code.code_type.option.weeks' => 'Weeks',
    'physical_product_editor.sections.discount_code.code_type.option.months' => 'Months',

    'physical_product_editor.cart.delivery_address.title' => 'Delivery Details',
    'physical_product_editor.cart.delivery_address.first_name' => 'First name',
    'physical_product_editor.cart.delivery_address.last_name' => 'Last name',
    'physical_product_editor.cart.delivery_address.company' => 'Company / Organization',
    'physical_product_editor.cart.delivery_address.phone' => 'Phone number',
    'physical_product_editor.cart.delivery_address.street' => 'Street',
    'physical_product_editor.cart.delivery_address.building_number' => 'Building number',
    'physical_product_editor.cart.delivery_address.apartment_number' => 'Apartment number',
    'physical_product_editor.cart.delivery_address.postal_code' => 'Postal Code',
    'physical_product_editor.cart.delivery_address.city' => 'City',

    'physical_product_editor.cart.delivery_address.validate.phone' => 'Please enter a valid delivery phone number',
    'physical_product_editor.cart.delivery_address.validate.street' => 'Enter a valid delivery address',
    'physical_product_editor.cart.delivery_address.validate.building_number' => 'Enter a valid delivery building number',
    'physical_product_editor.cart.delivery_address.validate.apartment_number' => 'Enter a valid delivery address',
    'physical_product_editor.cart.delivery_address.validate.postal_code' => 'Enter a valid delivery postcode',
    'physical_product_editor.cart.delivery_address.validate.city' => 'Enter a valid delivery city',
    'physical_product_editor.cart.delivery_address.validate.first_name' => 'Enter a valid delivery receiver first name',
    'physical_product_editor.cart.delivery_address.validate.last_name' => 'Enter a valid delivery receiver last name',

    'receipt.fees' => 'Fees',

    'packages.info.you_need_to_upgrade_your_plan' => 'Available %s.',
    'packages.info.you_need_to_upgrade_your_plan_to' => 'in package %s',
    'packages.info.you_need_to_upgrade_your_plan_to.short' => '%s',
    'packages.info.you_need_to_upgrade_your_plan_to.one_of' => 'in packages %s and %s',
    'packages.info.you_need_to_upgrade_your_plan_to.one_of.short' => '%s or %s',

    'airbrake.key_and_id_set' => 'Airbrake Project ID set to "%s" and Project Key to "%s".',
    'airbrake.key_and_id_unset' => 'Airbrake configuration data has been removed.',
    'airbrake.invalid_id' => 'Invalid ID format entered',
    'airbrake.invalid_key' => 'Invalid key format entered',

    'product.available_quantities.format_x_of_y' => 'Available: %s from %s',
    'product.available_quantities.format_x' => 'Available quantities: %s',

    'product.item.category' => 'Category:',
    'product.item.categories' => 'Categories:',
    'product.item.tag' => 'Tag:',
    'product.item.tags' => 'Tags:',

    'categories.menu_title' => 'Product categories',
    'tags.menu_title' => 'Product tags',

    'quiz_editor.time_for_quiz' => 'Time for quiz',
    'quiz_editor.time_for_quiz.description' => 'Time (in minutes) to solve the quiz.',
    'quiz_editor.randomize_question_order' => 'Randomizing the order of questions',
    'quiz_editor.randomize_question_order.description' => 'Enable this option to randomize the order of questions.',
    'quiz_editor.randomize_answer_order' => 'Randomizing the order of answers',
    'quiz_editor.randomize_answer_order.description' => 'Enable this option to randomize the order of answers to a question.',

    'quiz_editor.sections.files.table.column.priority' => 'Order',
    'quiz_editor.sections.files.table.column.file_name' => 'Attachment name',
    'quiz_editor.sections.files.table.column.file_url' => 'Attachment URL',
    'quiz_editor.sections.files.table.button.browse_media' => 'Select attachment',
    'quiz_editor.sections.files.table.button.add_file' => 'Add attachment',
    'quiz_editor.sections.files.table.button.save' => 'Save',
    'quiz_editor.sections.files.table.message.saving' => 'Saving ...',
    'quiz_editor.sections.files.table.button.cancel' => 'Cancel',
    'quiz_editor.sections.files.table.message.you_have_unsaved_changes' => 'You have unsaved changes!',
    'quiz_editor.sections.files.table.message.be_careful' => 'Watch out',
    'quiz_editor.sections.files.table.message.active_files' => 'Uploaded attachment',
    'quiz_editor.sections.files.table.message.no_active_files' => 'No attachment uploaded',
    'quiz_editor.sections.files.table.message.save_success' =>'The settings have been saved!',
    'quiz_editor.sections.files.table.message.save_error' => 'An error occurred while saving. Please contact the administrator.',

    'quiz_editor.sections.files.info' => 'Here you can add optional attachments (diagrams, photos, audio files, etc.) which can be referenced in specific questions in the quiz',

    'quiz_editor.preview_button' => 'Preview quiz',
    'quiz_editor.answers_preview' => 'Preview of the answers',
    'quiz_editor.answers_preview.desc' => 'Enable this option to allow the user to check their answers after completing the quiz',
    'quiz_editor.also_show_correct_answers' => 'Also show correct answers',
    'quiz_editor.also_show_correct_answers.desc' => 'Enable this option to show the user the correct answers to the questions they answered incorrectly.',

    'quiz_editor.structure.show_question_comment_field' => 'Add answer comment',
    'quiz_editor.structure.show_question_comment_field.not_empty' => 'Show added comment',

    'quiz.answers_preview.more_correct_answers_info' => 'This question has more correct answers.',
    'quiz.answers_preview.see_answers' => 'See answers',
    'quiz.answers_preview.file.no_file' => 'No file has been sent.',
    'quiz.answers_preview.file' => 'File sent:',
    'quiz.answers_preview.empty_answer' => 'You did not answer this question.',
    'quiz.answers_preview.empty_answer_to_open_question' => 'You have not provided any answer to the open question.',
    'quiz.answers_preview.correct_answer' => 'Correct',
    'quiz.answers_preview.incorrect_answer' => 'Incorrect',
    'quiz.answers_preview.assessed_by_moderator' => 'Answer assessed individually by the moderator.',
    'quiz.answers_preview.question_comment' => 'Comment to the answer: ',

    'quiz.end_view.try_again' => 'Try again',
    'quiz.end_view.time_is_up' => 'The time for solving the quiz has expired.',
];