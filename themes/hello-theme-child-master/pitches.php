<?php
/*
Template Name: Pitches
*/
get_header(); ?>
<script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/pitch-style.css" />
<main class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
      <!-- Filter Section -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="mb-4">
          <h2 class="text-xl font-semibold text-gray-800">Filters</h2>
        </div>

        <form
          id="filterForm"
          class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4"
        >
          <!-- Ticker -->
          <div>
            <label
              for="ticker"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Ticker Symbol</label
            >
            <div class="relative">
              <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              >
                <i class="fas fa-tag text-gray-400"></i>
              </div>
              <input
                type="text"
                id="ticker"
                name="ticker"
                placeholder="e.g. AAPL"
                class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <!-- Company Name -->
          <div>
            <label
              for="company_name"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Company Name</label
            >
            <div class="relative">
              <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              >
                <i class="fas fa-building text-gray-400"></i>
              </div>
              <input
                type="text"
                id="company_name"
                name="company_name"
                placeholder="e.g. Apple Inc"
                class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <!-- Keyword -->
          <div>
            <label
              for="keyword"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Keyword</label
            >
            <div class="relative">
              <div
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
              >
                <i class="fas fa-search text-gray-400"></i>
              </div>
              <input
                type="text"
                id="keyword"
                name="keyword"
                placeholder="Search in descriptions"
                class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              />
            </div>
          </div>

          <!-- Date Range -->
          <div>
            <label
              for="date_range"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Date Range</label
            >
            <div class="grid grid-cols-2 gap-2">
              <div class="relative">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <i class="fas fa-calendar-alt text-gray-400"></i>
                </div>
                <input
                  type="date"
                  id="date_from"
                  name="date_from"
                  class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <div class="relative">
                <div
                  class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                  <i class="fas fa-calendar-alt text-gray-400"></i>
                </div>
                <input
                  type="date"
                  id="date_to"
                  name="date_to"
                  class="w-full pl-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>
          </div>

          <!-- Market Cap Range -->
          <div>
            <label
              for="marketcap_range"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Market Cap (millions $)</label
            >
            <div class="grid grid-cols-2 gap-2">
              <select
                id="min_marketcap"
                name="min_marketcap"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Min</option>
                <option value="100">$100M</option>
                <option value="500">$500M</option>
                <option value="1000">$1B</option>
                <option value="5000">$5B</option>
                <option value="10000">$10B</option>
                <option value="50000">$50B</option>
              </select>
              <select
                id="max_marketcap"
                name="max_marketcap"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Max</option>
                <option value="500">$500M</option>
                <option value="1000">$1B</option>
                <option value="5000">$5B</option>
                <option value="10000">$10B</option>
                <option value="50000">$50B</option>
                <option value="100000">$100B</option>
                <option value="1000000">$1T+</option>
              </select>
            </div>
          </div>

          <!-- Price Range -->
          <div>
            <label
              for="price_range"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Stock Price ($)</label
            >
            <div class="grid grid-cols-2 gap-2">
              <select
                id="min_price"
                name="min_price"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Min</option>
                <option value="1">$1</option>
                <option value="5">$5</option>
                <option value="10">$10</option>
                <option value="25">$25</option>
                <option value="50">$50</option>
                <option value="100">$100</option>
                <option value="250">$250</option>
              </select>
              <select
                id="max_price"
                name="max_price"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">Max</option>
                <option value="5">$5</option>
                <option value="10">$10</option>
                <option value="25">$25</option>
                <option value="50">$50</option>
                <option value="100">$100</option>
                <option value="250">$250</option>
                <option value="500">$500</option>
                <option value="1000">$1000+</option>
              </select>
            </div>
          </div>

          <!-- Rating -->
          <div>
            <label
              for="min_rating"
              class="block text-sm font-medium text-gray-700 mb-1"
              >Minimum Rating</label
            >
            <select
              id="min_rating"
              name="min_rating"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Any Rating</option>
              <option value="1">1+</option>
              <option value="2">2+</option>
              <option value="3">3+</option>
              <option value="4">4+</option>
              <option value="5">5+</option>
              <option value="6">6+</option>
              <option value="7">7+</option>
              <option value="8">8+</option>
              <option value="9">9+</option>
            </select>
          </div>
        </form>

        <!-- Buttons -->
        <div class="flex justify-start gap-3 mt-6">
          <button
            id="applyFilters"
            class="bg-[#0D3E6F] hover:bg-blue-800 text-white font-medium py-2 px-4 rounded-md transition duration-300"
          >
            Search
          </button>
          <button
            id="clearFilters"
            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-md transition duration-300"
          >
            Reset
          </button>
        </div>
      </div>

      <!-- Results Section -->
      <div class="mb-8">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-800">Results</h2>
          <div
            id="resultsCount"
            class="text-gray-600 bg-blue-50 px-3 py-1 rounded-full font-medium"
          >
            Loading...
          </div>
        </div>

        <!-- Loading Indicator -->
        <div
          id="loadingIndicator"
          class="flex flex-col items-center justify-center my-12"
        >
          <div
            class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-700"
          ></div>
          <p class="mt-4 text-gray-600">Loading results...</p>
        </div>

        <!-- Results Grid -->
        <div id="resultsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Cards will be dynamically inserted here -->
        </div>

        <!-- No Results Message -->
        <div
          id="noResults"
          class="hidden py-12 text-center bg-white rounded-lg shadow-md"
        >
          <div class="flex justify-center mb-4">
            <i class="fas fa-search text-gray-400 text-3xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-700 mb-2">
            No Results Found
          </h3>
          <p class="text-gray-600 mb-6">
            Try adjusting your filters or search terms to find more matches.
          </p>
          <button
            id="resetFilters"
            class="bg-[#0D3E6F] hover:bg-blue-800 text-white font-medium py-2 px-4 rounded-md transition duration-300"
          >
            Reset All Filters
          </button>
        </div>
      </div>

      <!-- Updated Pagination -->
      <div id="pagination" class="flex justify-center items-center my-8">
        <button
          id="prevPage"
          class="flex items-center justify-center w-8 h-8 rounded-full border border-gray-300 hover:bg-blue-50 hover:border-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
          aria-label="Previous page"
        >
          <i class="fas fa-chevron-left"></i>
        </button>
        <div id="pageNumbers" class="flex space-x-1 mx-2"></div>
        <button
          id="nextPage"
          class="flex items-center justify-center w-8 h-8 rounded-full border border-gray-300 hover:bg-blue-50 hover:border-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200"
          aria-label="Next page"
        >
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/pitch-script.js"></script>
</main>
<?php get_template_part('template-parts/landing/landing', 'weekly-articles'); ?>
<?php get_footer(); ?>