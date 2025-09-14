# Frontend - Domain Entity Template

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

    // ... other getters

    // Business Logic
    can{Action}(user?: User): boolean {
        // Business logic implementation
        return /* condition */;
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
