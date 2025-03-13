import { createRouter, createWebHistory } from 'vue-router';
import TaskList from '../views/TaskList.vue';
import TaskCreate from '../views/TaskCreate.vue';
import TaskEdit from '../views/TaskEdit.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: TaskList
    },
    {
        path: '/tasks/create',
        name: 'task-create',
        component: TaskCreate
    },
    {
        path: '/tasks/:id/edit',
        name: 'task-edit',
        component: TaskEdit
    }
];

const router = createRouter({
    history: createWebHistory('/'),
    routes
});

export default router;
