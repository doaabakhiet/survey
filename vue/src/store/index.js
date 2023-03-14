import { createStore } from "vuex";
import axiosClient from "../axios";
// const tmpSurveys = [
//   {
//     id: 1,
//     title: "doaa",
//     slug: "doaa",
//     status: "draft",
//     image: "https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600",
//     description: "sssssssss",
//     created_at: "2023-03-02 23:18:40",
//     updated_at: "2023-03-02 23:18:40",
//     expire_date: "2023-03-02 23:18:40",
//     questions: [
//       {
//         id: 1,
//         type: "select",
//         question: "djfijdeedd",
//         description: null,
//         data: {
//           options: [
//             { uuid: "asdfghjkhgfhg", text: "usa" },
//             { uuid: "asdfghjkhgfkj", text: "india" },
//             { uuid: "asdfghjkhgfxf", text: "german" },
//             { uuid: "asdfghjkhgfrerd", text: "england" },
//           ],
//         },
//       },
//       {
//         id: 2,
//         type: "checkbox",
//         question: "djfijdeedd",
//         description: null,
//         data: {
//           options: [
//             { uuid: "asdfghjkhgfhg", text: "usa" },
//             { uuid: "asdfghjkhgfkj", text: "india" },
//             { uuid: "asdfghjkhgfxf", text: "german" },
//             { uuid: "asdfghjkhgfrerd", text: "england" },
//           ],
//         },
//       },
//       {
//         id: 3,
//         type: "checkbox",
//         question: "djfijdeedd",
//         description: null,
//         data: {
//           options: [
//             { uuid: "asdfghjkhgfhg", text: "usa" },
//             { uuid: "asdfghjkhgfkj", text: "india" },
//             { uuid: "asdfghjkhgfxf", text: "german" },
//             { uuid: "asdfghjkhgfrerd", text: "england" },
//           ],
//         },
//       },
//       {
//         id: 4,
//         type: "radio",
//         question: "djfijdeedd",
//         description: null,
//         data: {
//           options: [
//             { uuid: "asdfghjkhgfhg", text: "usa" },
//             { uuid: "asdfghjkhgfkj", text: "india" },
//             { uuid: "asdfghjkhgfxf", text: "german" },
//             { uuid: "asdfghjkhgfrerd", text: "england" },
//           ],
//         },
//       },
//       {
//         id: 5,
//         type: "text",
//         question: "djfijdeedd",
//         description: null,
//         data: {},
//       },
//       {
//         id: 5,
//         type: "textarea",
//         question: "djfijdeedd",
//         description: null,
//         data: {},
//       },
//     ],
//   },
//   {
//     id: 2,
//     title: "doaa",
//     slug: "doaa",
//     status: "draft",
//     image: "https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600",
//     description: "sssssssss",
//     created_at: "2023-03-02 23:18:40",
//     updated_at: "2023-03-02 23:18:40",
//     expire_date: "2023-03-02 23:18:40",
//     questions: [],
//   },
//   {
//     id: 3,
//     title: "doaa",
//     slug: "doaa",
//     status: "draft",
//     image: "https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600",
//     description: "sssssssss",
//     created_at: "2023-03-02 23:18:40",
//     updated_at: "2023-03-02 23:18:40",
//     expire_date: "2023-03-02 23:18:40",
//     questions: [],
//   },
//   {
//     id: 4,
//     title: "doaa",
//     slug: "doaa",
//     status: "draft",
//     image: "https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600",
//     description: "sssssssss",
//     created_at: "2023-03-02 23:18:40",
//     updated_at: "2023-03-02 23:18:40",
//     expire_date: "2023-03-02 23:18:40",
//     questions: [],
//   },
// ];
const questionTypes = ["text", "checkbox", "select", "radio", "textarea"];
const store = createStore({
  state: {
    user: {
      data: {},
      token: sessionStorage.getItem("TOKEN"),
    },
    surveys: {
      loading: false,
      data: [],
      links: [],
    },
    questionTypes: [...questionTypes],
    currentSurvey: {
      loading: false,
      data: {},
    },
    notification: {
      show: false,
      message: null,
      type: null,
    },
    dashboard:{
      loading: false,
      data: {},
    }
    // surveys: {
    //   loading: false,
    //   links: [],
    //   data: [],
    // },
  },
  getters: {},
  actions: {
    register({ commit }, user) {
      return axiosClient.post("/register", user).then((res) => {
        commit("setUser", res);
        return res;
      });
      // return fetch(`http://localhost:8000/api/register`,{
      //     headers:{
      //         "Content-Type":"application/json",
      //         Accept:"application/json",
      //     },
      //     method:"POST",
      //     body:JSON.stringify(user),
      // })
      // .then((res)=>res.json())
      // .then((res)=>{
      //     commit("setUser",res);
      //     return res;
      // });
    },
    login({ commit }, user) {
      return axiosClient.post("/login", user).then((res) => {
        commit("setUser", res);
        return res;
      });
    },
    logout({ commit }) {
      return axiosClient.get("/logout").then((response) => {
        commit("logout");
        return response;
      });
    },
    saveSurvey({ commit }, survey) {
      delete survey.image_url;
      let response;
      console.log(survey);
      if (survey.id) {
        response = axiosClient
          .put(`/survey/${survey.id}`, survey)
          .then((res) => {
            commit("setCurrentSurvey", res.data);
            return res;
          });
      } else {
        response = axiosClient.post("/survey", survey).then((res) => {
          commit("setCurrentSurvey", res.data);
          return res;
        });
      }
      return response;
    },
    getSurvey({ commit }, id) {
      commit("setCurrentSurveyLoading", true);
      return axiosClient
        .get(`/survey/${id}`)
        .then((res) => {
          commit("setCurrentSurvey", res.data);
          commit("setCurrentSurveyLoading", false);
          return res;
        })
        .catch((err) => {
          commit("setCurrentSurveyLoading", false);
          throw err;
        });
    },
    deleteSurvey({ commit }, id) {
      return axiosClient.delete(`/survey/${id}`);
    },
    getSurveys({ commit }, { url = null } = {}) {
      url = url || "/survey";
      commit("setSurveysLoading", true);
      return axiosClient.get(url).then((res) => {
        commit("setSurveysLoading", false);
        commit("setSurveys", res.data);
        return res;
      });
    },
    getSurveyBySlug({ commit }, slug) {
      commit("setCurrentSurveyLoading", true);
      return axiosClient.get(`survey-by-slug/${slug}`).then((res) => {
        commit("setCurrentSurvey", res.data);
        commit("setCurrentSurveyLoading", false);
        return res;
      }).catch((err)=>{
        commit("setCurrentSurveyLoading", false);
        throw err;
      });
    },
    saveSurveyAnswer({commit},{surveyId,answers}){
      return axiosClient.post(`survey/${surveyId}/answer`,{answers});
    },
    getDashboardData({commit}){
      commit('dashboardLoading',true);
      return axiosClient.get('dashboard/index').then((res)=>{
        commit('dashboardLoading',false);
        commit('setDashboardData',res.data);
        return res;
      }).catch((err)=>{
        commit("dashboardLoading", false);
        throw err;
      });

    }
  },
  mutations: {
    logout: (state) => {
      state.user.data = {};
      state.user.token = null;
      sessionStorage.removeItem("TOKEN");
    },
    setUser: (state, userData) => {
      state.user.token = userData.data.token;
      state.user.user = userData.data.user;
      sessionStorage.setItem("TOKEN", userData.data.token);
      // sessionStorage.setItem('user',userData.data.user);
    },
    // saveSurvey: (state, survey) => {
    //   state.surveys = [...state.surveys, survey.data];
    // },
    // updateSurvey: (state, survey) => {
    //   state.surveys = state.surveys.map((s) => {
    //     if (s.id == survey.data.id) {
    //       return survey.data;
    //     }
    //     return s;
    //   });
    // },
    setDashboardData:(state, data) => {
      state.dashboard.data = data;
    },
    dashboardLoading: (state, loading) => {
      state.dashboard.loading = loading;
    },
    setCurrentSurveyLoading: (state, loading) => {
      state.currentSurvey.loading = loading;
    },
    setSurveysLoading: (state, loading) => {
      state.surveys.loading = loading;
    },
    setCurrentSurvey: (state, survey) => {
      state.currentSurvey.data = survey.data;
    },
    setSurveys: (state, surveys) => {
      state.surveys.data = surveys;
      state.surveys.links = surveys.meta.links;
    },
    notify: (state, { message, type }) => {
      state.notification.show = true;
      state.notification.type = type;
      state.notification.message = message;
      setTimeout(() => {
        state.notification.show = false;
      }, 3000);
    },
  },
  modules: {},
});
export default store;
