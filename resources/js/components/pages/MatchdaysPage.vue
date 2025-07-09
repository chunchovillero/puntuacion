<template>
    <div class="matchdays-page">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-calendar mr-2"></i>
                            Jornadas
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item active">Jornadas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <section class="content">
            <div class="container-fluid">
                <MatchdayManager />
            </div>
        </section>
    </div>
</template>

<script>
import MatchdayManager from '../MatchdayManager.vue';

export default {
    name: 'MatchdaysPage',
    components: {
        MatchdayManager
    }
};
</script>

<style scoped>
.matchdays-page {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}
</style>
