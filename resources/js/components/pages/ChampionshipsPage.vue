<template>
    <div class="championships-page">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-trophy mr-2"></i>
                            Campeonatos
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item active">Campeonatos</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <section class="content">
            <div class="container-fluid">
                <ChampionshipManager />
            </div>
        </section>
    </div>
</template>

<script>
import ChampionshipManager from '../ChampionshipManager.vue';

export default {
    name: 'ChampionshipsPage',
    components: {
        ChampionshipManager
    }
};
</script>

<style scoped>
.championships-page {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}
</style>
