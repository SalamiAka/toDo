<template>
    <div>
        <div class="page-header">
            <h1 class="text-2xl font-bold">Modifier la tâche</h1>
        </div>

        <div v-if="loading" class="text-center py-8">
            <div class="spinner"></div>
            <p>Chargement de la tâche...</p>
        </div>

        <div v-else-if="notFound" class="not-found">
            <div class="empty-icon">🔍</div>
            <h3>Tâche non trouvée</h3>
            <p>La tâche que vous recherchez n'existe pas ou a été supprimée.</p>
            <router-link to="/" class="btn-primary">Retour à la liste</router-link>
        </div>

        <div v-else class="card form-card">
            <form @submit.prevent="submitForm">
                <div class="form-group">
                    <label for="title" class="form-label">Titre <span class="required">*</span></label>
                    <input
                        id="title"
                        v-model="task.title"
                        type="text"
                        class="form-control"
                        :class="{ 'is-invalid': errors.title }"
                        placeholder="Entrez le titre de la tâche"
                        required
                    >
                    <div v-if="errors.title" class="error-message">{{ errors.title }}</div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea
                        id="description"
                        v-model="task.description"
                        class="form-control"
                        :class="{ 'is-invalid': errors.description }"
                        placeholder="Décrivez la tâche (optionnel)"
                        rows="4"
                    ></textarea>
                    <div v-if="errors.description" class="error-message">{{ errors.description }}</div>
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="status" class="form-label">Statut</label>
                        <select
                            id="status"
                            v-model="task.status"
                            class="form-control"
                            :class="{ 'is-invalid': errors.status }"
                        >
                            <option value="pending">À faire</option>
                            <option value="in_progress">En cours</option>
                            <option value="completed">Terminé</option>
                        </select>
                        <div v-if="errors.status" class="error-message">{{ errors.status }}</div>
                    </div>

                    <div class="form-group col">
                        <label for="priority" class="form-label">Priorité</label>
                        <select
                            id="priority"
                            v-model="task.priority"
                            class="form-control"
                            :class="{ 'is-invalid': errors.priority }"
                        >
                            <option value="low">Basse</option>
                            <option value="medium">Moyenne</option>
                            <option value="high">Haute</option>
                        </select>
                        <div v-if="errors.priority" class="error-message">{{ errors.priority }}</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="due_date" class="form-label">Date d'échéance</label>
                    <input
                        id="due_date"
                        v-model="task.due_date"
                        type="datetime-local"
                        class="form-control"
                        :class="{ 'is-invalid': errors.due_date }"
                    >
                    <div v-if="errors.due_date" class="error-message">{{ errors.due_date }}</div>
                </div>

                <div class="form-actions">
                    <router-link to="/" class="btn-secondary">Annuler</router-link>
                    <button type="submit" class="btn-primary" :disabled="isSubmitting">
                        <span v-if="isSubmitting" class="spinner-sm"></span>
                        {{ isSubmitting ? 'Enregistrement...' : 'Enregistrer les modifications' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import TaskService from '../services/TaskService';
import { ref, reactive, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

export default {
    name: 'TaskEdit',

    setup() {
        const router = useRouter();
        const route = useRoute();
        const taskId = route.params.id;

        const loading = ref(true);
        const notFound = ref(false);
        const isSubmitting = ref(false);

        const task = reactive({
            id: null,
            title: '',
            description: '',
            status: 'pending',
            priority: 'medium',
            due_date: ''
        });

        const errors = reactive({
            title: '',
            description: '',
            status: '',
            priority: '',
            due_date: ''
        });

        const fetchTask = async () => {
            loading.value = true;
            try {
                const response = await TaskService.getTask(taskId);
                const taskData = response.data.data;

                // Format the date for datetime-local input
                if (taskData.due_date) {
                    const date = new Date(taskData.due_date);
                    const localDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
                        .toISOString()
                        .slice(0, 16);
                    taskData.due_date = localDate;
                }

                Object.assign(task, taskData);
            } catch (error) {
                console.error('Error fetching task:', error);
                notFound.value = true;
            } finally {
                loading.value = false;
            }
        };

        const resetErrors = () => {
            Object.keys(errors).forEach(key => {
                errors[key] = '';
            });
        };

        const validateForm = () => {
            resetErrors();
            let isValid = true;

            if (!task.title.trim()) {
                errors.title = 'Le titre est obligatoire';
                isValid = false;
            }

            return isValid;
        };

        const submitForm = async () => {
            if (!validateForm()) return;

            isSubmitting.value = true;
            try {
                await TaskService.updateTask(taskId, task);
                router.push({ path: '/' });
            } catch (error) {
                console.error('Error updating task:', error);
                if (error.response && error.response.data && error.response.data.errors) {
                    const responseErrors = error.response.data.errors;
                    Object.keys(responseErrors).forEach(key => {
                        errors[key] = responseErrors[key][0];
                    });
                }
            } finally {
                isSubmitting.value = false;
            }
        };

        onMounted(fetchTask);

        return {
            task,
            errors,
            loading,
            notFound,
            isSubmitting,
            submitForm
        };
    }
};
</script>

<style scoped>
.form-card {
    max-width: 700px;
    margin: 0 auto;
}

.form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.col {
    flex: 1;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
}

.error-message {
    color: var(--danger);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.required {
    color: var(--danger);
}

.is-invalid {
    border-color: var(--danger);
}

.spinner-sm {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s linear infinite;
    margin-right: 0.5rem;
}

.spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border-left-color: var(--primary);
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.not-found {
    text-align: center;
    padding: 3rem 0;
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.btn-secondary {
    background-color: #f3f4f6;
    color: #333;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-secondary:hover {
    background-color: #e5e7eb;
}
</style>
