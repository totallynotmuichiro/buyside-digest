<?php

require_once('utils.php');

$investor_slug  = get_query_var('investor_slug');
$investor_data  = wp_remote_get('https://sectobsddjango-production.up.railway.app/api/holdings/?investor_name=' . str_replace('-', ' ', $investor_slug));
$investors_data = wp_remote_get('https://sectobsddjango-production.up.railway.app/api/investors/');

if (is_array($investor_data)) {
    $investor_data = json_decode($investor_data['body'], true)[0];
}

if (is_array($investors_data)) {
    $investors_data = json_decode($investors_data['body'], true);
    foreach ($investors_data as $investor) {
        if ($investor['name'] === $investor_data['investor_name']) {
            $investor_data['stocks_info'] = $investor['stocks_info'];
            $investor_data['value'] = $investor['value'];
            $investor_data['turnover'] = $investor['turnover'];
            $investor_data['image_path'] = $investor['image_path'];
            break;
        }
    }
}

$top_holdings = get_top_holdings($investor_data['holdings'], 5);

get_header(); ?>

<section class="bsd-container">
    <div class="container mx-auto px-4 lg:px-20 my-5">

        <!-- Responsive grid layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <!-- Info -->
            <div class="lg:col-span-5 border text-card-foreground w-full mx-auto bg-white shadow-md rounded-xl overflow-hidden" data-v0-t="card">
                <div class="p-5">
                    <div class="flex flex-col items-center space-y-3 md:flex-row md:items-start md:space-y-0 md:space-x-4">
                        <img alt="<?php echo esc_attr($investor_data['investor_name']); ?>" class="w-20 h-20 rounded-full object-cover shadow-lg" src="<?php echo esc_url($investor_data['image_path']); ?>">
                        <div class="flex-1 w-full">
                            <h2 class="text-xl font-bold text-gray-800 text-center md:text-left"><?php echo esc_html($investor_data['investor_name']); ?></h2>
                            <p class="text-sm text-gray-600 mb-3 text-center md:text-left"><?php echo esc_html($investor_data['company']); ?></p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Value</p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo $investor_data['value']; ?></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Stocks</p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo esc_html($investor_data['stocks_info']); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Turnover</p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo esc_html($investor_data['turnover']); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">CIK</p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo esc_html($investor_data['cik']); ?></p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs font-medium text-gray-500">Top Holdings</p>
                                    <p class="text-sm font-semibold text-gray-900">
                                        <?php foreach ($top_holdings as $holding): ?>
                                            <?php echo $holding['ticker'] . '(' . number_format($holding['percentage_of_total'], 2) . '%) '; ?>
                                        <?php endforeach; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- About -->
            <div class="lg:col-span-4 border text-card-foreground w-full mx-auto bg-white shadow-md rounded-xl overflow-hidden p-5" data-v0-t="card">
                <h3 class="text-lg font-bold text-gray-800 text-center md:text-left">About Investor</h3>
                <p class="text-sm">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus minus error facilis, recusandae magnam hic adipisci nobis aliquam. Voluptatum unde veniam sunt dolorem explicabo, quibusdam vel labore! Laudantium ratione error maxime nostrum dolores explicabo incidunt id architecto mollitia distinctio! Commodi modi accusantium ratione sunt quae eum quasi enim facilis. Facilis? abo incidunt id architecto mollitia distinctio! Commodi modi accusantium ratione sunt quae eum quasi enim facilis. Facilis?
                </p>
            </div>
            <!-- Philosophy -->
            <div class="lg:col-span-3 border text-card-foreground w-full mx-auto bg-white shadow-md rounded-xl overflow-hidden p-5" data-v0-t="card">
                <h3 class="text-lg font-bold text-gray-800 text-center md:text-left">Investor Philosophy</h3>
                <p class="text-sm">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Delectus minus error facilis, recusandae magnam hic adipisci nobis aliquam. Voluptatum unde veniam sunt dolorem explicabo, quibusdam vel labore! Laudantium ratione error maxime nostrum dolores explicabo incidunt id architecto mollitia distinctio! Commodi modi accusantium ratione sunt quae eum quasi enim facilis. Facilis?
                </p>
            </div>
        </div>
    </div>
</section>
<?php get_footer(); ?>