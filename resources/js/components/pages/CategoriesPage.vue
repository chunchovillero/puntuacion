<template>
    <div class="categories-page">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-tags mr-2"></i>
                            Categorías
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item active">Categorías</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <section class="content">
            <div class="container-fluid">
                <CategoryManager />
            </div>
        </section>
    </div>
</template>

<script>
import CategoryManager from '../CategoryManager.vue';

export default {
    name: 'CategoriesPage',
    components: {
        CategoryManager
    }
};
</script>

<style scoped>
.categories-page {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}
</style>
