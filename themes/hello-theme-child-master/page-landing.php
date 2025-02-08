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
                <div class="flex flex-col xl:flex-row gap-5 mt-16">
                    <?php get_template_part('template-parts/landing/landing', 'blog'); ?>
                    <?php get_template_part('template-parts/landing/landing', 'news'); ?>
                </div>
                <?php get_template_part('template-parts/landing/landing', 'cta-1'); ?>
                <?php get_template_part('template-parts/landing/landing', 'articles'); ?>
            </section>
            <aside class="w-full lg:w-[23%] bg-gray-200 flex justify-center items-center">
                sidebar
            </aside>
        </div>
    </main>

    <?php get_footer(); ?>