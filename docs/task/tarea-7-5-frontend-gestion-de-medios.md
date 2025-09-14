**Historia de Usuario:** HISTORIA 7: GESTIÓN DE MEDIOS

#### **Tarea 7.5: Frontend Gestión de Medios**

- 7.5.1 Crear componentes de upload con drag & drop
- 7.5.2 Implementar MediaGallery para selección de archivos
- 7.5.3 Crear HttpMediaRepository
- 7.5.4 Implementar progress bars para uploads
- 7.5.5 Crear composable useMedia()

**Flujo de Upload:**

```
Usuario arrastra imagen → UploadComponent → File validation → 
UploadMediaUseCase → AWS S3 upload → Image optimization → 
Thumbnail generation → Database record → URL pública generada → 
Media disponible en galería
```
