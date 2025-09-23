<?php

declare(strict_types=1);

namespace Blog\Domain\User\ValueObjects;

enum Permission: string
{
    // User Management
    case MANAGE_USERS = 'manage_users';
    case VIEW_USERS = 'view_users';
    case CREATE_USERS = 'create_users';
    case EDIT_USERS = 'edit_users';
    case DELETE_USERS = 'delete_users';

    // Post Management
    case MANAGE_POSTS = 'manage_posts';
    case VIEW_POSTS = 'view_posts';
    case CREATE_POSTS = 'create_posts';
    case EDIT_POSTS = 'edit_posts';
    case EDIT_OWN_POSTS = 'edit_own_posts';
    case DELETE_POSTS = 'delete_posts';
    case PUBLISH_POSTS = 'publish_posts';
    case READ_POSTS = 'read_posts';

    // Comment Management
    case MANAGE_COMMENTS = 'manage_comments';
    case MODERATE_COMMENTS = 'moderate_content';
    case CREATE_COMMENTS = 'create_comments';
    case EDIT_COMMENTS = 'edit_comments';
    case DELETE_COMMENTS = 'delete_comments';
    case APPROVE_COMMENTS = 'approve_comments';

    // System Management
    case MANAGE_SYSTEM = 'manage_system';
    case MANAGE_SETTINGS = 'manage_settings';
    case VIEW_ANALYTICS = 'view_analytics';
    case VIEW_OWN_ANALYTICS = 'view_own_analytics';

    // Newsletter Management
    case MANAGE_NEWSLETTER = 'manage_newsletter';
    case SEND_NEWSLETTER = 'send_newsletter';
    case VIEW_SUBSCRIBERS = 'view_subscribers';

    // Media Management
    case MANAGE_MEDIA = 'manage_media';
    case UPLOAD_MEDIA = 'upload_media';
    case DELETE_MEDIA = 'delete_media';

    // Pages Management
    case MANAGE_PAGES = 'manage_pages';
    case CREATE_PAGES = 'create_pages';
    case EDIT_PAGES = 'edit_pages';
    case DELETE_PAGES = 'delete_pages';

    public function getDescription(): string
    {
        return match($this) {
            self::MANAGE_USERS => 'Gestionar usuarios del sistema',
            self::VIEW_USERS => 'Ver listado de usuarios',
            self::CREATE_USERS => 'Crear nuevos usuarios',
            self::EDIT_USERS => 'Editar usuarios existentes',
            self::DELETE_USERS => 'Eliminar usuarios',

            self::MANAGE_POSTS => 'Gestión completa de posts',
            self::VIEW_POSTS => 'Ver posts del sistema',
            self::CREATE_POSTS => 'Crear nuevos posts',
            self::EDIT_POSTS => 'Editar cualquier post',
            self::EDIT_OWN_POSTS => 'Editar solo posts propios',
            self::DELETE_POSTS => 'Eliminar posts',
            self::PUBLISH_POSTS => 'Publicar posts',
            self::READ_POSTS => 'Leer posts públicos',

            self::MANAGE_COMMENTS => 'Gestión completa de comentarios',
            self::MODERATE_COMMENTS => 'Moderar contenido',
            self::CREATE_COMMENTS => 'Crear comentarios',
            self::EDIT_COMMENTS => 'Editar comentarios',
            self::DELETE_COMMENTS => 'Eliminar comentarios',
            self::APPROVE_COMMENTS => 'Aprobar comentarios',

            self::MANAGE_SYSTEM => 'Gestión del sistema',
            self::MANAGE_SETTINGS => 'Configuración del sistema',
            self::VIEW_ANALYTICS => 'Ver estadísticas completas',
            self::VIEW_OWN_ANALYTICS => 'Ver estadísticas propias',

            self::MANAGE_NEWSLETTER => 'Gestionar newsletter',
            self::SEND_NEWSLETTER => 'Enviar newsletter',
            self::VIEW_SUBSCRIBERS => 'Ver suscriptores',

            self::MANAGE_MEDIA => 'Gestión completa de medios',
            self::UPLOAD_MEDIA => 'Subir archivos multimedia',
            self::DELETE_MEDIA => 'Eliminar archivos multimedia',

            self::MANAGE_PAGES => 'Gestión completa de páginas',
            self::CREATE_PAGES => 'Crear páginas estáticas',
            self::EDIT_PAGES => 'Editar páginas estáticas',
            self::DELETE_PAGES => 'Eliminar páginas estáticas',
        };
    }

    public function getGroup(): string
    {
        return match($this) {
            self::MANAGE_USERS, self::VIEW_USERS, self::CREATE_USERS, self::EDIT_USERS, self::DELETE_USERS => 'users',
            self::MANAGE_POSTS, self::VIEW_POSTS, self::CREATE_POSTS, self::EDIT_POSTS, self::EDIT_OWN_POSTS, self::DELETE_POSTS, self::PUBLISH_POSTS, self::READ_POSTS => 'posts',
            self::MANAGE_COMMENTS, self::MODERATE_COMMENTS, self::CREATE_COMMENTS, self::EDIT_COMMENTS, self::DELETE_COMMENTS, self::APPROVE_COMMENTS => 'comments',
            self::MANAGE_SYSTEM, self::MANAGE_SETTINGS, self::VIEW_ANALYTICS, self::VIEW_OWN_ANALYTICS => 'system',
            self::MANAGE_NEWSLETTER, self::SEND_NEWSLETTER, self::VIEW_SUBSCRIBERS => 'newsletter',
            self::MANAGE_MEDIA, self::UPLOAD_MEDIA, self::DELETE_MEDIA => 'media',
            self::MANAGE_PAGES, self::CREATE_PAGES, self::EDIT_PAGES, self::DELETE_PAGES => 'pages',
        };
    }
}