<?php
add_action('wp_ajax_wdf_stripe_create_session', 'wdf_stripe_create_session');
add_action('wp_ajax_nopriv_wdf_stripe_create_session', 'wdf_stripe_create_session');

if (!class_exists('WDF_Gateway_Stripe')) {
    class WDF_Gateway_Stripe extends WDF_Gateway {

        public $plugin_name = 'stripe';
        public $admin_name = '';
        public $public_name = '';
        public $force_ssl = true;
        public $payment_types = 'simple, advanced';
        public $skip_form = true;
        public $allow_reccuring = true;

        function skip_form() {
            return true;
        }

        function on_creation() {
            $this->public_name = $this->admin_name = 'Stripe';
            remove_filter('wdf_checkout_payment_form_stripe', array($this, '_payment_form_wrapper'), 20);
        }

        function admin_settings() {
            if (!class_exists('Psource_HelpTooltips')) require_once WDF_PLUGIN_BASE_DIR . '/lib/classes/class.wd_help_tooltips.php';
            $tips = new Psource_HelpTooltips();
            $tips->set_icon_url(WDF_PLUGIN_URL.'/img/information.png');
            $settings = get_option('wdf_settings'); ?>
            <table class="form-table">
                <tbody>
                <tr valign="top">
                    <th scope="row"><?php _e('Stripe Webhook-URL', 'wdf'); ?></th>
                    <td>
                        <code><?php echo esc_html( home_url( '/?wdf_stripe_webhook=1' ) ); ?></code>
                        <br>
                        <span class="description">
                            <?php _e('Diese URL muss im Stripe Dashboard als Webhook hinterlegt werden. Aktiviere mindestens das Event <code>checkout.session.completed</code>.', 'wdf'); ?>
                        </span>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Stripe Secret Key', 'wdf'); ?></th>
                    <td>
                        <input class="regular-text" type="text" name="wdf_settings[stripe][secret_key]" value="<?php echo esc_attr(isset($settings['stripe']['secret_key']) ? $settings['stripe']['secret_key'] : ''); ?>" />
                        <?php echo $tips->add_tip(__('Dein Stripe Secret Key aus dem Stripe Dashboard.', 'wdf')); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Stripe Publishable Key', 'wdf'); ?></th>
                    <td>
                        <input class="regular-text" type="text" name="wdf_settings[stripe][publishable_key]" value="<?php echo esc_attr(isset($settings['stripe']['publishable_key']) ? $settings['stripe']['publishable_key'] : ''); ?>" />
                        <?php echo $tips->add_tip(__('Dein Stripe Publishable Key aus dem Stripe Dashboard.', 'wdf')); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Webhook Secret', 'wdf'); ?></th>
                    <td>
                        <input class="regular-text" type="text" name="wdf_settings[stripe][webhook_secret]" value="<?php echo esc_attr(isset($settings['stripe']['webhook_secret']) ? $settings['stripe']['webhook_secret'] : ''); ?>" />
                        <?php echo $tips->add_tip(__('Das Webhook Secret aus deinem Stripe Dashboard für die Webhook-Überprüfung.', 'wdf')); ?>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Erlaubte Stripe-Zahlarten', 'wdf'); ?></th>
                    <td>
                        <?php
                        $all_methods = [
                            'card' => 'Kreditkarte (Visa, Mastercard, Amex, ...)',
                            'sepa_debit' => 'SEPA-Lastschrift',
                            'sofort' => 'SOFORT',
                            'giropay' => 'giropay',
                            'bancontact' => 'Bancontact',
                            'ideal' => 'iDEAL',
                            'eps' => 'EPS',
                            'p24' => 'Przelewy24',
                            'alipay' => 'Alipay',
                            'wechat_pay' => 'WeChat Pay',
                            'paypal' => 'PayPal (Beta, ggf. nicht überall verfügbar)',
                            // Weitere Methoden nach Bedarf ergänzen
                        ];
                        $enabled = isset($settings['stripe']['methods']) ? (array)$settings['stripe']['methods'] : ['card'];
                        foreach ($all_methods as $key => $label) {
                            ?>
                            <label style="display:block;">
                                <input type="checkbox" name="wdf_settings[stripe][methods][]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $enabled)); ?> />
                                <?php echo esc_html($label); ?>
                            </label>
                            <?php
                        }
                        ?>
                        <span class="description">
                            <?php _e('Nur aktivierte Methoden im Stripe Dashboard funktionieren. <a href="https://dashboard.stripe.com/settings/payment_methods" target="_blank">Hier aktivieren &rarr;</a>', 'wdf'); ?>
                        </span>
                    </td>
                </tr>
                </tbody>
            </table>
            <?php
        }

        function save_gateway_settings() {
            if (isset($_POST['wdf_settings']['stripe'])) {
                $new = array();
                $new['stripe'] = $_POST['wdf_settings']['stripe'];
                $new['stripe'] = array_map('sanitize_text_field', $new['stripe']);
                $settings = get_option('wdf_settings');
                $settings = array_merge($settings, $new);
                update_option('wdf_settings', $settings);
            }
        }

        function payment_form() {
            global $wdf;
            $settings = get_option('wdf_settings');
            $publishable_key = isset($settings['stripe']['publishable_key']) ? $settings['stripe']['publishable_key'] : '';
            $betrag = isset($_SESSION['wdf_pledge']) ? $wdf->format_currency('', $_SESSION['wdf_pledge']) : '';
            ob_start();
            ?>
            <div class="wdf_payment_summary">
                <h4><?php printf(__('Deine Unterstützung von %s ist fast vollständig.','wdf'), $betrag); ?></h4>
                <?php
                if (isset($wdf->wdf_error) && $wdf->wdf_error == true) {
                    echo apply_filters('wdf_error_gateway','');
                }
                ?>
            </div>
            <div class="wdf_stripe_payment_form wdf_payment_form">
                <p><?php _e('Du wirst zum Stripe Checkout weitergeleitet.', 'wdf'); ?></p>
                <button type="button" id="wdf_stripe_button" class="button button-primary"><?php _e('Jetzt mit Stripe zahlen', 'wdf'); ?></button>
                <p id="wdf_stripe_error" role="alert" hidden><?php _e('Die Stripe-Zahlung konnte nicht gestartet werden. Bitte versuche es erneut.', 'wdf'); ?></p>
            </div>
            <script src="https://js.stripe.com/v3/"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var btn = document.getElementById('wdf_stripe_button');
                var errorMessage = document.getElementById('wdf_stripe_error');
                if(btn){
                    btn.addEventListener('click', async function(e) {
                        e.preventDefault();
                        btn.disabled = true;
                        errorMessage.hidden = true;

                        try {
                            const response = await fetch('<?php echo admin_url('admin-ajax.php?action=wdf_stripe_create_session'); ?>', { method: 'POST' });
                            const session = await response.json();
                            if (!response.ok || !session.id) {
                                throw new Error('Stripe session creation failed.');
                            }

                            const stripe = Stripe('<?php echo esc_js($publishable_key); ?>');
                            const result = await stripe.redirectToCheckout({ sessionId: session.id });
                            if (result.error) {
                                throw result.error;
                            }
                        } catch (error) {
                            errorMessage.hidden = false;
                            btn.disabled = false;
                        }
                    });
                }
            });
            </script>
            <?php
            return ob_get_clean();
        }

        function process_simple() {}
        function process_advanced() {}

        public function process_webhook_event($event) {
            if (!isset($event->type) || !in_array($event->type, array('checkout.session.completed', 'checkout.session.async_payment_succeeded'), true)) {
                return true;
            }

            if (!isset($event->data->object)) {
                return false;
            }

            $checkout_session = $event->data->object;
            if (!isset($checkout_session->payment_status) || $checkout_session->payment_status !== 'paid') {
                return true;
            }

            $metadata = isset($checkout_session->metadata) ? $checkout_session->metadata : new stdClass();
            $pledge_id = !empty($metadata->pledge_id) ? $metadata->pledge_id : (isset($checkout_session->client_reference_id) ? $checkout_session->client_reference_id : '');
            $funder_id = isset($metadata->funder_id) ? absint($metadata->funder_id) : 0;
            $amount = isset($checkout_session->amount_total) ? (int) $checkout_session->amount_total : 0;
            $currency = isset($checkout_session->currency) ? strtoupper(sanitize_text_field($checkout_session->currency)) : '';

            if (!$pledge_id || !$funder_id || $amount <= 0 || !$currency) {
                return false;
            }

            $transaction = array(
                'gross' => $amount / 100,
                'type' => !empty($metadata->type) ? sanitize_text_field($metadata->type) : 'simple',
                'currency_code' => $currency,
                'gateway_public' => $this->public_name,
                'gateway' => $this->plugin_name,
                'status' => __('Komplett', 'wdf'),
                'gateway_msg' => __('Stripe-Zahlung abgeschlossen.', 'wdf'),
                'ipn_id' => isset($checkout_session->payment_intent) ? $checkout_session->payment_intent : $checkout_session->id,
            );

            if (!empty($metadata->reward)) {
                $transaction['reward'] = absint($metadata->reward);
            }

            if (isset($checkout_session->customer_details->email)) {
                $transaction['payer_email'] = sanitize_email($checkout_session->customer_details->email);
            } elseif (isset($checkout_session->customer_email)) {
                $transaction['payer_email'] = sanitize_email($checkout_session->customer_email);
            }

            if (isset($checkout_session->customer_details->name)) {
                $name = explode(' ', sanitize_text_field($checkout_session->customer_details->name), 2);
                $transaction['first_name'] = $name[0];
                if (isset($name[1])) {
                    $transaction['last_name'] = $name[1];
                }
            }

            global $wdf;
            return (bool) $wdf->update_pledge($pledge_id, $funder_id, 'wdf_complete', $transaction);
        }

        function handle_webhook() {
            $settings = get_option('wdf_settings');
            $secret = isset($settings['stripe']['webhook_secret']) ? $settings['stripe']['webhook_secret'] : '';
            $payload = file_get_contents('php://input');
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
            if (!$secret) {
                status_header(400);
                exit('Webhook secret not set.');
            }
            require_once(WDF_PLUGIN_BASE_DIR . '/lib/gateways/vendor/autoload.php');
            try {
                $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $secret);
            } catch (\UnexpectedValueException $e) {
                status_header(400);
                exit('Invalid webhook payload.');
            } catch (\Stripe\Exception\SignatureVerificationException $e) {
                status_header(400);
                exit('Invalid webhook signature.');
            }

            if (!$this->process_webhook_event($event)) {
                status_header(500);
                exit('Webhook event could not be processed.');
            }
            status_header(200);
            exit('Webhook received.');
        }
    }

    wdf_register_gateway_plugin('WDF_Gateway_Stripe', 'stripe', 'Stripe', array('simple','advanced'));

    // Webhook-Handler
    add_action('init', function() {
        if (isset($_GET['wdf_stripe_webhook'])) {
            $gateway = new WDF_Gateway_Stripe();
            $gateway->handle_webhook();
            exit;
        }
    });
}

// AJAX-Handler – Stripe-Aktiv-Prüfung jetzt HIER!
function wdf_stripe_create_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $settings = get_option('wdf_settings');
    if (!isset($settings['active_gateways']['stripe']) || $settings['active_gateways']['stripe'] != '1') {
        wp_send_json_error(['error' => 'Stripe nicht aktiv!'], 400);
    }

    $stripe_settings = isset($settings['stripe']) && is_array($settings['stripe']) ? $settings['stripe'] : array();
    $secret_key = isset($stripe_settings['secret_key']) ? $stripe_settings['secret_key'] : '';
    $amount = isset($_SESSION['wdf_pledge']) && is_numeric($_SESSION['wdf_pledge']) ? (float) $_SESSION['wdf_pledge'] : 0;
    $post_id = isset($_SESSION['wdf_pledge_post_id']) ? absint($_SESSION['wdf_pledge_post_id']) : 0;

    if (!$post_id || $amount <= 0 || !$secret_key) {
        wp_send_json_error(['error' => 'Für den Stripe-Checkout fehlen gültige Zahlungsdaten.'], 400);
    }

    global $wdf;
    if (empty($_SESSION['wdf_pledge_id'])) {
        $_SESSION['wdf_pledge_id'] = $wdf->generate_pledge_id();
    }
    $pledge_id = sanitize_text_field($_SESSION['wdf_pledge_id']);

    if (!class_exists('\Stripe\Stripe')) {
        require_once(WDF_PLUGIN_BASE_DIR . '/lib/gateways/vendor/autoload.php');
    }
    \Stripe\Stripe::setApiKey($secret_key);

    $currency = isset($settings['currency']) ? strtolower(sanitize_text_field($settings['currency'])) : 'eur';
    $methods = isset($stripe_settings['methods']) && is_array($stripe_settings['methods']) ? $stripe_settings['methods'] : ['card'];
    $session_args = [
        'payment_method_types' => array_map('sanitize_text_field', $methods),
        'line_items' => [[
            'price_data' => [
                'currency' => $currency,
                'product_data' => [
                    'name' => __('Spende', 'wdf'),
                ],
                'unit_amount' => (int) round($amount * 100),
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => add_query_arg(
            ['wdf_stripe_success' => 1, 'wdf_pledge_id' => $pledge_id],
            wdf_get_funder_page('confirmation', $post_id)
        ),
        'cancel_url' => add_query_arg('wdf_stripe_cancel', 1, wdf_get_funder_page('checkout', $post_id)),
        'client_reference_id' => $pledge_id,
        'metadata' => [
            'pledge_id' => $pledge_id,
            'funder_id' => (string) $post_id,
            'type' => isset($_SESSION['wdf_type']) ? sanitize_text_field($_SESSION['wdf_type']) : 'simple',
            'reward' => isset($_SESSION['wdf_reward']) ? (string) absint($_SESSION['wdf_reward']) : '',
        ],
    ];

    try {
        $session = \Stripe\Checkout\Session::create($session_args);
        wp_send_json(['id' => $session->id]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        wp_send_json_error(['error' => __('Die Stripe-Checkout-Session konnte nicht erstellt werden.', 'wdf')], 400);
    }
}
?>