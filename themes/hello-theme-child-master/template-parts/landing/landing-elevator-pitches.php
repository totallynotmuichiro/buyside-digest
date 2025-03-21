<?php
// Get date from 1 week ago
$last_week = date('Y-m-d H:i:s', strtotime('-2 month'));

$args = array(
    'post_type'      => 'elevator_pitch',
    'posts_per_page' => 3,
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
    <section style="margin-top: 50px" class="w-full">
        <h2 class="text-xl font-bold text-black/80 border-b border-gray-300 pb-2 mb-4">Elevator Pitches of the Week</h2>
        <div class="space-y-3 mt-3">
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
                <div class="flex items-center bg-gray-100 p-4 shadow-sm space-x-4 transition-all transform duration-300 rounded-lg">
                    <!-- Logo (Fixed for proper scaling) -->
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>" class="lg:hidden xl:flex flex-shrink-0 w-20 h-20 flex items-center justify-center  p-2 rounded-md overflow-hidden">
                            <?php
                            $image_id = get_post_thumbnail_id();
                            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                            $image_url = wp_get_attachment_image_src($image_id, 'full')[0];
                            ?>
                            <img
                                src="<?php echo esc_url($image_url); ?>"
                                alt="<?php echo esc_attr($image_alt); ?>"
                                class="max-w-full max-h-full w-auto h-auto object-contain mix-blend-multiply " />
                        </a>
                    <?php endif; ?>
                    <div class="flex-1">
                        <!-- Title -->
                        <h3 class="text-gray-900">
                            <a href="<?php the_permalink(); ?>" class="hover:underline font-semibold !text-lg"><?php the_title(); ?></a>
                        </h3>
                        <!-- Meta data -->
                        <div class="grid grid-cols-[auto_1fr] lg:flex lg:flex-col 2xl:grid 2xl:grid-cols-[auto_1fr] gap-x-4 gap-y-1 mt-1">
                            <p class="text-sm col-span-2">
                                <span class="font-semibold"> Fund: </span>
                                <?php echo esc_html($fund_name); ?>
                            </p>
                            <p class="font-semibold text-sm col-span-2">
                                Ticker: <?php echo $ticker_term ? '<a href="' . esc_url(get_term_link($tickers)) . '" class="text-blue-600 hover:underline">' . esc_html($ticker_name) . '</a>' : 'No Ticker'; ?>
                            </p>

                            <?php if (have_rows('data_values')) : ?>
                                <?php while (have_rows('data_values')) : the_row(); ?>
                                    <?php if ($add_values_here = get_sub_field('add_values_here')) : ?>
                                        <?php $parts = explode(":", $add_values_here); ?>
                                        <div class="flex justify-between items-center text-sm min-w-0">
                                            <span class="font-semibold whitespace-nowrap"><?php echo esc_html($parts[0]) . ': '; ?></span>
                                            <span class="ml-1 flex-1 truncate"><?php echo esc_html($parts[1]); ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                        <!-- View Letter & Fund Buttons -->
                        <div class="flex space-x-2 mt-1">
                            <?php if ($letters_pdf) : ?>
                                <a href="<?= esc_url($letters_pdf); ?>" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 block">View Letter</a>
                            <?php endif; ?>
                            <?php if ($fund_link) : ?>
                                <a href="<?= esc_url($fund_link); ?>" target="_blank" class="text-xs text-blue-600 hover:underline mt-1 block">View Fund</a>
                            <?php endif; ?>
                        </div>
                        <div class="elev-pitch-stats">

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>