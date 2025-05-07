<?php
declare( strict_types=1 );
use Automattic\WordpressMcp\Core\RegisterMcpTool;
use ModelContextPremium\Vendor\OpenAI\OpenAI;
use WP_Error;

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
				'description'          => 'Search secret knowledge base.',
				'type'                 => 'read',
				'inputSchema'          => array(
					'type'       => 'object',
					'properties' => array(
						'query' => array(
							'type'        => 'string',
							'description' => 'The search query for the vector store (passed via query).',
						),
					),
					'required'   => array( 'query' ),
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
	 * @param array $args Arguments passed to the tool, including the 'query'.
	 * @return array An array containing the search result or an error.
	 */
	public function search_private_data_callback( array $args ): array {
		$search_term = $args['query'] ?? '';
		if ( empty( $search_term ) ) {
			return array( 'error' => 'Search term (from query parameter) is missing.' );
		}

		$vector_store_id = 'vs_680a5bbf54e881919eb4862f0688ea23'; // Hardcoded as per request

		// Retrieve API key (similar to VectorStoreCommand.php)
		$api_key_from_option = null;
		$mcp_settings        = get_option( 'mcp_settings' );
		if ( ! empty( $mcp_settings ) && isset( $mcp_settings['openai_api_key'] ) ) {
			$api_key_from_option = $mcp_settings['openai_api_key'];
		}
		$api_key = defined( 'MCP_OPENAI_API_KEY' ) ? \MCP_OPENAI_API_KEY : $api_key_from_option;

		if ( empty( $api_key ) ) {
			return array( 'error' => 'OpenAI API key not configured.' );
		}

		try {
			$openai_client = new OpenAI( $api_key );
			// Using null for filters and a default limit of 5 for now.
			// The schema for filters and limit can be expanded if needed.
			$results       = $openai_client->search_vector_store( $vector_store_id, $search_term, null, 10 );

			if ( is_wp_error( $results ) ) {
				/** @var WP_Error $results */
				return array(
					'error'   => 'OpenAI API Error: ' . $results->get_error_message(),
					'details' => $results->get_error_data(),
				);
			}

			if ( empty( $results['data'] ) ) {
				return array( 'result' => 'No results found.' );
			}

			// Simplified result for now. Can be expanded to match CLI output if needed.
			return array(
				'search_query' => $results['search_query'] ?? $search_term,
				'data'         => $results['data'],
				'has_more'     => $results['has_more'] ?? false,
				'next_page'    => $results['next_page'] ?? null,
			);

		} catch ( \Exception $e ) {
			return array( 'error' => 'An unexpected error occurred: ' . $e->getMessage() );
		}
	}

	/**
	 * Permissions callback for the search_private_data tool.
	 *
	 * For Phase 1, this allows unauthenticated access as per PRD.
	 *
	 * @return bool True if the current user has permission, false otherwise.
	 */
	public function permissions_callback(): bool {
		// For now, restrict to administrators. This should be updated for Phase 2.
		return current_user_can( 'manage_options' );
	}
} 