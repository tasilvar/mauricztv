let affiliateSetCookie = function (name, value, options = {}) {
    options = {
        path: '/',
        ...options
    };
    if (options.expires instanceof Date) {
        options.expires = options.expires.toUTCString();
    }

    let updatedCookie = encodeURIComponent(name) + "=" + encodeURIComponent(value);

    for (let optionKey in options) {
        updatedCookie += "; " + optionKey;
        let optionValue = options[optionKey];
        if (optionValue !== true) {
            updatedCookie += "=" + optionValue;
        }
    }

    document.cookie = updatedCookie;
}

Date.prototype.addDays = function (days) {
    const date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
};

let cookie_name = 'publigo_ap_cookie';
let cookie_campaign_name = 'publigo_ap_campaign_cookie';

let params = new Proxy(new URLSearchParams(window.location.search), {
    get: (searchParams, prop) => searchParams.get(prop),
});

let affiliate_id = params['afp'];
let campaign_name = params['afp-campaign'];

if (affiliate_id) {
    let encoded_id = btoa(affiliate_id);
    let expiration_date = new Date().addDays(30);
    affiliateSetCookie(cookie_name, encoded_id, {'expires': expiration_date})

    campaign_name = campaign_name ?? '';

    let encoded_campaign_name = btoa(campaign_name);
    affiliateSetCookie(cookie_campaign_name, encoded_campaign_name, {'expires': expiration_date})
}