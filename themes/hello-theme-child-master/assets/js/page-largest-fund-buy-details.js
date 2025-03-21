const ITEMS_PER_PAGE = 10;
let currentPage = 1;
let investors = [];
let currentSort = { column: "value", direction: "desc" }; // Default sort

// Get ticker from URL
const urlParams = new URLSearchParams(window.location.search);
const ticker = urlParams.get("ticker");

// API URL
const API_URL = `https://sectobsddjango-production.up.railway.app/api/company-investors/${ticker}/`;

// Utility functions
const getInitials = (name) => {
  if (!name) return "";

  const nameParts = name.split(" ");
  return nameParts.length <= 1
    ? name.substring(0, 2).toUpperCase()
    : (
        nameParts[0].charAt(0) + nameParts[nameParts.length - 1].charAt(0)
      ).toUpperCase();
};

const formatNumber = (value, prefix = "") => {
  if (value === null || value === undefined) return "N/A";

  const absValue = Math.abs(value);

  if (absValue >= 1e9) {
    const formattedValue = value / 1e9;
    return `${prefix}${
      Number.isInteger(formattedValue)
        ? formattedValue.toFixed(0)
        : formattedValue.toFixed(2)
    }B`;
  } else if (absValue >= 1e6) {
    const formattedValue = value / 1e6;
    return `${prefix}${
      Number.isInteger(formattedValue)
        ? formattedValue.toFixed(0)
        : formattedValue.toFixed(2)
    }M`;
  } else if (absValue >= 1e3) {
    const formattedValue = value / 1e3;
    return `${prefix}${
      Number.isInteger(formattedValue)
        ? formattedValue.toFixed(0)
        : formattedValue.toFixed(2)
    }K`;
  }

  return `${prefix}${
    Number.isInteger(value) ? value.toFixed(0) : value.toFixed(2)
  }`;
};

const formatCurrency = (value) => formatNumber(value, "$ ");
const formatShares = formatNumber;
const formatMarketCap = formatNumber;

const formatPercentage = (value) => {
  if (value === 0 || value === null || value === undefined) {
    return '<span class="text-gray-500">0.00%</span>';
  }

  const formattedValue = value.toFixed(2);
  const isPositive = value > 0;

  return `<span class="text-${isPositive ? "green" : "red"}-600">${
    isPositive ? "+" : ""
  }${formattedValue}%</span>`;
};

// DOM elements
const bsd_elements = {
  tableBody: () => document.getElementById("investorsTableBody"),
  pageStart: () => document.getElementById("pageStart"),
  pageEnd: () => document.getElementById("pageEnd"),
  totalItems: () => document.getElementById("totalItems"),
  theadRow: () => document.querySelector("thead tr"),
  paginationContainer: () => document.getElementById("paginationContainer"),
  tickerHeader: () => document.getElementById("tickerHeader"),
  companyName: () => document.getElementById("companyName"),
  shareholdersHeader: () => document.getElementById("shareholdersHeader"),
  shareholdersSubtext: () => document.getElementById("shareholdersSubtext"),
};

// Sort investors based on current sort settings
const sortInvestors = () => {
  if (!currentSort.column) return;

  const getValueForSorting = (investor, column) => {
    const details = investor.investment_details;

    switch (column) {
      case "name":
        return (investor.investor_name || "").toLowerCase();
      case "weight":
        return details.weighting_pct || 0;
      case "value":
        return investor.total_investment_value || 0;
      case "shares":
        return details.shares || 0;
      case "marketCap":
        return details.market_cap_millions || 0;
      case "tradeImpact":
        return details.trade_impact_pct || 0;
      case "outstanding":
        return details.shares_outstanding_pct || 0;
      case "threeMonth":
        return details.three_month_change_pct || 0;
      case "ytd":
        return details.ytd_change_pct || 0;
      default:
        return 0;
    }
  };

  investors.sort((a, b) => {
    const valueA = getValueForSorting(a, currentSort.column);
    const valueB = getValueForSorting(b, currentSort.column);

    // Handle string comparison
    if (typeof valueA === "string") {
      const comparison = valueA.localeCompare(valueB);
      return currentSort.direction === "asc" ? comparison : -comparison;
    }

    // Handle number comparison
    const comparison = valueA - valueB;
    return currentSort.direction === "asc" ? comparison : -comparison;
  });
};

// Update table based on current page
const updateTable = () => {
  const tableBody = bsd_elements.tableBody();
  tableBody.innerHTML = "";

  const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
  const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, investors.length);

  // Update pagination info
  bsd_elements.pageStart().textContent = startIndex + 1;
  bsd_elements.pageEnd().textContent = endIndex;
  bsd_elements.totalItems().textContent = investors.length;

  // Create table rows
  for (let i = startIndex; i < endIndex; i++) {
    const investor = investors[i];
    const details = investor.investment_details;
    const formattedName = investor.investor_name
      .toLowerCase()
      .replace(/-/g, '--')
      .replace(/\s+/g, '-');

    const row = document.createElement("tr");
    row.className = `hover:bg-gray-50 ${
      i % 2 === 0 ? "bg-white" : "bg-gray-50"
    }`;

    row.innerHTML = `
      <td class="px-4 py-3 whitespace-nowrap">
        <div class="flex items-center">
          <div class="flex-shrink-0 h-10 w-10 mr-3">
            <div class="h-full w-full rounded-full bg-[var(--primary-color)] flex items-center justify-center text-white font-medium">
              ${getInitials(investor.investor_name)}
            </div>
          </div>
          <div class="font-medium text-sm primary-text">
          <a href="/investor/${formattedName}" 
           class="primary-text hover:underline font-medium text-sm">
          ${
            investor.investor_name
          }
        </a></div>
        </div>
      </td>
      <td class="px-4 py-3 whitespace-nowrap text-sm">
        ${
          details.weighting_pct ? details.weighting_pct.toFixed(2) + "%" : "N/A"
        }
      </td>
      <td class="px-4 py-3 whitespace-nowrap text-sm">
        ${formatCurrency(investor.total_investment_value)}
      </td>
      <td class="px-4 py-3 whitespace-nowrap text-sm">
        ${formatShares(details.shares)}
      </td>
      <td class="px-4 py-3 whitespace-nowrap text-sm">
        ${formatMarketCap(details.market_cap_millions)}
      </td>
      <td class="px-4 py-3 whitespace-nowrap text-sm">
        ${formatPercentage(details.trade_impact_pct)}
      </td>
      <td class="px-4 py-3 whitespace-nowrap text-sm">
        ${
          details.shares_outstanding_pct
            ? details.shares_outstanding_pct.toFixed(2) + "%"
            : "N/A"
        }
      </td>
      <td class="px-4 py-3 whitespace-nowrap text-sm">
        ${formatPercentage(details.three_month_change_pct)}
      </td>
      <td class="px-4 py-3 whitespace-nowrap text-sm">
        ${formatPercentage(details.ytd_change_pct)}
      </td>
    `;

    tableBody.appendChild(row);
  }
};

// Set up sorting headers
const setupSortHeaders = () => {
  const headers = [
    { id: "name", text: "Fund/Manager Name", column: "name" },
    { id: "weight", text: "Portfolio Weight", column: "weight" },
    { id: "value", text: "Investment Value", column: "value" },
    { id: "shares", text: "Shares Owned", column: "shares" },
    { id: "marketCap", text: "Market Cap", column: "marketCap" },
    { id: "tradeImpact", text: "Trade Impact", column: "tradeImpact" },
    { id: "outstanding", text: "Shares Outstanding", column: "outstanding" },
    { id: "threeMonth", text: "3-Month Change", column: "threeMonth" },
    { id: "ytd", text: "YTD Change", column: "ytd" },
  ];

  const theadRow = bsd_elements.theadRow();
  theadRow.innerHTML = "";

  // Create sortable headers
  headers.forEach((header) => {
    const th = document.createElement("th");
    th.className =
      "px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer";

    // Create content with sort arrows
    const headerContent = document.createElement("div");
    headerContent.className = "flex items-center space-x-1";
    headerContent.innerHTML = `
      <span>${header.text}</span>
      <span class="sort-arrows inline-flex flex-col">
        <svg class="w-3 h-3 ${
          currentSort.column === header.column &&
          currentSort.direction === "asc"
            ? "text-blue-500"
            : "text-gray-400"
        }" 
             fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
        </svg>
        <svg class="w-3 h-3 ${
          currentSort.column === header.column &&
          currentSort.direction === "desc"
            ? "text-blue-500"
            : "text-gray-400"
        }" 
             fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
      </span>
    `;

    th.appendChild(headerContent);

    // Add click event for sorting
    th.addEventListener("click", () => {
      if (currentSort.column === header.column) {
        // Toggle direction if same column
        currentSort.direction =
          currentSort.direction === "asc" ? "desc" : "asc";
      } else {
        // Set new column and default to ascending
        currentSort.column = header.column;
        currentSort.direction = "asc";
      }

      // Sort and update table
      sortInvestors();
      updateTable();
      setupSortHeaders();
    });

    theadRow.appendChild(th);
  });
};

// Set up pagination
const setupPagination = () => {
  const totalPages = Math.ceil(investors.length / ITEMS_PER_PAGE);
  const paginationContainer = bsd_elements.paginationContainer();
  paginationContainer.innerHTML = "";

  // Create pagination button
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
      currentPage--;
      updateTable();
      setupPagination();
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
      currentPage++;
      updateTable();
      setupPagination();
    },
    nextButtonClass
  );

  paginationContainer.appendChild(nextButton);
};

// Helper function to show loading or error state
const updateTableStatus = (message, isError = false) => {
  bsd_elements.tableBody().innerHTML = `
    <tr>
      <td colspan="9" class="px-4 py-4 text-center ${
        isError ? "text-red-500" : ""
      }">
        ${message}
      </td>
    </tr>
  `;
};

// Fetch and display data
const fetchInvestorData = async () => {
  try {
    // Set page title and loading state
    document.title = `Loading ${ticker} Details...`;
    bsd_elements.tickerHeader().textContent = ticker;
    bsd_elements.companyName().textContent = "Loading...";
    bsd_elements.shareholdersHeader().textContent = `Shareholders of ${ticker}`;
    bsd_elements.shareholdersSubtext().textContent = `Showing popular funds holding ${ticker}`;
    updateTableStatus("Loading investor data...");

    const response = await fetch(API_URL);

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    const data = await response.json();

    if (!data.investors || data.investors.length === 0) {
      throw new Error("No investor data available");
    }

    // Update company information (using first investor's data)
    const companyInfo = data.investors[0].investment_details;
    document.title = `${ticker}: ${companyInfo.company_name} - Investor Details`;
    bsd_elements.tickerHeader().textContent = ticker;
    bsd_elements.companyName().textContent = companyInfo.company_name;

    // Store investors and initialize table
    investors = data.investors;
    sortInvestors();
    setupSortHeaders();
    updateTable();
    setupPagination();
  } catch (error) {
    console.error("Error fetching investor data:", error);
    updateTableStatus("Error loading data. Please try again later.", true);
  }
};

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
  if (!ticker) {
    bsd_elements.tickerHeader().textContent = "Error";
    bsd_elements.companyName().textContent = "No ticker provided";
    updateTableStatus(
      "No ticker symbol provided. Please go back and select a company.",
      true
    );
    return;
  }

  fetchInvestorData();
});