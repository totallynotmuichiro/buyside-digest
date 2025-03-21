<?php

// Get the news items
$newsItems = BSD_API::get_news();

// Generate image array
$images = range(7, 15);
shuffle($images);

// Limit the number of news items displayed to 9
$newsItems = array_slice($newsItems, 0, 9);
?>

<section class="my-8 w-full">
    <h2 class="text-xl font-bold text-black/80 border-b border-gray-300 pb-2 mb-4">News from the BuySide</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mt-3">
        <?php foreach ($newsItems as $index => $newsItem): ?>
            <a class="flex flex-row gap-3 h-full group rounded-lg hover:bg-blue-50/70 transition-all" href="<?php echo $newsItem['link'] ?>" target="_blank">
                <img
                    class="w-32 h-24 object-cover rounded-md"
                    src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/finance-<?php echo $images[$index] ?>.jpg"
                    alt="<?php echo $newsItem['title']; ?>" />
                <div>
                    <h3 class="leading-tight font-semibold group-hover:underline line-clamp-2">
                        <?php echo $newsItem['title']; ?>
                    </h3>
                    <div class="text-xs text-black/30 space-x-2 mt-1">
                        <span>
                            <?php 
                            $timestamp = strtotime($newsItem['published_date']);
                            $readable_date = gmdate('F j, Y', $timestamp);
                            echo $readable_date;
                            ?>
                        </span>
                        <span>|</span>
                        <span><?php echo $newsItem['source']; ?></span>
                    </div>
                    <p class="text-sm line-clamp-2 mt-2">
                        <?php echo $newsItem['excerpt']; ?>
                    </p>
                </div>
            </a>
        <?php endforeach ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>
