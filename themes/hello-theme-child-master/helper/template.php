<?php 

class BSD_Template {

    public static function get_letter_quarter( $letter_id ) {
        if ( ! $letter_id || ! is_numeric( $letter_id ) ) {
            return '-';
        }
    
        $terms = wp_get_post_terms( $letter_id, 'quarter' );
    
        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return '-';
        }
    
        return isset( $terms[0]->name ) ? $terms[0]->name : '';
    }

    public static function get_letter_fund_name( $letter_id ) {
        $fund_name =  get_post_meta( $letter_id, 'fund_text_meta_key', true );

        return ! empty( $fund_name ) ? $fund_name : '-';
    }

    public static function get_letter_fund_permalink( $letter_id ) {
        if ( ! $letter_id || ! is_numeric( $letter_id ) ) {
            return '';
        }

        global $wpdb;
    
        $table_name = $wpdb->prefix . "jet_rel_default";
    
        $fund_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT parent_object_id FROM $table_name WHERE child_object_id = %d",
                $letter_id
            )
        );

        if ( count( $fund_ids ) <= 0 ) {
            return '';
        }

        $fund_id = $fund_ids[0];

        return get_the_permalink( $fund_id );
    }

    public static function get_letter_key_person( $letter_id ) {
        if ( ! $letter_id || ! is_numeric( $letter_id ) ) {
            return '-';
        }
    
        $terms = wp_get_post_terms( $letter_id, 'key-person' );
    
        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return '-';
        }
    
        return isset( $terms[0]->name ) ? $terms[0]->name : '';
    }

    public static function get_letter_QTD( $letter_id ) {
        $fund_name =  get_post_meta( $letter_id, 'bsd-letter-qtd', true );

        return ! empty( $fund_name ) ? $fund_name : '-';
    }

    public static function get_letter_YTD( $letter_id ) {
        $fund_name =  get_post_meta( $letter_id, 'bsd-letter-ytd', true );

        return ! empty( $fund_name ) ? $fund_name : '-';
    }

    public static function get_letter_tickers( $letter_id ) {
        if ( ! $letter_id || ! is_numeric( $letter_id ) ) {
            return array();
        }
    
        $terms = wp_get_post_terms( $letter_id, 'tickers' );
    
        if ( is_wp_error( $terms ) || empty( $terms ) ) {
            return array();
        }

        $tickers = array();
        foreach( $terms as $term ) {
            $tickers[] = array(
                'name' => trim( $term->name ),
                'permalink' => get_the_permalink( $term->term_id ),
            );
        }
    
        return $tickers;
    }

    public static function get_letter_link( $letter_id ) {
        if ( ! $letter_id || ! is_numeric( $letter_id ) ) {
            return '';
        }

        $letter_link =  get_post_meta( $letter_id, 'letter-link', true );

        return ! empty( $letter_link ) ? $letter_link : '';
    }

    public static function get_fund_CAGR( $fund_id ) {
        if ( ! $fund_id || ! is_numeric( $fund_id ) ) {
            return '-';
        }

        $letter_link =  get_post_meta( $fund_id, 'return-since-inception', true );

        return ! empty( $letter_link ) ? $letter_link : '-';
    }

    public static function get_fund_quarterly( $fund_id ) {
        if ( ! $fund_id || ! is_numeric( $fund_id ) ) {
            return '-';
        }

        $letter_link =  get_post_meta( $fund_id, 'quarterly', true );

        return ! empty( $letter_link ) ? $letter_link : '-';
    }

    public static function get_fund_ytd( $fund_id ) {
        if ( ! $fund_id || ! is_numeric( $fund_id ) ) {
            return '-';
        }

        $letter_link =  get_post_meta( $fund_id, 'ytd', true );

        return ! empty( $letter_link ) ? $letter_link : '-';
    }

    public static function get_fund_website_link( $fund_id ) {
        if ( ! $fund_id || ! is_numeric( $fund_id ) ) {
            return '';
        }

        $letter_link =  get_post_meta( $fund_id, 'website-link', true );

        return ! empty( $letter_link ) ? $letter_link : '';
    }

    public static function get_fund_twitter_link( $fund_id ) {
        if ( ! $fund_id || ! is_numeric( $fund_id ) ) {
            return '';
        }

        $letter_link =  get_post_meta( $fund_id, 'twitter-link', true );

        return ! empty( $letter_link ) ? $letter_link : '';
    }

    public static function get_fund_investor_name( $fund_id ) {
        if ( ! $fund_id || ! is_numeric( $fund_id ) ) {
            return '-';
        }

        $letter_link =  get_post_meta( $fund_id, 'investor-name', true );

        return ! empty( $letter_link ) ? $letter_link : '-';
    }

    /**
     * Get alternating row background class
     * 
     * @param int $index Current row index
     * @return string CSS class for row background
     */
    public static function get_alternating_row_class($index) {
        return $index % 2 === 1 ? 'bg-gray-50 hover:bg-gray-100' : 'hover:bg-gray-50';
    }

    /**
     * Get color class based on value
     * 
     * @param float $value Value to compare
     * @return string CSS class for text color
     */
    public static function get_color_class($value) {
        return $value >= 0 ? 'text-green-600' : 'text-red-600';
    }

    /**
     * Truncate text with ellipsis if too long
     * 
     * @param string $text Text to truncate
     * @param int $max_length Maximum length before truncating
     * @param string $suffix String to append after truncation
     * @return string Truncated text
     */
    public static function truncate_text($text, $max_length = 20, $suffix = '...') {
        return strlen($text) > $max_length ? 
            substr($text, 0, $max_length - strlen($suffix)) . $suffix : 
            $text;
    }
}