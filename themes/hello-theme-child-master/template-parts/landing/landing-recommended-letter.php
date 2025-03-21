<?php
$args = array(
    'post_type'      => 'letters',
    'posts_per_page' => 10,
    'post__in'       => array(27011, 27319, 27187, 26481, 26819, 26844, 27136, 27504, 27419,27803 ),
    'orderby'        => 'post__in',
);

$letters = new WP_Query($args);
?>

<section class="mt-8 w-full">
    <div class="flex justify-between border-b border-black/20 pb-2 mb-6 lg:mb-4">
        <h2 class="flex text-xl items-center font-bold text-black/80 capitalize">BSD Recommended Letters</h2>
        <a href="/hedge-fund-database/" class="border border-[1.4px] border-primary hover:bg-primary/5 font-medium text-sm rounded-md text-primary py-1 px-4">
            Full list
        </a>
    </div>

    <div>
        <div class="w-full hide-scrollbar overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100 rounded-md">
            <table class="w-full border-collapse table-auto mb-0 rounded-md">
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
                                <?php echo BSD_Template::get_letter_quarter(get_the_ID()); ?>
                            </td>
                            <td class="border-b border-gray-300 px-3 py-1 whitespace-nowrap">
                                <a class="text-primary font-medium hover:underline"
                                    href="<?php print_r(BSD_Template::get_letter_fund_permalink(get_the_ID())); ?>">
                                    <?php echo BSD_Template::get_letter_fund_name(get_the_ID()); ?>
                                </a>
                            </td>
                            <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap"><?php echo BSD_Template::get_letter_key_person(get_the_ID()); ?></td>
                            <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap"><?php echo BSD_Template::get_letter_QTD(get_the_ID()); ?></td>
                            <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap"><?php echo BSD_Template::get_letter_YTD(get_the_ID()); ?></td>
                            <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">
                                <?php
                                $tickers = BSD_Template::get_letter_tickers(get_the_ID());

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
                                <a class="text-primary font-medium hover:underline whitespace-nowrap" href="<?php echo BSD_Template::get_letter_link(get_the_ID()); ?>">View</a>
                            </td>
                        </tr>
                    <?php endwhile ?>
                    <?php wp_reset_postdata(); ?>
                </tbody>
            </table>
        </div>
    </div>
</section>