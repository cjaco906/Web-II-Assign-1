<?php
// $_SERVER (PHP_SELF) - https://www.php.net/manual/en/reserved.variables.server.php

require_once "include/header.php";
require_once "include/footer.php";
require_once "include/database.php";
require_once "include/ui.php";

const DATABASE = new StocksDatabase();

define("SELECTED_CUSTOMER_ID", 7);

$companies = StocksDatabase::TABLE_COMPANIES;
$history = StocksDatabase::TABLE_HISTORY;
$portfolio = StocksDatabase::TABLE_PORTFOLIO;

$query = "SELECT";
$query .= "\n\nSUM(shares * close) AS value";
$query .= "\nFROM ("; // brings in share and close results
$query .= "\n\nSELECT";
$query .= "\n\n\nSUM($portfolio.amount) AS shares,"; // prevents duplication
$query .= "\n\n\n$history.close AS close";
$query .= "\n\nFROM $history";
$query .= "\n\nINNER JOIN $portfolio ON $portfolio.symbol = $history.symbol"; // brings in close results
$query .= "\n\nWHERE $portfolio.userId = :id AND $history.close = ("; // match with the newest close results for every company
$query .= "\n\n\nSELECT";
$query .= "\n\n\n\n$history.close";
$query .= "\n\n\nFROM $history";
$query .= "\n\n\nWHERE $history.symbol = $portfolio.symbol";
$query .= "\n\n\nORDER BY $history.date DESC"; // sort by newest
$query .= "\n\n\nLIMIT 1)"; // returns the newest close result
$query .= "\n\nGROUP BY $portfolio.symbol)";

query_by_customer($query);
define("STOCK_VALUES", DATABASE->fetch_all());

$query = "SELECT DISTINCT";
$query .= "\n\n$companies.symbol,";
$query .= "\n\n$companies.name,";
$query .= "\n\n$companies.sector,";
$query .= "\n\nSUM($portfolio.amount) AS shares";
$query .= "\nFROM $companies";
$query .= "\nINNER JOIN $portfolio ON $portfolio.symbol = $companies.symbol";
$query .= "\nWHERE $portfolio.userId = :id";
$query .= "\nGROUP BY $companies.symbol";
$query .= "\nORDER BY $companies.symbol";

query_by_customer($query);
define("PORTFOLIO_DETAILS", DATABASE->fetch_all());

$query = "SELECT";
$query .= "\n\nCOUNT(DISTINCT $portfolio.symbol) AS symbol,";
$query .= "\n\nSUM(portfolio.amount) AS shares";
$query .= "\nFROM $portfolio";
$query .= "\nWHERE $portfolio.userId = :id";

query_by_customer($query);
define("DASHBOARD_DETAILS", DATABASE->fetch_all()[0]);

function query_by_customer(string $query): void
{
    DATABASE->prepare($query);
    DATABASE->bind(":id", SELECTED_CUSTOMER_ID);
    DATABASE->execute();
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

    DATABASE->prepare($query);
    DATABASE->bind(":symbol", $symbol);
    DATABASE->execute();
    
    $test = DATABASE->fetch()["close"];
    return $test;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Company</title>
    
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/deflist.css">
    <link rel="stylesheet" href="css/main.css">
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
                    DATABASE->select("id, firstname, lastname", StocksDatabase::TABLE_USERS);
                    DATABASE->execute();
                    
                    while ($customer = DATABASE->fetch()) {
                        $name = $customer['lastname'] . ", " . $customer['firstname'];
                        $id = $customer["id"];
                        
                        echo "<li><a href='" . $_SERVER["PHP_SELF"] . "?ref=$id'>$name</a></li>";
                    }
                    ?>
                </ul>
            </section>
            <section class="customer-portfolio">
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
                            
                            echo number_format($total_value, 2);
                            ?>
                        </h1>
                    </div>
                </div>
                <div class="table customer-porfolio-details">
                    <h1 class="table-title">Portfolio Details</h1>
                    <div class="table-data">
                        <div class="table-data-primary">
                            <h2>SYMBOL</h2>
                            <ul>
                                <?php
                                foreach (PORTFOLIO_DETAILS as $row)
                                {
                                    $symbol = $row["symbol"];

                                    echo "<li><a>$symbol</a></li>";
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

                                        echo "<li><a>$name</a></li>";
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
                                        $value = number_format($value, 2);

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
DATABASE->close();