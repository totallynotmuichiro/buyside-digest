const API_URL =
  "https://sectobsddjango-production.up.railway.app/api/company-investment-data/";
const ITEMS_PER_PAGE = 6;
let currentPage = 1;
let companies = [];
let currentSort = { column: "investment", direction: "desc" }; // Default sort by investment value descending

// Format currency function
function formatCurrency(value) {
  // Convert to billions for display
  const billions = value / 1000000000;
  return `$ ${billions.toFixed(2)}B`;
}

// Fetch data from API
async function fetchData() {
  // Show loading state
  document.getElementById("tableBody").innerHTML = `
      <tr>
        <td colspan="4" class="px-3 py-1 text-center text-gray-500">
          Loading data, please wait...
        </td>
      </tr>
    `;

  try {
    const response = await fetch(API_URL);

    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }

    const data = await response.json();
    companies = data.companies;

    // Initial sort by total investment value (descending)
    sortCompanies();

    // Initialize table, headers, and pagination
    setupSortHeaders();
    updateTable();
    setupPagination();
  } catch (error) {
    console.error("Error fetching data:", error);
    document.getElementById("tableBody").innerHTML = `
        <tr>
          <td colspan="4" class="px-3 py-1 text-center text-red-500">
            Error loading data. Please try again later.
          </td>
        </tr>
      `;
  }
}

// Sort companies based on current sort settings
function sortCompanies() {
  companies.sort((a, b) => {
    let valueA, valueB;

    switch (currentSort.column) {
      case "ticker":
        valueA = a.tickers || "";
        valueB = b.tickers || "";
        break;
      case "company":
        valueA = a.company_name || "";
        valueB = b.company_name || "";
        break;
      case "investment":
      default:
        valueA = a.total_investment_value || 0;
        valueB = b.total_investment_value || 0;
        break;
    }

    // Handle string comparison
    if (typeof valueA === "string") {
      const comparison = valueA.localeCompare(valueB);
      return currentSort.direction === "asc" ? comparison : -comparison;
    }

    // Handle number comparison
    return currentSort.direction === "asc" ? valueA - valueB : valueB - valueA;
  });
}

// Create sort arrow SVGs with appropriate highlighting based on current sort
function createSortArrows(column) {
  return `
    <span class="sort-arrows inline-flex flex-col">
      <svg class="w-3 h-3 ${
        currentSort.column === column && currentSort.direction === "asc"
          ? "text-blue-500"
          : "text-gray-400"
      }" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
      </svg>
      <svg class="w-3 h-3 ${
        currentSort.column === column && currentSort.direction === "desc"
          ? "text-blue-500"
          : "text-gray-400"
      }" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
      </svg>
    </span>
  `;
}

// Set up sortable headers
function setupSortHeaders() {
  const headers = [
    { id: "ticker", text: "Stock", column: "ticker" },
    { id: "company", text: "Company Name", column: "company" },
    { id: "investment", text: "Total Value Bought", column: "investment" },
    { id: "funds", text: "Top 3 Funds Buying", column: null }, // Not sortable
  ];

  // Find the thead row
  const theadRow = document.querySelector("thead tr");
  theadRow.innerHTML = "";

  // Create sortable headers
  headers.forEach((header) => {
    const th = document.createElement("th");
    th.className =
      "px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider";

    if (header.column) {
      th.className += " cursor-pointer";

      // Create content with sort arrows
      const headerContent = document.createElement("div");
      headerContent.className = "flex items-center space-x-1";
      headerContent.innerHTML = `
        <span>${header.text}</span>
        ${header.column ? createSortArrows(header.column) : ""}
      `;

      th.appendChild(headerContent);

      // Add click event for sorting
      th.addEventListener("click", () => {
        if (!header.column) return; // Skip if not sortable

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
        sortCompanies();
        updateTable();
        setupSortHeaders(); // Refresh headers to update arrow colors
      });
    } else {
      // Non-sortable header
      th.textContent = header.text;
    }

    theadRow.appendChild(th);
  });
}

// Update table based on current page
function updateTable() {
  const tableBody = document.getElementById("tableBody");
  tableBody.innerHTML = "";

  const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
  const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, companies.length);

  // Update pagination info
  document.getElementById("pageStart").textContent = startIndex + 1;
  document.getElementById("pageEnd").textContent = endIndex;
  document.getElementById("totalItems").textContent = companies.length;

  // Create table rows
  for (let i = startIndex; i < endIndex; i++) {
    const company = companies[i];
    const isEven = i % 2 === 0;

    // Create top investors HTML
    const topInvestorsHtml = company.top_investors
      .map((investor) => {
        const investorValue = formatCurrency(investor.holding_value);
        return `<div class="mb-2">
             <div>${investor.investor_name} - <span class="font-medium">${investorValue}</span></div>
           </div>`;
      })
      .join("");

    const row = document.createElement("tr");
    row.className = isEven ? "bg-white" : "bg-gray-50"; // Alternating row bg

    row.innerHTML = `
      <td class="px-3 py-1 whitespace-nowrap">
        <a href="/largest-fund-buy-detail/?ticker=${company.tickers}" 
           class="primary-text hover:underline font-medium text-sm"
           target="_blank">
          ${company.tickers}
        </a>
      </td>
      <td class="px-3 py-1 whitespace-nowrap text-sm">
        ${company.company_name}
      </td>
      <td class="px-3 py-1 whitespace-nowrap font-medium text-sm">
        ${formatCurrency(company.total_investment_value)}
      </td>
      <td class="px-3 py-1 whitespace-nowrap text-sm">
        ${topInvestorsHtml}
      </td>
    `;

    tableBody.appendChild(row);
  }
}

// Create pagination button with appropriate styling
function createPaginationButton(type, content, isDisabled, clickHandler) {
  const button = document.createElement("button");

  if (type === "prev" || type === "next") {
    // Navigation button (prev/next)
    button.className = `relative inline-flex items-center px-2 py-2 ${
      type === "prev" ? "rounded-l-md" : "rounded-r-md"
    } border border-gray-300 bg-white text-sm font-medium ${
      isDisabled
        ? "text-gray-300 cursor-not-allowed"
        : "text-gray-500 hover:bg-gray-50"
    }`;
    button.innerHTML = content;
    button.disabled = isDisabled;
  } else {
    // Page number button
    const isActive = typeof content === "number" && content === currentPage;
    button.className = `relative inline-flex items-center px-4 py-2 border ${
      isActive
        ? "z-10 bg-[var(--primary-color)] border-[var(--primary-color)] text-white hover:bg-[var(--secondary-color)] hover:border-[var(--primary-color)]"
        : "bg-white border-gray-300 text-gray-500 hover:bg-gray-50"
    } text-sm font-medium`;
    button.textContent = content;
  }

  button.addEventListener("click", clickHandler);
  return button;
}

// Set up pagination
function setupPagination() {
  const totalPages = Math.ceil(companies.length / ITEMS_PER_PAGE);
  const paginationContainer = document.getElementById("paginationContainer");
  paginationContainer.innerHTML = "";

  // Previous button
  const prevButtonContent =
    '<span class="sr-only">Previous</span><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
  const prevButton = createPaginationButton(
    "prev",
    prevButtonContent,
    currentPage === 1,
    () => {
      if (currentPage > 1) {
        currentPage--;
        updateTable();
        setupPagination();
      }
    }
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
    const pageButton = createPaginationButton("page", i, false, () => {
      currentPage = i;
      updateTable();
      setupPagination();
    });
    paginationContainer.appendChild(pageButton);
  }

  // Next button
  const nextButtonContent =
    '<span class="sr-only">Next</span><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>';
  const nextButton = createPaginationButton(
    "next",
    nextButtonContent,
    currentPage === totalPages,
    () => {
      if (currentPage < totalPages) {
        currentPage++;
        updateTable();
        setupPagination();
      }
    }
  );
  paginationContainer.appendChild(nextButton);
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
  fetchData();
});
