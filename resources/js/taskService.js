import axios from 'axios';

const apiClient = axios.create({
    baseURL: 'http://127.0.0.1:8000/api', // A URL base da sua API Laravel
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

export default {
    fetchTasks() {
        return apiClient.get('/tasks');
    },
    createTask(task) {
        return apiClient.post('/tasks', task);
    },
    updateTask(id, task) {
        return apiClient.put(`/tasks/${id}`, task);
    },
    toggleTask(id) {
        return apiClient.patch(`/tasks/${id}/toggle`);
    },
    deleteTask(id) {
        return apiClient.delete(`/tasks/${id}`);
    }
};