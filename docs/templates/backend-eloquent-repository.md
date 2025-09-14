# Backend - Eloquent Repository Template

```php
// Template: src/Infrastructure/Persistence/Eloquent/Repositories/Eloquent{EntityName}Repository.php
<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Repositories\{EntityName}RepositoryInterface;
use App\Domain\Entities\{EntityName};
use App\Domain\ValueObjects\{EntityName}Id;
use App\Domain\Collections\{EntityName}Collection;
use App\Infrastructure\Persistence\Eloquent\Models\{EntityName}EloquentModel;
use App\Infrastructure\Persistence\Eloquent\Mappers\{EntityName}Mapper;
use App\Application\Interfaces\CacheServiceInterface;

final class Eloquent{EntityName}Repository implements {EntityName}RepositoryInterface
{
    public function __construct(
        private {EntityName}Mapper $mapper,
        private CacheServiceInterface $cache
    ) {}

    public function save({EntityName} ${entity}): void
    {
        $eloquentModel = $this->findEloquentModel(${entity}->getId()) 
            ?? new {EntityName}EloquentModel();
        
        $eloquentModel->fill($this->mapper->toEloquentArray(${entity}));
        $eloquentModel->save();

        // Handle relationships if needed
        $this->saveRelationships(${entity}, $eloquentModel);

        // Invalidate cache
        $this->invalidate{EntityName}Cache(${entity}->getId());
    }

    public function findById({EntityName}Id $id): ?{EntityName}
    {
        $cacheKey = "entity:{$id->getValue()}";
        
        return $this->cache->remember($cacheKey, 3600, function () use ($id) {
            $eloquentModel = {EntityName}EloquentModel::with([/* relations */])
                ->find($id->getValue());

            return $eloquentModel ? $this->mapper->toDomainEntity($eloquentModel) : null;
        });
    }

    // ... other methods
}
```
