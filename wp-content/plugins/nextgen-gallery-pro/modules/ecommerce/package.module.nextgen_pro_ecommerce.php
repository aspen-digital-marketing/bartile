<?php
class A_Digital_Downloads_Form extends Mixin
{
    function get_title()
    {
        return __('Digital Downloads', 'nextgen-gallery-pro');
    }
    function _get_field_names()
    {
        return array('digital_downloads');
    }
    function save_action()
    {
        return $this->get_model()->is_valid();
    }
    function enqueue_static_resources()
    {
        wp_enqueue_script('nextgen_pro_lightbox_digital_downloads_form_settings', $this->get_static_url('photocrati-nextgen_pro_ecommerce#source_settings_downloads.js'));
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->show_licensing_link = __('Display link to license terms?', 'nextgen-gallery-pro');
        $i18n->licensing_page = __('Licensing page:', 'nextgen-gallery-pro');
        $i18n->name_header = __('Name:', 'nextgen-gallery-pro');
        $i18n->price_header = __('Price:', 'nextgen-gallery-pro');
        $i18n->resolution_header = __('Longest Image Dimension:', 'nextgen-gallery-pro');
        $i18n->resolution_tooltip = __('A setting of 0px will deliver full-resolution images', 'nextgen-gallery-pro');
        $i18n->resolution_placeholder = __('Enter 0 for maximum', 'nextgen-gallery-pro');
        $i18n->item_title_placeholder = __('Enter title of the item', 'nextgen-gallery-pro');
        $i18n->delete = __('Delete', 'nextgen-gallery-pro');
        $i18n->add_another_item = __('Add another item', 'nextgen-gallery-pro');
        $i18n->no_items = __('No items available for this source.', 'nextgen-gallery-pro');
        $i18n->free_items_label = __('Allow free downloads to download directly from the cart sidebar', 'nextgen-gallery-pro');
        return $i18n;
    }
    function get_image_resolutions()
    {
        $retval = array('100' => 'Full');
        for ($i = 90; $i > 0; $i -= 10) {
            $retval[$i] = "{$i}%";
        }
        return $retval;
    }
    function get_pages()
    {
        return get_pages(array('number' => 100));
    }
    function _render_digital_downloads_field()
    {
        $items = $this->get_model()->get_digital_downloads();
        $settings = $this->get_model()->digital_download_settings;
        // This attribute was added after initial release; this just prevents a PHP warning for existing pricelists
        if (!isset($settings['skip_checkout'])) {
            $settings['skip_checkout'] = '0';
        }
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#accordion_download_items', array('items' => $items, 'settings' => $settings, 'i18n' => $this->object->get_i18n_strings(), 'image_resolutions' => $this->object->get_image_resolutions(), 'pages' => $this->object->get_pages(), 'item_category' => NGG_PRO_ECOMMERCE_CATEGORY_DIGITAL_DOWNLOADS), TRUE);
    }
}
class A_Display_Type_Ecommerce_Form extends Mixin
{
    function _get_field_names()
    {
        $fields = $this->call_parent('_get_field_names');
        // Add an option to enable e-commerce only if there are pricelists created
        if (C_Pricelist_Mapper::get_instance()->count() > 0) {
            if (is_array($fields)) {
                $fields[] = 'is_ecommerce_enabled';
            }
        }
        return $fields;
    }
    function _render_is_ecommerce_enabled_field($display_type)
    {
        $value = isset($display_type->settings['is_ecommerce_enabled']) ? $display_type->settings['is_ecommerce_enabled'] : FALSE;
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#field_ecommerce_enabled', array('display_type_name' => $display_type->name, 'instructions_label' => esc_attr(__('see instructions', 'nextgen-gallery-pro')), 'non_https_warning' => __('<strong>Warning</strong>: HTTPS was not detected! Without it NextGen Gallery Pro cannot process payments.', 'nextgen-gallery-pro'), 'name' => 'is_ecommerce_enabled', 'label' => __('Enable ecommerce?', 'nextgen-gallery-pro'), 'value' => $value, 'text' => '', 'hidden' => FALSE, 'href' => esc_attr(admin_url('/admin.php?page=ngg-ecommerce-instructions-page')), 'is_ssl' => is_ssl()), TRUE);
    }
}
class A_Ecommerce_Ajax extends Mixin
{
    /**
     * Read an image file into memory and display it
     *
     * This is necessary for htaccess or server-side protection that blocks access to filenames ending with "_backup"
     * At the moment it only supports the backup or full size image.
     */
    function get_image_file_action()
    {
        $image_id = $this->param('image_id', FALSE);
        $order_id = $this->param('order_id', FALSE);
        $item_id = $this->param('item_id', FALSE);
        $invalid_request = FALSE;
        // Image id must be present along with either an order id or pricelist item id
        if (!$image_id || !$order_id && !$item_id || $order_id && $item_id) {
            $invalid_request = TRUE;
        }
        if (!$invalid_request && $order_id) {
            $order = C_Order_Mapper::get_instance()->find_by_hash($order_id);
            if (!$order || !is_object($order) || $order->status !== 'paid' || !in_array($image_id, $order->cart['image_ids'])) {
                $invalid_request = TRUE;
            }
        } else {
            if (!$invalid_request && $item_id) {
                $mapper = C_Pricelist_Item_Mapper::get_instance();
                $item = $mapper->find($item_id);
                if (!$item || !is_object($item) || $item->price > 0) {
                    $invalid_request = TRUE;
                }
            }
        }
        if ($invalid_request) {
            header('HTTP/1.1 404 Not found');
            exit;
        }
        $storage = C_Gallery_Storage::get_instance();
        // By default this method serves the backup image as that is the only size requested by
        // C_Digital_Downloads->render_download_list()
        $abspath = $storage->get_image_abspath($image_id, 'backup');
        // Just in case the image has been removed from NextGen but the file was not removed (this can be triggered via
        // the Gallery > Other Options > Image Options > "Delete Image File?" setting
        if (empty($abspath)) {
            if (!isset($order)) {
                $order = C_Order_Mapper::get_instance()->find_by_hash($order_id);
            }
            $image = new stdClass();
            $original = $order->cart['images'][$image_id];
            foreach ($original as $key => $value) {
                $image->{$key} = $value;
            }
            $storage = C_Gallery_Storage::get_instance();
            $abspath = $storage->get_image_abspath($image, 'backup');
            $image_id = $image;
        }
        if ($item_id && $item->resolution != 0) {
            $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
            $params = array('width' => $item->resolution, 'height' => $item->resolution, 'crop' => FALSE, 'watermark' => FALSE, 'quality' => 100);
            $named_size = $dynthumbs->get_size_name($params);
            $abspath = $storage->get_image_abspath($image_id, $named_size, TRUE);
            if (!$abspath) {
                $thumbnail = $storage->generate_image_size($image_id, $named_size);
                if ($thumbnail) {
                    $thumbnail->destruct();
                    $abspath = $storage->get_image_abspath($image_id, $named_size, TRUE);
                }
            }
        }
        $mimetype = 'application/octet';
        if (function_exists('finfo_buffer')) {
            $finfo = new finfo(FILEINFO_MIME);
            $mimetype = @$finfo->file($abspath);
        } elseif (function_exists('mime_content_type')) {
            $mimetype = @mime_content_type($abspath);
        }
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . basename($storage->get_image_abspath($image_id, 'full')));
        header("Content-type: " . $mimetype);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . @filesize($abspath));
        readfile($abspath);
        exit;
    }
    function get_digital_download_settings_action($original_retval = array())
    {
        $ids = explode(',', $this->object->param('id'));
        foreach ($ids as $id) {
            if ($pricelist = C_Pricelist_Mapper::get_instance()->find_for_image($id)) {
                $retval = $pricelist->digital_download_settings;
                $retval['header'] = esc_html(__('Digital Downloads', 'nextgen-gallery-pro'));
                if (intval($retval['show_licensing_link']) > 0) {
                    $retval['licensing_link'] = get_page_link($retval['licensing_page_id']);
                    $view_licensing_terms = __('View license terms', 'nextgen-gallery-pro');
                    $retval['header'] .= " <a target='_blank' href='{$retval['licensing_link']}'>({$view_licensing_terms})</a>";
                }
                $original_retval[$id]['digital_download_settings'] = $retval;
            }
        }
        return $original_retval;
    }
    function get_cart_items_action()
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        $settings = $this->param('settings');
        if (!$settings) {
            $settings = array();
        }
        if (!isset($settings['shipping_method'])) {
            $settings['shipping_method'] = FALSE;
        }
        $cart = new C_NextGen_Pro_Cart($this->param('cart'), $settings);
        return $cart->to_array();
    }
    function get_shipping_amount_action()
    {
        $cart = new C_NextGen_Pro_Cart($this->param('cart'), $this->param('settings'));
        return array('shipping' => $cart->get_shipping());
    }
    function is_print_lab_ready($cache = FALSE)
    {
        static $_cache = NULL;
        if ($_cache && $cache) {
            return $_cache;
        }
        $retval = M_NextGen_Pro_Ecommerce::check_ecommerce_requirements();
        $_cache = $retval['printlab_ecommerce_ready'];
        return $_cache;
    }
    /**
     * @param bool $cache
     * @param C_Pricelist_Item $item
     * @return bool|null
     */
    function is_ecommerce_ready($cache, $item)
    {
        static $_cache = array();
        if (!empty($_cache[$item->ID]) && $cache) {
            return $_cache[$item->ID];
        }
        $retval = M_NextGen_Pro_Ecommerce::check_ecommerce_requirements();
        $key = 'manual_ecommerce_ready';
        if ($item->source === NGG_PRO_DIGITAL_DOWNLOADS_SOURCE) {
            $key = 'download_ecommerce_ready';
        }
        $_cache[$item->ID] = $retval[$key];
        return $_cache[$item->ID];
    }
    function get_image_items_action($original_retval = array())
    {
        $ids = explode(',', $this->object->param('id'));
        foreach ($ids as $image_id) {
            $retval = array('items' => array(), 'valid_license' => M_Licensing::is_valid_license());
            $cart = $this->param('cart');
            $cart = new C_NextGen_Pro_Cart($cart);
            if ($pricelist = C_Pricelist_Mapper::get_instance()->find_for_image($image_id, TRUE)) {
                $retval['pricelist'] = $pricelist->ID;
                $retval['has_pricelist'] = TRUE;
                $is_print_lab_ready = $retval['is_print_lab_ready'] = $this->is_print_lab_ready(TRUE);
                $items = $pricelist->get_items($image_id, TRUE);
                foreach (array_values($items) as $item) {
                    $ecommerce_ready = $retval[$item->ID . '_ecommerce_ready'] = $this->is_ecommerce_ready(TRUE, $item);
                    $is_labfulfilled = $retval[$item->ID . '_is_labfulfilled'] = $item->is_lab_fulfilled();
                    if ($ecommerce_ready && (!$is_labfulfilled || $is_labfulfilled && $is_print_lab_ready)) {
                        // Determine if the item is in the cart. If so, set the item's quantity
                        foreach ($cart->get_items($item->source) as $cart_item) {
                            if ($cart_item->ID == $item->ID && $cart_item->image->pid == $image_id) {
                                $item->quantity = $cart_item->quantity;
                                $item->crop_offset = $cart_item->crop_offset;
                                break;
                            }
                        }
                        $retval['items'][] = $item->get_entity();
                    }
                }
            } else {
                $retval['has_pricelist'] = FALSE;
            }
            $original_retval[$image_id]['image_items'] = $retval;
        }
        return $original_retval;
    }
    function is_order_paid_action()
    {
        $retval = array('paid' => FALSE);
        if ($order = C_Order_Mapper::get_instance()->find_by_hash($this->param('order'))) {
            if ($order->status == 'paid') {
                $retval['paid'] = TRUE;
                $checkout = C_NextGen_Pro_Checkout::get_instance();
                $retval['thank_you_page_url'] = $checkout->get_thank_you_page_url($order->hash, TRUE);
            }
        } else {
            $retval['error'] = __("We're sorry, but we couldn't find your order.", 'nextgen-gallery-pro');
        }
        return $retval;
    }
    function check_ecommerce_requirements_action()
    {
        header('Accept: application/json');
        header('Content-Type: application/json');
        $retval = array('success' => FALSE);
        if (wp_verify_nonce($_POST['nonce'], 'check_ecommerce_requirements')) {
            try {
                $retval['status'] = M_NextGen_Pro_Ecommerce::check_ecommerce_requirements();
                $retval['success'] = TRUE;
            } catch (Exception $ex) {
                $retval['error'] = $ex->toString();
            }
        } else {
            $retval['error'] = "Cheatin', eh?";
        }
        $retval = array_merge($retval, $_POST);
        return $retval;
    }
    function get_print_lab_order_action()
    {
        header('Accept: application/json');
        header('Content-Type: application/json');
        $retval = array('success' => FALSE);
        // Accepts JSON document as body
        $input = file_get_contents("php://input");
        if ($json = json_decode($input)) {
            if (isset($json->nonce) && WPSimpleNonce::checkNonce($json->nonce->name, $json->nonce->value)) {
                if (isset($json->order) && ($order = C_Order_Mapper::get_instance()->find_by_hash($json->order, TRUE))) {
                    // To always ensure that the order has the latest representation of the cart
                    $order->cart = $order->get_cart()->to_array();
                    $order = $order->get_entity();
                    unset($order->cart['images']);
                    // Set print lab service parameters
                    $order->licenseKey = isset($order->license_key) ? $order->license_key : M_NextGen_Pro_Ecommerce::get_license('photocrati-nextgen-pro');
                    $order->stripeCustId = C_NextGen_Settings::get_instance()->get('stripe_cus_id', NULL);
                    $order->stripeMode = M_NextGen_Pro_Ecommerce::is_stripe_test_mode_enabled() ? 'testing' : 'live';
                    $retval['success'] = TRUE;
                    $retval['order'] = $order;
                }
            }
        }
        return $retval;
    }
    function resubmit_lab_order_action()
    {
        $retval = array('success' => FALSE);
        if (isset($_POST['order']) and $order = C_Order_Mapper::get_instance()->find_by_hash($_POST['order'], TRUE)) {
            if (isset($_POST['nonce']) and wp_verify_nonce($_POST['nonce'], 'resubmit_lab_order')) {
                if (!C_NextGen_Pro_Checkout::get_instance()->submit_lab_order($order, TRUE) instanceof WP_Error) {
                    $retval['success'] = TRUE;
                }
            }
        }
        return $retval;
    }
    function delete_credit_card_info_action()
    {
        $retval = array('success' => FALSE, 'next_nonce' => base64_encode(json_encode(WPSimpleNonce::createNonce('deleteCreditCardInfo'))));
        if (isset($_POST['nonce']) && ($nonce = json_decode($this->param('nonce'))) && WPSimpleNonce::checkNonce($nonce->name, $nonce->value)) {
            $settings = C_NextGen_Settings::get_instance();
            if ($settings->stripe_cus_id) {
                // TODO: Write an endpoint which removes the customer record in Stripe
                $settings->delete('stripe_cus_id');
                $settings->delete('stripe_card_info');
                $retval['success'] = $settings->save();
            } else {
                $retval['success'] = TRUE;
            }
        }
        return $retval;
    }
    function update_credit_card_info_action()
    {
        $retval = array('success' => FALSE, 'next_nonce' => base64_encode(json_encode(WPSimpleNonce::createNonce('saveCreditCardInfo'))));
        if (isset($_POST['nonce']) && ($nonce = json_decode($this->param('nonce'))) && WPSimpleNonce::checkNonce($nonce->name, $nonce->value)) {
            if (isset($_POST['payment_method'])) {
                $token = $this->param('payment_method');
                if ($token) {
                    $settings = C_NextGen_Settings::get_instance();
                    $body = json_encode(array('payment_method' => $token, 'studio_name' => $settings->get('ecommerce_studio_name'), 'studio_email' => M_NextGen_Pro_Ecommerce::get_studio_email_address(), 'studio_street_address' => $settings->get('ecommerce_studio_street_address'), 'studio_address_line' => $settings->get('ecommerce_studio_address_line'), 'studio_city' => $settings->get('ecommerce_studio_city'), 'studio_zip' => $settings->get('ecommerce_home_zip'), 'studio_country' => $settings->get('ecommerce_home_country'), 'studio_state' => $settings->get('ecommerce_home_state'), 'license' => M_NextGen_Pro_Ecommerce::get_license('photocrati-nextgen-pro'), 'testing' => M_NextGen_Pro_Ecommerce::is_stripe_test_mode_enabled()));
                    $response = wp_remote_post('https://4osfgn6rvj.execute-api.us-east-1.amazonaws.com/latest/saveCustomer', array('body' => $body, 'headers' => array('Content-Type' => 'application/json'), 'timeout' => 30));
                    if (!is_wp_error($response)) {
                        $retval['backend_response'] = $response['body'] = json_decode($response['body']);
                        if (property_exists($response['body'], 'id')) {
                            $settings->set('stripe_cus_id', $response['body']->id);
                            $retval['success'] = TRUE;
                        }
                        if (property_exists($response['body'], 'payment_method')) {
                            $payment_method = $response['body']->payment_method;
                            $settings->set('stripe_card_info', get_object_vars($payment_method));
                        }
                        $settings->save();
                    } else {
                        $retval['backend_error'] = $response->get_error_message();
                    }
                }
            }
        }
        return $retval;
    }
    /**
     * @return array
     */
    function save_pricelist_action()
    {
        $pricelist_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
        $mapper = C_Pricelist_Mapper::get_instance();
        $pricelist = $mapper->find($pricelist_id, TRUE);
        if (!$pricelist) {
            $pricelist = $mapper->create();
        }
        // disable caching or the changes we're about to save() won't be displayed
        $mapper = C_Pricelist_Mapper::get_instance();
        $mapper->_use_cache = FALSE;
        // A prior bug caused titles to have quotation marks escaped every time the pricelist was saved.
        // For this reason we now strip backslashes entirely from pricelist & item titles
        $pricelist_param = $this->object->param('pricelist');
        $pricelist_param['title'] = str_replace('\\', '', $pricelist_param['title']);
        if ($pricelist->save($pricelist_param)) {
            // Reset the pricelist object
            $this->pricelist = $pricelist;
            if ('checked' === $this->object->param('is_default_pricelist', NULL)) {
                $settings = C_NextGen_Settings::get_instance();
                if ($settings->get('ecommerce_default_pricelist') !== $pricelist->ID) {
                    $settings->set('ecommerce_default_pricelist', $pricelist->ID);
                    $settings->save();
                }
            }
            $pricelist_item = $this->object->param('pricelist_item');
            if (is_null($pricelist_item)) {
                $pricelist_item = array();
            } elseif (is_string($pricelist_item)) {
                $pricelist_item = json_decode($pricelist_item, true);
            }
            // Create price list items
            $item_mapper = C_Pricelist_Item_Mapper::get_instance();
            foreach ($pricelist_item as $id => $updates) {
                // Set the pricelist associated to each item
                $updates['pricelist_id'] = $pricelist->id();
                $updates['title'] = str_replace('\\', '', $updates['title']);
                if (!isset($updates['source_data'])) {
                    $updates['source_data'] = '';
                } else {
                    // These fields are omitted from the form sent to the browser as they're both extraneous (the user
                    // can't edit them and the data is retrieved from the catalog anyway) and costly in size to transmit
                    // simply parsing the DOM is grab all of the printlab source_data attributes is slow in the browser.
                    $sd = $updates['source_data'];
                    $manager = C_NextGEN_Printlab_Manager::get_instance();
                    $catalog = $manager->get_catalog($sd['catalog_id']);
                    $product = $catalog->find_product($sd['product_id']);
                    // TODO: Remove the next line in some way
                    // The cost must always be stored in the original amount provided by the printlab, but the
                    // manage-pricelist page submits the cost value after it's been converted to the site currency.
                    $updates['cost'] = $product->get_cost();
                    // $sd['catalog_id']      = $updates['source_data]['catalog_id'];
                    $sd['category_id'] = $product->category_original;
                    $sd['lab_attributes'] = $product->get_property('lab_attributes');
                    $sd['lab_id'] = $product->lab_id;
                    $sd['lab_properties'] = $product->get_property('lab_properties');
                    $sd['parent_category_id'] = $product->category;
                    $sd['product_id'] = strval($product->id);
                    $updates['source_data'] = $sd;
                }
                if (strpos($id, 'new-') !== FALSE) {
                    // Form validation & retrieving errors from pricelist items is not currently
                    // working / complete but we can skip creating new blank entries
                    if (empty($updates['title'])) {
                        continue;
                    }
                    $item = $item_mapper->create($updates);
                    $item->save();
                } else {
                    $item = $item_mapper->find($id, TRUE);
                    $item->save($updates);
                }
            }
            if (isset($_REQUEST['deleted_items'])) {
                $pricelist->destroy_items($_REQUEST['deleted_items']);
            }
            return array('redirect_url' => admin_url("edit.php?post_type=ngg_pricelist&ngg_edit=1&id=" . $pricelist->id() . '&message=saved'));
        }
        return array('error' => __('An error occurred saving this pricelist', 'nextgen-gallery-pro'));
    }
}
/**
 * Sets default values for added ecommerce settings
 *
 * @mixin C_Display_Type_Mapper
 * @adapts I_Display_Type_Mapper
 */
class A_Ecommerce_Display_Type_Mapper extends Mixin
{
    function set_defaults($entity)
    {
        $this->call_parent('set_defaults', $entity);
        $this->object->_set_default_value($entity, 'settings', 'is_ecommerce_enabled', FALSE);
    }
}
class A_Ecommerce_Factory extends Mixin
{
    function ngg_order($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return new C_NextGen_Pro_Order($properties, $mapper, $context);
    }
    function order($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return $this->ngg_order($properties, $mapper, $context);
    }
}
class A_Ecommerce_Gallery extends Mixin
{
    function define_columns()
    {
        $this->object->define_column('pricelist_id', 'BIGINT', 0, TRUE);
    }
}
class A_Ecommerce_Image extends Mixin
{
    function define_columns()
    {
        $this->object->define_column('pricelist_id', 'BIGINT', 0, TRUE);
    }
}
class A_Ecommerce_Instructions_Controller extends C_NextGen_Admin_Page_Controller
{
    function get_page_title()
    {
        return __('Ecommerce Set Up', 'nextgen-gallery-pro');
    }
    function get_page_heading()
    {
        return __('Ecommerce Set Up', 'nextgen-gallery-pro');
    }
    function get_required_permission()
    {
        return 'NextGEN Change options';
    }
}
class A_Ecommerce_Instructions_Form extends Mixin
{
    function get_title()
    {
        return $this->get_page_heading();
    }
    function get_page_heading()
    {
        return __('Getting Started', 'nextgen-gallery-pro');
    }
    function _get_field_names()
    {
        return array('ecommerce_instructions');
    }
    function _render_ecommerce_instructions_field()
    {
        $i18n = $this->get_i18n_strings();
        $ecommerce_steps = array('ecommerce_pages' => $i18n->ecommerce_pages, 'studio_address' => $i18n->studio_address, 'payment_gateway' => $i18n->payment_gateway, 'image_settings' => $i18n->image_settings, 'pro_lightbox' => $i18n->pro_lightbox, 'pricelist_created' => $i18n->pricelist_created, 'pricelist_associated' => $i18n->assocated_pricelist, 'has_ssl' => $i18n->has_ssl, 'enable_ecommerce' => $i18n->enabled_ecommerce);
        $printlab_steps = array('active_license' => $i18n->has_active_license, 'card_on_file' => $i18n->card_on_file, 'has_printlab_items' => $i18n->has_items, 'image_resizing' => $i18n->image_resizing);
        return $this->render_partial('photocrati-nextgen_pro_ecommerce#instructions', array('can_pro_wizard_run' => M_NextGen_Pro_Ecommerce::can_pro_wizard_run(), 'nonce' => wp_create_nonce('check_ecommerce_requirements'), 'status' => M_NextGen_Pro_Ecommerce::check_ecommerce_requirements(), 'i18n' => $i18n, 'ecommerce_steps' => $ecommerce_steps, 'printlab_steps' => $printlab_steps, 'render_status' => array($this, 'render_status')), TRUE);
    }
    function render_status($status, $step_id, $default)
    {
        $i18n = $this->get_i18n_strings();
        $classname = $default;
        if (array_key_exists($step_id, $status)) {
            if ($status[$step_id] === 'optional') {
                $classname = 'optional';
            } else {
                if ($status[$step_id]) {
                    $classname = 'done';
                } else {
                    $classname = 'required';
                }
            }
        }
        echo sprintf("<span class='ngg-status %s'>%s</span>", $classname, $i18n->{$classname});
    }
    function enqueue_static_resources()
    {
        $this->call_parent('enqueue_static_resources');
        $router = C_Router::get_instance();
        wp_enqueue_style('photocrati-nextgen_pro_ecommerce_instructions', $this->get_static_url('photocrati-nextgen_pro_ecommerce#ecommerce_instructions.css'));
        wp_enqueue_script('photocrati-nextgen_pro_ecommerce_instructions-js', $this->get_static_url('photocrati-nextgen_pro_ecommerce#ecommerce_instructions.js'), array('jquery', 'photocrati_ajax'));
        wp_localize_script('photocrati-nextgen_pro_ecommerce_instructions-js', 'ecommerce_instructions_i18n', get_object_vars($this->object->get_i18n_strings()));
    }
    function get_i18n_strings()
    {
        $retval = new stdClass();
        $retval->intro = __('Want help? Watch our short ecommerce overview video!', 'nextgen-gallery-pro');
        $retval->ecom_requirements = __('The following are required for ALL ECOMMERCE including manual and automated print fulfillment.', 'nextgen-gallery-pro');
        $retval->ecom_colors = __('Status will show green if done, red if still needed.', 'nextgen-gallery-pro');
        $retval->print_requirements = __('The following are required only for AUTOMATED PRINT FULFILLMENT.', 'nextgen-gallery-pro');
        $retval->checking = __('Checking...', 'nextgen-gallery-pro');
        $retval->check_now = __('Check Now', 'nextgen-gallery-pro');
        $retval->check_again = __('Check Again', 'nextgen-gallery-pro');
        $retval->ecomm_header = __("How to create a gallery with ecommerce", 'nextgen-gallery-pro');
        $retval->unknown = __('Unknown', 'nextgen-gallery-pro');
        $retval->done = __('Done', 'nextgen-gallery-pro');
        $retval->required = __('Required', 'nextgen-gallery-pro');
        $retval->optional = __('Optional', 'nextgen-gallery-pro');
        $retval->ecommerce_pages = sprintf(__('Choose pages to use for Checkout, Order Confirmation, Cancel, and Digital Downloads. %s', 'nextgen-gallery-pro'), sprintf('<a href="%s" target="_blank">%s</a>', admin_url('admin.php?page=ngg_ecommerce_options'), __('Ecommerce Settings', 'nextgen-gallery-pro')));
        $retval->studio_address = sprintf(__("Add studio name, email, and address. %s", 'nextgen-gallery-pro'), sprintf('<a href="%s" target="_blank">%s</a>', admin_url('admin.php?page=ngg_ecommerce_options'), __('Ecommerce Settings', 'nextgen-gallery-pro')));
        $retval->payment_gateway = sprintf(__("Set up a payment gateway. %s", 'nextgen-gallery-pro'), sprintf('<a href="%s" target="_blank">%s</a>', admin_url('admin.php?page=ngg_ecommerce_options'), __('Ecommerce Settings.', 'nextgen-gallery-pro')));
        $retval->image_settings = sprintf(__("Enable image backups. %s", 'nextgen-gallery-pro'), sprintf('<a href="%s" target="_blank">%s</a>', admin_url('admin.php?page=ngg_other_options'), __('Other Options', 'nextgen-gallery-pro')));
        $retval->image_resizing = sprintf(__("Enabled image resizing on upload. %s", "nextgen-gallery-pro"), sprintf('<a href="%s" target="_blank">%s</a>', admin_url('admin.php?page=ngg_other_options'), __('Other Options', 'nextgen-gallery-pro')));
        $retval->pro_lightbox = sprintf(__("Select Pro Lighbox as your lightbox. %s", 'nextgen-gallery-pro'), sprintf('<a href="%s" target="_blank">%s</a>', admin_url('admin.php?page=ngg_other_options'), __('Other Options ', 'nextgen-gallery-pro')));
        $retval->pricelist_created = sprintf(__("Create a Pricelist. %s", 'nextgen-gallery-pro'), sprintf('<a href="%s" target="_blank">%s</a>', admin_url('edit.php?post_type=ngg_pricelist'), __('Manage Pricelists', 'nextgen-gallery-pro')));
        $retval->assocated_pricelist = sprintf(__("Associate your pricelist with a gallery. %s", 'nextgen-gallery-pro'), sprintf('<a href="%s" target="_blank">%s</a>', admin_url('admin.php?page=nggallery-manage-gallery'), __('Manage Galleries', 'nextgen-gallery-pro')));
        $retval->enabled_ecommerce = __("When inserting your gallery on a page, be sure to enable ecommerce.", 'nextgen-gallery-pro');
        $retval->has_active_license = sprintf(__("Have active NextGEN Pro License. %s", 'nextgen-gallery-pro'), sprintf('<a href="https://www.imagely.com/account" target="_blank">%s</a>', __('Login to Imagely to renew', 'nextgen-gallery-pro')));
        $retval->card_on_file = sprintf(__("Have credit card on file (it will charged to cover wholesale cost from print lab). %s", 'nextgen-gallery-pro'), sprintf('<a href="%s" target="_blank">%s</a>', admin_url('admin.php?page=ngg_ecommerce_options'), __('Ecommerce Options', 'nextgen-gallery-pro')));
        $retval->has_items = sprintf(__("Add Print Lab items to a Pricelist. %s", 'nextgen-gallery-pro'), sprintf('<a href="%s" target="_blank">%s</a>', admin_url('edit.php?post_type=ngg_pricelist'), __('Manage Pricelists', 'nextgen-gallery-pro')));
        $retval->has_ssl = sprintf(__("Add SSL certificate. Without HTTPS NextGen Gallery Pro cannot process payments.", 'nextgen-gallery-pro'), sprintf('<a href="%s" target="_blank">%s</a>', 'https://searchengineland.com/google-starts-giving-ranking-boost-secure-httpsssl-sites-199446', __('SEO and search results', 'nextgen-gallery-pro')), sprintf('<a href="%s" target="_blank">%s</a>', 'https://www.searchenginejournal.com/chrome-browser-https/', __('insecure', 'nextgen-gallery-pro')));
        $retval->additional_documentation = sprintf(__("Additional Documentation on %s", 'nextgen-gallery-pro'), sprintf("<a target='_blank' href='%s'>%s</a>", 'https://www.imagely.com/?utm_source=ngg&utm_medium=ngguser&utm_campaign=ecommerce', __('imagely.com', 'nextgen-gallery-pro')));
        $retval->documentation_links = array('https://www.imagely.com/docs/ecommerce-overview/?utm_source=ngg&utm_medium=ngguser&utm_campaign=ecommerce' => __('Ecommerce Overview', 'nextgen-gallery-pro'), 'https://www.imagely.com/docs/ecommerce-settings/?utm_source=ngg&utm_medium=ngguser&utm_campaign=ecommerce' => __('How to Configure Ecommerce Options', 'nextgen-gallery-pro'), 'https://www.imagely.com/docs/create-pricelist/?utm_source=ngg&utm_medium=ngguser&utm_campaign=ecommerce' => __('How to Create and Assign a Pricelist', 'nextgen-gallery-pro'), 'https://www.imagely.com/docs/add-ecommerce/?utm_source=ngg&utm_medium=ngguser&utm_campaign=ecommerce' => __('How to Add Ecommerce to a Gallery', 'nextgen-gallery-pro'));
        $retval->proofing_header = __('How to create a proofing gallery', 'nextgen-gallery-pro');
        $retval->proofing_step_1 = sprintf(__("Configure your %s.", 'nextgen-gallery-pro'), sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=ngg_ecommerce_options'), __('proofing settings', 'nextgen-gallery-pro')));
        $retval->proofing_step_2 = sprintf(__("Select %s as your desired lightbox effect.", 'nextgen-gallery-pro'), sprintf('<a href="%s">%s</a>', admin_url('admin.php?page=ngg_other_options'), __('NextGen Pro Lightbox', 'nextgen-gallery-pro')));
        $retval->proofing_step_3 = __("When adding a gallery via the NextGen Insert Gallery Window, click the option to enable proofing.", 'nextgen-gallery-pro');
        return $retval;
    }
}
/**
 * @property C_NextGen_Admin_Page_Controller object
 */
class A_Ecommerce_Options_Controller extends Mixin
{
    /**
     * @return string
     */
    function get_page_title()
    {
        return __('Ecommerce Options', 'nextgen-gallery-pro');
    }
    /**
     * @return string
     */
    function get_page_heading()
    {
        return $this->get_page_title();
    }
    /**
     * @return stdClass
     */
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        return $i18n;
    }
    /**
     * @return string
     */
    function get_required_permission()
    {
        return 'NextGEN Change options';
    }
    function create_new_page($title, $content)
    {
        global $user_ID;
        $page = array('post_type' => 'page', 'post_status' => 'publish', 'post_content' => $content, 'post_author' => $user_ID, 'post_title' => $title, 'comment_status' => 'closed');
        return wp_insert_post($page);
    }
    function save_action()
    {
        if ($ecommerce = $this->object->param('ecommerce')) {
            $settings = C_NextGen_Settings::get_instance();
            // If we change currencies, we have to update the price of all pricelist items to cost + default markup
            $update_pricelist_item_price = $settings->ecommerce_currency != $ecommerce['currency'];
            foreach ($ecommerce as $key => $value) {
                $key = "ecommerce_{$key}";
                $settings->{$key} = $value;
            }
            if ($ecommerce['page_checkout'] == '') {
                $settings->ecommerce_page_checkout = $this->create_new_page(__('Shopping Cart', 'nextgen-gallery-pro'), '[ngg_pro_checkout]');
            } else {
                $this->add_shortcode_to_post($settings->ecommerce_page_checkout = $ecommerce['page_checkout'], '[ngg_pro_checkout]');
            }
            if ($ecommerce['page_thanks'] == '') {
                $settings->ecommerce_page_thanks = $this->create_new_page(__('Thanks', 'nextgen-gallery-pro'), '[ngg_pro_order_details]');
            } else {
                $this->add_shortcode_to_post($settings->ecommerce_page_thanks = $ecommerce['page_thanks'], '[ngg_pro_order_details]');
            }
            if ($ecommerce['page_cancel'] == '') {
                $settings->ecommerce_page_cancel = $this->create_new_page(__('Order Cancelled', 'nextgen-gallery-pro'), __('You order was cancelled.', 'nextgen-gallery-pro'));
            } else {
                $this->add_shortcode_to_post($settings->ecommerce_page_cancel = $ecommerce['page_cancel'], __('Your order was cancelled', 'nextgen-gallery-pro'), TRUE);
            }
            if ($ecommerce['page_digital_downloads'] == '') {
                $settings->ecommerce_page_digital_downloads = $this->create_new_page(__('Digital Downloads', 'nextgen-gallery-pro'), __('[ngg_pro_digital_downloads]'));
            } else {
                $this->add_shortcode_to_post($settings->ecommerce_page_digital_downloads = $ecommerce['page_digital_downloads'], '[ngg_pro_digital_downloads]');
            }
            if (isset($ecommerce['cart_menu_item']) && $ecommerce['cart_menu_item'] != 'none') {
                $this->add_checkout_page_to_menu();
            } else {
                $this->remove_checkout_page_from_menu();
            }
            if (!M_Licensing::is_valid_license()) {
                $settings->ecommerce_tax_enable = 0;
            }
            if ($settings->save() && $update_pricelist_item_price) {
                $manager = C_NextGEN_Printlab_Manager::get_instance();
                foreach ($manager->get_catalog_ids() as $id) {
                    $catalog = $manager->get_catalog($id);
                    C_NextGen_Pro_Currencies::get_conversion_rate($catalog->currency, $settings->ecommerce_currency);
                }
                $this->update_pricelist_item_prices();
            }
        }
    }
    function add_checkout_page_to_menu()
    {
        $checkout_page_id = intval(C_NextGen_Settings::get_instance()->ecommerce_page_checkout);
        foreach (get_nav_menu_locations() as $location => $menu_id) {
            $items = wp_get_nav_menu_items($menu_id);
            $has_checkout_page = FALSE;
            if (is_array($items) and !empty($items)) {
                foreach ($items as $item) {
                    if ($item instanceof WP_Post && intval($item->object_id) == $checkout_page_id) {
                        $has_checkout_page = TRUE;
                    }
                }
            }
            if (!$has_checkout_page) {
                $checkout_page = WP_Post::get_instance($checkout_page_id);
                wp_update_nav_menu_item($menu_id, 0, $args = array('menu-item-object-id' => intval($checkout_page_id), 'menu-item-object' => $checkout_page->post_type, 'menu-item-type' => 'post_type', 'menu-item-status' => 'publish', 'menu-item-classes' => 'nextgen-menu-cart-icon-auto'));
            }
            break;
            // only add to the first navigation menu location
        }
    }
    function remove_checkout_page_from_menu()
    {
        $checkout_page_id = C_NextGen_Settings::get_instance()->ecommerce_page_checkout;
        foreach (get_nav_menu_locations() as $location => $menu_id) {
            $items = wp_get_nav_menu_items($menu_id);
            if (!is_array($items) || empty($items)) {
                continue;
            }
            foreach ($items as $item) {
                if ($item instanceof WP_Post && intval($item->object_id) == $checkout_page_id && in_array('nextgen-menu-cart-icon-auto', $item->classes)) {
                    _wp_delete_post_menu_item($item->db_id);
                }
            }
        }
    }
    function update_pricelist_item_prices()
    {
        global $wpdb;
        $mapper = C_Pricelist_Item_Mapper::get_instance();
        $post_ids = array();
        $post_content_when_clauses = array();
        $meta_value_when_clauses = array();
        foreach ($mapper->find_all() as $item) {
            if (isset($item->cost)) {
                // Mark this post as one to be updated
                $post_ids[] = $item->ID;
                $item->price = $mapper->get_price($item, TRUE, TRUE, FALSE, TRUE);
                $post = $mapper->_convert_entity_to_post($item);
                $post_content_when_clauses[] = "WHEN {$post->ID} THEN \"{$post->post_content_filtered}\"";
                $meta_value_when_clauses[] = "WHEN (post_id = {$post->ID} AND meta_key = 'price') THEN {$item->price}";
            }
        }
        // Are there posts to update?
        if ($post_ids) {
            $post_ids = implode(",", $post_ids);
            $post_content_when_clauses = implode("\n", $post_content_when_clauses);
            $meta_value_when_clauses = implode("\n", $meta_value_when_clauses);
            // Update posts table
            $sql = trim("\n                                UPDATE {$wpdb->posts}\n                                SET\n                                    post_content = (\n                                        CASE ID\n                                            {$post_content_when_clauses}\n                                        END\n                                    ),\n                                    post_content_filtered = (\n                                        CASE ID\n                                            {$post_content_when_clauses}\n                                        END\n                                    )\n                                \n                                WHERE ID IN ({$post_ids}); \n                            ");
            $wpdb->query($sql);
            // Update postmeta table
            $sql = trim("\n                    UPDATE ({$wpdb->postmeta})\n                    SET meta_value = (\n                        CASE\n                            {$meta_value_when_clauses}\n                        END\n                    )\n                    WHERE post_id IN ({$post_ids}) AND meta_key = 'price';\n                ");
            $wpdb->query($sql);
            return TRUE;
        }
        return FALSE;
    }
    function add_shortcode_to_post($post_id, $shortcode, $only_if_empty = FALSE)
    {
        if ($post = get_post($post_id)) {
            if ($only_if_empty) {
                if (strlen($post->post_content) == 0) {
                    $post->post_content .= "\n" . $shortcode;
                    wp_update_post($post);
                }
            } elseif (strpos($post->post_content, $shortcode) === FALSE) {
                $post->post_content .= "\n" . $shortcode;
                wp_update_post($post);
            }
        }
    }
}
/** @property C_Form $object */
class A_Ecommerce_Options_Form extends Mixin
{
    function get_title()
    {
        return $this->get_page_heading();
    }
    function get_page_heading()
    {
        return __('General Options', 'nextgen-gallery-pro');
    }
    function _get_field_names()
    {
        return array(
            'nextgen_pro_ecommerce_currency',
            'nextgen_pro_ecommerce_page_checkout',
            'nextgen_pro_ecommerce_page_thanks',
            'nextgen_pro_ecommerce_page_cancel',
            'nextgen_pro_ecommerce_page_digital_downloads',
            'nextgen_pro_ecommerce_cart_menu_item',
            'nextgen_pro_ecommerce_default_pricelist',
            'nextgen_pro_ecommerce_not_for_sale_msg',
            'nextgen_pro_ecommerce_studio_name',
            'nextgen_pro_ecommerce_studio_street_address',
            'nextgen_pro_ecommerce_studio_address_line',
            'nextgen_pro_ecommerce_studio_city',
            'nextgen_pro_ecommerce_home_country',
            'nextgen_pro_ecommerce_home_state',
            'nextgen_pro_ecommerce_home_zip',
            'nextgen_pro_ecommerce_studio_email',
            'nextgen_pro_ecommerce_domestic_shipping_rate',
            'nextgen_pro_ecommerce_intl_shipping_rate',
            // 'nextgen_pro_ecommerce_whcc_intl_shipping_rate',
            'nextgen_pro_ecommerce_tax_enable',
            'nextgen_pro_ecommerce_cookies_enable',
        );
    }
    function get_i18n_strings()
    {
        $i18n = NULL;
        try {
            $i18n = $this->call_parent('get_i18n_strings');
        } catch (Exception $ex) {
        }
        if (!$i18n) {
            $i18n = new stdClass();
        }
        $i18n->calculating = __('Calculating...', 'nextgen-gallery-pro');
        $i18n->currency_changed = sprintf(__("If you change your currency, any print lab items in your pricelist will have their price updated using the last bulk markup value applied to that pricelist.\n\nIf no previous bulk markup value is found, a default markup of %d%% will be applied.\n\nPlease select OK to continue or CANCEL to revert to the previous currency selected.", "nextgen-gallery-pro"), NGG_PRO_ECOMMERCE_DEFAULT_MARKUP);
        $i18n->error_empty = __('%s cannot be empty.', 'nextgen-gallery-pro');
        $i18n->error_invalid = __('%s is in an invalid format.', 'nextgen-gallery-pro');
        $i18n->error_minimum = __('%s needs to be at least %s characters.', 'nextgen-gallery-pro');
        $i18n->form_invalid = __('Form contains errors, please fix these errors before saving.', 'nextgen-gallery-pro');
        $i18n->invalid_zip = __('Invalid zip or postal code.', 'nextgen-gallery-pro');
        $i18n->license_expired = __('Your NextGEN Pro license has expired. Sales tax has been disabled.', 'nextgen-gallery-pro');
        $i18n->select_country = __('Select Country', 'nextgen-gallery-pro');
        $i18n->select_region = __('Select Region', 'nextgen-gallery-pro');
        return $i18n;
    }
    function enqueue_static_resources()
    {
        $this->call_parent('enqueue_static_resources');
        $router = C_Router::get_instance();
        if (!wp_script_is('sprintf')) {
            wp_register_script('sprintf', $router->get_static_url('photocrati-nextgen_pro_ecommerce#sprintf.js'));
        }
        if (!wp_script_is('stripe-v3')) {
            wp_register_script('stripe-v3', 'https://js.stripe.com/v3/');
        }
        // Enqueue fontawesome
        if (method_exists('M_Gallery_Display', 'enqueue_fontawesome')) {
            M_Gallery_Display::enqueue_fontawesome();
        }
        wp_enqueue_style('fontawesome');
        wp_enqueue_style('photocrati-nextgen_pro_ecommerce_options', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#ecommerce_options.css'));
        wp_enqueue_script('photocrati-nextgen_pro_ecommerce_options-settings-js', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#ecommerce_options_form_settings.js'), array('jquery', 'jquery-ui-tooltip', 'jquery.nextgen_radio_toggle', 'sprintf', 'stripe-v3'), NGG_PRO_ECOMMERCE_MODULE_VERSION);
        $settings = C_NextGen_Settings::get_instance();
        wp_localize_script('photocrati-nextgen_pro_ecommerce_options-settings-js', 'NGG_Pro_EComm_Settings', array('iso_4217_countries' => C_NextGen_Pro_Currencies::$countries, 'i18n' => $this->object->get_i18n_strings(), 'country_list_json_url' => M_NextGen_Pro_Ecommerce::get_country_list_json_url(), 'selected_country' => $settings->ecommerce_home_country, 'selected_state' => $settings->ecommerce_home_state, 'field_selectors' => array('root' => '#ngg_page_content form', 'name' => '#ecommerce_studio_name', 'street_address' => '#ecommerce_studio_street_address', 'address_line' => '#ecommerce_studio_address_line', 'city' => '#ecommerce_studio_city', 'country' => '#ecommerce_home_country', 'state' => '#ecommerce_home_state', 'zip' => '#ecommerce_home_zip', 'email' => '#ecommerce_studio_email', 'paypal_email' => '#ecommerce_paypal_email', 'paypal_user' => '#ecommerce_paypal_username', 'paypal_pass' => '#ecommerce_paypal_password', 'paypal_sig' => '#ecommerce_paypal_signature', 'paypal_std_email' => '#ecommerce_paypal_std_email', 'stripe_key_pub' => '#ecommerce_stripe_key_public', 'stripe_key_priv' => '#ecommerce_stripe_key_private')));
    }
    function _render_nextgen_pro_ecommerce_not_for_sale_msg_field()
    {
        $settings = C_NextGen_Settings::get_instance();
        // _render_select_field only needs $model->name
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->object->_render_textarea_field($model, 'not_for_sale_msg', __("\"Not for sale\" Message", 'nextgen-gallery-pro'), $settings->ecommerce_not_for_sale_msg);
    }
    function _render_nextgen_pro_ecommerce_home_country_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        // _render_select_field only needs $model->name
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->object->_render_select_field($model, 'home_country', __('<strong>Studio Country</strong>', 'nextgen-gallery-pro'), array(), $settings->ecommerce_home_country);
    }
    function _render_nextgen_pro_ecommerce_home_state_field($model)
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->object->_render_text_field($model, 'home_state', __('<strong>Studio State</strong>', 'nextgen-gallery-pro'), '', FALSE);
    }
    function _render_nextgen_pro_ecommerce_home_zip_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->object->_render_text_field($model, 'home_zip', __('<strong>Studio Postal Code</strong> (required for taxes)', 'nextgen-gallery-pro'), $settings->get('ecommerce_home_zip'), FALSE);
    }
    function _render_nextgen_pro_ecommerce_studio_name_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->object->_render_text_field($model, 'studio_name', __('<strong>Studio Name</strong>', 'nextgen-gallery-pro'), $settings->get('ecommerce_studio_name'), FALSE);
    }
    function _render_nextgen_pro_ecommerce_studio_street_address_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->object->_render_text_field($model, 'studio_street_address', __('<strong>Studio Street Address</strong>', 'nextgen-gallery-pro'), $settings->get('ecommerce_studio_street_address'), FALSE);
    }
    function _render_nextgen_pro_ecommerce_studio_address_line_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->object->_render_text_field($model, 'studio_address_line', __('Studio Address Line #2', 'nextgen-gallery-pro'), $settings->get('ecommerce_studio_address_line'), FALSE);
    }
    function _render_nextgen_pro_ecommerce_studio_city_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->object->_render_text_field($model, 'studio_city', __('<strong>Studio City</strong>', 'nextgen-gallery-pro'), $settings->get('ecommerce_studio_city'), FALSE);
    }
    function _render_nextgen_pro_ecommerce_studio_email_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $model = new stdClass();
        $model->name = 'ecommerce';
        $input = $this->object->_render_text_field($model, 'studio_email', __('<strong>Studio Email</strong>', 'nextgen-gallery-pro'), $settings->get('ecommerce_studio_email'), FALSE);
        $input = str_replace("/>", "readonly onfocus=\"if (this.hasAttribute('readonly')) {this.removeAttribute('readonly'); this.blur(); this.trigger('focus');}\"/>", $input);
        return $input;
    }
    function _retrieve_page_list()
    {
        $pages = apply_filters('ngg_ecommerce_page_list', get_pages());
        $options = array('' => __('Create new', 'nextgen-gallery-pro'));
        foreach ($pages as $page) {
            $options[$page->ID] = $page->post_title;
        }
        return $options;
    }
    function _render_nextgen_pro_ecommerce_currency_field($model)
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        $currencies = array();
        foreach (C_NextGen_Pro_Currencies::$currencies as $id => $currency) {
            $currencies[$id] = $currency['name'];
        }
        return $this->object->_render_select_field($model, 'currency', __('Currency', 'nextgen-gallery-pro'), $currencies, C_NextGen_Settings::get_instance()->ecommerce_currency);
    }
    function _render_nextgen_pro_ecommerce_page_checkout_field($model)
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        $pages = $this->_retrieve_page_list();
        return $this->object->_render_select_field($model, 'page_checkout', __('Checkout page', 'nextgen-gallery-pro'), $pages, C_NextGen_Settings::get_instance()->ecommerce_page_checkout, __("This page requires the [ngg_pro_checkout] shortcode, which will be automatically added if not already present. Selecting \"Create new\" will create a new page that will appear in your Primary Menu unless you've customized your menu settings: http://codex.wordpress.org/Appearance_Menus_SubPanel", 'nextgen-gallery-pro'));
    }
    function _render_nextgen_pro_ecommerce_page_thanks_field($model)
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        $pages = $this->_retrieve_page_list();
        return $this->object->_render_select_field($model, 'page_thanks', __('Thank-you page', 'nextgen-gallery-pro'), $pages, C_NextGen_Settings::get_instance()->ecommerce_page_thanks, __("This page should have the [ngg_pro_order_details] shortcode, which will be automatically added if not already present. Selecting \"Create new\" will create a new page that will appear in your Primary Menu unless you've customized your menu settings: http://codex.wordpress.org/Appearance_Menus_SubPanel", 'nextgen-gallery-pro'));
    }
    function _render_nextgen_pro_ecommerce_page_cancel_field($model)
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        $pages = $this->_retrieve_page_list();
        return $this->object->_render_select_field($model, 'page_cancel', __('Cancel page', 'nextgen-gallery-pro'), $pages, C_NextGen_Settings::get_instance()->ecommerce_page_cancel, __("Selecting \"Create new\" will create a new page that will appear in your Primary Menu unless you've customized your menu settings: http://codex.wordpress.org/Appearance_Menus_SubPanel", 'nextgen-gallery-pro'));
    }
    function _render_nextgen_pro_ecommerce_page_digital_downloads_field($model)
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        $pages = $this->_retrieve_page_list();
        return $this->object->_render_select_field($model, 'page_digital_downloads', __('Digital downloads page', 'nextgen-gallery-pro'), $pages, C_NextGen_Settings::get_instance()->ecommerce_page_digital_downloads, __("This page requires the [ngg_pro_digital_downloads] shortcode, which will be automatically added if not already present. Selecting \"Create new\" will create a new page that will appear in your Primary Menu unless you've customized your menu settings: http://codex.wordpress.org/Appearance_Menus_SubPanel", 'nextgen-gallery-pro'));
    }
    function _render_nextgen_pro_ecommerce_tax_enable_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $value = $settings->ecommerce_tax_enable;
        $license = M_Licensing::is_valid_license();
        if (!$license) {
            $value = 0;
        }
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#field_tax_enable', array('display_type_name' => 'ecommerce', 'name' => 'tax_enable', 'label' => __('Enable sales tax', 'nextgen-gallery-pro'), 'value' => $value, 'text' => __('A valid NextGen Pro license is required to calculate sales tax.', 'nextgen-gallery-pro'), 'tax1' => __('SALES TAX NOTE: Sales tax is complex. ', 'nextgen-gallery-pro'), 'tax2' => __('CLICK HERE', 'nextgen-gallery-pro'), 'tax3' => __(' to learn more about sales tax and how NextGEN Pro calculates it. Because we use a third party service (TaxJar), an active Pro license is required to enable sales tax.', 'nextgen-gallery-pro'), 'hidden' => FALSE, 'isValid' => $license), TRUE);
    }
    function _render_nextgen_pro_ecommerce_domestic_shipping_rate_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $currency = C_NextGen_Pro_Currencies::$currencies[$settings->ecommerce_currency];
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#shipping_field', array('display_type_name' => 'ecommerce', 'name' => 'domestic_shipping', 'name_amount' => 'domestic_shipping_rate', 'label' => __('Domestic shipping for manual-fulfilled items', 'nextgen-gallery-pro'), 'options' => array('flat_rate' => __('Flat Rate', 'nextgen-gallery-pro'), 'percent_rate' => __('Percentage Rate', 'nextgen-gallery-pro')), 'options_pieces' => array('flat_rate' => $currency['code'], 'percent_rate' => __('%', 'nextgen-gallery-pro')), 'value' => $settings->ecommerce_domestic_shipping, 'value_amount' => $settings->ecommerce_domestic_shipping_rate), true);
    }
    function _render_nextgen_pro_ecommerce_intl_shipping_rate_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $currency = C_NextGen_Pro_Currencies::$currencies[$settings->ecommerce_currency];
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#shipping_field', array('display_type_name' => 'ecommerce', 'name' => 'intl_shipping', 'name_amount' => 'intl_shipping_rate', 'label' => __('Allow International shipping for manual-fulfilled items', 'nextgen-gallery-pro'), 'options' => array('disabled' => __('Disabled', 'nextgen-gallery-pro'), 'flat_rate' => __('Flat Rate', 'nextgen-gallery-pro'), 'percent_rate' => __('Percentage Rate', 'nextgen-gallery-pro')), 'options_pieces' => array('flat_rate' => $currency['code'], 'percent_rate' => __('%', 'nextgen-gallery-pro')), 'value' => $settings->ecommerce_intl_shipping, 'value_amount' => $settings->ecommerce_intl_shipping_rate), true);
    }
    function _render_nextgen_pro_ecommerce_whcc_intl_shipping_rate_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $currency = C_NextGen_Pro_Currencies::$currencies[$settings->ecommerce_currency];
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#intl_shipping_field', array('tooltip' => __('WHCC will fulfill your automated print lab orders. WHCC is based in the US and will provide a shipping cost estimate at the time of checkout. If you want to allow shipments outside US/Canada, you will need to turn this option on and configure the settings below. They\'ll be used to charge for shipping when users order prints. WHCC will then charge you (through us) separately for the cost of shipping.', 'nextgen-gallery-pro'), 'display_type_name' => 'ecommerce', 'name' => 'whcc_intl_shipping', 'name_amount' => 'whcc_intl_shipping_rate', 'label' => _('Allow Automated Print Lab Shipments Outside US and Canada'), 'options' => array('disabled' => __('Disabled', 'nextgen-gallery-pro'), 'flat_rate' => __('Flat Rate', 'nextgen-gallery-pro'), 'percent_rate' => __('Percentage Rate', 'nextgen-gallery-pro')), 'options_pieces' => array('flat_rate' => $currency['code'], 'percent_rate' => __('%', 'nextgen-gallery-pro')), 'value' => $settings->ecommerce_whcc_intl_shipping, 'value_amount' => $settings->ecommerce_whcc_intl_shipping_rate), true);
        return $output;
    }
    function _render_nextgen_pro_ecommerce_cookies_enable_field($model)
    {
        $settings = C_NextGen_Settings::get_instance();
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->object->_render_radio_field($model, 'cookies_enable', __('Use cookies for cart storage', 'nextgen-gallery-pro'), $settings->ecommerce_cookies_enable, __("Cookies are adequate for most customers but can only hold a limited number (around 30) of products due to browser limitations. When disabled the browser localStorage API will be used which does not have this problem but cart contents will be different on example.com vs www.example.com as well as across HTTP/HTTPS", 'nextgen-gallery-pro'));
    }
    function _render_nextgen_pro_ecommerce_cart_menu_item_field($model)
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $this->object->_render_select_field($model, 'cart_menu_item', __('Cart menu icon', 'nextgen-gallery-pro'), array('none' => __('None', 'nextgen-gallery-pro'), 'icon' => __('Icon Only', 'nextgen-gallery-pro'), 'icon_with_items' => __('Icon Only (When Cart Has Items)', 'nextgen-gallery-pro'), 'icon_and_total' => __('Icon & Total', 'nextgen-gallery-pro'), 'icon_and_total_with_items' => __('Icon & Total (When Cart Has Items)', 'nextgen-gallery-pro')), C_NextGen_Settings::get_instance()->ecommerce_cart_menu_item, __('Determines the appearance of the Checkout page selected above when shown as an entry inside a menu. When a setting other than None is selected, the checkout page will be added to the first navigation menu registered by your theme.', 'nextgen-gallery-pro'));
    }
    function _render_nextgen_pro_ecommerce_default_pricelist_field($model)
    {
        // _render_select_field only needs $model->name
        $model = new stdClass();
        $model->name = 'ecommerce';
        $manager = C_Pricelist_Mapper::get_instance();
        $list = ['' => __('-- No default pricelist --', 'nextgen-gallery-pro')];
        foreach ($manager->find_all() as $pricelist) {
            $list[$pricelist->ID] = esc_html($pricelist->title);
        }
        return $this->object->_render_select_field($model, 'default_pricelist', __('Default pricelist', 'nextgen-gallery-pro'), $list, C_NextGen_Settings::get_instance()->get('ecommerce_default_pricelist'), __('New galleries will be assigned this pricelist', 'nextgen-gallery-pro'));
    }
}
class A_Ecommerce_Pages extends Mixin
{
    function setup()
    {
        $this->object->add(NGG_PRO_ECOMMERCE_OPTIONS_PAGE, array('adapter' => 'A_Ecommerce_Options_Controller', 'parent' => 'ngg_ecommerce_options', 'add_menu' => TRUE));
        $this->object->add('ngg_manage_pricelists', array('url' => '/edit.php?post_type=ngg_pricelist', 'menu_title' => __('Manage Pricelists', 'nextgen-gallery-pro'), 'permission' => 'NextGEN Change options', 'parent' => 'ngg_ecommerce_options'));
        $this->object->add('ngg_manage_coupons', array('url' => '/edit.php?post_type=ngg_coupon', 'menu_title' => __('Manage Coupons', 'nextgen-gallery-pro'), 'permission' => 'NextGEN Change options', 'parent' => 'ngg_ecommerce_options'));
        $this->object->add('ngg_manage_orders', array('url' => '/edit.php?post_type=ngg_order', 'menu_title' => __('View Orders', 'nextgen-gallery-pro'), 'permission' => 'NextGEN Change options', 'parent' => 'ngg_ecommerce_options'));
        $this->object->add('ngg_manage_proofs', array('url' => '/edit.php?post_type=nextgen_proof', 'menu_title' => __('View Proofs', 'nextgen-gallery-pro'), 'permission' => 'NextGEN Change options', 'parent' => 'ngg_ecommerce_options'));
        $this->object->add(NGG_PRO_ECOMMERCE_INSTRUCTIONS_PAGE, array('adapter' => 'A_Ecommerce_Instructions_Controller', 'parent' => 'ngg_ecommerce_options'));
        return $this->call_parent('setup');
    }
}
/**
 * Class A_Ecommerce_Printlab_Form
 * @mixin C_Form
 */
class A_Ecommerce_Printlab_Form extends Mixin
{
    function get_title()
    {
        return $this->get_page_heading();
    }
    function get_page_heading()
    {
        return __('Print Lab Integration', 'nextgen-gallery-pro');
    }
    function _get_field_names()
    {
        return array('ecommerce_stripe_connect');
    }
    function enqueue_static_resources()
    {
        parent::call_parent('enqueue_static_resources');
        wp_localize_script('photocrati-nextgen_pro_ecommerce_options-settings-js', 'print_lab_i18n', get_object_vars($this->get_i18n_strings()));
    }
    function get_i18n_strings()
    {
        $i18n = $this->page->get_i18n_strings();
        $i18n->faq1 = __("<strong>DO I NEED THIS?</strong> A credit card is needed only if you want to use automated print fulfillment.", 'nextgen-gallery-pro');
        $i18n->faq2 = __("<strong>IS THIS SECURE?</strong> Assuming you've enabled SSL on your website, then yes. This form sends your card information directly to Stripe, one of the world's leading payment processors. It is stored securely at Stripe, not locally by WordPress or NextGEN Gallery. Note: Without SSL, this form is not 100% secure. You should also enable SSL before receiving payments from your own visitors.", 'nextgen-gallery-pro');
        $i18n->faq3 = __("<strong>WILL YOU CHARGE ME?</strong> You will not be charged now. Your card will only be charged if someone submits a print lab order on your site. At that point, you will be billed for print and shipping costs from the print lab. You would pay those costs yourself if you worked directly with the lab. We're just automating the process for you.", 'nextgen-gallery-pro');
        $i18n->agreement = __("<strong>AGREEMENT: By submitting your card here, you authorise Imagely to bill your card for the cost of print lab orders.</strong>", 'nextgen-gallery-pro');
        $i18n->stripe_connect = __("Update credit card", 'nextgen-gallery-pro');
        $i18n->valid_card = __('Done! You have a valid credit card on file (last four digits %s).', 'nextgen-gallery-pro');
        $i18n->invalid_card = __('The credit card you submitted (last four digits %s) has expired.', 'nextgen-gallery-pro');
        $i18n->no_card = __('No card on file.', 'nextgen-gallery-pro');
        $i18n->connected = __('Your card is now connected and ready for automatic print lab fulfillment.', 'nextgen-gallery-pro');
        $i18n->not_connected = __("We're sorry, but we were unable to save your credit card information. Please check your card information and try again.", 'nextgen-gallery-pro');
        $i18n->remove_card = __('Remove card', 'nextgen-gallery-pro');
        $i18n->remove_card_err = __('There was a problem trying to remove your card', 'nextgen-gallery-pro');
        $i18n->card_removed = __('Your card has been removed', 'nextgen-gallery-pro');
        $i18n->non_https_warning = __('<strong>IMPORTANT: Your site is not using SSL/HTTPS. Please add SSL/HTTPS and return to this tab to add your credit card.</strong>', 'nextgen-gallery-pro');
        $i18n->btn_disabled = __("You are unable to save a credit card until SSL/HTTPS has been enabled for your site", 'nextgen-gallery-pro');
        return $i18n;
    }
    function get_last_4_digits()
    {
        $retval = FALSE;
        $card = C_NextGen_Settings::get_instance()->get('stripe_card_info');
        if ($card) {
            $retval = $card['last4'];
        }
        return $retval;
    }
    function has_card_expired()
    {
        $retval = TRUE;
        $card = C_NextGen_Settings::get_instance()->get('stripe_card_info');
        if ($card) {
            $expiry = new DateTime();
            $expiry->setDate(intval($card['exp_year']), intval($card['exp_month']), 1);
            $now = new DateTime();
            $retval = $now >= $expiry;
        }
        return $retval;
    }
    function get_stripe_customer_id()
    {
        return C_NextGen_Settings::get_instance()->get('stripe_cus_id', FALSE);
    }
    function _render_ecommerce_stripe_connect_field()
    {
        wp_localize_script('photocrati-nextgen_pro_ecommerce_options-settings-js', 'nggpro_stripe_data', array('server_url' => 'https://4osfgn6rvj.execute-api.us-east-1.amazonaws.com/latest/getSetupIntentSecret', 'return_url' => site_url("stripe_intents_rtn=1"), 'testing' => M_NextGen_Pro_Ecommerce::is_stripe_test_mode_enabled(), 'isSetupDone' => $this->get_stripe_customer_id() ? TRUE : FALSE, 'update_nonce' => base64_encode(json_encode(WPSimpleNonce::createNonce('saveCreditCardInfo'))), 'site_url' => site_url()));
        $delete_nonce = base64_encode(json_encode(WPSimpleNonce::createNonce('deleteCreditCardInfo')));
        return $this->render_partial("photocrati-nextgen_pro_ecommerce#stripe-connect", array('i18n' => $this->object->get_i18n_strings(), 'expired' => $this->has_card_expired(), 'last_4_digits' => $this->get_last_4_digits(), 'delete_nonce' => $delete_nonce, 'is_ssl' => is_ssl()), TRUE);
    }
}
class A_Ecommerce_Pro_Lightbox_Form extends A_NextGen_Pro_Lightbox_Form
{
    function _get_field_names()
    {
        $fields = $this->call_parent('_get_field_names');
        $fields[] = 'ecommerce_pro_lightbox_ecommerce_header';
        $fields[] = 'ecommerce_pro_lightbox_display_cart';
        return $fields;
    }
    function enqueue_static_resources()
    {
        wp_enqueue_script('ngg_pro_ecommerce_lightbox_form', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#ecommerce_pro_lightbox_form.js'));
        return $this->call_parent('enqueue_static_resources');
    }
    function _render_ecommerce_pro_lightbox_ecommerce_header_field($lightbox)
    {
        return $this->_render_header_field($lightbox, 'ecommerce', __('ECommerce', 'nextgen-gallery-pro'));
    }
    function _render_ecommerce_pro_lightbox_display_cart_field($lightbox)
    {
        $value = NULL;
        if (is_array($lightbox->values) && isset($lightbox->values['nplModalSettings'])) {
            if (isset($lightbox->values['nplModalSettings']['display_cart'])) {
                $value = $lightbox->values['nplModalSettings']['display_cart'];
            }
        } elseif (isset($lightbox->display_settings['display_cart'])) {
            $value = $lightbox->display_settings['display_cart'];
        }
        return $this->_render_radio_field($lightbox, 'display_cart', __('Display cart initially', 'nextgen-gallery-pro'), $value, __('When on the cart sidebar will be opened at startup. If the "Display Comments" option is also on the comments panel will open instead.', 'nextgen-gallery-pro'));
    }
}
class A_Manual_Pricelist_Settings_Form extends Mixin
{
    function get_title()
    {
        return __('Manual Fulfillment Settings', 'nextgen-gallery-pro');
    }
    function _get_field_names()
    {
        return array('manual_pricelist_settings');
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->domestic_shipping = __('Domestic shipping rate:', 'nextgen-gallery-pro');
        $i18n->global_shipping = __('International shipping rate:', 'nextgen-gallery-pro');
        $i18n->allow_global_shipping = __('Enable international shipping?', 'nextgen-gallery-pro');
        return $i18n;
    }
    function save_action()
    {
        return $this->get_model()->is_valid();
    }
    function _render_manual_pricelist_settings_field()
    {
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#accordion_pricelist_settings', array('settings' => $this->get_model()->settings, 'i18n' => $this->get_i18n_strings(), 'shipping_methods' => $this->object->get_shipping_methods()), TRUE);
    }
    function get_shipping_methods()
    {
        return array('flat' => __('Flat Rate', 'nextgen-gallery-pro'), 'percentage' => __('Percentage', 'nextgen-gallery-pro'));
    }
    function enqueue_static_resources()
    {
        wp_enqueue_script('nggpro_manual_pricelist_css', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#source_settings_manual.js'));
    }
}
class A_NextGen_Pro_Lightbox_Mail_Form extends Mixin
{
    function get_title()
    {
        return __('E-mail', 'nextgen-gallery-pro');
    }
    function get_page_heading()
    {
        return __('E-mail Settings', 'nextgen-gallery-pro');
    }
    function _get_field_names()
    {
        return array('ngg_pro_ecommerce_email_notification_subject', 'ngg_pro_ecommerce_email_notification_body', 'ngg_pro_ecommerce_enable_email_receipt', 'ngg_pro_ecommerce_email_receipt_subject', 'ngg_pro_ecommerce_email_receipt_body');
    }
    function get_proxy_model()
    {
        $model = new stdClass();
        $model->name = 'ecommerce';
        return $model;
    }
    function get_model()
    {
        return $settings = C_Settings_Model::get_instance();
    }
    function _render_ngg_pro_ecommerce_email_notification_subject_field()
    {
        return $this->_render_text_field($this->get_proxy_model(), 'email_notification_subject', __('Order notification e-mail subject:', 'nextgen-gallery-pro'), $this->get_model()->ecommerce_email_notification_subject, NULL, NULL, __('Subject', 'nextgen-gallery-pro'));
    }
    function _render_ngg_pro_ecommerce_email_notification_recipient_field()
    {
        return $this->_render_text_field($this->get_proxy_model(), 'email_notification_recipient', __('Order notification e-mail recipient:', 'nextgen-gallery-pro'), $this->get_model()->ecommerce_email_notification_recipient, NULL, NULL, __('john@example.com', 'nextgen-gallery-pro'));
    }
    function _render_ngg_pro_ecommerce_email_notification_body_field()
    {
        return $this->_render_textarea_field($this->get_proxy_model(), 'email_notification_body', __('Order notification e-mail content:', 'nextgen-gallery-pro'), $this->get_model()->ecommerce_email_notification_body, __("Wrap placeholders in %%param%%. Accepted placeholders: customer_name, email, total_amount, item_count, shipping_street_address, shipping_city, shipping_state, shipping_zip, shipping_country, order_id, hash, order_details_page, admin_email, blog_name, blog_description, blog_url, site_url, home_url, and file_list", 'nextgen-gallery-pro'), NULL);
    }
    function _render_ngg_pro_ecommerce_enable_email_receipt_field()
    {
        $model = $this->get_model();
        return $this->_render_radio_field($this->get_proxy_model(), 'enable_email_receipt', __('Send e-mail receipt to customer?', 'nextgen-gallery-pro'), $model->ecommerce_enable_email_receipt, __('If enabled a receipt will be sent to the customer after successful checkout', 'nextgen-gallery-pro'));
    }
    function _render_ngg_pro_ecommerce_email_receipt_subject_field()
    {
        $model = $this->get_model();
        return $this->_render_text_field($this->get_proxy_model(), 'email_receipt_subject', __('E-mail subject:', 'nextgen-gallery-pro'), $this->get_model()->ecommerce_email_receipt_subject, NULL, $model->ecommerce_enable_email_receipt ? FALSE : TRUE, __('Subject', 'nextgen-gallery-pro'));
    }
    function _render_ngg_pro_ecommerce_email_receipt_body_field()
    {
        $model = $this->get_model();
        return $this->_render_textarea_field($this->get_proxy_model(), 'email_receipt_body', __('E-mail content:', 'nextgen-gallery-pro'), $this->get_model()->ecommerce_email_receipt_body, __("Wrap placeholders in %%param%%. Accepted placeholders: customer_name, email, total_amount, item_count, shipping_street_address, shipping_city, shipping_state, shipping_zip, shipping_country, order_id, hash, order_details_page, admin_email, blog_name, blog_description, blog_url, site_url, and home_url", 'nextgen-gallery-pro'), $model->ecommerce_enable_email_receipt ? FALSE : TRUE);
    }
}
class A_NplModal_Ecommerce_Overrides extends Mixin
{
    function enqueue_lightbox_resources($displayed_gallery)
    {
        $settings = C_NextGen_Settings::get_instance();
        if ($settings->thumbEffect == NGG_PRO_LIGHTBOX) {
            wp_enqueue_script('ngg_nplmodal_ecommerce', $this->get_static_url('photocrati-nextgen_pro_ecommerce#nplmodal_overrides.js'), ['photocrati-nextgen_pro_lightbox-1']);
        }
        $this->call_parent('enqueue_lightbox_resources', $displayed_gallery);
    }
}
/** @property C_NextGen_Admin_Page_Controller object */
class A_Payment_Gateway_Form extends Mixin
{
    /**
     * @return string
     */
    function get_title()
    {
        return $this->object->get_page_heading();
    }
    /**
     * @return string
     */
    function get_page_heading()
    {
        return __('Payment Gateway', 'nextgen-gallery-pro');
    }
    /**
     * @return array
     */
    function _get_field_names()
    {
        return array();
    }
    function save_action()
    {
        $ecommerce = $this->object->param('ecommerce');
        if (empty($ecommerce)) {
            return;
        }
    }
    function enqueue_static_resources()
    {
        wp_enqueue_script('photocrati-nextgen_pro_ecommerce_payment_gateway-settings-js', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#ecommerce_payment_gateway_form_settings.js'), array('jquery.nextgen_radio_toggle'));
    }
}
class A_Pricelist_Datamapper_Column extends Mixin
{
    function define_columns()
    {
        $this->object->define_column('pricelist_id', 'BIGINT', 0, TRUE);
    }
}
class A_Pricelist_Factory extends Mixin
{
    function ngg_pricelist($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return new C_Pricelist($properties, $mapper, $context);
    }
    function pricelist($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return $this->ngg_pricelist($properties, $mapper, $context);
    }
    function ngg_pricelist_item($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return new C_Pricelist_Item($properties, $mapper, $context);
    }
    function pricelist_item($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        return $this->ngg_pricelist_item($properties, $mapper, $context);
    }
}
/** @property C_Form object */
class A_Print_Category_Form extends Mixin
{
    function get_title()
    {
        $title = __('Print Pricelist', 'nextgen-gallery-pro');
        switch ($this->object->context) {
            case NGG_PRO_ECOMMERCE_CATEGORY_CANVAS:
                $title = __('Canvas', 'nextgen-gallery-pro');
                break;
            case NGG_PRO_ECOMMERCE_CATEGORY_METAL_PRINTS:
                $title = __('Metal Prints', 'nextgen-gallery-pro');
                break;
            case NGG_PRO_ECOMMERCE_CATEGORY_MOUNTED_PRINTS:
                $title = __('Mounted Prints', 'nextgen-gallery-pro');
                break;
            case NGG_PRO_ECOMMERCE_CATEGORY_PRINTS:
                $title = __('Prints', 'nextgen-gallery-pro');
                break;
            case NGG_PRO_ECOMMERCE_CATEGORY_ACRYLIC_PRINTS:
                $title = __('Acrylic Prints', 'nextgen-gallery-pro');
                break;
            case NGG_PRO_ECOMMERCE_CATEGORY_WOOD_PRINTS:
                $title = __('Wood Prints', 'nextgen-gallery-pro');
                break;
            case NGG_PRO_ECOMMERCE_CATEGORY_BAMBOO_PANELS:
                $title = __('Bamboo Panels', 'nextgen-gallery-pro');
                break;
        }
        return $title;
    }
    /**
     * @param array $attrs
     * @param string|false $currency Example '840' for USD, '978' for EUR
     * @return string
     */
    static function render_price_field($attrs = array(), $currency = FALSE)
    {
        // Wish I could use a map and anonymous function here, but we're stuck with
        // PHP 5.2 and create_function() is NOT an alternative
        $attr_list = array();
        foreach ($attrs as $k => $v) {
            $attr_list[] = "{$k}=\"" . esc_attr($v) . "\"";
        }
        $attr_list = implode(" ", $attr_list);
        return sprintf(M_NextGen_Pro_Ecommerce::get_price_format_string($currency, TRUE, TRUE), "<input autocomplete='off' {$attr_list}/>");
    }
    function _get_field_names()
    {
        return array('print_pricelist_items');
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->add_another_item = __('Add another item', 'nextgen-gallery-pro');
        $i18n->cost_header = __('Cost', 'nextgen-gallery-pro');
        $i18n->cost_header_alt = __('Cost (%s)', 'nextgen-gallery-pro');
        $i18n->delete = __('Delete', 'nextgen-gallery-pro');
        $i18n->item_title_placeholder = __('Enter title of the item', 'nextgen-gallery-pro');
        $i18n->name_header = __('Name', 'nextgen-gallery-pro');
        $i18n->no_items = __('No items available for this source.', 'nextgen-gallery-pro');
        $i18n->price_header = __('Price', 'nextgen-gallery-pro');
        $i18n->price_header_alt = __('Price (%s)', 'nextgen-gallery-pro');
        $i18n->price_header_tooltip = __('All amounts shown are in %s', 'nextgen-gallery-pro');
        $i18n->cost_header_tooltip = __('All amounts shown are in %s. Due to fluctuations in currency conversion rates prices may not be exact.', 'nextgen-gallery-pro');
        return $i18n;
    }
    function save_action()
    {
        /** @var C_Pricelist $pricelist */
        $pricelist = $this->object->get_model();
        return $pricelist->is_valid();
    }
    function _render_print_pricelist_items_field()
    {
        $manager = C_NextGEN_Printlab_Manager::get_instance();
        $settings = C_NextGen_Settings::get_instance();
        /** @var C_Pricelist $pricelist */
        $pricelist = $this->object->get_model();
        $items = $pricelist->get_category_items($this->object->context);
        $site_currency_id = $cost_currency_id = $price_currency_id = $settings->ecommerce_currency;
        // If we cannot get a conversion ratio we use the catalog's currency instead of the site setting
        // TODO - this doesn't look generic and will break when we add more print labs
        $catalog = $manager->get_catalog('whcc');
        if ($catalog->currency !== $site_currency_id) {
            $currency_conversion_error = get_transient(C_NextGen_Pro_Currencies::get_conversion_error_transient_name($catalog->currency, $site_currency_id));
            if ($currency_conversion_error === FALSE) {
                $cost_currency_id = $site_currency_id;
            } else {
                $cost_currency_id = $catalog->currency;
            }
        }
        $cost_currency = C_NextGen_Pro_Currencies::$currencies[$cost_currency_id];
        $price_currency = C_NextGen_Pro_Currencies::$currencies[$price_currency_id];
        $cost_step = 1.0 / pow(10, $cost_currency['exponent']);
        $price_step = 1.0 / pow(10, $price_currency['exponent']);
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#accordion_print_items', array(
            'cost_currency' => $cost_currency,
            'cost_currency_id' => $cost_currency_id,
            'cost_step' => $cost_step,
            'i18n' => $this->get_i18n_strings(),
            'item_category' => $this->object->context,
            'items' => $items,
            'price_currency' => $price_currency,
            'price_currency_id' => $price_currency_id,
            'price_step' => $price_step,
            'printlab_manager' => C_NextGEN_Printlab_Manager::get_instance(),
            'show_alt_headers' => $site_currency_id == '840' ? FALSE : TRUE,
            // Enable for all currencies but USD
            'item_mapper' => C_Pricelist_Item_Mapper::get_instance(),
        ), TRUE);
    }
    function enqueue_static_resources()
    {
        wp_enqueue_script('jquery.serialize-json-js', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#jquery.serializejson.js'), array('jquery'));
    }
}
class C_Currency_Conversion_Notice
{
    /** @var C_Currency_Conversion_Notice $_instance */
    protected static $_instance = NULL;
    /** @var string $_message */
    protected static $_message = '';
    /**
     * @return C_Currency_Conversion_Notice
     */
    public static function get_instance()
    {
        if (!self::$_instance) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    /**
     * @return string
     */
    public function get_css_class()
    {
        return 'error';
    }
    /**
     * @param string $message
     */
    protected static function add_message($message)
    {
        if ($message === FALSE) {
            return;
        }
        if (!empty(self::$_message)) {
            self::$_message .= "<br/>";
        }
        self::$_message = $message;
    }
    /**
     * @return bool
     */
    public function is_renderable()
    {
        if (!C_NextGen_Admin_Page_Manager::is_requested()) {
            return FALSE;
        }
        // TODO: Remove this specific if() when a non-USD accepting printlab has been added
        // At the moment NextGen Pro's only printlab is WHCC which only accepts USD; for performance we
        // just skip this notification entirely if the site currency is USD as there's no conversion to do anyway.
        $settings = C_NextGen_Settings::get_instance();
        $currency = $settings->ecommerce_currency;
        if ($currency === '840') {
            return FALSE;
        }
        $manager = C_NextGEN_Printlab_Manager::get_instance();
        // TODO: Fix this
        // This is a clumsy hack. The problem is that this notice gets executed during the POST submission
        // when updating ecommerce settings and most importantly this notice is run BEFORE THE ECOMMERCE OPTIONS CONTROLLER
        // which means C_NextGen_Settings in no way yet represents what it's about to become.
        //
        // To ensure that this notification is presented correctly on the page loaded immediately after changing the
        // ecommerce options and uses the *new* currency we check if we're dealing with the ecommerce options page
        // and that a new currency has been provided before changing the currency we use in this notification
        if (!empty($_GET['page']) && $_GET['page'] === 'ngg_ecommerce_options' && !empty($_POST['ecommerce']) && !empty($_POST['ecommerce']['currency'])) {
            $new_currency = $_POST['ecommerce']['currency'];
            if (!empty(C_NextGen_Pro_Currencies::$currencies[$new_currency])) {
                $currency = $new_currency;
            }
        }
        foreach ($manager->get_catalog_ids() as $id) {
            $catalog = $manager->get_catalog($id);
            if ($catalog->currency !== $currency) {
                $transient_name = C_NextGen_Pro_Currencies::get_conversion_transient_name($catalog->currency, $currency);
                $transient_error_name = C_NextGen_Pro_Currencies::get_conversion_error_transient_name($catalog->currency, $currency);
                $error = get_transient($transient_error_name);
                if ($error !== FALSE) {
                    self::add_message($error);
                } else {
                    // lookup transient
                    $rate = get_transient($transient_name);
                    // doesn't exist: refresh our memory..
                    if ($rate === FALSE) {
                        $rate = C_NextGen_Pro_Currencies::get_conversion_rate($catalog->currency, $currency);
                    }
                    // no conversion: again look for the error message
                    if ($rate === 0) {
                        self::add_message(get_transient($transient_error_name));
                    }
                }
            }
        }
        if (!empty(self::$_message)) {
            return TRUE;
        }
        return FALSE;
    }
    /**
     * @return string
     */
    public function render()
    {
        return self::$_message;
    }
}
/**
 * NextGEN Gallery 2.0.66 didn't have proper implementations of handling backup images
 */
class Mixin_Pro_Storage extends Mixin
{
    /**
     * Use the 'backup' image as the 'original' so that generated images use the backup image as their source
     *
     * @param $image
     * @param bool $check_existance
     *
     * @return mixed
     */
    function get_original_abspath($image, $check_existance = FALSE)
    {
        return $this->object->get_image_abspath($image, 'backup', $check_existance);
    }
    /**
     * Gets the absolute path where the image is stored
     * Can optionally return the path for a particular sized image
     */
    function get_image_abspath($image, $size = 'full', $check_existance = FALSE)
    {
        $retval = NULL;
        $fs = C_Fs::get_instance();
        // Ensure that we have a size
        if (!$size) {
            $size = 'full';
        }
        // If we have the id, get the actual image entity
        if (is_numeric($image)) {
            $image = $this->object->_image_mapper->find($image);
        }
        // Ensure we have the image entity - user could have passed in an
        // incorrect id
        if (is_object($image)) {
            if ($gallery_path = $this->object->get_gallery_abspath($image->galleryid)) {
                $folder = $prefix = $size;
                switch ($size) {
                    # Images are stored in the associated gallery folder
                    case 'full':
                    case 'original':
                    case 'image':
                        $retval = $fs->join_paths($gallery_path, $image->filename);
                        break;
                    case 'backup':
                        $retval = $fs->join_paths($gallery_path, $image->filename . '_backup');
                        if (!@file_exists($retval)) {
                            $retval = $fs->join_paths($gallery_path, $image->filename);
                        }
                        break;
                    case 'thumbnails':
                    case 'thumbnail':
                    case 'thumb':
                    case 'thumbs':
                        $size = 'thumbnail';
                        $folder = 'thumbs';
                        $prefix = 'thumbs';
                    // deliberately no break here
                    // We assume any other size of image is stored in the a
                    //subdirectory of the same name within the gallery folder
                    // gallery folder, but with the size appended to the filename
                    default:
                        $image_path = $fs->join_paths($gallery_path, $folder);
                        // NGG 2.0 stores relative filenames in the meta data of
                        // an image. It does this because it uses filenames
                        // that follow conventional WordPress naming scheme.
                        if (isset($image->meta_data) && isset($image->meta_data[$size]) && isset($image->meta_data[$size]['filename'])) {
                            $image_path = $fs->join_paths($image_path, $image->meta_data[$size]['filename']);
                        } else {
                            $image_path = $fs->join_paths($image_path, "{$prefix}_{$image->filename}");
                        }
                        $retval = $image_path;
                        break;
                }
            }
        }
        // Check the existance of the file
        if ($retval && $check_existance) {
            if (!file_exists($retval)) {
                $retval = NULL;
            }
        }
        return $retval ? rtrim($retval, "/\\") : $retval;
    }
    /**
     * Backs up an image file
     * @param int|object $image
     */
    function backup_image($image)
    {
        $retval = FALSE;
        if ($image_path = $this->object->get_image_abspath($image)) {
            $retval = copy($image_path, $this->object->get_backup_abspath($image));
            // Store the dimensions of the image
            if (function_exists('getimagesize')) {
                if (!is_object($image)) {
                    $image = C_Image_Mapper::get_instance()->find($image);
                }
                if ($image) {
                    $dimensions = getimagesize($retval);
                    $image->meta_data['backup'] = array('filename' => basename($retval), 'width' => $dimensions[0], 'height' => $dimensions[1], 'generated' => microtime());
                }
            }
        }
        return $retval;
    }
    /**
     * Gets the absolute path of the backup of an original image
     * @param string $image
     */
    function get_backup_abspath($image)
    {
        return $this->object->get_image_abspath($image, 'backup');
    }
    function get_backup_dimensions($image)
    {
        return $this->object->get_image_dimensions($image, 'backup');
    }
    function get_backup_url($image)
    {
        return $this->object->get_image_url($image, 'backup');
    }
}
/**
 * Class Mixin_Pro_Ecomm_Storage
 *
 * NextGen Gallery's get_original_abspath() points to the fullsize image which we don't want
 */
class Mixin_Pro_Ecomm_Storage extends Mixin
{
    /**
     * Use the 'backup' image as the 'original' so that generated images use the backup image as their source
     *
     * @param $image
     * @param bool $check_existance
     *
     * @return mixed
     */
    function get_original_abspath($image, $check_existance = FALSE)
    {
        return $this->object->get_image_abspath($image, 'backup', $check_existance);
    }
    /**
     * At some point NGG's get_image_abspath() changed so that 'original' returned the main image and not the _backup
     * version of it. This causes digital downloads to render from that main image which is not desired.
     *
     * @param int|stdClass|C_Image $image
     * @param string $size
     * @param bool $check_existance
     * @return null|string
     */
    function get_image_abspath($image, $size = 'full', $check_existance = FALSE)
    {
        if ($size === 'original') {
            $size = 'backup';
        }
        return $this->call_parent('get_image_abspath', $image, $size, $check_existance);
    }
}
class C_Digital_Downloads extends C_MVC_Controller
{
    static $instance = NULL;
    /**
     * @return C_Digital_Downloads
     */
    static function get_instance()
    {
        if (!self::$instance) {
            $klass = get_class();
            self::$instance = new $klass();
        }
        return self::$instance;
    }
    function get_i18n_strings($order)
    {
        $retval = new stdClass();
        $retval->image_header = __('Image', 'nextgen-gallery-pro');
        $retval->resolution_header = __('Resolution', 'nextgen-gallery-pro');
        $retval->item_description_header = __('Item', 'nextgen-gallery-pro');
        $retval->download_header = __('Download', 'nextgen-gallery-pro');
        $retval->order_info = sprintf(__('Digital Downloads for Order #%s', 'nextgen-gallery-pro'), $order->ID);
        return $retval;
    }
    function index_action()
    {
        wp_enqueue_style('ngg-digital-downloads-page', $this->get_static_url('photocrati-nextgen_pro_ecommerce#digital_downloads_page.css'));
        $retval = __('Oops! This page usually displays details for image purchases, but you have not ordered any images yet. Please feel free to continue browsing. Thanks for visiting.', 'nextgen-gallery-pro');
        if ($order = C_Order_Mapper::get_instance()->find_by_hash($this->param('order'), TRUE)) {
            // Display digital downloads for verified transactions
            if ($order->status == 'paid') {
                $retval = $this->render_download_list($order);
            } else {
                $retval = $this->render_partial('photocrati-nextgen_pro_ecommerce#waiting_for_confirmation', array('msg' => __("We haven't received payment confirmation yet. This may take a few minutes. Please wait...", 'nextgen-gallery-pro')), TRUE);
            }
        }
        return $retval;
    }
    function get_gallery_storage()
    {
        $storage = C_Gallery_Storage::get_instance();
        if (version_compare(NGG_PLUGIN_VERSION, '2.0.66.99') <= 0) {
            $storage->get_wrapped_instance()->add_mixin('Mixin_Pro_Storage');
        } else {
            $storage->get_wrapped_instance()->add_mixin('Mixin_Pro_Ecomm_Storage');
        }
        return $storage;
    }
    function render_download_list($order)
    {
        $cart = $order->get_cart()->to_array();
        $storage = $this->get_gallery_storage();
        $images = array();
        $settings = C_NextGen_Settings::get_instance();
        foreach ($cart['images'] as $image_obj) {
            foreach ($image_obj->items as $item) {
                $image = new stdClass();
                foreach (get_object_vars($image_obj) as $key => $val) {
                    $image->{$key} = $val;
                }
                if ($item->source == NGG_PRO_DIGITAL_DOWNLOADS_SOURCE) {
                    $named_size = 'backup';
                    // Use the full resolution image
                    if ($item->resolution != 0) {
                        $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
                        $params = array('width' => $item->resolution, 'height' => $item->resolution, 'crop' => FALSE, 'watermark' => FALSE, 'quality' => 100);
                        $named_size = $dynthumbs->get_size_name($params);
                        $new_path = $storage->get_image_abspath($image, $named_size, TRUE);
                        if (!$new_path) {
                            $thumbnail = $storage->generate_image_size($image, $named_size);
                            if ($thumbnail) {
                                $thumbnail->destruct();
                            }
                        }
                    }
                    if ($named_size == 'backup') {
                        // in case the backup files are protected by server side rules we serve fullsize images from
                        // an ajax endpoint.
                        //
                        // we don't need to honor permalink styles as this is mostly hidden just determine the most
                        // reliable path to the photocrati_ajax controller
                        $url = $settings->get('ajax_url');
                        $pos = strpos($url, '?');
                        if ($pos === FALSE) {
                            $url .= '?';
                        } else {
                            $url .= '&';
                        }
                        $url .= 'action=get_image_file&order_id=' . $order->hash . '&image_id=' . $image_obj->{$image_obj->id_field};
                        $image->download_url = $url;
                    } else {
                        $image->download_url = $storage->get_image_url($image, $named_size);
                    }
                    // Set other properties
                    $dimensions = $storage->get_image_dimensions($image, $named_size);
                    $image->dimensions = $dimensions;
                    $image->resolution = $dimensions['width'] . 'x' . $dimensions['height'];
                    $image->item_description = $item->title;
                    $image->thumbnail_url = $storage->get_thumbnail_url($image);
                    array_push($images, $image);
                }
            }
        }
        return $this->render_partial('photocrati-nextgen_pro_ecommerce#digital_downloads_list', array('images' => $images, 'order' => $order, 'i18n' => $this->get_i18n_strings($order)), TRUE);
    }
}
class C_Invalid_License_Notice
{
    /** @var C_Invalid_License_Notice */
    static $_instance = NULL;
    /**
     * @return string
     */
    function get_css_class()
    {
        return 'error';
    }
    /**
     * @return bool
     */
    function is_renderable()
    {
        return C_NextGen_Admin_Page_Manager::is_requested() && !M_Licensing::is_valid_license();
    }
    /**
     * @return string
     */
    function render()
    {
        return __("<strong>Your NextGEN Pro license has expired.</strong> Automated print lab fulfillment and automated sales tax require active membership. To use these services, please renew your license at <a target='_blank' href='http://www.imagely.com'>imagely.com</a>.", 'nextgen-gallery-pro');
    }
    /**
     * @return C_Invalid_License_Notice
     */
    static function get_instance()
    {
        if (!self::$_instance) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
}
class E_NextGen_Mail_Missing_Details extends RuntimeException
{
    function __construct($message_or_previous = FALSE, $code = 0, $previous = NULL)
    {
        // We don't know if we have been passed a message yet or not
        $message = FALSE;
        // Determine if the first parameter is a string or exception
        if ($message_or_previous) {
            if (is_string($message_or_previous)) {
                $message = $message_or_previous;
            } else {
                $previous = $message_or_previous;
            }
        }
        // If no message was provided, create a default message
        if (!$message) {
            $message = __('To send an e-mail, recipient, subject, and content is required', 'nextgen-gallery-pro');
        }
        parent::__construct($message, $code);
    }
}
class C_Nextgen_Mail_Content
{
    var $_list;
    var $_private;
    var $_template;
    function __construct()
    {
        $this->_list = array();
        $this->_private = array();
    }
    function is_property($name)
    {
        return isset($this->_list[$name]);
    }
    function is_property_private($name)
    {
        return isset($this->_private[$name]) && $this->_private[$name];
    }
    function get_property($name)
    {
        if (isset($this->_list[$name])) {
            return $this->_list[$name];
        }
        return null;
    }
    function set_property($name, $value)
    {
        $this->_list[$name] = $value;
        $this->_private[$name] = false;
    }
    function set_property_private($name, $value)
    {
        $this->_list[$name] = $value;
        $this->_private[$name] = true;
    }
    function get_subject()
    {
        return $this->get_property('subject');
    }
    function set_subject($subject)
    {
        $this->set_property_private('subject', $subject);
    }
    function get_sender()
    {
        return $this->get_property('sender');
    }
    function set_sender($sender)
    {
        $this->set_property_private('sender', $sender);
    }
    function load_template($template_text)
    {
        $this->_template = $template_text;
    }
    function evaluate_template($template_text = null)
    {
        if ($template_text == null) {
            $template_text = $this->_template;
        }
        $template_text = str_replace(array("\r\n", "\n"), "\n", $template_text);
        $matches = null;
        if (preg_match_all('/%%(\\w+)%%/', $template_text, $matches, PREG_SET_ORDER) > 0) {
            foreach ($matches as $match) {
                $var = $match[1];
                $parts = explode('_', $var);
                $root = array_shift($parts);
                $name = implode('_', $parts);
                $replace = null;
                $var_value = !$this->is_property_private($var) ? $this->get_property($var) : null;
                if ($var_value == null) {
                    $var_meta = !$this->is_property_private($root) ? $this->get_property($root) : null;
                    if ($var_meta != null && isset($var_meta[$name])) {
                        $var_value = $var_meta[$name];
                    }
                }
                if ($var_value == null) {
                    // This is a place to have certain defaults set, or values which are not easily settable in a property list. It could also be extended in the future with custom callbacks etc.
                    switch ($root) {
                        case 'time':
                            switch ($name) {
                                case 'now_utc':
                                    // for clarification in case it's not obvious, this will replace the meta variable %%time_now_utc%% in the mail template
                                    $var_value = date(DATE_RFC850);
                                    break;
                            }
                            break;
                    }
                }
                if ($var_value != null) {
                    $replace = $var_value;
                }
                if (is_array($replace)) {
                    $replace = implode(', ', $replace);
                }
                if (!is_string($replace)) {
                    $replace = '';
                }
                $template_text = str_replace($match[0], $replace, $template_text);
            }
        }
        return $template_text;
    }
}
/*
* How you would send an e-mail

	$mailman = $registry->get_utility('I_Nextgen_Mail_Manager');
	$content = $mailman->create_content();
	$content->set_subject('test');
	$content->set_property('user', 'Test');
	$content->load_template('Hi %%user%%, test');

	$mailman->send_mail($content, 'some@email.com');
*/
class Mixin_Nextgen_Mail_Manager extends Mixin
{
    private $_from_name = NULL;
    private $_from_email = NULL;
    function create_content($type = null)
    {
        if ($type == null) {
            $type = 'C_Nextgen_Mail_Content';
        }
        return new $type();
    }
    function _override_from_name_hook($_)
    {
        return $this->_from_name;
    }
    function _override_from_email_hook($_)
    {
        return $this->_from_email;
    }
    function send($to_email, $subject = FALSE, $content = FALSE, $from_name = FALSE, $from_email = FALSE, $mail_headers = array())
    {
        $settings = C_NextGen_Settings::get_instance();
        // Ensure that we have a sender name
        if (!$from_name) {
            $from_name = $settings->get('ecommerce_studio_name');
        }
        if (!$from_name) {
            $from_name = $settings->get('ecommerce_studio_name');
        }
        if (!$from_name) {
            $from_name = get_bloginfo('name');
        }
        // Ensure that we have a sender e-mail
        if (!$from_email) {
            $from_email = M_NextGen_Pro_Ecommerce::get_studio_email_address();
        }
        $this->_from_name = $from_name;
        $this->_from_email = $from_email;
        // Get mail content
        $mail_body = null;
        if (is_string($content)) {
            $mail_body = $content;
        } else {
            if ($content instanceof C_Nextgen_Mail_Content) {
                if ($subject == null) {
                    $subject = $content->get_subject();
                }
                $mail_body = $content->evaluate_template();
            }
        }
        // Do we have everything required?
        if (!$to_email || !$subject || !$content) {
            throw new E_NextGen_Mail_Missing_Details();
        }
        // Override sender
        add_filter('wp_mail_from', array($this, '_override_from_email_hook'), PHP_INT_MAX - 1);
        add_filter('wp_mail_from_name', array($this, '_override_from_name_hook'), PHP_INT_MAX - 1);
        $retval = wp_mail($to_email, $subject, $mail_body, array_merge($mail_headers, array("From:\"{$from_name}\" <{$from_email}>")));
        remove_filter('wp_mail_from', array($this, '_override_from_email_hook'), PHP_INT_MAX - 1);
        remove_filter('wp_mail_from_name', array($this, '_override_from_name_hook'), PHP_INT_MAX - 1);
        return $retval;
    }
    /**
     * Please use send() instead
     * @deprecated
     * @see send()
     */
    function send_mail($content, $receiver, $subject = null, $sender = null, $mail_headers = array())
    {
        $mail_body = null;
        if (is_string($content)) {
            $mail_body = $content;
        } else {
            if ($content instanceof C_Nextgen_Mail_Content) {
                if ($subject == null) {
                    $subject = $content->get_subject();
                }
                if ($sender == null) {
                    $sender = $content->get_sender();
                }
                $mail_body = $content->evaluate_template();
            }
        }
        if ($mail_body != null) {
            if ($sender != null) {
                $mail_headers['From'] = $sender;
            }
            wp_mail($receiver, $subject, $mail_body, $mail_headers);
        }
    }
}
/**
 * @implements I_NextGen_Mail_Manager
 * @mixin Mixin_Nextgen_Mail_Manager
 */
class C_Nextgen_Mail_Manager extends C_Component
{
    static $_instances = array();
    function define($context = FALSE)
    {
        parent::define($context);
        $this->implement('I_Nextgen_Mail_Manager');
        $this->add_mixin('Mixin_Nextgen_Mail_Manager');
    }
    /**
     * @param bool|string $context
     * @return C_Nextgen_Mail_Manager
     */
    static function get_instance($context = False)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Nextgen_Mail_Manager($context);
        }
        return self::$_instances[$context];
    }
}
class C_NextGEN_Printlab_Catalog_Data
{
    static function get_whcc_currency()
    {
        return 'USD';
    }
    static function get_whcc_data($force = FALSE)
    {
        $timeout = NGG_PRO_WHCC_CATALOG_TTL;
        if ($force || !($catalog = get_transient('ngg_whcc_catalog_standard', FALSE))) {
            $req = new WP_Http();
            $res = $req->get('https://s3.amazonaws.com/imagely-catalogs/catalog.json', ['timeout' => 120]);
            $catalog = !$res instanceof WP_Error && isset($res['body']) && ($json = json_decode($res['body'], TRUE)) ? $json : FALSE;
            if ($json) {
                set_transient('ngg_whcc_catalog_standard', $json, $timeout);
                set_transient('ngg_whcc_catalog_standard_version', is_object($json) ? $json->version : $json['version'], $timeout);
            }
        }
        return $catalog;
    }
}
class C_NextGEN_Printlab_Item
{
    var $_id = null;
    var $_catalog = null;
    var $_properties = array();
    var $_cache = array();
    function __construct(&$catalog, $properties)
    {
        $this->_catalog =& $catalog;
        $this->_properties = $properties;
    }
    function get_property($name)
    {
        if (isset($this->_properties[$name])) {
            return $this->_properties[$name];
        }
        return null;
    }
    function __get($name)
    {
        $retval = NULL;
        switch ($name) {
            case 'id':
                $retval = $this->get_property('hash');
                break;
            case 'lab_id':
                $retval = $this->get_property('id');
                break;
            case 'label':
                $options = $this->get_property('options');
                $retval = $this->get_property('label');
                if ($options) {
                    $retval .= ' (' . implode(', ', array_values($options)) . ')';
                }
                break;
            case 'catalog_id':
                $retval = $this->_catalog->id;
                break;
            case 'catalog':
                $retval = $this->_catalog;
                break;
            case 'cost':
                $retval = $this->get_property('price');
                break;
            default:
                $retval = $this->get_property($name);
        }
        return $retval;
    }
    function is_default()
    {
        return $this->is_common;
    }
    function get_cost()
    {
        if (!isset($this->_cache['cost'])) {
            $currency = C_NextGen_Pro_Currencies::$currencies[$this->_catalog->currency];
            $exponent = $currency['exponent'];
            $this->_cache['cost'] = bcadd($this->cost, 0.0, $exponent);
        }
        return $this->_cache['cost'];
    }
    function get_cost_display()
    {
        return M_NextGen_Pro_Ecommerce::get_formatted_price($this->get_cost(), $this->_catalog->currency);
    }
    /**
     * @param float|int $markup
     * @return float|int
     */
    function get_cost_estimate($markup = 0)
    {
        $item = new stdClass();
        $item->cost = $this->get_cost();
        return C_Pricelist_Item_Mapper::get_instance()->get_price($item, $markup, TRUE);
    }
    function get_cost_estimate_display($markup = 0)
    {
        return M_NextGen_Pro_Ecommerce::get_formatted_price($this->get_cost_estimate($markup));
    }
}
/**
 * @property string $id
 * @property string $currency
 */
class C_NextGEN_Printlab_Catalog
{
    var $_id = null;
    var $_data = null;
    var $_currency = null;
    var $_order_handler = null;
    var $_categories = array();
    var $_items = array();
    function __construct($id, $data, $currency)
    {
        ini_set('memory_limit', '1024M');
        $this->_id = $id;
        $this->_data = $data;
        $this->_currency = $currency;
        $this->_parse_data();
    }
    function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->_id;
            case 'currency':
                return $this->_currency;
        }
        return null;
    }
    function _parse_data()
    {
        foreach ($this->_data['categories'] as $category_id => $category) {
            $products = isset($category['products']) ? $category['products'] : null;
            $parent = isset($category['parent']) ? $category['parent'] : null;
            $root = $parent != null ? $parent : $category_id;
            if ($products != null) {
                foreach ($products as $product_id => $product) {
                    $product['category'] = $root;
                    $product['category_original'] = $category_id;
                    $this->_items[$root][] = new C_NextGEN_Printlab_Item($this, $product);
                }
            }
            unset($category['products']);
            $category['id'] = $category_id;
            $category['gid'] = $this->_id . '.' . $category_id;
            $this->_categories[$category_id] = $category;
        }
    }
    /**
     * @return array
     */
    function get_categories()
    {
        return array_keys($this->_categories);
    }
    /**
     * @return array
     */
    function get_root_categories()
    {
        return array_keys($this->_items);
    }
    function get_category_info($category)
    {
        return $this->_categories[$category];
    }
    /**
     * @param $category
     * @return C_NextGEN_Printlab_Item[]|null
     */
    function get_category_items($category)
    {
        if (isset($this->_items[$category])) {
            usort($this->_items[$category], array($this, '_sort_by_label'));
            return $this->_items[$category];
        }
        return null;
    }
    function _sort_by_label($a, $b)
    {
        return strnatcasecmp($a->label, $b->label);
    }
    /**
     * @param string $product_id
     * @return C_NextGEN_Printlab_Item|null
     */
    function find_product($product_id)
    {
        foreach ($this->_items as $category => $items) {
            foreach ($items as $item) {
                if ($item->id == $product_id) {
                    return $item;
                }
            }
        }
        return null;
    }
    /**
     * @param string $order_hash
     * @return array|null
     */
    function get_order_data($order_hash)
    {
        return M_NextGen_Pro_Ecommerce::get_lab_order_status($order_hash);
    }
}
class C_NextGEN_Printlab_Manager extends C_Component
{
    static $_instances = array();
    var $_catalogs = array();
    /**
     * Returns an instance of the printlab manager
     * @return C_NextGEN_Printlab_Manager
     */
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    /**
     * Defines the instance
     * @param mixed $context
     */
    function define($context = FALSE)
    {
        parent::define($context);
        $this->implement('I_NextGEN_Printlab_Manager');
        $this->add_catalog('whcc', C_NextGEN_Printlab_Catalog_Data::get_whcc_data(), C_NextGEN_Printlab_Catalog_Data::get_whcc_currency(), array('C_NextGEN_Printlab_Catalog_Data'));
    }
    function add_catalog($catalog_id, $catalog_data, $currency)
    {
        $currency = C_NextGen_Pro_Currencies::find_currency_id($currency);
        $catalog = new C_NextGEN_Printlab_Catalog($catalog_id, $catalog_data, $currency);
        $this->_catalogs[$catalog_id] = $catalog;
        return $catalog;
    }
    function remove_catalog($catalog_id)
    {
        if (isset($this->_catalogs[$catalog_id])) {
            unset($this->_catalogs[$catalog_id]);
        }
    }
    /**
     * @param $catalog_id
     * @return C_NextGEN_Printlab_Catalog|null
     */
    function get_catalog($catalog_id)
    {
        if (isset($this->_catalogs[$catalog_id])) {
            return $this->_catalogs[$catalog_id];
        }
        return null;
    }
    /**
     * @return string[]
     */
    public function get_catalog_ids()
    {
        $retval = array();
        foreach ($this->_catalogs as $catalog_id => $catalog) {
            $retval[] = $catalog_id;
        }
        return $retval;
    }
    function get_printlab_item($item_id)
    {
    }
}
class C_NextGen_Pro_Add_To_Cart
{
    static $_template_rendered = FALSE;
    function enqueue_static_resources()
    {
        $router = C_Router::get_instance();
        // For some reason ajax.js isn't registered yet in 2.0.67.14 and above, so we have
        // to do it manually.
        if (method_exists('M_Ajax', 'register_scripts')) {
            M_Ajax::register_scripts();
        }
        $dependencies = ['photocrati-nextgen_pro_lightbox-1'];
        if (version_compare(NGG_PLUGIN_VERSION, '2.0.67') <= 0) {
            $dependencies[] = 'ngg-store-js';
        }
        wp_enqueue_script('ngg-pro-lightbox-ecommerce-overrides', $router->get_static_url('photocrati-nextgen_pro_ecommerce#lightbox_overrides.js'), $dependencies, NGG_PRO_ECOMMERCE_MODULE_VERSION, TRUE);
        wp_enqueue_style('ngg-pro-add-to-cart', $router->get_static_url('photocrati-nextgen_pro_ecommerce#add_to_cart.css'), array(), NGG_PRO_ECOMMERCE_MODULE_VERSION);
        M_NextGen_Pro_Ecommerce::enqueue_cart_resources();
        if (!self::$_template_rendered) {
            self::$_template_rendered = TRUE;
            $parameters = array('not_for_sale_msg' => C_NextGen_Settings::get_instance()->ecommerce_not_for_sale_msg, 'categories' => $this->get_categories(), 'i18n' => $this->get_i18n_strings());
            $add_to_cart_wrapper = new C_MVC_View('photocrati-nextgen_pro_ecommerce#add_to_cart/wrapper', $parameters);
            $add_to_cart_header = new C_MVC_View('photocrati-nextgen_pro_ecommerce#add_to_cart/header', $parameters);
            $add_to_cart_normal_item = new C_MVC_View('photocrati-nextgen_pro_ecommerce#add_to_cart/normal_item', $parameters);
            $add_to_cart_download_item = new C_MVC_View('photocrati-nextgen_pro_ecommerce#add_to_cart/download_item', $parameters);
            wp_localize_script('ngg-pro-lightbox-ecommerce-overrides', 'ngg_add_to_cart_templates', array('add_to_cart_wrapper' => $add_to_cart_wrapper->render(TRUE), 'add_to_cart_header' => $add_to_cart_header->render(TRUE), 'add_to_cart_normal_item' => $add_to_cart_normal_item->render(TRUE), 'add_to_cart_download_item' => $add_to_cart_download_item->render(TRUE)));
            wp_localize_script('ngg-pro-lightbox-ecommerce-overrides', 'ngg_cart_i18n', (array) $this->get_i18n_strings());
        }
    }
    function get_categories()
    {
        $result = array();
        $manager = C_Pricelist_Category_Manager::get_instance();
        foreach ($manager->get_ids() as $id) {
            $category = $manager->get($id);
            $result[$id] = $this->_render_category_header_template($id, $category['title']);
        }
        return $result;
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->add_to_cart = __('Add To Cart', 'nextgen-gallery-pro');
        $i18n->checkout = __('View Cart / Checkout', 'nextgen-gallery-pro');
        $i18n->coupon_error = __('Invalid coupon', 'nextgen-gallery-pro');
        $i18n->description = __('Description', 'nextgen-gallery-pro');
        $i18n->free_price = __('Free', 'nextgen-gallery-pro');
        $i18n->item_count = __('%d item(s)', 'nextgen-gallery-pro');
        $i18n->not_for_sale = __('This image is not for sale', 'nextgen-gallery-pro');
        $i18n->price = __('Price', 'nextgen-gallery-pro');
        $i18n->qty_add_desc = __('Change quantities to update your cart.', 'nextgen-gallery-pro');
        $i18n->quantity = __('Quantity', 'nextgen-gallery-pro');
        $i18n->total = __('Total', 'nextgen-gallery-pro');
        $i18n->update_cart = __('Update Cart', 'nextgen-gallery-pro');
        $i18n->nggpl_cart_updated = __('Your cart has been updated', 'nextgen-gallery-pro');
        $i18n->nggpl_toggle_sidebar = __('Toggle cart sidebar', 'nextgen-gallery-pro');
        $i18n->download_add = __('Add', 'nextgen-gallery-pro');
        $i18n->download_free = __('Download', 'nextgen-gallery-pro');
        $i18n->download_remove = __('Remove', 'nextgen-gallery-pro');
        return $i18n;
    }
    function _render_category_header_template($id, $title)
    {
        return "<h3><span id='{$id}_header'>{$title}</span></h3><div class='nggpl-category_contents' id='{$id}'></div>";
    }
}
class C_NextGen_Pro_Cart
{
    protected $_parsing = FALSE;
    protected $_state = array();
    protected $_subtotal = NULL;
    protected $_shipping = NULL;
    protected $_total = NULL;
    protected $_settings = array();
    protected $_saved = FALSE;
    protected $_discount = NULL;
    protected $_undiscounted_subtotal = NULL;
    protected $_shipments = NULL;
    protected $_shipping_methods = NULL;
    protected $_tax_info = NULL;
    protected $_error = NULL;
    /** @var null|C_Coupon */
    protected $_coupon = NULL;
    /**
     * C_NextGen_Pro_Cart constructor.
     * @param null|array $json
     * @param array $cart_settings
     * @param bool $force_saved Use ONLY when working with orders pulled from the database; this sets the saved status to TRUE to prevent errors when working with legacy (before the '_saved' attribute) orders.
     */
    function __construct($json = NULL, $cart_settings = array())
    {
        if ($cart_settings && is_array($cart_settings)) {
            $this->_settings = $cart_settings;
        }
        $this->_settings = $this->validate_setting($this->_settings);
        if ($json) {
            $this->_parse_state($json);
        }
        $this->recalculate();
    }
    function is_saved_cart()
    {
        return $this->_saved;
    }
    function to_array()
    {
        $this->recalculate();
        // will only occur on non-saved carts
        $retval = array('images' => array(), 'image_ids' => array(), 'subtotal' => $this->get_subtotal(), 'shipments' => $this->get_shipments(), 'shipping_methods' => array_values($this->get_shipping_methods()), 'shipping' => $this->get_shipping(), 'total' => $this->get_total(), 'tax_info' => $this->get_tax_info(), 'tax' => $this->get_tax(), 'tax_enable' => $this->is_tax_enabled(), 'tax_rate' => $this->get_default_tax_rate(), 'has_shippable_items' => $this->has_shippable_items(), 'undiscounted_subtotal' => $this->get_undiscounted_subtotal(), 'settings' => $this->_settings, 'error' => $this->_error, 'currency' => $this->get_currency());
        foreach ($this->_state as $image_id => $image) {
            $image = clone $image;
            $items = $image->items;
            $image->item_ids = array();
            $image->items = array();
            foreach ($items as $source => $items_array) {
                foreach ($items_array as $pricelist_id => $inner_items_array) {
                    foreach ($inner_items_array as $item_id => $item) {
                        $image->item_ids[] = $item_id;
                        $image->items[$item_id] = $item;
                    }
                }
            }
            $retval['images'][$image_id] = $image;
            $retval['image_ids'][] = $image_id;
        }
        if (isset($this->_coupon) && ($discount = $this->get_discount())) {
            $retval['coupon'] = $this->_coupon;
            $retval['coupon']['discount_given'] = $discount;
        }
        return $retval;
    }
    function recalculate($force = FALSE)
    {
        if ($this->has_items() && ($force || !$this->is_saved_cart() && !$this->_parsing)) {
            $this->_intl_shipping = C_NextGen_Settings::get_instance()->ecommerce_intl_shipping;
            $this->_tax_enable = C_NextGen_Settings::get_instance()->get('ecommerce_tax_enable', FALSE);
            $this->_tax_rate = C_NextGen_Settings::get_instance()->get('ecommerce_tax_rate');
            $this->_tax_info = $this->_calculate_tax();
            $this->_discount = $this->_calculate_discount();
            $this->_undiscounted_subtotal = $this->_calculate_undiscounted_subtotal();
            $this->_subtotal = $this->_calculate_subtotal();
            $this->_shipments = $this->_calculate_shipments();
            $this->_shipping_methods = $this->_calculate_shipping_methods();
            $this->_settings['shipping_method'] = $this->_calculate_selected_shipping_method();
            $this->_shipments = $this->_calculate_shipments();
            $this->_shipping = $this->_calculate_shipping();
            try {
                // Prevent multiple tax calculations being done
                if (NULL === $this->_tax_info || !is_object($this->_tax_info)) {
                    $this->_tax_info = $this->_calculate_tax();
                }
            } catch (RuntimeException $exception) {
                $this->_error = $exception->getMessage();
                $this->_tax_info = new stdClass();
                $this->_tax_info->amount_to_collect = 0.0;
            }
            // _calculate_total() depends on and thus must follow _calculate_tax()
            $this->_total = $this->_calculate_total();
        }
    }
    function get_settings()
    {
        return $this->_settings;
    }
    function get_setting($key, $default = NULL)
    {
        return array_key_exists($key, $this->_settings) ? $this->_settings[$key] : $default;
    }
    /**
     * @param array $settings
     * @param array $overrides (optional)
     * @return array
     */
    function validate_setting($settings, $overrides = array())
    {
        $ngg_settings = C_NextGen_Settings::get_instance();
        if (!is_array($settings)) {
            $settings = array();
        }
        // Override fields
        if ($overrides) {
            foreach ($overrides as $key => $value) {
                if ($value) {
                    $settings[$key] = $value;
                }
            }
        }
        // Set default values for all shipping address fields
        foreach (array('shipping_address', 'studio_address') as $address_key) {
            if (!isset($settings[$address_key]) || !is_array($settings[$address_key])) {
                $settings[$address_key] = array();
            }
            foreach (array('name', 'street_address', 'address_line', 'city', 'state', 'zip', 'country') as $field) {
                // Ensure there's no whitespace or padding
                if (!empty($settings[$address_key][$field])) {
                    $settings[$address_key][$field] = trim(strip_tags($settings[$address_key][$field]));
                }
                if (empty($settings[$address_key][$field])) {
                    if ($address_key == 'studio_address') {
                        switch ($field) {
                            case 'name':
                                $settings[$address_key][$field] = $ngg_settings->get('ecommerce_studio_name');
                                break;
                            case 'street_address':
                                $settings[$address_key][$field] = $ngg_settings->get('ecommerce_studio_street_address');
                                break;
                            case 'address_line':
                                $settings[$address_key][$field] = $ngg_settings->get('ecommerce_studio_address_line');
                                break;
                            case 'city':
                                $settings[$address_key][$field] = $ngg_settings->get('ecommerce_studio_city');
                                break;
                            case 'state':
                                $settings[$address_key][$field] = $ngg_settings->get('ecommerce_home_state');
                                break;
                            case 'zip':
                                $settings[$address_key][$field] = $ngg_settings->get('ecommerce_home_zip');
                                break;
                            case 'email':
                                $settings[$address_key][$field] = M_NextGen_Pro_Ecommerce::get_studio_email_address();
                                break;
                            case 'country':
                                // Get country
                                if ($country_code = $ngg_settings->get('ecommerce_home_country')) {
                                    $settings[$address_key][$field] = $country_code;
                                } else {
                                    $settings[$address_key][$field] = '';
                                }
                                break;
                        }
                    } else {
                        if ($field == 'country' && ($country_code = $ngg_settings->get('ecommerce_home_country'))) {
                            $settings[$address_key][$field] = $country_code;
                        } else {
                            $settings[$address_key][$field] = '';
                        }
                    }
                }
            }
            if (!isset($settings['studio_address']['email'])) {
                $settings['studio_address']['email'] = M_NextGen_Pro_Ecommerce::get_studio_email_address();
            }
            if (isset($settings['saved'])) {
                $this->_saved = TRUE;
            }
        }
        return $settings;
    }
    /**
     * Simplified state to represent the cart:
     * array(
     *  'images'                        =>  array(
     *          1 (image_id)            =>  array(
     *              'items'             =>  array(
     *                  1 (item_id)     =>  array(
     *                      'quantity'  =>  2
     *                  )
     *              ),
     *              'item_ids'          =>  array(
     *                  1 (item_id)
     *              )
     *          )
     *  ),
     *  'image_ids'                     =>  array(
     *          1 (image_id)
     *  )
     * )
     * @var array $client_state
     * @var bool $force_saved When TRUE this will force $this->_saved to TRUE
     */
    function _parse_state($client_state)
    {
        $this->_parsing = TRUE;
        // Restore cached values so that we don't have calculate this stuff over and over
        foreach (array('undiscounted_subtotal', 'subtotal', 'shipping', 'total', 'settings', 'saved', 'discount', 'coupon', 'tax_info', 'tax_rate', 'intl_shipping', 'currency', 'error', 'shipments') as $param) {
            if (isset($client_state[$param])) {
                $key = "_{$param}";
                $this->{$key} = $client_state[$param];
            }
        }
        // Backwards compatbility - before print lab, we only had tax, not tax info
        if (isset($client_state['tax']) && !isset($client_state['tax_info'])) {
            $this->_tax_info = new stdClass();
            $this->_tax_info->amount_to_collect = floatval($client_state['tax']);
        }
        // Ensure that tax info is an object
        if (is_array($this->_tax_info)) {
            $this->_tax_info = $this->_arr_to_object($this->_tax_info);
        }
        // Ensure that shipments are objects, not arrays
        if ($this->_shipments) {
            foreach ($this->_shipments as $source => $shipments) {
                $this->_shipments[$source] = array_map(array($this, '_arr_to_object'), $shipments);
            }
        }
        if (isset($client_state['images']) and is_array($client_state['images'])) {
            foreach ($client_state['images'] as $image_id => $image_props) {
                $this->add_image($image_id, $image_props);
            }
        }
        if (!$this->is_saved_cart()) {
            $code = NULL;
            if (!empty($client_state['coupon'])) {
                $code = is_array($client_state['coupon']) ? $client_state['coupon']['code'] : $client_state['coupon'];
            }
            $this->apply_coupon($code);
        }
        $this->_parsing = FALSE;
    }
    function apply_coupon($code = NULL)
    {
        if (M_NextGen_Pro_Coupons::are_coupons_enabled() && !empty($code) && ($coupon = C_Coupon_Mapper::get_instance()->find_by_code($code, TRUE))) {
            if ($coupon->validate_current_availability()) {
                $this->_coupon = $coupon->get_limited_entity();
            } else {
                $this->_coupon = NULL;
            }
        } else {
            $this->_coupon = NULL;
        }
    }
    function add_items($items = array())
    {
        if (!is_array($items)) {
            return;
        }
        foreach ($items as $image_id => $image_items) {
            $this->add_image($image_id, array('items' => $image_items));
        }
    }
    function has_items()
    {
        return count($this->_state) ? TRUE : FALSE;
    }
    function get_exif_orientation($image, $size = 'full')
    {
        $storage = C_Gallery_Storage::get_instance();
        // This method is necessary
        if (!function_exists('exif_read_data')) {
            return;
        }
        // We only need to continue if the Orientation tag is set
        $exif = @exif_read_data($storage->get_image_abspath($image, $size), 'exif');
        if (empty($exif['Orientation']) || $exif['Orientation'] == 1) {
            return;
        }
        $degree = FALSE;
        if ($exif['Orientation'] == 3) {
            $degree = 180;
        }
        if ($exif['Orientation'] == 6) {
            $degree = 90;
        }
        if ($exif['Orientation'] == 8) {
            $degree = 270;
        }
        return $degree;
    }
    /**
     * Gets an image from the cart or DB
     */
    function get_image($image_id, $fallback = NULL)
    {
        $image = isset($this->_state[$image_id]) ? $this->_state[$image_id] : FALSE;
        if (!$image) {
            $mapper = C_Image_Mapper::get_instance();
            if ($image = $mapper->find($image_id)) {
                $storage = C_Gallery_Storage::get_instance();
                $rotation = $this->get_exif_orientation($image);
                $dynthumbs = C_Dynamic_Thumbnails_Manager::get_instance();
                $params = array('width' => 200, 'height' => 200, 'crop' => FALSE, 'watermark' => FALSE, 'rotation' => $rotation, 'quality' => 100);
                $thumb_size = $dynthumbs->get_size_name($params);
                $params = array('width' => 1024, 'height' => 1024, 'crop' => FALSE, 'rotation' => $rotation);
                $crop_size = $dynthumbs->get_size_name($params);
                $image->thumbnail_url = $storage->get_image_url($image, $thumb_size);
                $image->dimensions = $storage->get_image_dimensions($image, $thumb_size);
                $image->width = $image->dimensions['width'];
                $image->height = $image->dimensions['height'];
                $image->full_url = $storage->get_full_url($image);
                $image->full_dimensions = $storage->get_full_dimensions($image);
                $image->exif_orientation = $rotation;
                $image->crop_url = $storage->get_image_url($image, $crop_size);
                $image->crop_dimensions = $storage->get_image_dimensions($image, $crop_size);
                $image->ecommerce_size = isset($image->meta_data['backup']) ? 'backup' : 'full';
                $image->md5 = $this->_get_image_checksum($image, $image->ecommerce_size);
                $image->url = $storage->get_image_url($image, $image->ecommerce_size);
                $image->items = array();
            } else {
                $image = $fallback ? (object) $fallback : NULL;
            }
        }
        return $image;
    }
    function get_item($image_id, $item_id, $fallback = NULL)
    {
        $item = FALSE;
        if (!$this->has_image($image_id)) {
            $this->add_image($image_id, array());
        }
        if ($image = $this->get_image($image_id)) {
            // First try to retrieve the item from the cart
            if (isset($image->items)) {
                foreach ($image->items as $source_id => $pricelist_to_items) {
                    foreach ($pricelist_to_items as $pricelist_id => $items) {
                        if (isset($items[$item_id])) {
                            $item = $items[$item_id];
                            break;
                        }
                    }
                    if ($item) {
                        break;
                    }
                }
            }
            // If no item was found in the cart, then look it up using the mapper
            if (!$item) {
                $mapper = C_Pricelist_Item_Mapper::get_instance();
                if ($item = $mapper->find($item_id)) {
                    $source = C_Pricelist_Source_Manager::get_instance()->get_handler($item->source);
                    $item->quantity = 0;
                    $item->shippable_to = $source->get_shippable_countries();
                    $item->crop_offset = $this->_calculate_item_crop_offset($image_id, $item);
                    if (!$item->crop_offset) {
                        $crop_offset = '';
                    }
                } else {
                    $item = $fallback ? (object) $fallback : NULL;
                }
            }
        }
        return $item;
    }
    function has_image($image_id)
    {
        return isset($this->_state[$image_id]);
    }
    function add_image($image_id, $image_props)
    {
        if ($image = $this->get_image($image_id, $image_props)) {
            // Get items from image props.
            $image_props = is_object($image_props) ? get_object_vars($image_props) : $image_props;
            $items = isset($image_props['items']) ? $image_props['items'] : array();
            // Persist the image to the cart
            foreach ($image_props as $key => $val) {
                $image->{$key} = $val;
            }
            unset($image->items);
            $this->_state[$image_id] = $image;
            // Add items associated with the image
            foreach ($items as $item_id => $item_props) {
                if (is_numeric($item_id)) {
                    $this->add_item($image_id, $item_id, $item_props);
                }
            }
            $this->recalculate();
        }
    }
    function _calculate_item_crop_offset($image_id, $item)
    {
        if ($image = $this->get_image($image_id)) {
            // calculate default crop_offset
            $full_dimensions = isset($image->crop_dimensions) ? $image->crop_dimensions : NULL;
            if ($full_dimensions != null && isset($item->source_data['lab_properties'])) {
                $print_ratio = $item->source_data['lab_properties']['aspect']['ratio'];
                $image_ratio = $full_dimensions['width'] / $full_dimensions['height'];
                if ($print_ratio != 0 && $image_ratio != 0) {
                    if ($print_ratio > 1 && $image_ratio < 1 || $print_ratio < 1 && $image_ratio > 1) {
                        $print_ratio = 1 / $print_ratio;
                    }
                    $ratio_diff = $image_ratio - $print_ratio;
                    if ($ratio_diff < 0) {
                        $crop_width = $full_dimensions['width'];
                        $crop_height = $crop_width / $print_ratio;
                    } else {
                        $crop_height = $full_dimensions['height'];
                        $crop_width = $crop_height * $print_ratio;
                    }
                    $crop_x = ($full_dimensions['width'] - $crop_width) / 2;
                    $crop_y = ($full_dimensions['height'] - $crop_height) / 2;
                    return sprintf('%d,%d,%d,%d', $crop_x, $crop_y, $crop_x + $crop_width, $crop_y + $crop_height);
                }
            }
        }
        return NULL;
    }
    /**
     * Returns the checksum of the image
     * @param $image
     * @param string $size
     * @return null|string
     */
    function _get_image_checksum($image, $size = 'full')
    {
        $storage = C_Gallery_Storage::get_instance();
        $retval = NULL;
        if ($storage->has_method('get_image_checksum')) {
            $retval = $storage->get_image_checksum($image, $size);
        } else {
            if ($image_abspath = $storage->get_image_abspath($image, $size, TRUE)) {
                $retval = md5_file($image_abspath);
            }
        }
        return $retval;
    }
    function add_item($image_id, $item_id, $item_props = array())
    {
        $image = $this->get_image($image_id);
        if ($image && ($item = $this->get_item($image_id, $item_id, $item_props))) {
            // Treat an object as if it were an array
            if (is_object($item_props)) {
                $item_props = get_object_vars($item_props);
            }
            // Ensure that the items source key exists as an array
            if (!isset($image->items[$item->source])) {
                $image->items[$item->source] = array();
            }
            // Ensure that the item's pricelist id exists as a key in the array
            if (!isset($image->items[$item->source][$item->pricelist_id])) {
                $image->items[$item->source][$item->pricelist_id] = array();
            }
            // Append item props
            foreach ($item_props as $key => $val) {
                if ($key == 'quantity') {
                    $val = intval($val);
                }
                $item->{$key} = $val;
            }
            // Assure that the quantity has been provided
            if (!isset($item->quantity)) {
                $item->quantity = 1;
            }
            // Persist
            $image->items[$item->source][$item->pricelist_id][$item_id] = $item;
            $this->_state[$image_id] = $image;
            $this->recalculate();
            return TRUE;
        }
        return FALSE;
    }
    function has_international_shipping_rate()
    {
        $intl_shipping = isset($this->_intl_shipping) ? $this->_intl_shipping : C_NextGen_Settings::get_instance()->get('ecommerce_intl_shipping', '');
        return intl_shipping != '' && intl_shipping != 'disabled';
    }
    function get_images($with_items = FALSE)
    {
        $retval = array();
        foreach (array_values($this->_state) as $image) {
            $i = clone $image;
            if (!$with_items) {
                unset($i->items);
            }
            $retval[] = $i;
        }
        return $retval;
    }
    function get_items($source = NULL)
    {
        $retval = array();
        foreach (array_values($this->_state) as $image) {
            $items = is_array($image) ? $image['items'] : $image->items;
            foreach ($items as $source_id => $pricelists) {
                foreach ($pricelists as $pricelist_id => $items) {
                    foreach ($items as $item_id => $item) {
                        if (!$source || $item->source == $source) {
                            $i = clone $item;
                            $i->image = clone $image;
                            $retval[] = $i;
                        }
                    }
                }
            }
        }
        return $retval;
    }
    function is_tax_enabled()
    {
        return isset($this->_tax_enable) ? $this->_tax_enable : (bool) C_NextGen_Settings::get_instance()->get('ecommerce_tax_enable', FALSE);
    }
    function get_default_tax_rate()
    {
        return isset($this->_tax_rate) ? $this->_tax_rate : C_NextGen_Settings::get_instance()->get('ecommerce_tax_rate');
    }
    /**
     * Determines if the cart has digital downloads
     * @return bool
     */
    function has_digital_downloads()
    {
        $retval = FALSE;
        foreach ($this->_state as $image_id => $image) {
            foreach ($image->items as $source => $pricelists) {
                foreach ($pricelists as $pricelist_id => $items) {
                    foreach ($items as $item) {
                        if ($item->source == NGG_PRO_DIGITAL_DOWNLOADS_SOURCE) {
                            $retval = TRUE;
                            break;
                        }
                    }
                }
            }
        }
        return $retval;
    }
    /**
     * @return int|float
     */
    function _calculate_discount()
    {
        $currency = C_NextGen_Pro_Currencies::$currencies[$this->get_currency()];
        if (!empty($this->_coupon)) {
            $coupon = NULL;
            $id = NULL;
            if (is_string($this->_coupon)) {
                $id = $this->_coupon;
            } else {
                if (is_array($this->_coupon) && isset($this->_coupon['code'])) {
                    $id = $this->_coupon['code'];
                }
            }
            $coupon = C_Coupon_Mapper::get_instance()->find_by_code($id, TRUE);
            if ($coupon) {
                if (!$coupon->validate_current_availability()) {
                    return 0;
                }
                return $coupon->get_discount_amount($this->get_undiscounted_subtotal(), $currency['exponent']);
            }
        }
        return 0;
    }
    function prepare_for_persistence()
    {
        $this->_saved = TRUE;
    }
    function get_discount()
    {
        if (!$this->_discount) {
            $this->_discount = $this->_calculate_discount();
        }
        return $this->_discount;
    }
    /**
     * @return C_Coupon|null
     */
    public function get_coupon()
    {
        if (!$this->_coupon) {
            return NULL;
        }
        return $this->_coupon;
    }
    function get_currency()
    {
        return isset($this->_currency) ? $this->_currency : C_NextGen_Settings::get_instance()->get('ecommerce_currency');
    }
    /**
     * Gets the subtotal of all items in the cart
     *
     * @return float
     */
    function _calculate_subtotal()
    {
        $currency = C_NextGen_Pro_Currencies::$currencies[$this->get_currency()];
        $total = bcsub($this->get_undiscounted_subtotal(), $this->get_discount(), $currency['exponent']);
        if ($total < 0.0) {
            $total = 0;
        }
        return $total;
    }
    function get_subtotal()
    {
        if ($this->_subtotal === NULL) {
            $this->_subtotal = $this->_calculate_subtotal();
        }
        return $this->_subtotal;
    }
    function get_undiscounted_subtotal()
    {
        if ($this->_undiscounted_subtotal === NULL) {
            $this->_undiscounted_subtotal = $this->_calculate_undiscounted_subtotal();
        }
        return $this->_undiscounted_subtotal;
    }
    function _calculate_undiscounted_subtotal()
    {
        $retval = 0;
        $settings = C_NextGen_Settings::get_instance();
        $currency = C_NextGen_Pro_Currencies::$currencies[$this->get_currency()];
        foreach ($this->_state as $image_id => $image) {
            foreach ($image->items as $source => $pricelists) {
                foreach ($pricelists as $pricelist_id => $items) {
                    foreach ($items as $item_id => $item) {
                        $retval = bcadd($retval, round(bcmul($item->price, $item->quantity, intval($currency['exponent']) * 2), $currency['exponent'], PHP_ROUND_HALF_UP), $currency['exponent']);
                    }
                }
            }
        }
        return $retval;
    }
    /**
     * Returns all items sorted by pricelist and then source
     */
    function get_sorted_items()
    {
        $retval = array();
        foreach (array_values($this->_state) as $image) {
            $items = is_array($image) ? $image['items'] : $image->items;
            foreach ($items as $source => $pricelists) {
                foreach ($pricelists as $pricelist_id => $items) {
                    foreach ($items as $item) {
                        if (!isset($retval[$pricelist_id])) {
                            $retval[$pricelist_id] = array();
                        }
                        if (!isset($retval[$pricelist_id][$source])) {
                            $retval[$pricelist_id][$source] = array();
                        }
                        $i = clone $item;
                        $i->image = clone $image;
                        $retval[$pricelist_id][$source][] = $i;
                    }
                }
            }
        }
        return $retval;
    }
    function _calculate_shipments()
    {
        $retval = array();
        $source_manager = C_Pricelist_Source_Manager::get_instance();
        foreach ($source_manager->get_ids() as $source_id) {
            $items = $this->get_items($source_id);
            $source = $source_manager->get_handler($source_id);
            if ($shipments = $source->get_shipments($items, $this->_settings, $this)) {
                $retval[$source_id] = array_map(array($this, '_arr_to_object'), $shipments);
            }
        }
        return $retval;
    }
    function get_shipments()
    {
        if ($this->_shipments === NULL) {
            $this->_shipments = $this->_calculate_shipments();
        }
        return $this->_shipments;
    }
    function _arr_to_object($arr)
    {
        return (object) $arr;
    }
    function _add_to_inner_array(&$arr, $key, $value)
    {
        if (!isset($arr[$key])) {
            $arr[$key] = array($value);
        } else {
            $arr[$key][] = $value;
        }
    }
    function _get_universal_shipping_methods()
    {
        $retval = array();
        $manager = C_Pricelist_Shipping_Method_Manager::get_instance();
        foreach ($this->get_shipments() as $source_id => $shipments) {
            foreach ($shipments as $shipment) {
                foreach ($shipment->shipping_methods as $shipping_method_id => $shipping_method) {
                    if ($manager->is_universal_method($shipping_method_id)) {
                        $this->_add_to_inner_array($retval, $shipping_method_id, $shipping_method);
                    }
                }
            }
        }
        return $retval;
    }
    /**
     * Gets all shipping methods which are common to all shipments
     *
     * @return array
     */
    function _get_common_shipping_methods()
    {
        $manager = C_Pricelist_Shipping_Method_Manager::get_instance();
        // Find common shipping methods
        $common_methods = array();
        foreach ($manager->get_ids() as $shipping_method_id) {
            $common = TRUE;
            foreach ($this->get_shipments() as $source_id => $shipments) {
                // Iterate over all shipments for that source
                foreach ($shipments as $shipment) {
                    $found = FALSE;
                    foreach ($shipment->shipping_methods as $id => $underlying_shipping_method) {
                        if ($id == $shipping_method_id) {
                            $found = TRUE;
                            break;
                        } else {
                            if ($manager->is_universal_method($id)) {
                                $found = TRUE;
                            }
                        }
                    }
                    if (!$found) {
                        $common = FALSE;
                    }
                    if (!$common) {
                        break;
                    }
                }
                if (!$common) {
                    break;
                }
            }
            // If the method is common, we need to get the underlying shipping method
            // for each shipment
            if ($common) {
                $common_methods[$shipping_method_id] = array();
                // Get all shipments per source
                foreach ($this->get_shipments() as $source_id => $shipments) {
                    // Iterate over all shipments for that source
                    foreach ($shipments as $shipment) {
                        if (isset($shipment->shipping_methods[$shipping_method_id])) {
                            $common_methods[$shipping_method_id][] = $shipment->shipping_methods[$shipping_method_id];
                        }
                    }
                }
            }
            // Remove shipping methods which have no underlying methods
            foreach (array_keys($common_methods) as $common_method_id) {
                if (!$common_methods[$common_method_id]) {
                    unset($common_methods[$common_method_id]);
                }
            }
        }
        return $common_methods;
    }
    function _combine_shipping_methods($shipping_method_id, $title, $shipping_methods)
    {
        $amount = 0.0;
        $data = array();
        $settings = C_NextGen_Settings::get_instance();
        $currency = C_NextGen_Pro_Currencies::$currencies[$settings->get('ecommerce_currency')];
        // It's possible my development system is broken somehow, but the POPE autoloader just isn't working for this one file for me (B.Owens)
        if (!class_exists('C_NextGen_Pro_WHCC_DAS_Prices')) {
            require_once 'class.nextgen_das_prices.php';
        }
        foreach ($shipping_methods as $method_details) {
            // Combine data
            if (isset($method_details['data']) && $method_details['data']) {
                $data = array_merge($data, $method_details['data']);
            }
            // Combine additional delivery area surcharges
            if (isset($method_details['shipping_type']) && $method_details['shipping_type']) {
                $das_arrays = ['das' => C_NextGen_Pro_WHCC_DAS_Prices::$das, 'extended' => C_NextGen_Pro_WHCC_DAS_Prices::$extended, 'remote' => C_NextGen_Pro_WHCC_DAS_Prices::$remote, 'hawaai' => C_NextGen_Pro_WHCC_DAS_Prices::$hawaii, 'alaska' => C_NextGen_Pro_WHCC_DAS_Prices::$alaska];
                foreach ($das_arrays as $label => $region) {
                    if (in_array($this->_settings['shipping_address']['zip'], $region)) {
                        if ($method_details['shipping_type'] === 'whcc_parcel') {
                            $amount = bcadd($amount, C_NextGen_Pro_WHCC_DAS_Prices::$parcel_carrier_costs[$label], $currency['exponent']);
                        } elseif ($method_details['shipping_type'] === 'whcc_mail') {
                            $amount = bcadd($amount, C_NextGen_Pro_WHCC_DAS_Prices::$mail_carrier_costs[$label], $currency['exponent']);
                        }
                    }
                }
            }
            // Combine price
            if (isset($method_details['price']) && $method_details['price']) {
                $amount = bcadd($amount, $method_details['price'], $currency['exponent']);
            }
            if (isset($method_details['amount']) && $method_details['amount']) {
                $amount = bcadd($amount, $method_details['amount'], $currency['exponent']);
            }
            // Combine surcharges
            if (isset($method_details['surcharge']) && $method_details['surcharge']) {
                $amount = bcadd($amount, $method_details['surcharge'], $currency['exponent']);
            }
            if (isset($method_details['other_surcharge']) && $method_details['other_surcharge']) {
                $amount = bcadd($amount, $method_details['other_surcharge'], $currency['exponent']);
            }
        }
        return array('name' => $shipping_method_id, 'title' => $title, 'amount' => $amount, 'underlying_methods' => $shipping_methods, 'data' => $data);
    }
    function has_shipments_with_no_methods()
    {
        $retval = FALSE;
        foreach ($this->get_shipments() as $source_id => $shipments) {
            foreach ($shipments as $shipment) {
                if (!count($shipment->shipping_methods)) {
                    $retval = TRUE;
                }
            }
        }
        return $retval;
    }
    function get_shipping_methods()
    {
        if ($this->_shipping_methods === NULL) {
            $this->_shipping_methods = $this->_calculate_shipping_methods();
        }
        return $this->_shipping_methods;
    }
    /**
     * Returns a list of all shipping methods compatible with the cart
     * @param array $settings
     * @return array
     */
    function _calculate_shipping_methods()
    {
        $manager = C_Pricelist_Shipping_Method_Manager::get_instance();
        $retval = array();
        /*
                {
           ngg_manual_shipping: [
               {
                   alias => 'Manual Domestic Shipping for Pricelist 5',
                   price => 5.00,
                   surcharge => 0.00,
                   other_surcharge => 0.00,
                   source => 'ngg_manual_pricelist'
               },
               {
                   alias => 'Manual Domestic Shipping for Pricelist 8',
                   price => 15.00,
                   surcharge => 0.00,
                   other_surcharge => 5.00,
                   source => 'ngg_manual_pricelist'
               },
           ],
        
           ngg_international_shipping: [
               {
                   alias => 'WHCC International Shipping',
                   price => 10.00,
                   surcharge => 0.00,
                   other_surcharge => 5.00,
                   source => 'ngg_whcc_pricelist'
               },
           ]
        		}
        */
        $common_methods = $this->_get_common_shipping_methods();
        // Segment universal shipping methods from common methods
        $universal_methods = $this->_get_universal_shipping_methods();
        foreach (array_keys($common_methods) as $shipping_method_id) {
            if ($manager->is_universal_method($shipping_method_id)) {
                unset($common_methods[$shipping_method_id]);
            }
        }
        // If there are only universal shipping methods, treat them as common methods
        if (!$common_methods && !$this->has_shipments_with_no_methods()) {
            $common_methods = array_merge($common_methods, $universal_methods);
            $universal_methods = array();
        }
        // Combine common methods
        foreach ($common_methods as $shipping_method_id => $underyling_methods) {
            $retval[$shipping_method_id] = $this->_combine_shipping_methods($shipping_method_id, $manager->get($shipping_method_id, 'title'), $underyling_methods);
        }
        // Add/combine universal methods to each common method
        if ($universal_methods) {
            $universal_methods = array_values($universal_methods);
            $universal_methods = array_pop($universal_methods);
            foreach (array_keys($common_methods) as $shipping_method_id) {
                $retval[$shipping_method_id] = $this->_combine_shipping_methods($shipping_method_id, $manager->get($shipping_method_id, 'title'), array_merge(array($retval[$shipping_method_id]), $universal_methods), TRUE);
            }
        }
        return $retval;
    }
    function _get_least_expensive_shipping_method($cart_settings = array(), $shipping_methods = array())
    {
        $cart_settings = $this->validate_setting($this->_settings, $cart_settings);
        if (!$shipping_methods) {
            $shipping_methods = $this->get_shipping_methods($cart_settings);
        }
        $amount = PHP_INT_MAX;
        $least_expensive = NULL;
        foreach ($shipping_methods as $shipping_method_name => $properties) {
            if ($properties['amount'] < $amount) {
                $least_expensive = $shipping_method_name;
                $amount = $properties['amount'];
            }
        }
        return $least_expensive;
    }
    function get_selected_shipping_method()
    {
        if (!array_key_exists('shipping_method', $this->_settings) or $this->_settings['shipping_method'] === NULL) {
            $this->_settings['shipping_method'] = $this->_calculate_selected_shipping_method();
        }
        return $this->_settings['shipping_method'];
    }
    function _calculate_selected_shipping_method()
    {
        $cart_settings = $this->_settings;
        $selected_method = isset($cart_settings['shipping_method']) ? $cart_settings['shipping_method'] : NULL;
        if ($this->_shipping_methods && !$selected_method || $this->_shipping_methods && !isset($this->_shipping_methods[$selected_method])) {
            $selected_method = $this->_get_least_expensive_shipping_method();
        }
        return $selected_method;
    }
    function get_shipping()
    {
        if ($this->_shipping === NULL) {
            $this->_shipping = $this->_calculate_shipping();
        }
        return $this->_shipping;
    }
    function _calculate_shipping()
    {
        $retval = 0.0;
        $shipping_methods = $this->get_shipping_methods();
        $selected_method = $this->get_selected_shipping_method();
        if ($selected_method && isset($shipping_methods[$selected_method])) {
            $shipping_method = $shipping_methods[$selected_method];
            $retval = $shipping_method['amount'];
        }
        return $retval;
    }
    function get_total()
    {
        if ($this->_total === NULL) {
            $this->_total = $this->_calculate_total();
        }
        return $this->_total;
    }
    public function get_total_array()
    {
        return $this->_calculate_total(TRUE);
    }
    function _calculate_total($return_array = FALSE)
    {
        $currency = C_NextGen_Pro_Currencies::$currencies[$this->get_currency()];
        $subtotal = $this->get_subtotal();
        // includes discount
        $total = $subtotal;
        $taxes = 0.0;
        if ($this->is_tax_enabled()) {
            $taxes = $this->get_tax();
            $total = bcadd($total, $taxes, $currency['exponent']);
        }
        $shipping = $this->get_shipping();
        $total = bcadd($shipping, $total, $currency['exponent']);
        if (!$return_array) {
            return $total;
        } else {
            return array('discount' => $this->get_discount(), 'shipping' => $shipping, 'subtotal' => $subtotal, 'taxes' => $taxes, 'total' => $total);
        }
    }
    function get_tax()
    {
        return $this->get_tax_info()->amount_to_collect;
    }
    function get_tax_info()
    {
        if ($this->_tax_info === NULL) {
            $this->_tax_info = $this->_calculate_tax();
        }
        return $this->_tax_info;
    }
    function _calculate_tax()
    {
        $retval = new stdClass();
        $retval->amount_to_collect = 0.0;
        $cart_settings = $this->_settings;
        if ($this->is_tax_enabled() && $this->has_shippable_items() && $cart_settings['shipping_address']['country'] && $cart_settings['shipping_address']['state']) {
            $ecommerce_status = M_NextGen_Pro_Ecommerce::check_ecommerce_requirements();
            $has_print_lab = isset($ecommerce_status['print_lab_ready']) ? $ecommerce_status['print_lab_ready'] : FALSE;
            if (!defined('NGG_PRO_USE_WHCC_NEXUS')) {
                define('NGG_PRO_USE_WHCC_NEXUS', $has_print_lab);
            }
            $default_nexus = defined('NGG_PRO_USE_WHCC_NEXUS') && constant('NGG_PRO_USE_WHCC_NEXUS') ? array(array('country' => 'US', 'state' => 'AL'), array('country' => 'US', 'state' => 'AZ'), array('country' => 'US', 'state' => 'CA'), array('country' => 'US', 'state' => 'CO'), array('country' => 'US', 'state' => 'CT'), array('country' => 'US', 'state' => 'FL'), array('country' => 'US', 'state' => 'GA'), array('country' => 'US', 'state' => 'ID'), array('country' => 'US', 'state' => 'IL'), array('country' => 'US', 'state' => 'IN'), array('country' => 'US', 'state' => 'IA'), array('country' => 'US', 'state' => 'KS'), array('country' => 'US', 'state' => 'KY'), array('country' => 'US', 'state' => 'LA'), array('country' => 'US', 'state' => 'MD'), array('country' => 'US', 'state' => 'MA'), array('country' => 'US', 'state' => 'MI'), array('country' => 'US', 'state' => 'MO'), array('country' => 'US', 'state' => 'MN'), array('country' => 'US', 'state' => 'NJ'), array('country' => 'US', 'state' => 'NY'), array('country' => 'US', 'state' => 'NC'), array('country' => 'US', 'state' => 'OH'), array('country' => 'US', 'state' => 'OK'), array('country' => 'US', 'state' => 'PA'), array('country' => 'US', 'state' => 'SC'), array('country' => 'US', 'state' => 'TN'), array('country' => 'US', 'state' => 'TX'), array('country' => 'US', 'state' => 'UT'), array('country' => 'US', 'state' => 'WA'), array('country' => 'US', 'state' => 'WI'), array('country' => 'US', 'state' => 'VA'), array('country' => 'CA', 'state' => 'AB'), array('country' => 'CA', 'state' => 'BC'), array('country' => 'CA', 'state' => 'MB'), array('country' => 'CA', 'state' => 'NB'), array('country' => 'CA', 'state' => 'NL'), array('country' => 'CA', 'state' => 'NS'), array('country' => 'CA', 'state' => 'NT'), array('country' => 'CA', 'state' => 'NU'), array('country' => 'CA', 'state' => 'ON'), array('country' => 'CA', 'state' => 'PE'), array('country' => 'CA', 'state' => 'QC'), array('country' => 'CA', 'state' => 'SK'), array('country' => 'CA', 'state' => 'YT')) : array();
            $nexus = apply_filters('ngg_pro_taxjar_nexus', $default_nexus);
            $taxjar_params = array('license_key' => M_NextGen_Pro_Ecommerce::get_license('photocrati-nextgen-pro'), 'from' => $cart_settings['studio_address'], 'to' => $cart_settings['shipping_address'], 'amount' => $this->get_subtotal($cart_settings), 'shipping' => $this->get_shipping($cart_settings));
            if ($nexus) {
                $taxjar_params['nexus'] = $nexus;
            }
            $body = json_encode(apply_filters('ngg_pro_taxjar_params', $taxjar_params));
            $response = wp_remote_post('https://xta4y4f2g4.execute-api.us-east-1.amazonaws.com/latest/getTaxes', array('body' => $body, 'headers' => array('Content-Type' => 'application/json')));
            if (!is_wp_error($response)) {
                $response['body'] = json_decode($response['body']);
                if (isset($response['body']) && property_exists($response['body'], 'tax')) {
                    $retval = $response['body']->tax;
                } else {
                    if (isset($response['body']) && property_exists($response['body'], 'detail')) {
                        $error = FALSE;
                        switch ($response['body']->detail) {
                            case 'invalid_zip':
                                $error = __('Please enter a valid zip or postal code', 'nextgen-gallery-pro');
                                break;
                            case 'invalid_license':
                                $error = __('Please inform the studio that there is a licensing problem and that taxes cannot be calculated', 'nextgen-gallery-pro');
                                break;
                        }
                        if ($error) {
                            throw new RuntimeException($error);
                        }
                    }
                }
            }
        }
        return $retval;
    }
    /**
     * Determines if the cart has shippable items
     * @return bool
     */
    function has_shippable_items()
    {
        return $this->has_lab_items() || $this->has_manual_items();
    }
    function has_lab_items()
    {
        $retval = FALSE;
        $sources = C_Pricelist_Source_Manager::get_instance();
        foreach ($sources->get_ids() as $source_id) {
            if (count($this->get_items($source_id))) {
                if ($sources->get($source_id, 'lab_fulfilled')) {
                    $retval = TRUE;
                    break;
                }
            }
        }
        return $retval;
    }
    function has_manual_items()
    {
        return count($this->get_items(NGG_PRO_MANUAL_PRICELIST_SOURCE)) ? TRUE : FALSE;
    }
    function has_whcc_items()
    {
        return count($this->get_items(NGG_PRO_WHCC_PRICELIST_SOURCE)) ? TRUE : FALSE;
    }
}
/**
 * Class C_NextGen_Pro_Checkout
 * @mixin Mixin_NextGen_Pro_Checkout
 * @mixin A_PayPal_Checkout_Form
 * @mixin A_Stripe_Checkout_Button
 * @mixin A_PayPal_Standard_Button
 * @mixin A_PayPal_Express_Checkout_Button
 */
class C_NextGen_Pro_Checkout extends C_MVC_Controller
{
    static $_instance = NULL;
    /**
     * @return C_NextGen_Pro_Checkout
     */
    static function get_instance()
    {
        if (!self::$_instance) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    function define($context = FALSE)
    {
        parent::define();
        $this->implement('I_NextGen_Pro_Checkout');
        $this->add_mixin('Mixin_NextGen_Pro_Checkout');
    }
}
/**
 * @property C_NextGen_Pro_Checkout $object
 */
class Mixin_NextGen_Pro_Checkout extends Mixin
{
    /**
     * Adapters are expected to override to provide more payment gateway buttons
     * @return array
     */
    function get_checkout_buttons()
    {
        return array();
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->image_header = __('Image', 'nextgen-gallery-pro');
        $i18n->quantity_header = __('Quantity', 'nextgen-gallery-pro');
        $i18n->item_header = __('Description', 'nextgen-gallery-pro');
        $i18n->crop_button = __('Edit Crop', 'nextgen-gallery-pro');
        $i18n->crop_button_close = __('Save Crop', 'nextgen-gallery-pro');
        $i18n->price_header = __('Price', 'nextgen-gallery-pro');
        $i18n->total_header = __('Totals', 'nextgen-gallery-pro');
        $i18n->subtotal = __('Subtotal:', 'nextgen-gallery-pro');
        $i18n->shipping = __('Shipping:', 'nextgen-gallery-pro');
        $i18n->total = __('Total:', 'nextgen-gallery-pro');
        $i18n->no_items = __('There have been no items added to your cart.', 'nextgen-gallery-pro');
        $i18n->continue_shopping = __('Continue shopping', 'nextgen-gallery-pro');
        $i18n->empty_cart = __('Empty cart', 'nextgen-gallery-pro');
        $i18n->ship_to = __('Ship to:', 'nextgen-gallery-pro');
        $i18n->ship_via = __('Ship via:', 'nextgen-gallery-pro');
        $i18n->ship_elsewhere = __('International', 'nextgen-gallery-pro');
        $i18n->tax = __('Tax:', 'nextgen-gallery-pro');
        $i18n->update_shipping = __('Update shipping & taxes', 'nextgen-gallery-pro');
        $i18n->coupon_undiscounted_subtotal = __('Subtotal before discount:', 'nextgen-gallery-pro');
        $i18n->coupon_discount_amount = __('Discount:', 'nextgen-gallery-pro');
        $i18n->coupon_placeholder = __('Coupon code', 'nextgen-gallery-pro');
        $i18n->coupon_apply = __('Apply', 'nextgen-gallery-pro');
        $i18n->coupon_notice = __('Coupon has been applied', 'nextgen-gallery-pro');
        $i18n->shipping_name_label = __('Full Name', 'nextgen-gallery-pro');
        $i18n->shipping_name_tip = __('Full Name', 'nextgen-gallery-pro');
        $i18n->shipping_email_label = __('Email', 'nextgen-gallery-pro');
        $i18n->shipping_email_tip = __('Email', 'nextgen-gallery-pro');
        $i18n->shipping_street_address_label = __('Address Line 1', 'nextgen-gallery-pro');
        $i18n->shipping_street_address_tip = __('Address Line 1', 'nextgen-gallery-pro');
        $i18n->shipping_address_line_label = __('Address Line 2', 'nextgen-gallery-pro');
        $i18n->shipping_address_line_tip = __('Address Line 2', 'nextgen-gallery-pro');
        $i18n->shipping_city_label = __('City', 'nextgen-gallery-pro');
        $i18n->shipping_city_tip = __('City', 'nextgen-gallery-pro');
        $i18n->shipping_country_label = __('Country', 'nextgen-gallery-pro');
        $i18n->shipping_country_tip = __('Country', 'nextgen-gallery-pro');
        $i18n->shipping_state_label = __('State / Region', 'nextgen-gallery-pro');
        $i18n->shipping_state_tip = __('State / Region', 'nextgen-gallery-pro');
        $i18n->shipping_zip_label = __('Postal Code', 'nextgen-gallery-pro');
        $i18n->shipping_zip_tip = __('Zip / Postal Code', 'nextgen-gallery-pro');
        $i18n->shipping_phone_label = __('Phone', 'nextgen-gallery-pro');
        $i18n->shipping_phone_tip = __('Phone', 'nextgen-gallery-pro');
        $i18n->unshippable = __("We're sorry, but one or more items you've selected cannot be shipped to this country.", 'nextgen-gallery-pro');
        $i18n->tbd = __('Please Add Address', 'nextgen-gallery-pro');
        $i18n->select_country = __('Select Country', 'nextgen-gallery-pro');
        $i18n->select_region = __('Select Region', 'nextgen-gallery-pro');
        $i18n->error_empty = __('%s cannot be empty.', 'nextgen-gallery-pro');
        $i18n->error_minimum = __('%s needs to be at least %s characters.', 'nextgen-gallery-pro');
        $i18n->error_invalid = __('%s is in an invalid format.', 'nextgen-gallery-pro');
        $i18n->error_form_invalid = __('Form contains errors, please correct all errors before submitting the order.', 'nextgen-gallery-pro');
        $i18n->calculating = __('Calculating...', 'nextgen-gallery-pro');
        return $i18n;
    }
    function enqueue_static_resources()
    {
        M_NextGen_Pro_Ecommerce::enqueue_cart_resources();
        // Enqueue fontawesome
        if (method_exists('M_Gallery_Display', 'enqueue_fontawesome')) {
            M_Gallery_Display::enqueue_fontawesome();
        } else {
            C_Display_Type_Controller::get_instance()->enqueue_displayed_gallery_trigger_buttons_resources();
        }
        C_Lightbox_Library_Manager::get_instance()->enqueue();
        wp_enqueue_style('fontawesome');
        wp_enqueue_style('lightbox-featherlight', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#featherlight/featherlight.min.css'), false, '1.7.13');
        wp_enqueue_script('lightbox-featherlight', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#featherlight/featherlight.js'), array('jquery'), '1.7.13');
        wp_enqueue_style('croppie', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#croppie/croppie.css'), false, '2.6.4');
        wp_enqueue_script('croppie', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#croppie/croppie.js'), array('jquery'), '2.6.4');
        wp_enqueue_style('ngg-pro-checkout', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#checkout.css'));
        wp_enqueue_script('ngg-pro-checkout', $this->object->get_static_url('photocrati-nextgen_pro_ecommerce#checkout.js'), ['jquery', 'ngg_pro_cart']);
        foreach ($this->object->get_checkout_buttons() as $btn) {
            $method = "enqueue_{$btn}_resources";
            if ($this->object->has_method($method)) {
                $this->object->{$method}();
            }
        }
    }
    function get_continue_shopping_url()
    {
        return isset($_GET['referrer']) ? $_GET['referrer'] : '';
    }
    function checkout_form()
    {
        $this->enqueue_static_resources();
        if ($this->object->is_post_request()) {
            $this->processor();
        }
        // Get checkout buttons
        $buttons = array();
        foreach ($this->object->get_checkout_buttons() as $btn) {
            $method = "_render_{$btn}_button";
            $buttons[] = $this->object->{$method}();
        }
        $settings = C_NextGen_Settings::get_instance();
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#checkout_form', array('buttons' => $buttons, 'referrer_url' => $this->get_continue_shopping_url(), 'i18n' => $this->object->get_i18n_strings(), 'display_taxes' => $settings->ecommerce_tax_enable, 'display_coupon' => M_NextGen_Pro_Coupons::are_coupons_enabled()), TRUE);
    }
    function processor()
    {
        if ($gateway = $this->object->param('ngg_pro_checkout')) {
            $method = "process_{$gateway}_request";
            if ($this->object->has_method($method)) {
                $this->object->{$method}();
            }
        }
    }
    /**
     * @param C_NextGen_Pro_Cart $cart
     * @param string $customer_name
     * @param string $email
     * @param string $payment_gateway
     * @param string $status
     * @return C_NextGen_Pro_Order
     */
    function create_order($cart, $customer_name, $email, $payment_gateway, $status = 'awaiting_payment', $gateway_note = '')
    {
        $settings = $cart->get_settings();
        $order_mapper = C_Order_Mapper::get_instance();
        $properties = array('customer_name' => $customer_name, 'email' => $email, 'payment_gateway' => $payment_gateway, 'cart' => $cart->to_array(), 'status' => $status, 'post_status' => 'publish', 'subtotal' => $cart->get_subtotal(), 'tax' => $cart->get_tax(), 'total_amount' => $cart->get_total(), 'shipping' => $cart->get_shipping(), 'gateway_admin_note' => $gateway_note);
        if (isset($settings['shipping_address'])) {
            $shipping_address = $settings['shipping_address'];
            $properties = array_merge($properties, array('shipping_street_address' => $shipping_address['street_address'], 'shipping_street_address' => $shipping_address['street_address'], 'shipping_address_line' => $shipping_address['address_line'], 'shipping_city' => $shipping_address['city'], 'shipping_state' => $shipping_address['state'], 'shipping_zip' => $shipping_address['zip'], 'shipping_country' => $shipping_address['country'], 'shipping_phone' => $shipping_address['phone'], 'shipping_name' => $shipping_address['name']));
        }
        /** @var C_NextGen_Pro_Order $order */
        $order = $order_mapper->create($properties);
        return $order;
    }
    /**
     * @param array $settings
     * @param array $items
     * @param string $coupon
     * @param bool $inverse_price_validation Enable the free gateway to reverse the validation of cart totals
     * @return array
     * @throws Exception
     */
    public function prepare_order($settings = array(), $items = array(), $coupon = '', $inverse_price_validation = FALSE)
    {
        if (!is_array($items) || empty($items)) {
            throw new Exception(__('Your cart is empty', 'nextgen-gallery-pro'));
        }
        $cart = new C_NextGen_Pro_Cart(NULL, $settings);
        $shipping_address = $cart->get_setting('shipping_address');
        $customer = array_merge($shipping_address, array('name' => $shipping_address['name'], 'email' => $shipping_address['email']));
        $cart->add_items($items);
        $cart->apply_coupon($coupon);
        if (!$inverse_price_validation) {
            if ((float) $cart->get_total() <= 0) {
                throw new Exception(__('Invalid request', 'nextgen-gallery-pro'));
            }
        } else {
            $total = bcsub((float) $cart->get_total(), (float) $cart->get_discount());
            if ($total > 0 || $total < 0) {
                throw new Exception(__('Invalid request', 'nextgen-gallery-pro'));
            }
        }
        if (!$cart->has_items()) {
            throw new Exception(__('Your cart is empty', 'nextgen-gallery-pro'));
        }
        if ($cart->has_shippable_items()) {
            $found = FALSE;
            foreach (C_NextGen_Pro_Currencies::$countries as $id => $country) {
                if ($country['code'] === $customer['country']) {
                    $found = TRUE;
                    break;
                }
            }
            if (!$found) {
                throw new Exception(__('Invalid country selected, please try again.', 'nextgen-gallery-pro'));
            }
        }
        $retval = array();
        $retval['customer'] = $customer;
        $retval['cart'] = $cart;
        // We add this now because the discount amounts are only calculated by this method and we
        // need those values before we call create_order()
        $retval['cart_array'] = $cart->to_array();
        return $retval;
    }
    /**
     * @param array $settings
     * @param array $items
     * @param string $coupon
     * @param string $status
     * @param string $gateway
     * @param string $gateway_message
     * @param bool $inverse_price_validation
     * @param bool $send_order_notification
     * @return array
     * @throws Exception
     */
    public function save_order($settings, $items, $coupon, $status, $gateway, $gateway_message, $send_order_notification = TRUE, $inverse_price_validation = FALSE)
    {
        /*
         * Step One: basic validation, rules checking, preparation
         */
        $prepared_order = $this->object->prepare_order($settings, $items, $coupon, $inverse_price_validation);
        /*
         * Step two: create the C_Order object itself
         */
        $order = $this->object->create_order($prepared_order['cart'], $prepared_order['customer']['name'], $prepared_order['customer']['email'], $gateway, $status, $gateway_message);
        /*
         * Step three: save the C_Order object; retrieve and parse any errors into a readable format
         */
        $result = C_Order_Mapper::get_instance()->save($order);
        $errors = $order->get_errors();
        if (!$result || !empty($errors)) {
            $errmsg = __('Could not save order:', 'nextgen-gallery-pro');
            foreach ($errors as $field => $field_errors) {
                foreach ($field_errors as $error) {
                    $errmsg .= "\n" . $error;
                }
            }
            throw new Exception($errmsg);
        }
        if ($send_order_notification) {
            $this->send_order_notification($order);
        }
        switch ($status) {
            case 'verified':
            case 'paid':
                $this->mark_as_paid($order, TRUE, TRUE, $gateway_message);
                break;
            case 'unpaid':
                $this->mark_as_unpaid($order, TRUE, $gateway_message);
                break;
            case 'unverified':
            case 'awaiting_payment':
            case 'awaiting-payment':
            case 'waiting-payment':
            case 'waiting_payment':
                $this->mark_as_awaiting_payment($order, TRUE, $gateway_message);
                break;
            case 'failed':
                $this->mark_as_failed($order, TRUE, $gateway_message);
                break;
            case 'fraud':
                $this->mark_as_fraud($order, TRUE, $gateway_message);
                break;
        }
        /*
         * Step four: all is well, finish:
         */
        $retval = $prepared_order;
        // customer and cart keys are provided by prepare_order()
        $retval['order'] = $order->hash;
        $retval['redirect'] = $this->object->get_thank_you_page_url($order->hash, TRUE);
        return $retval;
    }
    /**
     * @param C_NextGen_Pro_Order|string $order_object_or_hash Order object or hash ID
     * @param string $subject
     * @param string $body
     * @param null|string $to
     * @return bool
     */
    function _send_email($order_object_or_hash, $subject, $body, $to = NULL)
    {
        $retval = FALSE;
        $order = $this->_get_order($order_object_or_hash);
        // Ensure that we have a valid order
        if ($order) {
            // Use only the order entity
            if (get_class($order) != 'stdClass') {
                $order = $order->get_entity();
            }
            // Get the order total
            $cart = new C_NextGen_Pro_Cart($order->cart);
            $order->total_amount = $order->total_amount = $cart->get_total();
            // Get the destination url
            $order_details_page = $this->get_thank_you_page_url($order->hash, TRUE);
            // Get needed components
            $mail = C_Nextgen_Mail_Manager::get_instance();
            // Set additional order variables
            $order->order_details_page = $order_details_page;
            $order->total_amount_formatted = M_NextGen_Pro_Ecommerce::get_formatted_price($order->total_amount, $cart->get_currency(), FALSE);
            $order->order_total_formatted = M_NextGen_Pro_Ecommerce::get_formatted_price($order->total_amount, $cart->get_currency(), FALSE);
            $order->admin_email = M_NextGen_Pro_Ecommerce::get_studio_email_address();
            $order->blog_description = get_bloginfo('description');
            $order->blog_name = get_bloginfo('name');
            $order->blog_url = site_url();
            $order->site_url = site_url();
            $order->home_url = home_url();
            $order->order_id = $order->ID;
            // Determine image filenames
            $file_list = array();
            foreach ($cart->get_images() as $image) {
                $file_list[] = $image->filename;
            }
            $order->item_count = count($cart->get_items());
            $order->file_list = implode(", ", $file_list);
            // Send the e-mail
            $content = $mail->create_content();
            $content->set_subject($subject);
            $content->load_template($body);
            foreach (get_object_vars($order) as $key => $val) {
                $content->set_property($key, $val);
            }
            $mail->send($to ? $to : $order->email, $subject, $content);
            $retval = TRUE;
        }
        return $retval;
    }
    /**
     * @param C_NextGen_Pro_Order|string $order_object_or_hash Order object or hash ID
     * @return bool
     */
    function send_email_receipt($order_object_or_hash)
    {
        $retval = FALSE;
        if ($order = $this->_get_order($order_object_or_hash)) {
            $settings = C_NextGen_Settings::get_instance();
            // Send e-mail receipt to customer
            if ((!isset($order->has_sent_email_receipt) || !$order->has_sent_email_receipt) && $settings->ecommerce_enable_email_receipt) {
                $retval = $this->_send_email($order_object_or_hash, $settings->ecommerce_email_receipt_subject, str_replace(array('%%order_total%%', '%%order_amount%%', '%%total_amount%%'), array('%%order_total_formatted%%', '%%order_amount_formatted%%', '%%total_amount_formatted%%'), $settings->ecommerce_email_receipt_body));
                if ($retval) {
                    $order->has_sent_email_receipt = TRUE;
                    $order->save();
                }
            }
        }
        return $retval;
    }
    /**
     * @param C_NextGen_Pro_Order|string $order_object_or_hash Order object or hash ID
     * @return bool
     */
    function send_order_notification($order_object_or_hash)
    {
        $retval = FALSE;
        if ($order = $this->_get_order($order_object_or_hash)) {
            $settings = C_NextGen_Settings::get_instance();
            // Send admin notification
            if ((!isset($order->has_sent_email_notification) || !$order->has_sent_email_notification) && $settings->ecommerce_enable_email_notification) {
                $retval = $this->_send_email($order_object_or_hash, $settings->ecommerce_email_notification_subject, str_replace(array('%%order_total%%', '%%order_amount%%', '%%total_amount%%'), array('%%order_total_formatted%%', '%%order_amount_formatted%%', '%%total_amount_formatted%%'), $settings->ecommerce_email_notification_body), M_NextGen_Pro_Ecommerce::get_studio_email_address());
                if ($retval) {
                    $order->has_sent_email_notification = TRUE;
                    $order->save();
                }
            }
        }
        return $retval;
    }
    /**
     * Marks an order as paid
     *
     * @param C_NextGen_Pro_Order|string $order Order object or hash ID
     * @param bool $send_emails
     * @param bool $lab_fulfill
     * @param string $note
     * @return C_NextGen_Pro_Order|FALSE
     */
    function mark_as_paid($order, $send_emails = TRUE, $lab_fulfill = TRUE, $note = '')
    {
        $retval = FALSE;
        // Get the order
        if ($order = $this->_get_order($order)) {
            $order->status = 'paid';
            $order->status_note = $note;
            $order->save();
            $order = apply_filters('ngg_order_marked_as_paid', $order);
            if (apply_filters('ngg_order_marked_as_paid_send_emails', $send_emails, $order)) {
                $this->send_email_receipt($order);
            }
            if (apply_filters('ngg_order_marked_as_paid_lab_fulfill', $lab_fulfill && $order->get_cart()->has_lab_items(), $order)) {
                self::submit_lab_order($order);
            }
            $retval = $order;
        }
        return $retval;
    }
    /**
     * Marks an order as unpaid
     *
     * @param C_NextGen_Pro_Order|string $order Order object or hash ID
     * @param bool $send_emails
     * @param string $note
     * @return bool|C_NextGen_Pro_Order
     */
    function mark_as_unpaid($order, $send_emails = TRUE, $note = '')
    {
        $retval = FALSE;
        // Get the order
        if ($order = $this->_get_order($order)) {
            $order->status = 'unpaid';
            $order->status_note = $note;
            $order->save();
            if (apply_filters('ngg_order_marked_as_unpaid_send_emails', $send_emails, $order)) {
                // TODO: Allow customized e-mails to be sent
            }
            $retval = $order;
        }
        return $retval;
    }
    /**
     * Marks the order as awaiting payment
     *
     * @param C_NextGen_Pro_Order|string $order Order object or hash ID
     * @param bool $send_emails
     * @param string $note
     * @return bool|C_NextGen_Pro_Order
     */
    function mark_as_awaiting_payment($order, $send_emails = TRUE, $note = '')
    {
        $retval = FALSE;
        // Get the order
        if ($order = $this->_get_order($order)) {
            $order->status = 'awaiting_payment';
            $order->status_note = $note;
            $order->save();
            if (apply_filters('ngg_order_marked_as_awaiting_payment_send_emails', $send_emails, $order)) {
                // TODO: Allow customized e-mails to be sent
            }
            $retval = $order;
        }
        return $retval;
    }
    /**
     * @param C_NextGen_Pro_Order|string $order Order object or hash ID
     * @param bool $send_emails
     * @param string $note
     * @return bool|C_NextGen_Pro_Order
     */
    function mark_as_fraud($order, $send_emails = TRUE, $note = '')
    {
        $retval = FALSE;
        // Get the order
        if ($order = $this->_get_order($order)) {
            $order->status = 'fraud';
            $order->status_note = $note;
            $order->save();
            if (apply_filters('ngg_order_marked_as_fraud_send_emails', $send_emails, $order)) {
                // TODO: Allow customized e-mails to be sent
            }
            $retval = $order;
        }
        return $retval;
    }
    /**
     * @param C_NextGen_Pro_Order|string $order Order object or hash ID
     * @param bool $send_emails
     * @param string $note
     * @return bool|C_NextGen_Pro_Order
     */
    function mark_as_failed($order, $send_emails = TRUE, $note = '')
    {
        $retval = FALSE;
        // Get the order
        if ($order = $this->_get_order($order)) {
            $order->status = 'failed';
            $order->status_note = $note;
            $order->save();
            if (apply_filters('ngg_order_marked_as_failed_send_emails', $send_emails, $order)) {
                // TODO: Allow customized e-mails to be sent
            }
            $retval = $order;
        }
        return $retval;
    }
    /**
     * Determines whether an order is paid
     *
     * @param C_NextGen_Pro_Order|string $order Order object or hash ID
     * @return bool
     */
    function is_order_paid($order)
    {
        $retval = FALSE;
        if ($order = $this->_get_order($order)) {
            $retval = $order->is_paid();
        }
        return $retval;
    }
    /**
     * @param C_NextGen_Pro_Order|string $order Order object or hash ID
     * @return C_NextGen_Pro_Order
     */
    function _get_order($order)
    {
        if (!is_object($order)) {
            $order = is_string($order) ? C_Order_Mapper::get_instance()->find_by_hash($order, TRUE) : C_Order_Mapper::get_instance()->find($order, TRUE);
        }
        return $order;
    }
    /**
     * @param C_NextGen_Pro_Order|string $order Order object or hash ID
     */
    function redirect_to_thank_you_page($order)
    {
        // Get the order
        if ($order = $this->_get_order($order)) {
            // Expose hook for third-parties
            do_action('ngg_pro_purchase_complete', $order);
            // Get the destination url
            $order_details_page = $this->get_thank_you_page_url($order->hash, TRUE);
            wp_redirect($order_details_page);
        } else {
            echo __("We couldn't find your order. We apologize for the inconvenience", 'nextgen-gallery-pro');
        }
        exit;
    }
    /**
     * @param C_NextGen_Pro_Order $order
     * @param bool $force
     * @return array|WP_Error|void
     */
    function submit_lab_order(C_NextGen_Pro_Order $order, $force = FALSE)
    {
        if ($force || !isset($order->aws_order_id)) {
            $settings = C_NextGen_Settings::get_instance();
            $params = array('url' => add_query_arg('action', 'get_print_lab_order', $settings->get('ajax_url')), 'order' => $order->hash, 'nonce' => WPSimpleNonce::createNonce('get_print_lab_order'), 'retrieved' => FALSE);
            // Use test mode?
            $test_mode = FALSE;
            if (defined('NGG_PRO_LAB_TEST_MODE')) {
                $test_mode = NGG_PRO_LAB_TEST_MODE;
            }
            $test_mode = apply_filters('ngg_pro_lab_test_mode', $test_mode, $order);
            $prod_url = 'https://jy12m1w2q2.execute-api.us-east-1.amazonaws.com/latest/';
            $test_url = 'https://pv7bfbnfge.execute-api.us-east-1.amazonaws.com/latest/';
            $api_url = apply_filters('ngg_pro_lab_api_url', $test_mode ? $test_url : $prod_url);
            if (!defined('NGG_PRO_LAB_API_URL')) {
                define('NGG_PRO_LAB_API_URL', $api_url);
            }
            $response = wp_remote_post(NGG_PRO_LAB_API_URL, array('body' => json_encode($params), 'headers' => array('Content-Type' => 'application/json'), 'timeout' => 30));
            if (!is_wp_error($response)) {
                $response['body'] = json_decode($response['body']);
                if (property_exists($response['body'], 'executionArn')) {
                    $order->aws_order_id = $response['body']->executionArn;
                    $order->save();
                }
            }
            return $response;
        }
    }
    function redirect_to_cancel_page()
    {
        wp_redirect($this->get_cancel_page_url());
        throw new E_Clean_Exit();
    }
    /**
     * @param $order_hash
     */
    function redirect_to_order_verification_page($order_hash)
    {
        wp_redirect($this->object->get_order_verification_page_url($order_hash));
        throw new E_Clean_Exit();
    }
    function get_thank_you_page_url($order_id, $order_complete = FALSE)
    {
        $params = array('order' => $order_id);
        if ($order_complete) {
            $params['ngg_order_complete'] = 1;
        }
        $settings = C_NextGen_Settings::get_instance();
        if ($settings->ecommerce_page_thanks) {
            return $this->get_page_url(C_NextGen_Settings::get_instance()->ecommerce_page_thanks, $params);
        } else {
            return $this->_add_to_querystring(site_url('/?ngg_pro_return_page=1'), $params);
        }
    }
    function _add_to_querystring($url, $params = array())
    {
        if ($params) {
            $qs = array();
            foreach ($params as $key => $value) {
                $qs[] = urlencode($key) . '=' . urlencode($value);
            }
            $url .= (strpos($url, '?') === FALSE ? '?' : '&') . implode('&', $qs);
        }
        return $url;
    }
    /**
     * @param string $order_hash
     * @return string|void
     */
    function get_order_verification_page_url($order_hash)
    {
        $settings = C_NextGen_Settings::get_instance();
        if ($settings->get('ecommerce_page_order_verification', FALSE)) {
            return $this->_add_to_querystring($this->get_page_url($settings->get('ecommerce_page_order_verification')), array('order' => $order_hash));
        } else {
            return site_url('/?ngg_pro_verify_page=1&order=' . $order_hash);
        }
    }
    function get_cancel_page_url()
    {
        $settings = C_NextGen_Settings::get_instance();
        if ($settings->ecommerce_page_cancel) {
            return $this->get_page_url($settings->ecommerce_page_cancel);
        } else {
            return $this->_add_to_querystring(site_url('/?ngg_pro_cancel_page=1'));
        }
    }
    function get_page_url($page_id, $params = array())
    {
        $link = get_page_link($page_id);
        if ($params) {
            $link = $this->_add_to_querystring($link, $params);
        }
        return $link;
    }
    function redirect_to_page($page_id, $params = array())
    {
        wp_redirect($this->get_page_url($page_id, $params));
    }
}
class C_NextGen_Pro_Currencies
{
    /** @var int $recheck_rate */
    public static $recheck_rate = 86400;
    // Once per day (seconds)
    /** @var array $currency_rates */
    public static $currency_rates = array();
    /**
     * Nations by ISO 3166 listing with currency (ISO 4217) mapping
     *
     * @link http://en.wikipedia.org/wiki/Iso_3166
     * @var array Countries
     */
    public static $countries = array(4 => array(
        'name' => 'Afghanistan',
        // Us-English Name
        'code' => 'AF',
        // ISO-3166-1 Alpha-2
        'id' => 4,
        // ISO-3166-1 Numeric
        'currency_code' => '971',
    ), 248 => array('name' => 'Åland Islands', 'code' => 'AX', 'id' => 248, 'currency_code' => '978'), 8 => array('name' => 'Albania', 'code' => 'AL', 'id' => 8, 'currency_code' => '008'), 12 => array('name' => 'Algeria', 'code' => 'DZ', 'id' => 12, 'currency_code' => '012'), 16 => array('name' => 'American Samoa', 'code' => 'AS', 'id' => 16, 'currency_code' => '840'), 20 => array('name' => 'Andorra', 'code' => 'AD', 'id' => 20, 'currency_code' => '978'), 24 => array('name' => 'Angola', 'code' => 'AO', 'id' => 24, 'currency_code' => '973'), 660 => array('name' => 'Anguilla', 'code' => 'AI', 'id' => 660, 'currency_code' => '951'), 28 => array('name' => 'Antigua and Barbuda', 'code' => 'AG', 'id' => 28, 'currency_code' => '951'), 32 => array('name' => 'Argentina', 'code' => 'AR', 'id' => 32, 'currency_code' => '032'), 51 => array('name' => 'Armenia', 'code' => 'AM', 'id' => 51, 'currency_code' => '051'), 533 => array('name' => 'Aruba', 'code' => 'AW', 'id' => 533, 'currency_code' => '533'), 36 => array('name' => 'Australia', 'code' => 'AU', 'id' => 36, 'currency_code' => '036'), 40 => array('name' => 'Austria', 'code' => 'AT', 'id' => 40, 'currency_code' => '978'), 31 => array('name' => 'Azerbaijan', 'code' => 'AZ', 'id' => 31, 'currency_code' => '944'), 44 => array('name' => 'Bahamas', 'code' => 'BS', 'id' => 44, 'currency_code' => '044'), 48 => array('name' => 'Bahrain', 'code' => 'BH', 'id' => 48, 'currency_code' => '048'), 50 => array('name' => 'Bangladesh', 'code' => 'BD', 'id' => 50, 'currency_code' => '050'), 52 => array('name' => 'Barbados', 'code' => 'BB', 'id' => 52, 'currency_code' => '052'), 112 => array('name' => 'Belarus', 'code' => 'BY', 'id' => 112, 'currency_code' => '974'), 56 => array('name' => 'Belgium', 'code' => 'BE', 'id' => 56, 'currency_code' => '978'), 84 => array('name' => 'Belize', 'code' => 'BZ', 'id' => 84, 'currency_code' => '084'), 204 => array('name' => 'Benin', 'code' => 'BJ', 'id' => 204, 'currency_code' => '952'), 60 => array('name' => 'Bermuda', 'code' => 'BM', 'id' => 60, 'currency_code' => '060'), 64 => array('name' => 'Bhutan', 'code' => 'BT', 'id' => 64, 'currency_code' => '356'), 68 => array('name' => 'Bolivia, Plurinational State of', 'code' => 'BO', 'id' => 68, 'currency_code' => '068'), 535 => array('name' => 'Bonaire, Sint Eustatius and Saba', 'code' => 'BQ', 'id' => 535, 'currency_code' => '840'), 70 => array('name' => 'Bosnia and Herzegovina', 'code' => 'BA', 'id' => 70, 'currency_code' => '977'), 72 => array('name' => 'Botswana', 'code' => 'BW', 'id' => 72, 'currency_code' => '072'), 74 => array('name' => 'Bouvet Island', 'code' => 'BV', 'id' => 74, 'currency_code' => '578'), 76 => array('name' => 'Brazil', 'code' => 'BR', 'id' => 76, 'currency_code' => '986'), 86 => array('name' => 'British Indian Ocean Territory', 'code' => 'IO', 'id' => 86, 'currency_code' => '840'), 96 => array('name' => 'Brunei Darussalam', 'code' => 'BN', 'id' => 96, 'currency_code' => '096'), 100 => array('name' => 'Bulgaria', 'code' => 'BG', 'id' => 100, 'currency_code' => '975'), 854 => array('name' => 'Burkina Faso', 'code' => 'BF', 'id' => 854, 'currency_code' => '952'), 108 => array('name' => 'Burundi', 'code' => 'BI', 'id' => 108, 'currency_code' => '108'), 116 => array('name' => 'Cambodia', 'code' => 'KH', 'id' => 116, 'currency_code' => '116'), 120 => array('name' => 'Cameroon', 'code' => 'CM', 'id' => 120, 'currency_code' => '950'), 124 => array('name' => 'Canada', 'code' => 'CA', 'id' => 124, 'currency_code' => '124'), 132 => array('name' => 'Cape Verde', 'code' => 'CV', 'id' => 132, 'currency_code' => '132'), 136 => array('name' => 'Cayman Islands', 'code' => 'KY', 'id' => 136, 'currency_code' => '136'), 140 => array('name' => 'Central African Republic', 'code' => 'CF', 'id' => 140, 'currency_code' => '950'), 148 => array('name' => 'Chad', 'code' => 'TD', 'id' => 148, 'currency_code' => '950'), 152 => array('name' => 'Chile', 'code' => 'CL', 'id' => 152, 'currency_code' => '152'), 156 => array('name' => 'China', 'code' => 'CN', 'id' => 156, 'currency_code' => '156'), 162 => array('name' => 'Christmas Island', 'code' => 'CX', 'id' => 162, 'currency_code' => '036'), 166 => array('name' => 'Cocos (Keeling) Islands', 'code' => 'CC', 'id' => 166, 'currency_code' => '036'), 170 => array('name' => 'Colombia', 'code' => 'CO', 'id' => 170, 'currency_code' => '170'), 174 => array('name' => 'Comoros', 'code' => 'KM', 'id' => 174, 'currency_code' => '174'), 178 => array('name' => 'Congo', 'code' => 'CG', 'id' => 178, 'currency_code' => '950'), 180 => array('name' => 'Congo, the Democratic Republic of the', 'code' => 'CD', 'id' => 180, 'currency_code' => '976'), 184 => array('name' => 'Cook Islands', 'code' => 'CK', 'id' => 184, 'currency_code' => '554'), 188 => array('name' => 'Costa Rica', 'code' => 'CR', 'id' => 188, 'currency_code' => '188'), 191 => array('name' => 'Croatia', 'code' => 'HR', 'id' => 191, 'currency_code' => '191'), 192 => array('name' => 'Cuba', 'code' => 'CU', 'id' => 192, 'currency_code' => '192'), 531 => array('name' => 'Curaçao', 'code' => 'CW', 'id' => 531, 'currency_code' => '532'), 196 => array('name' => 'Cyprus', 'code' => 'CY', 'id' => 196, 'currency_code' => '978'), 203 => array('name' => 'Czech Republic', 'code' => 'CZ', 'id' => 203, 'currency_code' => '203'), 384 => array('name' => 'Côte d\'Ivoire', 'code' => 'CI', 'id' => 384, 'currency_code' => '952'), 208 => array('name' => 'Denmark', 'code' => 'DK', 'id' => 208, 'currency_code' => '208'), 262 => array('name' => 'Djibouti', 'code' => 'DJ', 'id' => 262, 'currency_code' => '262'), 212 => array('name' => 'Dominica', 'code' => 'DM', 'id' => 212, 'currency_code' => '951'), 214 => array('name' => 'Dominican Republic', 'code' => 'DO', 'id' => 214, 'currency_code' => '214'), 218 => array('name' => 'Ecuador', 'code' => 'EC', 'id' => 218, 'currency_code' => '840'), 818 => array('name' => 'Egypt', 'code' => 'EG', 'id' => 818, 'currency_code' => '818'), 222 => array('name' => 'El Salvador', 'code' => 'SV', 'id' => 222, 'currency_code' => '840'), 226 => array('name' => 'Equatorial Guinea', 'code' => 'GQ', 'id' => 226, 'currency_code' => '950'), 232 => array('name' => 'Eritrea', 'code' => 'ER', 'id' => 232, 'currency_code' => '232'), 233 => array('name' => 'Estonia', 'code' => 'EE', 'id' => 233, 'currency_code' => '978'), 231 => array('name' => 'Ethiopia', 'code' => 'ET', 'id' => 231, 'currency_code' => '230'), 238 => array('name' => 'Falkland Islands (Malvinas)', 'code' => 'FK', 'id' => 238, 'currency_code' => '238'), 234 => array('name' => 'Faroe Islands', 'code' => 'FO', 'id' => 234, 'currency_code' => '208'), 242 => array('name' => 'Fiji', 'code' => 'FJ', 'id' => 242, 'currency_code' => '242'), 246 => array('name' => 'Finland', 'code' => 'FI', 'id' => 246, 'currency_code' => '978'), 250 => array('name' => 'France', 'code' => 'FR', 'id' => 250, 'currency_code' => '978'), 254 => array('name' => 'French Guiana', 'code' => 'GF', 'id' => 254, 'currency_code' => '978'), 258 => array('name' => 'French Polynesia', 'code' => 'PF', 'id' => 258, 'currency_code' => '953'), 260 => array('name' => 'French Southern Territories', 'code' => 'TF', 'id' => 260, 'currency_code' => '978'), 266 => array('name' => 'Gabon', 'code' => 'GA', 'id' => 266, 'currency_code' => '950'), 270 => array('name' => 'Gambia', 'code' => 'GM', 'id' => 270, 'currency_code' => '270'), 268 => array('name' => 'Georgia', 'code' => 'GE', 'id' => 268, 'currency_code' => '981'), 276 => array('name' => 'Germany', 'code' => 'DE', 'id' => 276, 'currency_code' => '978'), 288 => array('name' => 'Ghana', 'code' => 'GH', 'id' => 288, 'currency_code' => '936'), 292 => array('name' => 'Gibraltar', 'code' => 'GI', 'id' => 292, 'currency_code' => '292'), 300 => array('name' => 'Greece', 'code' => 'GR', 'id' => 300, 'currency_code' => '978'), 304 => array('name' => 'Greenland', 'code' => 'GL', 'id' => 304, 'currency_code' => '208'), 308 => array('name' => 'Grenada', 'code' => 'GD', 'id' => 308, 'currency_code' => '951'), 312 => array('name' => 'Guadeloupe', 'code' => 'GP', 'id' => 312, 'currency_code' => '978'), 316 => array('name' => 'Guam', 'code' => 'GU', 'id' => 316, 'currency_code' => '840'), 320 => array('name' => 'Guatemala', 'code' => 'GT', 'id' => 320, 'currency_code' => '320'), 831 => array('name' => 'Guernsey', 'code' => 'GG', 'id' => 831, 'currency_code' => '826'), 324 => array('name' => 'Guinea', 'code' => 'GN', 'id' => 324, 'currency_code' => '324'), 624 => array('name' => 'Guinea-Bissau', 'code' => 'GW', 'id' => 624, 'currency_code' => '952'), 328 => array('name' => 'Guyana', 'code' => 'GY', 'id' => 328, 'currency_code' => '328'), 332 => array('name' => 'Haiti', 'code' => 'HT', 'id' => 332, 'currency_code' => '840'), 334 => array('name' => 'Heard Island and McDonald Mcdonald Islands', 'code' => 'HM', 'id' => 334, 'currency_code' => '036'), 336 => array('name' => 'Holy See (Vatican City State)', 'code' => 'VA', 'id' => 336, 'currency_code' => '978'), 340 => array('name' => 'Honduras', 'code' => 'HN', 'id' => 340, 'currency_code' => '340'), 344 => array('name' => 'Hong Kong', 'code' => 'HK', 'id' => 344, 'currency_code' => '344'), 348 => array('name' => 'Hungary', 'code' => 'HU', 'id' => 348, 'currency_code' => '348'), 352 => array('name' => 'Iceland', 'code' => 'IS', 'id' => 352, 'currency_code' => '352'), 356 => array('name' => 'India', 'code' => 'IN', 'id' => 356, 'currency_code' => '356'), 360 => array('name' => 'Indonesia', 'code' => 'ID', 'id' => 360, 'currency_code' => '360'), 364 => array('name' => 'Iran, Islamic Republic of', 'code' => 'IR', 'id' => 364, 'currency_code' => '364'), 368 => array('name' => 'Iraq', 'code' => 'IQ', 'id' => 368, 'currency_code' => '368'), 372 => array('name' => 'Ireland', 'code' => 'IE', 'id' => 372, 'currency_code' => '978'), 833 => array('name' => 'Isle of Man', 'code' => 'IM', 'id' => 833, 'currency_code' => '826'), 376 => array('name' => 'Israel', 'code' => 'IL', 'id' => 376, 'currency_code' => '376'), 380 => array('name' => 'Italy', 'code' => 'IT', 'id' => 380, 'currency_code' => '978'), 388 => array('name' => 'Jamaica', 'code' => 'JM', 'id' => 388, 'currency_code' => '388'), 392 => array('name' => 'Japan', 'code' => 'JP', 'id' => 392, 'currency_code' => '392'), 832 => array('name' => 'Jersey', 'code' => 'JE', 'id' => 832, 'currency_code' => '826'), 400 => array('name' => 'Jordan', 'code' => 'JO', 'id' => 400, 'currency_code' => '400'), 398 => array('name' => 'Kazakhstan', 'code' => 'KZ', 'id' => 398, 'currency_code' => '398'), 404 => array('name' => 'Kenya', 'code' => 'KE', 'id' => 404, 'currency_code' => '404'), 296 => array('name' => 'Kiribati', 'code' => 'KI', 'id' => 296, 'currency_code' => '036'), 408 => array('name' => 'Korea, Democratic People\'s Republic of', 'code' => 'KP', 'id' => 408, 'currency_code' => '408'), 410 => array('name' => 'Korea, Republic of', 'code' => 'KR', 'id' => 410, 'currency_code' => '410'), 414 => array('name' => 'Kuwait', 'code' => 'KW', 'id' => 414, 'currency_code' => '414'), 417 => array('name' => 'Kyrgyzstan', 'code' => 'KG', 'id' => 417, 'currency_code' => '417'), 418 => array('name' => 'Lao People\'s Democratic Republic', 'code' => 'LA', 'id' => 418, 'currency_code' => '418'), 428 => array('name' => 'Latvia', 'code' => 'LV', 'id' => 428, 'currency_code' => '428'), 422 => array('name' => 'Lebanon', 'code' => 'LB', 'id' => 422, 'currency_code' => '422'), 426 => array('name' => 'Lesotho', 'code' => 'LS', 'id' => 426, 'currency_code' => '710'), 430 => array('name' => 'Liberia', 'code' => 'LR', 'id' => 430, 'currency_code' => '430'), 434 => array('name' => 'Libya', 'code' => 'LY', 'id' => 434, 'currency_code' => '434'), 438 => array('name' => 'Liechtenstein', 'code' => 'LI', 'id' => 438, 'currency_code' => '756'), 440 => array('name' => 'Lithuania', 'code' => 'LT', 'id' => 440, 'currency_code' => '440'), 442 => array('name' => 'Luxembourg', 'code' => 'LU', 'id' => 442, 'currency_code' => '978'), 446 => array('name' => 'Macao', 'code' => 'MO', 'id' => 446, 'currency_code' => '446'), 807 => array('name' => 'Macedonia, the Former Yugoslav Republic of', 'code' => 'MK', 'id' => 807, 'currency_code' => '807'), 450 => array('name' => 'Madagascar', 'code' => 'MG', 'id' => 450, 'currency_code' => '969'), 454 => array('name' => 'Malawi', 'code' => 'MW', 'id' => 454, 'currency_code' => '454'), 458 => array('name' => 'Malaysia', 'code' => 'MY', 'id' => 458, 'currency_code' => '458'), 462 => array('name' => 'Maldives', 'code' => 'MV', 'id' => 462, 'currency_code' => '462'), 466 => array('name' => 'Mali', 'code' => 'ML', 'id' => 466, 'currency_code' => '952'), 470 => array('name' => 'Malta', 'code' => 'MT', 'id' => 470, 'currency_code' => '978'), 584 => array('name' => 'Marshall Islands', 'code' => 'MH', 'id' => 584, 'currency_code' => '840'), 474 => array('name' => 'Martinique', 'code' => 'MQ', 'id' => 474, 'currency_code' => '978'), 478 => array('name' => 'Mauritania', 'code' => 'MR', 'id' => 478, 'currency_code' => '478'), 480 => array('name' => 'Mauritius', 'code' => 'MU', 'id' => 480, 'currency_code' => '480'), 175 => array('name' => 'Mayotte', 'code' => 'YT', 'id' => 175, 'currency_code' => '978'), 484 => array('name' => 'Mexico', 'code' => 'MX', 'id' => 484, 'currency_code' => '484'), 583 => array('name' => 'Micronesia, Federated States of', 'code' => 'FM', 'id' => 583, 'currency_code' => '840'), 498 => array('name' => 'Moldova, Republic of', 'code' => 'MD', 'id' => 498, 'currency_code' => '498'), 492 => array('name' => 'Monaco', 'code' => 'MC', 'id' => 492, 'currency_code' => '978'), 496 => array('name' => 'Mongolia', 'code' => 'MN', 'id' => 496, 'currency_code' => '496'), 499 => array('name' => 'Montenegro', 'code' => 'ME', 'id' => 499, 'currency_code' => '978'), 500 => array('name' => 'Montserrat', 'code' => 'MS', 'id' => 500, 'currency_code' => '951'), 504 => array('name' => 'Morocco', 'code' => 'MA', 'id' => 504, 'currency_code' => '504'), 508 => array('name' => 'Mozambique', 'code' => 'MZ', 'id' => 508, 'currency_code' => '943'), 104 => array('name' => 'Myanmar', 'code' => 'MM', 'id' => 104, 'currency_code' => '104'), 516 => array('name' => 'Namibia', 'code' => 'NA', 'id' => 516, 'currency_code' => '710'), 520 => array('name' => 'Nauru', 'code' => 'NR', 'id' => 520, 'currency_code' => '036'), 524 => array('name' => 'Nepal', 'code' => 'NP', 'id' => 524, 'currency_code' => '524'), 528 => array('name' => 'Netherlands', 'code' => 'NL', 'id' => 528, 'currency_code' => '978'), 540 => array('name' => 'New Caledonia', 'code' => 'NC', 'id' => 540, 'currency_code' => '953'), 554 => array('name' => 'New Zealand', 'code' => 'NZ', 'id' => 554, 'currency_code' => '554'), 558 => array('name' => 'Nicaragua', 'code' => 'NI', 'id' => 558, 'currency_code' => '558'), 562 => array('name' => 'Niger', 'code' => 'NE', 'id' => 562, 'currency_code' => '952'), 566 => array('name' => 'Nigeria', 'code' => 'NG', 'id' => 566, 'currency_code' => '566'), 570 => array('name' => 'Niue', 'code' => 'NU', 'id' => 570, 'currency_code' => '554'), 574 => array('name' => 'Norfolk Island', 'code' => 'NF', 'id' => 574, 'currency_code' => '036'), 580 => array('name' => 'Northern Mariana Islands', 'code' => 'MP', 'id' => 580, 'currency_code' => '840'), 578 => array('name' => 'Norway', 'code' => 'NO', 'id' => 578, 'currency_code' => '578'), 512 => array('name' => 'Oman', 'code' => 'OM', 'id' => 512, 'currency_code' => '512'), 586 => array('name' => 'Pakistan', 'code' => 'PK', 'id' => 586, 'currency_code' => '586'), 585 => array('name' => 'Palau', 'code' => 'PW', 'id' => 585, 'currency_code' => '840'), 591 => array('name' => 'Panama', 'code' => 'PA', 'id' => 591, 'currency_code' => '840'), 598 => array('name' => 'Papua New Guinea', 'code' => 'PG', 'id' => 598, 'currency_code' => '598'), 600 => array('name' => 'Paraguay', 'code' => 'PY', 'id' => 600, 'currency_code' => '600'), 604 => array('name' => 'Peru', 'code' => 'PE', 'id' => 604, 'currency_code' => '604'), 608 => array('name' => 'Philippines', 'code' => 'PH', 'id' => 608, 'currency_code' => '608'), 612 => array('name' => 'Pitcairn', 'code' => 'PN', 'id' => 612, 'currency_code' => '554'), 616 => array('name' => 'Poland', 'code' => 'PL', 'id' => 616, 'currency_code' => '985'), 620 => array('name' => 'Portugal', 'code' => 'PT', 'id' => 620, 'currency_code' => '978'), 630 => array('name' => 'Puerto Rico', 'code' => 'PR', 'id' => 630, 'currency_code' => '840'), 634 => array('name' => 'Qatar', 'code' => 'QA', 'id' => 634, 'currency_code' => '634'), 642 => array('name' => 'Romania', 'code' => 'RO', 'id' => 642, 'currency_code' => '946'), 643 => array('name' => 'Russian Federation', 'code' => 'RU', 'id' => 643, 'currency_code' => '643'), 646 => array('name' => 'Rwanda', 'code' => 'RW', 'id' => 646, 'currency_code' => '646'), 638 => array('name' => 'Réunion', 'code' => 'RE', 'id' => 638, 'currency_code' => '978'), 652 => array('name' => 'Saint Barthélemy', 'code' => 'BL', 'id' => 652, 'currency_code' => '978'), 654 => array('name' => 'Saint Helena, Ascension and Tristan da Cunha', 'code' => 'SH', 'id' => 654, 'currency_code' => '654'), 659 => array('name' => 'Saint Kitts and Nevis', 'code' => 'KN', 'id' => 659, 'currency_code' => '951'), 662 => array('name' => 'Saint Lucia', 'code' => 'LC', 'id' => 662, 'currency_code' => '951'), 663 => array('name' => 'Saint Martin (French part)', 'code' => 'MF', 'id' => 663, 'currency_code' => '978'), 666 => array('name' => 'Saint Pierre and Miquelon', 'code' => 'PM', 'id' => 666, 'currency_code' => '978'), 670 => array('name' => 'Saint Vincent and the Grenadines', 'code' => 'VC', 'id' => 670, 'currency_code' => '951'), 882 => array('name' => 'Samoa', 'code' => 'WS', 'id' => 882, 'currency_code' => '882'), 674 => array('name' => 'San Marino', 'code' => 'SM', 'id' => 674, 'currency_code' => '978'), 678 => array('name' => 'Sao Tome and Principe', 'code' => 'ST', 'id' => 678, 'currency_code' => '678'), 682 => array('name' => 'Saudi Arabia', 'code' => 'SA', 'id' => 682, 'currency_code' => '682'), 686 => array('name' => 'Senegal', 'code' => 'SN', 'id' => 686, 'currency_code' => '952'), 688 => array('name' => 'Serbia', 'code' => 'RS', 'id' => 688, 'currency_code' => '941'), 690 => array('name' => 'Seychelles', 'code' => 'SC', 'id' => 690, 'currency_code' => '690'), 694 => array('name' => 'Sierra Leone', 'code' => 'SL', 'id' => 694, 'currency_code' => '694'), 702 => array('name' => 'Singapore', 'code' => 'SG', 'id' => 702, 'currency_code' => '702'), 534 => array('name' => 'Sint Maarten (Dutch part)', 'code' => 'SX', 'id' => 534, 'currency_code' => '532'), 703 => array('name' => 'Slovakia', 'code' => 'SK', 'id' => 703, 'currency_code' => '978'), 705 => array('name' => 'Slovenia', 'code' => 'SI', 'id' => 705, 'currency_code' => '978'), 90 => array('name' => 'Solomon Islands', 'code' => 'SB', 'id' => 90, 'currency_code' => '090'), 706 => array('name' => 'Somalia', 'code' => 'SO', 'id' => 706, 'currency_code' => '706'), 710 => array('name' => 'South Africa', 'code' => 'ZA', 'id' => 710, 'currency_code' => '710'), 728 => array('name' => 'South Sudan', 'code' => 'SS', 'id' => 728, 'currency_code' => '728'), 724 => array('name' => 'Spain', 'code' => 'ES', 'id' => 724, 'currency_code' => '978'), 144 => array('name' => 'Sri Lanka', 'code' => 'LK', 'id' => 144, 'currency_code' => '144'), 729 => array('name' => 'Sudan', 'code' => 'SD', 'id' => 729, 'currency_code' => '938'), 740 => array('name' => 'Suriname', 'code' => 'SR', 'id' => 740, 'currency_code' => '968'), 744 => array('name' => 'Svalbard and Jan Mayen', 'code' => 'SJ', 'id' => 744, 'currency_code' => '578'), 748 => array('name' => 'Swaziland', 'code' => 'SZ', 'id' => 748, 'currency_code' => '748'), 752 => array('name' => 'Sweden', 'code' => 'SE', 'id' => 752, 'currency_code' => '752'), 756 => array('name' => 'Switzerland', 'code' => 'CH', 'id' => 756, 'currency_code' => '756'), 760 => array('name' => 'Syrian Arab Republic', 'code' => 'SY', 'id' => 760, 'currency_code' => '760'), 158 => array('name' => 'Taiwan, Province of China', 'code' => 'TW', 'id' => 158, 'currency_code' => '901'), 762 => array('name' => 'Tajikistan', 'code' => 'TJ', 'id' => 762, 'currency_code' => '972'), 834 => array('name' => 'Tanzania, United Republic of', 'code' => 'TZ', 'id' => 834, 'currency_code' => '834'), 764 => array('name' => 'Thailand', 'code' => 'TH', 'id' => 764, 'currency_code' => '764'), 626 => array('name' => 'Timor-Leste', 'code' => 'TL', 'id' => 626, 'currency_code' => '840'), 768 => array('name' => 'Togo', 'code' => 'TG', 'id' => 768, 'currency_code' => '952'), 772 => array('name' => 'Tokelau', 'code' => 'TK', 'id' => 772, 'currency_code' => '554'), 776 => array('name' => 'Tonga', 'code' => 'TO', 'id' => 776, 'currency_code' => '776'), 780 => array('name' => 'Trinidad and Tobago', 'code' => 'TT', 'id' => 780, 'currency_code' => '780'), 788 => array('name' => 'Tunisia', 'code' => 'TN', 'id' => 788, 'currency_code' => '788'), 792 => array('name' => 'Turkey', 'code' => 'TR', 'id' => 792, 'currency_code' => '949'), 795 => array('name' => 'Turkmenistan', 'code' => 'TM', 'id' => 795, 'currency_code' => '934'), 796 => array('name' => 'Turks and Caicos Islands', 'code' => 'TC', 'id' => 796, 'currency_code' => '840'), 798 => array('name' => 'Tuvalu', 'code' => 'TV', 'id' => 798, 'currency_code' => '036'), 800 => array('name' => 'Uganda', 'code' => 'UG', 'id' => 800, 'currency_code' => '800'), 804 => array('name' => 'Ukraine', 'code' => 'UA', 'id' => 804, 'currency_code' => '980'), 784 => array('name' => 'United Arab Emirates', 'code' => 'AE', 'id' => 784, 'currency_code' => '784'), 826 => array('name' => 'United Kingdom', 'code' => 'GB', 'id' => 826, 'currency_code' => '826'), 840 => array('name' => 'United States', 'code' => 'US', 'id' => 840, 'currency_code' => '840'), 581 => array('name' => 'United States Minor Outlying Islands', 'code' => 'UM', 'id' => 581, 'currency_code' => '840'), 858 => array('name' => 'Uruguay', 'code' => 'UY', 'id' => 858, 'currency_code' => '858'), 860 => array('name' => 'Uzbekistan', 'code' => 'UZ', 'id' => 860, 'currency_code' => '860'), 548 => array('name' => 'Vanuatu', 'code' => 'VU', 'id' => 548, 'currency_code' => '548'), 862 => array('name' => 'Venezuela, Bolivarian Republic of', 'code' => 'VE', 'id' => 862, 'currency_code' => '937'), 704 => array('name' => 'Viet Nam', 'code' => 'VN', 'id' => 704, 'currency_code' => '704'), 92 => array('name' => 'Virgin Islands, British', 'code' => 'VG', 'id' => 92, 'currency_code' => '840'), 850 => array('name' => 'Virgin Islands, U.S.', 'code' => 'VI', 'id' => 850, 'currency_code' => '840'), 876 => array('name' => 'Wallis and Futuna', 'code' => 'WF', 'id' => 876, 'currency_code' => '953'), 732 => array('name' => 'Western Sahara', 'code' => 'EH', 'id' => 732, 'currency_code' => '504'), 887 => array('name' => 'Yemen', 'code' => 'YE', 'id' => 887, 'currency_code' => '886'), 894 => array('name' => 'Zambia', 'code' => 'ZM', 'id' => 894, 'currency_code' => '967'), 716 => array('name' => 'Zimbabwe', 'code' => 'ZW', 'id' => 716, 'currency_code' => '932'));
    /**
     * Currencies of the world by ISO 4217
     *
     * @link http://en.wikipedia.org/wiki/ISO_4217
     * @var array
     */
    public static $currencies = array('971' => array(
        // Numeric code. *IMPORTANT* that this be quoted; PHP will not treat 008 the same as '008'
        'code' => 'AFN',
        // Alphabetical code, three digits
        'name' => 'Afghani',
        // US-English name of the currency
        'exponent' => '2',
        // Minor-units-how many decimals come after the major unit. USD has 2, while the Yen has 0
        'symbol' => '&#1547;',
    ), '008' => array('code' => 'ALL', 'name' => 'Lek', 'exponent' => '2', 'symbol' => 'L'), '012' => array('code' => 'DZD', 'name' => 'Algerian Dinar', 'exponent' => '2', 'symbol' => 'DA'), '840' => array('code' => 'USD', 'name' => 'US Dollar', 'exponent' => '2', 'symbol' => '$', 'fontawesome' => 'fa-usd'), '978' => array('code' => 'EUR', 'name' => 'Euro', 'exponent' => '2', 'symbol' => '&#8364;', 'fontawesome' => 'fa-eur'), '973' => array('code' => 'AOA', 'name' => 'Angolan Kwanza', 'exponent' => '2', 'symbol' => 'Kz'), '951' => array('code' => 'XCD', 'name' => 'East Caribbean Dollar', 'exponent' => '2', 'symbol' => '$'), '032' => array('code' => 'ARS', 'name' => 'Argentine Peso', 'exponent' => '2', 'symbol' => '$'), '051' => array('code' => 'AMD', 'name' => 'Armenian Dram', 'exponent' => '2', 'symbol' => '&#1423;'), '533' => array('code' => 'AWG', 'name' => 'Aruban Florin', 'exponent' => '2', 'symbol' => '&#402;'), '036' => array('code' => 'AUD', 'name' => 'Australian Dollar', 'exponent' => '2', 'symbol' => '$'), '944' => array('code' => 'AZN', 'name' => 'Azerbaijanian Manat', 'exponent' => '2', 'symbol' => '&#8380'), '044' => array('code' => 'BSD', 'name' => 'Bahamian Dollar', 'exponent' => '2', 'symbol' => '$'), '048' => array('code' => 'BHD', 'name' => 'Bahraini Dinar', 'exponent' => '3', 'symbol' => 'BD'), '050' => array('code' => 'BDT', 'name' => 'Bangladeshi Taka', 'exponent' => '2', 'symbol' => 'Tk;'), '052' => array('code' => 'BBD', 'name' => 'Barbados Dollar', 'exponent' => '2', 'symbol' => '$'), '974' => array('code' => 'BYR', 'name' => 'Belarussian Ruble', 'exponent' => '0', 'symbol' => 'Br'), '084' => array('code' => 'BZD', 'name' => 'Belize Dollar', 'exponent' => '2', 'symbol' => 'BZ$'), '952' => array('code' => 'XOF', 'name' => 'CFA Franc BCEAO', 'exponent' => '0', 'symbol' => 'CFA'), '060' => array('code' => 'BMD', 'name' => 'Bermudian Dollar', 'exponent' => '2', 'symbol' => '$'), '356' => array('code' => 'INR', 'name' => 'Indian Rupee', 'exponent' => '2', 'symbol' => '&#8377;', 'fontawesome' => 'fa-inr'), '068' => array('code' => 'BOB', 'name' => 'Boliviano', 'exponent' => '2', 'symbol' => '$b'), '977' => array('code' => 'BAM', 'name' => 'Convertible Mark', 'exponent' => '2', 'symbol' => 'KM'), '072' => array('code' => 'BWP', 'name' => 'Pula', 'exponent' => '2', 'symbol' => 'P'), '578' => array('code' => 'NOK', 'name' => 'Norwegian Krone', 'exponent' => '2', 'symbol' => 'kr'), '986' => array('code' => 'BRL', 'name' => 'Brazilian Real', 'exponent' => '2', 'symbol' => 'R$'), '096' => array('code' => 'BND', 'name' => 'Brunei Dollar', 'exponent' => '2', 'symbol' => '$'), '975' => array('code' => 'BGN', 'name' => 'Bulgarian Lev', 'exponent' => '2', 'symbol' => '&#1083;&#1074;'), '108' => array('code' => 'BIF', 'name' => 'Burundi Franc', 'exponent' => '0', 'symbol' => 'FBu'), '116' => array('code' => 'KHR', 'name' => 'Cambodian Riel', 'exponent' => '2', 'symbol' => '&#6107;'), '950' => array('code' => 'XAF', 'name' => 'CFA Franc BEAC', 'exponent' => '0', 'symbol' => 'FCFA'), '124' => array('code' => 'CAD', 'name' => 'Canadian Dollar', 'exponent' => '2', 'symbol' => '$'), '132' => array('code' => 'CVE', 'name' => 'Cape Verde Escudo', 'exponent' => '2', 'symbol' => '$'), '136' => array('code' => 'KYD', 'name' => 'Cayman Islands Dollar', 'exponent' => '2', 'symbol' => '$'), '152' => array('code' => 'CLP', 'name' => 'Chilean Peso', 'exponent' => '0', 'symbol' => '$'), '156' => array('code' => 'CNY', 'name' => 'Yuan Renminbi', 'exponent' => '2', 'symbol' => '&#165;', 'fontawesome' => 'fa-cny'), '170' => array('code' => 'COP', 'name' => 'Colombian Peso', 'exponent' => '2', 'symbol' => '$'), '174' => array('code' => 'KMF', 'name' => 'Comoro Franc', 'exponent' => '0', 'symbol' => 'Fr'), '976' => array('code' => 'CDF', 'name' => 'Congolese Franc', 'exponent' => '2', 'symbol' => 'Fr'), '554' => array('code' => 'NZD', 'name' => 'New Zealand Dollar', 'exponent' => '2', 'symbol' => '$'), '188' => array('code' => 'CRC', 'name' => 'Costa Rican Colon', 'exponent' => '2', 'symbol' => '&#8353;'), '191' => array('code' => 'HRK', 'name' => 'Croatian Kuna', 'exponent' => '2', 'symbol' => 'kn'), '192' => array('code' => 'CUP', 'name' => 'Cuban Peso', 'exponent' => '2', 'symbol' => '$MN', 'fontawesome' => 'fa-rouble'), '532' => array('code' => 'ANG', 'name' => 'Netherlands Antillean Guilder', 'exponent' => '2', 'symbol' => 'NA&#402;'), '203' => array('code' => 'CZK', 'name' => 'Czech Koruna', 'exponent' => '2', 'symbol' => 'K&#269;'), '208' => array('code' => 'DKK', 'name' => 'Danish Krone', 'exponent' => '2', 'symbol' => 'kr'), '262' => array('code' => 'DJF', 'name' => 'Djibouti Franc', 'exponent' => '0', 'symbol' => 'fr'), '214' => array('code' => 'DOP', 'name' => 'Dominican Peso', 'exponent' => '2', 'symbol' => 'RD$'), '818' => array('code' => 'EGP', 'name' => 'Egyptian Pound', 'exponent' => '2', 'symbol' => '&#163;'), '232' => array('code' => 'ERN', 'name' => 'Nakfa', 'exponent' => '2', 'symbol' => 'Nfk'), '230' => array('code' => 'ETB', 'name' => 'Ethiopian Birr', 'exponent' => '2', 'symbol' => 'Br'), '238' => array('code' => 'FKP', 'name' => 'Falkland Islands Pound', 'exponent' => '2', 'symbol' => '&#163;'), '242' => array('code' => 'FJD', 'name' => 'Fiji Dollar', 'exponent' => '2', 'symbol' => '$'), '953' => array('code' => 'XPF', 'name' => 'CFP Franc', 'exponent' => '0', 'symbol' => 'F'), '270' => array('code' => 'GMD', 'name' => 'Dalasi', 'exponent' => '2', 'symbol' => 'D'), '981' => array('code' => 'GEL', 'name' => 'Lari', 'exponent' => '2', 'symbol' => '&#4314;'), '936' => array('code' => 'GHS', 'name' => 'Ghana Cedi', 'exponent' => '2', 'symbol' => 'GH&#8373;'), '292' => array('code' => 'GIP', 'name' => 'Gibraltar Pound', 'exponent' => '2', 'symbol' => '&#163;'), '320' => array('code' => 'GTQ', 'name' => 'Quetzal', 'exponent' => '2', 'symbol' => 'Q'), '826' => array('code' => 'GBP', 'name' => 'Pound Sterling', 'exponent' => '2', 'symbol' => '&#163;', 'fontawesome' => 'fa-gbp'), '324' => array('code' => 'GNF', 'name' => 'Guinea Franc', 'exponent' => '0', 'symbol' => 'Fr'), '328' => array('code' => 'GYD', 'name' => 'Guyana Dollar', 'exponent' => '2', 'symbol' => 'G$'), '340' => array('code' => 'HNL', 'name' => 'Lempira', 'exponent' => '2', 'symbol' => 'L'), '344' => array('code' => 'HKD', 'name' => 'Hong Kong Dollar', 'exponent' => '2', 'symbol' => 'HK$'), '348' => array('code' => 'HUF', 'name' => 'Forint', 'exponent' => '2', 'symbol' => 'Ft'), '352' => array('code' => 'ISK', 'name' => 'Iceland Krona', 'exponent' => '0', 'symbol' => 'kr'), '360' => array('code' => 'IDR', 'name' => 'Rupiah', 'exponent' => '2', 'symbol' => 'Rp'), '364' => array('code' => 'IRR', 'name' => 'Iranian Rial', 'exponent' => '2', 'symbol' => '&#65020;'), '368' => array('code' => 'IQD', 'name' => 'Iraqi Dinar', 'exponent' => '3', 'symbol' => '&#1583;&#46;&#1593;'), '376' => array('code' => 'ILS', 'name' => 'New Israeli Sheqel', 'exponent' => '2', 'symbol' => '&#8362;'), '388' => array('code' => 'JMD', 'name' => 'Jamaican Dollar', 'exponent' => '2', 'symbol' => 'J$'), '392' => array('code' => 'JPY', 'name' => 'Yen', 'exponent' => '0', 'symbol' => '&#165;', 'fontawesome' => 'fa-jpy'), '400' => array('code' => 'JOD', 'name' => 'Jordanian Dinar', 'exponent' => '3', 'symbol' => 'JD'), '398' => array('code' => 'KZT', 'name' => 'Tenge', 'exponent' => '2', 'symbol' => '&#8376;'), '404' => array('code' => 'KES', 'name' => 'Kenyan Shilling', 'exponent' => '2', 'symbol' => 'Ksh'), '410' => array('code' => 'KRW', 'name' => 'Won', 'exponent' => '0', 'symbol' => '&#8361;', 'fontawesome' => 'fa-krw'), '414' => array('code' => 'KWD', 'name' => 'Kuwaiti Dinar', 'exponent' => '3', 'symbol' => '&#1603;'), '417' => array('code' => 'KGS', 'name' => 'Som', 'exponent' => '2', 'symbol' => '&#1083;&#1074;'), '418' => array('code' => 'LAK', 'name' => 'Kip', 'exponent' => '2', 'symbol' => '&#8365;'), '428' => array('code' => 'LVL', 'name' => 'Latvian Lats', 'exponent' => '2', 'symbol' => 'Ls'), '422' => array('code' => 'LBP', 'name' => 'Lebanese Pound', 'exponent' => '2', 'symbol' => '&#163;'), '710' => array('code' => 'ZAR', 'name' => 'Rand', 'exponent' => '2', 'symbol' => 'R'), '430' => array('code' => 'LRD', 'name' => 'Liberian Dollar', 'exponent' => '2', 'symbol' => '$'), '434' => array('code' => 'LYD', 'name' => 'Libyan Dinar', 'exponent' => '3', 'symbol' => 'LD'), '756' => array('code' => 'CHF', 'name' => 'Swiss Franc', 'exponent' => '2', 'symbol' => 'SFr'), '440' => array('code' => 'LTL', 'name' => 'Lithuanian Litas', 'exponent' => '2', 'symbol' => 'Lt'), '446' => array('code' => 'MOP', 'name' => 'Pataca', 'exponent' => '2', 'symbol' => 'MOP$'), '807' => array('code' => 'MKD', 'name' => 'Denar', 'exponent' => '2', 'symbol' => '&#1076;&#1077;&#1085;'), '969' => array('code' => 'MGA', 'name' => 'Malagasy Ariary', 'exponent' => '2', 'symbol' => 'Ar'), '454' => array('code' => 'MWK', 'name' => 'Kwacha', 'exponent' => '2', 'symbol' => 'MK'), '458' => array('code' => 'MYR', 'name' => 'Malaysian Ringgit', 'exponent' => '2', 'symbol' => 'RM'), '462' => array('code' => 'MVR', 'name' => 'Rufiyaa', 'exponent' => '2', 'symbol' => 'Rf.'), '478' => array('code' => 'MRO', 'name' => 'Ouguiya', 'exponent' => '2', 'symbol' => 'UM'), '480' => array('code' => 'MUR', 'name' => 'Mauritius Rupee', 'exponent' => '2', 'symbol' => 'Rs'), '484' => array('code' => 'MXN', 'name' => 'Mexican Peso', 'exponent' => '2', 'symbol' => '$'), '498' => array('code' => 'MDL', 'name' => 'Moldovan Leu', 'exponent' => '2', 'symbol' => 'L'), '496' => array('code' => 'MNT', 'name' => 'Tugrik', 'exponent' => '2', 'symbol' => '&#8366;'), '504' => array('code' => 'MAD', 'name' => 'Moroccan Dirham', 'exponent' => '2', 'symbol' => 'MAD'), '943' => array('code' => 'MZN', 'name' => 'Mozambique Metical', 'exponent' => '2', 'symbol' => 'MT'), '104' => array('code' => 'MMK', 'name' => 'Kyat', 'exponent' => '2', 'symbol' => 'K'), '524' => array('code' => 'NPR', 'name' => 'Nepalese Rupee', 'exponent' => '2', 'symbol' => 'Rs'), '558' => array('code' => 'NIO', 'name' => 'Cordoba Oro', 'exponent' => '2', 'symbol' => 'C$'), '566' => array('code' => 'NGN', 'name' => 'Naira', 'exponent' => '2', 'symbol' => '&#8358;'), '512' => array('code' => 'OMR', 'name' => 'Rial Omani', 'exponent' => '3', 'symbol' => '&#65020;'), '586' => array('code' => 'PKR', 'name' => 'Pakistan Rupee', 'exponent' => '2', 'symbol' => 'PKR'), '598' => array('code' => 'PGK', 'name' => 'Kina', 'exponent' => '2', 'symbol' => 'K'), '600' => array('code' => 'PYG', 'name' => 'Guarani', 'exponent' => '0', 'symbol' => 'Gs'), '604' => array('code' => 'PEN', 'name' => 'Nuevo Sol', 'exponent' => '2', 'symbol' => 'S/.'), '608' => array('code' => 'PHP', 'name' => 'Philippine Peso', 'exponent' => '2', 'symbol' => '&#8369;'), '985' => array('code' => 'PLN', 'name' => 'Zloty', 'exponent' => '2', 'symbol' => '&#122;&#322;'), '634' => array('code' => 'QAR', 'name' => 'Qatari Rial', 'exponent' => '2', 'symbol' => '&#65020;'), '946' => array('code' => 'RON', 'name' => 'New Romanian Leu', 'exponent' => '2', 'symbol' => 'lei'), '643' => array('code' => 'RUB', 'name' => 'Russian Ruble', 'exponent' => '2', 'symbol' => '&#8381;', 'fontawesome' => 'fa-rub'), '646' => array('code' => 'RWF', 'name' => 'Rwanda Franc', 'exponent' => '0', 'symbol' => 'FRw'), '654' => array('code' => 'SHP', 'name' => 'Saint Helena Pound', 'exponent' => '2', 'symbol' => '&#163;'), '882' => array('code' => 'WST', 'name' => 'Tala', 'exponent' => '2', 'symbol' => 'WS$'), '678' => array('code' => 'STD', 'name' => 'Dobra', 'exponent' => '2', 'symbol' => 'Db'), '682' => array('code' => 'SAR', 'name' => 'Saudi Riyal', 'exponent' => '2', 'symbol' => '&#65020;'), '941' => array('code' => 'RSD', 'name' => 'Serbian Dinar', 'exponent' => '2', 'symbol' => '&#1056;&#1057;&#1044;'), '690' => array('code' => 'SCR', 'name' => 'Seychelles Rupee', 'exponent' => '2', 'symbol' => 'Rs'), '694' => array('code' => 'SLL', 'name' => 'Leone', 'exponent' => '2', 'symbol' => 'Le'), '702' => array('code' => 'SGD', 'name' => 'Singapore Dollar', 'exponent' => '2', 'symbol' => 'S$'), '090' => array('code' => 'SBD', 'name' => 'Solomon Islands Dollar', 'exponent' => '2', 'symbol' => 'SI$'), '706' => array('code' => 'SOS', 'name' => 'Somali Shilling', 'exponent' => '2', 'symbol' => 'S'), '728' => array('code' => 'SSP', 'name' => 'South Sudanese Pound', 'exponent' => '2', 'symbol' => '&#163;'), '144' => array('code' => 'LKR', 'name' => 'Sri Lanka Rupee', 'exponent' => '2', 'symbol' => 'Rs'), '938' => array('code' => 'SDG', 'name' => 'Sudanese Pound', 'exponent' => '2', 'symbol' => '&#163;'), '968' => array('code' => 'SRD', 'name' => 'Surinam Dollar', 'exponent' => '2', 'symbol' => 'SRD$'), '748' => array('code' => 'SZL', 'name' => 'Lilangeni', 'exponent' => '2', 'symbol' => 'E'), '752' => array('code' => 'SEK', 'name' => 'Swedish Krona', 'exponent' => '2', 'symbol' => 'kr'), '760' => array('code' => 'SYP', 'name' => 'Syrian Pound', 'exponent' => '2', 'symbol' => '&#163;'), '901' => array('code' => 'TWD', 'name' => 'New Taiwan Dollar', 'exponent' => '2', 'symbol' => '&#20803'), '972' => array('code' => 'TJS', 'name' => 'Somoni', 'exponent' => '2', 'symbol' => '$'), '764' => array('code' => 'THB', 'name' => 'Baht', 'exponent' => '2', 'symbol' => '&#3647;'), '776' => array('code' => 'TOP', 'name' => 'Pa’anga', 'exponent' => '2', 'symbol' => 'T$'), '780' => array('code' => 'TTD', 'name' => 'Trinidad and Tobago Dollar', 'exponent' => '2', 'symbol' => 'TT$'), '788' => array('code' => 'TND', 'name' => 'Tunisian Dinar', 'exponent' => '3', 'symbol' => '$'), '949' => array('code' => 'TRY', 'name' => 'Turkish Lira', 'exponent' => '2', 'symbol' => '&#8378;', 'fontawesome' => 'fa-try'), '934' => array('code' => 'TMT', 'name' => 'Turkmenistan New Manat', 'exponent' => '2', 'symbol' => 'T'), '800' => array('code' => 'UGX', 'name' => 'Uganda Shilling', 'exponent' => '0', 'symbol' => 'USh'), '980' => array('code' => 'UAH', 'name' => 'Hryvnia', 'exponent' => '2', 'symbol' => '&#8372;'), '784' => array('code' => 'AED', 'name' => 'UAE Dirham', 'exponent' => '2', 'symbol' => '&#1583;&#46;&#1573;'), '858' => array('code' => 'UYU', 'name' => 'Peso Uruguayo', 'exponent' => '2', 'symbol' => '$U'), '548' => array('code' => 'VUV', 'name' => 'Vatu', 'exponent' => '0', 'symbol' => 'VT'), '937' => array('code' => 'VEF', 'name' => 'Bolivar', 'exponent' => '2', 'symbol' => 'Bs.'), '704' => array('code' => 'VND', 'name' => 'Dong', 'exponent' => '0', 'symbol' => '&#8363;'), '886' => array('code' => 'YER', 'name' => 'Yemeni Rial', 'exponent' => '2', 'symbol' => '&#164;'), '967' => array('code' => 'ZMW', 'name' => 'Zambian Kwacha', 'exponent' => '2', 'symbol' => 'ZK'), '932' => array('code' => 'ZWL', 'name' => 'Zimbabwe Dollar', 'exponent' => '2', 'symbol' => 'Z$'));
    public static function find_currency_id($currency_code)
    {
        foreach (self::$currencies as $id => $currency) {
            if ($currency['code'] == $currency_code) {
                return $id;
            }
        }
        return NULL;
    }
    public static function get_conversion_transient_name($from, $to)
    {
        return 'ngg_currency_rate_from_' . $from . '_to_' . $to;
    }
    public static function get_conversion_error_transient_name($from, $to)
    {
        return 'ngg_currency_rate_error_from_' . $from . '_to_' . $to;
    }
    /**
     * @param int $from_currency Example: 840 (USD)
     * @param int $to_currency Example: 978 (EUR)
     * @return float
     */
    public static function get_conversion_rate($from_currency, $to_currency)
    {
        if (!isset(self::$currency_rates[$from_currency]) || !isset(self::$currency_rates[$from_currency][$to_currency])) {
            $recheck_rate = self::$recheck_rate;
            // Ensure that we at least return a float
            self::$currency_rates[$from_currency][$to_currency] = 0;
            $transient_name = self::get_conversion_transient_name($from_currency, $to_currency);
            $transient_error = self::get_conversion_error_transient_name($from_currency, $to_currency);
            $rate = get_transient($transient_name);
            $error = get_transient($transient_error);
            if ($rate !== FALSE) {
                self::$currency_rates[$from_currency][$to_currency] = $rate;
            } elseif ($rate === FALSE && $error === FALSE) {
                $from_code = isset(self::$currencies[$from_currency]) ? self::$currencies[$from_currency]['code'] : NULL;
                $to_code = isset(self::$currencies[$to_currency]) ? self::$currencies[$to_currency]['code'] : NULL;
                try {
                    if (!$from_code || !$to_code) {
                        throw new Exception(sprintf(__("'%s' or '%s' are invalid ISO 4217 numeric codes.", 'nextgen-gallery-pro'), $from_currency, $to_currency));
                    }
                    $appid = 'b544b1dd920346f6af4a9d094a085ea1';
                    $response = wp_remote_get('https://openexchangerates.org/api/convert/1/' . $from_code . '/' . $to_code . '?app_id=' . $appid);
                    if (!is_array($response) || empty($response['body'])) {
                        throw new Exception(__('Could not connect to api.exchangeratesapi.io', 'nextgen-gallery-pro'));
                    }
                    $json = json_decode($response['body'], TRUE);
                    if (!empty($json['error']) && $json['error']) {
                        throw new Exception("Error encountered during currency conversion rate lookup: " . $json['description']);
                    }
                    if (!empty($json['response'])) {
                        $rate = $json['response'];
                        self::$currency_rates[$from_currency][$to_currency] = $rate;
                        set_transient($transient_name, $rate, $recheck_rate);
                        delete_transient($transient_error);
                    } else {
                        throw new Exception(__('Unknown error encountered during currency conversion rate lookup.', 'nextgen-gallery-pro'));
                    }
                } catch (Exception $exception) {
                    $timezone = get_option('timezone_string');
                    if ($timezone) {
                        date_default_timezone_set($timezone);
                    }
                    $nextDate = date(get_option('date_format') . ' H:i', time() + $recheck_rate);
                    $message = $exception->getMessage();
                    $message .= "<br/>";
                    $message .= sprintf(__('The next attempt to contact exchangeratesapi.io will happen at %s', 'nextgen-gallery-pro'), $nextDate);
                    set_transient($transient_error, $message, $recheck_rate);
                }
            }
        }
        return self::$currency_rates[$from_currency][$to_currency];
    }
}
class C_NextGen_Pro_Ecommerce_Trigger extends C_NextGen_Pro_Lightbox_Trigger
{
    static function is_renderable($name, $displayed_gallery)
    {
        $retval = FALSE;
        if (self::is_pro_lightbox_enabled() && self::are_triggers_enabled($displayed_gallery)) {
            if (self::does_source_return_images($displayed_gallery)) {
                if (isset($displayed_gallery->display_settings['is_ecommerce_enabled'])) {
                    $retval = intval($displayed_gallery->display_settings['is_ecommerce_enabled']) ? TRUE : FALSE;
                }
                if (isset($displayed_gallery->display_settings['original_settings']) && isset($displayed_gallery->display_settings['original_settings']['is_ecommerce_enabled'])) {
                    $retval = intval($displayed_gallery->display_settings['original_settings']['is_ecommerce_enabled']) ? TRUE : FALSE;
                }
            }
        }
        return $retval;
    }
    function get_attributes()
    {
        $attrs = parent::get_attributes();
        $attrs['data-nplmodal-show-cart'] = 1;
        $attrs['data-nplmodal-gallery-id'] = $this->displayed_gallery->id();
        if ($this->view->get_id() == 'nextgen_gallery.image') {
            $image = $this->view->get_object();
            $attrs['data-image-id'] = $image->{$image->id_field};
        }
        return $attrs;
    }
    function get_css_class()
    {
        return 'fa ngg-trigger nextgen_pro_lightbox fa-shopping-cart';
    }
    function render()
    {
        $retval = '';
        $context = $this->view->get_context('object');
        // For Galleria & slideshow displays: show the gallery trigger if a single
        // image is available for sale
        if ($context && get_class($context) == 'C_MVC_View' && !empty($context->_params['images'])) {
            $mapper = C_Pricelist_Mapper::get_instance();
            foreach ($context->_params['images'] as $image) {
                if ($mapper->find_for_image($image)) {
                    $retval = parent::render();
                    break;
                }
            }
        } else {
            // Display the trigger if the image is for sale
            $mapper = C_Pricelist_Mapper::get_instance();
            if ($mapper->find_for_image($context)) {
                $retval = parent::render();
            }
        }
        return $retval;
    }
}
/**
 * @implements I_Order
 * @property C_DataMapper_Model $object
 */
class C_NextGen_Pro_Order extends C_DataMapper_Model
{
    var $_mapper_interface = 'I_Order_Mapper';
    /** @var null|C_NextGen_Pro_Cart */
    var $_cart = NULL;
    /**
     * @param array $properties
     * @param C_Order_Mapper|FALSE $mapper
     * @param mixed $context
     */
    function define($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        parent::define($mapper, $properties, $context);
        $this->implement('I_Order');
    }
    /**
     * @param array $properties
     * @param C_Order_Mapper|FALSE $mapper
     * @param mixed $context
     */
    function initialize($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        // If no mapper was specified, then get the mapper
        if (!$mapper) {
            $mapper = $this->get_registry()->get_utility($this->_mapper_interface);
        }
        // Construct the model
        parent::initialize($mapper, $properties);
        if (is_object($properties)) {
            $properties = get_object_vars($properties);
        }
        if (!isset($properties['cart'])) {
            $properties['cart'] = array();
        }
        $properties['cart']['saved'] = isset($properties['hash']);
        $this->_cart = new C_NextGen_Pro_Cart($properties['cart']);
    }
    /**
     * @param string $property
     * @return mixed
     */
    function get_property($property)
    {
        if (isset($this->{$property})) {
            return $this->{$property};
        } else {
            if (isset($this->get_cart()->{$property})) {
                return $this->{$this}->get_cart()->{$property};
            } else {
                if (isset($this->get_cart()->settings[$property])) {
                    return $this->get_cart()->settings[$property];
                }
            }
        }
    }
    /**
     * @return C_NextGen_Pro_Cart
     */
    function get_cart()
    {
        return $this->_cart;
    }
    /**
     * @return bool
     */
    function is_paid()
    {
        return in_array($this->status, array('verified', 'paid'));
    }
    /**
     * @return bool
     */
    public function validation()
    {
        // First validate attributes that all orders must posess
        $this->object->validates_presence_of('customer_name', array(), __('You must provide a name for the shipping information.', 'nextgen-gallery-pro'));
        $this->object->validates_presence_of('email', array(), __('You must provide an email address in case of any problems fulfilling your order.', 'nextgen-gallery-pro'));
        $this->object->validates_presence_of('payment_gateway', array(), __('An error has occurred, please try again later.', 'nextgen-gallery-pro'));
        $this->object->validates_presence_of('status', array(), __('An error has occurred, please try again later.', 'nextgen-gallery-pro'));
        $this->object->validates_numericality_of('subtotal', FALSE, FALSE, FALSE, __('An error has occurred, please try again later.', 'nextgen-gallery-pro'));
        $this->object->validates_numericality_of('tax', FALSE, FALSE, FALSE, __('An error has occurred, please try again later.', 'nextgen-gallery-pro'));
        $this->object->validates_numericality_of('total_amount', FALSE, FALSE, FALSE, __('An error has occurred, please try again later.', 'nextgen-gallery-pro'));
        // We only need to validate shipping information if there are items to be shipped
        if ($this->object->get_cart()->has_shippable_items()) {
            $this->object->validates_presence_of('shipping_city', array(), __('You must provide a city for the shipping information.', 'nextgen-gallery-pro'));
            $this->object->validates_presence_of('shipping_country', array(), __('You must provide a country for the shipping information.', 'nextgen-gallery-pro'));
            $this->object->validates_presence_of('shipping_name', array(), __('You must provide a name for the shipping information.', 'nextgen-gallery-pro'));
            $this->object->validates_presence_of('shipping_state', array(), __('You must provide a state for the shipping information.', 'nextgen-gallery-pro'));
            $this->object->validates_presence_of('shipping_street_address', array(), __('You must provide a street address for the shipping information.', 'nextgen-gallery-pro'));
            // TODO: the countries regions and postal code regex should really be moved into C_NextGen_Pro_Currencies::$countries
            // NextGen's validates_format_of() is currently broken; see: https://imagely.myjetbrains.com/youtrack/issue/NGG-678
            $countries = json_decode(file_get_contents(C_Fs::get_instance()->get_abspath('photocrati-nextgen_pro_ecommerce#country_list.json')));
            foreach ($countries as $country) {
                if ($this->object->shipping_country == $country[1]) {
                    if (empty($country[3])) {
                        break;
                    }
                    if (!preg_match('/' . $country[3] . '/i', $this->object->shipping_zip)) {
                        $this->object->add_error(__('You must provide a valid postal code for the shipping information.', 'nextgen-gallery-pro'), 'shipping_zip');
                    }
                    break;
                }
            }
            unset($countries);
            // release this from memory ASAP
        }
        return $this->object->is_valid();
    }
}
class C_NextGen_Pro_Order_Controller extends C_MVC_Controller
{
    static $_instance = NULL;
    /**
     * @return C_NextGen_Pro_Order_Controller
     */
    static function get_instance()
    {
        if (is_null(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->image = __('Image', 'nextgen-gallery-pro');
        $i18n->quantity = __('Quantity', 'nextgen-gallery-pro');
        $i18n->description = __('Description', 'nextgen-gallery-pro');
        $i18n->price = __('Price', 'nextgen-gallery-pro');
        $i18n->total = __('Total', 'nextgen-gallery-pro');
        return $i18n;
    }
    function enqueue_static_resources()
    {
        M_Gallery_Display::enqueue_fontawesome();
        wp_enqueue_style('ngg-pro-order-info', $this->get_static_url('photocrati-nextgen_pro_ecommerce#order_info.css'));
    }
    function render($cart)
    {
        $this->enqueue_static_resources();
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#order', array('currency' => $cart->get_currency(), 'images' => $cart->get_images(TRUE), 'i18n' => $this->get_i18n_strings()), TRUE);
    }
}
class C_NextGen_Pro_Order_Verification extends C_MVC_Controller
{
    static $_instance = NULL;
    /**
     * @return C_NextGen_Pro_Order_Verification
     */
    static function get_instance()
    {
        if (!isset(self::$_instance)) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->please_wait_msg = __("Please wait - we appreciate your patience.", 'nextgen-gallery-pro');
        $i18n->verifying_order_msg = __("We're verifying your order. This might take a few minutes.", 'nextgen-gallery-pro');
        $i18n->redirect_msg = __('This page will redirect automatically.', 'nextgen-gallery-pro');
        return $i18n;
    }
    function render($order_hash)
    {
        wp_enqueue_script('photocrati_ajax');
        return $this->render_partial('photocrati-nextgen_pro_ecommerce#order_verification', array('order_hash' => $order_hash, 'i18n' => $this->get_i18n_strings()), TRUE);
    }
}
class C_NextGen_Pro_WHCC_DAS_Prices
{
    public static $parcel_carrier_costs = array('das' => 4.17, 'extended' => 5.37, 'remote' => 9.94, 'hawaii' => 9.0, 'alaska' => 28.5);
    public static $mail_carrier_costs = array('das' => 0.4, 'extended' => 0.51, 'remote' => 0.95, 'hawaii' => 0.86, 'alaska' => 2.71);
    public static $hawaii = array('96703', '96704', '96705', '96708', '96710', '96713', '96714', '96715', '96716', '96718', '96719', '96720', '96721', '96722', '96725', '96726', '96727', '96728', '96729', '96732', '96733', '96738', '96739', '96740', '96741', '96742', '96743', '96745', '96746', '96747', '96748', '96749', '96750', '96751', '96752', '96753', '96754', '96755', '96756', '96757', '96760', '96761', '96763', '96764', '96765', '96766', '96767', '96768', '96769', '96770', '96771', '96772', '96773', '96774', '96775', '96776', '96777', '96778', '96779', '96780', '96781', '96783', '96784', '96785', '96788', '96790', '96793', '96796');
    public static $alaska = array('99546', '99547', '99548', '99549', '99550', '99551', '99552', '99553', '99554', '99555', '99557', '99558', '99559', '99560', '99561', '99563', '99564', '99565', '99566', '99569', '99570', '99571', '99573', '99574', '99575', '99576', '99578', '99579', '99580', '99581', '99583', '99584', '99585', '99586', '99588', '99589', '99590', '99591', '99602', '99604', '99605', '99606', '99607', '99608', '99609', '99612', '99613', '99614', '99620', '99621', '99622', '99623', '99624', '99625', '99626', '99627', '99628', '99629', '99630', '99632', '99633', '99634', '99636', '99637', '99638', '99640', '99641', '99642', '99643', '99644', '99647', '99648', '99649', '99650', '99651', '99652', '99653', '99655', '99656', '99657', '99658', '99659', '99660', '99661', '99662', '99663', '99665', '99666', '99667', '99668', '99670', '99671', '99674', '99675', '99676', '99677', '99678', '99679', '99680', '99681', '99682', '99683', '99684', '99685', '99686', '99688', '99689', '99690', '99691', '99692', '99693', '99694', '99695', '99696', '99697', '99704', '99712', '99714', '99720', '99721', '99722', '99723', '99724', '99726', '99727', '99729', '99730', '99731', '99732', '99733', '99734', '99736', '99737', '99738', '99739', '99740', '99741', '99742', '99743', '99744', '99745', '99746', '99747', '99748', '99749', '99750', '99751', '99752', '99753', '99754', '99755', '99756', '99757', '99758', '99759', '99760', '99761', '99762', '99763', '99764', '99765', '99766', '99767', '99768', '99769', '99770', '99771', '99772', '99773', '99774', '99776', '99777', '99778', '99779', '99780', '99781', '99782', '99783', '99784', '99785', '99786', '99787', '99788', '99789', '99790', '99791', '99820', '99825', '99826', '99827', '99829', '99830', '99832', '99833', '99835', '99836', '99840', '99841', '99850', '99903', '99918', '99919', '99920', '99921', '99922', '99923', '99925', '99926', '99927', '99929', '99950');
    public static $das = array('01002', '01007', '01010', '01029', '01032', '01033', '01036', '01038', '01039', '01056', '01057', '01069', '01073', '01079', '01080', '01082', '01083', '01088', '01093', '01094', '01220', '01224', '01225', '01226', '01227', '01236', '01240', '01247', '01257', '01260', '01267', '01331', '01337', '01338', '01347', '01349', '01351', '01364', '01375', '01376', '01420', '01430', '01431', '01432', '01436', '01438', '01450', '01451', '01463', '01464', '01467', '01468', '01469', '01470', '01471', '01472', '01473', '01474', '01503', '01504', '01506', '01507', '01509', '01515', '01516', '01518', '01521', '01522', '01523', '01526', '01529', '01535', '01541', '01542', '01543', '01562', '01566', '01568', '01590', '01611', '01612', '01720', '01741', '01754', '01756', '01770', '01773', '01775', '01826', '01827', '01830', '01832', '01833', '01860', '01879', '01921', '01922', '01929', '01930', '01936', '01938', '01944', '01949', '01950', '01951', '01952', '01966', '01969', '01982', '01983', '01985', '02025', '02030', '02031', '02045', '02053', '02054', '02056', '02066', '02330', '02332', '02338', '02341', '02347', '02355', '02364', '02366', '02367', '02534', '02538', '02539', '02540', '02541', '02542', '02557', '02564', '02568', '02630', '02631', '02633', '02635', '02638', '02639', '02641', '02642', '02643', '02645', '02646', '02647', '02648', '02650', '02651', '02652', '02653', '02655', '02657', '02659', '02660', '02662', '02668', '02669', '02670', '02671', '02675', '02702', '02715', '02717', '02739', '02743', '02744', '02748', '02769', '02770', '02779', '02790', '02801', '02804', '02812', '02813', '02814', '02817', '02822', '02829', '02830', '02835', '02836', '02837', '02838', '02839', '02857', '02858', '02859', '02873', '02874', '02876', '02877', '02878', '02879', '02880', '02891', '02892', '03032', '03033', '03034', '03036', '03037', '03040', '03045', '03046', '03048', '03049', '03055', '03057', '03070', '03084', '03086', '03220', '03226', '03229', '03233', '03235', '03238', '03246', '03249', '03252', '03254', '03257', '03258', '03264', '03275', '03281', '03303', '03304', '03446', '03448', '03449', '03451', '03452', '03458', '03461', '03466', '03467', '03468', '03469', '03470', '03570', '03575', '03581', '03584', '03585', '03589', '03598', '03608', '03741', '03743', '03748', '03749', '03753', '03754', '03770', '03771', '03773', '03774', '03785', '03811', '03819', '03823', '03824', '03825', '03827', '03838', '03841', '03844', '03848', '03850', '03856', '03861', '03868', '03869', '03870', '03873', '03901', '03902', '03903', '03905', '03907', '03908', '04002', '04008', '04015', '04019', '04021', '04030', '04033', '04038', '04039', '04042', '04046', '04054', '04061', '04062', '04073', '04077', '04079', '04083', '04084', '04085', '04086', '04087', '04090', '04093', '04096', '04097', '04222', '04228', '04230', '04236', '04238', '04239', '04250', '04252', '04254', '04256', '04257', '04259', '04260', '04263', '04265', '04268', '04270', '04271', '04274', '04280', '04281', '04282', '04284', '04291', '04343', '04345', '04350', '04351', '04355', '04357', '04419', '04429', '04444', '04456', '04468', '04473', '04474', '04496', '04530', '04537', '04538', '04541', '04543', '04553', '04573', '04579', '04605', '04609', '04619', '04662', '04665', '04669', '04672', '04675', '04690', '04730', '04736', '04738', '04740', '04742', '04841', '04843', '04849', '04850', '04854', '04856', '04861', '04865', '04915', '04917', '04918', '04927', '04933', '04937', '04938', '04940', '04944', '04953', '04963', '04967', '04972', '04975', '04976', '04981', '04992', '05001', '05030', '05033', '05050', '05053', '05055', '05060', '05061', '05073', '05089', '05091', '05101', '05141', '05150', '05154', '05156', '05158', '05159', '05201', '05253', '05254', '05255', '05301', '05302', '05303', '05304', '05346', '05354', '05443', '05445', '05454', '05461', '05465', '05469', '05473', '05477', '05494', '05602', '05641', '05649', '05654', '05656', '05657', '05661', '05662', '05663', '05664', '05670', '05671', '05672', '05676', '05677', '05678', '05736', '05740', '05753', '05765', '05768', '05819', '05823', '05829', '05830', '05838', '05848', '05850', '05851', '05855', '05863', '06001', '06013', '06016', '06018', '06019', '06020', '06022', '06026', '06031', '06035', '06039', '06043', '06057', '06060', '06062', '06071', '06073', '06075', '06076', '06077', '06078', '06081', '06084', '06085', '06087', '06089', '06090', '06092', '06093', '06094', '06231', '06232', '06234', '06237', '06238', '06239', '06243', '06246', '06248', '06249', '06250', '06254', '06255', '06262', '06264', '06266', '06268', '06277', '06279', '06280', '06330', '06331', '06333', '06335', '06339', '06351', '06353', '06354', '06355', '06357', '06360', '06370', '06371', '06372', '06374', '06375', '06376', '06378', '06383', '06387', '06388', '06390', '06401', '06403', '06404', '06409', '06412', '06414', '06415', '06417', '06419', '06422', '06423', '06424', '06426', '06438', '06439', '06440', '06441', '06442', '06443', '06447', '06469', '06470', '06474', '06475', '06478', '06479', '06480', '06481', '06482', '06483', '06487', '06488', '06498', '06524', '06525', '06612', '06704', '06712', '06716', '06749', '06751', '06759', '06762', '06776', '06778', '06779', '06781', '06782', '06786', '06787', '06790', '06791', '06793', '06795', '06798', '06801', '06812', '06840', '06875', '06876', '06877', '06883', '06896', '06897', '07005', '07045', '07416', '07418', '07419', '07421', '07422', '07438', '07439', '07456', '07461', '07462', '07465', '07480', '07710', '07716', '07717', '07719', '07720', '07722', '07727', '07732', '07738', '07762', '07803', '07821', '07822', '07823', '07830', '07837', '07839', '07847', '07848', '07849', '07853', '07855', '07856', '07860', '07870', '07871', '07877', '07879', '07882', '07924', '07930', '07931', '07934', '07935', '07938', '07945', '07976', '07977', '07978', '07979', '07980', '08001', '08004', '08005', '08008', '08011', '08014', '08015', '08022', '08025', '08037', '08038', '08042', '08050', '08067', '08068', '08069', '08070', '08072', '08073', '08079', '08087', '08088', '08089', '08092', '08095', '08098', '08201', '08202', '08203', '08204', '08205', '08210', '08212', '08213', '08214', '08215', '08217', '08220', '08221', '08223', '08224', '08225', '08226', '08230', '08232', '08240', '08241', '08242', '08243', '08245', '08247', '08248', '08251', '08260', '08302', '08310', '08312', '08313', '08317', '08318', '08320', '08322', '08326', '08328', '08329', '08330', '08341', '08342', '08343', '08344', '08347', '08348', '08350', '08352', '08361', '08402', '08403', '08405', '08406', '08501', '08502', '08510', '08511', '08514', '08515', '08525', '08526', '08530', '08533', '08535', '08551', '08553', '08554', '08555', '08560', '08562', '08721', '08731', '08732', '08735', '08736', '08740', '08751', '08752', '08757', '08758', '08759', '08802', '08803', '08804', '08809', '08812', '08823', '08824', '08825', '08826', '08827', '08829', '08836', '08844', '08848', '08852', '08853', '08858', '08865', '08867', '08868', '08870', '08886', '08889', '10505', '10509', '10516', '10518', '10519', '10524', '10526', '10536', '10541', '10542', '10545', '10560', '10566', '10576', '10578', '10579', '10588', '10590', '10597', '10912', '10916', '10919', '10922', '10924', '10925', '10930', '10931', '10933', '10958', '10959', '10963', '10964', '10968', '10973', '10974', '10975', '10979', '10985', '10986', '10987', '10990', '10998', '11569', '11792', '11930', '11932', '11933', '11935', '11937', '11939', '11941', '11942', '11944', '11946', '11948', '11949', '11952', '11954', '11955', '11956', '11957', '11958', '11959', '11960', '11961', '11962', '11963', '11968', '11970', '11971', '11972', '11975', '11976', '11977', '11978', '12008', '12009', '12015', '12018', '12019', '12020', '12027', '12033', '12041', '12043', '12050', '12059', '12063', '12067', '12074', '12078', '12083', '12086', '12089', '12090', '12106', '12115', '12118', '12123', '12132', '12140', '12143', '12148', '12150', '12151', '12154', '12173', '12174', '12177', '12184', '12185', '12186', '12192', '12196', '12407', '12414', '12417', '12419', '12420', '12423', '12431', '12432', '12433', '12436', '12440', '12443', '12451', '12453', '12456', '12458', '12466', '12472', '12473', '12477', '12482', '12484', '12486', '12487', '12491', '12498', '12504', '12507', '12513', '12514', '12515', '12525', '12528', '12530', '12531', '12533', '12534', '12538', '12541', '12542', '12544', '12545', '12547', '12548', '12561', '12563', '12564', '12565', '12566', '12568', '12569', '12570', '12571', '12572', '12574', '12575', '12578', '12580', '12582', '12585', '12592', '12594', '12701', '12721', '12722', '12724', '12729', '12733', '12742', '12754', '12759', '12767', '12769', '12771', '12775', '12779', '12781', '12785', '12787', '12803', '12804', '12820', '12821', '12831', '12833', '12834', '12839', '12845', '12848', '12854', '12863', '12864', '12884', '12886', '12921', '12924', '12946', '12953', '12958', '12962', '12972', '12975', '12977', '12978', '12979', '12983', '12992', '12995', '13030', '13032', '13033', '13035', '13036', '13037', '13043', '13045', '13056', '13060', '13062', '13064', '13065', '13068', '13069', '13073', '13074', '13076', '13078', '13080', '13082', '13084', '13093', '13103', '13107', '13108', '13112', '13113', '13117', '13119', '13120', '13126', '13131', '13132', '13134', '13135', '13138', '13142', '13148', '13152', '13159', '13164', '13166', '13167', '13301', '13304', '13305', '13308', '13310', '13313', '13314', '13326', '13328', '13335', '13337', '13346', '13352', '13354', '13357', '13363', '13364', '13401', '13402', '13407', '13408', '13409', '13425', '13428', '13435', '13455', '13456', '13461', '13469', '13475', '13476', '13480', '13490', '13605', '13606', '13607', '13611', '13612', '13615', '13616', '13617', '13619', '13624', '13628', '13634', '13636', '13638', '13640', '13642', '13647', '13651', '13657', '13676', '13677', '13685', '13688', '13732', '13738', '13744', '13747', '13758', '13777', '13778', '13784', '13795', '13802', '13814', '13815', '13820', '13826', '13827', '13833', '13834', '13838', '13860', '13861', '14001', '14005', '14009', '14011', '14020', '14025', '14027', '14030', '14033', '14036', '14041', '14057', '14061', '14063', '14070', '14080', '14081', '14103', '14105', '14108', '14111', '14112', '14126', '14129', '14131', '14132', '14134', '14135', '14136', '14139', '14141', '14143', '14168', '14169', '14170', '14172', '14173', '14174', '14411', '14414', '14416', '14422', '14423', '14424', '14428', '14429', '14432', '14437', '14443', '14449', '14452', '14454', '14456', '14464', '14469', '14470', '14472', '14475', '14478', '14480', '14482', '14485', '14487', '14489', '14504', '14505', '14506', '14511', '14518', '14519', '14520', '14522', '14527', '14529', '14530', '14532', '14533', '14538', '14543', '14544', '14545', '14546', '14548', '14551', '14555', '14556', '14557', '14563', '14568', '14569', '14572', '14585', '14588', '14589', '14592', '14701', '14707', '14710', '14712', '14716', '14720', '14732', '14733', '14752', '14756', '14757', '14760', '14783', '14785', '14787', '14788', '14802', '14810', '14816', '14817', '14818', '14824', '14825', '14827', '14830', '14831', '14838', '14843', '14847', '14854', '14856', '14863', '14864', '14865', '14867', '14869', '14871', '14872', '14881', '14882', '14886', '14891', '14894', '15019', '15020', '15026', '15030', '15031', '15033', '15037', '15050', '15052', '15053', '15054', '15057', '15059', '15060', '15075', '15076', '15077', '15081', '15088', '15126', '15313', '15314', '15315', '15316', '15320', '15324', '15325', '15327', '15330', '15331', '15332', '15334', '15340', '15344', '15346', '15347', '15348', '15351', '15353', '15357', '15358', '15365', '15366', '15368', '15370', '15378', '15379', '15413', '15415', '15417', '15420', '15421', '15422', '15423', '15427', '15428', '15429', '15430', '15431', '15435', '15437', '15438', '15442', '15443', '15444', '15445', '15446', '15447', '15449', '15450', '15454', '15458', '15460', '15461', '15462', '15463', '15466', '15468', '15469', '15473', '15474', '15475', '15476', '15477', '15478', '15479', '15480', '15482', '15483', '15490', '15501', '15520', '15521', '15522', '15537', '15539', '15541', '15546', '15549', '15554', '15562', '15610', '15612', '15613', '15615', '15617', '15618', '15622', '15627', '15628', '15630', '15641', '15646', '15656', '15658', '15670', '15671', '15679', '15680', '15681', '15682', '15684', '15688', '15690', '15698', '15710', '15714', '15716', '15717', '15720', '15721', '15722', '15728', '15731', '15733', '15734', '15738', '15739', '15744', '15748', '15750', '15752', '15754', '15761', '15762', '15764', '15767', '15773', '15823', '15824', '15825', '15827', '15840', '15841', '15845', '15846', '15851', '15853', '15857', '15865', '15866', '15906', '15909', '15920', '15922', '15927', '15928', '15930', '15931', '15934', '15935', '15937', '15938', '15943', '15946', '15949', '15954', '15958', '15960', '15962', '15963', '16002', '16022', '16023', '16024', '16025', '16027', '16030', '16033', '16037', '16038', '16039', '16051', '16052', '16053', '16055', '16056', '16057', '16061', '16102', '16105', '16112', '16113', '16116', '16117', '16123', '16125', '16132', '16133', '16134', '16136', '16137', '16140', '16142', '16143', '16150', '16151', '16153', '16154', '16155', '16156', '16159', '16160', '16172', '16201', '16217', '16223', '16229', '16232', '16236', '16246', '16250', '16254', '16262', '16301', '16311', '16314', '16316', '16319', '16323', '16326', '16327', '16333', '16346', '16352', '16354', '16360', '16365', '16366', '16367', '16368', '16369', '16372', '16401', '16403', '16406', '16407', '16410', '16411', '16412', '16413', '16417', '16422', '16424', '16426', '16428', '16430', '16433', '16438', '16440', '16441', '16443', '16475', '16613', '16614', '16620', '16624', '16629', '16630', '16631', '16633', '16637', '16640', '16641', '16646', '16652', '16654', '16659', '16660', '16662', '16671', '16681', '16684', '16686', '16701', '16725', '16821', '16823', '16826', '16827', '16828', '16833', '16840', '16844', '16847', '16848', '16851', '16870', '16873', '16876', '16877', '16879', '16901', '16910', '16920', '16933', '17007', '17009', '17014', '17018', '17020', '17023', '17024', '17026', '17028', '17032', '17039', '17044', '17053', '17061', '17065', '17067', '17069', '17073', '17074', '17077', '17080', '17081', '17090', '17094', '17098', '17214', '17221', '17223', '17225', '17231', '17233', '17235', '17236', '17237', '17241', '17246', '17247', '17249', '17252', '17253', '17254', '17256', '17257', '17261', '17266', '17270', '17272', '17301', '17303', '17304', '17306', '17307', '17309', '17310', '17311', '17314', '17316', '17318', '17320', '17321', '17322', '17325', '17327', '17329', '17340', '17343', '17349', '17350', '17352', '17358', '17360', '17362', '17363', '17364', '17365', '17372', '17502', '17505', '17506', '17507', '17516', '17519', '17528', '17529', '17532', '17534', '17535', '17562', '17565', '17566', '17567', '17569', '17572', '17578', '17579', '17581', '17582', '17702', '17720', '17721', '17726', '17728', '17731', '17737', '17740', '17744', '17745', '17748', '17750', '17751', '17752', '17762', '17767', '17769', '17772', '17777', '17779', '17801', '17810', '17820', '17827', '17832', '17833', '17834', '17840', '17842', '17844', '17846', '17847', '17850', '17851', '17855', '17856', '17857', '17860', '17861', '17864', '17865', '17872', '17876', '17881', '17889', '17921', '17922', '17925', '17932', '17933', '17934', '17935', '17936', '17942', '17943', '17946', '17948', '17949', '17951', '17959', '17963', '17966', '17967', '17974', '17976', '17978', '17979', '17980', '17981', '18012', '18013', '18038', '18051', '18053', '18054', '18056', '18058', '18066', '18068', '18069', '18070', '18071', '18074', '18077', '18080', '18083', '18084', '18092', '18201', '18210', '18211', '18214', '18216', '18219', '18221', '18222', '18224', '18229', '18231', '18235', '18237', '18240', '18244', '18245', '18247', '18250', '18251', '18252', '18254', '18255', '18256', '18301', '18302', '18320', '18324', '18326', '18328', '18330', '18331', '18332', '18333', '18336', '18337', '18342', '18343', '18346', '18347', '18352', '18353', '18354', '18355', '18356', '18360', '18370', '18407', '18411', '18414', '18416', '18424', '18427', '18430', '18431', '18433', '18436', '18438', '18440', '18444', '18447', '18451', '18458', '18459', '18463', '18472', '18503', '18601', '18603', '18610', '18615', '18617', '18618', '18621', '18626', '18644', '18653', '18654', '18655', '18657', '18660', '18707', '18801', '18810', '18813', '18814', '18817', '18820', '18821', '18822', '18826', '18827', '18834', '18843', '18847', '18848', '18910', '18911', '18917', '18921', '18922', '18923', '18927', '18930', '18934', '18938', '18942', '18944', '18947', '18949', '18951', '18953', '18963', '18972', '18981', '19009', '19310', '19330', '19343', '19344', '19351', '19360', '19362', '19363', '19390', '19421', '19425', '19435', '19465', '19470', '19472', '19474', '19475', '19481', '19494', '19504', '19506', '19508', '19511', '19512', '19516', '19518', '19519', '19522', '19523', '19525', '19526', '19530', '19534', '19535', '19536', '19538', '19539', '19540', '19541', '19543', '19545', '19547', '19549', '19551', '19554', '19555', '19564', '19565', '19567', '19706', '19716', '19730', '19731', '19734', '19735', '19930', '19931', '19933', '19934', '19938', '19939', '19940', '19941', '19943', '19944', '19945', '19946', '19947', '19950', '19951', '19952', '19954', '19956', '19960', '19962', '19963', '19968', '19977', '19979', '20106', '20107', '20119', '20129', '20131', '20132', '20134', '20135', '20137', '20138', '20139', '20141', '20142', '20143', '20158', '20160', '20180', '20181', '20182', '20184', '20185', '20186', '20187', '20197', '20242', '20602', '20604', '20607', '20610', '20611', '20612', '20613', '20615', '20616', '20617', '20620', '20622', '20623', '20627', '20629', '20632', '20634', '20635', '20636', '20637', '20639', '20640', '20643', '20646', '20650', '20653', '20657', '20658', '20659', '20660', '20667', '20670', '20675', '20676', '20677', '20678', '20684', '20685', '20688', '20689', '20690', '20693', '20695', '20711', '20732', '20733', '20736', '20744', '20749', '20751', '20754', '20758', '20764', '20765', '20770', '20776', '20777', '20778', '20779', '20833', '20841', '20860', '20861', '20862', '20872', '20882', '20993', '21005', '21010', '21013', '21023', '21028', '21032', '21035', '21036', '21037', '21042', '21047', '21048', '21050', '21051', '21056', '21057', '21071', '21074', '21078', '21082', '21084', '21102', '21104', '21105', '21111', '21120', '21131', '21136', '21153', '21154', '21155', '21156', '21157', '21158', '21163', '21522', '21528', '21529', '21532', '21539', '21541', '21545', '21550', '21562', '21601', '21613', '21617', '21619', '21620', '21623', '21628', '21629', '21632', '21638', '21639', '21643', '21647', '21649', '21651', '21652', '21653', '21654', '21655', '21660', '21663', '21664', '21666', '21670', '21671', '21710', '21713', '21716', '21719', '21720', '21721', '21722', '21723', '21727', '21733', '21737', '21738', '21746', '21754', '21755', '21756', '21758', '21769', '21771', '21773', '21774', '21776', '21777', '21779', '21780', '21782', '21783', '21784', '21787', '21788', '21791', '21794', '21797', '21798', '21811', '21813', '21830', '21849', '21850', '21851', '21862', '21866', '21874', '21875', '21890', '21901', '21903', '21904', '21911', '21916', '21917', '21921', '21922', '22037', '22066', '22082', '22134', '22135', '22172', '22451', '22480', '22481', '22485', '22508', '22526', '22544', '22551', '22553', '22560', '22611', '22620', '22623', '22625', '22642', '22645', '22646', '22649', '22657', '22660', '22663', '22664', '22701', '22712', '22720', '22722', '22724', '22728', '22731', '22734', '22742', '22802', '22803', '22815', '22821', '22824', '22827', '22831', '22835', '22840', '22844', '22846', '22853', '22923', '22932', '22936', '22942', '22947', '22957', '22958', '22960', '22963', '22964', '22965', '22968', '22974', '22987', '22989', '23001', '23009', '23043', '23061', '23064', '23066', '23069', '23072', '23092', '23102', '23103', '23120', '23124', '23125', '23129', '23131', '23139', '23141', '23146', '23160', '23183', '23184', '23192', '23304', '23337', '23397', '23418', '23430', '23431', '23434', '23440', '23442', '23457', '23483', '23487', '23651', '23691', '23803', '23804', '23805', '23806', '23824', '23829', '23838', '23842', '23847', '23851', '23860', '23870', '23891', '23899', '23901', '23909', '23919', '23939', '23950', '23970', '24058', '24064', '24078', '24083', '24089', '24092', '24093', '24095', '24112', '24114', '24115', '24121', '24122', '24134', '24151', '24157', '24168', '24174', '24175', '24176', '24184', '24202', '24210', '24211', '24212', '24216', '24219', '24266', '24273', '24277', '24290', '24293', '24301', '24311', '24333', '24354', '24382', '24416', '24431', '24437', '24440', '24441', '24450', '24457', '24467', '24469', '24471', '24486', '24517', '24523', '24527', '24540', '24541', '24543', '24550', '24570', '24572', '24588', '24605', '24606', '24612', '24635', '24637', '24641', '24701', '24712', '24731', '24737', '24739', '24740', '24751', '24816', '24855', '24857', '24881', '24901', '24902', '24957', '24970', '24986', '25015', '25024', '25033', '25040', '25070', '25071', '25081', '25086', '25090', '25103', '25108', '25110', '25139', '25143', '25156', '25159', '25162', '25165', '25168', '25185', '25186', '25202', '25213', '25260', '25262', '25264', '25306', '25311', '25312', '25315', '25404', '25410', '25413', '25414', '25420', '25423', '25425', '25427', '25429', '25430', '25443', '25510', '25530', '25535', '25541', '25545', '25547', '25559', '25562', '25570', '25601', '25606', '25611', '25612', '25614', '25624', '25628', '25630', '25639', '25652', '25653', '25691', '25810', '25813', '25823', '25825', '25826', '25840', '25849', '25855', '25873', '25878', '25901', '25909', '25911', '26030', '26031', '26035', '26037', '26041', '26050', '26060', '26075', '26104', '26120', '26134', '26150', '26155', '26159', '26164', '26181', '26187', '26201', '26241', '26275', '26292', '26346', '26347', '26349', '26366', '26369', '26378', '26386', '26404', '26408', '26416', '26422', '26424', '26431', '26438', '26452', '26520', '26524', '26531', '26534', '26542', '26543', '26544', '26547', '26560', '26568', '26574', '26576', '26578', '26587', '26588', '26591', '26719', '26726', '26750', '26753', '26767', '26847', '26855', '27006', '27009', '27018', '27019', '27021', '27025', '27028', '27030', '27041', '27042', '27043', '27045', '27049', '27050', '27052', '27055', '27094', '27098', '27099', '27202', '27205', '27209', '27214', '27217', '27228', '27233', '27244', '27248', '27249', '27253', '27258', '27278', '27283', '27288', '27289', '27292', '27293', '27294', '27298', '27312', '27313', '27317', '27320', '27321', '27323', '27330', '27331', '27342', '27344', '27350', '27351', '27357', '27359', '27370', '27375', '27376', '27501', '27503', '27504', '27508', '27522', '27525', '27527', '27530', '27533', '27536', '27537', '27542', '27546', '27555', '27556', '27562', '27568', '27573', '27576', '27581', '27584', '27591', '27592', '27593', '27596', '27597', '27801', '27802', '27803', '27807', '27809', '27811', '27813', '27819', '27823', '27825', '27828', '27837', '27855', '27856', '27857', '27863', '27864', '27868', '27870', '27880', '27881', '27886', '27889', '27891', '27892', '27906', '27909', '27910', '27916', '27917', '27929', '27930', '27932', '27939', '27941', '27944', '27947', '27949', '27954', '27958', '27959', '27964', '27966', '27981', '27982', '27986', '28001', '28002', '28009', '28016', '28017', '28019', '28021', '28024', '28033', '28034', '28038', '28039', '28040', '28043', '28073', '28077', '28080', '28086', '28089', '28097', '28107', '28108', '28111', '28112', '28115', '28123', '28124', '28136', '28138', '28139', '28150', '28151', '28152', '28160', '28163', '28164', '28166', '28168', '28169', '28174', '28312', '28326', '28327', '28328', '28329', '28330', '28333', '28334', '28335', '28339', '28343', '28345', '28349', '28351', '28352', '28355', '28356', '28357', '28360', '28364', '28368', '28371', '28372', '28373', '28375', '28376', '28378', '28379', '28380', '28384', '28391', '28392', '28394', '28422', '28425', '28443', '28445', '28457', '28460', '28462', '28466', '28468', '28472', '28479', '28501', '28502', '28503', '28504', '28512', '28513', '28519', '28522', '28529', '28530', '28532', '28539', '28544', '28551', '28557', '28570', '28574', '28575', '28577', '28582', '28584', '28594', '28605', '28607', '28609', '28612', '28619', '28621', '28625', '28628', '28630', '28638', '28642', '28645', '28650', '28655', '28656', '28659', '28661', '28666', '28668', '28671', '28673', '28678', '28680', '28681', '28682', '28688', '28691', '28694', '28697', '28699', '28701', '28711', '28715', '28716', '28717', '28719', '28720', '28721', '28722', '28723', '28725', '28729', '28730', '28731', '28733', '28734', '28735', '28737', '28739', '28741', '28742', '28744', '28745', '28748', '28750', '28752', '28757', '28760', '28766', '28770', '28777', '28778', '28779', '28782', '28786', '28787', '28788', '28901', '28903', '29006', '29020', '29021', '29044', '29046', '29053', '29054', '29070', '29074', '29075', '29079', '29102', '29108', '29115', '29118', '29122', '29123', '29127', '29153', '29154', '29160', '29161', '29322', '29323', '29325', '29330', '29333', '29340', '29342', '29348', '29356', '29360', '29376', '29386', '29388', '29430', '29433', '29449', '29461', '29486', '29487', '29488', '29493', '29511', '29520', '29527', '29532', '29536', '29541', '29550', '29560', '29566', '29568', '29569', '29571', '29576', '29585', '29588', '29591', '29594', '29627', '29630', '29643', '29644', '29653', '29654', '29656', '29657', '29667', '29669', '29670', '29671', '29672', '29675', '29677', '29678', '29682', '29688', '29689', '29690', '29691', '29693', '29697', '29702', '29704', '29710', '29714', '29720', '29721', '29744', '29745', '29805', '29809', '29829', '29906', '29907', '29912', '29915', '29920', '29923', '29927', '29935', '30011', '30014', '30016', '30025', '30028', '30052', '30054', '30094', '30107', '30110', '30114', '30116', '30117', '30125', '30129', '30132', '30138', '30143', '30145', '30149', '30153', '30161', '30162', '30164', '30165', '30172', '30179', '30187', '30212', '30215', '30223', '30224', '30228', '30229', '30233', '30241', '30248', '30250', '30252', '30261', '30263', '30264', '30268', '30271', '30273', '30275', '30276', '30277', '30284', '30286', '30290', '30401', '30415', '30417', '30423', '30429', '30436', '30448', '30461', '30464', '30474', '30475', '30506', '30510', '30511', '30514', '30527', '30528', '30530', '30531', '30533', '30535', '30538', '30539', '30543', '30548', '30549', '30553', '30563', '30565', '30575', '30577', '30597', '30598', '30620', '30621', '30623', '30628', '30635', '30638', '30643', '30645', '30646', '30650', '30655', '30656', '30666', '30677', '30683', '30701', '30705', '30707', '30710', '30725', '30728', '30739', '30740', '30750', '30752', '30753', '30755', '30756', '30757', '30814', '30815', '30824', '30830', '31004', '31008', '31010', '31015', '31029', '31030', '31032', '31034', '31046', '31047', '31052', '31059', '31061', '31069', '31086', '31220', '31301', '31302', '31307', '31308', '31310', '31312', '31313', '31315', '31318', '31324', '31328', '31329', '31333', '31411', '31503', '31515', '31516', '31522', '31523', '31534', '31535', '31545', '31547', '31548', '31558', '31564', '31605', '31620', '31632', '31636', '31709', '31719', '31720', '31721', '31722', '31728', '31729', '31753', '31757', '31763', '31768', '31776', '31782', '31788', '31791', '31792', '31794', '31804', '31807', '31808', '31822', '31829', '31831', '31833', '31905', '32007', '32011', '32024', '32025', '32041', '32043', '32055', '32056', '32063', '32064', '32079', '32080', '32083', '32084', '32091', '32097', '32113', '32132', '32136', '32137', '32168', '32177', '32178', '32193', '32195', '32310', '32326', '32327', '32330', '32332', '32333', '32351', '32353', '32355', '32409', '32410', '32422', '32424', '32428', '32433', '32439', '32440', '32446', '32447', '32448', '32456', '32457', '32463', '32530', '32533', '32536', '32539', '32563', '32570', '32572', '32583', '32615', '32617', '32618', '32643', '32655', '32658', '32664', '32669', '32696', '32732', '32736', '32744', '32745', '32754', '32757', '32776', '32777', '32784', '32798', '32815', '32833', '32908', '32911', '32949', '32960', '32962', '32963', '32965', '32967', '32968', '32970', '32976', '33031', '33035', '33036', '33037', '33042', '33043', '33044', '33050', '33051', '33052', '33070', '33187', '33327', '33455', '33475', '33503', '33513', '33521', '33523', '33525', '33526', '33527', '33530', '33537', '33540', '33545', '33547', '33565', '33567', '33574', '33576', '33587', '33592', '33593', '33598', '33820', '33825', '33826', '33827', '33830', '33831', '33839', '33843', '33847', '33848', '33854', '33856', '33859', '33860', '33863', '33868', '33870', '33871', '33872', '33873', '33875', '33877', '33898', '33920', '33921', '33922', '33924', '33945', '33946', '33947', '33955', '33956', '33957', '33972', '33980', '33981', '34114', '34117', '34120', '34140', '34219', '34223', '34237', '34241', '34269', '34275', '34286', '34289', '34290', '34291', '34292', '34423', '34428', '34429', '34430', '34431', '34432', '34434', '34436', '34441', '34442', '34445', '34446', '34447', '34448', '34450', '34451', '34452', '34453', '34460', '34461', '34465', '34473', '34482', '34487', '34488', '34489', '34601', '34603', '34605', '34607', '34610', '34636', '34654', '34669', '34688', '34714', '34715', '34729', '34736', '34737', '34753', '34755', '34762', '34771', '34772', '34785', '34797', '34946', '34947', '34949', '34951', '34957', '34987', '34990', '35010', '35011', '35013', '35016', '35020', '35023', '35032', '35040', '35043', '35045', '35052', '35054', '35057', '35058', '35060', '35062', '35073', '35091', '35096', '35112', '35114', '35115', '35116', '35117', '35119', '35120', '35121', '35125', '35126', '35127', '35128', '35135', '35139', '35146', '35147', '35148', '35149', '35150', '35160', '35161', '35176', '35180', '35181', '35185', '35187', '35224', '35475', '35490', '35501', '35503', '35504', '35575', '35582', '35584', '35611', '35614', '35620', '35634', '35640', '35650', '35653', '35660', '35673', '35674', '35739', '35741', '35748', '35750', '35754', '35759', '35760', '35761', '35768', '35773', '35901', '35904', '35905', '35906', '35950', '35951', '35954', '35956', '35957', '35967', '35968', '35976', '35986', '35987', '36020', '36022', '36025', '36027', '36038', '36065', '36067', '36072', '36078', '36081', '36082', '36087', '36088', '36092', '36093', '36105', '36125', '36205', '36250', '36253', '36260', '36265', '36268', '36271', '36277', '36279', '36312', '36313', '36321', '36322', '36340', '36345', '36349', '36350', '36352', '36360', '36361', '36370', '36371', '36376', '36420', '36426', '36427', '36429', '36460', '36461', '36502', '36503', '36504', '36509', '36512', '36523', '36528', '36541', '36542', '36543', '36544', '36549', '36555', '36561', '36567', '36568', '36571', '36572', '36574', '36576', '36578', '36580', '36587', '36612', '36613', '36701', '36702', '36703', '36732', '36745', '36804', '36854', '36856', '36859', '36865', '36869', '36877', '37014', '37015', '37020', '37029', '37030', '37031', '37034', '37035', '37037', '37046', '37048', '37055', '37056', '37062', '37073', '37074', '37080', '37082', '37083', '37085', '37090', '37091', '37110', '37111', '37143', '37146', '37148', '37152', '37153', '37160', '37166', '37172', '37180', '37187', '37188', '37189', '37303', '37304', '37318', '37321', '37326', '37331', '37336', '37347', '37349', '37352', '37354', '37355', '37373', '37379', '37388', '37389', '37398', '37616', '37618', '37641', '37642', '37643', '37644', '37656', '37690', '37692', '37694', '37709', '37711', '37719', '37721', '37725', '37737', '37738', '37742', '37743', '37744', '37748', '37755', '37757', '37760', '37763', '37766', '37772', '37774', '37778', '37803', '37806', '37813', '37820', '37821', '37822', '37824', '37840', '37845', '37854', '37857', '37860', '37865', '37871', '37874', '37876', '37877', '37886', '37890', '37891', '38001', '38004', '38006', '38011', '38019', '38021', '38024', '38025', '38028', '38036', '38047', '38058', '38059', '38060', '38063', '38071', '38127', '38225', '38235', '38237', '38238', '38242', '38255', '38261', '38271', '38281', '38320', '38324', '38338', '38340', '38343', '38344', '38351', '38355', '38358', '38362', '38375', '38382', '38389', '38451', '38456', '38464', '38474', '38478', '38483', '38488', '38544', '38558', '38559', '38570', '38571', '38574', '38578', '38580', '38583', '38606', '38632', '38644', '38651', '38652', '38655', '38664', '38668', '38680', '38686', '38701', '38703', '38722', '38731', '38732', '38733', '38751', '38756', '38760', '38771', '38780', '38781', '38821', '38825', '38826', '38827', '38829', '38841', '38849', '38857', '38863', '38865', '38866', '38869', '38880', '38915', '38947', '38957', '38966', '39042', '39046', '39073', '39074', '39080', '39111', '39167', '39170', '39173', '39180', '39181', '39182', '39212', '39305', '39320', '39325', '39342', '39345', '39350', '39359', '39426', '39429', '39436', '39437', '39443', '39457', '39459', '39460', '39463', '39465', '39466', '39475', '39477', '39520', '39521', '39552', '39553', '39556', '39558', '39561', '39563', '39565', '39571', '39601', '39602', '39603', '39630', '39648', '39649', '39666', '39701', '39702', '39736', '39759', '39766', '39773', '39819', '39828', '39852', '39885', '40004', '40010', '40012', '40013', '40022', '40023', '40026', '40031', '40032', '40033', '40061', '40063', '40065', '40071', '40108', '40117', '40121', '40165', '40175', '40310', '40317', '40319', '40320', '40339', '40342', '40347', '40348', '40351', '40353', '40357', '40361', '40362', '40390', '40403', '40404', '40434', '40448', '40484', '40601', '40602', '40621', '40737', '40740', '40744', '40827', '40831', '40845', '40854', '40906', '40944', '40965', '41030', '41031', '41035', '41056', '41059', '41080', '41092', '41095', '41096', '41129', '41143', '41146', '41168', '41183', '41216', '41230', '41240', '41268', '41274', '41314', '41365', '41501', '41503', '41517', '41542', '41560', '41601', '41605', '41612', '41621', '41622', '41635', '41649', '41650', '41651', '41653', '41659', '41666', '41669', '41701', '41810', '41826', '41844', '41858', '42020', '42025', '42027', '42029', '42044', '42051', '42058', '42061', '42066', '42082', '42086', '42122', '42134', '42135', '42141', '42142', '42152', '42159', '42163', '42164', '42171', '42234', '42240', '42241', '42262', '42274', '42276', '42320', '42330', '42347', '42354', '42367', '42374', '42402', '42410', '42413', '42431', '42440', '42445', '42452', '42501', '42503', '42638', '42642', '42718', '42719', '42755', '43001', '43005', '43007', '43008', '43010', '43019', '43022', '43023', '43025', '43028', '43029', '43030', '43031', '43036', '43040', '43046', '43047', '43050', '43055', '43056', '43058', '43067', '43070', '43074', '43076', '43077', '43078', '43101', '43102', '43103', '43105', '43112', '43117', '43126', '43127', '43140', '43144', '43146', '43151', '43154', '43155', '43157', '43302', '43311', '43315', '43316', '43317', '43321', '43322', '43324', '43325', '43326', '43331', '43338', '43342', '43350', '43351', '43402', '43410', '43416', '43420', '43430', '43431', '43432', '43433', '43436', '43437', '43438', '43439', '43440', '43441', '43443', '43447', '43449', '43450', '43452', '43456', '43468', '43469', '43502', '43504', '43505', '43506', '43510', '43512', '43515', '43519', '43520', '43522', '43526', '43529', '43530', '43531', '43542', '43545', '43550', '43553', '43555', '43558', '43567', '43713', '43717', '43718', '43719', '43721', '43723', '43724', '43725', '43731', '43733', '43735', '43740', '43759', '43761', '43764', '43768', '43791', '43804', '43812', '43822', '43828', '43830', '43836', '43845', '43906', '43909', '43912', '43913', '43920', '43926', '43934', '43935', '43939', '43940', '43943', '43947', '43953', '43961', '43963', '43964', '43967', '43971', '43974', '43981', '43985', '44003', '44010', '44021', '44030', '44041', '44044', '44047', '44048', '44049', '44050', '44057', '44062', '44064', '44065', '44068', '44074', '44080', '44084', '44086', '44088', '44089', '44090', '44201', '44217', '44231', '44234', '44235', '44253', '44254', '44255', '44264', '44265', '44270', '44272', '44273', '44275', '44280', '44287', '44288', '44401', '44402', '44403', '44408', '44411', '44412', '44413', '44415', '44418', '44423', '44424', '44431', '44432', '44436', '44439', '44443', '44444', '44445', '44454', '44455', '44470', '44473', '44490', '44491', '44606', '44608', '44609', '44610', '44612', '44615', '44617', '44618', '44621', '44624', '44626', '44627', '44633', '44634', '44640', '44643', '44644', '44645', '44653', '44654', '44657', '44659', '44660', '44662', '44665', '44666', '44669', '44671', '44675', '44676', '44677', '44678', '44680', '44681', '44682', '44683', '44688', '44689', '44690', '44693', '44697', '44730', '44801', '44809', '44811', '44813', '44814', '44815', '44816', '44820', '44822', '44824', '44827', '44830', '44833', '44838', '44842', '44843', '44845', '44846', '44847', '44848', '44850', '44854', '44856', '44859', '44861', '44865', '44866', '44875', '44878', '44880', '44881', '44883', '44888', '44889', '44890', '45003', '45032', '45052', '45068', '45111', '45113', '45118', '45119', '45122', '45131', '45133', '45145', '45152', '45154', '45155', '45156', '45157', '45162', '45164', '45172', '45302', '45304', '45306', '45307', '45309', '45310', '45314', '45316', '45318', '45320', '45325', '45327', '45328', '45330', '45331', '45335', '45336', '45338', '45339', '45341', '45344', '45345', '45347', '45350', '45351', '45352', '45353', '45358', '45359', '45360', '45361', '45363', '45369', '45370', '45372', '45378', '45380', '45381', '45383', '45389', '45502', '45619', '45628', '45629', '45631', '45636', '45640', '45644', '45648', '45653', '45661', '45669', '45673', '45674', '45690', '45692', '45694', '45698', '45699', '45701', '45712', '45714', '45740', '45760', '45780', '45784', '45806', '45807', '45808', '45810', '45816', '45817', '45822', '45826', '45828', '45833', '45846', '45848', '45853', '45854', '45859', '45861', '45865', '45866', '45869', '45870', '45871', '45872', '45875', '45876', '45877', '45879', '45883', '45884', '45885', '45887', '45888', '45889', '45891', '45895', '46001', '46011', '46012', '46017', '46030', '46031', '46034', '46036', '46040', '46041', '46044', '46047', '46051', '46063', '46064', '46065', '46067', '46068', '46069', '46072', '46076', '46102', '46103', '46106', '46110', '46122', '46125', '46126', '46129', '46130', '46131', '46135', '46149', '46151', '46154', '46157', '46158', '46167', '46176', '46301', '46303', '46310', '46341', '46346', '46356', '46371', '46372', '46377', '46391', '46406', '46407', '46409', '46506', '46511', '46524', '46528', '46534', '46536', '46537', '46538', '46540', '46542', '46543', '46550', '46552', '46553', '46554', '46555', '46562', '46563', '46565', '46567', '46571', '46573', '46574', '46595', '46701', '46703', '46710', '46711', '46721', '46723', '46725', '46731', '46732', '46733', '46737', '46743', '46745', '46747', '46750', '46755', '46763', '46767', '46769', '46770', '46777', '46781', '46782', '46784', '46786', '46787', '46788', '46789', '46791', '46793', '46795', '46796', '46797', '46798', '46799', '46914', '46915', '46916', '46919', '46922', '46931', '46932', '46933', '46936', '46938', '46943', '46945', '46947', '46952', '46962', '46967', '46970', '46975', '46979', '46980', '46987', '46989', '46992', '46994', '46995', '47001', '47003', '47006', '47022', '47025', '47033', '47034', '47060', '47102', '47104', '47106', '47111', '47112', '47114', '47119', '47122', '47124', '47126', '47136', '47143', '47161', '47164', '47165', '47166', '47167', '47170', '47177', '47203', '47240', '47244', '47250', '47274', '47280', '47302', '47305', '47306', '47308', '47322', '47324', '47330', '47331', '47334', '47335', '47356', '47361', '47362', '47371', '47381', '47383', '47385', '47392', '47394', '47396', '47420', '47421', '47429', '47441', '47451', '47455', '47462', '47464', '47469', '47501', '47522', '47523', '47532', '47535', '47541', '47558', '47579', '47586', '47588', '47591', '47596', '47601', '47610', '47613', '47620', '47638', '47639', '47648', '47670', '47805', '47834', '47842', '47845', '47848', '47851', '47857', '47869', '47876', '47880', '47881', '47882', '47884', '47885', '47920', '47925', '47933', '47955', '47958', '47960', '47964', '47965', '47997', '48001', '48003', '48005', '48014', '48023', '48027', '48039', '48041', '48048', '48049', '48050', '48054', '48062', '48063', '48064', '48079', '48096', '48115', '48117', '48118', '48130', '48131', '48133', '48145', '48157', '48158', '48160', '48161', '48166', '48169', '48179', '48189', '48190', '48191', '48353', '48356', '48357', '48367', '48370', '48412', '48413', '48417', '48421', '48428', '48429', '48436', '48438', '48440', '48444', '48446', '48449', '48451', '48455', '48457', '48458', '48460', '48461', '48462', '48463', '48464', '48471', '48473', '48476', '48607', '48611', '48612', '48616', '48617', '48622', '48623', '48626', '48629', '48630', '48631', '48633', '48634', '48650', '48655', '48657', '48658', '48661', '48722', '48723', '48724', '48734', '48738', '48746', '48750', '48755', '48757', '48758', '48763', '48768', '48769', '48801', '48804', '48808', '48812', '48813', '48817', '48820', '48827', '48833', '48836', '48837', '48838', '48846', '48848', '48852', '48853', '48857', '48858', '48859', '48867', '48872', '48875', '48876', '48879', '48881', '48882', '49010', '49012', '49017', '49031', '49032', '49034', '49036', '49038', '49042', '49043', '49046', '49047', '49051', '49055', '49058', '49060', '49062', '49064', '49065', '49068', '49070', '49075', '49078', '49079', '49080', '49083', '49087', '49088', '49090', '49091', '49093', '49097', '49098', '49101', '49102', '49103', '49104', '49106', '49111', '49112', '49113', '49115', '49117', '49126', '49128', '49129', '49130', '49221', '49224', '49228', '49229', '49230', '49233', '49234', '49236', '49237', '49239', '49240', '49242', '49245', '49250', '49254', '49258', '49259', '49261', '49263', '49265', '49267', '49269', '49270', '49272', '49277', '49278', '49282', '49283', '49285', '49286', '49287', '49302', '49307', '49311', '49312', '49314', '49319', '49323', '49329', '49330', '49331', '49333', '49335', '49337', '49339', '49344', '49345', '49348', '49404', '49406', '49409', '49412', '49413', '49415', '49430', '49431', '49434', '49435', '49437', '49440', '49445', '49448', '49453', '49457', '49458', '49460', '49461', '49601', '49610', '49616', '49627', '49628', '49629', '49634', '49635', '49636', '49637', '49643', '49646', '49650', '49657', '49660', '49675', '49676', '49677', '49685', '49690', '49696', '49701', '49707', '49712', '49720', '49723', '49734', '49735', '49737', '49740', '49781', '49782', '49783', '49785', '49796', '49801', '49802', '49808', '49837', '49841', '49852', '49858', '49863', '49864', '49870', '49871', '49874', '49876', '49894', '49915', '49917', '49931', '49964', '50003', '50035', '50036', '50038', '50047', '50061', '50063', '50069', '50078', '50105', '50109', '50112', '50124', '50125', '50126', '50127', '50137', '50152', '50158', '50160', '50163', '50201', '50208', '50211', '50219', '50226', '50236', '50241', '50243', '50244', '50423', '50427', '50435', '50436', '50511', '50521', '50529', '50548', '50588', '50595', '50616', '50620', '50622', '50631', '50634', '50643', '50644', '50648', '50649', '50652', '50657', '50659', '50662', '50664', '50667', '50677', '51008', '51015', '51031', '51045', '51054', '51109', '51250', '51301', '51331', '51340', '51351', '51360', '51363', '51401', '51459', '51534', '51537', '51543', '51550', '51554', '51601', '51651', '52036', '52040', '52056', '52057', '52060', '52068', '52101', '52135', '52149', '52166', '52175', '52203', '52204', '52205', '52206', '52227', '52228', '52235', '52253', '52312', '52314', '52315', '52318', '52324', '52326', '52333', '52338', '52341', '52345', '52348', '52351', '52353', '52501', '52530', '52534', '52544', '52556', '52566', '52568', '52577', '52601', '52624', '52627', '52632', '52638', '52641', '52648', '52652', '52657', '52726', '52728', '52730', '52742', '52747', '52749', '52752', '52753', '52757', '52761', '52773', '53002', '53006', '53013', '53016', '53017', '53021', '53027', '53033', '53034', '53036', '53037', '53040', '53047', '53048', '53058', '53060', '53061', '53062', '53070', '53073', '53075', '53076', '53081', '53085', '53086', '53090', '53094', '53098', '53102', '53103', '53104', '53105', '53109', '53114', '53115', '53118', '53119', '53120', '53121', '53125', '53126', '53127', '53128', '53138', '53147', '53148', '53149', '53152', '53153', '53159', '53167', '53168', '53176', '53179', '53181', '53182', '53183', '53184', '53185', '53190', '53191', '53507', '53508', '53515', '53521', '53523', '53525', '53526', '53528', '53531', '53533', '53534', '53536', '53538', '53540', '53549', '53551', '53555', '53559', '53560', '53563', '53566', '53571', '53572', '53575', '53578', '53583', '53589', '53594', '53595', '53803', '53808', '53811', '53813', '53817', '53821', '53824', '53901', '53911', '53913', '53916', '53925', '53940', '53947', '53950', '53954', '53955', '53958', '53959', '53960', '53965', '53969', '54002', '54005', '54017', '54020', '54021', '54022', '54023', '54024', '54025', '54123', '54130', '54141', '54153', '54157', '54160', '54162', '54165', '54166', '54169', '54171', '54180', '54201', '54208', '54211', '54215', '54217', '54229', '54232', '54235', '54241', '54403', '54409', '54441', '54455', '54464', '54469', '54482', '54494', '54495', '54501', '54543', '54548', '54568', '54615', '54630', '54636', '54643', '54654', '54656', '54660', '54662', '54669', '54724', '54758', '54760', '54765', '54773', '54812', '54818', '54841', '54850', '54857', '54861', '54868', '54923', '54926', '54929', '54933', '54934', '54941', '54944', '54946', '54947', '54961', '54963', '54971', '54978', '54980', '54981', '54986', '55001', '55003', '55005', '55008', '55011', '55018', '55019', '55020', '55021', '55024', '55025', '55029', '55033', '55038', '55043', '55049', '55052', '55054', '55056', '55057', '55060', '55066', '55070', '55085', '55087', '55090', '55092', '55302', '55308', '55309', '55313', '55315', '55319', '55320', '55327', '55328', '55331', '55341', '55348', '55350', '55352', '55355', '55357', '55358', '55359', '55364', '55366', '55371', '55375', '55377', '55384', '55386', '55609', '55720', '55744', '55745', '55746', '55768', '55792', '55803', '55804', '55808', '55810', '55912', '55920', '55944', '55947', '55950', '55955', '55960', '55976', '56007', '56011', '56017', '56024', '56031', '56035', '56050', '56055', '56058', '56062', '56063', '56071', '56073', '56075', '56084', '56093', '56143', '56146', '56187', '56215', '56241', '56258', '56265', '56273', '56277', '56307', '56308', '56310', '56313', '56320', '56333', '56345', '56369', '56374', '56401', '56430', '56441', '56465', '56470', '56482', '56484', '56501', '56502', '56537', '56538', '56577', '56601', '56630', '56668', '56687', '56716', '56721', '57005', '57006', '57007', '57013', '57020', '57032', '57041', '57042', '57055', '57056', '57068', '57069', '57078', '57202', '57252', '57301', '57346', '57361', '57401', '57402', '57439', '57501', '57719', '57751', '57770', '57783', '58001', '58012', '58042', '58074', '58075', '58076', '58204', '58205', '58401', '58402', '58554', '58601', '58602', '58701', '58703', '58704', '58707', '58801', '58802', '58803', '59004', '59036', '59044', '59106', '59404', '59414', '59477', '59602', '59635', '59714', '59715', '59716', '59717', '59756', '59803', '59812', '59840', '59841', '59855', '59901', '59902', '59937', '60033', '60071', '60072', '60080', '60081', '60097', '60098', '60109', '60112', '60113', '60119', '60129', '60135', '60140', '60144', '60145', '60146', '60147', '60152', '60175', '60180', '60184', '60401', '60407', '60408', '60410', '60416', '60421', '60423', '60442', '60447', '60449', '60450', '60466', '60472', '60474', '60481', '60512', '60536', '60548', '60554', '60560', '60827', '60901', '60932', '60936', '60945', '60950', '60954', '60957', '60970', '61010', '61011', '61013', '61016', '61020', '61021', '61025', '61032', '61036', '61057', '61058', '61061', '61065', '61068', '61071', '61072', '61077', '61081', '61084', '61088', '61240', '61241', '61257', '61273', '61275', '61278', '61284', '61301', '61317', '61320', '61323', '61332', '61335', '61350', '61356', '61362', '61363', '61364', '61371', '61372', '61374', '61411', '61419', '61430', '61443', '61448', '61451', '61455', '61519', '61520', '61523', '61524', '61528', '61530', '61535', '61537', '61539', '61547', '61548', '61553', '61562', '61564', '61568', '61705', '61727', '61729', '61736', '61755', '61764', '61815', '61834', '61847', '61848', '61851', '61856', '61858', '61864', '61873', '61877', '61880', '61883', '61884', '61911', '61920', '61936', '61938', '61944', '61953', '61955', '61956', '62009', '62035', '62046', '62048', '62056', '62058', '62067', '62069', '62077', '62084', '62085', '62087', '62088', '62089', '62090', '62093', '62201', '62202', '62203', '62204', '62215', '62216', '62219', '62224', '62230', '62232', '62233', '62236', '62240', '62243', '62246', '62249', '62250', '62254', '62256', '62258', '62259', '62260', '62264', '62265', '62278', '62279', '62282', '62285', '62286', '62289', '62292', '62293', '62295', '62298', '62352', '62362', '62450', '62454', '62458', '62459', '62467', '62471', '62481', '62517', '62522', '62523', '62525', '62537', '62540', '62549', '62554', '62561', '62563', '62568', '62615', '62650', '62651', '62656', '62690', '62693', '62801', '62812', '62819', '62822', '62825', '62829', '62832', '62841', '62846', '62848', '62874', '62876', '62881', '62883', '62891', '62896', '62902', '62903', '62906', '62918', '62921', '62927', '62930', '62933', '62939', '62946', '62949', '62951', '62966', '62999', '63001', '63012', '63016', '63019', '63020', '63028', '63038', '63039', '63040', '63050', '63051', '63052', '63065', '63069', '63079', '63080', '63084', '63089', '63090', '63341', '63348', '63362', '63379', '63383', '63389', '63401', '63442', '63457', '63501', '63563', '63601', '63640', '63651', '63663', '63742', '63746', '63755', '63758', '63775', '63776', '63779', '63780', '63824', '63826', '63828', '63840', '63841', '63853', '63857', '63863', '63866', '63901', '63902', '64024', '64028', '64034', '64048', '64056', '64060', '64070', '64075', '64078', '64079', '64080', '64089', '64093', '64097', '64136', '64146', '64149', '64163', '64165', '64167', '64429', '64468', '64485', '64504', '64505', '64601', '64628', '64658', '64701', '64735', '64743', '64772', '64830', '64834', '64836', '64840', '64850', '64857', '64858', '64874', '65010', '65020', '65026', '65043', '65049', '65233', '65243', '65251', '65255', '65265', '65270', '65278', '65305', '65340', '65401', '65409', '65453', '65468', '65536', '65559', '65583', '65610', '65613', '65630', '65631', '65672', '65673', '65686', '65706', '65712', '65721', '65726', '65739', '65742', '65757', '66002', '66006', '66007', '66013', '66025', '66036', '66047', '66048', '66067', '66071', '66083', '66113', '66402', '66409', '66420', '66434', '66441', '66503', '66536', '66539', '66542', '66547', '66618', '66712', '66720', '66742', '66749', '66757', '66763', '66782', '66801', '66855', '67005', '67010', '67016', '67030', '67042', '67052', '67062', '67067', '67107', '67110', '67114', '67133', '67147', '67152', '67156', '67337', '67340', '67357', '67460', '67476', '67482', '67501', '67505', '67553', '67585', '67665', '67846', '67851', '67901', '67905', '68001', '68008', '68023', '68025', '68026', '68041', '68059', '68063', '68064', '68068', '68069', '68072', '68310', '68333', '68366', '68430', '68434', '68462', '68467', '68514', '68523', '68527', '68532', '68601', '68602', '68661', '68701', '68731', '68810', '68840', '68845', '68847', '68848', '68865', '68901', '69001', '69341', '69357', '70030', '70031', '70051', '70068', '70069', '70070', '70071', '70076', '70084', '70092', '70126', '70301', '70302', '70310', '70342', '70343', '70345', '70359', '70363', '70364', '70374', '70380', '70381', '70393', '70394', '70395', '70420', '70422', '70427', '70429', '70435', '70437', '70443', '70445', '70448', '70451', '70452', '70454', '70455', '70457', '70464', '70466', '70510', '70511', '70517', '70525', '70526', '70528', '70529', '70533', '70535', '70541', '70546', '70551', '70555', '70563', '70575', '70576', '70578', '70611', '70634', '70640', '70646', '70647', '70659', '70665', '70668', '70704', '70706', '70711', '70714', '70721', '70723', '70733', '70743', '70754', '70760', '70763', '70764', '70765', '70767', '70770', '70774', '70776', '70778', '70780', '70782', '70785', '70791', '70792', '70807', '71006', '71033', '71037', '71055', '71058', '71066', '71075', '71107', '71119', '71220', '71221', '71225', '71240', '71241', '71245', '71247', '71270', '71272', '71273', '71280', '71329', '71350', '71351', '71360', '71405', '71457', '71458', '71459', '71463', '71466', '71485', '71497', '71601', '71602', '71603', '71611', '71612', '71613', '71635', '71654', '71655', '71656', '71657', '71701', '71711', '71730', '71753', '71754', '71759', '71762', '71768', '71801', '71802', '71909', '71910', '71923', '71942', '71943', '71964', '71966', '71998', '71999', '72002', '72007', '72011', '72012', '72015', '72019', '72023', '72058', '72061', '72065', '72068', '72079', '72086', '72104', '72105', '72106', '72110', '72143', '72149', '72150', '72160', '72173', '72176', '72181', '72301', '72310', '72315', '72316', '72329', '72335', '72336', '72364', '72396', '72411', '72417', '72426', '72427', '72445', '72472', '72476', '72501', '72503', '72526', '72543', '72553', '72575', '72601', '72602', '72615', '72626', '72651', '72653', '72654', '72722', '72734', '72739', '72751', '72753', '72761', '72801', '72802', '72812', '72829', '72830', '72834', '72855', '72858', '72865', '72921', '72935', '72936', '72941', '72945', '72951', '73007', '73010', '73018', '73020', '73022', '73023', '73026', '73033', '73036', '73044', '73045', '73048', '73049', '73065', '73068', '73078', '73080', '73084', '73089', '73093', '73150', '73151', '73165', '73173', '73401', '73402', '73459', '73491', '73501', '73502', '73507', '73521', '73522', '73531', '73533', '73536', '73601', '73960', '74003', '74004', '74005', '74008', '74015', '74017', '74018', '74019', '74021', '74031', '74041', '74053', '74063', '74066', '74067', '74068', '74070', '74073', '74075', '74126', '74130', '74344', '74345', '74350', '74354', '74355', '74358', '74361', '74362', '74401', '74402', '74403', '74429', '74444', '74446', '74447', '74456', '74463', '74464', '74465', '74467', '74477', '74501', '74502', '74522', '74554', '74565', '74601', '74602', '74701', '74702', '74720', '74745', '74801', '74802', '74804', '74818', '74820', '74821', '74830', '74837', '74851', '74857', '74868', '74901', '74902', '74936', '74946', '74947', '74953', '74954', '74955', '75009', '75021', '75065', '75076', '75097', '75101', '75103', '75110', '75114', '75117', '75119', '75120', '75121', '75126', '75135', '75142', '75147', '75151', '75152', '75156', '75160', '75164', '75165', '75166', '75167', '75173', '75189', '75401', '75403', '75407', '75409', '75414', '75418', '75444', '75454', '75455', '75456', '75459', '75460', '75461', '75474', '75475', '75482', '75483', '75485', '75489', '75490', '75495', '75551', '75561', '75570', '75603', '75642', '75644', '75647', '75652', '75654', '75658', '75662', '75663', '75670', '75671', '75672', '75680', '75687', '75688', '75693', '75694', '75704', '75751', '75757', '75758', '75762', '75766', '75771', '75773', '75780', '75790', '75791', '75801', '75849', '75958', '75961', '75962', '75964', '75976', '75978', '76008', '76009', '76020', '76031', '76033', '76048', '76049', '76058', '76059', '76071', '76078', '76082', '76085', '76087', '76227', '76240', '76245', '76247', '76249', '76250', '76253', '76258', '76259', '76266', '76268', '76273', '76305', '76351', '76354', '76364', '76367', '76369', '76401', '76402', '76465', '76513', '76522', '76524', '76526', '76528', '76537', '76554', '76558', '76559', '76564', '76571', '76574', '76579', '76596', '76597', '76598', '76599', '76630', '76633', '76645', '76655', '76667', '76676', '76801', '76804', '76904', '76905', '76906', '77016', '77028', '77049', '77050', '77078', '77302', '77316', '77318', '77327', '77336', '77340', '77341', '77342', '77354', '77356', '77357', '77362', '77372', '77378', '77423', '77437', '77441', '77447', '77463', '77467', '77471', '77473', '77474', '77476', '77484', '77486', '77510', '77511', '77512', '77515', '77516', '77517', '77523', '77532', '77534', '77535', '77541', '77542', '77547', '77554', '77563', '77580', '77582', '77583', '77614', '77626', '77632', '77656', '77662', '77713', '77808', '77833', '77834', '77862', '77866', '77868', '77869', '77870', '77905', '77951', '77960', '77971', '77977', '77978', '77979', '77983', '77991', '78006', '78009', '78028', '78029', '78039', '78046', '78069', '78070', '78073', '78101', '78112', '78114', '78121', '78124', '78132', '78133', '78163', '78252', '78263', '78264', '78266', '78330', '78332', '78333', '78336', '78342', '78351', '78358', '78363', '78364', '78373', '78380', '78381', '78382', '78406', '78558', '78559', '78560', '78561', '78562', '78565', '78566', '78578', '78580', '78584', '78585', '78590', '78593', '78595', '78597', '78602', '78612', '78616', '78617', '78619', '78620', '78621', '78623', '78642', '78644', '78645', '78653', '78654', '78669', '78673', '78674', '78676', '78801', '78802', '78834', '78840', '78853', '78951', '79007', '79008', '79012', '79015', '79029', '79033', '79108', '79363', '79366', '79563', '79706', '79711', '79733', '79745', '79766', '79776', '79786', '79836', '79838', '79849', '79911', '79968', '80018', '80403', '80422', '80435', '80437', '80439', '80443', '80454', '80457', '80465', '80477', '80487', '80488', '80498', '80511', '80513', '80514', '80517', '80520', '80530', '80543', '80544', '80603', '80615', '80621', '80623', '80645', '80651', '80653', '80654', '80705', '80722', '80723', '80819', '80831', '80863', '80866', '80908', '80925', '80930', '80939', '81005', '81006', '81007', '81074', '81101', '81102', '81128', '81201', '81212', '81215', '81221', '81225', '81226', '81227', '81242', '81244', '81290', '81301', '81302', '81303', '81321', '81401', '81416', '81526', '81601', '81602', '81611', '81612', '81615', '81620', '81621', '81623', '81631', '81632', '81637', '81657', '81658', '82003', '82009', '82071', '82072', '82414', '82430', '82440', '82501', '82515', '82601', '82636', '82644', '82716', '82717', '82718', '82801', '82833', '82845', '82901', '82902', '82929', '82942', '83001', '83002', '83025', '83204', '83221', '83256', '83318', '83328', '83333', '83336', '83338', '83340', '83341', '83343', '83401', '83403', '83421', '83422', '83440', '83441', '83442', '83454', '83607', '83626', '83634', '83644', '83648', '83656', '83676', '83835', '83837', '83843', '83858', '83864', '83865', '84005', '84006', '84029', '84044', '84049', '84060', '84068', '84074', '84075', '84302', '84315', '84319', '84325', '84326', '84332', '84333', '84335', '84337', '84339', '84633', '84634', '84701', '84720', '84721', '84738', '84742', '84765', '84767', '84770', '84771', '84779', '84780', '84784', '85087', '85123', '85127', '85128', '85138', '85139', '85191', '85193', '85194', '85218', '85221', '85228', '85239', '85243', '85262', '85263', '85331', '85339', '85350', '85355', '85377', '85387', '85396', '85552', '85607', '85608', '85613', '85615', '85620', '85622', '85626', '85640', '85641', '85648', '85650', '85653', '85654', '85658', '85901', '85926', '85929', '85930', '85935', '85939', '85941', '86015', '86302', '86303', '86304', '86305', '86315', '86322', '86323', '86326', '86327', '86329', '86335', '86336', '86339', '86340', '86341', '86342', '86351', '86401', '86402', '86404', '86406', '86426', '86429', '86430', '86431', '86433', '87004', '87008', '87015', '87021', '87031', '87043', '87051', '87060', '87319', '87410', '87415', '87416', '87417', '87421', '87506', '87508', '87514', '87532', '87533', '87547', '87574', '87582', '87583', '87828', '87831', '88002', '88005', '88007', '88021', '88032', '88033', '88046', '88047', '88081', '88101', '88102', '88122', '88201', '88202', '88203', '88210', '88211', '88220', '88221', '88240', '88310', '88311', '88330', '88346', '88349', '89028', '89029', '89034', '89048', '89067', '89166', '89402', '89403', '89408', '89411', '89433', '89439', '89441', '89448', '89449', '89450', '89451', '89460', '89508', '90265', '90290', '90704', '91024', '91310', '91321', '91383', '91384', '91390', '91901', '91903', '91931', '91935', '91978', '92003', '92026', '92027', '92028', '92040', '92059', '92065', '92067', '92075', '92091', '92220', '92227', '92236', '92240', '92241', '92243', '92249', '92251', '92273', '92275', '92276', '92278', '92284', '92286', '92307', '92308', '92311', '92312', '92315', '92317', '92325', '92326', '92344', '92352', '92358', '92359', '92372', '92386', '92391', '92393', '92394', '92395', '92398', '92414', '92543', '92546', '92548', '92567', '92582', '92596', '92624', '92679', '93013', '93060', '93061', '93067', '93215', '93216', '93221', '93223', '93230', '93235', '93245', '93247', '93257', '93258', '93274', '93292', '93402', '93420', '93421', '93422', '93423', '93427', '93434', '93436', '93437', '93438', '93440', '93442', '93443', '93444', '93445', '93446', '93447', '93460', '93463', '93464', '93465', '93514', '93515', '93524', '93535', '93542', '93543', '93546', '93552', '93560', '93606', '93615', '93619', '93625', '93630', '93631', '93636', '93637', '93638', '93639', '93648', '93654', '93657', '93673', '93723', '93901', '93905', '93907', '93912', '93921', '93922', '93923', '94019', '94028', '94037', '94038', '94044', '94508', '94515', '94517', '94528', '94548', '94549', '94550', '94552', '94556', '94558', '94559', '94561', '94562', '94563', '94570', '94573', '94574', '94575', '94585', '94599', '94930', '94938', '94947', '94951', '94952', '94978', '95004', '95005', '95007', '95012', '95013', '95018', '95023', '95024', '95041', '95046', '95076', '95139', '95215', '95240', '95242', '95258', '95301', '95312', '95315', '95320', '95324', '95326', '95334', '95340', '95341', '95344', '95348', '95357', '95358', '95361', '95370', '95372', '95373', '95380', '95404', '95416', '95422', '95424', '95425', '95433', '95435', '95436', '95439', '95444', '95448', '95453', '95470', '95472', '95473', '95476', '95482', '95486', '95487', '95490', '95503', '95519', '95524', '95534', '95537', '95540', '95551', '95602', '95603', '95619', '95620', '95626', '95632', '95639', '95642', '95650', '95654', '95658', '95663', '95665', '95667', '95668', '95672', '95676', '95681', '95682', '95683', '95686', '95693', '95695', '95712', '95722', '95830', '95901', '95903', '95913', '95924', '95940', '95945', '95946', '95948', '95949', '95951', '95954', '95961', '95965', '95966', '95968', '95969', '95973', '95976', '95980', '96007', '96073', '96078', '96087', '96092', '96146', '96148', '96150', '96151', '96154', '96155', '96158', '96160', '96161', '97004', '97009', '97022', '97026', '97031', '97032', '97048', '97051', '97053', '97055', '97056', '97058', '97089', '97113', '97115', '97116', '97134', '97137', '97138', '97146', '97325', '97333', '97335', '97338', '97339', '97341', '97351', '97352', '97355', '97361', '97364', '97366', '97369', '97381', '97383', '97384', '97385', '97386', '97389', '97392', '97409', '97411', '97423', '97426', '97428', '97432', '97439', '97448', '97449', '97455', '97459', '97470', '97471', '97472', '97478', '97520', '97524', '97526', '97527', '97533', '97540', '97601', '97603', '97604', '97707', '97756', '97759', '97801', '97838', '97850', '97882', '97902', '97914', '98010', '98025', '98038', '98045', '98065', '98223', '98232', '98236', '98238', '98239', '98240', '98247', '98248', '98249', '98250', '98253', '98255', '98257', '98260', '98263', '98264', '98272', '98274', '98276', '98277', '98282', '98290', '98291', '98292', '98296', '98312', '98321', '98322', '98325', '98329', '98333', '98338', '98339', '98342', '98343', '98344', '98346', '98359', '98363', '98367', '98370', '98382', '98385', '98392', '98395', '98439', '98512', '98520', '98522', '98524', '98528', '98531', '98532', '98558', '98569', '98584', '98597', '98604', '98605', '98606', '98609', '98622', '98623', '98626', '98642', '98671', '98674', '98802', '98823', '98824', '98828', '98837', '98841', '98844', '98848', '98850', '98923', '98934', '98939', '98942', '98944', '98951', '98953', '99005', '99014', '99021', '99022', '99025', '99121', '99323', '99338', '99362', '99363');
    public static $extended = array('01005', '01008', '01011', '01012', '01026', '01031', '01034', '01037', '01050', '01054', '01066', '01068', '01070', '01071', '01072', '01074', '01081', '01084', '01092', '01096', '01097', '01098', '01222', '01223', '01229', '01230', '01235', '01237', '01243', '01244', '01245', '01252', '01253', '01254', '01255', '01256', '01258', '01259', '01262', '01263', '01264', '01266', '01270', '01330', '01339', '01340', '01341', '01343', '01344', '01346', '01350', '01355', '01360', '01366', '01367', '01370', '01378', '01379', '01452', '01475', '01531', '01561', '01585', '01885', '02047', '02535', '02552', '02574', '02575', '02663', '02666', '02667', '02713', '02791', '02808', '02815', '02825', '02827', '02832', '02833', '02872', '02875', '02883', '02894', '03043', '03044', '03047', '03071', '03082', '03215', '03216', '03217', '03218', '03221', '03222', '03223', '03224', '03225', '03227', '03230', '03231', '03234', '03237', '03240', '03241', '03242', '03243', '03244', '03245', '03251', '03253', '03255', '03256', '03259', '03260', '03261', '03262', '03263', '03266', '03268', '03269', '03272', '03273', '03278', '03279', '03280', '03282', '03284', '03285', '03287', '03290', '03291', '03293', '03307', '03440', '03441', '03442', '03443', '03444', '03445', '03447', '03450', '03455', '03456', '03457', '03462', '03464', '03465', '03561', '03574', '03576', '03579', '03580', '03582', '03583', '03586', '03588', '03590', '03592', '03593', '03595', '03601', '03602', '03603', '03604', '03605', '03607', '03609', '03740', '03745', '03746', '03750', '03751', '03752', '03765', '03768', '03769', '03777', '03779', '03780', '03781', '03782', '03809', '03810', '03812', '03813', '03814', '03815', '03816', '03817', '03818', '03830', '03832', '03835', '03836', '03837', '03845', '03846', '03849', '03851', '03852', '03853', '03855', '03860', '03864', '03872', '03875', '03882', '03883', '03884', '03886', '03887', '03890', '03894', '03896', '03897', '04001', '04003', '04009', '04010', '04013', '04016', '04017', '04020', '04022', '04024', '04027', '04029', '04032', '04034', '04037', '04040', '04041', '04047', '04048', '04049', '04050', '04051', '04053', '04055', '04056', '04063', '04066', '04068', '04069', '04071', '04075', '04076', '04078', '04088', '04091', '04095', '04108', '04109', '04216', '04217', '04219', '04220', '04221', '04224', '04225', '04227', '04231', '04234', '04237', '04253', '04255', '04258', '04261', '04266', '04267', '04275', '04276', '04278', '04285', '04286', '04287', '04288', '04289', '04290', '04292', '04294', '04341', '04342', '04348', '04349', '04352', '04353', '04354', '04358', '04360', '04363', '04364', '04406', '04408', '04410', '04411', '04414', '04415', '04416', '04418', '04420', '04421', '04422', '04423', '04424', '04426', '04427', '04428', '04430', '04431', '04434', '04435', '04438', '04441', '04442', '04443', '04448', '04449', '04450', '04451', '04453', '04455', '04457', '04459', '04460', '04461', '04462', '04463', '04464', '04467', '04471', '04472', '04475', '04476', '04478', '04479', '04481', '04485', '04488', '04489', '04491', '04493', '04497', '04535', '04539', '04544', '04547', '04551', '04552', '04554', '04555', '04556', '04558', '04562', '04563', '04564', '04565', '04567', '04568', '04570', '04571', '04572', '04574', '04575', '04576', '04578', '04607', '04611', '04612', '04613', '04614', '04615', '04616', '04617', '04622', '04623', '04624', '04625', '04626', '04627', '04628', '04629', '04630', '04631', '04634', '04635', '04637', '04640', '04642', '04643', '04644', '04645', '04646', '04648', '04649', '04650', '04652', '04653', '04654', '04655', '04656', '04657', '04658', '04660', '04664', '04666', '04667', '04668', '04671', '04673', '04674', '04676', '04677', '04679', '04680', '04681', '04683', '04684', '04685', '04686', '04691', '04693', '04694', '04732', '04733', '04734', '04735', '04737', '04739', '04741', '04743', '04744', '04745', '04747', '04750', '04751', '04756', '04757', '04758', '04759', '04760', '04761', '04763', '04765', '04766', '04768', '04772', '04773', '04774', '04776', '04777', '04779', '04780', '04781', '04785', '04786', '04787', '04847', '04848', '04851', '04852', '04853', '04855', '04857', '04858', '04859', '04860', '04862', '04863', '04864', '04910', '04911', '04912', '04920', '04921', '04922', '04923', '04924', '04925', '04926', '04928', '04929', '04930', '04932', '04939', '04941', '04943', '04945', '04947', '04949', '04950', '04951', '04952', '04954', '04955', '04956', '04957', '04958', '04961', '04962', '04964', '04965', '04966', '04969', '04971', '04973', '04974', '04978', '04979', '04983', '04984', '04986', '04987', '04988', '04989', '05031', '05032', '05034', '05035', '05036', '05037', '05038', '05039', '05040', '05041', '05042', '05043', '05045', '05046', '05047', '05048', '05049', '05051', '05052', '05056', '05058', '05059', '05062', '05065', '05067', '05068', '05069', '05070', '05071', '05072', '05074', '05075', '05077', '05079', '05081', '05083', '05085', '05086', '05142', '05143', '05146', '05148', '05149', '05151', '05152', '05153', '05155', '05161', '05250', '05251', '05252', '05257', '05260', '05261', '05262', '05340', '05341', '05342', '05343', '05344', '05345', '05350', '05351', '05352', '05353', '05355', '05356', '05357', '05358', '05359', '05360', '05361', '05362', '05363', '05440', '05441', '05442', '05444', '05447', '05448', '05450', '05455', '05456', '05457', '05458', '05459', '05460', '05462', '05463', '05464', '05466', '05470', '05471', '05472', '05474', '05476', '05481', '05483', '05486', '05487', '05488', '05489', '05490', '05491', '05492', '05640', '05647', '05648', '05650', '05651', '05652', '05653', '05655', '05658', '05660', '05665', '05666', '05667', '05669', '05673', '05674', '05675', '05679', '05680', '05681', '05682', '05730', '05731', '05732', '05733', '05734', '05735', '05737', '05738', '05739', '05741', '05742', '05743', '05744', '05745', '05746', '05747', '05748', '05750', '05751', '05757', '05758', '05760', '05761', '05762', '05763', '05764', '05766', '05767', '05769', '05770', '05772', '05773', '05774', '05775', '05776', '05777', '05778', '05820', '05821', '05822', '05824', '05825', '05826', '05827', '05828', '05832', '05833', '05836', '05837', '05839', '05841', '05842', '05843', '05845', '05846', '05847', '05849', '05857', '05858', '05859', '05860', '05861', '05862', '05866', '05868', '05871', '05872', '05873', '05874', '05875', '05902', '05903', '05904', '05905', '05906', '05907', '06021', '06024', '06027', '06058', '06059', '06061', '06063', '06065', '06068', '06069', '06079', '06091', '06098', '06230', '06235', '06242', '06247', '06251', '06258', '06259', '06267', '06278', '06281', '06282', '06334', '06336', '06350', '06359', '06365', '06373', '06377', '06384', '06420', '06750', '06752', '06753', '06754', '06755', '06756', '06757', '06758', '06763', '06777', '06783', '06784', '06785', '06794', '06796', '07428', '07435', '07460', '07825', '07826', '07827', '07832', '07833', '07838', '07844', '07846', '07851', '07863', '07865', '07875', '07880', '07890', '08006', '08019', '08041', '08064', '08219', '08246', '08250', '08252', '08270', '08311', '08314', '08315', '08316', '08319', '08321', '08323', '08324', '08327', '08340', '08345', '08346', '08349', '08556', '08557', '08559', '10911', '10928', '10969', '11770', '11964', '11965', '12007', '12017', '12022', '12023', '12024', '12025', '12028', '12029', '12031', '12032', '12035', '12036', '12037', '12040', '12042', '12046', '12052', '12053', '12055', '12056', '12057', '12058', '12060', '12062', '12064', '12066', '12068', '12069', '12070', '12071', '12072', '12073', '12075', '12076', '12087', '12092', '12093', '12094', '12107', '12108', '12116', '12117', '12120', '12121', '12122', '12125', '12133', '12134', '12136', '12137', '12138', '12139', '12141', '12147', '12149', '12153', '12155', '12156', '12157', '12160', '12164', '12165', '12166', '12167', '12168', '12169', '12170', '12175', '12176', '12187', '12190', '12193', '12194', '12195', '12197', '12404', '12405', '12406', '12409', '12410', '12411', '12412', '12413', '12416', '12418', '12421', '12422', '12424', '12427', '12428', '12430', '12434', '12435', '12438', '12439', '12441', '12442', '12444', '12446', '12448', '12450', '12452', '12454', '12455', '12457', '12459', '12460', '12461', '12463', '12464', '12465', '12468', '12469', '12470', '12474', '12475', '12480', '12481', '12483', '12485', '12489', '12492', '12493', '12494', '12495', '12496', '12501', '12502', '12503', '12516', '12517', '12521', '12522', '12523', '12526', '12529', '12546', '12567', '12581', '12583', '12719', '12723', '12725', '12726', '12732', '12734', '12736', '12737', '12738', '12740', '12741', '12743', '12745', '12746', '12747', '12748', '12749', '12750', '12751', '12758', '12760', '12763', '12764', '12765', '12766', '12768', '12770', '12776', '12777', '12778', '12780', '12783', '12784', '12788', '12789', '12790', '12791', '12808', '12809', '12810', '12811', '12812', '12814', '12815', '12816', '12817', '12822', '12823', '12824', '12827', '12828', '12832', '12835', '12837', '12838', '12842', '12843', '12844', '12846', '12849', '12850', '12851', '12852', '12853', '12855', '12857', '12859', '12860', '12865', '12870', '12871', '12873', '12874', '12878', '12883', '12885', '12887', '12910', '12911', '12912', '12913', '12914', '12916', '12917', '12918', '12920', '12922', '12923', '12926', '12927', '12928', '12930', '12932', '12934', '12935', '12936', '12937', '12939', '12941', '12942', '12943', '12944', '12945', '12949', '12950', '12952', '12955', '12956', '12957', '12959', '12960', '12961', '12964', '12965', '12966', '12967', '12969', '12970', '12973', '12974', '12976', '12980', '12981', '12985', '12986', '12987', '12989', '12993', '12996', '12997', '12998', '13026', '13028', '13034', '13040', '13042', '13044', '13052', '13053', '13054', '13061', '13063', '13071', '13072', '13077', '13081', '13083', '13087', '13092', '13101', '13102', '13110', '13111', '13114', '13118', '13121', '13122', '13123', '13124', '13136', '13139', '13140', '13141', '13143', '13144', '13145', '13146', '13147', '13154', '13155', '13156', '13158', '13160', '13162', '13302', '13303', '13309', '13312', '13315', '13316', '13317', '13318', '13320', '13322', '13324', '13325', '13327', '13329', '13331', '13332', '13334', '13338', '13339', '13342', '13343', '13345', '13348', '13360', '13361', '13365', '13367', '13368', '13406', '13411', '13415', '13416', '13418', '13420', '13426', '13431', '13433', '13436', '13437', '13438', '13439', '13450', '13452', '13454', '13457', '13459', '13460', '13464', '13468', '13470', '13471', '13472', '13473', '13477', '13482', '13483', '13484', '13485', '13486', '13488', '13489', '13491', '13493', '13494', '13602', '13608', '13613', '13614', '13618', '13620', '13622', '13625', '13626', '13630', '13631', '13632', '13635', '13637', '13639', '13641', '13646', '13648', '13650', '13652', '13654', '13655', '13656', '13658', '13659', '13660', '13661', '13664', '13665', '13666', '13667', '13668', '13670', '13671', '13673', '13674', '13675', '13679', '13680', '13681', '13682', '13684', '13687', '13690', '13691', '13692', '13693', '13694', '13695', '13696', '13697', '13730', '13731', '13733', '13734', '13736', '13739', '13743', '13746', '13750', '13751', '13752', '13753', '13754', '13756', '13757', '13774', '13775', '13776', '13780', '13782', '13783', '13786', '13787', '13788', '13794', '13797', '13801', '13803', '13806', '13807', '13808', '13809', '13810', '13811', '13813', '13825', '13830', '13835', '13837', '13839', '13841', '13842', '13843', '13844', '13845', '13848', '13849', '13856', '13859', '13862', '13863', '13864', '13865', '14008', '14012', '14013', '14024', '14028', '14034', '14037', '14040', '14042', '14054', '14055', '14058', '14060', '14062', '14065', '14066', '14067', '14069', '14082', '14083', '14091', '14098', '14101', '14109', '14113', '14125', '14133', '14138', '14145', '14166', '14167', '14171', '14415', '14418', '14427', '14433', '14435', '14462', '14466', '14471', '14476', '14477', '14479', '14481', '14486', '14507', '14510', '14512', '14516', '14517', '14521', '14525', '14536', '14539', '14541', '14549', '14550', '14560', '14561', '14571', '14590', '14591', '14706', '14708', '14709', '14711', '14714', '14715', '14717', '14718', '14719', '14721', '14723', '14724', '14726', '14727', '14728', '14729', '14731', '14735', '14736', '14737', '14738', '14739', '14740', '14741', '14743', '14744', '14747', '14748', '14753', '14754', '14755', '14767', '14769', '14770', '14772', '14774', '14775', '14777', '14779', '14781', '14782', '14784', '14801', '14803', '14804', '14805', '14806', '14807', '14808', '14809', '14812', '14813', '14815', '14819', '14820', '14821', '14822', '14823', '14826', '14836', '14837', '14839', '14840', '14841', '14842', '14846', '14855', '14857', '14858', '14859', '14860', '14861', '14873', '14874', '14877', '14878', '14879', '14880', '14883', '14884', '14885', '14889', '14895', '14897', '14898', '15018', '15021', '15036', '15043', '15064', '15072', '15078', '15083', '15311', '15312', '15322', '15323', '15329', '15333', '15337', '15338', '15341', '15345', '15349', '15352', '15354', '15359', '15360', '15364', '15376', '15380', '15410', '15411', '15412', '15424', '15433', '15439', '15440', '15451', '15459', '15464', '15467', '15470', '15484', '15485', '15486', '15488', '15510', '15530', '15531', '15532', '15533', '15534', '15536', '15540', '15542', '15544', '15545', '15547', '15548', '15550', '15551', '15552', '15553', '15555', '15557', '15558', '15559', '15563', '15565', '15655', '15673', '15677', '15686', '15687', '15693', '15711', '15712', '15713', '15723', '15724', '15725', '15727', '15729', '15730', '15732', '15736', '15737', '15741', '15742', '15745', '15746', '15747', '15753', '15756', '15757', '15759', '15760', '15765', '15770', '15771', '15772', '15774', '15775', '15776', '15777', '15778', '15779', '15780', '15783', '15821', '15828', '15829', '15831', '15834', '15847', '15848', '15849', '15856', '15860', '15861', '15863', '15864', '15868', '15870', '15923', '15924', '15926', '15929', '15936', '15940', '15942', '15944', '15953', '15955', '15957', '15961', '16020', '16028', '16034', '16036', '16040', '16041', '16048', '16049', '16050', '16110', '16111', '16114', '16115', '16120', '16124', '16130', '16131', '16141', '16145', '16157', '16210', '16212', '16213', '16218', '16222', '16224', '16226', '16230', '16233', '16234', '16235', '16239', '16240', '16242', '16244', '16245', '16248', '16249', '16253', '16255', '16256', '16258', '16259', '16260', '16263', '16313', '16317', '16328', '16329', '16331', '16332', '16334', '16340', '16341', '16342', '16345', '16347', '16350', '16351', '16353', '16361', '16362', '16364', '16370', '16371', '16373', '16374', '16375', '16402', '16404', '16405', '16416', '16420', '16432', '16434', '16435', '16436', '16442', '16611', '16616', '16619', '16621', '16622', '16623', '16625', '16627', '16634', '16636', '16638', '16639', '16644', '16645', '16647', '16650', '16651', '16655', '16656', '16657', '16661', '16663', '16664', '16666', '16667', '16668', '16669', '16670', '16672', '16674', '16677', '16678', '16679', '16680', '16683', '16685', '16689', '16691', '16692', '16693', '16694', '16695', '16698', '16720', '16726', '16727', '16729', '16731', '16732', '16733', '16734', '16735', '16738', '16740', '16743', '16744', '16745', '16748', '16749', '16750', '16751', '16820', '16822', '16829', '16830', '16832', '16834', '16835', '16836', '16838', '16839', '16841', '16845', '16849', '16852', '16854', '16855', '16856', '16858', '16859', '16860', '16861', '16863', '16865', '16866', '16872', '16874', '16875', '16878', '16881', '16882', '16911', '16912', '16914', '16915', '16917', '16918', '16922', '16923', '16925', '16926', '16927', '16928', '16929', '16930', '16932', '16937', '16938', '16939', '16940', '16941', '16943', '16946', '16947', '16948', '17002', '17004', '17006', '17017', '17021', '17029', '17030', '17035', '17037', '17040', '17041', '17045', '17047', '17048', '17049', '17051', '17052', '17054', '17056', '17058', '17059', '17060', '17062', '17063', '17066', '17068', '17071', '17075', '17082', '17086', '17087', '17099', '17210', '17212', '17213', '17215', '17217', '17219', '17220', '17222', '17224', '17228', '17229', '17232', '17238', '17239', '17240', '17243', '17244', '17255', '17260', '17262', '17263', '17264', '17265', '17267', '17271', '17302', '17324', '17337', '17342', '17353', '17375', '17503', '17509', '17518', '17536', '17555', '17563', '17723', '17724', '17727', '17735', '17738', '17742', '17747', '17756', '17758', '17760', '17763', '17764', '17765', '17771', '17774', '17776', '17812', '17813', '17814', '17821', '17823', '17824', '17825', '17828', '17830', '17836', '17841', '17843', '17845', '17853', '17858', '17859', '17862', '17867', '17877', '17878', '17880', '17882', '17883', '17888', '17923', '17927', '17938', '17941', '17945', '17952', '17960', '17964', '17968', '17983', '17985', '18039', '18063', '18065', '18220', '18230', '18241', '18242', '18243', '18248', '18249', '18323', '18325', '18327', '18334', '18335', '18340', '18341', '18348', '18350', '18351', '18357', '18371', '18401', '18405', '18413', '18415', '18417', '18419', '18420', '18421', '18425', '18426', '18428', '18435', '18437', '18439', '18441', '18443', '18445', '18446', '18453', '18454', '18456', '18457', '18460', '18461', '18462', '18464', '18465', '18466', '18469', '18470', '18473', '18611', '18614', '18616', '18619', '18622', '18623', '18624', '18625', '18628', '18630', '18631', '18632', '18635', '18636', '18656', '18661', '18812', '18815', '18818', '18823', '18824', '18825', '18828', '18829', '18830', '18831', '18832', '18837', '18839', '18840', '18842', '18844', '18845', '18846', '18850', '18851', '18853', '18854', '18920', '18928', '18933', '19052', '19507', '19529', '19544', '19548', '19736', '19953', '19955', '19964', '19975', '19980', '20115', '20117', '20144', '20198', '20606', '20608', '20609', '20618', '20621', '20624', '20625', '20626', '20628', '20630', '20645', '20656', '20662', '20664', '20674', '20680', '20682', '20687', '20692', '20837', '20838', '20839', '20842', '21020', '21034', '21053', '21132', '21160', '21161', '21520', '21521', '21523', '21524', '21530', '21531', '21536', '21538', '21543', '21555', '21557', '21561', '21607', '21612', '21622', '21624', '21625', '21631', '21634', '21635', '21636', '21640', '21641', '21644', '21645', '21648', '21650', '21657', '21658', '21659', '21661', '21662', '21665', '21667', '21668', '21672', '21673', '21675', '21676', '21677', '21678', '21679', '21711', '21715', '21750', '21757', '21762', '21765', '21766', '21778', '21790', '21814', '21817', '21821', '21822', '21824', '21829', '21835', '21836', '21837', '21840', '21841', '21853', '21856', '21857', '21861', '21863', '21864', '21865', '21869', '21871', '21872', '21912', '21913', '21915', '21918', '21919', '21920', '21930', '22427', '22432', '22433', '22435', '22436', '22437', '22443', '22454', '22460', '22469', '22472', '22473', '22476', '22482', '22488', '22503', '22504', '22507', '22511', '22513', '22514', '22517', '22520', '22523', '22529', '22530', '22534', '22535', '22539', '22542', '22546', '22565', '22567', '22572', '22576', '22578', '22579', '22580', '22610', '22627', '22637', '22639', '22640', '22641', '22643', '22644', '22650', '22652', '22654', '22709', '22711', '22713', '22714', '22715', '22716', '22718', '22719', '22721', '22723', '22725', '22726', '22727', '22730', '22732', '22733', '22735', '22736', '22737', '22738', '22740', '22741', '22743', '22747', '22748', '22749', '22810', '22820', '22832', '22834', '22841', '22842', '22843', '22845', '22847', '22849', '22850', '22851', '22920', '22922', '22931', '22935', '22937', '22938', '22940', '22943', '22946', '22948', '22949', '22952', '22954', '22959', '22967', '22969', '22971', '22972', '22973', '22976', '23002', '23004', '23011', '23015', '23021', '23022', '23023', '23024', '23025', '23027', '23030', '23031', '23032', '23035', '23038', '23039', '23040', '23045', '23047', '23050', '23055', '23056', '23065', '23068', '23070', '23071', '23076', '23084', '23086', '23089', '23091', '23093', '23106', '23108', '23109', '23110', '23115', '23117', '23119', '23126', '23128', '23130', '23138', '23140', '23148', '23149', '23153', '23154', '23156', '23161', '23163', '23169', '23175', '23176', '23177', '23178', '23181', '23301', '23302', '23303', '23306', '23307', '23308', '23310', '23315', '23336', '23345', '23350', '23354', '23356', '23357', '23358', '23359', '23395', '23396', '23399', '23401', '23404', '23405', '23408', '23410', '23413', '23415', '23416', '23417', '23419', '23421', '23423', '23426', '23427', '23432', '23437', '23438', '23821', '23827', '23828', '23830', '23833', '23837', '23839', '23840', '23841', '23843', '23844', '23845', '23846', '23850', '23856', '23857', '23866', '23867', '23868', '23874', '23876', '23878', '23879', '23882', '23883', '23885', '23887', '23888', '23889', '23890', '23893', '23894', '23897', '23898', '23915', '23917', '23920', '23921', '23922', '23923', '23924', '23927', '23930', '23934', '23936', '23937', '23938', '23944', '23947', '23952', '23954', '23958', '23959', '23960', '23962', '23963', '23964', '23966', '23967', '23968', '23974', '23976', '24053', '24054', '24055', '24059', '24065', '24066', '24067', '24069', '24070', '24072', '24079', '24082', '24085', '24086', '24087', '24090', '24094', '24101', '24102', '24104', '24105', '24124', '24128', '24132', '24133', '24136', '24137', '24139', '24147', '24148', '24149', '24150', '24161', '24165', '24167', '24177', '24185', '24218', '24224', '24225', '24230', '24236', '24237', '24248', '24250', '24251', '24256', '24258', '24260', '24269', '24270', '24271', '24279', '24280', '24281', '24283', '24285', '24292', '24312', '24313', '24314', '24315', '24316', '24317', '24318', '24319', '24322', '24323', '24324', '24326', '24327', '24328', '24330', '24340', '24343', '24347', '24348', '24350', '24351', '24352', '24360', '24361', '24363', '24368', '24370', '24374', '24375', '24378', '24380', '24381', '24412', '24413', '24421', '24422', '24426', '24430', '24432', '24433', '24435', '24439', '24445', '24448', '24458', '24459', '24464', '24468', '24472', '24473', '24474', '24476', '24479', '24483', '24485', '24520', '24521', '24522', '24528', '24529', '24531', '24534', '24536', '24538', '24549', '24553', '24554', '24555', '24557', '24558', '24562', '24563', '24566', '24569', '24571', '24574', '24576', '24577', '24578', '24579', '24581', '24585', '24586', '24589', '24590', '24592', '24593', '24595', '24597', '24598', '24599', '24601', '24603', '24607', '24609', '24613', '24614', '24618', '24630', '24631', '24639', '24640', '24646', '24649', '24651', '24656', '24714', '24716', '24724', '24733', '24747', '24801', '24808', '24813', '24818', '24821', '24822', '24823', '24824', '24825', '24826', '24829', '24830', '24831', '24832', '24834', '24836', '24839', '24841', '24842', '24843', '24845', '24846', '24847', '24851', '24852', '24853', '24856', '24859', '24860', '24861', '24862', '24867', '24868', '24869', '24870', '24871', '24874', '24877', '24878', '24879', '24880', '24882', '24883', '24887', '24888', '24892', '24898', '24899', '24910', '24915', '24916', '24917', '24919', '24924', '24925', '24931', '24935', '24936', '24938', '24941', '24942', '24943', '24944', '24945', '24946', '24950', '24951', '24954', '24958', '24961', '24963', '24976', '24977', '24981', '24983', '24985', '24991', '25002', '25003', '25004', '25005', '25007', '25008', '25010', '25018', '25019', '25021', '25025', '25028', '25031', '25035', '25036', '25039', '25044', '25045', '25046', '25048', '25049', '25051', '25053', '25054', '25057', '25059', '25061', '25062', '25067', '25075', '25076', '25082', '25085', '25093', '25102', '25106', '25107', '25109', '25114', '25115', '25118', '25119', '25121', '25122', '25124', '25126', '25130', '25132', '25134', '25136', '25140', '25141', '25142', '25148', '25149', '25150', '25152', '25154', '25160', '25161', '25169', '25173', '25174', '25181', '25182', '25183', '25187', '25193', '25201', '25203', '25205', '25206', '25208', '25209', '25214', '25231', '25239', '25241', '25244', '25245', '25247', '25248', '25250', '25251', '25258', '25270', '25276', '25279', '25281', '25283', '25285', '25320', '25411', '25421', '25432', '25442', '25446', '25501', '25502', '25503', '25505', '25506', '25508', '25514', '25520', '25523', '25529', '25537', '25540', '25550', '25555', '25564', '25565', '25567', '25571', '25572', '25573', '25607', '25608', '25617', '25621', '25625', '25634', '25635', '25636', '25637', '25638', '25644', '25645', '25646', '25647', '25649', '25650', '25651', '25661', '25665', '25666', '25667', '25670', '25672', '25674', '25676', '25678', '25685', '25687', '25690', '25694', '25696', '25812', '25816', '25817', '25831', '25836', '25837', '25839', '25841', '25843', '25844', '25845', '25846', '25847', '25848', '25853', '25856', '25857', '25862', '25865', '25866', '25868', '25870', '25875', '25876', '25879', '25880', '25882', '25904', '25913', '25917', '25918', '25920', '25922', '25928', '25932', '25951', '25958', '25962', '25965', '25966', '25967', '25969', '25971', '25972', '25976', '25977', '25978', '25979', '25984', '25985', '25986', '25988', '25989', '26032', '26033', '26034', '26036', '26047', '26056', '26058', '26070', '26133', '26135', '26136', '26137', '26138', '26142', '26143', '26146', '26147', '26148', '26149', '26170', '26173', '26175', '26180', '26184', '26186', '26202', '26205', '26208', '26209', '26210', '26229', '26236', '26238', '26250', '26253', '26257', '26259', '26260', '26261', '26263', '26266', '26267', '26269', '26271', '26276', '26278', '26280', '26283', '26285', '26287', '26288', '26289', '26291', '26293', '26320', '26328', '26332', '26334', '26337', '26348', '26354', '26361', '26374', '26375', '26377', '26385', '26405', '26415', '26419', '26426', '26430', '26434', '26435', '26440', '26461', '26519', '26521', '26525', '26527', '26529', '26535', '26537', '26541', '26546', '26559', '26561', '26563', '26570', '26571', '26572', '26575', '26586', '26589', '26590', '26623', '26624', '26629', '26634', '26639', '26641', '26651', '26656', '26660', '26662', '26667', '26674', '26675', '26676', '26678', '26681', '26684', '26690', '26691', '26704', '26705', '26710', '26711', '26734', '26743', '26757', '26761', '26763', '26802', '26808', '26810', '26817', '26824', '26833', '26836', '26845', '26886', '27007', '27011', '27013', '27017', '27020', '27022', '27024', '27027', '27046', '27047', '27048', '27053', '27054', '27207', '27208', '27212', '27213', '27229', '27231', '27239', '27242', '27243', '27252', '27256', '27281', '27299', '27305', '27311', '27315', '27316', '27325', '27326', '27340', '27341', '27349', '27355', '27356', '27371', '27379', '27505', '27506', '27507', '27521', '27524', '27541', '27544', '27549', '27551', '27553', '27557', '27559', '27563', '27565', '27569', '27572', '27574', '27582', '27583', '27589', '27805', '27806', '27810', '27812', '27814', '27816', '27817', '27818', '27820', '27821', '27822', '27824', '27826', '27829', '27830', '27831', '27832', '27839', '27840', '27841', '27842', '27843', '27844', '27845', '27846', '27847', '27849', '27850', '27851', '27852', '27853', '27860', '27862', '27865', '27869', '27871', '27872', '27873', '27874', '27876', '27877', '27878', '27882', '27883', '27884', '27885', '27888', '27890', '27897', '27915', '27919', '27920', '27921', '27922', '27923', '27924', '27925', '27926', '27927', '27928', '27935', '27936', '27937', '27938', '27943', '27946', '27950', '27953', '27956', '27957', '27960', '27962', '27965', '27967', '27968', '27969', '27970', '27972', '27973', '27974', '27976', '27978', '27979', '27980', '27983', '28006', '28018', '28020', '28042', '28071', '28090', '28091', '28103', '28109', '28114', '28119', '28125', '28127', '28128', '28129', '28133', '28135', '28167', '28170', '28315', '28318', '28319', '28320', '28323', '28325', '28332', '28337', '28338', '28340', '28341', '28342', '28344', '28347', '28350', '28361', '28363', '28365', '28366', '28367', '28369', '28377', '28382', '28383', '28385', '28386', '28393', '28395', '28396', '28398', '28399', '28420', '28421', '28423', '28424', '28430', '28431', '28432', '28433', '28434', '28435', '28436', '28438', '28439', '28441', '28442', '28444', '28447', '28448', '28450', '28452', '28453', '28454', '28455', '28456', '28458', '28459', '28461', '28463', '28464', '28469', '28470', '28478', '28508', '28510', '28511', '28515', '28516', '28518', '28520', '28521', '28523', '28524', '28525', '28526', '28527', '28528', '28531', '28537', '28538', '28552', '28553', '28554', '28555', '28571', '28572', '28573', '28578', '28579', '28580', '28581', '28583', '28585', '28586', '28587', '28604', '28606', '28617', '28618', '28622', '28623', '28624', '28626', '28627', '28634', '28635', '28636', '28640', '28644', '28646', '28651', '28654', '28660', '28663', '28665', '28669', '28670', '28672', '28675', '28676', '28683', '28684', '28685', '28689', '28698', '28702', '28705', '28708', '28709', '28710', '28712', '28713', '28718', '28736', '28740', '28743', '28746', '28747', '28751', '28754', '28761', '28762', '28763', '28768', '28771', '28774', '28775', '28785', '28789', '28902', '28904', '28905', '28906', '28909', '29001', '29003', '29009', '29010', '29014', '29015', '29018', '29030', '29032', '29037', '29038', '29039', '29040', '29042', '29045', '29047', '29048', '29051', '29052', '29055', '29058', '29059', '29062', '29065', '29067', '29069', '29078', '29080', '29081', '29082', '29101', '29104', '29105', '29107', '29111', '29112', '29113', '29114', '29124', '29125', '29126', '29128', '29129', '29130', '29133', '29135', '29137', '29138', '29142', '29143', '29145', '29146', '29148', '29162', '29163', '29164', '29166', '29168', '29175', '29178', '29180', '29321', '29332', '29335', '29351', '29353', '29364', '29368', '29370', '29372', '29373', '29374', '29377', '29379', '29384', '29429', '29431', '29432', '29434', '29435', '29436', '29437', '29438', '29440', '29448', '29450', '29452', '29453', '29455', '29458', '29468', '29469', '29470', '29471', '29472', '29474', '29475', '29477', '29479', '29481', '29510', '29512', '29516', '29518', '29525', '29530', '29540', '29544', '29545', '29547', '29555', '29556', '29565', '29567', '29570', '29574', '29581', '29583', '29584', '29589', '29592', '29596', '29620', '29628', '29635', '29638', '29639', '29645', '29655', '29658', '29659', '29661', '29664', '29665', '29666', '29676', '29683', '29684', '29685', '29686', '29692', '29696', '29706', '29709', '29712', '29717', '29718', '29726', '29727', '29728', '29729', '29741', '29742', '29743', '29810', '29812', '29817', '29819', '29821', '29824', '29827', '29831', '29832', '29835', '29838', '29839', '29840', '29842', '29843', '29844', '29846', '29847', '29849', '29853', '29856', '29899', '29911', '29916', '29918', '29921', '29924', '29929', '29932', '29933', '29936', '29939', '29940', '29941', '29944', '29945', '30055', '30056', '30103', '30104', '30105', '30108', '30113', '30124', '30139', '30147', '30148', '30151', '30170', '30171', '30173', '30176', '30177', '30178', '30182', '30183', '30184', '30185', '30204', '30205', '30206', '30216', '30217', '30218', '30220', '30222', '30230', '30232', '30234', '30251', '30256', '30257', '30258', '30259', '30272', '30285', '30289', '30292', '30293', '30295', '30410', '30411', '30412', '30413', '30414', '30420', '30421', '30425', '30426', '30427', '30434', '30438', '30439', '30442', '30445', '30446', '30450', '30451', '30452', '30453', '30455', '30456', '30457', '30467', '30470', '30471', '30473', '30477', '30512', '30513', '30516', '30520', '30521', '30523', '30525', '30529', '30534', '30537', '30540', '30545', '30547', '30554', '30557', '30558', '30564', '30567', '30568', '30571', '30572', '30573', '30576', '30581', '30582', '30599', '30619', '30624', '30625', '30627', '30629', '30630', '30631', '30633', '30634', '30641', '30642', '30647', '30648', '30660', '30662', '30663', '30667', '30668', '30669', '30671', '30673', '30678', '30708', '30711', '30724', '30730', '30733', '30734', '30735', '30738', '30746', '30747', '30751', '30802', '30803', '30805', '30807', '30808', '30810', '30816', '30817', '30818', '30819', '30820', '30822', '30823', '30828', '30833', '31006', '31007', '31009', '31012', '31013', '31014', '31016', '31017', '31018', '31019', '31020', '31021', '31022', '31023', '31024', '31025', '31026', '31027', '31031', '31033', '31035', '31036', '31037', '31041', '31042', '31044', '31050', '31051', '31054', '31055', '31057', '31058', '31063', '31064', '31066', '31068', '31070', '31071', '31072', '31075', '31076', '31078', '31079', '31082', '31089', '31090', '31091', '31092', '31096', '31303', '31305', '31309', '31314', '31316', '31319', '31320', '31321', '31323', '31331', '31510', '31512', '31513', '31518', '31519', '31527', '31532', '31533', '31537', '31539', '31542', '31543', '31544', '31546', '31549', '31550', '31551', '31552', '31553', '31557', '31560', '31562', '31563', '31565', '31566', '31567', '31568', '31569', '31598', '31599', '31606', '31622', '31623', '31625', '31626', '31627', '31630', '31631', '31634', '31635', '31637', '31638', '31639', '31641', '31642', '31643', '31645', '31646', '31647', '31648', '31649', '31650', '31711', '31712', '31713', '31714', '31715', '31716', '31723', '31724', '31725', '31726', '31730', '31732', '31733', '31734', '31735', '31737', '31738', '31741', '31742', '31743', '31744', '31746', '31747', '31749', '31750', '31751', '31754', '31756', '31765', '31771', '31772', '31773', '31774', '31775', '31777', '31778', '31779', '31780', '31781', '31783', '31784', '31786', '31787', '31789', '31790', '31795', '31796', '31798', '31801', '31803', '31805', '31806', '31811', '31815', '31816', '31823', '31824', '31825', '31826', '31827', '31830', '31832', '31836', '32009', '32013', '32033', '32038', '32040', '32044', '32046', '32052', '32053', '32054', '32058', '32059', '32060', '32062', '32066', '32094', '32096', '32102', '32112', '32130', '32131', '32134', '32138', '32139', '32140', '32145', '32147', '32148', '32149', '32157', '32179', '32180', '32181', '32182', '32187', '32189', '32190', '32227', '32234', '32320', '32321', '32322', '32323', '32324', '32328', '32334', '32340', '32344', '32346', '32347', '32348', '32350', '32352', '32359', '32420', '32421', '32423', '32425', '32426', '32427', '32430', '32431', '32435', '32438', '32442', '32445', '32449', '32452', '32460', '32462', '32464', '32465', '32466', '32531', '32535', '32538', '32564', '32565', '32567', '32577', '32619', '32621', '32622', '32625', '32626', '32628', '32631', '32639', '32640', '32641', '32656', '32666', '32667', '32668', '32680', '32686', '32692', '32693', '32694', '32697', '32702', '32709', '32764', '32767', '32834', '32948', '33001', '33109', '33430', '33438', '33440', '33471', '33493', '33514', '33538', '33585', '33597', '33834', '33835', '33841', '33849', '33852', '33867', '33876', '33930', '33935', '33944', '33960', '33975', '33982', '34138', '34139', '34141', '34142', '34143', '34251', '34266', '34268', '34433', '34449', '34484', '34498', '34614', '34679', '34705', '34773', '34945', '34956', '34972', '34974', '35005', '35006', '35014', '35019', '35031', '35033', '35034', '35035', '35036', '35042', '35044', '35046', '35049', '35051', '35053', '35063', '35072', '35074', '35077', '35078', '35079', '35083', '35085', '35087', '35089', '35097', '35098', '35118', '35130', '35131', '35133', '35143', '35151', '35171', '35172', '35175', '35178', '35179', '35183', '35184', '35186', '35188', '35442', '35444', '35446', '35452', '35453', '35456', '35457', '35458', '35460', '35462', '35463', '35466', '35470', '35471', '35474', '35480', '35481', '35540', '35541', '35542', '35543', '35544', '35545', '35546', '35548', '35549', '35550', '35552', '35554', '35555', '35560', '35563', '35564', '35565', '35570', '35571', '35573', '35574', '35576', '35577', '35578', '35579', '35580', '35585', '35586', '35587', '35592', '35593', '35594', '35610', '35616', '35618', '35619', '35621', '35622', '35633', '35643', '35645', '35646', '35647', '35648', '35652', '35670', '35671', '35672', '35740', '35744', '35746', '35747', '35751', '35752', '35755', '35756', '35764', '35765', '35769', '35771', '35772', '35774', '35775', '35776', '35952', '35953', '35958', '35959', '35960', '35961', '35962', '35963', '35964', '35966', '35971', '35972', '35973', '35974', '35975', '35978', '35979', '35980', '35981', '35983', '35984', '35988', '35989', '35990', '36005', '36006', '36009', '36010', '36013', '36024', '36026', '36028', '36032', '36033', '36034', '36035', '36036', '36037', '36041', '36042', '36043', '36048', '36049', '36051', '36052', '36057', '36064', '36069', '36071', '36075', '36079', '36080', '36083', '36251', '36255', '36256', '36262', '36263', '36264', '36269', '36270', '36272', '36273', '36274', '36275', '36276', '36278', '36280', '36311', '36314', '36316', '36319', '36320', '36323', '36343', '36344', '36346', '36351', '36353', '36373', '36374', '36375', '36401', '36421', '36431', '36432', '36436', '36439', '36441', '36445', '36446', '36451', '36453', '36454', '36455', '36456', '36458', '36467', '36470', '36471', '36473', '36474', '36475', '36476', '36477', '36483', '36505', '36507', '36511', '36521', '36522', '36525', '36530', '36545', '36548', '36551', '36560', '36562', '36569', '36583', '36584', '36586', '36720', '36721', '36722', '36726', '36728', '36736', '36741', '36744', '36748', '36750', '36753', '36758', '36759', '36763', '36764', '36765', '36766', '36769', '36773', '36775', '36779', '36782', '36783', '36784', '36790', '36792', '36850', '36851', '36852', '36853', '36855', '36861', '36862', '36863', '36866', '36871', '36874', '36875', '36879', '36904', '36907', '36910', '36913', '36916', '36921', '36925', '37010', '37016', '37018', '37019', '37022', '37023', '37025', '37026', '37028', '37032', '37033', '37036', '37047', '37049', '37050', '37051', '37052', '37057', '37059', '37060', '37061', '37078', '37079', '37095', '37098', '37101', '37118', '37134', '37137', '37141', '37142', '37144', '37147', '37149', '37150', '37151', '37165', '37171', '37178', '37181', '37183', '37184', '37185', '37186', '37190', '37191', '37301', '37305', '37306', '37307', '37308', '37309', '37310', '37313', '37316', '37317', '37322', '37324', '37325', '37327', '37328', '37329', '37330', '37332', '37333', '37334', '37335', '37337', '37338', '37339', '37340', '37342', '37345', '37348', '37353', '37356', '37357', '37359', '37360', '37361', '37362', '37366', '37369', '37370', '37374', '37375', '37378', '37380', '37381', '37391', '37394', '37397', '37617', '37650', '37657', '37658', '37681', '37683', '37687', '37688', '37691', '37705', '37707', '37708', '37710', '37713', '37714', '37715', '37722', '37723', '37724', '37726', '37745', '37752', '37754', '37756', '37762', '37764', '37765', '37769', '37770', '37773', '37779', '37807', '37809', '37810', '37811', '37818', '37819', '37825', '37826', '37829', '37841', '37843', '37846', '37847', '37848', '37852', '37861', '37862', '37863', '37866', '37869', '37870', '37872', '37873', '37878', '37879', '37880', '37881', '37882', '37885', '37887', '37892', '38007', '38008', '38012', '38015', '38023', '38030', '38034', '38037', '38040', '38041', '38044', '38049', '38050', '38052', '38057', '38061', '38066', '38067', '38068', '38069', '38074', '38075', '38076', '38079', '38080', '38201', '38220', '38221', '38222', '38224', '38226', '38229', '38230', '38231', '38232', '38233', '38236', '38240', '38241', '38251', '38253', '38256', '38257', '38258', '38259', '38260', '38310', '38313', '38315', '38316', '38317', '38318', '38321', '38326', '38327', '38328', '38329', '38330', '38332', '38333', '38334', '38337', '38339', '38341', '38342', '38345', '38347', '38348', '38352', '38356', '38357', '38359', '38361', '38363', '38365', '38366', '38367', '38368', '38369', '38370', '38371', '38372', '38374', '38376', '38379', '38380', '38381', '38387', '38388', '38390', '38391', '38392', '38425', '38449', '38450', '38452', '38454', '38457', '38460', '38461', '38462', '38463', '38468', '38469', '38472', '38473', '38476', '38477', '38481', '38482', '38485', '38486', '38487', '38541', '38542', '38543', '38545', '38547', '38548', '38549', '38551', '38552', '38553', '38555', '38560', '38563', '38564', '38565', '38567', '38568', '38572', '38573', '38575', '38577', '38579', '38581', '38582', '38585', '38587', '38601', '38603', '38610', '38611', '38614', '38617', '38618', '38619', '38620', '38621', '38625', '38626', '38627', '38628', '38629', '38631', '38633', '38635', '38638', '38639', '38641', '38645', '38646', '38658', '38661', '38663', '38665', '38666', '38670', '38673', '38674', '38675', '38676', '38683', '38685', '38721', '38723', '38726', '38730', '38736', '38737', '38738', '38739', '38740', '38744', '38746', '38748', '38749', '38753', '38754', '38759', '38761', '38762', '38764', '38765', '38767', '38768', '38772', '38773', '38774', '38776', '38824', '38828', '38833', '38834', '38838', '38839', '38843', '38844', '38846', '38847', '38850', '38851', '38852', '38854', '38855', '38856', '38858', '38860', '38862', '38864', '38868', '38870', '38871', '38876', '38878', '38901', '38912', '38913', '38914', '38916', '38917', '38921', '38922', '38923', '38927', '38940', '38941', '38943', '38944', '38949', '38952', '38953', '38954', '38960', '38962', '38965', '38967', '39038', '39039', '39040', '39041', '39044', '39051', '39054', '39057', '39059', '39062', '39063', '39066', '39069', '39071', '39072', '39077', '39078', '39079', '39082', '39083', '39087', '39088', '39090', '39092', '39096', '39097', '39109', '39112', '39113', '39114', '39115', '39116', '39120', '39122', '39144', '39145', '39148', '39149', '39150', '39153', '39154', '39159', '39162', '39165', '39168', '39169', '39175', '39176', '39183', '39189', '39190', '39191', '39194', '39322', '39323', '39326', '39327', '39328', '39330', '39332', '39335', '39336', '39341', '39346', '39347', '39348', '39355', '39356', '39358', '39360', '39362', '39363', '39364', '39365', '39367', '39421', '39425', '39427', '39428', '39439', '39452', '39461', '39462', '39464', '39470', '39474', '39476', '39478', '39479', '39480', '39481', '39482', '39555', '39562', '39572', '39574', '39577', '39629', '39631', '39632', '39635', '39638', '39643', '39652', '39653', '39654', '39656', '39657', '39662', '39664', '39667', '39668', '39730', '39735', '39737', '39739', '39740', '39741', '39743', '39744', '39746', '39750', '39751', '39752', '39753', '39754', '39755', '39756', '39762', '39769', '39771', '39772', '39813', '39815', '39817', '39823', '39824', '39825', '39826', '39827', '39832', '39834', '39837', '39840', '39841', '39842', '39845', '39846', '39851', '39854', '39859', '39861', '39862', '39866', '39867', '39870', '39877', '39886', '39897', '40003', '40006', '40007', '40008', '40011', '40019', '40037', '40040', '40045', '40051', '40052', '40055', '40057', '40060', '40062', '40068', '40069', '40070', '40076', '40077', '40104', '40107', '40110', '40111', '40115', '40142', '40144', '40146', '40150', '40155', '40161', '40162', '40164', '40176', '40177', '40311', '40312', '40313', '40316', '40322', '40328', '40330', '40334', '40336', '40337', '40346', '40350', '40355', '40358', '40359', '40360', '40363', '40370', '40371', '40372', '40376', '40379', '40380', '40385', '40387', '40402', '40409', '40410', '40419', '40437', '40440', '40442', '40444', '40447', '40452', '40461', '40464', '40468', '40472', '40473', '40481', '40486', '40488', '40489', '40701', '40729', '40734', '40741', '40754', '40769', '40771', '40801', '40803', '40806', '40807', '40815', '40816', '40818', '40820', '40823', '40828', '40829', '40830', '40844', '40847', '40855', '40856', '40862', '40863', '40865', '40868', '40870', '40873', '40902', '40903', '40915', '40921', '40923', '40927', '40930', '40935', '40941', '40943', '40949', '40953', '40958', '40962', '40977', '40979', '40999', '41002', '41003', '41004', '41006', '41007', '41008', '41033', '41034', '41039', '41041', '41043', '41044', '41045', '41046', '41049', '41052', '41055', '41061', '41062', '41063', '41064', '41081', '41086', '41093', '41097', '41121', '41127', '41137', '41141', '41142', '41144', '41166', '41171', '41173', '41174', '41175', '41179', '41180', '41189', '41203', '41204', '41214', '41215', '41222', '41224', '41228', '41231', '41234', '41238', '41250', '41254', '41255', '41256', '41260', '41262', '41263', '41264', '41265', '41267', '41271', '41301', '41307', '41310', '41311', '41313', '41332', '41338', '41342', '41344', '41360', '41364', '41367', '41377', '41386', '41390', '41397', '41408', '41419', '41421', '41422', '41426', '41465', '41477', '41512', '41513', '41514', '41519', '41520', '41522', '41524', '41527', '41528', '41531', '41535', '41537', '41539', '41540', '41543', '41544', '41546', '41547', '41548', '41549', '41553', '41554', '41555', '41557', '41558', '41559', '41562', '41563', '41564', '41566', '41567', '41568', '41571', '41572', '41602', '41603', '41604', '41606', '41607', '41615', '41616', '41619', '41630', '41631', '41636', '41640', '41642', '41643', '41645', '41647', '41655', '41660', '41663', '41667', '41719', '41722', '41723', '41725', '41727', '41730', '41735', '41739', '41740', '41746', '41749', '41760', '41764', '41772', '41773', '41774', '41778', '41812', '41815', '41817', '41822', '41824', '41825', '41828', '41831', '41832', '41833', '41835', '41837', '41838', '41839', '41840', '41843', '41845', '41848', '41849', '41855', '41859', '41861', '41862', '42021', '42023', '42024', '42028', '42035', '42036', '42037', '42038', '42039', '42040', '42041', '42045', '42047', '42048', '42049', '42050', '42053', '42054', '42055', '42056', '42060', '42064', '42069', '42070', '42071', '42076', '42078', '42079', '42081', '42084', '42087', '42088', '42120', '42123', '42127', '42129', '42130', '42140', '42151', '42153', '42154', '42156', '42157', '42160', '42170', '42202', '42204', '42206', '42207', '42209', '42210', '42211', '42214', '42215', '42217', '42219', '42220', '42232', '42236', '42252', '42254', '42256', '42257', '42259', '42261', '42265', '42266', '42267', '42273', '42275', '42280', '42283', '42285', '42286', '42287', '42321', '42322', '42323', '42324', '42325', '42326', '42327', '42328', '42332', '42333', '42337', '42339', '42343', '42344', '42345', '42348', '42350', '42351', '42352', '42355', '42365', '42366', '42368', '42369', '42370', '42371', '42372', '42376', '42378', '42404', '42406', '42408', '42409', '42411', '42436', '42437', '42441', '42442', '42450', '42451', '42453', '42455', '42456', '42457', '42458', '42459', '42461', '42462', '42463', '42464', '42516', '42518', '42519', '42528', '42539', '42541', '42544', '42553', '42565', '42566', '42567', '42602', '42603', '42629', '42631', '42633', '42635', '42647', '42649', '42653', '42711', '42712', '42713', '42715', '42716', '42717', '42721', '42722', '42726', '42728', '42729', '42733', '42741', '42742', '42743', '42746', '42748', '42749', '42753', '42754', '42757', '42758', '42759', '42761', '42762', '42764', '42765', '42776', '42782', '42784', '42786', '42788', '43003', '43006', '43009', '43011', '43013', '43014', '43027', '43037', '43044', '43045', '43060', '43061', '43066', '43071', '43072', '43080', '43084', '43106', '43107', '43111', '43115', '43128', '43135', '43138', '43142', '43143', '43145', '43148', '43149', '43150', '43152', '43153', '43160', '43163', '43164', '43310', '43314', '43318', '43319', '43320', '43323', '43330', '43332', '43333', '43334', '43335', '43336', '43337', '43340', '43341', '43343', '43344', '43345', '43347', '43356', '43357', '43358', '43359', '43360', '43406', '43407', '43412', '43413', '43435', '43442', '43445', '43446', '43451', '43457', '43462', '43464', '43466', '43467', '43501', '43511', '43516', '43517', '43518', '43521', '43523', '43524', '43527', '43532', '43533', '43534', '43535', '43536', '43540', '43541', '43543', '43547', '43548', '43549', '43554', '43556', '43557', '43569', '43570', '43716', '43720', '43722', '43727', '43728', '43730', '43732', '43739', '43746', '43747', '43748', '43749', '43755', '43756', '43758', '43760', '43762', '43766', '43767', '43771', '43772', '43773', '43777', '43778', '43779', '43780', '43782', '43783', '43786', '43788', '43789', '43793', '43802', '43803', '43811', '43821', '43824', '43832', '43837', '43840', '43844', '43901', '43903', '43907', '43908', '43910', '43914', '43915', '43916', '43917', '43925', '43930', '43931', '43932', '43933', '43938', '43941', '43942', '43944', '43945', '43946', '43948', '43951', '43962', '43966', '43968', '43973', '43977', '43986', '43988', '44032', '44046', '44076', '44082', '44085', '44093', '44099', '44214', '44276', '44404', '44416', '44417', '44427', '44428', '44441', '44449', '44450', '44451', '44607', '44611', '44620', '44625', '44628', '44629', '44636', '44637', '44638', '44639', '44656', '44661', '44670', '44695', '44699', '44802', '44803', '44804', '44807', '44817', '44818', '44825', '44826', '44836', '44837', '44840', '44841', '44844', '44849', '44851', '44853', '44855', '44860', '44864', '44867', '44874', '44882', '44887', '45033', '45041', '45053', '45054', '45064', '45101', '45106', '45107', '45112', '45114', '45115', '45120', '45121', '45123', '45130', '45135', '45138', '45142', '45144', '45146', '45148', '45153', '45159', '45166', '45167', '45168', '45169', '45171', '45176', '45303', '45308', '45311', '45312', '45317', '45321', '45326', '45332', '45333', '45334', '45337', '45340', '45346', '45348', '45362', '45368', '45382', '45388', '45390', '45612', '45613', '45614', '45616', '45620', '45621', '45622', '45623', '45630', '45634', '45645', '45646', '45647', '45650', '45651', '45652', '45654', '45656', '45657', '45659', '45660', '45663', '45671', '45672', '45675', '45677', '45678', '45679', '45681', '45682', '45683', '45684', '45686', '45693', '45695', '45696', '45697', '45710', '45711', '45715', '45719', '45720', '45721', '45723', '45724', '45727', '45729', '45732', '45734', '45735', '45741', '45742', '45743', '45744', '45761', '45764', '45766', '45767', '45768', '45769', '45770', '45771', '45772', '45773', '45775', '45776', '45777', '45778', '45779', '45781', '45782', '45786', '45787', '45788', '45789', '45812', '45813', '45814', '45819', '45820', '45821', '45827', '45830', '45831', '45832', '45835', '45836', '45837', '45841', '45843', '45844', '45845', '45849', '45850', '45851', '45856', '45858', '45860', '45862', '45863', '45864', '45868', '45873', '45874', '45880', '45881', '45882', '45886', '45890', '45893', '45894', '45896', '45897', '45898', '45899', '46035', '46039', '46049', '46050', '46056', '46057', '46058', '46070', '46071', '46104', '46105', '46111', '46115', '46117', '46120', '46121', '46127', '46128', '46133', '46147', '46148', '46155', '46156', '46160', '46161', '46162', '46164', '46165', '46166', '46171', '46172', '46173', '46175', '46180', '46181', '46183', '46186', '46340', '46345', '46347', '46348', '46349', '46365', '46366', '46374', '46376', '46379', '46380', '46381', '46382', '46390', '46392', '46501', '46504', '46510', '46531', '46532', '46539', '46566', '46570', '46702', '46705', '46730', '46740', '46741', '46742', '46746', '46759', '46760', '46761', '46764', '46766', '46771', '46772', '46773', '46776', '46779', '46785', '46792', '46794', '46910', '46911', '46912', '46913', '46917', '46920', '46923', '46926', '46928', '46929', '46930', '46937', '46939', '46940', '46941', '46946', '46950', '46951', '46959', '46960', '46974', '46978', '46982', '46984', '46985', '46986', '46988', '46990', '46991', '46996', '46998', '47010', '47011', '47012', '47016', '47017', '47018', '47020', '47023', '47024', '47030', '47031', '47032', '47035', '47036', '47037', '47040', '47041', '47042', '47043', '47110', '47115', '47116', '47117', '47120', '47123', '47125', '47137', '47138', '47140', '47141', '47142', '47147', '47160', '47162', '47174', '47220', '47223', '47226', '47227', '47228', '47229', '47230', '47231', '47232', '47234', '47235', '47236', '47243', '47245', '47246', '47247', '47260', '47264', '47265', '47270', '47273', '47281', '47282', '47283', '47320', '47325', '47326', '47327', '47336', '47337', '47338', '47339', '47340', '47341', '47342', '47344', '47345', '47346', '47348', '47351', '47352', '47353', '47354', '47355', '47357', '47358', '47359', '47360', '47366', '47367', '47368', '47369', '47373', '47380', '47382', '47384', '47386', '47387', '47390', '47393', '47424', '47427', '47431', '47432', '47433', '47436', '47438', '47443', '47446', '47448', '47449', '47452', '47453', '47454', '47456', '47457', '47459', '47460', '47465', '47468', '47470', '47471', '47512', '47513', '47514', '47515', '47516', '47519', '47520', '47521', '47524', '47525', '47527', '47528', '47529', '47531', '47536', '47537', '47550', '47551', '47552', '47553', '47556', '47557', '47561', '47562', '47564', '47567', '47568', '47573', '47575', '47576', '47577', '47578', '47580', '47581', '47584', '47585', '47590', '47597', '47598', '47611', '47612', '47615', '47616', '47619', '47631', '47633', '47634', '47635', '47637', '47640', '47647', '47649', '47654', '47660', '47665', '47666', '47683', '47830', '47832', '47833', '47836', '47838', '47840', '47841', '47846', '47847', '47849', '47850', '47852', '47854', '47855', '47858', '47859', '47860', '47861', '47862', '47866', '47868', '47871', '47872', '47874', '47875', '47879', '47918', '47921', '47922', '47923', '47926', '47928', '47929', '47930', '47932', '47940', '47942', '47943', '47944', '47946', '47948', '47949', '47950', '47951', '47952', '47954', '47957', '47959', '47963', '47966', '47967', '47968', '47969', '47970', '47971', '47974', '47975', '47977', '47978', '47980', '47981', '47982', '47987', '47989', '47990', '47991', '47992', '47993', '47994', '47995', '48002', '48006', '48022', '48028', '48032', '48097', '48110', '48137', '48140', '48159', '48401', '48410', '48411', '48414', '48415', '48416', '48418', '48419', '48422', '48426', '48427', '48432', '48434', '48435', '48441', '48445', '48450', '48453', '48454', '48456', '48465', '48466', '48467', '48468', '48469', '48470', '48472', '48475', '48610', '48613', '48614', '48615', '48618', '48619', '48620', '48621', '48624', '48625', '48627', '48628', '48632', '48635', '48636', '48637', '48647', '48649', '48651', '48652', '48653', '48654', '48656', '48659', '48662', '48701', '48703', '48705', '48720', '48721', '48725', '48726', '48727', '48728', '48729', '48730', '48731', '48733', '48735', '48736', '48737', '48739', '48740', '48741', '48742', '48743', '48744', '48745', '48747', '48748', '48749', '48754', '48756', '48759', '48760', '48761', '48762', '48764', '48765', '48766', '48767', '48770', '48806', '48807', '48809', '48811', '48815', '48818', '48819', '48822', '48829', '48830', '48831', '48832', '48834', '48835', '48841', '48845', '48847', '48849', '48850', '48851', '48856', '48860', '48861', '48862', '48865', '48866', '48871', '48873', '48877', '48878', '48880', '48883', '48884', '48885', '48886', '48888', '48889', '48890', '48891', '48892', '48893', '48894', '48897', '49011', '49013', '49021', '49026', '49028', '49029', '49030', '49033', '49035', '49040', '49045', '49050', '49052', '49056', '49057', '49061', '49063', '49066', '49067', '49072', '49073', '49076', '49082', '49089', '49092', '49094', '49095', '49096', '49099', '49220', '49227', '49232', '49235', '49238', '49241', '49246', '49247', '49248', '49249', '49251', '49252', '49253', '49255', '49256', '49262', '49264', '49266', '49268', '49271', '49274', '49275', '49276', '49279', '49284', '49288', '49303', '49304', '49305', '49309', '49310', '49318', '49320', '49322', '49325', '49326', '49327', '49328', '49332', '49336', '49338', '49340', '49342', '49343', '49346', '49347', '49349', '49402', '49403', '49405', '49408', '49410', '49411', '49416', '49419', '49420', '49421', '49425', '49436', '49446', '49449', '49450', '49451', '49452', '49454', '49455', '49459', '49612', '49613', '49614', '49615', '49617', '49618', '49619', '49620', '49621', '49622', '49623', '49625', '49630', '49631', '49632', '49633', '49638', '49639', '49640', '49642', '49644', '49645', '49648', '49649', '49651', '49653', '49654', '49655', '49656', '49659', '49663', '49664', '49665', '49667', '49668', '49670', '49674', '49679', '49680', '49682', '49683', '49688', '49689', '49705', '49706', '49709', '49710', '49713', '49715', '49716', '49718', '49719', '49721', '49724', '49725', '49726', '49727', '49729', '49730', '49733', '49738', '49739', '49743', '49744', '49745', '49746', '49747', '49748', '49749', '49751', '49752', '49753', '49755', '49756', '49757', '49759', '49760', '49761', '49762', '49765', '49766', '49768', '49769', '49775', '49776', '49777', '49779', '49780', '49788', '49791', '49792', '49793', '49795', '49799', '49805', '49806', '49807', '49812', '49813', '49814', '49815', '49816', '49817', '49818', '49820', '49821', '49822', '49825', '49826', '49827', '49831', '49833', '49834', '49835', '49836', '49839', '49840', '49847', '49848', '49849', '49853', '49854', '49861', '49862', '49866', '49868', '49872', '49877', '49878', '49879', '49880', '49881', '49883', '49884', '49885', '49886', '49887', '49891', '49892', '49893', '49896', '49901', '49902', '49903', '49905', '49908', '49910', '49911', '49912', '49913', '49916', '49918', '49920', '49921', '49922', '49925', '49927', '49929', '49930', '49934', '49935', '49938', '49945', '49946', '49947', '49948', '49952', '49953', '49955', '49959', '49960', '49961', '49962', '49963', '49965', '49967', '49968', '49971', '50001', '50002', '50005', '50006', '50007', '50008', '50020', '50022', '50025', '50026', '50027', '50028', '50029', '50031', '50032', '50033', '50034', '50039', '50040', '50041', '50042', '50044', '50046', '50048', '50049', '50050', '50051', '50054', '50055', '50056', '50057', '50058', '50059', '50060', '50062', '50064', '50065', '50066', '50070', '50071', '50072', '50073', '50075', '50076', '50099', '50101', '50102', '50103', '50104', '50106', '50107', '50110', '50115', '50116', '50118', '50119', '50120', '50122', '50123', '50128', '50129', '50130', '50132', '50133', '50134', '50135', '50136', '50138', '50139', '50140', '50141', '50142', '50143', '50144', '50145', '50146', '50147', '50148', '50149', '50150', '50151', '50153', '50154', '50156', '50157', '50161', '50162', '50164', '50165', '50166', '50167', '50168', '50169', '50170', '50171', '50173', '50174', '50206', '50207', '50210', '50212', '50213', '50214', '50216', '50217', '50218', '50220', '50222', '50223', '50225', '50227', '50228', '50229', '50230', '50231', '50232', '50233', '50234', '50235', '50237', '50238', '50239', '50240', '50242', '50246', '50247', '50248', '50249', '50250', '50251', '50252', '50255', '50256', '50257', '50258', '50261', '50262', '50268', '50269', '50271', '50272', '50273', '50274', '50276', '50277', '50278', '50420', '50421', '50424', '50426', '50430', '50431', '50432', '50433', '50434', '50438', '50439', '50440', '50441', '50444', '50446', '50447', '50448', '50449', '50450', '50451', '50452', '50453', '50454', '50455', '50456', '50457', '50458', '50459', '50460', '50461', '50464', '50465', '50466', '50467', '50468', '50469', '50470', '50471', '50472', '50473', '50475', '50476', '50477', '50478', '50479', '50480', '50481', '50482', '50483', '50484', '50510', '50514', '50515', '50516', '50517', '50518', '50519', '50520', '50522', '50523', '50524', '50525', '50527', '50528', '50530', '50531', '50532', '50533', '50535', '50536', '50538', '50539', '50540', '50541', '50542', '50543', '50544', '50545', '50546', '50552', '50554', '50556', '50557', '50558', '50559', '50560', '50561', '50562', '50563', '50565', '50566', '50567', '50568', '50569', '50570', '50571', '50573', '50574', '50575', '50576', '50577', '50578', '50579', '50581', '50582', '50583', '50585', '50586', '50590', '50591', '50592', '50593', '50594', '50597', '50598', '50599', '50601', '50602', '50603', '50604', '50605', '50606', '50608', '50609', '50611', '50612', '50619', '50621', '50623', '50624', '50625', '50626', '50627', '50628', '50629', '50630', '50632', '50633', '50635', '50636', '50638', '50641', '50642', '50645', '50647', '50651', '50653', '50655', '50658', '50660', '50661', '50665', '50666', '50668', '50669', '50670', '50671', '50672', '50673', '50674', '50675', '50676', '50680', '50681', '50682', '50801', '50830', '50833', '50835', '50839', '50840', '50841', '50842', '50846', '50847', '50849', '50851', '50853', '50854', '50860', '50863', '50864', '51001', '51002', '51003', '51004', '51005', '51006', '51007', '51009', '51010', '51012', '51014', '51016', '51018', '51019', '51020', '51022', '51023', '51024', '51025', '51026', '51027', '51028', '51029', '51030', '51034', '51035', '51036', '51037', '51038', '51039', '51040', '51041', '51044', '51046', '51047', '51048', '51049', '51050', '51051', '51052', '51053', '51055', '51056', '51058', '51059', '51060', '51061', '51062', '51063', '51201', '51230', '51231', '51232', '51234', '51235', '51237', '51238', '51239', '51240', '51241', '51242', '51243', '51245', '51246', '51247', '51248', '51249', '51330', '51333', '51334', '51338', '51341', '51342', '51343', '51345', '51346', '51347', '51349', '51350', '51354', '51357', '51358', '51364', '51365', '51366', '51430', '51431', '51433', '51436', '51439', '51440', '51441', '51442', '51443', '51444', '51445', '51446', '51448', '51449', '51450', '51451', '51452', '51453', '51454', '51455', '51458', '51460', '51461', '51462', '51463', '51465', '51466', '51467', '51520', '51521', '51523', '51525', '51526', '51527', '51528', '51529', '51530', '51531', '51532', '51533', '51535', '51536', '51540', '51541', '51542', '51544', '51546', '51548', '51549', '51551', '51552', '51553', '51555', '51556', '51557', '51558', '51559', '51560', '51561', '51562', '51563', '51564', '51565', '51566', '51570', '51571', '51572', '51573', '51574', '51575', '51576', '51577', '51578', '51579', '51603', '51630', '51632', '51636', '51637', '51638', '51639', '51640', '51645', '51646', '51647', '51648', '51649', '51650', '51652', '51653', '51654', '51656', '52031', '52032', '52033', '52035', '52037', '52038', '52039', '52041', '52042', '52043', '52045', '52046', '52047', '52049', '52052', '52053', '52054', '52055', '52064', '52065', '52066', '52069', '52070', '52073', '52076', '52077', '52078', '52132', '52133', '52134', '52136', '52140', '52141', '52142', '52144', '52146', '52147', '52151', '52154', '52155', '52157', '52158', '52159', '52160', '52161', '52162', '52163', '52164', '52165', '52169', '52170', '52171', '52172', '52201', '52202', '52207', '52208', '52209', '52210', '52211', '52213', '52214', '52215', '52216', '52217', '52218', '52219', '52220', '52222', '52223', '52224', '52225', '52226', '52229', '52231', '52232', '52236', '52237', '52247', '52248', '52249', '52251', '52254', '52255', '52257', '52301', '52305', '52306', '52308', '52309', '52310', '52313', '52316', '52320', '52321', '52322', '52323', '52325', '52327', '52329', '52330', '52332', '52334', '52335', '52336', '52337', '52339', '52340', '52342', '52344', '52346', '52347', '52349', '52352', '52354', '52355', '52356', '52358', '52359', '52361', '52362', '52531', '52533', '52535', '52536', '52537', '52538', '52540', '52542', '52543', '52548', '52549', '52550', '52551', '52552', '52553', '52554', '52555', '52560', '52561', '52562', '52563', '52565', '52567', '52570', '52571', '52572', '52573', '52574', '52576', '52580', '52584', '52585', '52586', '52588', '52590', '52591', '52593', '52594', '52620', '52621', '52623', '52625', '52626', '52630', '52631', '52635', '52637', '52639', '52640', '52645', '52646', '52647', '52649', '52650', '52651', '52653', '52654', '52656', '52658', '52659', '52660', '52720', '52721', '52727', '52729', '52731', '52737', '52738', '52739', '52745', '52746', '52750', '52751', '52754', '52755', '52756', '52760', '52765', '52766', '52768', '52769', '52772', '52774', '52776', '52777', '52778', '53001', '53003', '53004', '53010', '53011', '53014', '53015', '53019', '53020', '53023', '53026', '53032', '53035', '53038', '53039', '53042', '53049', '53050', '53057', '53059', '53063', '53065', '53078', '53079', '53091', '53093', '53099', '53137', '53139', '53156', '53157', '53178', '53502', '53503', '53504', '53505', '53506', '53510', '53516', '53517', '53518', '53520', '53522', '53529', '53530', '53541', '53543', '53544', '53550', '53553', '53554', '53556', '53557', '53561', '53565', '53569', '53570', '53573', '53574', '53576', '53577', '53579', '53580', '53581', '53582', '53585', '53586', '53587', '53588', '53599', '53801', '53802', '53804', '53805', '53807', '53809', '53810', '53816', '53818', '53820', '53825', '53826', '53910', '53919', '53920', '53922', '53923', '53924', '53926', '53928', '53929', '53930', '53931', '53932', '53933', '53934', '53935', '53936', '53937', '53939', '53941', '53943', '53944', '53946', '53948', '53949', '53951', '53952', '53956', '53957', '53961', '53963', '53964', '53968', '54001', '54003', '54004', '54006', '54007', '54009', '54011', '54013', '54014', '54015', '54026', '54027', '54028', '54082', '54101', '54102', '54103', '54106', '54107', '54110', '54111', '54112', '54114', '54119', '54120', '54121', '54124', '54125', '54126', '54127', '54128', '54129', '54135', '54137', '54138', '54139', '54149', '54150', '54151', '54152', '54154', '54156', '54159', '54161', '54170', '54174', '54175', '54177', '54182', '54202', '54204', '54205', '54207', '54209', '54210', '54212', '54213', '54216', '54227', '54228', '54230', '54234', '54245', '54246', '54247', '54405', '54406', '54407', '54408', '54410', '54411', '54412', '54413', '54414', '54415', '54416', '54417', '54418', '54420', '54421', '54423', '54424', '54425', '54426', '54427', '54428', '54429', '54430', '54433', '54435', '54436', '54440', '54442', '54443', '54446', '54447', '54448', '54450', '54451', '54452', '54454', '54456', '54457', '54458', '54460', '54463', '54465', '54466', '54470', '54471', '54473', '54475', '54479', '54480', '54484', '54485', '54486', '54487', '54488', '54489', '54490', '54491', '54493', '54498', '54499', '54511', '54512', '54513', '54514', '54515', '54519', '54520', '54521', '54525', '54526', '54527', '54529', '54530', '54531', '54534', '54536', '54537', '54538', '54539', '54541', '54542', '54545', '54546', '54547', '54550', '54552', '54554', '54555', '54557', '54558', '54559', '54560', '54561', '54562', '54563', '54566', '54610', '54611', '54612', '54613', '54614', '54616', '54618', '54619', '54621', '54622', '54623', '54624', '54625', '54627', '54628', '54629', '54631', '54632', '54634', '54635', '54638', '54639', '54642', '54644', '54646', '54648', '54651', '54652', '54653', '54658', '54659', '54661', '54664', '54665', '54666', '54667', '54670', '54721', '54722', '54723', '54725', '54726', '54727', '54728', '54730', '54731', '54732', '54733', '54734', '54736', '54737', '54738', '54739', '54740', '54741', '54742', '54743', '54745', '54746', '54747', '54748', '54749', '54750', '54754', '54755', '54756', '54757', '54759', '54761', '54762', '54763', '54766', '54767', '54768', '54769', '54770', '54771', '54772', '54801', '54805', '54806', '54810', '54814', '54817', '54819', '54821', '54822', '54824', '54826', '54829', '54832', '54836', '54837', '54838', '54839', '54840', '54842', '54843', '54844', '54845', '54846', '54847', '54848', '54849', '54853', '54855', '54856', '54858', '54864', '54867', '54870', '54871', '54872', '54873', '54874', '54875', '54876', '54888', '54889', '54891', '54893', '54895', '54909', '54921', '54922', '54928', '54930', '54931', '54932', '54940', '54943', '54945', '54948', '54949', '54950', '54951', '54960', '54962', '54964', '54965', '54966', '54967', '54969', '54970', '54974', '54975', '54976', '54977', '54979', '54982', '54983', '54984', '54990', '55006', '55007', '55009', '55012', '55013', '55017', '55026', '55027', '55030', '55031', '55032', '55036', '55037', '55040', '55041', '55045', '55046', '55047', '55051', '55053', '55063', '55065', '55069', '55072', '55073', '55074', '55079', '55080', '55084', '55088', '55089', '55307', '55310', '55312', '55314', '55321', '55322', '55324', '55325', '55329', '55332', '55333', '55334', '55335', '55336', '55338', '55339', '55342', '55349', '55353', '55354', '55360', '55363', '55367', '55368', '55370', '55381', '55385', '55388', '55389', '55390', '55395', '55396', '55397', '55398', '55555', '55601', '55603', '55604', '55605', '55612', '55613', '55614', '55615', '55616', '55702', '55704', '55705', '55706', '55707', '55708', '55709', '55710', '55711', '55712', '55713', '55716', '55717', '55718', '55719', '55721', '55722', '55723', '55725', '55726', '55731', '55733', '55734', '55735', '55736', '55738', '55741', '55742', '55748', '55749', '55750', '55751', '55753', '55756', '55757', '55758', '55760', '55764', '55767', '55769', '55775', '55779', '55780', '55781', '55782', '55783', '55784', '55786', '55787', '55790', '55793', '55795', '55796', '55797', '55798', '55909', '55910', '55917', '55918', '55919', '55921', '55922', '55923', '55924', '55925', '55926', '55927', '55929', '55931', '55932', '55933', '55934', '55935', '55936', '55939', '55940', '55941', '55943', '55945', '55946', '55949', '55951', '55952', '55953', '55954', '55956', '55957', '55959', '55961', '55962', '55963', '55964', '55965', '55967', '55968', '55969', '55970', '55971', '55972', '55973', '55974', '55975', '55977', '55979', '55981', '55982', '55983', '55985', '55990', '55991', '55992', '56009', '56010', '56013', '56014', '56016', '56019', '56021', '56022', '56023', '56025', '56026', '56027', '56028', '56029', '56030', '56032', '56033', '56034', '56036', '56037', '56039', '56041', '56042', '56043', '56044', '56045', '56046', '56047', '56048', '56051', '56052', '56054', '56056', '56057', '56060', '56064', '56065', '56068', '56069', '56072', '56074', '56076', '56078', '56080', '56081', '56083', '56085', '56087', '56088', '56089', '56090', '56091', '56096', '56097', '56098', '56101', '56110', '56111', '56113', '56114', '56115', '56116', '56117', '56118', '56119', '56120', '56121', '56122', '56123', '56125', '56127', '56128', '56129', '56131', '56132', '56134', '56137', '56138', '56139', '56140', '56141', '56144', '56145', '56149', '56150', '56151', '56152', '56153', '56155', '56156', '56157', '56158', '56159', '56160', '56161', '56162', '56164', '56165', '56166', '56167', '56168', '56169', '56170', '56171', '56172', '56173', '56174', '56175', '56176', '56178', '56180', '56181', '56183', '56185', '56186', '56207', '56208', '56209', '56210', '56211', '56212', '56214', '56216', '56218', '56219', '56220', '56221', '56222', '56223', '56224', '56225', '56226', '56227', '56228', '56229', '56230', '56231', '56232', '56235', '56236', '56237', '56239', '56240', '56243', '56244', '56245', '56246', '56248', '56249', '56251', '56252', '56253', '56255', '56256', '56257', '56260', '56262', '56263', '56264', '56266', '56267', '56270', '56271', '56274', '56276', '56278', '56279', '56280', '56281', '56282', '56283', '56284', '56285', '56287', '56288', '56289', '56291', '56292', '56293', '56294', '56295', '56296', '56297', '56309', '56311', '56312', '56314', '56315', '56316', '56317', '56318', '56319', '56321', '56323', '56324', '56325', '56326', '56327', '56328', '56329', '56330', '56331', '56332', '56334', '56335', '56336', '56339', '56340', '56341', '56342', '56343', '56344', '56347', '56349', '56350', '56352', '56353', '56354', '56355', '56356', '56358', '56359', '56360', '56361', '56362', '56364', '56367', '56368', '56371', '56373', '56375', '56376', '56378', '56381', '56382', '56384', '56385', '56386', '56389', '56431', '56433', '56434', '56435', '56436', '56437', '56438', '56440', '56442', '56443', '56444', '56446', '56447', '56448', '56449', '56450', '56452', '56453', '56455', '56456', '56458', '56461', '56464', '56466', '56467', '56468', '56469', '56472', '56473', '56474', '56475', '56477', '56478', '56479', '56481', '56510', '56511', '56514', '56515', '56516', '56518', '56520', '56521', '56523', '56524', '56525', '56527', '56528', '56531', '56533', '56534', '56535', '56536', '56540', '56541', '56542', '56543', '56544', '56545', '56546', '56547', '56548', '56549', '56550', '56551', '56552', '56554', '56556', '56557', '56565', '56566', '56567', '56568', '56570', '56571', '56572', '56573', '56574', '56576', '56578', '56579', '56580', '56584', '56585', '56586', '56587', '56588', '56589', '56590', '56591', '56592', '56593', '56594', '56621', '56623', '56627', '56633', '56634', '56636', '56637', '56641', '56644', '56646', '56647', '56649', '56650', '56651', '56652', '56653', '56654', '56655', '56659', '56660', '56662', '56663', '56667', '56670', '56671', '56672', '56676', '56678', '56680', '56682', '56683', '56684', '56710', '56712', '56713', '56714', '56715', '56723', '56724', '56725', '56726', '56727', '56728', '56731', '56732', '56733', '56734', '56735', '56736', '56737', '56738', '56740', '56742', '56744', '56748', '56750', '56751', '56754', '56757', '56758', '56760', '56761', '56762', '56763', '57001', '57002', '57003', '57004', '57010', '57012', '57014', '57015', '57016', '57017', '57018', '57021', '57022', '57024', '57025', '57026', '57027', '57028', '57029', '57030', '57031', '57033', '57034', '57035', '57036', '57037', '57038', '57039', '57040', '57043', '57044', '57045', '57046', '57047', '57048', '57050', '57051', '57053', '57054', '57057', '57058', '57059', '57062', '57063', '57065', '57066', '57070', '57071', '57072', '57073', '57075', '57076', '57077', '57198', '57212', '57213', '57214', '57216', '57218', '57219', '57220', '57221', '57223', '57224', '57225', '57226', '57227', '57231', '57233', '57234', '57237', '57238', '57239', '57241', '57243', '57244', '57245', '57246', '57247', '57248', '57249', '57251', '57256', '57259', '57261', '57262', '57263', '57264', '57266', '57268', '57269', '57271', '57272', '57273', '57274', '57276', '57278', '57279', '57311', '57312', '57313', '57314', '57315', '57319', '57322', '57323', '57324', '57325', '57328', '57331', '57332', '57334', '57339', '57342', '57348', '57349', '57350', '57353', '57354', '57355', '57357', '57359', '57362', '57364', '57365', '57366', '57367', '57368', '57369', '57374', '57376', '57380', '57384', '57386', '57399', '57421', '57422', '57426', '57427', '57428', '57429', '57430', '57432', '57433', '57436', '57438', '57441', '57442', '57445', '57446', '57450', '57451', '57454', '57461', '57462', '57465', '57468', '57469', '57472', '57476', '57477', '57479', '57522', '57526', '57528', '57532', '57533', '57542', '57548', '57559', '57564', '57568', '57570', '57572', '57576', '57580', '57601', '57626', '57628', '57631', '57632', '57640', '57647', '57656', '57717', '57718', '57729', '57730', '57732', '57736', '57742', '57744', '57745', '57747', '57750', '57752', '57754', '57759', '57761', '57765', '57766', '57769', '57773', '57777', '57779', '57782', '57785', '57790', '57793', '58004', '58005', '58006', '58007', '58008', '58011', '58015', '58018', '58021', '58027', '58029', '58031', '58032', '58033', '58035', '58036', '58038', '58040', '58041', '58043', '58045', '58046', '58047', '58048', '58049', '58051', '58052', '58053', '58054', '58059', '58060', '58064', '58067', '58068', '58071', '58072', '58077', '58079', '58081', '58210', '58213', '58214', '58218', '58220', '58222', '58224', '58225', '58227', '58228', '58230', '58231', '58235', '58237', '58238', '58240', '58243', '58249', '58250', '58251', '58255', '58256', '58257', '58258', '58259', '58261', '58262', '58265', '58266', '58267', '58269', '58270', '58272', '58273', '58274', '58275', '58276', '58278', '58282', '58301', '58310', '58311', '58313', '58316', '58317', '58318', '58319', '58324', '58325', '58327', '58329', '58330', '58332', '58335', '58341', '58344', '58345', '58346', '58348', '58352', '58355', '58356', '58362', '58363', '58366', '58367', '58368', '58369', '58370', '58380', '58384', '58415', '58420', '58421', '58425', '58428', '58433', '58436', '58442', '58448', '58455', '58458', '58461', '58463', '58464', '58474', '58479', '58482', '58484', '58489', '58492', '58495', '58523', '58538', '58540', '58544', '58545', '58549', '58552', '58553', '58558', '58559', '58561', '58565', '58571', '58573', '58575', '58576', '58577', '58579', '58621', '58622', '58623', '58626', '58631', '58638', '58639', '58640', '58645', '58652', '58655', '58710', '58711', '58713', '58721', '58722', '58725', '58727', '58730', '58731', '58733', '58740', '58744', '58746', '58747', '58748', '58750', '58752', '58756', '58757', '58758', '58759', '58761', '58763', '58765', '58768', '58770', '58775', '58781', '58782', '58784', '58785', '58790', '58793', '58831', '58843', '58849', '58853', '58854', '59001', '59006', '59007', '59012', '59013', '59014', '59018', '59020', '59026', '59027', '59029', '59030', '59034', '59035', '59037', '59047', '59063', '59065', '59068', '59079', '59082', '59088', '59089', '59201', '59212', '59217', '59218', '59221', '59223', '59230', '59231', '59245', '59248', '59254', '59255', '59263', '59270', '59273', '59301', '59323', '59330', '59333', '59338', '59348', '59412', '59417', '59422', '59425', '59427', '59433', '59434', '59436', '59442', '59443', '59445', '59452', '59457', '59461', '59465', '59468', '59472', '59474', '59482', '59485', '59486', '59487', '59501', '59521', '59522', '59526', '59544', '59631', '59632', '59634', '59638', '59639', '59640', '59643', '59644', '59645', '59710', '59711', '59713', '59722', '59725', '59729', '59730', '59735', '59740', '59741', '59743', '59746', '59747', '59748', '59749', '59751', '59752', '59754', '59755', '59759', '59760', '59761', '59821', '59824', '59825', '59828', '59830', '59831', '59833', '59834', '59836', '59842', '59843', '59846', '59847', '59856', '59858', '59860', '59864', '59865', '59868', '59870', '59871', '59872', '59873', '59875', '59910', '59911', '59912', '59914', '59915', '59917', '59918', '59919', '59922', '59923', '59926', '59927', '59929', '59931', '59932', '59935', '59936', '60034', '60111', '60150', '60151', '60182', '60420', '60424', '60437', '60444', '60460', '60468', '60470', '60479', '60511', '60518', '60520', '60530', '60531', '60537', '60541', '60549', '60550', '60551', '60552', '60553', '60557', '60910', '60911', '60912', '60913', '60917', '60918', '60919', '60920', '60921', '60922', '60924', '60927', '60928', '60929', '60930', '60931', '60933', '60934', '60935', '60938', '60939', '60940', '60941', '60942', '60946', '60948', '60949', '60951', '60952', '60953', '60955', '60956', '60958', '60959', '60960', '60961', '60962', '60963', '60964', '60966', '60967', '60968', '60969', '60973', '60974', '61001', '61006', '61007', '61012', '61014', '61015', '61018', '61019', '61024', '61027', '61028', '61030', '61031', '61038', '61039', '61041', '61042', '61043', '61044', '61046', '61047', '61048', '61049', '61050', '61051', '61052', '61053', '61054', '61059', '61060', '61062', '61063', '61064', '61067', '61070', '61074', '61075', '61078', '61085', '61087', '61089', '61091', '61230', '61231', '61233', '61234', '61235', '61236', '61237', '61238', '61242', '61243', '61250', '61251', '61252', '61258', '61259', '61260', '61261', '61262', '61263', '61270', '61272', '61274', '61277', '61279', '61281', '61283', '61285', '61310', '61312', '61313', '61314', '61315', '61316', '61318', '61319', '61321', '61322', '61324', '61325', '61326', '61327', '61329', '61330', '61331', '61333', '61334', '61336', '61337', '61338', '61340', '61341', '61342', '61344', '61345', '61346', '61349', '61353', '61358', '61359', '61360', '61361', '61367', '61368', '61369', '61370', '61373', '61375', '61376', '61377', '61378', '61379', '61410', '61412', '61413', '61414', '61415', '61416', '61417', '61418', '61420', '61421', '61422', '61423', '61424', '61425', '61426', '61427', '61428', '61431', '61432', '61433', '61434', '61435', '61436', '61437', '61438', '61439', '61440', '61441', '61442', '61447', '61449', '61450', '61452', '61453', '61454', '61458', '61459', '61460', '61462', '61465', '61466', '61467', '61468', '61469', '61470', '61471', '61472', '61473', '61474', '61475', '61476', '61477', '61478', '61479', '61480', '61482', '61483', '61484', '61485', '61486', '61488', '61489', '61490', '61491', '61501', '61516', '61517', '61526', '61529', '61531', '61532', '61533', '61534', '61536', '61540', '61542', '61544', '61545', '61546', '61559', '61560', '61561', '61565', '61567', '61569', '61570', '61572', '61720', '61721', '61722', '61723', '61724', '61725', '61726', '61728', '61730', '61731', '61732', '61733', '61734', '61735', '61737', '61738', '61739', '61740', '61741', '61743', '61744', '61745', '61747', '61748', '61749', '61750', '61751', '61752', '61753', '61754', '61756', '61758', '61759', '61760', '61769', '61770', '61771', '61772', '61773', '61774', '61775', '61776', '61777', '61778', '61810', '61811', '61812', '61813', '61814', '61816', '61817', '61818', '61830', '61831', '61839', '61840', '61841', '61842', '61843', '61844', '61845', '61846', '61849', '61850', '61852', '61854', '61855', '61857', '61859', '61862', '61863', '61865', '61870', '61871', '61872', '61875', '61876', '61878', '61882', '61910', '61912', '61913', '61914', '61917', '61919', '61924', '61925', '61928', '61929', '61930', '61931', '61932', '61933', '61937', '61940', '61941', '61942', '61943', '61949', '61951', '61957', '62006', '62011', '62012', '62013', '62014', '62015', '62016', '62017', '62019', '62021', '62022', '62023', '62027', '62028', '62030', '62031', '62032', '62033', '62036', '62044', '62045', '62047', '62049', '62050', '62051', '62052', '62054', '62061', '62063', '62065', '62074', '62075', '62076', '62078', '62079', '62080', '62081', '62082', '62083', '62086', '62091', '62092', '62094', '62097', '62214', '62217', '62218', '62231', '62237', '62238', '62241', '62242', '62244', '62245', '62253', '62255', '62257', '62261', '62263', '62266', '62268', '62271', '62272', '62273', '62274', '62275', '62277', '62281', '62283', '62284', '62288', '62297', '62311', '62312', '62313', '62314', '62316', '62318', '62319', '62320', '62321', '62324', '62325', '62326', '62330', '62334', '62336', '62338', '62339', '62340', '62341', '62343', '62345', '62346', '62347', '62348', '62349', '62351', '62353', '62354', '62356', '62357', '62358', '62359', '62360', '62361', '62363', '62365', '62366', '62367', '62370', '62373', '62374', '62375', '62376', '62378', '62379', '62380', '62410', '62411', '62413', '62414', '62417', '62418', '62419', '62420', '62421', '62422', '62423', '62424', '62425', '62426', '62427', '62428', '62431', '62432', '62433', '62434', '62436', '62438', '62439', '62440', '62441', '62442', '62444', '62445', '62446', '62447', '62448', '62449', '62451', '62452', '62460', '62461', '62462', '62463', '62465', '62466', '62468', '62469', '62473', '62474', '62475', '62476', '62477', '62478', '62479', '62480', '62501', '62510', '62512', '62513', '62514', '62515', '62518', '62519', '62520', '62530', '62531', '62532', '62533', '62534', '62536', '62539', '62541', '62543', '62544', '62545', '62546', '62547', '62548', '62550', '62551', '62552', '62555', '62556', '62557', '62558', '62560', '62565', '62567', '62571', '62572', '62573', '62601', '62610', '62611', '62612', '62613', '62617', '62618', '62621', '62622', '62624', '62625', '62626', '62627', '62628', '62630', '62631', '62633', '62634', '62635', '62638', '62639', '62640', '62642', '62643', '62644', '62649', '62655', '62659', '62661', '62662', '62663', '62664', '62665', '62666', '62667', '62668', '62670', '62671', '62672', '62673', '62674', '62675', '62677', '62681', '62682', '62683', '62685', '62686', '62688', '62691', '62692', '62694', '62803', '62805', '62806', '62807', '62808', '62809', '62810', '62811', '62814', '62815', '62816', '62817', '62818', '62820', '62821', '62823', '62824', '62827', '62828', '62830', '62831', '62835', '62836', '62837', '62838', '62842', '62844', '62845', '62847', '62849', '62850', '62851', '62853', '62854', '62857', '62858', '62859', '62860', '62862', '62863', '62865', '62866', '62867', '62868', '62869', '62870', '62871', '62872', '62875', '62877', '62878', '62879', '62880', '62882', '62884', '62885', '62886', '62887', '62888', '62889', '62890', '62892', '62893', '62894', '62895', '62897', '62898', '62899', '62905', '62907', '62908', '62912', '62914', '62916', '62917', '62919', '62922', '62923', '62924', '62926', '62932', '62934', '62935', '62938', '62941', '62942', '62943', '62950', '62952', '62953', '62954', '62956', '62957', '62958', '62960', '62961', '62963', '62964', '62965', '62967', '62969', '62970', '62972', '62974', '62975', '62976', '62977', '62979', '62982', '62983', '62984', '62985', '62987', '62988', '62990', '62991', '62992', '62994', '62995', '62996', '62997', '62998', '63013', '63014', '63015', '63023', '63036', '63037', '63041', '63055', '63056', '63057', '63060', '63061', '63066', '63068', '63072', '63077', '63087', '63091', '63330', '63332', '63333', '63334', '63336', '63339', '63343', '63344', '63345', '63347', '63349', '63350', '63351', '63352', '63353', '63357', '63359', '63361', '63369', '63373', '63377', '63380', '63381', '63382', '63384', '63390', '63430', '63431', '63432', '63434', '63435', '63436', '63437', '63438', '63440', '63441', '63443', '63445', '63447', '63448', '63451', '63452', '63453', '63454', '63456', '63458', '63459', '63460', '63461', '63462', '63463', '63464', '63465', '63466', '63467', '63468', '63469', '63471', '63472', '63473', '63474', '63530', '63531', '63532', '63533', '63534', '63535', '63536', '63537', '63538', '63539', '63540', '63541', '63543', '63545', '63546', '63547', '63548', '63549', '63551', '63552', '63555', '63556', '63558', '63559', '63561', '63565', '63567', '63621', '63622', '63623', '63624', '63626', '63627', '63628', '63631', '63637', '63645', '63648', '63650', '63653', '63654', '63660', '63664', '63670', '63673', '63730', '63732', '63735', '63736', '63739', '63740', '63743', '63744', '63745', '63747', '63748', '63750', '63753', '63760', '63766', '63767', '63769', '63770', '63771', '63774', '63782', '63783', '63785', '63820', '63821', '63822', '63823', '63825', '63827', '63829', '63830', '63834', '63837', '63839', '63845', '63846', '63848', '63849', '63851', '63852', '63855', '63860', '63862', '63867', '63868', '63869', '63870', '63871', '63873', '63874', '63875', '63876', '63877', '63879', '63880', '63932', '63933', '63934', '63935', '63939', '63940', '63943', '63945', '63947', '63953', '63955', '63960', '63963', '63965', '63966', '63967', '64001', '64011', '64016', '64017', '64018', '64019', '64020', '64021', '64022', '64035', '64036', '64037', '64040', '64061', '64062', '64067', '64071', '64074', '64076', '64077', '64084', '64085', '64088', '64090', '64096', '64098', '64164', '64166', '64401', '64420', '64422', '64423', '64424', '64426', '64427', '64428', '64430', '64431', '64432', '64433', '64434', '64436', '64438', '64439', '64440', '64442', '64443', '64444', '64445', '64446', '64448', '64449', '64451', '64453', '64454', '64455', '64458', '64459', '64461', '64463', '64465', '64466', '64467', '64469', '64470', '64471', '64473', '64474', '64475', '64476', '64477', '64479', '64480', '64482', '64483', '64484', '64486', '64489', '64490', '64492', '64493', '64494', '64496', '64497', '64620', '64622', '64623', '64624', '64625', '64630', '64631', '64632', '64633', '64635', '64636', '64637', '64638', '64639', '64640', '64641', '64642', '64643', '64644', '64645', '64647', '64648', '64649', '64650', '64651', '64652', '64653', '64654', '64655', '64656', '64657', '64659', '64660', '64661', '64664', '64668', '64670', '64671', '64673', '64674', '64676', '64679', '64680', '64681', '64682', '64683', '64686', '64688', '64689', '64720', '64722', '64723', '64724', '64725', '64726', '64728', '64730', '64733', '64734', '64738', '64739', '64741', '64742', '64744', '64746', '64747', '64748', '64750', '64751', '64752', '64755', '64759', '64761', '64762', '64763', '64765', '64767', '64769', '64770', '64771', '64776', '64778', '64779', '64780', '64781', '64783', '64784', '64790', '64831', '64832', '64833', '64842', '64843', '64844', '64847', '64848', '64849', '64853', '64854', '64855', '64856', '64859', '64861', '64862', '64863', '64865', '64867', '64873', '65001', '65013', '65016', '65018', '65023', '65024', '65025', '65032', '65034', '65035', '65036', '65037', '65038', '65039', '65040', '65041', '65042', '65046', '65047', '65048', '65051', '65052', '65053', '65054', '65055', '65059', '65063', '65066', '65069', '65072', '65074', '65075', '65076', '65079', '65080', '65081', '65082', '65083', '65084', '65085', '65230', '65231', '65232', '65236', '65239', '65240', '65246', '65247', '65248', '65250', '65254', '65256', '65257', '65258', '65259', '65260', '65261', '65262', '65263', '65264', '65274', '65275', '65276', '65279', '65280', '65281', '65282', '65284', '65286', '65287', '65320', '65321', '65322', '65324', '65325', '65326', '65327', '65329', '65330', '65332', '65333', '65334', '65335', '65336', '65337', '65338', '65344', '65345', '65347', '65348', '65349', '65350', '65351', '65354', '65355', '65360', '65433', '65444', '65446', '65456', '65457', '65459', '65462', '65464', '65466', '65470', '65473', '65483', '65484', '65534', '65535', '65540', '65541', '65542', '65543', '65548', '65552', '65555', '65557', '65560', '65566', '65567', '65570', '65571', '65580', '65582', '65584', '65588', '65589', '65591', '65601', '65604', '65605', '65606', '65608', '65609', '65611', '65612', '65614', '65617', '65618', '65620', '65622', '65623', '65624', '65626', '65627', '65629', '65633', '65636', '65640', '65641', '65644', '65648', '65649', '65650', '65652', '65653', '65654', '65656', '65658', '65659', '65660', '65661', '65662', '65664', '65666', '65667', '65668', '65669', '65674', '65675', '65679', '65680', '65681', '65682', '65688', '65689', '65701', '65705', '65708', '65710', '65711', '65715', '65720', '65722', '65724', '65725', '65728', '65729', '65730', '65731', '65732', '65733', '65737', '65740', '65741', '65746', '65752', '65753', '65754', '65755', '65756', '65759', '65760', '65764', '65766', '65767', '65770', '65771', '65773', '65774', '65775', '65776', '65777', '65781', '65785', '65786', '65787', '65788', '65790', '65791', '65793', '66008', '66010', '66015', '66016', '66017', '66020', '66021', '66023', '66026', '66032', '66035', '66040', '66041', '66050', '66052', '66053', '66054', '66056', '66058', '66060', '66064', '66066', '66070', '66072', '66073', '66075', '66076', '66078', '66079', '66080', '66086', '66087', '66088', '66090', '66091', '66092', '66094', '66095', '66097', '66407', '66408', '66411', '66413', '66414', '66415', '66416', '66417', '66418', '66419', '66422', '66423', '66424', '66425', '66427', '66428', '66429', '66431', '66432', '66436', '66438', '66439', '66440', '66449', '66450', '66451', '66501', '66507', '66508', '66509', '66510', '66512', '66514', '66515', '66516', '66520', '66521', '66522', '66523', '66524', '66526', '66528', '66531', '66533', '66534', '66535', '66537', '66538', '66543', '66546', '66548', '66549', '66550', '66551', '66552', '66554', '66630', '66701', '66711', '66713', '66716', '66717', '66724', '66725', '66727', '66728', '66732', '66733', '66735', '66736', '66738', '66739', '66740', '66741', '66743', '66746', '66748', '66753', '66756', '66758', '66759', '66760', '66761', '66767', '66769', '66770', '66771', '66772', '66773', '66775', '66776', '66778', '66779', '66780', '66781', '66830', '66834', '66835', '66838', '66839', '66842', '66843', '66845', '66846', '66849', '66851', '66852', '66854', '66856', '66858', '66859', '66861', '66862', '66864', '66865', '66868', '66869', '66871', '66872', '66873', '66901', '66930', '66932', '66935', '66936', '66937', '66938', '66939', '66940', '66941', '66944', '66945', '66949', '66951', '66952', '66953', '66955', '66956', '66961', '66962', '66964', '66966', '66967', '66968', '67001', '67003', '67004', '67008', '67012', '67013', '67017', '67019', '67020', '67021', '67022', '67024', '67025', '67026', '67029', '67031', '67035', '67038', '67039', '67041', '67045', '67047', '67050', '67051', '67053', '67054', '67056', '67058', '67059', '67063', '67065', '67066', '67068', '67070', '67071', '67072', '67073', '67074', '67103', '67104', '67105', '67106', '67108', '67111', '67118', '67119', '67120', '67123', '67124', '67127', '67131', '67132', '67134', '67135', '67138', '67140', '67143', '67144', '67146', '67149', '67151', '67154', '67159', '67232', '67301', '67330', '67332', '67333', '67334', '67335', '67336', '67341', '67342', '67346', '67347', '67349', '67351', '67352', '67353', '67354', '67356', '67361', '67364', '67410', '67416', '67417', '67418', '67420', '67422', '67425', '67427', '67428', '67430', '67431', '67432', '67436', '67437', '67438', '67439', '67441', '67442', '67443', '67445', '67446', '67448', '67449', '67450', '67451', '67454', '67455', '67456', '67457', '67459', '67464', '67466', '67467', '67468', '67470', '67473', '67474', '67475', '67480', '67483', '67485', '67487', '67490', '67491', '67492', '67510', '67512', '67514', '67516', '67520', '67521', '67522', '67523', '67524', '67525', '67526', '67529', '67530', '67543', '67544', '67545', '67546', '67547', '67548', '67550', '67552', '67554', '67556', '67557', '67560', '67561', '67563', '67564', '67565', '67566', '67567', '67570', '67572', '67574', '67575', '67576', '67578', '67579', '67583', '67584', '67621', '67622', '67625', '67626', '67627', '67629', '67632', '67637', '67638', '67639', '67640', '67642', '67644', '67647', '67648', '67649', '67651', '67657', '67659', '67660', '67661', '67663', '67667', '67669', '67671', '67672', '67674', '67701', '67730', '67731', '67732', '67733', '67734', '67735', '67737', '67738', '67739', '67740', '67745', '67747', '67748', '67749', '67751', '67752', '67753', '67756', '67757', '67758', '67834', '67835', '67837', '67838', '67843', '67849', '67854', '67855', '67859', '67860', '67861', '67864', '67865', '67867', '67869', '67871', '67876', '67877', '67878', '67880', '67882', '67950', '67951', '67952', '68002', '68003', '68004', '68014', '68015', '68016', '68017', '68018', '68020', '68030', '68031', '68033', '68034', '68035', '68036', '68037', '68038', '68039', '68040', '68044', '68045', '68047', '68048', '68050', '68054', '68057', '68058', '68061', '68062', '68065', '68066', '68067', '68070', '68071', '68073', '68301', '68303', '68304', '68305', '68307', '68309', '68313', '68314', '68315', '68316', '68317', '68318', '68319', '68320', '68321', '68322', '68323', '68324', '68325', '68326', '68327', '68328', '68329', '68331', '68332', '68335', '68336', '68337', '68338', '68339', '68340', '68341', '68342', '68343', '68344', '68345', '68346', '68347', '68348', '68349', '68350', '68351', '68352', '68354', '68355', '68357', '68358', '68359', '68360', '68361', '68362', '68364', '68365', '68367', '68368', '68370', '68371', '68372', '68375', '68376', '68377', '68378', '68379', '68380', '68382', '68401', '68402', '68403', '68404', '68405', '68406', '68407', '68409', '68410', '68413', '68414', '68415', '68416', '68417', '68418', '68419', '68420', '68421', '68422', '68423', '68424', '68428', '68431', '68433', '68436', '68437', '68438', '68439', '68440', '68441', '68442', '68443', '68445', '68446', '68447', '68448', '68450', '68452', '68453', '68454', '68455', '68456', '68457', '68458', '68460', '68461', '68463', '68464', '68465', '68466', '68531', '68620', '68622', '68623', '68624', '68626', '68627', '68628', '68629', '68632', '68633', '68634', '68635', '68636', '68638', '68640', '68641', '68642', '68643', '68644', '68647', '68648', '68649', '68651', '68652', '68653', '68654', '68655', '68658', '68660', '68662', '68663', '68665', '68666', '68667', '68669', '68710', '68713', '68715', '68716', '68717', '68718', '68720', '68722', '68723', '68724', '68725', '68727', '68728', '68729', '68730', '68732', '68733', '68734', '68736', '68737', '68738', '68739', '68740', '68741', '68743', '68745', '68747', '68748', '68752', '68756', '68757', '68758', '68761', '68763', '68764', '68765', '68766', '68767', '68768', '68769', '68770', '68771', '68772', '68773', '68777', '68779', '68780', '68781', '68783', '68784', '68785', '68786', '68787', '68788', '68789', '68790', '68791', '68792', '68812', '68814', '68815', '68816', '68817', '68818', '68819', '68820', '68822', '68824', '68825', '68826', '68827', '68831', '68832', '68835', '68836', '68841', '68842', '68843', '68844', '68846', '68850', '68852', '68853', '68854', '68856', '68858', '68861', '68862', '68863', '68864', '68866', '68869', '68870', '68872', '68873', '68874', '68875', '68876', '68878', '68881', '68882', '68883', '68920', '68922', '68923', '68924', '68925', '68927', '68928', '68929', '68930', '68932', '68933', '68935', '68937', '68938', '68939', '68940', '68941', '68942', '68944', '68945', '68947', '68949', '68950', '68952', '68954', '68955', '68956', '68957', '68958', '68959', '68960', '68961', '68963', '68964', '68966', '68967', '68970', '68971', '68973', '68974', '68975', '68976', '68978', '68979', '68980', '68981', '68982', '69021', '69022', '69025', '69028', '69029', '69031', '69033', '69036', '69043', '69120', '69122', '69129', '69130', '69133', '69134', '69138', '69140', '69143', '69145', '69146', '69150', '69151', '69153', '69154', '69155', '69160', '69162', '69165', '69169', '69171', '69190', '69217', '69301', '69334', '69336', '69337', '69339', '69348', '69349', '69352', '69353', '69355', '69356', '69358', '69365', '70036', '70040', '70041', '70046', '70049', '70050', '70052', '70057', '70066', '70067', '70080', '70082', '70083', '70085', '70086', '70090', '70091', '70339', '70340', '70341', '70344', '70346', '70352', '70353', '70354', '70355', '70356', '70357', '70358', '70372', '70373', '70375', '70377', '70390', '70391', '70392', '70397', '70421', '70431', '70436', '70438', '70442', '70444', '70446', '70449', '70453', '70456', '70462', '70465', '70467', '70512', '70513', '70514', '70515', '70516', '70521', '70524', '70531', '70534', '70537', '70538', '70542', '70543', '70544', '70548', '70549', '70552', '70554', '70556', '70559', '70577', '70581', '70582', '70584', '70585', '70586', '70589', '70591', '70633', '70638', '70639', '70644', '70645', '70648', '70650', '70652', '70653', '70655', '70657', '70658', '70660', '70661', '70662', '70712', '70715', '70717', '70720', '70722', '70725', '70729', '70730', '70732', '70736', '70738', '70740', '70744', '70748', '70749', '70750', '70752', '70753', '70755', '70756', '70757', '70759', '70761', '70762', '70772', '70773', '70775', '70777', '70781', '70783', '70784', '70787', '70788', '70789', '71001', '71004', '71007', '71008', '71018', '71019', '71023', '71024', '71027', '71028', '71029', '71030', '71032', '71038', '71039', '71040', '71043', '71044', '71045', '71046', '71047', '71049', '71051', '71052', '71060', '71061', '71063', '71065', '71068', '71069', '71071', '71072', '71073', '71078', '71079', '71082', '71218', '71219', '71222', '71226', '71227', '71229', '71230', '71232', '71233', '71234', '71235', '71237', '71238', '71243', '71251', '71254', '71256', '71259', '71261', '71263', '71266', '71268', '71269', '71275', '71276', '71277', '71282', '71286', '71295', '71316', '71322', '71323', '71324', '71325', '71326', '71327', '71328', '71330', '71331', '71333', '71334', '71336', '71339', '71341', '71342', '71346', '71353', '71354', '71355', '71356', '71357', '71358', '71362', '71366', '71367', '71368', '71369', '71371', '71373', '71377', '71378', '71404', '71407', '71409', '71410', '71411', '71415', '71417', '71418', '71419', '71422', '71423', '71424', '71426', '71427', '71429', '71430', '71432', '71433', '71435', '71438', '71439', '71440', '71441', '71446', '71447', '71449', '71454', '71456', '71460', '71465', '71467', '71469', '71474', '71479', '71480', '71483', '71486', '71496', '71630', '71638', '71639', '71640', '71642', '71643', '71644', '71646', '71647', '71653', '71658', '71660', '71663', '71665', '71666', '71667', '71670', '71671', '71674', '71675', '71720', '71724', '71725', '71728', '71740', '71742', '71743', '71744', '71745', '71747', '71748', '71749', '71751', '71752', '71758', '71763', '71764', '71765', '71766', '71770', '71772', '71820', '71822', '71823', '71825', '71826', '71832', '71835', '71837', '71838', '71842', '71847', '71851', '71852', '71855', '71857', '71858', '71859', '71860', '71862', '71865', '71866', '71921', '71922', '71929', '71933', '71937', '71940', '71941', '71944', '71945', '71949', '71950', '71952', '71953', '71956', '71957', '71958', '71959', '71960', '71961', '71965', '71968', '71970', '71971', '71972', '72006', '72010', '72016', '72017', '72020', '72021', '72025', '72029', '72036', '72039', '72040', '72041', '72042', '72044', '72045', '72046', '72047', '72055', '72057', '72059', '72060', '72063', '72064', '72067', '72070', '72074', '72081', '72082', '72083', '72084', '72085', '72087', '72088', '72101', '72102', '72107', '72111', '72112', '72121', '72122', '72125', '72126', '72127', '72128', '72129', '72130', '72131', '72132', '72133', '72135', '72136', '72137', '72139', '72142', '72156', '72157', '72169', '72170', '72175', '72179', '72180', '72182', '72311', '72312', '72313', '72321', '72322', '72324', '72325', '72326', '72327', '72330', '72331', '72333', '72338', '72339', '72340', '72341', '72342', '72346', '72347', '72348', '72350', '72351', '72352', '72354', '72355', '72358', '72360', '72365', '72366', '72370', '72372', '72373', '72374', '72376', '72383', '72384', '72386', '72387', '72390', '72392', '72395', '72412', '72413', '72414', '72415', '72416', '72419', '72422', '72429', '72430', '72432', '72433', '72434', '72436', '72437', '72438', '72439', '72440', '72441', '72442', '72443', '72444', '72447', '72450', '72454', '72455', '72456', '72457', '72458', '72460', '72461', '72462', '72464', '72466', '72467', '72469', '72470', '72471', '72473', '72475', '72478', '72479', '72512', '72513', '72521', '72522', '72523', '72524', '72527', '72529', '72532', '72534', '72536', '72537', '72539', '72540', '72544', '72550', '72554', '72555', '72556', '72562', '72564', '72565', '72567', '72568', '72571', '72572', '72573', '72576', '72577', '72579', '72581', '72610', '72611', '72613', '72616', '72619', '72623', '72629', '72630', '72631', '72632', '72633', '72634', '72635', '72636', '72638', '72642', '72644', '72657', '72658', '72660', '72661', '72662', '72666', '72668', '72669', '72672', '72679', '72721', '72727', '72732', '72736', '72738', '72740', '72744', '72747', '72749', '72768', '72769', '72773', '72774', '72821', '72823', '72824', '72827', '72828', '72832', '72833', '72835', '72837', '72838', '72840', '72842', '72843', '72845', '72846', '72847', '72853', '72863', '72924', '72927', '72928', '72930', '72932', '72933', '72937', '72938', '72940', '72943', '72944', '72946', '72947', '72949', '72950', '72952', '72958', '73002', '73004', '73005', '73006', '73009', '73011', '73015', '73016', '73017', '73021', '73024', '73027', '73028', '73029', '73030', '73031', '73032', '73038', '73042', '73047', '73050', '73051', '73052', '73053', '73054', '73055', '73057', '73059', '73061', '73062', '73063', '73073', '73074', '73075', '73077', '73086', '73090', '73092', '73095', '73098', '73432', '73436', '73438', '73439', '73440', '73441', '73442', '73443', '73444', '73446', '73447', '73448', '73449', '73453', '73455', '73458', '73460', '73463', '73481', '73487', '73520', '73526', '73527', '73528', '73529', '73530', '73532', '73537', '73538', '73539', '73540', '73541', '73542', '73543', '73546', '73547', '73548', '73549', '73550', '73551', '73552', '73553', '73554', '73555', '73556', '73557', '73560', '73562', '73564', '73565', '73566', '73567', '73568', '73569', '73570', '73572', '73573', '73620', '73622', '73624', '73626', '73632', '73639', '73641', '73644', '73645', '73646', '73647', '73648', '73650', '73651', '73654', '73655', '73661', '73662', '73663', '73664', '73666', '73668', '73669', '73716', '73717', '73718', '73720', '73724', '73726', '73727', '73728', '73729', '73730', '73731', '73733', '73735', '73736', '73737', '73738', '73739', '73741', '73742', '73743', '73747', '73749', '73750', '73754', '73756', '73757', '73758', '73759', '73760', '73762', '73764', '73766', '73771', '73772', '73773', '73801', '73802', '73832', '73834', '73838', '73840', '73841', '73847', '73852', '73853', '73857', '73858', '73859', '73860', '73901', '73932', '73938', '73942', '73945', '73950', '73951', '74009', '74010', '74016', '74020', '74022', '74023', '74026', '74027', '74029', '74030', '74032', '74034', '74035', '74036', '74038', '74039', '74044', '74045', '74047', '74048', '74051', '74052', '74054', '74058', '74059', '74060', '74061', '74062', '74079', '74080', '74081', '74082', '74083', '74084', '74301', '74330', '74331', '74332', '74333', '74335', '74337', '74338', '74340', '74342', '74343', '74346', '74347', '74349', '74352', '74359', '74363', '74365', '74366', '74367', '74368', '74421', '74422', '74423', '74425', '74426', '74427', '74428', '74430', '74431', '74432', '74434', '74435', '74436', '74437', '74438', '74441', '74445', '74450', '74451', '74452', '74454', '74455', '74458', '74459', '74460', '74462', '74469', '74470', '74471', '74472', '74530', '74533', '74534', '74535', '74542', '74545', '74546', '74547', '74553', '74561', '74563', '74567', '74570', '74578', '74604', '74630', '74631', '74632', '74633', '74636', '74637', '74640', '74641', '74643', '74644', '74646', '74647', '74651', '74653', '74722', '74723', '74726', '74727', '74728', '74729', '74730', '74731', '74733', '74734', '74736', '74740', '74741', '74743', '74750', '74764', '74824', '74825', '74827', '74829', '74831', '74832', '74833', '74834', '74836', '74840', '74843', '74844', '74848', '74849', '74850', '74852', '74854', '74855', '74856', '74859', '74860', '74864', '74865', '74866', '74869', '74872', '74873', '74875', '74880', '74881', '74883', '74884', '74932', '74940', '74941', '74944', '74945', '74948', '74951', '74956', '74959', '74960', '74964', '74965', '74966', '75058', '75102', '75124', '75125', '75127', '75140', '75143', '75144', '75148', '75155', '75158', '75161', '75163', '75169', '75410', '75411', '75412', '75413', '75415', '75416', '75417', '75420', '75421', '75422', '75423', '75424', '75426', '75428', '75429', '75431', '75432', '75433', '75435', '75436', '75437', '75438', '75439', '75440', '75442', '75446', '75447', '75448', '75449', '75451', '75452', '75453', '75457', '75462', '75468', '75470', '75471', '75472', '75473', '75476', '75478', '75479', '75480', '75481', '75486', '75487', '75488', '75491', '75493', '75494', '75496', '75497', '75550', '75556', '75563', '75566', '75567', '75568', '75571', '75572', '75573', '75574', '75630', '75631', '75633', '75636', '75637', '75638', '75639', '75640', '75643', '75645', '75650', '75651', '75653', '75656', '75657', '75659', '75661', '75667', '75668', '75681', '75682', '75683', '75684', '75685', '75686', '75689', '75691', '75692', '75705', '75750', '75752', '75754', '75755', '75756', '75763', '75765', '75770', '75778', '75783', '75784', '75788', '75789', '75792', '75802', '75803', '75831', '75834', '75835', '75839', '75840', '75844', '75845', '75846', '75848', '75853', '75855', '75856', '75858', '75859', '75860', '75861', '75880', '75884', '75886', '75925', '75931', '75934', '75935', '75936', '75937', '75939', '75941', '75943', '75949', '75951', '75954', '75956', '75966', '75968', '75969', '75972', '75975', '75979', '75990', '76023', '76035', '76041', '76043', '76044', '76050', '76055', '76061', '76064', '76066', '76067', '76070', '76073', '76077', '76084', '76088', '76093', '76225', '76228', '76230', '76233', '76234', '76238', '76239', '76252', '76264', '76265', '76270', '76271', '76272', '76357', '76360', '76365', '76366', '76370', '76371', '76372', '76373', '76374', '76377', '76379', '76380', '76384', '76385', '76389', '76424', '76426', '76427', '76430', '76431', '76432', '76433', '76435', '76437', '76442', '76443', '76445', '76446', '76448', '76450', '76454', '76455', '76457', '76458', '76460', '76462', '76469', '76470', '76471', '76475', '76476', '76483', '76484', '76486', '76487', '76490', '76491', '76511', '76519', '76520', '76527', '76530', '76534', '76539', '76550', '76556', '76557', '76561', '76567', '76569', '76570', '76577', '76578', '76621', '76622', '76623', '76624', '76627', '76628', '76629', '76631', '76632', '76634', '76635', '76636', '76637', '76638', '76639', '76640', '76641', '76642', '76648', '76649', '76650', '76651', '76652', '76653', '76654', '76656', '76660', '76661', '76664', '76665', '76666', '76670', '76671', '76673', '76675', '76677', '76678', '76679', '76680', '76681', '76682', '76685', '76687', '76689', '76690', '76691', '76692', '76693', '76802', '76821', '76823', '76825', '76834', '76837', '76842', '76844', '76857', '76861', '76862', '76864', '76865', '76866', '76874', '76875', '76877', '76890', '76932', '76933', '76934', '76935', '76936', '76937', '76940', '76941', '76943', '76945', '76951', '76957', '76958', '77306', '77320', '77328', '77331', '77335', '77343', '77349', '77351', '77358', '77359', '77360', '77363', '77364', '77367', '77369', '77371', '77374', '77376', '77399', '77412', '77414', '77417', '77418', '77419', '77420', '77422', '77426', '77434', '77435', '77440', '77442', '77443', '77445', '77446', '77448', '77451', '77453', '77454', '77455', '77456', '77457', '77458', '77461', '77464', '77465', '77466', '77470', '77475', '77480', '77481', '77482', '77483', '77485', '77488', '77514', '77519', '77533', '77538', '77560', '77561', '77564', '77575', '77577', '77585', '77613', '77615', '77617', '77622', '77625', '77629', '77650', '77655', '77659', '77660', '77663', '77665', '77835', '77836', '77837', '77855', '77856', '77857', '77859', '77861', '77864', '77867', '77871', '77872', '77873', '77879', '77880', '77950', '77954', '77957', '77962', '77963', '77964', '77968', '77972', '77974', '77975', '77982', '77984', '77990', '77994', '77995', '78002', '78003', '78004', '78007', '78010', '78012', '78013', '78016', '78017', '78022', '78025', '78026', '78050', '78052', '78055', '78056', '78057', '78059', '78060', '78061', '78062', '78063', '78064', '78065', '78066', '78071', '78075', '78076', '78102', '78107', '78111', '78113', '78116', '78117', '78118', '78119', '78122', '78125', '78140', '78141', '78142', '78143', '78145', '78146', '78147', '78151', '78152', '78155', '78159', '78160', '78161', '78162', '78164', '78338', '78339', '78340', '78341', '78343', '78352', '78355', '78357', '78361', '78368', '78370', '78372', '78375', '78377', '78379', '78383', '78384', '78385', '78387', '78389', '78390', '78391', '78393', '78538', '78543', '78545', '78548', '78549', '78569', '78582', '78583', '78592', '78594', '78598', '78603', '78605', '78606', '78609', '78611', '78614', '78615', '78622', '78624', '78629', '78631', '78635', '78638', '78639', '78648', '78650', '78655', '78656', '78657', '78658', '78659', '78661', '78662', '78670', '78671', '78672', '78742', '78827', '78832', '78838', '78839', '78850', '78852', '78861', '78870', '78872', '78877', '78879', '78881', '78883', '78885', '78886', '78931', '78934', '78938', '78940', '78941', '78942', '78943', '78944', '78945', '78947', '78948', '78949', '78954', '78957', '78962', '78963', '79001', '79003', '79005', '79009', '79013', '79019', '79022', '79024', '79025', '79027', '79031', '79032', '79035', '79036', '79039', '79041', '79042', '79043', '79044', '79045', '79052', '79054', '79059', '79061', '79063', '79064', '79065', '79068', '79070', '79079', '79081', '79082', '79084', '79085', '79086', '79088', '79092', '79095', '79096', '79097', '79098', '79201', '79225', '79226', '79227', '79232', '79233', '79235', '79236', '79237', '79240', '79241', '79244', '79245', '79250', '79252', '79255', '79256', '79257', '79258', '79261', '79311', '79312', '79313', '79314', '79316', '79320', '79322', '79323', '79325', '79326', '79329', '79331', '79336', '79339', '79342', '79343', '79346', '79347', '79355', '79356', '79357', '79358', '79359', '79360', '79364', '79369', '79370', '79371', '79372', '79373', '79377', '79378', '79379', '79381', '79383', '79501', '79502', '79504', '79505', '79506', '79508', '79510', '79511', '79512', '79517', '79520', '79521', '79525', '79526', '79527', '79528', '79529', '79530', '79532', '79533', '79534', '79535', '79536', '79539', '79541', '79543', '79544', '79545', '79546', '79547', '79549', '79550', '79553', '79556', '79560', '79561', '79562', '79565', '79567', '79714', '79718', '79719', '79720', '79731', '79735', '79738', '79740', '79742', '79743', '79744', '79749', '79752', '79756', '79758', '79772', '79777', '79778', '79780', '79781', '79782', '79785', '79788', '79789', '79830', '79832', '79834', '79839', '79843', '79845', '79848', '79850', '79851', '79852', '79853', '79855', '80025', '80102', '80105', '80106', '80107', '80116', '80117', '80118', '80131', '80135', '80136', '80137', '80420', '80421', '80424', '80425', '80426', '80427', '80428', '80429', '80433', '80436', '80438', '80442', '80444', '80446', '80451', '80452', '80453', '80455', '80461', '80463', '80466', '80467', '80469', '80470', '80473', '80474', '80475', '80476', '80478', '80481', '80482', '80483', '80512', '80532', '80535', '80540', '80542', '80545', '80549', '80610', '80611', '80612', '80622', '80624', '80642', '80644', '80648', '80650', '80652', '80720', '80721', '80726', '80733', '80737', '80746', '80750', '80757', '80758', '80759', '80807', '80808', '80809', '80810', '80813', '80814', '80815', '80821', '80822', '80828', '80835', '80836', '80860', '80929', '80938', '81019', '81022', '81023', '81036', '81038', '81039', '81042', '81047', '81050', '81052', '81054', '81055', '81058', '81062', '81063', '81067', '81073', '81076', '81082', '81092', '81120', '81121', '81122', '81123', '81125', '81130', '81131', '81132', '81133', '81136', '81137', '81140', '81141', '81144', '81146', '81147', '81148', '81151', '81153', '81154', '81157', '81210', '81211', '81222', '81224', '81230', '81235', '81236', '81237', '81239', '81240', '81241', '81248', '81251', '81253', '81323', '81326', '81327', '81328', '81329', '81330', '81332', '81334', '81335', '81403', '81410', '81411', '81413', '81415', '81418', '81419', '81420', '81422', '81423', '81424', '81425', '81426', '81427', '81428', '81429', '81430', '81432', '81435', '81524', '81525', '81527', '81625', '81626', '81630', '81633', '81635', '81636', '81639', '81641', '81642', '81643', '81645', '81646', '81647', '81648', '81649', '81652', '81653', '81654', '81655', '81656', '82050', '82054', '82059', '82060', '82063', '82070', '82082', '82201', '82212', '82214', '82215', '82221', '82225', '82240', '82244', '82301', '82321', '82323', '82324', '82325', '82327', '82332', '82335', '82336', '82401', '82410', '82411', '82412', '82420', '82421', '82423', '82426', '82431', '82432', '82434', '82435', '82441', '82442', '82443', '82450', '82510', '82513', '82516', '82520', '82523', '82524', '82604', '82631', '82633', '82637', '82643', '82648', '82649', '82711', '82732', '82832', '82834', '82836', '82838', '82839', '82842', '82922', '82923', '82925', '82930', '82935', '82937', '82939', '82943', '82944', '83011', '83012', '83014', '83101', '83110', '83112', '83113', '83114', '83115', '83116', '83118', '83119', '83123', '83124', '83126', '83127', '83128', '83203', '83210', '83211', '83214', '83217', '83223', '83226', '83228', '83232', '83234', '83236', '83237', '83238', '83239', '83241', '83245', '83246', '83250', '83251', '83252', '83254', '83255', '83261', '83262', '83263', '83271', '83272', '83274', '83276', '83277', '83286', '83287', '83311', '83312', '83313', '83314', '83316', '83320', '83321', '83322', '83323', '83324', '83325', '83327', '83330', '83332', '83334', '83335', '83337', '83344', '83346', '83347', '83348', '83349', '83350', '83352', '83353', '83355', '83414', '83420', '83423', '83424', '83427', '83428', '83429', '83431', '83433', '83434', '83435', '83436', '83438', '83443', '83444', '83445', '83448', '83449', '83450', '83451', '83452', '83455', '83467', '83468', '83520', '83522', '83530', '83535', '83536', '83540', '83544', '83545', '83548', '83549', '83551', '83552', '83553', '83555', '83601', '83602', '83611', '83615', '83617', '83619', '83620', '83622', '83623', '83627', '83628', '83629', '83631', '83632', '83633', '83637', '83638', '83639', '83641', '83643', '83645', '83647', '83654', '83655', '83660', '83661', '83672', '83801', '83803', '83804', '83805', '83806', '83809', '83810', '83811', '83812', '83813', '83821', '83822', '83823', '83825', '83830', '83832', '83833', '83834', '83836', '83839', '83840', '83841', '83842', '83845', '83846', '83847', '83848', '83849', '83850', '83851', '83855', '83856', '83857', '83860', '83861', '83867', '83869', '83871', '83872', '83873', '83874', '83876', '84001', '84002', '84007', '84008', '84013', '84017', '84018', '84021', '84022', '84026', '84028', '84031', '84032', '84033', '84035', '84036', '84038', '84039', '84050', '84051', '84053', '84055', '84061', '84063', '84066', '84069', '84071', '84072', '84073', '84076', '84078', '84079', '84080', '84082', '84083', '84085', '84086', '84301', '84305', '84306', '84307', '84308', '84309', '84310', '84311', '84312', '84314', '84316', '84317', '84320', '84324', '84327', '84328', '84330', '84331', '84334', '84338', '84340', '84501', '84511', '84512', '84513', '84518', '84521', '84522', '84523', '84525', '84526', '84528', '84530', '84532', '84533', '84535', '84537', '84542', '84620', '84621', '84622', '84623', '84624', '84627', '84628', '84629', '84630', '84631', '84632', '84636', '84637', '84638', '84639', '84640', '84642', '84643', '84644', '84645', '84646', '84647', '84648', '84649', '84650', '84652', '84654', '84655', '84656', '84657', '84662', '84665', '84667', '84710', '84711', '84712', '84713', '84715', '84717', '84718', '84719', '84722', '84723', '84724', '84725', '84729', '84730', '84732', '84734', '84739', '84740', '84741', '84744', '84745', '84746', '84747', '84749', '84750', '84751', '84752', '84754', '84755', '84757', '84758', '84759', '84760', '84761', '84762', '84763', '84764', '84766', '84772', '84774', '84775', '84776', '84781', '84782', '84783', '85118', '85121', '85131', '85132', '85135', '85137', '85141', '85145', '85172', '85173', '85190', '85231', '85232', '85235', '85237', '85241', '85245', '85272', '85273', '85291', '85292', '85320', '85321', '85324', '85325', '85327', '85334', '85337', '85343', '85344', '85346', '85348', '85352', '85356', '85357', '85359', '85361', '85371', '85390', '85501', '85533', '85534', '85535', '85536', '85539', '85540', '85541', '85542', '85543', '85544', '85545', '85546', '85548', '85550', '85551', '85553', '85601', '85602', '85603', '85605', '85606', '85616', '85617', '85618', '85623', '85624', '85629', '85630', '85631', '85637', '85638', '85643', '85645', '85646', '85735', '85912', '85923', '85925', '85931', '85932', '85933', '85934', '85938', '85942', '86017', '86018', '86021', '86022', '86023', '86025', '86028', '86029', '86032', '86036', '86039', '86040', '86042', '86045', '86046', '86047', '86052', '86324', '86325', '86331', '86333', '86334', '86413', '86427', '86434', '86435', '86436', '86440', '86503', '86504', '86510', '86514', '86515', '86535', '86540', '87001', '87002', '87006', '87007', '87009', '87010', '87017', '87020', '87022', '87023', '87024', '87025', '87034', '87035', '87041', '87042', '87044', '87047', '87052', '87053', '87057', '87059', '87062', '87064', '87068', '87072', '87083', '87301', '87316', '87322', '87327', '87328', '87347', '87365', '87413', '87418', '87420', '87511', '87516', '87519', '87522', '87523', '87525', '87527', '87529', '87531', '87535', '87537', '87540', '87548', '87552', '87553', '87558', '87560', '87566', '87567', '87569', '87571', '87575', '87576', '87578', '87701', '87711', '87715', '87722', '87723', '87724', '87729', '87732', '87740', '87747', '87801', '87823', '87825', '87830', '87901', '87930', '87931', '87935', '87936', '87937', '87940', '87941', '87942', '88012', '88023', '88027', '88028', '88029', '88030', '88039', '88040', '88043', '88044', '88048', '88052', '88054', '88056', '88061', '88065', '88072', '88124', '88130', '88135', '88230', '88231', '88232', '88242', '88252', '88253', '88255', '88256', '88260', '88263', '88264', '88265', '88267', '88268', '88312', '88314', '88316', '88317', '88324', '88325', '88336', '88337', '88338', '88341', '88345', '88348', '88350', '88351', '88352', '88353', '88401', '88416', '88426', '88433', '88435', '88441', '89003', '89004', '89005', '89007', '89018', '89019', '89020', '89021', '89023', '89025', '89027', '89037', '89040', '89042', '89054', '89060', '89061', '89070', '89191', '89301', '89315', '89318', '89319', '89406', '89410', '89413', '89415', '89419', '89422', '89428', '89429', '89430', '89440', '89442', '89444', '89445', '89447', '89496', '89704', '89801', '89803', '89815', '89820', '89822', '89825', '89826', '89828', '89832', '89835', '89883', '91759', '91906', '91916', '91917', '91934', '91948', '92004', '92036', '92060', '92061', '92082', '92147', '92225', '92233', '92239', '92242', '92250', '92252', '92254', '92256', '92258', '92259', '92268', '92274', '92277', '92281', '92282', '92283', '92285', '92301', '92310', '92314', '92321', '92322', '92327', '92333', '92338', '92339', '92341', '92342', '92356', '92363', '92366', '92371', '92382', '92385', '92397', '92549', '93015', '93023', '93040', '93066', '93201', '93203', '93204', '93205', '93206', '93210', '93212', '93218', '93219', '93222', '93224', '93225', '93234', '93237', '93238', '93239', '93240', '93242', '93243', '93244', '93249', '93250', '93256', '93261', '93262', '93265', '93266', '93267', '93268', '93270', '93272', '93276', '93280', '93285', '93286', '93424', '93428', '93430', '93432', '93441', '93451', '93452', '93461', '93501', '93505', '93510', '93512', '93513', '93516', '93522', '93523', '93526', '93527', '93528', '93529', '93531', '93532', '93545', '93549', '93553', '93555', '93561', '93562', '93581', '93591', '93603', '93604', '93607', '93609', '93610', '93614', '93620', '93622', '93626', '93628', '93633', '93634', '93635', '93640', '93644', '93645', '93646', '93647', '93649', '93652', '93656', '93660', '93661', '93668', '93669', '93670', '93908', '93924', '93925', '93926', '93927', '93928', '93930', '93960', '94021', '94511', '94514', '94569', '94571', '94586', '94923', '94924', '94929', '94933', '94937', '94946', '94950', '94956', '94963', '94970', '94971', '94973', '95006', '95017', '95033', '95039', '95045', '95075', '95141', '95220', '95221', '95222', '95223', '95228', '95233', '95237', '95247', '95248', '95249', '95250', '95252', '95303', '95305', '95306', '95309', '95313', '95314', '95316', '95317', '95318', '95321', '95322', '95323', '95327', '95329', '95338', '95347', '95360', '95365', '95374', '95379', '95386', '95387', '95388', '95410', '95415', '95420', '95423', '95426', '95427', '95428', '95431', '95432', '95437', '95442', '95443', '95445', '95446', '95449', '95451', '95452', '95456', '95457', '95458', '95460', '95461', '95462', '95463', '95464', '95467', '95469', '95485', '95493', '95497', '95525', '95527', '95531', '95536', '95542', '95543', '95546', '95547', '95548', '95553', '95555', '95556', '95558', '95560', '95562', '95563', '95564', '95565', '95567', '95569', '95570', '95571', '95573', '95585', '95589', '95606', '95607', '95612', '95613', '95614', '95615', '95623', '95627', '95629', '95631', '95633', '95635', '95637', '95638', '95640', '95646', '95653', '95656', '95659', '95664', '95666', '95669', '95674', '95675', '95685', '95689', '95692', '95694', '95697', '95703', '95709', '95713', '95724', '95726', '95728', '95736', '95799', '95912', '95917', '95918', '95930', '95931', '95932', '95934', '95935', '95936', '95937', '95938', '95941', '95943', '95947', '95950', '95953', '95955', '95956', '95957', '95958', '95959', '95962', '95963', '95970', '95971', '95972', '95974', '95975', '95977', '95979', '95982', '95987', '95988', '96006', '96008', '96009', '96013', '96015', '96016', '96020', '96021', '96022', '96025', '96027', '96028', '96032', '96033', '96035', '96037', '96038', '96040', '96052', '96053', '96054', '96055', '96057', '96062', '96063', '96067', '96068', '96069', '96071', '96080', '96084', '96088', '96090', '96093', '96094', '96095', '96096', '96097', '96101', '96103', '96106', '96107', '96108', '96109', '96110', '96111', '96113', '96114', '96118', '96120', '96121', '96122', '96125', '96126', '96128', '96129', '96130', '96133', '96134', '96135', '96137', '96140', '96141', '96142', '96143', '96145', '96162', '97002', '97011', '97014', '97016', '97017', '97019', '97021', '97023', '97037', '97038', '97039', '97040', '97041', '97042', '97049', '97050', '97054', '97057', '97063', '97064', '97065', '97067', '97101', '97102', '97103', '97106', '97108', '97109', '97110', '97111', '97112', '97114', '97117', '97119', '97121', '97122', '97130', '97133', '97135', '97141', '97144', '97147', '97148', '97149', '97231', '97327', '97347', '97348', '97350', '97358', '97360', '97368', '97370', '97371', '97374', '97375', '97376', '97377', '97378', '97380', '97388', '97391', '97394', '97396', '97410', '97412', '97413', '97414', '97415', '97416', '97417', '97419', '97420', '97424', '97425', '97427', '97430', '97431', '97434', '97435', '97436', '97437', '97438', '97441', '97442', '97443', '97444', '97446', '97450', '97452', '97453', '97454', '97456', '97457', '97458', '97461', '97462', '97463', '97464', '97465', '97466', '97467', '97469', '97473', '97479', '97480', '97481', '97486', '97487', '97488', '97489', '97490', '97491', '97492', '97495', '97496', '97497', '97498', '97499', '97522', '97523', '97525', '97530', '97531', '97532', '97536', '97537', '97538', '97539', '97544', '97620', '97627', '97632', '97633', '97635', '97636', '97637', '97640', '97641', '97720', '97730', '97731', '97734', '97737', '97738', '97739', '97740', '97741', '97753', '97754', '97760', '97810', '97812', '97813', '97814', '97818', '97820', '97824', '97826', '97827', '97828', '97831', '97833', '97834', '97835', '97836', '97839', '97840', '97841', '97843', '97844', '97845', '97846', '97857', '97862', '97865', '97867', '97868', '97869', '97870', '97873', '97875', '97876', '97877', '97883', '97885', '97886', '97901', '97903', '97907', '97913', '97918', '98014', '98019', '98022', '98024', '98051', '98068', '98070', '98220', '98222', '98237', '98241', '98243', '98244', '98245', '98251', '98252', '98256', '98261', '98262', '98266', '98267', '98279', '98281', '98284', '98286', '98288', '98294', '98295', '98297', '98303', '98304', '98305', '98320', '98323', '98328', '98331', '98336', '98340', '98348', '98349', '98350', '98351', '98355', '98356', '98357', '98358', '98360', '98361', '98364', '98365', '98376', '98377', '98380', '98394', '98396', '98397', '98527', '98530', '98533', '98535', '98536', '98541', '98542', '98546', '98548', '98550', '98554', '98555', '98557', '98561', '98563', '98564', '98565', '98568', '98570', '98571', '98572', '98576', '98577', '98579', '98580', '98581', '98582', '98585', '98586', '98587', '98588', '98589', '98590', '98591', '98592', '98593', '98595', '98596', '98601', '98603', '98610', '98611', '98612', '98613', '98616', '98617', '98619', '98620', '98624', '98625', '98628', '98629', '98631', '98635', '98638', '98639', '98644', '98645', '98648', '98649', '98650', '98651', '98672', '98675', '98812', '98813', '98814', '98815', '98816', '98819', '98821', '98822', '98826', '98827', '98829', '98831', '98833', '98836', '98840', '98843', '98846', '98847', '98849', '98851', '98855', '98856', '98857', '98858', '98862', '98922', '98926', '98930', '98932', '98933', '98935', '98936', '98937', '98938', '98940', '98941', '98943', '98946', '98947', '98948', '98950', '98952', '99003', '99004', '99006', '99008', '99012', '99013', '99023', '99026', '99031', '99033', '99034', '99036', '99039', '99101', '99102', '99103', '99107', '99109', '99110', '99111', '99113', '99114', '99118', '99119', '99125', '99126', '99128', '99133', '99135', '99139', '99141', '99147', '99148', '99150', '99151', '99152', '99156', '99159', '99160', '99161', '99166', '99167', '99169', '99170', '99171', '99173', '99179', '99180', '99181', '99185', '99320', '99321', '99326', '99328', '99329', '99330', '99332', '99341', '99343', '99344', '99345', '99346', '99347', '99348', '99349', '99350', '99357', '99359', '99361', '99371', '99402');
    public static $remote = array('01368', '01380', '02807', '02898', '03597', '04057', '04226', '04413', '04417', '04454', '04487', '04490', '04492', '04495', '04548', '04606', '04746', '04762', '04764', '04783', '04936', '04942', '04970', '04982', '04985', '05076', '05084', '05485', '05840', '05853', '05867', '05901', '06244', '07881', '10910', '12016', '12082', '12131', '12490', '12506', '12588', '12720', '12727', '12752', '12762', '12782', '12786', '12792', '12819', '12836', '12841', '12847', '12856', '12858', '12861', '12862', '12872', '12879', '12915', '12933', '13333', '13353', '13355', '13404', '13621', '13623', '13627', '13633', '13643', '13645', '13649', '13672', '13678', '13740', '13755', '13796', '13804', '13832', '13840', '13846', '13847', '14029', '14035', '14039', '14413', '14461', '14547', '14730', '14745', '14751', '14758', '14766', '14786', '14876', '14887', '14893', '15310', '15362', '15377', '15535', '15538', '15560', '15564', '15758', '15763', '15832', '16021', '16035', '16058', '16220', '16221', '16225', '16312', '16321', '16322', '16675', '16724', '16730', '16746', '16837', '16864', '16871', '16921', '16935', '16936', '16942', '16945', '16950', '17076', '17097', '17211', '17251', '17729', '17768', '17778', '17835', '17885', '18212', '18246', '18449', '18455', '18629', '18816', '18833', '20116', '20118', '20128', '20130', '20140', '20661', '20686', '21542', '21556', '21610', '21626', '21627', '21669', '21781', '21810', '21838', '21867', '22438', '22442', '22446', '22509', '22524', '22538', '22548', '22552', '22558', '22570', '22626', '22729', '22746', '22811', '22830', '22848', '23003', '23014', '23063', '23067', '23079', '23083', '23085', '23123', '23147', '23180', '23313', '23316', '23341', '23347', '23389', '23398', '23407', '23409', '23412', '23414', '23420', '23422', '23429', '23441', '23443', '23480', '23486', '23488', '23872', '23873', '23881', '23884', '23942', '23955', '24076', '24088', '24091', '24120', '24127', '24130', '24131', '24138', '24162', '24171', '24217', '24220', '24221', '24226', '24228', '24239', '24243', '24244', '24245', '24246', '24263', '24265', '24272', '24282', '24325', '24366', '24377', '24411', '24415', '24442', '24460', '24465', '24484', '24487', '24526', '24530', '24533', '24539', '24556', '24565', '24580', '24594', '24602', '24619', '24620', '24622', '24624', '24627', '24628', '24634', '24657', '24658', '24715', '24719', '24726', '24736', '24738', '24811', '24815', '24817', '24827', '24828', '24844', '24848', '24849', '24850', '24866', '24872', '24873', '24884', '24894', '24895', '24918', '24920', '24927', '24934', '24962', '24966', '24974', '24984', '24993', '25009', '25022', '25030', '25043', '25047', '25060', '25063', '25083', '25088', '25111', '25113', '25123', '25125', '25133', '25164', '25180', '25204', '25211', '25234', '25235', '25243', '25252', '25253', '25259', '25261', '25266', '25267', '25268', '25271', '25275', '25286', '25287', '25422', '25431', '25434', '25437', '25441', '25444', '25511', '25512', '25515', '25517', '25521', '25524', '25534', '25544', '25557', '25632', '25654', '25669', '25671', '25682', '25686', '25688', '25692', '25699', '25811', '25820', '25851', '25854', '25864', '25902', '25907', '25908', '25915', '25936', '25938', '25942', '25981', '26039', '26055', '26121', '26141', '26151', '26152', '26160', '26161', '26162', '26167', '26169', '26178', '26203', '26206', '26215', '26217', '26218', '26222', '26224', '26228', '26230', '26234', '26237', '26254', '26264', '26268', '26270', '26273', '26282', '26294', '26296', '26298', '26321', '26325', '26327', '26335', '26338', '26339', '26342', '26343', '26351', '26362', '26372', '26376', '26384', '26410', '26411', '26412', '26421', '26425', '26436', '26437', '26443', '26444', '26447', '26448', '26456', '26562', '26566', '26581', '26582', '26585', '26601', '26610', '26611', '26615', '26617', '26619', '26621', '26627', '26631', '26636', '26638', '26671', '26679', '26680', '26707', '26714', '26716', '26717', '26720', '26722', '26731', '26739', '26755', '26764', '26801', '26804', '26807', '26812', '26814', '26815', '26818', '26823', '26838', '26851', '26852', '26865', '26866', '26884', '27016', '27247', '27259', '27291', '27306', '27314', '27343', '27570', '27586', '27594', '27808', '27861', '27866', '27867', '27875', '27887', '27942', '27985', '28007', '28010', '28074', '28076', '28102', '28137', '28362', '28509', '28556', '28589', '28611', '28615', '28616', '28629', '28631', '28641', '28643', '28647', '28649', '28652', '28653', '28657', '28662', '28664', '28679', '28692', '28693', '28707', '28714', '28749', '28753', '28755', '28756', '28765', '28772', '28773', '28781', '28783', '28784', '28790', '29031', '29041', '29056', '29331', '29346', '29355', '29375', '29426', '29442', '29446', '29447', '29476', '29519', '29543', '29546', '29554', '29563', '29564', '29580', '29590', '29593', '29813', '29822', '29826', '29836', '29845', '29848', '29914', '29922', '29931', '29934', '29943', '30018', '30140', '30150', '30175', '30424', '30428', '30441', '30447', '30449', '30454', '30499', '30522', '30536', '30541', '30546', '30552', '30555', '30559', '30560', '30562', '30580', '30639', '30664', '30665', '30731', '30732', '30811', '30821', '31001', '31002', '31003', '31011', '31038', '31045', '31049', '31060', '31065', '31067', '31077', '31081', '31083', '31084', '31085', '31087', '31094', '31097', '31304', '31327', '31554', '31555', '31556', '31624', '31629', '31717', '31736', '31740', '31745', '31752', '31759', '31760', '31761', '31762', '31764', '31766', '31767', '31769', '31770', '31785', '31797', '31810', '31812', '31821', '32008', '32042', '32061', '32071', '32072', '32087', '32105', '32110', '32133', '32160', '32183', '32185', '32192', '32331', '32335', '32336', '32337', '32341', '32345', '32356', '32357', '32358', '32360', '32361', '32432', '32434', '32437', '32443', '32455', '32537', '32568', '32633', '32634', '32644', '32648', '32654', '32662', '32663', '32681', '32683', '32759', '32775', '33439', '33459', '33476', '33855', '33857', '33862', '33865', '33890', '34137', '34265', '34267', '34739', '34973', '35070', '35136', '35441', '35443', '35447', '35459', '35461', '35464', '35469', '35477', '35553', '35559', '35572', '35581', '35651', '35654', '35677', '35745', '35766', '36003', '36015', '36016', '36017', '36029', '36030', '36031', '36039', '36040', '36046', '36047', '36053', '36061', '36062', '36089', '36091', '36258', '36261', '36266', '36267', '36310', '36317', '36318', '36425', '36435', '36442', '36444', '36480', '36481', '36482', '36513', '36515', '36518', '36524', '36529', '36538', '36539', '36540', '36550', '36553', '36556', '36558', '36579', '36581', '36585', '36723', '36727', '36738', '36740', '36742', '36749', '36751', '36752', '36754', '36756', '36761', '36762', '36767', '36768', '36776', '36785', '36786', '36793', '36858', '36860', '36901', '36908', '36912', '36915', '36919', '36922', '37012', '37058', '37096', '37097', '37140', '37145', '37175', '37365', '37367', '37376', '37382', '37383', '37385', '37387', '37396', '37640', '37680', '37727', '37729', '37731', '37732', '37733', '37753', '37888', '38039', '38042', '38046', '38070', '38077', '38254', '38311', '38453', '38455', '38459', '38471', '38475', '38504', '38554', '38556', '38562', '38569', '38588', '38589', '38602', '38622', '38623', '38634', '38642', '38643', '38647', '38649', '38650', '38659', '38720', '38725', '38745', '38769', '38778', '38820', '38848', '38859', '38873', '38874', '38877', '38902', '38920', '38924', '38925', '38926', '38928', '38929', '38946', '38948', '38950', '38951', '38955', '38959', '38961', '38963', '38964', '39045', '39061', '39067', '39086', '39094', '39095', '39098', '39107', '39108', '39117', '39119', '39121', '39140', '39146', '39152', '39156', '39160', '39166', '39177', '39179', '39192', '39324', '39337', '39338', '39339', '39352', '39354', '39361', '39366', '39422', '39423', '39451', '39455', '39456', '39483', '39573', '39633', '39641', '39645', '39647', '39661', '39663', '39665', '39669', '39745', '39747', '39767', '39776', '39818', '39829', '39836', '40009', '40036', '40046', '40050', '40058', '40075', '40078', '40119', '40140', '40143', '40145', '40152', '40153', '40157', '40170', '40171', '40178', '40374', '40445', '40456', '40460', '40492', '40759', '40763', '40808', '40810', '40813', '40819', '40824', '40826', '40840', '40843', '40849', '40858', '40874', '40913', '40914', '40939', '40940', '40946', '40955', '40964', '40972', '40982', '40983', '40988', '40995', '40997', '41010', '41040', '41083', '41098', '41124', '41128', '41132', '41135', '41149', '41159', '41160', '41164', '41181', '41201', '41219', '41226', '41232', '41257', '41317', '41339', '41348', '41351', '41352', '41366', '41368', '41385', '41413', '41425', '41464', '41472', '41534', '41538', '41561', '41632', '41712', '41713', '41714', '41721', '41731', '41743', '41745', '41747', '41751', '41754', '41759', '41762', '41763', '41766', '41775', '41776', '41777', '41804', '41819', '41821', '41834', '41836', '41847', '42022', '42031', '42032', '42033', '42083', '42085', '42124', '42131', '42133', '42166', '42167', '42201', '42216', '42221', '42338', '42349', '42361', '42364', '42444', '42558', '42634', '42720', '42724', '42731', '42732', '42735', '42740', '43048', '43156', '43158', '43346', '43349', '43711', '43738', '43752', '43754', '43757', '43787', '43805', '43843', '43902', '43928', '43976', '43983', '44033', '44453', '44651', '45105', '45132', '45618', '45624', '45633', '45642', '45643', '45658', '45685', '45687', '45688', '45713', '45739', '45745', '45746', '45783', '45815', '45867', '46146', '46150', '46182', '46508', '46572', '46968', '47038', '47108', '47118', '47135', '47145', '47163', '47175', '47224', '47225', '47263', '47272', '47370', '47435', '47437', '47574', '47617', '47837', '47917', '47986', '48004', '48143', '48816', '48870', '48874', '49027', '49257', '49611', '49666', '49673', '49711', '49717', '49728', '49736', '49774', '49797', '49819', '49838', '49865', '49873', '49895', '49919', '49942', '49950', '49958', '49969', '49970', '50043', '50052', '50067', '50068', '50074', '50108', '50117', '50155', '50254', '50259', '50264', '50275', '50551', '50607', '50650', '50654', '50831', '50836', '50837', '50843', '50845', '50848', '50857', '50858', '50859', '50861', '50862', '51011', '51033', '51244', '51344', '51432', '51447', '51545', '51631', '52044', '52048', '52050', '52071', '52072', '52074', '52075', '52079', '52156', '52212', '52221', '52307', '52569', '52581', '52583', '52619', '52644', '52701', '53031', '53195', '53501', '53535', '53542', '53584', '53806', '53827', '53927', '53942', '53953', '54012', '54104', '54240', '54422', '54437', '54459', '54462', '54517', '54524', '54540', '54556', '54564', '54565', '54626', '54637', '54640', '54641', '54645', '54655', '54657', '54813', '54820', '54827', '54828', '54830', '54835', '54854', '54859', '54862', '54865', '54890', '54896', '54968', '55002', '55010', '55067', '55382', '55602', '55606', '55607', '55701', '55703', '55724', '55732', '55752', '55763', '55765', '55766', '55771', '55772', '55785', '56020', '56136', '56142', '56147', '56338', '56357', '56517', '56519', '56522', '56553', '56569', '56575', '56581', '56583', '56626', '56628', '56629', '56639', '56657', '56661', '56666', '56669', '56673', '56681', '56685', '56686', '56688', '56711', '56720', '56722', '56729', '56741', '56755', '56756', '56759', '57052', '57061', '57067', '57217', '57232', '57235', '57236', '57242', '57255', '57257', '57258', '57260', '57265', '57270', '57317', '57321', '57329', '57330', '57335', '57337', '57340', '57341', '57344', '57345', '57356', '57358', '57363', '57370', '57371', '57373', '57375', '57379', '57381', '57382', '57383', '57385', '57420', '57424', '57434', '57435', '57437', '57440', '57448', '57449', '57452', '57455', '57456', '57457', '57460', '57466', '57467', '57470', '57471', '57473', '57474', '57475', '57481', '57520', '57521', '57523', '57529', '57531', '57534', '57536', '57537', '57538', '57540', '57541', '57543', '57544', '57547', '57551', '57552', '57553', '57555', '57560', '57562', '57563', '57566', '57567', '57569', '57571', '57574', '57577', '57579', '57584', '57585', '57620', '57621', '57622', '57623', '57625', '57630', '57633', '57634', '57636', '57638', '57639', '57641', '57642', '57644', '57645', '57646', '57648', '57649', '57650', '57651', '57652', '57657', '57658', '57659', '57660', '57661', '57714', '57716', '57720', '57722', '57724', '57725', '57735', '57737', '57738', '57748', '57755', '57756', '57758', '57760', '57762', '57763', '57764', '57767', '57772', '57775', '57778', '57780', '57787', '57788', '57791', '57792', '57794', '58002', '58009', '58013', '58016', '58017', '58030', '58039', '58056', '58057', '58058', '58061', '58062', '58063', '58065', '58069', '58212', '58216', '58219', '58223', '58229', '58233', '58236', '58239', '58241', '58244', '58254', '58260', '58277', '58281', '58321', '58323', '58331', '58338', '58339', '58343', '58351', '58353', '58357', '58361', '58365', '58372', '58374', '58377', '58381', '58382', '58385', '58386', '58413', '58416', '58418', '58422', '58423', '58424', '58426', '58429', '58430', '58431', '58438', '58439', '58440', '58441', '58443', '58444', '58445', '58451', '58454', '58456', '58460', '58466', '58467', '58472', '58475', '58476', '58477', '58478', '58480', '58481', '58483', '58486', '58487', '58488', '58490', '58494', '58496', '58497', '58520', '58521', '58524', '58528', '58529', '58530', '58531', '58532', '58533', '58535', '58541', '58542', '58560', '58562', '58563', '58564', '58566', '58568', '58569', '58570', '58572', '58580', '58581', '58620', '58625', '58627', '58630', '58632', '58634', '58636', '58641', '58642', '58643', '58644', '58646', '58647', '58649', '58650', '58651', '58653', '58654', '58656', '58712', '58716', '58718', '58723', '58734', '58735', '58736', '58737', '58741', '58755', '58760', '58762', '58769', '58771', '58772', '58773', '58776', '58778', '58779', '58783', '58787', '58788', '58789', '58792', '58794', '58795', '58830', '58833', '58835', '58838', '58844', '58845', '58847', '58852', '58856', '59002', '59003', '59008', '59010', '59011', '59015', '59016', '59019', '59022', '59024', '59025', '59028', '59031', '59032', '59033', '59038', '59039', '59041', '59043', '59046', '59050', '59052', '59053', '59054', '59055', '59057', '59058', '59059', '59061', '59062', '59064', '59066', '59067', '59069', '59070', '59071', '59072', '59073', '59074', '59075', '59076', '59077', '59078', '59081', '59084', '59085', '59086', '59087', '59211', '59213', '59214', '59215', '59219', '59222', '59225', '59226', '59240', '59241', '59242', '59243', '59244', '59247', '59250', '59252', '59253', '59256', '59257', '59258', '59259', '59260', '59261', '59262', '59274', '59275', '59276', '59311', '59312', '59313', '59314', '59315', '59316', '59317', '59318', '59319', '59322', '59324', '59326', '59327', '59332', '59336', '59337', '59339', '59341', '59343', '59344', '59345', '59347', '59349', '59351', '59353', '59354', '59410', '59411', '59416', '59418', '59419', '59420', '59421', '59424', '59430', '59432', '59435', '59440', '59441', '59444', '59446', '59447', '59448', '59450', '59451', '59453', '59454', '59456', '59460', '59462', '59463', '59464', '59466', '59467', '59469', '59471', '59479', '59480', '59483', '59484', '59489', '59520', '59523', '59524', '59525', '59527', '59528', '59529', '59530', '59531', '59532', '59535', '59537', '59538', '59540', '59542', '59545', '59546', '59547', '59633', '59641', '59642', '59647', '59648', '59720', '59721', '59724', '59727', '59728', '59731', '59732', '59733', '59736', '59739', '59745', '59750', '59758', '59762', '59820', '59823', '59826', '59827', '59829', '59832', '59837', '59844', '59845', '59848', '59853', '59854', '59859', '59863', '59866', '59867', '59874', '59913', '59916', '59920', '59925', '59928', '59930', '59933', '59934', '60944', '61311', '61328', '61541', '61543', '62001', '62037', '62053', '62070', '62098', '62252', '62262', '62280', '62323', '62329', '62344', '62355', '62443', '62464', '62538', '62553', '62833', '62843', '62852', '62855', '62861', '62909', '62910', '62920', '62928', '62931', '62940', '62947', '62962', '63030', '63047', '63071', '63342', '63363', '63370', '63378', '63386', '63387', '63388', '63433', '63439', '63446', '63450', '63544', '63557', '63560', '63566', '63620', '63625', '63629', '63630', '63633', '63636', '63638', '63655', '63656', '63662', '63665', '63666', '63674', '63675', '63738', '63751', '63763', '63764', '63781', '63784', '63787', '63833', '63847', '63850', '63878', '63882', '63931', '63936', '63937', '63941', '63942', '63944', '63950', '63951', '63952', '63954', '63956', '63957', '63961', '63964', '64066', '64402', '64421', '64437', '64441', '64456', '64457', '64481', '64487', '64491', '64498', '64499', '64646', '64667', '64672', '64740', '64745', '64756', '64788', '64866', '64868', '65011', '65014', '65017', '65050', '65058', '65061', '65062', '65064', '65067', '65068', '65077', '65078', '65237', '65244', '65283', '65285', '65323', '65339', '65436', '65438', '65439', '65440', '65441', '65443', '65449', '65452', '65461', '65463', '65479', '65486', '65501', '65529', '65532', '65550', '65556', '65564', '65565', '65586', '65590', '65603', '65625', '65632', '65634', '65635', '65637', '65638', '65646', '65647', '65655', '65657', '65663', '65676', '65685', '65690', '65692', '65702', '65704', '65707', '65713', '65717', '65723', '65727', '65734', '65735', '65744', '65745', '65747', '65761', '65762', '65768', '65769', '65772', '65778', '65779', '65783', '65784', '65789', '66014', '66033', '66039', '66042', '66093', '66401', '66403', '66404', '66406', '66412', '66518', '66527', '66532', '66540', '66541', '66544', '66710', '66714', '66734', '66751', '66754', '66755', '66777', '66783', '66833', '66840', '66850', '66853', '66857', '66860', '66863', '66866', '66870', '66933', '66942', '66943', '66946', '66948', '66958', '66959', '66960', '66963', '66970', '67009', '67018', '67023', '67028', '67036', '67049', '67057', '67061', '67102', '67109', '67112', '67122', '67137', '67142', '67150', '67155', '67344', '67345', '67355', '67360', '67363', '67423', '67444', '67447', '67452', '67458', '67478', '67481', '67484', '67511', '67513', '67515', '67518', '67519', '67559', '67568', '67573', '67581', '67623', '67628', '67631', '67634', '67635', '67643', '67645', '67646', '67650', '67653', '67654', '67656', '67658', '67664', '67673', '67675', '67736', '67741', '67743', '67744', '67761', '67762', '67764', '67831', '67836', '67839', '67840', '67841', '67842', '67844', '67850', '67853', '67857', '67862', '67863', '67868', '67870', '67879', '67953', '67954', '68019', '68029', '68042', '68055', '68330', '68374', '68381', '68429', '68444', '68621', '68631', '68637', '68659', '68711', '68714', '68719', '68726', '68735', '68742', '68746', '68749', '68751', '68753', '68755', '68759', '68760', '68774', '68778', '68813', '68821', '68823', '68828', '68833', '68834', '68837', '68838', '68855', '68859', '68860', '68871', '68879', '68926', '68934', '68936', '68943', '68946', '68948', '68969', '68972', '68977', '69020', '69023', '69024', '69026', '69027', '69030', '69032', '69034', '69037', '69038', '69039', '69040', '69041', '69042', '69044', '69045', '69046', '69121', '69123', '69125', '69127', '69128', '69131', '69132', '69135', '69141', '69142', '69144', '69147', '69148', '69149', '69152', '69156', '69157', '69161', '69163', '69166', '69167', '69168', '69170', '69201', '69210', '69211', '69212', '69214', '69216', '69218', '69219', '69220', '69221', '69331', '69333', '69335', '69340', '69343', '69345', '69346', '69347', '69350', '69351', '69354', '69360', '69366', '69367', '70038', '70081', '70426', '70441', '70450', '70463', '70519', '70522', '70532', '70540', '70569', '70580', '70630', '70631', '70632', '70637', '70643', '70651', '70654', '70656', '70747', '71002', '71003', '71016', '71021', '71031', '71034', '71048', '71064', '71067', '71070', '71080', '71223', '71242', '71249', '71250', '71253', '71260', '71264', '71279', '71284', '71320', '71340', '71343', '71345', '71375', '71401', '71403', '71406', '71414', '71416', '71425', '71428', '71434', '71450', '71452', '71455', '71462', '71468', '71472', '71473', '71475', '71631', '71651', '71652', '71659', '71661', '71662', '71676', '71677', '71678', '71721', '71722', '71726', '71827', '71828', '71831', '71833', '71834', '71836', '71839', '71841', '71845', '71846', '71853', '71861', '71864', '71920', '71935', '71962', '71969', '71973', '72001', '72003', '72004', '72005', '72013', '72014', '72024', '72026', '72027', '72028', '72030', '72031', '72037', '72038', '72043', '72048', '72051', '72052', '72066', '72069', '72072', '72073', '72075', '72080', '72108', '72123', '72134', '72140', '72141', '72152', '72153', '72165', '72166', '72167', '72168', '72320', '72328', '72332', '72353', '72359', '72367', '72368', '72369', '72379', '72394', '72410', '72421', '72424', '72425', '72428', '72431', '72435', '72449', '72453', '72459', '72465', '72482', '72515', '72517', '72519', '72520', '72525', '72528', '72530', '72531', '72533', '72538', '72542', '72546', '72560', '72561', '72566', '72569', '72578', '72583', '72584', '72585', '72587', '72617', '72624', '72628', '72639', '72640', '72641', '72645', '72648', '72650', '72655', '72663', '72670', '72675', '72677', '72680', '72682', '72683', '72685', '72686', '72687', '72717', '72729', '72733', '72742', '72752', '72760', '72776', '72820', '72826', '72839', '72841', '72851', '72852', '72854', '72856', '72857', '72860', '72926', '72934', '72948', '72955', '72959', '73001', '73014', '73040', '73041', '73043', '73056', '73058', '73067', '73079', '73082', '73425', '73430', '73433', '73434', '73435', '73437', '73450', '73456', '73461', '73488', '73544', '73559', '73561', '73571', '73625', '73627', '73628', '73638', '73642', '73658', '73659', '73660', '73667', '73673', '73719', '73722', '73734', '73744', '73746', '73753', '73755', '73761', '73763', '73768', '73770', '73835', '73842', '73843', '73844', '73848', '73851', '73855', '73931', '73933', '73937', '73939', '73944', '73946', '73947', '73949', '74001', '74002', '74028', '74042', '74043', '74046', '74056', '74071', '74072', '74085', '74360', '74364', '74369', '74370', '74440', '74442', '74457', '74461', '74468', '74521', '74523', '74525', '74528', '74529', '74531', '74536', '74538', '74540', '74543', '74549', '74552', '74555', '74556', '74557', '74558', '74559', '74560', '74562', '74569', '74571', '74572', '74574', '74576', '74577', '74650', '74652', '74721', '74724', '74735', '74737', '74738', '74747', '74748', '74752', '74753', '74754', '74755', '74756', '74759', '74760', '74761', '74766', '74826', '74839', '74842', '74845', '74867', '74871', '74878', '74930', '74931', '74935', '74937', '74939', '74942', '74943', '74949', '74957', '74962', '74963', '75105', '75109', '75153', '75157', '75425', '75434', '75441', '75450', '75458', '75469', '75477', '75492', '75554', '75555', '75558', '75559', '75560', '75562', '75565', '75666', '75669', '75760', '75764', '75772', '75779', '75782', '75785', '75832', '75833', '75838', '75847', '75850', '75851', '75852', '75862', '75865', '75926', '75928', '75929', '75930', '75932', '75933', '75938', '75942', '75944', '75946', '75948', '75959', '75960', '75973', '75974', '75977', '75980', '76068', '76241', '76246', '76251', '76255', '76261', '76263', '76267', '76352', '76363', '76388', '76429', '76436', '76439', '76444', '76449', '76452', '76453', '76459', '76461', '76463', '76464', '76466', '76467', '76468', '76472', '76474', '76481', '76485', '76518', '76523', '76525', '76531', '76538', '76565', '76566', '76573', '76626', '76644', '76684', '76686', '76803', '76820', '76824', '76827', '76828', '76831', '76832', '76836', '76841', '76845', '76848', '76849', '76852', '76853', '76854', '76856', '76858', '76859', '76869', '76870', '76871', '76872', '76873', '76878', '76880', '76882', '76883', '76884', '76885', '76886', '76887', '76888', '76930', '76939', '76949', '76950', '76953', '76955', '77326', '77332', '77334', '77344', '77350', '77368', '77404', '77415', '77428', '77430', '77431', '77432', '77436', '77444', '77452', '77460', '77468', '77597', '77612', '77616', '77623', '77624', '77661', '77664', '77830', '77831', '77838', '77850', '77852', '77853', '77863', '77865', '77875', '77876', '77878', '77882', '77961', '77969', '77970', '77973', '77987', '77989', '77993', '78001', '78005', '78008', '78011', '78014', '78019', '78021', '78024', '78027', '78054', '78058', '78067', '78072', '78074', '78104', '78344', '78349', '78350', '78353', '78360', '78369', '78371', '78376', '78536', '78547', '78563', '78564', '78588', '78591', '78604', '78607', '78608', '78618', '78632', '78636', '78643', '78663', '78675', '78677', '78828', '78829', '78830', '78833', '78836', '78837', '78851', '78860', '78871', '78873', '78880', '78884', '78932', '78933', '78935', '78946', '78950', '78952', '78953', '78956', '78959', '78960', '79002', '79010', '79011', '79014', '79018', '79021', '79034', '79040', '79046', '79051', '79053', '79056', '79057', '79058', '79062', '79066', '79078', '79080', '79083', '79087', '79091', '79093', '79094', '79220', '79221', '79223', '79229', '79230', '79231', '79234', '79239', '79243', '79247', '79248', '79251', '79259', '79324', '79330', '79338', '79344', '79345', '79351', '79353', '79376', '79380', '79503', '79518', '79519', '79537', '79538', '79540', '79548', '79566', '79713', '79721', '79730', '79734', '79739', '79741', '79748', '79754', '79755', '79759', '79770', '79783', '79831', '79837', '79842', '79846', '79847', '79854', '80101', '80103', '80423', '80430', '80432', '80434', '80440', '80447', '80448', '80449', '80456', '80459', '80468', '80471', '80479', '80480', '80510', '80515', '80536', '80541', '80643', '80646', '80649', '80727', '80728', '80729', '80731', '80732', '80734', '80735', '80736', '80740', '80741', '80742', '80743', '80744', '80745', '80747', '80749', '80754', '80755', '80801', '80802', '80804', '80805', '80812', '80816', '80818', '80820', '80823', '80824', '80825', '80826', '80827', '80830', '80832', '80833', '80834', '80861', '80862', '80864', '80926', '80928', '81020', '81021', '81024', '81025', '81027', '81029', '81030', '81033', '81034', '81040', '81041', '81043', '81044', '81045', '81046', '81049', '81057', '81059', '81064', '81069', '81071', '81077', '81081', '81084', '81087', '81089', '81090', '81091', '81124', '81126', '81127', '81129', '81135', '81138', '81143', '81149', '81152', '81155', '81220', '81223', '81228', '81231', '81232', '81233', '81243', '81252', '81320', '81324', '81325', '81331', '81414', '81431', '81433', '81434', '81522', '81523', '81610', '81624', '81638', '81640', '81650', '82051', '82052', '82053', '82055', '82058', '82061', '82073', '82081', '82083', '82084', '82190', '82210', '82213', '82217', '82218', '82219', '82222', '82223', '82224', '82227', '82229', '82242', '82243', '82310', '82322', '82329', '82331', '82334', '82422', '82428', '82433', '82512', '82514', '82620', '82630', '82635', '82638', '82639', '82642', '82646', '82701', '82710', '82712', '82714', '82715', '82720', '82721', '82723', '82725', '82727', '82729', '82730', '82731', '82831', '82835', '82837', '82840', '82844', '82931', '82932', '82933', '82934', '82936', '82938', '82941', '82945', '83013', '83111', '83120', '83121', '83122', '83212', '83213', '83215', '83220', '83227', '83229', '83230', '83233', '83235', '83243', '83244', '83253', '83278', '83281', '83283', '83285', '83302', '83342', '83425', '83446', '83447', '83462', '83463', '83464', '83465', '83466', '83469', '83523', '83524', '83525', '83526', '83531', '83533', '83537', '83539', '83541', '83542', '83543', '83546', '83547', '83554', '83604', '83610', '83612', '83624', '83635', '83636', '83650', '83657', '83666', '83670', '83677', '83802', '83808', '83824', '83826', '83827', '83853', '83866', '83870', '84023', '84024', '84027', '84034', '84046', '84052', '84064', '84304', '84313', '84329', '84336', '84510', '84515', '84516', '84520', '84529', '84531', '84534', '84536', '84539', '84540', '84626', '84635', '84714', '84716', '84726', '84728', '84731', '84733', '84735', '84736', '84743', '84753', '84756', '84773', '85192', '85264', '85322', '85328', '85332', '85333', '85341', '85342', '85347', '85354', '85358', '85360', '85362', '85502', '85530', '85547', '85554', '85609', '85610', '85611', '85619', '85625', '85627', '85632', '85633', '85634', '85644', '85736', '85911', '85920', '85922', '85924', '85927', '85928', '85936', '85937', '85940', '86016', '86020', '86024', '86030', '86031', '86033', '86034', '86035', '86038', '86043', '86044', '86053', '86054', '86320', '86321', '86332', '86337', '86338', '86343', '86411', '86432', '86437', '86438', '86441', '86443', '86444', '86445', '86446', '86502', '86505', '86506', '86507', '86508', '86511', '86512', '86520', '86538', '86544', '86545', '86547', '86556', '87005', '87011', '87012', '87013', '87014', '87016', '87018', '87026', '87027', '87028', '87029', '87032', '87036', '87037', '87040', '87045', '87046', '87049', '87056', '87061', '87063', '87070', '87302', '87305', '87310', '87311', '87312', '87313', '87315', '87317', '87320', '87321', '87323', '87325', '87326', '87357', '87364', '87375', '87412', '87419', '87455', '87461', '87510', '87512', '87513', '87515', '87517', '87518', '87520', '87521', '87524', '87528', '87530', '87538', '87539', '87543', '87549', '87551', '87556', '87557', '87562', '87564', '87565', '87573', '87577', '87579', '87580', '87581', '87710', '87712', '87713', '87714', '87718', '87728', '87730', '87731', '87733', '87734', '87735', '87736', '87742', '87743', '87745', '87746', '87749', '87750', '87752', '87753', '87820', '87821', '87824', '87827', '87829', '87832', '87933', '87939', '87943', '88009', '88020', '88022', '88025', '88026', '88031', '88034', '88036', '88038', '88041', '88042', '88045', '88049', '88051', '88053', '88055', '88058', '88062', '88112', '88113', '88114', '88115', '88116', '88118', '88119', '88120', '88121', '88123', '88125', '88126', '88132', '88133', '88134', '88136', '88213', '88250', '88254', '88262', '88301', '88318', '88321', '88323', '88339', '88340', '88342', '88343', '88344', '88347', '88354', '88355', '88410', '88411', '88414', '88415', '88417', '88418', '88419', '88421', '88422', '88424', '88427', '88430', '88431', '88434', '88436', '88437', '88439', '89001', '89006', '89008', '89010', '89013', '89017', '89022', '89024', '89036', '89039', '89041', '89043', '89045', '89046', '89047', '89049', '89124', '89161', '89310', '89311', '89314', '89316', '89317', '89404', '89405', '89407', '89409', '89412', '89414', '89418', '89420', '89421', '89424', '89425', '89426', '89427', '89438', '89446', '89510', '89821', '89823', '89830', '89831', '89833', '89834', '90264', '91905', '91962', '91963', '92066', '92070', '92086', '92222', '92226', '92257', '92266', '92267', '92280', '92304', '92305', '92309', '92323', '92328', '92329', '92332', '92347', '92364', '92365', '92368', '92384', '92389', '92536', '92539', '92561', '92676', '93016', '93024', '93064', '93207', '93208', '93226', '93251', '93252', '93254', '93255', '93260', '93271', '93283', '93287', '93426', '93429', '93435', '93450', '93453', '93502', '93504', '93517', '93518', '93519', '93530', '93541', '93544', '93554', '93558', '93563', '93592', '93601', '93602', '93605', '93608', '93621', '93623', '93624', '93627', '93641', '93643', '93651', '93653', '93664', '93665', '93667', '93675', '93920', '93932', '93954', '93962', '94020', '94060', '94074', '94512', '94567', '94576', '94922', '94940', '94972', '95026', '95043', '95140', '95224', '95225', '95226', '95227', '95229', '95230', '95232', '95236', '95245', '95246', '95251', '95253', '95254', '95255', '95257', '95310', '95311', '95325', '95333', '95335', '95345', '95346', '95364', '95369', '95375', '95383', '95385', '95389', '95412', '95417', '95419', '95421', '95429', '95430', '95441', '95450', '95454', '95459', '95465', '95466', '95468', '95471', '95480', '95488', '95494', '95511', '95514', '95526', '95528', '95532', '95538', '95545', '95549', '95550', '95552', '95554', '95559', '95568', '95587', '95595', '95601', '95634', '95636', '95641', '95645', '95651', '95679', '95680', '95684', '95690', '95698', '95701', '95714', '95715', '95717', '95720', '95721', '95735', '95910', '95914', '95915', '95916', '95919', '95920', '95922', '95923', '95925', '95939', '95942', '95944', '95960', '95978', '95981', '95983', '95984', '95986', '96010', '96011', '96014', '96017', '96023', '96024', '96029', '96031', '96034', '96039', '96041', '96044', '96046', '96047', '96048', '96050', '96051', '96056', '96058', '96059', '96061', '96064', '96065', '96070', '96075', '96076', '96085', '96086', '96091', '96104', '96105', '96112', '96115', '96116', '96117', '96119', '96123', '96124', '96127', '96132', '96136', '97001', '97010', '97028', '97029', '97033', '97044', '97107', '97118', '97125', '97131', '97136', '97143', '97145', '97324', '97326', '97329', '97336', '97342', '97343', '97344', '97345', '97346', '97357', '97390', '97406', '97429', '97447', '97451', '97476', '97484', '97493', '97494', '97534', '97541', '97543', '97621', '97622', '97623', '97624', '97625', '97626', '97630', '97634', '97638', '97639', '97710', '97711', '97712', '97721', '97722', '97732', '97733', '97735', '97736', '97750', '97751', '97752', '97758', '97761', '97817', '97819', '97823', '97825', '97830', '97837', '97842', '97848', '97856', '97859', '97861', '97864', '97874', '97880', '97884', '97904', '97905', '97906', '97908', '97909', '97910', '97911', '97917', '97920', '98013', '98224', '98259', '98280', '98283', '98287', '98293', '98326', '98330', '98381', '98526', '98537', '98538', '98539', '98547', '98552', '98559', '98560', '98562', '98566', '98575', '98583', '98602', '98614', '98621', '98637', '98640', '98641', '98643', '98647', '98670', '98673', '98811', '98817', '98830', '98832', '98834', '98845', '98852', '98853', '98859', '98860', '98920', '98921', '98925', '98929', '99009', '99015', '99017', '99018', '99020', '99029', '99030', '99032', '99040', '99105', '99115', '99116', '99117', '99122', '99123', '99124', '99127', '99129', '99130', '99131', '99134', '99136', '99137', '99138', '99140', '99143', '99144', '99146', '99149', '99153', '99154', '99155', '99157', '99158', '99174', '99176', '99322', '99333', '99335', '99356', '99360', '99401');
}
class C_Non_HTTPS_Notice
{
    /** @var C_Non_HTTPS_Notice $_instance */
    static $_instance = NULL;
    /**
     * @return string
     */
    function get_css_class()
    {
        return 'notice notice-warning';
    }
    /**
     * @return bool
     */
    function is_renderable()
    {
        if (!C_NextGen_Admin_Page_Manager::is_requested()) {
            return FALSE;
        }
        // This notification should only display on ecommerce pages
        // TODO: consolidate this into a cleaner method than manual listing every page
        if ((empty($_GET['page']) || !in_array($_GET['page'], array('ngg_ecommerce_options', 'ngg-ecommerce-instructions-page'))) && (empty($_GET['post_type']) || !in_array($_GET['post_type'], array('ngg_pricelist', 'ngg_order', 'ngg_coupon', 'nextgen_proof')))) {
            return FALSE;
        }
        if (is_ssl()) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    /**
     * @return string
     */
    function render()
    {
        return __('NextGen has detected that your site does not have HTTPS enabled. While your site will function without HTTPS it is not recommended. HTTPS will improve your cart security, SEO, search results, and will remove the "insecure" marker that Google Chrome displays for sites without HTTPS.', 'nextgen-gallery-pro');
    }
    /**
     * @return C_Non_HTTPS_Notice
     */
    static function get_instance()
    {
        if (!self::$_instance) {
            $klass = get_class();
            self::$_instance = new $klass();
        }
        return self::$_instance;
    }
}
/**
 * @mixin Mixin_Order_Mapper
 */
class C_Order_Mapper extends C_CustomPost_DataMapper_Driver
{
    public static $_instances = array();
    /**
     * @param bool|string $context
     * @return C_Order_Mapper
     */
    public static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    function define($context = FALSE, $object_name = 'ngg_order')
    {
        // Add the object name to the context of the object as well
        // This allows us to adapt the driver itself, if required
        if (!is_array($context)) {
            $context = array($context);
        }
        array_push($context, $object_name);
        parent::define($object_name, $context);
        $this->add_mixin('Mixin_Order_Mapper');
        $this->set_model_factory_method($object_name);
        // Define columns/properties
        $this->define_column('ID', 'BIGINT', 0);
        $this->define_column('email', 'VARCHAR(255)');
        $this->define_column('customer_name', 'VARCHAR(255');
        $this->define_column('phone', 'VARCHAR(255)');
        $this->define_column('total_amount', 'DECIMAL', 0.0);
        $this->define_column('payment_gateway', 'VARCHAR(255)');
        $this->define_column('shipping_street_address', 'VARCHAR(255)');
        $this->define_column('shipping_address_line', 'VARCHAR(255)');
        $this->define_column('shipping_city', 'VARCHAR(255)');
        $this->define_column('shipping_state', 'VARCHAR(255)');
        $this->define_column('shipping_zip', 'VARCHAR(255)');
        $this->define_column('shipping_country', 'VARCHAR(255)');
        $this->define_column('shipping_phone', 'VARCHAR(255)');
        $this->define_column('cart', 'TEXT');
        $this->define_column('hash', 'VARCHAR(255)');
        $this->define_column('gateway_admin_note', 'VARCHAR(255)');
        $this->define_column('has_sent_email_receipt', 'BOOLEAN', FALSE);
        $this->define_column('has_sent_email_notification', 'BOOLEAN', FALSE);
        $this->define_column('aws_order_id', 'VARCHAR(255)');
        $this->add_serialized_column('cart');
    }
    function initialize($context = FALSE)
    {
        parent::initialize('ngg_order');
    }
    function find_by_hash($hash, $model = FALSE)
    {
        $results = $this->select()->where(array("hash = %s", $hash))->run_query(NULL, $model);
        return array_pop($results);
    }
}
class Mixin_Order_Mapper extends Mixin
{
    function _save_entity($entity)
    {
        if (is_string($this->cart)) {
            $this->cart = json_decode($this->cart);
        }
        if (is_object($this->cart)) {
            $this->cart->prepare_for_persistence();
        }
        // Create a unique hash
        if (!property_exists($entity, 'hash') or !$entity->hash) {
            $entity->hash = md5(time() . $entity->email . json_encode($this->cart));
        }
        $retval = $this->call_parent('_save_entity', $entity);
        do_action('ngg_order_saved', $retval, $entity);
        return $retval;
    }
    /**
     * Uses the title attribute as the post title
     * @param stdClass $entity
     * @return string
     */
    function get_post_title($entity)
    {
        return $entity->customer_name;
    }
    function set_defaults($entity)
    {
        // Pricelists should be published by default
        $entity->post_status = 'publish';
        // TODO: This should be part of the datamapper actually
        $entity->post_title = $this->get_post_title($entity);
    }
}
/**
 * @property C_Pricelist $object
 */
class C_Pricelist extends C_DataMapper_Model
{
    var $_mapper_interface = 'I_Pricelist_Mapper';
    function define($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        parent::define($mapper, $properties, $context);
        $this->implement('I_Pricelist');
    }
    /**
     * Initializes a display type with properties
     *
     * @param FALSE|C_Display_Type_Mapper $mapper
     * @param array|stdClass|C_Display_Type $properties
     * @param FALSE|string|array $context
     */
    function initialize($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        // If no mapper was specified, then get the mapper
        if (!$mapper) {
            $mapper = $this->get_registry()->get_utility($this->_mapper_interface);
        }
        // Construct the model
        parent::initialize($mapper, $properties);
    }
    /**
     * Gets all items from all sources for the pricelist, optionally filtered by an image
     *
     * @param null $image
     * @return array
     */
    function get_items($image = NULL, $model = FALSE)
    {
        $retval = array();
        $manager = C_Pricelist_Category_Manager::get_instance();
        foreach ($manager->get_ids() as $id) {
            $category_items = $this->get_category_items($id, $model);
            $retval = array_merge($retval, $this->object->_filter_pricelist_items($image, $category_items));
        }
        return $retval;
    }
    /**
     * Filter the list of pricelist items for the particular item
     */
    function _filter_pricelist_items($image, $items)
    {
        $retval = array();
        $source_manager = C_Pricelist_Source_Manager::get_instance();
        foreach ($items as $item_id => $item) {
            // If it is a lab item ensure that we have a license and that the image meets the minimum size requirements
            if ($source_manager->get($item->source, 'lab_fulfilled')) {
                if (M_Licensing::is_valid_license() && (!$image || M_NextGen_Pro_Ecommerce::does_item_meet_minimum_requirements($item, $image))) {
                    $retval[$item_id] = $item;
                }
            } else {
                if (in_array($item->source, array(NGG_PRO_MANUAL_PRICELIST_SOURCE, NGG_PRO_DIGITAL_DOWNLOADS_SOURCE))) {
                    $retval[$item_id] = $item;
                }
            }
        }
        return apply_filters('ngg_pro_pricelist_items', $retval, $image);
    }
    function delete_items($ids = array())
    {
        $this->get_mapper()->destroy_items($this->id(), $ids);
    }
    function destroy_items($ids = array())
    {
        return $this->delete_items($ids);
    }
    function get_ngg_manual_pricelist_items($image)
    {
        return $this->get_manual_items($image);
    }
    function get_ngg_digital_downloads_items($image)
    {
        return $this->get_digital_downloads($image);
    }
    /**
     * Gets all manual items of the pricelist
     * @param null $image
     * @return mixed
     */
    function get_manual_items($image = NULL)
    {
        $mapper = C_Pricelist_Item_Mapper::get_instance();
        $conditions = array(array("pricelist_id = %d", $this->object->id()), array("source IN %s", array(NGG_PRO_MANUAL_PRICELIST_SOURCE)));
        // Omit placeholder items that were incorrectly saved
        $retval = array();
        $items = $mapper->select()->where($conditions)->order_by('ID', 'ASC')->run_query();
        foreach ($items as $item) {
            if (empty($item->title) && empty($item->price)) {
                continue;
            }
            $retval[] = $item;
        }
        return $retval;
    }
    function get_category_items($category, $model = FALSE)
    {
        $mapper = C_Pricelist_Item_Mapper::get_instance();
        $items = $mapper->get_category_items($this->object->id(), $category, $model);
        return $items;
    }
    /**
     * Gets all digital downloads for the pricelist
     * @param null $image_id
     * @return mixed
     */
    function get_digital_downloads($image_id = NULL)
    {
        // Find digital download items
        $mapper = C_Pricelist_Item_Mapper::get_instance();
        $items = $mapper->get_category_items($this->object->id(), NGG_PRO_ECOMMERCE_CATEGORY_DIGITAL_DOWNLOADS);
        // Filter by image resolutions
        if ($image_id) {
            $modified = $this->add_digital_downloads_resolutions($image_id, $items);
            if (!empty($modified)) {
                $items = $modified;
            }
        }
        return $items;
    }
    function add_digital_downloads_resolutions($image_id, $items)
    {
        $retval = array();
        $image = is_object($image_id) ? $image_id : C_Image_Mapper::get_instance()->find($image_id);
        if ($image) {
            $storage = C_Gallery_Storage::get_instance();
            foreach ($items as $item) {
                $source_width = $image->meta_data['width'];
                $source_height = $image->meta_data['height'];
                // the downloads themselves come from the backup as source so if possible only filter images
                // whose backup file doesn't have sufficient dimensions
                $backup_abspath = $storage->get_backup_abspath($image);
                if (@file_exists($backup_abspath)) {
                    $dimensions = @getimagesize($backup_abspath);
                    $source_width = $dimensions[0];
                    $source_height = $dimensions[1];
                }
                if (isset($item->resolution) && $item->resolution >= 0 && ($source_height >= $item->resolution or $source_width >= $item->resolution)) {
                    $retval[] = $item;
                }
            }
        }
        return $retval;
    }
    function destroy()
    {
        if (parent::destroy()) {
            return $this->destroy_items();
        }
        return FALSE;
    }
    function validate()
    {
        $this->validates_presence_of('title');
    }
    function get_default_markup()
    {
        $this->get_mapper()->get_default_markup_for_pricelist($this->ID);
    }
}
class C_Pricelist_Category_Manager
{
    public static $_instance = NULL;
    protected $_registered = array();
    /**
     * @return C_Pricelist_Category_Manager
     */
    static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new C_Pricelist_Category_Manager();
        }
        return self::$_instance;
    }
    /**
     * Registers a pricelist category with the system
     *
     * @param $id
     * @param array $properties
     */
    function register($id, $properties = array())
    {
        $this->_registered[$id] = $properties;
    }
    /**
     * Deregisters a pricelist category with the system
     *
     * @param $id
     */
    function deregister($id)
    {
        unset($this->_registered[$id]);
    }
    /**
     * Updates a category properties
     *
     * @param $id
     * @param array $properties
     */
    function update($id, $properties = array())
    {
        $retval = FALSE;
        if (isset($this->_registered[$id])) {
            foreach ($properties as $k => $v) {
                $this->_registered[$id][$k] = $v;
            }
            $retval = TRUE;
        }
        return $retval;
    }
    /**
     * Gets all or a specific property of a pricelist category
     *
     * @param $id
     * @param bool $property
     * @return null
     */
    function get($id, $property = FALSE)
    {
        $retval = NULL;
        if (isset($this->_registered[$id])) {
            if ($property && isset($this->_registered[$id][$property])) {
                $retval = $this->_registered[$id][$property];
            } else {
                if (!$property) {
                    $retval = $this->_registered[$id];
                }
            }
        }
        return $retval;
    }
    /**
     * Gets ids of all registered categories
     *
     * @return array
     */
    function get_ids()
    {
        return array_keys($this->_registered);
    }
    /**
     * Gets all categories registered to a source
     *
     * @param string $source Source ID
     * @return array Categories
     */
    function get_by_source($source_id)
    {
        $retval = array();
        foreach ($this->_registered as $category_id => $category) {
            if (in_array($source_id, $category['source'])) {
                $retval[$category_id] = $category;
            }
        }
        return $retval;
    }
}
/** @property C_NextGen_Admin_Page_Controller $object */
class C_Pricelist_Category_Page extends C_NextGen_Admin_Page_Controller
{
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Pricelist_Category_Page();
        }
        return self::$_instances[$context];
    }
    function define($context = FALSE)
    {
        parent::define(NGG_PRO_PRICELIST_CATEGORY_PAGE);
    }
    function get_required_permission()
    {
        return 'NextGEN Change options';
    }
    function get_page_heading()
    {
        return __('Manage Pricelist', 'nextgen-gallery-pro');
    }
    function enqueue_backend_resources()
    {
        parent::enqueue_backend_resources();
        $router = C_Router::get_instance();
        if (!wp_script_is('sprintf')) {
            wp_register_script('sprintf', $router->get_static_url('photocrati-nextgen_pro_ecommerce#sprintf.js'));
        }
        wp_enqueue_script('sprintf');
        wp_enqueue_script('jquery.number');
        // Enqueue fontawesome
        if (method_exists('M_Gallery_Display', 'enqueue_fontawesome')) {
            M_Gallery_Display::enqueue_fontawesome();
        } else {
            C_Display_Type_Controller::get_instance()->enqueue_displayed_gallery_trigger_buttons_resources();
        }
        wp_enqueue_style('fontawesome');
        wp_enqueue_script('nggpro_manage_pricelist_page_js', $router->get_static_url('photocrati-nextgen_pro_ecommerce#manage_pricelist_page.js'), array('jquery', 'thickbox', 'jquery-ui-tooltip', 'underscore', 'jquery-ui-sortable'), NGG_PRO_ECOMMERCE_MODULE_VERSION);
        $sources = array();
        $source_manager = C_Pricelist_Source_Manager::get_instance();
        foreach ($source_manager->get_ids() as $source_id) {
            $sources[$source_id] = $source_manager->get($source_id, 'lab_fulfilled');
        }
        wp_localize_script('nggpro_manage_pricelist_page_js', 'ngg_pro_pricelist_sources', $sources);
        wp_localize_script('nggpro_manage_pricelist_page_js', 'manage_pricelist_page_i18n', get_object_vars($this->get_i18n_strings()));
        wp_enqueue_style('nggpro_manage_pricelist_page_css', $router->get_static_url('photocrati-nextgen_pro_ecommerce#manage_pricelist_page.css'));
        wp_enqueue_style('thickbox');
    }
    // Without this index_action() any direct invocation of this method results in __call() execing it over and over
    // and over and over until the PHP VM gives up and moves on. Do not remove this.
    function index_action()
    {
        return parent::index_action();
    }
    /**
     * Adds additional parameters to the manage_pricelist.php template
     *
     * @return array
     */
    function get_index_params()
    {
        $manager = C_Pricelist_Source_Manager::get_instance();
        $sources = array();
        $router = C_Router::get_instance();
        foreach ($manager->get_ids() as $source) {
            $info = $manager->get($source);
            $source_obj = new $info['classname']();
            $info['add_new_template'] = $source_obj->add_new_item_template();
            $sources[$source] = $info;
        }
        $model = $this->get_model();
        return array('pricelist_sources' => $sources, 'settings' => $model->settings, 'wrap_css_class' => version_compare('2.2.99', NGG_PLUGIN_VERSION) > 0 ? 'not-redesign' : 'redesign', 'logo' => $router->get_static_url('photocrati-nextgen_pro_ecommerce#imagely_icon.png'));
    }
    function index_template()
    {
        return 'photocrati-nextgen_pro_ecommerce#manage_pricelist';
    }
    function get_model()
    {
        if (!isset($this->pricelist)) {
            $pricelist_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 0;
            $mapper = C_Pricelist_Mapper::get_instance();
            $this->pricelist = $mapper->find($pricelist_id, TRUE);
            if (!$this->pricelist) {
                $this->pricelist = $mapper->create();
            }
        }
        return $this->pricelist;
    }
    function get_i18n_strings()
    {
        $i18n = new stdClass();
        $i18n->saved = __('Saved pricelist successfully', 'nextgen-gallery-pro');
        $i18n->deleted = __('Deleted pricelist', 'nextgen-gallery-pro');
        $i18n->gallery_wrap_notice = __('You are adding a Canvas to a pricelist. Please be aware that when printing a Canvas, the edges of the photo (between 1 to 3 inches) will wrap around the side of the product.', 'nextgen-gallery-pro');
        return $i18n;
    }
    /**
     * Gets the action to be executed
     * @return string
     */
    function _get_action()
    {
        $action = $this->object->param('action');
        if (!$action && isset($_REQUEST['action_proxy'])) {
            $action = $_REQUEST['action_proxy'];
        }
        $action = $action ?: '';
        $retval = preg_quote($action, '/');
        $retval = strtolower(preg_replace("/[^\\w]/", '_', $retval));
        $retval = preg_replace("/_{2,}/", "_", $retval) . '_action';
        return $retval;
    }
    function get_success_message()
    {
        $retval = $this->param('message');
        if (!$retval) {
            if ($this->_get_action() == 'delete_action') {
                $retval = 'deleted';
            } else {
                $retval = 'saved';
            }
        }
        return $this->get_i18n_strings()->{$retval};
    }
    function delete_action()
    {
        if ($this->get_model()->destroy()) {
            return wp_redirect(admin_url('edit.php?post_type=ngg_pricelist&ids=' . $this->get_model()->id()));
        } else {
            return FALSE;
        }
    }
}
class C_Pricelist_Item extends C_DataMapper_Model
{
    var $_mapper_interface = 'I_Pricelist_Item_Mapper';
    function define($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        parent::define($mapper, $properties, $context);
        $this->implement('I_Pricelist_Item');
    }
    /**
     * Initializes a display type with properties
     *
     * @param false|C_Display_Type_Mapper $mapper
     * @param array|object|C_Display_Type $properties
     * @param false|string|array $context
     */
    function initialize($properties = array(), $mapper = FALSE, $context = FALSE)
    {
        // If no mapper was specified, then get the mapper
        if (!$mapper) {
            $mapper = $this->get_registry()->get_utility($this->_mapper_interface);
        }
        // Construct the model
        parent::initialize($mapper, $properties);
    }
    function validation()
    {
        $this->validates_presence_of('title');
        $this->validates_presence_of('price');
        $this->validates_presence_of('source');
        $this->validates_presence_of('category');
        $this->validates_presence_of('pricelist_id');
        $this->validates_presence_of('sortorder');
        $this->validates_numericality_of('sortorder');
        $this->validates_numericality_of('price', 0.0, '>=');
    }
    function get_price($apply_markup = TRUE, $apply_conversion = FALSE, $force_conversion = FALSE, $round = FALSE)
    {
        return $this->get_mapper()->get_price($this->get_entity(), $apply_markup, $apply_conversion, $force_conversion, $round);
    }
    function get_formatted_price($apply_markup = TRUE, $apply_conversion = FALSE, $force_conversion = FALSE, $round = FALSE)
    {
        return M_NextGen_Pro_Ecommerce::get_formatted_price($this->get_price($apply_markup, $apply_conversion, $force_conversion, $round));
    }
    function is_lab_fulfilled()
    {
        $source_manager = C_Pricelist_Source_Manager::get_instance();
        return $source_manager->get($this->source, 'lab_fulfilled');
    }
}
/**
 * @mixin Mixin_Pricelist_Item_Mapper
 */
class C_Pricelist_Item_Mapper extends C_CustomPost_DataMapper_Driver
{
    public static $_instances = array();
    /**
     * @param mixed $context
     * @return C_Pricelist_Item_Mapper
     */
    public static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            self::$_instances[$context] = new C_Pricelist_Item_Mapper();
        }
        return self::$_instances[$context];
    }
    function define($context = FALSE, $object_name = 'ngg_pricelist_item')
    {
        // Add the object name to the context of the object as well
        // This allows us to adapt the driver itself, if required
        if (!is_array($context)) {
            $context = array($context);
        }
        array_push($context, $object_name);
        parent::define($object_name, $context);
        $this->add_mixin('Mixin_Pricelist_Item_Mapper');
        $this->set_model_factory_method($object_name);
        // Define columns
        $this->define_column('ID', 'BIGINT', 0);
        $this->define_column('pricelist_id', 'BIGINT', 0);
        $this->define_column('price', 'DECIMAL', 0.0);
        $this->define_column('source', 'VARCHAR(255)');
        $this->define_column('source_data', 'TEXT');
        $this->define_column('category', 'VARCHAR(255)');
        $this->define_column('resolution', 'DECIMAL');
        $this->define_column('sortorder', 'BIGINT', 0);
        $this->define_column('is_shippable', 'BOOLEAN', FALSE);
        $this->add_serialized_column('source_data');
    }
    function initialize($context = FALSE)
    {
        parent::initialize('ngg_pricelist_item');
    }
}
/**
 * @property C_Pricelist_Item_Mapper $object
 */
class Mixin_Pricelist_Item_Mapper extends Mixin
{
    /**
     * Uses the title attribute as the post title
     *
     * @param stdClass $entity
     * @return string
     */
    function get_post_title($entity)
    {
        return $entity->title;
    }
    function get_category_items($pricelist_id, $category, $model = FALSE)
    {
        // The Nextgen datamapper can't retrieve posts based on a lack of a WP meta attribute
        // and when only some entries have that attribute it will always return those; for this
        // one instance we create a new WP_Query
        $source_part = array();
        $source_part['key'] = 'source';
        $source_part['value'] = NGG_PRO_DIGITAL_DOWNLOADS_SOURCE;
        $source_part['compare'] = $category == NGG_PRO_ECOMMERCE_CATEGORY_DIGITAL_DOWNLOADS ? '=' : '!=';
        $pricelist_id_part = array('key' => 'pricelist_id', 'value' => $pricelist_id, 'compare' => '=');
        $category_part = array('key' => 'category', 'value' => $category, 'compare' => '=');
        // A plain query: check for items in this pricelist with an appropriate source
        $meta_query = array($pricelist_id_part, $source_part, $category_part);
        // "Prints" are the fallback category; any pricelist item without a digital-downloads source and without
        // a category is treated as a Print
        if (in_array($category, array(NGG_PRO_ECOMMERCE_CATEGORY_PRINTS, NGG_PRO_ECOMMERCE_CATEGORY_DIGITAL_DOWNLOADS))) {
            $meta_query = array($pricelist_id_part, $source_part, array('relation' => 'OR', $category_part, array('key' => 'category', 'compare' => 'NOT EXISTS')));
        }
        $args = array('orderby' => 'ID', 'order' => 'ASC', 'post_type' => 'ngg_pricelist_item', 'post_status' => 'draft', 'posts_per_page' => -1, 'meta_query' => $meta_query);
        $query = new WP_Query($args);
        $results = $query->get_posts();
        $mapper = C_Pricelist_Item_Mapper::get_instance();
        foreach ($results as $ndx => $item) {
            $results[$ndx] = $mapper->find($item, $model);
        }
        foreach ($results as $ndx => $item) {
            // Again we must correct the missing category attribute for pricelist items created before categories
            if (empty($item->category) && !empty($item->source)) {
                if ($item->source == NGG_PRO_DIGITAL_DOWNLOADS_SOURCE) {
                    $item->category = NGG_PRO_ECOMMERCE_CATEGORY_DIGITAL_DOWNLOADS;
                } else {
                    $item->category = NGG_PRO_ECOMMERCE_CATEGORY_PRINTS;
                }
                $results[$ndx] = $item;
            }
        }
        // WP_Query doesn't well handle ordering entries by the sortorder meta value by their numeric value. It also
        // doesn't well handle mixed cases of items having a sortorder and some items having no sortorder attribute.
        // So we sort them out manually here: by sortorder first, then ID
        usort($results, array($this, 'compare_sort_order'));
        unset($query);
        return $results;
    }
    /**
     * Used in above comparison of pricelist items to build a sortorder
     *
     * @param $item_one
     * @param $item_two
     * @return int
     */
    function compare_sort_order($item_one, $item_two)
    {
        if ($item_one->ID == $item_two->ID) {
            return 0;
        }
        if (empty($item_one->sortorder) && empty($item_two->sortorder)) {
            return $item_one->ID < $item_two->ID ? -1 : 1;
        }
        return $item_one->sortorder < $item_two->sortorder ? -1 : 1;
    }
    function round($item, $amount)
    {
        $settings = C_NextGen_Settings::get_instance();
        $currency = $settings->ecommerce_currency;
        $currency_info = C_NextGen_Pro_Currencies::$currencies[$currency];
        $exponent = intval($currency_info['exponent']);
        $pricelist_mapper = C_Pricelist_Mapper::get_instance();
        $markup = isset($item->pricelist_id) ? $pricelist_mapper->get_default_markup_for_pricelist($item->pricelist_id) : array('percentage' => NGG_PRO_ECOMMERCE_DEFAULT_MARKUP, 'rounding' => "none");
        $exponent_2 = 4;
        if ($markup['rounding'] != 'none') {
            $amount = round($amount, $exponent_2, PHP_ROUND_HALF_UP);
            if ($markup['rounding'] == 'cent') {
                $prev_amount = $amount;
                $amount = floor($amount);
                if ($prev_amount - $amount > 0) {
                    $amount = bcadd($amount, 0.99, $exponent_2);
                } else {
                    $amount = bcsub($amount, 0.01, $exponent_2);
                }
            } else {
                if ($markup['rounding'] == 'zero') {
                    $amount = ceil($amount);
                }
            }
        }
        return bcadd($amount, 0.0, $exponent);
    }
    /**
     * @param C_Pricelist_Item|stdClass $item
     * @param bool $apply_markup
     * @param bool $apply_conversion_rate
     * @param bool $force_conversion
     * @return float|int
     */
    function get_price($item, $apply_markup = TRUE, $apply_conversion_rate = FALSE, $force_conversion = FALSE, $round = FALSE)
    {
        $settings = C_NextGen_Settings::get_instance();
        $currency = $settings->ecommerce_currency;
        $currency_info = C_NextGen_Pro_Currencies::$currencies[$currency];
        $pricelist_mapper = C_Pricelist_Mapper::get_instance();
        $exponent = intval($currency_info['exponent']);
        $exponent_2 = 4;
        $markup = isset($item->pricelist_id) ? $pricelist_mapper->get_default_markup_for_pricelist($item->pricelist_id) : NGG_PRO_ECOMMERCE_DEFAULT_MARKUP;
        $price = 0.0;
        // Calculate cost
        if ($item && isset($item->cost)) {
            $price = bcadd($item->cost, 0.0, $exponent_2);
            // Apply conversion rate
            if ($apply_conversion_rate) {
                $lab_manager = C_NextGEN_Printlab_Manager::get_instance();
                // TODO: This shouldn't be hard coded to 'whcc'.
                // The catalog should be associated with the pricelist item
                $catalog = $lab_manager->get_catalog('whcc');
                if ($currency != $catalog->currency || $force_conversion) {
                    $rate = C_NextGen_Pro_Currencies::get_conversion_rate($catalog->currency, $currency);
                    if ($rate != 0) {
                        $price = bcmul($price, bcmul($rate, 1.01, $exponent_2), $exponent_2);
                    }
                }
            }
            // Apply markup
            if ($apply_markup) {
                $price = bcmul(bcadd(1, bcdiv($markup['percentage'], 100, $exponent_2), $exponent_2), $price, $exponent_2);
                $price = $this->round($item, $price);
            }
        }
        $price = bcadd($price, 0.0, $exponent);
        if (floatval($price) < 0 && $exponent == 0) {
            $price = round($item->cost ? $item->cost : $price, 0, PHP_ROUND_HALF_UP);
        }
        return $round ? $this->round($item, $price) : $price;
    }
}
/**
 * @mixin Mixin_Pricelist_Mapper
 */
class C_Pricelist_Mapper extends C_CustomPost_DataMapper_Driver
{
    public static $_instances = array();
    /**
     * @param mixed $context
     * @return C_Pricelist_Mapper
     */
    static function get_instance($context = FALSE)
    {
        if (!isset(self::$_instances[$context])) {
            $klass = get_class();
            self::$_instances[$context] = new $klass($context);
        }
        return self::$_instances[$context];
    }
    function define($context = FALSE, $not_used = FALSE)
    {
        $object_name = 'ngg_pricelist';
        // Add the object name to the context of the object as well
        // This allows us to adapt the driver itself, if required
        if (!is_array($context)) {
            $context = array($context);
        }
        array_push($context, $object_name);
        parent::define($object_name, $context);
        $this->add_mixin('Mixin_Pricelist_Mapper');
        $this->set_model_factory_method($object_name);
        // Define columns
        $this->define_column('ID', 'BIGINT');
        $this->define_column('post_author', 'BIGINT');
        $this->define_column('title', 'VARCHAR(255)');
        $this->define_column('settings', 'TEXT');
        $this->define_column('digital_download_settings', 'TEXT');
        $this->define_column('ngg_catalog_versions', 'TEXT');
        // Mark the columns which should be unserialized
        $this->add_serialized_column('settings');
        $this->add_serialized_column('digital_download_settings');
        $this->add_serialized_column('ngg_catalog_versions');
    }
    function initialize($context = FALSE)
    {
        parent::initialize('ngg_pricelist');
    }
}
/**
 * @property C_CustomPost_DataMapper_Driver $object
 */
class Mixin_Pricelist_Mapper extends Mixin
{
    function destroy($entity)
    {
        if ($this->call_parent('destroy', $entity)) {
            return $this->destroy_items($entity);
        } else {
            return FALSE;
        }
    }
    function destroy_items($pricelist_id, $ids = array())
    {
        global $wpdb;
        // If no ids have been provided, then delete all items for the given pricelist
        if (!$ids) {
            // Ensure we have the pricelist id
            if (!is_int($pricelist_id)) {
                $pricelist_id = $pricelist_id->ID;
            }
            // Find all item ids
            $item_mapper = C_Pricelist_Item_Mapper::get_instance();
            $ids = array();
            $results = $item_mapper->select("ID, post_parent")->where(array('pricelist_id = %d', $pricelist_id))->run_query();
            foreach ($results as $row) {
                $ids[] = $row->ID;
                if ($row->post_parent) {
                    $ids[] = $row->post_parent;
                }
            }
        }
        // Get unique ids
        $ids = array_unique($ids);
        // Delete all posts and post meta for the item ids
        $sql = array();
        $sql[] = "DELETE FROM {$wpdb->posts} WHERE ID IN (" . implode(',', $ids) . ')';
        $sql[] = "DELETE FROM {$wpdb->postmeta} WHERE post_id IN (" . implode(',', $ids) . ')';
        foreach ($sql as $query) {
            $wpdb->query($query);
        }
        return TRUE;
    }
    /**
     * Uses the title attribute as the post title
     * @param stdClass $entity
     * @return string
     */
    function get_post_title($entity)
    {
        return $entity->title;
    }
    /**
     * @param $id
     * @param bool $model
     * @return C_Pricelist|stdClass|null
     */
    function find_for_gallery($id, $model = FALSE)
    {
        $retval = NULL;
        if (is_object($id)) {
            $id = $id->{$id->id_field};
        }
        $mapper = C_Gallery_Mapper::get_instance();
        if ($gallery = $mapper->find($id)) {
            if (isset($gallery->pricelist_id)) {
                $retval = $this->object->find($gallery->pricelist_id, $model);
            }
        }
        return $retval;
    }
    /**
     * @param $id
     * @param bool $model
     * @return C_Pricelist|stdClass|null
     */
    function find_for_image($id, $model = FALSE)
    {
        $retval = NULL;
        $image = NULL;
        // Find the image
        if (is_object($id)) {
            $image = $id;
        } else {
            $mapper = C_Image_Mapper::get_instance();
            $image = $mapper->find($id);
        }
        // If we've found the image, then find it's pricelist
        if ($image) {
            if ($image->pricelist_id) {
                $retval = $this->object->find($image->pricelist_id, $model);
            } else {
                $retval = $this->find_for_gallery($image->galleryid, $model);
            }
        }
        return $retval;
    }
    function get_default_markup_for_pricelist($pricelist_id)
    {
        $retval = array('percentage' => NGG_PRO_ECOMMERCE_DEFAULT_MARKUP, 'rounding' => 'none');
        if ($pricelist_id) {
            $mapper = C_Pricelist_Mapper::get_instance();
            $pricelist = $mapper->find($pricelist_id);
            if ($pricelist) {
                // TODO: bulk_markup_amount shouldn't be part of manual settings
                $retval['percentage'] = floatval($pricelist->settings['bulk_markup_amount']);
                $retval['rounding'] = $pricelist->settings['bulk_markup_rounding'];
            }
        }
        return $retval;
    }
    function set_defaults($entity)
    {
        // Set defaults for manual pricelist settings
        if (!isset($entity->settings)) {
            $entity->settings = array();
        }
        if (!array_key_exists('bulk_markup_amount', $entity->settings)) {
            $entity->settings['bulk_markup_amount'] = NGG_PRO_ECOMMERCE_DEFAULT_MARKUP;
        }
        if (!array_key_exists('bulk_markup_rounding', $entity->settings)) {
            $entity->settings['bulk_markup_rounding'] = 'zero';
        }
        // Set defaults for digital download settings
        if (!isset($entity->digital_download_settings)) {
            $entity->digital_download_settings = array();
        }
        if (!isset($entity->digital_download_settings['show_licensing_link'])) {
            $entity->digital_download_settings['show_licensing_link'] = 0;
        }
        if (!isset($entity->digital_download_settings['licensing_page_id'])) {
            $entity->digital_download_settings['licensing_page_id'] = 0;
        }
        if (!isset($entity->digital_download_settings['skip_checkout'])) {
            $entity->digital_download_settings['skip_checkout'] = 0;
        }
        // Pricelists should be published by default
        $entity->post_status = 'publish';
        // TODO: This should be part of the datamapper actually
        $entity->post_title = $this->get_post_title($entity);
    }
}
class C_Pricelist_Shipping_Method_Manager
{
    /**
     * @var $_instance C_Pricelist_Shipping_Method_Manager
     */
    static $_instance = NULL;
    private $_registered = array();
    /**
     * @return C_Pricelist_Shipping_Method_Manager
     */
    static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new C_Pricelist_Shipping_Method_Manager();
        }
        return self::$_instance;
    }
    /**
     * Registers a shipping method
     * @param $id
     * @param array $properties
     */
    function register($id, $properties = array())
    {
        $this->_registered[$id] = $properties;
        return $this;
    }
    /**
     * Deregisters a shipping method with the system
     * @param $id
     */
    function deregister($id)
    {
        unset($this->_registered[$id]);
        return $this;
    }
    /**
     * Updates a shipping method properties
     * @param $id
     * @param array $properties
     */
    function update($id, $properties = array())
    {
        $retval = FALSE;
        if (isset($this->_registered[$id])) {
            foreach ($properties as $k => $v) {
                $this->_registered[$id][$k] = $v;
            }
            $retval = TRUE;
        }
        return $retval;
    }
    /**
     * Gets all or a specific property of a shipping method
     * @param $id
     * @param bool $property
     * @return null
     */
    function get($id, $property = FALSE)
    {
        $retval = NULL;
        if (isset($this->_registered[$id])) {
            if ($property && isset($this->_registered[$id][$property])) {
                $retval = $this->_registered[$id][$property];
            } else {
                if (!$property) {
                    $retval = $this->_registered[$id];
                }
            }
        }
        return $retval;
    }
    /**
     * Determines whether the shipping method is universal
     *
     * What does that mean?
     *
     * Your cart can have multiple shipments, and each shipment can offer multiple shipping methods. E.g.
     *
     * shipment A
     * - method A
     * - method B
     * - method C
     *
     * shipment B
     * -> method A
     * -> method C
     *
     * When we're presenting a list of shipping methods available for the end-user to select, we can only show
     * the common shipping methods. See C_NextGen_Pro_Cart::get_shipping_methods().
     *
     * However, some shipping methods can be universal. The manual shipping method, whether it be domestic or
     * international, can be combined with any other shipping method.
     */
    function is_universal_method($id)
    {
        return $this->get($id, 'universal') && TRUE;
    }
    /**
     * Gets ids of all registered shipping methods
     * @return array
     */
    function get_ids()
    {
        return array_keys($this->_registered);
    }
}
/**
 * Provides a parent interface for pricelist sources to extend if they wish to be listed in the Manage Pricelist
 * "Add Product" dialog
 *
 * Class C_Pricelist_Source
 */
class C_Pricelist_Source extends C_MVC_Controller
{
    public static function get_info()
    {
        // These fields are not i18n'ed but they're also never meant to be displayed to any user
        return array('title' => 'A Pricelist Source', 'settings_field' => '', 'description' => 'A dummy pricelist source to be extended', 'classname' => get_class(), 'lab_fulfilled' => FALSE);
    }
    public function has_shipping_address($cart_settings)
    {
        $retval = TRUE;
        if (!isset($cart_settings['shipping_address'])) {
            $retval = FALSE;
        } else {
            foreach ($cart_settings['shipping_address'] as $key => $val) {
                if (!$val && $key != 'address_line' && $key != 'zip' && $key != 'phone') {
                    $retval = FALSE;
                }
            }
        }
        return $retval;
    }
    public function get_shippable_countries()
    {
        $settings = C_NextGen_Settings::get_instance();
        $codes = array();
        if ($settings->ecommerce_intl_shipping && $settings->ecommerce_intl_shipping != 'disabled') {
            foreach (C_NextGen_Pro_Currencies::$countries as $country) {
                $codes[] = $country['code'];
            }
        } else {
            $codes[] = $settings->ecommerce_home_country;
        }
        return $codes;
    }
    public function get_shipments($items, $cart_setting, $cart)
    {
    }
    public function get_selected_shipping_method($cart_settings, $cart)
    {
        return $cart->get_selected_shipping_method($cart_settings);
    }
    public function get_subtotal($items)
    {
        $settings = C_NextGen_Settings::get_instance();
        $currency = C_NextGen_Pro_Currencies::$currencies[$settings->ecommerce_currency];
        $retval = 0.0;
        foreach ($items as $item) {
            if ($item->source == $this->id) {
                $retval = round(bcadd($retval, bcmul($item->cost, $item->quantity, $currency['exponent']), $currency['exponent']), $currency['exponent'], PHP_ROUND_HALF_UP);
            }
        }
        return $retval;
    }
    protected function _add_shipping_method_to_array($retval, $source, $item, $currency, $key, $alias, $price_in_percent, $surcharge = 0.0, $other_surcharge = 0.0, $data = array(), $shipping_type = '')
    {
        if (!isset($item->cost)) {
            $item->cost = $item->price;
        }
        if (!isset($retval[$key])) {
            $retval[$key] = array('alias' => $alias, 'data' => $data, 'price' => round(bcmul(bcmul($item->cost, $price_in_percent, $currency['exponent'] * 2), $item->quantity, $currency['exponent'] * 2), $currency['exponent'], PHP_ROUND_HALF_UP), 'surcharge' => $surcharge, 'other_surcharge' => $other_surcharge, 'source' => $source, 'shipping_type' => $shipping_type);
        } else {
            $retval[$key]['price'] = round(bcadd(bcmul(bcmul($item->cost, $price_in_percent, $currency['exponent'] * 2), $item->quantity, $currency['exponent'] * 2), $retval[$key]['price'], $currency['exponent'] * 2), $currency['exponent'], PHP_ROUND_HALF_UP);
            if ($surcharge) {
                $retval[$key]['surcharge'] = $surcharge;
            }
            if ($other_surcharge) {
                $retval[$key]['other_surcharge'] = $other_surcharge;
            }
        }
        return $retval;
    }
    public function add_new_item_template()
    {
        return '';
    }
}
class C_Pricelist_Source_Download extends C_Pricelist_Source
{
    public static function get_info()
    {
        return array_merge(parent::get_info(), array('title' => __('Digital Downloads', 'nextgen-gallery-pro'), 'settings_field' => 'digital_download_settings', 'description' => __('Image files are made available to users for download from your site', 'nextgen-gallery-pro'), 'classname' => get_class(), 'lab_fulfilled' => FALSE));
    }
    public function get_i18n()
    {
        return array('new_product_name' => __('New product name', 'nextgen-gallery-pro'), 'new_product_name_hint' => __('Low, Medium, or High Resolution', 'nextgen-gallery-pro'), 'new_product_price' => __('Price', 'nextgen-gallery-pro'), 'new_product_resolution' => __('Longest Image Dimension', 'nextgen-gallery-pro'), 'new_product_resolution_placeholder' => __('Enter 0 for maximum', 'nextgen-gallery-pro'));
    }
    public function add_new_item_template()
    {
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#add_new_download_item', array('i18n' => $this->get_i18n()), TRUE);
    }
}
class C_Pricelist_Source_Manager
{
    static $_instance = NULL;
    var $_registered = array();
    /**
     * @return C_Pricelist_Source_Manager
     */
    static function get_instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new C_Pricelist_Source_Manager();
        }
        return self::$_instance;
    }
    /**
     * Registers a pricelist source with the system
     * @param $id
     * @param array $properties
     */
    function register($id, $properties = array())
    {
        $this->_registered[$id] = $properties;
    }
    /**
     * Deregisters a pricelist source with the system
     * @param $id
     */
    function deregister($id)
    {
        unset($this->_registered[$id]);
    }
    /**
     * Updates a source properties
     * @param $id
     * @param array $properties
     */
    function update($id, $properties = array())
    {
        $retval = FALSE;
        if (isset($this->_registered[$id])) {
            foreach ($properties as $k => $v) {
                $this->_registered[$id][$k] = $v;
            }
            $retval = TRUE;
        }
        return $retval;
    }
    /**
     * Gets all or a specific property of a pricelist source
     * @param $id
     * @param bool $property
     * @return null
     */
    function get($id, $property = FALSE)
    {
        $retval = NULL;
        if (isset($this->_registered[$id])) {
            if ($property && isset($this->_registered[$id][$property])) {
                $retval = $this->_registered[$id][$property];
            } else {
                if (!$property) {
                    $retval = $this->_registered[$id];
                }
            }
        }
        return $retval;
    }
    /*
     * Returns the source class for the pricelist source id
     * @return C_Pricelist_Source
     */
    function get_handler($id)
    {
        $klass = $this->get($id, 'classname');
        $handler = new $klass();
        $handler->id = $id;
        return $handler;
    }
    /**
     * Gets ids of all registered sources
     * @return array
     */
    function get_ids()
    {
        return array_keys($this->_registered);
    }
}
class C_Pricelist_Source_Manual extends C_Pricelist_Source
{
    public static function get_info()
    {
        return array_merge(parent::get_info(), array('title' => __('Manual Fulfillment', 'nextgen-gallery-pro'), 'settings_field' => 'manual_settings', 'description' => __('Manual fulfillment of purchased goods and items', 'nextgen-gallery-pro'), 'classname' => get_class(), 'lab_fulfilled' => FALSE, 'id' => NGG_PRO_WHCC_PRICELIST_SOURCE));
    }
    public function get_i18n()
    {
        return array('new_product_name' => __('New product name', 'nextgen-gallery-pro'), 'new_product_name_hint' => __('8x10" Canvas or 4x6" Glossy', 'nextgen-gallery-pro'), 'new_product_price' => __('Price', 'nextgen-gallery-pro'), 'new_product_category' => __('Category', 'nextgen-gallery-pro'));
    }
    public function add_new_item_template()
    {
        $manager = C_Pricelist_Category_Manager::get_instance();
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#add_new_manual_item', array('categories' => $manager->get_by_source(NGG_PRO_MANUAL_PRICELIST_SOURCE), 'i18n' => $this->get_i18n()), TRUE);
    }
    /**
     * @param array $items
     * @param array $cart_setting
     * @param $cart
     * @return array
     */
    public function get_shipments($items, $cart_setting, $cart)
    {
        // Variables used throughout the method
        $settings = C_NextGen_Settings::get_instance();
        $currency = C_NextGen_Pro_Currencies::$currencies[$settings->ecommerce_currency];
        $domestic_shipping = $settings->get('ecommerce_domestic_shipping', 'flat');
        $domestic_shipping_rate = $settings->get('ecommerce_domestic_shipping_rate', 5);
        // A shipment will be generated for each pricelist in the cart
        $retval = array();
        if (is_array($items) && !empty($items)) {
            // First determine if there's items to manually fulfill
            $has_shipments = FALSE;
            foreach ($items as $item) {
                if ($item->source == NGG_PRO_MANUAL_PRICELIST_SOURCE) {
                    $has_shipments = TRUE;
                    break;
                }
            }
            // Create a single combined shipment for all manually fulfilled items
            if ($has_shipments) {
                $shipment = new stdClass();
                $shipment->name = NGG_PRO_MANUAL_PRICELIST_SOURCE . '-' . 'combined';
                $shipment->title = __('Manual shipping for combined items', 'nextgen-gallery-pro');
                $shipment->items = array();
                // We have one shipment with many items
                foreach ($items as $item) {
                    $shipment->items[] = $item;
                }
                $shipment->shipping_methods = array();
                $retval[NGG_PRO_MANUAL_PRICELIST_SOURCE . '-' . 'combined'] = $shipment;
            }
        }
        if ($retval and $this->has_shipping_address($cart_setting) and $home_country = strtoupper($settings->get('ecommerce_home_country'))) {
            $country = strtoupper($cart_setting['shipping_address']['country']);
            $allow_international = $settings->get('ecommerce_intl_shipping', FALSE);
            $allow_international = $allow_international && $allow_international != 'disabled';
            // For each shipment, calculate the shipping methods
            foreach ($retval as $shipment) {
                foreach ($shipment->items as $item) {
                    $global_rate = floatval($settings->ecommerce_intl_shipping_rate);
                    $local_rate = floatval($domestic_shipping_rate);
                    // Get domestic shipping method
                    if ($country == $home_country) {
                        $local_surcharge = 0.0;
                        // Use percentage rate?
                        if ($domestic_shipping == 'flat' || $domestic_shipping == 'flat_rate') {
                            $local_surcharge = $local_rate;
                            $local_rate = 0;
                        } else {
                            $local_rate = round(bcdiv($local_rate, 100, $currency['exponent']), $currency['exponent'] * 2, PHP_ROUND_HALF_UP);
                        }
                        // Add the shipping method
                        $shipment->shipping_methods = $this->_add_shipping_method_to_array($shipment->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_MANUAL, __('Manual Shipping', 'nextgen-gallery-pro'), $local_rate, $local_surcharge, 0.0);
                    } else {
                        if ($allow_international) {
                            $global_surcharge = 0.0;
                            // Use a flat international shipping rate
                            if ($settings->ecommerce_intl_shipping == 'flat_rate') {
                                $global_surcharge = $global_rate;
                                $global_rate = 0;
                            } else {
                                $global_rate = round(bcdiv($global_rate, 100, $currency['exponent']), $currency['exponent'] * 2, PHP_ROUND_HALF_UP);
                            }
                            // Add the shipping method
                            $shipment->shipping_methods = $this->_add_shipping_method_to_array($shipment->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_MANUAL, __('Manual Shipping', 'nextgen-gallery-pro'), $global_rate, $global_surcharge, 0.0);
                        }
                    }
                }
            }
        }
        return $retval;
    }
}
class C_Pricelist_Source_WHCC extends C_Pricelist_Source
{
    public static function get_info()
    {
        return array_merge(parent::get_info(), array('title' => __('WHCC Prints', 'nextgen-gallery-pro'), 'settings_field' => 'print_catalog_settings', 'description' => __('Automatic fulfillment of purchased goods and items through Printlab integration', 'nextgen-gallery-pro'), 'classname' => get_class(), 'lab_fulfilled' => TRUE, 'id' => NGG_PRO_WHCC_PRICELIST_SOURCE, 'has_shipping_calculators_for' => array('CA', 'US')));
    }
    public function get_i18n()
    {
        return array('new_product_name' => __('New product name', 'nextgen-gallery-pro'), 'new_product_name_hint' => __('8x10" Canvas or 4x6" Glossy', 'nextgen-gallery-pro'), 'new_product_price' => __('Price', 'nextgen-gallery-pro'), 'new_product_category' => __('Category', 'nextgen-gallery-pro'));
    }
    public function add_new_item_template()
    {
        return $this->object->render_partial('photocrati-nextgen_pro_ecommerce#add_new_print_item', array('catalog' => C_NextGEN_Printlab_Manager::get_instance()->get_catalog('whcc'), 'i18n' => $this->get_i18n()), TRUE);
    }
    public function get_shippable_countries()
    {
        // See Pro-817 for why this method does not honor $settings->ecommerce_whcc_intl_shipping
        return array('CA', 'US');
    }
    private function is_canada_or_us($country)
    {
        return $country == 'US' || $country == 'CA';
    }
    private function add_to_international_shipment($alias, $shipment, $item, $shipping_attributes = array())
    {
        $settings = C_NextGen_Settings::get_instance();
        $currency = C_NextGen_Pro_Currencies::$currencies[$settings->ecommerce_currency];
        $global_rate = 0.0;
        $intl_shipping = 'disabled';
        // See Pro-817: $settings->ecommerce_whcc_intl_shipping;
        $intl_shipping_rate = $settings->get('ecommerce_whcc_intl_shipping_rate', 40);
        // This setting has no form controls and will always be 40
        if ($intl_shipping == 'flat_rate') {
            $global_rate = round(bcadd($global_rate, $intl_shipping_rate, intval($currency['exponent'])), intval($currency['exponent']), PHP_ROUND_HALF_UP);
            if ($global_rate) {
                $shipment->shipping_methods = $this->_add_shipping_method_to_array($shipment->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_INTERNATIONAL, $alias, 0.0, 0.0, $global_rate, $shipping_attributes);
            }
        } else {
            if ($intl_shipping == 'percent_rate') {
                $global_rate = round(bcdiv($intl_shipping_rate, 100, $currency['exponent']), $currency['exponent'] * 2, PHP_ROUND_HALF_UP);
                if ($global_rate) {
                    $shipment->shipping_methods = $this->_add_shipping_method_to_array($shipment->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_INTERNATIONAL, $alias, $global_rate, 0.0, 0.0);
                }
            }
        }
        return $shipment;
    }
    public function get_shipments($items, $cart_setting, $cart)
    {
        $retval = array();
        // Variables used throughout the body of this method
        $settings = C_NextGen_Settings::get_instance();
        $currency = C_NextGen_Pro_Currencies::$currencies[$settings->ecommerce_currency];
        // Define shipments
        $gallery_wraps = new stdClass();
        $gallery_wraps->name = 'gallery_wraps';
        $gallery_wraps->title = __('Gallery Wraps', 'nextgen-gallery-pro');
        $gallery_wraps->items = array();
        $gallery_wraps->shipping_methods = array();
        $prints = new stdClass();
        $prints->name = 'prints';
        $prints->title = __('Prints', 'nextgen-gallery-pro');
        $prints->items = array();
        $prints->shipping_methods = array();
        if ($this->has_shipping_address($cart_setting) && ($home_country = strtoupper($settings->get('ecommerce_home_country')))) {
            // Get settings necessary for calculating shipping rates
            $country = strtoupper($cart_setting['shipping_address']['country']);
            $allow_international = 'disabled';
            // See: Pro-817: $settings->get('ecommerce_whcc_intl_shipping');
            $allow_international = $allow_international && $allow_international != 'disabled';
            // Determine if we only have items under a certain size. Those are less expensive to ship
            $only_small_items = FALSE;
            foreach ($items as $item) {
                if (isset($item->source_data) && isset($item->source_data['lab_properties']) && strpos($item->source_data['category_id'], 'gallery_wrap') === FALSE) {
                    $lab_properties = $item->source_data['lab_properties'];
                    $only_small_items = $lab_properties['H'] < 12 && $lab_properties['W'] < 8 || $lab_properties['W'] < 12 && $lab_properties['H'] < 8;
                    if (!$only_small_items) {
                        break;
                    }
                }
            }
            // Iterate over every item in the cart and allocate it to a particular shipment
            reset($items);
            foreach ($items as $item) {
                // Only process WHCC items
                if ($item->source == NGG_PRO_WHCC_PRICELIST_SOURCE) {
                    // GALLERY WRAPS
                    if (strpos($item->source_data['category_id'], 'gallery_wrap') !== false) {
                        $gallery_wraps->items[] = $item;
                        $shipping_methods = array();
                        // International shipping rates apply
                        if ($allow_international && !$this->is_canada_or_us($country)) {
                            $gallery_wraps = $this->add_to_international_shipment(__('International Shipping (Gallery Wraps)', 'nextgen-gallery-pro'), $gallery_wraps, $item, array(96, 105));
                        } else {
                            if ($country == 'US') {
                                // Add Economy Trackable
                                $gallery_wraps->shipping_methods = $this->_add_shipping_method_to_array($gallery_wraps->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_ECONOMY, __('Economy Trackable (Gallery Wraps)', 'nextgen-gallery-pro'), 0.09, 0.0, 10.45, array(96, 546), 'whcc_parcel');
                                // 3 Days or Less
                                $gallery_wraps->shipping_methods = $this->_add_shipping_method_to_array($gallery_wraps->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_STANDARD, __('3 Days or Less (Gallery Wraps)', 'nextgen-gallery-pro'), 0.16, 0.0, 13.95, array(96, 100), 'whcc_parcel');
                                // Next Day Saver
                                $gallery_wraps->shipping_methods = $this->_add_shipping_method_to_array($gallery_wraps->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_EXPEDITED, __('Next Day Saver (Gallery Wraps)', 'nextgen-gallery-pro'), 0.26, 0.0, 25.95, array(96, 101), 'whcc_parcel');
                            } else {
                                if ($this->is_canada_or_us($country)) {
                                    $gallery_wraps->shipping_methods = $this->_add_shipping_method_to_array($gallery_wraps->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_STANDARD, __('UPS Canada (Gallery Wraps)', 'nextgen-gallery-pro'), 0.15, 0.0, 19.75, array(96, 104));
                                }
                            }
                        }
                    } else {
                        $prints->items[] = $item;
                        // International shipping rates apply
                        if ($allow_international && !$this->is_canada_or_us($country)) {
                            $this->add_to_international_shipment(__("International Shipping (Prints)", 'nextgen-gallery-pro'), $prints, $item, array(96, 105));
                        } else {
                            if ($country == 'US') {
                                // Economy Small Trackable
                                if ($only_small_items) {
                                    $prints->shipping_methods = $this->_add_shipping_method_to_array($prints->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_ECONOMY, __('Economy Trackable - Small sizes', 'nextgen-gallery-pro'), 0.05, 6.5, 0.0, [96, 1719], 'whcc_mail');
                                } else {
                                    $prints->shipping_methods = $this->_add_shipping_method_to_array($prints->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_ECONOMY, __('Economy Trackable - All sizes', 'nextgen-gallery-pro'), 0.09, 10.45, 0.0, array(96, 546), 'whcc_parcel');
                                }
                                // 3 Days or Less
                                $prints->shipping_methods = $this->_add_shipping_method_to_array($prints->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_STANDARD, __('3 Days or Less', 'nextgen-gallery-pro'), 0.16, 13.95, 0.0, array(96, 100), 'whcc_parcel');
                                // Next Day Saver
                                $prints->shipping_methods = $this->_add_shipping_method_to_array($prints->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_EXPEDITED, __('Next Day Saver', 'nextgen-gallery-pro'), 0.26, 25.95, 0.0, array(96, 101), 'whcc_parcel');
                                // Priority One-day
                                $prints->shipping_methods = $this->_add_shipping_method_to_array($prints->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_PRIORITY, __('Priority One-day', 'nextgen-gallery-pro'), 0.26, 25.95, 0.0, array(96, 1728), 'whcc_parcel');
                            } else {
                                if ($this->is_canada_or_us($country)) {
                                    $prints->shipping_methods = $this->_add_shipping_method_to_array($prints->shipping_methods, $this->id, $item, $currency, NGG_PRO_ECOMMERCE_SHIPPING_METHOD_STANDARD, __('UPS Canada', 'nextgen-gallery-pro'), 0.15, 19.75, 0.0, array(96, 104));
                                }
                            }
                        }
                    }
                }
            }
            // Return shipments
            if ($prints->items) {
                $retval[$prints->name] = $prints;
            }
            if ($gallery_wraps->items) {
                $retval[$gallery_wraps->name] = $gallery_wraps;
            }
        }
        return $retval;
    }
}