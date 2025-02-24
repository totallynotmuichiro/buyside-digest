<?php
$videos = BSD_API::get_yt_videos();
?>

<section class="mt-8 w-full">
    <h2 class="text-xl font-bold text-black/80 border-b border-gray-300 pb-2 mb-4">Must watch Videos</h2>
    <div class="swiper mySwiper">
        <div class="swiper-wrapper cursor-grab">
            <?php foreach ($videos as $video): ?>
                <div class="swiper-slide">
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm h-full">
                        <div class="flex flex-col space-y-1.5 p-4">
                            <div class="relative pb-[56.25%] overflow-hidden rounded-lg">
                                <iframe src="https://www.youtube.com/embed/<?php echo $video['video_id'] ?>" title="<?php echo $video['title'] ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="absolute top-0 left-0 w-full h-full"></iframe>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold tracking-tight text-lg mb-2 line-clamp-2"><?php echo $video['title'] ?></h3>
                            <div class="text-sm font-medium text-primary mb-2"><?php echo $video['channel_name'] ?></div>
                            <p class="text-sm text-muted-foreground line-clamp-2">'<?php echo $video['description'] ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>