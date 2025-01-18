<?php
/**
 * Register QTD metadata and metabox for letter types
 *
 * @package MD_Buyside
 */

class BSD_Meta_Letter_QTD {

    function __construct(){
        add_action( 'init', array( $this, 'register_letters_meta' ));
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ));
        add_action( 'save_post_letters', array( $this, 'save_letter_performance_meta' ));
    }

    public function register_letters_meta(){
        register_post_meta(
            'letters',
            'bsd-letter-ytd',
            array(
                'type'              => 'string',
                'description'       => __( 'Letter YTD Performace', 'buyside-digest' ),
                'single'            => true,
                'sanitize_callback' => array( $this, 'sanitize_float_meta' ),
            )
        );
        register_post_meta( 
            'letters',
            'bsd-letter-qtd',
            array(
                'type'              => 'string',
                'description'       => __( 'Letter QTD Performace', 'buyside-digest' ),
                'single'            => true,
                'sanitize_callback' => array( $this, 'sanitize_float_meta' ),
            )
        );
    }

    public function sanitize_float_meta($meta_value) {
        $sanitized = sanitize_text_field($meta_value);
        
        if ($sanitized === '-') return '-';

        $value = filter_var($sanitized, FILTER_VALIDATE_FLOAT);
        return $value !== false ? $value : '-';
    }

    public function add_meta_box(){
        add_meta_box(
            'bsd-letter-peformance-meta',
            __( 'Letter Performance', 'buyside-digest' ),
            array( $this, 'display_letter_performance_meta_box' ),
            'letters',
            'side',
            'high'
        );
    }

    public function display_letter_performance_meta_box( $post ){
        $qtd = get_post_meta( $post->ID, 'bsd-letter-qtd', true );
        $ytd = get_post_meta( $post->ID, 'bsd-letter-ytd', true );
        wp_nonce_field( plugin_basename( __FILE__ ), 'bsd-letter-performance-meta-nonce' );
        ?>
            <div>
                <label for="bsd-letter-qtd">
                    <?php esc_html_e( 'QTD', 'buyside-digest' ); ?>
                </label>
                <input 
                    type="text" 
                    name="bsd-letter-qtd"
                    id="bsd-letter-qtd" 
                    value="<?php echo esc_attr( $qtd ); ?>"
                    placeholder="<?php esc_attr_e( 'Percentage', 'buyside-digest' ); ?>" 
                    class="widefat mt-2px"
                />
            </div>
            <div class="mt-5px">
                <label for="bsd-letter-ytd">
                    <?php esc_html_e( 'YTD', 'buyside-digest' ); ?>
                </label>
                <input 
                    type="text" 
                    name="bsd-letter-ytd"
                    id="bsd-letter-ytd"
                    value="<?php echo esc_attr( $ytd ); ?>"
                    placeholder="<?php esc_attr_e( 'Percentage', 'buyside-digest' ); ?>" 
                    class="widefat mt-2px"
                />
            </div>
        <?php
    }
    

    public function save_letter_performance_meta($post_id) {
        // Verify nonce exists
        if (!isset($_POST['bsd-letter-performance-meta-nonce'])) {
            return;
        }
    
        // Security checks
        if (!wp_verify_nonce(
            sanitize_text_field($_POST['bsd-letter-performance-meta-nonce']), 
            plugin_basename(__FILE__)
        )) {
            return;
        }
    
        // Skip autosave, ajax, bulk edit
        if (
            (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
            (defined('DOING_AJAX') && DOING_AJAX) ||
            isset($_REQUEST['bulk_edit'])
        ) {
            return;
        }
    
        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Process YTD
        $ytd_input = sanitize_text_field($_POST['bsd-letter-ytd']);
        if ($this->is_valid_input($ytd_input)) {
            $ytd = $ytd_input === '-' ? $ytd_input : floatval($ytd_input);
            $old_ytd = get_post_meta($post_id, 'bsd-letter-ytd', true);
            if ($ytd !== $old_ytd) {
                update_post_meta($post_id, 'bsd-letter-ytd', $ytd);
            }
        }
    
        // Process QTD
        $qtd_input = sanitize_text_field($_POST['bsd-letter-qtd']);
        if ($this->is_valid_input($qtd_input)) {
            $qtd = $qtd_input === '-' ? $qtd_input : floatval($qtd_input);
            $old_qtd = get_post_meta($post_id, 'bsd-letter-qtd', true);
            if ($qtd !== $old_qtd) {
                update_post_meta($post_id, 'bsd-letter-qtd', $qtd);
            }
        }
    }
    
    /**
     * Checks if input is either a valid float or "-"
     */
    private function is_valid_input($input) {
        if ($input === '-') {
            return true;
        }
        return filter_var($input, FILTER_VALIDATE_FLOAT) !== false;
    }
}

new BSD_Meta_Letter_QTD();