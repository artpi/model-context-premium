<?php

/**
 * Plugin Name: Template
 * Description: Artpi WordPress plugin template.
 * Version: 0.0.1
 * Author: Artpi
 * Author URI: https://piszek.com/
 * Text Domain: template
 * License: GPL2
 */

// Ensure this file is loaded within WordPress context.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include the MCP tool class.
require_once __DIR__ . '/tools/McpSearchPrivateData.php';

// Instantiate the class to register the tool.
new McpSearchPrivateData();
