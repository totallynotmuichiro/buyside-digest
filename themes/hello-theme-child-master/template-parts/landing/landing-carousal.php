<?php
$videos = BSD_API::get_yt_videos();
?>

<section class="mt-8 w-full">
    <h2 class="text-xl font-bold text-black/80 border-b border-gray-300 pb-2 mb-4">Must watch Videos</h2>
    <div class="swiper mySwiper">
        <div class="swiper-wrapper cursor-grab">
            <?php foreach ($videos as $video): ?>
                <div class="swiper-slide">
                    <div class="rounded-lg border bg-blue-50/50 shadow-sm h-full">
                        <div class="flex flex-col space-y-1.5 p-4">
                            <div class="relative pb-[56.25%] overflow-hidden rounded-lg">
                                <div class="youtube-thumbnail absolute top-0 left-0 w-full h-full cursor-pointer group" data-video-id="<?php echo esc_attr($video['video_id']); ?>">
                                    <img src="https://i.ytimg.com/vi/<?php echo esc_attr($video['video_id']); ?>/hqdefault.jpg" alt="<?php echo esc_attr($video['title']); ?>" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 flex items-center justify-center transition-opacity duration-300 group-hover:opacity-80">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="68" height="48" viewBox="0 0 68 48" class="play-icon">
                                            <path d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#f00"></path>
                                            <path d="M 45,24 27,14 27,34" fill="#fff"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold tracking-tight text-lg mb-2 line-clamp-2"><?php echo esc_html($video['title']); ?></h3>
                            <div class="text-sm font-medium text-primary mb-2"><?php echo esc_html($video['channel_name']); ?></div>
                            <p class="text-sm text-muted-foreground line-clamp-2"><?php echo esc_html($video['description']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>