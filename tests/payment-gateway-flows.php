<?php

namespace Stripe {
    class Stripe {
        public static $api_key;

        public static function setApiKey($api_key) {
            self::$api_key = $api_key;
        }
    }
}

namespace Stripe\Checkout {
    class Session {
        public static $args;

        public static function create($args) {
            self::$args = $args;
            return (object) array('id' => 'cs_test_checkout');
        }
    }
}

namespace Stripe\Exception {
    class ApiErrorException extends \Exception {
    }
}

namespace {
    define('WDF_PLUGIN_BASE_DIR', dirname(__DIR__));

    class JsonResponse extends \Exception {
        public $data;
        public $status;

        public function __construct($data, $status) {
            $this->data = $data;
            $this->status = $status;
        }
    }

    class RedirectResponse extends \Exception {
    }

    class FakeFundraising {
        public $updated_pledge;

        public function generate_pledge_id() {
            return 'pledge-test-123';
        }

        public function update_pledge($pledge_id, $funder_id, $status, $transaction) {
            $this->updated_pledge = array($pledge_id, $funder_id, $status, $transaction);
            return 42;
        }
    }

    class WDF_Gateway {
        public function __construct() {
            $this->on_creation();
        }
    }

    function add_action() {}
    function add_shortcode() {}
    function remove_filter() {}
    function wdf_register_gateway_plugin() {}
    function sanitize_html_class($class) {
        return preg_replace('/[^A-Za-z0-9_-]/', '', $class);
    }
    function get_option($name) {
        global $test_options;
        return isset($test_options[$name]) ? $test_options[$name] : false;
    }
    function __($text) {
        return $text;
    }
    function absint($value) {
        return abs((int) $value);
    }
    function sanitize_text_field($value) {
        return is_scalar($value) ? trim((string) $value) : '';
    }
    function sanitize_email($value) {
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }
    function add_query_arg($args, $url = '') {
        if (!is_array($args)) {
            $args = array($args => func_get_arg(1));
            $url = func_get_arg(2);
        }
        return $url . '?' . http_build_query($args);
    }
    function wdf_get_funder_page($page, $post_id) {
        return 'https://example.test/fundraiser/' . $post_id . '/' . $page;
    }
    function home_url($path = '') {
        return 'https://example.test/' . ltrim($path, '/');
    }
    function get_post($post_id) {
        return (object) array('ID' => (int) $post_id);
    }
    function get_post_meta($post_id, $meta_key, $single = false) {
        return false;
    }
    function wp_redirect($url) {
        throw new RedirectResponse($url);
    }
    function esc_html($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
    function esc_html__($value) {
        return esc_html($value);
    }
    function esc_attr($value) {
        return esc_html($value);
    }
    function esc_attr__($value) {
        return esc_attr($value);
    }
    function esc_textarea($value) {
        return esc_html($value);
    }
    function wp_kses_post($value) {
        return strip_tags($value, '<a><b><br><em><i><p><strong><ul><ol><li>');
    }
    function wp_send_json($data, $status = 200) {
        throw new JsonResponse($data, $status);
    }
    function wp_send_json_error($data, $status = 400) {
        throw new JsonResponse($data, $status);
    }

    function assert_same($expected, $actual, $message) {
        if ($expected !== $actual) {
            fwrite(STDERR, "FAIL: {$message}\n");
            exit(1);
        }
    }

    require_once dirname(__DIR__) . '/lib/gateways/stripe.php';
    require_once dirname(__DIR__) . '/lib/gateways/paypal.php';
    require_once dirname(__DIR__) . '/lib/gateways/manual.php';
    require_once dirname(__DIR__) . '/lib/style-functions.php';
    require_once dirname(__DIR__) . '/lib/template-functions.php';

    assert_same('wdf-modern', wdf_normalize_style('wdf-note'), 'Legacy fundraiser styles use the modern layout');
    assert_same('wdf-modern', wdf_normalize_style('wdf-modern'), 'Modern fundraiser style remains selected');

    class VerifiedPayPalGateway extends \WDF_Gateway_PayPal {
        public function verify_paypal() {
            return true;
        }
    }

    global $test_options, $wdf;
    $test_options = array(
        'wdf_settings' => array(
            'active_gateways' => array('stripe' => '1'),
            'stripe' => array(
                'secret_key' => 'sk_test_not_a_real_key',
                'methods' => array('card'),
            ),
            'currency' => 'EUR',
        ),
    );
    $wdf = new FakeFundraising();
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION = array(
        'wdf_pledge' => '25.99',
        'wdf_pledge_post_id' => 17,
        'wdf_type' => 'simple',
        'wdf_reward' => 2,
    );

    try {
        wdf_stripe_create_session();
        throw new \RuntimeException('Expected the JSON response to end the request.');
    } catch (JsonResponse $response) {
        assert_same(200, $response->status, 'Checkout session request succeeds');
        assert_same('cs_test_checkout', $response->data['id'], 'Checkout session ID is returned');
    }

    $checkout_args = \Stripe\Checkout\Session::$args;
    assert_same(2599, $checkout_args['line_items'][0]['price_data']['unit_amount'], 'Checkout amount is expressed in cents');
    assert_same('pledge-test-123', $checkout_args['client_reference_id'], 'Checkout session contains a pledge reference');
    assert_same('pledge-test-123', $checkout_args['metadata']['pledge_id'], 'Checkout metadata contains the pledge ID');
    assert_same('17', $checkout_args['metadata']['funder_id'], 'Checkout metadata contains the fundraiser ID');
    assert_same('pledge-test-123', $_SESSION['wdf_pledge_id'], 'Pledge ID is retained in the session');
    assert_same(true, strpos($checkout_args['success_url'], 'wdf_pledge_id=pledge-test-123') !== false, 'Success URL carries the pledge ID');

    $gateway = new WDF_Gateway_Stripe();
    $event = (object) array(
        'type' => 'checkout.session.completed',
        'data' => (object) array(
            'object' => (object) array(
                'id' => 'cs_test_checkout',
                'payment_intent' => 'pi_test_payment',
                'payment_status' => 'paid',
                'amount_total' => 2599,
                'currency' => 'eur',
                'client_reference_id' => 'pledge-test-123',
                'metadata' => (object) array(
                    'pledge_id' => 'pledge-test-123',
                    'funder_id' => '17',
                    'type' => 'simple',
                    'reward' => '2',
                ),
                'customer_details' => (object) array(
                    'email' => 'donor@example.test',
                    'name' => 'Ada Example',
                ),
            ),
        ),
    );
    assert_same(true, $gateway->process_webhook_event($event), 'Paid checkout webhook is processed');
    assert_same('pledge-test-123', $wdf->updated_pledge[0], 'Webhook updates the referenced pledge');
    assert_same(17, $wdf->updated_pledge[1], 'Webhook updates the correct fundraiser');
    assert_same('wdf_complete', $wdf->updated_pledge[2], 'Paid webhook marks the pledge complete');
    assert_same(25.99, $wdf->updated_pledge[3]['gross'], 'Webhook stores the paid amount');
    assert_same('EUR', $wdf->updated_pledge[3]['currency_code'], 'Webhook stores the currency');
    assert_same('donor@example.test', $wdf->updated_pledge[3]['payer_email'], 'Webhook stores the payer email');
    assert_same(2, $wdf->updated_pledge[3]['reward'], 'Webhook preserves the selected reward');

    $wdf->updated_pledge = null;
    $event->data->object->payment_status = 'unpaid';
    assert_same(true, $gateway->process_webhook_event($event), 'Unpaid checkout event is safely ignored');
    assert_same(null, $wdf->updated_pledge, 'Unpaid event does not update a pledge');

    $event->type = 'checkout.session.async_payment_succeeded';
    $event->data->object->payment_status = 'paid';
    assert_same(true, $gateway->process_webhook_event($event), 'Asynchronous successful payment is processed');
    assert_same('wdf_complete', $wdf->updated_pledge[2], 'Asynchronous payment marks the pledge complete');
    $test_options['wdf_settings']['manual'] = array(
        'status' => 'wdf_complete',
        'account_name' => 'Demo Konto',
        'iban' => 'DE89 3704 0044 0532 0130 00',
        'bic' => 'COBADEFFXXX',
        'bank_name' => 'Demo Bank',
        'reference_prefix' => 'Spende',
    );
    $wdf->updated_pledge = null;
    $_SESSION['funder_id'] = 17;
    $_SESSION['wdf_pledge'] = '10.00';
    $_POST = array(
        'first_name' => 'Ada',
        'last_name' => 'Example',
        'e-mail' => 'donor@example.test',
    );
    $manual = new \WDF_Gateway_Manual();
    try {
        $manual->process_simple();
        throw new \RuntimeException('Expected the manual gateway to redirect.');
    } catch (RedirectResponse $response) {
        assert_same(true, strpos($response->getMessage(), 'pledge_id=pledge-test-123') !== false, 'Manual transfer confirmation contains the pledge ID');
    }
    assert_same('wdf_pending', $wdf->updated_pledge[2], 'Manual transfer is saved as pending regardless of legacy status setting');
    assert_same('Ausstehend', $wdf->updated_pledge[3]['status'], 'Manual transaction stores pending status label');
    assert_same('Spende pledge-test-123', $wdf->updated_pledge[3]['reference'], 'Manual transaction stores a unique payment reference');
    $manual_info = $manual->payment_info('', $wdf->updated_pledge[3]);
    assert_same(true, strpos($manual_info, 'data:image/svg+xml;base64,') !== false, 'Valid bank details produce a local SVG EPC QR');
    assert_same(true, strpos($manual_info, 'DE89370400440532013000') !== false, 'Transfer details include the normalized IBAN');
    assert_same(true, strpos($manual_info, 'Spende pledge-test-123') !== false, 'Transfer details include the unique reference');
    echo "Manual transfer and EPC QR flow tests passed.\n";

    $test_options['wdf_settings']['paypal'] = array();
    $test_options['wdf_settings']['paypal_sb'] = 'no';
    $wdf->updated_pledge = null;
    $_POST = array(
        'txn_type' => 'web_accept',
        'reason_code' => '',
        'custom' => '17||pledge-paypal-123||2||DE',
        'payment_gross' => '14.50',
        'mc_currency' => 'EUR',
        'txn_id' => 'txn_test_1',
        'first_name' => 'Ada',
        'last_name' => 'Example',
        'payer_email' => 'donor@example.test',
        'payment_status' => 'Completed',
    );
    register_shutdown_function(function() use ($wdf) {
        assert_same('pledge-paypal-123', $wdf->updated_pledge[0], 'Verified PayPal IPN updates the pledge');
        assert_same('17', $wdf->updated_pledge[1], 'PayPal IPN updates the correct fundraiser');
        assert_same('wdf_complete', $wdf->updated_pledge[2], 'Completed PayPal IPN marks the pledge complete');
        assert_same('14.50', $wdf->updated_pledge[3]['gross'], 'PayPal IPN stores the paid amount');
        assert_same('EUR', $wdf->updated_pledge[3]['currency_code'], 'PayPal IPN stores the currency');
        assert_same('txn_test_1', $wdf->updated_pledge[3]['ipn_id'], 'PayPal IPN stores the transaction ID');
        assert_same('2', $wdf->updated_pledge[3]['reward'], 'PayPal IPN preserves the selected reward');
        echo "PayPal IPN flow test passed.\n";
    });
    $paypal = new VerifiedPayPalGateway();
    $paypal->handle_ipn();

    session_write_close();
}
