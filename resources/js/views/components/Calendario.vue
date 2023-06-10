<template>
    <div class="card">
        <div class="card-body" style="text-align: right;">
            <FullCalendar :options="calendarOptions" />
        </div>
    </div>

</template>
<script>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'

export default {
    components: {
        FullCalendar,
    },
    data() {
        const token = localStorage.getItem("authToken");

        return {
            header: {
                headers: {
                    Authorization: `Bearer ${token}`, // Cabecera para todos los metodo de axio
                },
            },
            Toast: '',
            acceptedFileTypes: ['.docx', '.doc', '.pdf'],
            proyectos: null,
            calendarOptions: {
                plugins: [dayGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek,dayGridDay'
                },
                buttonText: {
                    today: 'Hoy',
                    month: 'Mes',
                    week: 'Semana',
                    day: 'Día',
                },
                //dateClick: ,
                events: this.mostrarProyectos,
                eventClick: this.showAlertEventoTarea
            }
        }
    },
    mounted() {
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
        async mostrarProyectos() {
            //(ruta, datos, header)
            var eventos = [];

            const ruta = '/api/proyecto';
            const header = this.header;

            var proyectos = [];

            await this.$store.dispatch('auth/traerDatosTabla', { ruta, header }).then((result) => {
                proyectos = result.data;
            });

            proyectos.map((proyecto) => {

                eventos.push({
                    evento: proyecto,
                    tipo: 'proyecto',
                    title: proyecto.nombre,
                    start: proyecto.fecha_incial,
                    end: proyecto.fecha_final,
                    backgroundColor: '#FF6666',
                    borderColor: '#FF6666',
                });

                if(proyecto.estado === 'En progreso' || proyecto.estado === 'En revisión' || proyecto.estado === 'Aplazada'){
                    proyecto.tareas.map((tarea) => {
                    var fechaFinal = new Date(tarea.fecha_limite);
                    fechaFinal.setDate(fechaFinal.getDate() + 2);
                    eventos.push({
                        evento: tarea,
                        tipo: 'tarea',
                        title: tarea.name == null ? 'Descripcion: ' + tarea.descripcion : 'Responsable: ' + tarea.name + ',   Descripcion: ' + tarea.descripcion,
                        start: proyecto.fecha_incial,
                        end: fechaFinal,
                        allDay: true,
                        backgroundColor: '#ADD8E6',
                        borderColor: '#ADD8E6',
                        textColor: 'black',
                    });
                })
                }

            });

            return eventos;
        },

        async mostrarComentarios(id,descripcion) {
            //(ruta, datos, header)
            var eventos = [];

            const ruta = '/api/comentarios/'+id;
            const header = this.header;

            var comentarios = [];
            var htmlTarea = '<div class="body-chat">';

            await this.$store.dispatch('auth/traerDatosTabla', { ruta, header }).then((result) => {
                comentarios = result.data;
            });

            comentarios.map((comentario) => {
                const date = new Date(comentario.created_at);


                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                const hours = String(date.getHours()).padStart(2, '0');
                const minutes = String(date.getMinutes()).padStart(2, '0');

                const formattedDateTime = `${year}-${month}-${day} ${hours}:${minutes}`;

                htmlTarea+=`<div class="container-chat">
                                <label for="">${comentario.email}</label>
                                <br>
                                <p>${comentario.contenido}</p>
                                <span class="time-right">${formattedDateTime}</span>
                            </div>
                                `
            });

            htmlTarea+=`</div>`;

            await this.$swal.fire({
                toast: false,
                title: 'Tarea: '+descripcion,
                width: '45rem',
                html: htmlTarea,
                showCancelButton:  false,
                showConfirmButton:  false,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Comentarios',
                focusConfirm: false,
                showLoaderOnConfirm: true,
                backdrop: true,
                preConfirm: () => {

                }
            });

        },
        async showAlertEventoTarea(arg) {
            const comparacion = arg.event.extendedProps.tipo === 'proyecto';
            var title = (comparacion ? 'Proyecto: ' + arg.event.extendedProps.evento.nombre : 'Tarea: ' + arg.event.extendedProps.evento.descripcion);
            var evento = arg.event.extendedProps.evento;

            var htmlTarea = `<div class="container-fluid">
                                    <div class="row">
                                        <div class="col" style='text-align: left;'>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend2">Descripción</span>
                                                </div>
                                                <textarea name="descripcion" id="descripcion" readonly class="contact-message form-control">${evento.descripcion}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col" style='text-align: left;'>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend2">Responsable</span>
                                                </div>
                                                <input type="text" value="${evento.name}" readonly class="contact-email form-control" >
                                            </div>
                                        </div>
                                        <div class="col" style='text-align: left;'>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend2">Prioridad</span>
                                                </div>
                                                <input type="text" value="${evento.prioridad}" readonly class="contact-email form-control" >
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
                                                <select class="form-control" name="estado" id="estado">
                                                    <option ${evento.estado === 'Pendiente' ? 'selected':''} value="Pendiente">Pendiente</option>
                                                    <option ${evento.estado === 'En progreso' ? 'selected':''} value="En progreso">En progreso</option>
                                                    <option ${evento.estado === 'Completada' ? 'selected':''} value="Completada">Completada</option>
                                                    <option ${evento.estado === 'En revisión' ? 'selected':''} value="En revisión">En revisión</option>
                                                    <option ${evento.estado === 'Aplazada' ? 'selected':''} value="Aplazada">Aplazada</option>
                                                    <option ${evento.estado === 'Bloqueada' ? 'selected':''} value="Bloqueada">Bloqueada</option>
                                                    <option ${evento.estado === 'Cancelada' ? 'selected':''} value="Cancelada">Cancelada</option>
                                                    <option ${evento.estado === 'Rechazada' ? 'selected':''} value="Rechazada">Rechazada</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col" style='text-align: left;'>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend2">Fecha Limite</span>
                                                </div>
                                                <input type="date" value="${evento.fecha_limite}" readonly class="contact-email form-control" >
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-2"></div>
                                        <div class="col-8" style='text-align: left;'>
                                            <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroupPrepend2">Comentar</span>
                                                    </div>
                                                    <textarea name="comentario" id="comentario" class="contact-message form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-2"></div>
                                    </div>
                                </div>`;


            var htmlProyecto = `<div class="container-fluid">
                                <div class="row">
                                    <div class="col" style='text-align: left;'>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend2">Fecha Inicio</span>
                                            </div>
                                            <input type="date" readonly value="${evento.fecha_incial}" class="contact-email form-control">
                                        </div>
                                    </div>
                                    <div class="col" style='text-align: left;'>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroupPrepend2">Fecha Final</span>
                                            </div>
                                            <input type="date" readonly value="${evento.fecha_final}" class="contact-email form-control">
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-2"></div>
                                    <div class="col-8" style='text-align: left;'>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroupPrepend2">Estado</span>
                                                </div>
                                                <select class="form-control" name="estado" id="estado">
                                                    <option ${evento.estado === 'Pendiente' ? 'selected':''} value="Pendiente">Pendiente</option>
                                                    <option ${evento.estado === 'En progreso' ? 'selected':''} value="En progreso">En progreso</option>
                                                    <option ${evento.estado === 'Completada' ? 'selected':''} value="Completada">Completada</option>
                                                    <option ${evento.estado === 'En revisión' ? 'selected':''} value="En revisión">En revisión</option>
                                                    <option ${evento.estado === 'Aplazada' ? 'selected':''} value="Aplazada">Aplazada</option>
                                                    <option ${evento.estado === 'Bloqueada' ? 'selected':''} value="Bloqueada">Bloqueada</option>
                                                    <option ${evento.estado === 'Cancelada' ? 'selected':''} value="Cancelada">Cancelada</option>
                                                    <option ${evento.estado === 'Rechazada' ? 'selected':''} value="Rechazada">Rechazada</option>
                                                </select>
                                            </div>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                            </div>`;

            await this.$swal.fire({
                toast: false,
                title: title,
                width: '45rem',
                html: comparacion ? htmlProyecto : htmlTarea,
                showCancelButton: comparacion ? false : true,
                showConfirmButton:  true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Comentarios',
                focusConfirm: false,
                showLoaderOnConfirm: true,
                backdrop: true,
                preConfirm: () => {

                    const data = {
                        'estado': document.getElementById("estado").value,
                        'comentario': comparacion ? '' : document.getElementById("comentario").value ,
                        'id': evento.id,
                        'tipo': comparacion ? 'proyecto' : 'tareas',
                    };
                    const header = this.header;
                    const ruta = '/api/estado';

                    this.$store.dispatch('auth/actualizarEstado', { ruta, data, header }).then((result) => {
                        this.Toast.fire({
                            icon: 'success',
                            title: 'Se actaulizo el estado exito'
                        })
                        window.location.reload();
                    });
                }
            }).then((result) => {
                if (result.dismiss === 'cancel') {

                    this.mostrarComentarios(evento.id, evento.descripcion);
                }
            });
        },
    }
}
</script>
