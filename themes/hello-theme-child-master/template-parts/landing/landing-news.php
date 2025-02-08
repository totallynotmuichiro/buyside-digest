<?php
    $response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/news-articles/");
    if ( wp_remote_retrieve_response_code($response) !== 200 ) {
        return;
    } 
    $data = wp_remote_retrieve_body($response);
    $newsItems = json_decode($data, true);

    // Function to check if a blog has all required fields
    function has_all_required_news_data($newsItem) {
        return !empty($newsItem['source_name']) && !empty($newsItem['title']) && !empty($newsItem['link']) && !empty($newsItem['published_date']) && !empty($newsItem['description']);
    }

    // Sorting: Prioritize complete blogs, then sort by title length
    usort($newsItems, function($a, $b) {
        $a_complete = has_all_required_news_data($a);
        $b_complete = has_all_required_news_data($b);

        if ($a_complete !== $b_complete) {
            return $b_complete - $a_complete; // Complete blogs come first
        }

        return strlen($b['title']) - strlen($a['title']); // Longer titles come first
    });

    $newsItems = array_slice( $newsItems, 0, 6 );
?>

<section class="my-5">
    <h2 class="flex frank items-center md:text-xl font-bold text-black1/80 capitalize border-b border-black/20 mb-5">
        News
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php foreach ( $newsItems as $newsItem ): ?>
        <a class="flex flex-col h-full group px-4 py-3 rounded-lg border hover:bg-blue-50/40 transition-all hover:scale-" href="<?php echo $newsItem['link'] ?>" target="_blank">
            <div class="mb-2">
                <span class="bg-primary text-[11px] rounded-sm text-white px-2 py-1"><?php echo $newsItem["source_name"] ?></span>
            </div>
            <h3 class="leading-tight font-medium group-hover:underline line-clamp-2">
               <?php echo $newsItem['title'] ?>
            </h3>
            <p class="text-sm line-clamp-2 mt-2">
                <?php echo $newsItem['description']?>      
            </p>
            <div class="text-sm space-x-2 mt-2">
                <span>
                    <?php $timestamp = strtotime($newsItem['published_date']); $readable_date = gmdate('F j, Y', $timestamp); echo $readable_date; ?>
                </span>
            </div>
        </a>
        <?php endforeach ?>
    </div>
</section>