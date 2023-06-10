<template>
    <div class="card">
        <div class="card-header">
           <h3>Metricas de los usuarios - Tareas</h3>
        </div>
        <div class="card-body" style="text-align: center;">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Completadas</th>
                        <th scope="col">En Proceso</th>
                        <th scope="col">Pendientes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in datos" :key="item.id">
                        <td>{{ item.id }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.email }}</td>
                        <td>{{ item.Completadas }} ({{ ((item.Completadas/(item.Completadas+item.EnProceso+item.Pendiente))*100).toFixed(2) }}%)</td>
                        <td>{{ item.EnProceso }} ({{ ((item.EnProceso/(item.Completadas+item.EnProceso+item.Pendiente))*100).toFixed(2) }}%)</td>
                        <td>{{ item.Pendiente }} ({{ ((item.Pendiente/(item.Completadas+item.EnProceso+item.Pendiente))*100).toFixed(2) }}%)</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
export default {
    components: {

    },
    data() {
        const token = localStorage.getItem("authToken");

        return {
            header: {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            },
            datos: null,
        }
    },
    mounted() {
        this.mostraTareas();
    },
    methods: {
        async mostraTareas() {
            const ruta = '/api/metricas';
            const header = this.header;

            await this.$store.dispatch('auth/traerDatosTabla', { ruta, header }).then((result) => {
                this.datos = result.datos;
            });

        },
    },
}
</script>
