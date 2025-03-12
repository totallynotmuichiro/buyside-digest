<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );

//Enqueue all files/shortcodes for my content
require_once(get_stylesheet_directory()."/template-parts/my-content/my-content-functions.php");


function enqueue_owl_carousel_scripts_styles() {
    if (is_front_page()) {
        // Enqueue Owl Carousel CSS
        wp_enqueue_style('owl-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css');
        wp_enqueue_style('owl-carousel-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css');

    }
}

add_action('wp_enqueue_scripts', 'enqueue_owl_carousel_scripts_styles');

function localize_ajaxurl() {
    ?>
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
}
add_action('wp_head', 'localize_ajaxurl');

//logout redirect
add_action('wp_logout','auto_redirect_after_logout');

function auto_redirect_after_logout(){
  wp_safe_redirect( home_url() );
  exit;
}

// Logout without confirmation
add_action('check_admin_referer', 'logout_without_confirm', 10, 2);
function logout_without_confirm($action, $result)
{
    /**
     * Allow logout without confirmation
     */
    if ($action == "log-out" && !isset($_GET['_wpnonce'])) {
        $redirect_to = isset($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : '/';
        $location = str_replace('&amp;', '&', wp_logout_url($redirect_to));
        header("Location: $location");
        die;
    }
}


function enqueue_new_style() {
    wp_enqueue_style('new-style', get_stylesheet_directory_uri() . '/new-style.css', array(), '1.0', 'all');
}

add_action('wp_enqueue_scripts', 'enqueue_new_style');

 


//template-parts/Related BSD the content
function get_bsdcontent( $hattr ) {
    ob_start();
    get_template_part( 'template-parts/content-bsd' );
    return ob_get_clean();
}
add_shortcode( 'bsdContent', 'get_bsdcontent' );

//template-parts/Related BSD the header
function get_bsdheader( $hrattr ) {
    ob_start();
    get_template_part( 'template-parts/content-bsd-header' );
    return ob_get_clean();
}
add_shortcode( 'bsdHeader', 'get_bsdheader' );



function register_custom_field_realtion_block( $blocks ) {

	// "custom_field_email_block" corresponds to the block slug.
	$blocks['custom_field_realtion_block'] = [
		'name'            => 'Custom Field - Relation',
		'render_callback' => 'render_custom_field_realtion_block',
	];

	return $blocks;

}
add_filter( 'wp_grid_builder/blocks', 'register_custom_field_realtion_block' );

function render_custom_field_realtion_block() {
	global $wpdb;
	// Object can be a post, term or user.
	$object = wpgb_get_object();

	// If this is not a post (you may change this condition for user or term).
	if ( ! isset( $object->post_type ) ) {
		return;
	}

	// You have to change "custom_field_name" by yours.
	$fund_text_meta_key = get_post_meta( $object->ID, 'fund_text_meta_key', true );
	 
	 
   $postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $fund_text_meta_key . "' AND post_status = 'publish'" );
 
	 
	printf(
		' <a href="%s" target="_blank">View fund</a>',
		esc_url( get_the_permalink($postid) ),
		esc_html( $fund_text_meta_key )
	);
}


add_action('wp_ajax_check_current_password', 'check_current_password');
add_action('wp_ajax_nopriv_check_current_password', 'check_current_password');

function check_current_password() {
    $current_password = sanitize_text_field($_POST['currentPassword']);
    $user = wp_get_current_user();

    if (wp_check_password($current_password, $user->user_pass, $user->ID)) {
        echo 'valid';
    } else {
        echo 'invalid';
    }

    die();
}



add_action('wp_ajax_send_import_start_email_cron_callback', 'callback_send_import_start_email_cron_callback');
function callback_send_import_start_email_cron_callback()
{    
    global $wpdb;
    $post_type = $_POST['type'];

    $crontable_name = $wpdb->prefix . 'useremail_cron';
    $query = $wpdb->prepare("SELECT * FROM $crontable_name WHERE post_type = %s", $post_type);
    $results = $wpdb->get_results($query, ARRAY_A);


    if($post_type == 'letters'){
        $subject = "Buyside Digest Update: New Tickers Added from Your Watchlist!";
    }
    else if($post_type=='funds'){
        $subject = "Buyside Digest Update: New Investor Letters Added from Your Watchlist!";
    }
    
    $user_posts = array();
    if($post_type == 'letters'){
        $subscriber_ticker_ids=[];
        foreach($results as $result){
             $post_id = $result["post_id"];
    
            $table_relationships = 'wp_term_relationships';
            $table_terms = 'wp_terms';
            $table_subscriptions = 'wp_ticker_subscriptions';
    
            $query = $wpdb->prepare("
                SELECT s.* , r.object_id as post_id
                FROM $table_relationships r
                INNER JOIN $table_terms t ON r.term_taxonomy_id = t.term_id
                INNER JOIN $table_subscriptions s ON t.term_id = s.ticker_id
                WHERE r.object_id = %d
            ", $post_id);
    
            $subscriber_ticker_ids = array_merge($subscriber_ticker_ids,$wpdb->get_results($query, ARRAY_A));
            
        }
        
        foreach ($subscriber_ticker_ids as $item) {
            $user_id = $item["user_id"];
            $post_id = $item["post_id"];
            $ticker_id = $item["ticker_id"];
        
            // Append the post ID to the nested array corresponding to user ID and ticker ID
            $user_posts[$user_id][$ticker_id][] = $post_id;
            
        }
        

    }
    else if($post_type=='funds'){

        $post_IDs = [];
        foreach($results as $result){
            $post_IDs[] = $result["post_id"]; // Append each post ID to the $post_IDs array
        }
        
        // If $post_IDs is empty, set it to an empty array to prevent errors
        if (empty($post_IDs)) {
            $post_IDs = array('');
        }
        
        $table = 'wp_fund_subscriptions';
        
        // Create placeholders for each fund ID in the array
        $placeholders = implode(',', array_fill(0, count($post_IDs), '%s'));
        
        // Prepare the SQL query with placeholders for multiple fund IDs
        $query = $wpdb->prepare("SELECT * FROM $table WHERE fund_id IN ($placeholders);", $post_IDs);
        
        // Retrieve the subscriber user IDs
        $subscriber_user_ids = $wpdb->get_results($query);

        foreach ($subscriber_user_ids as $item) {
            $user_id = $item->user_id;
            $post_id = $item->fund_id;
            $user_posts[$user_id][] = $post_id;
        }
    }

    // echo "<pre>";
    // print_r($user_posts);
    // die;

    foreach ($user_posts as $user_id => $post_ID) {
        
        $site_logo = wp_get_attachment_url('12538');
        $background_img = wp_get_attachment_url('12537');

        $query = $wpdb->prepare(
            "SELECT user_email, display_name FROM {$wpdb->users} WHERE ID = %d",
            $user_id
        );
        $user_data = $wpdb->get_row($query);
        $user_email = $user_data->user_email;
        $user_name = $user_data->display_name;

        $email_files_path = get_stylesheet_directory() . '/template-parts';
        $header_path = file_get_contents($email_files_path.'/email_header.php');

        if($post_type == 'letters'){
            $subscripiton_post_type = 'Tickers';
        }
        else if($post_type == 'funds'){
            $subscripiton_post_type = 'Funds';
        }
        $search_header = array('{BACKGROUND_IMG}','{SITE_LOGO}','{POST_TYPE}','{USER_NAME}');
        $replace_header = array($background_img,$site_logo,$subscripiton_post_type,$user_name);

        $email_header = str_replace($search_header, $replace_header, $header_path);

        $email_body_part = '';

        foreach($post_ID as $ticker_id => $post_ids_array){
           
            
            if($post_type=='funds'){
                $fund_id =  $post_ids_array;
                $post_type = get_post_type($fund_id);
                $fund_name = get_the_title($fund_id);
                $featured_image_url = get_the_post_thumbnail_url($fund_id);
                $logo_img =  get_post_meta($fund_id, 'logo', true);
                $logo_img_html = '';
                if($logo_img){
                    $logo_img_html = '<p style="margin: 0 0 15px;">
                    <img style="width: auto;height: 40px;" src="'.$logo_img.'" title="" alt=""></p>';
                }
                $fund_link = get_the_permalink($fund_id);

                $key_terms = wp_get_post_terms($fund_id, 'key-person');
               


                $keyperson_name = [];
                foreach ($key_terms as $keyperson){
                    $keyperson_name[] = $keyperson->name;
                }

                $keyperson_names = implode(', ', $keyperson_name);
                
                

                $qtd_dec = get_post_meta( $fund_id, 'quarterly',true );
                $qtd = number_format((float) $qtd_dec, 1, '.', '');
                $ytd_dec = get_post_meta( $fund_id, 'ytd',true );
                $ytd = number_format((float)$ytd_dec, 1, '.', '');
                $site_logo = wp_get_attachment_url('12538');
                $background_img = wp_get_attachment_url('12537');

                if($qtd == 0.0){
                    $qtd_per = ' -';
                }
                else{
                    $qtd_per = $qtd.'%';
                }

                if($ytd == 0.0){
                    $ytd_per = ' -';
                }
                else{
                    $ytd_per = $ytd.'%';
                }

                if($featured_image_url){

                    $featured_data= '
                    
                    <td align="top" style="width: 192px;background: #e1e7ee;min-width: 192px;text-align: center;font-size: 30px;color: #0d3d6d;font-weight: bold;">
                    <div style="padding: 20px 0 20px 20px;">
                        <img width="192" height="auto" title="" alt="" style="border: 2px solid #0d3d6d;display:inline-block;vertical-align:top;width: 192px;max-width: 192px;" 
                        src="'.$featured_image_url.'">
                    </div>
                    </td> ';

                }
                else{

                    $featured_data= '
                    <td align="top" style="width: 192px;background: #ffffff;min-width: 192px;text-align: center;font-size: 18px;color: #0d3d6d;font-weight: bold;border: 2px solid #0d3d6d;height: 200px;">
                    <div style="padding: 20px 5px 20px 5px;">
                        <p>'.$fund_name.'</p>
                    </div>
                    </td> 
                    ';
                }


                // echo "<pre>";
                // print_r($featured_data);
                // die("dfdf");

                $email_body_part .= '
                    <tr>
                    <td></td>
                    <td>
                        <table cellpadding="0" cellspacing="0" style="width: 100%;">
                            <tbody>
                                <tr>
                                '.$featured_data.'
                                <td style="width: 100%;background-color: #e1e7ee;padding: 10px 10px 10px 30px;font-size: 16px;">
                                   '.$logo_img_html.'
                                    <p style="margin: 0 0 5px;font-size: 16px;"><span style="color: #0f3a4f;">Fund:</span> '.$fund_name.'
                                    </p>
                                    <p style="margin: 0 0 5px;font-size: 16px;"><span style="color: #0f3a4f;">Key
                                        Person:</span> '.$keyperson_names.'
                                    </p>
                                    <p style="margin: 0 0 15px;font-size: 16px;"><span
                                        style="color: #0f3a4f;">QTD:</span> '.$qtd_per.' &nbsp; &nbsp; <span
                                        style="color: #0f3a4f;">YTD:</span> '.$ytd_per.'</p>
                                    <p><a style="display: inline-block;
                                        padding: 4px 30px 4px 20px;
                                        text-align: center;
                                        color: #ffffff;
                                        background-color: #0f3a4f;
                                        border-radius: 5px;
                                        font-size: 16px;
                                        margin-bottom: 5px;
                                        width: auto;                                            
                                        font-weight: normal;
                                        vertical-align: top;
                                        line-height: 35px;
                                        text-decoration: none;
                                        " href="'.$fund_link.'" target="_blank">View Fund</a></p>
                                </td>
                                </tr>
                                <tr>
                                <td height="15"></td>
                                </tr>
                            </tbody>
                        </table>
                        </td>
                        <td></td>
                    </tr>
                    ';
            }
            else if($post_type=='letters'){

                foreach ($post_ids_array as $postid) {
                    // echo $postid.'<br>';
                $table_name_posts = $wpdb->prefix . 'posts';
                $table_name_rel = $wpdb->prefix . 'jet_rel_default';    
                $post_data = $wpdb->prepare(
                    "SELECT p.ID, r.parent_object_id
                    FROM $table_name_posts p
                    JOIN $table_name_rel r ON p.ID = r.child_object_id
                    WHERE p.post_type = 'letters'
                    AND p.post_status = 'publish'
                    AND p.ID = %d
                    ORDER BY p.post_date DESC
                    LIMIT 1", // Limit to 1 since you're retrieving information for a specific post ID
                    $postid
                );

                $results = $wpdb->get_results($post_data, ARRAY_A);

                $fund_id = $results['0']['parent_object_id'];
                $letter_id = $results['0']['ID'];

                $fund_name = get_the_title($fund_id);
                $key_terms = wp_get_post_terms($letter_id, 'key-person');
                // $letter_link =  get_post_meta($letter_id, 'letter-link', true);
                $letter_link =  get_term_link( $ticker_id, 'tickers');
        
                // echo "<pre>";
                // print_r($letter_link);
                // die;
                $keyperson_name = [];
                foreach ($key_terms as $keyperson){
                    $keyperson_name[] = $keyperson->name;
                }
        
                $keyperson_names = implode(', ', $keyperson_name);
        
                $term = get_term_by('id', $ticker_id, 'tickers');
                        $ticker_name = '';
                        if ($term) {
                             $ticker_name = $term->name;
                        }
                  $email_body_part .= '
                    <tr>
                    <td></td>
                    <td>
                        <table cellpadding="0" cellspacing="0" style="width: 100%;">
                            <tbody>
                                <tr>
                                <td align="top" style="width: 192px;background: #ffffff;min-width: 192px;text-align: center;font-size: 30px;color: #0d3d6d;font-weight: bold;border: 2px solid #0d3d6d;height: 200px;">
                                <div>
                                    <p>'.$ticker_name.'</p>
                                </div>
                                </td> 
                                <td style="width: 100%;background-color: #e1e7ee;padding: 10px 10px 10px 30px;font-size: 16px;">
                                    <p style="margin: 0 0 5px;font-size: 16px;"><span
                                        style="color: #0f3a4f;">Ticker:</span> '.$ticker_name.'</p>
                                    <p style="margin: 0 0 5px;font-size: 16px;"><span style="color: #0f3a4f;">Fund:</span> '.$fund_name.'
                                    </p>
                                    <p style="margin: 0 0 5px;font-size: 16px;"><span style="color: #0f3a4f;">Key
                                        Person:</span> '.$keyperson_names.'
                                    </p>
                                    <p><a style="display: inline-block;
                                        padding: 4px 30px 4px 20px;
                                        text-align: center;
                                        color: #ffffff;
                                        background-color: #0f3a4f;
                                        border-radius: 5px;
                                        font-size: 16px;
                                        margin-bottom: 5px;
                                        width: auto;                                            
                                        font-weight: normal;
                                        vertical-align: top;
                                        line-height: 35px;
                                        text-decoration: none;
                                        " href="'.$letter_link.'" target="_blank">View Ticker</a></p>
                                </td>
                                </tr>
                                <tr>
                                <td height="15"></td>
                                </tr>
                            </tbody>
                        </table>
                        </td>
                        <td></td>
                    </tr>
                        ';
                }

            }
            
        }

        $email_footer = file_get_contents($email_files_path.'/email_footer.php');

        $full_mail_content = $email_header;
        $full_mail_content .= $email_body_part;
        $full_mail_content .= $email_footer;

        // Get user email
        $user_email = $wpdb->get_var($wpdb->prepare("SELECT user_email FROM {$wpdb->users} WHERE ID = %d", $user_id));
		
        // Create channel name
        if($post_type == 'funds'){
            $channel = "funds " . date('Y-m-d');
        } else if($post_type == 'letters'){
            $channel = "tickers " . date('Y-m-d');
        }
		
		// Email Headers
        $headers = array(
            'From: Buyside Monitor <netqomuser3@gmail.com>',
        );
		
        $temp_channel_name = get_option( 'ee_channel_name' );

        // Send email
        update_option('ee_channel_name', $channel );
        wp_mail($user_email, $subject, $full_mail_content, $headers);
        update_option('ee_channel_name', $temp_channel_name );

        // Delete data from the cron table after processing
        $crontable_name = $wpdb->prefix . 'useremail_cron';

        // Delete rows from the table where post type is 'xyz'
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM $crontable_name WHERE post_type = %s",
                $post_type
            )
        );
    }

    $return_message = '<div class="no_action_required">
                        <img width="70" src="/wp-content/uploads/2024/02/tick-green.png" alt="" title="">
                            <h3>All mails send Successfully.</h3>
                        </div>';

    wp_send_json_success(['status'=>'success','message'=>$return_message]);    
    // die;
}


// Hook the function to the save_post action
add_action('save_post', 'save_post_details_to_useremail_cron_table', 10, 3);

function save_post_details_to_useremail_cron_table($post_ID, $post, $update) {

    // Get the list of user IDs who follow this post (custom function needed)
    global $wpdb;

    //Get fund user subscriber details
	$fund_user = $wpdb->prepare("SELECT * FROM wp_fund_subscriptions WHERE fund_id=%s;", $post_ID );
	$fund_subscriber = $wpdb->get_results( $fund_user );

    //Get ticker user subscriber details
    $table_relationships = 'wp_term_relationships';
    $table_terms = 'wp_terms';
    $table_subscriptions = 'wp_ticker_subscriptions';

    $query = $wpdb->prepare("
        SELECT s.* 
        FROM $table_relationships r
        INNER JOIN $table_terms t ON r.term_taxonomy_id = t.term_id
        INNER JOIN $table_subscriptions s ON t.term_id = s.ticker_id
        WHERE r.object_id = %d
    ", $post_ID);

    $subscriber_ticker_ids = $wpdb->get_results($query, ARRAY_A);

    $useremail_cron_table = $wpdb->prefix . 'useremail_cron';
    $post_exists = $wpdb->get_var(
        $wpdb->prepare("SELECT post_id FROM $useremail_cron_table WHERE post_id = %d", $post_ID)
    );

    // Check if this is an update by an admin
    if (current_user_can('manage_options') && $post->post_status == 'publish' && $post->post_type == 'letters' && $subscriber_ticker_ids) {
        
        if (!$post_exists) {
            // Post ID does not exist, proceed with the insertion
            $data_to_insert = array(
                'post_type'=>'letters',
                'post_id' => $post_ID,
                // Add other columns and their values as needed
            );
            $insert_post_id = $wpdb->insert($useremail_cron_table, $data_to_insert);
        } 
    }
    else if (current_user_can('manage_options') && $post->post_status == 'publish' && $post->post_type == 'funds' && $fund_subscriber) {
       
        if (!$post_exists) {
            // Post ID does not exist, proceed with the insertion
            $data_to_insert = array(
                'post_type'=>'funds',
                'post_id' => $post_ID,
                // Add other columns and their values as needed
            );
            $insert_post_id = $wpdb->insert($useremail_cron_table, $data_to_insert);
        } 
    }

}


function display_total_users_count() {
    $total_users = count_users();
    $total_users_count = $total_users['total_users'];
    ?>
    <div class="notice notice-success is-dismissible">
        <p style="font-weight:600;">Total number of registered users: <strong><?php echo $total_users_count; ?></strong></p>
    </div>
    <?php
}

// Hook the function to the admin_notices action
add_action('admin_notices', 'display_total_users_count');



function notify_admin_on_new_subscriber($user_id) {
    $user = get_userdata($user_id);

    // Check if the user has the 'subscriber' role
    // if (in_array('subscriber', $user->roles)) {
        $to = $user->user_email; // Replace with your admin email address
        $subject = 'Welcome to the Buyside Monitor';
        $message = "Thank you for registering on our site. We're excited to have you!";

        // Send email notification
        wp_mail($to, $subject, $message);
    // }
}

// Hook into user registration to trigger the notification
// add_action('user_register', 'notify_admin_on_new_subscriber');

//Email sent to the user after reset password

function send_password_reset_email($user, $new_pass) {
    // Get user data
    $user_info = get_userdata($user->ID);
    // echo "<pre>"; print_r($user_info);die;
    // Email subject
    $subject = 'Your Password Has Been Reset';

    // Email message
    $message = sprintf('Hello %s, your password has been reset. Your new password is: %s', $user_info->display_name, $new_pass);

    // Send email
    wp_mail($user_info->user_email, $subject, $message);
}

add_action('password_reset', 'send_password_reset_email', 10, 2);


function mailchimp_modal_shortcode() {
    ob_start(); // Start output buffering
    ?>
    <button class="trigger btn">To register your fund with us, click here</button>
    <div class="modal subscribe-modal" id="modal">
        <div class="modal-content">
            <span class="close-button subscribe-close-button" id="close-button">×</span>
            <div class="modal-body">
                <div id="mc_embed_shell">
                    <link href="//cdn-images.mailchimp.com/embedcode/classic-061523.css" rel="stylesheet" type="text/css">
                    <style type="text/css">
                            /* #mc_embed_signup{background:#fff; false;clear:left; font:14px Helvetica,Arial,sans-serif; width: 600px;} */
                            /* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
                            We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
                    </style>
                    <div id="mc_embed_signup">
                        <form action="https://gmail.us14.list-manage.com/subscribe/post?u=db5fab14888de1bd0ae4cb3c9&amp;id=a6b44a2aa1&amp;f_id=004186e0f0" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
                            <div id="mc_embed_signup_scroll"><h2>Subscribe</h2>
                                <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
                                <div class="mc-field-group"><label for="mce-FNAME">First Name <span class="asterisk">*</span></label><input type="text" name="FNAME" class="required text" id="mce-FNAME" required="" value=""></div><div class="mc-field-group"><label for="mce-LNAME">Last Name <span class="asterisk">*</span></label><input type="text" name="LNAME" class="required text" id="mce-LNAME" required="" value=""></div>
                                <div class="mc-field-group"><label for="mce-PHONE">Phone Number </label><input type="text" name="PHONE" class="REQ_CSS" id="mce-PHONE" value=""></div><div class="mc-field-group"><label for="mce-EMAIL">Email Address <span class="asterisk">*</span></label><input type="email" name="EMAIL" class="required email" id="mce-EMAIL" required="" value=""><span id="mce-EMAIL-HELPERTEXT" class="helper_text"></span></div>
                                <div id="mce-responses" class="clear foot">
                                    <div class="response" id="mce-error-response" style="display: none;"></div>
                                    <div class="response" id="mce-success-response" style="display: none;"></div>
                                </div>
                                <div aria-hidden="true" style="position: absolute; left: -5000px;">
                                    /* real people should not fill this in and expect good things - do not remove this or risk form bot signups */
                                    <input type="text" name="b_db5fab14888de1bd0ae4cb3c9_a6b44a2aa1" tabindex="-1" value="">
                                </div>
                                <div class="optionalParent">
                                    <div class="clear foot">
                                        <input type="submit" name="subscribe" id="mc-embedded-subscribe" class="button" value="Subscribe">
                                        <!-- <p style="margin: 0px auto;"><a href="http://eepurl.com/iD7bBM" title="Mailchimp - email marketing made easy and fun"><span style="display: inline-block; background-color: transparent; border-radius: 4px;"><img class="refferal_badge" src="https://digitalasset.intuit.com/render/content/dam/intuit/mc-fe/en_us/images/intuit-mc-rewards-text-dark.svg" alt="Intuit Mailchimp" style="width: 220px; height: 40px; display: flex; padding: 2px 0px; justify-content: center; align-items: center;"></span></a></p> -->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js"></script><script type="text/javascript">(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[4]='PHONE';ftypes[4]='phone';fnames[0]='EMAIL';ftypes[0]='email';fnames[3]='ADDRESS';ftypes[3]='address';fnames[5]='BIRTHDAY';ftypes[5]='birthday';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean(); // End and clean the output buffer
}
add_shortcode('mailchimp_modal', 'mailchimp_modal_shortcode');


function removal_fund_btn_mailchimp_modal_shortcode() {
    ob_start(); // Start output buffering
    ?>
    <button class="fund-removal btn">To delete your fund from our database, click here</button>
    <div class="modal fund_remove_modal" id="fund_remove_modal">
        <div class="modal-content">
            <span class="close-button fund-close-button" id="fund-close-button">×</span>
            <div class="modal-body">
                <div id="mc_embed_shell">
                    <link href="//cdn-images.mailchimp.com/embedcode/classic-061523.css" rel="stylesheet" type="text/css">
                    <style type="text/css">
                            /* #mc_embed_signup{background:#fff; false;clear:left; font:14px Helvetica,Arial,sans-serif; width: 600px;} */
                            /* Add your own Mailchimp form style overrides in your site stylesheet or in this style block.
                            We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
                    </style>
                    <div id="mc_embed_signup">
                        <form action="https://gmail.us14.list-manage.com/subscribe/post?u=db5fab14888de1bd0ae4cb3c9&amp;id=a6b44a2aa1&amp;f_id=004186e0f0" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
                            <div id="mc_embed_signup_scroll"><h2>Remove Fund Mail</h2>
                                <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
                                <div class="mc-field-group"><label for="mce-FNAME">First Name <span class="asterisk">*</span></label><input type="text" name="FNAME" class="required text" id="mce-FNAME" required="" value=""></div><div class="mc-field-group"><label for="mce-LNAME">Last Name <span class="asterisk">*</span></label><input type="text" name="LNAME" class="required text" id="mce-LNAME" required="" value=""></div>
                                <div class="mc-field-group"><label for="mce-PHONE">Phone Number </label><input type="text" name="PHONE" class="REQ_CSS" id="mce-PHONE" value=""></div><div class="mc-field-group"><label for="mce-EMAIL">Email Address <span class="asterisk">*</span></label><input type="email" name="EMAIL" class="required email" id="mce-EMAIL" required="" value=""><span id="mce-EMAIL-HELPERTEXT" class="helper_text"></span></div>
                                <div id="mce-responses" class="clear foot">
                                    <div class="response" id="mce-error-response" style="display: none;"></div>
                                    <div class="response" id="mce-success-response" style="display: none;"></div>
                                </div>
                                <div aria-hidden="true" style="position: absolute; left: -5000px;">
                                    /* real people should not fill this in and expect good things - do not remove this or risk form bot signups */
                                    <input type="text" name="b_db5fab14888de1bd0ae4cb3c9_a6b44a2aa1" tabindex="-1" value="">
                                </div>
                                <div class="optionalParent">
                                    <div class="clear foot">
                                        <input type="submit" name="subscribe" id="mc-embedded-subscribe" class="button" value="Submit">
                                        <!-- <p style="margin: 0px auto;"><a href="http://eepurl.com/iD7bBM" title="Mailchimp - email marketing made easy and fun"><span style="display: inline-block; background-color: transparent; border-radius: 4px;"><img class="refferal_badge" src="https://digitalasset.intuit.com/render/content/dam/intuit/mc-fe/en_us/images/intuit-mc-rewards-text-dark.svg" alt="Intuit Mailchimp" style="width: 220px; height: 40px; display: flex; padding: 2px 0px; justify-content: center; align-items: center;"></span></a></p> -->
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js"></script><script type="text/javascript">(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[4]='PHONE';ftypes[4]='phone';fnames[0]='EMAIL';ftypes[0]='email';fnames[3]='ADDRESS';ftypes[3]='address';fnames[5]='BIRTHDAY';ftypes[5]='birthday';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean(); // End and clean the output buffer
}
add_shortcode('remove_fund_mailchimp_modal', 'removal_fund_btn_mailchimp_modal_shortcode');




function custom_post_type_posts_shortcode() {
    // Replace 'funds' with your custom post type slug
    $post_type = 'funds';

    // Get all posts of the custom post type
    $posts = get_posts(array(
        'post_type' => $post_type,
        'numberposts' => -1, // Retrieve all posts
    ));

    // Check if there are posts
    if ($posts) {
        $output = '<ul>';

        // Loop through each post and append the post name to the output
        foreach ($posts as $post) {
            $output .= '<li>' . esc_html($post->post_title) . '</li>';
        }

        $output .= '</ul>';

        return $output;
    } else {
        // Return a message if there are no posts
        return 'No posts found for the custom post type';
    }
}

// Register the shortcode
add_shortcode('custom_post_type_posts', 'custom_post_type_posts_shortcode');



add_shortcode( 'featured_letters','callback_featured_letters' );

function callback_featured_letters(){

    ob_start();
    global $wpdb;

    $table_name_posts = $wpdb->prefix . 'posts';
    $table_name_rel = $wpdb->prefix . 'jet_rel_default';
    $table_name_term_relationships = $wpdb->prefix . 'term_relationships';
    $table_name_term_taxonomy = $wpdb->prefix . 'term_taxonomy';
    $taxonomy_name = 'featured';

    $query = $wpdb->prepare(
        "SELECT DISTINCT p.ID, r.parent_object_id
        FROM $table_name_posts p
        JOIN $table_name_rel r ON p.ID = r.child_object_id
        JOIN $table_name_term_relationships tr ON p.ID = tr.object_id
        JOIN $table_name_term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        WHERE p.post_type = 'letters'
        AND p.post_status = 'publish'
        AND tt.taxonomy = %s
        GROUP BY p.ID
        ORDER BY p.post_date DESC
        LIMIT 8",
        $taxonomy_name
    );

    $results = $wpdb->get_results($query, ARRAY_A);

    if(!empty($results)){
        echo '<div class="featured-row owl-carousel owl-theme">';
    foreach($results as $post){
        $fund_id = $post['parent_object_id'];
        $letter_id = $post['ID'];

        $featured_image_url = get_the_post_thumbnail_url($fund_id);
        $logo_img =  get_post_meta($fund_id, 'logo', true);
        $letter_link =  get_post_meta($letter_id, 'letter-link', true);
        // $logo_img = wp_get_attachment_url($logo_img_id);
        $key_terms = wp_get_post_terms($letter_id, 'key-person');

        $keyperson_name = [];
        foreach ($key_terms as $keyperson){
            $keyperson_name[] = $keyperson->name;
        }

        $keyperson_names = implode(', ', $keyperson_name);

        $qtd_dec = get_post_meta( $fund_id, 'quarterly',true );
        $qtd = number_format((float)$qtd_dec, 1, '.', '');
        $ytd_dec = get_post_meta( $fund_id, 'ytd',true );
        $ytd = number_format((float)$ytd_dec, 1, '.', '');

        if($qtd == 0.0){
            $qtd_per = ' -';
        }
        else{
            $qtd_per = $qtd.'%';
        }

        if($ytd == 0.0){
            $ytd_per = ' -';
        }
        else{
            $ytd_per = $ytd.'%';
        }

        // echo "<pre>";
        // print_r($keyperson_names);
        // die;
        ?>
            
                <div class="featured-item item">
                    <div class="featured-item-img">
                        <img src="<?=$featured_image_url ? $featured_image_url : '/wp-content/uploads/2023/12/dummy-image-square.jpg' ?>" alt="" title="">
                    </div>
                    <div class="featured-item-content">
                        <?php if($logo_img) { ?>
                            <div class="featured-item-logo"><img src="<?=$logo_img ? $logo_img : '/wp-content/uploads/2023/12/dummy-image-square.jpg' ?>" alt="" title=""></div>
                        <?php } ?>
                        <div class="featured-item-info">
                            <p><span class="clr">Funds:</span> <?=get_the_title($fund_id)?></p>
                            <p><span class="clr">Key Person:</span> <?=$keyperson_names?></p>
                            <!-- <p><span class="clr">QTD:</span> <?= !empty($qtd)? $qtd : '___' ?> <span class="clr">YTD:</span> <?= !empty($ytd)? $ytd : '___'?></p> -->
                            <p><span class="clr">QTD:</span> <?= $qtd_per ?> &nbsp;&nbsp;  <span class="clr">YTD:</span> <?= $ytd_per ?></p>
                        </div>
                        <div class="featured-item-action">
                            <a href="<?=$letter_link?>" target="_blank">View Letter</a>
                            <?php
                                if (is_user_logged_in()) {

                                    $table_name = $wpdb->prefix . 'fund_subscriptions';
                                    $current_user_id = get_current_user_id();
                                    
                                    $query = $wpdb->prepare(
                                        "SELECT fund_id FROM $table_name WHERE fund_id = %d AND user_id = %d LIMIT 1",
                                        $fund_id,
                                        $current_user_id
                                    );
                                    
                                    $fund_id_result = $wpdb->get_var($query);

                                    if (isset($fund_id_result) && !empty($fund_id_result)) {
                                        $track_button =
                                            "<button id='md_fund_subscribe_btn_id_" .$letter_id ."' class='md-fund-subscribe-btn md-fund-table-subscribe-btn md-fund-subscribe-btn--subscribed' data-fund-id='" .$fund_id ."' data-letter-id='" .$letter_id ."'>Unfollow</button>";
                                    } else {
                                        $track_button =
                                            "<button id='md_fund_subscribe_btn_id_" .$letter_id ."' class='md-fund-subscribe-btn md-fund-table-subscribe-btn md-fund-subscribe-btn--not-subscribed' data-fund-id='" .$fund_id ."' data-letter-id='" .$letter_id ."'>Follow</button>";
                                    }
                                } else {
                                    $track_button =
                                        "<a href='" .site_url("/register") ."' class='md-slide-login-btn' id='fund-track'>Follow</a>";
                                }

                                echo $track_button;
                            ?>
                           
                        </div>
                    </div>
                </div>
        <?php
    }
    echo "</div>";

    }
    
    ?>
    <?php
    return ob_get_clean();
}


add_action('wp_ajax_callback_home_page_blog_posts', 'callback_home_page_blog_posts');
add_action('wp_ajax_nopriv_callback_home_page_blog_posts', 'callback_home_page_blog_posts');
add_shortcode( 'home_page_blog_posts','callback_home_page_blog_posts' );

function callback_home_page_blog_posts() {
    
    // ob_start();
    $args = array(
        'post_type'      => 'bsd-weekly',
        'posts_per_page' => 4,
        'post_status'    => 'publish',  // Include only published posts
        'orderby'        => 'date',     // Order by published date
        'order'          => 'DESC',     // Descending order (newest first)
    );

    if (isset($_POST['offset_value'])) {
        $args['offset'] = $_POST['offset_value'];
    }

    
    $custom_query = new WP_Query($args);

    $post_count = $custom_query->found_posts;
    
    if ($custom_query->have_posts()) {
        echo '<div id="weekly_news_letter_posts" class="news_letter_posts">';
        while ($custom_query->have_posts()) {
            $custom_query->the_post(); ?>

            <div class="post-item">
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="post-info">
                    <?php echo get_the_date(); ?> |
                    <?php $categories = get_the_category();
                    if (!empty($categories)) : ?>
                         <?php the_category(', '); ?> |
                    <?php endif; ?>
                    <?php the_author(); ?>
                </div>
                <div class="post-content">
                <?php
                // Display the first 20 words of the content.
                $content =  get_post_meta(get_the_ID(), 'short_description', true);
                $word_limit = 35;
                $content_words = explode(' ', $content);
                echo implode(' ', array_slice($content_words, 0, $word_limit));
                echo '... <a class="more-link" href="' . get_permalink() . '">Read More</a>';
                ?>
            </div>
            </div>
            <?php
        }

        // Restore original post data.
        wp_reset_postdata();

        echo '</div>';

        if (!isset($_POST['offset_value'])) {
            echo '<br><center class="ajax-load-more"><button class="load-more-btn" id="load_more_weekly_posts" total_weekly_posts="'.$post_count.'">Load More </button></center>';
        }
        else{
            wp_die();
        }
       
    } else {
        echo 'No posts found.';
    }
    // return ob_get_clean();
}


function check_email_exists() {
    // Get the email from the AJAX request
    $email = $_POST['email'];

    // Your database query to check if the email exists
    $user = get_user_by('email', $email);

    // Check if user exists
    if ($user) {
        echo json_encode(array('exists' => true,'email'=>$email));
    } else {
        echo json_encode(array('exists' => false));
    }

    // Always exit to prevent further execution
    wp_die();
}
add_action('wp_ajax_check_email_exists', 'check_email_exists');
add_action('wp_ajax_nopriv_check_email_exists', 'check_email_exists');

add_shortcode('sign_up_free', 'callback_sign_up_free');
function callback_sign_up_free(){
    ?>
    <form class="free_sign_up" action="">
        <span>
            <img class="envolep-icon" src="/wp-content/uploads/2023/12/email-icon.png">
            <input type="email" class="user_email" name="user_email" placeholder="e-mail address" required />
        </span>
        <button type="submit">Sign Up Free <img style="display: none;" class="load-icon" src="/wp-content/uploads/2024/01/spinner-icon.gif"></button>
    </form>
    <p class="invalid_email"></p>
    <button class="popmake-12669">Click to open free Sign Up form</button>
    <?php
}


/* Investors custom post type*/

// Add custom post type 'investors'
function custom_post_type_investors() {
    $labels = array(
        'name'               => _x('Investors', 'post type general name', 'your-text-domain'),
        'singular_name'      => _x('Investor', 'post type singular name', 'your-text-domain'),
        'menu_name'          => _x('Investors', 'admin menu', 'your-text-domain'),
        'name_admin_bar'     => _x('Investor', 'add new on admin bar', 'your-text-domain'),
        'add_new'            => _x('Add New', 'investor', 'your-text-domain'),
        'add_new_item'       => __('Add New Investor', 'your-text-domain'),
        'new_item'           => __('New Investor', 'your-text-domain'),
        'edit_item'          => __('Edit Investor', 'your-text-domain'),
        'view_item'          => __('View Investor', 'your-text-domain'),
        'all_items'          => __('All Investors', 'your-text-domain'),
        'search_items'       => __('Search Investors', 'your-text-domain'),
        'parent_item_colon'  => __('Parent Investors:', 'your-text-domain'),
        'not_found'          => __('No investors found.', 'your-text-domain'),
        'not_found_in_trash' => __('No investors found in Trash.', 'your-text-domain')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Description.', 'your-text-domain'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'investors'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'taxonomies'         => array('category', 'post_tag')
    );

    register_post_type('investors', $args);
}

// Hook into the 'init' action
add_action('init', 'custom_post_type_investors');


// Add custom post type 'articles'
function custom_post_type_articles() {
    $labels = array(
        'name'               => _x('Articles', 'post type general name', 'your-text-domain'),
        'singular_name'      => _x('Article', 'post type singular name', 'your-text-domain'),
        'menu_name'          => _x('Articles', 'admin menu', 'your-text-domain'),
        'name_admin_bar'     => _x('Article', 'add new on admin bar', 'your-text-domain'),
        'add_new'            => _x('Add New', 'article', 'your-text-domain'),
        'add_new_item'       => __('Add New Article', 'your-text-domain'),
        'new_item'           => __('New Article', 'your-text-domain'),
        'edit_item'          => __('Edit Article', 'your-text-domain'),
        'view_item'          => __('View Article', 'your-text-domain'),
        'all_items'          => __('All Articles', 'your-text-domain'),
        'search_items'       => __('Search Articles', 'your-text-domain'),
        'parent_item_colon'  => __('Parent Articles:', 'your-text-domain'),
        'not_found'          => __('No articles found.', 'your-text-domain'),
        'not_found_in_trash' => __('No articles found in Trash.', 'your-text-domain')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Description.', 'your-text-domain'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'articles'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'taxonomies'         => array('category', 'post_tag')
    );

    register_post_type('articles', $args);
}

// Hook into the 'init' action
add_action('init', 'custom_post_type_articles');



function investors_list_shortcode() {
    ob_start();

    // Define the query parameters
    $args = array(
        'post_type'      => 'investors',
        'post_status'    => 'publish',
        'orderby'        => 'name',  // Order by date (created or updated)
        'order'          => 'asc',  // Descending order
        'posts_per_page' => 8,      // Display all posts
    );

    // Execute the query
    $investors_query = new WP_Query($args);

    // Check if there are any posts
    if ($investors_query->have_posts()) {
        echo '<div class="investors-list">';

        // Loop through the posts
        while ($investors_query->have_posts()) : $investors_query->the_post();
        ?>
            <div class="investors-item">
                <div class="investors-item-pic">
                    <?php
                    // Display the full-size post thumbnail
                    if (has_post_thumbnail()) {
                        $thumbnail_src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
                        echo '<img src="' . esc_url($thumbnail_src[0]) . '" alt="" title="">';
                    } else {
                        // If no thumbnail is set, you can display a default image or leave it blank
                        echo '<img src="/wp-content/uploads/2022/10/placeholder.png" alt="" title="">';
                    }
                    ?>
                </div>
                <div class="investors-item-info">
                    <h2><?php the_title(); ?></h2>
                    <p><?php echo get_post_meta(get_the_ID(), 'position', true); ?></p>
                    <a href="<?php the_permalink(); ?>" class="profile-link">View Profile <img src="/wp-content/uploads/2024/01/arrow-right.png" alt="" title=""></a>
                </div>
            </div>
        <?php
        endwhile;

        echo '</div>';

        // Reset post data to restore the main loop
        wp_reset_postdata();
    } else {
        // Display a message if no posts are found
        echo '<p>No investors found.</p>';
    }

    return ob_get_clean();
}

// Register the shortcode
add_shortcode('investors_list', 'investors_list_shortcode');


add_shortcode('show_featured_list_of_article', 'callback_show_featured_list_of_article');
function callback_show_featured_list_of_article($atts){

    ob_start();

    $atts = shortcode_atts(
        array(
            'posts_per_page' => 4,  // Limit to 4 articles
            'show_category'  => 'featured', // Show only articles in the 'featured' category
            'show_article_on'  => 'BSD'
        ),
        $atts,
        'show_articles'
    );
    
    // echo '<pre>';
    // print_r($atts);
    // echo '</pre>';
    // die;
    // Query for posts with video_link
    $args_with_video = array(
        'post_type'      => 'articles',
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'posts_per_page' => $atts['posts_per_page'], // Number of articles to display
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $atts['show_category'],
            ),
        ),
        'meta_query'     => array(
            'relation' => 'AND', // Relation between the conditions
            array(
                'key'     => 'video_link',
                'value'   => '',
                'compare' => '!=', // Check if 'video_link' has a value
            ),
            array(
                'key'     => 'show_article_on',
                'value'   => $atts['show_article_on'],
                'compare' => '=', // Check if 'show_on_page' is equal to the specified value
            ),
        ),
    );
    
    // Run the query for posts with video_link
    $query_with_video = new WP_Query($args_with_video);
    
    // Check if there are any articles with video_link
    if ($query_with_video->have_posts()) {
        echo '<div class="news-list">';
        // Loop through the articles with video_link
        while ($query_with_video->have_posts()) : $query_with_video->the_post();
            $logo_id    = get_post_meta(get_the_ID(), 'logo', true);
            $logo_url   = wp_get_attachment_image_url($logo_id, 'full');
            $video_link = get_post_meta(get_the_ID(), 'video_link', true);
            $has_video  = has_youtube_video($video_link);
            $link       = get_post_meta(get_the_ID(), 'article_link', true);
            ?>
            <div class="news-item">
                
                <?php 
                
                if ($video_link) : ?>
                    <?php if (strpos($video_link, 'youtube.com') !== false) : ?>
                        <!-- YouTube video -->
                        <div class="news-item-pic">
                            <iframe src="<?php echo esc_url($has_video); ?>" frameborder="0" allowfullscreen></iframe>
                        </div>
                    <?php elseif (pathinfo($video_link, PATHINFO_EXTENSION) === 'mp4') : ?>
                        <!-- MP4 video -->
                        <div class="news-item-pic">
                            <video controls>
                                <source src="<?php echo esc_url($video_link); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    <?php else : ?>
                        <!-- Display post thumbnail if it's neither YouTube nor MP4 video -->
                        <div class="news-item-pic">
                            <?php the_post_thumbnail(); ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <!-- Display post thumbnail if no video -->
                    <div class="news-item-pic">
                        <?php the_post_thumbnail(); ?>
                    </div>
                <?php endif; ?>
                
    
                <div class="news-item-info">
                    <div class="news-logo">
                        <img src="<?= esc_url($logo_url); ?>" alt="" title="">
                    </div>
                    <h2><?php the_title(); ?></h2>
                    <!-- <p class="news-date"><?php echo get_the_date('M j, Y'); ?></p> -->
                    <div class="news-desc"><?php the_excerpt(); ?></div>
    
                    <?php
                    // if (!$has_video) {
                    //     echo '<a href="' . esc_url($link) . '" target="_blank" class="news-link">Keep reading...</a>';
                    // }
                    ?>
    
                </div>
            </div>
        <?php endwhile;
        echo '</div>';
        // Reset post data
        wp_reset_postdata();
    }
    
    // Query for the next four posts without video_link
    $args_without_video = array(
        'post_type'      => 'articles',
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'posts_per_page' => $atts['posts_per_page'], // Number of articles to display
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $atts['show_category'],
            ),
        ),
        'meta_query'     => array(
            'relation' => 'AND', // Relation between the conditions
            array(
                'relation' => 'OR', // Relation between the two video_link conditions
                array(
                    'key'     => 'video_link',
                    'compare' => 'NOT EXISTS', // Check if 'video_link' does not exist
                ),
                array(
                    'key'     => 'video_link',
                    'value'   => '',
                    'compare' => '=',
                ),
            ),
            array(
                'key'     => 'show_article_on',
                'value'   => $atts['show_article_on'],
                'compare' => '=', // Check if 'show_on_page' is equal to the specified value
            ),
        ),
    );
    
    // Run the query for posts without video_link
    $query_without_video = new WP_Query($args_without_video);
    
    // Check if there are any articles without video_link
    if ($query_without_video->have_posts()) {
        echo '<div class="news-list news-list-without-video">';
        // Loop through the articles without video_link
        while ($query_without_video->have_posts()) : $query_without_video->the_post();
            $logo_id    = get_post_meta(get_the_ID(), 'logo', true);
            $logo_url   = wp_get_attachment_image_url($logo_id, 'full');
            $link       = get_post_meta(get_the_ID(), 'article_link', true);
            ?>
            <div class="news-item">
                <div class="news-item-pic">
                    <?php the_post_thumbnail(); ?>
                </div>
    
                <div class="news-item-info">
                <div class="news-item-info-top">
                    <div class="news-logo">
                        <img src="<?= esc_url($logo_url); ?>" alt="" title="">
                    </div>
                    <h2><?php the_title(); ?></h2>
                    <p class="news-date"><?php echo get_the_date('M j, Y'); ?></p>
                    <div class="news-desc"><?php the_excerpt(); ?></div>
                </div>
                    <a href="<?php echo esc_url($link); ?>" target="_blank" class="news-link">Keep reading...</a>
                </div>
            </div>
        <?php endwhile;
        echo '</div>';
        // Reset post data
        wp_reset_postdata();
    } else {
        echo '<div class="news-list news-list-without-video">';
        echo 'No articles without video found.';
        echo '</div>';
    }
    
    return ob_get_clean();
    
}


// Function to extract YouTube video URL from post content
function has_youtube_video($content) {
    $pattern = '/https:\/\/www\.youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/i';
    preg_match($pattern, $content, $matches);

    // Check if a match is found
    if (isset($matches[1])) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }

    return false;
}





// Register Custom Post Type
function custom_post_type_insights() {
    $labels = array(
        'name'                  => _x( 'Insights', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Insight', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Insights', 'text_domain' ),
        'name_admin_bar'        => __( 'Insight', 'text_domain' ),
        'archives'              => __( 'Insight Archives', 'text_domain' ),
        'attributes'            => __( 'Insight Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Insight:', 'text_domain' ),
        'all_items'             => __( 'All Insights', 'text_domain' ),
        'add_new_item'          => __( 'Add New Insight', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Insight', 'text_domain' ),
        'edit_item'             => __( 'Edit Insight', 'text_domain' ),
        'update_item'           => __( 'Update Insight', 'text_domain' ),
        'view_item'             => __( 'View Insight', 'text_domain' ),
        'view_items'            => __( 'View Insights', 'text_domain' ),
        'search_items'          => __( 'Search Insight', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into Insight', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this Insight', 'text_domain' ),
        'items_list'            => __( 'Insights list', 'text_domain' ),
        'items_list_navigation' => __( 'Insights list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter Insights list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Insight', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'taxonomies'            => array( 'topics' ), // Use 'topics' taxonomy
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-lightbulb',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'insights', $args );
}
add_action( 'init', 'custom_post_type_insights', 0 );

// Register Custom Taxonomy
function custom_taxonomy_topics() {
    $labels = array(
        'name'                       => _x( 'Topics', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Topic', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Topics', 'text_domain' ),
        'all_items'                  => __( 'All Topics', 'text_domain' ),
        'parent_item'                => __( 'Parent Topic', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Topic:', 'text_domain' ),
        'new_item_name'              => __( 'New Topic Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Topic', 'text_domain' ),
        'edit_item'                  => __( 'Edit Topic', 'text_domain' ),
        'update_item'                => __( 'Update Topic', 'text_domain' ),
        'view_item'                  => __( 'View Topic', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate topics with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove topics', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
        'popular_items'              => __( 'Popular Topics', 'text_domain' ),
        'search_items'               => __( 'Search Topics', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
        'no_terms'                   => __( 'No topics', 'text_domain' ),
        'items_list'                 => __( 'Topics list', 'text_domain' ),
        'items_list_navigation'      => __( 'Topics list navigation', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'topics', array( 'insights' ), $args );
}
add_action( 'init', 'custom_taxonomy_topics' );

// Register Custom Taxonomy
function custom_taxonomy_Featured() {
    $labels = array(
        'name'                       => _x( 'Featured', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Topic', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Featured', 'text_domain' ),
        'all_items'                  => __( 'All Featured', 'text_domain' ),
        'parent_item'                => __( 'Parent Topic', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Topic:', 'text_domain' ),
        'new_item_name'              => __( 'New Topic Name', 'text_domain' ),
        'add_new_item'               => __( 'Add New Topic', 'text_domain' ),
        'edit_item'                  => __( 'Edit Topic', 'text_domain' ),
        'update_item'                => __( 'Update Topic', 'text_domain' ),
        'view_item'                  => __( 'View Topic', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate Featured with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove Featured', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
        'popular_items'              => __( 'Popular Featured', 'text_domain' ),
        'search_items'               => __( 'Search Featured', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
        'no_terms'                   => __( 'No Featured', 'text_domain' ),
        'items_list'                 => __( 'Featured list', 'text_domain' ),
        'items_list_navigation'      => __( 'Featured list navigation', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'featured-insights', array( 'insights' ), $args );
}
add_action( 'init', 'custom_taxonomy_featured' );



// Register Custom Taxonomy for Tags
function custom_taxonomy_insight_tags() {
    $labels = array(
        'name'                       => _x( 'Insight Tags', 'Taxonomy General Name', 'text_domain' ),
        'singular_name'              => _x( 'Insight Tag', 'Taxonomy Singular Name', 'text_domain' ),
        'menu_name'                  => __( 'Insight Tags', 'text_domain' ),
        'all_items'                  => __( 'All Insight Tags', 'text_domain' ),
        'edit_item'                  => __( 'Edit Insight Tag', 'text_domain' ),
        'view_item'                  => __( 'View Insight Tag', 'text_domain' ),
        'update_item'                => __( 'Update Insight Tag', 'text_domain' ),
        'add_new_item'               => __( 'Add New Insight Tag', 'text_domain' ),
        'new_item_name'              => __( 'New Insight Tag Name', 'text_domain' ),
        'parent_item'                => __( 'Parent Insight Tag', 'text_domain' ),
        'parent_item_colon'          => __( 'Parent Insight Tag:', 'text_domain' ),
        'search_items'               => __( 'Search Insight Tags', 'text_domain' ),
        'popular_items'              => __( 'Popular Insight Tags', 'text_domain' ),
        'separate_items_with_commas' => __( 'Separate tags with commas', 'text_domain' ),
        'add_or_remove_items'        => __( 'Add or remove tags', 'text_domain' ),
        'choose_from_most_used'      => __( 'Choose from the most used tags', 'text_domain' ),
        'not_found'                  => __( 'Not Found', 'text_domain' ),
        'no_terms'                   => __( 'No tags', 'text_domain' ),
        'items_list'                 => __( 'Insight Tags list', 'text_domain' ),
        'items_list_navigation'      => __( 'Insight Tags list navigation', 'text_domain' ),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => false,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );
    register_taxonomy( 'insight_tags', array( 'insights' ), $args );
}
add_action( 'init', 'custom_taxonomy_insight_tags' );


// Function to generate dynamic topics list HTML
function dynamic_topics_list_shortcode() {
    ob_start(); ?>
<div class="insight-search-outer">
    <form  role="search" method="post" class="insight-search" id="searchform_by_insight_name">
        <div class="d-flex form-border">
            <input type="text" name="insight_search" id="insight_search" placeholder="What are you looking for?" required>
            <button type="submit"><img src="/wp-content/uploads/2024/01/search-icon.png" alt="" title=""></button>
        </div>
    </form>
    <div class="custom_clear">
			<a class="">
				<i aria-hidden="true" class="far fa-window-close"></i>
                <span class="elementor-button-text">Clear</span>
		   </a>
		</div>
</div>
    <div class="result_loader">
        <?php 
        $result_loader = wp_get_attachment_url(16783);
        echo '<img src="'.$result_loader.'" alt="">';
        ?>
    </div>
    <div class="search_category_insight">
    </div>

    <div class="cat-list child_category_insight">
        <?php
        // Get all terms from the 'topics' taxonomy
        $terms = get_terms(array(
            'taxonomy'   => 'topics',
            'hide_empty' => true,
        ));

        // echo "<pre>";
        // print_r($terms);
        // die;
        
        foreach ($terms as $term) {
            // echo $term->term_id;
            // Check if the term is a parent (has no parent)
            if ($term->parent == 0) {
                // Get child categories of the parent category
                $child_categories = get_categories(array(
                    'taxonomy' => 'topics',
                    'parent' => $term->term_id,
                ));

                $total_posts = $child_categories ? 0 : $term->count;
                foreach($child_categories as $child_cat){
                    $total_posts = $total_posts+$child_cat->count;
                }
                

                // Get term image (custom field named 'term_image')
                $term_image_id = get_term_meta($term->term_id, 'image', true);
                $term_image = wp_get_attachment_image_src($term_image_id, 'full');
                $term_link = get_term_link($term);
                ?>
                <a href="<?=$term_link?>" class="cat-item">
                    <div class="cat-icon">
                        <?php if ($term_image[0]) : ?>
                            <img src="<?php echo esc_url($term_image[0]); ?>" alt="" title="">
                        <?php else : ?>
                            <!-- Add a default image or leave it blank if no image is set -->
                            <img src="/wp-content/uploads/2024/01/default-image.png" alt="" title="">
                        <?php endif; ?>
                    </div>
                    <p><?php echo esc_html($term->name); ?></p>
                    <div class="cat-counts"><?php echo esc_html($total_posts); ?> items</div>
                </a>
            <?php }
        } ?>
    </div>

    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('dynamic_topics_list', 'dynamic_topics_list_shortcode');



/*  To increase the post pagination for per page*/

// function custom_posts_per_page_for_taxonomy( $query ) {
//     // Check if it's the main query and on a specific taxonomy archive page
//     if ( is_tax('topics') && $query->is_main_query() ) {
//         $query->set( 'posts_per_page', 15 ); // Change 15 to your desired number of posts per page
//     }
// }
// add_action( 'pre_get_posts', 'custom_posts_per_page_for_taxonomy' );


add_shortcode( 'search_bsd_post_by_tag', 'callback_search_bsd_post_by_tag' );
function callback_search_bsd_post_by_tag(){
    ?>
        <form role="search" method="get" id="searchform_by_tag" action="<?php echo home_url('/'); ?>">
        <input type="text" value="" name="tag-search" id="tag-search" required placeholder="Search here..."/>
        <input type="submit" id="searchsubmit" value="Search" />
    </form>

    <?php
}



function return_article_posts_template_old($args){

    // Get the current page from the query parameter 'paged'
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

    // Add the 'paged' parameter to the query
    $args['paged'] = $paged;

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        echo '<div class="news-list article_list_page">';
        // Loop through the articles
        while ($query->have_posts()) : $query->the_post(); 
            $logo_id = get_post_meta(get_the_ID(), 'logo', true); // Assuming 'logo_field_id' is the custom field name for the logo ID
            $logo_url = wp_get_attachment_image_url($logo_id, 'full'); // 'full' for the full-size image, you can change it to other sizes if needed
            $video_link =  get_post_meta(get_the_ID(), 'video_link', true); 
            $has_video = has_youtube_video($video_link);
            $link = get_post_meta(get_the_ID(), 'article_link', true);
            ?>
            <div class="news-item">

            <?php
            if ($video_link) : ?>
                    <?php if (strpos($video_link, 'youtube.com') !== false) : ?>
                        <!-- YouTube video -->
                        <div class="news-item-pic">
                            <iframe src="<?php echo esc_url($has_video); ?>" frameborder="0" allowfullscreen></iframe>
                        </div>
                    <?php elseif (pathinfo($video_link, PATHINFO_EXTENSION) === 'mp4') : ?>
                        <!-- MP4 video -->
                        <div class="news-item-pic">
                            <video controls>
                                <source src="<?php echo esc_url($video_link); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    <?php else : ?>
                        <!-- Display post thumbnail if it's neither YouTube nor MP4 video -->
                        <div class="news-item-pic">
                            <?php the_post_thumbnail(); ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <!-- Display post thumbnail if no video -->
                    <div class="news-item-pic">
                        <?php the_post_thumbnail(); ?>
                    </div>
                <?php endif; ?>
                

                <div class="news-item-info">
                    <div class="news-logo">
                        <?php // Assuming you have a custom field for the logo ?>
                        <img src="<?= esc_url($logo_url); ?>" alt="" title="">
                    </div>
                    <h2><?php the_title(); ?></h2>
                    <p class="news-date"><?php echo get_the_date('M j, Y'); ?></p>
                    <div class="news-desc"><?php the_excerpt(); ?></div>
                    
                    <?php 
                        if(!$has_video){
                            echo '<a href="'.$link.'" target="_blank" class="news-link">Keep reading...</a>';
                        }
                    ?>
                    
                </div>
            </div>
        <?php endwhile;
        echo '</div>';

        // Output pagination links
        echo '<div class="pagination">';
        echo paginate_links(array(
            'total' => $query->max_num_pages,
            'current' => max(1, $paged),
            'prev_text' => '&laquo; Previous',
            'next_text' => 'Next &raquo;',
        ));
        echo '</div>';
        // Reset post data
        wp_reset_postdata();
        return ob_get_clean();
    } 

    return false;


}


function return_article_posts_template($args) {

    // Get the current page from the query parameter 'paged'
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    // Add the 'paged' parameter to the query
    $args['paged'] = $paged;

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ob_start(); // Start output buffering

        echo '<div class="table-responsive">
        <table class="article_list_page">';

        // Loop through the articles
        while ($query->have_posts()) : $query->the_post();
            $logo_id = get_post_meta(get_the_ID(), 'logo', true);
            $logo_url = wp_get_attachment_image_url($logo_id, 'full');
            $video_link = get_post_meta(get_the_ID(), 'video_link', true);
            $has_video = has_youtube_video($video_link);
            $article_link = get_post_meta(get_the_ID(), 'article_link', true);
            $video_link = get_post_meta(get_the_ID(), 'video_link', true);
            $class_article = '';
            if(empty($article_link) && empty($video_link)){
                $class_article = 'disabled';
            }
            ?>

            <tr class="">
                <td class="title"><a class="<?=$class_article?>" href="<?php echo $article_link?$article_link:$video_link; ?>" target="_blank"><?php the_title(); ?></a></td>
                <!-- <td class="desc"><span class="by_author"><?php //the_excerpt(); ?></span></td> -->
                <td class="date"><?php echo get_the_date('M j, Y'); ?></td>
                <td class="media"><a class="<?=$class_article?>" href="<?php echo $article_link?$article_link:$video_link; ?>" target="_blank"><i class="fa fa-eye"></i></a></td>
            </tr>

        <?php endwhile;
        echo '</table></div>';
        // Output pagination links
        echo '<div class="pagination">';
        echo paginate_links(array(
            'total' => $query->max_num_pages,
            'current' => max(1, $paged),
            'prev_text' => '&laquo; Previous',
            'next_text' => 'Next &raquo;',
        ));
        echo '</div>';

        wp_reset_postdata(); // Reset post data
        return ob_get_clean(); // Return the buffered output

    } 

    return false;
}



function callback_show_list_of_article($atts) {
    ob_start();

    $atts = shortcode_atts(
        array(
            'posts_per_page' => -1, // Default number of articles to display
            'show_category'  => '', // Default category to show (empty for all categories)
        ),
        $atts,
        'show_articles'
    );

    // Define custom query parameters
    $args = array(
        'post_type'      => 'articles',
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'posts_per_page' => $atts['posts_per_page'], // Number of articles to display
    );

    echo return_article_posts_template($args);

}
add_shortcode('show_list_of_article', 'callback_show_list_of_article');


function callback_custom_ajax_search_bsg_post() {
    $tag_search = $_POST['tag_search'];
    $args = array(
        'post_type'      => 'articles',
        'posts_per_page' => -1,
        's'              => $tag_search, // Add this line for partial matching
    );
    
    $list_data = return_article_posts_template($args);

    if ($list_data) {
        wp_send_json_success($list_data); // Send success response
    } else {
        wp_send_json_error("No data found"); // Send error response
    }

}

add_action('wp_ajax_custom_ajax_search_bsg_post', 'callback_custom_ajax_search_bsg_post');
add_action('wp_ajax_nopriv_custom_ajax_search_bsg_post', 'callback_custom_ajax_search_bsg_post');


add_shortcode( 'search_bsd_page_profile', 'callback_search_bsd_page_profile' );
function callback_search_bsd_page_profile(){
// ob_start();
    ?>
        <form role="search" method="get" id="searchform_by_investor_name" action="<?php echo home_url('/'); ?>">
        <input type="text" value="" name="investorname-search" id="investorname-search" required placeholder="Search BSDs..."/>
        <input type="submit" id="searchsubmit_investor" value="Search" />
        </form>
        <!-- <div id="investor_search_result">

        </div> -->

    <?php
    // ob_get_content();
}


// Function to handle AJAX search request
add_action('wp_ajax_search_investor_bsds', 'search_investor_bsds');
add_action('wp_ajax_nopriv_search_investor_bsds', 'search_investor_bsds'); // For non-logged-in users

function search_investor_bsds() {
    global $wpdb;

    $search_term = sanitize_text_field($_POST['investor_search']);

    $search_term = '%' . $search_term . '%';

    $sql = $wpdb->prepare(
        "SELECT p.ID, p.post_title, p.post_author
        FROM {$wpdb->prefix}posts AS p
        LEFT JOIN {$wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id
        LEFT JOIN {$wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        LEFT JOIN {$wpdb->prefix}terms AS t ON tt.term_id = t.term_id
        WHERE p.post_type = 'investors'
        AND p.post_status = 'publish'
        AND (
            p.post_title LIKE %s
            OR t.name LIKE %s
        )
        GROUP BY p.ID
        ORDER BY p.post_date DESC",
        $search_term,
        $search_term
    );
    
    $posts = $wpdb->get_results($sql, ARRAY_A);

    if ($posts) {
        $response = [
            'success' => true,
            'html' => '',
        ];
        $response['html'] .='<h2 class="investor_search_heading">Articles, Publications, Videos and Links:</h2>';
        $response['html'] .='<div class="investors-list">';
        foreach ($posts as $post) {
            $response['html'] .= '
                <div class="investors-item">
                    <div class="investors-item-pic">
                        <img src="' . get_the_post_thumbnail_url($post['ID'], 'full') . '">
                    </div>
                    <div class="investors-item-info">
                        <h2>' . get_the_title($post['ID']) . '</h2>
                        <p>' . get_post_meta($post['ID'], 'position', true) . '</p>
                        <a href="' . get_permalink($post['ID']) . '" class="profile-link">View Profile <img src="/wp-content/uploads/2024/01/arrow-right.png" alt="" title=""></a>
                    </div>
                </div>
            ';
        }
        $response['html'] .='</div>';

            wp_send_json_success($response); // Send success response

            
    } else {
       wp_send_json_error("No data found"); // Send error response
    }

    wp_die();
}




function search_insights_shortcode() {
    ob_start(); ?>

    <form role="search" method="post" id="searchform_by_insight_name">
        <input type="text" value="" name="insight_search" id="insight_search" required placeholder="Search Insights...">
        <input type="submit" id="searchsubmit_insight" value="Search">
    </form>

    <div id="insight_search_result"></div>
    
    <?php
    return ob_get_clean();
}

// Register shortcode
add_shortcode('insight_search_form', 'search_insights_shortcode');


function search_insights() {
    global $wpdb;

    $search_term = sanitize_text_field($_POST['insight_search']);

    $sql = $wpdb->prepare(
        "SELECT ID, post_title, post_author
        FROM {$wpdb->prefix}posts
        WHERE post_type = 'insights'
        AND post_title LIKE %s
        AND post_status = 'publish'
        ORDER BY post_date DESC",
        '%' . $search_term . '%'
    );


    $search_investors = $wpdb->prepare(
        "SELECT ID
        FROM {$wpdb->prefix}posts
        WHERE post_type = 'investors'
        AND post_title LIKE '%%%s%%'
        AND post_status = 'publish'
        ORDER BY post_date DESC LIMIT 1",
        $search_term
    );
    
    $investor_data = $wpdb->get_results($search_investors, ARRAY_A);
    $investor_article_link = get_permalink($investor_data['0']['ID']);
    $investor_publish_link = get_the_date('Y', $investor_data['0']['ID']);

    $posts = $wpdb->get_results($sql, ARRAY_A);

    if ($posts) {
        $response = [
            'success' => true,
            'html' => '',
        ];

        $response['html'] .= '<div class="sub-cat-sec">
                                    <div class="container-1140">
                                    <h2>Search Results:</h2>
                                        <table>';
        $i=1;
        $investor_id = '';
        foreach ($posts as $post) {
            $article__pdf__video_link = get_post_meta($post['ID'], 'article__pdf__video_link', true);
            $author_name = get_post_meta($post['ID'], 'author_name', true);
            $publish_date = get_post_meta($post['ID'], 'publish_date', true);
            $media_type_child = get_post_meta($post['ID'], 'type', true);
            
            $insight_tags = wp_get_post_terms($post['ID'], 'insight_tags');

            $investor_link = '';

            
            foreach ($insight_tags as $insight) {
                $post_type = 'investors';
                $post_name = $insight->name;
            
                $query = $wpdb->prepare(
                    "SELECT ID
                    FROM {$wpdb->prefix}posts
                    WHERE post_type = %s
                    AND post_title = %s
                    LIMIT 1",
                    $post_type,
                    $post_name
                );
            
                $investor_id = $wpdb->get_var($query);
            
                if ($investor_id) {
                    $investor_link = get_permalink($investor_id);
                    
                }
            }

            $media_type_url_child = '';

            $post_link = '';
            switch ($media_type_child) {
                case "PDF":
                    $media_type_url_child = wp_get_attachment_url(17614);
                    $post_link = $article__pdf__video_link;
                    break;
                case "link":
                    $media_type_url_child = wp_get_attachment_url(17615);
                    $post_link = $article__pdf__video_link;
                    break;
                case "Video":
                    $media_type_url_child = wp_get_attachment_url(17616);
                    $post_link = $article__pdf__video_link;
                    break;
                case "Article":
                    $media_type_url_child = wp_get_attachment_url(17618);
                    $post_link = get_permalink($post['ID']);
                    break;
                default:
                    $media_type_url_child = wp_get_attachment_url(17614);
                    $post_link = $article__pdf__video_link;
                    break;
            }

            $post_link = !empty($investor_link)?$investor_link:$post_link;

            if($investor_data['0']['ID'] && $i == 1){
                $response['html'] .= '
                    <tr>
                        <td class="title"> <a href="'.$investor_article_link.'" target="_blank">' . get_the_title($investor_data['0']['ID']) . ' Bio</a></td>
                        <td class="auther">by Buyside Digest</td>';


                $response['html'] .= '
                        <td class="date">' .$investor_publish_link. '</td>
                        <td class="media"><a href="' . $investor_article_link . '"><img src="' . wp_get_attachment_url(17618) . '" alt="" title="' . get_the_title($investor_data['0']['ID']) . '"></a></td>
                    </tr>';
                }
            $i++;

            $response['html'] .= '
                <tr>
                    <td class="title"> <a href="'.$post_link.'" target="_blank">' . get_the_title($post['ID']) . '</a></td>';

            if ($author_name) {
                $response['html'] .= '<td class="auther">by ' . $author_name . '</td>';
            } else {
                $response['html'] .= '<td class="auther">-</td>';
            }

            $response['html'] .= '
                    <td class="date">' . ($publish_date ? $publish_date : '-') . '</td>
                    <td class="media"><a href="' . $article__pdf__video_link . '"><img src="' . $media_type_url_child . '" alt="" title="' . get_the_title($post['ID']) . '"></a></td>
                </tr>';
        }
        $response['html'] .= '</table></div"></div>';

        wp_send_json_success($response); // Send success response
    } else {
        wp_send_json_error("No data found"); // Send error response
    }

    wp_die();
}
// Add action hooks for AJAX functionality
add_action('wp_ajax_search_insights', 'search_insights');
add_action('wp_ajax_nopriv_search_insights', 'search_insights'); // For non-logged-in users



add_shortcode( 'fund_view_posts', 'callback_fund_view_posts' );

function callback_fund_view_posts() {
    ob_start();
    global $wpdb;
    // Get the current post ID
    $post_id = get_the_ID();

    // Retrieve post data
    $fund_person = get_post_meta($post_id,'investor-name', true);
    $fund_image = get_the_post_thumbnail($post_id);
    $placeholder_img = wp_get_attachment_url(17662);
    // echo "<pre>";
    // print_r($fund_image);
    // die;
    $fund_logo = get_post_meta($post_id,'logo', true); // Replace 'fund_logo' with the actual custom field name for the logo
    $fund_name = get_the_title($post_id);
    $fund_desc = get_the_content($post_id);
    $annualized_since_inception = get_post_meta($post_id,'return-since-inception', true); // Replace 'annualized_since_inception' with the actual custom field name
    $quarterly = get_post_meta($post_id,'quarterly', true);  // Replace 'quarterly' with the actual custom field name
    $ytd = get_post_meta($post_id,'ytd', true); // Replace 'ytd' with the actual custom field name
    $twitter_link = get_post_meta($post_id,'twitter-link', true); 
    $wesite_link = get_post_meta($post_id,'website-link', true); 
    $investor_name = get_post_meta($post_id,'investor-name', true); 

    $sql = $wpdb->prepare(
        "SELECT ID
        FROM {$wpdb->prefix}posts
        WHERE post_type = 'investors'
        AND post_title = '$investor_name'
        AND post_status = 'publish'
        ORDER BY post_date DESC LIMIT 1"
    );

    $investor_data = $wpdb->get_results($sql, ARRAY_A); 
   
    if(!empty($investor_data))
    { 
        $investor_ink = get_permalink($investor_data['0']['ID']); 
    }
    else{ 
        $investor_ink = '';
    }


    // $post_updated_date = get_the_modified_date('F j, Y', $post_id);
    $post_updated_date = get_post_meta($post_id,'date-for-all-the-returns', true);
    ?>

    <div class="go-back-top">
        <a href="#">Go Back</a>
    </div>
    <div class="fund-sec">
        <div class="fund-left">
            <div class="fund-image">
                <?php
                if($fund_image){
                    echo $fund_image;
                }
                else{
                    echo '<img src="'.$placeholder_img.'"/>';
                }
                ?>
            </div>
            <h3 class="fund-person"><?php echo $fund_person; ?></h3>
        </div>
        <div class="fund-right">
            <div class="fund-header">
                <?php echo do_shortcode("[hfe_template id='17833']"); ?>
                <div class="fund-logo">
                    <img src="<?php echo $fund_logo; ?>" alt="" title="">
                </div>
            </div>
            <div class="fund-name">
                <h1><?php echo $fund_name; ?></h1>
            </div>
            <div class="fund-desc">
                <p><?php echo $fund_desc; ?></p>
            </div>
            <div class="social_media_fund">
                <?php
                if($wesite_link){
                    echo '<a href="'.$wesite_link.'" target="_blank">View Website</a>';
                }
                if($twitter_link){
                    echo '<a href="'.$twitter_link.'" target="_blank">View Twitter</a>';
                }
                ?>
                
            </div>
            <div class="fund-prf-head">FUND PERFORMANCE AS OF <?=$post_updated_date?></div>
            <table class="fund-status">
                <tbody>
                    <tr>
                        <th>ANNUALIZED SINCE INCEPTION</th>
                        <th>QUARTERLY</th>
                        <th>YTD</th>
                    </tr>
                    <tr>
                        <td><?php echo $annualized_since_inception?$annualized_since_inception:'-'; ?></td>
                        <td><?php echo $quarterly?$quarterly:'-'; ?></td>
                        <td><?php echo $ytd?$ytd:'-'; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    return ob_get_clean();
}



// Hook to add a custom menu page
add_action('admin_menu', 'add_manage_mail_menu');

function add_manage_mail_menu() {
    add_menu_page(
        'Manage Mail',
        'Manage Mail',
        'manage_options',
        'manage-mail',
        'manage_mail_callback',
        'dashicons-email',
        30
    );

    // Enqueue scripts and styles for the admin page
    add_action('admin_enqueue_scripts', 'manage_mail_enqueue_scripts');
}

// Callback function to render the content of the custom menu page
function manage_mail_callback() {
    $enable_email_option = get_option('enable_email_option');
    if(isset($enable_email_option) && $enable_email_option == 'true'){
        $checked = 'checked';
    }
    ?>
    <style>
       .manage-mail-sec {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
}
        .manage-mail-sec h2 {
            font-size: 26px;
            margin-bottom: 15px;
            color: #93003f;
        }
        .manage_email_section {
    padding: 40px;
    text-align: center;
    border-radius: 5px;
    border: 1px solid #c3c4c7;
    box-shadow: 0 0 10px 3px #dddddd;
    background: #fff;
    font-size: 18px;
    display: flex;
    justify-content: start;
    align-items: start;
    flex-direction: column;
    gap: 16px;
    max-width: 100%;
    margin: 0px auto 0;
    font-weight: bold;
    height: calc(100% - 140px);
}
.manage-mail-sec > div {
    width: 50%;
    padding: 0 20px;
}
.manage-mail-sec .success {
    color: #14c914;
}
                form.manage-mail-form {
            width: 100%;
        }
        .manage_email_section input[type=checkbox] {
            margin-top: 0px;
        }
        div.manage_email_section form.manage-mail-form button {
            font-size: 16px;
            padding: 3px 20px;
            margin-top: 25px;
        }
        div.manage_email_section form.manage-mail-form button.button-primary {
            background: #2271b1;
            border: 1px solid #2271b1;
        }
        div.manage_email_section form.manage-mail-form button.button-primary:hover {
            background: #0f538b;
        }
        div.manage_email_section form.manage-mail-form button.button-secondary {
            background: #93003f;
            border: 1px solid #62022b;
            color: #fff;
            border-radius: 3px;
        }
        div.manage_email_section form.manage-mail-form button.button-secondary:hover {
            background: #62022b;
        }
        div#result-message {
            text-align: center;
            font-size: 19px;
            font-weight: bold;
            color: green;
            margin-top: 25px;
        }
        .manage_emails .notice{
            display:none;
        }
        .email_section_checkbox {
            margin-bottom: 15px;
            text-align: center;
        }
        .no_action_required {
            text-align: center;
            width: 100%;
        }
        .error-message {
            margin-top: 10px;
            font-size: 14px;
        } 
        .wp-core-ui .manage_email_section .button-primary:disabled, 
        .wp-core-ui .manage_email_section .button-primary[disabled] {
            color: #ffff!important;
            background: #93003f!important;
            border-color: #62022b!important;
        }
    </style>
    <div class="wrap manage_emails">
        <h1>Manage Mail</h1>

        <!-- Form with a checkbox -->
        
            <?php
                global $wpdb;

                $table_name = $wpdb->prefix . 'useremail_cron';
                
                // Query to retrieve data from the table
                $fund_posts = $wpdb->get_results("SELECT post_id FROM $table_name WHERE post_type = 'funds' ", ARRAY_A);
                $letter_posts = $wpdb->get_results("SELECT post_id FROM $table_name WHERE post_type = 'letters' ", ARRAY_A);
                // echo "<pre>";
                // print_r($fund_posts);
                // print_r($letter_posts);
                // die;

                echo '<div class="manage-mail-sec">';
                if($fund_posts){ ?>
                <div>
                    <h2>Funds</h2>
                    <div class="manage_email_section">
                        <form id="manage-fund-mail-form" class="manage-mail-form">
                        <div class="email_section_checkbox">
                            <input type="checkbox" id="fund_email" name="fund_email">
                            <label for="fund_email">Reset/Send Email to user for the Updated Funds</label>
                            <div class="error-message" style="display:none">Please check this box to proceed.</div>
                        </div>
                        <div class="hidden_field">
                            <input type="hidden" id="fund_hidden" name="type" value="funds">
                        </div>
                    
                        <button type="button" id="reset-fund-mail-settings" events="reset" class="button-secondary reset_send_email">Reset Email</button>

                        <button type="button" id="save-fund-mail-settings" class="button-primary fire_send_email">Send Email</button>
                        </form>
                        </div>
                        </div>

                <?php }
                else { ?>
                <div>
                <h2>Funds</h2>
                  <div class="manage_email_section">
                    <div class="no_action_required">
                            <img width="70" src="/wp-content/uploads/2024/02/tick-green.png" alt="" title="">
                            <h3>All mails send Successfully.</h3>
                        </div>
                        </div>
                    </div>
                <?php } 

                if($letter_posts){?>
                <div>
                <h2>Letters</h2>
                    <div class="manage_email_section">
                        <form id="manage-letter-mail-form" class="manage-mail-form">
                            <div class="email_section_checkbox">
                                <input type="checkbox" id="letter_email" name="letter_email">
                                <label for="letter_email">Reset/Send Email to user for the Updated Letters</label>
                                <div class="error-message"  style="display:none">Please check this box to proceed.</div>
                            </div>
                            <div class="hidden_field">
                                <input type="hidden" id="letters_hidden" name="type" value="letters">
                            </div>
                            <button type="button" id="reset-letter-mail-settings" events="reset" class="button-primary reset_send_email">Reset Email</button>
                            <button type="button" id="save-letter-mail-settings" class="button-primary fire_send_email">Send Email</button>
                        </form>
                    </div>
                    </div>
                <?php }
                else { ?>
                <div>
                <h2>Letters</h2>
                    <div class="manage_email_section">
                        <div class="no_action_required">
                        <img width="70" src="/wp-content/uploads/2024/02/tick-green.png" alt="" title="">
                            <h3>All mails send Successfully.</h3>
                        </div>
                    </div>
                </div>
                <?php } ?>
                </div>

        <div id="result-message"></div>
    </div>

    <script>
        jQuery(document).ready(function ($) {
            // Ajax request to save checkbox data
            $('.fire_send_email').on('click', function () {
                
                var formId = $(this).closest('form').attr('id');
                var checkboxId = $(this).siblings('.email_section_checkbox').find('input[type=checkbox]').attr('id');
                var type = $('#' + formId + ' input[name=type]').val();

                if (!$('#' + checkboxId).is(':checked')) {
                    $('#' + formId + ' .error-message').show();
                    return false; // Prevent form submission
                } else {
                    $('#' + formId + ' .error-message').hide();
                }

                  // Show confirmation box
                var confirmation = confirm('Are you sure you want to send the Emails?');
                if (!confirmation) {
                    return false; // Do nothing if user cancels
                }

                $(this).text('Sending...');
                $(this).css("cursor","not-allowed");
                $('button').prop('disabled', true);
                
                var data = {
                    action: 'send_import_start_email_cron_callback',
                    enable_email: $('#' + checkboxId).is(':checked'),
                    type: type,
                };

                $.post(ajaxurl, data, function (response) {
                    $('button').prop('disabled', false);
                    var html = $('#' + formId).closest('.manage_email_section').html(response.data.message);
                });
            });


            $('.reset_send_email').on('click', function () {
                
                var formId = $(this).closest('form').attr('id');
                var checkboxId = $(this).siblings('.email_section_checkbox').find('input[type=checkbox]').attr('id');
                var type = $('#' + formId + ' input[name=type]').val();
                var events = $(this).attr('events');

                if (!$('#' + checkboxId).is(':checked')) {
                    $('#' + formId + ' .error-message').show();
                    return false; // Prevent form submission
                } else {
                    $('#' + formId + ' .error-message').hide();
                }

                  // Show confirmation box
                var confirmation = confirm('Are you sure you want to reset the Emails?');
                if (!confirmation) {
                    return false; // Do nothing if user cancels
                }

                $(this).text('Reset...');
                $(this).css("cursor","not-allowed");
                $('button').prop('disabled', true);
                
                var data = {
                    action: 'reset_email_cron_callback',
                    reset_email: $('#' + checkboxId).is(':checked'),
                    type: type,
                    event_type: events,
                };
                // console.log(data)


                $.post(ajaxurl, data, function (response) {
                    $('button').prop('disabled', false);
                    var html = $('#' + formId).closest('.manage_email_section').html(response.data.message);
                });
            });
        });
    </script>   

    <?php
}

add_action('wp_ajax_reset_email_cron_callback', 'callback_reset_email_cron_callback');
function callback_reset_email_cron_callback() {
    global $wpdb;

    if($_POST['event_type']=='reset'){
    // Define the query to delete posts of the 'funds' post type
    $query = $wpdb->prepare("DELETE FROM wp_useremail_cron WHERE post_type = %s", $_POST['type']);
    // Execute the query
    $result = $wpdb->query($query);

    if($result){
        $return_message = '<div class="no_action_required">
                        <img width="70" src="/wp-content/uploads/2024/02/tick-green.png" alt="" title="">
                            <h3>All '.$_POST['type'].' mails reset Successfully.</h3>
                        </div>';

        wp_send_json_success(['status'=>'success','message'=>$return_message]);   
    }
    wp_die(); // Always include this to end AJAX requests
    }

}


// Enqueue scripts and styles for the admin page
function manage_mail_enqueue_scripts($hook) {
    // Load scripts and styles only on the custom menu page
    if ($hook === 'toplevel_page_manage-mail') {
        wp_enqueue_script('jquery');
    }
}

// Ajax handler to save checkbox data
// add_action('wp_ajax_save_mail_settings', 'save_mail_settings');

// function save_mail_settings() {
//     check_ajax_referer('manage_mail_nonce', 'security');

//     $enable_email = $_POST['enable_email'];


//     // Save the checkbox data to the options table
//     update_option('enable_email_option', $enable_email);

//     echo 'Settings saved successfully!';
//     wp_die();
// }





add_shortcode( 'ticker_view_posts', 'callback_ticker_view_posts' );

function callback_ticker_view_posts() {
    ob_start();
    if (is_tax('tickers')) {
        $term_id = get_queried_object_id();
    }
    $ticker = get_term($term_id);
    $ticker_image = get_term_meta( $term_id, 'ticker_image', true);
    $ticker_name = $ticker->name;
    $ticker_description = $ticker->description;
    $company_name = get_term_meta( $term_id, 'company_name', true);
    $website_link = get_term_meta( $term_id, 'website_link', true);
    $ticker_heading = get_term_meta( $term_id, 'ticker_heading', true);
 
    ?>

    <div class="go-back-top">
        <a href="#">Go Back</a>
    </div>
    <div class="fund-sec">
        <div class="fund-left">
            <div class="fund-image">
                <?php
                if($ticker_image){
                    echo $ticker_image;
                }
                else{
                    echo '<p>'.$ticker_name.'</p>';
                }
                ?>
            </div>
        </div>
        <div class="fund-right">
            <div class="fund-header">
                <?php echo do_shortcode("[hfe_template id='18295']"); ?>
                <!-- <div class="fund-logo">
                    <img src="" alt="" title="">
                </div> -->
            </div>
            <div class="fund-name">
                <h1><?php echo $company_name; ?></h1>
            </div>
            <div class="fund-desc">
                <h3><?php echo $ticker_heading; ?></h3>
                <p><?php echo $ticker_description; ?></p>
            </div>
            <div class="social_media_fund">
                <?php 
                    if($website_link){ 
                        echo '<a href="'.$website_link.'" target="_blank">View Website</a>';
                    }
                ?>
                
            </div>
        </div>
    </div>

    <?php
    return ob_get_clean();
}


// Add shortcode above the footer on single posts of post type 'investors'
function callback_investors_insight_posts() {
    if (is_singular('investors')) {
        global $wpdb;
        $investor_id = get_the_ID();
        $search_term = get_the_title($investor_id);

        $sql = $wpdb->prepare(
            "SELECT ID, post_title, post_author
            FROM {$wpdb->prefix}posts
            WHERE post_type = 'insights'
            AND post_title LIKE %s
            AND post_status = 'publish'
            ORDER BY post_date DESC",
            '%' . $search_term . '%'
        );
    
        $posts = $wpdb->get_results($sql, ARRAY_A); 
            
        // Set up the query arguments
        $atts = shortcode_atts(
            array(
                'posts_per_page' => -1, // Default number of articles to display
                'show_category'  => '', // Default category to show (empty for all categories)
            ),
            $atts,
            'show_articles'
        );
        
        // Define custom query parameters
        $args = array(
            'post_type'      => 'articles',
            'post_status'    => 'publish', 
            'posts_per_page' => $atts['posts_per_page'], // Number of articles to display
        );
        
        $query = new WP_Query($args);
        if($query->have_posts() || $posts)
        {
            $response = [
                'success' => true,
                'html' => '',
            ];

            $response['html'] .= '<div class="sub-cat-sec"><div class=""><h2>Articles, Videos, Letters and other Links</h2><table>';
        }
        if ($posts) {
            foreach ($posts as $post) {
                $article__pdf__video_link = get_post_meta($post['ID'], 'article__pdf__video_link', true);
                $author_name = get_post_meta($post['ID'], 'author_name', true);
                $publish_date = get_post_meta($post['ID'], 'publish_date', true);
                $media_type_child = get_post_meta($post['ID'], 'type', true);
                $media_type_url_child = '';
    
                $post_link = '';
                switch ($media_type_child) {
                    case "PDF":
                        $media_type_url_child = wp_get_attachment_url(17614);
                        $post_link = $article__pdf__video_link;
                        break;
                    case "link":
                        $media_type_url_child = wp_get_attachment_url(17615);
                        $post_link = $article__pdf__video_link;
                        break;
                    case "Video":
                        $media_type_url_child = wp_get_attachment_url(17616);
                        $post_link = $article__pdf__video_link;
                        break;
                    case "Article":
                        $media_type_url_child = wp_get_attachment_url(17618);
                        $post_link = get_permalink($post['ID']);
                        break;
                    default:
                        $media_type_url_child = wp_get_attachment_url(17614);
                        $post_link = $article__pdf__video_link;
                        break;
                }
    
                $response['html'] .= '
                    <tr>
                        <td class="title"> <a href="'.$post_link.'" target="_blank">' . get_the_title($post['ID']) . '</a></td>';
    
                if ($author_name) {
                    $response['html'] .= '<td class="auther">by ' . $author_name . '</td>';
                } else {
                    $response['html'] .= '<td class="auther">-</td>';
                }
    
                $response['html'] .= '
                        <td class="date">' . ($publish_date ? $publish_date : '-') . '</td>
                        <td class="media"><a href="' . $article__pdf__video_link . '"><img src="' . $media_type_url_child . '" alt="" title="' . get_the_title($post['ID']) . '"></a></td>
                    </tr>';
            }

        } 
        if($query->have_posts()){
            $response['html'] .='<tr><td class="title">';
            //ob_start(); // Start output buffering
        
            // Loop through the articles
            while ($query->have_posts()) : 
                $article_link = get_post_meta(get_the_ID(), 'investor_name_article', true); 
                if(!empty($article_link) && $search_term == $article_link){                    
                    $response['html'] .= ' <a href="#" target="_blank">'.$query->the_post().'</a>';
                }
            endwhile;   
            $response['html'] .= '</td></tr>';     
            //wp_reset_postdata(); // Reset post data 

        }
        if($query->have_posts() || $posts)
        {
            $response['html'] .= '</table></div></div>';
            echo $response['html'];
        }
    }
    else{?>
    <style>#investor_insight_sec{display: none;}</style>
    <?php
    }
}
add_shortcode('investors_insight_posts', 'callback_investors_insight_posts');


add_shortcode('show_list_of_tickers_data','callback_show_list_of_tickers_data');
function callback_show_list_of_tickers_data(){
    global $wpdb;
    $current_term = get_queried_object();

    $post_type = 'letters';
    $taxonomy = 'tickers';
    $term_id = $current_term->term_id;

    $query = $wpdb->prepare(
    "SELECT p.ID, jrd.parent_object_id
    FROM {$wpdb->prefix}posts p
    JOIN {$wpdb->prefix}term_relationships tr ON p.ID = tr.object_id
    JOIN {$wpdb->prefix}term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
    JOIN {$wpdb->prefix}terms t ON tt.term_id = t.term_id
    JOIN {$wpdb->prefix}jet_rel_default jrd ON p.ID = jrd.child_object_id
    JOIN {$wpdb->prefix}term_relationships tr2 ON p.ID = tr2.object_id
    JOIN {$wpdb->prefix}term_taxonomy tt2 ON tr2.term_taxonomy_id = tt2.term_taxonomy_id
    JOIN {$wpdb->prefix}terms t2 ON tt2.term_id = t2.term_id
    WHERE p.post_type = %s
    AND p.post_status = 'publish'
    AND tt.taxonomy = %s
    AND t.term_id = %d
    AND tt2.taxonomy = 'quarter'
    GROUP BY p.ID
    ORDER BY t2.name DESC",
    $post_type,
    $taxonomy,
    $term_id
);
    
    
    $results = $wpdb->get_results($query);

    echo '<div class="table-responsive"><table class="letters-with-tickers">';
    foreach($results as $result){
         $post_id = $result->ID;
         $fund_post_id = $result->parent_object_id;

         $query_term = $wpdb->prepare(
            "SELECT t.term_id, t.name, t.slug, tt.taxonomy
            FROM {$wpdb->prefix}terms t
            JOIN {$wpdb->prefix}term_taxonomy tt ON t.term_id = tt.term_id
            JOIN {$wpdb->prefix}term_relationships tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
            WHERE (tt.taxonomy = %s OR tt.taxonomy = 'quarter' OR tt.taxonomy = 'key-person')
            AND tr.object_id = %d",
            $taxonomy,
            $post_id
        );

        $results_term = $wpdb->get_results($query_term);

        ?>
            <?php 
                $ticker_terms = array();
                $quarter_terms = array();
                $key_person_terms = array();

                // Iterate through the results
                foreach ($results_term as $result) {
                    // Check the taxonomy of each term and categorize accordingly
                    switch ($result->taxonomy) {
                        case 'tickers':
                            $ticker_terms[] = $result;
                            break;
                        case 'quarter':
                            $quarter_terms[] = $result;
                            break;
                        case 'key-person':
                            $key_person_terms[] = $result;
                            break;
                        default:
                            // Handle unexpected taxonomy (if any)
                            break;
                    }

            ?>
           
            <?php } 
            ?>
            <tr>
                <td><a href="<?=get_permalink($fund_post_id)?>"><?= get_the_title($fund_post_id) ?></td>
                <td>
                    <?php 
                    $key_person_names = array();
                    foreach($key_person_terms as $key_person){
                        $key_person_names[] = $key_person->name;
                    }
                    echo implode(', ', $key_person_names);
                    ?>
                </td>
                <td>
                    <?php 
                    $letter_link = get_post_meta($post_id, 'letter-link', true);
                    echo '<a href="'.$letter_link.'" style="font-weight:600 !important">'.$quarter_terms[0]->name.' Letter </a>';
                    ?>
                </td>
                <td>
                    
                    <?php 
                    $ticker_links = array();
                    foreach($ticker_terms as $term){
                        $ticker_term = get_term($term->term_id, 'tickers');
                        $ticker_term_link = get_term_link($ticker_term);
                        $ticker_links[] = '<a href="'.$ticker_term_link.'">'.$term->name.'</a>';
                    }
                    echo implode(', ', $ticker_links);
                    ?>
                </td>
            </tr>

        <?php
        //  echo "<pre>";
        //  print_r($results_term);
        //  die;
    }
    echo "</table></div>";


}



add_shortcode('investor_next_prev','callback_investor_next_prev');

function callback_investor_next_prev(){
    $post_type = get_post_type();

    if ($post_type === 'investors') {
        $current_post = get_post();

        $args = array(
            'post_type' => 'investors',
            'posts_per_page' => -1, // Retrieve all posts
            'post_status' => 'publish',
            'orderby' => 'name',
            'order' => 'asc',
        );

        $investors_query = new WP_Query($args);

        // Find the current post index in the query results
        $current_index = array_search($current_post->ID, wp_list_pluck($investors_query->posts, 'ID'));

        // Get the previous and next posts based on the index
        $prev_post = ($current_index > 0) ? $investors_query->posts[$current_index - 1] : null;
        $next_post = ($current_index < count($investors_query->posts) - 1) ? $investors_query->posts[$current_index + 1] : null;

        // Output navigation links
        if ($prev_post || $next_post) {
            echo '<div class="navigation-links">';
            
            if ($prev_post) {
                echo '<div class="nav-previous">';
                echo '<a href="' . get_permalink($prev_post->ID) . '" rel="prev">Previous</a>';
                echo '</div>';
            }

            if ($next_post) {
                echo '<div class="nav-next">';
                echo '<a href="' . get_permalink($next_post->ID) . '" rel="next">Next</a>';
                echo '</div>';
            }

            echo '</div>';
        }

        // Reset the custom query
        wp_reset_postdata();
    }
}




// Register Custom Post Type
function register_elevator_pitch_post_type() {
    $labels = array(
        'name'                  => _x('Elevator Pitches', 'Post Type General Name', 'text_domain'),
        'singular_name'         => _x('Elevator Pitch', 'Post Type Singular Name', 'text_domain'),
        'menu_name'             => __('Elevator Pitches', 'text_domain'),
        'name_admin_bar'        => __('Elevator Pitch', 'text_domain'),
        'archives'              => __('Elevator Pitch Archives', 'text_domain'),
        'attributes'            => __('Elevator Pitch Attributes', 'text_domain'),
        'parent_item_colon'     => __('Parent Elevator Pitch:', 'text_domain'),
        'all_items'             => __('All Elevator Pitches', 'text_domain'),
        'add_new_item'          => __('Add New Elevator Pitch', 'text_domain'),
        'add_new'               => __('Add New', 'text_domain'),
        'new_item'              => __('New Elevator Pitch', 'text_domain'),
        'edit_item'             => __('Edit Elevator Pitch', 'text_domain'),
        'update_item'           => __('Update Elevator Pitch', 'text_domain'),
        'view_item'             => __('View Elevator Pitch', 'text_domain'),
        'view_items'            => __('View Elevator Pitches', 'text_domain'),
        'search_items'          => __('Search Elevator Pitch', 'text_domain'),
        'not_found'             => __('Not found', 'text_domain'),
        'not_found_in_trash'    => __('Not found in Trash', 'text_domain'),
        'featured_image'        => __('Featured Image', 'text_domain'),
        'set_featured_image'    => __('Set featured image', 'text_domain'),
        'remove_featured_image' => __('Remove featured image', 'text_domain'),
        'use_featured_image'    => __('Use as featured image', 'text_domain'),
        'insert_into_item'      => __('Insert into Elevator Pitch', 'text_domain'),
        'uploaded_to_this_item' => __('Uploaded to this Elevator Pitch', 'text_domain'),
        'items_list'            => __('Elevator Pitches list', 'text_domain'),
        'items_list_navigation' => __('Elevator Pitches list navigation', 'text_domain'),
        'filter_items_list'     => __('Filter Elevator Pitches list', 'text_domain'),
    );
    $args = array(
        'label'                 => __('Elevator Pitch', 'text_domain'),
        'description'           => __('Post Type Description', 'text_domain'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-megaphone', // You can choose a different dashicon or use a custom URL for an icon
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
    );
    register_post_type('elevator_pitch', $args);
}
add_action('init', 'register_elevator_pitch_post_type', 0);



add_action('wp_ajax_callback_elevator_pitches_posts', 'callback_elevator_pitches_posts');
add_action('wp_ajax_nopriv_callback_elevator_pitches_posts', 'callback_elevator_pitches_posts'); // For non-logged-in users

add_shortcode('elevator_pitches_posts', 'callback_elevator_pitches_posts');

function callback_elevator_pitches_posts($atts) {
    // ob_start();

    $atts = shortcode_atts(
        array(
            'featured_term' => '', // Default empty, replace with the default term if needed
            'posts_per_page' => 15, // Default 15 posts per page
        ),
        $atts,
        'elevator_pitches_posts'
    );

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

    $args = array(
        'post_type'      => 'elevator_pitch',
        'posts_per_page' => $atts['posts_per_page'], // You can adjust the number of posts to display
        'orderby'        => 'modified', 
        'order'          => 'DESC',
        'paged'          => $paged,
    );

    // If featured_term is provided, add taxonomy query
    if (!empty($atts['featured_term'])) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category', // Replace with the actual taxonomy name
                'field'    => 'slug',
                'terms'    => $atts['featured_term'],
            ),
        );
    }

    if ( is_tax( 'tickers' ) ) {
        $current_term = get_queried_object();
        // echo "<pre>";
        // print_r($current_term);
        // die;
        $args['meta_query'] = array(
            array(
                'key'     => 'tickers', // Adjust 'tickers' to the actual meta key where ticker terms are stored
                'value'   => $current_term->term_id, // Serialized value should contain the term ID within quotes
                'compare' => 'LIKE', // Perform a wildcard search for exact match within the serialized array
            ),
        );
    }


    if (!empty($_POST['ticker_search'])) {
        $ticker_term = sanitize_text_field($_POST['ticker_search']);
    
        // Get the term ID by searching for the term name
        $term = get_term_by('name', $ticker_term, 'tickers'); // Replace 'ticker' with the actual taxonomy name
            $term_id = $term->term_id;
            $args['meta_query'] = array(
                array(
                    'key'     => 'tickers', // Adjust 'tickers' to the actual meta key where ticker terms are stored
                    'value'   => $term_id, // Serialized value should contain the term ID within quotes
                    //'compare' => 'LIKE', // Perform a wildcard search for exact match within the serialized array
                ),
            );
    }

    $elevator_pitches = new WP_Query($args);
    // echo "<pre>";
    // print_r($elevator_pitches);
    // die;

    if ($elevator_pitches->have_posts()) {
        echo '<div class="elevetor_pitch_ticker_section">';
        while ($elevator_pitches->have_posts()) {
            $elevator_pitches->the_post();
            $link_fund_id = get_field('link_fund');
            // echo "<pre>";
            // print_r($link_fund_id );
            // die;
            ?>
            <div class="elev-pitch">
                <div class="elev-pitch-head">
                    <div class="title"><?php the_title(); ?> <span class="pitched_by_shift">Pitched by <?=$link_fund_id->post_title?> </span></div>
                    <?php
                    $tickers = get_field('tickers');
                    $term_tickers = get_term( $tickers );
                    // echo "<pre>";
                    // print_r($tickers);
                    // die;
                    $elevator_pitches_letters = get_field('elevator_pitches_letters');
                    $letters_pdf = get_post_meta( $elevator_pitches_letters, 'letter-link', true );

                   


                    if (!empty($tickers)) {
                        echo '<div class="ticker-name">Ticker: <a href="' . esc_url(get_term_link($tickers)) . '" style="text-transform: uppercase;" target="_blank">' . esc_html($term_tickers->name) . '</a></div>';
                    }
                    ?>
                </div>
                <div class="elev-pitch-info">
                    <div class="elev-pitch-logo">
                        <?php 
                        $fund_link_elev_pitch = '';
                        if (!empty($tickers)) {
                            $fund_link_elev_pitch = get_term_link($tickers);
                            ?>
                            <a href="<?= $fund_link_elev_pitch ?>" target="_blank">
                                <?php the_post_thumbnail('full'); ?>
                            </a>
                        <?php } else {
                            the_post_thumbnail('full');
                        } ?>

                    </div>
                    <div class="elev-pitch-stats">
                        <?php if (have_rows('data_values')) : ?>
                            <?php while (have_rows('data_values')) :
                                the_row(); ?>

                                <div class="elavator_data_val">
                                    <?php if ($add_values_here = get_sub_field('add_values_here')) : ?>
                                        <?php echo esc_html($add_values_here); ?>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php
                    if(!empty($link_fund_id)){
                        $fund_logo_image_url = get_post_meta( $link_fund_id->ID, 'logo', true );
                        $fund_link = get_post_meta( $link_fund_id->ID, 'logo', true );
                        // echo '<a href="'.get_permalink($link_fund_id->ID).'" target="_blank"><img src="'.$fund_logo_image_url.'"></a>';
                    } 
                    ?>
                    </div>
                    <div class="elev-pitch-buttons">
                    <?php
                    if ($link_fund_id) {
                        echo '<div class="elev-pitch-link"><a href="' . get_permalink($link_fund_id->ID) . '" target="_blank">View Fund</a></div>';
                    } else {
                        echo '<div class="elev-pitch-link disabled"><a href="javascript:void();">View Fund</a></div>';
                    }

                    if ($letters_pdf) {
                        echo '<div class="elev-pitch-link"><a href="' . $letters_pdf . '" target="_blank">View Letter</a></div>';
                    } else {
                        echo '<div class="elev-pitch-link disabled"><a href="javascript:void();">View Letter</a></div>';
                    }
                    ?>
                    </div>

                </div>
                <?php the_content(); ?>
               
                <br/>
               <div class="modified_elevt"><span>Updated on:</span> <?= get_the_modified_date('M. j, Y');; ?></div>
                
            </div>
        <?php
        }
        wp_reset_postdata();

        echo '</div>';
        
        // Pagination
        if(!is_page('19129')){
            $total_pages = $elevator_pitches->max_num_pages;
            if ($total_pages > 1) {
                $current_page = max(1, get_query_var('paged'));
                echo '<div class="pagination">';
                echo paginate_links(array(
                    'base'      => get_pagenum_link(1) . '%_%',
                    'format'    => 'page/%#%',
                    'current'   => $current_page,
                    'total'     => $total_pages,
                    'prev_text' => __('« Previous'),
                    'next_text' => __('Next »'),
                ));
                echo '</div>';
            }
        }
        
        if(is_page(19129)){
            echo '<center class="ajax-load-more"><a class="load-more-btn" href="'.get_permalink(19440).'">Load More</a></center>';
        }

        if (!empty($_POST['ticker_search'])) {
            exit();
        }
        

    } else {
        if ( !is_tax( 'tickers' ) ) {
        echo '<center><h4>No data found</h4></center>';
        }
        if (!empty($_POST['ticker_search'])) {
            exit();
        }
    }
    
    // return ob_get_clean(); 
}




add_shortcode( 'search_elevetor_pitches', 'callback_search_elevetor_pitches' );
function callback_search_elevetor_pitches(){
ob_start();
    ?>
        <form role="search" method="get" id="searchform_by_ticker_name" action="<?php echo home_url('/'); ?>">
        <input type="text" value="" name="ticker-search" id="ticker-search" required placeholder="Search ticker..."/>
        <input type="submit" id="searchsubmit_ticker" value="Search" />
        </form>
        <!-- <div id="investor_search_result">

        </div> -->

    <?php
   return ob_get_clean(); 
}


add_shortcode('elevetor_articles_list','callback_elevetor_articles_list');
function callback_elevetor_articles_list(){
    // Set up default attributes
    $atts = isset($atts) ? $atts : array();
    $atts = shortcode_atts(array(
        'posts_per_page' => -1, // Default number of posts per page
    ), $atts);

    // Query articles
    $args = array(
        'post_type'      => 'elevator_article', // Assuming articles are regular posts
        'posts_per_page' => $atts['posts_per_page'],
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if ( is_tax( 'tickers' ) ) {
        $current_term = get_queried_object();
        // echo "<pre>";
        // print_r($current_term);
        // die;
        $args['meta_query'] = array(
            array(
                'key'     => 'tickers', // Adjust 'tickers' to the actual meta key where ticker terms are stored
                'value'   => '"'.$current_term->term_id.'"', // Serialized value should contain the term ID within quotes
                'compare' => 'LIKE', // Perform a wildcard search for exact match within the serialized array
            ),
        );
    }

    $articles_query = new WP_Query($args);

    // Start generating HTML
    ob_start(); // Start output buffering

    if ($articles_query->have_posts()) {
        if (is_tax( 'tickers' ) ) {
        echo '<center><h3>Investment Elevator Pitch Articles</h3></center>';
        }
        echo '<table class="news-list article_list_page elevetor_articles_list">'; // Start table
        while ($articles_query->have_posts()) {
            $articles_query->the_post();
            $link = get_permalink(get_the_ID());
            $author = get_post_meta(get_the_ID(), 'posted_by', true);
            $class_article = '';
            if(empty($link)){
                $class_article = 'disabled';
            }
            ?>
            <tr class="">
                <td class="title"><a class="<?=$class_article?>" href="<?php echo $link; ?>"><?php the_title(); ?></a></td>
                <td class="date"><?php echo get_the_date('M j, Y'); ?></td>
                <td class="auth"><span class="by_author">By: <?= $author ?></span></td>
            </tr>
            <?php
        }
        echo '</table>'; // End table
        wp_reset_postdata(); // Reset post data
    } else {
        if ( !is_tax( 'tickers' ) ) {
        echo 'No articles found.';
        }
    }

    // End generating HTML
    $output = ob_get_clean(); // Get the buffered output
    return $output;
    ?>
    <?php
}


// Register Custom Post Type
function create_elevator_articles_post_type() {
    $labels = array(
        'name'                  => _x( 'Elevator Articles', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Elevator Article', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Elevator Articles', 'text_domain' ),
        'name_admin_bar'        => __( 'Elevator Article', 'text_domain' ),
        'archives'              => __( 'Elevator Article Archives', 'text_domain' ),
        'attributes'            => __( 'Elevator Article Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Elevator Article:', 'text_domain' ),
        'all_items'             => __( 'All Elevator Articles', 'text_domain' ),
        'add_new_item'          => __( 'Add New Elevator Article', 'text_domain' ),
        'add_new'               => __( 'Add New', 'text_domain' ),
        'new_item'              => __( 'New Elevator Article', 'text_domain' ),
        'edit_item'             => __( 'Edit Elevator Article', 'text_domain' ),
        'update_item'           => __( 'Update Elevator Article', 'text_domain' ),
        'view_item'             => __( 'View Elevator Article', 'text_domain' ),
        'view_items'            => __( 'View Elevator Articles', 'text_domain' ),
        'search_items'          => __( 'Search Elevator Article', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into Elevator Article', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this Elevator Article', 'text_domain' ),
        'items_list'            => __( 'Elevator Articles list', 'text_domain' ),
        'items_list_navigation' => __( 'Elevator Articles list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter Elevator Articles list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Elevator Article', 'text_domain' ),
        'description'           => __( 'Post Type Description', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'menu_icon'             => 'dashicons-feedback', // You can change the icon as per your preference
    );
    register_post_type( 'elevator_article', $args );
}
add_action( 'init', 'create_elevator_articles_post_type', 0 );




add_action('wp_ajax_redirect_to_callback_elevator_pitches_posts','redirect_to_callback_elevator_pitches_posts');
add_action('wp_ajax_nopriv_redirect_to_callback_elevator_pitches_posts','redirect_to_callback_elevator_pitches_posts');
function redirect_to_callback_elevator_pitches_posts(){
//   die("kdfjdk");
    $term_exist = get_term_by('name', $_POST['ticker_search'], 'tickers');

    if ($term_exist) {
        $ticker_link = get_term_link($term_exist->term_id);
        wp_send_json_success(['status' => 'success', 'url' => $ticker_link]);
    } else {
        wp_send_json_error(['status' => 'error', 'message' => '<center><h4>No data found</h4></center>']);
    }
}

// Hook into Gravity Forms submission to trigger custom activation notification
// Hook into Gravity Forms submission to trigger custom activation notification
// add_action('gform_after_submission', 'send_custom_activation_notification', 10, 2);

function send_custom_activation_notification($entry, $form) {
    // Replace 'X' with the ID of the custom hidden field storing the user's email address
    $user_email = rgar($entry, '3');

    $user_id = email_exists($user_email);
    
    // Generate activation key
    $activation_key = wp_generate_password(20, false);

    // Save activation key to user meta
    update_user_meta($user_id, 'activation_key', $activation_key);

    // Generate activation link
    $activation_link = add_query_arg(array(
        'action' => 'activate_account',
        'email' => urlencode($user_email),
        'key' => $activation_key
    ), home_url());

    // Email subject
    $subject = 'Activate Your Account';

    // Email message
    $message = "Dear User,\r\n\r\n";
    $message .= "Welcome to our site! Please click the following link to activate your account:\r\n";
    $message .= $activation_link . "\r\n\r\n";
    $message .= "If you have any questions, feel free to contact us.\r\n\r\n";
    $message .= "Thank you,\r\n";
    $message .= 'Your Site Name'; // Replace with your site name

    // Email headers
    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
    );

    // Send the email
    wp_mail($user_email, $subject, $message, $headers);
}

// Hook into WordPress init to handle account activation
// add_action('init', 'custom_handle_activation');

function custom_handle_activation() {
    if (isset($_GET['action']) && $_GET['action'] === 'activate_account') {
        $user_email = isset($_GET['email']) ? urldecode($_GET['email']) : '';
        $activation_key = isset($_GET['key']) ? $_GET['key'] : '';

        // Validate activation key
        $user_id = email_exists($user_email);
        
        if ($user_id && $activation_key === get_user_meta($user_id, 'activation_key', true)) {
            // Activate the user account
            delete_user_meta($user_id, 'activation_key');

            // Optionally, log in the user automatically
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id, true);
            wp_redirect(home_url());
            exit;
        } else {
            // Invalid activation link
            // wp_die('Invalid activation link.');
        }
    }
}

 



// Hook into the user activation process
add_action('gform_user_registered', 'custom_redirect_after_activation', 10, 4);
function custom_redirect_after_activation($user_id, $config, $entry, $user_pass) {
    // Check if activation is successful
    if (is_wp_error($user_id)) {
        return;
    }
    wp_clear_auth_cookie();
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    
    // Redirect to the login page after activation
    $redirect_url = site_url().'/login/?account=activate'; // You can replace this with your desired URL
    wp_safe_redirect($redirect_url);
    exit;
}

// Define the function that will be executed when the shortcode is encountered
function callback_join_for_free() {
    // Check if the user is logged in
    if ( ! is_user_logged_in() ) {
        // If the user is logged in, display the button
        
        return '<div class="elementor-button-wrapper">
        <center>
            <a class="elementor-button elementor-button-link elementor-size-md brown-btn" href="'.get_permalink('95').'">
            <span class="elementor-button-content-wrapper">
            <span class="elementor-button-text">join for free</span>
            </span>
            </a>
        </center>
        </div>';
    } 
}

// Register the shortcode with WordPress
add_shortcode('join_for_free', 'callback_join_for_free');

add_action( 'rest_api_init', 'register_webhook_endpoint' );

function register_webhook_endpoint() {
    register_rest_route( 'your-namespace/v1', '/webhook', array(
        'methods' => 'POST',
        'callback' => 'handle_webhook_request',
    ) );
}

function handle_webhook_request( $request ) {
    // Process webhook data here
    // You can access form data using $request->get_params()

    // Example: Send data to Elastic Email
    $form_data = $request->get_params();
    // Process form data and interact with Elastic Email API
}



//  add_action('gform_after_submission', 'send_to_elastic_email', 10, 2);
//  function send_to_elastic_email($entry, $form) {
//     // Replace these with your Elastic Email API key and list ID
//     $user_email = rgar($entry, '3'); // Replace '3' with the field ID for email
//     // $user_name = rgar($entry, '2');
    
//     $apiKey = '5C10F3B44C104981F9C1E2B5468A1AEE7CEFEED89431AD3F44540902F85F437B4E0D8F84ADBEBA7B237F25557092D09D';

//     $url = 'https://api.elasticemail.com//v2/contact/add?apikey='. $apiKey;

//     $headers = [
//         'Content-Type: application/json',
//        // 'Authorization: Basic ' . base64_encode('apikey:' . $apiKey)
//     ];
//     $data = [
//         'email' => $user_email, 
//         "Status"  => "Transactional",
//         'firstName' => 'John',
//         'lastName' => 'Doe',
          
//     ];
//     echo "<pre>";
//     print_r($headers);
//     print_r($data);
//     echo "</pre>";

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     $response = curl_exec($ch);

 
//     if ($response === false) {
//         echo 'Error: ' . curl_error($ch);
//     } else {
//         $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//         if ($statusCode == 200) {
//             $responseData = json_decode($response, true);
//             // Process the response data
//             print_r($responseData);
//         } else {
//             echo 'Error: HTTP ' . $statusCode . ' ' . $response;
//         }
//     }

//     curl_close($ch);
//  }

 add_action('gform_post_submission_7', 'send_to_elastic_email', 10, 2);

 function send_to_elastic_email($entry, $form) {
  
  // Replace with your Elastic Email API key 
  $ee_options = get_option( 'ee_options' );
  $api_key = isset($ee_options['ee_apikey']) ? $ee_options['ee_apikey'] : '';
  $url = 'https://api.elasticemail.com/v4/contacts';
  // Check if it's the specific form (replace FORM_ID with your actual form ID)
  if ($form['id'] != 7) {
    return;
  }

  // Extract data from Gravity Forms submission (replace with your field IDs)
  $user_email = rgar($entry, '3'); // Replace '3' with the field ID for email
  $fullName = rgar($entry, '5'); // Replace '1' with the field ID for name (optional)
  $industry_primary_role = rgar($entry, '7'); // Replace '1' with the field ID for name (optional)
  $accredited = strip_tags(rgar($entry, '6')); // Replace '1' with the field ID for name (optional) 
  $parts = explode(' ', $fullName);
  $firstName = !empty($parts[0]) ? $parts[0] : '';
  $lastName = !empty($parts[1]) ? implode(' ', array_slice($parts, 1)) : '';

  // Option 2: Using substr with strpos (handles names without spaces)
  if (empty($parts)) {
    $lastName = $fullName;
    $firstName = '';
  } else {
    $lastNamePos = strpos($fullName, ' ');
    if ($lastNamePos !== false) {
      $firstName = substr($fullName, 0, $lastNamePos);
      $lastName = substr($fullName, $lastNamePos + 1);
    } else {
      $firstName = $fullName;
      $lastName = '';
    }
  }
 
 $args = array(
        'headers' => array(
            'Content-Type'  => 'application/json',
            'X-ElasticEmail-ApiKey' => $api_key, // Corrected header
        ),
        'body' => json_encode( array(
            array(
                'Email' => $user_email,
                'Status' => 'Active',
                'FirstName' => $firstName,
                'LastName' => $lastName,
                'CustomFields' => array(
                    'Accredited' => $industry_primary_role,
                    'IndustryPrimaryRole' => $accredited,
                ),
                'Consent' => array(
                    'ConsentIP' => '192.168.0.1',
                    'ConsentDate' => date('n/j/Y g:i:s A'),
                    'ConsentTracking' => 'Unknown'
                )
            )
        )),
        'method' => 'POST',
    );

   try {
        // Make the API request
        $response = wp_remote_post($url, $args);
        // Check for WP errors
        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        // Retrieve the response body
        $body = wp_remote_retrieve_body($response);

        // Check if the API response contains an error
        $status_code = wp_remote_retrieve_response_code($response);
        // echo '<pre>';
        // print_r( $response );exit;
        if ($status_code == 200) {
          /*  $notification_name = 'EMAIL VERIFICATION'; // The exact name of your notification
            $notification_event = 'form_submission';   // The event triggering the notification

            // Get all notifications for this form based on the event
            $notifications = GFCommon::get_notifications_to_send($notification_event, $form, $entry);

            foreach ($notifications as $notification) {
                // Check if the notification matches the one we want to send
                if ($notification['name'] == $notification_name) {
                    // Send the notification
                    GFCommon::send_notification($notification, $form, $entry);
                }
            }*/
        }else{
            throw new Exception('API Error: ' . $body);
        }
    } catch (Exception $e) {
        // Catch any errors and return them
        echo '<pre>';
        print_r($e->getMessage());
        echo '</pre>';
        wp_die();
        return 'Error: ' . $e->getMessage();
    }
}

/*function send_to_elastic_email($entry, $form) {
  
  // Replace with your Elastic Email API key 
  $apiKey = '6983535E8FE5FBDE96223040922CB6D2F1BE56746A57DE95E19520D7234D411256ECDF9BB4582F5DB3C37EC3E5D4599B';

  // Check if it's the specific form (replace FORM_ID with your actual form ID)
  if ($form['id'] != 7) {
    return;
  }

  // Extract data from Gravity Forms submission (replace with your field IDs)
  $user_email = rgar($entry, '3'); // Replace '3' with the field ID for email
  $fullName = rgar($entry, '5'); // Replace '1' with the field ID for name (optional)
  $industry_primary_role = rgar($entry, '7'); // Replace '1' with the field ID for name (optional)
  $accredited = strip_tags(rgar($entry, '6')); // Replace '1' with the field ID for name (optional) 
  
  $parts = explode(' ', $fullName);
  $firstName = !empty($parts[0]) ? $parts[0] : '';
  $lastName = !empty($parts[1]) ? implode(' ', array_slice($parts, 1)) : '';

  // Option 2: Using substr with strpos (handles names without spaces)
  if (empty($parts)) {
    $lastName = $fullName;
    $firstName = '';
  } else {
    $lastNamePos = strpos($fullName, ' ');
    if ($lastNamePos !== false) {
      $firstName = substr($fullName, 0, $lastNamePos);
      $lastName = substr($fullName, $lastNamePos + 1);
    } else {
      $firstName = $fullName;
      $lastName = '';
    }
  }
 

  $data = [
    'email' => $user_email,
    
   ];

  // Add name if available
  if (!empty($user_name)) {
      $data['firstName'] = $user_name;
     
  }
  //$data['publicAccountID'] = 'e8763a23-bc4e-43d4-832c-961de70bdb4d';
  // 
  // Use Guzzle for HTTP requests
  require_once('vendor/autoload.php'); // Assuming Guzzle is installed via Composer

  $client = new GuzzleHttp\Client();
  $url = 'https://api.elasticemail.com/v2/contact/add?publicAccountID=e8763a23-bc4e-43d4-832c-961de70bdb4d&email='.$user_email.'&firstName='.$firstName.'&lastName='.$lastName.'&field_industryprimaryrole='.$industry_primary_role.'&field_accredited='.$accredited;
  
  $headers = [
    'Content-Type' => 'application/json',
    'Authorization' => 'Basic ' . base64_encode('apikey:' . $apiKey)
  ];

  try {
    $response = $client->post($url, [
      'headers' => $headers,
      'body' => json_encode($data)
    ]);

    $statusCode = $response->getStatusCode();

    if ($statusCode == 200) {
      // Success
    //   echo "<pre>";
    //    print_r( json_decode($response->getBody(), true));
        $notification_name = 'EMAIL VERIFICATION'; // The exact name of your notification
            $notification_event = 'form_submission';   // The event triggering the notification

            // Get all notifications for this form based on the event
            $notifications = GFCommon::get_notifications_to_send($notification_event, $form, $entry);

            foreach ($notifications as $notification) {
                // Check if the notification matches the one we want to send
                if ($notification['name'] == $notification_name) {
                    // Send the notification
                    GFCommon::send_notification($notification, $form, $entry);
                }
            }
        'Contact added to ElasticEmail successfully.';
    } else {
      $responseData = json_decode($response->getBody(), true);
        'Error: ' . $statusCode . ' - ' . $responseData['error'];
    }
  } catch (GuzzleHttp\Exception\ClientException $e) {
      'Error: ' . $e->getMessage();
  }
 
}*/
function welcome_aboard() {
    // Check if the user is logged in
    if ( is_user_logged_in() ) { 
        $user_id = get_current_user_id(); ?>
       <div id="popUpForm" class="welcome-abroad-popup" style="display:block !important;">
        <div id="popContainer">
                <div id="close">X</div>
                    
                    <h2>Welcome ABOARD</h2>
                    <h5>Track investors, letters, tickers and more!</h5>

                    <span style="margin-bottom:20px;">To start with, see our featured letters:</span>
                    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
.welcome-abroad-popup h5 {
    margin-bottom: 50px;
}
        .welcome-abroad-popup ul {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            gap: 80px;
        }
        
        .welcome-abroad-popup ul li {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .image-container {
            display: flex;
            align-items: end;
            height: 120px;
        }

        .image-container img {
            max-width: 100%;
            max-height: 100%;
        }

        .center-button {
            margin-top: 10px;
        }

        .center-button button {
            padding: 5px 30px !important;
            font-size: 16px;
            background: #fff !important;
    color: #194572 !important;
    border-color: #194572 !important;
    border: 1px solid !important;    width: auto !important;
        }
        div#welcome-pop-sec { background: transparent !important;}
        @media screen and (max-width: 767px) {
        .welcome-abroad-popup ul {
            gap: 20px;
            }
            .image-container{ height: 70px; }
        }
    </style>
<div>
        <ul>
            <li>
                <a href="https://www.buysidedigest.com/funds/headwaters-capital/">
                    <div class="image-container">
                        <img decoding="async" src="https://www.buysidedigest.com/wp-content/uploads/2024/08/Headwaters-Capital.png" alt="Headwaters Capital">
                    </div>
                </a>
                <div class="center-button">
                    <?php echo md_fund_subscribe_form_btn( 10842, site_url().'/funds/headwaters-capital/'); ?>
                </div>
            </li>
            <li>
                <a href="https://www.buysidedigest.com/funds/rowan-street-capital/">
                    <div class="image-container">
                        <img decoding="async" src="https://www.buysidedigest.com/wp-content/uploads/2024/08/rsz_rowan.jpg" alt="L1 Capital">
                    </div>
                </a>
                <div class="center-button">
                    <?php echo md_fund_subscribe_form_btn( 10725, site_url().'/funds/rowan-street-capital/'); ?>
                </div>
            </li>
            <li>
                <a href="https://www.buysidedigest.com/funds/greenlight-capital/">
                    <div class="image-container">
                        <img decoding="async" src="https://www.buysidedigest.com/wp-content/uploads/2024/08/Greenlight.png" alt="Greenlight">
                    </div>
                </a>
                <div class="center-button">
                   <?php echo md_fund_subscribe_form_btn( 10694, site_url().'/funds/greenlight-capital/'); ?>
                </div>
            </li>
            <li>
                <a href="https://www.buysidedigest.com/funds/hoisington-investment-management/">
                    <div class="image-container">
                        <img decoding="async" src="https://www.buysidedigest.com/wp-content/uploads/2024/08/Hoisington-Investment-Management.png" alt="Hoisington Investment Management">
                    </div>
                </a>
                <div class="center-button">
                    <?php echo md_fund_subscribe_form_btn( 10698, site_url().'/funds/hoisington-investment-management/'); ?>
                </div>
            </li>
        </ul>
         <ul>
            <li>
                <a href="https://www.buysidedigest.com/funds/l1-capital-international-fund/">
                    <div class="image-container">
                        <img decoding="async" src="https://www.buysidedigest.com/wp-content/uploads/2024/08/L1-Capital-Long-Short-Fund-1.png" alt="L1 Capital">
                    </div>
                </a>
                <div class="center-button">
                    <?php echo md_fund_subscribe_form_btn( 10809, site_url().'/funds/l1-capital-international-fund/'); ?>
                </div>
            </li>
            <li>
                <a href="https://www.buysidedigest.com/funds/robotti-value-investors/">
                    <div class="image-container">
                        <img decoding="async" src="https://www.buysidedigest.com/wp-content/uploads/2024/08/Robotti-2.png" alt="L1 Capital">
                    </div>
                </a>
                <div class="center-button">
                <?php echo md_fund_subscribe_form_btn( 15366, site_url().'/funds/robotti-value-investors/'); ?>
                </div>
            </li>
            <li>
                <a href="https://www.buysidedigest.com/funds/horizon-kinetics/">
                    <div class="image-container">
                        <img decoding="async" src="https://www.buysidedigest.com/wp-content/uploads/2024/08/horizon-kinetics-1.png" alt="Horizon Kinetics">
                    </div>
                </a>
                <div class="center-button">
                    <?php echo md_fund_subscribe_form_btn( 10700, site_url().'/funds/horizon-kinetics/'); ?>
                </div>
            </li>
            <li>
                <a href="https://www.buysidedigest.com/funds/goehring-rozencwajg-associates-llc/">
                    <div class="image-container">
                        <img decoding="async" src="https://www.buysidedigest.com/wp-content/uploads/2024/08/Goehring-2.png" alt="Goehring & Rozencwajg Associates, LLC">
                    </div>
                </a>
                <div class="center-button">
                    <?php echo md_fund_subscribe_form_btn( 10692, site_url().'/funds/goehring-rozencwajg-associates-llc/'); ?>
                </div>
            </li>
        </ul>
    </div></div></div>
    <script type="text/javascript">
        document.getElementById('close').addEventListener('click', function() {
        window.location.href = 'https://www.buysidedigest.com/hedge-fund-database/'; // Replace with your desired URL
});
    </script>
       <?php
    } 
}

// Register the shortcode with WordPress
add_shortcode('welcome_aboard', 'welcome_aboard');


/**
 * Enqueue script for fund follow link
 * 
 * This script will only be enqueued if the user is not logged in and the current page is a single fund page
 * and updated the "Fund Follow" link to redirect to the login page if the user is not logged in.
 * 
 * @return void
 */
function bsd_fund_follow_script() {
    if ( ! is_user_logged_in() && is_singular( 'funds' ) ) {
        wp_enqueue_script( 'bsd-fund-follow-link', get_stylesheet_directory_uri() . '/assets/js/fund-follow.js', array(), '1.0', true );
    }
}

add_action( 'wp_enqueue_scripts', 'bsd_fund_follow_script' );

/**
 * Load CSS for investors page
 * 
 * This function will enqueue a custom CSS file for the investors page if the current page is using the page-investors.php template.
 * 
 * @since 1.0.0
 */
function load_investors_page_css() {
    if (is_page_template('page-investors.php')) {
        wp_enqueue_style(
            'investor-page-style',
            get_stylesheet_directory_uri() . '/assets/css/page-investors.css',
            array(),
            '1.0'
        );
    }
}

add_action('wp_enqueue_scripts', 'load_investors_page_css');

/**
 * Custom URL Rewrite for investor page
 * 
 * This code will enable the creation of a custom URL for the investor page
 * that can be accessed by visiting `https://example.com/investor/username`
 * 
 * @since 1.0.0
 */
add_action('init', function() {
    /**
     * Add a custom URL rewrite rule to WordPress
     * 
     * The first parameter is the regex pattern to match the URL
     * The second parameter is the WordPress query string to execute
     * The third parameter is the priority of the rule
     */
    add_rewrite_rule('^investor/([^/]*)/?$', 'index.php?investor_slug=$matches[1]', 'top');
});

/**
 * Add a custom query var to WordPress
 * 
 * The custom query var is used to store the value of the `investor_slug`
 * query string parameter.
 * 
 * @param array $vars Array of query vars
 * @return array Modified array of query vars
 */
add_filter('query_vars', function($vars) {
    $vars[] = 'investor_slug';
    return $vars;
});

/**
 * Filter the template file to include for the custom URL
 * 
 * If the `investor_slug` query var is set, then use the custom template
 * file for the investor page.
 * 
 * @param string $template Path to the template file
 * @return string Modified path to the template file
 */
add_filter('template_include', function($template) {
    if (get_query_var('investor_slug')) {
        $custom_template = locate_template('page-investor.php');
        if ($custom_template) {
            return $custom_template;
        }
    }

    return $template;
});


// Load Single Investor page CSS
function load_single_investor_page_css() {
    if ( get_query_var('investor_slug') ) {
        // Enqueue the CSS file
        wp_enqueue_style(
            'investor-page-style',
            get_stylesheet_directory_uri() . '/assets/css/page-investor.css',
            array(),
            '1.0'
        );
    }
}

add_action('wp_enqueue_scripts', 'load_single_investor_page_css');

add_filter( 'body_class', function( $classes ) {
    if ( get_query_var('investor_slug') ) {
        $classes[] = 'page-template';
        $classes[] = 'page-template-page-investor';
    }
    return $classes;
});


// Load Landing page CSS
function load_landing_page_css() {
    if (is_page_template('page-landing.php')) {
        // Enqueue the CSS file
        wp_enqueue_style(
            'landing-page-style',
            get_stylesheet_directory_uri() . '/assets/css/page-landing.css',
            array(),
            '1.0'
        );
    }
}

add_action('wp_enqueue_scripts', 'load_landing_page_css');

// Load Landing page JS
function load_landing_page_script() {
    if (is_page_template('page-landing.php')) {
        // Enqueue Swiper CSS
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0');

        // Enqueue Swiper JS
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true);

        // Enqueue your custom JS
        wp_enqueue_script('bsd-page-landing', get_stylesheet_directory_uri() . '/assets/js/page-landing.js', array('swiper-js'), '1.0', true);
    }
}

add_action('wp_enqueue_scripts', 'load_landing_page_script');

function load_signup_page_script() {
    if (is_page('register')) {
        wp_enqueue_script( 'bsd-page-register', get_stylesheet_directory_uri() . '/assets/js/page-register.js', array(), '1.0', true );
    }
}

add_action('wp_enqueue_scripts', 'load_signup_page_script');

// Load Investor page JS
function load_investor_page_script() {
    if ( get_query_var('investor_slug') ) {
        // Enqueue echarts JS
        wp_enqueue_script('bsd-echarts-js', 'https://cdn.jsdelivr.net/npm/echarts@5.6.0/dist/echarts.min.js', array(), '5.6.0', true);

        // Enqueue your custom JS
        wp_enqueue_script('bsd-page-investor', get_stylesheet_directory_uri() . '/assets/js/page-investor.js', array('bsd-echarts-js'), '1.0', true);
    }
}

add_action('wp_enqueue_scripts', 'load_investor_page_script');

// Load Large Fund buy page CSS
function load_large_fund_buy_page_css() {
    if (is_page_template('page-largest-fund-buy.php')) {
        // Enqueue the CSS file
        wp_enqueue_style(
            'large-fund-buy-page-style',
            get_stylesheet_directory_uri() . '/assets/css/page-largest-fund-buy.css',
            array(),
            '1.0'
        );
    }
}

add_action('wp_enqueue_scripts', 'load_large_fund_buy_page_css'); 

// Load Large Fund buy page JS
function load_large_fund_buy_page_js() {
    if (is_page_template('page-largest-fund-buy.php')) {
        // Enqueue the JS file
        wp_enqueue_script(
            'large-fund-buy-page-script',
            get_stylesheet_directory_uri() . '/assets/js/page-largest-fund-buy.js',
            array(),
            '1.0',
            true
        );
    }
}

add_action('wp_enqueue_scripts', 'load_large_fund_buy_page_js');

// Load Large Fund Buy Detail page CSS
function load_large_fund_buy_detail_page_css() {
    if (is_page_template('page-largest-fund-buy-detail.php')) {
        // Enqueue the CSS file
        wp_enqueue_style(
            'large-fund-buy-detail-page-style',
            get_stylesheet_directory_uri() . '/assets/css/page-largest-fund-buy-detail.css',
            array(),
            '1.0'
        );
    }
}

add_action('wp_enqueue_scripts', 'load_large_fund_buy_detail_page_css');

// Load Large Fund Buy Detail page JS
function load_large_fund_buy_detail_page_js() {
    if (is_page_template('page-largest-fund-buy-detail.php')) {
        // Enqueue the JS file
        wp_enqueue_script(
            'large-fund-buy-detail-page-script', 
            get_stylesheet_directory_uri() . '/assets/js/page-largest-fund-buy-detail.js',
            array(),
            '1.0',
            true
        );
    }
}

add_action('wp_enqueue_scripts', 'load_large_fund_buy_detail_page_js');

// Helper functions
require_once get_stylesheet_directory() . '/helper/template.php';
require_once get_stylesheet_directory() . '/helper/api.php';
