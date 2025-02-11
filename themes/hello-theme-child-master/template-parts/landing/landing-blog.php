<?php
function get_cached_blog_items() {
    // Try to get cached data first
    $cached_blogs = get_transient('sectobs_blog_items');
    if ($cached_blogs !== false) {
        return $cached_blogs;
    }

    // If no cache, fetch fresh data
    $response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/blog-articles/", array( 'timeout' => 5000 ));
    if (wp_remote_retrieve_response_code($response) !== 200) {
        return [];
    }
    
    $data = wp_remote_retrieve_body($response);
    $blogs = json_decode($data, true);

    // Function to check if a blog has all required fields
    function has_all_required_data($blog) {
        return !empty($blog['blog_name']) && 
               !empty($blog['title']) && 
               !empty($blog['link']) && 
               !empty($blog['published_date']) && 
               !empty($blog['excerpt']);
    }

    // Sorting: Prioritize complete blogs, then sort by published date
    usort($blogs, function ($a, $b) {
        $a_complete = has_all_required_data($a);
        $b_complete = has_all_required_data($b);

        if ($a_complete !== $b_complete) {
            return $b_complete - $a_complete; // Complete blogs come first
        }

        return strtotime($b['published_date']) - strtotime($a['published_date']);
    });

    $blogs = array_slice($blogs, 0, 6);
    
    set_transient('sectobs_blog_items', $blogs, 18000);
    
    return $blogs;
}

// Get the blog items
$blogs = get_cached_blog_items();

// Generate image array
$images = range(1, 6);
shuffle($images);
?>

<section class="my-5 w-full">
    <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-5 py-2 w-fit lg:w-1/4 text-center">
        Must Read Blogs
    </h2>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($blogs as $index => $blog): ?>
            <a class="flex flex-row gap-5 h-full group px-4 py-3 rounded-lg border hover:bg-blue-50/40 transition-all" href="<?php echo $blog['link'] ?>" target="_blank">
                <img
                    class="h-full w-32 xl:w-20 2xl:w-32 object-cover rounded-md"
                    src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/finance-<?php echo $images[$index] ?>.jpg"
                    alt="<?php echo $blog['title']; ?>" />
                <div>
                    <h3 class="leading-tight font-semibold group-hover:underline line-clamp-2">
                        <?php echo $blog['title'] ?>
                    </h3>
                    <p class="text-sm line-clamp-3 mt-2">
                        <?php echo $blog['excerpt'] ?>
                    </p>
                    <div class="text-sm space-x-2 mt-2">
                        <span>
                            <?php $timestamp = strtotime($blog['published_date']);
                            $readable_date = gmdate('F j, Y', $timestamp);
                            echo $readable_date;
                            ?>
                        </span>
                    </div>
                </div>

            </a>
        <?php endforeach ?>
    </div>
</section>