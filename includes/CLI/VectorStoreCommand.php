<?php
/**
 * WP-CLI command for interacting with OpenAI Vector Stores.
 *
 * @package ModelContextPremium
 */

namespace ModelContextPremium\CLI;

use ModelContextPremium\Vendor\OpenAI\OpenAI;
use WP_Error;

if ( ! class_exists( 'WP_CLI_Command' ) ) {
	return;
}

/**
 * Manages OpenAI Vector Store interactions.
 */
class VectorStoreCommand extends \WP_CLI_Command {

	/**
	 * Searches an OpenAI Vector Store.
	 *
	 * ## OPTIONS
	 *
	 * <vector_store_id>
	 * : The ID of the Vector Store to search (e.g., vs_abc123).
	 *
	 * <query>
	 * : The search query string.
	 *
	 * [--filters=<json_filters>]
	 * : Optional. A JSON string representing filters to apply. Example: '{"file_ids": ["file_xyz789"]}'
	 *
	 * [--limit=<limit>]
	 * : Optional. The maximum number of search results to return. Min 1, Max 100. Defaults to 20.
	 *
	 * [--api_key=<api_key>]
	 * : Optional. The OpenAI API key. If not provided, the command will attempt to use the MCP_OPENAI_API_KEY constant or the 'mcp_openai_api_key' option.
	 *
	 * ## EXAMPLES
	 *
	 *   wp mcp vs search vs_abc123 "What is our return policy?"
	 *   wp mcp vs search vs_def456 "Latest product updates" --limit=5
	 *   wp mcp vs search vs_ghi789 "Company history" --filters='{"file_ids": ["file_jkl012"]}'
	 *   wp mcp vs search vs_mno345 "Support contact" --api_key=sk-yourkeyhere
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Associative arguments (options).
	 */
	public function search( array $args, array $assoc_args ): void {
		\WP_CLI::debug( 'Starting vector store search command.' );
		list( $vector_store_id, $query ) = $args;
		\WP_CLI::debug( sprintf( 'Vector Store ID: %s, Query: %s', $vector_store_id, $query ) );

		$api_key_from_option = null;
		$mcp_settings = get_option( 'mcp_settings' );
		if ( ! empty( $mcp_settings ) && isset( $mcp_settings['openai_api_key'] ) ) {
			$api_key_from_option = $mcp_settings['openai_api_key'];
			\WP_CLI::debug( 'Retrieved API key from mcp_settings option.' );
		} else {
			\WP_CLI::debug( 'API key not found in mcp_settings option.' );
		}

		$api_key = $assoc_args['api_key'] ?? ( defined( 'MCP_OPENAI_API_KEY' ) ? \MCP_OPENAI_API_KEY : $api_key_from_option );

		if ( defined( 'MCP_OPENAI_API_KEY' ) && ! $assoc_args['api_key'] ) {
			\WP_CLI::debug( 'Using API key from MCP_OPENAI_API_KEY constant.' );
		}

		if ( empty( $api_key ) ) {
			\WP_CLI::debug( 'API key is empty. Erroring out.' );
			\WP_CLI::error(
				"OpenAI API key not found. Please provide it via the --api_key option, define the MCP_OPENAI_API_KEY constant, or set the 'mcp_openai_api_key' option in MCP Settings."
			);
			return;
		}
		\WP_CLI::debug( 'API key successfully retrieved.' );

		$filters = null;
		if ( isset( $assoc_args['filters'] ) ) {
			\WP_CLI::debug( sprintf( 'Attempting to decode filters: %s', $assoc_args['filters'] ) );
			$filters = json_decode( $assoc_args['filters'], true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				\WP_CLI::debug( 'Filter decoding failed: ' . json_last_error_msg() );
				\WP_CLI::error( 'Invalid JSON provided for --filters: ' . json_last_error_msg() );
				return;
			}
			\WP_CLI::debug( 'Filters decoded successfully.' );
		} else {
			\WP_CLI::debug( 'No filters provided.' );
		}

		$limit = $assoc_args['limit'] ?? 20;
		$limit = (int) $limit;
		\WP_CLI::debug( sprintf( 'Limit set to: %d', $limit ) );
		if ( $limit < 1 || $limit > 100 ) {
			\WP_CLI::warning( 'Limit must be between 1 and 100. Using default of 20.' );
			$limit = 20;
			\WP_CLI::debug( sprintf( 'Limit adjusted to default: %d', $limit ) );
		}

		try {
			\WP_CLI::debug( 'Initializing OpenAI client.' );
			$openai_client = new OpenAI( $api_key );
			\WP_CLI::debug( 'Calling OpenAI search_vector_store method.' );
			$results       = $openai_client->search_vector_store( $vector_store_id, $query, $filters, $limit );
			\WP_CLI::debug( 'Received response from OpenAI API.' );

			if ( is_wp_error( $results ) ) {
				/** @var WP_Error $results */
				$error_message = $results->get_error_message();
				$error_data = $results->get_error_data();
				\WP_CLI::debug( sprintf( 'OpenAI API Error: %s. Data: %s', $error_message, wp_json_encode( $error_data ) ) );
				\WP_CLI::error(
					'OpenAI API Error: ' . $error_message .
					( $error_data ? ' Details: ' . wp_json_encode( $error_data ) : '' )
				);
				return;
			}

			\WP_CLI::debug( 'API call successful. Processing results.' );

			if ( empty( $results['data'] ) ) {
				\WP_CLI::line( 'No results found.' );
				\WP_CLI::debug( 'No data in results array.' );
			} else {
				\WP_CLI::debug( sprintf( 'Formatting %d items for output.', count( $results['data'] ) ) );

				$search_query_display = $results['search_query'] ?? $query;
				if ( is_array( $search_query_display ) ) {
					$search_query_display = wp_json_encode( $search_query_display );
				}
				\WP_CLI::line( sprintf( 'Search query: %s', $search_query_display ) );

				\WP_CLI\Utils\format_items( 'json', $results['data'], [ 'file_id', 'filename', 'score', 'content' ] );
				\WP_CLI::line( 'Has more: ' . ( $results['has_more'] ? 'Yes' : 'No' ) );
				if ( $results['next_page'] ) {
					\WP_CLI::line( 'Next page cursor: ' . $results['next_page'] );
				}
				\WP_CLI::debug( 'Finished formatting and displaying results.' );
			}
			\WP_CLI::success( 'Vector store search completed.' );

		} catch ( \Exception $e ) {
			\WP_CLI::debug( 'Exception caught: ' . $e->getMessage() );
			\WP_CLI::error( 'An unexpected error occurred: ' . $e->getMessage() );
		}
	}
}
 