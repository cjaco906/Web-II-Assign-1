<?php
require_once "../include/database.php";

const STOCKS_DATABASE = new StocksDatabase("../data/stocks.db");

define("SELECTED_COMPANY", $_GET["ref"]);
header("Content-Type: application/json");

$sql = "SELECT * FROM " . StocksDatabase::TABLE_COMPANIES;

if (!is_null(SELECTED_COMPANY))
{
    $sql .= "\nWHERE symbol = :symbol";
}

STOCKS_DATABASE->prepare($sql);

if (!is_null(SELECTED_COMPANY))
{
    STOCKS_DATABASE->bind(":symbol", SELECTED_COMPANY);
}

STOCKS_DATABASE->execute();

$result = STOCKS_DATABASE->fetch_all();

echo json_encode($result);