<?php
/**
 * Registers a menu page for statistics
 *
 * @package MD_Buyside
 */

/**
 * Class Statistics
 */
class BSD_Statistics {

	/**
	 * Constructor method to initialize the admin page
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_statistics_page' ) );
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
	}

	/**
	 * Adds the statistics page to the WordPress admin menu
	 *
	 * @return void
	 */
	public function add_statistics_page() {
		add_menu_page(
			__( 'Buyside Digest Statistics', 'buyside-digest' ),
			__( 'BSD Statistics', 'buyside-digest' ),
			'manage_options',
			'bsd-statistics',
			array( $this, 'render_statistics_page' ),
			'dashicons-chart-bar',
			2
		);
	}
	
	public function enqueue_scripts() {

		$current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : '';
		if ($current_page !== 'bsd-statistics') {
			return;
		}

		wp_enqueue_script(
			'bsd-statistics-script',
			BSD_ROOT_URL . '/js/bsd-statistics.js',
			array('flatpickr-js'),
			'1.0',
			true,
		);

		// Enqueue Flatpickr CSS
		wp_enqueue_style(
			'flatpickr-css',
			'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
			array(),
			'4.6.13'
		);

		// Enqueue Flatpickr JS
		wp_enqueue_script(
			'flatpickr-js',
			'https://cdn.jsdelivr.net/npm/flatpickr',
			array(),
			'4.6.13',
			true
		);

		$channels = $this->getAllChannels();

		wp_localize_script(
            'bsd-statistics-script',
            'bsdStatistics',
            array(
				'channels' => $channels,
            )
        );
	}

	private function getAllChannels() {
		$ee_options = get_option( 'ee_options' );
		$api_key = isset($ee_options['ee_apikey']) ? $ee_options['ee_apikey'] : '';

		if ( $api_key == '' ) return array();

		$url = 'https://api.smtprelay.co/v3/channels';
		
		$response = wp_remote_get( $url, array(
			'headers' => array(
				'X-ElasticEmail-ApiKey' => $api_key
			),
			'timeout' => 15,
		) );
		
		$data = wp_remote_retrieve_body( $response );
		$channels = json_decode($data);

		return $channels;
	}

	/**
	 * Render email statistics section
	 *
	 * @return void
	 */
	private function render_email_statistics() {
		// Get the selected date from the form, default to today.
		$selected_date = isset( $_GET['email-stats-date'] ) ? sanitize_text_field( $_GET['email-stats-date'] ) : wp_date( 'Y-m-d' );

		// Check if it is exporting to CSV.
		if ( isset( $_GET['export_email_stats_csv'] ) ) {
			bsd_export_email_stats_to_csv( $selected_date );
			exit;
		}

		// API key for Elastic Email.
		$ee_options = get_option( 'ee_options' );
		$api_key    = isset( $ee_options['ee_apikey'] ) ? $ee_options['ee_apikey'] : '';

		// Check if API key is set.
		if ( empty( $api_key ) ) {
			echo '<div class="error"><p>' . esc_html__( 'Elastic Email API key is not set. Please configure the API key.', 'buyside-digest' ) . '</p></div>';
			return;
		}

		// Function to fetch stats for a given channel name.
		$fetch_stats = function( $channel_name ) use ( $api_key ) {
			$api_url  = "https://api.elasticemail.com/v4/statistics/channels/{$channel_name}?api_key={$api_key}";
			$response = wp_remote_get( $api_url );

			// Default statistics if API call fails.
			$stats = array(
				'Sent'    => '-',
				'Opened'  => '-',
				'Clicked' => '-',
			);

			// Parse API response.
			if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
				$body      = wp_remote_retrieve_body( $response );
				$api_stats = json_decode( $body, true );

				if ( $api_stats ) {
					$stats = array(
						'Sent'    => $api_stats['EmailTotal'] ?? 0,
						'Opened'  => $api_stats['Opened'] ?? 0,
						'Clicked' => $api_stats['Clicked'] ?? 0,
					);
				}
			}

			return $stats;
		};

		// Fetch statistics for both Fund and Ticker.
		$fund_stats   = $fetch_stats( 'funds ' . $selected_date );
		$ticker_stats = $fetch_stats( 'tickers ' . $selected_date );
		?>
		<div class="bsd-stats-section">
			<h2><?php esc_html_e( 'Email Statistics', 'buyside-digest' ); ?></h2>
			
			<form method="get" class="bsd-stats-form">
				<input type="hidden" name="page" value="bsd-statistics" />
				<div class="tablenav top">
					<div class="alignleft actions">
						<label for="email-stats-date"><?php esc_html_e( 'Select Date:', 'buyside-digest' ); ?></label>
						<input type="date" id="email-stats-date" name="email-stats-date" value="<?php echo esc_attr( $selected_date ); ?>">
						<input type="submit" class="button" value="<?php esc_attr_e( 'Update Statistics', 'buyside-digest' ); ?>" />
					</div>
					<div class="alignright actions">
						<input type='submit' name='export_email_stats_csv' class='button' value='<?php esc_attr_e( 'Export to CSV', 'buyside-digest' ); ?>' />
					</div>
				</div>
			</form>
			<br>    
			<div class="bsd-stats-grid">
				<!-- Fund Email Metrics Table -->
				<div class="bsd-stats-column">
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th scope="col" class="manage-column"><?php esc_html_e( 'Fund Email Metrics', 'buyside-digest' ); ?></th>
								<th scope="col" class="manage-column"><?php esc_html_e( 'Count', 'buyside-digest' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html_e( 'Sent', 'buyside-digest' ); ?></td>
								<td><?php echo esc_html( $fund_stats['Sent'] ); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e( 'Opened', 'buyside-digest' ); ?></td>
								<td><?php echo esc_html( $fund_stats['Opened'] ); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e( 'Clicked', 'buyside-digest' ); ?></td>
								<td><?php echo esc_html( $fund_stats['Clicked'] ); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<!-- Ticker Email Metrics Table -->
				<div class="bsd-stats-column">
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th scope="col" class="manage-column"><?php esc_html_e( 'Ticker Email Metrics', 'buyside-digest' ); ?></th>
								<th scope="col" class="manage-column"><?php esc_html_e( 'Count', 'buyside-digest' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_html_e( 'Sent', 'buyside-digest' ); ?></td>
								<td><?php echo esc_html( $ticker_stats['Sent'] ); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e( 'Opened', 'buyside-digest' ); ?></td>
								<td><?php echo esc_html( $ticker_stats['Opened'] ); ?></td>
							</tr>
							<tr>
								<td><?php esc_html_e( 'Clicked', 'buyside-digest' ); ?></td>
								<td><?php echo esc_html( $ticker_stats['Clicked'] ); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render fund followers section with pagination and search functionality
	 *
	 * @return void
	 */
	private function render_fund_followers() {
		global $wpdb;

		if (isset($_GET['export_fund_followers_csv'])) {
			bsd_export_fund_followers_to_csv();
			exit;
		}

		$items_per_page = 10;
		$current_page   = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
		$search_query   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$offset         = ( $current_page - 1 ) * $items_per_page;

		// Base query for top funds
		$base_query = "SELECT p.ID as fund_id, COUNT(DISTINCT fs.user_id) as follower_count 
		FROM {$wpdb->posts} p
		LEFT JOIN {$wpdb->prefix}fund_subscriptions fs ON p.ID = fs.fund_id
		WHERE p.post_type = 'funds' AND p.post_status = 'publish'";

		// Add search condition if search query exists
		if ( ! empty( $search_query ) ) {
			$base_query .= $wpdb->prepare( 
				" AND p.post_title LIKE %s", 
				'%' . $wpdb->esc_like( $search_query ) . '%' 
			);
		}

		// Count query for total funds matching the search
		$count_query = "SELECT COUNT(DISTINCT p.ID) 
		FROM {$wpdb->posts} p 
		WHERE p.post_type = 'funds' AND p.post_status = 'publish'";
		
		if ( ! empty( $search_query ) ) {
			$count_query .= $wpdb->prepare( 
				" AND p.post_title LIKE %s", 
				'%' . $wpdb->esc_like( $search_query ) . '%' 
			);
		}
		
		// Complete query with grouping, ordering, and pagination
		$fund_followers_query = $base_query . $wpdb->prepare(
			" GROUP BY fund_id 
			ORDER BY follower_count DESC 
			LIMIT %d OFFSET %d",
			$items_per_page,
			$offset
		);

		// Execute queries
		$top_funds = $wpdb->get_results( $fund_followers_query, ARRAY_A );
		$total_funds = $wpdb->get_var( $count_query );
		$total_pages = ceil( $total_funds / $items_per_page );

		?>
		<div class="bsd-stats-section" style="margin-top: 20px;">
			<h2><?php esc_html_e( 'Fund Followers', 'buyside-digest' ); ?></h2>
			
			<div class="tablenav top">
				<form method="get" class="search-form">
					<label for="fund-search" class="screen-reader-text"><?php esc_html_e( 'Search Funds', 'buyside-digest' ); ?></label>
					<input type="search" id="fund-search" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="<?php esc_attr_e( 'Search funds...', 'buyside-digest' ); ?>" />
					<input type="hidden" name="paged" value="1" />
					<input type="hidden" name="page" value="bsd-statistics" />
					<input type="submit" class="button" value="<?php esc_attr_e( 'Search', 'buyside-digest' ); ?>" />
					<input type="submit" name="export_fund_followers_csv" class="button alignright" value="<?php esc_attr_e('Export to CSV', 'buyside-digest'); ?>" />
				</form>
			</div>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th scope="col" class="manage-column"><?php esc_html_e( 'Fund Name', 'buyside-digest' ); ?></th>
						<th scope="col" class="manage-column"><?php esc_html_e( 'Followers', 'buyside-digest' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ! empty( $top_funds ) ) : ?>
						<?php foreach ( $top_funds as $fund ) : ?>
							<tr>
								<td><?php echo esc_html( get_the_title( $fund['fund_id'] ) ); ?></td>
								<td><?php echo number_format_i18n( $fund['follower_count'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="2"><?php esc_html_e( 'No funds found.', 'buyside-digest' ); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>

			<div class="tablenav bottom">
				<div class="tablenav-pages">
					<span class="displaying-num"><?php printf( esc_html__( '%d items', 'buyside-digest' ), esc_html( $total_funds ) ); ?></span>
					<span class="pagination-links">
						<?php if ( $current_page > 1 ) : ?>
							<a class="first-page button" href="<?php echo esc_url( add_query_arg( array( 'paged' => 1, 's' => $search_query ) ) ); ?>">
								<span class="screen-reader-text"><?php esc_html_e( 'First page', 'buyside-digest' ); ?></span>
								<span aria-hidden="true">«</span>
							</a>
							<a class="prev-page button" href="<?php echo esc_url( add_query_arg( array( 'paged' => $current_page - 1, 's' => $search_query ) ) ); ?>">
								<span class="screen-reader-text"><?php esc_html_e( 'Previous page', 'buyside-digest' ); ?></span>
								<span aria-hidden="true">‹</span>
							</a>
						<?php else : ?>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
						<?php endif; ?>

						<span class="screen-reader-text"><?php esc_html_e( 'Current Page', 'buyside-digest' ); ?></span>
						<span id="table-paging" class="paging-input">
							<span class="tablenav-paging-text">
								<?php echo esc_html( $current_page ); ?> <?php esc_html_e( 'of', 'buyside-digest' ); ?>
								<span class="total-pages"><?php echo esc_html( $total_pages ); ?></span>
							</span>
						</span>

						<?php if ( $current_page < $total_pages ) : ?>
							<a class="next-page button" href="<?php echo esc_url( add_query_arg( array( 'paged' => $current_page + 1, 's' => $search_query ) ) ); ?>">
								<span class="screen-reader-text"><?php esc_html_e( 'Next page', 'buyside-digest' ); ?></span>
								<span aria-hidden="true">›</span>
							</a>
							<a class="last-page button" href="<?php echo esc_url( add_query_arg( array( 'paged' => $total_pages, 's' => $search_query ) ) ); ?>">
								<span class="screen-reader-text"><?php esc_html_e( 'Last page', 'buyside-digest' ); ?></span>
								<span aria-hidden="true">»</span>
							</a>
						<?php else : ?>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
							<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
						<?php endif; ?>
					</span>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render top followed tickers section
	 *
	 * @return void
	 */
	private function render_top_tickers() {

		if (isset($_GET['export_top_tickers_csv'])) {
			bsd_export_top_tickers_to_csv();
			exit;
		}

		$tickers = $this->get_top_followed_tickers();
		?>
		<div class="bsd-stats-section" style="margin-top: 20px;">
			<div style="display: flex; justify-content: space-between; align-items: center;">
				<h2><?php esc_html_e( 'Top 20 Followed Tickers', 'buyside-digest' ); ?></h2>
				<form method="get" class="search-form">
					<input type="hidden" name="page" value="bsd-statistics" />
					<input type="submit" name="export_top_tickers_csv" class="button" value="<?php esc_attr_e('Export to CSV', 'buyside-digest'); ?>" />
				</form>
			</div>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th scope="col" class="manage-column"><?php esc_html_e( 'Ticker', 'buyside-digest' ); ?></th>
						<th scope="col" class="manage-column"><?php esc_html_e( 'Followers', 'buyside-digest' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $tickers as $ticker ) : ?>
						<tr>
							<td><?php echo esc_html( $ticker['title'] ); ?></td>
							<td><?php echo number_format_i18n( $ticker['follower_count'] ); ?></td>
                            </tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Get the top 20 most followed tickers.
	 *
	 * @param int $count Number of top tickers to retrieve.
	 * @return array The top most followed tickers with their follower counts.
	 */
	public function get_top_followed_tickers( $count = 20 ) {
		global $wpdb;

		$tickers = array();

		$query = "
			SELECT ticker_id, COUNT(DISTINCT user_id) as follower_count
			FROM {$wpdb->prefix}ticker_subscriptions
			GROUP BY ticker_id
			ORDER BY follower_count DESC
			LIMIT 100
		";

		$results = $wpdb->get_results( $query, ARRAY_A );

		foreach ( $results as $result ) {
			if ( count( $tickers ) >= $count ) {
				break;
			}

			$ticker_id = $result['ticker_id'];

			$term = get_term_by( 'id', $ticker_id, 'tickers' );

			if ( $term && ! is_wp_error( $term ) ) {
				$result['title'] = $term->name;
				array_push( $tickers, $result );
			}
		}

		return $tickers;
	}

	/**
	 * Renders the content of the statistics page
	 * 
	 * @return void
	 */
	public function render_statistics_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Buyside Digest Statistics', 'buyside-digest' ); ?></h1>
			
			<?php 
			$this->render_email_statistics();
			$this->render_top_tickers();
			$this->render_fund_followers();
			?>
			
		</div>
		<?php
	}
}

// Initialize the class.
$bsd_statistics = new BSD_Statistics();