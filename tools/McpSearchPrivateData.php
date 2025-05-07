<?php
declare( strict_types=1 );

use Automattic\WordpressMcp\Core\RegisterMcpTool;

/**
 * Class McpSearchPrivateData
 *
 * Registers a tool to search private data (dummy implementation for Phase 1).
 *
 * @package Artpi\Template\Tools
 */
class McpSearchPrivateData {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wordpress_mcp_init', array( $this, 'register_tools' ) );
	}

	/**
	 * Register the tools.
	 */
	public function register_tools(): void {
		new RegisterMcpTool(
			array(
				'name'                 => 'search_private_data',
				'description'          => 'Exposes a dummy search interface for now, returning static content.',
				'type'                 => 'read',
				'inputSchema'          => array(
					'type'       => 'object',
					'properties' => new stdClass(),
					'required'   => new stdClass(),
				),
				'callback'             => array( $this, 'search_private_data_callback' ),
				'permissions_callback' => array( $this, 'permissions_callback' ),
			)
		);
	}

	/**
	 * Callback for the search_private_data tool.
	 *
	 * Returns a static string as a placeholder.
	 *
	 * @return array An array containing the dummy search result.
	 */
	public function search_private_data_callback(): array {
		return array(
			'result' => 'This is a dummy response from search_private_data. Premium content search will be implemented here.',
		);
	}

	/**
	 * Permissions callback for the search_private_data tool.
	 *
	 * For Phase 1, this allows unauthenticated access as per PRD.
	 *
	 * @return bool True if the current user has permission, false otherwise.
	 */
	public function permissions_callback(): bool {
		return true;
	}
} 