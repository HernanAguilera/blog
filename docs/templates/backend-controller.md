# Backend - Controller Template

```php
// Template: src/Interface/Http/Controllers/{EntityName}Controller.php
<?php

declare(strict_types=1);

namespace App\Interface\Http\Controllers;

use App\Application\UseCases\{Domain}\{ActionName}UseCase;
use App\Application\Commands\{ActionName}Command;
use App\Interface\Http\Requests\{ActionName}Request;
use App\Interface\Http\Resources\{EntityName}Resource;
use App\Domain\Exceptions\DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class {EntityName}Controller extends Controller
{
    public function __construct(
        private {ActionName}UseCase ${actionUseCase}
    ) {}

    public function {action}({ActionName}Request $request): JsonResponse
    {
        try {
            $command = new {ActionName}Command(
                // Map request data to command
                $request->input('field1'),
                $request->input('field2')
            );

            ${result} = $this->{actionUseCase}->execute($command);

            return response()->json([
                'success' => true,
                'data' => new {EntityName}Resource(${result})
            ], Response::HTTP_CREATED);

        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ], Response::HTTP_BAD_REQUEST);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // ... other methods
}
```
