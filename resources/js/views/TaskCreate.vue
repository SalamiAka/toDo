<template>
    <div>
        <div class="page-header">
            <h1 class="text-2xl font-bold">Créer une nouvelle tâche</h1>
        </div>

        <div class="card form-card">
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
                        {{ isSubmitting ? 'Enregistrement...' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import TaskService from '../services/TaskService';
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';

export default {
    name: 'TaskCreate',

    setup() {
        const router = useRouter();
        const isSubmitting = ref(false);

        const task = reactive({
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
                await TaskService.createTask(task);
                router.push({ path: '/' });
            } catch (error) {
                console.error('Error creating task:', error);
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

        return {
            task,
            errors,
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

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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
