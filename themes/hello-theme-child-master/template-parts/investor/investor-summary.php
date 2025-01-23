<?php
$investor_data = get_query_var('investor_data');
$holdings = array_slice($investor_data['holdings'], 0, 10);

function format_value($value) {
    $value = floatval(str_replace(['%', ','], '', $value));
    $class = $value >= 0 ? 'text-green-500' : 'text-red-500';
    return "<span class=\"{$class}\">" . number_format($value, 2) . "%</span>";
}

?>

<div class="lg:col-span-8 border text-card-foreground w-full mx-auto bg-white shadow-md rounded-xl overflow-hidden p-4">
    <div class="w-full overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
        <table class="w-full border-collapse table-auto text-sm mb-0">
            <thead>
                <tr class="bg-gray-100">
                    <th class="sticky left-0 bg-gray-100 border-b border-gray-300 px-3 !py-1 z-10 text-left whitespace-nowrap">Ticker</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Value</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Shares</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Industry</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-left whitespace-nowrap">Company Name</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap">Weighting %</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap">YTD Change %</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap">Trade Impact %</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap">Shares Change %</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap">Market Cap (M)</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap">Shares Outstanding %</th>
                    <th class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap">3M Change %</th>
                </tr>
            </thead>
            <tbody class="text-xs">
                <?php foreach ($holdings as $holding): ?>   
                    <tr class="hover:bg-gray-50">
                        <td class="sticky left-0 !bg-white border-b pb-1 border-gray-300 px-3 py-1 font-semibold z-10 whitespace-nowrap"><?php echo !empty($holding['ticker']) ? $holding['ticker'] : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap"><?php echo !empty($holding['value']) ? number_format($holding['value'], 2) : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap"><?php echo !empty($holding['shares']) ? number_format($holding['shares'], 2) : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 whitespace-nowrap"><?php echo !empty($holding['industry']) ? $holding['industry'] : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 whitespace-nowrap"><?php echo !empty($holding['company_name']) ? $holding['company_name'] : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap"><?php echo !empty($holding['weighting_pct']) ? format_value($holding['weighting_pct']) : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap"><?php echo !empty($holding['ytd_change_pct']) ? format_value($holding['ytd_change_pct']) : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap"><?php echo !empty($holding['trade_impact_pct']) ? format_value($holding['trade_impact_pct']) : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap"><?php echo !empty($holding['shares_change_pct']) ? format_value($holding['shares_change_pct']) : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap"><?php echo !empty($holding['market_cap_millions']) ? number_format($holding['market_cap_millions'], 2) : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap"><?php echo !empty($holding['shares_outstanding_pct']) ? format_value($holding['shares_outstanding_pct']) : '-'; ?></td>
                        <td class="border-b border-gray-300 px-3 py-1 text-right whitespace-nowrap"><?php echo !empty($holding['three_month_change_pct']) ? format_value($holding['three_month_change_pct']) : '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>