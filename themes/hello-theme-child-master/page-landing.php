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
      <?php get_template_part('template-parts/landing/landing', 'latest-letter'); ?>
      <?php get_template_part('template-parts/landing/landing', 'recommended-letter'); ?>
    </section>
    <aside class=" lg:w-[30%] flex flex-col">
      <?php get_template_part('template-parts/landing/landing', 'popular-tools'); ?>
      <?php get_template_part('template-parts/landing/landing', 'watchlist'); ?>
      <?php get_template_part('template-parts/landing/landing', 'elevator-pitches'); ?>
      <?php get_template_part('template-parts/landing/landing', 'stock-list'); ?>
    </aside>
  </div>
  <div style="padding:4rem 2.5rem" class="py-10 px-5 md:px-20 bg-white">
    <?php get_template_part('template-parts/landing/landing', 'articles'); ?>
    <?php get_template_part('template-parts/landing/landing', 'carousal'); ?>
  </div>
</main>
<?php get_template_part('template-parts/landing/landing', 'cta-2'); ?>
<?php get_template_part('template-parts/landing/landing', 'weekly-articles'); ?>
<?php get_footer(); ?>