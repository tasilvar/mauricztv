(function ($) {
    $.fn._isInViewport = function () {
        var elementTop = $(this).offset().top,
            elementBottom = elementTop + $(this).outerHeight(),
            viewportTop = $(window).scrollTop(),
            viewportBottom = viewportTop + $(window).height();

        return elementBottom > viewportTop && elementTop < viewportBottom;
    };

    $(document).ready(function () {

        const DATA_LAYERS_PREFIX = 'gtm';
        const DATA_METHOD = DATA_LAYERS_PREFIX + '-method';

        const ON_CLICK_METHOD = 'click';
        const ON_VIEWPORT_METHOD = 'viewport';

        const UA_ADD_TO_CART_EVENT_NAME = 'addToCart';

        const GA4_EVENTS_NAME = {
            'addToCart': 'add_to_cart',
            'productClick': 'select_item',
            'impressions': 'view_item_list'
        };

        let completedPushLayers = [];

        function init() {
            findAllLayers();
        }

        function sendPush(data) {
            window.gtag('event', data.event, data.ecommerce);
        }

        function prepareData($layer) {

            let product_count = getData($layer, 'product-count');
            let data = {};
            let productPrice = 0;
            let uaEventName = getData($layer, 'event');

            let step, products = [];
            for (step = 0; step < product_count; step++) {

                let product = {};

                if (getData($layer, 'product-has-variants', step) && uaEventName === UA_ADD_TO_CART_EVENT_NAME) {
                    let $option = $layer.parents('form').find('option[data-gtm-event="viewProductVariant"]'),
                        is_select = $option.length;

                    let $element = $layer.parents('form').find('input[data-gtm-event="viewProductVariant"]:checked');

                    if (is_select) {
                        let $select = $option.parent();
                        $element = $select.find('option:selected');
                    }

                    product = prepareProductByLayer($element, step);
                    productPrice = getData($element, 'product-price', step);

                } else {
                    product = prepareProductByLayer($layer, step);
                    productPrice = getData($layer, 'product-price', step);
                }

                products.push(product)
            }

            data.ecommerce = {};

            if (uaEventName === UA_ADD_TO_CART_EVENT_NAME) {
                data.ecommerce.currency = getData($layer, 'currency-code');
                data.ecommerce.value = productPrice;
            }

            data.event = GA4_EVENTS_NAME[uaEventName];
            data.ecommerce.items = products;

            return data;
        }

        function prepareProductByLayer($layer, step) {
            let product = {};
            product.item_id = getData($layer, 'product-id', step);
            product.item_name = getData($layer, 'product-name', step);
            product.price = getData($layer, 'product-price', step);
            product.quantity = getData($layer, 'product-quantity', step)
            let position = getData($layer, 'product-position', step);
            if (position) {
                product.index = position;
            }

            return product;
        }

        function getData($layer, name, i = null) {
            if (i == null) {
                return $layer.data(DATA_LAYERS_PREFIX + '-' + name) ?? ''
            }

            return $layer.data(DATA_LAYERS_PREFIX + '-' + name + '[' + i + ']') ?? ''
        }

        function prepareLayerAndPush($layer) {
            if (isLayerCompletedPush($layer)) {
                return;
            }
            setLayerCompletedPush($layer)

            sendPush(prepareData($layer))
        }

        function setLayerCompletedPush($layer) {
            completedPushLayers.push($layer)
        }

        function isLayerCompletedPush($layer) {
            return $.inArray($layer, completedPushLayers) >= 0;
        }

        function findAllLayers() {
            $('[data-' + DATA_LAYERS_PREFIX + ']').each(function () {
                initObject($(this));
            });
        }

        function initObject($layer) {
            if (getAction($layer) === ON_CLICK_METHOD) {
                onClick($layer)
            }

            if (getAction($layer) === ON_VIEWPORT_METHOD) {
                onViewport($layer)
            }
        }

        function getAction($layer) {
            return $layer.data(DATA_METHOD)
        }

        function onClick($layer) {
            $layer.click(function (e) {
                prepareLayerAndPush($layer)
            })
        }

        function onViewport($layer) {
            if ($layer._isInViewport()) {
                prepareLayerAndPush($layer)
            }
            $(window).scroll($layer, function () {
                if ($layer._isInViewport()) {
                    prepareLayerAndPush($layer)
                }
            });
        }

        init()
    });
})
(jQuery);