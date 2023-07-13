(function ($) {
    $.fn.isInViewport = function() {
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
        const ON_DISPLAY_METHOD = 'display';
        const ON_VIEWPORT_METHOD = 'viewport';

        var completed_push_layers = [];


        function init(){
            find_all_layers();
        }

        function send_push(data) {
            dataLayer.push(data)
        }

        function prepare_data($layer) {

            var product_count = get_data($layer, 'product-count');
            var data = {},
                action = get_data($layer, 'action');

            data.event = get_data($layer, 'event');

            var step,products = [];
            for (step = 0; step < product_count; step++) {

                var product = {};

                if(get_data($layer, 'product-has-variants', step) && data.event === 'addToCart'){
                   var $option = $layer.parents('form').find('option[data-gtm-event="viewProductVariant"]'),
                       is_select = $option.length;

                   if(is_select){
                       var $select = $option.parent(),
                           $element = $select.find('option:selected');
                   } else {
                       var $element = $layer.parents('form').find('input[data-gtm-event="viewProductVariant"]:checked');
                   }

                    product = prepareProductByLayer($element, step);
                } else {
                    product = prepareProductByLayer($layer, step);
                }

                products.push(product)
            }

            data.ecommerce = {};
            data.ecommerce.currencyCode = get_data($layer, 'currency-code');

            if(data.event == 'productImpressions'){
                data.ecommerce[action] = products;
            } else {

                data.ecommerce[action] = {}
                if(typeof  get_data($layer, 'action-field') !== 'undefined'){
                    data.ecommerce[action].actionField = get_data($layer, 'action-field');
                }
                data.ecommerce[action].products = products;

            }

            return data;
        }

        function prepareProductByLayer($layer, step)
        {
            var product = {};
            product.id = get_data($layer, 'product-id', step);
            product.name = get_data($layer, 'product-name', step);
            product.price = get_data($layer, 'product-price', step);
            product.brand = get_data($layer, 'product-brand', step);

            product = get_data_and_add_to_object($layer, product, 'category', step)
            product = get_data_and_add_to_object($layer, product, 'variant', step)
            product = get_data_and_add_to_object($layer, product, 'quantity', step)
            product = get_data_and_add_to_object($layer, product, 'list', step)
            product = get_data_and_add_to_object($layer, product, 'position', step)
            product = get_data_and_add_to_object($layer, product, 'coupon', step)

            return product;
        }

        function get_data_and_add_to_object($layer, product, name, step) {
            var data = get_data($layer, 'product-'+name, step);

            if((name == 'category' || name == 'variant') && data == ''){
                product[name] = '';
                return product;
            }

            if(data !== ''){
                product[name] = data;
                return product;
            }
            return product;
        }

        function get_data($layer, name, i = null) {
            if(i == null){
                return $layer.data(DATA_LAYERS_PREFIX + '-' + name) ?? ''
            }

            return $layer.data(DATA_LAYERS_PREFIX + '-' + name + '[' + i + ']') ?? ''
        }

        function prepare_layer_and_push($layer) {
            if(is_layer_completed_push($layer)){
                return;
            }
            set_layer_completed_push($layer)

            send_push(prepare_data($layer))
        }

        function set_layer_completed_push($layer) {
            completed_push_layers.push($layer)
        }

        function is_layer_completed_push($layer) {
            return $.inArray( $layer, completed_push_layers ) >= 0;
        }

        function find_all_layers(){
            $('[data-'+DATA_LAYERS_PREFIX+']').each(function() {
                init_object($(this));
            });
        }

        function init_object($layer) {

            if(get_action($layer) === ON_CLICK_METHOD){
                on_click($layer)
            }

            if(get_action($layer) === ON_DISPLAY_METHOD){
                on_display($layer)
            }

            if(get_action($layer) === ON_VIEWPORT_METHOD){
                on_viewport($layer)
            }
        }

        function get_action($layer) {
            return $layer.data(DATA_METHOD)
        }

        function on_click($layer) {
            $layer.click(function (e) {
                prepare_layer_and_push($layer)
            })
        }

        function on_display($layer) {
            prepare_layer_and_push($layer)
        }

        function on_viewport($layer) {
            if($layer.isInViewport()){
                prepare_layer_and_push($layer)
            }
            $(window).scroll($layer, function() {
                if($layer.isInViewport()){
                    prepare_layer_and_push($layer)
                }
            });
        }

        init()
    });
})
(jQuery);



