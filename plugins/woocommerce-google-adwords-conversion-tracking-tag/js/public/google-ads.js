if (typeof varExists !== "function") {
    varExists = function(varName) {
        return new Promise(function (resolve, reject) {
            (function waitForJQuery() {
                if (typeof window[varName] !== 'undefined') return resolve();
                setTimeout(waitForJQuery, 30);
            })();
        });
    }
}

varExists('jQuery').then(function () {

    jQuery(function () {

        if (wooptpmDataLayer.pixels.google.ads.dynamic_remarketing.status) {

            // view_item_list event
            jQuery(document).on('wooptpmViewItemList', function (event, product) {

                if(
                    wooptpmDataLayer.general.variationsOutput &&
                    product.isVariable &&
                    wooptpmDataLayer.pixels.google.ads.dynamic_remarketing.send_events_with_parent_ids === false
                ) return;

                // try to prevent that WC sends cached hits to Google
                if(!product) return;

                // console.log('firing google view_item_list event');
                // console.log(product);

                // console.log('send to: ' + wooptpmDataLayer.pixels.google.ads.conversionIds);

                gtag('event', 'view_item_list', {
                    send_to: wooptpmDataLayer.pixels.google.ads.conversionIds,
                    items  : [{
                        id                      : product.dyn_r_ids[wooptpmDataLayer.pixels.google.ads.dynamic_remarketing.id_type],
                        google_business_vertical: wooptpmDataLayer.pixels.google.ads.google_business_vertical,
                    }]
                });
            });

            // add_to_cart event
            jQuery(document).on('wooptpmAddToCart', function (event, product) {

                // console.log('firing google ads add_to_cart event');
                // console.log(product);
                // console.log(wooptpmDataLayer.pixels.google.ads.conversionIds);
                // console.log('dyn_r_id: ' + product.dyn_r_ids[wooptpmDataLayer.pixels.google.ads.dynamic_remarketing.id_type]);
                // console.log('dyn_r_id: ' + product.dyn_r_ids['gpf']);

                gtag("event", "add_to_cart", {
                    "send_to": wooptpmDataLayer.pixels.google.ads.conversionIds,
                    "value"  : product.quantity * product.price,
                    "items"  : [{
                        "id"                      : product.dyn_r_ids[wooptpmDataLayer.pixels.google.ads.dynamic_remarketing.id_type],
                        "quantity"                : product.quantity,
                        "price"                   : product.price,
                        "google_business_vertical": wooptpmDataLayer.pixels.google.ads.google_business_vertical
                    }]
                });
            });

            // view_item event
            jQuery(document).on('wooptpmViewItem', function (event, product) {

                // console.log('firing google ads view_item event');
                // console.log(product);
                // console.log(wooptpmDataLayer.pixels.google.ads.conversionIds);
                // console.log('dyn_r_id: ' + product.dyn_r_ids[wooptpmDataLayer.pixels.google.ads.dynamic_remarketing.id_type]);
                // console.log('dyn_r_id: ' + product.dyn_r_ids['gpf']);

                gtag("event", "view_item", {
                    "send_to": wooptpmDataLayer.pixels.google.ads.conversionIds,
                    "value"  : product.quantity * product.price,
                    "items"  : [{
                        "id"                      : product.dyn_r_ids[wooptpmDataLayer.pixels.google.ads.dynamic_remarketing.id_type],
                        "quantity"                : 1,
                        "price"                   : product.price,
                        "google_business_vertical": wooptpmDataLayer.pixels.google.ads.google_business_vertical
                    }]
                });
            });
        }
    })

    jQuery(window).on('load', function () {

        wooptpmExists().then(function () {

            try {
                if (wooptpmDataLayer.shop.page_type === 'product' && wooptpmDataLayer.shop.product_type !== 'variable' && wooptpm.getMainProductIdFromProductPage()) {

                    // console.log('productId: ' + wooptpm.getMainProductIdFromProductPage());

                    let product = wooptpm.getProductDataForViewItemEvent(wooptpm.getMainProductIdFromProductPage());

                    gtag("event", "view_item", {
                        "send_to": wooptpmDataLayer.pixels.google.ads.conversionIds,
                        "value"  : 1 * product.price,
                        "items"  : [{
                            "id"                      : product.dyn_r_ids[wooptpmDataLayer.pixels.google.ads.dynamic_remarketing.id_type],
                            "google_business_vertical": wooptpmDataLayer.pixels.google.ads.google_business_vertical
                        }]
                    });
                } else if (wooptpmDataLayer.shop.page_type === 'search') {

                    let products = [];

                    for (const [key, product] of Object.entries(wooptpmDataLayer.products)) {

                        if(
                            wooptpmDataLayer.general.variationsOutput &&
                            product.isVariable &&
                            wooptpmDataLayer.pixels.google.ads.dynamic_remarketing.send_events_with_parent_ids === false
                        ) return;

                        products.push({
                            "id"                      : product.dyn_r_ids[wooptpmDataLayer.pixels.google.ads.dynamic_remarketing.id_type],
                            "google_business_vertical": wooptpmDataLayer.pixels.google.ads.google_business_vertical
                        });
                    }

                    // console.log(products);

                    gtag("event", "view_search_results", {
                        "send_to": wooptpmDataLayer.pixels.google.ads.conversionIds,
                        // "value"  : 1 * product.price,
                        "items": products
                    });
                }
            } catch (e) {
                console.log(e);
            }
        })
    });

}).catch(function () {
    console.log('object couldn\'t be loaded');
})