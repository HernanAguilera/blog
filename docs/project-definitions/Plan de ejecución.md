# FASE 6: INSTRUCCIONES PARA LA EJECUCIÓN DEL PLAN

## 1. PLANTILLAS PARA CADA TIPO DE CLASE

### **A. PLANTILLAS BACKEND**

#### **Entidad de Dominio**

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

    public function __construct(
        {EntityName}Id $id
        // Add other required parameters
    ) {
        $this->id = $id;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new {EntityName}Created($this->id));
    }

    // Business methods
    public function update{Property}({PropertyType} ${property}): void
    {
        $this->{property} = ${property};
        $this->updatedAt = new DateTimeImmutable();
        
        $this->recordEvent(new {EntityName}{Property}Updated($this->id));
    }

    private function guard{Condition}(): void
    {
        if (/* condition */) {
            throw new {ConditionException}($this->id);
        }
    }

    // Getters
    public function getId(): {EntityName}Id
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    // Business logic queries
    public function is{State}(): bool
    {
        return /* condition */;
    }

    public function can{Action}(): bool
    {
        return /* condition */;
    }
}
```

#### **Value Object**

```php
// Template: src/Domain/ValueObjects/{ValueObjectName}.php
<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\Invalid{ValueObjectName}Exception;

final class {ValueObjectName}
{
    private {type} $value;

    public function __construct({type} $value)
    {
        $this->guardValid{ValidationRule}($value);
        $this->value = $this->normalize($value);
    }

    public static function from{Source}({sourceType} ${source}): self
    {
        $value = self::convert{Source}To{ValueObjectName}(${source});
        return new self($value);
    }

    public function getValue(): {type}
    {
        return $this->value;
    }

    public function equals({ValueObjectName} $other): bool
    {
        return $this->value === $other->value;
    }

    private function guardValid{ValidationRule}({type} $value): void
    {
        if (/* validation condition */) {
            throw new Invalid{ValueObjectName}Exception($value);
        }
    }

    private function normalize({type} $value): {type}
    {
        // Apply normalization logic
        return $value;
    }

    private static function convert{Source}To{ValueObjectName}({sourceType} ${source}): {type}
    {
        // Conversion logic
        return /* converted value */;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
```

#### **Use Case**

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

#### **Repository Interface**

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

#### **Eloquent Repository**

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

    public function findBy{Criteria}({CriteriaType} ${criteria}): ?{EntityName}
    {
        $eloquentModel = {EntityName}EloquentModel::where('{column}', ${criteria}->getValue())
            ->with([/* relations */])
            ->first();

        return $eloquentModel ? $this->mapper->toDomainEntity($eloquentModel) : null;
    }

    public function findAll{FilteredEntities}({FilterParams} $params = null): {EntityName}Collection
    {
        $query = {EntityName}EloquentModel::query();
        
        // Apply filters
        if ($params) {
            $this->applyFilters($query, $params);
        }

        $eloquentModels = $query->with([/* relations */])->get();

        return new {EntityName}Collection(
            $eloquentModels->map(fn($model) => $this->mapper->toDomainEntity($model))->toArray()
        );
    }

    public function existsBy{Criteria}({CriteriaType} ${criteria}): bool
    {
        return {EntityName}EloquentModel::where('{column}', ${criteria}->getValue())->exists();
    }

    public function delete({EntityName}Id $id): void
    {
        {EntityName}EloquentModel::destroy($id->getValue());
        $this->invalidate{EntityName}Cache($id);
    }

    public function count{FilteredEntities}({FilterParams} $params = null): int
    {
        $query = {EntityName}EloquentModel::query();
        
        if ($params) {
            $this->applyFilters($query, $params);
        }

        return $query->count();
    }

    private function findEloquentModel({EntityName}Id $id): ?{EntityName}EloquentModel
    {
        return {EntityName}EloquentModel::find($id->getValue());
    }

    private function saveRelationships({EntityName} ${entity}, {EntityName}EloquentModel $eloquentModel): void
    {
        // Implement relationship saving logic
    }

    private function applyFilters($query, {FilterParams} $params): void
    {
        // Apply filtering logic
    }

    private function invalidate{EntityName}Cache({EntityName}Id $id): void
    {
        $this->cache->forget("entity:{$id->getValue()}");
        $this->cache->flush('{entity}:*');
    }
}
```

#### **Controller**

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

    public function index(): JsonResponse
    {
        // Implementation for listing
    }

    public function show(string $id): JsonResponse
    {
        // Implementation for showing single resource
    }

    public function update(string $id, {UpdateName}Request $request): JsonResponse
    {
        // Implementation for updating
    }

    public function destroy(string $id): JsonResponse
    {
        // Implementation for deletion
    }
}
```

### **B. PLANTILLAS FRONTEND**

#### **Entidad de Dominio Frontend**

```typescript
// Template: domain/entities/{EntityName}.ts
export class {EntityName} {
    private constructor(
        private readonly _id: {EntityName}Id,
        private readonly _property1: {Type1},
        private readonly _property2: {Type2},
        private readonly _createdAt: Date,
        private readonly _updatedAt: Date
    ) {}

    static create(data: {EntityName}Data): {EntityName} {
        return new {EntityName}(
            new {EntityName}Id(data.id),
            data.property1,
            data.property2,
            new Date(data.createdAt),
            new Date(data.updatedAt)
        );
    }

    // Getters
    get id(): string {
        return this._id.value;
    }

    get property1(): {Type1} {
        return this._property1;
    }

    get property2(): {Type2} {
        return this._property2;
    }

    get createdAt(): Date {
        return this._createdAt;
    }

    get updatedAt(): Date {
        return this._updatedAt;
    }

    // Business Logic
    can{Action}(user?: User): boolean {
        // Business logic implementation
        return /* condition */;
    }

    is{State}(): boolean {
        // State checking logic
        return /* condition */;
    }

    get{CalculatedProperty}(): {CalculatedType} {
        // Calculated property logic
        return /* calculated value */;
    }

    toJSON(): Record<string, any> {
        return {
            id: this.id,
            property1: this.property1,
            property2: this.property2,
            createdAt: this.createdAt.toISOString(),
            updatedAt: this.updatedAt.toISOString()
        };
    }
}
```

#### **Use Case Frontend**

```typescript
// Template: application/use-cases/{domain}/{ActionName}UseCase.ts
import type { {EntityName}RepositoryInterface } from '~/domain/repositories/{EntityName}RepositoryInterface';
import type { {EntityName} } from '~/domain/entities/{EntityName}';
import { {ActionName}Command } from '~/application/commands/{ActionName}Command';

export class {ActionName}UseCase {
    constructor(
        private {entityRepository}: {EntityName}RepositoryInterface,
        private validationService: ValidationService
    ) {}

    async execute(command: {ActionName}Command): Promise<{ReturnType}> {
        // 1. Validation
        await this.validateCommand(command);

        // 2. Business Logic
        const result = await this.apply{BusinessLogic}(command);

        // 3. Side effects (notifications, etc.)
        await this.handleSideEffects(result);

        return result;
    }

    private async validateCommand(command: {ActionName}Command): Promise<void> {
        const errors = await this.validationService.validate(command, {
            // validation rules
        });

        if (errors.length > 0) {
            throw new ValidationException('Invalid command', errors);
        }
    }

    private async apply{BusinessLogic}(command: {ActionName}Command): Promise<{ReturnType}> {
        // Core business logic implementation
        return await this.{entityRepository}.{action}(command);
    }

    private async handleSideEffects(result: {ReturnType}): Promise<void> {
        // Handle notifications, analytics, etc.
    }
}
```

#### **Repository Frontend**

```typescript
// Template: infrastructure/repositories/Http{EntityName}Repository.ts
import type { {EntityName}RepositoryInterface } from '~/domain/repositories/{EntityName}RepositoryInterface';
import type { {EntityName} } from '~/domain/entities/{EntityName}';
import type { HttpClientInterface } from '~/infrastructure/api/HttpClientInterface';

export class Http{EntityName}Repository implements {EntityName}RepositoryInterface {
    private readonly basePath = '/{entities}';

    constructor(
        private httpClient: HttpClientInterface,
        private cache?: CacheServiceInterface
    ) {}

    async find{MethodName}({params}: {ParamTypes}): Promise<{EntityName} | null> {
        try {
            const cacheKey = `{entity}:{methodName}:${JSON.stringify(params)}`;
            
            if (this.cache) {
                const cached = await this.cache.get(cacheKey);
                if (cached) {
                    return {EntityName}.create(cached);
                }
            }

            const response = await this.httpClient.get<{EntityName}Response>(
                `${this.basePath}/{endpoint}`,
                { params }
            );

            if (this.cache) {
                await this.cache.set(cacheKey, response.data, 300); // 5 minutes
            }

            return {EntityName}.create(response.data);
        } catch (error) {
            if (error.status === 404) {
                return null;
            }
            throw error;
        }
    }

    async create{EntityName}(data: Create{EntityName}Data): Promise<{EntityName}> {
        const response = await this.httpClient.post<{EntityName}Response>(
            this.basePath,
            data
        );

        // Invalidate related caches
        if (this.cache) {
            await this.cache.invalidatePattern('{entity}:*');
        }

        return {EntityName}.create(response.data);
    }

    async update{EntityName}(id: string, data: Update{EntityName}Data): Promise<{EntityName}> {
        const response = await this.httpClient.put<{EntityName}Response>(
            `${this.basePath}/${id}`,
            data
        );

        // Invalidate caches
        if (this.cache) {
            await this.cache.invalidatePattern(`{entity}:*`);
            await this.cache.delete(`{entity}:${id}`);
        }

        return {EntityName}.create(response.data);
    }

    async delete{EntityName}(id: string): Promise<void> {
        await this.httpClient.delete(`${this.basePath}/${id}`);

        // Invalidate caches
        if (this.cache) {
            await this.cache.invalidatePattern(`{entity}:*`);
            await this.cache.delete(`{entity}:${id}`);
        }
    }

    async get{EntityName}List(filters?: {EntityName}Filters): Promise<{EntityName}Collection> {
        const response = await this.httpClient.get<{EntityName}CollectionResponse>(
            this.basePath,
            { params: filters }
        );

        return {
            {entities}: response.data.map(item => {EntityName}.create(item)),
            totalCount: response.meta.total,
            currentPage: response.meta.currentPage,
            totalPages: response.meta.totalPages
        };
    }
}
```

#### **Vue Component**

```vue
<!-- Template: interface/components/{domain}/{ComponentName}.vue -->
<template>
  <div class="{component-name}">
    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center p-8">
      <LoadingSpinner class="w-8 h-8" />
    </div>

    <!-- Error State -->
    <ErrorMessage 
      v-else-if="error" 
      :message="error.message"
      @retry="handleRetry"
    />

    <!-- Content -->
    <div v-else class="space-y-4">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">
          {{ $t('{domain}.{componentTitle}') }}
        </h2>
        
        <button
          v-if="can{Action}"
          @click="handle{Action}"
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
          :disabled="isSubmitting"
        >
          <LoadingSpinner v-if="isSubmitting" class="w-4 h-4 mr-2" />
          {{ $t('common.{actionText}') }}
        </button>
      </div>

      <!-- List/Content -->
      <div class="grid gap-4">
        <{ItemComponent}
          v-for="{item} in {items}"
          :key="{item}.id"
          :{item}="{item}"
          @{event}="handle{Event}"
          @{action}="handle{Action}"
        />
      </div>

      <!-- Pagination -->
      <Pagination
        v-if="totalPages > 1"
        v-model:current-page="currentPage"
        :total-pages="totalPages"
        :total-items="totalCount"
        @page-change="handlePageChange"
      />

      <!-- Empty State -->
      <EmptyState
        v-if="{items}.length === 0 && !isLoading"
        :title="$t('{domain}.empty.title')"
        :description="$t('{domain}.empty.description')"
        :action-text="can{Action} ? $t('common.{actionText}') : undefined"
        @action="handle{Action}"
      />
    </div>

    <!-- Modals/Dialogs -->
    <{ModalComponent}
      v-model:open="showModal"
      :{item}="selectedItem"
      @saved="handleSaved"
      @cancelled="handleCancelled"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { container } from '~/shared/container/container';
import type { {UseCase} } from '~/application/use-cases/{domain}/{UseCase}';
import type { {EntityName} } from '~/domain/entities/{EntityName}';

// Container services
const {useCase} = container().get<{UseCase}>('{UseCase}');

// Props
interface Props {
  {prop}?: {PropType};
}

const props = defineProps<Props>();

// Emits
const emit = defineEmits<{
  {event}: [{EntityName}];
  error: [Error];
}>();

// Reactive state
const {items} = ref<{EntityName}[]>([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const error = ref<Error | null>(null);
const currentPage = ref(1);
const totalPages = ref(1);
const totalCount = ref(0);
const showModal = ref(false);
const selectedItem = ref<{EntityName} | null>(null);

// Computed
const can{Action} = computed(() => {
  // Permission logic
  return /* permission check */;
});

// Methods
const load{Items} = async (page: number = 1) => {
  isLoading.value = true;
  error.value = null;

  try {
    const result = await {useCase}.execute({
      page,
      limit: 12,
      // filters
    });

    {items}.value = result.{items};
    currentPage.value = result.currentPage;
    totalPages.value = result.totalPages;
    totalCount.value = result.totalCount;

  } catch (err) {
    error.value = err instanceof Error ? err : new Error('Unknown error');
    emit('error', error.value);
  } finally {
    isLoading.value = false;
  }
};

const handle{Action} = async () => {
  if (isSubmitting.value) return;

  isSubmitting.value = true;

  try {
    // Action logic
    await load{Items}(currentPage.value);
  } catch (err) {
    error.value = err instanceof Error ? err : new Error('Unknown error');
  } finally {
    isSubmitting.value = false;
  }
};

const handle{Event} = ({item}: {EntityName}) => {
  emit('{event}', {item});
};

const handlePageChange = (page: number) => {
  load{Items}(page);
};

const handleRetry = () => {
  load{Items}(currentPage.value);
};

const handleSaved = ({item}: {EntityName}) => {
  showModal.value = false;
  selectedItem.value = null;
  load{Items}(currentPage.value);
};

const handleCancelled = () => {
  showModal.value = false;
  selectedItem.value = null;
};

// Lifecycle
onMounted(() => {
  load{Items}();
});
</script>

<style scoped>
.{component-name} {
  /* Component specific styles */
}
</style>
```

#### **Pinia Store**

```typescript
// Template: interface/stores/{entityName}.ts
import { defineStore } from 'pinia';
import { ref, computed, readonly } from 'vue';
import { container } from '~/shared/container/container';
import type { {UseCase} } from '~/application/use-cases/{domain}/{UseCase}';
import type { {EntityName} } from '~/domain/entities/{EntityName}';

export const use{EntityName}Store = defineStore('{entityName}', () => {
    // Use cases
    const {useCase} = container().get<{UseCase}>('{UseCase}');

    // State
    const {items} = ref<{EntityName}[]>([]);
    const current{EntityName} = ref<{EntityName} | null>(null);
    const isLoading = ref(false);
    const isSubmitting = ref(false);
    const error = ref<Error | null>(null);
    const totalCount = ref(0);
    const currentPage = ref(1);
    const totalPages = ref(1);

    // Computed
    const isEmpty = computed(() => {items}.value.length === 0);
    const hasMore = computed(() => currentPage.value < totalPages.value);
    
    // Actions
    const load{Items} = async (params?: {LoadParams}) => {
        isLoading.value = true;
        error.value = null;

        try {
            const result = await {useCase}.execute(params || {});
            
            {items}.value = result.{items};
            totalCount.value = result.totalCount;
            currentPage.value = result.currentPage;
            totalPages.value = result.totalPages;

        } catch (err) {
            error.value = err instanceof Error ? err : new Error('Load failed');
            throw err;
        } finally {
            isLoading.value = false;
        }
    };

    const get{EntityName}ById = (id: string): {EntityName} | undefined => {
        return {items}.value.find(item => item.id === id);
    };

    const add{EntityName} = ({item}: {EntityName}) => {
        {items}.value.unshift({item});
        totalCount.value++;
    };

    const update{EntityName} = ({item}: {EntityName}) => {
        const index = {items}.value.findIndex(existing => existing.id === {item}.id);
        if (index !== -1) {
            {items}.value[index] = {item};
        }
    };

    const remove{EntityName} = (id: string) => {
        const index = {items}.value.findIndex(item => item.id === id);
        if (index !== -1) {
            {items}.value.splice(index, 1);
            totalCount.value--;
        }
    };

    const setCurrent{EntityName} = ({item}: {EntityName} | null) => {
        current{EntityName}.value = {item};
    };

    const clearError = () => {
        error.value = null;
    };

    const reset = () => {
        {items}.value = [];
        current{EntityName}.value = null;
        isLoading.value = false;
        isSubmitting.value = false;
        error.value = null;
        totalCount.value = 0;
        currentPage.value = 1;
        totalPages.value = 1;
    };

    return {
        // State (readonly)
        {items}: readonly({items}),
        current{EntityName}: readonly(current{EntityName}),
        isLoading: readonly(isLoading),
        isSubmitting: readonly(isSubmitting),
        error: readonly(error),
        totalCount: readonly(totalCount),
        currentPage: readonly(currentPage),
        totalPages: readonly(totalPages),
        
        // Computed
        isEmpty,
        hasMore,
        
        // Actions
        load{Items},
        get{EntityName}ById,
        add{EntityName},
        update{EntityName},
        remove{EntityName},
        setCurrent{EntityName},
        clearError,
        reset
    };
});
```

## 2. PLANES DE ACCIÓN ORGANIZADOS POR TAREAS

### **PLAN DE EJECUCIÓN - HISTORIA 1: AUTENTICACIÓN Y GESTIÓN DE USUARIOS**

#### **Tarea 1.1: Configuración Base de Autenticación**

**Orden de implementación:**

1. **1.1.1** - Crear Value Objects base

```bash
   # Crear archivos siguiendo plantillas:
   src/Domain/ValueObjects/UserId.php
   src/Domain/ValueObjects/Email.php
   src/Domain/ValueObjects/Password.php
   src/Domain/ValueObjects/UserRole.php
```

2. **1.1.2** - Crear entidad User

```bash
   # Usando plantilla de entidad:
   src/Domain/Entities/User.php
```

3. **1.1.3** - Crear interfaces de repositorio

```bash
   src/Domain/Repositories/UserRepositoryInterface.php
```

4. **1.1.4** - Crear excepciones específicas

```bash
   src/Domain/Exceptions/InvalidEmailException.php
   src/Domain/Exceptions/UserNotFoundException.php
   src/Domain/Exceptions/UserInactiveException.php
```

5. **1.1.5** - Configurar container de servicios

```bash
   # Agregar bindings en shared/container/bindings.ts
```

**Criterios de completitud:**

- [ ]  Todas las classes pasan los tests unitarios
- [ ]  Value objects validan correctamente
- [ ]  Entidad User maneja eventos de dominio
- [ ]  Container resuelve dependencias correctamente

#### **Tarea 1.2: Autenticación Tradicional (Backend)**

**Orden de implementación:**

1. **1.2.1** - LoginUserUseCase

```bash
   # Usando plantilla de UseCase:
   src/Application/UseCases/Auth/LoginUserUseCase.php
   src/Application/Commands
   src/Application/Commands/LoginCommand.php
```

2. **1.2.2** - RegisterUserUseCase

```bash
   src/Application/UseCases/Auth/RegisterUserUseCase.php
   src/Application/Commands/RegisterUserCommand.php
```

3. **1.2.3** - EloquentUserRepository

```bash
   # Usando plantilla de repository:
   src/Infrastructure/Persistence/Eloquent/Repositories/EloquentUserRepository.php
   src/Infrastructure/Persistence/Eloquent/Models/UserEloquentModel.php
   src/Infrastructure/Persistence/Eloquent/Mappers/UserMapper.php
```

4. **1.2.4** - AuthController

```bash
   # Usando plantilla de controller:
   src/Interface/Http/Controllers/AuthController.php
   src/Interface/Http/Requests/LoginRequest.php
   src/Interface/Http/Requests/RegisterRequest.php
```

5. **1.2.5** - JWT Middleware

```bash
   src/Interface/Http/Middleware/JWTAuthMiddleware.php
```

6. **1.2.6** - Rate Limiting

```bash
   # Configurar en config/sanctum.php y routes
```

**Criterios de completitud:**

- [ ]  Login endpoint funciona con JWT
- [ ]  Register endpoint crea usuarios
- [ ]  Rate limiting bloquea ataques
- [ ]  Middleware protege rutas admin
- [ ]  Tests de integración pasan

#### **Tarea 1.3: OAuth Social (Backend)**

**Orden de implementación:**

1. **1.3.1** - Configurar Socialite

```bash
   # Instalar y configurar:
   composer require laravel/socialite
```

2. **1.3.2** - SocialAuthService

```bash
   src/Infrastructure/Services/SocialAuthService.php
```

3. **1.3.3** - SocialLoginUseCase

```bash
   src/Application/UseCases/Auth/SocialLoginUseCase.php
   src/Application/Commands/SocialLoginCommand.php
```

4. **1.3.4** - OAuth endpoints

```bash
   # Agregar rutas en routes/api.php
   GET /auth/social/{provider}/redirect
   GET /auth/social/{provider}/callback
```

5. **1.3.5** - Sistema de configuración

```bash
   # Settings en base de datos para activar/desactivar providers
```

**Criterios de completitud:**

- [ ]  Google OAuth funciona
- [ ]  Facebook OAuth funciona
- [ ]  Twitter OAuth funciona
- [ ]  Providers pueden desactivarse
- [ ]  Usuarios se vinculan correctamente

#### **Tarea 1.4: Sistema de Roles (Backend)**

**Orden de implementación:**

1. **1.4.1** - Spatie Permission

```bash
   composer require spatie/laravel-permission
   php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

2. **1.4.2** - Migraciones de roles

```bash
   php artisan migrate
```

3. **1.4.3** - UserRole Value Object mejorado

```bash
   # Actualizar con métodos de autorización
```

4. **1.4.4** - Seeders

```bash
   database/seeders/RoleSeeder.php
   database/seeders/SuperAdminSeeder.php
```

5. **1.4.5** - RoleMiddleware

```bash
   src/Interface/Http/Middleware/RoleMiddleware.php
```

**Criterios de completitud:**

- [ ]  Roles se crean correctamente
- [ ]  SuperAdmin está desactivado por defecto
- [ ]  Permisos funcionan en middleware
- [ ]  Seeders ejecutan sin errores

#### **Tarea 1.5: Seguridad y Rate Limiting**

**Orden de implementación:**

1. **1.5.1** - Cloudflare Turnstile

```bash
   src/Infrastructure/External/Cloudflare/TurnstileService.php
```

2. **1.5.2** - TurnstileService en container

```bash
   # Agregar binding en configuración
```

3. **1.5.3** - Rate limiting por endpoint

```bash
   # Configurar en RouteServiceProvider
```

4. **1.5.4** - Security logging

```bash
   src/Infrastructure/Services/SecurityLogger.php
```

5. **1.5.5** - IP blocking temporal

```bash
   # Implementar en RateLimitService
```

**Criterios de completitud:**

- [ ]  Turnstile valida correctamente
- [ ]  Rate limits se aplican
- [ ]  Logs de seguridad funcionan
- [ ]  IPs maliciosas se bloquean

#### **Tarea 1.6: Frontend - Sistema de Autenticación**

**Orden de implementación:**

1. **1.6.1** - Entidades de dominio frontend

```bash
   # Usando plantillas:
   domain/entities/User.ts
   domain/value-objects/UserId.ts
```

2. **1.6.2** - HttpUserRepository

```bash
   # Usando plantilla de repository:
   infrastructure/repositories/HttpUserRepository.ts
```

3. **1.6.3** - Use Cases frontend

```bash
   # Usando plantilla de UseCase:
   application/use-cases/auth/LoginUseCase.ts
   application/use-cases/auth/RegisterUseCase.ts
```

4. **1.6.4** - AuthStore

```bash
   # Usando plantilla de Store:
   interface/stores/auth.ts
```

5. **1.6.5** - useAuth composable

```bash
   interface/composables/useAuth.ts
```

6. **1.6.6** - Componentes de auth

```bash
   # Usando plantilla de componente:
   interface/components/auth/LoginForm.vue
   interface/components/auth/RegisterForm.vue
```

7. **1.6.7** - Middleware de autenticación

```bash
   interface/middleware/auth.ts
   interface/middleware/admin.ts
```

8. **1.6.8** - Páginas

```bash
   interface/pages/login.vue
   interface/pages/register.vue
```

**Criterios de completitud:**

- [ ]  Login form funciona
- [ ]  Register form funciona
- [ ]  OAuth buttons funcionan
- [ ]  Middleware protege rutas
- [ ]  Estado se persiste correctamente

### **CRONOGRAMA DE DESARROLLO**

#### **Sprint 1 (Días 1-3): Base de Autenticación**

```
Día 1: Tareas 1.1 (Configuración Base)
Día 2: Tarea 1.2 (Autenticación Tradicional Backend)  
Día 3: Tarea 1.4 (Sistema de Roles)
```

#### **Sprint 2 (Días 4-6): OAuth y Seguridad**

```
Día 4: Tarea 1.3 (OAuth Social)
Día 5: Tarea 1.5 (Seguridad y Rate Limiting)
Día 6: Testing y refinamiento backend
```

#### **Sprint 3 (Días 7-9): Frontend Autenticación**

```
Día 7: Tareas 1.6.1-1.6.3 (Dominio y Use Cases frontend)
Día 8: Tareas 1.6.4-1.6.6 (Store, Composables, Componentes)
Día 9: Tareas 1.6.7-1.6.8 (Middleware y Páginas)
```

### **PLAN DE EJECUCIÓN - HISTORIA 2: GESTIÓN DE POSTS**

#### **Sprint 4-6 (Días 10-18): Posts Completos**

**Día 10-12: Backend Posts**

```
Tarea 2.1: Dominio de Posts
- PostId, PostTitle, PostContent, PostSlug, PostStatus value objects
- Post entity con eventos
- PostRepositoryInterface

Tarea 2.2: CRUD Posts
- CreatePostUseCase, UpdatePostUseCase, PublishPostUseCase
- EloquentPostRepository
- PostController con endpoints
```

**Día 13-15: Editor Avanzado**

```
Tarea 2.3: Editor Backend
- Auto-save UseCase
- Preview con tokens
- HTML sanitization

Tarea 2.4: Estados y Scheduling
- PublishPostUseCase, SchedulePostUseCase
- ScheduledPostPublisher Job
- Cron jobs
```

**Día 16-18: Frontend Posts**

```
Tarea 2.6: Frontend Posts
- Post entity frontend
- HttpPostRepository
- PostsStore con Pinia

Tarea 2.7: Editor Quill.js
- Configuración Quill + Highlight.js
- Upload de imágenes
- Auto-save frontend
```

### **COMANDOS DE GENERACIÓN AUTOMÁTICA**

#### **Generador de Clases Backend**

```bash
# Crear script: scripts/generate-backend-class.php
php scripts/generate-backend-class.php entity User
php scripts/generate-backend-class.php usecase CreatePost
php scripts/generate-backend-class.php repository Post
php scripts/generate-backend-class.php controller Post
```

#### **Generador de Clases Frontend**

```bash
# Crear script: scripts/generate-frontend-class.ts
npm run generate:entity User
npm run generate:usecase CreatePost  
npm run generate:repository Post
npm run generate:component PostCard
```

### **CHECKLIST DE CALIDAD POR TAREA**

#### **Checklist Backend**

```
□ Entidad sigue Clean Architecture
□ Value Objects validan correctamente
□ Use Case maneja errores de dominio
□ Repository implementa interface
□ Controller maneja excepciones
□ Tests unitarios >90% coverage
□ Tests de integración pasan
□ Documentación API actualizada
```

#### **Checklist Frontend**

```
□ Entidad tiene business logic
□ Use Case maneja validaciones
□ Repository implementa cache
□ Component es reutilizable
□ Store maneja estado correctamente
□ Tests unitarios >80% coverage
□ Tests E2E críticos pasan
□ Componente es accesible
```

### **HERRAMIENTAS DE DESARROLLO**

#### **Scripts de Setup**

```bash
# setup.sh
#!/bin/bash
echo "Setting up Blog Platform development environment..."

# Backend setup
cd blog-backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed

# Frontend setup  
cd ../blog-frontend
npm install
cp .env.example .env

echo "Setup completed!"
```

#### **Scripts de Testing**

```bash
# test.sh
#!/bin/bash

echo "Running all tests..."

# Backend tests
cd blog-backend
php artisan test --coverage

# Frontend tests
cd ../blog-frontend
npm run test:unit
npm run test:e2e

echo "All tests completed!"
```

#### **Scripts de Deploy**

```bash
# deploy.sh
#!/bin/bash

echo "Deploying Blog Platform..."

# Build frontend
cd blog-frontend
npm run build

# Deploy backend
cd ../blog-backend
php artisan migrate --force
php artisan config:cache
php artisan route:cache

echo "Deployment completed!"
```

### **DOCUMENTACIÓN REQUERIDA**

#### **Por cada Entidad/Use Case:**

1. **README.md** con propósito y uso
2. **Diagrama de flujo** del proceso
3. **Ejemplos de uso** con código
4. **Test cases** documentados
5. **API documentation** (OpenAPI)

#### **Por cada Componente Frontend:**

1. **Storybook stories** para componentes
2. **Props documentation**
3. **Events documentation**
4. **Usage examples**
5. **Accessibility notes**

Este plan de ejecución proporciona:

- **Plantillas reutilizables** para acelerar desarrollo
- **Orden específico** de implementación
- **Criterios claros** de completitud
- **Cronograma realista** de 15 días
- **Herramientas automatizadas** para generación
- **Checklists de calidad** rigurosos
- **Scripts de automatización** para setup/testing/deploy