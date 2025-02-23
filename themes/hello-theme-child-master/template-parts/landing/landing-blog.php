<?php
// Get the blog items
$blogs = BSD_API::get_blogs();

// Generate image array
$images = range(1, 10);
shuffle($images);
?>

<section class="my-5 w-full">
    <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-10 py-2 w-fit text-center">
        Must Read Blogs
    </h2>
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <?php foreach ($blogs as $index => $blog): ?>
            <a class="flex flex-row gap-5 h-full group px-4 py-3 rounded-lg border hover:bg-blue-50/40 transition-all" href="<?php echo $blog['link'] ?>" target="_blank">
                <img
                    class="w-32 h-full object-cover rounded-md aspect-square"
                    src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/finance-<?php echo $images[$index] ?>.jpg"
                    alt="<?php echo $blog['title']; ?>" />
                <div>
                    <h3 class="leading-tight font-semibold group-hover:underline line-clamp-2">
                        <?php echo $blog['title'] ?>
                    </h3>
                    <p class="text-sm line-clamp-3 mt-2">
                        <?php echo $blog['excerpt'] ?>
                    </p>
                    <div class="text-sm space-x-2 mt-2">
                        <span>
                            <?php $timestamp = strtotime($blog['published_date']);
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