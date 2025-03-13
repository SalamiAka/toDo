// resources/js/services/TaskService.js
import axios from 'axios';

const apiClient = axios.create({
    baseURL: '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    }
});

export default {
    getTasks() {
        return apiClient.get('/tasks');
    },

    getTask(id) {
        return apiClient.get(`/tasks/${id}`);
    },

    createTask(task) {
        return apiClient.post('/tasks', task);
    },

    updateTask(id, task) {
        return apiClient.put(`/tasks/${id}`, task);
    },

    deleteTask(id) {
        return apiClient.delete(`/tasks/${id}`);
    }
};
