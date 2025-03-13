<template>
    <div class="app-container">
        <header class="app-header">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-primary">TaskMaster</h1>
                <nav>
                    <router-link to="/" class="btn-link mr-2">Accueil</router-link>
                    <router-link to="/tasks/create" class="btn-primary">Nouvelle tâche</router-link>
                </nav>
            </div>
        </header>

        <main class="container mx-auto px-4 py-8 min-h-screen">
            <router-view />
        </main>

        <footer class="app-footer">
            <div class="container mx-auto px-4 py-3 text-center text-gray-500">
                &copy; 2025 TaskMaster Application
            </div>
        </footer>
    </div>
</template>

<script>
export default {
    name: 'App'
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

:root {
    --primary: #3f51b5;
    --primary-dark: #303f9f;
    --accent: #ff4081;
    --light: #f5f5f5;
    --danger: #e53935;
    --success: #43a047;
    --warning: #fb8c00;
    --gray-light: #f9f9f9;
    --gray: #eeeeee;
    --text: #333333;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: var(--light);
    color: var(--text);
    line-height: 1.6;
}

.container {
    width: 100%;
    max-width: 1140px;
    margin: 0 auto;
}

.app-header {
    background-color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 10;
}

.app-footer {
    background-color: white;
    border-top: 1px solid var(--gray);
    padding: 1rem 0;
    margin-top: auto;
}

.text-primary {
    color: var(--primary);
}

.btn-primary {
    background-color: var(--primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s ease;
}

.btn-primary:hover {
    background-color: var(--primary-dark);
}

.btn-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
}

.btn-link:hover {
    text-decoration: underline;
}

.card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.2s ease;
}

.form-control:focus {
    border-color: var(--primary);
    outline: none;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 16px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-pending {
    background-color: var(--warning);
    color: white;
}

.badge-in-progress {
    background-color: var(--primary);
    color: white;
}

.badge-completed {
    background-color: var(--success);
    color: white;
}

.badge-high {
    background-color: var(--danger);
    color: white;
}

.badge-medium {
    background-color: var(--warning);
    color: white;
}

.badge-low {
    background-color: var(--success);
    color: white;
}

.flex { display: flex; }
.justify-between { justify-content: space-between; }
.items-center { align-items: center; }
.min-h-screen { min-height: calc(100vh - 120px); }
.mx-auto { margin-left: auto; margin-right: auto; }
.px-4 { padding-left: 1rem; padding-right: 1rem; }
.py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
.py-8 { padding-top: 2rem; padding-bottom: 2rem; }
.mr-2 { margin-right: 0.5rem; }
.text-2xl { font-size: 1.5rem; }
.font-bold { font-weight: 700; }
.text-center { text-align: center; }
.text-gray-500 { color: #6b7280; }
</style>
