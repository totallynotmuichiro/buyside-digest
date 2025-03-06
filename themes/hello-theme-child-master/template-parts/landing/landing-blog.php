<?php
// Get the blog items
$blogs = BSD_API::get_blogs();

// Generate image array
$images = range(16, 25);
shuffle($images);

$total_blogs = count($blogs);
?>

<section class="mt-8">
    <h2 class="text-xl font-bold text-black/80 border-b border-gray-300 pb-2 mb-4">Must Read Blogs</h2>
    <div class="grid grid-cols-1 md:grid-cols-8 gap-6">
        <!-- Featured Blog -->
        <?php if ($total_blogs > 0): ?>
            <div class="col-span-8 md:col-span-4">
                <a href="<?php echo $blogs[0]['link'] ?>" class="block group relative overflow-hidden rounded-lg shadow-lg" target="_blank">
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/img/finance-<?php echo $images[0] ?>.jpg"
                        alt="Blog Image"
                        class="w-full h-[400px] object-cover rounded-lg transition-transform duration-300 group-hover:scale-105">
                    <div class="absolute bottom-0 bg-gradient-to-t from-black/80 to-transparent p-5 w-full">
                        <div class="text-xs text-gray-300 space-x-2 mt-1 font-medium">
                            <span>
                                <?php
                                $timestamp = strtotime($blogs[0]['published_date']);
                                echo gmdate('F j, Y', $timestamp);
                                ?>
                            </span>
                            <span>|</span>
                            <span><?php echo $blogs[0]['blog_name']; ?></span>
                        </div>
                        <h3 class="text-white text-lg font-semibold leading-tight mb-2 group-hover:underline">
                            <?php echo $blogs[0]['title'] ?>
                        </h3>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <!-- Secondary Blogs (2-4) -->
        <?php if ($total_blogs > 1): ?>
            <div class="col-span-8 lg:col-span-4 space-y-6">
                <?php for ($i = 1; $i < min(5, $total_blogs); $i++): ?>
                    <a class="flex flex-row items-center gap-5 group rounded-lg transition-all" href="<?php echo $blogs[$i]['link'] ?>" target="_blank">
                        <img
                            class="w-20 object-cover rounded-md aspect-square"
                            src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/finance-<?php echo $images[$i] ?>.jpg"
                            alt="<?php echo $blogs[$i]['title']; ?>" />
                        <div class="flex flex-col justify-center">
                            <div class="text-xs space-x-2 mt-2">
                                <span>
                                    <?php
                                    $timestamp = strtotime($blogs[$i]['published_date']);
                                    $readable_date = gmdate('F j, Y', $timestamp);
                                    echo $readable_date;
                                    ?>
                                </span>
                                <span>|</span>
                                <span><?php echo $blogs[$i]['blog_name']; ?></span>
                            </div>
                            <h3 class="leading-tight font-semibold group-hover:text-blue-600 line-clamp-2">
                                <?php echo $blogs[$i]['title']; ?>
                            </h3>
                            <p class="text-gray-600 line-clamp-3 text-sm">
                                <?php echo $blogs[$i]['excerpt']; ?>
                            </p>
                        </div>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

        <!-- Additional Blogs (6-9) -->
        <?php if ($total_blogs > 5): ?>
            <?php for ($i = 5; $i < min(9, $total_blogs); $i++): ?>
                <a class="flex flex-row overflow-hidden col-span-8 lg:col-span-4 items-center gap-5 group rounded-lg transition-all" href="<?php echo $blogs[$i]['link'] ?>" target="_blank">
                    <img
                        class="w-20 object-cover rounded-md aspect-square"
                        src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/finance-<?php echo $images[$i] ?>.jpg"
                        alt="<?php echo $blogs[$i]['title']; ?>" />

                    <div class="flex flex-col justify-center">
                        <div class="text-xs space-x-2 mt-2">
                            <span>
                                <?php
                                $timestamp = strtotime($blogs[$i]['published_date']);
                                $readable_date = gmdate('F j, Y', $timestamp);
                                echo $readable_date;
                                ?>
                            </span>
                            <span>|</span>
                            <span><?php echo $blogs[$i]['blog_name']; ?></span>
                        </div>
                        <h3 class="leading-tight font-semibold group-hover:text-blue-600 line-clamp-2">
                            <?php echo $blogs[$i]['title']; ?>
                        </h3>
                        <p class="text-gray-600 line-clamp-3 text-sm">
                            <?php echo $blogs[$i]['excerpt']; ?>
                        </p>
                    </div>
                </a>
            <?php endfor; ?>
        <?php endif; ?>
    </div>
</section>