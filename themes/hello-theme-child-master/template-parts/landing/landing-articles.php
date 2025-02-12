<?php
function get_cached_news_items()
{
    // Try to get cached data first
    $cached_news = get_transient('sectobs_news_items');
    if ($cached_news !== false) {
        return $cached_news;
    }

    // If no cache, fetch fresh data
    $response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/news-articles/", array('timeout' => 5000));
    if (wp_remote_retrieve_response_code($response) !== 200) {
        return [];
    }

    $data = wp_remote_retrieve_body($response);
    $newsItems = json_decode($data, true);

    // Function to check if a blog has all required fields
    function has_all_required_news_data($newsItem)
    {
        return !empty($newsItem['source_name']) &&
            !empty($newsItem['title']) &&
            !empty($newsItem['link']) &&
            !empty($newsItem['published_date']) &&
            !empty($newsItem['description']);
    }

    // Sorting: Prioritize complete blogs, then sort by published date
    usort($newsItems, function ($a, $b) {
        $a_complete = has_all_required_news_data($a);
        $b_complete = has_all_required_news_data($b);

        if ($a_complete !== $b_complete) {
            return $b_complete - $a_complete; // Complete blogs come first
        }

        return strtotime($b['published_date']) - strtotime($a['published_date']);
    });

    $newsItems = array_slice($newsItems, 0, 8);

    set_transient('sectobs_news_items', $newsItems, 18000);

    return $newsItems;
}

// Get the news items
$newsItems = get_cached_news_items();

// Generate image array
$images = range(7, 15);
shuffle($images);
?>

<section class="my-5 w-full">
    <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-5 py-2 w-fit lg:w-1/5 text-center">
        News from the Buy Side
    </h2>
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php foreach ($newsItems as $index => $newsItem): ?>
            <a class="flex flex-row gap-5 h-full group px-4 py-3 rounded-lg border hover:bg-blue-50/40 transition-all" href="<?php echo $newsItem['link'] ?>" target="_blank">
                <img
                    class="w-32 h-full object-cover rounded-md aspect-square"
                    src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/finance-<?php echo $images[$index] ?>.jpg"
                    alt="<?php echo $newsItem['title']; ?>" />
                <div>
                    <h3 class="leading-tight font-semibold group-hover:underline line-clamp-2">
                        <?php echo $newsItem['title'] ?>
                    </h3>
                    <p class="text-sm line-clamp-3 mt-2">
                        <?php echo $newsItem['description'] ?>
                    </p>
                    <div class="text-sm space-x-2 mt-2">
                        <span>
                            <?php $timestamp = strtotime($newsItem['published_date']);
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