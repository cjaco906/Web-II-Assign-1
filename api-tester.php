<?php
require_once "include/database.php";
require_once "include/header.php";

const STOCKS_DATABASE = new StocksDatabase("data/stocks.db");

function list_endpoints(string $table, string $field): void
{
    $sql = "SELECT DISTINCT";
    $sql .= "\n\n$field";
    $sql .= "\nFROM $table";
    $sql .= "\nORDER BY $field";

    STOCKS_DATABASE->prepare($sql);
    STOCKS_DATABASE->execute();

    while ($row = STOCKS_DATABASE->fetch())
    {
        $data = $row[$field];
        $path = "api/$table.php?ref=$data";

        echo "<li><a href='$path'>$path</a></li>";
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
    <link rel="stylesheet" href="css/table.css">
    <link rel="stylesheet" href="css/api-tester.css">
</head>
<body>
    <?php
    render_header();
    ?>
    <main>
        <article>
            <section class="table api-tester-table">
                <h1 class="table-title">API</h1>
                <div class="table-data">
                    <div class="table-data-other api-tester-table-data-other">
                        <div>
                            <h2>COMPANIES</h2>
                            <ul>
                                <li><a href="api/companies.php">/api/companies.php</a></li>
                                <?php
                                list_endpoints(StocksDatabase::TABLE_COMPANIES, "symbol");
                                ?>
                            </ul>
                        </div>
                        <div>
                            <h2>HISTORY</h2>
                            <ul>
                                <?php
                                list_endpoints(StocksDatabase::TABLE_HISTORY, "symbol");
                                ?>
                            </ul>
                        </div>
                        <div>
                            <h2>PORTFOLIO</h2>
                            <ul>
                                <?php
                                list_endpoints(StocksDatabase::TABLE_PORTFOLIO, "userId");
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>
        </article>
    </main>
</body>
</html>
