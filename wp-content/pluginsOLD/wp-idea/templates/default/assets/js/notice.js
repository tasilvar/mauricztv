var closeNotice = () => {
    jQuery('.notice-on-login').hide()
    jQuery.post(wpidea.ajaxurl + '?wpi_route=notices/close_notice', {[wpidea.nonce_name]: wpidea.nonce_value}, () => {})
}
