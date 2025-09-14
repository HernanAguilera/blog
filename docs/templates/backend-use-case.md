# Backend - Use Case Template

```php
// Template: src/Application/UseCases/{Domain}/{ActionName}UseCase.php
<?php

declare(strict_types=1);

namespace App\Application\UseCases\{Domain};

use App\Domain\Repositories\{EntityName}RepositoryInterface;
use App\Domain\Services\{EntityName}DomainService;
use App\Application\Commands\{ActionName}Command;
use App\Domain\Entities\{EntityName};
use App\Domain\Events\EventDispatcherInterface;
use App\Domain\Exceptions\{ExceptionName};

final class {ActionName}UseCase
{
    public function __construct(
        private {EntityName}RepositoryInterface ${entityRepository},
        private {EntityName}DomainService ${entityDomainService},
        private EventDispatcherInterface $eventDispatcher
    ) {}

    public function execute({ActionName}Command $command): {ReturnType}
    {
        // 1. Validation and authorization
        $this->validateCommand($command);
        
        // 2. Load required entities
        ${entity} = $this->{entityRepository}->findById($command->{entityId});
        if (!${entity}) {
            throw new {EntityName}NotFoundException($command->{entityId});
        }

        // 3. Apply business logic
        ${result} = $this->apply{BusinessLogic}(${entity}, $command);

        // 4. Save changes
        $this->{entityRepository}->save(${entity});

        // 5. Dispatch events
        foreach (${entity}->getEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return ${result};
    }

    private function validateCommand({ActionName}Command $command): void
    {
        if (/* validation condition */) {
            throw new Invalid{ActionName}CommandException(/* details */);
        }
    }

    private function apply{BusinessLogic}({EntityName} ${entity}, {ActionName}Command $command): {ReturnType}
    {
        // Business logic implementation
        ${entity}->{action}($command->{parameter});
        
        return /* result */;
    }
}
```
