document.addEventListener("DOMContentLoaded", () => {
  const API_BASE_URL =
    "https://sectobsddjango-production.up.railway.app/api/company-pitches/";

  let currentPage = 1;
  let totalPages = 1;
  let isLoading = false;
  let currentFilters = {};

  const filterForm = document.getElementById("filterForm");
  const applyFiltersBtn = document.getElementById("applyFilters");
  const clearFiltersBtn = document.getElementById("clearFilters");
  const resetFiltersBtn = document.getElementById("resetFilters");
  const resultsGrid = document.getElementById("resultsGrid");
  const resultsCount = document.getElementById("resultsCount");
  const loadingIndicator = document.getElementById("loadingIndicator");
  const noResults = document.getElementById("noResults");
  const pagination = document.getElementById("pagination");
  const pageNumbers = document.getElementById("pageNumbers");
  const prevPageBtn = document.getElementById("prevPage");
  const nextPageBtn = document.getElementById("nextPage");

  fetchData();

  applyFiltersBtn.addEventListener("click", applyFilters);
  clearFiltersBtn.addEventListener("click", clearFilters);
  resetFiltersBtn.addEventListener("click", clearFilters);
  prevPageBtn.addEventListener("click", () => goToPage(currentPage - 1));
  nextPageBtn.addEventListener("click", () => goToPage(currentPage + 1));

  filterForm.addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      applyFilters();
    }
  });

  function applyFilters() {
    currentFilters = {};
    const formData = new FormData(filterForm);
    for (const [key, value] of formData.entries()) {
      if (value) {
        currentFilters[key] = value;
      }
    }
    currentPage = 1;
    fetchData();
  }

  function clearFilters() {
    filterForm.reset();
    currentFilters = {};
    currentPage = 1;
    fetchData();
  }

  function goToPage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    fetchData();
    const resultsSection = document.querySelector(".results-section");
    if (resultsSection) {
      resultsSection.scrollIntoView({ behavior: "smooth" });
    }
  }

  async function fetchData() {
    isLoading = true;
    updateUIState();

    try {
      let url = new URL(API_BASE_URL);
      url.searchParams.append("page", currentPage);
      Object.keys(currentFilters).forEach((key) => {
        url.searchParams.append(key, currentFilters[key]);
      });

      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();
      totalPages = Math.ceil(data.count / 8); // Show 8 cards per page
      renderResults(data);
      renderPagination();
      updateResultsCount(data.count);
    } catch (error) {
      console.error("Error fetching data:", error);
      resultsCount.textContent = "Error loading data. Please try again.";
      resultsCount.className =
        "text-red-600 bg-red-50 px-3 py-1 rounded-full font-medium";
    } finally {
      isLoading = false;
      updateUIState();
    }
  }

  function renderResults(data) {
    resultsGrid.innerHTML = "";
    if (data.results.length === 0) {
      noResults.classList.remove("hidden");
      return;
    }
    noResults.classList.add("hidden");
    data.results.forEach((item) => {
      const card = createCard(item);
      resultsGrid.appendChild(card);
    });
  }

  // function createCard(item) {
  //   const card = document.createElement("div");
  //   card.className =
  //     "bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 flex flex-col h-full";

  //   let description = (item.description || "No description available").replace(
  //     /\n/g,
  //     "<br>"
  //   );

  //   let ratingColor;
  //   const rating = parseFloat(item.rating);
  //   if (rating >= 8) {
  //     ratingColor = "bg-green-100 text-green-800";
  //   } else if (rating >= 5) {
  //     ratingColor = "bg-purple-100 text-purple-800";
  //   } else if (rating >= 3) {
  //     ratingColor = "bg-yellow-100 text-yellow-800";
  //   } else {
  //     ratingColor = "bg-red-100 text-red-800";
  //   }

  //   card.innerHTML = `
  //       <div class="p-5 flex-grow">
  //         <div class="flex justify-between items-start mb-3">
  //           <div>
  //             <div class="flex items-center">

  //               <h3 class="text-xl font-bold text-gray-800">${item.ticker}</h3>
  //               <span class="ml-2 ${ratingColor} text-xs font-semibold px-2.5 py-0.5 rounded">
  //                 ${item.rating}/10
  //               </span>
  //             </div>
  //             <h4 class="text-sm text-gray-600 line-clamp-1">${
  //               item.company_name || "Unknown Company"
  //             }</h4>
  //           </div>
  //           <span class="text-gray-400 text-xs font-medium px-2.5 py-0.5 rounded flex items-center">
  //             <i class="far fa-calendar-alt mr-1"></i>
  //             ${new Date(item.date).toLocaleDateString("en-US", {
  //               year: "numeric",
  //               month: "short",
  //               day: "numeric",
  //             })}
  //           </span>
  //         </div>
  //         <div class="grid grid-cols-2 gap-3 my-3">
  //           <div class="bg-[#0d3e6f]/40 rounded p-3 text-center">
  //             <p class="text-xs text-black mb-1">Market Cap</p>
  //             <p class="font-semibold text-black">$${item.marketcap}M</p>
  //           </div>
  //           <div class="bg-[#0d3e6f]/40 rounded p-3 text-center">
  //             <p class="text-xs text-black mb-1">Price</p>
  //             <p class="font-semibold text-black">$${item.price}</p>
  //           </div>
  //         </div>
  //         <p class="text-gray-600 text-sm mb-4">${description}</p>
  //       </div>
  //       <div class="px-5 py-3 bg-gray-50 border-t border-gray-200">
  //         <a href="${
  //           item.report_url
  //         }" target="_blank" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-[#0D3E6F]">
  //           Read Full Report
  //           <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
  //             <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
  //           </svg>
  //         </a>
  //       </div>
  //     `;
  //   return card;
  // }
  function createCard(item) {
    const card = document.createElement("div");
    card.className =
      "bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300 flex flex-col h-full relative";

    let description = (item.description || "No description available").replace(
      /\n/g,
      "<br>"
    );

    let ratingColor;
    const rating = parseFloat(item.rating);
    if (rating >= 8) {
      ratingColor = "bg-green-100 text-green-800";
    } else if (rating >= 5) {
      ratingColor = "bg-purple-100 text-purple-800";
    } else if (rating >= 3) {
      ratingColor = "bg-yellow-100 text-yellow-800";
    } else {
      ratingColor = "bg-red-100 text-red-800";
    }

    card.innerHTML = `
        <div class="p-5 flex-grow relative">
          <!-- Date at Top Right -->
          <span class="absolute top-3 right-3 text-gray-400 text-xs font-medium px-2.5 py-0.5 rounded flex items-center">
            <i class="fas fa-calendar-alt mr-1"></i>
            ${new Date(item.date).toLocaleDateString("en-US", {
              year: "numeric",
              month: "short",
              day: "numeric",
            })}
          </span>
  
          <!-- "Value Investor Club" Button -->
          <a href="https://valueinvestorsclub.com/ideas" target="_blank" 
             class="inline-block bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded mb-2 hover:bg-blue-700 transition">
            Value Investor Club
          </a>
  
          <!-- Ticker and Rating -->
          <div class="flex items-center">
            <h3 class="text-xl font-bold text-gray-800">${item.ticker}</h3>
            <span class="ml-2 ${ratingColor} text-xs font-semibold px-2.5 py-0.5 rounded">
              ${item.rating}/10
            </span>
          </div>
  
          <!-- Company Name -->
          <h4 class="text-sm text-gray-600 line-clamp-1">${
            item.company_name || "Unknown Company"
          }</h4>
  
          <!-- Market Cap & Price -->
          <div class="grid grid-cols-2 gap-3 my-3">
            <div class="bg-gray-100 rounded-xl px-3 py-5 text-center">
              <p class="text-xs text-black mb-1">Market Cap</p>
              <p class="font-semibold text-black">$${item.marketcap}M</p>
            </div>
            <div class="bg-gray-100 rounded-xl px-3 py-5 text-center">
              <p class="text-xs text-black mb-1">Price</p>
              <p class="font-semibold text-black">$${item.price}</p>
            </div>
          </div>
  
          <!-- Description -->
          <p class="text-gray-600 text-sm mb-4">${description}</p>
        </div>
  
        <!-- Read Full Report -->
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-200">
          <a href="${
            item.report_url
          }" target="_blank" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-[#0D3E6F]">
            Read Full Report
            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
          </a>
        </div>
      `;
    return card;
  }

  function renderPagination() {
    prevPageBtn.disabled = currentPage === 1;
    nextPageBtn.disabled = currentPage === totalPages;

    pageNumbers.innerHTML = "";
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);

    if (endPage - startPage < 4) {
      startPage = Math.max(1, endPage - 4);
    }

    if (startPage > 1) {
      addPageButton(1);
      if (startPage > 2) {
        addEllipsis();
      }
    }

    for (let i = startPage; i <= endPage; i++) {
      addPageButton(i);
    }

    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        addEllipsis();
      }
      addPageButton(totalPages);
    }
  }

  function addPageButton(pageNum) {
    const button = document.createElement("button");
    button.className =
      pageNum === currentPage
        ? "active bg-blue-700 text-white font-medium px-4 py-2 rounded-md border border-blue-700 transition-all duration-200"
        : "bg-white text-gray-800 font-medium px-4 py-2 rounded-md border border-gray-300 hover:bg-blue-50 hover:border-blue-700 transition-all duration-200";
    button.textContent = pageNum;
    button.addEventListener("click", () => goToPage(pageNum));
    pageNumbers.appendChild(button);
  }

  function addEllipsis() {
    const span = document.createElement("span");
    span.className = "ellipsis flex items-center justify-center text-gray-500";
    span.textContent = "•••";
    pageNumbers.appendChild(span);
  }

  function updateResultsCount(count) {
    resultsCount.textContent =
      count === 1 ? "1 result found" : `${count} results found`;
    resultsCount.className =
      "text-gray-600 bg-blue-50 px-3 py-1 rounded-full font-medium";
  }

  function updateUIState() {
    if (isLoading) {
      loadingIndicator.classList.remove("hidden");
      resultsGrid.classList.add("hidden");
      pagination.classList.add("hidden");
    } else {
      loadingIndicator.classList.add("hidden");
      resultsGrid.classList.remove("hidden");
      pagination.classList.remove("hidden");
    }
  }
});
