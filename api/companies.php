<?php
require_once "../include/database.php";
require_once "../include/api.php";

const STOCKS_DATABASE = new StocksDatabase("../data/stocks.db");

header("Content-Type: application/json");

$sql = "SELECT * FROM " . StocksDatabase::TABLE_COMPANIES;

if (isset($_GET[QUERY_STRING]))
{
    $sql .= "\nWHERE symbol = :symbol";
}

STOCKS_DATABASE->prepare($sql);

if (isset($_GET[QUERY_STRING]))
{
    STOCKS_DATABASE->bind(":symbol", $_GET[QUERY_STRING]);
}

STOCKS_DATABASE->execute();

$result = STOCKS_DATABASE->fetch_all();

echo json_encode($result);