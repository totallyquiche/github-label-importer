<?php declare(strict_types = 1);

namespace TotallyQuiche\GithubLabelImporter;

final class Label
{
    public function __construct(
        public string $name,
        public string $color,
        public string $description
    ) {}
}