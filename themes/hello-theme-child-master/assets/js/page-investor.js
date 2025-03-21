// Treemap chart
document.addEventListener("DOMContentLoaded", async function () {
  if (!document.getElementById("investor-treemap")) {
    return;
  }
try{
  // Utility functions for color interpolation.
    function hexToRgb(hex) {
      hex = hex.replace(/^#/, '');
      if (hex.length === 3) {
        hex = hex.split('').map(h => h + h).join('');
      }
      const intVal = parseInt(hex, 16);
      return { r: (intVal >> 16) & 255, g: (intVal >> 8) & 255, b: intVal & 255 };
    }
    function rgbToHex(r, g, b) {
      return "#" + [r, g, b].map(x => {
        const hex = x.toString(16);
        return hex.length === 1 ? '0' + hex : hex;
      }).join('');
    }
    function interpolateColor(color1, color2, factor) {
      const c1 = hexToRgb(color1);
      const c2 = hexToRgb(color2);
      const r = Math.round(c1.r + factor * (c2.r - c1.r));
      const g = Math.round(c1.g + factor * (c2.g - c1.g));
      const b = Math.round(c1.b + factor * (c2.b - c1.b));
      return rgbToHex(r, g, b);
    }
    
    const url = window.location.href;
    const cleanUrl = url.split("?")[0].replace(/\/$/, "");
    let investorName = cleanUrl.split("/").pop();

    investorName = investorName
      .toLowerCase()
      .replace(/--/g, "/")
      .replace(/-/g, " ")
      .replace(/\//g, "-");

    // Fetch the data
    const response = await fetch(
  `https://sectobsddjango-production.up.railway.app/api/holdings/?investor_name=${investorName}`
);
const apiData = await response.json(); // Get API data directly

const holdings = apiData[0].holdings;

// Get decreased investment tickers
const decreasedInvestmentTickers = apiData[0].stock_movement_data.decreased_investment.map(item => item.ticker);

// Group holdings by industry.
const industriesMap = {};
holdings.forEach(h => {
  if (!industriesMap[h.industry]) {
    industriesMap[h.industry] = [];
  }
  industriesMap[h.industry].push(h);
});

        
        // Function to calculate leaf node color based on if it's in decreased investments
        function getLeafColor(ticker, wPct) {
          if (decreasedInvestmentTickers.includes(ticker)) {
            // If in decreased_investment, use red scale
            let ratio = Math.min(Math.abs(wPct), 5) / 5;
            ratio = Math.max(0, Math.min(1, ratio));
            return interpolateColor("#d7191c", "#d4696b", ratio);
          } else {
            // Otherwise use green scale
            let ratio = Math.min(wPct, 5) / 5;
            ratio = Math.max(0, Math.min(1, ratio));
            return interpolateColor("#3dcd6b", "#1a9641", ratio);
          }
        }
        
        // Build hierarchical data for ECharts.
        const chartData = Object.keys(industriesMap).map(industryName => {
          // Check if this industry has any decreased investment tickers
          const industryTickers = industriesMap[industryName].map(h => h.ticker);
          const hasDecreasedInvestment = industryTickers.some(ticker => decreasedInvestmentTickers.includes(ticker));
          
          return {
            name: industryName,
            children: industriesMap[industryName].map(h => ({
              name: h.ticker,
              value: h.weighting_pct,  // Use weighting_pct (or h.value) for area.
              wPct: h.weighting_pct,
              companyName: h.company_name,
              industry: h.industry,
              isDecreased: decreasedInvestmentTickers.includes(h.ticker),
              itemStyle: { color: getLeafColor(h.ticker, h.weighting_pct) }
            })),
            // Add a custom property to track if this industry has decreased investments
            hasDecreasedInvestment: hasDecreasedInvestment
          };
        });

        // Initialize ECharts instance.
        var chart = echarts.init(document.getElementById('investor-treemap'));
        var option = {
          tooltip: {
            trigger: 'item',
            formatter: function (info) {
              const treePath = info.treePathInfo;
              if (treePath.length === 1) {
                return "<strong>Industry: " + info.name + "</strong>";
              } else {
                const decreasedLabel = info.data.isDecreased ? 
                  "<span style='color:#d7191c'>⬇ DECREASED INVESTMENT</span><br/>" : "";
                return "<strong>" + info.data.companyName + "</strong><br/>" +
                       decreasedLabel +
                       "Ticker: " + info.data.name + "<br/>" +
                       "Industry: " + info.data.industry + "<br/>" +
                       "Weighting %: " + Number(info.data.wPct).toFixed(1) + "%";
              }
            }
          },
          series: [{
            name: 'Portfolio',
            type: 'treemap',
            // Allow both zooming and panning (dragging)
            roam: true,
            top: 0,
            left: 0,
            right: 0,
            bottom: 0,
            data: chartData,
            // upperLabel shows the industry header at the top of each industry block.
            upperLabel: {
              show: true,
              height: 20,
              color: '#ffffff',
              backgroundColor: '#0E3F70',  // Keep this fixed as it was originally
              formatter: '{b}'
            },
            label: {
              show: true,
              formatter: function (params) {
                // For ticker nodes: display ticker name and wPct with one decimal.
                if (params.treePathInfo.length > 1) {
                  return params.name + "\n" + Number(params.data.wPct).toFixed(1) + "%";
                }
                return '';
              },
              position: 'inside',
              textStyle: {
                fontSize: 16,
                fontWeight: 'bold',
                color: '#fff'
              }
            },
            itemStyle: {
              borderColor: '#fff',
              borderWidth: 1,
              gapWidth: 0  // No gap between cells.
            },
            levels: [
              {
                // Level 1: Industry nodes.
                itemStyle: {
                  borderColor: '#ccc',
                  borderWidth: 2,
                  gapWidth: 0,
                  color: function(params) {
                    // Change industry background color based on if it has decreased investments
                    return params.data.hasDecreasedInvestment ? '#ffe0e0' : '#eeeeee';
                  }
                },
                label: {
                  show: false  // Industry header is shown via upperLabel.
                }
              },
              {
                // Level 2: Ticker nodes.
                itemStyle: {
                  borderColor: '#fff',
                  borderWidth: 1,
                  gapWidth: 0
                }
              }
            ]
          }]
        };
        chart.setOption(option);
      }catch (error) {
    console.log("Error fetching data:", error);
  }
});
/*  try {
    // Get the investor name from the URL
    const url = window.location.href;
    const cleanUrl = url.split("?")[0].replace(/\/$/, "");
    let investorName = cleanUrl.split("/").pop();

    investorName = investorName
      .toLowerCase()
      .replace(/--/g, "/")
      .replace(/-/g, " ")
      .replace(/\//g, "-");

    // Fetch the data
    const response = await fetch(
      `https://sectobsddjango-production.up.railway.app/api/holdings/?investor_name=${investorName}`
    );
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
      const lightness =
        maxLightness - intensity * (maxLightness - minLightness);

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
          roam: false,
          nodeClick: false,
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
    console.log("Error in chart initialization:", error);
  }
});*/

// Pie chart
document.addEventListener("DOMContentLoaded", async function () {
  if (!document.getElementById("investor-pie")) {
    return;
  }
  try {
    const url = window.location.href;
    const cleanUrl = url.split("?")[0].replace(/\/$/, "");
    let investorName = cleanUrl.split("/").pop();
    investorName = investorName
      .toLowerCase()
      .replace(/--/g, "/")
      .replace(/-/g, " ")
      .replace(/\//g, "-");

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
            image:
              "https://ui-avatars.com/api/?name=" +
              investorName +
              "&background=0d3e6f&color=fff",
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
  }
});
