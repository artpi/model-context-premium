<?php
/**
 * Model Context Premium Settings
 *
 * @package MCP
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * MCP_Settings class.
 */
class MCP_Settings {

	/**
	 * Option group.
	 *
	 * @var string
	 */
	private string $option_group = 'mcp_settings_group';

	/**
	 * Option name.
	 *
	 * @var string
	 */
	private string $option_name = 'mcp_settings';

	/**
	 * Settings page slug.
	 *
	 * @var string
	 */
	private string $settings_page_slug = 'mcp-settings';

	/**
	 * Hook suffix for the settings page.
	 *
	 * @var string|false
	 */
	private $settings_hook_suffix = false;

	/**
	 * Initialize the settings.
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Add the admin menu item for the settings page.
	 */
	public function add_admin_menu(): void {
		$this->settings_hook_suffix = add_options_page(
			__( 'Model Context Premium Settings', 'model-context-premium' ),
			__( 'MCP Settings', 'model-context-premium' ),
			'manage_options',
			$this->settings_page_slug,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings, sections, and fields.
	 */
	public function register_settings(): void {
		register_setting(
			$this->option_group,
			$this->option_name,
			array( $this, 'sanitize_settings' )
		);

		add_settings_section(
			'mcp_general_settings_section',
			__( 'General Settings', 'model-context-premium' ),
			null,
			$this->settings_page_slug
		);

		add_settings_field(
			'openai_api_key',
			__( 'OpenAI API Key', 'model-context-premium' ),
			array( $this, 'render_openai_api_key_field' ),
			$this->settings_page_slug,
			'mcp_general_settings_section'
		);
	}

	/**
	 * Render the settings page wrapper.
	 */
	public function render_settings_page(): void {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( $this->option_group );
				do_settings_sections( $this->settings_page_slug );
				submit_button( __( 'Save Settings', 'model-context-premium' ) );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the OpenAI API Key field.
	 */
	public function render_openai_api_key_field(): void {
		$options = get_option( $this->option_name );
		$api_key = isset( $options['openai_api_key'] ) ? $options['openai_api_key'] : '';
		?>
		<input type="password" name="<?php echo esc_attr( $this->option_name ); ?>[openai_api_key]" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
		<p class="description">
			<?php esc_html_e( 'Enter your OpenAI API key. This is required for the plugin to interact with OpenAI services.', 'model-context-premium' ); ?>
		</p>
		<?php
	}

	/**
	 * Sanitize the settings array.
	 *
	 * @param array|null $input The input array from the settings form.
	 * @return array The sanitized array.
	 */
	public function sanitize_settings( ?array $input ): array {
		$new_input = array();

		if ( isset( $input['openai_api_key'] ) ) {
			$new_input['openai_api_key'] = sanitize_text_field( $input['openai_api_key'] );
		}

		// Potentially add more settings to sanitize here.

		return $new_input;
	}
}
 