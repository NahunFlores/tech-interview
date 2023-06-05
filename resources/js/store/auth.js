import axios from "axios";

export default {
    namespaced: true,

    state: {
        userData: null
    },

    getters: {
        user: state => state.userData
    },

    mutations: {
        setUserData(state, user) {
            state.userData = user;
        }
    },

    actions: {
        getUserData({ commit }) {
            axios
                .get(process.env.VUE_APP_API_URL + "user")
                .then(response => {
                    commit("setUserData", response.data);
                })
                .catch(() => {
                    localStorage.removeItem("authToken");
                });
        },
        sendLoginRequest({ commit }, data) {
            commit("setErrors", {}, { root: true });
            return axios
                .post("/api/login", data)
                .then(response => {
                    if (response.data.estado == 1) {
                        commit("setErrors", response.data.data, { root: true });
                    }
                    if (response.data.estado == 0) {
                        commit("setUserData", response.data.user);
                        localStorage.setItem("authToken", response.data.token);
                        localStorage.setItem("user", JSON.stringify(response.data.user));
                    }
                });
        },
        sendLogoutRequest({ commit }) {
            axios.post("/api/logout", {
                headers: {
                    Authorization: `Bearer ${localStorage.getItem("authToken")}`, // Cabecera para todos los metodo de axio
                },
            }).then(() => {
                commit("setUserData", null);
            });
        },
        async traerDatosTabla({ commit }, data) {
            var res = null;
            await axios.get(data.ruta, data.header).then((response) => {
                res = response;
            }).catch((err) => {

            });
            return res.data;
        },
        async actualizarEstado({ commit }, data) {
            var res = null;
            await axios.put(data.ruta, data.data, data.header).then((response) => {
                res = response;
            }).catch((err) => {
                console.log(err);
            });
            return res.data.data;
        },
    }
};
