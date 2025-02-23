<?php
$response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/yahoo-articles/");

if (wp_remote_retrieve_response_code($response) !== 200) {
    return;
}

$data = wp_remote_retrieve_body($response);
$articles = json_decode($data, true);

$uniqueTitles = [];
$articles = array_filter($articles, function ($item) use (&$uniqueTitles) {
    if (!in_array($item['title'], $uniqueTitles)) {
        $uniqueTitles[] = $item['title'];
        return true;
    }
    return false;
});

usort($articles, function ($a, $b) {
    return strtotime($b['published_date']) - strtotime($a['published_date']);
});

$articles = array_slice($articles, 0, 9);

// Safety check - ensure we have articles
if (empty($articles)) {
    return;
}

// Generate unique IDs for each article modal
$modal_ids = array_map(function ($index) {
    return 'article-modal-' . $index;
}, array_keys($articles));

$total_articles = count($articles);
?>

<!-- Overlay (one for all modals) -->
<div data-overlay-show="true" class="transition-all hidden fixed inset-0 z-50 bg-black/60 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0"></div>

<!-- Generate modals for each article -->
<?php foreach ($articles as $index => $article): ?>
    <div id="<?php echo $modal_ids[$index]; ?>" class="hidden fixed inset-0 z-50 overflow-y-auto transition-all">
        <div class="min-h-screen px-4 md:px-8 py-12">
            <div class="relative max-w-7xl mx-auto bg-white rounded-none md:rounded-xl shadow-2xl">
                <!-- Close button -->
                <button class="absolute right-4 top-4 z-10 p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>

                <!-- Article content -->
                <div class="p-6 md:p-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                        <?php echo $article['title']; ?>
                    </h2>

                    <div class="mt-4 flex items-center space-x-4 text-sm text-gray-600">
                        <span class="font-medium"><?php echo $article['author']; ?></span>
                        <span>•</span>
                        <span>
                            <?php
                            $timestamp = strtotime($article['published_date']);
                            $readable_date = gmdate('F j, Y', $timestamp);
                            echo $readable_date;
                            ?>
                        </span>
                    </div>

                    <div class="mt-8 -mx-6 md:-mx-10">
                        <img src="<?php echo $article['image_url'] ? $article['image_url'] : get_stylesheet_directory_uri() . '/assets/img/finance.jpg' ?>"
                            alt="<?php echo $article['title']; ?>"
                            class="w-full h-[400px] md:h-[500px] object-cover">
                    </div>

                    <div class="mt-8 prose prose-lg max-w-none">
                        <p class="text-gray-700 text-lg leading-relaxed">
                            <?php echo $article['content']; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<section class="my-5">
    <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-10 py-2 w-fit text-center">
        BSDs in the News
    </h2>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-8 gap-6">
        <!-- Featured Article -->
        <?php if ($total_articles > 0): ?>
            <div class="col-span-5 md:col-span-3 lg:col-span-4">
                <div class="overflow-hidden">
                    <a href="#" data-modal-trigger="<?php echo $modal_ids[0]; ?>" class="block">
                        <img src="<?php echo $articles[0]['image_url'] ? $articles[0]['image_url'] : get_stylesheet_directory_uri() . '/assets/img/finance.jpg' ?>"
                            alt="Article Image"
                            class="w-full h-[350px] object-cover rounded-md">
                        <div class="pt-4">
                            <h3 class="text-lg font-medium leading-tight mb-2">
                                <?php echo $articles[0]['title'] ?>
                            </h3>
                            <p class="text-sm line-clamp-2 mt-1">
                                <?php echo $articles[0]['content']; ?>
                            </p>
                            <div class="text-sm text-date space-x-2 mt-1 font-medium">
                                <span>
                                    <?php
                                    $timestamp = strtotime($articles[0]['published_date']);
                                    $readable_date = gmdate('F j, Y', $timestamp);
                                    echo $readable_date;
                                    ?>
                                </span>
                                <span>|</span>
                                <span><?php echo $articles[0]['author']; ?></span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Secondary Articles (2-5) -->
        <?php if ($total_articles > 1): ?>
            <div class="col-span-5 lg:col-span-4 space-y-6">
                <?php for ($i = 1; $i < min(5, $total_articles); $i++): ?>
                    <div class="flex items-center overflow-hidden">
                        <a href="#" data-modal-trigger="<?php echo $modal_ids[$i]; ?>" class="flex">
                            <img src="<?php echo $articles[$i]['image_url'] ? $articles[$i]['image_url'] : get_stylesheet_directory_uri() . '/assets/img/finance.jpg' ?>"
                                alt="<?php echo $articles[$i]['title']; ?>"
                                class="w-28 h-18 object-cover rounded-md">
                            <div class="pl-5">
                                <h3 class="leading-tight font-medium text-lg line-clamp-1">
                                    <?php echo $articles[$i]['title']; ?>
                                </h3>
                                <p class="text-sm line-clamp-2 mt-2">
                                    <?php echo $articles[$i]['content']; ?>
                                </p>
                                <p class="text-sm mt-1 font-medium">
                                    <?php
                                    $timestamp = strtotime($articles[$i]['published_date']);
                                    $readable_date = gmdate('F j, Y', $timestamp);
                                    echo $readable_date;
                                    ?>
                                </p>
                            </div>
                        </a>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <!-- Additional Articles (6-9) -->
        <?php if ($total_articles > 5): ?>
            <?php for ($i = 5; $i < min(9, $total_articles); $i++): ?>
                <div class="flex items-center overflow-hidden col-span-8 lg:col-span-4">
                    <a href="#" data-modal-trigger="<?php echo $modal_ids[$i]; ?>" class="flex">
                        <img src="<?php echo $articles[$i]['image_url'] ? $articles[$i]['image_url'] : get_stylesheet_directory_uri() . '/assets/img/finance.jpg' ?>"
                            alt="<?php echo $articles[$i]['title']; ?>"
                            class="w-28 h-18 object-cover rounded-md">
                        <div class="pl-5">
                            <h3 class="leading-tight font-medium text-lg line-clamp-1">
                                <?php echo $articles[$i]['title']; ?>
                            </h3>
                            <p class="text-sm line-clamp-2 mt-2">
                                <?php echo $articles[$i]['content']; ?>
                            </p>
                            <p class="text-sm mt-1 font-medium">
                                <?php
                                $timestamp = strtotime($articles[$i]['published_date']);
                                $readable_date = gmdate('F j, Y', $timestamp);
                                echo $readable_date;
                                ?>
                            </p>
                        </div>
                    </a>
                </div>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
</section>