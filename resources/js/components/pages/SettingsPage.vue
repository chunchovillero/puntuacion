<template>
    <div class="settings-page">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-cog mr-2"></i>
                            Configuración
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item active">Configuración</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <section class="content">
            <div class="container-fluid">
                <SettingsManager />
            </div>
        </section>
    </div>
</template>

<script>
import SettingsManager from '../SettingsManager.vue';

export default {
    name: 'SettingsPage',
    components: {
        SettingsManager
    }
};
</script>

<style scoped>
.settings-page {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}
</style>
