<template>
    <div class="activity-logs-page">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-history mr-2"></i>
                            Registro de Actividad
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item active">Actividad</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <section class="content">
            <div class="container-fluid">
                <ActivityLogManager />
            </div>
        </section>
    </div>
</template>

<script>
import ActivityLogManager from '../ActivityLogManager.vue';

export default {
    name: 'ActivityLogsPage',
    components: {
        ActivityLogManager
    }
};
</script>

<style scoped>
.activity-logs-page {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}
</style>
