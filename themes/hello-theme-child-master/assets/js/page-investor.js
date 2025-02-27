document.addEventListener("DOMContentLoaded", async function () {
  // Get the investor name from the URL
  const url = window.location.href;
  const cleanUrl = url.split("?")[0].replace(/\/$/, "");
  let investorName = cleanUrl.split("/").pop();

  // Convert dashes to spaces
  investorName = investorName.replace(/-/g, " ");

  // Fetch the data
  const response = await fetch(`/wp-content/uploads/data.json`);
  if (!response.ok) {
    throw new Error(`HTTP error! status: ${response.status}`);
  }
  const data = await response.json();

  // Log the data
  let holdings = data[0].holdings;

  // Total value of the portfolio
  const totalValue = holdings.reduce((acc, holding) => {
    return acc + holding.value;
  }, 0);

  // Keep only value, industry, company_name, share_change_pct, market_cap_millions
  holdings = holdings.map((holding) => {
    return {
      value: holding.value,
      industry: holding.industry,
      companyName: holding.company_name,
      ticker: holding.ticker,
      sharesChange: holding.shares_change_pct,
      marketCap: holding.market_cap_millions,
      percentageOfPortfolio: (holding.value / totalValue) * 100,
    };
  });

  // Group by industry
  const holdingsGroupedByIndustry = holdings.reduce((acc, holding) => {
    const industry = holding.industry;
    if (!acc[industry]) {
      acc[industry] = [];
    }
    acc[industry].push(holding);
    return acc;
  }, {});

  // Function to get color as per share change
  const getColorForChange = (sharesChange) => {
    const maxChange = 5;
    const saturation = Math.min(Math.abs(sharesChange) / maxChange, 1);
    const alpha = 0.3 + saturation * 0.7;
    return sharesChange > 0
      ? `rgba(77, 175, 74, ${alpha})`
      : `rgba(228, 26, 28, ${alpha})`;
  };

  // Convert the data to the format required by echarts
  const chartData = {
    name: "Financial Services",
    children: [],
  };

  for (const industry in holdingsGroupedByIndustry) {
    const industryHoldings = holdingsGroupedByIndustry[industry];

    // Calculate the change for the industry
    const industryChange = industryHoldings.reduce((acc, holding) => {
      return acc + holding.sharesChange;
    }, 0);

    // Create the industry item
    const industryItem = {
      name: industry,
      children: [],
      change: industryChange / industryHoldings.length,
      itemStyle: {
        color: getColorForChange(industryChange),
        borderColor: getColorForChange(industryChange),
      },
    };

    // Add the companies
    for (const holding of industryHoldings) {
      const companyItem = {
        name: holding.ticker,
        value: holding.percentageOfPortfolio,
        change: holding.sharesChange,
        itemStyle: {
          color: getColorForChange(holding.sharesChange),
          borderColor: getColorForChange(holding.sharesChange),
        },
      };
      industryItem.children.push(companyItem);
    }

    chartData.children.push(industryItem);
  }

  // Chart config
  const chartConfig = {
    tooltip: {
      formatter: (info) => {
        const value = info.value ? `${info.value.toFixed(2)}%` : "";
        const change =
          info.data.change !== undefined
            ? `${info.data.change.toFixed(2)}%`
            : "N/A";
        return `${info.name}<br>Value: ${value}<br>Change: ${change}`;
      },
    },
    series: [
      {
        type: "treemap",
        data: [chartData],
        roam: false,
        breadcrumb: { show: true },
        label: { show: true, formatter: "{b}",  overflow: "truncate" },
        upperLabel: { show: true, height: 30 },
        itemStyle: { borderWidth: 1, gapWidth: 2 },
        levels: [
          {},
          {
            itemStyle: { borderWidth: 5, gapWidth: 10, borderColor: "#000000" },
            upperLabel: {
              show: true,
              textBorderWidth: 0,
              textBorderColor: "transparent",
              color: "#fff",
            },
          },
          {
            upperLabel: {
              show: true,
              textBorderWidth: 0,
              textBorderColor: "transparent",
              color: "#fff",
            },
            itemStyle: { borderWidth: 5, gapWidth: 1 },
          },
          {
            label: {
              formatter: (info) => {
                return [`{a|${info.name}}`, `{b|${info.data.change.toFixed(2)}%}`].join(
                  "\n"
                );
              },
              rich: {
                a: {
                  color: "#fff",
                  fontSize: 25,
                  fontWeight: 500,
                  padding: [0, 0, 5, 0],
                },
                b: { color: "#fff", fontSize:15, fontWeight: 300 },
              },
              padding: 10,
            },
          },
        ],
      },
    ],
  };

  // Get and render the chart
  const chartContainer = document.getElementById("main");
  const treeMapChart = echarts.init(chartContainer);
  treeMapChart.resize();
  treeMapChart.setOption(chartConfig);
  window.addEventListener("resize", () => treeMapChart.resize());
});
