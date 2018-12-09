<?php

$finder = PhpCsFixer\Finder::create()
            ->files()
            ->in(__DIR__)
            ->exclude('vendor')
            ->notName("*.txt")
            ->notPath("seoimage.php")
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);
;

return PhpCsFixer\Config::create()
    ->setFinder($finder);
