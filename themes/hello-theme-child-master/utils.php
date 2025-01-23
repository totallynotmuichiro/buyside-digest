<?php

function get_top_holdings($holdings, $count = 5) {
    usort($holdings, function($a, $b) {
        return $b['value'] - $a['value'];
    });

    $total_value = array_sum(array_column($holdings, 'value'));
    $top_holdings = array_slice($holdings, 0, $count);

    foreach ($top_holdings as &$holding) {
        $holding['percentage_of_total'] = ($holding['value'] / $total_value) * 100;
    }

    return $top_holdings;
}