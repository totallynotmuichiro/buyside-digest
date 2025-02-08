<?php
// Get date from 1 week ago
$last_week = date('Y-m-d H:i:s', strtotime('-2 month'));

$args = array(
    'post_type'      => 'elevator_pitch',
    'posts_per_page' => 5,
    'date_query'     => array(
        'relation' => 'OR',
        array(
            // Check for posts published in last week
            'column'    => 'post_date',
            'after'     => $last_week,
        ),
        array(
            // Check for posts modified in last week
            'column'    => 'post_modified',
            'after'     => $last_week,
        ),
    ),
    'orderby'        => 'modified',
    'order'          => 'DESC',
);
$weekly_pitches = new WP_Query($args);
if ($weekly_pitches->have_posts()) : ?>
    <section class="mt-10 w-full">
        <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-8 py-2 w-fit text-center">
            Elevator Pitches of the Week
        </h2>
        <div class="space-y-5 mt-6">
            <?php while ($weekly_pitches->have_posts()) : $weekly_pitches->the_post(); ?>
                <?php
                // Get Fund Name & Link
                $fund_post = get_field('link_fund');
                $fund_name = $fund_post ? get_the_title($fund_post->ID) : 'N/A';
                $fund_link = $fund_post ? get_permalink($fund_post->ID) : '';
                // Get Ticker Term
                $tickers = get_field('tickers');
                $ticker_term = $tickers ? get_term($tickers) : null;
                $ticker_name = $ticker_term ? $ticker_term->name : 'No Ticker';
                // Get Letter PDF
                $elevator_pitches_letters = get_field('elevator_pitches_letters');
                $letters_pdf = get_post_meta($elevator_pitches_letters, 'letter-link', true);
                ?>
                <div class="flex items-center bg-gray-100 p-4 rounded-lg shadow-sm space-x-4 transition-all transform duration-300 hover:scale-105 rounded-lg">
                    <!-- Logo (Fixed for proper scaling) -->
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="flex-shrink-0 w-20 h-20 flex items-center justify-center  p-2 rounded-md overflow-hidden">
                            <?php
                            $image_id = get_post_thumbnail_id();
                            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                            $image_url = wp_get_attachment_image_src($image_id, 'full')[0];
                            ?>
                            <img
                                src="<?php echo esc_url($image_url); ?>"
                                alt="<?php echo esc_attr($image_alt); ?>"
                                class="max-w-full max-h-full w-auto h-auto object-contain mix-blend-multiply" />
                        </a>
                    <?php endif; ?>
                    <div class="flex-1">
                        <!-- Title -->
                        <h3 class="text-lg font-semibold text-gray-900">
                            <a href="<?php the_permalink(); ?>" class="hover:underline"><?php the_title(); ?></a>
                        </h3>
                        <!-- Fund & Ticker -->
                        <p class="font-semibold text-sm">Fund: <?= esc_html($fund_name); ?> </p>
                        <p class="font-semibold text-sm">Ticker: <?= $ticker_term ? '<a href="' . esc_url(get_term_link($tickers)) . '" class="text-blue-600 hover:underline">' . esc_html($ticker_name) . '</a>' : 'No Ticker'; ?> </p>
                        <!-- View Letter & Fund Buttons -->
                        <div class="flex space-x-2">
                            <?php if ($letters_pdf) : ?>
                                <a href="<?= esc_url($letters_pdf); ?>" target="_blank" class="text-sm text-blue-600 hover:underline mt-1 block">View Letter</a>
                            <?php endif; ?>
                            <?php if ($fund_link) : ?>
                                <a href="<?= esc_url($fund_link); ?>" target="_blank" class="text-sm text-blue-600 hover:underline mt-1 block">View Fund</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>