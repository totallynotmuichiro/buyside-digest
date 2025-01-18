<?php

/**
 * @package Melt_Design_Buyside
 * @version 2.0.1
*/

/*
Plugin Name: Melt Design Buyside Letters
Plugin URI: https://meltdesign.co.uk/
Description: Plugin to create Letter Emails for Buyside
Author: Darren Parlett
Version: 2.0.1
*/

// Add the Functions File for the Plugin
  require_once plugin_dir_path(__FILE__) . 'includes/mdBuysideFunctions.php';


// Add Scripts
  // Frontend Scripts
    function md_frontend_scripts() {
      wp_enqueue_style( 'mdBuysideFrontend.css', plugins_url('/css/mdBuysideFrontend.css?v=2.0.1', __FILE__) );
      wp_enqueue_script( 'mdBuysideFrontend.js', plugins_url() . '/mdBuyside/js/mdBuysideFrontend.js?zszs', array('jquery'), null, true );
    }
    add_action( 'wp_enqueue_scripts', 'md_frontend_scripts' );


  // Admin Scripts
    function md_admin_register_head() {
      $siteurl = get_option('siteurl');
      $cssUrl = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/mdBuysideAdmin.css?v=2.0.1';
          echo "<link rel='stylesheet' type='text/css' href='$cssUrl' />\n";
    }
    add_action('admin_head', 'md_admin_register_head');



  // Add Ajax for functions
    add_action('wp_ajax_md_ajax_fund_subscribe', 'md_ajax_fund_subscribe');
    add_action('wp_ajax_nopriv_md_ajax_fund_subscribe', 'md_ajax_fund_subscribe');
    add_action('wp_ajax_md_ajax_ticker_subscribe', 'md_ajax_ticker_subscribe');
    add_action('wp_ajax_nopriv_md_ajax_ticker_subscribe', 'md_ajax_ticker_subscribe');





  // Add Admin pages
    function add_admin_pages(){
      add_menu_page( 'MD Letters', 'MD Letters', 'manage_options', 'md-letters', 'letters_page', 'dashicons-tickets');
    }
    add_action('admin_menu', 'add_admin_pages');


  // Letters Page Function
    function letters_page(){
      echo "<div class='md-admin-wrap'>";
        echo "<h2>Buyside Emails</h2>";
        echo "<h3>Test email send</h3>";

        $users_array = md_test_create_email();
      echo "</div>";
    }




  // Add Cron job
    // register_activation_hook(__FILE__, 'md_Create_Email_Activation');
    // add_action('md_Create_Email_Hourly_Event', 'md_create_email');

    // function md_Create_Email_Activation() {
    //   wp_schedule_event( time(), 'hourly', 'md_Create_Email_Hourly_Event');
    // }


    /* TestCron Job */
      /*
      register_activation_hook(__FILE__, 'md_Hourly_Test_Cron_Job_Test_Activation');
      add_action('md_Hourly_Test_Cron_Job', 'md_test_cron_job');

      function md_Hourly_Test_Cron_Job_Test_Activation() {
        wp_schedule_event( time(), 'hourly', 'md_Hourly_Test_Cron_Job');
      }
      */


    /* Text Cron Job 2 */

    // Create Activation Hook for Alert Emails
      /*
      register_activation_hook(__FILE__, 'mdITU_Scheduled_Events');
      add_action('mdITU_Email_Event', 'md_test_cron_job_two');

      function mdITU_Scheduled_Events() {
        wp_schedule_event( time(), 'hourly', 'mdITU_Email_Event');
      }
      */
define( 'BSD_ROOT_URL',  plugin_dir_url( __FILE__ ) );
require_once( __DIR__ . '/includes/classes/class-plugin.php' );
require_once(  __DIR__. '/includes/utils/utils.php' );