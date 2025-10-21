<?php
// $_SERVER (PHP_SELF) - https://www.php.net/manual/en/reserved.variables.server.php
// is_null - https://www.php.net/manual/en/function.is-null.php

require_once "include/header.php";
require_once "include/database.php";
require_once "include/api.php";

const STOCKS_DATABASE = new StocksDatabase("data/stocks.db");

if (isset($_GET[QUERY_STRING]))
{
    define("SELECTED_USER_ID", $_GET[QUERY_STRING]);
}
else
{
    define("SELECTED_USER_ID", null);
}

$companies = StocksDatabase::TABLE_COMPANIES;
$history = StocksDatabase::TABLE_HISTORY;
$portfolio = StocksDatabase::TABLE_PORTFOLIO;

$sql = "SELECT";
$sql .= "\n\nSUM(shares * close) AS value";
$sql .= "\nFROM ("; // brings in share and close results
$sql .= "\n\nSELECT";
$sql .= "\n\n\nSUM($portfolio.amount) AS shares,"; // prevents duplication
$sql .= "\n\n\n$history.close AS close";
$sql .= "\n\nFROM $history";
$sql .= "\n\nINNER JOIN $portfolio ON $portfolio.symbol = $history.symbol"; // brings in close results
$sql .= "\n\nWHERE $portfolio.userId = :id AND $history.close = ("; // match with the newest close results for every company
$sql .= "\n\n\nSELECT";
$sql .= "\n\n\n\n$history.close";
$sql .= "\n\n\nFROM $history";
$sql .= "\n\n\nWHERE $history.symbol = $portfolio.symbol";
$sql .= "\n\n\nORDER BY $history.date DESC"; // sort by newest
$sql .= "\n\n\nLIMIT 1)"; // returns the newest close result
$sql .= "\n\nGROUP BY $portfolio.symbol)";

query_by_user($sql);
define("STOCK_VALUES", STOCKS_DATABASE->fetch_all());

$sql = "SELECT DISTINCT";
$sql .= "\n\n$companies.symbol,";
$sql .= "\n\n$companies.name,";
$sql .= "\n\n$companies.sector,";
$sql .= "\n\nSUM($portfolio.amount) AS shares";
$sql .= "\nFROM $companies";
$sql .= "\nINNER JOIN $portfolio ON $portfolio.symbol = $companies.symbol";
$sql .= "\nWHERE $portfolio.userId = :id";
$sql .= "\nGROUP BY $companies.symbol";
$sql .= "\nORDER BY $companies.symbol";

query_by_user($sql);
define("PORTFOLIO_DETAILS", STOCKS_DATABASE->fetch_all());

$sql = "SELECT";
$sql .= "\n\nCOUNT(DISTINCT $portfolio.symbol) AS symbol,";
$sql .= "\n\nSUM(portfolio.amount) AS shares";
$sql .= "\nFROM $portfolio";
$sql .= "\nWHERE $portfolio.userId = :id";

query_by_user($sql);
define("DASHBOARD_DETAILS", STOCKS_DATABASE->fetch_all()[0]);

function query_users():void
{
    $sql = "SELECT";
    $sql .= "\n\nid, firstname, lastname";
    $sql .= "\nFROM " . StocksDatabase::TABLE_USERS;

    STOCKS_DATABASE->prepare($sql);
    STOCKS_DATABASE->execute();   
}

function query_by_user(string $query): void
{
    STOCKS_DATABASE->prepare($query);
    STOCKS_DATABASE->bind(":id", SELECTED_USER_ID);
    STOCKS_DATABASE->execute();
}

function compute_stock_value(string $symbol): float
{
    $history = StocksDatabase::TABLE_HISTORY;

    $query = "SELECT";
    $query .= "\n\n$history.close";
    $query .= "\nFROM $history";
    $query .= "\nWHERE $history.symbol = :symbol";
    $query .= "\nORDER BY $history.date DESC"; // sort by newest
    $query .= "\nLIMIT 1"; // returns the newest close result

    STOCKS_DATABASE->prepare($query);
    STOCKS_DATABASE->bind(":symbol", $symbol);
    STOCKS_DATABASE->execute();
    
    return STOCKS_DATABASE->fetch_once("close");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Company</title>

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/home.css">
</head>
<body>
    <?php
    render_header();
    ?>
    <main>
        <article>
            <section class="customer-list neat-shadow">
                <h2 class="customer-list-title">Customers</h2>
                <ul class="customer-list-contents">
                    <?php
                    query_users();
                    
                    while ($customer = STOCKS_DATABASE->fetch()) {
                        $name = $customer['lastname'] . ", " . $customer['firstname'];
                        $id = $customer["id"];
                        
                        echo "<li><a href='" . $_SERVER["PHP_SELF"] . "?ref=$id'>$name</a></li>";
                    }
                    ?>
                </ul>
            </section>
            <section class="customer-portfolio">
                <?php
                if (is_null(SELECTED_USER_ID)) {
                    echo "<h1 class='customer-portfolio-empty neat-shadow'>Please select a customer</h1>";
                    return;
                }
                ?>
                <div class="dashboard customer-portfolio-dashboard">
                    <div>
                        <h1>COMPANIES</h1>
                        <h1>
                            <?php
                            echo DASHBOARD_DETAILS["symbol"];
                            ?>
                        </h1>
                    </div>
                    <div>
                        <h1>TOTAL SHARES</h1>
                        <h1>
                            <?php
                            echo DASHBOARD_DETAILS["shares"];
                            ?>
                        </h1>
                    </div>
                    <div>
                        <h1>TOTAL VALUE</h1>
                        <h1 class="format-accounting">
                            <?php
                            $total_value = 0;

                            foreach (STOCK_VALUES as $row)
                            {
                                $total_value += $row["value"];
                            }
                            
                            echo format($total_value);
                            ?>
                        </h1>
                    </div>
                </div>
                <div class="table customer-portfolio-details">
                    <h1 class="table-title">Portfolio Details</h1>
                    <div class="table-data">
                        <div class="table-data-primary">
                            <h2>SYMBOL</h2>
                            <ul>
                                <?php
                                foreach (PORTFOLIO_DETAILS as $row)
                                {
                                    $symbol = $row["symbol"];

                                    echo "<li><a href='company.php?ref=$symbol'>$symbol</a></li>";
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="table-data-other customer-portfolio-data-other">
                            <div>
                                <h2>NAME</h2>
                                <ul>
                                    <?php
                                    foreach (PORTFOLIO_DETAILS as $row)
                                    {
                                        $name = $row["name"];

                                        echo "<li>$name</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div>
                                <h2>SECTOR</h2>
                                <ul>
                                    <?php
                                    foreach (PORTFOLIO_DETAILS as $row)
                                    {
                                        $name = $row["sector"];

                                        echo "<li>$name</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div>
                                <h2>SHARES</h2>
                                <ul>
                                    <?php
                                    foreach (PORTFOLIO_DETAILS as $row)
                                    {
                                        $shares = $row["shares"];

                                        echo "<li>$shares</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div class="format-accounting-list">
                                <h2>VALUE</h2>
                                <ul>
                                    <?php
                                    foreach (PORTFOLIO_DETAILS as $row)
                                    {
                                        $symbol = $row["symbol"];
                                        $shares = $row["shares"];

                                        $close = compute_stock_value($symbol);
                                        $value = $shares * $close;
                                        $value = format($value);

                                        echo "<li>$value</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </article>
    </main>
</body>
</html>
<?php
STOCKS_DATABASE->close();