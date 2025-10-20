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
        try {
            $this->pdo = new PDO("sqlite:$name");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function prepare($query): PDOStatement
    {
        try {
            return $this->statement = $this->pdo->prepare($query);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function bind($name, $value): bool
    {
        try {
            return $this->statement->bindValue($name, $value);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    public function execute(): bool
    {
        try {
            return $this->statement->execute();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function fetch(): mixed
    {
        try {
            return $this->statement->fetch();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function fetch_once(string $field): mixed
    {
        try {
            return $this->statement->fetch()[$field];
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function fetch_all(): array
    {
        try {
            return $this->statement->fetchAll();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
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