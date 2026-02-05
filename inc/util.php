<?php
function h($value)
{
    return htmlspecialchars($value ?? "", ENT_QUOTES, "UTF-8");
}

function get_param($key, $default = "")
{
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

function post_param($key, $default = "")
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

function redirect_to($url)
{
    header("Location: " . $url);
    exit;
}

function sql_value($connection, $value, $is_numeric = false)
{
    if ($value === null || $value === "") {
        return "NULL";
    }
    if ($is_numeric) {
        return (string) ((int) $value);
    }
    return "'" . mysqli_real_escape_string($connection, $value) . "'";
}
?>
