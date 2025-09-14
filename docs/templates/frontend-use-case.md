# Frontend - Use Case Template

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
        // ... validation logic
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
