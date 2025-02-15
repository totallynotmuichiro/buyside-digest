<?php
$args = array(
    'post_type' => 'letters',
    'posts_per_page' => 10,
    'orderby' => 'date',
    'order' => 'DESC',
);

$letters = new WP_Query($args);
?>

<section class="my-5 w-full">
    <div class="flex justify-between items-center">
        <h2 class="bg-primary text-white text-lg lg:text-xl font-bold px-5 py-2 w-fit lg:w-1/5 text-center">
            Recently Posted Letters
        </h2>
        <a href="/hedge-fund-database/" class="border-[1.5px] border-primary hover:bg-primary/5 font-medium rounded-sm text-primary py-1 px-5">
            Full list
        </a>
    </div>
    <div class="mt-6">
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <table class="w-full border-collapse table-auto mb-0">
                <thead>
                    <tr class="bg-primary text-white">
                        <th class="sticky left-0 bg-primary border-b border-gray-300 px-3 !py-1 z-10 text-left whitespace-nowrap">Quarter</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Fund</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Key person</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">QTD</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">YTD</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Tickers</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Letter</th>
                    </tr>
                </thead>
                <tbody class="text-md">
                    <?php
                    while ($letters->have_posts()) :
                        $letters->the_post() ?>
                        <tr class="bg-white even:bg-gray-100">
                            <td class="border-b border-gray-300 px-3 py-1 whitespace-nowrap">
                                <?php echo BSD_Helper::get_letter_quarter(get_the_ID()); ?>
                            </td>
                            <td class="border-b border-gray-300 px-3 py-1 whitespace-nowrap">
                                <a class="text-primary font-medium hover:underline"
                                    href="<?php print_r(BSD_Helper::get_letter_fund_permalink(get_the_ID())); ?>">
                                    <?php echo BSD_Helper::get_letter_fund_name(get_the_ID()); ?>
                                </a>
                            </td>
                            <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap"><?php echo BSD_Helper::get_letter_key_person(get_the_ID()); ?></td>
                            <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap"><?php echo BSD_Helper::get_letter_QTD(get_the_ID()); ?></td>
                            <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap"><?php echo BSD_Helper::get_letter_YTD(get_the_ID()); ?></td>
                            <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">
                                <?php
                                $tickers = BSD_Helper::get_letter_tickers(get_the_ID());

                                if (empty($tickers)) {
                                    echo '-';
                                } else {
                                    foreach ($tickers as $index => $ticker):
                                ?>
                                        <a class="text-primary whitespace-nowrap font-medium hover:underline after:content-[','] last:after:content-none"
                                            href="<?php echo $ticker['permalink']; ?>"><?php echo $ticker['name']; ?></a>
                                <?php
                                    endforeach;
                                }
                                ?>
                            </td>
                            <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">
                                <a class="text-primary font-medium hover:underline whitespace-nowrap" href="<?php echo BSD_Helper::get_letter_link(get_the_ID()); ?>">View</a>
                            </td>
                        </tr>
                    <?php endwhile ?>
                    <?php wp_reset_postdata(); ?>
                </tbody>
            </table>
        </div>
    </div>
</section>