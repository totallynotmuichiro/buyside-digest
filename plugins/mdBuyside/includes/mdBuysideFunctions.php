<?php

/*



  // #md_display_fund_table

    // Function Description: Creates and Displays the Fund Table

    // Called by: md_display_fund_table Shortcode



  // #md_ajax_fund_subscribe

    // Function Description: Ajax function to save subscritions to funds

    // Called by: mdBuysideFrontend.js mdUpdateFundSubscription()



  // #md_fund_subscribe_form_creation

    // Function Description: Creates the Fund Subscribe Button

    // Called by: fund-subscribe-button Shortcode



  // #md_ajax_ticker_subscribe

    // Function Description: Ajax function to save subscritions to tickers

    // Called by: mdBuysideFrontend.js mdUpdateTickerSubscription()



  // #md_ticker_subscribe_form_creation

    // Function Description: Creates the Fund Subscribe Button

    // Called by: fund-subscribe-button Shortcode



  // #md_get_users_to_send_letters_to

    // Function Description: Checks funds and tickers for letters published and returns array of users

    // Called by:



  // #md_get_user_email_data

    // Function Description: Gets the users data and saves in array.

    // Called by:



  // #md_get_users_subscribed_funds

    // Function Description: Creates an array of Funds a user is subscribed to.

    // Called by: button-feed.php, md_create_email



  // #md_get_users_subscribed_tickers

    // Function Description: Creates an array of Tickers a user is subscribed to.

    // Called by: button-feed.php, md_create_email



  // #md_get_funds_subscribed_users

    // Function Description: Creates an array of Users that have subscribed to a fund.

    // Called by:



  // #md_get_tickers_subscribed_users

    // Function Description: Creates an array of Users that have subscribed to a fund.

    // Called by:



 // #md_get_related_fund_ids

   // Function Description: Gets the connected funds for a letter

   // Called by: md_get_letters_to_send



 // #md_get_letters_ids_for_tickers

   // Function Description: Gets the connected letters for an array of tickers and returns the letter ids as an array

   // Called by:



 // #md_get_related_tickers_ids

   // Function Description: Gets the connected tickers for a letter and returns them as an array

   // Called by: md_get_letters_to_send



 // #md_get_letters_to_send

   // Function Description: Emails the Letters to subscribers

   // Called by: md_create_email



 // #md_create_email

  // Function Description: Emails the Letters to subscribers

  // Called by: Cron job



  // #md_test_create_email

    // Function Description: Emails the Letters to subscribers

    // Called by: MD Letters Page



  // #md_send_email

    // Function Description: Creates a metabox for the Fund Title

    // Called by: On load



  // #add_meta_boxes

    // Function Description: Creates a metabox for the Fund Title

    // Called by: On load



  // #md_create_fund_text_field_meta_box

    // Function Description: Creates the Fund Title HTML and adds a value

    // Called by: add_meta_boxes



  // #md_save_postdata

    // Function Description: Saves the meta key value

    // Called by: Fires on Post Save



  // #md_letter_import_add_parent_fund

    // Function Description: Adds the parent fund to wp_jet_rel_default

    // Called by: Called by pmxi_saved_post hook



  // #md_letter_import_add_parent_fund

    // Function Description: Adds the parent fund to wp_jet_rel_default

    // Called by: Called by pmxi_saved_post hook





*/

// #md_display_recent_letters_shortcode

// Function Description: Creates and Displays the Home Page Letters

// Called by: md-display-recent-letters shortcode

function md_display_recent_letters_shortcode($atts = null)
{
    if ($atts != null) {
        if (isset($atts["type"]) && !empty($atts["type"])) {
            $fund_type = $atts["type"];
        } else {
            $fund_type = "default";
        }

        if (isset($atts["title"]) && !empty($atts["title"])) {
            $title = $atts["title"];
        } else {
            $title = "Most Recent Letters";
        }

        if (
            isset($atts["num"]) &&
            !empty($atts["num"]) &&
            is_numeric($atts["num"])
        ) {
            $fund_limit = $atts["num"];
        } else {
            $fund_limit = all;
        }
    } else {
        $fund_type = "default";

        $title = "Most Recent Letters";

        $fund_limit = all;
    }

    $users_funds = md_get_users_subscribed_funds();

    $title_id = strtolower($title);

    $title_id = str_replace(" ", "_", $title_id);

    $database_url = site_url("/hedge-fund-database");

    $funds_display =
        "

        <div id='md_" .
        $title_id .
        "' class='md-slider-wrap'>

          <div class='md_slides_title'>

            <div>

              <h2>" .
        $title .
        "</h2>

            </div>

            <div>

              <a href='" .
        $database_url .
        "'>View More</a>

            </div>

          </div>

          <div id='md_" .
        $title_id .
        "_container' class='md-container'>

            <div id='md_" .
        $title_id .
        "_slider_container' class='md-slider-container'>

              <div id='md_" .
        $title_id .
        "_slider' class='md-slider'>";

    if ($fund_type == "default") {
        // Not looking for a category

        if (is_numeric($fund_limit)) {
            $args = [
                "post_type" => "letters",

                "post_status" => "publish",

                "orderby" => "date",

                "order" => "DESC",

                "nopaging" => false,

                "posts_per_page" => $fund_limit,
            ];
        } else {
            $args = [
                "post_type" => "letters",

                "post_status" => "publish",

                "orderby" => "date",

                "order" => "DESC",

                "nopaging" => false,
            ];
        }
    } else {
        // Looking for a category

        if (!empty($fund_type)) {
            $args = [
                "post_type" => "letters",

                "post_status" => "publish",

                "orderby" => "date",

                "order" => "DESC",

                "nopaging" => false,

                "posts_per_page" => $fund_limit,

                "tax_query" => [
                    [
                        "taxonomy" => $fund_type,

                        "field" => "slug",

                        "terms" => $fund_type,
                    ],
                ],
            ];
        } else {
            $args = [
                "post_type" => "letters",

                "post_status" => "publish",

                "orderby" => "date",

                "order" => "DESC",

                "nopaging" => false,

                "tax_query" => [
                    [
                        "taxonomy" => $fund_type,

                        "field" => "slug",

                        "terms" => $fund_type,
                    ],
                ],
            ];
        }
    }

    $loop = new WP_Query($args);

    while ($loop->have_posts()):
        $loop->the_post();

        global $post;

        $letter_id = $post->ID;

        $letter_name = get_the_title($letter_id);

        $letter_url = get_post_meta($post->ID, "letter-link", true);

        $login_url = site_url("/register");

        $fund_array = md_get_related_fund_ids($letter_id);

        $fund_id = $fund_array[0];

        $fund_name = get_the_title($fund_id);

        $fund_url = get_permalink($fund_id);

        $fund_status = get_post_status($fund_id);

        $key_person = get_post_meta(
            $fund_id,
            "name-of-cio-or-portfolio-manager",
            true
        );

        $quarterly = get_post_meta($fund_id, "quarterly", true);

        $siteurl = home_url();

        // Quarter

        $terms = wp_get_post_terms($letter_id, ["quarter"]);

        $quarter = "";

        foreach ($terms as $term) {
            $quarter = $term->name;
        }

        // Track Button

        if (is_user_logged_in()) {
            if (in_array($fund_id, $users_funds)) {
                $track_button =
                    "<button id='md_fund_subscribe_btn_id_" .$letter_id ."' class='md-fund-subscribe-btn_class_".$fund_id ." md-fund-subscribe-btn md-fund-table-subscribe-btn md-fund-subscribe-btn--subscribed' data-fund-id='" .$fund_id ."' data-letter-id='" .$letter_id ."'>Unfollow</button>";
            } else {
                $track_button =
                    "<button id='md_fund_subscribe_btn_id_" .$letter_id ."' class='md-fund-subscribe-btn_class_".$fund_id ." md-fund-subscribe-btn md-fund-table-subscribe-btn md-fund-subscribe-btn--not-subscribed' data-fund-id='" .$fund_id ."' data-letter-id='" .$letter_id ."'>Follow</button>";
            }
        } else {
            $track_button =
                "<a href='" .$login_url ."' class='md-slide-login-btn' id='fund-track'>Follow</a>";
        }

        // Tickers

        $tickers_array = wp_get_object_terms($letter_id, "tickers", [
            "fields" => "all",
        ]);

        $tickers_count = count($tickers_array);

        $tickers_link = home_url("/tickers");

        $ticker_wrap =
            "<div id='md-ticker-row-wrap-" .
            $letter_id .
            "' class='md-ticker-row-wrap'>";

        $i = 1;

        foreach ($tickers_array as $ticker) {
            // $ticker_slug = str_replace(" ","-",$ticker);

            // $ticker_slug = strtolower($ticker_slug);

            $ticker_slug = $ticker->slug;

            $ticker_link = $tickers_link . "/" . $ticker_slug;

            $ticker_name = str_replace("|", ", ", $ticker->name);

            if ($i >= $tickers_count) {
                $after_ticker = "";
            } else {
                $after_ticker = ",";
            }

            if ($i < 7) {
                $ticker_wrap .=
                    "<span class='md-ticker-span'><a href='" .
                    $ticker_link .
                    "'>" .
                    $ticker_name .
                    "</a>" .
                    $after_ticker .
                    "</span>";
            } elseif ($i == 8) {
                $ticker_wrap .=
                    "<span class='md-ticker-span md-d-none'><a href='" .
                    $ticker_link .
                    "'>" .
                    $ticker_name .
                    "</a>" .
                    $after_ticker .
                    "</span>";
            } else {
                $ticker_wrap .=
                    "<span class='md-ticker-span md-d-none'><a href='" .
                    $ticker_link .
                    "'>" .
                    $ticker_name .
                    "</a>" .
                    $after_ticker .
                    "</span>";
            }

            $i++;
        }

        if ($tickers_count > 7) {
            $ticker_wrap .=
                "<button id='md-ticker-wrap-more-" .
                $letter_id .
                "' class='md-ticker-wrap-more-btn'>...more</button>";
        }

        $ticker_wrap .= "</div>";

        // Display the Letter

        $funds_display .=
            "<div id='md-recent-letter-" . $letter_id . "' class='md-slide'>";

        $funds_display .= "<div class='md-slide-header'>";

        $funds_display .=
            "<h3><a href='" .
            $fund_url .
            "'>" .
            $fund_name .
            " " .
            $quarter .
            "</a></h3>";

        $funds_display .= "</div>";

        $funds_display .= "<div class='md-slide-content'>";

        $funds_display .=
            "<div class='md-slide-content-row'><div>Quarterly:</div><div class='md-value'>" .
            $quarterly .
            "</div></div>";

        $funds_display .=
            "<div class='md-slide-content-row'><div>Key Person:</div><div class='md-value'>" .
            $key_person .
            "</div></div>";

        $funds_display .=
            "<div class='md-slide-content-row'><div>Tickers:</div><div class='md-value'>" .
            $ticker_wrap .
            "</div></div>";

        $funds_display .= "</div>";

        $funds_display .= "<div class='md-slide-footer'>";

        $funds_display .=
            "<div><a href='" .
            $letter_url .
            "' target='_blank'>View letter</a></div>";

        $funds_display .= "<div>" . $track_button . "</div>";

        $funds_display .= "</div>";

        $funds_display .= "</div>";
    endwhile;

    $funds_display .= "</div>";

    $funds_display .= "</div>";

    $funds_display .= "</div>";

    $funds_display .= "</div>";

    $funds_display .=
        "<div id='md_" .
        $title_id .
        "_slide_controls' class='md_slide_controls'>";

    $funds_display .=
        "<button id='md_" .
        $title_id .
        "_slide_prev_btn' class='md-prev-next-btn md-prev-slide-btn md-disabled' ><</button>";

    $funds_display .=
        "<div id='md_" .
        $title_id .
        "_slider_button_wrap' class='md-slider-btn-wrap'></div>";

    $funds_display .=
        "<button id='md_" .
        $title_id .
        "_slide_next_btn' class='md-prev-next-btn md-next-slide-btn'>></button>";

    $funds_display .= "</div>";

    $funds_display .= "</div>";

    wp_reset_query();

    return $funds_display;
}

add_shortcode(
    "md-display-recent-letters",
    "md_display_recent_letters_shortcode"
);

// #md_display_fund_table

// Function Description: Creates and Displays the Fund Table

// Called by: md_display_fund_table Shortcode

function md_display_fund_table_shortcode()
{
    $users_funds = md_get_users_subscribed_funds();

    // Get the current page

    global $wp;

    $page_url = home_url($wp->request);

    // Check if we are searching for text from the input box


    $fund_search_query = isset($_GET["md-fund-letter-table-fund-search"]) ? sanitize_text_field($_GET["md-fund-letter-table-fund-search"]) : "";
    $cio_search_query = isset($_GET["md-fund-letter-table-cio-search"]) ? sanitize_text_field($_GET["md-fund-letter-table-cio-search"]) : "";
    $ticker_search_query = isset($_GET["md-fund-letter-table-ticker-search"]) ? sanitize_text_field($_GET["md-fund-letter-table-ticker-search"]) : "";

    // echo strtoupper($ticker_search_query);

    // Check if we are searching for specific Quarter

    if (isset($_GET["md-fund-letter-table-select"])) {
        $select_query = $_GET["md-fund-letter-table-select"];
    } else {
        $select_query = "";
    }

    // Check if we are looking for a particular type of Letter

    if (isset($_GET["type"])) {
        $query_type = $_GET["type"];
    } else {
        $query_type = "";
    }

    // Check which direction the search should be

    if (isset($_GET["direction"])) {
        $search_direction = $_GET["direction"];
    } else {
        $search_direction = "";
    }

    // Build the Query

    $args = [
        "post_type" => "letters",
        "post_status" => "publish",
        "orderby" => "date",
        "order" => "DESC",
        "posts_per_page" => -1,
    ];
    
    $tax_query = [];
    
    // Check if CIO search is provided
    if (!empty($cio_search_query)) {
        // Split the search query into individual parts
        $cio_search_parts = explode(' ', $cio_search_query);
    
        // Initialize an array to store term IDs
        $term_ids = [];
    
        // Loop through each part of the search query
        foreach ($cio_search_parts as $search_part) {
            // Get term IDs based on each part of the search query
            $term_ids_part = get_terms([
                'taxonomy' => 'key-person',
                'fields' => 'ids',
                'name__like' => $search_part,
            ]);
    
            // Merge the term IDs with the existing array
            $term_ids = array_merge($term_ids, $term_ids_part);
        }
    
        // Remove duplicate term IDs
        $term_ids = array_unique($term_ids);
    
        // Add the terms to the tax_query using the IN operator
        $tax_query[] = [
            'taxonomy' => 'key-person',
            'field' => 'id',
            'terms' => $term_ids,
            'operator' => 'IN',
        ];
    }
/*zee*/    
    // Check if Ticker search is provided
   /* if (!empty($ticker_search_query)) {
        $tax_query[] = [
            'taxonomy' => 'tickers',
            'field' => 'name',
            'terms' => $ticker_search_query,
        ];
    }*/

    if (!empty($ticker_search_query)) {
    // Step 1: Fetch all tickers
    $all_tickers = get_terms([
        'taxonomy' => 'tickers',
        'fields' => 'names', // get only names to save memory
        'hide_empty' => false,
    ]);
    $upperticker = strtoupper($ticker_search_query);
    // Step 2: Filter tickers based on the country code
    $filtered_tickers = array_filter($all_tickers, function($ticker) use ($upperticker) {
        $ticker = trim($ticker); // Remove any extra whitespace
        // Ensure the search query is considered with a space before it
        $query_with_space = ' ' . $upperticker;
        $query_length = strlen($query_with_space);
        return (substr($ticker, -$query_length) === $query_with_space && $query_length > 1); // Ensure space is accounted for
    });

    // Step 3: Update the tax_query with the filtered tickers
    if (!empty($filtered_tickers)) {
        $tax_query[] = [
            'taxonomy' => 'tickers',
            'field' => 'name',
            'terms' => $filtered_tickers,
        ];
    }else{
        $tax_query[] = [
            'taxonomy' => 'tickers',
            'field' => 'name',
            'terms' => $ticker_search_query,
        ];
    }
}
    
    if (!empty($fund_search_query)) {
        $args["s"] = $fund_search_query;
    }
    
    // Add tax_query to args if it exists
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }
    
    
    
    //     echo "<pre>";
    // print_r($args);
    // die;
    
    $loop = new WP_Query($args);



    

    // array to hold the fund letters

    $fund_letters_array = [];

    // Loop through the posts and add them to the fund letters array

    while ($loop->have_posts()):
        $loop->the_post();

        global $post;

        $letter_id = $post->ID;

        $published_date = $post->post_date;

        // Make sure there is an associated fund for this letter

        $fund_array = md_get_related_fund_ids($letter_id);

        if (count($fund_array) > 0) 
        {
            $fund_id = $fund_array[0];

            // Quarters

            $terms = wp_get_post_terms($letter_id, ["quarter"]);

            $quarter = "";

            foreach ($terms as $term) {
                $quarter = $term->name;
            }

            // Tickers

            $tickers_array = wp_get_object_terms($letter_id, "tickers", [
                "fields" => "all",
            ]);
            $tickers_array_name = wp_get_object_terms($letter_id, "tickers", [
                "fields" => "names",
            ]);
            // echo "test test <pre>"; print_r($tickers_array);die();
            $fund_name = get_the_title($fund_id);

            $fund_url = get_permalink($fund_id);

            $fund_status = get_post_status($fund_id);

            // Tracking Status

            // Check if the user is subscribed to this fund

            if (in_array($fund_id, $users_funds)) {
                $status = "tracking";
            } else {
                $status = "not tracking";
            }

            // $key_person = get_post_meta(
            //     $fund_id,
            //     "name-of-cio-or-portfolio-manager",
            //     true
            // );

            $key_person_terms = get_the_terms($letter_id, 'key-person');
            if ($key_person_terms && !is_wp_error($key_person_terms)) {
                // Initialize an empty string to store key person names
                $key_person = '';
            
                // Loop through each term and append the name to the string
                foreach ($key_person_terms as $key_person_term) {
                    $key_person .= $key_person_term->name . ', ';
                }
            
                // Remove the trailing comma and space
                $key_person = rtrim($key_person, ', ');
            
                // Output the string
                // echo $key_person;
            }
            
            // echo "<pre>";
            // print_r($key_person);
            // die;

            $quarterly = str_replace(
                "%",
                "",
                get_post_meta($fund_id, "quarterly", true)
            );

            $ytd = str_replace("%", "", get_post_meta($fund_id, "ytd", true));

            $letter_link = get_post_meta($post->ID, "letter-link", true);
        

            // Create a row array to add to $fund_letters_array

            $this_row = [
                "letter_id" => $letter_id,

                "fund_id" => $fund_id,

                "quarter" => $quarter,

                "fund_name" => $fund_name,

                "fund_url" => $fund_url,

                "key_person" => $key_person,

                "status" => $status,

                "quarterly" => $quarterly,

                "ytd" => $ytd,

                "tickers_array" => $tickers_array,

                "letter_link" => $letter_link,

                "post_date" => $published_date,

                "fund_status" => $fund_status,
            ];

            // if this is a Search Query, search in the array for the $search_query, if it exists add the row to the array

            $search_query_found = "false";

            // echo count($tickers_array);
            // print_r($tickers_array);
            // echo $key_person;
            // die();

            if (!empty($search_query)) {
                $search_query_lower = strtolower($search_query);

                if (isset($tickers_array_name) || !empty($tickers_array_name)) {
                    if (
                        str_contains(strtolower($fund_name), $search_query_lower) ||
                        str_contains(
                            strtolower($key_person),
                            $search_query_lower
                        ) ||
                        in_array(
                            $search_query_lower,
                            array_map("strtolower", $tickers_array_name)
                        )
                    ) {
                        $search_query_found = "true";
                    }
                } else {
                    $search_query_found = "empty";
                }
            }

            // if this is a Select Query, search in the quarter for the $select_query, if it exists add the row to the array

            $select_query_found = "false";

            if (!empty($select_query)) {
                $select_query_lower = strtolower($select_query);

                if (str_contains(strtolower($quarter), $select_query_lower)) {
                    $select_query_found = "true";
                } else {
                    //echo "Search for ".$select_query_lower." NOT Found in ".strtolower($quarter)." ".$fund_name."<br>";
                }
            } else {
                $select_query_found = "empty";
            }

            // Do we need to add this row to the $fund_letters_array i.e. Select and Search Queries weren't empty and either Select or Search queries were found.

            if (empty($search_query) && empty($select_query)) {
                array_push($fund_letters_array, $this_row);

                // echo "<strong>Adding Row</strong> Both Search and Select are empty<br><br>";
            } elseif (
                !empty($search_query) &&
                !empty($select_query) &&
                ($search_query_found == "true" && $select_query_found == "true")
            ) {
                array_push($fund_letters_array, $this_row);

                // echo "<strong>Adding Row</strong> Search Found: ".$select_query_found." Select Found: ".$select_query_found."<br><br>";
            } elseif (
                !empty($search_query) &&
                empty($select_query) &&
                $search_query_found == "true"
            ) {
                array_push($fund_letters_array, $this_row);

                // echo "<strong>Adding Row</strong> Search Found: ".$select_query_found." Select Found: ".$select_query_found."<br><br>";
            } elseif (
                empty($search_query) &&
                !empty($select_query) &&
                $select_query_found == "true"
            ) {
                array_push($fund_letters_array, $this_row);

                // echo "<strong>Adding Row</strong> Search Found: ".$select_query_found." Select Found: ".$select_query_found."<br><br>";
            } else {
                // echo "<strong>Not Adding Row</strong> Search Found: ".$select_query_found." Select Found: ".$select_query_found."<br><br>";
            }
        }
    endwhile;

    // Rearrange the table based upon the type of search we are doing

    if (count($fund_letters_array) > 0) {
        if ($query_type == "fundname") {
            if ($search_direction == "desc") {
                array_multisort(
                    array_column($fund_letters_array, "fund_name"),
                    SORT_DESC,
                    $fund_letters_array
                );
            } else {
                array_multisort(
                    array_column($fund_letters_array, "fund_name"),
                    SORT_ASC,
                    $fund_letters_array
                );
            }
        } elseif ($query_type == "quarterly") {
            if ($search_direction == "desc") {
                array_multisort(
                    array_column($fund_letters_array, "quarterly"),
                    SORT_DESC,
                    $fund_letters_array
                );
            } else {
                array_multisort(
                    array_column($fund_letters_array, "quarterly"),
                    SORT_ASC,
                    $fund_letters_array
                );
            }
        } elseif ($query_type == "ytd") {
            if ($search_direction == "desc") {
                array_multisort(
                    array_column($fund_letters_array, "ytd"),
                    SORT_DESC,
                    $fund_letters_array
                );
            } else {
                array_multisort(
                    array_column($fund_letters_array, "ytd"),
                    SORT_ASC,
                    $fund_letters_array
                );
            }
        } else {
            if ($search_direction == "asc") {
                array_multisort(
                    array_column($fund_letters_array, "post_date"),
                    SORT_ASC,
                    $fund_letters_array
                );
            } else {
                array_multisort(
                    array_column($fund_letters_array, "post_date"),
                    SORT_DESC,
                    $fund_letters_array
                );
            }
        }
    }
    $search_query = isset( $search_query ) ? $search_query : '';

    // Build the Table HTML
    $table_html = md_create_fund_letters_table_html(
        $fund_letters_array,
        $page_url,
        $query_type,
        $search_direction,
        $search_query,
        $select_query
    );

    wp_reset_query();

    return $table_html;
}

add_shortcode("md-display-fund-table", "md_display_fund_table_shortcode");

// #md_create_fund_letters_table_html

// Function Description: Builds the HTML for the Fund Letter Table

// Called by: md_display_fund_table_shortcode

function md_create_fund_letters_table_html(
    $fund_letters_array,
    $page_url,
    $query_type,
    $search_direction,
    $search_query,
    $select_query
) {
    // Set the class for the direction arrows

    $fundname_asc_btn_css = "";

    $fundname_desc_btn_css = "";

    $quarters_asc_btn_css = "";

    $quarters_desc_btn_css = "";

    $quarterly_asc_btn_css = "";

    $quarterly_desc_btn_css = "";

    $ytd_asc_btn_css = "";

    $ytd_desc_btn_css = "";

    if ($query_type == "fundname") {
        if ($search_direction == "asc") {
            $fundname_asc_btn_css = "md-sort-active";
        }

        if ($search_direction == "desc") {
            $fundname_desc_btn_css = "md-sort-active";
        }
    } elseif ($query_type == "quarterly") {
        if ($search_direction == "asc") {
            $quarterly_asc_btn_css = "md-sort-active";
        }

        if ($search_direction == "desc") {
            $quarterly_desc_btn_css = "md-sort-active";
        }
    } elseif ($query_type == "ytd") {
        if ($search_direction == "asc") {
            $ytd_asc_btn_css = "md-sort-active";
        }

        if ($search_direction == "desc") {
            $ytd_desc_btn_css = "md-sort-active";
        }
    }


    // Add Quarters Select

    // Get the unique 'quarters'

    global $wpdb;

    $taxonomy_table_name = $wpdb->prefix . "term_taxonomy";

    $terms_table_name = $wpdb->prefix . "terms";

    // $quarters_array = $wpdb->get_results( "

    //     SELECT *

    //     FROM ".$taxonomy_table_name." AS termtaxonomy

    //     INNER JOIN ".$terms_table_name." AS terms

    //     ON termtaxonomy.term_id = terms.term_id

    //     AND termtaxonomy.count > 0

    //     WHERE termtaxonomy.taxonomy = 'quarter'

    //     ORDER BY termtaxonomy.term_name DESC

    //   ", ARRAY_A);

    $quarters_array = $wpdb->get_results(
        "

              SELECT *

              FROM " .
        $taxonomy_table_name .
        " AS termtaxonomy

              INNER JOIN " .
        $terms_table_name .
        " AS terms 

              ON termtaxonomy.term_id = terms.term_id

              AND termtaxonomy.count > 0

              WHERE termtaxonomy.taxonomy = 'quarter' 

              ORDER BY terms.slug DESC

            ",
        ARRAY_A
    );

    // Create an Option for each of the unique 'quarters'

    $options_display = "";
    $quartersArr = [];
    foreach ($quarters_array as $quarter) {
        $quartersArr[] = $quarter["name"];

        $quarter_name = $quarter["name"];

        $term_id = $quarter["term_id"];

        $quarter_count = $quarter["count"];

        // Set selected if required

        if (!empty($select_query) && $quarter_name == $select_query) {
            $selected = " selected ";
        } else {
            $selected = "";
        }

        // If the number of posts with category is greater than 0 add the option

        if ($quarter_count > 0) {
            $options_display .=
                "<option value='" .
                $quarter_name .
                "' " .
                $selected .
                ">" .
                $quarter_name .
                "&nbsp;(" .
                $quarter_count .
                ")</option>";
        }
    }

    // Add the Search Paramaters to the URL
    $fund_term = htmlspecialchars(isset($_GET['md-fund-letter-table-fund-search']) ? sanitize_text_field($_GET['md-fund-letter-table-fund-search']) : '');
    $fund_term = stripslashes($fund_term);
    $cio_term = htmlspecialchars(isset($_GET['md-fund-letter-table-cio-search']) ? sanitize_text_field($_GET['md-fund-letter-table-cio-search']) : '');
    $cio_term = stripslashes($cio_term);
    // $cio_term = html_entity_decode($cio_term);
    $tickers_term = htmlspecialchars(isset($_GET['md-fund-letter-table-ticker-search']) ? sanitize_text_field($_GET['md-fund-letter-table-ticker-search']) : '');

    // Add the Search Paramaters to the URL
    if (!empty($fund_term) || !empty($cio_term) || !empty($tickers_term)) {
        $search_param="&md-fund-letter-table-fund-search=".$fund_term."&md-fund-letter-table-cio-search=".$cio_term."&md-fund-letter-table-ticker-search=".$tickers_term;
    } else {
        $search_param = "";
    }

    // Add the Select Paramaters to the URL
    if (!empty($select_query)) {
        $select_param = "&md-fund-letter-table-select=" . $select_query;
    } else {
        $select_param = "";
    }
     $fund = '<div>

                    <label for="md-fund-letter-table-fund-search">Search by fund</label>

                    <input type="search" id="md-fund-letter-table-fund-search" name="md-fund-letter-table-fund-search" class="md-search-table-input" placeholder="Search by fund" value="'. $fund_term .'" />

                </div>';
    $return_table =
        "
        <div id='md-search-wrap' class='md-search-wrap'>
            <form id='md-search-form' name='md-search-form'>
            ".$fund."
                <div>
                    <label for='md-fund-letter-table-cio-search'>Search by CIO</label>
                    <input type='search' id='md-fund-letter-table-cio-search' name='md-fund-letter-table-cio-search' class='md-search-table-input' placeholder='Search by CIO' value='". $cio_term ."' />
                </div>
                <div>
                    <label for='md-fund-letter-table-ticker-search'>Search by ticker</label>
                    <input type='search' id='md-fund-letter-table-ticker-search' name='md-fund-letter-table-ticker-search' class='md-search-table-input' placeholder='Search by ticker' value='". $tickers_term ."' />
                </div>
                <div>
                    <label for='md-fund-letter-table-select'>Quarter</label>
                    <select id='md-fund-letter-table-select' name='md-fund-letter-table-select'>
                        <option value=''>All</option>
                        '". $options_display ."'
                    </select>
                </div>
                <div>
                    <input type='submit' value='Search' />
                </div>
            </form>
        </div>

              

        <div class='md-responsive-table'>";

    // Show the Clear Filter button if there is an active Filter or sort

    if (!empty($search_query) || !empty($query_type)) {
        $return_table .= "<div class='md-clear-filters-wrap'><a href='" . $page_url . "' class='md-clear-filter-btn'>Clear Filters</a></div>";
    }

    // $return_table .=  "<div class='mailchimp-btn'>".do_shortcode('[mailchimp_modal] [remove_fund_mailchimp_modal]')."</div>";
    $return_table .= "<div class='mailchimp-btn'> If you would like to add or delete your fund to our database, please contact <a href='mailto:info@buysidedigest.com'>info@buysidedigest.com</a></div>";
    $return_table .= "<table id='md-fund-letter-table' class='md-fund-table'>
                <tr>
                    <th class='md-fund-table-quarter'>Quarter</th>
                    <th>
                        <div class='md-th-flex'>
                            <div>Fund name</div> 
                            <div>
                                <a href='" . $page_url . "?type=fundname&direction=asc" . $search_param . $select_param . "' class='md-sort-btn md-sort-btn-asc " . $fundname_asc_btn_css . "' title='Sort Ascending'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 19.99 19.913'><path d='m107.196 55.896-3.586 6.187-3.586 6.188-3.565-6.2-3.566-6.199 7.152.012z' style='stroke-width:.264583' transform='matrix(1.39766 -.0021 .00241 1.6073 -129.969 -89.608)'/></svg>
                                </a>
                                <a href='" . $page_url . "?type=fundname&direction=desc" . $search_param . $select_param . "' class='md-sort-btn md-sort-btn-desc " . $fundname_desc_btn_css . "' title='Sort Descending'>

                                    <svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 19.99 19.913'><path d='m107.196 55.896-3.586 6.187-3.586 6.188-3.565-6.2-3.566-6.199 7.152.012z' style='stroke-width:.264583' transform='matrix(1.39766 -.0021 .00241 1.6073 -129.969 -89.608)'/></svg>

                                </a>
                            </div>
                        </div>
                    </th>

                    <th>Fund profile</th>
                    <th>Key person</th>
                    <th class='md-fund-tbl-status'>Follow Fund</th>
                    <th>
                        <div class='md-th-flex'>
                            <div>QTD</div>
                            <div>
                                <a href='" . $page_url . "?type=quarterly&direction=asc" . $search_param . $select_param . "' class='md-sort-btn md-sort-btn-asc " . $quarterly_asc_btn_css . "' title='Sort Ascending'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 19.99 19.913'><path d='m107.196 55.896-3.586 6.187-3.586 6.188-3.565-6.2-3.566-6.199 7.152.012z' style='stroke-width:.264583' transform='matrix(1.39766 -.0021 .00241 1.6073 -129.969 -89.608)'/></svg>

                                </a>
                                <a href='" . $page_url . "?type=quarterly&direction=desc" . $search_param . $select_param . "' class='md-sort-btn md-sort-btn-desc " . $quarterly_desc_btn_css . "' title='Sort Descending'>
                                    <svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 19.99 19.913'><path d='m107.196 55.896-3.586 6.187-3.586 6.188-3.565-6.2-3.566-6.199 7.152.012z' style='stroke-width:.264583' transform='matrix(1.39766 -.0021 .00241 1.6073 -129.969 -89.608)'/></svg>
                                </a>
                            </div>
                        </div>
                    </th>

                    <th>
                        <div  class='md-th-flex'>
                            <div>YTD</div> 
                            <div>
                                <a href='" . $page_url . "?type=ytd&direction=asc" . $search_param . $select_param . "' class='md-sort-btn md-sort-btn-asc " . $ytd_asc_btn_css . "' title='Sort Ascending'>

                                    <svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 19.99 19.913'><path d='m107.196 55.896-3.586 6.187-3.586 6.188-3.565-6.2-3.566-6.199 7.152.012z' style='stroke-width:.264583' transform='matrix(1.39766 -.0021 .00241 1.6073 -129.969 -89.608)'/></svg>
                                </a>
                                <a href='" . $page_url . "?type=ytd&direction=desc" . $search_param . $select_param . "' class='md-sort-btn md-sort-btn-desc " . $ytd_desc_btn_css . "' title='Sort Descending'>

                                    <svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 19.99 19.913'><path d='m107.196 55.896-3.586 6.187-3.586 6.188-3.565-6.2-3.566-6.199 7.152.012z' style='stroke-width:.264583' transform='matrix(1.39766 -.0021 .00241 1.6073 -129.969 -89.608)'/></svg>

                                </a>
                            </div>
                        </div>    
                    </th>

                    <th class='md-tickers-td'>Tickers</th>
                    <th>Letter</th>
                </tr>";

    // Build the rows

    // Custom sorting function
    function customSort($a, $b, $quartersArr)
    {
        $quarterA = array_search($a['quarter'], $quartersArr);
        $quarterB = array_search($b['quarter'], $quartersArr);

        return $quarterA - $quarterB;
    }

    // Use usort to sort the array using the custom sorting function
    usort($fund_letters_array, function ($a, $b) use ($quartersArr) {
        return customSort($a, $b, $quartersArr);
    });

    // echo"<pre>";
    // print_r($fund_letters_array);
    // die("dfd");


    if (count($fund_letters_array) > 0) {
        $row_count = 1;


        // echo"<pre>";
        // print_r($fund_letters_array);
        // die;
        $encountered_combinations = array();
        $filtered_fund_letters_array = array();
        foreach ($fund_letters_array as $fund_letter) {
            if ($row_count > 30) {
                $hide_row_css = "md-hide-row";
            } else {
                $hide_row_css = "";
            }

            $letter_id = $fund_letter["letter_id"];

            $fund_id = $fund_letter["fund_id"];

            $quarter = $fund_letter["quarter"];

            $fund_name = $fund_letter["fund_name"];

            $fund_url = $fund_letter["fund_url"];

            $key_person = $fund_letter["key_person"];

            $status = $fund_letter["status"];

            $quarterly = $fund_letter["quarterly"];
			$quarterly = get_post_meta( $letter_id, 'bsd-letter-qtd', true );
            $quarterly = empty($quarterly) || $quarterly === "-" ? '-' : floatval( $quarterly ) * 100;

            // Create a unique identifier for the combination of values
            $combination_identifier = "$quarter|$fund_name|$key_person";

            // Check if the combination has been encountered before
            if (!in_array($combination_identifier, $encountered_combinations)) {
                $encountered_combinations[] = $combination_identifier;
                $filtered_fund_letters_array[] = $fund_letter;
                if (isset($quarterly) && $quarterly != "-") {
                    $num_quarterly = $quarterly;
                    $num_quarterly = floatval($num_quarterly); // or $num = (float) $num;
                    $formatted_num = round(number_format($num_quarterly, 2, '.', ','), 1);
                    $quarterly = $formatted_num . "%";
                }

                $ytd = $fund_letter["ytd"];
				$ytd = get_post_meta( $letter_id, 'bsd-letter-ytd', true );
                $ytd = empty($ytd) || $ytd === "-" ? '-'  : floatval( $ytd ) * 100;

                if ($ytd != "-") {
                    $num_ytd = $ytd;
                    $num_ytd = floatval($num_ytd); // or $num = (float) $num;
                    $formatted_ytd = number_format($num_ytd, 2, '.', ',');
                    $ytd = number_format($formatted_ytd, 1) . "%";
                }

                $fund_status = $fund_letter["fund_status"];

                // $letter_link = $fund_letter['letter_link'];

                // Remove spaces from the URL
                $letter_link = str_replace(" ", "%20", $fund_letter["letter_link"]);

                $tickers_link = home_url("/tickers");

                $tickers_array = $fund_letter["tickers_array"];

                // echo "<pre>";
                // print_r($tickers_array);
                // echo "kfjdkf";

               

                $ticker_wrap =
                    "<div id='md-ticker-row-wrap-" . $letter_id . "' class='md-ticker-row-wrap'>";

                // $ticker_wrap.= $tickers_count;
                $i = 1;

                $tickers_count='';
                if($tickers_array){
                    $tickers_count = count($tickers_array);
                    foreach ($tickers_array as $ticker) {
                        // $ticker_slug = str_replace(" ","-",$ticker);
    
                        // $ticker_slug = strtolower($ticker_slug);
    
                        $ticker_slug = $ticker->slug;
    
                        $ticker_link = $tickers_link . "/" . $ticker_slug;
    
                        $ticker_name = str_replace(",", ", ", $ticker->name);
    
                        if ($i >= $tickers_count) {
                            $after_ticker = "";
                        } else {
                            $after_ticker = ",";
                        }
    
                        if ($i < 7) {
                            $ticker_wrap .=
                                "<span class='md-ticker-span'><a href='" .
                                $ticker_link .
                                "'>" .$ticker_name.''.$after_ticker."</a></span>";
                        } elseif ($i == 8) {
                            $ticker_wrap .=
                                "<span class='md-ticker-span md-d-none'><a href='" .
                                $ticker_link .
                                "'>" .
                                $ticker_name .''.$after_ticker.
                                "</a></span>";
                        } else {
                            $ticker_wrap .=
                                "<span class='md-ticker-span md-d-none'><a href='" .
                                $ticker_link .
                                "'>" .
                                $ticker_name .''.$after_ticker.
                                "</a></span>";
                        }
    
                        $i++;
                    }

                }
                else{
                    $ticker_wrap .="-";
                }
                

                $ticker_wrap .= "</div>";

                if ($tickers_count > 7) {
                    $ticker_wrap .=
                        "<button id='md-ticker-wrap-more-" .
                        $letter_id .
                        "' class='md-ticker-wrap-more-btn'>...more</button>";
                }

                if (is_user_logged_in()) {
                    if ($status == "tracking") {
                        $subscription_btn_html =
                            "<button id='md_fund_subscribe_btn_id_" .$letter_id ."' class='md-fund-subscribe-btn_class_".$fund_id ."  md-fund-subscribe-btn md-fund-table-subscribe-btn md-fund-subscribe-btn--subscribed' data-fund-id='" .$fund_id ."' data-letter-id='" .$letter_id ."'>Unfollow</button>";
                    } else {
                        $subscription_btn_html =
                            "<button id='md_fund_subscribe_btn_id_" .$letter_id ."' class='md-fund-subscribe-btn_class_".$fund_id ." md-fund-subscribe-btn md-fund-table-subscribe-btn md-fund-subscribe-btn--not-subscribed' data-fund-id='" .$fund_id ."' data-letter-id='" .$letter_id ."'>Follow</button>";
                    }
                } else {
                    $login_url = site_url("/register");

                    $subscription_btn_html =
                        "<a href='" .
                        $login_url .
                        "' class='md-slide-login-btn' id='fund-track'>Follow</a>";
                }

                if ($fund_status === "publish") {
                    $return_table .=
                        "

                <tr class='md-fund-table-row " .
                        $hide_row_css .
                        "'>

                    <td class='md-text-center'>" .
                        $quarter .
                        "</td>

                    <td>" .
                        $fund_name .
                        "</td>

                    <td class='md-text-center'><a href='" .
                        $fund_url .
                        "'>View</a></td>

                    <td>" .
                        $key_person .
                        "</td>

                    <td class='md-fund-tbl-status'>" .
                        $subscription_btn_html .
                        "</td>

                    <td class='md-text-center'>" .
                        $quarterly .
                        "</td>

                    <td class='md-text-center'>" .
                        $ytd .
                        "</td>

                    <td class='md-tickers-td'>" .
                        $ticker_wrap .
                        "</td>

                    <td class='md-text-center'><a href=" .
                        $letter_link .
                        " target='_blank'>View</a></td>

                </tr>";
                }

                $row_count++;
            }
            $total_row_count = count($filtered_fund_letters_array);

            if ($total_row_count > 29) {
                $showing = "30";
            } else {
                $showing = $total_row_count;
            }

        }
    } else {
        $return_table .=
            "<tr><td colspan='9'>No results found.</td></tr>";
    }

    $return_table .= "</table></div>";

    if ($fund_status === "publish") {
        $return_table .= "<div class='md-fund-table-paging'>";

        $return_table .=
            "<p>Showing <span id='md-current-count'>" .
            $showing .
            "</span> of <span id='md-total-count'>" .
            $total_row_count .
            "</span>. ";
    }

    if ($total_row_count > 30) {
        $return_table .=
            "<button id='md-fund-table-more-btn' class='md-btn' type='button'>More...</button></p>";
    }

    // $return_table .= "</div>";

    return $return_table;
}

// #md_ajax_fund_subscribe

// Function Description: Ajax function to save subscritions to funds

// Called by: mdBuysideFrontend.js mdUpdateFundSubscription()

function md_ajax_fund_subscribe()
{
    global $wpdb;

    // Get data from ajax

    $fund_id = $_POST["fund_id"];
    $letter_id = $_POST["letter_id"];

    $user_id = get_current_user_id();

    // If data valid update DB

    if (is_numeric($fund_id) && is_numeric($user_id)) {
        // Check if the user is already subscribed to this fund

        $table_name = $wpdb->prefix . "fund_subscriptions";

        $subscription_id = $wpdb->get_var(
            "SELECT id FROM $table_name WHERE fund_id = '$fund_id' AND user_id = '$user_id'"
        );

        if (is_numeric($subscription_id)) {
            // User is already subscribed to this fund so unsubscribe them

            $table_name = $wpdb->prefix . "fund_subscriptions";

            $wpdb->delete($table_name, ["id" => $subscription_id]);

            // Check for WPDB errors

            if ($wpdb->last_error) {
                // echo 'Error ' . $wpdb->last_error;

                $result = "Unsubscribe Error";
            } else {
                $result = "Unsubscribe Success";
            }
        } else {
            // User isn't subscribed to this fund so subscribe them

            $table_name = $wpdb->prefix . "fund_subscriptions";

            $wpdb->insert($table_name, [
                "fund_id" => $fund_id,

                "user_id" => $user_id,
            ]);

            // Check for WPDB errors

            if ($wpdb->last_error) {
                // echo 'Error ' . $wpdb->last_error;

                $result = "Subscribe Error";
            } else {
                $result = "Subscribe Success";
            }
        }
    } else {
        $result = "Validation Error";
    }

    // Return

    $return = [
        "result" => $result,

        "user_id" => $user_id,

        "fund_Id" => $fund_id,
    ];

    echo json_encode($return);

    wp_die();
}

// #md_fund_subscribe_form_creation

// Function Description: Creates the Fund Subscribe Button

// Called by: fund-subscribe-button Shortcode

function md_fund_subscribe_form_creation()
{
    if (is_user_logged_in() && is_singular("funds")) {
        $fund_id = get_the_ID();

        $current_user = wp_get_current_user();

        $user_id = $current_user->ID;

        global $wpdb;

        // Check if the user is already subscribed to this fund

        $table_name = $wpdb->prefix . "fund_subscriptions";

        $subscribed = $wpdb->get_var(
            "SELECT id FROM $table_name WHERE fund_id = '$fund_id' AND user_id = '$user_id'"
        );

        if (is_numeric($subscribed)) {
            $subscribed_class = "md-fund-subscribe-btn--subscribed";

            $subscribe_btn_text = "Unfollow this Fund";
        } else {
            $subscribed_class = "md-fund-subscribe-btn--not-subscribed";

            $subscribe_btn_text = "Follow this Fund";
        }

        // display the button

        echo "<button id='md_fund_subscribe_btn_id_" .$fund_id ."' class='md-fund-subscribe-btn " .$subscribed_class ." ' data-fund-id='" .$fund_id ."'>" .$subscribe_btn_text ."</button>";
    }
}

add_shortcode("fund-subscribe-button", "md_fund_subscribe_form_creation");

// #md_ajax_ticker_subscribe

// Function Description: Ajax function to save subscritions to tickers

// Called by: mdBuysideFrontend.js mdUpdateTickerSubscription()

function md_ajax_ticker_subscribe()
{
    global $wpdb;

    // Get data from ajax

    $ticker_id = $_POST["ticker_id"];

    $user_id = get_current_user_id();

    // If data valid update DB

    if (is_numeric($ticker_id) && is_numeric($user_id)) {
        // Check if the user is already subscribed to this fund

        $table_name = $wpdb->prefix . "ticker_subscriptions";

        $subscription_id = $wpdb->get_var(
            "SELECT id FROM $table_name WHERE ticker_id = '$ticker_id' AND user_id = '$user_id'"
        );

        if (is_numeric($subscription_id)) {
            // User is already subscribed to this fund so unsubscribe them

            $table_name = $wpdb->prefix . "ticker_subscriptions";

            $wpdb->delete($table_name, ["id" => $subscription_id]);

            // Check for WPDB errors

            if ($wpdb->last_error) {
                // echo 'Error ' . $wpdb->last_error;

                $result = "Unsubscribe Error";
            } else {
                $result = "Unsubscribe Success";
            }
        } else {
            // User isn't subscribed to this fund so subscribe them

            $table_name = $wpdb->prefix . "ticker_subscriptions";

            $wpdb->insert($table_name, [
                "ticker_id" => $ticker_id,

                "user_id" => $user_id,
            ]);

            // Check for WPDB errors

            if ($wpdb->last_error) {
                // echo 'Error ' . $wpdb->last_error;

                $result = "Subscribe Error";
            } else {
                $result = "Subscribe Success";
            }
        }
    } else {
        $result = "Validation Error";
    }

    // Return

    $return = [
        "result" => $result,

        "user_id" => $user_id,

        "fund_Id" => $ticker_id,
    ];

    echo json_encode($return);

    wp_die();
}

// #md_ticker_subscribe_form_creation

// Function Description: Creates the Fund Subscribe Button

// Called by: fund-subscribe-button Shortcode

function md_ticker_subscribe_form_creation()
{
    if (is_user_logged_in() && is_archive("tickers")) {
        $ticker_id = get_queried_object_id();

        $current_user = wp_get_current_user();

        $user_id = $current_user->ID;

        global $wpdb;

        // Check if the user is already subscribed to this fund

        $table_name = $wpdb->prefix . "ticker_subscriptions";

        $subscribed = $wpdb->get_var(
            "SELECT id FROM $table_name WHERE ticker_id = '$ticker_id' AND user_id = '$user_id'"
        );

        if (is_numeric($subscribed)) {
            $subscribed_class = "md-ticker-subscribe-btn--subscribed";

            $subscribe_btn_text = "Unfollow Ticker";
        } else {
            $subscribed_class = "md-ticker-subscribe-btn--not-subscribed";

            $subscribe_btn_text = "Follow Ticker";
        }

        // display the button

        echo "<button id='md_ticker_subscribe_btn_id_" .
            $ticker_id .
            "' class='md-ticker-subscribe-btn " .
            $subscribed_class .
            " '>" .
            $subscribe_btn_text .
            "</button>";
    }
}

add_shortcode("ticker-subscribe-button", "md_ticker_subscribe_form_creation");

// #md_get_users_to_send_letters_to()

// Function Description: Checks funds and tickers for letters published and returns array of users

// Called by:

function md_get_users_to_send_letters_to($letters_array)
{
    // create the arrays

    $funds_array = [];

    $tickers_array = [];

    $users_array = [];

    foreach ($letters_array as $letter) {
        $letter_id = $letter["letter_id"];

        // Get the related funds for this letter

        $fund_ids_array = md_get_related_fund_ids($letter_id);

        // Get the users (id) that are subscribed to these funds and add them to the $users_array

        if (count($fund_ids_array) > 0) {
            $funds_users_array = md_get_funds_subscribed_users($fund_ids_array);

            if (count($funds_users_array) > 0) {
                foreach ($funds_users_array as $user_id) {
                    array_push($users_array, $user_id);
                }
            }
        }

        // Get the related tickers for this letter

        $ticker_ids_array = md_get_related_tickers_ids($letter_id);

        // Get the users (id) that are subscribed to these ticers and add them to the $users_array

        if (count($ticker_ids_array) > 0) {
            $tickers_users_array = md_get_tickers_subscribed_users(
                $ticker_ids_array
            );

            if (count($tickers_users_array) > 0) {
                foreach ($tickers_users_array as $user_id) {
                    array_push($users_array, $user_id);
                }
            }
        }
    }

    // Remove the duplicates from $users_array

    $users_array = array_unique($users_array);

    return $users_array;
}

// #md_get_user_email_data

// Function Description: Gets the users data and saves in array.

// Called by:

function md_get_user_email_data($subscriber_id)
{
    $new_user = get_userdata($subscriber_id);

    if (!empty($new_user)) {
        $first_name = $new_user->first_name;

        $email_address = $new_user->user_email;

        $user_name = $new_user->user_login;

        $users_email_data = [
            "user_exists" => "True",
            "user_id" => $subscriber_id,
            "user_name" => $user_name,
            "email_address" => $email_address,
            "first_name" => $first_name,
        ];
    } else {
        $users_email_data = [
            "user_exists" => "False",
            "user_id" => "",
            "user_name" => "",
            "email_address" => "",
            "first_name" => "",
        ];
    }

    return $users_email_data;
}

// #md_get_users_subscribed_funds

// Function Description: Creates an array of Funds a user is subscribed to.

// Called by: button-feed.php, md_create_email

function md_get_users_subscribed_funds($subscriber_id = null)
{
    // If the function is called by button-feed.php $subscriber_id will be NULL so get the current users ID instead

    if ($subscriber_id == null) {
        $user_id = get_current_user_id();
    } else {
        $user_id = $subscriber_id;
    }

    $fund_array = [];

    if (is_numeric($user_id) && $user_id > 0) {
        global $wpdb;

        $table_name = $wpdb->prefix . "fund_subscriptions";

        $results = $wpdb->get_results(
            "SELECT fund_id FROM $table_name WHERE user_id = $user_id"
        );

        if ($results) {
            foreach ($results as $result) {
                $fund_id = $result->fund_id;

                array_push($fund_array, $fund_id);
            }
        }
    }

    return $fund_array;
}

// #md_get_users_subscribed_tickers

// Function Description: Creates an array of Tickers a user is subscribed to.

// Called by: button-feed.php, md_create_email

function md_get_users_subscribed_tickers($subscriber_id = null)
{
    // If the function is called by button-feed.php $subscriber_id will be NULL so get the current users ID instead

    if ($subscriber_id == null) {
        $user_id = get_current_user_id();
    } else {
        $user_id = $subscriber_id;
    }

    $ticker_array = [];

    if (is_numeric($user_id) && $user_id > 0) {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT ticker_id FROM {$wpdb->prefix}ticker_subscriptions AS ts INNER JOIN {$wpdb->prefix}terms AS wt  ON ts.ticker_id = wt.term_id AND user_id = $user_id"
        );

        if ($results) {
            foreach ($results as $result) {
                $ticker_id = $result->ticker_id;

                array_push($ticker_array, $ticker_id);
            }
        }
    }

    $ticker_array = array_unique($ticker_array);

    return $ticker_array;
}

// #md_get_funds_subscribed_users

// Function Description: Creates an array of Users that have subscribed to a fund.

// Called by:

function md_get_funds_subscribed_users($fund_ids_array)
{
    $user_array = [];

    if (count($fund_ids_array) > 0) {
        global $wpdb;

        $table_name = $wpdb->prefix . "fund_subscriptions";

        // Generate the SQL statement.

        // The number of %s items is based on the length of the $fund_ids_array array

        $sql =
            "

            SELECT DISTINCT user_id

            FROM $table_name

            WHERE fund_id IN(" .
            implode(", ", array_fill(0, count($fund_ids_array), "%s")) .
            ")

          ";

        // Call $wpdb->prepare passing the values of the array as separate arguments

        $query = call_user_func_array(
            [$wpdb, "prepare"],
            array_merge([$sql], $fund_ids_array)
        );

        $results = $wpdb->get_results($query);

        if ($results) {
            foreach ($results as $result) {
                $user_id = $result->user_id;

                array_push($user_array, $user_id);
            }
        }
    }

    return $user_array;
}

// #md_get_tickers_subscribed_users

// Function Description: Creates an array of Users that have subscribed to a ticker.

// Called by:

function md_get_tickers_subscribed_users($tickers_ids_array)
{
    $user_array = [];

    if (count($tickers_ids_array) > 0) {
        global $wpdb;

        $table_name = $wpdb->prefix . "ticker_subscriptions";

        // Generate the SQL statement.

        // The number of %s items is based on the length of the $tickers_ids_array array

        $sql =
            "

            SELECT DISTINCT user_id

            FROM $table_name

            WHERE ticker_id IN(" .
            implode(", ", array_fill(0, count($tickers_ids_array), "%s")) .
            ")

          ";

        // Call $wpdb->prepare passing the values of the array as separate arguments

        $query = call_user_func_array(
            [$wpdb, "prepare"],
            array_merge([$sql], $tickers_ids_array)
        );

        $results = $wpdb->get_results($query);

        if ($results) {
            foreach ($results as $result) {
                $user_id = $result->user_id;

                array_push($user_array, $user_id);
            }
        }
    }

    return $user_array;
}

// #md_get_related_fund_ids

// Function Description: Gets the connected funds for a letter

// Called by: md_get_letters_to_send

function md_get_related_fund_ids($letter_id)
{
    global $wpdb;

    $table_name = $wpdb->prefix . "jet_rel_default";

    $results = $wpdb->get_results(
        "SELECT parent_object_id FROM $table_name WHERE child_object_id = $letter_id"
    );

    $fund_ids = [];

    if ($results) {
        foreach ($results as $result) {
            $fund_id = $result->parent_object_id;

            array_push($fund_ids, $fund_id);
        }
    }

    return $fund_ids;
}

// #md_get_letters_ids_for_tickers

// Function Description: Gets the connected letters for an array of tickers and returns the letter ids as an array

// Called by:

function md_get_letters_ids_for_tickers($ticker_ids_array)
{
    // Remove any duplicates from $ticker_ids_array
    $ticker_ids_array = array_unique($ticker_ids_array);

    // Create return array
    $return_array = [];

    // Query the DB
    global $wpdb;
    $table_name = $wpdb->prefix . "term_relationships";
    $posts_table = $wpdb->posts;

    $sql =
        "
        SELECT DISTINCT object_id
        FROM $table_name AS tr
        INNER JOIN $posts_table AS p ON tr.object_id = p.ID
        WHERE term_taxonomy_id IN (" .
        implode(", ", array_fill(0, count($ticker_ids_array), "%s")) .
        ")
        AND p.post_type = 'letters'
        ORDER BY p.post_modified DESC
        ";

    // Call $wpdb->prepare passing the values of the array as separate arguments
    $query = call_user_func_array(
        [$wpdb, "prepare"],
        array_merge([$sql], $ticker_ids_array)
    );

    $results = $wpdb->get_results($query);

    if ($results) {
        foreach ($results as $result) {
            $return_array[] = $result->object_id;
        }
    }

    return $return_array;
}



// #md_get_related_tickers_ids

// Function Description: Gets the connected tickers for a letter and returns them as an array

// Called by: md_get_letters_to_send

function md_get_related_tickers_ids($letter_id)
{
    $ticker_ids = [];

    $term_list = get_the_terms($letter_id, "tickers");

    if ($term_list) {
        foreach ($term_list as $term_single) {
            array_push($ticker_ids, $term_single->term_id);
        }
    }

    return $ticker_ids;
}

// #md_get_letters_to_send

// Function Description: Emails the Letters to subscribers

// Called by: md_create_email

function md_get_letters_to_send()
{
    // Get this weeks letters

    $letters_array = [];

    $todays_date = date("Y-m-d");

    $one_week_ago_date = date("Y-m-d", strtotime("-7 days"));

    $args = [
        "post_type" => "letters",

        "post_status" => "publish",
    ];

    $loop = new WP_Query($args);

    $i = 0;

    while ($loop->have_posts()):
        $loop->the_post();

        global $post;

        if (
            $post->post_date > $one_week_ago_date &&
            $post->post_date < $todays_date
        ) {
            $letter_id = $post->ID;

            $letter_title = $post->post_title;

            /*

              $siteurl = get_option('siteurl');

              $letter_url = $siteurl."/letters/".$post->post_name;

            */

            $letter_url = get_post_meta($letter_id, "letter-link", true);

            // Create an array to hold the subscribers to this letter

            $this_letters_subscribers = [];

            // Get the related fund ids for this letter

            $related_funds_array = md_get_related_fund_ids($letter_id);

            // Get the users who have subscribed to the related funds

            $related_funds_subscribers = md_get_tickers_subscribed_users(
                $related_funds_array
            );

            // Get the related ticker ids for this letter

            $related_tickers_array = md_get_related_tickers_ids($letter_id);

            // Get the users who have subscribed to the related tickers

            $related_tickers_subscribers = md_get_tickers_subscribed_users(
                $related_tickers_array
            );

            // Add the Related Funds Subscribers to this $this_letters_subscribers

            $this_letters_subscribers = array_merge(
                $related_funds_subscribers,
                $related_tickers_subscribers
            );

            // Remove all of the duplicates from the array

            $this_letters_subscribers = array_unique($this_letters_subscribers);

            $letters_array[$i] = [
                "letter_id" => $letter_id,

                "letter_title" => $letter_title,

                "letter_url" => $letter_url,

                "users_subscribed_to_this_letter" => $this_letters_subscribers,
            ];
        }

        $i++;
    endwhile;

    return $letters_array;
}

// #md_create_email

// Function Description: Emails the Letters to subscribers

// Called by: Cron job

function md_create_email()
{
    // Check if we have already sent emails for this week (WP Cron Jobs arent accurate enough)

    // Is today a Monday?

    $today = date("Y-m-d");

    $dayofweek = date("w", strtotime($today));

    // Check if we have sent an email already today

    global $wpdb;

    $table_name = $wpdb->prefix . "fund_emails";

    $sent_emails = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE DATE(sent_date) = '$today' "
    );

    if ($dayofweek == 1 && count($sent_emails) < 1) {
        // Check if any letters were published in the last seven days

        $letters_array = md_get_letters_to_send();

        // Get a list of subscribers to send emails to based on fund and ticker subscription

        $subscribers_array = md_get_users_to_send_letters_to($letters_array);

        // Send an email to each subscriber

        if (count($subscribers_array) > 0) {
            foreach ($subscribers_array as $subscribers_id) {
                // Create Array to hold the letters to send

                $subscribers_letters_to_send_array = [];

                // Iterate through letters and check if this user is subscribed to the letter

                foreach ($letters_array as $letter) {
                    $letter_id = $letter["letter_id"];

                    // If user is subscribed add letter to $subscribers_letters_to_send_array

                    if (
                        in_array(
                            $subscribers_id,
                            $letter["users_subscribed_to_this_letter"]
                        )
                    ) {
                        $subscribers_letters_to_send_array[$letter_id] = [
                            "letter_title" => $letter["letter_title"],

                            "letter_url" => $letter["letter_url"],
                        ];
                    }
                }

                // If this subscriber has any letters to send then send 'em

                if (count($subscribers_letters_to_send_array) > 0) {
                    $users_email_data = md_get_user_email_data($subscribers_id);

                    if ($users_email_data["user_exists"] == "True") {
                        $to = $users_email_data["email_address"];

                        $first_name = $users_email_data["first_name"];

                        $username = $users_email_data["user_name"];

                        $letters_attachment_display = "";

                        $letters_attachment_count = 0;

                        foreach ($subscribers_letters_to_send_array as $subscribers_letter_to_send) {
                            if (
                                !empty(
                                $subscribers_letter_to_send["letter_url"]
                            )
                            ) {
                                $letters_attachment_display .=
                                    '<p style="font-family: sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>' .
                                    $subscribers_letter_to_send[
                                        "letter_title"
                                    ] .
                                    "</strong><br>";

                                $letters_attachment_display .=
                                    '<a href="' .
                                    $subscribers_letter_to_send["letter_url"] .
                                    '">View Letter</a></p>';

                                $letters_attachment_count++;
                            }
                        }

                        if ($letters_attachment_count > 0) {
                            if ($letters_attachment_count > 1) {
                                $letters_link_introduction =
                                    '<p style="font-family: sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 15px;">We have uploaded some new letters for funds you are following on <a href="https://www.buysidedigest.com">www.buysidedigest.com</a></p>';
                            } else {
                                $letters_link_introduction =
                                    '<p style="font-family: sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 15px;">We have uploaded a new letter for funds you are following on <a href="https://www.buysidedigest.com">www.buysidedigest.com</a></p>';
                            }

                            $year = date("Y");

                            md_send_email(
                                $first_name,
                                $to,
                                $letters_attachment_display,
                                $letters_link_introduction,
                                $year
                            );
                        }
                    }
                }
            }
        }

        global $wpdb;

        $table_name = $wpdb->prefix . "fund_emails";

        $now = date("Y-m-d");

        $wpdb->insert($table_name, [
            "sent_date" => $now,
        ]);
    }
}

// #md_test_create_email

// Function Description: Emails the Letters to subscribers

// Called by: MD Letters Page

function md_test_create_email()
{
    // Check if we have already sent emails for this week (WP Cron Jobs arent accurate enough)

    // Is today a Monday?

    $today = date("Y-m-d");

    $dayofweek = date("w", strtotime($today));

    // Check if we have sent an email already today

    global $wpdb;

    $table_name = $wpdb->prefix . "fund_emails";

    $sent_emails = $wpdb->get_results(
        "SELECT * FROM $table_name WHERE DATE(sent_date) = '$today' "
    );

    if (count($sent_emails) < 1) {
        // Check if any letters were published in the last seven days

        $letters_array = md_get_letters_to_send();

        // Get a list of subscribers to send emails to based on fund and ticker subscription

        $subscribers_array = md_get_users_to_send_letters_to($letters_array);

        // Send an email to each subscriber

        if (count($subscribers_array) > 0) {
            foreach ($subscribers_array as $subscribers_id) {
                // Create Array to hold the letters to send

                $subscribers_letters_to_send_array = [];

                // Iterate through letters and check if this user is subscribed to the letter

                foreach ($letters_array as $letter) {
                    $letter_id = $letter["letter_id"];

                    // If user is subscribed add letter to $subscribers_letters_to_send_array

                    if (
                        in_array(
                            $subscribers_id,
                            $letter["users_subscribed_to_this_letter"]
                        )
                    ) {
                        $subscribers_letters_to_send_array[$letter_id] = [
                            "letter_title" => $letter["letter_title"],

                            "letter_url" => $letter["letter_url"],
                        ];
                    }
                }

                // If this subscriber has any letters to send then send 'em

                if (count($subscribers_letters_to_send_array) > 0) {
                    $users_email_data = md_get_user_email_data($subscribers_id);

                    if ($users_email_data["user_exists"] == "True") {
                        $to = $users_email_data["email_address"];

                        $first_name = $users_email_data["first_name"];

                        $username = $users_email_data["user_name"];

                        $letters_attachment_display = "";

                        $letters_attachment_count = 0;

                        foreach ($subscribers_letters_to_send_array as $subscribers_letter_to_send) {
                            if (
                                !empty(
                                $subscribers_letter_to_send["letter_url"]
                            )
                            ) {
                                $letters_attachment_display .=
                                    '<p style="font-family: sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 15px;"><strong>' .
                                    $subscribers_letter_to_send[
                                        "letter_title"
                                    ] .
                                    "</strong><br>";

                                $letters_attachment_display .=
                                    '<a href="' .
                                    $subscribers_letter_to_send["letter_url"] .
                                    '">View Letter</a></p>';

                                $letters_attachment_count++;
                            }
                        }

                        if ($letters_attachment_count > 0) {
                            if ($letters_attachment_count > 1) {
                                $letters_link_introduction =
                                    '<p style="font-family: sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 15px;">We have uploaded some new letters for funds you are following on <a href="https://www.buysidedigest.com">www.buysidedigest.com</a></p>';
                            } else {
                                $letters_link_introduction =
                                    '<p style="font-family: sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 15px;">We have uploaded a new letter for funds you are following on <a href="https://www.buysidedigest.com">www.buysidedigest.com</a></p>';
                            }

                            echo "<h4>Subscriber Id: " .
                                $subscribers_id .
                                "</h4>";

                            echo "email_address: " . $to . "<br>";

                            echo "first_name: " . $first_name . "<br>";

                            echo "user_name: " . $username . "<br>";

                            echo "Letters: <br>";

                            echo $letters_attachment_display . "<br>";

                            echo "<br>";

                            echo "<br>";

                            $year = date("Y");

                            md_send_email(
                                $first_name,
                                $to,
                                $letters_attachment_display,
                                $letters_link_introduction,
                                $year
                            );
                        }
                    }
                }
            }
        }
    }
}

// #md_send_email

// Function Description: Creates a metabox for the Fund Title

// Called by: On load

function md_send_email(
    $first_name,
    $to,
    $letters_attachment_display,
    $letters_link_introduction,
    $year
) {
    // Build the salutation

    if (!empty($first_name)) {
        $salutation = "<p>Hi " . $first_name . ",</p>";
    } else {
        $salutation = "<p>Hi,</p>";
    }

    $mail_content =
        '<!doctype html>

          <html>

            <head>

              <meta name="viewport" content="width=device-width, initial-scale=1.0">

              <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

              <title>Simple Transactional Email</title>

              <style>

          @media only screen and (max-width: 620px) {

            table.body h1 {

              font-size: 28px !important;

              margin-bottom: 10px !important;

            }



            table.body p,

          table.body ul,

          table.body ol,

          table.body td,

          table.body span,

          table.body a {

              font-size: 16px !important;

            }



            table.body .wrapper,

          table.body .article {

              padding: 10px !important;

            }



            table.body .content {

              padding: 0 !important;

            }



            table.body .container {

              padding: 0 !important;

              width: 100% !important;

            }



            table.body .main {

              border-left-width: 0 !important;

              border-radius: 0 !important;

              border-right-width: 0 !important;

            }



            table.body .btn table {

              width: 100% !important;

            }



            table.body .btn a {

              width: 100% !important;

            }



            table.body .img-responsive {

              height: auto !important;

              max-width: 100% !important;

              width: auto !important;

            }

          }

          @media all {

            .ExternalClass {

              width: 100%;

            }



            .ExternalClass,

          .ExternalClass p,

          .ExternalClass span,

          .ExternalClass font,

          .ExternalClass td,

          .ExternalClass div {

              line-height: 100%;

            }



            .apple-link a {

              color: inherit !important;

              font-family: inherit !important;

              font-size: inherit !important;

              font-weight: inherit !important;

              line-height: inherit !important;

              text-decoration: none !important;

            }



            #MessageViewBody a {

              color: inherit;

              text-decoration: none;

              font-size: inherit;

              font-family: inherit;

              font-weight: inherit;

              line-height: inherit;

            }



            .btn-primary table td:hover {

              background-color: #34495e !important;

            }



            .btn-primary a:hover {

              background-color: #34495e !important;

              border-color: #34495e !important;

            }

          }

          </style>

            </head>

            <body style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">

              <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">This is preheader text. Some clients will show this text as a preview.</span>



              <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" width="100%" bgcolor="#f6f6f6">

                <tr>

                  <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">&nbsp;</td>

                  <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; margin: 0 auto;" width="580" valign="top">

                    <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;">

                      <div class="container" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 0; color: #ffffff; background-color: #05222f;">

                        <img src="https://buyside.memblab.com/wp-content/uploads/2023/02/buyside-header.png" alt="Buyside Digest" width="560" height="123" />

                      </div>

                      <!-- START CENTERED WHITE CONTAINER -->

                      <table role="presentation" class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #ffffff; border-radius: 3px; width: 100%;" width="100%">



                        <!-- START MAIN CONTENT AREA -->

                        <tr>

                          <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;" valign="top">

                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">

                              <tr>

                                <td style="font-family: sans-serif; font-size: 16px; vertical-align: top;" valign="top">

                                  <p style="font-family: sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 15px;">' .
        $salutation .
        '</p>

                                  <p style="font-family: sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 15px;">' .
        $letters_link_introduction .
        '</p>

                                  <p style="font-family: sans-serif; font-size: 16px; font-weight: normal; margin: 0; margin-bottom: 15px;">Here are the new letters</p>

                                  ' .
        $letters_attachment_display .
        '

                                  <p>Kind regards,<br>Buyside Digest</p>

                                  <img style="margin-left: auto; margin-right: auto; display: block; margin-bottom: 10px;" src="https://buyside.memblab.com/wp-content/uploads/2023/02/Buyside-footer.png" alt="buyside Digest" width="130" height="84" />

                                  <p style="font-family: sans-serif; text-align: center; font-style: italic; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 5px;">Copyright &copy; ' .
        $year .
        ' <a href="https://www.buysidedigest.com">Buysidedigest.com</a>. All rights reserved.</p>

                                  <p style="font-family: sans-serif; font-size: 14px; text-align: center; font-weight: normal; margin: 0; margin-bottom: 15px;">You are receiving this email because you opted in via our website.</p>

                                </td>

                              </tr>

                            </table>

                          </td>

                        </tr>



                      <!-- END MAIN CONTENT AREA -->

                      </table>

                      <!-- END CENTERED WHITE CONTAINER -->

                    </div>

                  </td>

                  <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">&nbsp;</td>

                </tr>

              </table>

            </body>

          </html>';

    $subject = "Buyside Letter";

    // wp_mail($to, $subject, $mail_content);
}

// #add_meta_boxes

// Function Description: Creates a metabox for the Fund Title

// Called by: On load

add_action("add_meta_boxes", function () {
    add_meta_box(
        "md-fund-text",

        "Fund Text",

        "md_create_fund_text_field_meta_box",

        ["letters"]
    );
});

// #md_create_fund_text_field_meta_box

// Function Description: Creates the Fund Title HTML and adds a value

// Called by: add_meta_boxes

function md_create_fund_text_field_meta_box($post)
{
    $fund_text = get_post_meta($post->ID, "fund_text_meta_key", true); ?>

    <label for="md_import_fund_text">Fund Text</label>

    <input type="text" id="md_import_fund_text" name="md_import_fund_text" value="<?php echo $fund_text; ?>" disabled />

    <?php
}

// #md_save_postdata

// Function Description: Saves the meta key value

// Called by: Fires on Post Save

function md_save_postdata($post_id)
{
    if (array_key_exists("md_import_fund_text", $_POST)) {
        update_post_meta(
            $post_id,

            "fund_text_meta_key",

            $_POST["md_import_fund_text"]
        );
    }
}

add_action("save_post", "md_save_postdata");

// #md_letter_import_add_parent_fund

// Function Description: Adds the parent fund to wp_jet_rel_default

// Called by: Called by pmxi_saved_post hook

function md_letter_import_add_parent_fund($post_id)
{
    $fund_title = get_post_meta($post_id, "fund_text_meta_key", true);

    // Check if the Fund exists and get its ID

    global $wpdb;

    $table_name = $wpdb->prefix . "posts";

    $fund_id = $wpdb->get_var(
        "SELECT ID FROM $table_name WHERE post_title = '$fund_title' AND post_type = 'funds'"
    );

    // Add the entry to wp_jet_rel_default

    $import_check = false;

    if (is_numeric($fund_id) && $fund_id > 0) {
        $table_name = $wpdb->prefix . "jet_rel_default";

        $import_check = $wpdb->insert($table_name, [
            "parent_object_id" => $fund_id,

            "child_object_id" => $post_id,

            "rel_id" => "5",

            "parent_rel" => "0",
        ]);
    }

    // if $import_check is true save the post else add an error to the log

    if ($import_check == true) {
        // Add success note to Log

        $logger = function ($m) {
            printf("[%s] $m", date("H:i:s"));
            flush();
        };

        call_user_func(
            $logger,
            "Buyside, adding Parent Fund (" . $fund_title . ") Success"
        );
    } else {
        // add error note to log

        $logger = function ($m) {
            printf("[%s] $m", date("H:i:s"));
            flush();
        };

        call_user_func(
            $logger,
            "Buyside, adding Parent Fund (" . $fund_title . ") Failed"
        );
    }
}

add_action("pmxi_saved_post", "md_letter_import_add_parent_fund", 10, 1);

// #md_letter_import_add_parent_fund

// Function Description: Adds the parent fund to wp_jet_rel_default

// Called by: Called by pmxi_saved_post hook

function md_create_only_if_fund_exists_in_db(
    $continue_import,
    $data,
    $import_id
) {
    // Check the data includes a "fundtitle" key (I.E this is a Letters Import) otherwise do nothing

    if (array_key_exists("fundtitle", $data)) {
        $fund_title = $data["fundtitle"];

        // Check if the Fund exists and get its ID

        global $wpdb;

        $table_name = $wpdb->prefix . "posts";

        $fund_id = $wpdb->get_var(
            "SELECT ID FROM $table_name WHERE post_title = '$fund_title' AND post_type = 'funds'"
        );

        // If a fund_id is returned return true else return false

        if (is_numeric($fund_id) && $fund_id > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        if ($continue_import == 1) {
            return true;
        } else {
            return false;
        }
    }
}

add_filter(
    "wp_all_import_is_post_to_create",
    "md_create_only_if_fund_exists_in_db",
    10,
    3
);

/*zeba development*/

function md_fund_subscribe_form_btn($fund_id, $link)
{
    if (is_user_logged_in()) {
        // $fund_id = get_the_ID();
        $current_user = wp_get_current_user();

        $user_id = $current_user->ID;

        global $wpdb;

        // Check if the user is already subscribed to this fund

        $table_name = $wpdb->prefix . "fund_subscriptions";

        $subscribed = $wpdb->get_var(
            "SELECT id FROM $table_name WHERE fund_id = '$fund_id' AND user_id = '$user_id'"
        );

        if (is_numeric($subscribed)) {
            $subscribed_class = "md-fund-subscribe-btn--subscribed";

            $subscribe_btn_text = "Unfollow";
        } else {
            $subscribed_class = "md-fund-subscribe-btn--not-subscribed";

            $subscribe_btn_text = "Follow";
        }

        // display the button

        echo "<button id='md_fund_subscribe_btn_id_" .$fund_id ."' class='md-fund-subscribe-btn welcomeboard " .$subscribed_class ." 'data-link='".$link."' data-fund-id='" .$fund_id ."'>" .$subscribe_btn_text ."</button>";
    }
}