<?php
require_once "include/database.php";
require_once "include/header.php";

const STOCKS_DATABASE = new StocksDatabase("data/stocks.db");
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
                                <li><a href="api/company.php">/api/companies.php</a></li>
                                <?php
                                $sql = "SELECT symbol FROM " . StocksDatabase::TABLE_COMPANIES;

                                STOCKS_DATABASE->prepare($sql);
                                STOCKS_DATABASE->execute();

                                while ($row = STOCKS_DATABASE->fetch())
                                {
                                    $symbol = $row["symbol"];
                                    $path = "api/company.php?ref=$symbol";

                                    echo "<li><a href='$path'>$path</a></li>";
                                }
                                ?>
                            </ul>
                        </div>
                        <div>
                            <h2>HISTORY</h2>
                            <ul>
                                <?php

                                $sql = "SELECT DISTINCT symbol FROM " . StocksDatabase::TABLE_HISTORY;
                                $sql .= " ORDER BY symbol";

                                STOCKS_DATABASE->prepare($sql);
                                STOCKS_DATABASE->execute();

                                while ($row = STOCKS_DATABASE->fetch()) {
                                    $symbol = $row["symbol"];
                                    $path = "api/history.php?ref=$symbol";

                                    echo "<li><a href='$path'>$path</a></li>";
                                }
                                ?>
                            </ul>
                        </div>
                        <div>
                            <h2>PORTFOLIO</h2>
                            <ul>
                                <?php
                                $sql = "SELECT DISTINCT userId FROM " . StocksDatabase::TABLE_PORTFOLIO;
                                $sql .= " ORDER BY userId";

                                STOCKS_DATABASE->prepare($sql);
                                STOCKS_DATABASE->execute();

                                while ($row = STOCKS_DATABASE->fetch())
                                {
                                    $id = $row["userId"];
                                    $path = "api/portfolio.php?ref=$id";

                                    echo "<li><a href='$path'>$path</a></li>";
                                }
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
