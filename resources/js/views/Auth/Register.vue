<template>
    <div class="card">
        <div class="card-header" style="text-align: right;">
            <div class="row">
                <div class="col" style="text-align: left;">
                    <h3>Listado de Usuarios</h3>
                </div>
                <div class="col" style="text-align: right;">
                    <button class="btn btn-primary" @click="showAlertGuardarEditar([], false)">Nuevo</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <DataTable id="tabla_Usuarios" :data="Usuarios" :columns="columns"
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
                            <th>Correo Electronico</th>
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
            Usuarios: [],
            nuevoUsuario: {
                name: '',
                email: '',
                password: '',
                confrimPassword: '',
            },
            Usuario: {
                name: '',
                email: '',
                password: '',
                confrimPassword: '',
            },
            Toast: '',
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'email' },
                {
                    data: function (data, type, row, meta) {

                        if (data.email === 'admin@gmail.com') {
                            return ``;
                        }
                        return `<button type="button" id="ButtonEditar" class="editar ${data.id} edit-modal btn btn-warning">
                                    <abbr title="Editar"><span class="fa fa-edit"></span></abbr>
                                </button>
                                <button type="button" id="ButtonEliminar" class="eliminar ${data.id} edit-modal btn btn-danger">
                                    <span class="fa fa-trash"></span>
                                </button>`;

                    }
                },
            ],
            botones: [
                {
                    title: 'Reporte de Usuarios',
                    extend: 'excelHtml5',
                    text: 'Excel',
                    className: 'btn btn-success'
                },
                {
                    title: 'Reporte de Usuarios',
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    className: 'btn btn-danger'
                },
                {
                    title: 'Reporte de Usuarios',
                    extend: 'print',
                    text: 'Imprimir',
                    className: 'btn btn-dark'
                }
            ]
        }
    },
    mounted() {
        if (localStorage.getItem("authToken")) {
            const user = JSON.parse(localStorage.getItem("user"));
            this.nuevoUsuario.id_user_crear = user.id;
        }
        this.mostrarUsuarios();

        this.datosEditar('#tabla_Usuarios tbody', 'tabla_Usuarios', this);
        this.eliminar('#tabla_Usuarios tbody', 'tabla_Usuarios', this);
        this.verTareas('#tabla_Usuarios tbody', 'tabla_Usuarios', this);

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
        async mostrarUsuarios() {
            const ruta = '/api/users';
            const header = this.header;

            await this.$store.dispatch('auth/traerDatosTabla', { ruta, header }).then((result) => {
                this.Usuarios = result.data;
            });
        },
        async traerUsuario(id) {
            axios.get('/api/users/' + id, this.header).then(
                response => {
                    this.Usuario = response.data.data;
                    this.showAlertGuardarEditar(this.Usuario, true);
                }
            ).catch(error => {
                this.Usuario = [];
            });
        },
        guardarUsuario() {
            axios.post('/api/users/store', this.nuevoUsuario, this.header).then(
                response => {
                    this.mostrarUsuarios();
                    this.Toast.fire({
                        icon: 'success',
                        title: 'Se guardo con exito'
                    })
                }
            );
        },
        async actualizarUsuarios(id) {
            axios.put('api/users/update/' + id, this.Usuario, this.header).then(
                response => {
                    this.mostrarUsuarios();
                    this.Toast.fire({
                        icon: 'success',
                        title: 'Se edito con exito'
                    })
                }
            ).catch(error => {
                console.log(error)
            });
        },
        async eliminarUsuario(id) {
            axios.delete('/api/Usuario/' + id, this.header).then(
                response => {
                    this.mostrarUsuarios();
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
            this.$swal.fire({
                title: edit ? 'Editar Usuario' : 'Nuevo Usuario',
                width: '45rem',
                html:
                    `<div class="container-fluid">
                        <div class="row">
                            <div class="col" style='text-align: left;'>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend2">Nombre</span>
                                    </div>
                                    <input type="text" name="name" id="name" value="${edit ? data.name : ''}" class="contact-email form-control">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col" style='text-align: left;'>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroupPrepend2">Correo Electronico</span>
                                    </div>
                                    <input type="email" name="email" id="email" value="${edit ? data.email : ''}" class="contact-email form-control">
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
                    const user = {
                        'name': document.getElementById("name").value,
                        'email': document.getElementById("email").value
                    };


                    if (!user['name']) {
                        this.$swal.showValidationMessage('El nombre no puede estar vacio')
                    }
                    if (!user['email']) {
                        this.$swal.showValidationMessage('El correo no puede estar vacio')
                    }

                    if (edit) {
                        this.Usuario.name = user['name'];
                        this.Usuario.email = user['email'];
                        this.actualizarUsuarios(data.id);
                    } else {
                        this.nuevoUsuario.name = user['name'];
                        this.nuevoUsuario.email = user['email'];
                        this.guardarUsuario();
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
                    this.eliminarUsuario(id);
                }
            })
        },
        async datosEditar(tbody, table, refe) {
            $(tbody).on("click", "button.editar", function () {
                var Usuarios = refe.Usuarios;
                var Usuario = Usuarios.filter((x) => x.id == $(this)[0].classList[1]);
                refe.traerUsuario(Usuario[0].id);
            });
        },
        async eliminar(tbody, table, refe) {
            $(tbody).on("click", "button.eliminar", function () {
                var Usuarios = refe.Usuarios;
                var Usuario = Usuarios.filter((x) => x.id == $(this)[0].classList[1]);
                refe.showAlertEliminar(Usuario[0].id);
            });
        },
        async verTareas(tbody, table, refe) {
            $(tbody).on("click", "button.tareas", function () {
                var Usuarios = refe.Usuarios;
                var Usuario = Usuarios.filter((x) => x.id == $(this)[0].classList[1]);
                refe.mostrarTareas(Usuario[0].id);
            });
        },
    }
}
</script>
<style>
@import 'datatables.net-bs5';
</style>'


