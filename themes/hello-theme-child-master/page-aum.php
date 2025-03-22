<?php
/*
Template Name: AUM
*/

get_header();
?>
<script src="https://cdn.tailwindcss.com"></script>
<div class="container mx-auto py-6 lg:px-4 lg:py-6">
    <!-- Header Section -->
    <div class="flex mb-4">
        <h1
            id="headerTitle"
            class="text-2xl lg:text-3xl font-bold"
            style="color: var(--primary-color)">
            BSD Biggest Bets During the Period
        </h1>
    </div>
    <div class="border-b-2 border-gray-200 mb-6"></div>

    <!-- Filters Section -->
    <div class="bg-white shadow-lg rounded-md p-4 mb-10">
        <h3
            class="text-lg font-semibold mb-3"
            style="color: var(--primary-color)">
            Filters
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Investor Name Filter -->
            <div class="mb-2">
                <label
                    for="investorNameFilter"
                    class="block text-sm font-medium text-gray-700 mb-1">Manager Name</label>
                <input
                    type="text"
                    id="investorNameFilter"
                    placeholder="Search by manager name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] focus:border-transparent placeholder:text-base" />
            </div>

            <!-- Company Filter -->
            <div class="mb-2">
                <label
                    for="companyFilter"
                    class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                <input
                    type="text"
                    id="companyFilter"
                    placeholder="Search by company"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] focus:border-transparent placeholder:text-base" />
            </div>

            <!-- Total Value Range Filter (Dropdown) -->
            <div class="mb-2">
                <label
                    for="totalValueFilter"
                    class="block text-sm font-medium text-gray-700 mb-1">Total Value</label>
                <div class="flex space-x-2">
                    <select
                        id="totalValueMinSelect"
                        class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] focus:border-transparent">
                        <option value="">Min Value</option>
                        <option value="1000000">$1M</option>
                        <option value="10000000">$10M</option>
                        <option value="50000000">$50M</option>
                        <option value="100000000">$100M</option>
                        <option value="500000000">$500M</option>
                        <option value="1000000000">$1B</option>
                        <option value="5000000000">$5B</option>
                        <option value="10000000000">$10B</option>
                    </select>
                    <select
                        id="totalValueMaxSelect"
                        class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] focus:border-transparent">
                        <option value="">Max Value</option>
                        <option value="10000000">$10M</option>
                        <option value="50000000">$50M</option>
                        <option value="100000000">$100M</option>
                        <option value="500000000">$500M</option>
                        <option value="1000000000">$1B</option>
                        <option value="5000000000">$5B</option>
                        <option value="10000000000">$10B</option>
                        <option value="50000000000">$50B</option>
                    </select>
                </div>
            </div>

            <!-- New Stocks Percentage Filter -->
            <div class="mb-2">
                <label
                    for="newStocksPercentageFilter"
                    class="block text-sm font-medium text-gray-700 mb-1">New Stocks %</label>
                <div class="flex space-x-2">
                    <select
                        id="newStocksPercentageSelect"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[var(--primary-color)] focus:border-transparent">
                        <option value="">Any</option>
                        <option value="0-5">0-5%</option>
                        <option value="5-10">5-10%</option>
                        <option value="10-15">10-15%</option>
                        <option value="15-20">15-20%</option>
                        <option value="20+">20%+</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Filter Buttons -->
        <div class="flex justify-start mt-4">
            <button
                id="applyFiltersBtn"
                class="px-4 py-2 mr-2 text-sm font-medium text-white rounded-md"
                style="background-color: var(--primary-color)">
                Search
            </button>

            <button
                id="clearFiltersBtn"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-100 focus:outline-none">
                Reset
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div
        class="bg-white overflow-x-auto no-scrollbar shadow-lg rounded-md mb-8 table-container">
        <table class="w-full divide-y divide-gray-200 no-left-align">
            <thead style="background-color: var(--primary-color); opacity: 0.8">
                <tr>
                    <th
                        class="px-2 py-3 text-left text-xs font-medium text-white uppercase tracking-wider sticky-header">
                        Manager Name
                    </th>
                    <th
                        class="px-2 py-3 text-left text-xs font-medium text-white uppercase tracking-wider sticky-header">
                        Company
                    </th>
                    <th
                        class="px-2 py-3 text-center text-xs font-medium text-white uppercase tracking-wider sticky-header">
                        Total Value
                    </th>
                    <th
                        class="px-2 py-3 text-center text-xs font-medium text-white uppercase tracking-wider sticky-header">
                        New Stocks Value
                    </th>
                    <th
                        class="px-2 py-3 text-center text-xs font-medium text-white uppercase tracking-wider sticky-header">
                        New Stocks %
                    </th>
                    <th
                        class="px-2 py-3 text-center text-xs font-medium text-white uppercase tracking-wider sticky-header">
                        Increased Stocks Value
                    </th>
                    <th
                        class="px-2 py-3 text-center text-xs font-medium text-white uppercase tracking-wider sticky-header">
                        Increased Stocks %
                    </th>
                </tr>
            </thead>
            <tbody id="aumTableBody" class="bg-white divide-y divide-gray-200">
                <!-- Table rows will be populated by JavaScript -->
                <tr>
                    <td colspan="7" class="px-4 py-4 text-center">Loading data...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    <div
        class="px-6 py-3 flex items-center justify-between border-t border-gray-200 pagination-container">
        <div
            class="flex flex-col sm:flex-row items-center justify-between w-full">
            <div>
                <p class="text-sm text-gray-700" id="paginationInfo">
                    Showing <span id="pageStart">1</span> to
                    <span id="pageEnd">10</span> of
                    <span id="totalItems">0</span> results
                </p>
            </div>
            <div class="mt-5">
                <nav
                    class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                    aria-label="Pagination"
                    id="paginationContainer">
                    <!-- Pagination buttons will be populated by JavaScript -->
                </nav>
            </div>
        </div>
    </div>
</div>