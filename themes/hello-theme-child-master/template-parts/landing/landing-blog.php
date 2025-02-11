<?php
$response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/blog-articles/");
if (wp_remote_retrieve_response_code($response) !== 200) {
    return;
}
$data = wp_remote_retrieve_body($response);
$blogs = json_decode($data, true);

// Function to check if a blog has all required fields
function has_all_required_data($blog)
{
    return !empty($blog['blog_name']) && !empty($blog['title']) && !empty($blog['link']) && !empty($blog['published_date']) && !empty($blog['excerpt']);
}

// Sorting: Prioritize complete blogs, then sort by title length
usort($blogs, function ($a, $b) {
    $a_complete = has_all_required_data($a);
    $b_complete = has_all_required_data($b);

    if ($a_complete !== $b_complete) {
        return $b_complete - $a_complete; // Complete blogs come first
    }

    return strtotime($b['published_date']) - strtotime($a['published_date']);
});

$blogs = array_slice($blogs, 0, 6);
$images = [1,2,3,4,5,6];
shuffle($images);
?>

<section class="my-5 w-full xl:w-2/5">
    <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-5 py-2 w-fit lg:w-1/2 text-center">
        Must Read Blogs
    </h2>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ($blogs as $index => $blog): ?>
            <a class="flex flex-col h-full group px-4 py-3 rounded-lg border hover:bg-blue-50/40 transition-all" href="<?php echo $blog['link'] ?>" target="_blank">
                <img class="w-full h-52 md:h-32 object-cover rounded-t-lg" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/finance-<?php echo $images[$index] ?>.jpg" alt="<?php echo $blog['title']; ?>">
                <h3 class="mt-2 leading-tight font-semibold group-hover:underline line-clamp-2">
                    <?php echo $blog['title'] ?>
                </h3>
                <div class="text-sm space-x-2 mt-2">
                    <span>
                        <?php echo $blog['published_date'] ?>
                    </span>
                </div>
            </a>
        <?php endforeach ?>
    </div>
</section>