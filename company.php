<?php
// is_numeric - https://www.php.net/manual/en/function.is-numeric.php
// round - https://www.php.net/manual/en/function.round.php
// number_format - https://www.php.net/manual/en/function.number-format.php
// wordwrap - https://www.php.net/manual/en/function.wordwrap.php

require_once "include/database.php";
require_once "include/ui.php";

const DATABASE = new StocksDatabase();
const COMPANY_SYMBOL = "A"; // TODO: remove (testing purposes)

//define("COMPANY_SYMBOL", $_GET["ref"]);
define("COMPANY", fetch_all(StocksDatabase::TABLE_COMPANIES, "*")[0]);
define("HISTORY", fetch_all(StocksDatabase::TABLE_HISTORY, "*"));

function query(string $table, string $field, string $options = ""): void
{
    DATABASE->select($field, $table, "WHERE symbol = :symbol " . $options);
    DATABASE->bind(":symbol", COMPANY_SYMBOL);
    DATABASE->execute();
}

function fetch_once(string $table, string $field, string $options = ""): mixed
{
    query($table, $field, $options);
    
    return DATABASE->fetch();
}

function fetch_all(string $table, string $field, string $options = ""): array
{
    query($table, $field, $options);
    
    return DATABASE->fetch_all();
}

function list_history(string $field, bool $format_decimal = true): void
{
    render_rows(HISTORY, $field, $format_decimal);
}

function list_financials(string $field, bool $format_decimal = true): void
{
    try
    {
        $json = json_decode(COMPANY["financials"], true);
    }
    catch (ValueError $e)
    {
        die($e->getMessage());
    }
    
    render_json($json, $field, $format_decimal);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Company</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/deflist.css">
    <link rel="stylesheet" href="css/company.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <header>
        <nav></nav>
    </header>
    <main>
        <article>
            <section class="company-information">
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
            <section class="table company-financials">
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
                <div>
                    <h1>
                        HISTORY<br>
                        HIGH
                    </h1>
                    <h1 class="format-accounting">
                        <?php
                        $highest = fetch_once(StocksDatabase::TABLE_HISTORY, "MAX(high) AS Highest")["Highest"];
                        $highest = format($highest);

                        echo $highest;
                        ?>
                    </h1>
                </div>
                <div>
                    <h1>
                        HISTORY<br>
                        LOW
                    </h1>
                    <h1 class="format-accounting">
                        <?php
                        $lowest = fetch_once(StocksDatabase::TABLE_HISTORY, "MIN(low) AS Lowest")["Lowest"];
                        $lowest = format($lowest);

                        echo $lowest;
                        ?>
                    </h1>
                </div>
                <div>
                    <h1>
                        TOTAL<br>
                        VOLUME
                    </h1>
                    <h1>
                        <?php
                        $total = fetch_once(StocksDatabase::TABLE_HISTORY, "SUM(volume) AS TotalVolume")["TotalVolume"];
                        $total = format($total); // add commas

                        echo $total;
                        ?>
                    </h1>
                </div>
                <div>
                    <h1>
                        AVG.<br>
                        VOLUME
                    </h1>
                    <h1>
                        <?php
                        $avg = fetch_once(StocksDatabase::TABLE_HISTORY, "AVG(volume) AS AvgVolume")["AvgVolume"];
                        $avg = format(round($avg), false); // add commas and round to nearest whole

                        echo $avg;
                        ?>
                    </h1>
                </div>
            </section>
            <section class="table company-history">
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
                        <div class="history-data-volume">
                            <h2>VOLUME</h2>
                            <ul>
                                <?php
                                list_history("volume");
                                ?>
                            </ul>
                        </div>
                        <div class="accounting-format-list">
                            <h2>OPEN</h2>
                            <ul>
                                <?php
                                list_history("open");
                                ?>
                            </ul>
                        </div>
                        <div class="accounting-format-list">
                            <h2>CLOSE</h2>
                            <ul>
                                <?php
                                list_history("close");
                                ?>
                            </ul>
                        </div>
                        <div class="accounting-format-list">
                            <h2>HIGH</h2>
                            <ul>
                                <?php
                                list_history("high");
                                ?>
                            </ul>
                        </div>
                        <div class="accounting-format-list">
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
DATABASE->close();