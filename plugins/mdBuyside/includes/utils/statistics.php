<?php
/**
 * Utilities for statistics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Export the fund followers to a CSV file
 * 
 * @return void
 */
function bsd_export_fund_followers_to_csv() {
    global $wpdb;

    if (ob_get_level()) {
        ob_end_clean();
    }

    $sql_query = "SELECT p.ID as fund_id, p.post_title as fund_name, COUNT(DISTINCT fs.user_id) as follower_count 
                  FROM {$wpdb->posts} p
                  LEFT JOIN {$wpdb->prefix}fund_subscriptions fs ON p.ID = fs.fund_id
                  WHERE p.post_type = 'funds' AND p.post_status = 'publish'
                  GROUP BY p.ID, p.post_title
                  ORDER BY follower_count DESC";

    $results = $wpdb->get_results( $sql_query, ARRAY_A );

    $csv_data = "Fund Name,Follower Count\n";
    foreach ( $results as $result ) {
       $fund_name = '"' . str_replace('"', '""', $result['fund_name']) . '"';
       $csv_data .= "{$fund_name},{$result['follower_count']}\n";
    }

    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment; filename="fund_followers.csv"' );
    echo $csv_data;
    exit;
}


/**
 * Export the top followed tickers to a CSV file
 * 
 * @return void
 */
function bsd_export_top_tickers_to_csv() {
    global $wpdb;

    if (ob_get_level()) {
        ob_end_clean();
    }

    $sql_query = "SELECT ticker_id, COUNT(DISTINCT user_id) as follower_count
                  FROM {$wpdb->prefix}ticker_subscriptions
                  GROUP BY ticker_id
                  ORDER BY follower_count DESC
                  LIMIT 100";

    $results = $wpdb->get_results( $sql_query, ARRAY_A );

    $csv_data = "Ticker,Follower Count\n";

    foreach ( $results as $result ) {
        $term = get_term_by( 'id', $result['ticker_id'], 'tickers' );
        if ( $term ) {
            $term_name = '"' . str_replace('"', '""', $term->name) . '"';
            $csv_data .= "{$term_name},{$result['follower_count']}\n";
        }
    }

    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment; filename="top_tickers.csv"' );
    echo $csv_data;
    exit;
}

/**
 * Export email statistics to a CSV file
 * 
 * @return void
 */
function bsd_export_email_stats_to_csv( $date ) {

    if ( ob_get_level() ) {
        ob_end_clean();
    }

    if ( empty( $date ) ) {
        $date = date( 'Y-m-d' );
    }

    $fund_channel = "funds " . $date;
    $ticker_channel = "tickers " . $date;

    $fund_stats = bsd_get_email_stats( $fund_channel );
    $ticker_stats = bsd_get_email_stats( $ticker_channel );

    $csv_data = "Type,Date,Sent,Opened,Clicked\n";

    $csv_data .= "Funds," . $date . "," . $fund_stats['sent'] . "," . $fund_stats['opened'] . "," . $fund_stats['clicked'] . "\n";
    $csv_data .= "Tickers," . $date . "," . $ticker_stats['sent'] . "," . $ticker_stats['opened'] . "," . $ticker_stats['clicked'] . "\n";

    header( 'Content-Type: text/csv' );
    header( 'Content-Disposition: attachment; filename="email_stats.csv"' );
    echo $csv_data;
    exit;
}


/**
 * Get email statistics from Elastic Email API
 *
 * @param string $channel Channel name
 * @return array Email statistics (sent, opened, clicked)
 */
function bsd_get_email_stats( $channel ) {
    $api_key = get_option( 'ee_options' )['ee_apikey'] ?? '';

    if ( empty( $api_key ) ) {
        return array_fill_keys( ['sent', 'opened', 'clicked'], 0 );
    }

    $api_url = add_query_arg( 
        'api_key', 
        $api_key, 
        "https://api.elasticemail.com/v4/statistics/channels/{$channel}" 
    );
    
    $response = wp_remote_get( $api_url );

    if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
        return array_fill_keys( ['sent', 'opened', 'clicked'], 0 );
    }

    $stats = json_decode( wp_remote_retrieve_body( $response ), true );
    return [
        'sent' => $stats['EmailTotal'] ?? 0,
        'opened' => $stats['Opened'] ?? 0,
        'clicked' => $stats['Clicked'] ?? 0,
    ];
}