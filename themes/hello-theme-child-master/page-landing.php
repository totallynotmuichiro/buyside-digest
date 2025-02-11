<?php
/*
Template Name: Landing
*/

get_header();
?>
<main class="bsd-container">
    <div class="flex flex-col lg:flex-row gap-5 mx-5 md:mx-12">
        <section class="w-full lg:w-[77%]">
            <?php get_template_part('template-parts/landing/landing', 'hero'); ?>
            <?php get_template_part('template-parts/landing/landing', 'blog'); ?>
            <?php get_template_part('template-parts/landing/landing', 'news'); ?>
            <?php get_template_part('template-parts/landing/landing', 'cta-1'); ?>
            <?php get_template_part('template-parts/landing/landing', 'articles'); ?>
        </section>
        <aside class="w-full lg:w-[23%] flex flex-col">
            <?php get_template_part('template-parts/landing/landing', 'watchlist'); ?>
            <?php get_template_part('template-parts/landing/landing', 'popular-tools'); ?>
            <?php get_template_part('template-parts/landing/landing', 'elevator-pitches'); ?>
        </aside>
    </div>
</main>

<?php get_footer(); ?>