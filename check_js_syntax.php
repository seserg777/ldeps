<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking JavaScript syntax in detail...\n";

try {
    $view = view('layouts.app');
    $html = $view->render();

    // Find all script tags
    preg_match_all('/<script[^>]*>(.*?)<\/script>/s', $html, $matches);

    if (empty($matches[1])) {
        echo "❌ No script tags found\n";
        return;
    }

    echo "Found " . count($matches[1]) . " script tags\n";

    foreach ($matches[1] as $i => $js) {
        echo "\n--- Script tag " . ($i + 1) . " ---\n";
        echo "Length: " . strlen($js) . " characters\n";

        // Check for common syntax issues
        $issues = [];

        // Check for unclosed template literals
        $backticks = substr_count($js, '`');
        if ($backticks % 2 !== 0) {
            $issues[] = "Unclosed template literal (backticks: $backticks)";
        }

        // Check for unclosed parentheses
        $openParens = substr_count($js, '(');
        $closeParens = substr_count($js, ')');
        if ($openParens !== $closeParens) {
            $issues[] = "Unmatched parentheses (open: $openParens, close: $closeParens)";
        }

        // Check for unclosed braces
        $openBraces = substr_count($js, '{');
        $closeBraces = substr_count($js, '}');
        if ($openBraces !== $closeBraces) {
            $issues[] = "Unmatched braces (open: $openBraces, close: $closeBraces)";
        }

        // Check for unclosed brackets
        $openBrackets = substr_count($js, '[');
        $closeBrackets = substr_count($js, ']');
        if ($openBrackets !== $closeBrackets) {
            $issues[] = "Unmatched brackets (open: $openBrackets, close: $closeBrackets)";
        }

        // Check for unclosed quotes
        $singleQuotes = substr_count($js, "'");
        $doubleQuotes = substr_count($js, '"');
        if ($singleQuotes % 2 !== 0) {
            $issues[] = "Unmatched single quotes ($singleQuotes)";
        }
        if ($doubleQuotes % 2 !== 0) {
            $issues[] = "Unmatched double quotes ($doubleQuotes)";
        }

        if (empty($issues)) {
            echo "✅ No syntax issues found in script " . ($i + 1) . "\n";
        } else {
            echo "❌ Found issues in script " . ($i + 1) . ":\n";
            foreach ($issues as $issue) {
                echo "  - $issue\n";
            }
        }

        // Show first 200 characters of the script
        echo "First 200 characters:\n";
        echo substr($js, 0, 200) . "...\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
