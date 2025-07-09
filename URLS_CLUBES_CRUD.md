# URLs para CRUD de Clubes - Vue.js Frontend

## URLs Públicas (Sin autenticación requerida)

### Para mostrar datos (Solo lectura)
- **GET** `/clubes` - Lista todos los clubes (vista HTML)
- **GET** `/clubes/vue` - Página Vue para gestión de clubes (solo vista)
- **GET** `/clubes/{id}` - Mostrar un club específico (vista HTML)

### API Pública para Vue.js (Solo lectura)
- **GET** `/admin/api/clubs` - Lista todos los clubes (JSON para Vue)
- **GET** `/admin/api/clubs/export` - Exportar clubes (CSV/Excel)

---

## URLs Autenticadas (Requieren login de admin)

### Gestión Web (HTML tradicional)
- **GET** `/gestionar/clubes` - Lista todos los clubes (admin)
- **GET** `/gestionar/clubes/vue` - Página Vue para gestión completa
- **GET** `/gestionar/clubes/crear` - Formulario para crear club
- **POST** `/gestionar/clubes` - Crear nuevo club
- **GET** `/gestionar/clubes/{id}` - Ver club específico (admin)
- **GET** `/gestionar/clubes/{id}/editar` - Formulario para editar club
- **PUT** `/gestionar/clubes/{id}` - Actualizar club
- **DELETE** `/gestionar/clubes/{id}` - Eliminar club

### API para Vue.js (CRUD completo)
- **GET** `/gestionar/api/clubs` - Lista todos los clubes (JSON)
- **POST** `/gestionar/api/clubs` - Crear nuevo club (JSON)
- **GET** `/gestionar/api/clubs/{id}` - Obtener club específico (JSON)
- **PUT** `/gestionar/api/clubs/{id}` - Actualizar club (JSON)
- **DELETE** `/gestionar/api/clubs/{id}` - Eliminar club
- **GET** `/gestionar/api/clubs/export` - Exportar clubes

---

## URLs Recomendadas para Componentes Vue

### Para componentes público (ClubList.vue)
```javascript
// Solo lectura
axios.get('/admin/api/clubs') // Lista de clubes
```

### Para componentes autenticados (ClubManager.vue)
```javascript
// CRUD completo
axios.get('/gestionar/api/clubs')           // Lista
axios.post('/gestionar/api/clubs', data)    // Crear
axios.get('/gestionar/api/clubs/' + id)     // Obtener
axios.put('/gestionar/api/clubs/' + id, data) // Actualizar
axios.delete('/gestionar/api/clubs/' + id)  // Eliminar
```

---

## Estado Actual

✅ **Todas las rutas están configuradas correctamente**
✅ **Los métodos del controlador están implementados**
✅ **Error de ruta GET resuelto**
✅ **Botón "Ver" en vista pública corregido - ahora dirige a `/clubes/{id}` sin login**

## Próximos pasos

1. ✅ Verificar que el componente Vue está usando las URLs correctas
2. ✅ Remover el código de debug del componente Vue
3. ✅ Confirmar que la funcionalidad completa está funcionando
4. ✅ Corregir redirección del botón "Ver" en vista pública

**¡El sistema está completamente funcional!** 🎉
