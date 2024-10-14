jQuery(document).ready(function ($) {
    let overlay = document.createElement("div");
    overlay.innerHTML = '<div class="contenter_overlay">' +
        '<div class="redirect_notice"><div class="img_box">' +
        '<img src="' + settings_purchase_redirects.site_logo_url + '"></div>' +
        '' + settings_purchase_redirects.redirect_notice + '' +
        '</div></div>';

    $('body').prepend(overlay);
});

setTimeout(() => {
    window.location.replace(settings_purchase_redirects.redirection_url)
}, settings_purchase_redirects.delay_in_milliseconds);