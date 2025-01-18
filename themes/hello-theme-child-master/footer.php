<?php
/**
 * The template for displaying the footer.
 *
 * Contains the body & html closing tags.
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	if ( did_action( 'elementor/loaded' ) && hello_header_footer_experiment_active() ) {
		get_template_part( 'template-parts/dynamic-footer' );
	} else {
		get_template_part( 'template-parts/footer' );
	}
}

?>

 

<!-- <button class="trigger">Click the modal!</button>
<div class="modal">
    <div class="modal-content">
        <span class="close-button">×</span>
		<div class="modal-body">
			<div id="mc_embed_shell">
				<link href="//cdn-images.mailchimp.com/embedcode/classic-061523.css" rel="stylesheet" type="text/css"> -->
				<!-- <div id="mc_embed_signup">
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
							</div> -->
							<!-- <div class="optionalParent">
								<div class="clear foot">
									<input type="submit" name="subscribe" id="mc-embedded-subscribe" class="button" value="Subscribe"> -->
									<!-- <p style="margin: 0px auto;"><a href="http://eepurl.com/iD7bBM" title="Mailchimp - email marketing made easy and fun"><span style="display: inline-block; background-color: transparent; border-radius: 4px;"><img class="refferal_badge" src="https://digitalasset.intuit.com/render/content/dam/intuit/mc-fe/en_us/images/intuit-mc-rewards-text-dark.svg" alt="Intuit Mailchimp" style="width: 220px; height: 40px; display: flex; padding: 2px 0px; justify-content: center; align-items: center;"></span></a></p> -->
								<!-- </div>
							</div>
						</div>
					</form>
				</div>
				<script type="text/javascript" src="//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js"></script><script type="text/javascript">(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';fnames[4]='PHONE';ftypes[4]='phone';fnames[0]='EMAIL';ftypes[0]='email';fnames[3]='ADDRESS';ftypes[3]='address';fnames[5]='BIRTHDAY';ftypes[5]='birthday';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
			</div>
		</div>
    </div>
</div> -->

      	
      	


<?php wp_footer(); ?>

<script>
	
	jQuery(document).ready(function($) {
		jQuery('.gform_wrapper.gravity-theme #field_5_3').css('display',"none");
		jQuery('.gform_wrapper.gravity-theme #gform_submit_button_5').css('display',"none");
		
		var currentPassword = jQuery('#input_5_4').val();
		if (currentPassword === '') {
			if ($('.error-message').length === 0) {
				jQuery('#input_5_4_1_container .verify-current-pwd').before('<div class="error-message">This field is required.</div>');
			}else{
				jQuery('.error-message').text('This field is required.');
			}
		}else{
			// Make an AJAX request to verify the current password.
			$.ajax({
				type: 'POST',
				url: '<?=admin_url('admin-ajax.php'); ?>', // WordPress AJAX endpoint
				data: {
					action: 'check_current_password',
					currentPassword: currentPassword
				},
				success: function(response) {
					// Handle the response, e.g., show an error message if the password is incorrect.
					if (response === 'invalid') {
						if ($('.error-message').length === 0) {
							jQuery('#input_5_4_1_container .verify-current-pwd').before('<div class="error-message">Current password is incorrect. Please enter correct password to reset new password.</div>');
						}else{
							jQuery('.error-message').text('Current password is incorrect. Please enter correct password to reset new password');
						}
					} else {
						jQuery('.error-message').text(''); // Clear any previous error message.
						jQuery('.error-message').css('display',"none");
						jQuery('.gform_wrapper.gravity-theme #field_5_3').css('display',"block");
						jQuery('.gform_wrapper.gravity-theme #gform_submit_button_5').css('display',"block");
					}
				}
			});
		}
		
		jQuery('#input_5_4').val('');
		jQuery('#input_5_4').attr('autocomplete','current-password');
		// Create a button element
		var button = $('<button class="btn btn-primary mt-3 verify-current-pwd">Click Me</button>');

		// Append the button to the div with ID "hello"
		jQuery('#input_5_4_1_container').append(button);

		$('.verify-current-pwd').on('click', function() {
			var currentPassword = jQuery('#input_5_4').val();
			// console.log(currentPassword);
			if (currentPassword === '') {

				// jQuery('#input_5_4').prop('required', true);
				if ($('.error-message').length === 0) {
					jQuery('#input_5_4_1_container .verify-current-pwd').before('<div class="error-message">This field is required.</div>');
				}else{
					jQuery('.error-message').text('This field is required.');
				}
			}else{
				// Make an AJAX request to verify the current password.
				$.ajax({
					type: 'POST',
					url: '<?=admin_url('admin-ajax.php'); ?>', // WordPress AJAX endpoint
					data: {
						action: 'check_current_password',
						currentPassword: currentPassword
					},
					success: function(response) {
						console.log(response);
						// Handle the response, e.g., show an error message if the password is incorrect.
						if (response === 'invalid') {
							if ($('.error-message').length === 0) {
								jQuery('#input_5_4_1_container .verify-current-pwd').before('<div class="error-message">Current password is incorrect. Please enter correct password to reset new password.</div>');
							}else{
								jQuery('.error-message').text('Current password is incorrect. Please enter correct password to reset new password');
							}
						} else {
							jQuery('.error-message').text(''); // Clear any previous error message.
							jQuery('.error-message').css('display',"none");
							jQuery('.gform_wrapper.gravity-theme #field_5_3').css('display',"block");
							jQuery('.gform_wrapper.gravity-theme #gform_submit_button_5').css('display',"block");
						}
					}
				});
			}
		});


		var modal = document.querySelector(".subscribe-modal");
		var trigger = document.querySelector(".trigger");
		var closeButton = document.querySelector(".subscribe-close-button");

		var fund_removal_modal = document.querySelector(".fund_remove_modal");
		var fund_remove = document.querySelector(".fund-removal");
		var fundCloseButton = document.querySelector(".fund-close-button");

		function toggleModal(modal) {
			modal.classList.toggle("show-modal");
		}

		function windowOnClick(event, modal) {
			if (event.target === modal) {
				toggleModal(modal);
			}
		}

		trigger.addEventListener("click", function () {
			toggleModal(modal);
		});
		closeButton.addEventListener("click", function () {
			toggleModal(modal);
		});
		window.addEventListener("click", function (event) {
			windowOnClick(event, modal);
		});

		fund_remove.addEventListener("click", function () {
			toggleModal(fund_removal_modal);
		});
		fundCloseButton.addEventListener("click", function () {
			toggleModal(fund_removal_modal);
		});
		window.addEventListener("click", function (event) {
			windowOnClick(event, fund_removal_modal);
		});

	});

</script>

<script>
	jQuery(document).ready(function($) {
    $('.free_sign_up').on('submit', function(e) {
		e.preventDefault();
		var currentForm = $(this);
		$(currentForm).find('.load-icon').show();
		$('.invalid_email').empty();
        var email = currentForm.find('.user_email').val();
        // Make AJAX request
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'check_email_exists',
                email: email,
            },
            success: function(response) {
                var result = JSON.parse(response);

                if (result.exists) {
					$(currentForm).find('.load-icon').hide();
                    // Email exists, handle accordingly (e.g., show a message)
                    // console.log('Email exists in the database!');
					var nearest = $(currentForm).next('.invalid_email').html('The user with Email ' + result.email + ' already exist. <br> Please use the different email.');
					
                } else {
					$(currentForm).find('.load-icon').hide();
					$('#popmake-12669 #input_1_3').val(email);
					$('#popmake-12669 #field_1_3').hide();
					$('.popmake-12669').click();
                    // Email doesn't exist, handle accordingly
                    // console.log('Email does not exist in the database.');
                }
            },
        });
    });
});

</script>

<?php
 if (is_front_page()) {
	?>
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>
  // Owl Carousel
  jQuery(document).ready(function ($) {
        var owl = $(".owl-carousel");
        owl.owlCarousel({
            items: 6,
            margin: 10,
            loop: false,
            nav: false,
			slideBy: 4,
			responsive: {
						0: {
							items: 1 // Show 1 item on small screens
						},
						600: {
							items: 3 // Show 2 items on screens wider than 600px
						},
						768: {
							items: 4 // Show 3 items on screens wider than 768px
						},
						992: {
							items: 6 // Show 4 items on screens wider than 992px
						}
						// Add more breakpoints as needed
					}
        });
	});
    </script>
	<?php
 	}
	?>
	<script>
		jQuery(document).ready(function ($) {
				$('#searchform_by_tag').submit(function (e) {
					e.preventDefault();
					var tagSearch = $('#tag-search').val();

					if($.trim(tagSearch) === ''){
						return
					}

					$('.article_list_page').hide();
					$('.pagination').hide();
					$('.spinner_loading').show();
					

					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'custom_ajax_search_bsg_post',
							tag_search: tagSearch,
						},
						success: function (response) {
							if (response.success) {
								$('.spinner_loading').hide();
								$('#search-results').show();
								$('#search-results').html(response.data);
								$('#clear_search').show();
							} else {
								$('.spinner_loading').hide();
								$('#search-results').show();
								// Handle error, for example, display a message
								$('#search-results').html('<center><h4>No data found</h4></center>');
								$('#clear_search').show();
							}
						},
						error: function (jqXHR, textStatus, errorThrown) {
							$('.spinner_loading').hide();
							$('#search-results').show();
							$('#search-results').html('<p>Error fetching data</p>');
							$('#clear_search').show();
						}
					});
			return false;
			});


			$('#clear_search').click(function(){
				$('#tag-search').val('');
				$('#search-results').hide();
				$('.article_list_page').show();
				$('.pagination').show();
				$(this).hide();
			})
		});

	</script>

<script>
		jQuery(document).ready(function ($) {
    $('#searchform_by_investor_name').submit(function (e) {
        e.preventDefault();
        var investor_search = $('#investorname-search').val();

        if ($.trim(investor_search) === '') {
            return;
        }

        $('.hide_investors_lists').hide();
        $('#investor_search_result').hide();
        $('.spinner_loading_investor').show();
        

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'search_investor_bsds',
                investor_search: investor_search,
            },
            success: function (response) {
				// console.log(response.data.html)
                if (response.success) {
                    console.log(response);
					$('.spinner_loading_investor').hide();
					$('#investor_search_result').show();
                    $('#investor_search_result').html(response.data.html);
					$('#clear_invest_search').show();
                } else {
                    $('.spinner_loading_investor').hide();
					$('#investor_search_result').show();
                    $('#investor_search_result').html('<center><h4>No data found</h4></center>');
					$('#clear_invest_search').show();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('.spinner_loading_investor').hide();
                $('#investor_search_result').html('<p>Error fetching data</p>');
				$('#clear_invest_search').show();
            }
        });
        return false;
    });
	$('#clear_invest_search').click(function(){
			$('#investorname-search,#ticker-search').val('');
			$('#investor_search_result,#elevetor_search_result').hide();
			$('.hide_investors_lists').show();
			$('#elevetor_result_spinner').hide();
			$(this).hide();
		})
});



	</script>



<script>
	jQuery(document).ready(function ($) {
    $('#searchform_by_insight_name').submit(function (e) {
        e.preventDefault();
        var insight_search = $('#insight_search').val();

        if ($.trim(insight_search) == '') {
            return;
        }
		$('.search_category_insight').hide();
		$('.child_category_insight').hide();
		$('.result_loader').show();

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'search_insights',
                insight_search: insight_search,
            },
            success: function (response) {
				// console.log(response.data.html);
                if (response.success) {
					$('.result_loader').hide();
					$('.search_category_insight').html(response.data.html);
					$('.search_category_insight').show();
					$('.custom_clear').show();
                } else {
					$('.result_loader').hide();
                    $('.search_category_insight').html('<center><h4>No insights found</h4></center>');
					$('.search_category_insight').show();
					$('.custom_clear').show();
                }
            }
            
        });

        return false;
    });

	$('.custom_clear').click(function(){
			$('#insight_search').val('');
			$('.search_category_insight').hide();
			$('.child_category_insight').show();
			$(this).hide();
		})
});
</script>



<script>
	jQuery(document).ready(function ($) {
    $('#searchform_by_ticker_name').submit(function (e) {
        e.preventDefault();
        var ticker_search = $('#ticker-search').val();

        if ($.trim(ticker_search) == '') {
            return;
        }

		$('#elevetor_result_spinner,.spinner_loading_investor').show();
		//$('#featured_elevator').hide();
		$('#elevetor_search_result').hide();
		// $('.child_category_insight').hide();
		// $('.result_loader').show();

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'redirect_to_callback_elevator_pitches_posts',
                ticker_search: ticker_search,
            },
			success: function (response) {
            if (response.data.status == 'success') {
				$('#elevetor_search_result').html('<center><h4>Redirecting you to '+ticker_search+'...</h4></center>');
				$('.spinner_loading_investor').hide();
				$('#elevetor_search_result').show();
				$('#clear_invest_search').show();
                // If the response indicates success and contains the URL
                var redirectUrl = response.data.url;
                window.location.href = redirectUrl; // Redirect to the URL
            } else {
                // Handle error or no response scenario
                $('#elevetor_search_result').html(response.data.message);
					$('.spinner_loading_investor').hide();
					$('#elevetor_search_result').show();
					$('#clear_invest_search').show();
            }
			}
            
        });

        return false;
    });

	// $('.custom_clear').click(function(){
	// 		$('#insight_search').val('');
	// 		$('.search_category_insight').hide();
	// 		$('.child_category_insight').show();
	// 		$(this).hide();
	// 	})
});
</script>
<script>
	jQuery(document).ready(function ($) {
		$('#go_back, .go-back-top, .go-back-top_below').click(function(e){
			e.preventDefault();
			window.history.go(-1);
		})
	});
</script>

<script>
	jQuery(document).ready(function($) {

		var loadMoreButton = $("#load_more_weekly_posts");
		// Count the number of elements with class .post-item
		var postItemCount = $('#weekly_news_letter_posts .post-item').length;
		loadMoreButton.attr('data-offset', postItemCount);

		loadMoreButton.click(function (e) {
	
		$(this).text("Loading...");
		$(this).css("cursor", "wait");
		
        e.preventDefault();
        var offset_value =  $(this).attr("data-offset");
        
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'callback_home_page_blog_posts',
                offset_value: offset_value,
            },
            success: function (response) {
                if (response) {
					$(loadMoreButton).text("Load More");
					$(loadMoreButton).css("cursor", "pointer");

					var lastWeeklyPosts = $('.news_letter_posts .post-item:last');
					// Append the response after the last occurrence of #weekly_news_letter_posts
					lastWeeklyPosts.after(response);
					var postItemCount = $('#weekly_news_letter_posts .post-item').length;
					loadMoreButton.attr('data-offset', postItemCount);

					var totalWeeklyPosts = parseInt(loadMoreButton.attr("total_weekly_posts"));
					var offsetValue = parseInt(loadMoreButton.attr("data-offset"));
					
					if (totalWeeklyPosts === offsetValue) {
						loadMoreButton.prop("disabled", true);
					}
                } 
            }
            
        });

        return false;
    });
	});

	jQuery(document).ready(function() {
		jQuery('.elementor-menu-toggle').click(function() {
        // Get the actual viewport width
        var viewportWidth = $(window).width();

        // Calculate the dynamic left value as a negative fraction of the viewport width
        var leftValue = -viewportWidth * 0.84; // Adjust the factor as needed

        // Add the .elementor-active class
        jQuery(this).toggleClass('elementor-active');

        // Apply the inline styles
        jQuery('.elementor-nav-menu--dropdown').css({
            'left': leftValue + 'px',
            'width': viewportWidth + 'px',
			'top': '33px'
        });
    });
});
</script>

 <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.5.1/moment.min.js"></script>

<?php
// Check if the 'welcome' parameter exists in the query string
if (isset($_GET['account'])) {
	if($_GET['account'] == 'activate'){
     ?>

<script type="text/javascript">
	$(document).ready(function() { 
      $('#popUpForm').fadeIn(1000); 
      $('#popUpForm').show(); 
	// $('#custom-letter-date .elementor-button-text')
   });
$( "#close" ).click(function() {
  $( "#popUpForm" ).css("display", "none");
});

</script>

<?php
	}
}  
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.custom-letter-date .elementor-button-text').each(function() {
			var timestamp = $(this).text();
			 var dateStr = timestamp; // or "07-31-2024"
			    var parts = dateStr.split(/[-\/]/); // Split by either "-" or "/"
			    var finalDate = parts[1] + "-" + parts[0] + "-" + parts[2];
			console.log(timestamp, 'ddd');
			if (timestamp != '') {
				var formattedDate = moment(finalDate, 'DD-MM-YYYY').format('MMM DD, YYYY');
				console.log(formattedDate, 'eee');
				$(this).text(formattedDate);
			} else {
				$(this).text('');
			}
		});
	});
</script>

</body>
</html>

