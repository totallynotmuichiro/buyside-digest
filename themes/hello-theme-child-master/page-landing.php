<?php
/*
Template Name: Landing
*/

get_header();
?>
<main class="bsd-container" style="background: linear-gradient(to bottom, #FFFFFF, #DCF5FF, #FFFFFF, #DCF5FF, #FFFFFF, #DCF5FF, #FFFFFF);">
    <?php get_template_part('template-parts/landing/landing', 'hero'); ?>
    <div style="z-index: 1000000; margin-top:2rem; padding:4rem 2.5rem" class="w-full mx-auto relative z-[100000] flex justify-between flex-col lg:flex-row gap-5 py-10 px-5 md:px-20 bg-white mt-10">
        <section class=" lg:w-[62%]">
            <?php get_template_part('template-parts/landing/landing', 'blog'); ?>
			<?php get_template_part('template-parts/landing/landing', 'substack'); ?>
            <?php get_template_part('template-parts/landing/landing', 'news'); ?>
			<?php get_template_part('template-parts/landing/landing', 'cta-1'); ?>
        </section>
        <aside class=" lg:w-[30%] flex flex-col">
            <?php get_template_part('template-parts/landing/landing', 'popular-tools'); ?>
            <?php get_template_part('template-parts/landing/landing', 'watchlist'); ?>
            <?php get_template_part('template-parts/landing/landing', 'elevator-pitches'); ?>
			
			<!-- stock list -->
<div style="margin-top: 2rem;">
  <!-- Title -->
  <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 0.75rem;" class="border-b border-gray-500 pb-2">Stock Lists</h2>
  
  <!-- Flex container with inline styles for wrapping -->
  <div style="display: flex; flex-wrap: wrap; gap: 6px; width: 100%;">
    <!-- First pill with primary background and white text -->
    <a href="#" style="padding: 0.25rem 1rem; background-color: #0D3E6F; color: #fff; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;">
      5G
    </a>
    
    <!-- Other pills with default white background; change to primary on hover -->
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      Biotech
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      Blue Chip
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      FAANG
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      Gold
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      Large Cap
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      Marijuana
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      Micro Cap
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      Oil
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      REITs
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      Russell 2000
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      Small Cap
    </a>
    <a href="#" style="padding: 0.25rem 1rem; background-color: #fff; color: #4a5568; border: 1px solid #e2e8f0; border-radius: 9999px; font-size: 0.8rem; text-align: center; text-decoration: none;"
       onmouseover="this.style.backgroundColor='#0D3E6F'; this.style.color='#fff';"
       onmouseout="this.style.backgroundColor='#fff'; this.style.color='#4a5568';">
      Warren Buffett
    </a>
  </div>
</div>



        </aside>
    </div>
	
   <div class="mx-5 md:mx-12 py-10">  
        <div class="overflow-x-auto">
            <?php get_template_part('template-parts/landing/landing', 'latest-letter'); ?>
        </div>
        <div class="overflow-x-auto">
            <?php get_template_part('template-parts/landing/landing', 'recommended-letter'); ?>
        </div>
    <?php //get_template_part('template-parts/landing/landing', 'recommended-funds'); ?>
   </div>
	
	
	  <div style="padding:4rem 2.5rem" class="py-10 px-5 md:px-20 bg-white">
		 <?php get_template_part('template-parts/landing/landing', 'articles'); ?>
    <?php get_template_part('template-parts/landing/landing', 'carousal'); ?>
	   </div>

</main>
<?php get_template_part('template-parts/landing/landing', 'cta-2'); ?>
<?php get_template_part('template-parts/landing/landing', 'weekly-articles'); ?>
<?php get_footer(); ?>