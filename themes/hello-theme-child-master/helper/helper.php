<?php 

class BSD_Helper {

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

}