<?php declare(strict_types = 1);

namespace TotallyQuiche\GithubLabelImporter;

final class Configuration
{
    public function __construct(
        public bool $should_replace_destination_label_colors,
        public bool $should_delete_destination_labels
    ) {}
}