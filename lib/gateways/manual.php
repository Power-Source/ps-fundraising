<?php
if(!class_exists('WDF_Gateway_Manual')) {
	class WDF_Gateway_Manual extends WDF_Gateway {

		// Private gateway slug. Lowercase alpha (a-z) and dashes (-) only please!
		public $plugin_name = 'manual';

		// Name of your gateway, for the admin side.
		public $admin_name = '';

		// Public name of your gateway, for lists and such.
		public $public_name = '';

		// Whether or not ssl is needed for checkout page
		public $force_ssl = false;

		// An array of allowed payment types (simple, advanced)
		public $payment_types = 'simple';

		// If you are redirecting to a 3rd party make sure this is set to true
		public $skip_form = false;

		// Allow recurring payments with your gateway
		public $allow_reccuring = false;

		function on_creation() {
			$this->public_name = $this->admin_name = __('Überweisung','wdf');
		}

		function payment_form() {
			$content = '<div class="wdf_manual_payment_form wdf_payment_form">';

			$content .= '<p class="wdf_manual_payment_form_basic_message wdf_payment_form_basic_message">'.__('Bitte fülle alle Details aus um den Vorgang abzuschließen','wdf').'</p>';

			$content .= '<p class="wdf_manual_payment_form_basic_info wdf_payment_form_basic_info">';
				$content .= '<label for="first_name class="wdf_first_name">'.__('Dein Vorname','wdf').':</label><br />';
				$content .= '<input type="text" class="wdf_first_name" name="first_name" value="'.( isset($_POST['first_name']) ? esc_attr($_POST['first_name']) : '' ).'" /><br />';
				$content .= '<label for="last_name class="wdf_last_name">'.__('Dein Familienname','wdf').':</label><br />';
				$content .= '<input type="text" class="wdf_last_name" name="last_name" value="'.( isset($_POST['last_name']) ? esc_attr($_POST['last_name']) : '' ).'" /><br />';
				$content .= '<label for="e-mail" class="wdf_email">'.__('Deine E-mail Adresse','wdf').':</label><br />';
				$content .= '<input type="text" class="wdf_email" name="e-mail" value="'.( isset($_POST['e-mail']) ? esc_attr($_POST['e-mail']) : '') .'" />';
			$content .= '</p>';

			$funder_id = get_the_ID();
			if(get_post_meta($funder_id,'wdf_collect_address', true)) {
				$collect_address_message = get_post_meta($funder_id,'wdf_collect_address_message', true);
				if($collect_address_message)
					$content .= '<p class="wdf_manual_payment_form_address_message wdf_payment_form_address_message">'.$collect_address_message.'</p>';

				$content .= '<p class="wdf_manual_payment_form_address_info wdf_payment_form_address_info">';
					$collect_address_country = get_post_meta($funder_id,'wdf_collect_address_country', true);
					if($collect_address_country){
						$content .= '<label for="country" class="wdf_country">'.__('Land','wdf').':</label><br />';
						$content .= '<input type="text" class="wdf_country" name="country" value="'.( isset($_POST['country']) ? esc_attr($_POST['country']) : '') .'" /><br />';
					}
					$content .= '<label for="address1" class="wdf_address1">'.__('Addresse','wdf').' <small>'.__('(Straße, Postfach, Firmenname, c/o)','wdf').'</small>:</label><br />';
					$content .= '<input type="text" class="wdf_address1" name="address1" value="'.( isset($_POST['address1']) ? esc_attr($_POST['address1']) : '') .'" /><br />';
					$content .= '<label for="address2" class="wdf_address2">'.__('Addresse 2','wdf').' <small>'.__('(Wohnung, Suite, Einheit, Gebäude, Etage usw.)','wdf').'</small>:</label><br />';
					$content .= '<input type="text" class="wdf_address2" name="address2" value="'.( isset($_POST['address2']) ? esc_attr($_POST['address2']) : '') .'" /><br />';
					$content .= '<label for="city" class="wdf_city">'.__('Stadt','wdf').':</label><br />';
					$content .= '<input type="text" class="wdf_city" name="city" value="'.( isset($_POST['city']) ? esc_attr($_POST['city']) : '') .'" /><br />';
					$content .= '<label for="state" class="wdf_state">'.__('Bundesland','wdf').':</label><br />';
					$content .= '<input type="text" class="wdf_state" name="state" value="'.( isset($_POST['state']) ? esc_attr($_POST['state']) : '') .'" /><br />';
					$content .= '<label for="zip" class="wdf_zip">'.__('Postleitzahl','wdf').':</label><br />';
					$content .= '<input type="text" class="wdf_zip" name="zip" value="'.( isset($_POST['zip']) ? esc_attr($_POST['zip']) : '') .'" />';
				$content .= '</p>';
			}

			$content .= '</div>';
			return $content;
		}

		function process_simple() {
			if( !empty($_POST['first_name']) && !empty($_POST['last_name']) &&
				!empty($_POST['e-mail']) && preg_match("/^[-+\\.0-9=a-z_]+@([-0-9a-z]+\\.)+([0-9a-z]){2,4}$/i", $_POST['e-mail']) &&
				(isset($_POST['city']) && !empty($_POST['address1']) && !empty($_POST['city'])) || !isset($_POST['city']) &&
				(isset($_POST['country']) && !empty($_POST['country'])) || !isset($_POST['country'])
			) {
				global $wdf;
				$settings = get_option('wdf_settings');
				$funder_id = $_SESSION['funder_id'];

				if($funder = get_post($funder_id) ){
					$pledge_id = $wdf->generate_pledge_id();
					$this->return_url =  add_query_arg('pledge_id', $pledge_id, wdf_get_funder_page('confirmation',$funder->ID));

					$_SESSION['wdf_pledge_id'] = $pledge_id;

					$settings = get_option('wdf_settings');

					$transaction = array();

					$transaction['gross'] = $_SESSION['wdf_pledge'];
					$transaction['type'] = 'simple';
					$transaction['currency_code'] = ( isset($settings['currency']) ? $settings['currency'] : 'USD');
					$transaction['first_name'] = (isset($_POST['first_name']) ? $_POST['first_name'] : '' );
					$transaction['last_name'] = (isset($_POST['last_name']) ? $_POST['last_name'] : '' );
					$transaction['payer_email'] = (isset($_POST['e-mail']) ? $_POST['e-mail'] : '' );
					$transaction['gateway_public'] = $this->public_name;
					$transaction['gateway'] = $this->plugin_name;
					$status = 'wdf_pending';
					$transaction['status'] = __('Ausstehend','wdf');
					$transaction['gateway_msg'] = __('Warte auf den Eingang der Überweisung.','wdf');
					$transaction['reference'] = $this->get_payment_reference($pledge_id, $settings);

					if(isset($_SESSION['wdf_reward']))
						$transaction['reward'] = $_SESSION['wdf_reward'];

					$collect_address = get_post_meta($funder_id,'wdf_collect_address', true);
					if($collect_address) {
						$transaction['country'] = (isset($_POST['country']) ? $_POST['country'] : '' );
						$transaction['address1'] = (isset($_POST['address1']) ? $_POST['address1'] : '' );
						$transaction['address2'] = (isset($_POST['address2']) ? $_POST['address2'] : '' );
						$transaction['city'] = (isset($_POST['city']) ? $_POST['city'] : '' );
						$transaction['state'] = (isset($_POST['state']) ? $_POST['state'] : '' );
						$transaction['zip'] = (isset($_POST['zip']) ? $_POST['zip'] : '' );
					}


					$wdf->update_pledge( $pledge_id, $funder_id, $status, $transaction);

					if(!headers_sent()) {
						wp_redirect($this->return_url);
						exit;
					}

				} else {
					$_POST['wdf_step'] = 'gateway';
					//No $_SESSION['funder_id'] was passed to this function.
					$this->create_gateway_error(__('Spendenaktion konnte nicht ermittelt werden','wdf'));
				}
			} else {
				$_POST['wdf_step'] = 'gateway';
				$this->create_gateway_error(__('Stelle sicher, dass alle Details korrekt ausgefüllt sind.','wdf'));
			}
		}
		function process_advanced() {
			$this->process_simple();
		}
		function confirm() {
		}
		function payment_info( $content, $transaction ) {
			$settings = get_option('wdf_settings');
			$manual_settings = isset($settings['manual']) && is_array($settings['manual']) ? $settings['manual'] : array();
			$account_name = isset($manual_settings['account_name']) ? $manual_settings['account_name'] : '';
			$iban = isset($manual_settings['iban']) ? strtoupper(preg_replace('/\s+/', '', $manual_settings['iban'])) : '';
			$bic = isset($manual_settings['bic']) ? strtoupper(preg_replace('/\s+/', '', $manual_settings['bic'])) : '';
			$bank_name = isset($manual_settings['bank_name']) ? $manual_settings['bank_name'] : '';
			$reference = isset($transaction['reference']) ? $transaction['reference'] : '';
			$amount = isset($transaction['gross']) && is_numeric($transaction['gross']) ? number_format((float) $transaction['gross'], 2, '.', '') : '';
			$currency = isset($transaction['currency_code']) ? strtoupper($transaction['currency_code']) : '';

			$content = '<div class="manual_transaction_info">';
			$content .= '<h3>' . esc_html__('Überweisung ausstehend', 'wdf') . '</h3>';
			$content .= '<p>' . esc_html__('Bitte überweise Deine Unterstützung. Sie wird nach Zahlungseingang bestätigt.', 'wdf') . '</p>';
			$content .= '<dl>';
			if ($account_name) {
				$content .= '<dt>' . esc_html__('Kontoinhaber', 'wdf') . '</dt><dd>' . esc_html($account_name) . '</dd>';
			}
			if ($bank_name) {
				$content .= '<dt>' . esc_html__('Bank', 'wdf') . '</dt><dd>' . esc_html($bank_name) . '</dd>';
			}
			if ($iban) {
				$content .= '<dt>' . esc_html__('IBAN', 'wdf') . '</dt><dd><code>' . esc_html($iban) . '</code></dd>';
			}
			if ($bic) {
				$content .= '<dt>' . esc_html__('BIC', 'wdf') . '</dt><dd><code>' . esc_html($bic) . '</code></dd>';
			}
			if ($amount !== '') {
				$content .= '<dt>' . esc_html__('Betrag', 'wdf') . '</dt><dd>' . esc_html($amount . ' ' . $currency) . '</dd>';
			}
			if ($reference) {
				$content .= '<dt>' . esc_html__('Verwendungszweck', 'wdf') . '</dt><dd><code>' . esc_html($reference) . '</code></dd>';
			}
			$content .= '</dl>';

			if (!$account_name || !$iban) {
				$content .= '<p class="manual_transaction_missing_details">' . esc_html__('Die Bankverbindung ist noch nicht vollständig eingerichtet. Bitte kontaktiere den Veranstalter für die Überweisungsdaten.', 'wdf') . '</p>';
			}

			if ($currency === 'EUR' && $this->can_generate_epc_qr($iban, $account_name) && $amount !== '' && $reference) {
				$payload = implode("\n", array(
					'BCD',
					'001',
					'1',
					'SCT',
					preg_match('/^[A-Z0-9]{8}([A-Z0-9]{3})?$/', $bic) ? $bic : '',
					substr($account_name, 0, 70),
					$iban,
					$currency . $amount,
					'',
					'',
					substr($reference, 0, 140),
				));

				if (is_readable(WDF_PLUGIN_BASE_DIR . '/lib/gateways/vendor/autoload.php')) {
					require_once WDF_PLUGIN_BASE_DIR . '/lib/gateways/vendor/autoload.php';
					$qr_options = new \chillerlan\QRCode\QROptions(array(
						'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_MARKUP_SVG,
						'imageBase64' => true,
						'scale' => 6,
						'eccLevel' => \chillerlan\QRCode\QRCode::ECC_M,
					));
					$qr_code = (new \chillerlan\QRCode\QRCode($qr_options))->render($payload);
					$content .= '<p class="manual_transaction_qr"><img src="' . esc_attr($qr_code) . '" width="240" height="240" alt="' . esc_attr__('SEPA-Überweisungs-QR-Code', 'wdf') . '" /><br />';
					$content .= '<span>' . esc_html__('Mit der Banking-App scannen, um Empfänger, Betrag und Verwendungszweck zu übernehmen.', 'wdf') . '</span></p>';
				}
			}

			$after_info = isset($manual_settings['after_info']) ? html_entity_decode(stripslashes($manual_settings['after_info']), ENT_QUOTES, get_option('blog_charset')) : '';
			if ($after_info) {
				$content .= '<div class="manual_transaction_extra_info">' . wp_kses_post($after_info) . '</div>';
			}
			$content .= '</div>';
			return $content;
		}

		private function get_payment_reference($pledge_id, $settings) {
			$manual_settings = isset($settings['manual']) && is_array($settings['manual']) ? $settings['manual'] : array();
			$prefix = isset($manual_settings['reference_prefix']) ? sanitize_text_field($manual_settings['reference_prefix']) : '';
			$reference = trim(substr($prefix, 0, 100) . ' ' . $pledge_id);

			return substr($reference, 0, 140);
		}

		private function can_generate_epc_qr($iban, $account_name) {
			if (!$account_name || !preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{11,30}$/', $iban)) {
				return false;
			}

			$iban_check = substr($iban, 4) . substr($iban, 0, 4);
			$iban_check = preg_replace_callback('/[A-Z]/', function($match) {
				return (string) (ord($match[0]) - 55);
			}, $iban_check);

			$remainder = 0;
			foreach (str_split($iban_check) as $digit) {
				$remainder = (int) (($remainder . $digit) % 97);
			}

			return $remainder === 1;
		}
		function handle_ipn() {
		}
		function execute_payment($type, $pledge, $transaction) {
		}

		function admin_settings() {
			$settings = get_option('wdf_settings');
			$manual = isset($settings['manual']) && is_array($settings['manual']) ? $settings['manual'] : array();
		?>
			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row">
						<?php _e('Zahlungsstatus', 'wdf'); ?>
					</th>
					<td>
						<strong><?php _e('Ausstehend', 'wdf'); ?></strong>
						<p class="description"><?php _e('Überweisungen werden zunächst als ausstehend gespeichert. Markiere sie nach dem Zahlungseingang in der Unterstützung als „Komplett“.', 'wdf'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wdf_manual_account_name"><?php _e('Kontoinhaber', 'wdf'); ?></label></th>
					<td><input id="wdf_manual_account_name" type="text" class="regular-text" name="wdf_settings[manual][account_name]" value="<?php echo esc_attr(isset($manual['account_name']) ? $manual['account_name'] : ''); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wdf_manual_iban"><?php _e('IBAN', 'wdf'); ?></label></th>
					<td><input id="wdf_manual_iban" type="text" class="regular-text" name="wdf_settings[manual][iban]" value="<?php echo esc_attr(isset($manual['iban']) ? $manual['iban'] : ''); ?>" autocomplete="off" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wdf_manual_bic"><?php _e('BIC (optional)', 'wdf'); ?></label></th>
					<td><input id="wdf_manual_bic" type="text" class="regular-text" name="wdf_settings[manual][bic]" value="<?php echo esc_attr(isset($manual['bic']) ? $manual['bic'] : ''); ?>" autocomplete="off" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wdf_manual_bank_name"><?php _e('Name der Bank (optional)', 'wdf'); ?></label></th>
					<td><input id="wdf_manual_bank_name" type="text" class="regular-text" name="wdf_settings[manual][bank_name]" value="<?php echo esc_attr(isset($manual['bank_name']) ? $manual['bank_name'] : ''); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wdf_manual_reference_prefix"><?php _e('Verwendungszweck-Präfix', 'wdf'); ?></label></th>
					<td>
						<input id="wdf_manual_reference_prefix" type="text" class="regular-text" name="wdf_settings[manual][reference_prefix]" value="<?php echo esc_attr(isset($manual['reference_prefix']) ? $manual['reference_prefix'] : __('Spende', 'wdf')); ?>" />
						<p class="description"><?php _e('Die eindeutige Spendenreferenz wird automatisch angehängt und im EPC-QR-Code verwendet.', 'wdf'); ?></p>
					</td>
				</tr>
				<tr id="wdf_settings_manual_after_info">
					<th scope="row">
						<label for="wdf_manual_after_info"><?php _e('Zusätzliche Hinweise nach der Spende', 'wdf'); ?></label>
					</th>
					<td>
						<textarea id="wdf_manual_after_info" name="wdf_settings[manual][after_info]" class="widefat" rows="5"><?php echo esc_textarea(isset($manual['after_info']) ? html_entity_decode(stripslashes($manual['after_info']), ENT_QUOTES, get_option('blog_charset')) : ''); ?></textarea>
						<p class="description"><?php _e('EPC-QR wird lokal erzeugt, sofern Kontoinhaber und eine gültige IBAN eingetragen sind. Es werden keine Bankdaten an einen QR-Dienst übermittelt.', 'wdf'); ?></p>
					</td>
				</tr>
				</tbody>
			</table>
		<?php
		}
		function save_gateway_settings() {

			if( isset($_POST['wdf_settings']['manual']) ) {
				$posted_manual = wp_unslash($_POST['wdf_settings']['manual']);
				$settings = get_option('wdf_settings');
				$settings = is_array($settings) ? $settings : array();
				$manual = isset($settings['manual']) && is_array($settings['manual']) ? $settings['manual'] : array();
				$manual['account_name'] = sanitize_text_field(isset($posted_manual['account_name']) ? $posted_manual['account_name'] : '');
				$manual['iban'] = strtoupper(preg_replace('/\s+/', '', sanitize_text_field(isset($posted_manual['iban']) ? $posted_manual['iban'] : '')));
				$manual['bic'] = strtoupper(preg_replace('/\s+/', '', sanitize_text_field(isset($posted_manual['bic']) ? $posted_manual['bic'] : '')));
				$manual['bank_name'] = sanitize_text_field(isset($posted_manual['bank_name']) ? $posted_manual['bank_name'] : '');
				$manual['reference_prefix'] = sanitize_text_field(isset($posted_manual['reference_prefix']) ? $posted_manual['reference_prefix'] : __('Spende', 'wdf'));
				$manual['after_info'] = wp_kses_post(isset($posted_manual['after_info']) ? $posted_manual['after_info'] : '');
				$manual['status'] = 'wdf_pending';
				$settings['manual'] = $manual;
				update_option('wdf_settings',$settings);
			}
		}

	}
wdf_register_gateway_plugin('WDF_Gateway_Manual', 'manual', 'Überweisung', array('simple','standard','advanced'));
}
?>
