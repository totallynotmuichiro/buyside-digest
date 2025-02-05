let channels = bsdStatistics.channels

channels = channels.map( channel => channel.Name );
channels = channels.filter( channel => channel.includes('funds') || channel.includes('tickers'))
const dates = channels.map( channel => channel.replace(/\b(tickers|funds)\b/g, "").trim() );

flatpickr("#email-stats-date", {
  disable: [
    new Date(1000, 0, 1),  
    new Date(5000, 0, 1)
  ],
  enable: dates
});
