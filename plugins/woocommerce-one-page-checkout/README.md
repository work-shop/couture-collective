# WooCommerce One Page Checkout

Super fast purchase with WooCommerce.

Display product selection fields at the top of a complete checkout process one any page, post or custom post type.

You can use a custom template, display only certain products and choose which input type (e.g. radio, button or quantity input) is used for product selection fields using attributes on the shortcode.

To add the checkout process to a page or post, there's also a handy button on the visual editor that provides a graphical interface for choosing products and other attributes. To learn how to use the button on the visual editor, please watch this brief [2 minute video](https://vimeo.com/prospress/review/102062905/44706b89fb).

You can also manually add a shortcode `[woocommerce_one_page_checkout]` to any page or post and use the shortcode's attributes.

### Requirements:

* WooCommerce 2.2 or newer
* WordPress 4.0 or newer

### Shortcode Attributes

Each of the following attributes may only exist once per shortcode. If more than one set of attributes exist, the second set of attributes will be used.

#### product_ids

To show a limited set of products, use the `product_ids` attribute within the shortcode. The `product_ids` attribute expects a list of product ID's separated by commas as seen below.

The order of the products included here will also determine the order in which the product's are displayed on the checkout page.

Usage: `[woocommerce_one_page_checkout product_ids="30,45,12"]`

######Important Notes:**  
* If the value of `product_ids` is empty, all products will be displayed
* If one of the three product ids is invalid, the other two products will still be displayed.

#### template

To give you control over how the product selection fields are displayed to the customer, the shortcode uses an optional `template` attribute.

Out-of-the-box, One Page Checkout includes a number of templates (explained in detail below). To use one of these templates, include the file slug of the template within the `template` attribute.

To use a custom template, add the `template` attribute to the shortcode with the path of a template located in your theme's `/woocommerce/templates/` directory. More information on creating custom templates is included below.

Usage: `[woocommerce_one_page_checkout template="pricing-table"]`

_Default: The product table template is used unless another value is specified here._

### Product Selection Templates

One Page Checkout includes a number of templates for displaying product selection fields at the top of your checkout forms.

#### Product Table

The Product Table template displays a row for each product containing its thumbnail, title and price. The style is based on the table displayed by default on the cart page.

This template is best for displaying a few products where the product images are helpful for making a choice, e.g. a set of halloween masks.

#### Product List

The Product List template displays a list of products with a radio button for selecting an option.

This template is useful when the customer does not need a description or photograph to choose which product to purchase, e.g. versions of an eBook.

#### Product Product

The Single Product template displays the product in a very similar way to how it will be presented on the single product page of your site.

This means it includes the product's description, images, gallery and other meta data.

This template is suitable for displaying one or two products on a page and providing all of the products information in the product selection fields, rather than the content before the shortcode.

#### Pricing Table Template

The built-in pricing table displays the products in a 2-5 column pricing table with the product's title, price and add-to-cart button at the top, followed by the product's attributes. Both taxonomy attributes and custom product attributes will be displayed.

If the products have shipping weight and dimensions, these will also be displayed at the base of the table.

Tip: if one or more of the product have fewer attributes than the others, and therefore smaller column height, position it to the right of the other products (i.e. include it after them in the shortcode).

#### Easy Pricing Table template

The extension also supports the [free Easy Pricing Tables plugin](https://wordpress.org/plugins/easy-pricing-tables/) and the [premium Easy Pricing Tables plugin](http://fatcatapps.com/easypricingtables/) (both by FatCatApps).

To use a pricing table as the template, set the template value to `easy_pricing_table` and include the pricing table's ID using the `easy_pricing_table_id` shortcode attribute, for example:

Usage: `[woocommerce_one_page_checkout template="easy_pricing_table" easy_pricing_table_id="454"]`

#### Default "Add to Cart" Input Type

The built-in templates decide whether to display the add to cart button or a quantity input based on whether the product is set to be sold individually.

If the product is sold individually, then a button input is used (as only one of the product can be added to an order). If the product is not set to be sold individually, then the WooCommerce quantity input is displayed to allow the customer to add larger quantities of the product to their order.

You can set a product to be sold individually (and therefore, display a button rather than quantity input) on the [Inventory Tab](http://docs.woothemes.com/document/managing-products/#inventory-tab).


#### Variable and Grouped Product Handling

The built-in One Page Checkout templates currently only support displaying each product individually.

If you add a variable product to a shortcode, each of its variations will be displayed separately (e.g. as a separate column in the pricing table).

Similarly, if you add a Grouped product, each of the Simple products in the group will be displayed separately (e.g. as a separate row in the table).

It is on the roadmap to add new templates which support selecting a variation from a variable product.


### Custom Product Selection Templates

As mentioned above, the One Page Checkout shortcode includes a `template` attribute to determine which template to use for displaying product selection fields.

You can create custom product selection templates to customise the display of products on your single page checkout.

To create a custom template, you need to:

1. create a file in your theme's `/templates/` directory
1. display the product's passed to the template
1. make sure there is an input field for each product with the add to cart ID attribute
1. register your template with One Page Checkout using the `'wcopc_templates'` filter


#### 1. Create a Template File

One Page Checkout conforms to the [WooCommerce Template Structure](http://docs.woothemes.com/document/template-structure/).

To create a custom One Page Checkout product selection template, create a file within your theme's directory of the form: `/templates/checkout/{template-name}.php` where `{template-name}` is replaced with the name of your template, e.g. `my-pricing-table`.

#### 2. Display Products for Selection

One you have created a template file, you need to add the markup and PHP to the template to display each of the products for that page.

Before beginning to develop your template, review one or two of the built-in templates. These examples will give you a quick overview of the requirements for a custom template and help illustrate the information discussed in this section.

##### 2.1. The Products Loop

You template will receive an array of products to display.

The products are passed in a variable named `$products` with each of the elements representing a product object, e.g. `WC_Product_Simple`, `WC_Product_Variation` or `WC_Product_Subscription`.

As these objects are standard WooCommerce product classes, they include all the standard data and methods of a product object to use in the template, e.g. `WC_Product::get_title()`.

##### 2.1. Extra Data

The product objects passed to your template also include a few extra pieces of data useful for the product selection template, these include:

* `$add_to_cart_id` - the ID of the product for adding to the cart - i.e. either `$product->variation_id` for a product variation or `$product->id` for a simple product
* `$in_cart` - a boolean value indicating whether the product is already in the cart or not, useful for pre-checking input values on page load
* `$cart_item` - if the item is in the cart, the cart item's data is included here, useful for pre-filling quantity inputs on page load

#### 3. Selection Fields

To select a product, the customer needs a selection field. One Page Checkout supports `button`, `radio`, `checkbox`, `a` and `number` elements for input.

##### 3.1. Built-in Template Parts

The easiest method for adding selection fields to your template is to use the built-in template parts.

For example, to display a button for adding/removing an item from the cart, include the following line of code:

```
<?php wc_get_template( 'checkout/add-to-cart/button.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path ); ?>
```

Similarly, to include a quantity input, use the following code:

```
<?php wc_get_template( 'checkout/add-to-cart/quantity-input.php', array( 'product' => $product ), '', PP_One_Page_Checkout::$template_path ); ?>
```

The same can be done for `radio` or `checkbox` inputs.

##### 3.2. Custom Product Selection Fields

You may notice the built-in templates include a `data-add_to_cart` data attribute on each product selection field.

This attribute is used by One Page Checkout to identify product selection fields and handle adding/removing the item with the ID in that attribute to/from the order.

Therefore, to create a custom product selection field, you need to ensure that you include a `data-add_to_cart_id` attribute in your template, and that the value of that attribute is the `$product->add_to_cart_id` property of the product.

By using the built-in template parts for product selection input fields, this value will be added automatically.

#### 4. Register your Template

Once you have created a template, you can use it on a page by including it within the shortcode's `template` attribute simply by including the file name (either with or without the `.php` extension).

However, you can also register the template with One Page Checkout to have it display in the list of templates included with the graphic interface for creating the One Page Checkout shortcode.

To register your template, attach a callback to the `'wcopc_templates'` filter and add a new array of your template's details to the `$templates` array passed to your function.

For example, to register a custom pricing table template, the code would be similar to:

```
function eg_add_opc_template( $templates ) {

	$templates['my-custom-pricing-table'] = array(
		'label'       => __( 'My Pricing Table', 'eg' ),
		'description' => __( "Display a sophisticated and colourful pricing table with each product's attributes, but not weight or dimensions.", 'eg' ),
	);

	return $templates;
}
add_filter( 'wcopc_templates', 'eg_add_opc_template' ) );
```

The key used in the `$templates` array should be the template's file name (excluding the extension). The `label` element of the array is the name displayed on the One Page Checkout dialog. The `description` element is used for the tooltip next to the template's name.