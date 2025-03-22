// const ITEMS_PER_PAGE = 10;
// let currentPage = 1;
// let aumData = [];
// let totalPages = 0;
// let nextPageUrl = null;

// // Base API URL
// const BASE_API_URL = `https://sectobsddjango-production.up.railway.app/api/aum-data/`;

// // DOM elements
// const elements = {
//   tableBody: () => document.getElementById("aumTableBody"),
//   pageStart: () => document.getElementById("pageStart"),
//   pageEnd: () => document.getElementById("pageEnd"),
//   totalItems: () => document.getElementById("totalItems"),
//   paginationContainer: () => document.getElementById("paginationContainer"),
// };

// // Helper function to show loading or error state
// const updateTableStatus = (message, isError = false) => {
//   elements.tableBody().innerHTML = `
//     <tr>
//       <td colspan="5" class="px-4 py-4 text-center ${
//         isError ? "text-red-500" : ""
//       }">
//         ${message}
//       </td>
//     </tr>
//   `;
// };

// // Format currency function
// const formatCurrency = (value) => {
//   if (value >= 1_000_000_000) {
//     return `$${(value / 1_000_000_000).toFixed(2)}B`;
//   } else if (value >= 1_000_000) {
//     return `$${(value / 1_000_000).toFixed(2)}M`;
//   } else if (value >= 1_000) {
//     return `$${(value / 1_000).toFixed(2)}K`;
//   } else {
//     return `$${value.toFixed(2)}`;
//   }
// };

// // Toggle industries display (show more/less)
// const toggleIndustriesDisplay = (fundId) => {
//   const container = document.getElementById(`industries-${fundId}`);
//   const hiddenContainer = document.getElementById(
//     `hidden-industries-${fundId}`
//   );
//   const expandBtn = document.getElementById(`expand-btn-${fundId}`);
//   const collapseBtn = document.getElementById(`collapse-btn-${fundId}`);

//   if (hiddenContainer.style.display === "block") {
//     // Collapse
//     hiddenContainer.style.display = "none";
//     expandBtn.style.display = "inline-flex";
//     collapseBtn.style.display = "none";
//   } else {
//     // Expand
//     hiddenContainer.style.display = "block";
//     expandBtn.style.display = "none";
//     collapseBtn.style.display = "inline-flex";
//   }
// };

// // Filter-related variables and DOM elements
// const filterElements = {
//   fundName: () => document.getElementById("fundNameFilter"),
//   fundSizeMin: () => document.getElementById("fundSizeMinSelect"),
//   fundSizeMax: () => document.getElementById("fundSizeMaxSelect"),
//   aum: () => document.getElementById("aumFilter"),
//   industry: () => document.getElementById("industryFilter"),
//   applyBtn: () => document.getElementById("applyFiltersBtn"),
//   clearBtn: () => document.getElementById("clearFiltersBtn"),
// };

// // Store original data for resetting filters
// let originalAumData = [];
// let filteredData = [];

// // Apply filters to the data
// const applyFilters = () => {
//   // Get filter values
//   const fundNameFilter = filterElements.fundName().value.toLowerCase();
//   const fundSizeMin = parseFloat(filterElements.fundSizeMin().value) || 0;
//   const fundSizeMax =
//     parseFloat(filterElements.fundSizeMax().value) || Number.MAX_SAFE_INTEGER;
//   const aumFilter = filterElements.aum().value;
//   const industryFilter = filterElements.industry().value.toLowerCase();

//   // Apply filters
//   filteredData = originalAumData.filter((fund) => {
//     // Fund name filter
//     const nameMatch = fund.fund_name.toLowerCase().includes(fundNameFilter);

//     // Fund size filter
//     const sizeMatch =
//       fund.fund_size >= fundSizeMin &&
//       (fundSizeMax === Number.MAX_SAFE_INTEGER ||
//         fund.fund_size <= fundSizeMax);

//     // AUM filter
//     let aumMatch = true;
//     if (aumFilter) {
//       switch (aumFilter) {
//         case "lt1":
//           aumMatch = fund.AUM < 1;
//           break;
//         case "1to2":
//           aumMatch = fund.AUM >= 1 && fund.AUM <= 2;
//           break;
//         case "2to5":
//           aumMatch = fund.AUM > 2 && fund.AUM <= 5;
//           break;
//         case "gt5":
//           aumMatch = fund.AUM > 5;
//           break;
//       }
//     }

//     // Industry filter
//     const industryMatch =
//       industryFilter === "" ||
//       fund.industries.some((industry) =>
//         industry.toLowerCase().includes(industryFilter)
//       );

//     return nameMatch && sizeMatch && aumMatch && industryMatch;
//   });

//   // Update the data and display
//   aumData = filteredData;

//   // Reset to first page
//   currentPage = 1;

//   // Calculate total pages
//   totalPages = Math.ceil(aumData.length / ITEMS_PER_PAGE);

//   // Update table and pagination
//   updateTable();
//   setupPagination();

//   // Show filter status
//   const count = aumData.length;
//   elements.totalItems().textContent = count;

//   // If no results, show a message
//   if (count === 0) {
//     updateTableStatus(
//       "No funds match your filter criteria. Try adjusting your filters."
//     );
//   }
// };

// // Clear all filters
// const clearFilters = () => {
//   // Reset filter input values
//   filterElements.fundName().value = "";
//   filterElements.fundSizeMin().value = "";
//   filterElements.fundSizeMax().value = "";
//   filterElements.aum().value = "";
//   filterElements.industry().value = "";

//   // Reset data to original
//   aumData = [...originalAumData];

//   // Reset page
//   currentPage = 1;

//   // Recalculate total pages
//   totalPages = Math.ceil(aumData.length / ITEMS_PER_PAGE);

//   // Update table and pagination
//   updateTable();
//   setupPagination();
// };

// // Initialize filter event listeners
// const initFilters = () => {
//   // Apply filters button
//   filterElements.applyBtn().addEventListener("click", applyFilters);

//   // Clear filters button
//   filterElements.clearBtn().addEventListener("click", clearFilters);

//   // Apply filters on Enter key for text inputs
//   filterElements.fundName().addEventListener("keypress", (e) => {
//     if (e.key === "Enter") applyFilters();
//   });

//   filterElements.industry().addEventListener("keypress", (e) => {
//     if (e.key === "Enter") applyFilters();
//   });

//   // Validate fund size selections (ensure min <= max)
//   filterElements.fundSizeMin().addEventListener("change", () => {
//     const minVal = parseFloat(filterElements.fundSizeMin().value) || 0;
//     const maxVal =
//       parseFloat(filterElements.fundSizeMax().value) || Number.MAX_SAFE_INTEGER;

//     if (minVal > maxVal && maxVal !== Number.MAX_SAFE_INTEGER) {
//       // Auto-adjust max to be at least equal to min
//       const maxSelect = filterElements.fundSizeMax();
//       for (let i = 0; i < maxSelect.options.length; i++) {
//         const optionValue =
//           parseFloat(maxSelect.options[i].value) || Number.MAX_SAFE_INTEGER;
//         if (optionValue >= minVal) {
//           maxSelect.selectedIndex = i;
//           break;
//         }
//       }
//     }
//   });
// };

// // Update the fetchAUMData function to store original data
// const fetchAUMData = async (url = BASE_API_URL) => {
//   try {
//     updateTableStatus("Loading fund data...");

//     const response = await fetch(url);

//     if (!response.ok) {
//       throw new Error(`HTTP error! Status: ${response.status}`);
//     }

//     const data = await response.json();

//     if (!data || data.length === 0) {
//       throw new Error("No fund data available");
//     }

//     // Store original data for filtering
//     originalAumData = [...data];

//     // Store all AUM data
//     aumData = data;

//     // Calculate total pages
//     totalPages = Math.ceil(aumData.length / ITEMS_PER_PAGE);

//     // Update total items
//     elements.totalItems().textContent = aumData.length;

//     // Update table and pagination
//     updateTable();
//     setupPagination();
//   } catch (error) {
//     console.error("Error fetching AUM data:", error);
//     updateTableStatus("Error loading data. Please try again later.", true);
//   }
// };

// // Update the DOM ready function to initialize filters
// document.addEventListener("DOMContentLoaded", () => {
//   // Initialize filters
//   initFilters();

//   // Fetch initial page of data
//   fetchAUMData();
// });
// // Fetch AUM data
// // const fetchAUMData = async (url = BASE_API_URL) => {
// //   try {
// //     updateTableStatus("Loading fund data...");

// //     const response = await fetch(url);

// //     if (!response.ok) {
// //       throw new Error(`HTTP error! Status: ${response.status}`);
// //     }

// //     const data = await response.json();

// //     if (!data || data.length === 0) {
// //       throw new Error("No fund data available");
// //     }

// //     // Store all AUM data
// //     aumData = data;

// //     // Calculate total pages (assuming API doesn't provide pagination)
// //     totalPages = Math.ceil(aumData.length / ITEMS_PER_PAGE);

// //     // Update total items
// //     elements.totalItems().textContent = aumData.length;

// //     // Update table and pagination
// //     updateTable();
// //     setupPagination();
// //   } catch (error) {
// //     console.error("Error fetching AUM data:", error);
// //     updateTableStatus("Error loading data. Please try again later.", true);
// //   }
// // };

// // Update table based on current page
// const updateTable = () => {
//   const tableBody = elements.tableBody();
//   tableBody.innerHTML = "";

//   // Calculate start and end indices for current page
//   const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
//   const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, aumData.length);

//   // Get current page data
//   const currentPageData = aumData.slice(startIndex, endIndex);

//   for (let i = 0; i < currentPageData.length; i++) {
//     const fund = currentPageData[i];
//     const fundId = `fund-${startIndex + i}`; // Create unique ID for each fund

//     const row = document.createElement("tr");
//     row.className = `hover:bg-gray-50 ${
//       i % 2 === 0 ? "bg-white" : "bg-gray-50"
//     }`;

//     // Fund Name Column
//     const fundNameCell = document.createElement("td");
//     fundNameCell.className = "px-4 py-3 whitespace-nowrap";
//     fundNameCell.innerHTML = `
//       <div class="font-medium text-sm" style="color: var(--primary-color)">${fund.fund_name}</div>
//     `;
//     row.appendChild(fundNameCell);

//     // Fund Size Column
//     const fundSizeCell = document.createElement("td");
//     fundSizeCell.className = "px-4 py-3 text-center";
//     fundSizeCell.innerHTML = `
//       <div class="text-sm currency">${formatCurrency(fund.fund_size)}</div>
//     `;
//     row.appendChild(fundSizeCell);

//     // New Stocks Column
//     const newStocksCell = document.createElement("td");
//     newStocksCell.className = "px-4 py-3 text-center";
//     newStocksCell.innerHTML = `
//       <div class="text-sm font-medium">${fund.total_new_stocks}</div>
//     `;
//     row.appendChild(newStocksCell);

//     // AUM Column
//     const aumCell = document.createElement("td");
//     aumCell.className = "px-4 py-3 text-center";
//     aumCell.innerHTML = `
//       <div class="text-sm font-medium">${fund.AUM.toFixed(2)}</div>
//     `;
//     row.appendChild(aumCell);

//     // Industries Column
//     const industriesCell = document.createElement("td");
//     industriesCell.className = "px-4 py-3";

//     // Filter out empty industries
//     const uniqueIndustries = fund.industries.filter(
//       (industry) => industry !== ""
//     );

//     // Split industries into visible and hidden
//     const visibleIndustries = uniqueIndustries.slice(0, 15);
//     const hiddenIndustries = uniqueIndustries.slice(15);

//     // Only show expand button if there are hidden industries
//     const showExpandButton = hiddenIndustries.length > 0;

//     // Create the industries display
//     // Industries Column
//     industriesCell.innerHTML = `
// <div class="text-sm text-left">
//   <!--
//     <div class="mb-2 text-left">
//       <span class="font-medium">${uniqueIndustries.length} industries</span>
//     </div>
//     -->

//   <div class="flex flex-wrap items-center">
//     ${visibleIndustries
//       .map((industry) => `<span class="industry-chip">${industry}</span>`)
//       .join("")}

//     ${
//       showExpandButton
//         ? `
//     <button id="expand-btn-${fundId}" class="text-[var(--primary-color)] opacity-60 hover:opacity-100 text-xs inline-flex items-center ml-1" onclick="toggleIndustriesDisplay('${fundId}')">
//        Show more
//     </button>
//     `
//         : ""
//     }

//     <div id="hidden-industries-${fundId}" style="display: none; width: 100%">
//       ${hiddenIndustries
//         .map((industry) => `<span class="industry-chip">${industry}</span>`)
//         .join("")}

//       <button id="collapse-btn-${fundId}" class="text-[var(--primary-color)] opacity-60 hover:opacity-100 text-xs inline-flex items-center ml-1" style="display: none;" onclick="toggleIndustriesDisplay('${fundId}')">
//         Show less
//       </button>
//     </div>
//   </div>
// </div>
// `;
//     row.appendChild(industriesCell);

//     tableBody.appendChild(row);
//   }

//   // Update pagination info
//   const start = startIndex + 1;
//   const end = endIndex;
//   elements.pageStart().textContent = start;
//   elements.pageEnd().textContent = end;
//   elements.totalItems().textContent = aumData.length;
// };

// // Set up pagination
// const setupPagination = () => {
//   const paginationContainer = elements.paginationContainer();
//   paginationContainer.innerHTML = "";

//   // Create pagination button helper function
//   const createPaginationButton = (
//     content,
//     isDisabled,
//     isActive,
//     onClick,
//     classes
//   ) => {
//     const button = document.createElement("button");
//     button.className = classes;
//     button.innerHTML = content;
//     button.disabled = isDisabled;

//     if (!isDisabled) {
//       button.addEventListener("click", onClick);
//     }

//     return button;
//   };

//   // Previous button
//   const prevButtonClass = `relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium ${
//     currentPage === 1
//       ? "text-gray-300 cursor-not-allowed"
//       : "text-gray-500 hover:bg-gray-50"
//   }`;

//   const prevButton = createPaginationButton(
//     '<span class="sr-only">Previous</span><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>',
//     currentPage === 1,
//     false,
//     () => {
//       if (currentPage > 1) {
//         currentPage--;
//         updateTable();
//         setupPagination();
//       }
//     },
//     prevButtonClass
//   );

//   paginationContainer.appendChild(prevButton);

//   // Page numbers
//   const maxVisiblePages = 5;
//   let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
//   let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

//   if (endPage - startPage + 1 < maxVisiblePages) {
//     startPage = Math.max(1, endPage - maxVisiblePages + 1);
//   }

//   for (let i = startPage; i <= endPage; i++) {
//     const isActive = i === currentPage;
//     const pageButtonClass = `relative inline-flex items-center px-4 py-2 border ${
//       isActive
//         ? "z-10 bg-[var(--primary-color)] border-[var(--primary-color)] text-white hover:bg-[var(--secondary-color)] hover:border-[var(--primary-color)]"
//         : "bg-white border-gray-300 text-gray-500 hover:bg-gray-50"
//     } text-sm font-medium`;

//     const pageButton = createPaginationButton(
//       i.toString(),
//       false,
//       isActive,
//       () => {
//         currentPage = i;
//         updateTable();
//         setupPagination();
//       },
//       pageButtonClass
//     );

//     paginationContainer.appendChild(pageButton);
//   }

//   // Next button
//   const nextButtonClass = `relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium ${
//     currentPage === totalPages
//       ? "text-gray-300 cursor-not-allowed"
//       : "text-gray-500 hover:bg-gray-50"
//   }`;

//   const nextButton = createPaginationButton(
//     '<span class="sr-only">Next</span><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>',
//     currentPage === totalPages,
//     false,
//     () => {
//       if (currentPage < totalPages) {
//         currentPage++;
//         updateTable();
//         setupPagination();
//       }
//     },
//     nextButtonClass
//   );

//   paginationContainer.appendChild(nextButton);
// };

// // Make the toggleIndustriesDisplay function globally available
// window.toggleIndustriesDisplay = toggleIndustriesDisplay;

// // Initialize on page load
// document.addEventListener("DOMContentLoaded", () => {
//   // Fetch initial page of data
//   fetchAUMData();
// });
const ITEMS_PER_PAGE = 20;
let currentPage = 1;
let aumData = [];
let totalPages = 0;
let nextPageUrl = null;

// Base API URL
const BASE_API_URL = `https://sectobsddjango-production.up.railway.app/api/aum-data/`;

// DOM elements
const elements = {
  tableBody: () => document.getElementById("aumTableBody"),
  pageStart: () => document.getElementById("pageStart"),
  pageEnd: () => document.getElementById("pageEnd"),
  totalItems: () => document.getElementById("totalItems"),
  paginationContainer: () => document.getElementById("paginationContainer"),
};

// Helper function to show loading or error state
const updateTableStatus = (message, isError = false) => {
  elements.tableBody().innerHTML = `
    <tr>
      <td colspan="7" class="px-4 py-4 text-center ${
        isError ? "text-red-500" : ""
      }">
        ${message}
      </td>
    </tr>
  `;
};

// Format currency function
const formatCurrency = (value) => {
  if (value >= 1_000_000_000) {
    return `$${(value / 1_000_000_000).toFixed(2)}B`;
  } else if (value >= 1_000_000) {
    return `$${(value / 1_000_000).toFixed(2)}M`;
  } else if (value >= 1_000) {
    return `$${(value / 1_000).toFixed(2)}K`;
  } else {
    return `$${value.toFixed(2)}`;
  }
};

// Format percentage function
const formatPercentage = (value) => {
  return `${value.toFixed(2)}%`;
};

// Filter-related variables and DOM elements
const filterElements = {
  investorName: () => document.getElementById("investorNameFilter"),
  company: () => document.getElementById("companyFilter"),
  totalValueMin: () => document.getElementById("totalValueMinSelect"),
  totalValueMax: () => document.getElementById("totalValueMaxSelect"),
  newStocksPercentage: () =>
    document.getElementById("newStocksPercentageSelect"),
  applyBtn: () => document.getElementById("applyFiltersBtn"),
  clearBtn: () => document.getElementById("clearFiltersBtn"),
};

// Store original data for resetting filters
let originalAumData = [];
let filteredData = [];

// Apply filters to the data
const applyFilters = () => {
  // Get filter values
  const investorNameFilter = filterElements.investorName().value.toLowerCase();
  const companyFilter = filterElements.company().value.toLowerCase();
  const totalValueMin = parseFloat(filterElements.totalValueMin().value) || 0;
  const totalValueMax =
    parseFloat(filterElements.totalValueMax().value) || Number.MAX_SAFE_INTEGER;
  const newStocksPercentageFilter = filterElements.newStocksPercentage().value;

  // Apply filters to original data
  filteredData = originalAumData.filter((item) => {
    // Investor name filter
    const nameMatch = item.investor_name
      .toLowerCase()
      .includes(investorNameFilter);

    // Company filter
    const companyMatch = item.company.toLowerCase().includes(companyFilter);

    // Total value filter
    const valueMatch =
      item.total_value >= totalValueMin &&
      (totalValueMax === Number.MAX_SAFE_INTEGER ||
        item.total_value <= totalValueMax);

    // New stocks percentage filter
    let percentageMatch = true;
    if (newStocksPercentageFilter) {
      const newStocksPercentage = item.new_stocks_percentage;
      switch (newStocksPercentageFilter) {
        case "0-5":
          percentageMatch = newStocksPercentage >= 0 && newStocksPercentage < 5;
          break;
        case "5-10":
          percentageMatch =
            newStocksPercentage >= 5 && newStocksPercentage < 10;
          break;
        case "10-15":
          percentageMatch =
            newStocksPercentage >= 10 && newStocksPercentage < 15;
          break;
        case "15-20":
          percentageMatch =
            newStocksPercentage >= 15 && newStocksPercentage < 20;
          break;
        case "20+":
          percentageMatch = newStocksPercentage >= 20;
          break;
      }
    }

    return nameMatch && companyMatch && valueMatch && percentageMatch;
  });

  // Update the data and display
  aumData = [...filteredData]; // Create a fresh copy to avoid reference issues

  // Reset to first page
  currentPage = 1;

  // Calculate total pages
  totalPages = Math.ceil(aumData.length / ITEMS_PER_PAGE);

  // Update table and pagination
  updateTable();
  setupPagination();

  // Show filter status
  const count = aumData.length;
  elements.totalItems().textContent = count;

  // If no results, show a message
  if (count === 0) {
    updateTableStatus(
      "No data matches your filter criteria. Try adjusting your filters."
    );
  }
};

// Clear all filters
const clearFilters = () => {
  // Reset filter input values
  filterElements.investorName().value = "";
  filterElements.company().value = "";
  filterElements.totalValueMin().value = "";
  filterElements.totalValueMax().value = "";
  filterElements.newStocksPercentage().value = "";

  // Reset data to original
  aumData = [...originalAumData];

  // Reset page
  currentPage = 1;

  // Recalculate total pages
  totalPages = Math.ceil(aumData.length / ITEMS_PER_PAGE);

  // Update table and pagination
  updateTable();
  setupPagination();
};

// Initialize filter event listeners
const initFilters = () => {
  // Apply filters button
  filterElements.applyBtn().addEventListener("click", applyFilters);

  // Clear filters button
  filterElements.clearBtn().addEventListener("click", clearFilters);

  // Apply filters on Enter key for text inputs
  filterElements.investorName().addEventListener("keypress", (e) => {
    if (e.key === "Enter") applyFilters();
  });

  filterElements.company().addEventListener("keypress", (e) => {
    if (e.key === "Enter") applyFilters();
  });

  // Validate total value selections (ensure min <= max)
  filterElements.totalValueMin().addEventListener("change", () => {
    const minVal = parseFloat(filterElements.totalValueMin().value) || 0;
    const maxVal =
      parseFloat(filterElements.totalValueMax().value) ||
      Number.MAX_SAFE_INTEGER;

    if (minVal > maxVal && maxVal !== Number.MAX_SAFE_INTEGER) {
      // Auto-adjust max to be at least equal to min
      const maxSelect = filterElements.totalValueMax();
      for (let i = 0; i < maxSelect.options.length; i++) {
        const optionValue =
          parseFloat(maxSelect.options[i].value) || Number.MAX_SAFE_INTEGER;
        if (optionValue >= minVal) {
          maxSelect.selectedIndex = i;
          break;
        }
      }
    }
  });
};

// Update the fetchAUMData function to store original data
const fetchAUMData = async (url = BASE_API_URL) => {
  try {
    updateTableStatus("Loading data...");

    const response = await fetch(url);

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    const data = await response.json();

    if (!data || data.length === 0) {
      throw new Error("No data available");
    }

    // Store original data for filtering
    originalAumData = [...data];

    // Store all AUM data
    aumData = [...data]; // Create a fresh copy to avoid reference issues

    // Calculate total pages
    totalPages = Math.ceil(aumData.length / ITEMS_PER_PAGE);

    // Update total items
    elements.totalItems().textContent = aumData.length;

    // Update table and pagination
    updateTable();
    setupPagination();
  } catch (error) {
    console.error("Error fetching data:", error);
    updateTableStatus("Error loading data. Please try again later.", true);
  }
};

// Helper function to format investor name for URL
const formatInvestorNameForUrl = (name) => {
  return name.toLowerCase().replace(/\s+/g, "-");
};

// Update table based on current page
const updateTable = () => {
  const tableBody = elements.tableBody();
  tableBody.innerHTML = "";

  // Calculate start and end indices for current page
  const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
  const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, aumData.length);

  // Get current page data
  const currentPageData = aumData.slice(startIndex, endIndex);

  // If no data to display
  if (currentPageData.length === 0) {
    updateTableStatus("No matching data found.");
    return;
  }

  for (let i = 0; i < currentPageData.length; i++) {
    const item = currentPageData[i];

    const row = document.createElement("tr");
    row.className = `hover:bg-gray-50 ${
      i % 2 === 0 ? "bg-white" : "bg-gray-50"
    }`;

    // Investor Name Column
    const investorNameCell = document.createElement("td");
    investorNameCell.className = "px-2 py-2 whitespace-nowrap";

    // Format the investor name for URL
    const formattedInvestorName = formatInvestorNameForUrl(item.investor_name);

    investorNameCell.innerHTML = `
      <div class="font-medium text-sm">
        <a href="https://www.buysidedigest.com/investor/${formattedInvestorName}/" 
           class="text-[var(--primary-color)] hover:underline cursor-pointer">${item.investor_name}</a>
      </div>
    `;
    row.appendChild(investorNameCell);

    // Company Column
    const companyCell = document.createElement("td");
    companyCell.className = "px-2 py-2";
    companyCell.innerHTML = `
      <div class="text-sm">${item.company}</div>
    `;
    row.appendChild(companyCell);

    // Total Value Column
    const totalValueCell = document.createElement("td");
    totalValueCell.className = "px-2 py-2 text-center";
    totalValueCell.innerHTML = `
      <div class="text-sm currency">${formatCurrency(item.total_value)}</div>
    `;
    row.appendChild(totalValueCell);

    // New Stocks Value Column
    const newStocksValueCell = document.createElement("td");
    newStocksValueCell.className = "px-2 py-2 text-center";
    newStocksValueCell.innerHTML = `
      <div class="text-sm currency">${formatCurrency(
        item.new_stocks_value
      )}</div>
    `;
    row.appendChild(newStocksValueCell);

    // New Stocks Percentage Column
    const newStocksPercentageCell = document.createElement("td");
    newStocksPercentageCell.className = "px-2 py-2 text-center";
    newStocksPercentageCell.innerHTML = `
      <div class="text-sm font-medium">${formatPercentage(
        item.new_stocks_percentage
      )}</div>
    `;
    row.appendChild(newStocksPercentageCell);

    // Increased Stocks Value Column
    const increasedStocksValueCell = document.createElement("td");
    increasedStocksValueCell.className = "px-2 py-2 text-center";
    increasedStocksValueCell.innerHTML = `
      <div class="text-sm currency">${formatCurrency(
        item.increased_stocks_value
      )}</div>
    `;
    row.appendChild(increasedStocksValueCell);

    // Increased Stocks Percentage Column
    const increasedStocksPercentageCell = document.createElement("td");
    increasedStocksPercentageCell.className = "px-2 py-2 text-center";
    increasedStocksPercentageCell.innerHTML = `
      <div class="text-sm font-medium">${formatPercentage(
        item.increased_stocks_percentage
      )}</div>
    `;
    row.appendChild(increasedStocksPercentageCell);

    tableBody.appendChild(row);
  }

  // Update pagination info
  const start = startIndex + 1;
  const end = endIndex;
  elements.pageStart().textContent = start;
  elements.pageEnd().textContent = end;
  elements.totalItems().textContent = aumData.length;
};

// Set up pagination
const setupPagination = () => {
  const paginationContainer = elements.paginationContainer();
  paginationContainer.innerHTML = "";

  // Hide pagination if no data or only one page
  if (aumData.length === 0 || totalPages <= 1) {
    paginationContainer.style.display = "none";
    return;
  } else {
    paginationContainer.style.display = "flex";
  }

  // Create pagination button helper function
  const createPaginationButton = (
    content,
    isDisabled,
    isActive,
    onClick,
    classes
  ) => {
    const button = document.createElement("button");
    button.className = classes;
    button.innerHTML = content;
    button.disabled = isDisabled;

    if (!isDisabled) {
      button.addEventListener("click", onClick);
    }

    return button;
  };

  // Previous button
  const prevButtonClass = `relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium ${
    currentPage === 1
      ? "text-gray-300 cursor-not-allowed"
      : "text-gray-500 hover:bg-gray-50"
  }`;

  const prevButton = createPaginationButton(
    '<span class="sr-only">Previous</span><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>',
    currentPage === 1,
    false,
    () => {
      if (currentPage > 1) {
        currentPage--;
        updateTable();
        setupPagination();
      }
    },
    prevButtonClass
  );

  paginationContainer.appendChild(prevButton);

  // Page numbers
  const maxVisiblePages = 5;
  let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
  let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

  if (endPage - startPage + 1 < maxVisiblePages) {
    startPage = Math.max(1, endPage - maxVisiblePages + 1);
  }

  for (let i = startPage; i <= endPage; i++) {
    const isActive = i === currentPage;
    const pageButtonClass = `relative inline-flex items-center px-4 py-2 border ${
      isActive
        ? "z-10 bg-[var(--primary-color)] border-[var(--primary-color)] text-white hover:bg-[var(--secondary-color)] hover:border-[var(--primary-color)]"
        : "bg-white border-gray-300 text-gray-500 hover:bg-gray-50"
    } text-sm font-medium`;

    const pageButton = createPaginationButton(
      i.toString(),
      false,
      isActive,
      () => {
        currentPage = i;
        updateTable();
        setupPagination();
      },
      pageButtonClass
    );

    paginationContainer.appendChild(pageButton);
  }

  // Next button
  const nextButtonClass = `relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium ${
    currentPage === totalPages
      ? "text-gray-300 cursor-not-allowed"
      : "text-gray-500 hover:bg-gray-50"
  }`;

  const nextButton = createPaginationButton(
    '<span class="sr-only">Next</span><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>',
    currentPage === totalPages,
    false,
    () => {
      if (currentPage < totalPages) {
        currentPage++;
        updateTable();
        setupPagination();
      }
    },
    nextButtonClass
  );

  paginationContainer.appendChild(nextButton);
};

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
  // Initialize filters
  initFilters();

  // Fetch initial page of data
  fetchAUMData();
});
