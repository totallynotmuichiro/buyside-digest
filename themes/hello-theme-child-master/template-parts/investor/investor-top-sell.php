<?php
$investor_holdings = (isset($args) && isset($args['holdings'])) ? $args['holdings'] : [];

// Sort based on shares change percentage
usort($investor_holdings, function ($a, $b) {
    return $a['shares_change_pct'] <=> $b['shares_change_pct'];
});

// Get top 5 holdings
$top_holdings = array_slice($investor_holdings, 0, 5);

?>
<div class="lg:col-span-4 border text-card-foreground w-full mx-auto bg-white shadow-md rounded-xl overflow-hidden p-4">
    <div class="w-full bg-white rounded overflow-hidden">
        <div class="w-full bg-white rounded border-gray-300 overflow-hidden">
            <div class="p-4 pt-0 pl-2 border-b border-gray-200">
                <h2 class="text-gray-800 text-lg font-bold">Top Sell (13F)</h2>
            </div>

            <div class="p-0">
                <!-- Table Header -->
                <div class="flex border-b border-gray-200 py-3 px-4 bg-gray-50">
                    <div class="w-1/2 text-gray-600 font-medium text-xs">Name</div>
                    <div class="w-1/2 text-right text-gray-600 font-medium text-xs">% Change</div>
                </div>

                <!-- Table Rows - dynamically generated -->
                <?php foreach ($top_holdings as $index => $holding): ?>
                    <div class="flex border-b border-gray-200 <?php echo BSD_Template::get_alternating_row_class($index); ?> py-3 px-4">
                        <div class="w-1/2 flex items-center">
                            <span class="font-bold text-gray-800 text-sm"><?php echo $holding['ticker']; ?></span>
                            <p class="ml-2 text-primary font-medium text-xs truncate "><?php echo $holding['company_name']; ?></p>
                        </div>
                        <div class="w-1/2 text-right text-sm text-red-600"><?php echo number_format($holding['shares_change_pct'], 2); ?>%</div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($top_holdings)): ?>
                    <div class="flex py-3 px-4">
                        <div class="w-full text-center text-gray-600">No holdings data available</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>