<template>
    <div>
        <div class="mb-6">
            <h2 class="text-2xl font-bold">{{ isEditing ? 'Modifier la tâche' : 'Nouvelle tâche' }}</h2>
        </div>

        <form @submit.prevent="saveTask" class="space-y-4">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Titre *</label>
                <input type="text" id="title" v-model="task.title" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title[0] }}</p>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" v-model="task.description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description[0] }}</p>
            </div>

            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="completed" type="checkbox" v-model="task.completed"
                           class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                </div>
                <div class="ml-3 text-sm">
                    <label for="completed" class="font-medium text-gray-700">Tâche terminée</label>
                </div>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ isEditing ? 'Mettre à jour' : 'Créer' }}
                </button>
                <router-link :to="{ name: 'tasks.index' }" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Annuler
                </router-link>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'TaskForm',
    props: {
        id: {
            type: String,
            required: false
        }
    },
    data() {
        return {
            task: {
                title: '',
                description: '',
                completed: false
            },
            errors: {},
            loading: false,
            isEditing: false
        }
    },
    created() {
        if (this.id) {
            this.isEditing = true;
            this.fetchTask();
        }
    },
    methods: {
        fetchTask() {
            this.loading = true;
            axios.get(`/api/tasks/${this.id}`)
                .then(response => {
                    this.task = response.data;
                })
                .catch(error => {
                    console.error('Erreur lors du chargement de la tâche:', error);
                    alert('Erreur lors du chargement de la tâche. Veuillez réessayer.');
                    this.$router.push({ name: 'tasks.index' });
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        saveTask() {
            this.loading = true;
            this.errors = {};

            const method = this.isEditing ? 'put' : 'post';
            const url = this.isEditing ? `/api/tasks/${this.id}` : '/api/tasks';

            axios[method](url, this.task)
                .then(() => {
                    this.$router.push({ name: 'tasks.index' });
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data;
                    } else {
                        console.error('Erreur lors de l\'enregistrement de la tâche:', error);
                        alert('Erreur lors de l\'enregistrement de la tâche. Veuillez réessayer.');
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    }
}
</script>
