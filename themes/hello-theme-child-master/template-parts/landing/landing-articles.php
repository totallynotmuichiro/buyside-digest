<?php

// Get the news items
$newsItems = BSD_API::get_news();

// Generate image array
$images = range(7, 15);
shuffle($images);
?>

<section class="mt-8 w-full">
    <h2 class="text-xl font-bold text-black/80 border-b border-gray-300 pb-2 mb-4"> News from the Buy Side</h2>
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php foreach ($newsItems as $index => $newsItem): ?>
            <a class="flex flex-row gap-5 h-full group px-4 py-3 rounded-lg border bg-blue-50/40 hover:bg-blue-50/50 shadow-sm transition-all" href="<?php echo $newsItem['link'] ?>" target="_blank">
                <img
                    class="w-32 object-cover rounded-md aspect-square"
                    src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/finance-<?php echo $images[$index] ?>.jpg"
                    alt="<?php echo $newsItem['title']; ?>" />
                <div class="flex flex-col justify-center">
                    <h3 class="leading-tight font-semibold group-hover:text-blue-600 line-clamp-2">
                        <?php echo $newsItem['title']; ?>
                    </h3>
                    <div class="text-sm space-x-2 mt-2">
                        <span>
                            <?php 
                            $timestamp = strtotime($newsItem['published_date']);
                            $readable_date = gmdate('F j, Y', $timestamp);
                            echo $readable_date;
                            ?>
                        </span>
                        <span>|</span>
                        <span>
                            <?php echo $newsItem['source']; ?>
                        </span>
                    </div>
                    <p class="text-sm line-clamp-3 mt-2">
                        <?php echo $newsItem['excerpt']; ?>
                    </p>
                    <span class="text-blue-600 font-medium text-sm group-hover:text-blue-700 transition-all mt-2">
                        Read More →
                    </span>
                </div>
            </a>
        <?php endforeach ?>
    </div>
</section>