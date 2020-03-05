import Vue from 'vue'
import VueRouter from 'vue-router'
import Subject from './components/Subject.vue'
import Question from './components/Question.vue'
Vue.use(VueRouter);
const routes = [
    {
        path : '/subject',
        component : Subject
    },
    {
        path : '/question',
        component : Question
    }
];

export default new VueRouter({
    mode : 'history',
    routes
});