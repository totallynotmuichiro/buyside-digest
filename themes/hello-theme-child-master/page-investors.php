<?php
/*
Template Name: Investors
*/

// Fetch investors from the API
$investors = wp_remote_get('https://sectobsddjango-production.up.railway.app/api/investors/');

if (is_array($investors)) {
    $investors = json_decode($investors['body'], true);
}

// Define the dropdown options
$options = array(
    'value' => array(
        'All' => 'all',
        '<$1M' => 'less-than-1m',
        '$1M-$10M' => '1m-to-10m',
        '$10M-$50M' => '10m-to-50m',
        '>$50M' => 'greater-than-50m',
    ),
    'num-stocks' => array(
        'All' => 'all',
        '1-10' => '1-to-10',
        '11-50' => '11-to-50',
        '51-100' => '51-to-100',
        '>100' => 'greater-than-100',
    ),
    'turnover-ratio' => array(
        'All' => 'all',
        '<10%' => 'less-than-10',
        '10%-20%' => '10-to-20',
        '20%-50%' => '20-to-50',
        '>50%' => 'greater-than-50',
    ),
);

// Pagination settings
$items_per_page = 9;
$current_page = isset($_GET['page-number']) ? max(1, intval($_GET['page-number'])) : 1;

// Get the query parameters
$value = isset($_GET['value']) ? $_GET['value'] : 'all';
$numStocks = isset($_GET['num-stocks']) ? $_GET['num-stocks'] : 'all';
$turnoverRatio = isset($_GET['turnover-ratio']) ? $_GET['turnover-ratio'] : 'all';
$investorName = isset($_GET['investor-name']) ? $_GET['investor-name'] : '';

// Filter the investors based on the query parameters
$investors = array_filter($investors, function($investor) use ($value, $numStocks, $turnoverRatio, $investorName) {
    // Extract numeric value from "Value $X.XX Mil/Bil" format
    if ($value !== 'all') {
        preg_match('/\$([0-9.]+)\s*(Mil|Bil)/', $investor['value'], $matches);
        $investorValue = isset($matches[1]) ? floatval($matches[1]) : 0;
        $unit = isset($matches[2]) ? $matches[2] : 'Mil';
        
        // Convert Billion to Million for consistent comparison
        if ($unit === 'Bil') {
            $investorValue *= 1000;
        }
        
        switch($value) {
            case 'less-than-1m':
                if ($investorValue >= 1) return false;
                break;
            case '1m-to-10m':
                if ($investorValue < 1 || $investorValue > 10) return false;
                break;
            case '10m-to-50m':
                if ($investorValue < 10 || $investorValue > 50) return false;
                break;
            case 'greater-than-50m':
                if ($investorValue <= 50) return false;
                break;
        }
    }

    // Extract number of stocks from "X Stocks (Y new)" format
    if ($numStocks !== 'all') {
        preg_match('/(\d+)\s+Stocks/', $investor['stocks_info'], $matches);
        $stockCount = isset($matches[1]) ? intval($matches[1]) : 0;
        
        switch($numStocks) {
            case '1-to-10':
                if ($stockCount < 1 || $stockCount > 10) return false;
                break;
            case '11-to-50':
                if ($stockCount < 11 || $stockCount > 50) return false;
                break;
            case '51-to-100':
                if ($stockCount < 51 || $stockCount > 100) return false;
                break;
            case 'greater-than-100':
                if ($stockCount <= 100) return false;
                break;
        }
    }

    // Extract turnover percentage from "36%" format
    if ($turnoverRatio !== 'all') {
        preg_match('/(\d+)%/', $investor['turnover'], $matches);
        $turnoverPercentage = isset($matches[1]) ? intval($matches[1]) : 0;

        switch($turnoverRatio) {
            case 'less-than-10':
                if ($turnoverPercentage >= 10) return false;
                break;
            case '10-to-20':
                if ($turnoverPercentage < 10 || $turnoverPercentage > 20) return false;
                break;
            case '20-to-50':
                if ($turnoverPercentage < 20 || $turnoverPercentage > 50) return false;
                break;
            case 'greater-than-50':
                if ($turnoverPercentage <= 50) return false;
                break;
        }
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

            <?php if (! empty($paginated_investors)) : ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-5">
                    <?php foreach ($paginated_investors as $investor): ?>
                        <!-- Investor Card -->
                        <div class="border text-card-foreground w-full max-w-md mx-auto bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-xl transition duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 cursor-pointer">
                            <div class="p-6">
                                <div class="flex items-center space-x-4 mb-4">
                                    <span class="relative flex shrink-0 overflow-hidden rounded-full h-16 w-16">
                                        <?php 
                                            $response = wp_remote_head($investor['image_path'], ['timeout' => 1]);

                                            if ( wp_remote_retrieve_response_code( $response ) === 404 || empty($investor['image_path']) ) {
                                                $investor['image_path'] = 'https://ui-avatars.com/api/?name=' . urlencode($investor['name']) . '&background=0d3e6f&color=fff';
                                            }
                                        ?>
                                        <img
                                            class="aspect-square h-full w-full object-cover"
                                            alt="<?php echo esc_attr($investor['name']); ?>"
                                            src="<?php echo esc_url($investor['image_path']); ?>"
                                        >
                                    </span>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-800">
                                            <?php echo esc_html($investor['name']); ?>
                                        </h2>
                                        <p class="text-sm text-gray-600">
                                            <?php echo esc_html($investor['company']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mt-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Value</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            <?php
                                            $value = str_replace('Value ', '', $investor['value']);
                                            echo empty($value) ? '-' : $value;
                                            ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Stocks</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            <?php echo esc_html($investor['stocks_info']); ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Turnover</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            <?php 
                                            $turnover = str_replace('Turnover ', '', $investor['turnover']);
                                            echo empty($turnover) ? '-' : $turnover;
                                            ?>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">CIK</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            <?php 
                                                echo $investor['cik'] ? $investor['cik'] : '-';
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
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

            <?php else : ?>
                <div class="my-5 md:my-10">
                    <p class="text-center text-lg font-medium text-slate-600">No investors found.</p>
                </div>
            <?php endif; ?>
        </form>
    </div>
</section>

<?php get_footer(); ?>