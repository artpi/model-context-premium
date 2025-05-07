<?php
/**
 * OpenAI API client.
 *
 * @package ModelContextPremium
 */

namespace ModelContextPremium\Vendor\OpenAI;

use WP_Error;

/**
 * Class OpenAI
 *
 * Handles communication with the OpenAI API.
 */
class OpenAI {

	/**
	 * Base URL for the OpenAI API.
	 *
	 * @var string
	 */
	private string $base_url = 'https://api.openai.com/v1/';

	/**
	 * OpenAI API Key.
	 *
	 * @var string
	 */
	private string $api_key;

	/**
	 * Constructor.
	 *
	 * @param string $api_key The OpenAI API key.
	 */
	public function __construct( string $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 * Searches a specific vector store.
	 *
	 * @param string $vector_store_id The ID of the vector store to search.
	 * @param string $search_term     The term to search for.
	 * @param ?array $filters         Optional. Filters to apply to the search. Example: `['file_ids' => ['file_abc123']]`.
	 * @param int    $limit           Optional. The number of results to return. Default 20. Min 1, Max 50.
	 *
	 * @return array|WP_Error The API response as an associative array, or a WP_Error on failure.
	 */
	public function search_vector_store( string $vector_store_id, string $search_term, ?array $filters = null, int $limit = 20 ): array|WP_Error {
		$endpoint = $this->base_url . 'vector_stores/' . rawurlencode( $vector_store_id ) . '/search';

		// Ensure limit is within bounds.
		$max_results = max( 1, min( 50, $limit ) ); // Max is 50 according to docs, default 10.

		$body = [
			'query'             => $search_term,
			'max_num_results' => $max_results,
		];

		if ( ! is_null( $filters ) && ! empty( $filters ) ) {
			$body['filters'] = $filters;
		}

		$args = [
			'method'  => 'POST',
			'headers' => [
				'Authorization' => 'Bearer ' . $this->api_key,
				'Content-Type'  => 'application/json; charset=utf-8',
				'OpenAI-Beta'   => 'assistants=v2',
			],
			'body'    => wp_json_encode( $body ),
			'timeout' => 30, // Seconds.
		];

		$response = wp_remote_post( $endpoint, $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		if ( $response_code >= 300 || $response_code < 200 ) {
			$error_message = 'OpenAI API Error: HTTP ' . $response_code;
			$decoded_body  = json_decode( $response_body, true );
			if ( $decoded_body && isset( $decoded_body['error']['message'] ) ) {
				$error_message .= ' - ' . $decoded_body['error']['message'];
			} elseif ( ! empty( $response_body ) ) {
				$error_message .= ' - ' . $response_body;
			}
			return new WP_Error( 'openai_api_error', $error_message, [ 'status' => $response_code, 'body' => $response_body ] );
		}

		$decoded_response = json_decode( $response_body, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return new WP_Error( 'json_decode_error', 'Failed to decode JSON response from OpenAI API: ' . json_last_error_msg(), [ 'body' => $response_body ] );
		}

		return $decoded_response;
	}
} 