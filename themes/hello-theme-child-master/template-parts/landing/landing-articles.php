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

$articles = array_slice($articles, 0, 5);

// echo '<pre>';
// print_r($articles);
// echo '</pre>';
?>

<section class="my-5">
    <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-5 py-2 w-fit lg:w-1/4 text-center">
        Letter Commentaries
    </h2>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-8 gap-6">
        <div class="col-span-5 md:col-span-3 lg:col-span-4">
            <div class="overflow-hidden">
                <a target="_blank" rel="noopener noreferrer">
                    <img src="<?php echo $articles[0]['image_url'] ?>" alt="Article Image" class="w-full h-[350px] object-cover rounded-md">
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
        <?php
        array_shift($articles);
        ?>
        <div class="col-span-5 lg:col-span-4 space-y-6">
            <?php foreach ($articles as $article): ?>
                <div class="flex items-center overflow-hidden">
                    <a target="_blank" rel="noopener noreferrer" class="flex">
                        <img src="<?php echo $article['image_url'] ?>" alt="South Korea’s KHNP Signs Long-Term Uranium Agreement With Centrus Energy" class="w-28 h-18 object-cover rounded-md">
                        <div class="pl-5">
                            <h3 class="leading-tight font-medium text-lg line-clamp-1"><?php echo $article['title']; ?></h3>
                            <p class="text-sm line-clamp-2 mt-2">
                                <?php echo $article['content']; ?>
                            </p>
                            <p class="text-sm mt-1 font-medium">
                                <?php
                                $timestamp = strtotime($article['published_date']);
                                $readable_date = gmdate('F j, Y', $timestamp);
                                echo $readable_date;
                                ?>
                            </p>
                        </div>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>