// Add Event Listener to the Edit Actions Form Submit Button
var elements = document.getElementsByClassName("md-fund-subscribe-btn");
for (var i = 0; i < elements.length; i++) {
  elements[i].addEventListener('click', mdUpdateFundSubscription, false);
}



function mdUpdateFundSubscription(){
  var btn_id = this.id;
  const the_btn = document.getElementById(btn_id);
  
  // Get the Fund Id from the buttons data attribute 
    var fund_id = the_btn.dataset.fundId;
    var letter_id = the_btn.dataset.letterId;
    

  jQuery.ajax({
    //url: "http://" + window.location.hostname + "/md_Buyside_Monitor/wp-admin/admin-ajax.php",
    url: "https://" + window.location.hostname + "/wp-admin/admin-ajax.php",
    type:'post',
    dataType:"json",
    data:{
      action:'md_ajax_fund_subscribe',
      fund_id: fund_id,
      letter_id: letter_id,
    },
    success: function(response) {
      console.log(response)
      if(response.result = "Subscribe Success"){
        // Check if this is a button in the Fund Table 
          if(the_btn.classList.contains("md-fund-table-subscribe-btn")){

            // Was the fund previously subscribed too or unsubscribed 
              if(the_btn.classList.contains("md-fund-subscribe-btn--subscribed")){
                var previously_subscribed = true;
              } else {
                var previously_subscribed = false;
              }

            // Loop through all of the Buttons in the table and check which ones have an attribute of this fund_id 
              var tracking_btns = document.getElementsByClassName("md-fund-table-subscribe-btn");
              Array.prototype.forEach.call(tracking_btns, function(tracking_btn) {       
                if(tracking_btn.dataset.fundId == fund_id){
                  // Change the button
                    if(previously_subscribed == true){
                      tracking_btn.classList.remove("md-fund-subscribe-btn--subscribed");
                      tracking_btn.classList.add("md-fund-subscribe-btn--not-subscribed");
                      tracking_btn.innerHTML = "Follow";
                    } else {
                      tracking_btn.classList.remove("md-fund-subscribe-btn--not-subscribed");
                      tracking_btn.classList.add("md-fund-subscribe-btn--subscribed");
                      tracking_btn.innerHTML = "Unfollow";
                    }
                }
              });
          } else if(the_btn.classList.contains("md-users-favourites-fund-unsubscribe-btn")){

            // Remove the fund
              var elem_to_remove = document.getElementById("md-recommended-fund-id-" + fund_id);
              elem_to_remove.parentNode.removeChild(elem_to_remove);

              
          } else {
            // Check if the user has just subscribed or unsubscribed 
              if (the_btn.classList.contains("md-fund-subscribe-btn--subscribed")){

                the_btn.classList.remove("md-fund-subscribe-btn--subscribed");
                the_btn.classList.add("md-fund-subscribe-btn--not-subscribed");
                
                if(the_btn.classList.contains("welcomeboard")){
                  
                  the_btn.innerHTML = "Follow"; 
                }else{
                  the_btn.innerHTML = "Follow this Fund";
                }
              } else if (the_btn.classList.contains("md-fund-subscribe-btn--not-subscribed")){
                the_btn.classList.remove("md-fund-subscribe-btn--not-subscribed");
                the_btn.classList.add("md-fund-subscribe-btn--subscribed");
                if(the_btn.classList.contains("welcomeboard")){
                  const dataLink = the_btn.dataset.link;
                  the_btn.innerHTML = "Unfollow"; 
                  // Log the value to the console
                  //console.log(dataLink);
                  //window.location.href = dataLink;
                }else{
                  the_btn.innerHTML = "Unfollow this Fund";
                }
                
              }
          }
      }
    }
  });
};

  // Search Table 
  jQuery(document).ready(function() {
      if(document.getElementById("md-fund-letter-table")){


        // Add event listener #for md-fund-table-more-btn 
          var fund_table_more_btn = document.getElementById("md-fund-table-more-btn");
          // alert(fund_table_more_btn);
          fund_table_more_btn.addEventListener('click', mdShowMoreTableRows, false);

          function mdShowMoreTableRows(){
            // Check if there are any hidden rows
              var fund_table_hidden_rows = document.getElementsByClassName('md-hide-row');

            // Get the total number of rows 
              var total_rows_count = document.getElementById('md-total-count').innerHTML;

            // Current Count 
              var current_shown_rows_count = document.getElementById('md-current-count').innerHTML;
              var rows_to_show = parseInt(current_shown_rows_count) + 31;

              if(rows_to_show > total_rows_count){
                rows_to_show = total_rows_count;
                // disable the show more button
                  document.getElementById('md-fund-table-more-btn').disabled = true;
              }


            // Get all the rows
              var fund_table_rows = document.getElementsByClassName('md-fund-table-row');

            // Loop through the rows and show the next 30

              console.log("rows_to_show: " + rows_to_show);

              for (i = 0; i < rows_to_show; i++) {
                fund_table_rows[i].classList.remove("md-hide-row");
              } 

            // Update the row counts
              var current_count_span = document.getElementById("md-current-count");
              var current_count = current_count_span.innerHTML;   

              var difference_between_current_and_total = (parseInt(total_rows_count) - parseInt(current_count));
              
              if( difference_between_current_and_total > 30 ){
                var next_count = parseInt(current_count) + 30;
              } else {
                var next_count = total_rows_count;
              }
                
              document.getElementById("md-current-count").innerHTML = next_count;

          }






        // Add Event Listener to show the extra Tickers
          var tickerButtons = document.getElementsByClassName("md-ticker-wrap-more-btn");
          for (var i = 0; i < tickerButtons.length; i++) {
            tickerButtons[i].addEventListener('click', mdToggleTickerButtons, false);
          }

          function mdToggleTickerButtons(){
            var btn_id = this.id;
            
            var more_less_btn = document.getElementById(btn_id);

            var ticker_row_wrap_id = btn_id.replace("md-ticker-wrap-more-", "md-ticker-row-wrap-");
            
            if(more_less_btn.innerHTML == "&#8230more"){
              // Get the ticker-row-wrap 
                var ticker_row_wrap = document.getElementById(ticker_row_wrap_id);

              // Make the extra tickers visible 
                var ticker_children = ticker_row_wrap.children;
                for(var i = 0; i < ticker_children.length; i++) {
                  if(ticker_children[i].classList.contains("md-d-none")){
                    ticker_children[i].classList.add("md-d-show");
                  }
                }
              // Change the button text
                more_less_btn.innerHTML = "&#8230less";
                
            
            } else if (more_less_btn.innerHTML == "&#8230less"){
              // Get the ticker-row-wrap 
                var ticker_row_wrap = document.getElementById(ticker_row_wrap_id);

              // Make the extra tickers visible 
                var ticker_children = ticker_row_wrap.children;
                for(var i = 0; i < ticker_children.length; i++) {
                  if(i > 5){
                    ticker_children[i].classList.remove("md-d-show");
                  }
                }
              
              // Change the button text
                more_less_btn.innerHTML = "&#8230more";
            }
          };
    };
  });





  





  // Add Event Listener to the Edit Actions Form Submit Button
    var elements = document.getElementsByClassName("md-ticker-subscribe-btn");
    for (var i = 0; i < elements.length; i++) {
      elements[i].addEventListener('click', mdUpdateTickerSubscription, false);
    }



    function mdUpdateTickerSubscription(){
      var btn_id = this.id;
      var ticker_id = btn_id.replace("md_ticker_subscribe_btn_id_", "");
      jQuery.ajax({
        //url: "http://" + window.location.hostname + "/md_Buyside_Monitor/wp-admin/admin-ajax.php",
        url: "https://" + window.location.hostname + "/wp-admin/admin-ajax.php",
        type:'post',
        dataType:"json",
        data:{
          action:'md_ajax_ticker_subscribe',
          ticker_id: ticker_id,
        },
        success: function(response) {
          if(response.result = "Subscribe Success"){
            var the_btn = document.getElementById(btn_id);
            
            if(the_btn.classList.contains("md-users-favourites-ticker-unsubscribe-btn")){
                // Remove the fund
                  var elem_to_remove = document.getElementById("md-recommended-ticker-id-" + ticker_id);
                  elem_to_remove.parentNode.removeChild(elem_to_remove);
                  
            } else {
              if (the_btn.classList.contains("md-ticker-subscribe-btn--subscribed")){
                the_btn.classList.remove("md-ticker-subscribe-btn--subscribed");
                the_btn.classList.add("md-ticker-subscribe-btn--not-subscribed");
                the_btn.innerHTML = "Follow Ticker";
              } else if (the_btn.classList.contains("md-ticker-subscribe-btn--not-subscribed")){
                the_btn.classList.remove("md-ticker-subscribe-btn--not-subscribed");
                the_btn.classList.add("md-ticker-subscribe-btn--subscribed");
                the_btn.innerHTML = "Unfollow Ticker";
              }
              document.getElementById(btn_id).blur()
            }
            
          }
        }
      });
    };





//  Slider 

  // Slider Ticker Wrap More Button 
    if(document.getElementsByClassName('md-slider-wrap')){
      // Add Event Listener to show the extra Tickers
        var tickerButtons = document.getElementsByClassName("md-ticker-wrap-more-btn");
        for (var i = 0; i < tickerButtons.length; i++) {
          tickerButtons[i].addEventListener('click', mdToggleTickerButtons, false);
        }

        function mdToggleTickerButtons(){
          var btn_id = this.id;
          
          var more_less_btn = document.getElementById(btn_id);

          var ticker_row_wrap_id = btn_id.replace("md-ticker-wrap-more-", "md-ticker-row-wrap-");
            
          if(more_less_btn.innerHTML == "...more"){
            // Get the ticker-row-wrap 
              var ticker_row_wrap = document.getElementById(ticker_row_wrap_id);

            // Make the extra tickers visible 
              var ticker_children = ticker_row_wrap.children;
              for(var i = 0; i < ticker_children.length; i++) {
                if(ticker_children[i].classList.contains("md-d-none")){
                  ticker_children[i].classList.remove("md-d-none");
                }
              }
      
            // Change the button text
                more_less_btn.innerHTML = "...less";
            
          } else if (more_less_btn.innerHTML == "...less"){
            // Get the ticker-row-wrap 
              var ticker_row_wrap = document.getElementById(ticker_row_wrap_id);

            // Make the extra tickers visible 
              var ticker_children = ticker_row_wrap.children;
              for(var i = 0; i < ticker_children.length; i++) {
                if( (i > 5) && (ticker_children[i].classList.contains("md-ticker-span")) ){
                  ticker_children[i].classList.add("md-d-none");
                }
              }
              
            // Change the button text
              more_less_btn.innerHTML = "...more";
            }
          };
    }




  // Sliders 
    if(document.getElementsByClassName('md-slider-wrap')){
      
      // Run the slider setup for the first time 
        call_slider_setup();

      // Add an event listener to detect window width change
        window.addEventListener("resize", detect_window_width_change);


      // Function to run if window size changes
        function detect_window_width_change(){
          // add a pause to allow user to finish changing the screen size
            setTimeout(call_slider_setup,500);
        }


      // function to start setting up sliders 
        function call_slider_setup(){
          els = document.getElementsByClassName('md-slider-wrap')
          Array.prototype.forEach.call(els, function(el) {
            md_setup_slider(el.id);
          })
        }


      // Function to set up any sliders
        function md_setup_slider(slider_id){
          // Get the variables
            var content = document.body;
            var content_width = content.offsetWidth;


            var container = document.getElementById(slider_id + '_slider_container');
            var slider = document.getElementById(slider_id + '_slider');
            var slides = slider.getElementsByClassName('md-slide');
            var buttons_wrap  = document.getElementById(slider_id + '_slider_button_wrap');
            var slide_width = 350;



          // Set the width of the container 
            if(content_width < 1000){
              // Show only one card 
                var number_of_cards = 1;
                var slider_container_width = 370;
            } else if ( (content_width > 999) && (content_width < 1200) ) {
              // Show two cards 
                var number_of_cards = 2;
                var slider_container_width = 740;        
            } else {
              // Show three cards 
                var number_of_cards = 3;
                var slider_container_width = 1110;
            }

          // Add an attribute to the slider to hold the max_number of cards 
            slider.dataset.maxcards = number_of_cards;

            container.style.width = slider_container_width + "px";


          // Set the slide widths and margins
            var slide_widths_total = slide_width * number_of_cards;
            var slide_margins_total = slider_container_width - slide_widths_total;
            var slide_margin = 20;

            for (var i = 0; i < slides.length; i++) {
              if(i == 0){
                slides[i].classList.add("md-slide-active");
              }
              slides[i].style.width = slide_width + "px";
              slides[i].style.minWidth = slide_width + "px";
              slides[i].style.marginRight = slide_margin + "px";
            }


          // Add the control buttons
            // Check if the buttons have already been created 
              var buttons_check = buttons_wrap.getElementsByClassName("md-slider-slide-btn");
              if(buttons_check.length == 0){
                for (var i = 0; i < slides.length; i++) {
                  var new_button = document.createElement("button");
                  new_button.id = slider_id + "_slider-slide-btn-" + i;
                  
                  new_button.className = "md-slider-slide-btn " + slider_id + "_slide_btn";
                  buttons_wrap.appendChild(new_button);
                }

                // add an eventlistener for the control buttons       
                  var slide_buttons = buttons_wrap.getElementsByClassName("md-slider-slide-btn");
                  for (var i = 0; i < slide_buttons.length; i++) {
                    // Add the active class to the first button
                      if(i == 0){
                        slide_buttons[0].classList.add("md-slider-slide-btn-active");
                      }
                      slide_buttons[i].addEventListener('click', move_button, false);
                  }
              }
        
        };






      // Move the slides 
        function move_button(evt) {

          // Get the button ID 
            var this_btn_id = this.id;
          
            var slide_number_array = this_btn_id.split("btn-");
            var slide_number = slide_number_array[1]; 

            const slider_btn_array = this_btn_id.split("-");
            slider_id = slider_btn_array[0]; 

          // Get the slider id
            slider = document.getElementById(slider_id);

          // Get the max number of cards to display
            var max_cards = slider.dataset.maxcards;


          // Get the button wrap id 
            button_wrap_id = slider_btn_array[0] + "_button_wrap";
            button_wrap = document.getElementById(button_wrap_id);

          // Get the active button id 
            var control_buttons = button_wrap.getElementsByClassName("md-slider-slide-btn-active");
            var active_control_button = control_buttons[0];
            var active_slide_number = active_control_button.id.replace("md-slider-slide-btn-", "");


          // Count the number of slides (it will be the same as the number of buttons)
            var slides_list = button_wrap.getElementsByClassName("md-slider-slide-btn");
            var number_of_slides = slides_list.length;


          // If moving slides will create an empty space then don't move the slides 
            var num_of_slides_left_to_show = number_of_slides - slide_number;
            

          // Get the current container left-margin 
            const cssObj = window.getComputedStyle(slider, null);
            let current_left_margin = cssObj.getPropertyValue("margin-left");
                  
            var slide_width = 350;
            var slide_margin = slider.getElementsByClassName("md-slide")[0].style.marginRight;
            var slide_margin = slide_margin.replace("px","");

          // Left margin of #slider (controls left and right scroll)
            var slider_left_margin = parseFloat(slide_width) + parseFloat(slide_margin);
                          
              if(max_cards == 3){
                var  stop_at_card_number = parseFloat(number_of_slides) - 3;
              } else if( max_cards == 2){
                var stop_at_card_number = parseFloat(number_of_slides) - 2;
              } else {
                var stop_at_card_number = parseFloat(number_of_slides) + 3;
              }

              // Need to avoid leaving emptly space  
                if(slide_number >= stop_at_card_number){    
                  new_slider_left_margin =  slider_left_margin * stop_at_card_number;
                } else {
                  new_slider_left_margin =  slider_left_margin * slide_number;
                }

            new_slider_left_margin = "-" + new_slider_left_margin;

          // Change the left-margin 
            var new_left_margin = new_slider_left_margin + "px";
            slider.style.marginLeft = new_left_margin;
    
          // Change the Active Slide
            var the_slides = slider.getElementsByClassName("md-slide");
            for (var i = 0; i < the_slides.length; i++) {

              // Remove the active class from the slides
                if(the_slides[i].classList.contains("md-slide-active")){
                  the_slides[i].classList.remove("md-slide-active");
                }

                if(i == slide_number){
                  the_slides[i].classList.add("md-slide-active");
                }
            }

          // Change the active button 
             document.getElementById(active_control_button.id).classList.remove("md-slider-slide-btn-active");
             document.getElementById(this_btn_id).classList.add("md-slider-slide-btn-active");

          // Check if we need to disable either of the next / previous buttons 
            // Previous Button

              

              var prev_btn_id = slider_id.replace("_slider","");
              prev_btn_id = prev_btn_id + "_slide_prev_btn";
              
              var next_btn_id = slider_id.replace("_slider","");
              next_btn_id = next_btn_id + "_slide_next_btn";

              if(slide_number == 0){
                document.getElementById(prev_btn_id).disabled=true; // Not working!?
                document.getElementById(prev_btn_id).setAttribute('disabled', 'disabled'); // Not working!?
                document.getElementById(prev_btn_id).classList.add("md-disabled");
              } else {
                document.getElementById(prev_btn_id).removeAttribute('disabled');
                document.getElementById(prev_btn_id).classList.remove("md-disabled");
              }


            // Next Button
              if(slide_number == the_slides.length - 1){
                document.getElementById(next_btn_id).disabled=true; // Not working!?
                document.getElementById(next_btn_id).setAttribute('disabled', 'disabled'); // Not working!?
                document.getElementById(next_btn_id).classList.add("md-disabled");
              } else {
                document.getElementById(next_btn_id).removeAttribute('disabled');
                document.getElementById(next_btn_id).classList.remove("md-disabled");
              }
        };
  





      // Previous and Next Buttons 
        var prev_next_buttons = document.getElementsByClassName("md-prev-next-btn");
        for (var i = 0; i < prev_next_buttons.length; i++) {
          // Add event listener
            prev_next_buttons[i].addEventListener('click', move_button_from_prev_next_btn, false);
        }





      // Move the slides from Prev / Next Buttons
        function move_button_from_prev_next_btn(evt) {

          // Get the button ID 
            var this_btn_id = this.id;

          // Get the slider ID from the prev / next button
            if(document.getElementById(this_btn_id).classList.contains("md-prev-slide-btn")){
              var slider_id = this_btn_id.replace("md_", "");
              slider_id = this_btn_id.replace("_slide_prev_btn", "");
              var direction = "prev";
            } else {
              var slider_id = this_btn_id.replace("md_", "");
              slider_id = this_btn_id.replace("_slide_next_btn", "");
              var direction = "next";
            }
            
          // Get the slider
            slider = document.getElementById(slider_id);   

          // Get the slider controls wrap 
            var control_button_wrap_id = slider_id + "_slider_button_wrap";
            var control_button_wrap = document.getElementById(control_button_wrap_id);


          // Get the active slide button
            var active_buttons = control_button_wrap.getElementsByClassName("md-slider-slide-btn-active");
            var active_button_id = active_buttons[0].id;

            var slide_number_array = active_button_id.split("btn-");
            var slide_number = slide_number_array[1]; 

            if(direction == "prev"){
              new_slide_number = parseInt(slide_number) - 1;
            } else {
              new_slide_number = parseInt(slide_number) + 1;
            }

          // Get the max number of cards to display
            var max_cards = slider.dataset.maxcards;
            var max_cards = 3;

            console.log(slider_id + " max_cards: " + max_cards);

          // Get the button wrap id 
            button_wrap = document.getElementById(control_button_wrap);


          // Count the number of slides (it will be the same as the number of buttons)
            var slides_list = control_button_wrap.getElementsByClassName("md-slider-slide-btn");
            var number_of_slides = slides_list.length;


        
          // If moving slides will create an empty space then don't move the slides 
            var num_of_slides_left_to_show = number_of_slides - new_slide_number;
              


          // Get the current container left-margin 
            const cssObj = window.getComputedStyle(slider, null);
            let current_left_margin = cssObj.getPropertyValue("margin-left");
                    
            var slide_width = 350;
            var slide_margin = slider.getElementsByClassName("md-slide")[0].style.marginRight;
            var slide_margin = slide_margin.replace("px","");

          // Left margin of #slider (controls left and right scroll)
            var slider_left_margin = parseFloat(slide_width) + parseFloat(slide_margin);
                            
            if(max_cards == 3){
              var  stop_at_card_number = parseFloat(number_of_slides) - 3;
            } else if( max_cards == 2){
              var stop_at_card_number = parseFloat(number_of_slides) - 2;
            } else {
              var stop_at_card_number = parseFloat(number_of_slides) + 3;
            }

          // Need to avoid leaving emptly space  
            if(new_slide_number >= stop_at_card_number){    
              new_slider_left_margin =  slider_left_margin * stop_at_card_number;
            } else {
              new_slider_left_margin =  slider_left_margin * new_slide_number;
            }

            new_slider_left_margin = "-" + new_slider_left_margin;

          // Change the left-margin 
            var new_left_margin = new_slider_left_margin + "px";
            slider_container_id = slider_id + "_slider";
            
            document.getElementById(slider_container_id).style.marginLeft = new_left_margin;
        
          // Change the Active Slide
            var the_slides = slider.getElementsByClassName("md-slide");
            for (var i = 0; i < the_slides.length; i++) {

                // Remove the active class from the slides
                  if(the_slides[i].classList.contains("md-slide-active")){
                    the_slides[i].classList.remove("md-slide-active");
                  }

                  if(i == new_slide_number){
                    the_slides[i].classList.add("md-slide-active");
                  }
              }

            // Change the active button 
              var active_control_btn_id = slider_container_id + "-slide-btn-" + slide_number;
              var new_active_control_btn_id = slider_container_id + "-slide-btn-" + new_slide_number;

              document.getElementById(active_control_btn_id).classList.remove("md-slider-slide-btn-active");
              document.getElementById(new_active_control_btn_id).classList.add("md-slider-slide-btn-active");
          
            // Check if we need to disable either of the next / previous buttons 
              // Previous Button
                if(new_slide_number == 0){
                  document.getElementById(slider_id + "_slide_prev_btn").disabled=true; // Not working!?
                  document.getElementById(slider_id + "_slide_prev_btn").setAttribute('disabled', 'disabled'); // Not working!?
                  document.getElementById(slider_id + "_slide_prev_btn").classList.add("md-disabled");
                } else {
                  document.getElementById(slider_id + "_slide_prev_btn").removeAttribute('disabled');
                  document.getElementById(slider_id + "_slide_prev_btn").classList.remove("md-disabled");
                }

              // Next Button
                if(new_slide_number == the_slides.length - 1){
                  document.getElementById(slider_id + "_slide_next_btn").disabled=true; // Not working!?
                  document.getElementById(slider_id + "_slide_next_btn").setAttribute('disabled', 'disabled'); // Not working!?
                  document.getElementById(slider_id + "_slide_next_btn").classList.add("md-disabled");
                } else {
                  document.getElementById(slider_id + "_slide_next_btn").removeAttribute('disabled');
                  document.getElementById(slider_id + "_slide_next_btn").classList.remove("md-disabled");
                }

          };



  };