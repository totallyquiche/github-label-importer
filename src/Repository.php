<?php declare(strict_types = 1);

namespace TotallyQuiche\GithubLabelImporter;

final class Repository
{
    public array $labels = [];

    public function __construct(
        public string $user_or_organization_name,
        public string $name
    ) {}

    public function hasLabel(string $label_name) : bool
    {
        $has_label = false;

        foreach($this->labels as $label)
        {
            if($label->name == $label_name) {
                $has_label = true;
                break;
            }
        }

        return $has_label;
    }
}