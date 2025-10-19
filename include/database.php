<?php
class StocksDatabase
{
    public const string TABLE_COMPANIES = "companies";
    public const string TABLE_HISTORY = "history";
    public const string TABLE_PORTFOLIO = "portfolio";
    public const string TABLE_USERS = "users";

    private const string DATA_SOURCE_NAME = "sqlite:data/stocks.db";
    private PDO|null $pdo;
    private PDOStatement $statement;

    public function __construct()
    {
        $this->pdo = new PDO(self::DATA_SOURCE_NAME);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function prepare($query): PDOStatement
    {
        return $this->statement = $this->pdo->prepare($query);
    }

    public function select($fields, $table, $options = ""): PDOStatement
    {
        return $this->statement = $this->pdo->prepare("SELECT $fields FROM $table $options");
    }

    public function bind($name, $value): bool
    {
        return $this->statement->bindValue($name, $value);
    }
    public function execute(): bool
    {
        return $this->statement->execute();
    }

    public function fetch(): mixed
    {
        return $this->statement->fetch();
    }

    public function fetch_all(): array
    {
        return $this->statement->fetchAll();
    }

    public function close(): void
    {
        $this->pdo = null;
    }
}