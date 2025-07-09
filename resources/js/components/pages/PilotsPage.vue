<template>
    <div class="pilots-page">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-user-ninja mr-2"></i>
                            Pilotos BMX
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item active">Pilotos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <section class="content">
            <div class="container-fluid">
                <PilotManager />
            </div>
        </section>
    </div>
</template>

<script>
import PilotManager from '../PilotManager.vue';

export default {
    name: 'PilotsPage',
    components: {
        PilotManager
    }
};
</script>

<style scoped>
.pilots-page {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}
</style>
