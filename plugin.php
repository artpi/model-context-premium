<?php

/**
 * Plugin Name: Model Context Premium
 * Description: Integrates WooCommerce with a Model Context Protocol (MCP) server, allowing AI clients to interact with premium content.
 * Version: 0.1.0
 * Author: Artpi
 * Author URI: https://piszek.com/
 * Text Domain: model-context-premium
 * License: GPL2
 */

// Ensure this file is loaded within WordPress context.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin path constant.
if ( ! defined( 'MCP_PLUGIN_PATH' ) ) {
	define( 'MCP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

// Include the MCP tool class.
require_once MCP_PLUGIN_PATH . 'tools/McpSearchPrivateData.php';

// Instantiate the class to register the tool.
new McpSearchPrivateData();

// Include the OpenAI API client.
require_once MCP_PLUGIN_PATH . 'includes/OpenAI.php';

// Include and register WP-CLI commands if WP-CLI is running.
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once MCP_PLUGIN_PATH . 'includes/CLI/VectorStoreCommand.php';
	WP_CLI::add_command( 'mcp vs', 'ModelContextPremium\CLI\VectorStoreCommand' );
}

// Include and initialize the settings page.
if ( is_admin() ) {
	require_once MCP_PLUGIN_PATH . 'admin/class-mcp-settings.php';
	/**
	 * Initialize MCP Settings.
	 */
	function mcp_init_settings(): void {
		$mcp_settings = new MCP_Settings();
		$mcp_settings->init();
	}
	add_action( 'plugins_loaded', 'mcp_init_settings' );
}
