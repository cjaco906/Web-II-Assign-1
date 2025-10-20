<?php
require_once "../include/database.php";

const STOCKS_DATABASE = new StocksDatabase("../data/stocks.db");

define("SELECTED_PORTFOLIO", $_GET["ref"]);
header("Content-Type: application/json");

$sql = "SELECT * FROM " . StocksDatabase::TABLE_PORTFOLIO;
$sql .= "\nWHERE userId = :id";

STOCKS_DATABASE->prepare($sql);
STOCKS_DATABASE->bind(":id", SELECTED_PORTFOLIO);
STOCKS_DATABASE->execute();

$result = STOCKS_DATABASE->fetch_all();

echo json_encode($result, JSON_NUMERIC_CHECK);