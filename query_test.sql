/* PORTFOLIO_DETAILS */
SELECT DISTINCT
    companies.symbol,
    companies.name,
    companies.sector,
    SUM(portfolio.amount) AS shares
FROM companies
         INNER JOIN portfolio ON portfolio.symbol = companies.symbol
WHERE portfolio.userId = 7
GROUP BY companies.symbol
ORDER BY companies.symbol;

/* DASHBOARD_DETAILS */
SELECT
    COUNT(DISTINCT portfolio.symbol) AS symbol,
    SUM(portfolio.amount) as shares
FROM portfolio
WHERE portfolio.userId = 7;

/* STOCK_VALUES */
SELECT
    SUM(shares * close) AS value
FROM (
         SELECT
             SUM(portfolio.amount) AS shares,
             history.close AS close
         FROM history
                  INNER JOIN portfolio ON portfolio.symbol = history.symbol
         WHERE portfolio.userId = 7 AND history.close = (SELECT history.close
                                                         FROM history
                                                         WHERE history.symbol = portfolio.symbol
                                                         ORDER BY history.date DESC
                                                         LIMIT 1)
         GROUP BY portfolio.symbol);