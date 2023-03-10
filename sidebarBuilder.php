<?php

declare(strict_types=1);

use League\Flysystem\StorageAttributes;

require_once 'vendor/autoload.php';

const LIST_SYMBOL = '*';

$sitemapGen = new \Icamys\SitemapGenerator\SitemapGenerator('https://www.condorcet.io', __DIR__.'/docs/');
$sitemapGen->addURL('/');
$sitemapGen->addURL('/GithubReadme');


$adapter = new League\Flysystem\Local\LocalFilesystemAdapter(__DIR__.'/docs');
$filesystem = new League\Flysystem\Filesystem($adapter);

$listing = $filesystem->listContents('.', true)
    ->filter(fn (StorageAttributes $attributes) => $attributes->isFile())
    ->filter(fn (StorageAttributes $attributes) => str_ends_with($attributes->path(), '.md'))
    ->filter(fn (StorageAttributes $attributes) => ! str_starts_with($attributes->path(), '_'))
    ->filter(fn (StorageAttributes $attributes) => ! str_contains($attributes->path(), 'SUMMARY'))
    ->sortByPath();

// var_dump($listing->toArray());

$summaryMD = '';
$summaryMD .= "* [**Condorcet - Presentation**](/GithubReadme)\n";

$lastPath = false;

foreach ($listing as $file) {
    $fileContent = file_get_contents(__DIR__.'/docs/'.$file->path());

    $re = '/#(.*)\n/m';

    preg_match_all($re, $fileContent, $matches, PREG_SET_ORDER, 0);

    // Print the entire match result
    // var_dump($matches);
    $firstMatch = reset($matches);
    $title = is_array($firstMatch) ? $firstMatch[1] : false;

    if (is_string($title)) {
        $path = explode(DIRECTORY_SEPARATOR, $file->path());
        $fileName = array_pop($path);
        $folder = end($path);

        $depth = count($path);
        $depth < 0 && $depth = 0;

        $title = removeIndex(trim($title));
        $depth === 0 && $title = '**'.$title.'**';
        $title === '**Start**' && $title = '<span class="condorcet_secondary" style="font-weight:700;">**Start**</span>';

        if ($folder !== $lastPath && is_string($folder)) {
            $lastPath = $folder;
            $pathTitle = preg_replace('/([a-z])([A-Z])/m', '$1 $2', $folder);
            $pathTitle = removeIndex($pathTitle);
            ($depth > 0 ? $depth - 1 : 0) === 0 && $pathTitle = '**'.$pathTitle.'**';

            count($path) < 2 && $summaryMD .= "\n";
            $summaryMD .= str_repeat('  ', $depth > 0 ? $depth - 1 : 0).LIST_SYMBOL." {$pathTitle} \n";
            count($path) < 2 && $summaryMD .= "\n";
        }

        $thePath = !str_contains($file->path(), '1.Start') ? $file->path() : 'README';
        
        $summaryMD .= str_repeat('  ', $depth).LIST_SYMBOL." [{$title}]({$thePath}) \n";
        $sitemapGen->addURL(str_replace('.md', '', '/'.$thePath));
    } else {
        throw new Exception($thePath.' have no title');
    }
}

$summaryMD .= "\n";
$summaryMD .= "* [**Voting Methods**](VotingMethods)\n";
$sitemapGen->addURL('/VotingMethods');
$summaryMD .= "* [**API References**](ApiReferences)\n";
$sitemapGen->addURL('/ApiReferences');
$summaryMD .= "* [**Changelog**](Changelog)\n";
$sitemapGen->addURL('/Changelog');


var_dump($summaryMD);
$filesystem->write('_sidebar.md', $summaryMD);

// file_put_contents('docs/404.html', file_get_contents('docs/index.html'));

// Sitemap
$sitemapGen->flush();
$sitemapGen->finalize();
$sitemapGen->updateRobots();


function removeIndex(string $title): string
{
    $title = trim($title);

    return preg_replace('/^([0-9]\.)/m', '', $title) ?? $title;
}
