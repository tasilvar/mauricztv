<?php

return [
    'action_does_not_exist' => __('Action does not exist!', BPMJ_EDDCM_DOMAIN),
    'certificate_not_found' => __('Certificate not found', BPMJ_EDDCM_DOMAIN),
    'certificate_template_not_found' => __('Certificate template not found!', BPMJ_EDDCM_DOMAIN),
    'incorrect_return_params' => __('Incorrect return params', BPMJ_EDDCM_DOMAIN),
    'invalid_token' => __('Invalid token!', BPMJ_EDDCM_DOMAIN),
    'method_not_allowed' => __('Method not allowed!', BPMJ_EDDCM_DOMAIN),
    'name_exist' => __('Name exist!', BPMJ_EDDCM_DOMAIN),
    'no_permission_for_course' => __('You do not have permission for this course!', BPMJ_EDDCM_DOMAIN),
    'no_permission_for_run_action' => __('You do not have permission to run this action!', BPMJ_EDDCM_DOMAIN),
    'no_required_variables' => __('No required variables!', BPMJ_EDDCM_DOMAIN),
    'quiz_does_not_exist' => __('The quiz does not exist!', BPMJ_EDDCM_DOMAIN),
    'payment_error' => __('Payment error. Notify the administrator.', BPMJ_EDDCM_DOMAIN),
    'payment_error_details' => __('Details', BPMJ_EDDCM_DOMAIN),
    'understand' => 'Rozumiem',
    'no_limit' => 'Bez limitu',

    'course.settings.certificate_number.explanation.title' => 'Objaśnienie dla "Wzorzec numeracji":',

    'course.settings.certificate_number.explanation.text' => '
                                              <p>
                                                Skonfiguruj wzorzec numeracji certyfikatów wg własnych preferencji. Zostanie on automatycznie
                                                nadrukowany na wygenerowanym certyfikacie, jeśli szablon certyfikatu uwzględnia element o
                                                nazwie "Numer certyfikatu".
                                             </p>
                                             <p>
                                                <strong>Przykład:</strong><br>
                                                Wzorzec ZM / X / YYYY - zostanie na certyfikacie podmieniony na np. ZM / 172 / 2021, jeśli
                                                kolejny numer certyfikatu to 172, a bieżący rok to 2021.
                                             </p>
                                              <p>
                                                <strong>Dozwolone są:</strong>
                                                     <ul>
                                                    <li>wielkie i małe litery,</li>
                                                    <li>cyfry,</li>
                                                    <li>znaki: / - _ oraz spacje.</li>
                                                     </ul>
                                             </p>
                                             <p>
                                               <strong>Dostępne zmienne:</strong><br>
                                                X - zostanie zastąpiona przez kolejny numer certyfikatu (musisz ją użyć, by numeracja działała prawidłowo).<br>
                                                YY - zostanie zastąpiona przez dwucyfrowe oznaczenie bieżącego roku.<br>
                                                YYYY - zostanie zastąpiona przez czterocyfrowe oznaczenie bieżącego roku
                                            </p>',

    'dynamic_table.results_per_page' => __('Results per page', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.data_types' => __('Data types', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.data_types.hint' => __('Select which columns should be visible in the table.', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.results.showing' => __('Displaying', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.results.to' => __('to', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.results.of' => __('of', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.results.results' => __('results', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.pagination.item_x_of' => __('of', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.pagination.prev' => __('Previous', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.pagination.next' => __('Next', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.loading' => __('Loading', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.refresh' => __('Refresh data', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select' => __('Select', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.show' => __('Show filters', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.hide' => __('Hide filters', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.clear' => __('Clear filters', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.clear_one' => __('Clear filter', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.active_count' => __('Number of active filters', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.type' => __('Type to search', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date' => __('Select a date', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.today' => __('Today', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.yesterday' => __('Yesterday', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.this_week' => __('This week', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.last_week' => __('Last week', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.this_month' => __('This month', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.last_month' => __('Last month', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.from_the_start' => __('From the start', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.to_the_end' => __('To the end', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.apply' => __('Apply', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.custom_range' => __('Range', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.custom_range.days' => __('days', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.select_date.cancel' => __('Cancel', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.filters.number_range.to' => __('to', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.export' => __('Export to CSV', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.export.loading' => __('Generating CSV file...', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.cell_content.read_more' => __('expand', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.cell_content.read_less' => __('collapse', BPMJ_EDDCM_DOMAIN),
    'dynamic_table.bulk_actions' => __('Bulk actions', BPMJ_EDDCM_DOMAIN),

    'notice.pixel_caffeine' => __('%s plugin has a negative impact on the operation of WP Idea - we recommend removing and connecting Pixel through the built-in mechanism: %s',
        BPMJ_EDDCM_DOMAIN),

    'logs.page_title' => __('Logs', BPMJ_EDDCM_DOMAIN),
    'logs.menu_title' => __('Logs', BPMJ_EDDCM_DOMAIN),
    'logs.level.100' => __('Debug', BPMJ_EDDCM_DOMAIN),
    'logs.level.200' => __('Info', BPMJ_EDDCM_DOMAIN),
    'logs.level.250' => __('Notice', BPMJ_EDDCM_DOMAIN),
    'logs.level.300' => __('Warning', BPMJ_EDDCM_DOMAIN),
    'logs.level.400' => __('Error', BPMJ_EDDCM_DOMAIN),
    'logs.level.500' => __('Critical', BPMJ_EDDCM_DOMAIN),
    'logs.level.550' => __('Alert', BPMJ_EDDCM_DOMAIN),
    'logs.level.600' => __('Emergency', BPMJ_EDDCM_DOMAIN),
    'logs.delete_all' => __('Delete all logs', BPMJ_EDDCM_DOMAIN),
    'logs.source.wpi_default' => __('System', BPMJ_EDDCM_DOMAIN),
    'logs.source.wpi_invoices.fakturownia' => __('Integration with Fakturownia', BPMJ_EDDCM_DOMAIN),
    'logs.source.wpi_invoices.ifirma' => __('Integration with iFirma', BPMJ_EDDCM_DOMAIN),
    'logs.source.wpi_invoices.wfirma' => __('Integration with wFirma', BPMJ_EDDCM_DOMAIN),
    'logs.source.wpi_invoices.infakt' => __('Integration with Infakt', BPMJ_EDDCM_DOMAIN),
    'logs.source.wpi_invoices.taxe' => __('Integration with Taxe', BPMJ_EDDCM_DOMAIN),
    'logs.source.orders_source' => __('Orders', BPMJ_EDDCM_DOMAIN),
    'logs.source.communication' => __('Communication', BPMJ_EDDCM_DOMAIN),
    'logs.column.id' => __('ID', BPMJ_EDDCM_DOMAIN),
    'logs.column.created_at' => __('Date', BPMJ_EDDCM_DOMAIN),
    'logs.column.level' => __('Level', BPMJ_EDDCM_DOMAIN),
    'logs.column.source' => __('Source', BPMJ_EDDCM_DOMAIN),
    'logs.column.message' => __('Message', BPMJ_EDDCM_DOMAIN),

    'logs.log_message.user_logged_in' => __('User {login} ({email}) has just logged in from IP: {ip}.',
        BPMJ_EDDCM_DOMAIN),
    'logs.log_message.user_login_failed' => __('Nieudana próba logowania użytkownika: {login} z adresu IP: {ip}.',
        BPMJ_EDDCM_DOMAIN),
    'logs.log_message.user_registred_by_admin' => __('New user registered: {user_register_login} - {user_register_role}. Entered by: {current_user_login}',
        BPMJ_EDDCM_DOMAIN),
    'logs.log_message.user_registred_during_checkout' => __('New user registered: {user_register_login} - {user_register_role}.',
        BPMJ_EDDCM_DOMAIN),
    'logs.log_message.user_changed_permissions' => __('Changing user permissions: {edit_user_login} - {edit_user_role}. Entered by: {current_user_login}',
        BPMJ_EDDCM_DOMAIN),
    'logs.log_message.order_created' => __('Order no {order_id} for user {email} with the amount {amount} has been just created. ',
        BPMJ_EDDCM_DOMAIN),
    'logs.log_message.order_completed' => __('Order no {order_id} for user {email} with the amount {amount} has been just completed.',
        BPMJ_EDDCM_DOMAIN),
    'logs.log_message.triggering_an_webhook_event' => __('Webhook event: "{type_of_webhook}". The data has been sent to the following address: {url}. Returned response: {request}.',
        BPMJ_EDDCM_DOMAIN),

    'logs.invoices.queued' => __('An invoice has been queued to be issued for order: %s', BPMJ_EDDCM_DOMAIN),
    'logs.invoices.error' => __('An error occurred while trying to issue an invoice: %s', BPMJ_EDDCM_DOMAIN),
    'logs.invoices.success' => __('Invoice "%s" has been issued correctly for order: %s', BPMJ_EDDCM_DOMAIN),
    'logs.invoices.api_error' => __('Error communicating with API: %s', BPMJ_EDDCM_DOMAIN),

    'logs.log_message.exceeding_active_sessions_limit' => 'Podczas logowania na konto {email} wykryto przekroczenie dla niego liczby aktywnych jednocześnie sesji. Poprzednie sesje zostały usunięte. Ilość zakończonych sesji: {destroyed_sessions}',

    'webhooks.page_title' => __('Webhooks', BPMJ_EDDCM_DOMAIN),
    'webhooks.menu_title' => __('Webhooks', BPMJ_EDDCM_DOMAIN),
    'webhooks.event.order_paid' => __('Order paid', BPMJ_EDDCM_DOMAIN),
    'webhooks.event.quiz_finished' => __('Quiz finished', BPMJ_EDDCM_DOMAIN),
    'webhooks.event.certificate_issued' => 'Certyfikat został wystawiony',
    'webhooks.event.student_enrolled_in_course' => 'Student został zapisany na kurs',
    'webhooks.event.course_completed' => 'Kurs został ukończony',
    'webhooks.column.id' => __('ID', BPMJ_EDDCM_DOMAIN),
    'webhooks.column.type_of_event' => __('Type of event', BPMJ_EDDCM_DOMAIN),
    'webhooks.column.url' => __('URL address', BPMJ_EDDCM_DOMAIN),
    'webhooks.column.status' => __('Status', BPMJ_EDDCM_DOMAIN),
    'webhooks.status.active' => __('Active', BPMJ_EDDCM_DOMAIN),
    'webhooks.status.suspended' => __('Suspended', BPMJ_EDDCM_DOMAIN),
    'webhooks.actions.add_webhook' => __('Add new', BPMJ_EDDCM_DOMAIN),
    'webhooks.actions.documentation' => __('Documentation', BPMJ_EDDCM_DOMAIN),
    'webhooks.actions.edit' => __('Edit', BPMJ_EDDCM_DOMAIN),
    'webhooks.actions.edit.loading' => __('Editing...', BPMJ_EDDCM_DOMAIN),
    'webhooks.actions.delete' => __('Delete', BPMJ_EDDCM_DOMAIN),
    'webhooks.actions.delete.loading' => __('Deleting...', BPMJ_EDDCM_DOMAIN),
    'webhooks.actions.status.loading' => 'Zmieniam...',
    'webhooks.actions.status.active' => 'Aktywuj',
    'webhooks.actions.status.inactive' => 'Wstrzymaj',
    'webhooks.actions.delete.confirm' => __('Are you sure you want to delete this webhook? This action is irreversible.',
        BPMJ_EDDCM_DOMAIN),
    'webhooks.form.add' => __('Add a new webhook', BPMJ_EDDCM_DOMAIN),
    'webhooks.form.edit' => __('Edit webhook', BPMJ_EDDCM_DOMAIN),
    'webhooks.form.save' => __('Save', BPMJ_EDDCM_DOMAIN),
    'webhooks.form.cancel' => __('Cancel', BPMJ_EDDCM_DOMAIN),
    'webhooks.form.return' => __('Return', BPMJ_EDDCM_DOMAIN),
    'webhooks.form.select_option' => __('select an option', BPMJ_EDDCM_DOMAIN),
    'webhooks.documentation.title' => __('Webhooks documentation', BPMJ_EDDCM_DOMAIN),
    'webhooks.documentation.heading' => __('Format sending data for event "%s"', BPMJ_EDDCM_DOMAIN),
    'webhooks.documentation.description' => __('Description of individual fields', BPMJ_EDDCM_DOMAIN),
    'webhooks.render_page_wrong_plan' => 'Aby używać panelu zarządzania webhookami wymagana jest licencja na poziomie co najmniej: ',

    'orders.page_title' => __('Orders', BPMJ_EDDCM_DOMAIN),
    'orders.menu_title' => __('Orders', BPMJ_EDDCM_DOMAIN),
    'orders.column.id' => __('ID', BPMJ_EDDCM_DOMAIN),
    'orders.column.full_name' => __('Full name', BPMJ_EDDCM_DOMAIN),
    'orders.column.email' => __('Email', BPMJ_EDDCM_DOMAIN),
    'orders.column.phone_no' => __('Phone number', BPMJ_EDDCM_DOMAIN),
    'orders.column.date' => __('Date', BPMJ_EDDCM_DOMAIN),
    'orders.column.amount' => __('Amount', BPMJ_EDDCM_DOMAIN),
    'orders.column.value' => __('Value', BPMJ_EDDCM_DOMAIN),
    'orders.column.status' => __('Status', BPMJ_EDDCM_DOMAIN),
    'orders.column.increasing_sales_offer_type' => 'Zwiększenie sprzedaży',
    'orders.column.discount_code' => 'Kod zniżkowy',
    'orders.column.first_checkbox' => 'Checkbox 1',
    'orders.column.second_checkbox' => 'Checkbox 2',
    'orders.column.delivery_address' => 'Adres dostawy',
    'orders.column.additional_checkbox.yes' => 'tak',
    'orders.column.additional_checkbox.no' => 'nie',
    'orders.column.products' => __('Products', BPMJ_EDDCM_DOMAIN),
    'orders.column.country' => __('Country', BPMJ_EDDCM_DOMAIN),
    'orders.column.nip' => __('NIP', BPMJ_EDDCM_DOMAIN),
    'orders.column.company_name' => __('Company name', BPMJ_EDDCM_DOMAIN),
    'orders.column.details' => __('Details', BPMJ_EDDCM_DOMAIN),
    'orders.column.payment_method' => 'Metoda płatności',
    'orders.column.recurring_payments' => 'Płatności cykliczne',
    'orders.actions.add_payment' => __('Add order', BPMJ_EDDCM_DOMAIN),
    'orders.actions.delete' => __('Delete order', BPMJ_EDDCM_DOMAIN),
    'orders.actions.delete.bulk' => __('Delete orders', BPMJ_EDDCM_DOMAIN),
    'orders.actions.delete.loading' => __('Deleting...', BPMJ_EDDCM_DOMAIN),
    'orders.actions.delete.confirm' => __('Are you sure you want to delete this order? This action is irreversible.',
        BPMJ_EDDCM_DOMAIN),
    'orders.actions.delete.success' => __('The order has been successfully deleted!', BPMJ_EDDCM_DOMAIN),
    'orders.actions.delete.bulk.confirm' => __('Are you sure you want to delete these orders? This action is irreversible.',
        BPMJ_EDDCM_DOMAIN),
    'orders.actions.delete.bulk.success' => __('Orders have been successfully deleted!', BPMJ_EDDCM_DOMAIN),
    'orders.actions.see_details' => __('See order details', BPMJ_EDDCM_DOMAIN),
    'orders.actions.resend' => __('Resend email', BPMJ_EDDCM_DOMAIN),
    'orders.actions.resend.loading' => __('Sending email...', BPMJ_EDDCM_DOMAIN),
    'orders.actions.resend.success' => __('The email has been successfully sent!', BPMJ_EDDCM_DOMAIN),
    'orders.actions.resend.bulk' => __('Resend email for selected', BPMJ_EDDCM_DOMAIN),
    'orders.actions.resend.bulk.loading' => __('Sending emails...', BPMJ_EDDCM_DOMAIN),
    'orders.actions.resend.bulk.success' => __('Emails have been successfully sent!', BPMJ_EDDCM_DOMAIN),
    'orders.actions.payment.secure_ssl' => 'Jest to bezpieczna płatność zaszyfrowana SSL.',
    'orders.actions.payment.credit_card_info' => 'Informacje o karcie kredytowej',
    'orders.actions.payment.name_on_the_card' => 'Imię i nazwisko na karcie',
    'orders.actions.payment.full_name' => 'Imię i nazwisko',
    'orders.actions.payment.credit_card' => 'Karta kredytowa',
    'orders.actions.payment.billing_details' => 'Szczegóły płatności',
    'orders.actions.payment.billing_country' => 'Kraj',
    'orders.actions.payment.billing_zip' => 'Kod pocztowy',
    'orders.actions.payment.postal_code' => 'Kod pocztowy',
    'orders.status.payu_recurrent' => __('Pending (PayU recurrent payment)', BPMJ_EDDCM_DOMAIN),
    'orders.status.tpay_recurrent' => __('Pending (Tpay recurrent payment)', BPMJ_EDDCM_DOMAIN),
    'orders.unknown_email' => __('Unknown email', BPMJ_EDDCM_DOMAIN),
    'orders.invoice_data.full_name' => 'Imię i nazwisko',
    'orders.invoice_data.street' => 'Ulica',
    'orders.invoice_data.building_number' => 'Numer budynku',
    'orders.invoice_data.apartment_number' => 'Numer lokalu',
    'orders.recurring_payment.no' => 'Nie',
    'orders.recurring_payment.manual' => 'Tak - półautomatyczne',
    'orders.recurring_payment.automatic' => 'Tak - automatyczne',

    'orders.invoice_data.validate.street' => 'Wpisz prawidłową ulicę',
    'orders.invoice_data.validate.building_number' => 'Wpisz prawidłowy numer budynku',
    'orders.invoice_data.validate.apartment_number' => 'Wpisz prawidłowy numer lokalu',


    'affiliate_program.page_title' => 'Prowizje programu partnerskiego',
    'affiliate_program.menu_title' => 'Program partn.',
    'affiliate_program.partners_menu_title' => 'Partnerzy',
    'affiliate_program.column.id' => 'ID',
    'affiliate_program.column.partner_id' => 'ID partnera',
    'affiliate_program.column.partner_email' => 'E-mail partnera',
    'affiliate_program.column.partner_link' => 'Link partnerski',
    'affiliate_program.column.name' => 'Imię i nazwisko',
    'affiliate_program.column.email' => 'E-mail kupującego',
    'affiliate_program.column.sale_date' => 'Data sprzedaży',
    'affiliate_program.column.purchased_products' => 'Zakupione produkty',
    'affiliate_program.column.sales_amount' => 'Kwota sprzedaży',
    'affiliate_program.column.commission_percentage' => 'Procent prowizji',
    'affiliate_program.column.commission_amount' => 'Kwota prowizji',
    'affiliate_program.column.status' => 'Status',
    'affiliate_program.column.total_commissions' => 'Prowizje łącznie',
    'affiliate_program.column.unsettled_commissions' => 'Nierozliczone prowizje',
    'affiliate_program.column.total_sales' => 'Sprzedaż łącznie',
    'affiliate_program.actions.change_status' => 'Zmiana statusu',
    'affiliate_program.actions.change_status.confirm' => 'Czy na pewno chcesz zmienić status (rozliczony/nierozliczony)?',
    'affiliate_program.actions.change_status.loading' => 'Zmieniam ...',
    'affiliate_program.actions.change_status.bulk' => 'Zmiana statusów',
    'affiliate_program.actions.change_status.bulk.confirm' => 'Czy na pewno chcesz zmienić statusy (rozliczony/nierozliczony) dla wybranych partnerów?',
    'affiliate_program.actions.delete' => 'Usuń',
    'affiliate_program.actions.delete.confirm' => 'Czy na pewno chcesz usunąć wybraną pozycje? Ta czynność jest nieodwracalna.',
    'affiliate_program.actions.delete.loading' => 'Usuwam...',
    'affiliate_program.actions.delete.bulk' => 'Usuń zaznaczone',
    'affiliate_program.actions.delete.bulk.confirm' => 'Czy na pewno chcesz usunąć wybrane pozycje? Ta czynność jest nieodwracalna.',
    'affiliate_program.actions.add_partner' => 'Dodaj partnera',
    'affiliate_program.actions.add_partner.success' => 'Utworzono partnera!',
    'affiliate_program.status.settled' => 'Rozliczony',
    'affiliate_program.status.unsettled' => 'Nierozliczony',
    'affiliate_program.participants.page_title' => 'Program partnerski',
    'affiliate_program.participants.no_information' => 'Brak informacji',
    'affiliate_program.participants.id' => 'ID partnera',
    'affiliate_program.participants.link' => 'Link partnerski',
    'affiliate_program.participants.status' => 'Status',
    'affiliate_program.participants.status.active' => 'Aktywny',
    'affiliate_program.participants.status.inactive' => 'Nieaktywny',
    'affiliate_program.order_details.note' => 'Kupione z polecenia ',
    'affiliate_program.commissions' => 'Prowizje',
    'settings.affiliate_program' => 'Program partnerski',
    'settings.affiliate_program.commission_amount' => 'Wysokość prowizji',
    'settings.affiliate_program.licence_notice' => 'Zmień pakiet: Aby korzystać z programu partnerskiego musisz zmienic swoją licence na PRO.',

    'affiliate_program_redirections.page_title' => 'Generator linków partnerskich',
    'affiliate_program_redirections.menu_title' => 'Generator linków',
    'affiliate_program_redirections.actions.add' => 'Dodaj link',
    'affiliate_program_redirections.column.id' => 'ID',
    'affiliate_program_redirections.column.product' => 'Produkt',
    'affiliate_program_redirections.column.url' => 'Zewnętrzny adres URL',
    'affiliate_program_redirections.actions.delete' => 'Usuń',
    'affiliate_program_redirections.actions.delete.confirm' => 'Czy na pewno chcesz usunąć?',
    'affiliate_program_redirections.actions.delete.loading' => 'Usuwanie...',
    'affiliate_program_redirections.actions.add.page_title' => 'Dodaj link',
    'affiliate_program_redirections.actions.add.select_product' => 'Wybierz produkt',
    'affiliate_program_redirections.actions.add.save' => 'Zapisz',
    'affiliate_program_redirections.actions.add.cancel' => 'Wróć',

    'customers.page_title' => 'Klienci',
    'customers.menu_title' => 'Klienci',
    'customers.column.id' => 'ID',
    'customers.column.name' => 'Imię i nazwisko',
    'customers.column.email' => 'E-mail',
    'customers.column.purchases' => 'Zamówienia',
    'customers.column.total_spent' => 'Łączne wydatki',
    'customers.column.date_created' => 'Klientem od',
    'customers.actions.delete' => 'Usuń klienta',
    'customers.actions.delete.bulk' => 'Usuń klientów',
    'customers.actions.delete.loading' => 'Usuwanie...',
    'customers.actions.delete.confirm' => 'Czy na pewno chcesz usunąć tego klienta? To działanie jest nieodwracalne.',
    'customers.actions.delete.success' => 'Klient został pomyślnie usunięty!',
    'customers.actions.delete.bulk.confirm' => 'Czy na pewno chcesz usunąć tych klientów? To działanie jest nieodwracalne.',
    'customers.actions.delete.bulk.success' => 'Klienci zostali pomyślnie usunięci!',
    'customers.actions.data' => 'Zobacz dane kupującego',

    'students.column.id' => __('ID', BPMJ_EDDCM_DOMAIN),
    'students.column.username' => __('Username', BPMJ_EDDCM_DOMAIN),
    'students.column.name' => __('Name', BPMJ_EDDCM_DOMAIN),
    'students.column.courses' => __('Courses', BPMJ_EDDCM_DOMAIN),
    'students.column.email' => __('E-mail', BPMJ_EDDCM_DOMAIN),
    'students.column.user_login' => __('User login', BPMJ_EDDCM_DOMAIN),
    'students.page_title' => __('Students', BPMJ_EDDCM_DOMAIN),
    'students.menu_title' => __('Students', BPMJ_EDDCM_DOMAIN),
    'students.edit' => __('Edit', BPMJ_EDDCM_DOMAIN),
    'logs.emails.send_attempt' => __('Sending email: ', BPMJ_EDDCM_DOMAIN),
    'logs.emails.send_failed' => __('Sending failed: ', BPMJ_EDDCM_DOMAIN),
    'logs.emails.to' => __('Recipient: ', BPMJ_EDDCM_DOMAIN),
    'logs.emails.subject' => __('Subject: ', BPMJ_EDDCM_DOMAIN),
    'logs.emails.source' => __('Communication', BPMJ_EDDCM_DOMAIN),
    'courses.participants' => __('Participants', BPMJ_EDDCM_DOMAIN),

    'quizzes.page_title' => __('Solved quizzes', BPMJ_EDDCM_DOMAIN),
    'quizzes.menu_title' => __('Quizzes', BPMJ_EDDCM_DOMAIN),
    'quizzes.not_rated_quizzes' => __('Not rated quizzes', BPMJ_EDDCM_DOMAIN),
    'quizzes.column.id' => __('Id', BPMJ_EDDCM_DOMAIN),
    'quizzes.column.course' => __('Course', BPMJ_EDDCM_DOMAIN),
    'quizzes.column.title' => __('Quiz title', BPMJ_EDDCM_DOMAIN),
    'quizzes.column.email' => __('E-mail', BPMJ_EDDCM_DOMAIN),
    'quizzes.column.full_name' => __('Full name', BPMJ_EDDCM_DOMAIN),
    'quizzes.column.points' => __('Points', BPMJ_EDDCM_DOMAIN),
    'quizzes.column.result' => __('Result', BPMJ_EDDCM_DOMAIN),
    'quizzes.column.date' => __('Completed at', BPMJ_EDDCM_DOMAIN),
    'quizzes.actions.edit_quiz' => __('Edit quiz', BPMJ_EDDCM_DOMAIN),
    'quizzes.actions.show_answers' => __('Show quiz answers', BPMJ_EDDCM_DOMAIN),
    'quizzes.actions.show_student_profile' => __('Show student profile', BPMJ_EDDCM_DOMAIN),

    'quiz.result.not_rated_yet' => __('Test not rated yet', BPMJ_EDDCM_DOMAIN),
    'quiz.result.passed' => __('Test passed', BPMJ_EDDCM_DOMAIN),
    'quiz.result.failed' => __('Test failed', BPMJ_EDDCM_DOMAIN),

    'admin_menu_mode.switch_to_wp_menu' => __('Back to Dashboard', BPMJ_EDDCM_DOMAIN),
    'admin_menu_mode.switch_to_lms_menu' => __('WP Idea', BPMJ_EDDCM_DOMAIN),

    'admin_bar.bpmj_main' => __('WP Idea', BPMJ_EDDCM_DOMAIN),
    'admin_bar.support' => __('Support', BPMJ_EDDCM_DOMAIN),
    'admin_bar.license_info' => __('You are a %1$s package user', BPMJ_EDDCM_DOMAIN),
    'admin_bar.free_space' => __('The current consumption of the available space for files other than Vimeo stored videos is %s out of %s GB.',
        BPMJ_EDDCM_DOMAIN),
    'admin_bar.free_video_storage_space' => 'Obecne zużycie dostępnej przestrzeni dyskowej dla plików wideo wynosi %s z %s.',
    'admin_bar.free_video_storage_space_and_traffic' => 'Obecne zużycie dostępnej przestrzeni dyskowej dla plików wideo wynosi %s z %s. Wykorzystano także %s z %s dostępnego transferu wideo w obecnym miesiącu.',

    'certificates.page_title' => __('Certificates', BPMJ_EDDCM_DOMAIN),
    'certificates.column.id' => __('Id', BPMJ_EDDCM_DOMAIN),
    'certificates.column.course' => __('Course', BPMJ_EDDCM_DOMAIN),
    'certificates.column.full_name' => __('Name', BPMJ_EDDCM_DOMAIN),
    'certificates.column.email' => __('Email', BPMJ_EDDCM_DOMAIN),
    'certificates.column.certificate_number' => 'Numer certyfikatu',
    'certificates.column.created' => __('Created', BPMJ_EDDCM_DOMAIN),
    'certificates.regenerate' => __('Regenerate', BPMJ_EDDCM_DOMAIN),
    'certificates.download' => __('Download', BPMJ_EDDCM_DOMAIN),

    'edit_courses.certificate_numbering.enable' => 'Włącz numerację certyfikatów',
    'edit_courses.certificate_numbering.pattern' => 'Wzorzec numeracji',
    'edit_courses.certificate_numbering.error' => 'Uwaga! Podany przez Ciebie wzorzec numeracji nie zawiera parametru „X”, który ma za zadanie wygenerować numerację.',
    'edit_courses.max_input_vars.error' => 'Uwaga! Przy ostatniej próbie zapisu struktury kursu wystąpił błąd spowodowany niską wartością max_input_vars w PHP. Skontaktuj się ze swoim hostingodawcą.',

    'resources.type.course' => __('Course', BPMJ_EDDCM_DOMAIN),
    'resources.type.digital_product' => __('Digital product', BPMJ_EDDCM_DOMAIN),
    'resources.type.service' => 'Usługa',

    'digital_products.name' => 'Nazwa produktu cyfrowego',
    'digital_products.mailer.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do produktu cyfrowego',

    'media.limit_checker.title' => 'Plik nie został przesłany',
    'media.limit_checker.error' => __('Due to the limit being exceeded, your current WP Idea package is not able to handle more files of the transferred type. Please contact WP Idea Technical Support to present possible solutions.',
        BPMJ_EDDCM_DOMAIN),

    'media.video_format_blocker.title' => 'Niedozwolony format pliku',
    'media.video_format_blocker.error' => 'Jeśli chcesz przesłać pliki wideo, skorzystaj z dedykowanej do tego celu podstrony %1$s Wideo %2$s.',

    'notifications.cron_not_working_correctly' => 'Nasz system wykrył, że CRON na Twoim serwerze może nie działać poprawnie. Skontaktuj się ze swoim administratorem lub opiekunem strony, by sprawdzić jego poprawność działania.',

    'role.content_manager' => 'Menedżer treści',
    'role.partner' => 'Partner',

    'discount_codes.page_title' => 'Kody zniżkowe',
    'discount_codes.column.id' => 'ID',
    'discount_codes.column.name' => 'Nazwa kodu',
    'discount_codes.column.code' => 'Kod',
    'discount_codes.column.amount' => 'Wartość',
    'discount_codes.column.uses' => 'Ilość użyć',
    'discount_codes.column.start_date' => 'Data początkowa',
    'discount_codes.column.end_date' => 'Wygasa',
    'discount_codes.column.status' => 'Status',
    'discount_codes.status.active' => 'Aktywny',
    'discount_codes.status.inactive' => 'Nieaktywny',
    'discount_codes.status.expired' => 'Wygasł',
    'discount_codes.actions.add' => 'Dodaj kod zniżkowy',
    'discount_codes.actions.add.success' => 'Kod zniżkowy został dodany!',
    'discount_codes.actions.generate' => 'Generuj wiele kodów',
    'discount_codes.actions.edit' => 'Edytuj',
    'discount_codes.actions.edit.success' => 'Kod zniżkowy został zaktualizowany!',
    'discount_codes.actions.delete' => 'Usuń',
    'discount_codes.actions.delete.confirm' => 'Czy na pewno chcesz usunąć ten kod zniżkowy? Ta czynność jest nieodwracalna.',
    'discount_codes.actions.delete.loading' => 'Usuwam...',
    'discount_codes.actions.delete.success' => 'Kod zniżkowy został usunięty!',
    'discount_codes.plan_error.title' => 'Generator kodów zniżkowych',
    'discount_codes.plan_error.message' => 'Aby używać generatora kodów zniżkowych musisz zmienić swoją licencję na plan: %s lub %s.',

    'invoices.vat_rate.is_vat_payer' => 'Czy sprzedawca jest aktywnym płatnikiem VAT?',
    'invoices.vat_rate.is_vat_payer.yes' => 'Tak',
    'invoices.vat_rate.is_vat_payer.no' => 'Nie',
    'invoices.vat_rate.default_vat_rate' => 'Domyślna stawka VAT',
    'invoices.vat_rate.default_vat_rate.desc' => 'W procentach. Zastosowuje się tylko do aktywnych płatników VAT. Stawka może być również ustalona osobno dla każdego produktu.',
    'invoices.vat_rate' => 'Stawka VAT',
    'invoices.vat_rate.empty' => 'Pozostaw puste by zastosować stawkę domyślną:',
    'invoices.flat_rate_tax_symbol' => 'Podatek zryczałtowany',
    'invoices.no_flat_rate_tax' => 'Brak wybranej stawki',
    'invoices.warning_flat_rate_tax_not_supported' => 'Uwaga! Podatek zryczałtowany nie jest obsługiwany przez system: Taxe',
    'admin_courses.cant_remove_bundled_course' => 'Nie możesz usunąć tego kursu, ponieważ jest on przypisany do przynajmniej jednego pakietu. Usuń go ze wszystkich pakietów, a potem ponów próbę usunięcia.',
    'admin_courses.cant_remove_bundled_course.bulk' => 'Niektóre kursy zostały pominięte, ponieważ są przypisane do pakietów.',
    'admin_courses.participants' => 'Uczestnicy',

    'user_account.account_settings' => 'Ustawienia konta',
    'user_account.account_settings.details' => 'Podstawowe dane użytkownika, zmiana hasła',
    'user_account.my_courses' => 'Moje kursy',
    'user_account.my_courses.details' => 'Lista kursów, zarządzanie subskrypcjami',
    'user_account.my_digital_products' => 'Moje produkty cyfrowe',
    'user_account.my_digital_products.details' => 'Lista produktów cyfrowych',
    'user_account.my_services' => 'Moje usługi',
    'user_account.my_services.details' => 'Lista usług, zarządzanie subskrypcjami',
    'user_account.my_certificates' => 'Moje certyfikaty',
    'user_account.my_certificates.details' => 'Lista wygenerowanych certyfikatów',
    'user_account.my_certificates.product_name' => 'Nazwa produktu',
    'user_account.my_certificates.download_certificate' => 'Pobierz certyfikat',
    'user_account.my_certificates.download' => 'Pobierz',
    'user_account.orders' => 'Historia transakcji',
    'user_account.orders.details' => 'Lista oraz szczegóły transakcji',
    'user_account.partner_program' => 'Program partnerski',
    'user_account.partner_program.title' => 'Podstawowe informacje',
    'user_account.partner_program.details' => 'Podstawowe informacje, Linki partnerskie',
    'user_account.orders.send_invoice_again.done' => 'Prośba o ponowne wysłanie dokumentu sprzedaży została przekazana do systemu fakturującego.',
    'user_account.orders.send_invoice_again.something_went_wrong' => 'Ups! Coś poszło nie tak, skontaktuj się proszę z administratorem platformy.',
    'user_account.orders.invoice' => 'Faktury',
    'user_account.orders.send_on_email' => 'Wyślij na e-mail',
    'user_account.history_transaction.login_page' => 'Musisz się zalogować, aby zobaczyć historię transakcji.',
    'user_account.opinions' => 'Wystaw opinię',
    'user_account.opinions.details' => 'Oceń kursy i produkty, z których korzystałeś',
    'user_account.opinions.add.save' => 'Wystaw opinię',
    'user_account.opinions.add.label.reviewer_name' => 'Imię (możesz zmienić %stutaj%s)',
    'user_account.opinions.add.label.reviewed_product' => 'Produkt',
    'user_account.opinions.add.label.opinion_content' => 'Treść opinii',
    'user_account.opinions.add.label.rating' => 'Ocena',
    'user_account.opinions.add.select.select_product' => 'Wybierz produkt',
    'user_account.opinions.add.add_opinion_info' => 'Dodając opinię akceptujesz <a href="%s" target="_blank">regulamin</a>.',
    'user_account.opinions.add.saving_error' => 'Podczas zapisywania wystąpił błąd.',
    'user_account.opinions.add.no_product_to_review' => 'Nie masz żadnych produktów lub wszystkie zostały już ocenione.',

    'user_account.my_partner_profile.external_landing_links.title' => 'Linki do zewnętrznych stron promocyjnych (tworzone przez administratora)',
    'user_account.my_partner_profile.external_landing_links.id' => 'ID',
    'user_account.my_partner_profile.external_landing_links.product' => 'Produkt',
    'user_account.my_partner_profile.external_landing_links.link' => 'Link partnerski',
    'user_account.my_partner_profile.external_landing_links.info' => 'Brak linków.',
    'user_account.my_commissions.info' => 'Brak prowizji!',

    'user_account.my_partner_profile.campaign.title' => 'Jak sprawdzić, które źródła konwertują?',
    'user_account.my_partner_profile.campaign.info' => 'Jeśli używasz różnych kanałów komunikacji w trakcie promocji i chcesz wiedzieć, które są skuteczne i generują sprzedaż w ramach programu partnerskiego to zapoznaj się z tym wpisem w bazie wiedzy Publigo: <a href="https://poznaj.publigo.pl/articles/224298-jak-ledzi-kampanie-w-programie-partnerskim" target="_blank">https://poznaj.publigo.pl/articles/224298-jak-ledzi-kampanie-w-programie-partnerskim</a>',

    'user_account.affiliate_program.commissions' => 'Program partnerski - Prowizje',
    'user_account.my_commissions.id' => 'ID',
    'user_account.my_commissions.campaign' => 'Kampania',
    'user_account.my_commissions.sales_amount' => 'Kwota sprzedaży',
    'user_account.my_commissions.commission_amount' => 'Kwota prowizji',
    'user_account.my_commissions.sale_date' => 'Data sprzedaży',
    'user_account.my_commissions.status' => 'Status',

    'purchase_redirections.menu_title' => 'Przekierowania',
    'purchase_redirections.page_title' => 'Przekierowania po zakupie',
    'purchase_redirects.purchased_product' => 'Nazwa zakupionego produktu',
    'purchase_redirects.redirect_url' => 'Adres URL przekierowania po zakupie',
    'purchase_redirects.priority' => 'Priorytet',
    'purchase_redirects.active_rules' => 'Aktywne reguły przekierowań',
    'purchase_redirects.no_active_rules' => 'Nie zdefiniowano żadnych reguł przekierowań',
    'purchase_redirects.new_rule' => 'Nowe przekierowanie',
    'purchase_redirects.new_condition' => 'Dodaj warunek',
    'purchase_redirects.remove_condition' => 'Usuń warunek',
    'purchase_redirects.condition.and' => 'i kupił także',
    'purchase_redirects.condition.or' => 'lub kupił',
    'purchase_redirects.select_product' => 'Wybierz produkt',
    'purchase_redirects.enter_url' => 'Wpisz adres URL',
    'purchase_redirects.save' => 'Zapisz',
    'purchase_redirects.saving' => 'Zapisywanie...',
    'purchase_redirects.be_careful' => 'Uważaj',
    'purchase_redirects.you_have_unsaved_changes' => 'masz niezapisane zmiany',
    'purchase_redirects.reset_changes' => 'Cofnij zmiany',
    'purchase_redirects.save_success' => 'Zapisano pomyślnie!',
    'purchase_redirects.save_error' => 'Podczas zapisywania wystąpił błąd. Skontaktuj się z administratorem witryny.',
    'purchase_redirects.remove_rule' => 'Usuń przekierowanie',
    'purchase_redirects.wrong_plan' => 'Aby korzystać z panelu zarządzania przekierowaniami, musisz zaktualizować licencję do poziomu: ',

    'services.services' => 'Usługi',
    'services.add_service' => 'Dodaj usługę',
    'services.edit_service' => 'Edytuj usługę',
    'services.your_services' => 'Twoje usługi',
    'services.your_services.you_do_not_have_any_yet' => 'Nie utworzyłeś jeszcze żadnych usług!',
    'services.your_services.create_one_in_creator' => 'Utwórz usługę z pomocą kreatora',
    'services.your_services.view_service' => 'Zobacz usługę',
    'services.your_services.create' => 'Utwórz nową usługę',
    'services.your_services.service_name' => 'Nazwa usługi',
    'services.creator.title' => 'Utwórz usługę',
    'services.creator.saving_in_progress' => 'Twoja usługa jest zapisywana...',
    'services.creator.saving_in_progress.title' => 'Zapisywanie usługi',
    'services.creator.save.success' => 'Usługa została zapisana!',
    'services.creator.step_button.configure_integration' => 'Skonfiguruj integracje',
    'services.creator.step_button.save_service' => 'Zapisz usługę',
    'services.creator.step_name.details' => 'Szczegóły usługi',
    'services.creator.step_name.integrations' => 'Integracje',
    'services.creator.enter_service_name_here' => 'Wpisz tutaj nazwę swojej usługi.',
    'services.creator.how_much_costs' => 'Ile kosztuje usługa? Wpisz 0 jeśli chcesz udostępniać produkt bezpłatnie.',
    'services.settings.enable_services' => 'Włącz funkcjonalność usług',
    'services.settings.enable_services.desc' => 'Po włączeniu będziesz mógł sprzedawać usługi.',
    'services.editor.banner' => ' Baner usługi',
    'services.editor.enable_recurring_payments' => 'Włącz płatności cykliczne dla tej usługi',
    'services.editor.access_time_hint' => 'Jak długo użytkownik powinien mieć dostęp do usługi? Zostaw puste, aby nie ograniczać czasu dostępu.',
    'services.editor.no_access_to_access_time_message' => 'Aby móc ograniczyć czas dostępu do usługi musisz posiadać co najmniej pakiet: %s.',
    'services.mailer.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',
    'services.editor.promote_product' => 'Promuj ten produkt na stronie głównej',
    'services.editor.promote_service' => 'Promuj usługę na stronie głównej',
    'services.page_title' => 'Usługi',
    'services.column.id' => 'ID',
    'services.column.name' => 'Nazwa usługi',
    'services.column.show' => 'Pokaż',
    'services.column.sales' => 'Sprzedaż',

    'services.sales.status.enabled' => 'Włączona',
    'services.sales.status.disabled' => 'Wyłączona',

    'services.actions.create_service' => 'Utwórz nową usługę',
    'services.actions.edit' => 'Edytuj',
    'services.actions.duplicate' => 'Duplikuj',
    'services.actions.duplicate.not_available_in_your_package' => 'Duplikowanie dostępne jest %s.',
    'services.actions.delete' => 'Usuń',
    'services.actions.delete.success' => 'Usługa została pomyślnie usunięta!',
    'services.actions.delete.loading' => 'Usuwam...',
    'services.actions.delete.confirm' => 'Czy na pewno chcesz usunąć wybraną usługę? Ta czynność jest nieodwracalna.',
    'services.actions.sales.bulk' => 'Włącz / wyłącz sprzedaż',
    'services.actions.sales.active' => 'Włącz sprzedaż',
    'services.actions.sales.inactive' => 'Wyłącz sprzedaż',
    'services.actions.sales.loading' => 'Zmieniam...',
    'services.actions.delete.error' => 'Podczas usuwania wystąpił błąd. Skontaktuj się z administratorem witryny.',
    'services.actions.delete.info' => 'Nie możesz usunąć tej usługi, ponieważ jest on przypisany do przynajmniej jednego pakietu. Usuń go ze wszystkich pakietów, a potem ponów próbę usunięcia.',
    'services.buttons.service_panel.tooltip' => 'Zobacz usługę',

    'services.popup.close' => 'Zamknij',
    'services.popup.purchase_links.title' => 'Linki zakupowe',
    'services.buttons.purchase_links.tooltip' => 'Linki zakupowe',
    'services.buttons.digital_product_panel.tooltip' => 'Panel usługi',

    'templates_system.classic.undeveloped' => '(nierozwijany)',
    'templates_system.scarlet.activation_warning' => 'Uwaga! Szablon Klasyczny nie jest już rozwijany. Przełączenie się na szablon Scarlet, sprawi, że nie będzie już możliwości powrotu do szablonu Klasycznego. Czy chcesz kontynuować?',
    'templates_system.scarlet.settings.products_list_page' => 'Strona z listą produktów',

    'edit_course.start_date_warning' => 'Uwaga! Zmiana daty będzie miała wpływ tylko na nowych kursantów. Ci, którzy dokonali zakupu przed jej zmianą, uzyskają dostęp według pierwotnych ustawień.',

    'template_name.search_results' => 'Strona wyników wyszukiwania',
    'template_name.search_results.description' => 'Szablon strony, na której znajduje się blok wyszukiwarki oraz wyświetlane są wyniki wyszukiwania',
    'template_name.search_page' => 'Strona wyników wyszukiwania',
    'template_name.search_page.description' => 'Szablon strony, na której znajduje się blok wyszukiwarki oraz wyświetlane są wyniki wyszukiwania',
    'template_name.cart_page' => 'Zamówienie',
    'template_name.cart_page.description' => 'Szablon strony koszyka, na której m.in. wyświetlany jest  również formularz zamówienia',
	'template_name.experimental_cart_page' => 'Zamówienie',
	'template_name.experimental_cart_page.description' => 'Szablon strony koszyka, na której m.in. wyświetlany jest  również formularz zamówienia (widok eksperymentalny)',
	'template_name.category_page' => 'Strona kategorii',
    'template_name.category_page.description' => 'Szablon strony kategorii, na której wyświetlane są wszystkie produktu przypisane do danej kategorii',
    'template_name.course_lesson_page' => 'Lekcja kursu',
    'template_name.course_lesson_page.description' => 'Szablon strony dla dowolnej z lekcji kursu',
    'template_name.course_module_page' => 'Moduł kursu',
    'template_name.course_module_page.description' => 'Szablon strony dla dowolnego z modułów kursu',
    'template_name.course_offer_page' => 'Oferta',
    'template_name.course_offer_page.description' => 'Szablon strony opisu produktu widocznego przed zakupem - dla niezalogowanego użytkowników',
    'template_name.course_panel_page' => 'Panel kursu',
    'template_name.course_panel_page.description' => 'Szablon strony głównej danego kursu, na której zestawiona jest lista dostępnych modułów / lekcji',
    'template_name.course_quiz_page' => 'Quiz',
    'template_name.course_quiz_page.description' => 'Szablon strony, na której znajduje się blok quizu / testów',
    'template_name.products_page' => 'Lista produktów',
    'template_name.products_page.description' => 'Szablon domyślnej strony głównej platformy, zestawiającej listę produktów znajdujących się aktualnie w sprzedaży',
    'template_name.tag_page' => 'Strona tagu',
    'template_name.tag_page.description' => 'Szablon strony tagu, na której wyświetlane są wszystkie produkty z danym tagiem',
    'template_name.user_account_page' => 'Konto użytkownika',
    'template_name.user_account_page.description' => 'Szablon strony Moje konto, zestawiającej informacje istotne z perspektywy użytkownika / klienta',

    'search_results.block_name' => 'Wyszukiwarka',
    'search_results.page_title' => 'Wyszukiwanie',
    'search_results.type_to_search' => 'Wpisz frazę, którą chcesz wyszukać',
    'search_results.search' => 'Szukaj',
    'search_results.you_will_see_results_here' => 'Wyniki Twojego wyszukiwania pojawią się tutaj.',
    'search_results.no_search_results' => 'Brak wyników wyszukiwania.',
    'search_results.no_search_results.try_other_phrase' => 'Spróbuj wpisać inną frazę',
    'search_results.results_count' => 'Liczba wyników spełniających kryteria',

    'settings.active_sessions_limiter' => 'Włącz funkcjonalność limitu logowania do konta',
    'settings.active_sessions_limiter.desc' => 'Gdy ta opcja jest włączona, będziesz mieć możliwość ustawienia limitu użytkowników, którzy będą mogli jednocześnie zalogować się do tego samego konta.',
    'settings.max_active_sessions_number' => 'Limit użytkowników',
    'settings.active_sessions_limiter.license' => 'Zmień pakiet: Aby korzystać z limitu logowania musisz zmienic swoją licence na PLUS lub PRO.',
    'settings.main.payment_methods' => 'Formy płatności',
    'settings.main.payment_methods.traditional_transfer' => 'Przelew tradycyjny',
    'settings.main.payment_methods.traditional_transfer.name' => 'Firma / Imię i nazwisko',
    'settings.main.payment_methods.traditional_transfer.address' => 'Adres',
    'settings.main.payment_methods.traditional_transfer.account_number' => 'Nr konta',
    'settings.main.payment_methods.traditional_transfer.transfer_details' => 'Dane do przelewu',
    'settings.main.payment_methods.traditional_transfer.empty' => 'W celu dokonania płatności za zakupione produkty, skontaktuj się ze sprzedającym.',

    'settings.messages.purchase_subject' => 'Wpisz tytuł wiadomości wysyłanej do kupującego po zaksięgowaniu wpłaty za produkt.',
    'settings.messages.purchase_heading' => 'Wpisz nagłówek wiadomości wysyłanej do kupującego po zaksięgowaniu wpłaty za produkt.',

    'settings.messages.payment_reminders' => 'Włącz odzyskiwanie utraconych zamówień',
    'settings.messages.payment_reminders.notice' => 'Zmień pakiet: Aby korzystać z funkcjonalności odzyskiwania utraconych koszyków musisz zmienić swoją licence na PRO.',
    'settings.messages.payment_reminders.desc' => 'Włącza mechanizm wysyłający wiadomość do użytkowników, którzy nie dokonali płatności.',
    'settings.messages.payment_reminders.number_days' => 'Ilość dni, po których wysyłana jest wiadomość w przypadku braku płatności',
    'settings.messages.payment_reminders.message_subject' => 'Temat wiadomości wysyłanej w przypadku braku płatności',

    'color_settings.scarlet.header.general_settings' => '%s Ustawienia ogólne %s',
    'color_settings.scarlet.bg_color' => 'Tło główne',
    'color_settings.scarlet.generic_white_color' => 'Nazwa produktu promowanego na stronie głównej / %1$s Kwota na liście produktów / %1$s Liczba produktów w koszyku / %1$s Tekst na przyciskach nawigacyjnych w lekcji',
    'color_settings.scarlet.default_img_bg_color' => 'Tło obrazka wyróżniającego produkt na liście / %s Tło na stronie logowania',
    'color_settings.scarlet.content_header_color' => 'Nagłówki w treściach',
    'color_settings.scarlet.main_color' => 'Kolor przycisków / %s Ikonki w menu kursu',
    'color_settings.scarlet.main_color_hover' => 'Kolor przycisków po najechaniu',
    'color_settings.scarlet.inactive_border_color' => 'Dolne obramowanie przycisków',
    'color_settings.scarlet.breadcrumbs_color' => 'Nawigacja okruszkowa (Breadcrumbs)',
    'color_settings.scarlet.tab_alt_text_color' => 'Pozostałe linki / %1$s  Nagłówki strona zamówienia (koszyk) / %1$s Nagłówki Moje konto / %1$s Menu Moje konto',
    'color_settings.scarlet.price_bg_color' => 'Tło pod cenami',

    'color_settings.scarlet.header.the_menu_bar' => '%s Pasek menu %s',
    'color_settings.scarlet.content_header_bg_color' => 'Kolor paska menu',
    'color_settings.scarlet.link_color' => 'Linki',
    'color_settings.scarlet.menu_link_bg_color' => 'Tło linków w podmenu',
    'color_settings.scarlet.menu_bg_color' => 'Tło linków w podmenu - urządzenia mobilne',
    'color_settings.scarlet.menu_link_hover_color' => 'Tło linków w podmenu po najechaniu',
    'color_settings.scarlet.menu_link_border_color' => 'Obramowanie wokół linków w podmenu',
    'color_settings.scarlet.menu_border_color' => 'Obramowanie wokół linków w podmenu - urządzenia mobilne',

    'color_settings.scarlet.header.footer' => '%s Stopka %s',
    'color_settings.scarlet.footer_bg_color' => 'Kolor stopki',
    'color_settings.scarlet.footer_text_color' => 'Tekst stopki',

    'color_settings.scarlet.header.forms_order_page' => '%s Formularze / Strona zamówienia (koszyk) %s',
    'color_settings.scarlet.main_text_color' => 'Główne treści',
    'color_settings.scarlet.main_border_color' => 'Ramki w formularzach',
    'color_settings.scarlet.placeholder_color' => 'Tekst podpowiedzi w polach formularza',
    'color_settings.scarlet.form_text_color' => 'Tekst "pole wymagane"',
    'color_settings.scarlet.cart_border_color' => 'Ramki w koszyku',
    'color_settings.scarlet.cart_promotion_price_color' => 'Cena regularna',
    'color_settings.scarlet.cart_summary_price_color' => 'Kwota łączna w formularzu zamówienia',
    'color_settings.scarlet.discount_code_color' => 'Element Kod zniżkowy',

    'color_settings.scarlet.header.login_page' => '%s Strona logowania %s',
    'color_settings.scarlet.login_input_placeholder' => 'Tekst podpowiedzi w polach formularza logowania',
    'color_settings.scarlet.login_label_color' => 'Treść checkboxów',

    'color_settings.scarlet.header.list_of_products' => '%s Lista produktów (strona główna) %s',
    'color_settings.scarlet.main_box_border_color' => 'Ramka wokół produktów',
    'color_settings.scarlet.default_img_color' => 'Kolor ikonki obrazka wyróżniającego produktu na liście',
    'color_settings.scarlet.price_available_color' => 'Napis "dostępny" po zakupie (zamiast ceny)',
    'color_settings.scarlet.promotion_price_color' => 'Tło ceny regularnej przy włączonej promocyjnej',
    'color_settings.scarlet.category_link_color' => 'Link kategorii',
    'color_settings.scarlet.display_mode_color' => 'Napis "widok" przy zmianie trybu wyświetlania',
    'color_settings.scarlet.display_mode_icon_color' => 'Tryb wyświetlania - ikonki nieaktywne',
    'color_settings.scarlet.display_mode_active_icon_color' => 'Tryb wyświetlania - ikonka aktywna',

    'color_settings.scarlet.header.course_pages' => '%s Strony kursowe %s',
    'color_settings.scarlet.stage_text_color' => 'Link aktywny oraz po najechaniu w menu kursu',
    'color_settings.scarlet.lesson_completed_link_color' => 'Aktywny link lekcji w menu kursu',
    'color_settings.scarlet.stage_bg_color' => 'Tło linków po najechaniu w menu kursu',
    'color_settings.scarlet.box_border_color' => 'Ramki w menu kursu',
    'color_settings.scarlet.stage_text_border_color' => 'Ramki wewnętrzne w menu kursu',
    'color_settings.scarlet.course_stage_line_color' => 'Pionowe linie w menu kursu',
    'color_settings.scarlet.lesson_text_color' => 'Opisy modułów na liście',
    'color_settings.scarlet.completed_lesson_input_border' => 'Ramka przy zaznaczeniu ukończonej lekcji',
    'color_settings.scarlet.lesson_top_bg_color' => 'Kolor paska informacyjnego w lekcjach',
    'color_settings.scarlet.lesson_top_text_color' => 'Tekst na pasku informacyjnym w lekcjach',
    'color_settings.scarlet.lesson_icon_color' => 'Kolor przycisków quiz',
    'color_settings.scarlet.quiz_summary_img_bg_color' => 'Ikona pucharu w podsumowaniu quizu - tło',
    'color_settings.scarlet.quiz_summary_img_frame_color' => 'Ikona pucharu w podsumowaniu quizu - mniejsza ramka',
    'color_settings.scarlet.quiz_summary_img_border_color' => 'Ikona pucharu w podsumowaniu quizu - większa ramka',

    'color_settings.scarlet.header.comments' => '%s Komentarze %s',
    'color_settings.scarlet.comments_third_color' => 'Komentarze oraz komunikaty moderacyjne',

    'videos.menu_title' => 'Wideo',
    'videos.page_title' => 'Wideo',
    'videos.column.id' => 'ID',
    'videos.column.name' => 'Nazwa',
    'videos.column.file_size' => 'Rozmiar pliku',
    'videos.column.length' => 'Długość',
    'videos.column.date_created' => 'Data dodania',
    'videos.column.actions.add_video' => 'Dodaj wideo',
    'videos.column.actions.delete' => 'Usuń',
    'videos.actions.delete.confirm' => 'Czy na pewno chcesz usunąć wybrany plik wideo?',
    'videos.actions.delete.loading' => 'Usuwam...',
    'videos.actions.delete.bulk' => 'Usuń zaznaczone',
    'videos.actions.delete.bulk.confirm' => 'Czy na pewno chcesz usunąć wybrane pliki wideo?',
    'videos.column.actions.processing' => 'Przetwarzanie...',
    'videos.column.actions.edit_settings' => 'Edytuj ustawienia',

    'media.submenu.other_media.title' => 'Pozostałe media',

    'packages.packages' => 'Pakiety',
    'packages.add_package' => 'Dodaj pakiet',

    'courses.edit_course' => 'Edytuj kurs',
    'courses.edit_module' => 'Edytuj moduł',
    'courses.edit_lesson' => 'Edytuj lekcję',
    'courses.edit_test' => 'Edytuj quiz',
    'courses.settings.courses_enable' => 'Włącz funkcjonalność kursów',
    'courses.settings.courses_enable.desc' => 'Po włączeniu będziesz mógł sprzedawać kursy.',
    'courses.settings.courses_enable.is_courses' => 'By wyłączyć tę funkcjonalność, usuń wszystkie kursy.',

    'digital_products.settings.digital_products_enable' => 'Włącz funkcjonalność produktów cyfrowych',
    'digital_products.digital_products_enable.desc' => 'Gdy ta opcja jest włączona, będziesz mieć możliwość sprzedaży plików takich jak e-booki, audiobooki etc.',

    'breadcrumbs.list_courses' => 'Lista kursów',
    'breadcrumbs.list_products' => 'Lista produktów',

    'template_list.items.list_products' => 'Lista produktów',

    'blocks.products.title' => 'Lista produktów',
    'blocks.products.items_page' => 'Liczba produktów na stronie',
    'blocks.products.items_page.desc' => 'Jaką liczbę produktów wyświetlić na jednej stronie katalogu.',

    'blocks.products_slider.title' => 'Promowane produkty (slider)',
    'blocks.products_slider.front.title' => 'Promowane produkty',

    'blocks.notes.block_name' => 'Prywatne notatki / Komentarze',
    'blocks.notes.block_name.no_access_to_notes' => 'Prywatne notatki (tylko w PLUS/PRO) / Komentarze',
    'blocks.notes.tab_title' => 'Notatki',
    'blocks.notes.delete_note_prompt' => 'Czy na pewno chcesz usunąć tę notatkę?',
    'blocks.notes.note_content' => 'Treść notatki',
    'blocks.notes.save_note' => 'Zapisz notatkę',
    'blocks.notes.edit_note' => 'Edytuj notatkę',
    'blocks.notes.delete_note' => 'Usuń notatkę',

    'blocks.opinions.title' => 'Opinie',
    'blocks.opinions.items_page' => 'Liczba opinii na stronie',
    'blocks.opinions.items_page.desc' => 'Jaką liczbę opinii wyświetlić na stronie.',
    'blocks.opinions.items_page.min' => 'Minimalna liczba opinii na stronie to 1.',
    'blocks.opinions.empty' => 'Brak wystawionych opinii',
    'blocks.opinions.empty.user' => 'Klient',
    'blocks.opinions.column.label' => 'Pokaż opinie w',
    'blocks.opinions.column.desc' => 'Lista opinii może być podzielona na jedną lub 2 kolumny',
    'blocks.opinions.column.options1' => ' 1 kolumnie',
    'blocks.opinions.column.options2' => '2 kolumnach',


    'templates.checkout_cart.price' => 'Cena',
    'templates.checkout_cart.price.gross' => 'Cena brutto',
    'templates.checkout_cart.price.net' => 'Cena netto',

    'templates.checkout_cart.products_in_cart' => 'Produkty w koszyku',
    'templates.checkout_cart.remove_from_cart' => 'Usuń',
    'templates.checkout_cart.net' => 'Netto',
    'templates.checkout_cart.vat' => 'VAT',
    'templates.checkout_cart.delivery' => 'Koszt dostawy',

    'templates.checkout_confirmation.course_panel' => 'Panel kursu',

    'notifications.page_title' => 'Powiadomienia',
    'notifications.menu_title' => 'Powiadomienia',

    'notifications.form.allow_user_notice' => 'Włącz powiadomienia dla kursantów',
    'notifications.form.allow_user_notice.desc' => 'Włącza system powiadomień dla zalogowanych użytkowników platformy.',
    'notifications.form.allow_user_notice.notice' => 'Uwaga! Jakakolwiek modyfikacja treści lub wyłączenie i ponowne włączenie tej funkcjonalności sprawi, że każdy użytkownik ponownie zobaczy powiadomienie (nawet jeśli już je zamknął).',
    'notifications.form.allow_user_notice.content' => 'Treść powiadomienia',
    'notifications.form.allow_user_notice.show_close_button' => 'Pozwól na zamknięcie',
    'notifications.form.allow_user_notice.show_close_button.desc' => 'Włączenie tej opcji sprawia, że użytkownik ma możliwość zamknięcia powiadomienia. Zamknięte powiadomienie nie pojawi się ponownie aż do czasu zmiany jego treści lub wyłączenia i ponownego włączenia systemu powiadomień.',
    'notifications.form.save' => 'Zapisz',
    'notifications.form.while_saving' => 'Zapisuję...',
    'notifications.form.wrong_license_msg' => 'Aby używać powiadomień, musisz zmienić swoją licencję na plan: %s lub %s.',

    'settings.menu_title' => 'Ustawienia',
    'settings.page_title' => 'Ustawienia',
    'settings.sections.general' => 'Podstawowe',
    'settings.sections.accounting' => 'Księgowe',
    'settings.sections.payment' => 'Sposoby płatności',
    'settings.sections.design' => 'Wygląd',
    'settings.sections.integrations' => 'Integracje',
    'settings.sections.cart' => 'Koszyk zakupowy',
    'settings.sections.messages' => 'Wiadomości',
    'settings.sections.gift' => 'Zakupy na prezent',
    'settings.sections.certificate' => 'Certyfikaty',
    'settings.sections.analytics' => 'Analityka i skrypty',
    'settings.sections.modules' => 'Włącz moduły',
    'settings.sections.advanced' => 'Zaawansowane',

    'settings.field.button.save' => 'Zapisz',
    'settings.field.button.saving' => 'Zapisuję...',
    'settings.field.button.saved' => 'Zapisano!',
    'settings.field.button.cancel' => 'Anuluj',
    'settings.field.button.media' => 'Wybierz',
    'settings.field.select.choose' => 'Wybierz...',
    'settings.field.button.set' => 'Ustaw',

    'settings.field.validation.must_be_int' => 'Wpisana wartość musi być liczbą większą od 0',
    'settings.field.validation.cant_be_empty' => 'Podana wartość nie może być pusta',
    'settings.field.validation.invalid_url' => 'Niepoprawny format adresu url',
    'settings.field.validation.invalid_extension' => 'Niedozwolone rozszerzenie pliku',
    'settings.field.validation.invalid_email' => 'Niepoprawny format adresu email',
    'settings.field.validation.invalid_default_vat_rate' => 'Wpisana wartość musi być liczbą. Maksymalna ilość znaków to 2',
    'settings.field.validation.difference_must_be_at_least_5_hours' => 'Minimalny przedział to 5 godzin.',
    'settings.field.validation.fakturownia.invalid_apikey' => 'Do poprawnej integracji wymagany jest Token z prefiksem np: op7iHoQK4vHpbPYWQ2/moje-konto',

    'settings.popup.button.configure' => 'Konfiguruj',
    'settings.popup.button.save' => 'Zapisz',
    'settings.popup.button.close' => 'Zamknij',
    'settings.popup.button.cancel' => 'Anuluj',
    'settings.popup.button.saving' => 'Zapisuję...',
    'settings.popup.saved' => 'Ustawienia zostały zapisane!',
    'settings.popup.button.add_new_variant' => 'Dodaj nowy wariant',

    'settings.messages.an_error_occurred' => 'Podczas zapisywania wystąpił błąd. Skontaktuj się z administratorem.',
    'settings.messages.unsaved_data_error' => "Uwaga! Posiadasz niezapisane zmiany. \n Czy na pewno chcesz opuścić tę zakładkę?",

    'settings.sections.general.fieldset.service' => 'Serwis',

    'settings.sections.general.blog_name' => 'Tytuł serwisu',
    'settings.sections.general.blog_name.tooltip' => 'Dodaj tytuł, który będzie widoczny m.in. na karcie strony internetowej i wynikach wyszukiwania.',

    'settings.sections.general.blog_description' => 'Opis serwisu',
    'settings.sections.general.blog_description.tooltip' => 'Dodaj krótki opis, który będzie widoczny, m.in. na karcie strony internetowej i wynikach wyszukiwania.',

    'settings.sections.general.fieldset.license' => 'Licencja',

    'settings.sections.general.license_key' => 'Klucz licencyjny',

    'settings.sections.general.fieldset.branding' => 'Branding',

    'settings.sections.general.logo' => 'Logo platformy',
    'settings.sections.general.logo.desc' => 'Optymalny rozmiar to 165px na 68 px (lub 330px na 136px dla ekranów retina).',
    'settings.sections.general.logo.tooltip' => 'Dodaj logo, które pojawi się w lewym górnym rogu platformy i będzie widoczne na wszystkich stronach kursu.',

    'settings.sections.general.favicon' => 'Favicon',
    'settings.sections.general.favicon.desc' => 'Optymalny rozmiar to 16px na 16px.',
    'settings.sections.general.favicon.tooltip' => 'Dodaj swój Favicon. Favicon to ikona, która będzie widoczna w polu przeglądarki internetowej lub w zakładce strony www.',

    'settings.sections.general.fieldset.functional_pages' => 'Strony funkcjonalne',

    'settings.sections.general.page_on_front' => 'Strona główna',
    'settings.sections.general.page_on_front.tooltip' => 'Wybierz stronę, która wyświetli się jako strona główna.',

    'settings.sections.general.my_account' => 'Strona Moje konto',
    'settings.sections.general.my_account.tooltip' => 'Wybierz stronę, na której wyświetli się zawartość zakładki Moje konto.',

    'settings.sections.general.after_logging_in' => 'Strona po zalogowaniu',
    'settings.sections.general.after_logging_in.tooltip' => 'Wybierz stronę, którą zobaczy kursant po zalogowaniu do platformy.',

    'settings.sections.general.contact_page' => 'Strona kontakt / reCAPTCHA',
    'settings.sections.general.contact_page.tooltip' => 'Wybierz stronę, na której pojawi się formularz kontaktowy.',

    'settings.sections.general.contact_page.popup.title' => 'Strona kontakt / reCAPTCHA',
    'settings.sections.general.recaptcha.popup.contact_page.additional_information' => 'Do poprawnego działania formularza kontaktowego, wymagana jest reCAPTCHA',
    'settings.sections.general.recaptcha.popup.additional_information' => 'By poprawnie skonfigurować reCAPTCHA, należy wypełnić oba pola, znajdujące się poniżej.',
    'settings.sections.general.recaptcha.site_key' => 'Klucz witryny',
    'settings.sections.general.recaptcha.site_key.empty' => 'Pole "Klucz witryny" musi zostać wypełnione!',
    'settings.sections.general.recaptcha.secret_key' => 'Klucz tajny',
    'settings.sections.general.recaptcha.secret_key.empty' => 'Pole "Klucz tajny" musi zostać wypełnione!',

    'settings.sections.general.fieldset.comment_management' => 'Zarządzanie komentarzami',

    'settings.sections.general.comment_management' => 'Komentarze',
    'settings.sections.general.comment_management.tooltip' => 'Jeżeli chcesz, by w Twoim kursie pojawiły się komentarze, skorzystaj z konfiguratora.',

    'settings.sections.general.comment_management.popup.title' => 'Komentarze',

    'settings.sections.general.comments_notify' => 'Powiadamiaj o nowych komentarzach',
    'settings.sections.general.comments_notify.desc' => 'Zaznacz, jeśli chcesz otrzymywać powiadomienie za każdym razem, gdy ktoś doda komentarz',
    'settings.sections.general.comments_notify.tooltip' => 'Zaznacz, jeśli chcesz otrzymywać powiadomienia, gdy ktoś doda nowy komentarz.',

    'settings.sections.general.moderation_notify' => 'Powiadamiaj o nowych komentarzach oczekujących moderacji',
    'settings.sections.general.moderation_notify.desc' => 'Zaznacz, jeśli chcesz otrzymywać powiadomienie za każdym razem, gdy nowy komentarz oczekuje na moderację',
    'settings.sections.general.moderation_notify.tooltip' => 'Zaznacz, jeśli chcesz otrzymywać powiadomienia o nowym komentarzu oczekującym na moderację.',

    'settings.sections.general.comment_moderation' => 'Moderacja komentarzy przed ich publikacją',
    'settings.sections.general.comment_moderation.desc' => 'Zaznacz, jeśli komentarz musi być zatwierdzany ręcznie zanim pojawi się w serwisie',
    'settings.sections.general.comment_moderation.tooltip' => 'Zaznacz, jeśli chcesz zatwierdzać komentarze ręcznie przed ich publikacją.',

    'settings.sections.general.comment_previously_approved' => 'Zezwól na komentarze od zaufanych autorów',
    'settings.sections.general.comment_previously_approved.desc' => 'Zaznacz, jeśli inny komentarz tego autora musi być już ręcznie zatwierdzony, by kolejny pojawił się automatycznie w serwisie',
    'settings.sections.general.comment_previously_approved.tooltip' => 'Zaznacz, jeśli inny komentarz tego autora musi być zatwierdzony ręcznie, by kolejny pojawił się automatycznie na stronie.',

    'settings.sections.general.fieldset.email' => 'Administracyjne',

    'settings.sections.general.contact_email' => 'E-mail do kontaktu',
    'settings.sections.general.contact_email.tooltip' => 'Wpisz e-mail, na który będą przychodzić wiadomości z formularza kontaktowego.',
    'settings.sections.general.contact_email.desc' => 'Pozostaw pole puste, by użyć adresu z ustawień WordPressa.',

    'settings.sections.general.fieldset.footer' => 'Stopka',
    'settings.sections.general.footer' => 'Zawartość stopki',
    'settings.sections.general.footer.tooltip' => 'W tym miejscu możesz zaprojektować zawartość stopki.',

    'settings.sections.general.footer.popup.title' => 'Zawartość stopki',
    'settings.sections.general.footer.popup.footer_html' => 'Treść',
    'settings.sections.general.footer.popup.footer_html.tooltip' => 'Zaprojektuj treść, która pojawi się w stopce.',

    'settings.sections.general.fieldset.cookie_bar' => 'RODO',
    'settings.sections.general.cookie_bar' => 'Pasek cookie',
    'settings.sections.general.cookie_bar.tooltip' => 'W tym miejscu możesz aktywować i skonfigurować pasek z informacją o ciasteczkach.',

    'settings.sections.general.cookie_bar.popup.title' => 'Pasek cookie',

    'settings.sections.general.cookie_bar.popup.privacy_policy' => 'Polityka Prywatności',
    'settings.sections.general.cookie_bar.popup.privacy_policy.desc' => 'Wybierz stronę, która zawiera treść Twojej Polityki Prywatności',
    'settings.sections.general.cookie_bar.popup.privacy_policy.tooltip' => 'Wybierz stronę, na której umieszczona jest Polityka Prywatności.',

    'settings.sections.general.cookie_bar.popup.content' => 'Zawartość paska cookie',
    'settings.sections.general.cookie_bar.popup.content.tooltip' => 'Zaprojektuj treść, która pojawi się na pasku cookie lub skorzystaj z przygotowanej przez nas propozycji.',

    'settings.sections.general.cookie_bar.popup.button_text' => 'Przycisk na pasku cookie',
    'settings.sections.general.cookie_bar.popup.button_text.tooltip' => 'Zaprojektuj treść, która pojawi się na przycisku paska cookie lub skorzystaj z przygotowanej przez nas propozycji.',

    'settings.sections.general.fieldset.new_sale_notifications' => 'Sprzedaż',
    'settings.sections.general.new_sale_notifications' => 'Powiadomienia o nowej sprzedaży',
    'settings.sections.general.new_sale_notifications.tooltip' => 'Jeżeli chcesz otrzymywać powiadomienia o nowej sprzedaży skorzystaj z konfiguratora.',

    'settings.sections.general.new_sale_notifications.popup.title' => 'Powiadomienia o nowej sprzedaży',

    'settings.sections.general.new_sale_notifications.popup.admin_notice_policy' => 'Powiadomienia',
    'settings.sections.general.new_sale_notifications.popup.admin_notice_policy.tooltip' => 'Zdefiniuj, kiedy system ma wysyłać powiadomienia o nowej sprzedaży.',

    'settings.sections.general.fieldset.delivery' => 'Dostawa',
    'settings.sections.general.delivery_price' => 'Koszty dostawy',
    'settings.sections.general.delivery_price.tooltip' => '',
    'settings.sections.general.delivery_price.popup.title' => 'Koszty dostawy',

    'settings.sections.general.delivery_price.popup.delivery_price' => 'Koszt dostawy',
    'settings.sections.general.delivery_price.popup.delivery_price.desc' => '',
    'settings.sections.general.delivery_price.popup.delivery_price.tooltip' => 'Podana w tym polu kwota odnosi się do realizacji dostawy zamówienia, zawierającego przynajmniej jeden produkt fizyczny, bez względu na ich ilość w koszyku (ryczałt).',

    'settings.sections.general.delivery_price.popup.delivery_price.validation' => 'Podana wartość powinna być z zakresu od 0 do 9999.',

    'settings.sections.general.delivery_price.popup.delivery_provider' => 'Dostawca',
    'settings.sections.general.delivery_price.popup.delivery_provider.desc' => '',
    'settings.sections.general.delivery_price.popup.delivery_provider.tooltip' => 'W tym polu możesz opcjonalnie podać nazwę firmy kurierskiej',

    'settings.sections.general.option.admin_notice_policy.disabled' => 'Wyłączone',
    'settings.sections.general.option.admin_notice_policy.comments' => 'Tylko zamówienia z komentarzem',
    'settings.sections.general.option.admin_notice_policy.all' => 'Wszystkie zamówienia',

    'settings.sections.general.new_sale_notifications.popup.admin_notice_emails' => 'Adresy email do powiadomień sprzedażowych',
    'settings.sections.general.new_sale_notifications.popup.admin_notice_emails.desc' => 'Wpisz adres lub adresy email, na które będą wysyłane powiadomienia o sprzedaży. Każdy adres musi się znaleźć w osobnej linii.',
    'settings.sections.general.new_sale_notifications.popup.admin_notice_emails.tooltip' => 'Wpisz adresy e-mail, na które będą wysłane powiadomienia o nowej sprzedaży.',

    'settings.sections.accounting.fieldset.currency' => 'Waluta',

    'settings.sections.accounting.currency' => 'Waluta',
    'settings.sections.accounting.currency.tooltip' => 'Wybierz walutę, w której będą opłacane zamówienia.',

    'settings.sections.accounting.thousands_separator' => 'Separator tysięcy',
    'settings.sections.accounting.thousands_separator.tooltip' => 'Wybierz rodzaj separatora tysięcy.',

    'settings.sections.accounting.decimal_separator' => 'Separator dziesiętny',
    'settings.sections.accounting.decimal_separator.tooltip' => 'Wybierz rodzaj separatora dziesiętnego.',

    'settings.sections.accounting.option.separator.comma' => 'Przecinek',
    'settings.sections.accounting.option.separator.dot' => 'Kropka',
    'settings.sections.accounting.option.separator.space' => 'Spacja',
    'settings.sections.accounting.option.separator.disabled' => 'Wyłączone',

    'settings.sections.accounting.fieldset.invoicing' => 'Fakturowanie',

    'settings.sections.accounting.enable_invoices' => 'Włącz faktury',
    'settings.sections.accounting.enable_invoices.tooltip' => 'W formularzu zamówienia pojawią się dodatkowe pola umożliwiające kupującemu wpisanie danych do faktury.',

    'settings.sections.accounting.invoices_is_vat_payer' => 'Czy sprzedawca jest aktywnym płatnikiem VAT?',
    'settings.sections.accounting.invoices_is_vat_payer.tooltip' => 'Aktywuj, jeżeli jesteś aktywnym podatnikiem VAT. W konfiguratorze ustaw stawkę VAT.',

    'settings.sections.accounting.invoices_is_vat_payer.popup.title' => 'Czy sprzedawca jest aktywnym płatnikiem VAT?',

    'settings.sections.accounting.invoices_is_vat_payer.popup.invoices_default_vat_rate' => 'Domyślna stawka VAT',
    'settings.sections.accounting.invoices_is_vat_payer.popup.invoices_default_vat_rate.desc' => 'W procentach. Zastosowuje się tylko do aktywnych płatników VAT. Stawka może być również ustalona osobno dla każdego produktu.',

    'settings.sections.accounting.edd_id_force' => 'Wymagaj podania danych do faktury',
    'settings.sections.accounting.edd_id_force.tooltip' => 'Aktywuj, aby pola w formularzu zamówienia były obowiązkowe do wypełnienia.',

    'settings.sections.accounting.edd_id_person' => 'Faktury dla osób fizycznych',
    'settings.sections.accounting.edd_id_person.tooltip' => 'Aktywuj, aby dane do faktury mogły podawać również osoby fizyczne (imię, nazwisko i adres).',

    'settings.sections.accounting.edd_id_disable_tax_id_verification' => 'Wyłącz weryfikację NIP',
    'settings.sections.accounting.edd_id_disable_tax_id_verification.tooltip' => 'Aktywuj, aby NIP w formularzu zamówienia był weryfikowany.',

    'settings.sections.accounting.nip_for_receipts' => 'NIP dla osób prywatnych',
    'settings.sections.accounting.nip_for_receipts.tooltip' => 'Aktywuj, aby w formularzu zamówienia pojawiło się dodatkowe pole pozwalające wpisać NIP osobie fizycznej.',

    'settings.sections.accounting.edd_id_enable_vat_moss' => 'Włącz uproszczoną sprzedaż zagraniczną (beta)',
    'settings.sections.accounting.edd_id_enable_vat_moss.tooltip' => 'Aktywuj, aby w formularzu zamównienia pojawiło się dodatkowe pole do wyboru Państwa. Wskazanie państwa innego niż Polska spowoduje wprowadzenie zmian w fakturze (zgodnie z zasadami zwolnienia z VAT MOSS do wskazanego limitu kwotowego).',

    'settings.sections.accounting.enable_flat_rate_tax_symbol' => 'Włącz podatek zryczałtowany',
    'settings.sections.accounting.flat_rate_tax_symbol' => 'Podatek zryczałtowany',
    'settings.sections.accounting.flat_rate_tax_symbol.tooltip' => 'Aktywuj, jeśli płacisz podatek w formie ryczałtu od przychodów ewidencjonowanych. Zyskasz możliwość wskazania, z jaką stawką ryczałtu ewidencjonować sprzedaż swoich produktów. Stawkę ryczałtu zdefiniujesz w ustawieniach danego produktu.',

    'settings.sections.accounting.fieldset.gus' => 'API GUS',
    'settings.sections.accounting.gus' => 'Włącz usługę',
    'settings.sections.accounting.gus.tooltip' => 'Włączenie tej funkcjonalności umożliwi szybkie wypełnienie pól formularza zamówienia po podaniu NIP-u firmy.',
    'settings.sections.accounting.enable_gus.notice' => 'Zmień pakiet: Aby korzystać z funkcjonalności pobierania danych z bazy GUS musisz zmienić swoją licence na PLUS lub PRO.',

    'settings.sections.payments.fieldset.configuration_and_tests' => 'Konfiguracja i testy',
    'settings.sections.payments.payment_settings' => 'Ustawienia płatności',
    'settings.sections.payments.payment_settings.tooltip' => 'W tym miejscu możesz skonfigurować ustawnienia płatności.',

    'settings.sections.payments.test_mode' => 'Tryb testowy',
    'settings.sections.payments.test_mode.tooltip' => 'W trybie tym możliwe jest testowanie bramek płatności bez konieczności dokonywania realnych wpłat. Tryb testowy działa jedynie z bramkami udostępniającymi tzw. konto sandbox.',

    'settings.sections.payments.default_gateway' => 'Domyślny sposób płatności',
    'settings.sections.payments.default_gateway.tooltip' => 'Wybierz bramkę płatności, która domyślnie pojawi się w formularzu zamównienia.',

    'settings.sections.payments.display_payment_methods_as_icons' => 'Wyświetl metody płatności w postaci ikonek',
    'settings.sections.payments.display_payment_methods_as_icons.tooltip' => 'Zaznacz, aby w formularzu zamówienia wyświetliły się ikonki metod płatności zamiast listy. Ikonki będą widoczne, gdy aktywna będzie więcej niż jedna metoda płatności.',

    'settings.sections.payments.test_payment_gate' => 'Płatność testowa',
    'settings.sections.payments.test_payment_gate.tooltip' => 'Zaznacz, aby aktywować tryb testowy. Umożliwia on testowanie bramek płatności bez konieczności dokonywania realnych wpłat. Tryb testowy działa jedynie z bramkami udostępniającymi tzw. konto sandbox.',

    'settings.sections.payments.section.payment_gates' => 'Dostępne moduły płatności',

    'settings.sections.payments.fieldset.bank_and_recurring_payments' => 'Płatności elektroniczne bankowe oraz cykliczne',

    'settings.sections.payments.checkout_label' => 'Nazwa metody płatności',
    'settings.sections.payments.checkout_label.tooltip' => 'Wprowadź nazwę dla tej metody płatności.',

    'settings.sections.payments.tpay_payment_gate' => 'Tpay.com',
    'settings.sections.payments.tpay_payment_gate.tooltip' => 'W tym miejscu możesz skonfigurować płatności <a href="http://tpay.com">Tpay.com</a>',

    'settings.sections.payments.tpay.tpay_id' => 'Identyfikator tpay.com',
    'settings.sections.payments.tpay.tpay_id.desc' => 'Wprowadź Twój identyfikator z serwisu tpay.com',

    'settings.sections.payments.tpay.tpay_pin' => 'Kod bezpieczeństwa tpay.com',
    'settings.sections.payments.tpay.tpay_pin.desc' => 'Wprowadź Twój kod bezpieczeństwa (potwierdzający)',

    'settings.sections.payments.tpay.tpay_cards_api_key' => 'Klucz API dla kart płatniczych (opcjonalnie)',
    'settings.sections.payments.tpay.tpay_cards_api_key.desc' => 'Wprowadź Twój klucz API dla kart płatniczych. Wprowadzenie klucza i hasła umożliwia włączenie płatności cyklicznych (subskrypcyjnych)',
    'settings.sections.payments.tpay.tpay_cards_api_key.tooltip' => 'Wprowadź Twój klucz API dla kart płatniczych. Wprowadzenie klucza i hasła umożliwia włączenie płatności cyklicznych (subskrypcyjnych). Klucz API znajdziesz w panelu Tpay, wchodząc w <b>Płatności kartami > API</b>.',

    'settings.sections.payments.tpay.tpay_cards_api_password' => 'Hasło do API dla kart płatniczych (opcjonalnie)',
    'settings.sections.payments.tpay.tpay_cards_api_password.desc' => 'Wprowadź Twoje hasło do API dla kart płatniczych',
    'settings.sections.payments.tpay.tpay_cards_api_password.tooltip' => 'Wprowadź Twoje hasło do API dla kart płatniczych. Hasło do API znajdziesz wchodząc w <b>Płatności kartami > API</b>',

    'settings.sections.payments.tpay.tpay_cards_verification_code' => 'Kod weryfikacyjny dla API kart płatniczych',
    'settings.sections.payments.tpay.tpay_cards_verification_code.desc' => 'Wprowadź Twój kod weryfikacyjny do API dla kart płatniczych',
    'settings.sections.payments.tpay.tpay_cards_verification_code.tooltip' => 'Wprowadź Twój kod weryfikacyjny do API dla kart płatniczych. Kod weryfikacyjny znajdziesz wchodząc w <b>Płatności kartami > API</b>',

    'settings.sections.payments.tpay.tpay_recurrence_allow_standard_payments' => 'Włącz standardowe sposoby płatności dla zakupów cyklicznych',
    'settings.sections.payments.tpay.tpay_recurrence_allow_standard_payments.tooltip' => 'Zaznacz jeżeli chcesz, aby klient mógł dokonywać płatności cyklicznych za pośrednictwem standardowych systemów płatności, np. Blik, przelew online, mTransfer itp. Wybierając ten sposób płatności klient będzie otrzymywać co miesiąc powiadomienia mailowe o konieczności dokonania wpłaty (powiadomienia trzeba wcześniej ustawić). Jeżeli nie zaznaczysz tej opcji płatności cykliczne będą realizowane tylko po podaniu danych karty kredytowej. ',

    'settings.sections.payments.payu_payment_gate' => 'PayU',
    'settings.sections.payments.payu_payment_gate.tooltip' => 'W tym miejscu możesz skonfigurować płatności PayU',

    'settings.sections.payments.payu.payu_pos_id' => 'Id punktu płatności (pos_id)',
    'settings.sections.payments.payu.payu_pos_id.desc' => '',
    'settings.sections.payments.payu.payu_pos_id.tooltip' => 'Wprowadź Id punktu płatności (pos_id). Znajdziesz go w panelu administracyjnym <b>Pay U > Płatności elektroniczne > Moje sklepy > Punkty płatności.</b>',

    'settings.sections.payments.payu.payu_pos_auth_key' => 'Klucz autoryzacji płatności (pos_auth_key)',
    'settings.sections.payments.payu.payu_pos_auth_key.desc' => '',

    'settings.sections.payments.payu.payu_key1' => 'Klucz (MD5)',
    'settings.sections.payments.payu.payu_key1.desc' => '',

    'settings.sections.payments.payu.payu_key2' => 'Drugi klucz (MD5)',
    'settings.sections.payments.payu.payu_key2.desc' => '',
    'settings.sections.payments.payu.payu_key2.tooltip' => 'Wprowadź drugi klucz (MD5). Znajdziesz go w panelu administracyjnym <b>Pay U > Płatności elektroniczne > Moje sklepy > Punkty płatności.</b>',

    'settings.sections.payments.payu.payu_api_type' => 'Typ API PayU',
    'settings.sections.payments.payu.payu_api_type.desc' => '',
    'settings.sections.payments.payu.payu_api_type.tooltip' => 'Wybierz typ API Pay U. Zalecamy wybór nowszego i rozwijanego typy REST.',

    'settings.sections.payments.option.payu_api_type.rest' => 'REST (Checkout - Express Payment)',
    'settings.sections.payments.option.payu_api_type.classic' => 'Klasyczny (płatność ekspresowa)',

    'settings.sections.payments.payu.payu_return_url_failure' => '',
    'settings.sections.payments.payu.payu_return_url_failure.desc' => 'Skopiuj i wklej ten adres URL do swoich ustawień punktu płatności w PayU',

    'settings.sections.payments.payu.payu_return_url_success' => '',
    'settings.sections.payments.payu.payu_return_url_success.desc' => 'Skopiuj i wklej ten adres URL do swoich ustawień punktu płatności w PayU',

    'settings.sections.payments.payu.payu_return_url_reports' => '',
    'settings.sections.payments.payu.payu_return_url_reports.desc' => 'Skopiuj i wklej ten adres URL do swoich ustawień punktu płatności w PayU',

    'settings.sections.payments.payu.payu_api_environment' => 'Środowisko PayU API',
    'settings.sections.payments.payu.payu_api_environment.desc' => '',
    'settings.sections.payments.payu.payu_api_environment.tooltip' => 'Wybierz środowisko PayU API. Pamiętaj, że każde środowisko ma swoje klucze, które trzeba zmienić.',

    'settings.sections.payments.option.payu_api_environment.secure' => 'Secure (domyślnie)',
    'settings.sections.payments.option.payu_api_environment.sandbox' => 'Sandbox (do testów)',

    'settings.sections.payments.payu.payu_recurrence_allow_standard_payments' => 'Włącz standardowe sposoby płatności dla zakupów cyklicznych',
    'settings.sections.payments.payu.payu_recurrence_allow_standard_payments.tooltip' => 'Zaznacz jeżeli chcesz, aby klient mógł dokonywać płatności cyklicznych za pośrednictwem standardowych systemów płatności, np. Blik, przelew online, mTransfer itp. Wybierając ten sposób płatności klient będzie otrzymywać co miesiąc powiadomienia mailowe o konieczności dokonania wpłaty (powiadomienia trzeba wcześniej ustawić). Jeżeli nie zaznaczysz tej opcji płatności cykliczne będą realizowane tylko po podaniu danych karty kredytowej.',

    'settings.sections.payments.payu.payu_enable_debug' => 'Włącz tryb diagnostyczny',
    'settings.sections.payments.payu.payu_enable_debug.tooltip' => 'Zaznacz, jeżeli chcesz gromadzić dodatkowe informacje diagnostyczne dotyczące transakcji realizowanych przez PayU.',

    'settings.sections.payments.payu.payu_checkout_label' => 'Nazwa metody płatności',
    'settings.sections.payments.payu.payu_checkout_label.tooltip' => 'Wprowadź nazwę dla tej metody płatności.',

    'settings.sections.payments.fieldset.bank_payments' => 'Płatności elektroniczne bankowe',

    'settings.sections.payments.przelewy24_payment_gate' => 'Przelewy24',
    'settings.sections.payments.przelewy24_payment_gate.tooltip' => 'W tym miejscu możesz skonfigurować płatności Przelewy24.',

    'settings.sections.payments.przelewy24.przelewy24_id' => 'Przelewy24 ID',
    'settings.sections.payments.przelewy24.przelewy24_id.desc' => 'Identyfikator konta w Przelewy24',
    'settings.sections.payments.przelewy24.przelewy24_id.tooltip' => 'Wprowadź identyfikator konta w Przelewy24.',

    'settings.sections.payments.przelewy24.przelewy24_pin' => 'Przelewy24 CRC',
    'settings.sections.payments.przelewy24.przelewy24_pin.desc' => 'Kod ten znajdziesz w <a href="http://przelewy24.pl">Przelewy24.pl</a>: <b>Moje dane / Klucz do CRC</b>',
    'settings.sections.payments.przelewy24.przelewy24_pin.tooltip' => 'Wprowadź klucz CRC. Znjdziesz go w panelu administracyjnym <b>Przelewy24 > Moje dane > Klucz do CRC.</b>',

    'settings.sections.payments.przelewy24.przelewy24_checkout_label' => 'Nazwa metody płatności',
    'settings.sections.payments.przelewy24.przelewy24_checkout_label.tooltip' => 'Wprowadź nazwę dla tej metody płatności.',

    'settings.sections.payments.dotpay_payment_gate' => 'Dotpay',
    'settings.sections.payments.dotpay_payment_gate.tooltip' => 'W tym miejscu możesz skonfigurować płatności Dotpay.',

    'settings.sections.payments.dotpay.info_message' => 'Aby korzystanie z tej bramki płatności było możliwe, należy przejść do panelu administracyjnego <a href="http://www.dotpay.pl/">Dotpay.pl</a> i w zakładce <b>Ustawienia / Konfiguracja URLC / Edycja</b> odznaczyć <b>Blokuj zewnętrzne urlc</b> oraz <b>HTTPS verify</b>.',

    'settings.sections.payments.dotpay.dotpay_id' => 'Dotpay ID',
    'settings.sections.payments.dotpay.dotpay_id.desc' => 'Identyfikator konta w Dotpay',
    'settings.sections.payments.dotpay.dotpay_id.tooltip' => 'Wprowadź identyfikator konta w Dotpay.',

    'settings.sections.payments.dotpay.dotpay_pin' => 'Dotpay PIN',
    'settings.sections.payments.dotpay.dotpay_pin.desc' => 'Jest to ciąg znaków, który należy ustawić w <a href="http://www.dotpay.pl/">Dotpay.pl</a>: <b>Ustawienia / parametry URLC</b>',
    'settings.sections.payments.dotpay.dotpay_pin.tooltip' => 'Wprowadź swój PIN w Dotpay. Należy go ustwaić w <a href="http://www.dotpay.pl/">Dotpay.pl</a> <b>>Ustawienia >ParametryURLC</b>',

    'settings.sections.payments.dotpay.dotpay_onlinetransfer' => 'Natychmiastowa płatność',
    'settings.sections.payments.dotpay.dotpay_onlinetransfer.desc' => 'Zaznacz tę opcję, jeśli chcesz przyjmować jedynie płatności w czasie rzeczywistym (natychmiastowa płatność)',
    'settings.sections.payments.dotpay.dotpay_onlinetransfer.tooltip' => 'Zaznacz tę opcję, jeśli chcesz przyjmować jedynie płatności w czasie rzeczywistym.',

    'settings.sections.payments.dotpay.dotpay_checkout_label' => 'Nazwa metody płatności',
    'settings.sections.payments.dotpay.dotpay_checkout_label.tooltip' => 'Wprowadź nazwę dla tej metody płatności.',

    'settings.sections.payments.paynow_payment_gate' => 'Paynow',
    'settings.sections.payments.paynow_payment_gate.tooltip' => 'W tym miejscu możesz skonfigurować płatności Paynow.',

    'settings.sections.payments.paynow.paynow_access_key' => 'Klucz API',
    'settings.sections.payments.paynow.paynow_access_key.tooltip' => 'Wprowadź klucz API. Znajdziesz go w panelu administracyjnym Paynow.',

    'settings.sections.payments.paynow.paynow_signature_key' => 'Sygnatura klucza API',
    'settings.sections.payments.paynow.paynow_signature_key.tooltip' => 'Wprowadź sygnaturę klucza API. Sygnaturę znajdziesz w panelu administracyjnym Paynow pod nazwą <b>Klucz obliczania podpisu.</b>',

    'settings.sections.payments.paynow.paynow_environment' => 'Środowisko',
    'settings.sections.payments.paynow.paynow_environment.desc' => 'Wybierz środowisko',
    'settings.sections.payments.paynow.paynow_environment.tooltip' => 'Wybierz środowisko Paynow. Pamiętaj, że każde środowisko sandbox jest pod innym adresem.',

    'settings.sections.payments.paynow.paynow_environment.option_production' => 'Produkcyjne',
    'settings.sections.payments.paynow.paynow_environment.option_sandbox' => 'Sandbox (do testów)',

    'settings.sections.payments.paynow.paynow_checkout_label' => 'Nazwa metody płatności',
    'settings.sections.payments.paynow.paynow_checkout_label.tooltip' => 'Wprowadź nazwę dla tej metody płatności.',

    'settings.sections.payments.stripe_payment_gate' => 'Stripe',
    'settings.sections.payments.stripe_payment_gate.tooltip' => 'W tym miejscu możesz skonfigurować płatności Stripe.',

    'settings.sections.payments.stripe.test_secret_key' => 'Testowy klucz tajny',
    'settings.sections.payments.stripe.test_secret_key.desc' => 'Wpisz swój testowy klucz tajny (test secret key). Znajdziesz go na swoim koncie Stripe w zakładce Developers -> API keys (włączony tryb testowy).',
    'settings.sections.payments.stripe.test_secret_key.tooltip' => 'Wpisz swój testowy klucz tajny (test secret key). Znajdziesz go na swoim koncie Stripe w zakładce <b>Developers -> API keys</b> (włączony tryb testowy).',

    'settings.sections.payments.stripe.test_publishable_key' => 'Testowy klucz publiczny',
    'settings.sections.payments.stripe.test_publishable_key.desc' => 'Wpisz swój testowy klucz publiczny (test publishable key). Znajdziesz go na swoim koncie Stripe w zakładce Developers -> API keys (włączony tryb testowy).',
    'settings.sections.payments.stripe.test_publishable_key.tooltip' => 'Wpisz swój testowy klucz publiczny (test publishable key). Znajdziesz go na swoim koncie Stripe w zakładce <b>Developers -> API keys</b> (włączony tryb testowy).',

    'settings.sections.payments.stripe.live_secret_key' => 'Produkcyjny klucz tajny',
    'settings.sections.payments.stripe.live_secret_key.desc' => 'Wpisz swój produkcyjny klucz tajny live secret key). Znajdziesz go na swoim koncie Stripe w zakładce Developers -> API keys (wyłączonytryb testowy).',
    'settings.sections.payments.stripe.live_secret_key.tooltip' => 'Wpisz swój produkcyjny klucz tajny live secret key). Znajdziesz go na swoim koncie Stripe w zakładce <b>Developers -> API keys</b> (wyłączonytryb testowy).',

    'settings.sections.payments.stripe.live_publishable_key' => 'Produkcyjny klucz publiczny',
    'settings.sections.payments.stripe.live_publishable_key.desc' => 'Wpisz swój produkcyjny klucz publiczny (live publishable key). Znajdziesz go na swoim koncie Stripe w zakładce Developers -> API keys (wyłączony tryb testowy).',
    'settings.sections.payments.stripe.live_publishable_key.tooltip' => 'Wpisz swój produkcyjny klucz publiczny (live publishable key). Znajdziesz go na swoim koncie Stripe w zakładce <b>Developers -> API keys</b> (wyłączony tryb testowy).',

    'settings.sections.payments.stripe.stripe_checkout_label' => 'Nazwa metody płatności',
    'settings.sections.payments.stripe.stripe_checkout_label.tooltip' => 'Wprowadź nazwę dla tej metody płatności.',

    'settings.sections.payments.fieldset.other_payments' => 'Pozostałe typy płatności',

    'settings.sections.payments.paypal_payment_gate' => 'PayPal',
    'settings.sections.payments.paypal_payment_gate.tooltip' => 'W tym miejscu możesz skonfigurować płatności PayPal.',

    'settings.sections.payments.paypal.paypal_email' => 'PayPal Email',
    'settings.sections.payments.paypal.paypal_email.desc' => 'Wpisz adres email Twojego konta w PayPal',
    'settings.sections.payments.paypal.paypal_email.tooltip' => 'Wpisz adres email Twojego konta PayPal.',

    'settings.sections.payments.paypal.paypal_page_style' => 'Styl strony płatności PayPal',
    'settings.sections.payments.paypal.paypal_page_style.desc' => 'Wprowadź nazwę stylu strony, której chcesz użyć lub pozostaw puste miejsce dla ustawień domyślnych.',
    'settings.sections.payments.paypal.paypal_page_style.tooltip' => 'Wprowadź nazwę stylu strony, której chcesz użyć lub pozostaw puste miejsce dla ustawień domyślnych.',

    'settings.sections.payments.paypal.disable_paypal_verification' => 'Wyłącz weryfikację IPN',
    'settings.sections.payments.paypal.disable_paypal_verification.desc' => 'Zaznacz, jeśli status zamówień nie zmienia się na Zakończone. Opcja ta zmienia metodę weryfikacji płatności na nieco mniej bezpieczną.',
    'settings.sections.payments.paypal.disable_paypal_verification.tooltip' => 'Zaznacz, jeśli status zamówień nie zmienia się na Zakończone. Opcja ta zmienia metodę weryfikacji płatności na nieco mniej bezpieczną.',

    'settings.sections.payments.paypal.paypal_checkout_label' => 'Nazwa metody płatności',
    'settings.sections.payments.paypal.paypal_checkout_label.tooltip' => 'Wprowadź nazwę dla tej metody płatności.',

    'settings.sections.payments.coinbase_payment_gate' => 'Coinbase',
    'settings.sections.payments.coinbase_payment_gate.tooltip' => 'W tym miejscu możesz skonfigurować płatności Coinbase.',

    'settings.sections.payments.coinbase.info_message' => 'Aby ta metoda płatności funkcjonowała w pełni, musisz skonfigurować tzw. webhook. Aby to zrobić, przejdź teraz na stronę <a href="https://commerce.coinbase.com/dashboard/settings" target="_blank">swojego konta w Coinbase</a>. Następnie, dodaj webhook kierujący na adres URL podany poniżej.<br>Adres URL webhooka: %s<br>Zajrzyj do <a href="https://docs.easydigitaldownloads.com/article/314-coinbase-payment-gateway-setup-documentation">dokumentacji Coinbase</a> jeśli potrzebujesz więcej informacji.',

    'settings.sections.payments.coinbase.edd_coinbase_api_key' => 'API Key',
    'settings.sections.payments.coinbase.edd_coinbase_api_key.desc' => 'Wpisz swój klucz API z Coinbase',
    'settings.sections.payments.coinbase.edd_coinbase_api_key.tooltip' => 'Wprowadź swój klucz API z Coinbase.',

    'settings.sections.payments.coinbase.coinbase_checkout_label' => 'Nazwa metody płatności',
    'settings.sections.payments.coinbase.coinbase_checkout_label.tooltip' => 'Wprowadź nazwę dla tej metody płatności.',


    'settings.sections.payments.transfers_payment_gate' => 'Przelew tradycyjny',

    'settings.sections.payments.transfers.edd_przelewy_name' => 'Firma / Imię i nazwisko',
    'settings.sections.payments.transfers.edd_przelewy_name.desc' => '',

    'settings.sections.payments.transfers.edd_przelewy_address' => 'Adres',
    'settings.sections.payments.transfers.edd_przelewy_address.desc' => 'ul. Testowa 123, 11-123 Warszawa',

    'settings.sections.payments.transfers.edd_przelewy_account_number' => 'Nr konta',
    'settings.sections.payments.transfers.edd_przelewy_account_number.desc' => '',

    'settings.sections.payments.transfers.przelewy_checkout_label' => 'Nazwa metody płatności',

    'settings.sections.advanced.fieldset' => 'Zaawansowane',
    'settings.sections.advanced.allow_inline_file_download' => 'Otwieranie plików',
    'settings.sections.advanced.allow_inline_file_download.tooltip' => 'Wybierz w jaki sposób otwierać pliki po kliknięciu w nie przez użytkownika.',
    'settings.sections.advanced.allow_inline_file_download.desc' => '',
    'settings.sections.advanced.allow_inline_file_download.option.inline' => 'Otwórz w przeglądarce gdy jest to możliwe',
    'settings.sections.advanced.allow_inline_file_download.option.attachment' => 'Wymuś pobranie na dysk',

    'settings.sections.advanced.enable_logo_in_courses_to_home_page' => 'Kierowanie logo do strony głównej',
    'settings.sections.advanced.enable_logo_in_courses_to_home_page.desc' => '',
    'settings.sections.advanced.enable_logo_in_courses_to_home_page.tooltip' => 'Gdy ta opcja jest aktywna, logo w nagłówku będzie zawsze kierować do strony głównej serwisu (zamiast do panelu danego kursu, gdy znajdujemy się wewnątrz jego struktury).',
    'settings.sections.advanced.enable_logo_in_courses_to_home_page.popup.title' => 'settings.sections.advanced.enable_logo_in_courses_to_home_page.popup.title',

    'settings.sections.advanced.enable_active_sessions_limiter' => 'Funkcjonalność limitu logowania do konta',
    'settings.sections.advanced.enable_active_sessions_limiter.desc' => '',
    'settings.sections.advanced.enable_active_sessions_limiter.tooltip' => 'Gdy ta opcja jest włączona, będziesz mieć możliwość ustawienia limitu użytkowników, którzy będą mogli jednocześnie zalogować się do tego samego konta.',
    'settings.sections.advanced.enable_active_sessions_limiter.notice' => 'Zmień pakiet: Aby korzystać z limitu logowania musisz zmienic swoją licencję na PLUS lub PRO.',
    'settings.sections.advanced.enable_active_sessions_limiter.popup.title' => 'Funkcjonalność limitu logowania do konta',
    'settings.sections.advanced.enable_active_sessions_limiter.max_active_sessions_number' => 'Limit użytkowników',
    'settings.sections.advanced.enable_active_sessions_limiter.max_active_sessions_number.desc' => '',
    'settings.sections.advanced.enable_active_sessions_limiter.max_active_sessions_number.tooltip' => '',

    'settings.sections.advanced.enable_payment_reminders' => 'Odzyskiwanie utraconych zamówień',
    'settings.sections.advanced.enable_payment_reminders.desc' => '',
    'settings.sections.advanced.enable_payment_reminders.tooltip' => '',
    'settings.sections.advanced.enable_payment_reminders.notice' => 'Zmień pakiet: Aby korzystać z funkcjonalności odzyskiwania utraconych koszyków musisz zmienić swoją licence na PRO.',

    'settings.sections.advanced.enable_payment_reminders.popup.title' => 'Odzyskiwanie utraconych zamówień',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_number_days' => 'Ilość dni, po których wysyłana jest wiadomość w przypadku braku płatności',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_number_days.desc' => '',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_number_days.tooltip' => '',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject' => 'Temat wiadomości wysyłanej w przypadku braku płatności',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject.desc' => '',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject.tooltip' => '',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_subject.value' => 'Masz nieopłacone zamówienie nr: {payment_id}',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_content' => 'Treść wiadomości wysyłanej w przypadku braku płatności',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_content.desc' => '',
    'settings.sections.advanced.enable_payment_reminders.payment_reminders_message_content.tooltip' => '',

    'settings.sections.advanced.enable_sell_discounts' => 'Generowanie zniżek',
    'settings.sections.advanced.enable_sell_discounts.desc' => '',
    'settings.sections.advanced.enable_sell_discounts.tooltip' => 'Aktywacja tej opcji umożliwi generowanie zniżki przy każdym zakupie (aby np. przekazać unikalny kod zniżkowy na następne zakupy).',

    'settings.sections.advanced.purchase_limit_behaviour' => 'Kontrola limitu sprzedaży',
    'settings.sections.advanced.purchase_limit_behaviour.tooltip' => 'Opcja ta pozwala wybrać sposób kontrolowania limitu sprzedaży. Możliwe jest zliczanie złożonych lub tylko opłaconych zamówień (w drugim przypadku limit może zostać przekroczony ze względu na spływające płatności do złożonych zamówień).',
    'settings.sections.advanced.purchase_limit_behaviour.desc' => '',
    'settings.sections.advanced.purchase_limit_behaviour.option.begin_payment' => 'Przy składaniu zamówienia',
    'settings.sections.advanced.purchase_limit_behaviour.option.complete_payment' => 'Przy zaksięgowaniu płatności',

    'settings.sections.advanced.partner_program' => 'Program partnerski',
    'settings.sections.advanced.partner_program.tooltip' => '',
    'settings.sections.advanced.partner_program.desc' => '',

    'settings.sections.advanced.partner_program.notice' => 'Zmień pakiet: Aby korzystać z programu partnerskiego musisz zmienic swoją licence na PRO.',

    'settings.sections.advanced.partner_program_commission' => 'Wysokość prowizji',
    'settings.sections.advanced.partner_program_commission.tooltip' => '',
    'settings.sections.advanced.partner_program_commission.desc' => '',

    'settings.sections.advanced.quiz_settings' => 'Ustawienia quizów',
    'settings.sections.advanced.right_click_blocking_quiz' => 'Blokowanie prawokliku, zaznaczania i wklejania',

    'settings.sections.integrations.fieldset.mailing_systems' => 'Systemy mailingowe',

    'settings.sections.integrations.fieldset.mailing_systems.action' => 'Akcje',

    'settings.sections.integrations.fieldset.mailing_systems.sync' => 'Synchronizacja danych',
    'settings.sections.integrations.fieldset.mailing_systems.sync.tooltip' => '',
    'settings.sections.integrations.fieldset.mailing_systems.sync.button' => 'Wymuś',

    'settings.sections.integrations.fieldset.mailing_systems.pl' => 'Polskojęzyczne',

    'settings.sections.integrations.fieldset.mailing_systems.en' => 'Angielskojęzyczne',

    'settings.sections.integrations.getresponse_integration' => 'GetResponse',

    'settings.sections.integrations.getresponse.bpmj_eddres_token' => 'Klucz API',
    'settings.sections.integrations.getresponse.bpmj_eddres_token.desc' => 'Wpisz swój klucz API GetResponse',

    'settings.sections.integrations.getresponse.bpmj_eddres_show_checkout_signup' => 'Wyświetl opcję zapisu na listę na stronie zamówienia',
    'settings.sections.integrations.getresponse.bpmj_eddres_show_checkout_signup.tooltip' => 'Gdy opcja ta jest zaznaczona, klienci mają możliwość zapisu na wybraną listę podczas składania zamówienia',

    'settings.sections.integrations.getresponse.bpmj_eddres_list' => 'Wybierz listę do zapisania',
    'settings.sections.integrations.getresponse.bpmj_eddres_list.desc' => 'Wybierz listę, na którą kupujący ma zostać zapisany, gdy zaznaczy checkbox',

    'settings.sections.integrations.getresponse.bpmj_eddres_list_unsubscribe' => 'Wybierz listę do wypisania',
    'settings.sections.integrations.getresponse.bpmj_eddres_list_unsubscribe.desc' => 'Wybierz listę, z której kupujący ma zostać wypisany, gdy zaznaczy checkbox',

    'settings.sections.integrations.getresponse.bpmj_eddres_label' => 'Opis opcji zapisu',
    'settings.sections.integrations.getresponse.bpmj_eddres_label.desc' => 'Tekst ten pojawi się obok opcji umożliwiającej klientowi zapis na listę mailingową w formularzu zamówienia',

    'settings.sections.integrations.freshmail_integration' => 'FreshMail',

    'settings.sections.integrations.freshmail.bpmj_eddfm_api' => 'Klucz API',
    'settings.sections.integrations.freshmail.bpmj_eddfm_api.desc' => 'Wpisz swój API key',

    'settings.sections.integrations.freshmail.bpmj_eddfm_api_secret' => 'API Secret',
    'settings.sections.integrations.freshmail.bpmj_eddfm_api_secret.desc' => 'Wpisz swój API secret',

    'settings.sections.integrations.freshmail.bpmj_eddfm_show_checkout_signup' => 'Wyświetl opcję zapisu na listę na stronie zamówienia',
    'settings.sections.integrations.freshmail.bpmj_eddfm_show_checkout_signup.tooltip' => 'Gdy opcja ta jest zaznaczona, klienci mają możliwość zapisu na wybraną listę podczas składania zamówienia',

    'settings.sections.integrations.freshmail.bpmj_eddfm_group' => 'Wybierz listę do zapisania',
    'settings.sections.integrations.freshmail.bpmj_eddfm_group.desc' => 'Wybierz listę, na które kupujący ma zostać zapisany, gdy zaznaczy checkbox',

    'settings.sections.integrations.freshmail.bpmj_eddfm_group_unsubscribe' => 'Wybierz listę do wypisania',
    'settings.sections.integrations.freshmail.bpmj_eddfm_group_unsubscribe.desc' => 'Wybierz listę, z której kupujący ma zostać wypisany, gdy zaznaczy checkbox',

    'settings.sections.integrations.freshmail.bpmj_eddfm_label' => 'Opis opcji zapisu',
    'settings.sections.integrations.freshmail.bpmj_eddfm_label.desc' => 'Tekst ten pojawi się obok opcji umożliwiającej klientowi zapis na listę mailingową w formularzu zamówienia',

    'settings.sections.integrations.freshmail.bpmj_eddfm_double_opt_in' => 'Zapis z potwierdzeniem',
    'settings.sections.integrations.freshmail.bpmj_eddfm_double_opt_in.tooltip' => 'Gdy opcja ta jest zaznaczona, do użytkowników zapisujących się na listę wysłany zostanie wiadomość z prośbą o potwierdzenie ich adresu email',

    'settings.sections.integrations.salesmanago_integration' => 'SalesManago',

    'settings.sections.integrations.salesmanago.salesmanago_owner' => 'Adres email konta SALESmanago',
    'settings.sections.integrations.salesmanago.salesmanago_owner.desc' => 'Adres email na który zarejestrowane jest Twoje konto SAELESmanago.',

    'settings.sections.integrations.salesmanago.salesmanago_endpoint' => 'Endpoint',
    'settings.sections.integrations.salesmanago.salesmanago_endpoint.desc' => 'Indentyfikator Twojego serwera (endpoint) z panelu SALESmanago (Ustawienia->Integracja).',

    'settings.sections.integrations.salesmanago.salesmanago_client_id' => 'ID Klienta',
    'settings.sections.integrations.salesmanago.salesmanago_client_id.desc' => 'Twoje ID Klienta z panelu SALESmanago (Ustawienia->Integracja).',

    'settings.sections.integrations.salesmanago.salesmanago_api_secret' => 'API Secret',
    'settings.sections.integrations.salesmanago.salesmanago_api_secret.desc' => 'Kod API Secret z panelu SALESmanago (Ustawienia->Integracja).',

    'settings.sections.integrations.salesmanago.salesmanago_tracking_code' => 'Kod śledzący',
    'settings.sections.integrations.salesmanago.salesmanago_tracking_code.tooltip' => 'Zaznacz aby umieścić kod śledzący.',

    'settings.sections.integrations.salesmanago.salesmanago_checkout_mode' => 'Pole zapisu',
    'settings.sections.integrations.salesmanago.salesmanago_checkout_mode.tooltip' => 'Zaznacz aby pole zapisu zostało pokazane.',

    'settings.sections.integrations.salesmanago.salesmanago_checkout_label' => 'Opis pola zapisu',
    'settings.sections.integrations.salesmanago.salesmanago_checkout_label.desc' => 'Ten tekst wyświetli się obok opcji zapisu w podsumowaniu koszyka.',

    'settings.sections.integrations.salesmanago.bpmj_eddsm_salesmanago_tags' => 'Tagi dopisywane do użytkownika',
    'settings.sections.integrations.salesmanago.bpmj_eddsm_salesmanago_tags.desc' => 'Wpisz tagi (oddzielając je przecinkiem), które mają być dodane do kontaktu w panelu SALESmanago po każdym zakupie. Tagi te będą dodane tylko jeżeli będzie wyświetlone i zaznaczone pole zapisu w podsumowaniu koszyka. Tagi produktów będą dodane niezależnie.',
    'settings.sections.integrations.salesmanago.bpmj_eddsm_salesmanago_tags.placeholder' => 'Dodaj tag',

    'settings.sections.integrations.ipresso_integration' => 'iPresso',

    'settings.sections.integrations.ipresso.bpmj_eddip_api_endpoint' => 'URL panelu',
    'settings.sections.integrations.ipresso.bpmj_eddip_api_endpoint.desc' => 'Podaj adres URL swojego panelu iPresso (np. twojafirma.ipresso.com)',

    'settings.sections.integrations.ipresso.bpmj_eddip_api' => 'Klucz API',
    'settings.sections.integrations.ipresso.bpmj_eddip_api.desc' => 'Wprowadź swój klucz API iPresso',

    'settings.sections.integrations.ipresso.bpmj_eddip_api_login' => 'Logowanie API',
    'settings.sections.integrations.ipresso.bpmj_eddip_api_login.desc' => 'Wpisz swój login iPresso API',

    'settings.sections.integrations.ipresso.bpmj_eddip_api_password' => 'Hasło API',
    'settings.sections.integrations.ipresso.bpmj_eddip_api_password.desc' => 'Podaj hasło do iPresso API',

    'settings.sections.integrations.ipresso.bpmj_eddip_show_checkout_signup' => 'Wyświetl opcję zapisu na listę na stronie zamówienia',
    'settings.sections.integrations.ipresso.bpmj_eddip_show_checkout_signup.tooltip' => 'Gdy opcja ta jest zaznaczona, klienci mają możliwość zapisu na wybraną listę podczas składania zamówienia',

    'settings.sections.integrations.ipresso.bpmj_eddip_tracking_code' => 'Kod śledzenia',
    'settings.sections.integrations.ipresso.bpmj_eddip_tracking_code.desc' => 'Podaj kod śledzenia iPresso dla tej strony',

    'settings.sections.integrations.mailchimp_integration' => 'MailChimp',

    'settings.sections.integrations.mailchimp.eddmc_api' => 'Klucz API',
    'settings.sections.integrations.mailchimp.eddmc_api.desc' => 'Wpisz swój klucz API z systemu MailChimp',

    'settings.sections.integrations.mailchimp.eddmc_list' => 'Wybierz listę',
    'settings.sections.integrations.mailchimp.eddmc_list.desc' => 'Wybierz listę, na którą kupujący ma zostać zapisany, gdy zaznaczy checkbox',

    'settings.sections.integrations.mailchimp.eddmc_show_checkout_signup' => 'Wyświetl opcję zapisu na listę na stronie zamówienia',
    'settings.sections.integrations.mailchimp.eddmc_show_checkout_signup.tooltip' => 'Gdy opcja ta jest zaznaczona, klienci mają możliwość zapisu na wybraną listę podczas składania zamówienia',

    'settings.sections.integrations.mailchimp.eddmc_label' => 'Opis opcji zapisu',
    'settings.sections.integrations.mailchimp.eddmc_label.desc' => 'Tekst ten pojawi się obok opcji umożliwiającej klientowi zapis na listę mailingową w formularzu zamówienia',

    'settings.sections.integrations.mailchimp.eddmc_double_opt_in' => 'Zapis z potwierdzeniem',
    'settings.sections.integrations.mailchimp.eddmc_double_opt_in.tooltip' => 'Gdy opcja ta jest zaznaczona, do użytkowników zapisujących się na listę wysłany zostanie wiadomość z prośbą o potwierdzenie ich adresu email',

    'settings.sections.integrations.mailerlite_integration' => 'MailerLite',

    'settings.sections.integrations.mailerlite.bpmj_edd_ml_api' => 'Klucz API',
    'settings.sections.integrations.mailerlite.bpmj_edd_ml_api.desc' => 'Wpisz swój klucz API z systemu MailerLite',

    'settings.sections.integrations.mailerlite.bpmj_edd_ml_group' => 'Wybierz grupę',
    'settings.sections.integrations.mailerlite.bpmj_edd_ml_group.desc' => 'Wybierz listę, na którą kupujący ma zostać zapisany, gdy zaznaczy checkbox',

    'settings.sections.integrations.mailerlite.bpmj_edd_ml_show_checkout_signup' => 'Wyświetl opcję zapisu na listę na stronie zamówienia',
    'settings.sections.integrations.mailerlite.bpmj_edd_ml_show_checkout_signup.tooltip' => 'Gdy opcja ta jest zaznaczona, klienci mają możliwość zapisu na wybraną listę podczas składania zamówienia',

    'settings.sections.integrations.mailerlite.bpmj_edd_ml_label' => 'Opis opcji zapisu',
    'settings.sections.integrations.mailerlite.bpmj_edd_ml_label.tooltip' => 'Tekst ten pojawi się obok opcji umożliwiającej klientowi zapis na listę mailingową w formularzu zamówienia',

    'settings.sections.integrations.mailerlite.bpmj_edd_ml_double_opt_in' => 'Zapis z potwierdzeniem',
    'settings.sections.integrations.mailerlite.bpmj_edd_ml_double_opt_in.tooltip' => 'Gdy opcja ta jest zaznaczona, do użytkowników zapisujących się na listę wysłany zostanie wiadomość z prośbą o potwierdzenie ich adresu email',

    'settings.sections.integrations.interspire_integration' => 'Interspire',

    'settings.sections.integrations.interspire.bpmj_edd_in_username' => 'Nazwa użytkownika Interspire',
    'settings.sections.integrations.interspire.bpmj_edd_in_username.desc' => 'Wprowadź swoją nazwę użytkownika Interspire',

    'settings.sections.integrations.interspire.bpmj_edd_in_token' => 'Token Interspire',
    'settings.sections.integrations.interspire.bpmj_edd_in_token.desc' => 'Wprowadź token Interspire',

    'settings.sections.integrations.interspire.bpmj_edd_in_xmlEndpoint' => 'Ścieżka XML Interspire',
    'settings.sections.integrations.interspire.bpmj_edd_in_xmlEndpoint.desc' => 'Wprowadź pełną ścieżkę XML Interspire',

    'settings.sections.integrations.interspire.bpmj_edd_in_contact_list' => 'Wybierz listę kontaktów',
    'settings.sections.integrations.interspire.bpmj_edd_in_contact_list.desc' => 'Wybierz listę, na którą kupujący ma zostać zapisany, gdy zaznaczy checkbox',

    'settings.sections.integrations.interspire.bpmj_edd_in_show_checkout_signup' => 'Wyświetl opcję zapisu na listę na stronie zamówienia',
    'settings.sections.integrations.interspire.bpmj_edd_in_show_checkout_signup.tooltip' => 'Gdy opcja ta jest zaznaczona, klienci mają możliwość zapisu na wybraną listę podczas składania zamówienia',

    'settings.sections.integrations.interspire.bpmj_edd_in_label' => 'Opis opcji zapisu',
    'settings.sections.integrations.interspire.bpmj_edd_in_label.desc' => 'Tekst ten pojawi się obok opcji umożliwiającej klientowi zapis na listę mailingową w formularzu zamówienia',

    'settings.sections.integrations.interspire.bpmj_edd_in_double_opt_in' => 'Zapis z potwierdzeniem',
    'settings.sections.integrations.interspire.bpmj_edd_in_double_opt_in.tooltip' => 'Gdy opcja ta jest zaznaczona, do użytkowników zapisujących się na listę wysłany zostanie wiadomość z prośbą o potwierdzenie ich adresu email',

    'settings.sections.integrations.activecampaign_integration' => 'ActiveCampaign',

    'settings.sections.integrations.activecampaign.bpmj_eddact_api_url' => 'API URL',
    'settings.sections.integrations.activecampaign.bpmj_eddact_api_url.desc' => 'Wpisz swój API URL',

    'settings.sections.integrations.activecampaign.bpmj_eddact_api_token' => 'API token',
    'settings.sections.integrations.activecampaign.bpmj_eddact_api_token.desc' => 'Wpisz swój API token',

    'settings.sections.integrations.activecampaign.bpmj_eddact_show_checkout_signup' => 'Wyświetl opcję zapisu na listę na stronie zamówienia',
    'settings.sections.integrations.activecampaign.bpmj_eddact_show_checkout_signup.tooltip' => 'Gdy opcja ta jest zaznaczona, klienci mają możliwość zapisu na wybraną listę podczas składania zamówienia',

    'settings.sections.integrations.activecampaign.bpmj_eddact_list' => 'Wybierz listę do zapisania',
    'settings.sections.integrations.activecampaign.bpmj_eddact_list.desc' => 'Wybierz listę, na którą kupujący ma zostać zapisany, gdy zaznaczy checkbox',

    'settings.sections.integrations.activecampaign.bpmj_eddact_list_unsubscribe' => 'Wybierz listę do wypisania',
    'settings.sections.integrations.activecampaign.bpmj_eddact_list_unsubscribe.desc' => 'Wybierz listę, z której kupujący ma zostać wypisany, gdy zaznaczy checkbox',

    'settings.sections.integrations.activecampaign.bpmj_eddact_tag' => 'Tagi dopisywane do kontaktu',
    'settings.sections.integrations.activecampaign.bpmj_eddact_tag.desc' => 'Wpisz tagi, które mają być dodane do kontaktu.',
    'settings.sections.integrations.activecampaign.bpmj_eddact_tag.placeholder' => 'Dodaj tag',

    'settings.sections.integrations.activecampaign.bpmj_eddact_tag_unsubscribe' => 'Tagi usuwane z kontaktu',
    'settings.sections.integrations.activecampaign.bpmj_eddact_tag_unsubscribe.desc' => 'Wpisz tagi, które mają być usuwane z kontaktu.',
    'settings.sections.integrations.activecampaign.bpmj_eddact_tag_unsubscribe.placeholder' => 'Dodaj tag',

    'settings.sections.integrations.activecampaign.bpmj_eddact_label' => 'Opis opcji zapisu',
    'settings.sections.integrations.activecampaign.bpmj_eddact_label.desc' => 'Tekst ten pojawi się obok opcji umożliwiającej klientowi zapis na listę mailingową w formularzu zamówienia',

    'settings.sections.integrations.activecampaign.bpmj_eddact_form_id' => 'Formularz potwierdzenia',
    'settings.sections.integrations.activecampaign.bpmj_eddact_form_id.desc' => 'Wybierz formularz potwierdzenia. W ten sposób możesz włączyć opcję podwójnej zgody na subskrybcję użytkownika (zalecane). Formularze tworzy się w panelu administracyjnym pod adresem <b>ActiveCampaign.com / Apps / Add form</b>.',

    'settings.sections.integrations.convertkit_integration' => 'ConvertKit',

    'settings.sections.integrations.convertkit.edd_convertkit_api' => 'Klucz API',
    'settings.sections.integrations.convertkit.edd_convertkit_api.desc' => 'Wpisz swój klucz API ConvertKit',

    'settings.sections.integrations.convertkit.edd_convertkit_api_secret' => 'API secret',
    'settings.sections.integrations.convertkit.edd_convertkit_api_secret.desc' => 'Wprowadź swój API secret',

    'settings.sections.integrations.convertkit.edd_convertkit_show_checkout_signup' => 'Zezwolić klientom na zapisanie się na wybraną listę',
    'settings.sections.integrations.convertkit.edd_convertkit_show_checkout_signup.tooltip' => 'Zezwolić klientom na zapisanie się na listę wybraną poniżej podczas realizacji transakcji?',

    'settings.sections.integrations.convertkit.edd_convertkit_list' => 'Wybierz listę',
    'settings.sections.integrations.convertkit.edd_convertkit_list.desc' => 'Wybierz formularz, do którego chcesz subskrybować kupujących. Formularz można również wybrać dla poszczególnych produktów na ekranie edycji produktu',

    'settings.sections.integrations.convertkit.edd_convertkit_label' => 'Opis opcji zapisu',
    'settings.sections.integrations.convertkit.edd_convertkit_label.desc' => 'To jest tekst wyświetlany obok opcji rejestracji',

    'settings.sections.integrations.fieldset.invoicing_systems' => 'Systemy fakturujące',

    'settings.sections.integrations.fakturownia_integration' => 'Fakturownia',

    'settings.sections.integrations.fakturownia.apikey' => 'Klucz API / Nazwa konta',
    'settings.sections.integrations.fakturownia.apikey.desc' => 'Fakturownia.pl -> Ustawienia konta -> Integracja -> Zobacz Api Tokeny -> Dodaj nowy Token -> dodaj Token z prefiksem',

    'settings.sections.integrations.fakturownia.departments_id' => 'ID Firmy',
    'settings.sections.integrations.fakturownia.departments_id.desc' => 'W Fakturownia.pl -> Ustawienia -> Dane firmy należy kliknąć na firmę / dział i ID działu pojawi się w URL. Jeśli to pole pozostanie puste, wtedy będą wstawione domyślne dane Twojej firmy',

    'settings.sections.integrations.fakturownia.auto_sent' => 'Automatyczna wysyłka faktur',
    'settings.sections.integrations.fakturownia.auto_sent.desc' => 'Zaznacz, jeżeli faktury maja być wysyłane automatycznie e-mailem do klienta. Wymagana pełna aktywacja systemu Fakturownia.pl',

    'settings.sections.integrations.fakturownia.auto_sent_receipt' => 'Automatyczna wysyłka paragonów',
    'settings.sections.integrations.fakturownia.auto_sent_receipt.desc' => 'Zaznacz, jeżeli paragony maja być wysyłane automatycznie e-mailem do klienta. Wymagana pełna aktywacja systemu Fakturownia.pl',

    'settings.sections.integrations.fakturownia.receipt' => 'Wystawiaj też paragony',

    'settings.sections.integrations.fakturownia.vat_exemption' => 'Tekst do wstawienia na fakturze (faktura bez VAT)',
    'settings.sections.integrations.fakturownia.vat_exemption.default' => 'Podstawa prawna: stawka zw. zgodnie z art. 43 ust. 1 pkt 28 ustawy o VAT',

    'settings.sections.integrations.ifirma_integration' => 'iFirma',

    'settings.sections.integrations.ifirma.ifirma_ifirma_email' => 'Email z systemu iFirma',
    'settings.sections.integrations.ifirma.ifirma_ifirma_email.desc' => 'Podaj email, za pomocą którego logujesz się do panelu systemu iFirma',

    'settings.sections.integrations.ifirma.ifirma_ifirma_invoice_key' => 'Klucz API faktura',
    'settings.sections.integrations.ifirma.ifirma_ifirma_invoice_key.desc' => 'iFirma.pl -> Narzędzia -> API',

    'settings.sections.integrations.ifirma.ifirma_ifirma_subscriber_key' => 'Klucz API abonent',
    'settings.sections.integrations.ifirma.ifirma_ifirma_subscriber_key.desc' => 'iFirma.pl -> Narzędzia -> API',

    'settings.sections.integrations.ifirma.ifirma_vat_exemption' => 'Podstawa zwolnienia z VAT',
    'settings.sections.integrations.ifirma.ifirma_vat_exemption.value' => 'Art. 113 ust. 1',

    'settings.sections.integrations.ifirma.ifirma_auto_sent' => 'Automatyczna wysyłka',
    'settings.sections.integrations.ifirma.ifirma_auto_sent.desc' => 'Zaznacz, jeżeli dokumenty sprzedaży maja być wysyłane automatycznie e-mailem do klienta',

    'settings.sections.integrations.wfirma_integration' => 'wFirma',

    'settings.sections.integrations.wfirma.wfirma_auth_type' => 'Typ autoryzacji',
    'settings.sections.integrations.wfirma.wfirma_auth_type.desc' => '',
    'settings.sections.integrations.wfirma.wfirma_auth_type.tooltip' => 'Wybierz typ autoryzacji. Zalecamy wybór OAuth2, ponieważ autoryzacja Basic przestanie byc obsługiwana do końca czerwca 2023.',

    'settings.sections.integrations.wfirma.option.wfirma_auth_type.basic' => 'Basic (obsługiwana do końca czerwca 2023)',
    'settings.sections.integrations.wfirma.option.wfirma_auth_type.oauth2' => 'OAuth2',

    'settings.sections.integrations.wfirma.wfirma_wf_login' => 'Login',
    'settings.sections.integrations.wfirma.wfirma_wf_login.desc' => 'Login (adres email) do systemu wfirma.pl',

    'settings.sections.integrations.wfirma.wfirma_wf_pass' => 'Hasło',
    'settings.sections.integrations.wfirma.wfirma_wf_pass.desc' => 'Hasło do systemu wfirma.pl',

    'settings.sections.integrations.wfirma.oauth2_message1' => 'W celu wygenerowania poniższych danych, niezbędnych do zintegrowania Publigo z systemem wFirma, zapoznaj się proszę z <a href="https://poznaj.publigo.pl/articles/229095-konfiguracja-integracji-z-wfirma" target="_blank">artykułem w naszej bazie wiedzy</a><br/>Adres zwrotny - <b>{url}</b>, adres IP - <b>{ip}</b>.',
    'settings.sections.integrations.wfirma.oauth2_message1.ip_error' => '[Błąd pobierania IP, odśwież stronę]',

	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_client_id' => 'ID klienta',
	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_client_id.desc' => '',

	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_client_secret' => 'Hasło klienta',
	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_client_secret.desc' => '',

    'settings.sections.integrations.wfirma.oauth2_message2' => 'Kliknij poniższy przycisk, by wygenerować Kod autoryzacji. Uwaga! Publigo przekieruje Cię do systemu wFirma. To niezbędna część procesu.',

    'settings.sections.integrations.wfirma.oauth2_button_redir' => 'Generowanie kodu autoryzacji',
    'settings.sections.integrations.wfirma.oauth2_button_redir.value' => 'Generuj',

	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_authorization_code' => 'Kod autoryzacji',
	'settings.sections.integrations.wfirma.wfirma_wf_oauth2_authorization_code.desc' => '',

	'settings.sections.integrations.wfirma.wfirma_wf_company_id' => 'ID Firmy',
    'settings.sections.integrations.wfirma.wfirma_wf_company_id.desc' => 'Pozostaw pole puste, gdy posiadasz jedną firmę w wFirma',

    'settings.sections.integrations.wfirma.wfirma_receipt' => 'Wystawiaj też paragony',

    'settings.sections.integrations.wfirma.wfirma_auto_sent' => 'Automatyczna wysyłka faktur',
    'settings.sections.integrations.wfirma.wfirma_auto_sent.desc' => 'Zaznacz, jeżeli faktury i rachunki maja być wysyłane automatycznie e-mailem do klienta',

    'settings.sections.integrations.wfirma.wfirma_auto_sent_receipt' => 'Automatyczna wysyłka paragonów',
    'settings.sections.integrations.wfirma.wfirma_auto_sent_receipt.desc' => 'Zaznacz, jeżeli paragony maja być wysyłane automatycznie e-mailem do klienta',

    'settings.sections.integrations.taxe_integration' => 'Taxe',

    'settings.sections.integrations.taxe.taxe_taxe_login' => 'Login',

    'settings.sections.integrations.taxe.taxe_taxe_api_key' => 'Klucz API',
    'settings.sections.integrations.taxe.taxe_taxe_api_key.desc' => 'CRM -> Usługi API',

    'settings.sections.integrations.taxe.taxe_receipt' => 'Wystawiaj też paragony',

    'settings.sections.integrations.taxe.taxe_auto_sent' => 'Automatyczna wysyłka faktur',
    'settings.sections.integrations.taxe.taxe_auto_sent.desc' => 'Zaznacz, jeżeli faktury i rachunki maja być wysyłane automatycznie e-mailem do klienta. <br /><strong>Uwaga:</strong> upewnij się, że na stronie <a href="https://panel.taxe.pl/email-szablony/">Szablony wiadomości e-mail</a> masz ustawiony szablon domyślny dla czynności &quot;Wysyłka dokumentu w wiadomości e-mail&quot;.',

    'settings.sections.integrations.taxe.taxe_vat_exemption' => 'Podstawa zwolnienia z VAT',
    'settings.sections.integrations.taxe.taxe_vat_exemption.value' => 'Art. 113 ust. 1',
    'settings.sections.integrations.taxe.taxe_vat_exemption.default' => 'Art 113 ust. 1 lub ust. 9 ustawy o podatku od towarów i usług',
    
    'settings.sections.integrations.taxe.taxe_auto_sent_receipt' => 'Automatyczna wysyłka paragonów',
    'settings.sections.integrations.taxe.taxe_auto_sent_receipt.desc' => 'Zaznacz, jeżeli paragony maja być wysyłane automatycznie e-mailem do klienta. <br /><strong>Uwaga:</strong> upewnij się, że na stronie <a href="https://panel.taxe.pl/email-szablony/">Szablony wiadomości e-mail</a> masz ustawiony szablon domyślny dla czynności &quot;Wysyłka dokumentu w wiadomości e-mail&quot;.',

    'settings.sections.integrations.infakt_integration' => 'Infakt',

    'settings.sections.integrations.infakt.infakt_infakt_api_key' => 'Klucz API',
    'settings.sections.integrations.infakt.infakt_infakt_api_key.desc' => 'Ustawienia -> Inne opcje -> API',

    'settings.sections.integrations.infakt.infakt_vat_exemption' => 'Podstawa zwolnienia z VAT',
    'settings.sections.integrations.infakt.infakt_vat_exemption.value' => 'Art. 113 ust. 1',

    'settings.sections.integrations.infakt.infakt_auto_sent' => 'Automatyczna wysyłka faktur',
    'settings.sections.integrations.infakt.infakt_auto_sent.desc' => 'Zaznacz, jeżeli dokumenty sprzedaży maja być wysyłane automatycznie e-mailem do klienta',

    'settings.sections.design.fieldset.course_view_settings' => 'Ustawienia widoku kursu',
    'settings.sections.design.fieldset.directory_settings' => 'Ustawienia katalogu',

    'settings.sections.design.list_excerpt.label' => 'Pokaż skrócony opis',
    'settings.sections.design.list_excerpt.label.tooltip' => 'Aktywuj, aby przy produktach pojawił się skrócony opis. Będzie on widoczny przed zakupem. Opis ustawisz wchodząc w <b>Kursy > Edytuj wybrany kurs > Produkt >Edytuj opis produktu > Wprowadź opis i zapisz zmiany.</b>',
    'settings.sections.design.list_buy_button.label' => 'Pokaż przycisk zakupu',
    'settings.sections.design.list_buy_button.label.tooltip' => 'Aktywuj, aby przy produkcie wyświetlał się przycisk zakupu.',
    'settings.sections.design.list_pagination.label' => 'Pokaż paginacje',
    'settings.sections.design.list_pagination.label.tooltip' => 'Aktywuj, aby na dole strony pojawiła się paginacja. Możesz określić, ile kursów ma się pojawić na jednej stronie, wchodząc w <b>Ustawienia > Szablony > Edytuj aktywny szablon > Edytuj szablon "Lista kursów" > Kliknij w blok "Lista kursów" > Określ liczbę produktów na stronie.</b>',
    'settings.sections.design.display_categories.label' => 'Pokaż kategorie',
    'settings.sections.design.display_categories.label.tooltip' => 'Aktywuj, aby pod tytułem kursu wyświetliły się kategorie.',
    'settings.sections.design.show_available_quantities.label' => 'Pokaż dostępne ilości',
    'settings.sections.design.show_available_quantities.tooltip' => 'Aktywuj, aby pokazać ilość dostępnych sztuk produktów na stronie głównej',
    'settings.sections.design.show_available_quantities.package_notice' => 'Zmień plan na PLUS lub PRO, aby uzyskać dostęp do tej funkcjonalności.',

    'settings.sections.design.available_quantities_format.label' => 'Format',
    'settings.sections.design.available_quantities_format.format_x_of_y' => 'Dostępne: X z Y',
    'settings.sections.design.available_quantities_format.format_x' => 'Dostępnych sztuk: X',

    'settings.sections.design.display_tags.label' => 'Pokaż tagi',
    'settings.sections.design.display_tags.label.tooltip' => 'Aktywuj, aby pod tytułem kursu wyświetliły się tagi.',
    'settings.sections.design.default_view.label' => 'Domyślny widok',
    'settings.sections.design.default_view.label.tooltip' => 'Wybierz widok dla strony: katalogu produktów.',
    'settings.sections.design.default_view.grid' => 'Siatka',
    'settings.sections.design.default_view.grid_small' => 'Mała siatka',
    'settings.sections.design.default_view.list' => 'Lista',
    'settings.sections.design.progress_tracking.label' => 'Śledzenie postępów',
    'settings.sections.design.progress_tracking.label.tooltip' => 'Aktywuj, aby włączyć moduł śledzenia postępów w kursach. Jeżeli kursant po przerobieniu lekcji kliknie "Ukonczone" będzie to odnotowane w statystykach. Opcja ta może być nadpisana indywidualnie dla każdego kursu w jego ustawieniach.',
    'settings.sections.design.auto_progress.label' => 'Automatyczny progress',
    'settings.sections.design.auto_progress.label.tooltip' => 'Aktywuj, aby włączyć automatyczny progress. Lekcje będą automatycznie zaznaczane jako ukończone, gdy użytkownik kliknie w przycisk "Następna lekcja".',
    'settings.sections.design.display_author_info.label' => 'Autor kursu',
    'settings.sections.design.display_author_info.label.tooltip' => 'Aktywuj, aby włączyć wyświetlanie autora kursu w panelu kursu.',
    'settings.sections.design.responsive_video.label' => 'Responsywne wideo',
    'settings.sections.design.responsive_video.label.tooltip' => 'Aktywuj, aby włączyć responsywne wideo.',
    'settings.sections.design.progress_forced.label' => 'Dostęp progresywny',
    'settings.sections.design.progress_forced.label.tooltip' => 'Aktywuj, aby włączyć dostęp progresywny. Kursant będzie musiał oznaczyć lekcję jako przerobioną, aby przejść do kolejnej. Opcja ta może być nadpisana indywidualnie dla każdego kursu w jego ustawieniach.',
    'enabled' => 'Włączone',
    'disabled' => 'Wyłączone',
    'asc' => 'Rosnący',
    'desc' => 'Malejący',
    'settings.sections.design.list_price.label' => 'Pokaż cenę',
    'settings.sections.design.list_price.label.tooltip' => 'Aktywuj, aby w katalogu produktów widoczna była cena.',
    'settings.sections.design.inaccessible_lesson_display.label' => 'Wyświetlanie niedostępnych lekcji',
    'settings.sections.design.inaccessible_lesson_display.label.tooltip' => 'Zdefiniuj, jak mają wyświetlać się niedostępne lekcje.',
    'settings.sections.design.inaccessible_lesson_display.visible' => 'Zawsze widoczne',
    'settings.sections.design.inaccessible_lesson_display.grayed' => 'Widoczne, wyszarzone',
    'settings.sections.design.inaccessible_lesson_display.hidden' => 'Ukryte',
    'settings.sections.design.navigation_next_lesson_label.label' => 'Etykieta następnej lekcji',
    'settings.sections.design.navigation_next_lesson_label.label.tooltip' => 'Wybierz etykietę dla następnej lekcji.',
    'settings.sections.design.navigation_next_lesson_label.text_previous' => 'Tekst "Następna lekcja"',
    'settings.sections.design.navigation_next_lesson_label.title_previous' => 'Tytuł następnej lekcji',
    'settings.sections.design.navigation_next_lesson_label.text_next' => 'Tekst "Poprzednia lekcja"',
    'settings.sections.design.navigation_next_lesson_label.title_next' => 'Tytuł poprzedniej lekcji',
    'settings.sections.design.navigation_previous_lesson_label.label' => 'Etykieta poprzedniej lekcji',
    'settings.sections.design.navigation_previous_lesson_label.label.tooltip' => 'Wybierz etykietę dla poprzedniej lekcji.',
    'settings.sections.design.popup.label' => 'Ustawienia etykiet następnej i poprzedniej lekcji',
    'settings.sections.design.popup.label.tooltip' => 'W tym miejscu możesz skonfigurować etykiety nastepnej i poprzedniej lekcji.',
    'settings.sections.design.course_view_settings.label' => 'Ustawienia widoku kursu',
    'settings.sections.design.list_sort_type.label' => 'Rodzaj sortowania',
    'settings.sections.design.list_sort_type.label.tooltip' => 'Wybierz rodzaj sortowania.',
    'settings.sections.design.list_orderby.label' => 'Sortuj po',
    'settings.sections.design.list_orderby.label.tooltip' => 'Wybierz, w jaki sposób sortować produkty na stronie.',
    'settings.sections.design.list_orderby.post_date' => 'Data publikacji kursu',
    'settings.sections.design.list_orderby.id' => 'Identyfikator kursu',
    'settings.sections.design.list_orderby.title' => 'Nazwa kursu',
    'settings.sections.design.list_orderby.price' => 'Cena kursu',
    'settings.sections.design.list_orderby.random' => 'Losowo',
    'settings.sections.design.list_orderby.custom' => 'Kolejność własna',
    'settings.sections.design.list_orderby.custom.no_access' => 'Kolejność własna (dostępne w pakiecie PLUS/PRO)',
    'settings.sections.design.list_details_button.label' => 'Pokaż przycisk pełnego opisu',
    'settings.sections.design.list_details_button.label.tooltip' => 'Aktywuj, aby przy opisie kursu wyświetlał się przycisk "Czytaj więcej".',

    'settings.sections.design.custom_order_table.column.priority' => 'Kolejność',
    'settings.sections.design.custom_order_table.column.product_name' => 'Produkt',
    'settings.sections.design.custom_order_table.column.actions' => 'Akcje',
    'settings.sections.design.custom_order_table.column.select_product' => 'Wybierz produkt',
    'settings.sections.design.custom_order_table.button.add_product' => 'Dodaj produkt',
    'settings.sections.design.custom_order_table.button.save' => 'Zapisz',
    'settings.sections.design.custom_order_table.message.saving' => 'Zapisywanie...',
    'settings.sections.design.custom_order_table.button.cancel' => 'Anuluj',
    'settings.sections.design.custom_order_table.message.you_have_unsaved_changes' => 'masz niezapisane zmiany!',
    'settings.sections.design.custom_order_table.message.be_careful' => 'Uważaj',
    'settings.sections.design.custom_order_table.message.selected_products' => 'Ustaw kolejność produktów',
    'settings.sections.design.custom_order_table.message.no_selected_products' => 'Nie posiadasz żadnych produktów',
    'settings.sections.design.custom_order_table.message.save_success' => 'Ustawienia zostały zapisane!',
    'settings.sections.design.custom_order_table.message.save_error' => 'Podczas zapisywania wystąpił błąd. Skontaktuj się z administratorem.',
    'settings.sections.design.custom_order_table.message.product_id' => 'ID produktu',
    'settings.sections.design.custom_order_table.message.move_up' => 'Przesuń wyżej',
    'settings.sections.design.custom_order_table.message.move_down' => 'Przesuń niżej',
    'settings.sections.design.custom_order_table.message.move_to_top' => 'Przenieś na górę',
    'settings.sections.design.custom_order_table.message.move_to_bottom' => 'Przenieś na dół',

    'settings.sections.cart.data_in_form' => 'Dane w formularzu',

    'settings.sections.analytics.fieldset.google' => 'Google',
    'settings.sections.analytics.fieldset.facebook' => 'Facebook',
    'settings.sections.analytics.fieldset.additional_scripts' => 'Dodatkowe skrypty',

    'settings.sections.analytics.ga4_id' => 'Identyfikator Google Analytics 4',
    'settings.sections.analytics.ga4_id.desc' => '',
    'settings.sections.analytics.ga4_id.tooltip' => '',

    'settings.sections.analytics.enable_debug_view_ga4' => 'Włącz tryb debugowania',

    'settings.sections.analytics.ga_id' => 'Identyfikator Universal Analytics',
    'settings.sections.analytics.ga_id.desc' => '',
    'settings.sections.analytics.ga_id.tooltip' => '',

    'settings.sections.analytics.gtm_id' => 'Identyfikator Google Tag Manager',
    'settings.sections.analytics.gtm_id.desc' => '',
    'settings.sections.analytics.gtm_id.tooltip' => '',

    'settings.sections.analytics.pixel_fb_id' => 'Identyfikator pixela',
    'settings.sections.analytics.pixel_fb_id.desc' => '',
    'settings.sections.analytics.pixel_fb_id.tooltip' => '',

    'settings.sections.analytics.pixel_meta' => 'Pixel Meta',
    'settings.sections.analytics.pixel_meta.desc' => '',
    'settings.sections.analytics.pixel_meta.tooltip' => '',

    'settings.sections.analytics.pixel_meta.popup.title' => 'Pixel Meta',
    'settings.sections.analytics.pixel_meta.popup.additional_information' => 'By poprawnie skonfigurować API konwersji (Conversion API), należy wypełnić oba pola, znajdujące się poniżej.',

    'settings.sections.analytics.pixel_meta.access_token' => 'Token dostępu',
    'settings.sections.analytics.pixel_meta.access_token.desc' => '',
    'settings.sections.analytics.pixel_meta.access_token.tooltip' => '',

    'settings.sections.analytics.before_end_head' => 'Skrypt przed &lt;/head&gt;',
    'settings.sections.analytics.before_end_head.desc' => '',
    'settings.sections.analytics.before_end_head.tooltip' => '',
    'settings.sections.analytics.before_end_head.popup.title' => 'Skrypt przed &lt;/head&gt;',
    'settings.sections.analytics.before_end_head_additional' => 'Skrypt przed &lt;/head&gt;',
    'settings.sections.analytics.before_end_head_additional.desc' => "Np.: &lt;script&gt;alert('Example alert')&lt;/script&gt;",
    'settings.sections.analytics.before_end_head_additional.tooltip' => '',

    'settings.sections.analytics.after_begin_body' => 'Skrypt po &lt;body&gt;',
    'settings.sections.analytics.after_begin_body.desc' => '',
    'settings.sections.analytics.after_begin_body.tooltip' => '',
    'settings.sections.analytics.after_begin_body.popup.title' => 'Skrypt po &lt;body&gt;',
    'settings.sections.analytics.after_begin_body_additional' => 'Skrypt po &lt;body&gt;',
    'settings.sections.analytics.after_begin_body_additional.desc' => "Np.: &lt;script&gt;alert('Example alert')&lt;/script&gt;",
    'settings.sections.analytics.after_begin_body_additional.tooltip' => '',

    'settings.sections.analytics.before_end_body' => 'Skrypt przed &lt;/body&gt;',
    'settings.sections.analytics.before_end_body.desc' => '',
    'settings.sections.analytics.before_end_body.tooltip' => '',
    'settings.sections.analytics.before_end_body.popup.title' => 'Skrypt przed &lt;/body&gt;',
    'settings.sections.analytics.before_end_body_additional' => "Skrypt przed &lt;/body&gt;",
    'settings.sections.analytics.before_end_body_additional.desc' => "Np.: &lt;script&gt;alert('Example alert')&lt;/script&gt;",
    'settings.sections.analytics.before_end_body_additional.tooltip' => '',

    'settings.sections.cart.show_email2_on_checkout' => 'Weryfikacja email',
    'settings.sections.cart.show_email2_on_checkout.tooltip' => 'Aktywuj, aby włączyć weryfikację email. Pojawi się dodatkowe pole do wpisania adresu email.',
    'settings.sections.cart.edd_id_hide_fname' => 'Ukryj pole nazwisko',
    'settings.sections.cart.additional_checkboxes' => 'Dodatkowe checkboxy',
    'settings.sections.cart.sidebar' => 'Sidebar',
    'settings.sections.cart.show_comment_field' => 'Włącz pole na dodatkowy komentarz',
    'settings.sections.cart.show_comment_field.tooltip' => 'Aktywuj, aby włączyć pole na dodatkowy komentarz.',
    'settings.sections.cart.fieldset.statute' => 'Regulamin',
    'settings.sections.cart.agree_label' => 'Opis checkboxa akceptacji regulaminu',
    'settings.sections.cart.agree_label.tooltip' => 'Dodaj własny opis lub zostaw opis domyślny ("Akceptuję regulamin zakupów (konieczne do złożenia zamówienia")',
    'settings.sections.cart.info_1_title' => 'Tutuł pierwszego pola dodatkowcyh informacji.',
    'settings.sections.cart.info_1_title.tooltip' => 'Dodaj tytuł pierwszego pola dodatkowych informacji.',
    'settings.sections.cart.info_1_desc' => 'Opis pierwszego pola dodatkowych informacji.',
    'settings.sections.cart.info_1_desc.tooltip' => 'Dodaj opis pierwszego pola dodatkowych informacji.',
    'settings.sections.cart.cart_popup_2' => 'Pierwsza sekcja dodatkowych informacji',
    'settings.sections.cart.cart_popup_2.tooltip' => 'W tym miejscu skonfigurujesz pierwszą sekcje dodatkowych informacji. Możesz tu dodać np. Gwarancję satysfakcji.',
    'settings' => 'Ustawienia',
    'settings.sections.cart.info_2_title' => 'Tytuł drugiego pola dodatkowych informacji.',
    'settings.sections.cart.info_2_title.tooltip' => 'Dodaj tytuł drugiego pola dodatkowych informacji.',
    'settings.sections.cart.info_2_desc' => 'Opis drugiego pola dodatkowych informacji.',
    'settings.sections.cart.info_2_desc.tooltip' => 'Dodaj opis drugiego pola dodatkowych informacji.',
    'settings.sections.cart.cart_popup_3' => 'Druga sekcja dodatkowych informacji',
    'settings.sections.cart.cart_popup_3.tooltip' => 'W tym miejscu skonfigurujesz drugą sekcje dodatkowych informacji. Możesz tu dodać np. Bezpieczne połączenie',
    'settings.sections.cart.acd' => 'Opis checkboxu',
    'settings.sections.cart.acd.tooltip' => 'Dodaj opis checkboxu.',
    'settings.sections.cart.acdr' => 'Checkbox wymagany',
    'settings.sections.cart.acdr.tooltip' => 'Aktywuj, jeżeli checkbox ma być obowiązkowy do zaakceptowania.',
    'settings.sections.cart.acd2' => 'Opis checkboxu',
    'settings.sections.cart.acd2.tooltip' => 'Dodaj opis checkboxu.',
    'settings.sections.cart.acdr2' => 'Checkbox wymagany',
    'settings.sections.cart.acdr2.tooltip' => 'Aktywuj, jeżeli checkbox ma być obowiązkowy do zaakceptowania.',
    'settings.sections.cart.ac' => 'Checkbox 1',
    'settings.sections.cart.ac.tooltip' => 'W tym miejscu możesz skonfigurować pierwszy checkbox.',
    'settings.sections.cart.ac2' => 'Checkbox 2',
    'settings.sections.cart.ac2.tooltip' => 'W tym miejscu możesz skonfigurować drugiego checkboxa.',
    'settings.sections.cart.last_name_required' => 'Wymagaj podania nazwiska',
    'settings.sections.cart.hide_fname' => 'Ukryj pole Imię',
    'settings.sections.cart.hide_fname.tooltip' => 'Aktywuj, aby ukryć pole imię.',
    'settings.sections.cart.hide_lname' => 'Ukryj pole Nazwisko',
    'settings.sections.cart.hide_lname.tooltip' => 'Aktywuj, aby ukryć pole nazwisko',
    'settings.sections.cart.enable_field_phone' => 'Włącz pole numer telefonu',
    'settings.sections.cart.enable_field_phone.tooltip' => 'Aktywuj, aby włączyć dodatkowe pole na numer telefonu.',
    'settings.sections.cart.phone_required' => 'Wymagany numer telefonu',

    'settings.sections.messages.external_news' => 'Wiadomości zewnętrzne',

    'settings.sections.messages.sender_info' => 'Uwaga! Jeśli zmienisz poniższy adres e-mail, koniecznie zgłoś go nam mailowo, wysyłając do nas wiadomość (zapytaj@publigo.pl) z adresu e-mail, którego używasz do kontaktu z nami. W przeciwnym razie wiadomości wychodzące z Twojej platformy, przestaną działać.',

    'settings.sections.messages.fieldset.sender' => 'Nadawca',

    'settings.sections.messages.sender.from_name' => 'Nazwa nadawcy',
    'settings.sections.messages.sender.from_name.tooltip' => 'Dodaj nazwe nadawcy. Pojawi się ona w polu nadawcy w wysyłanych wiadomościach. Może to być twoje imię i nazwisko, nazwa strony lub platformy.',

    'settings.sections.messages.sender.from_email' => 'Email nadawcy',
    'settings.sections.messages.sender.from_email.tooltip' => 'Dodaj adres email, z którego będą wysyłane wiadomości. Zalecamy, aby adres był w takiej samej domenie, jak domena Twojej platformy. Dodatkowo warto rozważyć integrację platformy z systemem <a href="http://emaillabs.pl">emaillabs.pl</a> (dotyczy właścicieli Publigo BOX).',

    'settings.sections.messages.fieldset.message_after_purchase' => ' Wiadomość wysyłana po zakupie',

    'settings.sections.messages.message_after_purchase.purchase_subject' => 'Temat wiadomości',
    'settings.sections.messages.message_after_purchase.purchase_subject.tooltip' => 'Dodaj tytuł wiadomości wysyłanej po zaksięgowaniu wpłaty za kurs.',

    'settings.sections.messages.message_after_purchase.purchase_heading' => 'Nagłówek wiadomości',
    'settings.sections.messages.message_after_purchase.purchase_heading.tooltip' => 'Dodaj nagłówek wiadomości wysyłanej po zaksięgowaniu wpłaty za kurs.',

    'settings.sections.messages.message_after_purchase.purchase_receipt_popup' => 'Treść wiadomości',
    'settings.sections.messages.message_after_purchase.purchase_receipt_popup.tooltip' => 'Tutaj możesz skonfigurować treść wiadomości wysyłanej po zaksięgowaniu wpłaty za kurs.',

    'settings.sections.messages.message_after_purchase.purchase_receipt' => 'Treść',
    'settings.sections.messages.message_after_purchase.purchase_receipt.tooltip' => 'Wpisz treść wiadomości wysyłanej po zaksięgowaniu wpłaty za kurs. W treści można używać znaczników HTML oraz korzystać z tagów.',
    'settings.sections.messages.message_after_purchase.purchase_receipt.desc' => 'Skorzystaj z poniższych tagów:<br><code>{download_list}</code>- lista linków do zakupionych kursów<br><code>{name}</code>- imię kupującego<br><code>{fullname}</code>- imię i nazwisko kupującego<br><code>{username}</code>- nazwa użytkownika platformy pod którą zarejestrowany został kupujący<br><code>{user_email}</code>- email kupującego<br><code>{date}</code>- data dokonania zakupu<br><code>{price}</code>- łączna kwota płatności<br><code>{payment_id}</code> - unikalny identyfikator płatności<br><code>{receipt_id}</code> - unikalny numer rachunku<br><code>{payment_method}</code> - metoda płatności<br><code>{sitename}</code> - nazwa platformy<br><code>{discount_codes}</code> - lista kodów zniżkowych wykorzystanych przy tej transakcji<br><code>{ip_address}</code> - adres IP kupującego<br><code>{generated_discount_codes_details}</code> - lista wygenerowanych kodów zniżkowych ze szczegółami<br><code>{generated_discount_codes}</code> - lista wygenerowanych kodów zniżkowych odzielonych przecinkami<br></p>',

    'settings.sections.messages.fieldset.message_after_creating_account' => ' Wiadomość wysyłana po założeniu konta',

    'settings.sections.messages.message_after_creating_account.bpmj_edd_arc_subject' => 'Temat wiadomości',
    'settings.sections.messages.message_after_creating_account.bpmj_edd_arc_subject.tooltip' => 'Dodaj temat wiadomości wysyłanej po założeniu konta.',

    'settings.sections.messages.message_after_creating_account.edd_arc_content_popup' => 'Treść wiadomości',
    'settings.sections.messages.message_after_creating_account.edd_arc_content_popup.tooltip' => 'Tutaj możesz skonfigurować treść wiadomości wysyłanej po założeniu konta.',

    'settings.sections.messages.message_after_creating_account.bpmj_edd_arc_content' => 'Treść',
    'settings.sections.messages.message_after_creating_account.bpmj_edd_arc_content.tooltip' => 'Wpisz treść wiadomości wysyłanej po założeniu konta.',
	'settings.sections.messages.message_after_creating_account.bpmj_edd_arc_content.desc' => 'Skorzystaj z poniższych tagów:<br><code>{firstname}</code>- imię kupującego<br><code>{login}</code>- nazwa użytkownika platformy pod którą zarejestrowany został kupujący<br><code>{password}</code>- domyślne hasło kupującego<br><code>{password_reset_link}</code>- link do ustawienia nowego hasła<br></p>',

    'settings.sections.messages.messages_subscription' => 'Wiadomości dla subskrypcji',

    'settings.sections.messages.fieldset.discount_codes' => 'Kody rabatowe',

    'settings.sections.messages.discount_codes.bpmj_renewal_discount' => 'Włącz kody rabatowe w wiadomości',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount.tooltip' => 'Aktywuj, aby kody zniżkowe były generowane. Taki kod można dodać do wiadomości przypominającej o wygasającym dostępie do kursu. Zniżka może zachęcić do przedłużenia dostępu. Taką wiadomość ustawisz w <b>Przypomnieniach</b> poniżej. ',

    'settings.sections.messages.discount_codes.bpmj_renewal_discount_value' => 'Wartość kodu zniżkowego',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_value.tooltip' => 'Wybierz wartość kodu zniżkowego.',

    'settings.sections.messages.discount_codes.bpmj_renewal_discount_type' => 'Typ kodu zniżkowego',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_type.tooltip' => 'Wybierz typ kodu zniżkowego - procentowy lub kwotowy.',

    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time' => 'Okres ważności kodu rabatowego',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.tooltip' => 'Wybierz, jak długo ma być ważny kod zniżkowy (liczone od momentu wygenerowania).',

    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.one_day' => 'Jeden dzień',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.two_days' => 'Dwa dni',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.three_days' => 'Trzy dni',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.five_days' => 'Pięć dni',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.week' => 'Tydzień',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.two_weeks' => 'Dwa tygodnie',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.month' => 'Miesiąc',
    'settings.sections.messages.discount_codes.bpmj_renewal_discount_time.no_limit' => 'Bez limitu czasu',

    'settings.sections.messages.fieldset.reports' => 'Raporty',

    'settings.sections.messages.reports.bpmj_expired_access_report_email' => 'E-mail do przesyłania raportów',
    'settings.sections.messages.reports.bpmj_expired_access_report_email.tooltip' => 'Dodaj email, na który każdego dnia zostanie wysłany raport o wygasłych subskrypcjach użytkowników. Pozostaw puste pole, jeżeli nie chcesz otrzymywać takiego raportu.',

    'settings.sections.messages.fieldset.reminders' => 'Przypomnienia',

    'settings.sections.messages.reminders.paid_content_renewal' => 'Przypomnienie',
    'settings.sections.messages.reminders.paid_content_renewal.tooltip' => 'Ustaw przypomnienia o wygasającym czasie dostępu do treści, klikając w przycisk <b>Dodaj przypomnienie o odnowieniu.</b> Zaprojektujesz tam wiadomość przypominającą o odnowieniu. Po zapisaniu wszystkie dane pojawią się w tabeli obok. Przypomnienie można ustawić dla kursów, które mają określony czas dostępu.',

    'settings.sections.messages.reminder_hours.reminder_hours' => 'Godziny przypomnień',
    'settings.sections.messages.reminder_hours.reminder_hours.tooltip' => 'W jakich godzinach mają być wysyłane powiadomienia? Minimalny przedział to 5 godzin.',

    'settings.sections.messages.reminder_hours.bpmj_renewals_start' => 'Od',
    'settings.sections.messages.reminder_hours.bpmj_renewals_start.tooltip' => 'Wybierz, w jakim przedziale godzinowym będą wysyłane wiadmości z przypomnieniem o odnowieniu.',
    'settings.sections.messages.reminder_hours.bpmj_renewals_end' => 'Do',
    'settings.sections.messages.reminder_hours.bpmj_renewals_end.tooltip' => 'Wybierz, w jakim przedziale godzinowym będą wysyłane wiadmości z przypomnieniem o odnowieniu.',

    'settings.sections.messages.admin_sale_notification.title.default' => 'Zamówienie nr #{payment_id}',
    'settings.sections.messages.admin_sale_notification.message.default' => 'Dzień dobry!
    
Właśnie zostało opłacone nowe zamówienie.
    
Poniżej znajdują się szczegóły transakcji.
    
Zamówione produkty:

{download_list}

Imię i nazwisko: {fullname}
E-mail: {user_email}
Kwota: {price}
Nr zamówienia: {payment_id}
Data: {date}
Metoda płatności: {payment_method}

Komentarz kupującego: {comment}

-- 
System napędzany przez Publigo',

    'settings.sections.modules.fieldset.product_types' => 'Typy produktów',
    'settings.sections.modules.fieldset.sales_and_marketing' => 'Marketing i sprzedaż',

    'settings.sections.modules.courses_enable' => 'Kursy',
    'settings.sections.modules.courses_enable.tooltip' => 'Po włączeniu będziesz mógł sprzedawać kursy.',
    'settings.sections.modules.courses_enable.notice' => 'By wyłączyć tę funkcjonalność, usuń wszystkie kursy.',


    'settings.sections.modules.enable_digital_products' => 'Produkty cyfrowe',
    'settings.sections.modules.enable_digital_products.tooltip' => 'Gdy ta opcja jest włączona, będziesz mieć możliwość sprzedaży plików takich jak e-booki, audiobooki etc.',
    'settings.sections.modules.enable_digital_products.disable_notice' => 'By wyłączyć tę funkcjonalność, usuń wszystkie produkty cyfrowe.',

    'settings.sections.modules.enable_physical_products' => 'Produkty fizyczne',
    'settings.sections.modules.enable_physical_products.tooltip' => 'Gdy ta opcja jest włączona, będziesz mieć możliwość sprzedaży produktów fizycznych',
    'settings.sections.modules.enable_physical_products.disable_notice' => 'By wyłączyć tę funkcjonalność, usuń wszystkie produkty fizyczne.',

    'settings.sections.modules.services_enabled' => 'Usługi',
    'settings.sections.modules.services_enabled.tooltip' => 'Po włączeniu będziesz mógł sprzedawać usługi.',
    'settings.sections.modules.services_enabled.disable_notice' => 'By wyłączyć tę funkcjonalność, usuń wszystkie usługi.',

    'settings.sections.modules.increasing_sales_enabled' => 'Zwiększanie sprzedaży',
    'settings.sections.modules.increasing_sales_enabled.tooltip' => 'Po włączeniu będziesz mógł tworzyć oferty marketingowe zwiększające sprzedaż.',
    'settings.sections.modules.increasing_sales_enabled.notice' => 'Aby korzystać z tej funkcjonalności, musisz uaktualnić swoją licencję do poziomu: %s',

    'settings.sections.modules.fieldset.communication' => 'Komunikacja',

    'settings.sections.modules.enable_opinions' => 'Opinie',
    'settings.sections.modules.enable_opinions.tooltip' => 'Po włączeniu klienci będą mogli wystawiać opinie dla zakupionego kursu.',
    'settings.sections.modules.opinions_rules' => 'Adres URL regulaminu',
    'settings.sections.modules.opinions_rules.attention' => 'Aby w pełni aktywować system opinii, niezbędnym krokiem jest podanie w Konfiguruj adresu URL do regulaminu opinii.',
    'settings.sections.modules.opinions_rules.info' => 'Nie posiadasz regulaminu opinii? Wejdź na bezpieczny.biz i wygeneruj go w kilka minut.
                                                        Zobacz specjalną propozycję tylko dla klientów Publigo! Link: <a href="https://bit.ly/bbiz-opinie-pbg" target="_blank">https://bit.ly/bbiz-opinie-pbg</a>',


    'settings.sections.certificate.fieldset.certificate' => 'Certyfikaty',

    'settings.sections.certificate.enable_certificates' => 'Certyfikaty',

    'settings.sections.certificate.new_certificate.notice' => 'Certyfikaty dostępne są %s.',

    'settings.sections.certificate.enable_new_version_certificates_popup.notice' => 'Uwaga! Korzystasz ze starej wersji certyfikatów, która nie jest już rozwijana. Przejdź na nowe certyfikaty, by zyskać możliwość wizualnej edycji wzorów, tworzenia wielu szablonów, numeracji certyfikatów etc.',

    'settings.sections.certificate.enable_new_version_certificates_popup' => 'Przejdź na nowe certyfikaty',

    'settings.sections.certificate.certificate_templates' => 'Szablony certyfikatów',

    'settings.sections.gifts.enable' => "Zakupy na prezent",
    'settings.sections.gifts.email_body' => 'Wiadomość wysyłana po zakupie',
    'settings.sections.gifts.expiration_number' => '',
    'settings.sections.gifts.fieldset.voucher_as_pdf' => 'Voucher jako PDF',
    'settings.sections.gifts.generate_pdf' => 'Generuj voucher w postaci pliku PDF',
    'settings.sections.gifts.voucher_bg' => 'Tło vouchera',
    'settings.sections.gifts.voucher_orientation' => 'Orientacja vouchera',
    'portrait' => 'Pionowa',
    'landscape' => 'Pozioma',
    'settings.sections.gifts.voucher_template' => 'Szablon vouchera PDF',
    'settings.sections.gifts.voucher_template.desc' => 'Zdefiniuj szablon, na podstawie którego generowany będzie voucher PDF. W treści można używać znaczników HTML oraz korzystać z poniższych tagów:<br><strong><code>{voucher_code}</code> - Kod vouchera</strong><br><strong><code>{voucher_expiration_date}</code> - Data ważności vouchera</strong><br><strong><code>{redeem_link}</code> - Link prowadzący bezpośrednio do formualrza zamówienia z wpisanem voucherem</strong><br><strong><code>{product_name}</code> - Nazwa produktu</strong><br><code>{name}</code> - Imię kupującego<br><code>{fullname}</code> - Imię i nazwisko kupującego<br><code>{username}</code> - Nazwa użytkownika przypisana do kupującego, jeśli jest on zarejestrowany.<br><code>{user_email}</code> - Adres email kupującego<br><code>{date}</code> - Data zamówienia<br><code>{payment_id}</code> - Unikalny numer zamówienia<br><code>{receipt_id}</code> - Unikalny numer rachunku<br><code>{sitename}</code> - Nazwa Twojej strony<br><code>{generated_discount_codes_details}</code> - Wyświetla listę kupionych kodów rabatowych wraz z wszystkimi informacjami<br><code>{generated_discount_codes}</code> - Wyświetla zakupione kody rabatowe oddzielone przecinkiem<br><code>{invoice_type}</code> - Typ faktury<br><code>{invoice_person_name}</code> - Nazwa<br><code>{invoice_company_name}</code> - Nazwa<br><code>{invoice_buyer_name}</code> - Nazwa<br><code>{invoice_nip}</code> - NIP<br><code>{invoice_street}</code> - Ulica<br><code>{invoice_postcode}</code> - Kod pocztowy<br><code>{invoice_city}</code> - Miejscowość',
    'settings.sections.gifts.voucher_css' => 'Style css vouchera PDF',
    'settings.sections.gifts.voucher_css.desc' => 'W tym miejscu możesz zdefiniować style CSS dla vouchera. Możesz np. zdefiniować grafikę, która pojawi się w tle.',
    'hours' => 'Godziny',
    'days' => 'Dni',
    'weeks' => 'Tygodnie',
    'months' => 'Miesiące',
    'years' => 'Lata',
    'settings.sections.gifts.expiration' => 'Domyślny czas ważności vouchera',
    'settings.sections.gifts.unit' => 'Jednostka',
    'settings.sections.gifts.period' => 'Okres',

    'settings.sections.gifts.period.validation' => 'Podana wartość nie może być pusta i mniejsza niż 0',

    'settings.sections.cart.cart_view' => 'Widok koszyka',
    'settings.sections.cart.cart_view.standard' => 'Standardowy',
    'settings.sections.cart.cart_view.experimental' => 'Eksperymentalny',
    'settings.sections.cart.cart_view.go_back_button' => 'Dodatkowy przycisk (widok eksperymentalny)',
    'settings.sections.cart.cart_view.go_back_button_text' => 'Tekst przycisku',
    'settings.sections.cart.cart_view.go_back_button_url' => 'Adres url przycisku',

    'settings.package_notice.pro' => 'Zmień plan na PRO, aby uzyskać dostęp do tej funkcjonalności.',

    'help.diagnostics.memory_limit' => 'memory_limit',
    'help.diagnostics.memory_limit.fix_hint' => 'Zmień swoją konfigurację w pliku php.ini',
    'help.diagnostics.memory_limit.solve_hint' => 'Upewnij się, że wartość limitu jest ustawiona na conajmniej 256MB.',

    'partners.status.active' => 'Aktywny',
    'partners.status.inactive' => 'Nieaktywny',

    'alert.account_deleted' => 'Konto usunięte.',

    'templates_list.page_title' => 'Szablony',
    'templates_list.popup_title' => 'Ustawienia szablonów',
    'templates_list.column.layout_type' => 'Typ layoutu',
    'templates_list.column.layout_description' => 'Opis',
    'templates_list.actions.edit' => 'Edytuj',
    'templates_list.actions.colors_settings' => 'Ustawienia kolorów',
    'templates_list.actions.settings' => 'Ustawienia',
    'templates_list.actions.restore.active' => 'Przywróć',
    'templates_list.actions.restore.confirm_message' => 'Czy na pewno chcesz przywrócić ten layout?',

    'course_list.page_title' => 'Kursy',
    'course_list.column.id' => 'ID',
    'course_list.column.name' => 'Nazwa kursu',
    'course_list.column.show' => 'Pokaż',
    'course_list.column.sales' => 'Sprzedaż',
    'course_list.sales.status.enabled' => 'Włączona',
    'course_list.sales.status.disabled' => 'Wyłączona',
    'course_list.column.sales_limit_status' => 'Limit sprzedaży',
    'course_list.sales_limit_status.available' => 'Dostępne: %s',

    'course_list.actions.create_course' => 'Utwórz nowy kurs',
    'course_list.actions.edit' => 'Edytuj',
    'course_list.actions.duplicate' => 'Duplikuj',
    'course_list.actions.duplicate.loading' => 'Duplikuję...',
    'course_list.column.delete' => 'Usuń',
    'course_list.actions.delete.loading' => 'Usuwam...',
    'course_list.actions.delete.confirm' => 'Czy na pewno chcesz usunąć wybrany kurs? Ta czynność jest nieodwracalna.',
    'course_list.actions.delete.bulk' => 'Usuń',
    'course_list.actions.delete.bulk.confirm' => 'Czy na pewno chcesz usunąć wybrane kursy? Ta czynność jest nieodwracalna.',
    'course_list.actions.sales.bulk' => 'Włącz / wyłącz sprzedaż',
    'course_list.actions.sales.active' => 'Włącz sprzedaż',
    'course_list.actions.sales.inactive' => 'Wyłącz sprzedaż',
    'course_list.actions.sales.loading' => 'Zmieniam...',

    'course_list.popup.close' => 'Zamknij',
    'course_list.popup.purchase_links.title' => 'Linki zakupowe',
    'course_list.popup.course_stats.title' => 'Statystyki',

    'course_list.buttons.course_panel.tooltip' => 'Panel kursu',
    'course_list.buttons.course_stats.tooltip' => 'Statystyki',
    'course_list.buttons.course_students.tooltip' => 'Uczestnicy',
    'course_list.buttons.purchase_links.tooltip' => 'Linki zakupowe',
    'course_list.buttons.expiring_customers.tooltip' => 'Użytkownicy, którym wygasa dostęp',

    'course_list.stats.lesson' => 'Lekcja',
    'course_list.stats.passed' => 'Zaliczyło',

    'expiring_customers.page_title' => 'Wygasający dostęp',
    'expiring_customers.column.name' => 'Imię i nazwisko uczestnika',
    'expiring_customers.column.email' => 'E-mail uczestnika',
    'expiring_customers.column.access_to' => 'Data wygaśnięcia dostępu',
    'expiring_customers.column.course' => 'Kurs',
    'expiring_customers.access_to.unlimited' => 'Bez limitu',
    'digital_products_list.page_title' => 'Produkty cyfrowe',
    'digital_products_list.column.id' => 'ID',
    'digital_products_list.column.name' => 'Nazwa produktu cyfrowego',
    'digital_products_list.column.show' => 'Pokaż',
    'digital_products_list.column.sales' => 'Sprzedaż',

    'digital_products_list.actions.create_digital_product' => 'Utwórz nowy produkt cyfrowy',
    'digital_products_list.actions.edit' => 'Edytuj',
    'digital_products_list.actions.duplicate' => 'Duplikuj',
    'digital_products_list.actions.delete' => 'Usuń',
    'digital_products_list.actions.delete.success' => 'Produkt cyfrowy został pomyślnie usunięty!',
    'digital_products_list.actions.delete.loading' => 'Usuwam...',
    'digital_products_list.actions.delete.confirm' => 'Czy na pewno chcesz usunąć wybrany produkt cyfrowy? Ta czynność jest nieodwracalna.',
    'digital_products_list.actions.sales.bulk' => 'Włącz / wyłącz sprzedaż',
    'digital_products_list.actions.sales.active' => 'Włącz sprzedaż',
    'digital_products_list.actions.sales.inactive' => 'Wyłącz sprzedaż',
    'digital_products_list.actions.sales.loading' => 'Zmieniam...',
    'digital_products_list.actions.delete.error' => 'Podczas usuwania wystąpił błąd. Skontaktuj się z administratorem witryny.',
    'digital_products_list.actions.delete.info' => 'Nie możesz usunąć tego produktu, ponieważ jest on przypisany do przynajmniej jednego pakietu. Usuń go ze wszystkich pakietów, a potem ponów próbę usunięcia.',
    'digital_products_list.buttons.digital_product_panel.tooltip' => 'Zobacz produkt cyfrowy',

    'digital_products_list.popup.close' => 'Zamknij',
    'digital_products_list.popup.save' => 'Zapisz',
    'digital_products_list.popup.purchase_links.title' => 'Linki zakupowe',
    'digital_products_list.buttons.purchase_links.tooltip' => 'Linki zakupowe',

    'digital_products_list.sales.status.enabled' => 'Włączona',
    'digital_products_list.sales.status.disabled' => 'Wyłączona',

    'users.page_title' => 'Użytkownicy',
    'users.menu_title' => 'Użytkownicy',
    'users.column.id' => 'ID',
    'users.column.name' => 'Nazwa',
    'users.column.full_name' => 'Imię i nazwisko',
    'users.column.email' => 'E-mail',
    'users.column.roles' => 'Rola',

    'users.column.role.administrator' => 'Administrator',
    'users.column.role.lms_admin' => 'Menedżer Publigo',
    'users.column.role.lms_support' => 'Wsparcie Publigo',
    'users.column.role.editor' => 'Redaktor',
    'users.column.role.author' => 'Autor',
    'users.column.role.contributor' => 'Współtwórca',
    'users.column.role.subscriber' => 'Subskrybent',
    'users.column.role.lms_accountant' => 'Księgowy Publigo',
    'users.column.role.lms_content_manager' => 'Menedżer treści Publigo',
    'users.column.role.lms_partner' => 'Partner Publigo',
    'users.column.role.lms_assistant' => 'Asystent ds. testów i certyfikacji Publigo',

    'users.actions.edit' => 'Edytuj',
    'users.actions.delete' => 'Usuń',
    'users.actions.send_link' => 'Wyślij link do resetowania hasła',
    'users.actions.added_user' => 'Dodaj nowego użytkownika',
    'users.actions.loading' => 'Proszę czekać...',

    'users.actions.send_link.many_users.success' => 'Odnośnik do resetowania hasła został wysłany do %s użytkowników.',
    'users.actions.send_link.success' => 'Odnośnik do resetowania hasła został wysłany.',
    'users.actions.added_user.success' => 'Nowe konto użytkownika zostało pomyślnie utworzone.',
    'users.actions.delete.success' => 'Konto zostało usunięte.',

    'product_editor.sections.general.price.validation' => 'Podana wartość nie może być pusta i mniejsza niż 0.',
    'product_editor.sections.general.sale_price.validation' => 'Podana wartość nie może być pusta i mniejsza niż 0. Wpisz 0 jeśli chcesz wyłączyć cenę promocyjną.',
    'product_editor.sections.general.purchase_limit.validation' => 'Podana wartość nie może być pusta i mniejsza niż 0. Wpisz 0 by wyłączyć limit',
    'product_editor.sections.general.purchase_limit_items_left.validation' => 'Podana wartość nie może być pusta i mniejsza niż 0.',
    'product_editor.sections.general.featured_image.attachment_must_exist' => 'Adres URL musi być adresem istniejącego załącznika',
    'product_editor.sections.invoices.vat_rate.validation' => 'Podana wartość musi być większa lub równa 0. Maksymalna ilość znaków to 2',
    'product_editor.sections.invoices.vat_rate.validation.max_length' => 'Podana wartość jest błędna.',
    'product_editor.product_does_not_exist' => 'Produkt nie istnieje lub podane ID jest błędne.',

    'service_editor.add_service' => 'Dodaj usługę',
    'service_editor.page_title' => 'Edycja usługi',
    'service_editor.preview_button' => 'Zobacz usługę',

    'service_editor.sections.general' => 'Podstawowe',

    'service_editor.sections.general.fieldset.name' => 'Nazwa i opis',

    'service_editor.sections.general.name' => 'Nazwa usługi',

    'service_editor.sections.general.description' => 'Opis',
    'service_editor.sections.general.description.desc' => 'Skonfiguruj opis produktu, kategorie, tagi, rozszerzenie adresu URL oraz obrazek wyróżniający.',

    'service_editor.sections.general.short_description' => 'Krótki opis',

    'service_editor.sections.general.categories' => 'Kategorie',
    'service_editor.sections.general.select_categories' => 'Wybierz kategorie',

    'service_editor.sections.general.fieldset.location' => 'Umiejscowienie',

    'service_editor.sections.general.url' => 'Rozszerzenie dla adresu URL',

    'service_editor.sections.general.tags' => 'Tagi',
    'service_editor.sections.general.add_tags' => 'Dodaj nowy tag',
    'service_editor.sections.general.add_tags.desc' => 'Rozdzielaj przecinkami lub enterami.',

    'service_editor.sections.general.fieldset.price' => 'Cena',

    'service_editor.sections.general.price' => 'Cena',
    'service_editor.sections.general.price.tooltip' => 'Ile kosztuje dostęp do usługi? Wpisz 0 jeśli chcesz udostępniać usługę bezpłatnie.',

    'service_editor.sections.general.special_offer' => 'Promocja',

    'service_editor.sections.general.sale_price' => 'Cena promocyjna',
    'service_editor.sections.general.sale_price.tooltip' => 'Jaka jest cena promocyjna tej usługi? Wpisz 0 jeśli chcesz wyłączyć cenę promocyjną.',

    'service_editor.sections.general.sale_price_date_from' => 'Rozpoczęcie promocji',
    'service_editor.sections.general.sale_price_date_from.tooltip' => 'Moment aktywowania i deaktywowania promocji mogą być w praktyce opóźnione maksymalnie 5 minut w stosunku do ustawionej godziny',

    'service_editor.sections.general.sale_price_date_to' => 'Zakończenie promocji',
    'service_editor.sections.general.sale_price_date_to.tooltip' => 'Moment aktywowania i deaktywowania promocji mogą być w praktyce opóźnione maksymalnie 5 minut w stosunku do ustawionej godziny',


    'service_editor.sections.general.fieldset.quantities_available' => 'Dostępne ilości',
    'service_editor.sections.general.purchase_limit' => 'Łączna liczba szt. do zakupu',
    'service_editor.sections.general.purchase_limit.desc' => 'Wpisz 0 by wyłączyć limit',

    'service_editor.sections.general.purchase_limit_items_left' => 'Pozostało szt.',

    'service_editor.sections.general.fieldset.graphics' => 'Graficzne',

    'service_editor.sections.general.banner' => 'Baner',
    'service_editor.sections.general.featured_image' => 'Zdjęcie produktu',

    'service_editor.sections.general.fieldset.sale' => 'Sprzedaż',

    'service_editor.sections.general.sales_disabled' => 'Włącz sprzedaż',
    'service_editor.sections.general.sales_disabled.tooltip' => 'Zaznaczając tę opcję umożliwisz klientom zakup tego produktu',

    'service_editor.sections.general.hide_from_list' => 'Pokaż usługę w katalogu',

    'service_editor.sections.general.hide_purchase_button' => 'Pokaż przycisk zakupu',
    'service_editor.sections.general.hide_purchase_button.tooltip' => 'Ta opcja pokaże przycisk kupna na stronie produktu',

    'service_editor.sections.general.promote_course' => 'Promuj usługę na stronie głównej',
    'service_editor.sections.general.recurring_payments_enabled' => 'Włącz płatności cykliczne',
    'service_editor.sections.general.recurring_payments' => 'Płatności cykliczne',
    'service_editor.sections.general.recurring_payments_interval' => 'Odstęp czasu',
    'service_editor.sections.general.recurring_payments_interval.desc' => 'Ustawienie odstępu czasu pomiędzy powtarzającymi się płatnościami dla tej pozycji.',

    'service_editor.sections.general.payments_unit.option.days' => 'Dni',
    'service_editor.sections.general.payments_unit.option.weeks' => 'Tygodnie',
    'service_editor.sections.general.payments_unit.option.months' => 'Miesiące',
    'service_editor.sections.general.payments_unit.option.years' => 'Lata',

    'service_editor.sections.link_generator.message_1' => 'Za pomocą generatora linków, możesz przygotować link, który nie tylko od razu dodaje dany produkt do koszyka, ale również aplikuje kod znizkowy, czy aktywuje opcję zakupu na prezent. Link taki możesz umieścić w dowolnym miejscu np. na stronie sprzedażowej pod przyciskiem Kup Teraz.',

    'service_editor.sections.invoices.fieldset.general' => 'Ustawienia księgowe',

    'service_editor.sections.invoices.no_gtu' => 'Brak kodu GTU',
    'service_editor.sections.invoices.gtu' => 'Kod GTU',
    'service_editor.sections.invoices.gtu.not_supported_for' => 'Uwaga! Kody GTU nie są obsługiwane przez API:',
    'service_editor.sections.invoices.flat_rate_tax_symbol.not_supported_for' => 'Uwaga! Podatek zryczałtowany nie jest obsługiwany przez system:',

    'service_editor.sections.invoices.flat_rate_tax_symbol' => 'Stawka ryczałtu',
    'service_editor.sections.invoices.no_tax_symbol' => 'Brak stawki ryczałtu',

    'service_editor.sections.invoices.vat_rate' => 'Stawka VAT',

    'service_editor.sections.link_generator.fieldset.general' => 'Generator linków',
    'service_editor.sections.link_generator.link_generator' => 'Generator linków',

    'service_editor.sections.link_generator.variable_prices.price' => 'Cena',
    'service_editor.sections.link_generator.variable_prices.copy' => 'Kopiuj',
    'service_editor.sections.link_generator.variable_prices.copied' => 'Skopiowano',

    'service_editor.sections.link_generator' => 'Generator linków',
    'service_editor.sections.invoices' => 'Faktury',
    'service_editor.sections.mailings' => 'Systemy mailingowe',
    'service_editor.sections.discount_code' => 'Kod zniżkowy',

    'service_editor.sections.mailings.fieldset.mailings' => 'Systemy mailingowe',

    'service_editor.sections.mailings.empty_lists' => 'Nieprawidłowa konfiguracja lub brak list.',

    'service_editor.sections.mailings.mailchimp' => 'MailChimp',
    'service_editor.sections.mailings.popup.mailchimp' => 'Wybierz listy',
    'service_editor.sections.mailings.popup.mailchimp.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'service_editor.sections.mailings.mailerlite' => 'MailerLite',
    'service_editor.sections.mailings.popup.mailerlite' => 'Wybierz listy',
    'service_editor.sections.mailings.popup.mailerlite.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'service_editor.sections.mailings.freshmail' => 'FreshMail',
    'service_editor.sections.mailings.popup.freshmail' => 'Wybierz listy',
    'service_editor.sections.mailings.popup.freshmail.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'service_editor.sections.mailings.ipresso' => 'iPresso',
    'service_editor.sections.mailings.popup.ipresso_tags' => 'Dodaj tagi ',
    'service_editor.sections.mailings.popup.ipresso_tags.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>dodane</strong> do kontaktów w iPresso po zakończeniu zakupu.',

    'service_editor.sections.mailings.popup.ipresso_tags_unsubscribe' => 'Dodaj tagi ',
    'service_editor.sections.mailings.popup.ipresso_tags_unsubscribe.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>usunięte</strong> z kontaktów w iPresso po zakończeniu zakupu.',

    'service_editor.sections.mailings.activecampaign' => 'ActiveCampaign',
    'service_editor.sections.mailings.popup.activecampaign' => 'Wybierz listy',
    'service_editor.sections.mailings.popup.activecampaign.desc' => 'Wybierz listy, na które kupujący ma zostać <strong>zapisany</strong>, gdy opłaci dostęp do usługi.',

    'service_editor.sections.mailings.popup.activecampaign_unsubscribe' => 'Wybierz listy',
    'service_editor.sections.mailings.popup.activecampaign_unsubscribe.desc' => 'Wybierz listy, z których kupujący ma zostać <strong>wypisany</strong>, gdy opłaci dostęp do usługi.',

    'service_editor.sections.mailings.popup.activecampaign_tags' => 'Dodaj tagi',
    'service_editor.sections.mailings.popup.activecampaign_tags.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>dodane</strong> do kontaktów w ActiveCampaign po zakończeniu zakupu.',

    'service_editor.sections.mailings.popup.activecampaign_tags_unsubscribe' => 'Dodaj tagi',
    'service_editor.sections.mailings.popup.activecampaign_tags_unsubscribe.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>usunięte</strong> z kontaktów w ActiveCampaign po zakończeniu zakupu.',

    'service_editor.sections.mailings.getresponse' => 'GetResponse',
    'service_editor.sections.mailings.popup.getresponse' => 'Wybierz listy',
    'service_editor.sections.mailings.popup.getresponse.desc' => 'Wybierz listy, na które kupujący ma zostać <strong>zapisany</strong>, gdy opłaci dostęp do usługi.',

    'service_editor.sections.mailings.popup.getresponse_unsubscribe' => 'Wybierz listy',
    'service_editor.sections.mailings.popup.getresponse_unsubscribe.desc' => 'Wybierz listy, z których kupujący ma zostać <strong>wypisany</strong>, gdy opłaci dostęp do usługi.',

    'service_editor.sections.mailings.popup.getresponse_tags' => 'Wybierz tagi ',
    'service_editor.sections.mailings.popup.getresponse_tags.desc' => 'Wybierz tagi, do których kupujący mają być dodawani podczas zakupów.',

    'service_editor.sections.mailings.salesmanago' => 'SalesManago',
    'service_editor.sections.mailings.popup.salesmanago_tags' => 'Dodaj tagi',
    'service_editor.sections.mailings.popup.salesmanago_tags.desc' => 'Wpisz tagi (oddzielając je przecinkiem), które mają być dodane do kontaktu w panelu SALESmanago po zakupie tego produktu.',

    'service_editor.sections.mailings.interspire' => 'Interspire',
    'service_editor.sections.mailings.popup.interspire' => 'Wybierz listy',
    'service_editor.sections.mailings.popup.interspire.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'service_editor.sections.mailings.convertkit' => 'ConvertKit',
    'service_editor.sections.mailings.popup.convertkit' => 'Wybierz listy',
    'service_editor.sections.mailings.popup.convertkit.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'service_editor.sections.mailings.popup.convertkit_tags' => 'Wybierz tagi',
    'service_editor.sections.mailings.popup.convertkit_tags.desc' => 'Wybierz tagi, do których kupujący mają być <strong>dodawani</strong> podczas zakupów.',

    'service_editor.sections.mailings.popup.convertkit_tags_unsubscribe' => 'Wybierz tagi ',
    'service_editor.sections.mailings.popup.convertkit_tags_unsubscribe.desc' => 'Wybierz tagi, z których kupujący mają zostać <strong>usunięci</strong> podczas zakupów.',

    'service_editor.sections.mailings.select_list' => 'Wybierz listę lub grupę',
    'service_editor.sections.mailings.add_next' => 'Dodaj następną',

    'service_editor.sections.discount_code.message' => 'Razem z tym produktem możesz sprzedać kod rabatowy który zostanie wygenerowany na podstawie już wcześniej stworzonego. Możesz ustalić jego termin ważności.',
    'service_editor.sections.discount_code.fieldset.discount_code' => 'Kody zniżkowe',

    'service_editor.sections.discount_code.code_pattern' => 'Wybierz kod wzorcowy',
    'service_editor.sections.discount_code.code_pattern.desc' => 'Na jego podstawie wygenerujemy nowy kod po opłaceniu zamówienia.',

    'service_editor.sections.discount_code.code_time' => 'Okres ważności',
    'service_editor.sections.discount_code.code_time.desc' => 'Ten parametr jest opcjonalny. Domyślnie kod rabatowy nigdy nie wygasa.',
    'service_editor.sections.discount_code.code_time.validation' => 'Podana wartość nie może być mniejsza niż 0.',
    'service_editor.sections.discount_code.code_time.validation.must_be_a_number' => 'Podana wartość musi być liczbą.',
    'service_editor.sections.discount_code.code_time.validation.must_not_be_empty' => 'Musisz wybrać jedną z opcji w polu powyżej.',

    'service_editor.sections.discount_code.code_pattern.no_code.label' => 'Brak kuponów do wykorzystania',

    'service_editor.sections.discount_code.code_type.option.duration' => 'Okres trwania',
    'service_editor.sections.discount_code.code_type.option.days' => 'Dni',
    'service_editor.sections.discount_code.code_type.option.weeks' => 'Tygodnie',
    'service_editor.sections.discount_code.code_type.option.months' => 'Miesiące',

    'product_editor.popup.button.cancel' => 'Anuluj',
    'product_editor.popup.button.save' => 'Utwórz i edytuj',
    'product_editor.popup.button.saving' => 'Zapisuję...',

    'product_editor.popup.field.error.empty' => 'Pole nie może pozostać puste.',
    'product_editor.popup.field.error.price' => 'Podana wartość nie może być pusta i mniejsza niż 0.',

    'product_editor.popup.save.error' => 'Podczas zapisywania wystąpił błąd. Skontaktuj się z administratorem.',

    'product_editor.sections.general.variable_pricing' => 'Warianty cenowe',
    'product_editor.sections.general.variable_pricing_single_price' => 'Różne warianty w koszyku',
    'product_editor.sections.general.variable_pricing_single_price.tooltip' => 'Włącz możliwość zaznaczenia kilku opcji. Pozwala dodawać do koszyka różne opcje cenowe produktu w jednym zamówieniu.',

    'product_editor.sections.general.variable_prices' => 'Warianty cenowe',
    'product_editor.sections.general.variable_prices.edit_variants' => 'Edytuj warianty',

    'product_editor.sections.general.variable_prices.table.head.id' => 'ID',
    'product_editor.sections.general.variable_prices.table.head.name' => 'Nazwa wariantu',
    'product_editor.sections.general.variable_prices.table.head.price' => 'Cena (PLN)',
    'product_editor.sections.general.variable_prices.table.head.availability' => 'Dostępność (szt.)',
    'product_editor.sections.general.variable_prices.table.head.access_on' => 'Dostęp na',
    'product_editor.sections.general.variable_prices.table.head.recurring_payments' => 'Płatn. cykliczne',
    'product_editor.sections.general.variable_prices.table.head.interval' => 'Interwał',

    'product_editor.sections.general.variable_prices.table.body.recurring_payments_enabled.yes' => 'Tak',
    'product_editor.sections.general.variable_prices.table.body.recurring_payments_enabled.no' => 'Nie',

    'product_editor.sections.general.variable_prices.table.body.set_price_variants' => 'Ustaw warianty cenowe klikając w przycisk poniżej',
    'product_editor.sections.general.variable_prices.edit.table.purchase_limit_items_left' => 'Pozostało dostępnych szt.',
    'product_editor.sections.general.variable_prices.edit.table.purchase_limit' => 'Dostępnych szt. łącznie',

    'product_editor.sections.general.variable_prices.edit.table.message.success' => 'Warianty cenowe zostały poprawnie zapisane.',
    'product_editor.sections.general.variable_prices.edit.table.message.error' => 'Pole cena musi zostać wypełnione!',

    'product_editor.sections.general.access_time_unit.option.minutes' => 'Minuty',
    'product_editor.sections.general.access_time_unit.option.hours' => 'Godziny',
    'product_editor.sections.general.access_time_unit.option.days' => 'Dni',
    'product_editor.sections.general.access_time_unit.option.months' => 'Miesiące',
    'product_editor.sections.general.access_time_unit.option.years' => 'Lata',

    'service_popup_editor.popup.field.name' => 'Nazwa usługi',
    'service_popup_editor.popup.field.name.placeholder' => 'Podaj nazwę usługi',

    'service_popup_editor.popup.field.price' => 'Cena',
    'service_popup_editor.popup.field.price.placeholder' => 'Podaj cenę usługi',
    'service_popup_editor.popup.field.price.tooltip' => 'Ile kosztuje dostęp do usługi? Wpisz 0 jeśli chcesz udostępniać usługę bezpłatnie.',

    'support.rules.before_contacting' => 'Przed skontaktowaniem się z supportem upewnij się proszę, że rozwiązanie którego szukasz nie znajduje się w poniższych źródłach.',

    'digital_products.actions.create_digital_product' => 'Utwórz nowy produkt cyfrowy',

    'digital_products_popup_editor.popup.field.name' => 'Nazwa produktu cyfrowego',
    'digital_products_popup_editor.popup.field.name.placeholder' => 'Podaj nazwę produktu cyfrowego',

    'digital_products_popup_editor.popup.field.price' => 'Cena',
    'digital_products_popup_editor.popup.field.price.placeholder' => 'Podaj cenę produktu cyfrowego',
    'digital_products_popup_editor.popup.field.price.tooltip' => 'Ile kosztuje dostęp do produktu cyfrowego? Wpisz 0 jeśli chcesz udostępniać produkt cyfrowy bezpłatnie.',

    'digital_products.sections.general.price.tooltip' => 'Ile kosztuje dostęp do produktu cyfrowego? Wpisz 0 jeśli chcesz udostępniać produkt cyfrowy bezpłatnie.',

    'digital_product_editor.add_digital_product' => 'Dodaj produkt cyfrowy',
    'digital_product_editor.page_title' => 'Edycja produktu cyfrowego',
    'digital_product_editor.preview_button' => 'Zobacz produkt cyfrowy',

    /* digital product editor - general tab */
    'digital_product_editor.sections.general' => 'Podstawowe',

    'digital_product_editor.sections.general.fieldset.name' => 'Nazwa i opis',

    'digital_product_editor.sections.general.name' => 'Nazwa produktu cyfrowego',

    'digital_product_editor.sections.general.description' => 'Opis',
    'digital_product_editor.sections.general.description.desc' => 'Skonfiguruj opis produktu, kategorie, tagi, rozszerzenie adresu URL oraz obrazek wyróżniający.',

    'digital_product_editor.sections.general.short_description' => 'Krótki opis',

    'digital_product_editor.sections.general.categories' => 'Kategorie',
    'digital_product_editor.sections.general.select_categories' => 'Wybierz kategorie',

    'digital_product_editor.sections.general.fieldset.location' => 'Umiejscowienie',

    'digital_product_editor.sections.general.url' => 'Rozszerzenie dla adresu URL',

    'digital_product_editor.sections.general.tags' => 'Tagi',
    'digital_product_editor.sections.general.add_tags' => 'Dodaj nowy tag',
    'digital_product_editor.sections.general.add_tags.desc' => 'Rozdzielaj przecinkami lub enterami.',

    'digital_product_editor.sections.general.fieldset.price' => 'Cena',

    'digital_product_editor.sections.general.price' => 'Cena',
    'digital_product_editor.sections.general.price.validation' => 'Podana wartość nie może być pusta i mniejsza niż 0. Wpisz 0 jeśli chcesz udostępniać produkt cyfrowy bezpłatnie.',
    'digital_product_editor.sections.general.price.tooltip' => 'Ile kosztuje dostęp do produktu cyfrowego? Wpisz 0 jeśli chcesz udostępniać produkt cyfrowy bezpłatnie.',

    'digital_product_editor.sections.general.special_offer' => 'Promocja',

    'digital_product_editor.sections.general.sale_price' => 'Cena promocyjna',
    'digital_product_editor.sections.general.sale_price.tooltip' => 'Jaka jest cena promocyjna tego produktu cyfrowego? Wpisz 0 jeśli chcesz wyłączyć cenę promocyjną.',
    'digital_product_editor.sections.general.sale_price.validation' => 'Podana wartość nie może być pusta i mniejsza niż 0. Wpisz 0 jeśli chcesz wyłączyć cenę promocyjną.',

    'digital_product_editor.sections.general.sale_price_date_from' => 'Rozpoczęcie promocji',
    'digital_product_editor.sections.general.sale_price_date_from.tooltip' => 'Moment aktywowania i deaktywowania promocji mogą być w praktyce opóźnione maksymalnie 5 minut w stosunku do ustawionej godziny',

    'digital_product_editor.sections.general.sale_price_date_to' => 'Zakończenie promocji',
    'digital_product_editor.sections.general.sale_price_date_to.tooltip' => 'Moment aktywowania i deaktywowania promocji mogą być w praktyce opóźnione maksymalnie 5 minut w stosunku do ustawionej godziny',


    'digital_product_editor.sections.general.fieldset.quantities_available' => 'Dostępne ilości',
    'digital_product_editor.sections.general.purchase_limit' => 'Łączna liczba szt. do zakupu',
    'digital_product_editor.sections.general.purchase_limit.desc' => 'Wpisz 0 by wyłączyć limit',
    'digital_product_editor.sections.general.purchase_limit.validation' => 'Podana wartość nie może być pusta i mniejsza niż 0. Wpisz 0 by wyłączyć limit',

    'digital_product_editor.sections.general.purchase_limit_items_left' => 'Pozostało szt.',
    'digital_product_editor.sections.general.purchase_limit_items_left.validation' => 'Podana wartość nie może być pusta i mniejsza niż 0.',

    'digital_product_editor.sections.general.fieldset.graphics' => 'Graficzne',

    'digital_product_editor.sections.general.banner' => 'Baner',
    'digital_product_editor.sections.general.featured_image' => 'Zdjęcie produktu',
    'digital_product_editor.sections.general.featured_image.attachment_must_exist' => 'Adres URL musi być adresem istniejącego załącznika',

    'digital_product_editor.sections.general.fieldset.sale' => 'Sprzedaż',

    'digital_product_editor.sections.general.sales_disabled' => 'Włącz sprzedaż',
    'digital_product_editor.sections.general.sales_disabled.tooltip' => 'Zaznaczając tę opcję umożliwisz klientom zakup tego produktu',

    'digital_product_editor.sections.general.hide_from_list' => 'Pokaż produkt cyfrowy w katalogu',

    'digital_product_editor.sections.general.hide_purchase_button' => 'Pokaż przycisk zakupu',
    'digital_product_editor.sections.general.hide_purchase_button.tooltip' => 'Ta opcja pokaże przycisk kupna na stronie produktu',

    'digital_product_editor.sections.general.promote_course' => 'Promuj produkt cyfrowy na stronie głównej',
    'digital_product_editor.sections.general.recurring_payments_enabled' => 'Włącz płatności cykliczne',
    'digital_product_editor.sections.general.recurring_payments' => 'Płatności cykliczne',
    'digital_product_editor.sections.general.recurring_payments_interval' => 'Odstęp czasu',
    'digital_product_editor.sections.general.recurring_payments_interval.desc' => 'Ustawienie odstępu czasu pomiędzy powtarzającymi się płatnościami dla tej pozycji.',

    'digital_product_editor.sections.general.payments_unit.option.days' => 'Dni',
    'digital_product_editor.sections.general.payments_unit.option.weeks' => 'Tygodnie',
    'digital_product_editor.sections.general.payments_unit.option.months' => 'Miesiące',
    'digital_product_editor.sections.general.payments_unit.option.years' => 'Lata',

    /* digital product editor - mailings tab */
    'digital_product_editor.sections.mailings.fieldset.mailings' => 'Systemy mailingowe',

    'digital_product_editor.sections.mailings.empty_lists' => 'Nieprawidłowa konfiguracja lub brak list.',

    'digital_product_editor.sections.mailings.mailchimp' => 'MailChimp',
    'digital_product_editor.sections.mailings.popup.mailchimp' => 'Wybierz listy',
    'digital_product_editor.sections.mailings.popup.mailchimp.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do produktu cyfrowego',

    'digital_product_editor.sections.mailings.mailerlite' => 'MailerLite',
    'digital_product_editor.sections.mailings.popup.mailerlite' => 'Wybierz listy',
    'digital_product_editor.sections.mailings.popup.mailerlite.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do produktu cyfrowego',

    'digital_product_editor.sections.mailings.freshmail' => 'FreshMail',
    'digital_product_editor.sections.mailings.popup.freshmail' => 'Wybierz listy',
    'digital_product_editor.sections.mailings.popup.freshmail.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do produktu cyfrowego',

    'digital_product_editor.sections.mailings.ipresso' => 'iPresso',
    'digital_product_editor.sections.mailings.popup.ipresso_tags' => 'Dodaj tagi ',
    'digital_product_editor.sections.mailings.popup.ipresso_tags.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>dodane</strong> do kontaktów w iPresso po zakończeniu zakupu.',

    'digital_product_editor.sections.mailings.popup.ipresso_tags_unsubscribe' => 'Dodaj tagi ',
    'digital_product_editor.sections.mailings.popup.ipresso_tags_unsubscribe.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>usunięte</strong> z kontaktów w iPresso po zakończeniu zakupu.',

    'digital_product_editor.sections.mailings.activecampaign' => 'ActiveCampaign',
    'digital_product_editor.sections.mailings.popup.activecampaign' => 'Wybierz listy',
    'digital_product_editor.sections.mailings.popup.activecampaign.desc' => 'Wybierz listy, na które kupujący ma zostać <strong>zapisany</strong>, gdy opłaci dostęp do produktu cyfrowego.',

    'digital_product_editor.sections.mailings.popup.activecampaign_unsubscribe' => 'Wybierz listy',
    'digital_product_editor.sections.mailings.popup.activecampaign_unsubscribe.desc' => 'Wybierz listy, z których kupujący ma zostać <strong>wypisany</strong>, gdy opłaci dostęp do kursu.',

    'digital_product_editor.sections.mailings.popup.activecampaign_tags' => 'Dodaj tagi',
    'digital_product_editor.sections.mailings.popup.activecampaign_tags.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>dodane</strong> do kontaktów w ActiveCampaign po zakończeniu zakupu.',

    'digital_product_editor.sections.mailings.popup.activecampaign_tags_unsubscribe' => 'Dodaj tagi',
    'digital_product_editor.sections.mailings.popup.activecampaign_tags_unsubscribe.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>usunięte</strong> z kontaktów w ActiveCampaign po zakończeniu zakupu.',

    'digital_product_editor.sections.mailings.getresponse' => 'GetResponse',
    'digital_product_editor.sections.mailings.popup.getresponse' => 'Wybierz listy',
    'digital_product_editor.sections.mailings.popup.getresponse.desc' => 'Wybierz listy, na które kupujący ma zostać <strong>zapisany</strong>, gdy opłaci dostęp do produktu cyfrowego.',

    'digital_product_editor.sections.mailings.popup.getresponse_unsubscribe' => 'Wybierz listy',
    'digital_product_editor.sections.mailings.popup.getresponse_unsubscribe.desc' => 'Wybierz listy, z których kupujący ma zostać <strong>wypisany</strong>, gdy opłaci dostęp do produktu cyfrowego.',

    'digital_product_editor.sections.mailings.popup.getresponse_tags' => 'Wybierz tagi ',
    'digital_product_editor.sections.mailings.popup.getresponse_tags.desc' => 'Wybierz tagi, do których kupujący mają być dodawani podczas zakupów.',

    'digital_product_editor.sections.mailings.salesmanago' => 'SalesManago',
    'digital_product_editor.sections.mailings.popup.salesmanago_tags' => 'Dodaj tagi',
    'digital_product_editor.sections.mailings.popup.salesmanago_tags.desc' => 'Wpisz tagi (oddzielając je przecinkiem), które mają być dodane do kontaktu w panelu SALESmanago po zakupie tego produktu.',

    'digital_product_editor.sections.mailings.interspire' => 'Interspire',
    'digital_product_editor.sections.mailings.popup.interspire' => 'Wybierz listy',
    'digital_product_editor.sections.mailings.popup.interspire.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do produktu cyfrowego',

    'digital_product_editor.sections.mailings.convertkit' => 'ConvertKit',
    'digital_product_editor.sections.mailings.popup.convertkit' => 'Wybierz listy',
    'digital_product_editor.sections.mailings.popup.convertkit.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do produktu cyfrowego',

    'digital_product_editor.sections.mailings.popup.convertkit_tags' => 'Wybierz tagi',
    'digital_product_editor.sections.mailings.popup.convertkit_tags.desc' => 'Wybierz tagi, do których kupujący mają być <strong>dodawani</strong> podczas zakupów.',

    'digital_product_editor.sections.mailings.popup.convertkit_tags_unsubscribe' => 'Wybierz tagi ',
    'digital_product_editor.sections.mailings.popup.convertkit_tags_unsubscribe.desc' => 'Wybierz tagi, z których kupujący mają zostać <strong>usunięci</strong> podczas zakupów.',

    'digital_product_editor.sections.mailings.select_list' => 'Wybierz listę lub grupę',
    'digital_product_editor.sections.mailings.add_next' => 'Dodaj następną',

    /* digital product editor - files tab */
    'digital_product_editor.sections.files' => 'Pliki',
    'digital_product_editor.sections.files.info' => 'Poniżej możesz dodać różnego rodzaju pliki (pdf, audio, dokumenty, arkusze, grafiki itp.), które zostaną udostępnione Klientowi po dokonaniu zakupu. Możliwe jest także zdefiniowanie dla nich unikatowych nazw oraz ustawienie ich w określonej kolejności.',

    'digital_product_editor.sections.files.table.column.priority' => 'Kolejność',
    'digital_product_editor.sections.files.table.column.file_name' => 'Nazwa pliku',
    'digital_product_editor.sections.files.table.column.file_url' => 'Adres URL pliku',
    'digital_product_editor.sections.files.table.button.browse_media' => 'Wybierz plik',
    'digital_product_editor.sections.files.table.button.add_file' => 'Dodaj plik',
    'digital_product_editor.sections.files.table.button.save' => 'Zapisz',
    'digital_product_editor.sections.files.table.message.saving' => 'Zapisywanie...',
    'digital_product_editor.sections.files.table.button.cancel' => 'Anuluj',
    'digital_product_editor.sections.files.table.message.you_have_unsaved_changes' => 'masz niezapisane zmiany!',
    'digital_product_editor.sections.files.table.message.be_careful' => 'Uważaj',
    'digital_product_editor.sections.files.table.message.active_files' => 'Załadowane pliki',
    'digital_product_editor.sections.files.table.message.no_active_files' => 'Nie załadowano żadnych plików',
    'digital_product_editor.sections.files.table.message.save_success' => 'Ustawienia zostały zapisane!',
    'digital_product_editor.sections.files.table.message.save_error' => 'Podczas zapisywania wystąpił błąd. Skontaktuj się z administratorem.',

    'products.add_to_cart_button.sold_out' => 'Wyprzedane',
    'products.add_to_cart_button.sales_disabled' => 'Sprzedaż wyłączona',

    'my_account.my_digital_products.free_video_storage_space' => 'Status płatności: %s',

    'courses.duplicated_course_suffix' => '(kopia)',

    'course_editor.page_title' => 'Edycja kursu',
    'course_editor.preview_button.course' => 'Zobacz kurs',
    'course_editor.preview_button.course_panel' => 'Zobacz panel kursu',
    'course_editor.course_does_not_exist' => 'Kurs nie istnieje lub podane ID jest błędne.',

    'course_editor.sections.general' => 'Podstawowe',
    'course_editor.sections.structure' => 'Struktura',
    'course_editor.sections.link_generator' => 'Generator linków',
    'course_editor.sections.invoices' => 'Faktury',
    'course_editor.sections.mailings' => 'Systemy mailingowe',
    'course_editor.sections.discount_code' => 'Kod zniżkowy',

    'courses_popup_editor.popup.field.name' => 'Nazwa kursu',
    'courses_popup_editor.popup.field.name.placeholder' => 'Podaj nazwę kursu',

    'courses_popup_editor.popup.field.price' => 'Cena',
    'courses_popup_editor.popup.field.price.placeholder' => 'Podaj cenę kursu',
    'courses_popup_editor.popup.field.price.tooltip' => 'Ile kosztuje dostęp do kursu? Wpisz 0 jeśli chcesz udostępniać kurs bezpłatnie.',

    'course_editor.sections.general.fieldset.name' => 'Nazwa i opis',

    'course_editor.sections.general.name' => 'Nazwa produktu',

    'course_editor.sections.general.description' => 'Pełny opis oferty kursu',
    'course_editor.sections.general.description.desc' => 'Skonfiguruj opis kursu, kategorie, tagi, rozszerzenie adresu URL oraz obrazek wyróżniający.',

    'course_editor.sections.general.short_description' => 'Skrócony opis oferty kursu',
    'course_editor.sections.general.welcome' => 'Powitanie / opis w panelu kursanta',

    'course_editor.sections.general.categories' => 'Kategorie',
    'course_editor.sections.general.select_categories' => 'Wybierz kategorie',

    'course_editor.sections.general.fieldset.location' => 'Umiejscowienie',

    'course_editor.sections.general.url' => 'Rozszerzenie dla adresu URL',

    'course_editor.sections.general.tags' => 'Tagi',
    'course_editor.sections.general.add_tags' => 'Dodaj nowy tag',
    'course_editor.sections.general.add_tags.desc' => 'Rozdzielaj przecinkami lub enterami.',

    'course_editor.sections.general.fieldset.graphics' => 'Graficzne',

    'course_editor.sections.general.fieldset.view' => 'Widok',

    'course_editor.sections.general.view_options' => 'Opcje widoku',

    'course_editor.sections.general.navigation_next_lesson_label.label' => 'Etykieta następnej lekcji',
    'course_editor.sections.general.navigation_next_lesson_label.label.tooltip' => 'Wybierz etykietę dla następnej lekcji.',

    'course_editor.sections.general.view_options.default' => 'Domyślna (%s)',
    'course_editor.sections.general.navigation_next_lesson_label.lesson' => 'Tekst "Następna lekcja"',
    'course_editor.sections.general.navigation_next_lesson_label.lesson_title' => 'Tytuł następnej lekcji',
    'course_editor.sections.general.navigation_previous_lesson_label.lesson' => 'Tekst "Poprzednia lekcja"',
    'course_editor.sections.general.navigation_previous_lesson_label.lesson_title' => 'Tytuł poprzedniej lekcji',
    'course_editor.sections.general.navigation_previous_lesson_label.label' => 'Etykieta poprzedniej lekcji',
    'course_editor.sections.general.navigation_previous_lesson_label.label.tooltip' => 'Wybierz etykietę dla poprzedniej lekcji.',

    'course_editor.sections.general.inaccessible_lesson_display.label' => 'Wyświetlanie niedostępnych lekcji',

    'course_editor.sections.general.inaccessible_lesson_display.visible' => 'Zawsze widoczne',
    'course_editor.sections.general.inaccessible_lesson_display.grayed' => 'Widoczne, wyszarzone',
    'course_editor.sections.general.inaccessible_lesson_display.hidden' => 'Ukryte',

    'course_editor.sections.general.progress_tracking' => 'Śledzenie postępów',

    'course_editor.sections.general.progress_forced.label' => 'Dostęp progresywny',

    'course_editor.sections.general.progress_forced.enabled' => 'Włączone',
    'course_editor.sections.general.progress_forced.disabled' => 'Wyłączone',

    'course_editor.sections.general.progress_tracking.option.on' => 'Włączone',
    'course_editor.sections.general.progress_tracking.option.off' => 'Wyłączone',

    'course_editor.sections.general.featured_image' => 'Miniaturka kursu',
    'course_editor.sections.general.logo' => 'Logo kursu',
    'course_editor.sections.general.banner' => 'Baner kursu',

    'course_editor.sections.general.fieldset.certification' => 'Certyfikacja',

    'course_editor.sections.general.disable_certificates' => 'Certyfikaty',
    'course_editor.sections.general.disable_certificates.tooltip' => 'Zaznaczenie tej opcji aktywuje certyfikaty tylko dla tego kursu',
    'course_editor.sections.general.disable_certificates.popup.title' => 'Certyfikaty',

    'course_editor.sections.general.fieldset.sale' => 'Sprzedaż',

    'course_editor.sections.general.sales_disabled' => 'Włącz sprzedaż',
    'course_editor.sections.general.sales_disabled.tooltip' => 'Zaznaczając tę opcję umożliwisz klientom zakup tego kursu',

    'course_editor.sections.general.hide_from_list' => 'Pokaż kurs w katalogu',

    'course_editor.sections.general.hide_purchase_button' => 'Pokaż przycisk zakupu',
    'course_editor.sections.general.hide_purchase_button.tooltip' => 'Ta opcja pokaże przycisk kupna na stronie kursu',

    'course_editor.sections.general.promote_course' => 'Promuj kurs na stronie głównej',

    'course_editor.sections.general.fieldset.no_authorization' => 'Brak autoryzacji',

    'course_editor.sections.general.redirect_page' => 'Przekierowanie nieautoryzowanych użytkowników',
    'course_editor.sections.general.redirect_page.tooltip' => 'Gdy użytkownik nie posiada dostępu do danej strony, przekieruj go na:',
    'course_editor.sections.general.redirect_page.select.choose' => 'Wybierz stronę ...',

    'course_editor.sections.general.redirect_url' => 'lub adres URL',

    'course_editor.sections.general.certificate_template_id' => 'Wybierz szablon certyfikatu',
    'course_editor.sections.general.certificate_template_id.tooltip' => 'Możesz wybrać, który z szablonów ma zostać użyty do wygenerowania certyfikatu po zakończeniu tego kursu.',
    'course_editor.sections.general.certificate_template_id.select.default' => 'Domyślny',

    'course_editor.sections.general.enable_certificate_numbering' => 'Włącz numerację certyfikatów',

    'course_editor.sections.general.certificate_numbering_pattern' => 'Wzorzec numeracji',

    'course_editor.sections.general.disable_email_subscription' => 'Powiadomienia dotyczące subskrypcji',

    'course_editor.sections.general.fieldset.price' => 'Cena',

    'course_editor.sections.general.variable_pricing' => 'Warianty cenowe',
    'course_editor.sections.general.variable_pricing_single_price' => 'Różne warianty w koszyku',

    'course_editor.sections.general.variable_pricing.notice' => 'Uwaga! Włączenie lub wyłączenie wariantów, spowoduje wyłączenie sprzedaży kursu. W każdej chwili można będzie włączyć ją ponownie.',
    'course_editor.sections.general.sales_disabled.notice' => 'By móc włączyć sprzedaż, dodaj przynajmniej jeden wariant cenowy lub dezaktywuj moduł wariantów.',

    'course_editor.sections.general.variable_pricing_single_price.tooltip' => 'Włącz możliwość zaznaczenia kilku opcji. Pozwala dodawać do koszyka różne opcje cenowe produktu w jednym zamówieniu.',

    'course_editor.sections.general.variable_prices' => 'Warianty cenowe',
    'course_editor.sections.general.variable_prices.edit_variants' => 'Edytuj warianty',

    'course_editor.sections.general.variable_prices.table.head.id' => 'ID',
    'course_editor.sections.general.variable_prices.table.head.name' => 'Nazwa wariantu',
    'course_editor.sections.general.variable_prices.table.head.price' => 'Cena (PLN)',
    'course_editor.sections.general.variable_prices.table.head.availability' => 'Dostępność (szt.)',
    'course_editor.sections.general.variable_prices.table.head.access_on' => 'Dostęp na',
    'course_editor.sections.general.variable_prices.table.head.recurring_payments' => 'Płatn. cykliczne',

    'course_editor.sections.general.variable_prices.table.body.recurring_payments_enabled.yes' => 'Tak',
    'course_editor.sections.general.variable_prices.table.body.recurring_payments_enabled.no' => 'Nie',

    'course_editor.sections.general.variable_prices.table.body.set_price_variants' => 'Ustaw warianty cenowe klikając w przycisk poniżej',

    'course_editor.sections.general.variable_prices.edit.table.purchase_limit_items_left' => 'Pozostało dostępnych szt.',
    'course_editor.sections.general.variable_prices.edit.table.purchase_limit' => 'Dostępnych szt. łącznie',

    'course_editor.sections.general.variable_prices.edit.table.message.success' => 'Warianty cenowe zostały poprawnie zapisane.',
    'course_editor.sections.general.variable_prices.edit.table.message.error' => 'Pole cena musi zostać wypełnione!',

    'course_editor.sections.general.variable_prices.edit.table.message.one_price_min' => 'Musisz mieć co najmniej jeden wariant cenowy',

    'course_editor.sections.general.variable_prices.edit.table.message.one_field_min' => 'Musisz mieć co najmniej jedno uzupełnione pole',

    'course_editor.sections.general.price' => 'Cena',
    'course_editor.sections.general.price.tooltip' => 'Ile kosztuje dostęp do kursu? Wpisz 0 jeśli chcesz udostępniać kurs bezpłatnie.',

    'course_editor.sections.general.special_offer' => 'Promocja',
    'course_editor.sections.general.variable_prices_special_offer' => 'Planowanie promocji',

    'course_editor.sections.general.sale_price' => 'Cena promocyjna',
    'course_editor.sections.general.sale_price.tooltip' => 'Jaka jest cena promocyjna tego kursu? Wpisz 0 jeśli chcesz wyłączyć cenę promocyjną.',

    'course_editor.sections.general.sale_price_date_from' => 'Rozpoczęcie promocji',
    'course_editor.sections.general.sale_price_date_from.tooltip' => 'Moment aktywowania i deaktywowania promocji mogą być w praktyce opóźnione maksymalnie 5 minut w stosunku do ustawionej godziny',

    'course_editor.sections.general.sale_price_date_to' => 'Zakończenie promocji',
    'course_editor.sections.general.sale_price_date_to.tooltip' => 'Moment aktywowania i deaktywowania promocji mogą być w praktyce opóźnione maksymalnie 5 minut w stosunku do ustawionej godziny',


    'course_editor.sections.general.fieldset.quantities_available' => 'Dostępne ilości',

    'course_editor.sections.general.purchase_limit' => 'Łączna liczba szt. do zakupu',
    'course_editor.sections.general.purchase_limit.desc' => 'Wpisz 0 lub pozostaw puste by wyłączyć limit',

    'course_editor.sections.general.purchase_limit_items_left' => 'Pozostało sztuk:',

    'course_editor.sections.general.fieldset.course_start' => 'Rozpoczęcie kursu i długość dostępu',

    'course_editor.sections.general.access_time' => 'Dostęp na',
    'course_editor.sections.general.access_time.tooltip' => 'Przez jaki okres czasu kursant będzie miał dostęp do wykupionego kursu? Pozostaw pole puste, aby wyłączyć ograniczenie czasowe (dostęp bez limitu czasu).',

    'course_editor.sections.general.code_time.validation' => 'Podana wartość nie może być mniejsza niż 0.',
    'course_editor.sections.general.code_time.validation.must_be_a_number' => 'Podana wartość musi być liczbą.',
    'course_editor.sections.general.code_time.validation.must_not_be_empty' => 'Musisz wybrać jedną z opcji w polu powyżej.',

    'course_editor.sections.general.access_time_unit.option.minutes' => 'Minuty',
    'course_editor.sections.general.access_time_unit.option.hours' => 'Godziny',
    'course_editor.sections.general.access_time_unit.option.days' => 'Dni',
    'course_editor.sections.general.access_time_unit.option.months' => 'Miesiące',
    'course_editor.sections.general.access_time_unit.option.years' => 'Lata',

    'course_editor.sections.general.access_start' => 'Data startu kursu',
    'course_editor.sections.general.access_start.desc' => 'Uwaga! Zmiana daty będzie miała wpływ tylko na nowych kursantów. Ci, którzy dokonali zakupu przed jej zmianą, uzyskają dostęp według pierwotnych ustawień.',
    'course_editor.sections.general.access_start.tooltip' => 'Użyj tej opcji aby określić od kiedy zawartość kursu będzie dostępna dla kursantów. Pozostaw pole puste, aby wyłączyć datę startu.',
    'course_editor.sections.general.access_start.at' => 'o godzinie',

    'course_editor.sections.general.recurring_payments_enabled' => 'Włącz płatności cykliczne',
    'course_editor.sections.general.recurring_payments' => 'Płatności cykliczne',
    'course_editor.sections.general.recurring_payments_interval' => 'Odstęp czasu',
    'course_editor.sections.general.recurring_payments_interval.desc' => 'Ustawienie odstępu czasu pomiędzy powtarzającymi się płatnościami dla tej pozycji.',

    'course_editor.sections.general.payments_unit.option.days' => 'Dni',
    'course_editor.sections.general.payments_unit.option.weeks' => 'Tygodnie',
    'course_editor.sections.general.payments_unit.option.months' => 'Miesiące',
    'course_editor.sections.general.payments_unit.option.years' => 'Lata',

    'course_editor.sections.general.custom_purchase_link' => 'Link do zewnętrznej oferty',
    'course_editor.sections.general.custom_purchase_link.tooltip' => 'Adres url do którego ma nastąpić przekierowanie po kliknięciu w przycisk „Zamów”. Pozostaw pole puste jeżeli przycisk ma działać w standardowy sposób.',


    'course_editor.sections.mailings.fieldset.mailings' => 'Systemy mailingowe',

    'course_editor.sections.mailings.empty_lists' => 'Nieprawidłowa konfiguracja lub brak list.',

    'course_editor.sections.mailings.mailchimp' => 'MailChimp',
    'course_editor.sections.mailings.popup.mailchimp' => 'Wybierz listy',
    'course_editor.sections.mailings.popup.mailchimp.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'course_editor.sections.mailings.mailerlite' => 'MailerLite',
    'course_editor.sections.mailings.popup.mailerlite' => 'Wybierz listy',
    'course_editor.sections.mailings.popup.mailerlite.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'course_editor.sections.mailings.freshmail' => 'FreshMail',
    'course_editor.sections.mailings.popup.freshmail' => 'Wybierz listy',
    'course_editor.sections.mailings.popup.freshmail.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'course_editor.sections.mailings.ipresso' => 'iPresso',
    'course_editor.sections.mailings.popup.ipresso_tags' => 'Dodaj tagi ',
    'course_editor.sections.mailings.popup.ipresso_tags.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>dodane</strong> do kontaktów w iPresso po zakończeniu zakupu.',

    'course_editor.sections.mailings.popup.ipresso_tags_unsubscribe' => 'Dodaj tagi ',
    'course_editor.sections.mailings.popup.ipresso_tags_unsubscribe.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>usunięte</strong> z kontaktów w iPresso po zakończeniu zakupu.',

    'course_editor.sections.mailings.activecampaign' => 'ActiveCampaign',
    'course_editor.sections.mailings.popup.activecampaign' => 'Wybierz listy',
    'course_editor.sections.mailings.popup.activecampaign.desc' => 'Wybierz listy, na które kupujący ma zostać <strong>zapisany</strong>, gdy opłaci dostęp do usługi.',

    'course_editor.sections.mailings.popup.activecampaign_unsubscribe' => 'Wybierz listy',
    'course_editor.sections.mailings.popup.activecampaign_unsubscribe.desc' => 'Wybierz listy, z których kupujący ma zostać <strong>wypisany</strong>, gdy opłaci dostęp do usługi.',

    'course_editor.sections.mailings.popup.activecampaign_tags' => 'Dodaj tagi',
    'course_editor.sections.mailings.popup.activecampaign_tags.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>dodane</strong> do kontaktów w ActiveCampaign po zakończeniu zakupu.',

    'course_editor.sections.mailings.popup.activecampaign_tags_unsubscribe' => 'Dodaj tagi',
    'course_editor.sections.mailings.popup.activecampaign_tags_unsubscribe.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>usunięte</strong> z kontaktów w ActiveCampaign po zakończeniu zakupu.',

    'course_editor.sections.mailings.getresponse' => 'GetResponse',
    'course_editor.sections.mailings.popup.getresponse' => 'Wybierz listy',
    'course_editor.sections.mailings.popup.getresponse.desc' => 'Wybierz listy, na które kupujący ma zostać <strong>zapisany</strong>, gdy opłaci dostęp do usługi.',

    'course_editor.sections.mailings.popup.getresponse_unsubscribe' => 'Wybierz listy',
    'course_editor.sections.mailings.popup.getresponse_unsubscribe.desc' => 'Wybierz listy, z których kupujący ma zostać <strong>wypisany</strong>, gdy opłaci dostęp do usługi.',

    'course_editor.sections.mailings.popup.getresponse_tags' => 'Wybierz tagi ',
    'course_editor.sections.mailings.popup.getresponse_tags.desc' => 'Wybierz tagi, do których kupujący mają być dodawani podczas zakupów.',

    'course_editor.sections.mailings.salesmanago' => 'SalesManago',
    'course_editor.sections.mailings.popup.salesmanago_tags' => 'Dodaj tagi',
    'course_editor.sections.mailings.popup.salesmanago_tags.desc' => 'Wpisz tagi (oddzielając je przecinkiem), które mają być dodane do kontaktu w panelu SALESmanago po zakupie tego produktu.',

    'course_editor.sections.mailings.interspire' => 'Interspire',
    'course_editor.sections.mailings.popup.interspire' => 'Wybierz listy',
    'course_editor.sections.mailings.popup.interspire.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'course_editor.sections.mailings.convertkit' => 'ConvertKit',
    'course_editor.sections.mailings.popup.convertkit' => 'Wybierz listy',
    'course_editor.sections.mailings.popup.convertkit.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'course_editor.sections.mailings.popup.convertkit_tags' => 'Wybierz tagi',
    'course_editor.sections.mailings.popup.convertkit_tags.desc' => 'Wybierz tagi, do których kupujący mają być <strong>dodawani</strong> podczas zakupów.',

    'course_editor.sections.mailings.popup.convertkit_tags_unsubscribe' => 'Wybierz tagi ',
    'course_editor.sections.mailings.popup.convertkit_tags_unsubscribe.desc' => 'Wybierz tagi, z których kupujący mają zostać <strong>usunięci</strong> podczas zakupów.',

    'course_editor.sections.mailings.select_list' => 'Wybierz listę lub grupę',
    'course_editor.sections.mailings.add_next' => 'Dodaj następną',
    'user_security.user_banned.forever' => 'Zostałeś zablokowany dożywotnio',
    'user_security.user_banned.temporarily' => '<strong>Zostałeś zablokowany!</strong> Poczekaj ',
    'user_security.user_banned.just_a_moment' => 'jeszcze chwilę',

    'product.lowest_price_information' => 'Najniższa cena z ostatnich %d dni: %.2f %s',

    'course_editor.sections.structure.success_message' => 'Struktura została poprawnie zapisana.',
    'course_editor.sections.structure.error_message' => 'Podczas zapisywania wystąpił błąd. Skontaktuj się z administratorem witryny.',
    'course_editor.sections.structure.validation.empty_structure' => 'Przed zapisem dodaj strukturę dla swojego kursu.',
    'course_editor.sections.structure.validation.error_message' => 'Podane dane w strukturze są błędne.',

    'course_editor.sections.structure.info' => 'Poniżej możesz dodać strukturę dla swojego kursu. W zależności od potrzeb, masz do wyboru moduły, lekcje i quizy. Ikonka ołówka przy każdej z pozycji, otworzy nową zakładkę w przeglądarce, gdzie można będzie dokonać edycji poszczególnych treści.',

    'course_editor.sections.structure.fieldset.module.disabled' => 'Musisz zapisać zmiany, by móc edytować tę treść.',

    'course_editor.sections.structure.fieldset.module_availability' => 'Dostępność modułów i lekcji',

    'course_editor.sections.structure.default_drip_value' => 'Zmień opóźnienie udostępniania',

    'course_editor.sections.structure.fieldset.drip_value' => 'Opóźnienie udostępniania dla modułów i lekcji',
    'course_editor.sections.structure.fieldset.drip_value.tooltip' => 'Np. aby udostępniać zawartość jednej lekcji dziennie, ustaw 1 dzień. Pozostaw pole puste, aby udostępnić wszystkie lekcje od razu po zakupie kursu.',

    'course_editor.sections.structure.fieldset.drip_unit' => 'Jednostka opóźnienia',

    'course_editor.sections.structure.set_modules_lessons' => 'Ustaw dla modułów i lekcji',
    'course_editor.sections.structure.change_drip_unit' => 'Zmień jednostkę opóźnienia',
    'course_editor.sections.structure.set_drip_unit' => 'Ustaw jednostkę opóźnienia',

    'course_editor.sections.structure.variant_color_legend' => 'Wybierz, które moduły, lekcje i quizy mają być przypisane do poszczególnych wariantów:',


    'course_editor.sections.structure.delay' => 'Aby korzystać z funkcjonalności opóźnionego udostępniania treści, musisz zmienić swoją licencje na: %s',
    'course_editor.sections.structure.delay.info' => 'Np. aby udostępniać zawartość jednej lekcji dziennie, ustaw <strong>1 dzień</strong>. Pozostaw pole puste, aby udostępnić wszystkie lekcje od razu po zakupie kursu.',
    'course_editor.sections.structure.upgrade_needed' => 'Zmień pakiet',

    'course_editor.sections.structure.before_save.info1' => 'Zanim rozpoczniesz edycję nowych lekcji, zapisz zmiany w kursie!',
    'course_editor.sections.structure.before_save.info2' => 'Możesz przesuwać moduły i lekcje, aby zmienić ich kolejność w kursie.',

    'course_editor.sections.structure.button.add_module' => 'Dodaj moduł',
    'course_editor.sections.structure.button.add_lesson' => 'Dodaj lekcję',
    'course_editor.sections.structure.button.add_quiz' => 'Dodaj quiz',

    'course_editor.sections.structure.quiz.upgrade_needed' => 'Quizy dostępne są %s.',

    'course_editor.sections.structure.quiz.number_test_attempts' => 'Ilość podejść do testu',
    'course_editor.sections.structure.quiz.number_test_attempts.desc' => 'Określa, ile prób ma kursant na zdanie testu.',

    'course_editor.sections.structure.quiz.number_test_attempts.limit.wrong' => 'Niestety na wszystkie pytania odpowiedziałeś źle. <br><br><strong>Ilość prób podejścia do testu została wykorzystana.</strong>',
    'course_editor.sections.structure.quiz.number_test_attempts.limit.few_points' => 'Niestety liczba zdobytych przez ciebie punktów jest niewystarczająca by zaliczyć test. <br><br><strong>Ilość prób podejścia do testu została wykorzystana.</strong>',
    'course_editor.sections.structure.quiz.number_test_attempts.limit' => '<strong>Ilość prób podejścia do testu została wykorzystana.</strong>',
    'course_editor.sections.structure.quiz.number_test_attempts.attempts_left_info' => 'Ilość pozostałych podejść do testu: %d',

    'quiz_editor.page_title' => 'Edycja quizu',
    'quiz_editor.quiz_does_not_exist' => 'Quiz nie istnieje lub podane ID jest błędne.',
    'quiz_editor.sections.general' => 'Podstawowe',
    'quiz_editor.sections.structure' => 'Struktura',
    'quiz_editor.sections.files' => 'Załączniki',

    'quiz_editor.sections.general.fieldset.name_description' => 'Nazwa i opis',
    'quiz_editor.sections.general.fieldset.location' => 'Umiejscowienie',
    'quiz_editor.sections.general.fieldset.graphic' => 'Graficzne',
    'quiz_editor.sections.general.fieldset.quiz_settings' => 'Ustawienia quizu',


    'quiz_editor.sections.general.name' => 'Nazwa',
    'quiz_editor.sections.general.description' => 'Instrukcje dla quizu',
    'quiz_editor.sections.general.description.tooltip' => '',

    'quiz_editor.sections.general.subtitle' => 'Krótki opis quizu',
    'quiz_editor.sections.general.subtitle.tooltip' => 'Opcjonalny, dodatkowy opis wyświetlany pod tytułem quizu (na stronie quizu, modułu i w panelu).',

    'quiz_editor.sections.general.additional_info' => 'Dodatkowe informacje',
    'quiz_editor.sections.general.additional_info.tooltip' => '',

    'quiz_editor.sections.general.url' => 'Rozszerzenie dla adresu URL',

    'quiz_editor.sections.general.featured_image' => 'Miniaturka quizu',

    'quiz_editor.sections.general.level' => 'Poziom trudności',
    'quiz_editor.sections.general.level.tooltip' => 'Poziom trudności tego materiału.',

    'quiz_editor.sections.general.duration' => 'Czas trwania',
    'quiz_editor.sections.general.duration.tooltip' => 'Szacunkowy czas potrzebny na przerobienie tego materiału.',

    'quiz_editor.sections.general.time' => 'Czas na rozwiązanie quizu',
    'quiz_editor.sections.general.time.tooltip' => 'Czas (w minutach) na rozwiązanie quizu.',

    'quiz_editor.sections.general.time.validation' => 'Podana wartość nie może być pusta, mniejsza lub równa 0.',

    'quiz_editor.sections.general.number_test_attempts' => 'Ilość podejść do testu',
    'quiz_editor.sections.general.number_test_attempts.tooltip' => 'Określa, ile prób ma kursant na zdanie testu.',
    'quiz_editor.sections.general.number_test_attempts.validation' => 'Podana wartość nie może być pusta, mniejsza lub równa 0.',

    'quiz_editor.sections.general.evaluated_by_admin_mode' => 'Moderacja quizu',
    'quiz_editor.sections.general.evaluated_by_admin_mode.tooltip' => 'Aktywacja tej opcji spowoduje, że zaliczenie testu będzie zależne od jego oceny przez administratora.',

    'quiz_editor.sections.general.randomizing_and_limiting_questions' => 'Losowanie kolejności pytań',
    'quiz_editor.sections.general.randomize_question_order.tooltip' => 'Aktywacja tej opcji spowoduje, że pytania będą pojawiać się w losowej kolejności.',

    'quiz_editor.sections.general.randomize_answer_order' => 'Losuj odpowiedzi',
    'quiz_editor.sections.general.randomize_answer_order.tooltip' => 'Aktywacja tej opcji spowoduje, że odpowiedzi w pytaniach będą pojawiać się w losowej kolejności.',
    'quiz_editor.sections.general.answers_preview' => 'Podgląd odpowiedzi',
    'quiz_editor.sections.general.answers_preview.tooltip' => 'Aktywacja tej opcji umożliwi użytkownikowi sprawdzenie swoich odpowiedzi po zakończeniu quizu.',
    'quiz_editor.sections.general.also_show_correct_answers' => 'Pokaż również poprawne odpowiedzi oraz komentarze',
    'quiz_editor.sections.general.also_show_correct_answers.tooltip' => 'Aktywacja tej opcji spowoduje wyświetlenie użytkownikowi poprawnych odpowiedzi na pytania, na które błędnie odpowiedział. Dodatkowo jeśli w strukturze pytań zostaną dodane komentarze, również zostaną one wyświetlone.',

    'quiz_editor.sections.structure.success_message' => 'Struktura została poprawnie zapisana.',
    'quiz_editor.sections.structure.error_message' => 'Podczas zapisywania wystąpił błąd. Skontaktuj się z administratorem witryny.',
    'quiz_editor.sections.structure.validation.empty_structure' => 'Przed zapisem utwórz strukturę quizu.',
    'quiz_editor.sections.structure.validation.error_message' => 'Podane dane w strukturze są błędne.',
    'quiz_editor.sections.structure.info' => 'Poniżej możesz dodać strukturę dla swojego quizu. W zależności od potrzeb masz do wyboru różnego rodzaju pytania. Możesz także zdefiniować liczbę punktów, niezbędną do uzyskania zaliczenia testu.',

    'bundles_list.notice' => 'Zmień pakiet: Aby korzystać z funkcjonalności pakietów musisz zmienic swoją licence na PLUS lub PRO.',

    'bundles_list.page_title' => 'Pakiety',
    'bundles_list.column.id' => 'ID',
    'bundles_list.column.name' => 'Nazwa pakietu',
    'bundles_list.column.products' => 'Produkty',
    'bundles_list.column.show' => 'Pokaż',
    'bundles_list.column.sales' => 'Sprzedaż',

    'bundles_list.actions.create_bundle' => 'Dodaj pakiet',
    'bundles_list.actions.edit' => 'Edytuj',
    'bundles_list.actions.delete' => 'Usuń',
    'bundles_list.actions.delete.success' => 'Pakiet został pomyślnie usunięty!',
    'bundles_list.actions.delete.loading' => 'Usuwam...',
    'bundles_list.actions.delete.confirm' => 'Czy na pewno chcesz usunąć wybrany pakiet? Ta czynność jest nieodwracalna.',
    'bundles_list.actions.sales.bulk' => 'Włącz / wyłącz sprzedaż',
    'bundles_list.actions.sales.active' => 'Włącz sprzedaż',
    'bundles_list.actions.sales.inactive' => 'Wyłącz sprzedaż',
    'bundles_list.actions.sales.loading' => 'Zmieniam...',
    'bundles_list.actions.delete.error' => 'Podczas usuwania wystąpił błąd. Skontaktuj się z administratorem witryny.',

    'bundles_list.popup.close' => 'Zamknij',
    'bundles_list.popup.save' => 'Zapisz',
    'bundles_list.popup.purchase_links.title' => 'Linki zakupowe',
    'bundles_list.buttons.purchase_links.tooltip' => 'Linki zakupowe',

    'bundles_list.sales.status.enabled' => 'Włączona',
    'bundles_list.sales.status.disabled' => 'Wyłączona',

    'bundles_popup_editor.title' => 'Utwórz nowy pakiet',
    'bundles_popup_editor.popup.field.name' => 'Nazwa pakietu',
    'bundles_popup_editor.popup.field.name.placeholder' => 'Podaj nazwę pakietu',

    'bundles_popup_editor.popup.field.price' => 'Cena',
    'bundles_popup_editor.popup.field.price.placeholder' => 'Podaj cenę pakietu',
    'bundles_popup_editor.popup.field.price.tooltip' => 'Ile kosztuje dostęp do pakietu? Wpisz 0 jeśli chcesz udostępniać usługę bezpłatnie.',

    'gateways.manual_purchases' => 'Zakup ręczny',
    'bundle_editor.page_title' => 'Edycja pakietu',
    'bundle_editor.preview_button' => 'Zobacz pakiet',

    'bundle_editor.sections.general' => 'Podstawowe',
    'bundle_editor.sections.package_contents' => 'Zawartość pakietu',
    'bundle_editor.sections.invoices' => 'Faktury',
    'bundle_editor.sections.mailings' => 'Systemy mailingowe',

    'bundle_editor.sections.general.fieldset.name' => 'Nazwa i opis',

    'bundle_editor.sections.general.name' => 'Nazwa pakietu',

    'bundle_editor.sections.general.description' => 'Pełny opis oferty pakietu',
    'bundle_editor.sections.general.description.desc' => 'Skonfiguruj opis pakietu',
    'bundle_editor.sections.general.short_description' => 'Skrócony opis oferty pakietu',

    'bundle_editor.sections.general.categories' => 'Kategorie',
    'bundle_editor.sections.general.select_categories' => 'Wybierz kategorie',

    'bundle_editor.sections.general.fieldset.location' => 'Umiejscowienie',

    'bundle_editor.sections.general.url' => 'Rozszerzenie dla adresu URL',

    'bundle_editor.sections.general.tags' => 'Tagi',
    'bundle_editor.sections.general.add_tags' => 'Dodaj nowy tag',
    'bundle_editor.sections.general.add_tags.desc' => 'Rozdzielaj przecinkami lub enterami.',

    'bundle_editor.sections.general.fieldset.graphics' => 'Graficzne',

    'bundle_editor.sections.general.featured_image' => 'Miniaturka pakietu',
    'bundle_editor.sections.general.banner' => 'Baner pakietu',

    'bundle_editor.sections.general.fieldset.sale' => 'Sprzedaż',

    'bundle_editor.sections.general.sales_disabled' => 'Włącz sprzedaż',
    'bundle_editor.sections.general.sales_disabled.tooltip' => 'Zaznaczając tę opcję umożliwisz klientom zakup tego pakietu',

    'bundle_editor.sections.general.hide_from_list' => 'Pokaż pakiet w katalogu',

    'bundle_editor.sections.general.hide_purchase_button' => 'Pokaż przycisk zakupu',
    'bundle_editor.sections.general.hide_purchase_button.tooltip' => 'Ta opcja pokaże przycisk kupna na stronie kursu',

    'bundle_editor.sections.general.variable_prices' => 'Warianty cenowe',
    'bundle_editor.sections.general.variable_prices.edit_variants' => 'Edytuj warianty',

    'bundle_editor.sections.general.variable_pricing.notice' => 'Uwaga! Włączenie lub wyłączenie wariantów, spowoduje wyłączenie sprzedaży pakietu. W każdej chwili można będzie włączyć ją ponownie.',
    'bundle_editor.sections.general.sales_disabled.notice' => 'By móc włączyć sprzedaż, dodaj przynajmniej jeden wariant cenowy lub dezaktywuj moduł wariantów.',

    'bundle_editor.sections.general.fieldset.price' => 'Cena',

    'contact_form.captcha.spam_detected' => 'Przepraszamy, wykryto spam!',
    'bundle_editor.sections.general.variable_pricing' => 'Warianty cenowe',

    'bundle_editor.sections.general.price' => 'Cena',
    'bundle_editor.sections.general.price.tooltip' => 'Ile kosztuje dostęp do pakietu? Wpisz 0 jeśli chcesz udostępniać pakiet bezpłatnie.',

    'bundle_editor.sections.general.special_offer' => 'Promocja',

    'bundle_editor.sections.general.sale_price' => 'Cena promocyjna',
    'bundle_editor.sections.general.sale_price.tooltip' => 'Jaka jest cena promocyjna tego pakietu? Wpisz 0 jeśli chcesz wyłączyć cenę promocyjną.',

    'bundle_editor.sections.general.sale_price_date_from' => 'Rozpoczęcie promocji',
    'bundle_editor.sections.general.sale_price_date_from.tooltip' => 'Moment aktywowania i deaktywowania promocji mogą być w praktyce opóźnione maksymalnie 5 minut w stosunku do ustawionej godziny',

    'bundle_editor.sections.general.sale_price_date_to' => 'Zakończenie promocji',
    'bundle_editor.sections.general.sale_price_date_to.tooltip' => 'Moment aktywowania i deaktywowania promocji mogą być w praktyce opóźnione maksymalnie 5 minut w stosunku do ustawionej godziny',

    'bundle_editor.sections.general.recurring_payments_enabled' => 'Włącz płatności cykliczne',
    'bundle_editor.sections.general.recurring_payments' => 'Płatności cykliczne',
    'bundle_editor.sections.general.recurring_payments_interval' => 'Odstęp czasu',
    'bundle_editor.sections.general.recurring_payments_interval.desc' => 'Ustawienie odstępu czasu pomiędzy powtarzającymi się płatnościami dla tej pozycji.',

    'bundle_editor.sections.general.payments_unit.option.days' => 'Dni',
    'bundle_editor.sections.general.payments_unit.option.weeks' => 'Tygodnie',
    'bundle_editor.sections.general.payments_unit.option.months' => 'Miesiące',
    'bundle_editor.sections.general.payments_unit.option.years' => 'Lata',
    'bundle_editor.sections.mailings.fieldset.mailings' => 'Systemy mailingowe',

    'bundle_editor.sections.bundle_content.info' => 'Poniżej możesz zarządzać listą produktów i usług, które będą wchodzić w skład edytowanego pakietu. Dodaj dowolną ilość elementów i ustaw je wg pożądanej kolejności.',

    'bundle_editor.sections.bundle_content.column.priority' => 'Kolejność',
    'bundle_editor.sections.bundle_content.column.product_name' => 'Produkt',
    'bundle_editor.sections.bundle_content.column.select_product' => 'Wybierz produkt',
    'bundle_editor.sections.bundle_content.button.add_product' => 'Dodaj produkt',
    'bundle_editor.sections.bundle_content.button.save' => 'Zapisz',
    'bundle_editor.sections.bundle_content.message.saving' => 'Zapisywanie...',
    'bundle_editor.sections.bundle_content.button.cancel' => 'Anuluj',
    'bundle_editor.sections.bundle_content.message.you_have_unsaved_changes' => 'masz niezapisane zmiany!',
    'bundle_editor.sections.bundle_content.message.be_careful' => 'Uważaj',
    'bundle_editor.sections.bundle_content.message.selected_products' => 'Wybrane produkty',
    'bundle_editor.sections.bundle_content.message.no_selected_products' => 'Nie wybrano żadnych produktów',
    'bundle_editor.sections.bundle_content.message.save_success' => 'Ustawienia zostały zapisane!',
    'bundle_editor.sections.bundle_content.message.save_error' => 'Podczas zapisywania wystąpił błąd. Skontaktuj się z administratorem.',

    'price_history.menu_title' => 'Historia cen',
    'price_history.page_title' => 'Historia cen',
    'price_history.column.id' => 'ID',
    'price_history.column.product_name' => 'Nazwa produktu',
    'price_history.column.price' => 'Cena',
    'price_history.column.type_of_price' => 'Rodzaj ceny',
    'price_history.column.date_of_change' => 'Data zmiany',
    'price_history.column.price.promotion_disabled' => 'Wyłączona',

    'price_history.type_of_price.regular' => 'Regularna',
    'price_history.type_of_price.promo' => 'Promocyjna',

    'app_view.go_to' => 'Idź do',
    'app_view.course_panel' => 'Panel kursu',

    'lesson_editor.short_description' => 'Krótki opis',

    'newsletter.sign_up' => 'Zapisz się na newsletter',

    'tpay.checkout_button.text' => 'Kup i zapłać kartą',

    'physical_products.physical_products' => 'Produkty fizyczne',
    'physical_products.page_title' => 'Produkty fizyczne',

    'physical_products.column.id' => 'ID',
    'physical_products.column.name' => 'Nazwa produktu',
    'physical_products.column.show' => 'Pokaż',
    'physical_products.column.sales' => 'Sprzedaż',

    'physical_products.sales.status.enabled' => 'Włączona',
    'physical_products.sales.status.disabled' => 'Wyłączona',

    'physical_products.actions.create_product' => 'Dodaj produkt fizyczny',
    'physical_products.actions.edit' => 'Edytuj',
    'physical_products.actions.delete' => 'Usuń',
    'physical_products.actions.delete.success' => 'Produkt został pomyślnie usunięty!',
    'physical_products.actions.delete.info' => 'Nie możesz usunąć tego produktu, ponieważ jest on przypisany do przynajmniej jednego pakietu. Usuń go ze wszystkich pakietów, a potem ponów próbę usunięcia.',
    'physical_products.actions.delete.loading' => 'Usuwam...',
    'physical_products.actions.delete.confirm' => 'Czy na pewno chcesz usunąć wybrany produkt? Ta czynność jest nieodwracalna.',
    'physical_products.actions.sales.bulk' => 'Włącz / wyłącz sprzedaż',
    'physical_products.actions.sales.active' => 'Włącz sprzedaż',
    'physical_products.actions.sales.inactive' => 'Wyłącz sprzedaż',
    'physical_products.actions.sales.loading' => 'Zmieniam...',
    'physical_products.actions.delete.error' => 'Podczas usuwania wystąpił błąd. Skontaktuj się z administratorem witryny.',

    'physical_products.popup.close' => 'Zamknij',
    'physical_products.popup.purchase_links.title' => 'Linki zakupowe',
    'physical_products.buttons.purchase_links.tooltip' => 'Linki zakupowe',
    'physical_products.buttons.product_panel.tooltip' => 'Zobacz produkt',

    'physical_products_popup_editor.popup.field.name' => 'Nazwa produktu',
    'physical_products_popup_editor.popup.field.name.placeholder' => 'Podaj nazwę produktu',

    'physical_products_popup_editor.popup.field.price' => 'Cena',
    'physical_products_popup_editor.popup.field.price.placeholder' => 'Podaj cenę produktu',
    'physical_products_popup_editor.popup.field.price.tooltip' => 'Ile kosztuje produkt? Wpisz 0 jeśli chcesz żeby produkt był darmowy.',

    'physical_product_editor.add_service' => 'Dodaj produkt',
    'physical_product_editor.page_title' => 'Edycja produktu',
    'physical_product_editor.preview_button' => 'Zobacz produkt',

    'physical_product_editor.sections.general' => 'Podstawowe',

    'physical_product_editor.sections.general.fieldset.name' => 'Nazwa i opis',

    'physical_product_editor.sections.general.name' => 'Nazwa produktu',

    'physical_product_editor.sections.general.description' => 'Opis',
    'physical_product_editor.sections.general.description.desc' => 'Skonfiguruj opis produktu, kategorie, tagi, rozszerzenie adresu URL oraz obrazek wyróżniający.',

    'physical_product_editor.sections.general.short_description' => 'Krótki opis',

    'physical_product_editor.sections.general.categories' => 'Kategorie',
    'physical_product_editor.sections.general.select_categories' => 'Wybierz kategorie',

    'physical_product_editor.sections.general.fieldset.location' => 'Umiejscowienie',

    'physical_product_editor.sections.general.url' => 'Rozszerzenie dla adresu URL',

    'physical_product_editor.sections.general.tags' => 'Tagi',
    'physical_product_editor.sections.general.add_tags' => 'Dodaj nowy tag',
    'physical_product_editor.sections.general.add_tags.desc' => 'Rozdzielaj przecinkami lub enterami.',

    'physical_product_editor.sections.general.fieldset.price' => 'Cena',

    'physical_product_editor.sections.general.delivery_price_info' => 'Uwaga! Obecnie nie są doliczane koszty transportu. By to zmienić, skonfiguruj je w Ustawieniach Publigo w zakładce Podstawowe.',
    'physical_product_editor.sections.general.price' => 'Cena',
    'physical_product_editor.sections.general.price.tooltip' => 'Ile kosztuje produkt? Wpisz 0 jeśli chcesz udostępniać go bezpłatnie.',

    'physical_product_editor.sections.general.special_offer' => 'Promocja',

    'physical_product_editor.sections.general.sale_price' => 'Cena promocyjna',
    'physical_product_editor.sections.general.sale_price.tooltip' => 'Jaka jest cena promocyjna tego produktu? Wpisz 0 jeśli chcesz wyłączyć cenę promocyjną.',

    'physical_product_editor.sections.general.sale_price_date_from' => 'Rozpoczęcie promocji',
    'physical_product_editor.sections.general.sale_price_date_from.tooltip' => 'Moment aktywowania i deaktywowania promocji mogą być w praktyce opóźnione maksymalnie 5 minut w stosunku do ustawionej godziny',

    'physical_product_editor.sections.general.sale_price_date_to' => 'Zakończenie promocji',
    'physical_product_editor.sections.general.sale_price_date_to.tooltip' => 'Moment aktywowania i deaktywowania promocji mogą być w praktyce opóźnione maksymalnie 5 minut w stosunku do ustawionej godziny',


    'physical_product_editor.sections.general.fieldset.quantities_available' => 'Dostępne ilości',
    'physical_product_editor.sections.general.purchase_limit' => 'Łączna liczba szt. do zakupu',
    'physical_product_editor.sections.general.purchase_limit.desc' => 'Wpisz 0 by wyłączyć limit',

    'physical_product_editor.sections.general.purchase_limit_items_left' => 'Pozostało szt.',

    'physical_product_editor.sections.general.fieldset.graphics' => 'Graficzne',

    'physical_product_editor.sections.general.banner' => 'Baner',
    'physical_product_editor.sections.general.featured_image' => 'Zdjęcie produktu',

    'physical_product_editor.sections.general.fieldset.sale' => 'Sprzedaż',

    'physical_product_editor.sections.general.sales_disabled' => 'Włącz sprzedaż',
    'physical_product_editor.sections.general.sales_disabled.tooltip' => 'Zaznaczając tę opcję umożliwisz klientom zakup tego produktu',

    'physical_product_editor.sections.general.hide_from_list' => 'Pokaż produkt w katalogu',

    'physical_product_editor.sections.general.hide_purchase_button' => 'Pokaż przycisk zakupu',
    'physical_product_editor.sections.general.hide_purchase_button.tooltip' => 'Ta opcja pokaże przycisk kupna na stronie produktu',

    'physical_product_editor.sections.general.promote_course' => 'Promuj produkt na stronie głównej',
    'physical_product_editor.sections.general.recurring_payments_enabled' => 'Włącz płatności cykliczne',
    'physical_product_editor.sections.general.recurring_payments' => 'Płatności cykliczne',
    'physical_product_editor.sections.general.recurring_payments_interval' => 'Odstęp czasu',
    'physical_product_editor.sections.general.recurring_payments_interval.desc' => 'Ustawienie odstępu czasu pomiędzy powtarzającymi się płatnościami dla tej pozycji.',

    'physical_product_editor.sections.general.payments_unit.option.days' => 'Dni',
    'physical_product_editor.sections.general.payments_unit.option.weeks' => 'Tygodnie',
    'physical_product_editor.sections.general.payments_unit.option.months' => 'Miesiące',
    'physical_product_editor.sections.general.payments_unit.option.years' => 'Lata',

    'physical_product_editor.sections.link_generator.message_1' => 'Za pomocą generatora linków, możesz przygotować link, który nie tylko od razu dodaje dany produkt do koszyka, ale również aplikuje kod zniżkowy, czy aktywuje opcję zakupu na prezent. Link taki możesz umieścić w dowolnym miejscu np. na stronie sprzedażowej pod przyciskiem Kup Teraz.',

    'physical_product_editor.sections.invoices.fieldset.general' => 'Ustawienia księgowe',

    'physical_product_editor.sections.invoices.no_gtu' => 'Brak kodu GTU',
    'physical_product_editor.sections.invoices.gtu' => 'Kod GTU',
    'physical_product_editor.sections.invoices.gtu.not_supported_for' => 'Uwaga! Kody GTU nie są obsługiwane przez API:',
    'physical_product_editor.sections.invoices.flat_rate_tax_symbol.not_supported_for' => 'Uwaga! Podatek zryczałtowany nie jest obsługiwany przez system:',

    'physical_product_editor.sections.invoices.flat_rate_tax_symbol' => 'Stawka ryczałtu',
    'physical_product_editor.sections.invoices.no_tax_symbol' => 'Brak stawki ryczałtu',

    'physical_product_editor.sections.invoices.vat_rate' => 'Stawka VAT',

    'physical_product_editor.sections.link_generator.fieldset.general' => 'Generator linków',
    'physical_product_editor.sections.link_generator.link_generator' => 'Generator linków',

    'physical_product_editor.sections.link_generator.variable_prices.price' => 'Cena',
    'physical_product_editor.sections.link_generator.variable_prices.copy' => 'Kopiuj',
    'physical_product_editor.sections.link_generator.variable_prices.copied' => 'Skopiowano',

    'physical_product_editor.sections.link_generator' => 'Generator linków',
    'physical_product_editor.sections.invoices' => 'Faktury',
    'physical_product_editor.sections.mailings' => 'Systemy mailingowe',
    'physical_product_editor.sections.discount_code' => 'Kod zniżkowy',

    'physical_product_editor.sections.mailings.fieldset.mailings' => 'Systemy mailingowe',

    'physical_product_editor.sections.mailings.empty_lists' => 'Nieprawidłowa konfiguracja lub brak list.',

    'physical_product_editor.sections.mailings.mailchimp' => 'MailChimp',
    'physical_product_editor.sections.mailings.popup.mailchimp' => 'Wybierz listy',
    'physical_product_editor.sections.mailings.popup.mailchimp.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'physical_product_editor.sections.mailings.mailerlite' => 'MailerLite',
    'physical_product_editor.sections.mailings.popup.mailerlite' => 'Wybierz listy',
    'physical_product_editor.sections.mailings.popup.mailerlite.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'physical_product_editor.sections.mailings.freshmail' => 'FreshMail',
    'physical_product_editor.sections.mailings.popup.freshmail' => 'Wybierz listy',
    'physical_product_editor.sections.mailings.popup.freshmail.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'physical_product_editor.sections.mailings.ipresso' => 'iPresso',
    'physical_product_editor.sections.mailings.popup.ipresso_tags' => 'Dodaj tagi ',
    'physical_product_editor.sections.mailings.popup.ipresso_tags.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>dodane</strong> do kontaktów w iPresso po zakończeniu zakupu.',

    'physical_product_editor.sections.mailings.popup.ipresso_tags_unsubscribe' => 'Dodaj tagi ',
    'physical_product_editor.sections.mailings.popup.ipresso_tags_unsubscribe.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>usunięte</strong> z kontaktów w iPresso po zakończeniu zakupu.',

    'physical_product_editor.sections.mailings.activecampaign' => 'ActiveCampaign',
    'physical_product_editor.sections.mailings.popup.activecampaign' => 'Wybierz listy',
    'physical_product_editor.sections.mailings.popup.activecampaign.desc' => 'Wybierz listy, na które kupujący ma zostać <strong>zapisany</strong>, gdy opłaci dostęp do usługi.',

    'physical_product_editor.sections.mailings.popup.activecampaign_unsubscribe' => 'Wybierz listy',
    'physical_product_editor.sections.mailings.popup.activecampaign_unsubscribe.desc' => 'Wybierz listy, z których kupujący ma zostać <strong>wypisany</strong>, gdy opłaci dostęp do usługi.',

    'physical_product_editor.sections.mailings.popup.activecampaign_tags' => 'Dodaj tagi',
    'physical_product_editor.sections.mailings.popup.activecampaign_tags.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>dodane</strong> do kontaktów w ActiveCampaign po zakończeniu zakupu.',

    'physical_product_editor.sections.mailings.popup.activecampaign_tags_unsubscribe' => 'Dodaj tagi',
    'physical_product_editor.sections.mailings.popup.activecampaign_tags_unsubscribe.desc' => 'Dodaj tagi (oddzielone przecinkami), które zostaną <strong>usunięte</strong> z kontaktów w ActiveCampaign po zakończeniu zakupu.',

    'physical_product_editor.sections.mailings.getresponse' => 'GetResponse',
    'physical_product_editor.sections.mailings.popup.getresponse' => 'Wybierz listy',
    'physical_product_editor.sections.mailings.popup.getresponse.desc' => 'Wybierz listy, na które kupujący ma zostać <strong>zapisany</strong>, gdy opłaci dostęp do usługi.',

    'physical_product_editor.sections.mailings.popup.getresponse_unsubscribe' => 'Wybierz listy',
    'physical_product_editor.sections.mailings.popup.getresponse_unsubscribe.desc' => 'Wybierz listy, z których kupujący ma zostać <strong>wypisany</strong>, gdy opłaci dostęp do usługi.',

    'physical_product_editor.sections.mailings.popup.getresponse_tags' => 'Wybierz tagi ',
    'physical_product_editor.sections.mailings.popup.getresponse_tags.desc' => 'Wybierz tagi, do których kupujący mają być dodawani podczas zakupów.',

    'physical_product_editor.sections.mailings.salesmanago' => 'SalesManago',
    'physical_product_editor.sections.mailings.popup.salesmanago_tags' => 'Dodaj tagi',
    'physical_product_editor.sections.mailings.popup.salesmanago_tags.desc' => 'Wpisz tagi (oddzielając je przecinkiem), które mają być dodane do kontaktu w panelu SALESmanago po zakupie tego produktu.',

    'physical_product_editor.sections.mailings.interspire' => 'Interspire',
    'physical_product_editor.sections.mailings.popup.interspire' => 'Wybierz listy',
    'physical_product_editor.sections.mailings.popup.interspire.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'physical_product_editor.sections.mailings.convertkit' => 'ConvertKit',
    'physical_product_editor.sections.mailings.popup.convertkit' => 'Wybierz listy',
    'physical_product_editor.sections.mailings.popup.convertkit.desc' => 'Wybierz listy, na które kupujący ma zostać zapisany, gdy opłaci dostęp do usługi',

    'physical_product_editor.sections.mailings.popup.convertkit_tags' => 'Wybierz tagi',
    'physical_product_editor.sections.mailings.popup.convertkit_tags.desc' => 'Wybierz tagi, do których kupujący mają być <strong>dodawani</strong> podczas zakupów.',

    'physical_product_editor.sections.mailings.popup.convertkit_tags_unsubscribe' => 'Wybierz tagi ',
    'physical_product_editor.sections.mailings.popup.convertkit_tags_unsubscribe.desc' => 'Wybierz tagi, z których kupujący mają zostać <strong>usunięci</strong> podczas zakupów.',

    'physical_product_editor.sections.mailings.select_list' => 'Wybierz listę lub grupę',
    'physical_product_editor.sections.mailings.add_next' => 'Dodaj następną',

    'physical_product_editor.sections.discount_code.message' => 'Razem z tym produktem możesz sprzedać kod rabatowy który zostanie wygenerowany na podstawie już wcześniej stworzonego. Możesz ustalić jego termin ważności.',
    'physical_product_editor.sections.discount_code.fieldset.discount_code' => 'Kody zniżkowe',

    'physical_product_editor.sections.discount_code.code_pattern' => 'Wybierz kod wzorcowy',
    'physical_product_editor.sections.discount_code.code_pattern.desc' => 'Na jego podstawie wygenerujemy nowy kod po opłaceniu zamówienia.',

    'physical_product_editor.sections.discount_code.code_time' => 'Okres ważności',
    'physical_product_editor.sections.discount_code.code_time.desc' => 'Ten parametr jest opcjonalny. Domyślnie kod rabatowy nigdy nie wygasa.',
    'physical_product_editor.sections.discount_code.code_time.validation' => 'Podana wartość nie może być mniejsza niż 0.',
    'physical_product_editor.sections.discount_code.code_time.validation.must_be_a_number' => 'Podana wartość musi być liczbą.',
    'physical_product_editor.sections.discount_code.code_time.validation.must_not_be_empty' => 'Musisz wybrać jedną z opcji w polu powyżej.',

    'physical_product_editor.sections.discount_code.code_pattern.no_code.label' => 'Brak kuponów do wykorzystania',

    'physical_product_editor.sections.discount_code.code_type.option.duration' => 'Okres trwania',
    'physical_product_editor.sections.discount_code.code_type.option.days' => 'Dni',
    'physical_product_editor.sections.discount_code.code_type.option.weeks' => 'Tygodnie',
    'physical_product_editor.sections.discount_code.code_type.option.months' => 'Miesiące',

    'physical_product_editor.cart.delivery_address.title' => 'Dane dostawy',
    'physical_product_editor.cart.delivery_address.first_name' => 'Imię',
    'physical_product_editor.cart.delivery_address.last_name' => 'Nazwisko',
    'physical_product_editor.cart.delivery_address.company' => 'Nazwa firmy / Organizacji',
    'physical_product_editor.cart.delivery_address.phone' => 'Numer telefonu',
    'physical_product_editor.cart.delivery_address.street' => 'Ulica',
    'physical_product_editor.cart.delivery_address.building_number' => 'Numer budynku',
    'physical_product_editor.cart.delivery_address.apartment_number' => 'Numer lokalu',
    'physical_product_editor.cart.delivery_address.postal_code' => 'Kod pocztowy',
    'physical_product_editor.cart.delivery_address.city' => 'Miejscowość',

    'physical_product_editor.cart.delivery_address.validate.phone' => 'Wpisz prawidłowy numer telefonu dostawy',
    'physical_product_editor.cart.delivery_address.validate.street' => 'Wpisz prawidłową ulicę dostawy',
    'physical_product_editor.cart.delivery_address.validate.building_number' => 'Wpisz prawidłowy numer budynku dostawy',
    'physical_product_editor.cart.delivery_address.validate.apartment_number' => 'Wpisz prawidłowy numer lokalu dostawy',
    'physical_product_editor.cart.delivery_address.validate.postal_code' => 'Wpisz prawidłowy kod pocztowy dostawy',
    'physical_product_editor.cart.delivery_address.validate.city' => 'Wpisz prawidłową miejscowość dostawy',
    'physical_product_editor.cart.delivery_address.validate.first_name' => 'Wpisz prawidłowe imię odbiorcy dostawy',
    'physical_product_editor.cart.delivery_address.validate.last_name' => 'Wpisz prawidłowe nazwisko odbiorcy dostawy',

    'receipt.fees' => 'Opłaty',

    'packages.info.you_need_to_upgrade_your_plan' => 'Dostępne %s.',
    'packages.info.you_need_to_upgrade_your_plan_to' => 'w pakiecie %s',
    'packages.info.you_need_to_upgrade_your_plan_to.short' => '%s',
    'packages.info.you_need_to_upgrade_your_plan_to.one_of' => 'w pakietach %s i %s',
    'packages.info.you_need_to_upgrade_your_plan_to.one_of.short' => '%s lub %s',

	'airbrake.key_and_id_set' => 'Ustawiono ID Projektu Airbrake na "%s" i klucz projektu na "%s".',
	'airbrake.key_and_id_unset' => 'Usunięto dane konfiguracji Airbrake.',
    'airbrake.invalid_id' => 'Podano błędny format ID',
    'airbrake.invalid_key' => 'Podano błędny format klucza',

    'product.available_quantities.format_x_of_y' => 'Dostępne: %s z %s',
    'product.available_quantities.format_x' => 'Dostępnych sztuk: %s',

    'product.item.category' => 'Kategoria:',
    'product.item.categories' => 'Kategorie:',
    'product.item.tag' => 'Tag:',
    'product.item.tags' => 'Tagi:',

    'categories.menu_title' => 'Kategorie prod.',
    'tags.menu_title' => 'Tagi produktowe',

    'quiz_editor.time_for_quiz' => 'Czas na quiz',
    'quiz_editor.time_for_quiz.description' => 'Czas (w minutach) na rozwiązanie quizu.',
    'quiz_editor.randomize_question_order' => 'Losowanie kolejności pytań',
    'quiz_editor.randomize_question_order.description' => 'Aktywacja tej opcji spowoduje, że pytania będą pojawiać się w losowej kolejności.',
    'quiz_editor.randomize_answer_order' => 'Losowanie kolejności odpowiedzi',
    'quiz_editor.randomize_answer_order.description' => 'Aktywacja tej opcji spowoduje, że odpowiedzi w pytaniach będą pojawiać się w losowej kolejności.',

    'quiz_editor.sections.files.table.column.priority' => 'Kolejność',
    'quiz_editor.sections.files.table.column.file_name' => 'Nazwa załącznika',
    'quiz_editor.sections.files.table.column.file_url' => 'Adres URL załącznika',
    'quiz_editor.sections.files.table.button.browse_media' => 'Wybierz załącznik',
    'quiz_editor.sections.files.table.button.add_file' => 'Dodaj załącznik',
    'quiz_editor.sections.files.table.button.save' => 'Zapisz',
    'quiz_editor.sections.files.table.message.saving' => 'Zapisywanie...',
    'quiz_editor.sections.files.table.button.cancel' => 'Anuluj',
    'quiz_editor.sections.files.table.message.you_have_unsaved_changes' => 'masz niezapisane zmiany!',
    'quiz_editor.sections.files.table.message.be_careful' => 'Uważaj',
    'quiz_editor.sections.files.table.message.active_files' => 'Załadowane załączniki',
    'quiz_editor.sections.files.table.message.no_active_files' => 'Nie załadowano żadnych załączników',
    'quiz_editor.sections.files.table.message.save_success' => 'Ustawienia zostały zapisane!',
    'quiz_editor.sections.files.table.message.save_error' => 'Podczas zapisywania wystąpił błąd. Skontaktuj się z administratorem.',

    'quiz_editor.sections.files.info' => 'W tym miejscu możesz dodać opcjonalne załączniki (schematy, zdjęcia, pliki audio etc.), do których będzie można odwołać się w konkretnych pytaniach w quizie.',

    'quiz_editor.preview_button' => 'Zobacz quiz',
    'quiz_editor.answers_preview' => 'Podgląd odpowiedzi',
    'quiz_editor.answers_preview.desc' => 'Aktywacja tej opcji umożliwi użytkownikowi sprawdzenie swoich odpowiedzi po zakończeniu quizu.',
    'quiz_editor.also_show_correct_answers' => 'Pokaż również poprawne odpowiedzi',
    'quiz_editor.also_show_correct_answers.desc' => 'Aktywacja tej opcji spowoduje wyświetlenie użytkownikowi poprawnych odpowiedzi na pytania, na które błędnie odpowiedział. ',

    'quiz_editor.structure.show_question_comment_field' => 'Dodaj komentarz do odpowiedzi',
    'quiz_editor.structure.show_question_comment_field.not_empty' => 'Pokaż dodany komentarz',

    'quiz.answers_preview.more_correct_answers_info' => 'To pytanie posiada więcej poprawnych odpowiedzi.',
    'quiz.answers_preview.see_answers' => 'Zobacz odpowiedzi',
    'quiz.answers_preview.file.no_file' => 'Nie przesłano żadnego pliku.',
    'quiz.answers_preview.file' => 'Przesłano:',
    'quiz.answers_preview.empty_answer' => 'Nie udzielono odpowiedzi na to pytanie.',
    'quiz.answers_preview.empty_answer_to_open_question' => 'Nie udzielono żadnej odpowiedzi na pytanie otwarte.',
    'quiz.answers_preview.correct_answer' => 'Poprawna',
    'quiz.answers_preview.incorrect_answer' => 'Niepoprawna',
    'quiz.answers_preview.assessed_by_moderator' => 'Odpowiedź oceniana indywidualnie przez moderatora.',
    'quiz.answers_preview.question_comment' => 'Komentarz do odpowiedzi: ',

    'quiz.end_view.try_again' => 'Spróbuj ponownie',
    'quiz.end_view.time_is_up' => 'Czas przeznaczony na rozwiązanie quizu upłynął.',
];