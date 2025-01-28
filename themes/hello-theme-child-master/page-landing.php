<?php
/*
Template Name: Landing
*/

get_header();
?>
<main class="bsd-container">
    <section class="text-gray-600 body-font">
        <div class="container mx-auto flex px-5 py-12 md:flex-row flex-col items-center">
            <div class="lg:flex-grow md:w-1/2 lg:pr-24 md:pr-16 flex flex-col md:items-start md:text-left mb-16 md:mb-0 items-center text-center">
                <h1 class="title-font sm:text-4xl lg:text-5xl text-3xl mb-4 font-medium text-gray-900 animate-appear">
                 <span class="text-primary font-bold">Unlock</span>   
                 the Wisdom of the Markets <span class="text-primary font-bold"> One Quarterly Investor </span> Letter at a Time.
                </h1>
                <p class="mb-6 leading-relaxed animate-appear">Dive deep into the minds of leading hedge fund managers through their exclusive quarterly letters and stock pitches.</p>
                <div class="flex justify-center space-x-4">
                    <a href="<?php echo get_home_url() . '/hedge-fund-database'; ?>" class="inline-flex text-white bg-primary border-0 py-2 px-6 focus:outline-none hover:bg-primary/90 cursor-pointer rounded-md text-lg animate-appear">Learn More</a>
                </div>
            </div>
            <div class="w-[50vw] relative">
                <img class="object-cover object-center rounded animate-appear" alt="hero" src="http://bsd-test.local/wp-content/uploads/2024/04/banner-img.png">
            </div>
        </div>
    </section>
    <?php get_template_part('template-parts/landing/landing', 'signup'); ?>
</main>

<?php get_footer(); ?>