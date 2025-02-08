<?php
$response = wp_remote_get("https://sectobsddjango-production.up.railway.app/api/yahoo-articles/");
if (wp_remote_retrieve_response_code($response) !== 200) {
    return;
}
$data = wp_remote_retrieve_body($response);
$articles = json_decode($data, true);

// // Function to check if a blog has all required fields
// function has_all_required_articles_data($article)
// {
//     return !empty($article['investor_name']) && !empty($article['title']) && !empty($article['image_url']) && !empty($article['author']) && !empty($article['published_date']);
// }

// // Sorting: Prioritize complete blogs, then sort by title length
// usort($articles, function ($a, $b) {
//     $a_complete = has_all_required_articles_data($a);
//     $b_complete = has_all_required_articles_data($b);

//     if ($a_complete !== $b_complete) {
//         return $b_complete - $a_complete; // Complete blogs come first
//     }

//     return strlen($b['title']) - strlen($a['title']); // Longer titles come first
// });

$uniqueTitles = [];
$articles = array_filter($articles, function ($item) use (&$uniqueTitles) {
    if (!in_array($item['title'], $uniqueTitles)) {
        $uniqueTitles[] = $item['title'];
        return true;
    }
    return false;
});

$articles = array_slice($articles, 0, 5);

// echo '<pre>';
// print_r($articles);
// echo '</pre>';
?>

<section class="my-5">
    <h2 class="flex frank items-center md:text-xl font-bold text-black1/80 capitalize border-b border-black/20 mb-5">
        Articles
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-8 gap-6">
        <div class="col-span-5 md:col-span-3 lg:col-span-4">
            <div class="overflow-hidden hover:underline"><a href="<?php echo $articles[0]['image_url'] ?>" target="_blank" rel="noopener noreferrer">
                    <img src="<?php echo $articles[0]['image_url'] ?>" alt="Article Image" class="w-full h-[300px] object-cover rounded-md">
                    <div class="pt-4">
                        <h3 class="text-[18px] font-medium leading-tight mb-2  hover:underline">
                            <?php echo $articles[0]['title'] ?>
                        </h3>
                        <div class="text-[14px] text-date space-x-2">
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
                </a></div>
        </div>
        <?php
        array_shift($articles);
        ?>
        <div class="col-span-5 lg:col-span-4 space-y-6">
            <?php foreach ($articles as $article): ?>
                <div class="flex items-center overflow-hidden hover:underline">
                    <a href="https://www.nucnet.org/news/south-korea-s-khnp-signs-long-term-uranium-agreement-with-centrus-energy-2-3-2025" target="_blank" rel="noopener noreferrer" class="flex">
                        <img src="<?php echo $article['image_url'] ?>" alt="South Korea’s KHNP Signs Long-Term Uranium Agreement With Centrus Energy" class="w-28 h-18 object-cover rounded-md">
                        <div class="pl-5">
                            <div class="">
                                <h3 class="text-[15px] leading-tight mb-1  font-medium"><?php echo $article['title']; ?></h3>
                            </div>
                            <div class="text-[12px] text-date">
                                <?php
                                $timestamp = strtotime($article['published_date']);
                                $readable_date = gmdate('F j, Y', $timestamp);
                                echo $readable_date;
                                ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>