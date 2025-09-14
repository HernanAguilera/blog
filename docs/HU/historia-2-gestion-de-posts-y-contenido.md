**Depende de:** Historia 1

### **Historia 2: Gestión de Posts y Contenido**

**Como** administrador/colaborador  
**Quiero** crear, editar y publicar posts con un editor moderno  
**Para** mantener mi blog actualizado con contenido técnico de calidad

**Descripción detallada:** Sistema completo de gestión de contenido con editor WYSIWYG avanzado, soporte para código con resaltado de sintaxis, auto-guardado, preview, y gestión de estados. Optimizado para contenido técnico y desarrollo.

**Criterios de aceptación:**

- 2.1 Editor Quill.js implementado con toolbar completa
- 2.2 Resaltado de sintaxis con highlight.js (190+ lenguajes)
- 2.3 Soporte prioritario para: JS, PHP, Python, TypeScript, XML, JSON, YAML, MD, SQL, Bash
- 2.4 Upload de imágenes directo desde el editor
- 2.5 Estados de posts: draft, published, archived
- 2.6 Auto-save cada 30 segundos
- 2.7 Modo preview con URL temporal segura (/preview/{token})
- 2.8 Meta description personalizable
- 2.9 URLs amigables: /{locale}/{year}/{month}/{slug}
- 2.10 Generación automática de slug desde título
- 2.11 Validación de contenido y título obligatorios
- 2.12 Historial de versiones básico (última versión guardada)
