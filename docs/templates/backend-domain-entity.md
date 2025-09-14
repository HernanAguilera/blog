# Backend - Domain Entity Template

This template should be used to create new Domain Entities in the backend.

```php
// Template: src/Domain/Entities/{EntityName}.php
<?php
declare(strict_types=1);
namespace App\Domain\Entities;

use App\Domain\ValueObjects\{EntityName}Id;
use App\Domain\Events\{EntityName}Created;
use App\Shared\Domain\AggregateRoot;
use DateTimeImmutable;

final class {EntityName} extends AggregateRoot
{
    private {EntityName}Id $id;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct({EntityName}Id $id) {
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->recordEvent(new {EntityName}Created($this->id));
    }

    // Business methods...
    // Getters...
}
```
