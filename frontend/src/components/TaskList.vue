<template>
  <div class="task-list">
    <div class="header">
      <h2>Welcome, {{ user?.full_name }}</h2>
      <button @click="logout" class="logout-btn">Logout</button>
    </div>
    
    <div class="task-form">
      <h3>{{ editingTask ? 'Edit Task' : 'Add New Task' }}</h3>
      <form @submit.prevent="editingTask ? updateTask() : createTask()">
        <div class="form-row">
          <input v-model="taskForm.title" type="text" placeholder="Task Title" required>
          <textarea v-model="taskForm.description" placeholder="Task Description" required></textarea>
        </div>
        <div class="form-row">
          <select v-model="taskForm.status" required>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
          </select>
          <select v-model="taskForm.priority" required>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
          </select>
        </div>
        <div class="form-actions">
          <button type="submit" :disabled="loading">
            {{ loading ? 'Loading...' : (editingTask ? 'Update' : 'Create') }}
          </button>
          <button v-if="editingTask" type="button" @click="cancelEdit" class="cancel-btn">Cancel</button>
        </div>
      </form>
    </div>

    <div class="tasks">
      <h3>Your Tasks</h3>
      <div v-if="tasks.length === 0" class="no-tasks">No tasks found</div>
      <div v-for="task in tasks" :key="task.id" class="task-item">
        <div class="task-content">
          <h4>{{ task.title }}</h4>
          <p>{{ task.description }}</p>
          <div class="task-meta">
            <span class="status" :class="task.status">{{ task.status }}</span>
            <span class="priority" :class="task.priority">{{ task.priority }}</span>
          </div>
        </div>
        <div class="task-actions">
          <button @click="editTask(task)" class="edit-btn">Edit</button>
          <button @click="deleteTask(task.id)" class="delete-btn">Delete</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { taskAPI, authAPI } from '../services/api'

export default {
  name: 'TaskList',
  emits: ['logout'],
  props: {
    user: Object
  },
  data() {
    return {
      tasks: [],
      taskForm: {
        title: '',
        description: '',
        status: 'pending',
        priority: 'medium'
      },
      editingTask: null,
      loading: false
    }
  },
  async mounted() {
    await this.fetchTasks()
  },
  methods: {
    async fetchTasks() {
      try {
        const response = await taskAPI.getTasks()
        this.tasks = response.data.data
      } catch (error) {
        console.error('Failed to fetch tasks:', error)
      }
    },
    
    async createTask() {
      this.loading = true
      try {
        await taskAPI.createTask(this.taskForm)
        this.resetForm()
        await this.fetchTasks()
      } catch (error) {
        console.error('Failed to create task:', error)
      } finally {
        this.loading = false
      }
    },
    
    async updateTask() {
      this.loading = true
      try {
        await taskAPI.updateTask(this.editingTask.id, this.taskForm)
        this.resetForm()
        await this.fetchTasks()
      } catch (error) {
        console.error('Failed to update task:', error)
      } finally {
        this.loading = false
      }
    },
    
    async deleteTask(id) {
      if (confirm('Are you sure you want to delete this task?')) {
        try {
          await taskAPI.deleteTask(id)
          await this.fetchTasks()
        } catch (error) {
          console.error('Failed to delete task:', error)
        }
      }
    },
    
    editTask(task) {
      this.editingTask = task
      this.taskForm = {
        title: task.title,
        description: task.description,
        status: task.status,
        priority: task.priority
      }
    },
    
    cancelEdit() {
      this.editingTask = null
      this.resetForm()
    },
    
    resetForm() {
      this.taskForm = {
        title: '',
        description: '',
        status: 'pending',
        priority: 'medium'
      }
      this.editingTask = null
    },
    
    async logout() {
      try {
        await authAPI.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        this.$emit('logout')
      }
    }
  }
}
</script>

<style scoped>
.task-list {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.logout-btn {
  background: #dc3545;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}

.task-form {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
}

.form-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-row input, .form-row textarea, .form-row select {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.form-actions {
  display: flex;
  gap: 1rem;
}

.form-actions button {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.form-actions button[type="submit"] {
  background: #007bff;
  color: white;
}

.cancel-btn {
  background: #6c757d;
  color: white;
}

.tasks {
  margin-top: 2rem;
}

.no-tasks {
  text-align: center;
  color: #666;
  padding: 2rem;
}

.task-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
}

.task-content h4 {
  margin: 0 0 0.5rem 0;
}

.task-content p {
  margin: 0 0 0.5rem 0;
  color: #666;
}

.task-meta {
  display: flex;
  gap: 0.5rem;
}

.status, .priority {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: bold;
}

.status.pending { background: #ffc107; color: #000; }
.status.in_progress { background: #17a2b8; color: white; }
.status.completed { background: #28a745; color: white; }

.priority.low { background: #28a745; color: white; }
.priority.medium { background: #ffc107; color: #000; }
.priority.high { background: #dc3545; color: white; }

.task-actions {
  display: flex;
  gap: 0.5rem;
}

.edit-btn {
  background: #ffc107;
  color: #000;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}

.delete-btn {
  background: #dc3545;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}
</style>