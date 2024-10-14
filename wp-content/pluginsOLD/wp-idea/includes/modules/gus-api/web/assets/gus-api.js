jQuery(document).ready(function ($) {

    const GusApi = {

        init: function () {
            GusApi.listeners.invoiceTypeChecker();
            GusApi.listeners.nipValidator();
            GusApi.listeners.getDataByNip();
        },

        listeners: {
            invoiceTypeChecker: function () {
                $(document).on('change', 'select[name=bpmj_edd_invoice_data_invoice_type]', function () {
                    $('#message_invalid_nip_format').hide();
                });
            },
            nipValidator: function () {
                $(document).on('keyup change', '#bpmj_edd_invoice_data_invoice_nip', function () {
                    let removeDashAndLetters = $(this).val().replace(/[^0-9a-zA-z]/g, "");
                    $(this).val(removeDashAndLetters);
                });
            },
            getDataByNip: function () {
                $(document).on('click', '#billing-nip-check', function (e) {
                    e.preventDefault();

                    const nip = $('#bpmj_edd_invoice_data_invoice_nip').val();
                    const buttonBillingNipCheck = $("#billing-nip-check");
                    const invalidNipFormat = $('#message_invalid_nip_format');

                    if(!GusApi.functions.validateNip(nip)){
                        invalidNipFormat.html(BPMJ_WPI_GUS_API_I18N.wrong_tax_id);
                        invalidNipFormat.show();
                        return;
                    }

                    $.ajax({
                        type: "POST",
                        data: {
                            nip: nip,
                            subscription_type: BPMJ_WPI_GUS_API_I18N.subscription_type,
                            subscription_key: BPMJ_WPI_GUS_API_I18N.subscription_key,
                            host: BPMJ_WPI_GUS_API_I18N.host
                        },
                        url: BPMJ_WPI_GUS_API_I18N.search_data_endpoint,
                        beforeSend: function () {
                            buttonBillingNipCheck.prop("disabled", true);
                            buttonBillingNipCheck.html(BPMJ_WPI_GUS_API_I18N.downloading);
                        },
                        complete: function () {
                            buttonBillingNipCheck.prop("disabled", false);
                            $("#billing-nip-check").html(BPMJ_WPI_GUS_API_I18N.download_from_gus);
                        },
                        success: function (response) {
                            invalidNipFormat.hide();

                            let dataInArray = GusApi.functions.parseXmlToArray(response);

                            if (dataInArray['error_code']) {
                                let errorMessage = (dataInArray['error_code'] === '8') ? BPMJ_WPI_GUS_API_I18N.wrong_tax_id : BPMJ_WPI_GUS_API_I18N.entity_not_found;
                                invalidNipFormat.html(errorMessage);
                                invalidNipFormat.show();
                                return;
                            }

                            GusApi.functions.setDataForInvoiceFields(dataInArray);

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            if(jqXHR.status !== 200){
                                invalidNipFormat.html(BPMJ_WPI_GUS_API_I18N.error_processing_request);
                                invalidNipFormat.show();
                            }
                        }
                    });
                });
            }
        }, functions: {
            validateNip: function (nip) {
                let thisRegex = new RegExp('[0-9a-zA-z]');

                if(!thisRegex.test(nip)){
                   return false
                }

                return true
            },
            setDataForInvoiceFields: function (dataInArray) {
                $('#bpmj_edd_invoice_data_invoice_company_name').val(dataInArray['company_name']);
                $('#bpmj_edd_invoice_data_invoice_street').val(dataInArray['street']);
                $('#bpmj_edd_invoice_data_invoice_building_number').val(dataInArray['building_number']);
                $('#bpmj_edd_invoice_data_invoice_apartment_number').val(dataInArray['apartment_number']);
                $('#bpmj_edd_invoice_data_invoice_postcode').val(dataInArray['postal_code']);
                $('#bpmj_edd_invoice_data_invoice_city').val(dataInArray['city']);
            },
            parseXmlToArray: function (xml) {

                let parsedXml = $.parseXML(xml),
                    $xml = $(parsedXml);

                return {
                    'error_code': $xml.find('ErrorCode').text(),
                    'company_name': $xml.find('Nazwa').text(),
                    'street': $xml.find('Ulica').text(),
                    'building_number': $xml.find('NrNieruchomosci').text(),
                    'apartment_number': $xml.find('NrLokalu').text(),
                    'postal_code': $xml.find('KodPocztowy').text(),
                    'city': $xml.find('Miejscowosc').text()
                };
            }
        }

    };

    GusApi.init();
});