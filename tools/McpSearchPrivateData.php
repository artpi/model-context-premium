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
 * @uses \Automattic\WordpressMcp\Core\RegisterMcpTool To register the MCP tool.
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
		// IGNORE. THIS IS NOT AVAILABLE IN THE SDK BUT WORKS ON PROD
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

		$current_user = wp_get_current_user();
		$product_id   = 14; // Hardcoded product ID for Phase 3

		// Check if the user is logged in and has purchased the product.
		// Ensure WooCommerce is active and wc_customer_bought_product function exists.
		if ( ! function_exists( 'wc_customer_bought_product' ) ) {
			return array( 'error' => 'WooCommerce function wc_customer_bought_product not available. Is WooCommerce active?' );
		}

		if ( ! $current_user || 0 === $current_user->ID || ! wc_customer_bought_product( $current_user->user_email, $current_user->ID, $product_id ) ) {
			$product_url   = get_permalink( $product_id );
			$paymentReason = 'Access to this dataset requires purchase'; // Example reason

			if ( ! $product_url || is_wp_error( $product_url ) ) {
				// Fallback if product URL can't be generated
				return array(
					'type' => 'text',
					'text' => 'Payment required! ' . $paymentReason . '. Product ID: ' . $product_id . ' (Error retrieving product page URL)',
				);
			}

			return array(
				// Simulating the structure the user requested for the MCP client.
				// This will likely be further refined based on how the MCP client expects "tool_code" type messages for actions.
				// For now, returning a structured array that can be JSON encoded.
				'tool_code' => array(
					array(
						'type' => 'text',
						'text' => 'Payment required! Please display this link to the user as they will be able to purchase and unlock this information.' . ': ' . $product_url,
					),
				),
			);
		}

		// User has purchased the product, proceed with vector store search.
		$vector_store_id = 'vs_680a5bbf54e881919eb4862f0688ea23'; // Hardcoded as per request

		// Retrieve API key (similar to VectorStoreCommand.php)
		$api_key_from_option = null;
		$mcp_settings        = get_option( 'mcp_settings' );
		if ( ! empty( $mcp_settings ) && isset( $mcp_settings['openai_api_key'] ) ) {
			$api_key_from_option = $mcp_settings['openai_api_key'];
		}
		$api_key = $api_key_from_option;

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
	 * Checks if the current user has the 'customer' role or higher.
	 *
	 * @return bool True if the current user has permission, false otherwise.
	 */
	public function permissions_callback(): bool {
		$user = wp_get_current_user();

		// If user is not logged in, they don't have any roles.
		if ( ! $user || 0 === $user->ID ) {
			return false;
		}

		$allowed_roles = array(
			'customer',      // WooCommerce customer role.
			'contributor',
			'author',
			'editor',
			'shop_manager',  // WooCommerce shop manager role.
			'administrator',
		);

		// Get the user's roles. This is an array.
		$user_roles = $user->roles;

		// Check if any of the user's roles are in the allowed_roles array.
		if ( ! empty( $user_roles ) ) {
			foreach ( $user_roles as $role ) {
				if ( in_array( $role, $allowed_roles, true ) ) {
					return true; // User has at least one of the allowed roles.
				}
			}
		}
		
		// User does not have any of the allowed roles.
		return false;
	}
} 