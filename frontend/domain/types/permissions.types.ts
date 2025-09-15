export const ROLE = {
    SUPER_ADMIN: 'super_admin',
    ADMIN: 'admin',
    COLLABORATOR: 'collaborator',
    GUEST: 'guest'
} as const;

export const PERMISSION = {
    // Post permissions
    CREATE_POST: 'create_post',
    EDIT_POST: 'edit_post',
    DELETE_POST: 'delete_post',
    PUBLISH_POST: 'publish_post',
    MODERATE_POST: 'moderate_post',

    // User permissions
    MANAGE_USERS: 'manage_users',
    VIEW_USERS: 'view_users',
    EDIT_USER: 'edit_user',
    DELETE_USER: 'delete_user',

    // System permissions
    MANAGE_SETTINGS: 'manage_settings',
    ACCESS_ADMIN: 'access_admin',
    VIEW_ANALYTICS: 'view_analytics',
    MANAGE_COMMENTS: 'manage_comments',

    // Content permissions
    MANAGE_CATEGORIES: 'manage_categories',
    MANAGE_MEDIA: 'manage_media'
} as const;

export type RoleType = typeof ROLE[keyof typeof ROLE];
export type PermissionType = typeof PERMISSION[keyof typeof PERMISSION];

// Role-Permission mapping
export const ROLE_PERMISSIONS: Record<RoleType, PermissionType[]> = {
    [ROLE.SUPER_ADMIN]: [
        PERMISSION.CREATE_POST,
        PERMISSION.EDIT_POST,
        PERMISSION.DELETE_POST,
        PERMISSION.PUBLISH_POST,
        PERMISSION.MODERATE_POST,
        PERMISSION.MANAGE_USERS,
        PERMISSION.VIEW_USERS,
        PERMISSION.EDIT_USER,
        PERMISSION.DELETE_USER,
        PERMISSION.MANAGE_SETTINGS,
        PERMISSION.ACCESS_ADMIN,
        PERMISSION.VIEW_ANALYTICS,
        PERMISSION.MANAGE_COMMENTS,
        PERMISSION.MANAGE_CATEGORIES,
        PERMISSION.MANAGE_MEDIA
    ],
    [ROLE.ADMIN]: [
        PERMISSION.CREATE_POST,
        PERMISSION.EDIT_POST,
        PERMISSION.DELETE_POST,
        PERMISSION.PUBLISH_POST,
        PERMISSION.MODERATE_POST,
        PERMISSION.VIEW_USERS,
        PERMISSION.ACCESS_ADMIN,
        PERMISSION.VIEW_ANALYTICS,
        PERMISSION.MANAGE_COMMENTS,
        PERMISSION.MANAGE_CATEGORIES,
        PERMISSION.MANAGE_MEDIA
    ],
    [ROLE.COLLABORATOR]: [
        PERMISSION.CREATE_POST,
        PERMISSION.EDIT_POST,
        PERMISSION.ACCESS_ADMIN
    ],
    [ROLE.GUEST]: []
};