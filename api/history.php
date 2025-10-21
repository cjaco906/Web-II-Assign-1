<?php
require_once "../include/database.php";
require_once "../include/api.php";

header("Content-Type: application/json");

if (isset($_GET[QUERY_STRING]))
{
    $database = new StocksDatabase("../data/stocks.db");

    $sql = "SELECT * FROM " . StocksDatabase::TABLE_HISTORY;
    $sql .= "\nWHERE symbol = :symbol";
    $sql .= "\nORDER BY date ASC";

    $database->prepare($sql);
    $database->bind(":symbol", $_GET[QUERY_STRING]);
    $database->execute();

    $result = $database->fetch_all();

    echo json_encode($result, JSON_NUMERIC_CHECK);

}