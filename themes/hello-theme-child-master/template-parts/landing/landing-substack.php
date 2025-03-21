<?php
// Get the Substack posts
$substackPosts = BSD_API::get_substacks();
?>

<section style="margin-top:25px" class="mb-5 w-full">
    <div class="grid grid-cols-2 gap-6">
        <?php foreach ($substackPosts as $index => $post): ?>
            <a class="flex flex-row overflow-hidden items-start gap-4 group rounded-lg transition-all" 
               href="<?php echo $post['url'] ?>" target="_blank">
                <img
                    class="w-20 h-20 object-cover rounded-md aspect-square"
                    src="<?php echo $post['cover_image']; ?>"
                    alt="<?php echo $post['title']; ?>" />
                <div class="flex flex-col justify-start">
                    <div class="text-xs space-x-2">
                        <span>
                            <?php 
                                $timestamp = strtotime($post['published_date']);
                                $readable_date = gmdate('F j, Y', $timestamp);
                                echo $readable_date;
                            ?>
                        </span>
                        <span>|</span>
                        <span><?php echo $post['source_name']; ?></span>
                    </div>
                    <h3 class="leading-tight font-semibold text-[16px] group-hover:text-blue-600 line-clamp-2">
                        <?php echo $post['title'] ?>
                    </h3>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>


<?php wp_reset_postdata(); ?>
