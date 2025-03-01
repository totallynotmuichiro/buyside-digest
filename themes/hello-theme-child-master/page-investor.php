<?php

require_once('utils.php');

$investor_slug  = get_query_var('investor_slug');
$investor_data  = wp_remote_get('https://sectobsddjango-production.up.railway.app/api/holdings/?investor_name=' . str_replace('-', ' ', $investor_slug));
$investors_data = wp_remote_get('https://sectobsddjango-production.up.railway.app/api/investors/');

if (is_array($investor_data) && !empty($investor_data['body'])) {
    $decoded_data = json_decode($investor_data['body'], true);
    if (is_array($decoded_data) && !empty($decoded_data)) {
        $investor_data = $decoded_data[0];
    } else {
        get_template_part('template-parts/investor/investor-no-data-found');
    }
} else {
    get_template_part('template-parts/investor/investor-no-data-found');
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
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'summary';

get_header(); ?>

<section class="bsd-container">
    <div class="container mx-auto px-4 lg:px-20 my-5">

        <!-- Responsive grid layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
            <!-- Info -->
            <div class="lg:col-span-5 border text-card-foreground w-full mx-auto bg-white shadow-md rounded-xl overflow-hidden" data-v0-t="card">
                <div class="p-5">
                    <div class="flex flex-col items-center space-y-3 md:flex-row md:items-start md:space-y-0 md:space-x-4">
                        <?php
                        // Check if image URL is valid
                        $image_url = $investor_data['image_path'];
                        $response = wp_remote_head($image_url, ['timeout' => 1]);
                        
                        if (wp_remote_retrieve_response_code($response) === 404 || empty($image_url)) {
                            $image_url = 'https://ui-avatars.com/api/?name=' . urlencode($investor_data['investor_name']) . '&background=0d3e6f&color=fff';
                        }
                        ?>
                        <img 
                            alt="<?php echo esc_attr($investor_data['investor_name']); ?>" 
                            class="w-20 h-20 rounded-full object-cover shadow-lg" 
                            src="<?php echo esc_url($image_url); ?>"
                        >
                        <div class="flex-1 w-full">
                            <h2 class="text-xl font-bold text-gray-800 text-center md:text-left"><?php echo esc_html($investor_data['investor_name']); ?></h2>
                            <p class="text-sm text-gray-600 mb-3 text-center md:text-left"><?php echo esc_html($investor_data['company']); ?></p>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Value</p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo !empty($investor_data['value']) ? $investor_data['value'] : '-'; ?></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Stocks</p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo !empty($investor_data['stocks_info']) ? esc_html($investor_data['stocks_info']) : '-'; ?></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">Turnover</p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo !empty($investor_data['turnover']) ? esc_html($investor_data['turnover']) : '-'; ?></p>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-gray-500">CIK</p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo !empty($investor_data['cik']) ? esc_html($investor_data['cik']) : '-'; ?></p>
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
            <!-- Tabs -->
            <div class="lg:col-span-12 border text-card-foreground w-full mx-auto bg-white shadow-md rounded-xl overflow-hidden p-1" data-v0-t="card">
                <nav class="flex gap-2" aria-label="Tabs">
                    <?php
                    $current_tab_style = 'bg-primary text-white';
                    $other_tab_style = 'text-gray-500 hover:bg-gray-50 hover:text-gray-700';
                    ?>
                    <a
                        href="<?php echo get_permalink() . '?tab=summary'; ?>"
                        class="shrink-0 rounded-lg p-2 text-sm font-medium <?php echo $current_tab === 'summary' ? $current_tab_style : $other_tab_style; ?>"
                    >
                        Summary
                    </a>
                    <a
                        href="<?php echo get_permalink() . '?tab=current-portfolio'; ?>"
                        class="shrink-0 rounded-lg p-2 text-sm font-medium <?php echo $current_tab === 'current-portfolio' ? $current_tab_style : $other_tab_style; ?>"
                    >
                        Current Portfolio
                    </a>
                </nav>
            </div>

            <?php
            set_query_var('investor_data', $investor_data);
            if ($current_tab === 'summary') {
                get_template_part('template-parts/investor/investor', 'top-holdings');
                get_template_part('template-parts/investor/investor', 'treemap');
                get_template_part('template-parts/investor/investor', 'pie');
            } else if ($current_tab === 'current-portfolio') {
                get_template_part('template-parts/investor/investor-current-portfolio');
            }
            ?>

        </div>
    </div>
</section>
<?php get_footer(); ?>