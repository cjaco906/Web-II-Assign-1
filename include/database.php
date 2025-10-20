<?php
// number_format - https://www.php.net/manual/en/function.number-format.php
// is_float - https://www.php.net/manual/en/function.is-float.php

class StocksDatabase
{
    public const string TABLE_COMPANIES = "companies";
    public const string TABLE_HISTORY = "history";
    public const string TABLE_PORTFOLIO = "portfolio";
    public const string TABLE_USERS = "users";
    private PDO|null $pdo;
    private PDOStatement $statement;

    public function __construct(string $name)
    {
        $this->pdo = new PDO("sqlite:$name");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function prepare($query): PDOStatement
    {
        return $this->statement = $this->pdo->prepare($query);
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

function format($data, $decimals = true): mixed
{
    if (is_numeric($data))
    {
        if ($decimals && is_float($data))
        {
            return number_format($data, 2);
        }
        else
        {
            return number_format($data);
        }
    }

    return $data;
}