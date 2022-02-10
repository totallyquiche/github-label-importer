# GitHub Label Importer

Import labels from one GitHub repository into another.

## Usage

```php
$importer = new Importer(
    github_access_token: '',
    user_or_organization_name: '',
    origin_repository_name: '',
    destination_repository_name: '',
    should_update_destination_label_colors: false,
    should_delete_destination_labels: false
);

$importer->import();
```

## Configuration Options

|Name|Description|Default Value|
|-|-|-|
|`should_update_destination_label_colors`|Indicates whether or not the label color should be updated if the label already exists in the destination repository.|`false`|
|`should_delete_destination_labels`|Indicates whether all labels in the destination repository should be deleted before the import.|`false`|