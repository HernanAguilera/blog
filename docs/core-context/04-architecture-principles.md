# Clean Architecture Principles

1.  **Independencia de frameworks**: El dominio no conoce Laravel/Nuxt.
2.  **Testabilidad**: Todas las capas son testables por separado.
3.  **Independencia de UI**: Cambiar el frontend no afecta la lógica de negocio.
4.  **Independencia de DB**: Cambiar la base de datos no afecta el dominio.
5.  **Regla de dependencia**: Las dependencias solo fluyen hacia el centro (Dominio).

## Capas de la Arquitectura

### Backend
-   **Domain**: Reglas de negocio puras. Contiene Entidades, Value Objects, Interfaces de Repositorio, Servicios de Dominio y Eventos. No depende de nada.
-   **Application**: Orquesta los casos de uso. Contiene Casos de Uso (Use Cases), Comandos, Queries y las interfaces de servicios que necesita. Depende solo del Dominio.
-   **Infrastructure**: Implementaciones concretas de las interfaces. Contiene Repositorios de Eloquent, servicios de terceros (AWS S3, Mailgun), y manejo de colas. Depende del Dominio y la Aplicación.
-   **Interface**: Punto de entrada a la aplicación. Contiene Controladores HTTP, Comandos de consola y Recursos de API. Depende de la Aplicación.

### Frontend
-   **Domain**: Lógica de negocio del cliente. Contiene entidades, value objects y las interfaces de los repositorios.
-   **Application**: Casos de uso del cliente. Orquesta las acciones del usuario.
-   **Infrastructure**: Implementaciones concretas para el frontend. Contiene repositorios HTTP que llaman a la API del backend, y wrappers para el LocalStorage o servicios externos.
-   **Interface**: Componentes de Vue, páginas de Nuxt, stores de Pinia y composables.
