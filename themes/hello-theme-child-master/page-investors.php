<?php
/*
Template Name: Investors
*/

// Include the investor data
include 'investor-data.php';

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

function toCamelCase($string) {
    $string = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    return lcfirst($string);
}

get_header();
?>

<!-- TradingView Widget BEGIN -->
<section class="bsd-container">
    <div class="container mx-auto px-20 my-5">
        <form action="" method="get">
            <h1 class="text-3xl">Investors</h1>
            <div class="flex flex-col space-y-4 lg:space-y-0 lg:flex-row lg:space-x-4 items-end mt-5">
                <div class="w-full">
                    <label for="investor-name">Investor Name</label>
                    <input id="investor-name" type="text" placeholder="Search" name="investor-name" class="border-2 border-slate-200 rounded-md px-2 py-1" value="<?php echo esc_attr($investorName); ?>" />
                </div>

                <?php foreach ($options as $label => $dropdownOptions): ?>
                    <div class="w-full">
                        <label for="<?php echo esc_attr($label); ?>"><?php echo ucfirst(str_replace('-', ' ', $label)); ?></label>
                        <select id="<?php echo esc_attr($label); ?>" class="border-2 border-slate-200 rounded-md p-2" name="<?php echo esc_attr($label); ?>">
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
                <?php foreach ( $investors as $investor ): ?>
                    <div class="bg-slate-100 shadow-md p-4 rounded-md">
                        <h2 class="text-2xl"><?php echo esc_html($investor['name']); ?></h2>
                        <p><?php echo esc_html($investor['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
    </div>
</section>

<?php get_footer(); ?>