<template>
    <main>
        <!-- Navbar -->
        <div id="gradBgn">
            <div class="position-relative" v-if="user">
                <div class="d-flex justify-content-between p-3 align-items-center position-fixed w-100">
                    <div>
                        <router-link to="/" class="custom-link" v-show="user">
                            <img :src="imagePath" alt="Imagen" class="logo">
                        </router-link>
                    </div>
                    <div>
                        <div class="d-flex position-relative">
                            <div class="d-none custom-d-sm-block" v-show="user">
                                <router-link to="/proyectos" class="custom-link">Proyectos</router-link>
                            </div>

                            <div class="d-none custom-d-sm-block" v-show="user">
                                <router-link to="/seguimiento" class="custom-link">Seguimiento</router-link>
                            </div>
                            <div class="d-none custom-d-sm-block" v-show="user">
                                <router-link to="/metricas" class="custom-link">Metricas</router-link>
                            </div>
                            <div class="d-none custom-d-sm-block" v-show="user">
                                <router-link to="/users" class="custom-link">Usuarios</router-link>
                            </div>
                            <div class="d-none custom-d-sm-block" v-show="user">
                                <a id="dropdownBtn" class="custom-link">
                                    {{ userName }}
                                </a>
                                <ul id="dropdownMenu" class="dropdown-menu dropdown-menu-dark">
                                    <li><a class="dropdown-item custom-link-2" href="#" @click="logout">Salir</a></li>
                                </ul>
                            </div>
                            <div id="subMenu">
                                <span class="hamLine"></span>
                                <span class="hamLine"></span>
                                <span class="hamLine"></span>
                            </div>
                            <div id="fullPageMenu" class="visually-hidden">
                                <div
                                    class="anton d-flex align-items-center position-fixed vh-100 w-100 start-0 top-0 blurOverlay">
                                    <div class="container">
                                        <div class="display-1" v-show="user"><router-link to="/proyectos"
                                                class="custom-link-2">Proyectos</router-link></div>
                                        <div class="display-1" v-show="user"><router-link to="/seguimiento"
                                                class="custom-link-2">Seguimiento</router-link></div>
                                        <div class="display-1" v-show="user"><router-link to="/metricas"
                                                class="custom-link-2">Metricas</router-link></div>
                                        <div class="display-1" v-show="user"><router-link to="/users"
                                                class="custom-link-2">Usuarios</router-link></div>
                                        <div class="display-1"><a class="custom-link-2" href="#" @click="logout">Salir</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid container-margin">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <router-view></router-view>
                    </div>
                    <div class="col-md-1 d-xs-none">
                    </div>
                </div>
            </div>
        </div>
        <!-- Navbar -->
    </main>
</template>

<script>
export default {
    data() {
        return {
            imagePath: 'img/logo.png',
            userName: '',
        };
    },
    computed: {
        user() {
            if (localStorage.getItem('user')) {
                return localStorage.getItem('user');
            }
            return this.$store.state.auth.userData;
        }
    },

    mounted() {
        this.userName = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')).name : ''
    },

    methods: {
        logout() {
            localStorage.clear();
            this.$store.dispatch('auth/sendLogoutRequest');
            window.location.href = '/login';
        },
    }
};
</script>
