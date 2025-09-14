# Frontend - Repository Template

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
        // ... implementation with caching
    }

    async create{EntityName}(data: Create{EntityName}Data): Promise<{EntityName}> {
        // ... implementation
    }

    async update{EntityName}(id: string, data: Update{EntityName}Data): Promise<{EntityName}> {
        // ... implementation
    }

    async delete{EntityName}(id: string): Promise<void> {
        // ... implementation
    }

    async get{EntityName}List(filters?: {EntityName}Filters): Promise<{EntityName}Collection> {
        // ... implementation
    }
}
```
