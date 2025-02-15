<?php
function get_cached_substack_posts() {
    // Try to get cached data first
    $cached_posts = get_transient('sectobs_substack_posts');
    if ($cached_posts !== false) {
        return $cached_posts;
    }

    // If no cache, fetch fresh data
    $response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/substack-articles/", array('timeout' => 5000));
    if (wp_remote_retrieve_response_code($response) !== 200) {
        return [];
    }

    $data = wp_remote_retrieve_body($response);
    $substackPosts = json_decode($data, true);

    // Function to check if a post has all required fields
    function has_all_required_substack_data($post) {
        return !empty($post['source_name']) &&
            !empty($post['title']) &&
            !empty($post['url']) &&
            !empty($post['published_date']) &&
            !empty($post['subtitle']) && 
            !empty($post['cover_image']);
    }

    // Store only the latest article from each source_name
    $filteredPosts = [];
    foreach ($substackPosts as $post) {
        if (has_all_required_substack_data($post)) {
            $source = $post['source_name'];
            if (!isset($filteredPosts[$source]) || strtotime($post['published_date']) > strtotime($filteredPosts[$source]['published_date'])) {
                $filteredPosts[$source] = $post;
            }
        }
    }

    $finalPosts = array_values($filteredPosts);
    $finalPosts = array_slice( $finalPosts, 0, 12 );

    set_transient('sectobs_substack_posts', $finalPosts, 18000);

    return $finalPosts;
}

// Get the Substack posts
$substackPosts = get_cached_substack_posts();
?>

<section class="my-5 w-full">
    <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-10 py-2 w-fit text-center">
       Substack
    </h2>
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($substackPosts as $index => $post): ?>
            <a class="flex flex-row gap-5 h-full group px-4 py-3 rounded-lg border hover:bg-blue-50/40 transition-all" href="<?php echo $post['url'] ?>" target="_blank">
                <img
                    class="w-32 h-full object-cover rounded-md aspect-square"
                    src="<?php echo $post['cover_image']; ?>"
                    alt="<?php echo $post['title']; ?>" />
                <div>
                    <h3 class="leading-tight font-semibold group-hover:underline line-clamp-2">
                        <?php echo $post['title'] ?>
                    </h3>
                    <p class="text-sm line-clamp-3 mt-2">
                        <?php echo $post['subtitle'] ?>
                    </p>
                    <div class="text-sm space-x-2 mt-2">
                        <span>
                            <?php $timestamp = strtotime($post['published_date']);
                            $readable_date = gmdate('F j, Y', $timestamp);
                            echo $readable_date;
                            ?>
                        </span>
                        <span>|</span>
                        <span><?php echo $post['source_name']; ?></span>
                    </div>
                </div>
            </a>
        <?php endforeach ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>