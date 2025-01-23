<?php
get_header();
?>
<section class="bsd-container bg-gray-100 min-h-screen flex items-center justify-center">
    <div class=" max-w-2xl mx-auto px-4 py-16 text-center">
        <h2 class="text-4xl font-bold text-gray-800 mb-6">No Data Found</h2>
        <p class="text-xl text-gray-600 mb-10">Sorry, we couldn't find any data that matches your search criteria.</p>
        <a href="<?php echo get_home_url() . '/investor'; ?>" class="inline-block bg-primary hover:bg-primary/80 text-white font-bold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
            Go Back
        </a>
    </div>
</section>
<?php
get_footer();
exit;
?>