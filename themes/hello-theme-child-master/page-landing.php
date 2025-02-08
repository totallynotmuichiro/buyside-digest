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
            <section class="lg:flex-[10]">
                <?php get_template_part('template-parts/landing/landing', 'articles'); ?>
                <?php get_template_part('template-parts/landing/landing', 'blog'); ?>
                <?php get_template_part('template-parts/landing/landing', 'news'); ?>
            </section>
            <aside class="lg:flex-[3] bg-gray-200 flex justify-center items-center">
                sidebar
            </aside>
        </div>
    </main>

    <?php get_footer(); ?>