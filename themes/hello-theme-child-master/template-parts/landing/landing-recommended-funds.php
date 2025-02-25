<?php
global $wpdb;

// Query to get the top 10 most followed funds
$fund_followers_query = "SELECT p.ID as fund_id, COUNT(DISTINCT fs.user_id) as follower_count 
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->prefix}fund_subscriptions fs ON p.ID = fs.fund_id
        WHERE p.post_type = 'funds' AND p.post_status = 'publish'
        GROUP BY fund_id 
        ORDER BY follower_count DESC 
        LIMIT 10";

$top_funds = $wpdb->get_results($fund_followers_query, ARRAY_A);
?>

<section class="mt-8 w-full">
    <h2 class="text-xl font-bold text-black/80 border-b border-gray-300 pb-2 mb-4">BSD Recommended Letters</h2>
    <div>
        <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            <table class="w-full border-collapse table-auto mb-0">
                <thead>
                    <tr class="bg-primary text-white">
                        <th class="sticky left-0 bg-primary border-b border-gray-300 px-3 !py-1 z-10 text-left whitespace-nowrap">Fund</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Investor Name</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Quarterly</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">YTD</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Website</th>
                        <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Twitter</th>
                    </tr>
                </thead>
                <tbody class="text-md">
                    <?php if (!empty($top_funds)) : ?>
                        <?php foreach ($top_funds as $fund) : ?>
                            <tr class="bg-white even:bg-gray-100">
                                <td class="border-b border-gray-300 px-3 py-1 whitespace-nowrap">
                                    <a class="text-primary font-medium hover:underline" href="<?php echo get_permalink($fund['fund_id']); ?>">
                                        <?php echo esc_html(get_the_title($fund['fund_id'])); ?>
                                    </a>
                                </td>
                                <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">
                                    <?php echo BSD_Template::get_fund_investor_name($fund['fund_id']) ?>
                                </td>
                                <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">
                                    <?php echo BSD_Template::get_fund_quarterly($fund['fund_id']) ?>
                                </td>
                                <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">
                                    <?php echo BSD_Template::get_fund_ytd($fund['fund_id']) ?>
                                </td>

                                <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">
                                    <?php $website_link = BSD_Template::get_fund_website_link($fund['fund_id']); ?>
                                    <?php if (!empty($website_link)) : ?>
                                        <a class="text-primary font-medium hover:underline" href="<?php echo $website_link; ?>">
                                            View
                                        </a>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">
                                    <?php $twitter_link = BSD_Template::get_fund_twitter_link($fund['fund_id']); ?>
                                    <?php if (!empty($twitter_link)) : ?>
                                        <a class="text-primary font-medium hover:underline" href="<?php echo $twitter_link; ?>">
                                            View
                                        </a>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="2" class="border-b border-gray-300 px-3 py-1 text-center">
                                No funds found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>