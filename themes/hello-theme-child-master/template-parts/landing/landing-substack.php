<?php
// Get the Substack posts
$substackPosts = BSD_API::get_substacks();
?>

<section class="my-5 w-full">
    <h2 class="text-xl font-bold text-black/80 border-b border-gray-300 pb-2">Substack</h2>
    <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($substackPosts as $index => $post): ?>
            <a class="flex flex-row gap-5 h-full group px-4 py-3 rounded-lg border hover:bg-blue-50/40 transition-all" href="<?php echo $post['url'] ?>" target="_blank">
                <img
                    class="w-32 h-full object-cover rounded-md aspect-square"
                    src="<?php echo $post['cover_image']; ?>"
                    alt="<?php echo $post['title']; ?>" />
                <div>
                    <h3 class="leading-tight font-semibold group-hover:underline line-clamp-2">
                        <?php echo $post['title'] ?>
                    </h3>
                    <p class="text-sm line-clamp-3 mt-2">
                        <?php echo $post['subtitle'] ?>
                    </p>
                    <div class="text-sm space-x-2 mt-2">
                        <span>
                            <?php $timestamp = strtotime($post['published_date']);
                            $readable_date = gmdate('F j, Y', $timestamp);
                            echo $readable_date;
                            ?>
                        </span>
                        <span>|</span>
                        <span><?php echo $post['source_name']; ?></span>
                    </div>
                </div>
            </a>
        <?php endforeach ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>