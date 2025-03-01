// Treemap chart
document.addEventListener("DOMContentLoaded", async function () {
  try {
    // Get the investor name from the URL
    const url = window.location.href;
    const cleanUrl = url.split("?")[0].replace(/\/$/, "");
    let investorName = cleanUrl.split("/").pop();

    // Convert dashes to spaces
    investorName = investorName.replace(/-/g, " ");

    // Fetch the data
    const response = await fetch(`https://sectobsddjango-production.up.railway.app/api/holdings/?investor_name=${investorName}`);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const data = await response.json();

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
      const maxChange = 20; // Maximum value for scaling
      const normalizedChange = Math.min(Math.abs(sharesChange) / maxChange, 1);
      
      // Exponential scaling for better contrast control
      const intensity = Math.pow(normalizedChange, 1.2); 
      
      // Define the color range using HSL
      const hue = sharesChange > 0 ? 120 : 0; // Green (120°) or Red (0°)
      const saturation = 85; // Keep saturation high for vibrant colors
      const minLightness = 35; // Prevent colors from being too dark
      const maxLightness = 80; // Prevent colors from being too light
      const lightness = maxLightness - intensity * (maxLightness - minLightness); 
    
      return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
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
      title: {
        text: `${investorName
          .charAt(0)
          .toUpperCase()
          .concat(investorName.slice(1))}'s Holdings Heat Map`,
        left: "left",
        top: "top",
        textStyle: {
          fontSize: 18,
          fontWeight: "bold",
        },
      },
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
          roam: true,
          breadcrumb: { show: true },
          label: { show: true, formatter: "{b}", overflow: "truncate" },
          upperLabel: { show: true, height: 30 },
          itemStyle: { borderWidth: 1, gapWidth: 2 },
          levels: [
            {},
            {
              itemStyle: {
                borderWidth: 5,
                gapWidth: 10,
                borderColor: "#000000",
              },
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
                  return [
                    `{a|${info.name}}`,
                    `{b|${info.data.change.toFixed(2)}%}`,
                  ].join("\n");
                },
                rich: {
                  a: {
                    color: "#fff",
                    fontSize: 16,
                    fontWeight: 500,
                    padding: [0, 0, 5, 0],
                  },
                  b: { color: "#fff", fontSize: 14, fontWeight: 300 },
                },
                padding: 10,
              },
            },
          ],
        },
      ],
    };

    // Get and render the chart
    const chartContainer = document.getElementById("investor-treemap");
    const treeMapChart = echarts.init(chartContainer);
    treeMapChart.resize();
    treeMapChart.setOption(chartConfig);
    window.addEventListener("resize", () => treeMapChart.resize());
  } catch (error) {
    console.error("Error in chart initialization:", error);
  }
});

// Pie chart
document.addEventListener("DOMContentLoaded", async function () {
  try {
    const url = window.location.href;
    const cleanUrl = url.split("?")[0].replace(/\/$/, "");
    let investorName = cleanUrl.split("/").pop();
    investorName = investorName.replace(/-/g, " ");

    const response = await fetch(`https://sectobsddjango-production.up.railway.app/api/holdings/?investor_name=${investorName}`);
    const data = await response.json();

    const totalValue = data.reduce((acc, investor) => {
      if (!investor || !investor.holdings) return acc;

      return (
        acc +
        investor.holdings.reduce((sum, holding) => {
          return sum + (holding?.value ?? 0);
        }, 0)
      );
    }, 0);

    const industries = data
      ?.map((eachData) =>
        eachData?.holdings?.map((holding) => holding?.industry)
      )
      .flat();

    const allIndustries = new Set(industries);
    const chartData = [];

    for (const eachIndustries of allIndustries) {
      const totalValueEachIndustries = data?.reduce((acc, investor) => {
        return (
          acc +
          investor.holdings.reduce((sum, holding) => {
            if (eachIndustries === holding?.industry) {
              return sum + (holding?.value ?? 0);
            } else {
              return sum + 0;
            }
          }, 0)
        );
      }, 0);

      chartData.push({
        value: (totalValueEachIndustries / totalValue) * 100,
        name: eachIndustries,
      });
    }

    var dom = document.getElementById("investor-pie");
    var myChart = echarts.init(dom);

    const option = {
      title: {
        text: "Portfolio Data: 2024-12-01",
        left: "left",
        textStyle: {
          fontSize: 18,
          fontWeight: "bold",
        },
      },
      tooltip: {
        trigger: "item",
        formatter: "{b}: {c} ({d}%)",
      },
      series: [
        {
          name: "Portfolio Allocation",
          type: "pie",
          radius: ["40%", "70%"],
          center: ["50%", "50%"],
          avoidLabelOverlap: false,
          label: {
            show: true,
            position: "outside",
            formatter: "{b}\n{d}%",
          },
          data: chartData.map((item) => {
            return {
              ...item,
              percent:
                (item.value / chartData.reduce((sum, i) => sum + i.value, 0)) *
                100,
            };
          }),
        },
      ],
      graphic: [
        {
          type: "image",
          style: {
            image: "https://ui-avatars.com/api/?name=" + investorName  + "&background=0d3e6f&color=fff",
            width: 80,
            height: 80,
          },
          clipPath: {
            type: "circle",
            shape: {
              cx: 40,
              cy: 40,
              r: 40,
            },
          },
          left: "center",
          top: "center",
        },
      ],
    };

    if (option && typeof option === "object") {
      myChart.setOption(option);
    }

    window.addEventListener("resize", myChart.resize);
  } catch (error) {
    console.log("Error in chart initialization:", error);
    alert("Error While fetching data");
  }
});
