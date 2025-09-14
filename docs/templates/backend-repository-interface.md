# Backend - Repository Interface Template

```php
// Template: src/Domain/Repositories/{EntityName}RepositoryInterface.php
<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\{EntityName};
use App\Domain\ValueObjects\{EntityName}Id;
use App\Domain\Collections\{EntityName}Collection;

interface {EntityName}RepositoryInterface
{
    public function save({EntityName} ${entity}): void;
    
    public function findById({EntityName}Id $id): ?{EntityName};
    
    public function findBy{Criteria}({CriteriaType} ${criteria}): ?{EntityName};
    
    public function findAll{FilteredEntities}({FilterParams} $params = null): {EntityName}Collection;
    
    public function existsBy{Criteria}({CriteriaType} ${criteria}): bool;
    
    public function delete({EntityName}Id $id): void;
    
    public function count{FilteredEntities}({FilterParams} $params = null): int;
}
```
