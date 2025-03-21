<?php
// Function to determine the quarter end date from the filling date
function get_quarter_end_date($filling_date) {
    // Convert the filling_date to a timestamp
    $timestamp = strtotime($filling_date);

    // Extract the month and year from the filling_date
    $month = date('m', $timestamp);
    $year = date('Y', $timestamp);

    // Determine the quarter end date based on the month
    switch ($month) {
        case 1: case 2: case 3:
            $quarter_end_date = "$year-03-31"; // Q1 ends March 31
            break;
        case 4: case 5: case 6:
            $quarter_end_date = "$year-06-30"; // Q2 ends June 30
            break;
        case 7: case 8: case 9:
            $quarter_end_date = "$year-09-30"; // Q3 ends September 30
            break;
        case 10: case 11: case 12:
            $quarter_end_date = "$year-12-31"; // Q4 ends December 31
            break;
        default:
            $quarter_end_date = null; // In case the month is invalid
    }

    return $quarter_end_date;
}

// Function to generate dropdown with quarter end dates in descending order and the required format
function generate_dropdown($api_response) {
    // Initialize dropdown HTML
    $dropdown_html = '<select name="quarter_end_date" id="quarter_end_date_dropdown">';

    // Loop through all keys in the response and find historic holdings (historic_holdings1, historic_holdings2, etc.)
    $quarter_end_dates = []; // Initialize an array to store all quarter end dates

    foreach ($api_response as $key => $value) {
        if (strpos($key, 'historic_holdings') === 0) {
            // We have found a historic holdings section (like historic_holdings1, historic_holdings2, etc.)
            foreach ($value as $holding) {
                // Check if filling_date exists in the holding, otherwise use default
                $filling_date = isset($holding['filling_date']) ? $holding['filling_date'] : '2024-12-31'; // Default to '2024-12-31'

                // Get the quarter end date based on the filling date
                $quarter_end_date = get_quarter_end_date($filling_date);

                // Add the formatted quarter end date to the array (in format d-m-Y)
                $formatted_quarter_end_date = date('d-m-Y', strtotime($quarter_end_date));
                $quarter_end_dates[] = $formatted_quarter_end_date;
            }
        }
    }

    // Remove duplicates by converting the array to a unique set of quarter end dates
    $quarter_end_dates = array_unique($quarter_end_dates);

    // Sort the quarter end dates in descending order (latest first)
    rsort($quarter_end_dates);

    // Loop through the sorted quarter end dates and add them as options to the dropdown
    foreach ($quarter_end_dates as $quarter_end_date) {
        $dropdown_html .= "<option value='{$quarter_end_date}'>{$quarter_end_date}</option>";
    }

    // Close the select tag
    $dropdown_html .= '</select>';

    return $dropdown_html;
}

// Simulate API response
$api_response = [
    "id" => "f16106ec-c660-4638-87c5-dd775d6cabe3",
    "investor_name" => "John Rogers",
    "company" => "Ariel Investment",
    "cik" => "936753",
    "filling_date" => "2024-12-31",
    "holdings" => [
        // Sample data
    ],
    "historic_holdings1" => [
        [
            "value" => 297623,
            "shares" => 41165,
            "ticker" => "ADT",
            "industry" => "",
            "company_name" => "ADT INC DEL",
            "filling_date" => "2024-01-15"
        ],
        [
            "value" => 223415574,
            "shares" => 29396786,
            "ticker" => "ADT",
            "industry" => "",
            "company_name" => "ADT Inc",
            "filling_date" => "2024-02-20"
        ]
    ],
    "historic_holdings2" => [
        [
            "value" => 223415574,
            "shares" => 29396786,
            "ticker" => "ADT",
            "industry" => "",
            "company_name" => "ADT Inc",
            "filling_date" => "2024-04-10"
        ],
        [
            "value" => 7004905,
            "shares" => 921698,
            "ticker" => "ADT",
            "industry" => "",
            "company_name" => "ADT Inc",
            "filling_date" => "2024-06-30"
        ]
    ],
    "historic_holdings3" => [
        [
            "value" => 223415574,
            "shares" => 29396786,
            "ticker" => "ADT",
            "industry" => "",
            "company_name" => "ADT Inc",
            "filling_date" => "2024-07-10"
        ],
        [
            "value" => 7004905,
            "shares" => 921698,
            "ticker" => "ADT",
            "industry" => "",
            "company_name" => "ADT Inc",
            "filling_date" => "2024-09-15"
        ]
    ],
    "historic_holdings4" => [
        [
            "value" => 223415574,
            "shares" => 29396786,
            "ticker" => "ADT",
            "industry" => "",
            "company_name" => "ADT Inc",
            "filling_date" => "2024-10-10"
        ],
        [
            "value" => 7004905,
            "shares" => 921698,
            "ticker" => "ADT",
            "industry" => "",
            "company_name" => "ADT Inc",
            "filling_date" => "2024-12-10"
        ]
    ],
    "historic_holdings5" => [
        [
            "value" => 223415574,
            "shares" => 29396786,
            "ticker" => "ADT",
            "industry" => "",
            "company_name" => "ADT Inc",
            "filling_date" => "2024-07-10"
        ],
        [
            "value" => 7004905,
            "shares" => 921698,
            "ticker" => "ADT",
            "industry" => "",
            "company_name" => "ADT Inc",
            "filling_date" => "2024-09-15"
        ]
    ],
];

// Call the function to generate the dropdown HTML
$dropdown_html = generate_dropdown($api_response);

// Output the dropdown
echo $dropdown_html;
?>
