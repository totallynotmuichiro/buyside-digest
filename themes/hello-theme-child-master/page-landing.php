<?php
/*
Template Name: Landing
*/

get_header();
?>
<main class="bsd-container">
    <?php get_template_part('template-parts/landing/landing', 'hero'); ?>
    <div class="flex flex-col lg:flex-row gap-5 mx-5 md:mx-12">
        <section class="w-full lg:w-[77%]">
            <?php get_template_part('template-parts/landing/landing', 'blog'); ?>
            <?php get_template_part('template-parts/landing/landing', 'news'); ?>
            <?php get_template_part('template-parts/landing/landing', 'cta-1'); ?>
        </section>
        <aside class="w-full lg:w-[23%] flex flex-col">
            <?php get_template_part('template-parts/landing/landing', 'popular-tools'); ?>
            <?php get_template_part('template-parts/landing/landing', 'watchlist'); ?>
            <?php get_template_part('template-parts/landing/landing', 'elevator-pitches'); ?>
        </aside>
    </div>
    <div class="mx-5 md:mx-12">
        <?php get_template_part('template-parts/landing/landing', 'latest-letter'); ?>
        <?php get_template_part('template-parts/landing/landing', 'recommended-funds'); ?>
        <?php get_template_part('template-parts/landing/landing', 'articles'); ?>
        <?php get_template_part('template-parts/landing/landing', 'substack'); ?>
        <?php get_template_part('template-parts/landing/landing', 'carousal'); ?>
    </div>
</main>
<?php get_template_part('template-parts/landing/landing', 'cta-2'); ?>
<?php get_template_part('template-parts/landing/landing', 'weekly-articles'); ?>
<?php get_footer(); ?>