<?php
// $_SERVER (PHP_SELF) - https://www.php.net/manual/en/reserved.variables.server.php

require_once "include/header.php";
require_once "include/footer.php";
require_once "include/database.php";
require_once "include/ui.php";

const DATABASE = new StocksDatabase();

define("SELECTED_CUSTOMER_ID", $_GET["ref"]);
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
            <section class="customer-list">
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
                <?php
                $companies = StocksDatabase::TABLE_COMPANIES;
                $portfolio = StocksDatabase::TABLE_PORTFOLIO;
                $history = StocksDatabase::TABLE_HISTORY;
                
                $query = "SELECT DISTINCT";
                $query .= "\n$companies.symbol,";
                $query .= "\n$companies.name,";
                $query .= "\n$companies.sector,";
                $query .= "\nSUM($portfolio.amount) AS shares";
                $query .= "\nFROM $companies";
                $query .= "\nINNER JOIN $portfolio ON $portfolio.symbol = $companies.symbol";
                $query .= "\nWHERE $portfolio.userId = :customer";
                $query .= "\nGROUP BY $companies.symbol";
                $query .= "\nORDER BY $companies.symbol";
                
                DATABASE->prepare($query);
                DATABASE->bind(":customer", SELECTED_CUSTOMER_ID);
                DATABASE->execute();
                
                $portfolio = DATABASE->fetch_all();
                
                $query = "SELECT";
                $query .= "\nCOUNT(DISTINCT portfolio.symbol) AS symbol,";
                $query .= "\nSUM(portfolio.amount) AS shares";
                $query .= "\nFROM portfolio";
                $query .= "\nWHERE portfolio.userId = :id";

                DATABASE->prepare($query);
                DATABASE->bind(":id", SELECTED_CUSTOMER_ID);
                DATABASE->execute();

                $result = DATABASE->fetch();
                $total_companies = $result["symbol"];
                $total_shares = $result["shares"];
                ?>
                <div class="dashboard customer-portfolio-dashboard">
                    <div>
                        <h1>COMPANIES</h1>
                        <h1>
                            <?php
                            echo $total_companies;
                            ?>
                        </h1>
                    </div>
                    <div>
                        <h1>TOTAL SHARES</h1>
                        <h1>
                            <?php
                            echo $total_shares;
                            ?>
                        </h1>
                    </div>
                    <div>
                        <h1>TOTAL VALUE</h1>
                        <h1>
                            <?php
                            $total_value = 0;

                            foreach ($portfolio as $rows)
                            {
                                $symbol = $rows["symbol"];
                                $shares = $rows["shares"];
                                
                                $close = get_latest_history_close($symbol);
                                $value = $shares * $close;
                                $total_value += $value;
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
                                foreach ($portfolio as $row)
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
                                    foreach ($portfolio as $row)
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
                                    foreach ($portfolio as $row)
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
                                    foreach ($portfolio as $row)
                                    {
                                        $shares = $row["shares"];

                                        echo "<li>$shares</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div>
                                <h2>VALUE</h2>
                                <ul>
                                    <?php
                                    foreach ($portfolio as $row)
                                    {
                                        $symbol = $row["symbol"];
                                        $shares = $row["shares"];
                                        $close = get_latest_history_close($symbol);
                                        $value = $shares * $close;

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