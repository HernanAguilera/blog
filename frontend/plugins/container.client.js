"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
var simple_container_1 = require("~/shared/container/simple-container");
var bindings_1 = require("~/shared/container/bindings");
var auth_store_1 = require("~/interface/stores/auth.store");
exports.default = defineNuxtPlugin(function () {
    var container = new simple_container_1.SimpleContainer();
    // Configurar bindings
    (0, bindings_1.configureContainer)(container);
    // Inicializar dependencias del auth store directamente
    try {
        var authStore = (0, auth_store_1.useAuthStore)();
        authStore.initializeDependencies(container.get('LoginUseCase'), container.get('RegisterUseCase'), container.get('LogoutUseCase'), container.get('AuthorizationService'), container.get('TokenStorage'));
    }
    catch (error) {
        console.warn('Error initializing auth dependencies:', error);
    }
    return {
        provide: {
            container: container
        }
    };
});
