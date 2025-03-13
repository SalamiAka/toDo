<template>
    <div>
        <div class="page-header">
            <h1 class="text-2xl font-bold">Mes tâches</h1>
            <div class="filters">
                <select v-model="statusFilter" class="form-control filter-select">
                    <option value="">Tous les statuts</option>
                    <option value="pending">À faire</option>
                    <option value="in_progress">En cours</option>
                    <option value="completed">Terminé</option>
                </select>
                <select v-model="priorityFilter" class="form-control filter-select">
                    <option value="">Toutes les priorités</option>
                    <option value="low">Basse</option>
                    <option value="medium">Moyenne</option>
                    <option value="high">Haute</option>
                </select>
            </div>
        </div>

        <div class="empty-state" v-if="!loading && filteredTasks.length === 0">
            <div class="empty-icon">📋</div>
            <h3>Aucune tâche trouvée</h3>
            <p v-if="statusFilter || priorityFilter">Ajustez vos filtres ou </p>
            <router-link to="/tasks/create" class="btn-primary">Créer une nouvelle tâche</router-link>
        </div>

        <div v-else class="tasks-container">
            <div v-if="loading" class="text-center py-8">
                <div class="spinner"></div>
                <p>Chargement des tâches...</p>
            </div>

            <transition-group name="task-list" tag="div" class="task-grid">
                <div v-for="task in filteredTasks" :key="task.id" class="card task-card">
                    <div class="task-header">
                        <div class="badges">
              <span :class="['badge', `badge-${task.status}`]">
                {{ formatStatus(task.status) }}
              </span>
                            <span :class="['badge', `badge-${task.priority}`]">
                {{ formatPriority(task.priority) }}
              </span>
                        </div>
                        <div class="task-actions">
                            <button @click="editTask(task)" class="btn-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button @click="confirmDelete(task)" class="btn-icon text-danger">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </div>
                    </div>

                    <h3 class="task-title">{{ task.title }}</h3>
                    <p class="task-description">{{ task.description || 'Aucune description' }}</p>

                    <div class="task-footer">
                        <div v-if="task.due_date" class="task-due-date">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            {{ formatDate(task.due_date) }}
                        </div>
                        <div class="task-checkbox">
                            <label class="checkbox-container">
                                <input
                                    type="checkbox"
                                    :checked="task.status === 'completed'"
                                    @change="toggleTaskStatus(task)"
                                >
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </transition-group>
        </div>

        <div v-if="showDeleteModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-content">
                    <h3 class="modal-title">Confirmer la suppression</h3>
                    <p>Êtes-vous sûr de vouloir supprimer la tâche "{{ taskToDelete?.title }}" ?</p>
                    <div class="modal-actions">
                        <button @click="cancelDelete" class="btn-secondary">Annuler</button>
                        <button @click="deleteTask" class="btn-danger">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TaskService from '../services/TaskService';
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';

export default {
    name: 'TaskList',

    setup() {
        const router = useRouter();
        const tasks = ref([]);
        const loading = ref(true);
        const statusFilter = ref('');
        const priorityFilter = ref('');
        const showDeleteModal = ref(false);
        const taskToDelete = ref(null);

        const fetchTasks = async () => {
            loading.value = true;
            try {
                const response = await TaskService.getTasks();
                tasks.value = response.data.data;
            } catch (error) {
                console.error('Error fetching tasks:', error);
            } finally {
                loading.value = false;
            }
        };

        const filteredTasks = computed(() => {
            return tasks.value.filter(task => {
                const matchStatus = statusFilter.value ? task.status === statusFilter.value : true;
                const matchPriority = priorityFilter.value ? task.priority === priorityFilter.value : true;
                return matchStatus && matchPriority;
            });
        });

        const formatStatus = (status) => {
            const statusMap = {
                'pending': 'À faire',
                'in_progress': 'En cours',
                'completed': 'Terminé'
            };
            return statusMap[status] || status;
        };

        const formatPriority = (priority) => {
            const priorityMap = {
                'low': 'Basse',
                'medium': 'Moyenne',
                'high': 'Haute'
            };
            return priorityMap[priority] || priority;
        };

        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return new Intl.DateTimeFormat('fr-FR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            }).format(date);
        };

        const editTask = (task) => {
            router.push({ name: 'task-edit', params: { id: task.id } });
        };

        const confirmDelete = (task) => {
            taskToDelete.value = task;
            showDeleteModal.value = true;
        };

        const cancelDelete = () => {
            showDeleteModal.value = false;
            taskToDelete.value = null;
        };

        const deleteTask = async () => {
            if (!taskToDelete.value) return;

            try {
                await TaskService.deleteTask(taskToDelete.value.id);
                tasks.value = tasks.value.filter(t => t.id !== taskToDelete.value.id);
                showDeleteModal.value = false;
                taskToDelete.value = null;
            } catch (error) {
                console.error('Error deleting task:', error);
            }
        };

        const toggleTaskStatus = async (task) => {
            const newStatus = task.status === 'completed' ? 'pending' : 'completed';

            try {
                await TaskService.updateTask(task.id, {
                    ...task,
                    status: newStatus
                });

                // Update local state
                const index = tasks.value.findIndex(t => t.id === task.id);
                if (index !== -1) {
                    tasks.value[index].status = newStatus;
                }
            } catch (error) {
                console.error('Error updating task status:', error);
            }
        };

        onMounted(fetchTasks);

        return {
            tasks,
            loading,
            statusFilter,
            priorityFilter,
            showDeleteModal,
            taskToDelete,
            filteredTasks,
            formatStatus,
            formatPriority,
            formatDate,
            editTask,
            confirmDelete,
            cancelDelete,
            deleteTask,
            toggleTaskStatus
        };
    }
};
</script>

<style scoped>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.filters {
    display: flex;
    gap: 0.75rem;
}

.filter-select {
    min-width: 150px;
    max-width: 180px;
}

.task-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.task-card {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.badges {
    display: flex;
    gap: 0.5rem;
}

.task-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-icon {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    color: #666;
    transition: all 0.2s ease;
}

.btn-icon:hover {
    background-color: #f3f4f6;
    color: var(--primary);
}

.text-danger {
    color: var(--danger);
}

.btn-icon.text-danger:hover {
    background-color: rgba(229, 57, 53, 0.1);
}

.task-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    word-break: break-word;
}

.task-description {
    color: #666;
    flex-grow: 1;
    margin-bottom: 1rem;
    word-break: break-word;
}

.task-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    color: #666;
}

.task-due-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.checkbox-container {
    display: block;
    position: relative;
    padding-left: 25px;
    cursor: pointer;
    user-select: none;
}

.checkbox-container input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: #fff;
    border: 2px solid #ddd;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.checkbox-container:hover input ~ .checkmark {
    border-color: var(--primary);
}

.checkbox-container input:checked ~ .checkmark {
    background-color: var(--primary);
    border-color: var(--primary);
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.checkbox-container input:checked ~ .checkmark:after {
    display: block;
}

.checkbox-container .checkmark:after {
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.empty-state {
    text-align: center;
    padding: 3rem 0;
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
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

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 100;
}

.modal-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    max-width: 500px;
    width: 90%;
}

.modal-content {
    padding: 1.5rem;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 1.5rem;
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
}

.btn-secondary:hover {
    background-color: #e5e7eb;
}

.btn-danger {
    background-color: var(--danger);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    border: none;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-danger:hover {
    background-color: #d32f2f;
}

.task-list-enter-active,
.task-list-leave-active {
    transition: all 0.3s ease;
}

.task-list-enter-from,
.task-list-leave-to {
    opacity: 0;
    transform: translateY(30px);
}
</style>
