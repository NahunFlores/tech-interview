//Importacion para las rutas
const vInicioSession = () => import('./views/Auth/Login.vue');
const vUsers = () => import('./views/Auth/Register.vue');
const vInicio = () => import('./views/Principal/Inicio.vue');
const vMetricas= () => import('./views/components/Metricas.vue');
const IndexTarea = () => import('./views/Tarea/index.vue');
const vCalendar = () => import('./views/components/Calendario.vue');
const IndexProyeto = () => import('./views/Proyecto/index.vue');

const guest = (to, from, next) => {
    if (!localStorage.getItem("authToken")) {
        return next();
    } else {
        return next("/");
    }
};
const auth = (to, from, next) => {
    if (localStorage.getItem("authToken")) {
        return next();
    } else {
        return next("/login");
    }
};

export const routes = [
    {
        path: "/login",
        name: "Login",
        component: vInicioSession
    },
    {
        path: "/",
        name: "Inicio",
        component: vCalendar,
        beforeEnter: auth,
    },
    {
        path: "/metricas",
        name: "metricas",
        component: vMetricas,
        beforeEnter: auth,
    },
    {
        path: "/users",
        name: "Lista_usuarios",
        component: vUsers
    },
    {
        path: "/seguimiento",
        name: "Seguimiento",
        beforeEnter: auth,
        component: vCalendar
    },
    {
        name: 'Lista_tareas',
        path: '/tarea/:proyecto_id',
        component: IndexTarea,
        beforeEnter: auth,
    },
    {
        name: 'Lista_proyectos',
        path: '/proyectos',
        component: IndexProyeto,
        beforeEnter: auth,
    }
];


/**    {
        path: "/usuarios",
        name: "Usuarios",
        component: vInicioSession
    }, */
