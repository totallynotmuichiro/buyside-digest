<?php

/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section, opens the <body> tag and adds the site's header.
 *
 * @package HelloElementor
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

$viewport_content = apply_filters('hello_elementor_viewport_content', 'width=device-width, initial-scale=1');
$enable_skip_link = apply_filters('hello_elementor_enable_skip_link', true);
$skip_link_url = apply_filters('hello_elementor_skip_link_url', '#content');
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="<?php echo esc_attr($viewport_content); ?>">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <!-- <script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/db5fab14888de1bd0ae4cb3c9/96f1769929aefaa14af988672.js");</script> -->
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

  <?php wp_body_open(); ?>

  <?php if (is_page('landing')) : ?>
    <style>
      /* Keyframes cannot be applied inline, so we include them here */
      @keyframes marquee {
        0% {
          transform: translateX(0);
        }

        100% {
          transform: translateX(-50%);
        }
      }
    </style>
    <div id="marquee-container" style="overflow: hidden; white-space: nowrap; background-color: #0D3E6F; color: #ffffff; padding: 11px 0; position: relative;">
      <div id="marquee" style="display: flex; animation: marquee 12s linear infinite;">
        <!-- First copy of dynamic content -->
        <div class="marquee-group" id="group1" style="display: flex;"></div>
        <!-- Duplicate content for a continuous effect -->
        <div class="marquee-group" id="group2" style="display: flex;"></div>
      </div>
    </div>

    <script>
      // Replace with your actual API endpoint
      const apiUrl = 'https://sectobsddjango-production.up.railway.app/api/investors-crousal/';

      // Function to create an anchor element for a given investor object.
      function createLinkElement(investor) {
        const a = document.createElement('a');
        a.href = investor.url;
        a.textContent = investor.name;
        a.target = '_blank';

        // Inline styles for the anchor
        a.style.color = '#ffffff';
        a.style.textDecoration = 'none';
        a.style.margin = '0 2rem';
        a.style.fontSize = '14px';
        a.style.lineHeight = '1';

        // Add event listeners for hover effect: add border-bottom on hover with a light white color
        a.addEventListener('mouseover', function() {
          this.style.borderBottom = '1.4px solid rgba(255,255,255,0.8)';
        });
        a.addEventListener('mouseout', function() {
          this.style.borderBottom = 'none';
        });

        return a;
      }

      // Fetch the data from the API using GET
      fetch(apiUrl)
        .then(response => response.json())
        .then(data => {
          const group1 = document.getElementById('group1');
          // Iterate over the investors array and append each link to group1
          data.investors.forEach(investor => {
            const a = createLinkElement(investor);
            group1.appendChild(a);
          });
          // Duplicate the content in group2 to ensure a continuous scrolling effect
          const group2 = document.getElementById('group2');
          group2.innerHTML = group1.innerHTML;
        })
        .catch(error => {
          console.error('Error fetching data:', error);
        });
    </script>
  <?php endif; ?>

  <?php if ($enable_skip_link) { ?>
    <a class="skip-link screen-reader-text" href="<?php echo esc_url($skip_link_url); ?>"><?php echo esc_html__('Skip to content', 'hello-elementor'); ?></a>
  <?php } ?>

  <?php
  if (! function_exists('elementor_theme_do_location') || ! elementor_theme_do_location('header')) {
    if (did_action('elementor/loaded') && hello_header_footer_experiment_active()) {
      get_template_part('template-parts/dynamic-header');
    } else {
      get_template_part('template-parts/header');
    }
  }
