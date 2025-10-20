<?php
// wordwrap - https://www.php.net/manual/en/function.wordwrap.php

require_once "include/header.php";
require_once "include/database.php";

const STOCKS_DATABASE = new StocksDatabase("data/stocks.db");

define("COMPANY_SYMBOL", $_GET["ref"]);
query(StocksDatabase::TABLE_COMPANIES, "*");
define("COMPANY", STOCKS_DATABASE->fetch_all()[0]);

function query(string $table, string $field): void
{
    $sql = "SELECT";
    $sql .= "\n\n$field";
    $sql .= "\nFROM $table";
    $sql .= "\nWHERE symbol = :symbol";

    STOCKS_DATABASE->prepare($sql);
    STOCKS_DATABASE->bind(":symbol", COMPANY_SYMBOL);
    STOCKS_DATABASE->execute();
}

function list_history(string $field): void
{
    query(StocksDatabase::TABLE_HISTORY, $field);
    
    while ($company = STOCKS_DATABASE->fetch()) {
        $data = $company[$field];
        $data = format($data);

        echo "<li>" . $data . "</li>";
    }
}

function list_financials(string $field): void
{
    
    try
    {
        $company = COMPANY["financials"];
        $json = json_decode($company, true);
    }
    catch (ValueError $e)
    {
        die($e->getMessage());
    }
    
    $array = $json[$field];

    foreach ($array as $data)
    {
        $data = format($data);

        echo "<li>" . $data . "</li>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Company</title>

    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/deflist.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/company.css">
</head>
<body>
    <?php
    render_header();
    ?>
    <main>
        <article>
            <section class="company-information neat-shadow">
                <div class="company-description">
                    <h1 class="company-description-title">
                        <?php
                        
                        echo COMPANY["name"] . " (" . COMPANY_SYMBOL . ")";
                        ?>
                    </h1>
                    <dl class="company-profile">
                        <div>
                            <dt>Symbol</dt>
                            <dd>
                                <?php
                                echo COMPANY_SYMBOL;
                                ?>
                            </dd>
                        </div>
                        <div>
                            <dt>Sector</dt>
                            <dd>
                                <?php
                                echo COMPANY["sector"];
                                ?>
                            </dd>
                        </div>
                        <div>
                            <dt>Subindustry</dt>
                            <dd>
                                <?php
                                echo COMPANY["subindustry"];
                                ?>
                            </dd>
                        </div>
                    </dl>
                    <p class="company-description-text">
                        <?php
                        echo wordwrap(COMPANY["description"], 75, "<br />");
                        ?>
                    </p>
                </div>
                <div class="company-details">
                    <h1 class="company-details-title">Contact Information</h1>
                    <dl class="company-contact-details">
                        <div>
                            <dt>Website</dt>
                            <dd>
                                <?php
                                echo COMPANY["website"];
                                ?>
                            </dd>
                        </div>
                        <div>
                            <dt>Exchange</dt>
                            <dd>
                                <?php
                                echo COMPANY["exchange"];
                                ?>
                            </dd>
                        </div>
                        <div>
                            <dt>Address</dt>
                            <dd>
                                <?php
                                echo COMPANY["address"];
                                ?>
                            </dd>
                        </div>
                        <div>
                            <dt>Location</dt>
                            <dd>
                                <?php
                                echo "(" . COMPANY["latitude"] . ", " . COMPANY["longitude"] . ")";
                                ?>
                            </dd>
                        </div>
                    </dl>
                </div>
            </section>
            <section class="table company-financials neat-shadow">
                <h1 class="table-title">Company Financials</h1>
                <div class="table-data">
                    <div class="table-data-primary">
                        <h2>YEAR</h2>
                        <ul>
                            <?php
                            list_financials("years");
                            ?>
                        </ul>
                    </div>
                    <div class="table-data-other company-financials-data-other">
                        <div class="format-accounting-list">
                            <h2>REVENUE</h2>
                            <ul>
                                <?php
                                list_financials("revenue");
                                ?>
                            </ul>
                        </div>
                        <div class="format-accounting-list">
                            <h2>EARNINGS</h2>
                            <ul>
                                <?php
                                list_financials("earnings");
                                ?>
                            </ul>
                        </div>
                        <div class="format-accounting-list">
                            <h2>ASSETS</h2>
                            <ul>
                                <?php
                                list_financials("assets");
                                ?>
                            </ul>
                        </div>
                        <div class="format-accounting-list">
                            <h2>LIABILITIES</h2>
                            <ul>
                                <?php
                                list_financials("liabilities");
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
            <section class="dashboard company-history-dashboard">
                <div class="neat-shadow">
                    <h1>
                        HISTORY<br>
                        HIGH
                    </h1>
                    <h1 class="format-accounting">
                        <?php
                        query(StocksDatabase::TABLE_HISTORY, "MAX(high) as highest");
                        
                        $highest = STOCKS_DATABASE->fetch_once("highest");
                        $highest = format($highest);

                        echo $highest;
                        ?>
                    </h1>
                </div>
                <div class="neat-shadow">
                    <h1>
                        HISTORY<br>
                        LOW
                    </h1>
                    <h1 class="format-accounting">
                        <?php
                        query(StocksDatabase::TABLE_HISTORY, "MIN(low) as lowest");
                        
                        $lowest = STOCKS_DATABASE->fetch_once("lowest");
                        $lowest = format($lowest);

                        echo $lowest;
                        ?>
                    </h1>
                </div>
                <div class="neat-shadow">
                    <h1>
                        TOTAL<br>
                        VOLUME
                    </h1>
                    <h1>
                        <?php
                        query(StocksDatabase::TABLE_HISTORY, "SUM(volume) as total");
                        
                        $total = STOCKS_DATABASE->fetch_once("total");
                        $total = format($total); // add commas

                        echo $total;
                        ?>
                    </h1>
                </div>
                <div class="neat-shadow">
                    <h1>
                        AVG.<br>
                        VOLUME
                    </h1>
                    <h1>
                        <?php
                        query(StocksDatabase::TABLE_HISTORY, "AVG(volume) as avg");
                        
                        $avg = STOCKS_DATABASE->fetch_once("avg");
                        $avg = format($avg); // add commas and round to nearest whole

                        echo $avg;
                        ?>
                    </h1>
                </div>
            </section>
            <section class="table neat-shadow company-history">
                <h1 class="table-title">Company History</h1>
                <div class="table-data">
                    <div class="table-data-primary">
                        <h2>DATE</h2>
                        <ul>
                            <?php
                            list_history("date", false);
                            ?>
                        </ul>
                    </div>
                    <div class="table-data-other company-history-data-other">
                        <div>
                            <h2>VOLUME</h2>
                            <ul>
                                <?php
                                list_history("volume");
                                ?>
                            </ul>
                        </div>
                        <div class="format-accounting-list">
                            <h2>OPEN</h2>
                            <ul>
                                <?php
                                list_history("open");
                                ?>
                            </ul>
                        </div>
                        <div class="format-accounting-list">
                            <h2>CLOSE</h2>
                            <ul>
                                <?php
                                list_history("close");
                                ?>
                            </ul>
                        </div>
                        <div class="format-accounting-list">
                            <h2>HIGH</h2>
                            <ul>
                                <?php
                                list_history("high");
                                ?>
                            </ul>
                        </div>
                        <div class="format-accounting-list">
                            <h2>LOW</h2>
                            <ul>
                                <?php
                                list_history("low");
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </article>
    </main>
    <footer>

    </footer>
</body>
</html>

<?php
STOCKS_DATABASE->close();