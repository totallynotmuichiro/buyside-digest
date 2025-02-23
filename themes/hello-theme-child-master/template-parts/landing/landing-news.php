<?php

$articles = BSD_API::get_yahoo_news();

if (empty($articles)) {
    return;
}

$modal_ids = array_map(function ($index) {
    return 'article-modal-' . $index;
}, array_keys($articles));

$total_articles = count($articles);
?>

<section class="my-5">
    <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-10 py-2 w-fit text-center">
        BSDs in the News
    </h2>
    <div class="mt-6 grid grid-cols-1 md:grid-cols-8 gap-6">
        <!-- Featured Article -->
        <?php if ($total_articles > 0): ?>
            <div class="col-span-5 md:col-span-3 lg:col-span-4">
                <div class="overflow-hidden">
                    <a href="<?php echo $articles[0]['title'] ?>" data-modal-trigger="<?php echo $modal_ids[0]; ?>" class="block">
                        <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/finance.jpg' ?>"
                            alt="Article Image"
                            class="w-full h-[350px] object-cover rounded-md">
                        <div class="pt-4">
                            <h3 class="text-lg font-medium leading-tight mb-2">
                                <?php echo $articles[0]['title'] ?>
                            </h3>
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
                <?php for ($i = 1; $i < min(6, $total_articles); $i++): ?>
                    <div class="flex items-center overflow-hidden">
                        <a href="#" data-modal-trigger="<?php echo $modal_ids[$i]; ?>" class="flex">
                            <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/finance.jpg' ?>"
                                alt="<?php echo $articles[$i]['title']; ?>"
                                class="w-28 h-16 object-cover rounded-md">
                            <div class="pl-5">
                                <h3 class="leading-tight font-medium text-lg line-clamp-1">
                                    <?php echo $articles[$i]['title']; ?>
                                </h3>
                                <div class="text-sm text-date space-x-2 mt-1 font-medium">
                                    <span>
                                        <?php
                                        $timestamp = strtotime($articles[$i]['published_date']);
                                        $readable_date = gmdate('F j, Y', $timestamp);
                                        echo $readable_date;
                                        ?>
                                    </span>
                                    <span>|</span>
                                    <span><?php echo $articles[$i]['author']; ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <!-- Additional Articles (6-9) -->
        <?php if ($total_articles > 6): ?>
            <?php for ($i = 6; $i < min(10, $total_articles); $i++): ?>
                <a class="flex overflow-hidden col-span-8 lg:col-span-4">
                    <img src="<?php echo get_stylesheet_directory_uri() . '/assets/img/finance.jpg' ?>"
                        alt="<?php echo $articles[$i]['title']; ?>"
                        class="h-16 object-cover rounded-md">
                    <div class="pl-5">
                        <h3 class="leading-tight font-medium text-lg line-clamp-1">
                            <?php echo $articles[$i]['title']; ?>
                        </h3>
                        <div class="text-sm text-date space-x-2 mt-1 font-medium">
                            <span>
                                <?php
                                $timestamp = strtotime($articles[$i]['published_date']);
                                $readable_date = gmdate('F j, Y', $timestamp);
                                echo $readable_date;
                                ?>
                            </span>
                            <span>|</span>
                            <span><?php echo $articles[$i]['author']; ?></span>
                        </div>
                    </div>
                </a>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
</section>