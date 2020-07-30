# doctrine-full-text-search-bundle

## Introduction

This bundle aims to enable doctrine full text search. I understand there are better options such as elasticsearch.

## Installation

### Require the package

`composer require hopeter1018/doctrine-full-text-search-bundle`

### Add to kernel

#### Symfony 4+ or Symfony Flex

Add `/config/bundles.php`

```php
return [
  ...,
  HoPeter1018\DoctrineFullTextSearchBundle\HoPeter1018DoctrineFullTextSearchBundle::class => ['all' => true],
];
```

#### Symfony 2+

Add `/app/AppKernel.php`

```php
$bundles = [
  ...,
  new HoPeter1018\DoctrineFullTextSearchBundle\HoPeter1018DoctrineFullTextSearchBundle(),
];
```

### Add to doctrine config

```yaml
doctrine:
  orm:
    entity_managers:
      mappings:
        HoPeter1018DoctrineFullTextSearchBundle: ~
```

### Config

```yaml
hopeter1018_doctrine_full_text_search:
  # No config yet
```

### Usage

#### Entity

```php
namespace App\Entity;

use HoPeter1018\DoctrineFullTextSearchBundle\Annotation as Fts;
use HoPeter1018\DoctrineFullTextSearchBundle\Formatter\DateTimeFormatter;

/**
 * @Fts\Entity(
 *     columns={
 *         "__property_name_1__": @Fts\Column,
 *         "__property_name_2__": @Fts\Column(formatter=DateTimeFormatter::class),
 *     },
 *     columnGroups={
 *         "__group_name_1__": @Fts\ColumnGroup(columns={
 *             "__property_name_1__": @Fts\Column,
 *             "__property_name_2__": @Fts\Column,
 *         })
 *     }
 * )
 */
class TheEntity
{
    /**
     * @Fts\Column
     * @ORM\Column(type="string", length=255)
     */
    private $propertyName1;

    /**
     * @Fts\Column
     */
    public function methodName1()
    {
        return 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
    }
}
```

#### Use thru Repository

```php
use HoPeter1018\DoctrineFullTextSearchBundle\Entity\FullTextSearchIndex;

$fullTextSearchIndexRepo = $em->getRepository(FullTextSearchIndex::class);

$ids = $fullTextSearchIndexRepo->search('~~ The search keywords ~~', TheEntity::class, ['__name-property/group/method__']);

$repo = $em->getRepository(TheEntity::class);
$list = $repo->findBy(['id' => $ids]);
```

## TODO

-   [ ] Add mode to have FTS index per table
-   [ ] Entity
    -   `Trait` for **PERENTITY** mode
-   [ ] Repository
    -   `Trait` for search
-   [ ] Enhance Annotation
    -   [ ] Add more property
-   [ ] Add Command to
    -   ReIndex
    -   Search
-   [ ] Add helpers to
    -   [ ] SonataAdmin
    -   [ ] ApiPlatform
-   [ ] Check against different id type
