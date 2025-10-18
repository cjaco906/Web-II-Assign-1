<?php
// number_format - https://www.php.net/manual/en/function.number-format.php
// is_float - https://www.php.net/manual/en/function.is-float.php

function render_list(array $rows, string $field, bool $format_decimal = true): void
{
    foreach (HISTORY as $row) {
        $data = $row[$field];

        if (is_numeric($data))
        {
            if (is_float($data))
            {
                $data = number_format(round($data, 2), 2);
            }
            else
            {
                $data = number_format($data);
            }
        }

        echo "<li>" . $data . "</li>";
    }
}
function format_decimal($value): string
{
    return number_format(round($value, 2), 2);
}