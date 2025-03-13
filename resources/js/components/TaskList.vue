<template>
    <div>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Liste des tâches</h2>
            <router-link :to="{ name: 'tasks.create' }" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Nouvelle tâche
            </router-link>
        </div>

        <div v-if="loading" class="text-center py-4">
            Chargement des tâches...
        </div>

        <div v-else-if="tasks.length === 0" class="bg-gray-100 p-4 rounded text-center">
            Aucune tâche trouvée. Créez-en une nouvelle !
        </div>

        <div v-else class="space-y-4">
            <div v-for="task in tasks" :key="task.id" class="border rounded p-4 hover:shadow-md">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold" :class="{ 'line-through': task.completed }">
                            {{ task.title }}
                        </h3>
                        <p class="text-gray-600 mt-1" v-if="task.description">{{ task.description }}</p>
                        <span class="inline-block mt-2 px-2 py-1 text-xs rounded"
                              :class="task.completed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                            {{ task.completed ? 'Terminée' : 'En cours' }}
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        <button @click="toggleTaskStatus(task)" class="p-1 rounded hover:bg-gray-100" :title="task.completed ? 'Marquer comme non terminée' : 'Marquer comme terminée'">
                            <svg class="w-5 h-5" :class="task.completed ? 'text-green-500' : 'text-gray-400'" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        <router-link :to="{ name: 'tasks.edit', params: { id: task.id }}" class="p-1 rounded hover:bg-gray-100" title="Modifier">
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                            </svg>
                        </router-link>
                        <button @click="deleteTask(task)" class="p-1 rounded hover:bg-gray-100" title="Supprimer">
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'TaskList',
    data() {
        return {
            tasks: [],
            loading: true,
        }
    },
    created() {
        this.fetchTasks();
    },
    methods: {
        fetchTasks() {
            this.loading = true;
            axios.get('/api/tasks')
                .then(response => {
                    this.tasks = response.data;
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des tâches:', error);
                    alert('Erreur lors du chargement des tâches. Veuillez réessayer.');
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        toggleTaskStatus(task) {
            axios.put(`/api/tasks/${task.id}`, {
                title: task.title,
                description: task.description,
                completed: !task.completed
            })
                .then(response => {
                    const index = this.tasks.findIndex(t => t.id === task.id);
                    this.tasks[index].completed = !task.completed;
                })
                .catch(error => {
                    console.error('Erreur lors de la mise à jour du statut:', error);
                    alert('Erreur lors de la mise à jour du statut. Veuillez réessayer.');
                });
        },
        deleteTask(task) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')) {
                axios.delete(`/api/tasks/${task.id}`)
                    .then(() => {
                        this.tasks = this.tasks.filter(t => t.id !== task.id);
                    })
                    .catch(error => {
                        console.error('Erreur lors de la suppression de la tâche:', error);
                        alert('Erreur lors de la suppression de la tâche. Veuillez réessayer.');
                    });
            }
        }
    }
}
</script>
