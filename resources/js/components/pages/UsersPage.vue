<template>
    <div class="users-page">
        <!-- Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <i class="fas fa-users mr-2"></i>
                            Usuarios
                        </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item">
                                <router-link to="/">Inicio</router-link>
                            </li>
                            <li class="breadcrumb-item active">Usuarios</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <section class="content">
            <div class="container-fluid">
                <UserManager />
            </div>
        </section>
    </div>
</template>

<script>
import UserManager from '../UserManager.vue';

export default {
    name: 'UsersPage',
    components: {
        UserManager
    }
};
</script>

<style scoped>
.users-page {
    padding: 0;
}

.content-header {
    padding: 15px 0;
}
</style>
