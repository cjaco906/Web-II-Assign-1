<?php
require_once "../include/database.php";
require_once "../include/api.php";

header("Content-Type: application/json");

if (isset($_GET[QUERY_STRING]))
{
    $database = new StocksDatabase("../data/stocks.db");

    $sql = "SELECT * FROM " . StocksDatabase::TABLE_PORTFOLIO;
    $sql .= "\nWHERE userId = :id";

    $database->prepare($sql);
    $database->bind(":id", $_GET[QUERY_STRING]);
    $database->execute();

    $result = STOCKS_DATABASE->fetch_all();

    echo json_encode($result, JSON_NUMERIC_CHECK);
}