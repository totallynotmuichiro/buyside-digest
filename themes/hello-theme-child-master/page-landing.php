    <?php
    /*
    Template Name: Landing
    */

    get_header();
    ?>
    <main class="bsd-container">
        <?php get_template_part('template-parts/landing/landing', 'hero'); ?>
        <?php get_template_part('template-parts/landing/landing', 'signup'); ?>
        <div class="flex flex-col md:flex-row gap-5 mx-5 md:mx-16">
            <section class="h-56 lg:flex-[10] bg-emerald-300">
            </section>
            <aside class="h-56 lg:flex-[3] bg-red-200"></aside>
        </div>
    </main>

    <?php get_footer(); ?>