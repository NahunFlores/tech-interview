<template>
    <div class="login form-bg">
        <div class="container">
            <br>
            <br>
            <br>
            <div class="row">
                <div class="col-lg-offset-4 col-lg-3 col-md-offset-6 col-md-2">
                </div>
                <div class="col-lg-offset-3 col-lg-6 col-md-offset-2 col-md-8">
                    <div class="form-container">
                        <div class="form-icon">
                            <i class="fa fa-user-circle"></i>
                            <span class="signup"><a href=""></a></span>
                        </div>
                        <form class="form-horizontal">
                            <h3 class="title">Inicio de Sesión</h3>
                            <div class="alert alert-danger forgot-pass" role="alert" v-if="error.credenciales">
                                {{ error.credenciales }}
                            </div>
                            <div class="form-group">
                                <span class="input-icon"><i class="fa fa-envelope"></i></span>
                                <input class="form-control" type="email" :class="{ 'is-invalid': error.email }"
                                    placeholder="Correo Electrónico" v-model="details.email">
                                <div class="invalid-feedback forgot-pass" v-if="error.email">
                                    {{ error.email[0] }}
                                </div>
                            </div>
                            <div class="form-group">
                                <span class="input-icon"><i class="fa fa-lock"></i></span>
                                <input class="form-control" type="password" :class="{ 'is-invalid': error.password }"
                                    placeholder="Contraseña" v-model="details.password">
                                <div class="invalid-feedback forgot-pass" v-if="error.password">
                                    {{ error.password[0] }}
                                </div>
                            </div>
                            <button type="button" class="btn signin" @click="login">Acceder</button>
                            <span class="forgot-pass"><a href="#">¿Olvidó su nombre de usuario/contraseña?</a></span>
                        </form>
                    </div>
                </div>
                <div class="col-lg-offset-4 col-lg-3 col-md-offset-6 col-md-2">
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    data: function () {
        return {
            details: {
                email: null,
                password: null
            },
            errors: null
        };
    },
    computed: {
        error() {
            return this.$store.state.errors;
        }
    },
    methods: {
        login() {
            this.$store.dispatch('auth/sendLoginRequest', this.details).then(() => {
                window.location.href = '/';
            })
        },
    }
};
</script>
