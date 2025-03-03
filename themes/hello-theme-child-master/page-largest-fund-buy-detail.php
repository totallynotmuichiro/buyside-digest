<?php
/*
Template Name: Largest Fund Buy Detail
*/

get_header();
?>
<section class="bsd-container">
    <div class="container px-4 py-6">
        <!-- Header Section -->
        <div class="flex items-center mb-4">
            <h1 id="tickerHeader" class="text-3xl font-bold primary-text mr-2"></h1>
            <h2 id="companyName" class="text-xl text-gray-600"></h2>
        </div>
        <div class="border-b-2 border-gray-200 mb-6"></div>

        <!-- Table Section -->
        <h3
            id="shareholdersHeader"
            class="text-xl font-bold mb-2 primary-text"></h3>
        <p id="shareholdersSubtext" class="text-sm text-gray-600 mb-4"></p>
        <div
            class="bg-white overflow-x-auto no-scrollbar shadow-md mb-8 table-container">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <!-- Table headers will be populated by JavaScript -->
                    </tr>
                </thead>
                <tbody
                    id="investorsTableBody"
                    class="bg-white divide-y divide-gray-200">
                    <!-- Table rows will be populated by JavaScript -->
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

        <!-- "Back to List" Button -->
        <div class="back-button-container">
            <a
                href="/largest-fund-buy"
                class="inline-block py-2 px-4 bg-[var(--primary-color)] text-white rounded hover:bg-[var(--secondary-color)]">
                &larr; Back to List
            </a>
        </div>
    </div>
</section>
<?php get_footer(); ?>