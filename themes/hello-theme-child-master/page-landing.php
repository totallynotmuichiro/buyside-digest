    <?php
    /*
    Template Name: Landing
    */

    get_header();
    ?>
    <main class="bsd-container">
        <div class="flex flex-col lg:flex-row gap-5 mx-5 md:mx-16">
            <section class="w-full lg:w-[77%]">
                <?php get_template_part('template-parts/landing/landing', 'hero'); ?>
                <?php get_template_part('template-parts/landing/landing', 'articles'); ?>
                <?php get_template_part('template-parts/landing/landing', 'blog'); ?>
                <?php get_template_part('template-parts/landing/landing', 'news'); ?>
            </section>
            <aside class="w-full lg:w-[23%] bg-gray-200 flex justify-center items-center">
                sidebar
            </aside>
        </div>
    </main>

    <?php get_footer(); ?>