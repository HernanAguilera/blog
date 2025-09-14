# Main API Endpoints

### Autenticación

```
POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout
POST /api/auth/social/{provider}
```

### Posts

```
GET /api/posts                 # Listado público
GET /api/posts/{slug}          # Post individual
POST /api/admin/posts          # Crear post
PUT /api/admin/posts/{id}      # Actualizar post
DELETE /api/admin/posts/{id}   # Eliminar post
```

### Comentarios

```
GET /api/posts/{slug}/comments     # Comentarios de un post
POST /api/posts/{slug}/comments    # Crear comentario
GET /api/admin/comments            # Panel moderación
PUT /api/admin/comments/{id}       # Aprobar/rechazar
```

### Newsletter

```
POST /api/newsletter/subscribe     # Suscribirse
POST /api/admin/newsletter/send    # Envío manual
```
