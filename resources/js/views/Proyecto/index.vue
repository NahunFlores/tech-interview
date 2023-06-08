<template>
    <div class="card">
        <div class="card-header" style="text-align: right;">
            <div class="row">
                <div class="col" style="text-align: left;">
                    <h3>Lista de Proyectos</h3>
                </div>
                <div class="col" style="text-align: right;">
                    <button class="btn btn-primary" @click="showAlertGuardarEditar([], false)">Nuevo</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <DataTable id="tabla_proyectos" :data="proyectos" :columns="columns"
                    class="table table-striped table-bordered display" :options="{
                        responsive: true,
                        autoWidth: false,
                        dom: 'Bfrtip', language: {
                            search: 'Buscar',
                            zeroRecords: 'No hay registros para mostrar',
                            info: 'Mostrando del _START_ a _END_ de _TOTAL_ resgistros',
                            infoFiltered: '(Filtrados de _MAX_ registros.)',
                            paginate: { first: 'Primero', previous: 'Anterior', next: 'Siguiente', last: 'Último' }
                        }, buttons: botones
                    }">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>nombre</th>
                            <th>Fecha Inicial</th>
                            <th>Fecha Final</th>
                            <th>Estado</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                </DataTable>
            </div>

        </div>
    </div>
</template>

<script>
import axios from 'axios';
import DataTable from 'datatables.net-vue3';
import DataTableLib from 'datatables.net-bs5';
import Buttons from 'datatables.net-buttons-bs5';
import ButtonsHTML5 from 'datatables.net-buttons/js/buttons.html5';
import Print from 'datatables.net-buttons/js/buttons.print';
import pdfmake from 'pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
pdfmake.vfs = pdfFonts.pdfMake.vfs;
import 'datatables.net-responsive-bs5';
import JsZip from 'jszip';
window.JSZip = JsZip;
DataTable.use(DataTableLib);
DataTable.use(pdfmake);
DataTable.use(Buttons);

export default {
    components: { DataTable, axios },
    data() {
        const token = localStorage.getItem("authToken");

        return {
            header: {
                headers: {
                    Authorization: `Bearer ${token}`, // Cabecera para todos los metodo de axio
                },
            },
            proyectos: [],
            nuevoProyecto: {
                nombre: '',
                fecha_incial: '',
                fecha_final: '',
                estado: null,
                id_user_crear:'',
            },
            proyecto: {
                nombre: '',
                fecha_incial: '',
                fecha_final: '',
                estado: '',
            },
            Toast: '',
            fecha_actual: '',
            columns: [
                { data: 'id' },
                { data: 'nombre' },
                { data: 'fecha_incial' },
                { data: 'fecha_final' },
                { data: 'estado' },
                {
                    data: function (data, type, row, meta) {


                        var botones = `<button type="button" id="ButtonEditar" class="editar ${data.id} edit-modal btn btn-warning">
                                    <abbr title="Editar"><span class="fa fa-edit"></span></abbr>
                                </button>`

                        if (data.estado === 'Pendiente') {
                            botones += `
                                <button type="button" id="ButtonEliminar" class="eliminar ${data.id} edit-modal btn btn-danger">
                                    <span class="fa fa-trash"></span>
                                </button>`;
                        }

                        botones+=`<button type="button" id="ButtonTareas" class="tareas ${data.id} edit-modal btn btn-success">
                                    <abbr title="Ver o Agregar Tareas"><span class="fa fa-tasks"></span></abbr>
                                </button>`

                        if (data.estado === 'Completada') {
                            return `<button type="button" id="ButtonTareas" class="tareas ${data.id} edit-modal btn btn-success">
                                    <abbr title="Ver o Agregar Tareas"><span class="fa fa-tasks"></span></abbr>
                                </button>`;
                        }else{
                            return botones;
                        }
                    }
                },
            ],
            botones: [
                {
                    title: 'Reporte de proyectos',
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success'
                },
                {
                    title: 'Reporte de proyectos',
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    className: 'btn btn-danger'
                },
                {
                    title: 'Reporte de proyectos',
                    extend: 'print',
                    text: 'Imprimir',
                    className: 'btn btn-dark'
                }
            ]
        }
    },
    created() {
        this.mostrarProyectos();
    },
    mounted() {
        if (localStorage.getItem("authToken")) {
            const user = JSON.parse(localStorage.getItem("user"));
            this.nuevoProyecto.id_user_crear =  user.id;
        }

        var d = new Date();
        this.fecha_actual = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);


        this.datosEditar('#tabla_proyectos tbody', 'tabla_proyectos', this);
        this.eliminar('#tabla_proyectos tbody', 'tabla_proyectos', this);
        this.verTareas('#tabla_proyectos tbody', 'tabla_proyectos', this);

        this.Toast = this.$swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', this.$swal.stopTimer)
                toast.addEventListener('mouseleave', this.$swal.resumeTimer)
            }
        })
    },
    methods: {
        mostrarTareas(id) {
            this.$router.push({ name: 'Lista_tareas' , params: { proyecto_id: id } });
        },
        async mostrarProyectos() {
            const ruta = '/api/proyecto';
            const header = this.header;

            await this.$store.dispatch('auth/traerDatosTabla', { ruta, header }).then((result) => {
                this.proyectos = result.data;
            });
        },
        async traerProyecto(id) {
            axios.get('/api/proyecto/' + id,this.header).then(
                response => {
                    this.proyecto = response.data.data;
                    this.showAlertGuardarEditar(this.proyecto, true);
                }
            ).catch(error => {
                this.proyecto = [];
            });
        },
        async guardarProyecto() {
            axios.post('/api/proyecto', this.nuevoProyecto,this.header).then(
                response => {
                    if (response.data.estado == 1) {
                        this.validadcionMensajes(response.data.errors);
                    } else {
                        this.mostrarProyectos();
                        this.Toast.fire({
                            icon: 'success',
                            title: 'Se guardo con exito'
                        })
                    }

                }
            ).catch(error => {
                console.log(this.nuevoProyecto)
            });
        },
        async actualizarProyectos(id) {
            axios.put('/api/proyecto/' + id, this.proyecto,this.header).then(
                response => {
                    if (response.data.estado == 1) {
                        this.validadcionMensajes(response.data.errors);
                    } else {
                        this.mostrarProyectos();
                        this.Toast.fire({
                            icon: 'success',
                            title: 'Se edito con exito'
                        })
                    }

                }
            ).catch(error => {
                console.log(error)
            });
        },
        async eliminarProyecto(id) {
            axios.delete('/api/proyecto/' + id,this.header).then(
                response => {
                    this.mostrarProyectos();
                    this.Toast.fire({
                        icon: 'success',
                        title: 'Se elimino con exito'
                    })
                }
            ).catch(error => {
                console.log(error)
            });
        },
        async showAlertGuardarEditar(data, edit) {
            await this.$swal.fire({
                title: edit ? 'Editar proyecto' : 'Nuevo proyecto',
                width: '45rem',
                html:
                    `<div class="container-fluid">
                    <div class="row">
                        <div class="col" style='text-align: left;'>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend2">Descripción</span>
                                </div>
                                <input type="text" name="nombre" id="nombre" value="${edit ? data.nombre : ''}" class="contact-email form-control">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col" style='text-align: left;'>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend2">Fecha Inicio</span>
                                </div>
                                <input type="date" name="fecha_incial" id="fecha_incial" value="${edit ? data.fecha_incial : this.fecha_actual}" class="contact-email form-control" min="${this.fecha_actual}">
                            </div>
                        </div>
                        <div class="col" style='text-align: left;'>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend2">Fecha Final</span>
                                </div>
                                <input type="date" name="fecha_final" id="fecha_final" value="${edit ? data.fecha_final : this.fecha_actual}" class="contact-email form-control" min="${this.fecha_actual}">
                            </div>
                        </div>
                    </div>
                    </div>`,
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                focusConfirm: false,
                showLoaderOnConfirm: true,
                backdrop: false,
                preConfirm: () => {
                    var formValues = [
                        document.getElementById("nombre").value,
                        document.getElementById("fecha_incial").value,
                        document.getElementById("fecha_final").value,
                    ]
                    if (formValues) {
                        if (edit) {
                            this.proyecto.nombre = formValues[0];
                            this.proyecto.fecha_incial = formValues[1];
                            this.proyecto.fecha_final = formValues[2];
                            this.actualizarProyectos(data.id);
                        } else {
                            this.nuevoProyecto.nombre = formValues[0];
                            this.nuevoProyecto.fecha_incial = formValues[1];
                            this.nuevoProyecto.fecha_final = formValues[2];
                            this.guardarProyecto();
                        }
                    }
                }
            })
        },
        async showAlertEliminar(id) {
            this.$swal.fire({
                title: '¿Está seguro?',
                text: "No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '¡Sí, bórralo!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.eliminarProyecto(id);
                }
            })
        },
        async validadcionMensajes(errores) {
            var html = `<ul>`;

            errores.map((error) => {
                html += `<li>`+error+`</li>`;
            });

            html += `</ul>`;
            this.$swal.fire({
                title: '<strong>Algunos campo requiere de atencion:</strong>',
                icon: 'info',
                html: html,
                showCloseButton: true,
                showCancelButton: false,
                focusConfirm: false,
                confirmButtonText:
                    '<i class="fa fa-thumbs-up"></i> Great!',
                confirmButtonAriaLabel: 'Thumbs up, great!',
                cancelButtonText:
                    '<i class="fa fa-thumbs-down"></i>',
                cancelButtonAriaLabel: 'Thumbs down'
            })


        },
        async datosEditar(tbody, table, refe) {
            $(tbody).on("click", "button.editar", function () {
                var proyectos = refe.proyectos;
                var proyecto = proyectos.filter((x) => x.id == $(this)[0].classList[1]);
                refe.traerProyecto(proyecto[0].id);
            });
        },
        async eliminar(tbody, table, refe) {
            $(tbody).on("click", "button.eliminar", function () {
                var proyectos = refe.proyectos;
                var proyecto = proyectos.filter((x) => x.id == $(this)[0].classList[1]);
                refe.showAlertEliminar(proyecto[0].id);
            });
        },
        async verTareas(tbody, table, refe) {
            $(tbody).on("click", "button.tareas", function () {
                var proyectos = refe.proyectos;
                var proyecto = proyectos.filter((x) => x.id == $(this)[0].classList[1]);
                refe.mostrarTareas(proyecto[0].id);
            });
        },
    }
}
</script>
<style>
@import 'datatables.net-bs5';
</style>'


