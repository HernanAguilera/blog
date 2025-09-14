# Backend File Structure (Clean Architecture)

```
src/
├── Domain/                 # Capa de Dominio
│   ├── Entities/
│   ├── ValueObjects/
│   ├── Services/
│   ├── Repositories/
│   ├── Events/
│   └── Exceptions/
├── Application/            # Capa de Aplicación
│   ├── UseCases/
│   ├── Commands/
│   ├── Queries/
│   ├── Handlers/
│   ├── Services/
│   └── Interfaces/
├── Infrastructure/         # Capa de Infraestructura
│   ├── Persistence/
│   │   ├── Eloquent/
│   │   │   ├── Models/
│   │   │   ├── Repositories/
│   │   │   └── Mappers/
│   ├── Services/
│   ├── External/
│   ├── Queue/
│   └── Providers/
└── Interface/              # Capa de Interfaz
    ├── Http/
    │   ├── Controllers/
    │   ├── Requests/
    │   ├── Resources/
    │   └── Middleware/
    ├── Console/
    └── GraphQL/
```
