<?php
/*
Template Name: Largest Fund Buy
*/

get_header();
?>
<section class="bsd-container">
    <div class="container px-4 py-6">
        <!-- Header Section -->
        <h1 class="text-3xl font-bold mb-4 primary-text">Largest Fund Buys</h1>
        <div class="border-b-2 border-gray-200 mb-6"></div>
        <!-- Border below title -->

        <!-- Table Section -->
        <div class="bg-white overflow-x-auto no-scrollbar shadow-md">
            <table class="w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <!-- Table headers will be populated by JavaScript -->
                    </tr>
                </thead>
                <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Table rows will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div
            class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
            <div
                class="flex flex-col sm:flex-row items-center justify-between w-full">
                <div>
                    <p class="text-sm text-gray-700" id="paginationInfo">
                        Showing <span id="pageStart">1</span> to
                        <span id="pageEnd">10</span> of
                        <span id="totalItems">0</span> results
                    </p>
                </div>
                <div class="mt-5 sm:mt-0">
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
</section>

<?php get_footer(); ?>