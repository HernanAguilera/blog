# Clean Architecture Principles

1.  **Independencia de frameworks**: El dominio no conoce Laravel/Nuxt.
2.  **Testabilidad**: Todas las capas son testables por separado.
3.  **Independencia de UI**: Cambiar el frontend no afecta la lógica de negocio.
4.  **Independencia de DB**: Cambiar la base de datos no afecta el dominio.
5.  **Regla de dependencia**: Las dependencias solo fluyen hacia el centro (Dominio).

## Capas de la Arquitectura

### Backend
-   **Domain**: Reglas de negocio puras.
-   **Application**: Orquesta los casos de uso.
-   **Infrastructure**: Implementaciones concretas.
-   **Interface**: Punto de entrada a la aplicación.

### Frontend
-   **Domain**: Lógica de negocio del cliente.
-   **Application**: Casos de uso del cliente.
-   **Infrastructure**: Implementaciones concretas para el frontend.
-   **Interface**: Componentes de Vue, páginas de Nuxt, etc.
