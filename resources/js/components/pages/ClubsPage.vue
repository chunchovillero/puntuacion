<template>
    <div class="clubs-page">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-flag mr-2"></i>
                            Clubes
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item active">Clubes</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <section class="content">
            <div class="container-fluid">
                <ClubManager />
            </div>
        </section>
    </div>
</template>

<script>
import ClubManager from '../ClubManager.vue';

export default {
    name: 'ClubsPage',
    components: {
        ClubManager
    }
};
</script>

<style scoped>
.clubs-page {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}
</style>
