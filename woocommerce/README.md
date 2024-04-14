## Description

[!IMPORTANT]

If you add woocommerce to your theme or create a custom theme with woocommerce integrated you need to copy the fiels from /plugins/woocommerce/template to your themes/your_custom_theme/woocommerce folder

Then you need to add the apropriate shortcode in the woocommerce pages because from woocommerce 8.x up the blocks loaded in the pages are Guthenberg blocks and from what i saw its hard to override and they don't override from the .php files added in the woocommerce folder of you theme.

**Shortodes**

 - cart:
[woocommerce_cart]
 -  checkout
[woocommerce_checkout]

### Added a override for:
 -  Single product page
 -  cart page
 -  checkout page