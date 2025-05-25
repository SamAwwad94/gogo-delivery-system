<?php

$viewsPath = realpath(__DIR__ . '/resources/views');
$routesPath = realpath(__DIR__ . '/routes/web.php');
$outputFile = __DIR__ . '/conversion_report.txt';

$bladeFiles = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewsPath)
);

$routesContent = file_get_contents($routesPath);
$report = [];

foreach ($bladeFiles as $file) {
    if ($file->getExtension() !== 'php' || strpos($file->getFilename(), '.blade.php') === false) {
        continue;
    }

    $relativePath = str_replace($viewsPath . '/', '', $file->getRealPath());
    $viewName = str_replace(['/', '.blade.php'], ['.', ''], $relativePath);

    $foundInRoutes = strpos($routesContent, "'$viewName'") !== false || strpos($routesContent, "\"$viewName\"") !== false;

    if ($foundInRoutes) {
        $report[] = "[✔] $relativePath → Referenced in routes → Can Convert to Inertia";
    } elseif (strpos($relativePath, 'layouts') !== false) {
        $report[] = "[✘] $relativePath → Layout file → Keep as Blade";
    } else {
        $report[] = "[⚠] $relativePath → Not directly found in routes → Manual check needed";
    }
}

file_put_contents($outputFile, implode(PHP_EOL, $report));

echo "✅ Report generated at: $outputFile\n";
