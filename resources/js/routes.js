//Importacion para las rutas de tareas
const IndexTarea = ()=> import('./components/Tarea/index.vue');
const Create_UpdateTarea  = ()=> import('./components/Tarea/create_update.vue');


export const routes = [
    {
        name: 'Lista_tareas',
        path: '/',
        component: IndexTarea
    },
    {
        name: 'Lista_tareas',
        path: '/tarea',
        component: IndexTarea
    },
    {
        name: 'Crear_tareas',
        path: '/tarea/create',
        component: Create_UpdateTarea
    },
    {
        name: 'Editar_tareas',
        path: '/tarea/edit/:id',
        component: Create_UpdateTarea
    }
]
