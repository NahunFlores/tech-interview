<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col" style="text-align: left;">
                    <h3>Lista de Tareas - {{ proyecto.nombre }}</h3>
                </div>
                <div class="col" style="text-align: right;">
                    <button class="btn btn-primary" @click="showAlertGuardarEditar([], false)">Nuevo</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <DataTable id="tabla_tareas" :data="tareas" :columns="columns"
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
                            <th>Descripción</th>
                            <th>Prioridad</th>
                            <th>Fecha Limite</th>
                            <th>Responsable</th>
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
            proyecto_id: '',
            proyecto: '',
            tareas: [],
            usuarios: [],
            tarea: {
                descripcion: '',
                prioridad: '',
                fecha_limite: '',
                estado: '',
                responsable: '',
            },
            traeTarea: null,
            Toast: '',
            fecha_actual: '',
            columns: [
                { data: 'id' },
                { data: 'descripcion' },
                { data: 'prioridad' },
                { data: 'fecha_limite' },
                { data: 'name' },
                { data: 'estado' },
                {
                    data: function (data, type, row, meta) {
                        var botones = `<button type="button" id="ButtonEditar" class="editar ${data.id} edit-modal btn btn-warning">
                                    <span class="fa fa-edit"></span>
                                </button>`

                        if (data.estado === 'Pendiente') {
                            botones += `
                                <button type="button" id="ButtonEliminar" class="eliminar ${data.id} edit-modal btn btn-danger">
                                    <span class="fa fa-trash"></span>
                                </button>`;
                        }

                        if (data.estado === 'Completada') {
                            return '';
                        } else {
                            return botones;
                        }


                    }
                },
            ],
            botones: [
                {
                    title: 'Reporte de Tareas',
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success'
                },
                {
                    title: 'Reporte de Tareas',
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    className: 'btn btn-danger'
                },
                {
                    title: 'Reporte de Tareas',
                    extend: 'print',
                    text: 'Imprimir',
                    className: 'btn btn-dark'
                }
            ]
        }
    },
    mounted() {

        var d = new Date();
        this.fecha_actual = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
        this.proyecto_id = this.$route.params.proyecto_id;

        this.mostraTareas();
        this.traeProyectos(this.proyecto_id);
        this.datosEditar('#tabla_tareas tbody', 'tabla_tareas', this);
        this.eliminar('#tabla_tareas tbody', 'tabla_tareas', this);

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
        async mostraTareas() {
            const ruta = '/api/tarea/' + this.proyecto_id;
            const header = this.header;

            await this.$store.dispatch('auth/traerDatosTabla', { ruta, header }).then((result) => {
                this.tareas = result.data;
                this.usuarios = result.usuarios;
            });

        },
        async traerTarea(id) {
            axios.get('/api/operaciones/tarea/' + id, this.header).then(
                response => {
                    this.traeTarea = response.data.data;
                    this.showAlertGuardarEditar(this.traeTarea, true);
                }
            ).catch(function (error, response) {

            });
        },
        async guardarTareas() {
            axios.post('/api/tarea/' + this.proyecto_id, this.tarea, this.header).then(
                response => {
                    if (response.data.estado == 1) {
                        this.validadcionMensajes(response.data.errors);
                    } else {
                        this.mostraTareas();
                        this.Toast.fire({
                            icon: 'success',
                            title: 'Se guardo con exito'
                        })
                    }

                }
            ).catch(function (error) {
                console.log(JSON.stringify(event));
            });
        },
        async actualizarTareas(id) {
            axios.put('/api/operaciones/tarea/' + id, this.tarea, this.header).then(
                response => {
                    if (response.data.estado == 1) {
                        this.validadcionMensajes(response.data.errors);
                    } else {
                        this.mostraTareas();
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
        async eliminarTareas(id) {
            axios.delete('/api/operaciones/tarea/' + id, this.header).then(
                response => {
                    this.mostraTareas();
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

            var opciones = '';

            this.usuarios.map((usuario) => {
                opciones += `<option ${!edit ? '' : data.responsable == usuario.id ? 'selected' : ''} value="${usuario.id}">${usuario.name}</option>`;
            });

            await this.$swal.fire({
                toast: false,
                title: edit ? 'Editar Tarea' : 'Nueva Tarea',
                width: '45rem',
                html:
                    `<div class="container-fluid">
                    <div class="row">
                        <div class="col" style='text-align: left;'>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend2">Descripción</span>
                                </div>
                                <textarea name="descripcion" id="descripcion" class="contact-message form-control">${edit ? data.descripcion : ''}</textarea>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col" style='text-align: left;'>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend2">Estado</span>
                                </div>
                                <input type="text" name="estado" id="estado" class="contact-email form-control" value='${edit ? data.estado : 'Pendiente'}' readonly>
                            </div>
                        </div>
                        <div class="col" style='text-align: left;'>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroupPrepend2">Fecha Limite</span>
                                </div>
                                <input type="date" name="fecha_limite" id="fecha_limite" value="${edit ? data.fecha_limite : this.fecha_actual}" class="contact-email form-control" min="${this.proyecto.fecha_incial}" max="${this.proyecto.fecha_final}">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col">
                            <label for="contact-subject">Prioridad</label>
                                    <br>
                                    <br>
			                        <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" ${!edit ? '' : data.prioridad == 'Alta' ? 'checked' : ""}  name="prioridad" id="prioridad1" value="Alta">
                                            <label class="form-check-label">Alta</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" ${!edit ? '' : data.prioridad == 'Media' ? 'checked' : ""} name="prioridad" id="prioridad2" value="Media">
                                            <label class="form-check-label">Media</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" ${!edit ? 'checked' : data.prioridad == 'Baja' ? 'checked' : ""} name="prioridad" id="prioridad3" value="Baja">
                                            <label class="form-check-label">Baja</label>
                                        </div>
			                        </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-2"></div>
                        <div class="col-8">
                            <label for="contact-subject">Responsable</label>
                            <br>
                            <br>
                            <select class="form-control" name="responsable" id="responsable">
                                <option value="">Seleccione un resposable</option>
                                ${opciones}
                            </select>
                        </div>
                        <div class="col-2"></div>
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
                        document.getElementById("descripcion").value,
                        document.getElementById("estado").value,
                        document.getElementById("fecha_limite").value,
                        document.getElementById("prioridad1").checked,
                        document.getElementById("prioridad2").checked,
                        document.getElementById("prioridad3").checked,
                        document.getElementById("responsable").value,
                    ]
                    if (formValues) {
                        this.tarea.descripcion = formValues[0];
                        this.tarea.prioridad = formValues[3] ? "Alta" : formValues[4] ? "Media" : "Baja";
                        this.tarea.fecha_limite = formValues[2];
                        this.tarea.estado = formValues[1];
                        this.tarea.responsable = formValues[6];


                        if (edit) {
                            this.actualizarTareas(data.id);
                        } else {
                            this.guardarTareas();
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
                    this.eliminarTareas(id);
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
                var tareas = refe.tareas;
                var tarea = tareas.filter((x) => x.id == $(this)[0].classList[1]);
                console.log(tarea[0].id);
                refe.traerTarea(tarea[0].id);
            });
        },
        async eliminar(tbody, table, refe) {
            $(tbody).on("click", "button.eliminar", function () {
                var tareas = refe.tareas;
                var tarea = tareas.filter((x) => x.id == $(this)[0].classList[1]);
                console.log(tarea[0].id);
                refe.showAlertEliminar(tarea[0].id);
            });
        },

        async traeProyectos(id) {

            const ruta = '/api/proyecto/' + id;
            const header = this.header;

            await this.$store.dispatch('auth/traerDatosTabla', { ruta, header }).then((result) => {
                this.proyecto = result.data;
            });

        },
    }
}
</script>
<style>
@import 'datatables.net-bs5';
</style>'


