<?php declare(strict_types = 1);

namespace TotallyQuiche\GithubLabelImporter;

use Github\Client as GitHubApiClient;
use Github\AuthMethod;

final class Importer
{
    private GitHubApiClient $github_api_client;
    private Repository $origin_repository;
    private Repository $destination_repository;
    private Configuration $configuration;

    public function __construct(
        ?string $github_access_token,
        string $user_or_organization_name,
        string $origin_repository_name,
        string $destination_repository_name,
        bool $should_update_destination_label_colors = false,
        bool $should_delete_destination_labels = false
    )
    {
        $this->github_api_client =  self::getNewGitHubApiClient($github_access_token);

        $this->origin_repository = self::getNewRepository(
            $this->github_api_client,
            $user_or_organization_name,
            $origin_repository_name
        );

        $this->destination_repository = self::getNewRepository(
            $this->github_api_client,
            $user_or_organization_name,
            $destination_repository_name
        );

        $this->configuration = self::getNewConfiguration(
            $should_update_destination_label_colors,
            $should_delete_destination_labels
        );
    }

    private static function getNewGitHubApiClient(?string $github_access_token) : GitHubApiClient
    {
        $client = new GitHubApiClient;

        if (!is_null($github_access_token)) {
            $client->authenticate(
                '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',
                null,
                AuthMethod::ACCESS_TOKEN
            );
        }

        return $client;
    }

    private static function getNewLabel(
        string $name,
        string $color,
        string $description
    )
    {
        return new Label(
            $name,
            $color,
            $description
        );
    }

    private static function getNewRepository(
        GitHubApiClient $github_api_client,
        string $user_or_organization_name,
        string $repository_name
    ) : Repository
    {
        $repository = new Repository(
            $user_or_organization_name,
            $repository_name
        );

        $github_labels = $github_api_client->api('issue')
            ->labels()
            ->all(
                $user_or_organization_name,
                $repository_name
            );

        foreach ($github_labels as $github_label) {
            $repository->labels[] = self::getNewLabel(
                $github_label['name'],
                $github_label['color'],
                $github_label['description']
            );
        }

        return $repository;
    }

    private static function getNewConfiguration(
        bool $should_update_destination_label_colors,
        bool $should_delete_destination_labels
    ) : Configuration
    {
        return new Configuration(
            $should_update_destination_label_colors,
            $should_delete_destination_labels
        );
    }

    public function import()
    {
        $configuration = $this->configuration;
        $origin_repository = $this->origin_repository;
        $destination_repository = $this->destination_repository;
        $github_api_client = $this->github_api_client;

        if ($configuration->should_delete_destination_labels)
        {
            foreach ($destination_repository->labels as $label)
            {
                $github_api_client->api('issue')
                    ->labels()
                    ->deleteLabel(
                        $destination_repository->user_or_organization_name,
                        $destination_repository->name,
                        $label->name
                    );
            }
        }

        foreach ($origin_repository->labels as $label)
        {
            $label_already_exists = $destination_repository->hasLabel($label->name);

            if ($configuration->should_delete_destination_labels || !$label_already_exists)
            {
                $github_api_client->api('issue')
                    ->labels()
                    ->create(
                        $destination_repository->user_or_organization_name,
                        $destination_repository->name,
                        [
                            'name' => $label->name,
                            'color' => $label->color,
                            'description' => $label->description
                        ]
                    );
            } elseif ($configuration->should_update_destination_label_colors)
            {
                $github_api_client->api('issue')
                    ->labels()
                    ->update(
                        $destination_repository->user_or_organization_name,
                        $destination_repository->name,
                        $label->name,
                        $label->name,
                        $label->color
                    );
            }
        }
    }
}