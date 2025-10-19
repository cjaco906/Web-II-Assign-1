<?php
// number_format - https://www.php.net/manual/en/function.number-format.php
// is_float - https://www.php.net/manual/en/function.is-float.php

function render_rows(array $rows, string $field, bool $format_decimal = true): void
{
    foreach (HISTORY as $row) {
        $data = $row[$field];
        $data = format($data);

        echo "<li>" . $data . "</li>";
    }
}

function render_json(array $json, string $field, bool $format_decimal = true): void
{
    $array = $json[$field];

    foreach ($array as $data)
    {
        $data = format($data);

        echo "<li>" . $data . "</li>";
    }
}

function format($data, $decimals = true): mixed
{
    if (is_numeric($data))
    {
        if ($decimals && is_float($data))
        {
            return number_format(round($data, 2), 2);
        }
        else
        {
            return number_format($data);
        }
    }

    return $data;
}