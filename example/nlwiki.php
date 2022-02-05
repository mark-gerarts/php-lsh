<?php

use PhpLsh\LSH;

require '../vendor/autoload.php';

$limit = $argv[1] ?? 50_000;
$filename = 'nlwiki-latest-all-titles-in-ns0';

$lsh = LSH::createWithDefaults();

$candidates = $lsh->findCandidateItems(
    get_inputs($filename, $limit),
    5,
    20,
    0.9
);

$fullDataset = new SplFileObject($filename);
foreach ($candidates as [$a, $b]) {
    $fullDataset->seek($a);
    $titleA = $fullDataset->current();
    $fullDataset->seek($b);
    $titleB = $fullDataset->current();

    echo sprintf('"%s" and "%s"', trim($titleA), trim($titleB)) . PHP_EOL;
}

function get_inputs(string $filename, int $limit): iterable {
    $handle = fopen($filename, 'r');
    $lineNumber = -1;
    while (($line = fgets($handle)) !== false) {
        $lineNumber++;

        // We'll ignore small titles since these are not likely to be
        // interesting.
        if (strlen($line) < 10) {
            continue;
        }

        yield $lineNumber => trim($line);
        if ($limit > 0 && $lineNumber >= $limit) {
            return;
        }
    }
}
