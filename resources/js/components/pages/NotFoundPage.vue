<template>
    <div class="not-found-page">
        <div class="error-page">
            <h2 class="headline text-warning">404</h2>
            <div class="error-content">
                <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Página no encontrada.</h3>
                <p>
                    No pudimos encontrar la página que estás buscando.
                    Mientras tanto, puedes <router-link to="/">volver al inicio</router-link> o usar el menú de navegación.
                </p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Enlaces útiles</h3>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    <router-link to="/" class="list-group-item list-group-item-action">
                                        <i class="fas fa-home mr-2"></i> Inicio
                                    </router-link>
                                    <router-link to="/pilotos" class="list-group-item list-group-item-action">
                                        <i class="fas fa-user-ninja mr-2"></i> Pilotos
                                    </router-link>
                                    <router-link to="/clubes" class="list-group-item list-group-item-action">
                                        <i class="fas fa-flag mr-2"></i> Clubes
                                    </router-link>
                                    <router-link to="/campeonatos" class="list-group-item list-group-item-action">
                                        <i class="fas fa-trophy mr-2"></i> Campeonatos
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'NotFoundPage',
    mounted() {
        document.title = 'Página no encontrada | Sistema BMX';
    }
};
</script>

<style scoped>
.not-found-page {
    padding: 50px 20px;
    text-align: center;
}

.error-page {
    margin: 20px auto;
    max-width: 600px;
}

.headline {
    font-size: 100px;
    font-weight: 300;
    margin: 0;
    color: #f39c12 !important;
}

.error-content {
    margin-left: 190px;
    text-align: left;
}

@media (max-width: 767px) {
    .error-content {
        margin-left: 0;
        text-align: center;
    }
    
    .headline {
        font-size: 70px;
    }
}
</style>
