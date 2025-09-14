# Frontend File Structure (Clean Architecture)

```
domain/                     # Capa de Dominio
│   ├── entities/
│   ├── value-objects/
│   ├── repositories/
│   ├── services/
│   └── exceptions/
application/                # Capa de Aplicación
│   ├── use-cases/
│   ├── commands/
│   ├── queries/
│   └── services/
infrastructure/             # Capa de Infraestructura
│   ├── api/
│   ├── repositories/
│   ├── storage/
│   ├── external/
│   └── services/
interface/                  # Capa de Interfaz
│   ├── components/
│   ├── pages/
│   ├── layouts/
│   ├── stores/
│   ├── composables/
│   └── middleware/
shared/                     # Utilidades Compartidas
│   ├── constants/
│   ├── types/
│   ├── utils/
│   ├── container/
│   └── guards/
```
