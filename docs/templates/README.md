# Code Templates

This directory contains code templates for rapid development following Clean Architecture principles. Each template is designed to maintain consistency and quality across the codebase.

## Backend Templates

### Domain Layer
- **[backend-domain-entity.md](backend-domain-entity.md)**: Domain entity with aggregate root functionality
- **[backend-value-object.md](backend-value-object.md)**: Immutable value objects for domain modeling

### Application Layer
- **[backend-use-case.md](backend-use-case.md)**: Use case implementation with CQRS pattern
- **[backend-repository-interface.md](backend-repository-interface.md)**: Repository interface contracts

### Infrastructure Layer
- **[backend-eloquent-repository.md](backend-eloquent-repository.md)**: Eloquent implementation of repositories

### Interface Layer
- **[backend-controller.md](backend-controller.md)**: HTTP controllers with proper error handling

## Frontend Templates

### Domain Layer
- **[frontend-domain-entity.md](frontend-domain-entity.md)**: TypeScript domain entities

### Application Layer
- **[frontend-use-case.md](frontend-use-case.md)**: Frontend use cases for business logic

### Infrastructure Layer
- **[frontend-repository.md](frontend-repository.md)**: HTTP repositories for API communication

### Interface Layer
- **[frontend-vue-component.md](frontend-vue-component.md)**: Vue 3 components with Composition API
- **[frontend-pinia-store.md](frontend-pinia-store.md)**: Pinia stores for state management

## Usage Instructions

1. **Choose the appropriate template** for the component you're building
2. **Replace placeholder text** (marked with `{TemplateName}`, `{EntityName}`, etc.)
3. **Follow naming conventions** specified in each template
4. **Implement business logic** in the designated sections
5. **Add proper error handling** as shown in examples

## Template Variables

Common placeholder patterns used across templates:
- `{EntityName}`: The name of your entity (e.g., `User`, `Post`, `Comment`)
- `{ActionName}`: The action being performed (e.g., `Create`, `Update`, `Delete`)
- `{Domain}`: The domain context (e.g., `Auth`, `Posts`, `Comments`)
- `{PropertyType}`: Data type of entity properties
- `{ReturnType}`: Expected return type of methods

## Code Generation

Future versions may include automatic code generation based on these templates. For now, manual replacement of template variables is required.

## Contributing

When adding new templates:
1. Follow the existing naming convention: `{layer}-{component-type}.md`
2. Include comprehensive examples with error handling
3. Document all template variables used
4. Test the template with real implementations
5. Update this README with the new template description