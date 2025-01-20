<?php
/*
Template Name: Investors
*/

// Include the investor data
include 'investor-data.php';

// Pagination settings
$items_per_page = 9;
$current_page = isset($_GET['page-number']) ? max(1, intval($_GET['page-number'])) : 1;

// Get the query parameters
$country = isset($_GET['country']) ? $_GET['country'] : 'all';
$investorType = isset($_GET['investor-type']) ? $_GET['investor-type'] : 'all';
$totalValue = isset($_GET['total-value']) ? $_GET['total-value'] : 'all';
$numStocks = isset($_GET['num-stocks']) ? $_GET['num-stocks'] : 'all';
$turnoverRatio = isset($_GET['turnover-ratio']) ? $_GET['turnover-ratio'] : 'all';
$investorName = isset($_GET['investor-name']) ? $_GET['investor-name'] : '';

// Filter the investors based on the query parameters
$investors = array_filter($investors, function($investor) use ($country, $investorType, $totalValue, $numStocks, $turnoverRatio, $investorName) {
    if ($country !== 'all' && $investor['Country'] !== $country) {
        return false;
    }

    if ($investorType !== 'all' && $investor['type'] !== $investorType) {
        return false;
    }

    if ($totalValue !== 'all' && $investor['total_value'] !== $totalValue) {
        return false;
    }

    if ($numStocks !== 'all' && $investor['no_of_stocks'] !== $numStocks) {
        return false;
    }

    if ($turnoverRatio !== 'all' && $investor['turnover_ratio'] !== $turnoverRatio) {
        return false;
    }

    if (!empty($investorName) && stripos($investor['name'], $investorName) === false) {
        return false;
    }

    return true;
});

// Pagination calculations
$total_items = count($investors);
$total_pages = ceil($total_items / $items_per_page);
$current_page = min($current_page, $total_pages); // Ensure current page doesn't exceed total pages
$offset = ($current_page - 1) * $items_per_page;

// Slice the array for current page
$paginated_investors = array_slice($investors, $offset, $items_per_page);

function toCamelCase($string) {
    $string = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    return lcfirst($string);
}

// Function to generate pagination URL
function get_pagination_url($page) {
    $params = $_GET;
    $params['page-number'] = $page;
    return '?' . http_build_query($params);
}

get_header();
?>

<!-- TradingView Widget BEGIN -->
<section class="bsd-container">
    <div class="container mx-auto px-4 lg:px-20 my-5">
        <form action="" method="get">
            <h1 class="text-3xl">Investors</h1>
            <div class="flex flex-col space-y-4 lg:space-y-0 lg:flex-row lg:space-x-4 items-end mt-5">
                <div class="w-full">
                    <label for="investor-name">Investor Name</label>
                    <input id="investor-name" type="text" placeholder="Search" name="investor-name" class="border-2 border-slate-200 rounded-md px-2 py-1 w-full" value="<?php echo esc_attr($investorName); ?>" />
                </div>

                <?php foreach ($options as $label => $dropdownOptions): ?>
                    <div class="w-full">
                        <label for="<?php echo esc_attr($label); ?>"><?php echo ucfirst(str_replace('-', ' ', $label)); ?></label>
                        <select id="<?php echo esc_attr($label); ?>" class="border-2 border-slate-200 rounded-md p-2 w-full" name="<?php echo esc_attr($label); ?>">
                            <?php foreach ($dropdownOptions as $optionLabel => $optionValue): ?>
                                <option value="<?php echo esc_attr($optionValue); ?>" <?php echo $optionValue === ${toCamelCase($label)} ? 'selected' : ''; ?>>
                                    <?php echo esc_html($optionLabel); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endforeach; ?>

                <input type="submit" class="bg-primary hover:bg-secondary border-0 text-white px-10 py-2 h-full w-full lg:w-auto" value="Search" />
                <input type="reset" class="bg-primary hover:bg-secondary border-0 text-white px-10 py-2 h-full w-full lg:w-auto" value="Reset" onclick="window.location.href = '<?php echo get_permalink(); ?>'" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-5">
                <?php foreach ($paginated_investors as $investor): ?>
                    <div class="bg-slate-100 shadow-md p-4 rounded-md">
                        <h2 class="text-2xl"><?php echo esc_html($investor['name']); ?></h2>
                        <p><?php echo esc_html($investor['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="flex justify-center items-center space-x-2 my-8">
                <?php if ($current_page > 1): ?>
                    <a href="<?php echo esc_url(get_pagination_url($current_page - 1)); ?>" class="px-4 py-2 border rounded-md hover:bg-slate-100 text-slate-600">
                        Previous
                    </a>
                <?php endif; ?>

                <?php
                // Calculate range of pages to show
                $range = 2;
                $start_page = max(1, $current_page - $range);
                $end_page = min($total_pages, $current_page + $range);

                // Show first page if not in range
                if ($start_page > 1): ?>
                    <a href="<?php echo esc_url(get_pagination_url(1)); ?>" class="px-4 py-2 border rounded-md hover:bg-slate-100 text-slate-600">1</a>
                    <?php if ($start_page > 2): ?>
                        <span class="px-4 py-2 text-slate-600">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <?php if ($i === $current_page): ?>
                        <span class="px-4 py-2 border rounded-md bg-primary text-white"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="<?php echo esc_url(get_pagination_url($i)); ?>" class="px-4 py-2 border rounded-md hover:bg-slate-100 text-slate-600"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php
                // Show last page if not in range
                if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span class="px-4 py-2 text-slate-600">...</span>
                    <?php endif; ?>
                    <a href="<?php echo esc_url(get_pagination_url($total_pages)); ?>" class="px-4 py-2 border rounded-md hover:bg-slate-100 text-slate-600"><?php echo $total_pages; ?></a>
                <?php endif; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="<?php echo esc_url(get_pagination_url($current_page + 1)); ?>" class="px-4 py-2 border rounded-md hover:bg-slate-100 text-slate-600">
                        Next
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </form>
    </div>
</section>

<?php get_footer(); ?>