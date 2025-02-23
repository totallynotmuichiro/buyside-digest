<?php
function get_blog_items() {
    // Fetch fresh data
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

    return array_slice($blogs, 0, 10);
}

// Get the blog items
$blogs = get_blog_items();

// Generate image array
$images = range(1, 10);
shuffle($images);
?>

<section class="my-5 w-full">
    <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-10 py-2 w-fit text-center">
        Must Read Blogs
    </h2>
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php foreach ($blogs as $index => $blog): ?>
            <a class="flex flex-row gap-5 h-full group px-4 py-3 rounded-lg border hover:bg-blue-50/40 transition-all" href="<?php echo $blog['link'] ?>" target="_blank">
                <img
                    class="w-32 h-full object-cover rounded-md aspect-square"
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