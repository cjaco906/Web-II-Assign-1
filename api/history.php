<?php
require_once "../include/database.php";

const STOCKS_DATABASE = new StocksDatabase("../data/stocks.db");

define("SELECTED_COMPANY_HISTORY", $_GET["ref"]);
header("Content-Type: application/json");

$sql = "SELECT * FROM " . StocksDatabase::TABLE_HISTORY;
$sql .= "\nWHERE symbol = :symbol";
$sql .= "\nORDER BY date ASC";

STOCKS_DATABASE->prepare($sql);
STOCKS_DATABASE->bind(":symbol", SELECTED_COMPANY_HISTORY);
STOCKS_DATABASE->execute();

$result = STOCKS_DATABASE->fetch_all();

echo json_encode($result, JSON_NUMERIC_CHECK);
