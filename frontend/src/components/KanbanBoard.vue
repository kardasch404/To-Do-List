<template>
  <div class="kanban-dashboard">
    <div class="header">
      <h1>Task Dashboard</h1>
      <div class="user-info">
        <button @click="$emit('show-notifications')" class="notification-btn">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" xml:space="preserve" class="notification-icon"><path fill="#FFB236" d="M232 10h48v66h-48z"/><path fill="#FFE352" d="M396 248.685V213.34C396 135.83 333.582 72.039 256.072 72 178.719 71.961 116 134.656 116 212v36.685a300.009 300.009 0 0 1-42.962 154.699L51 440h410l-22.038-36.616A300.009 300.009 0 0 1 396 248.685z"/><path fill="#FFB236" d="M318 440c0 34.242-27.758 62-62 62s-62-27.758-62-62h124z"/><circle fill="#FFB0AA" cx="378" cy="165" r="62"/></svg>
          <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
        </button>
        
        <div class="user-profile" @click="toggleUserMenu" ref="userProfile">
          <div class="user-avatar">
            <span class="avatar-text">{{ getUserInitials() }}</span>
          </div>
          <div v-if="showUserMenu" class="user-dropdown">
            <div class="user-info">
              <div class="dropdown-avatar">
                <span class="avatar-text-large">{{ getUserInitials() }}</span>
              </div>
              <div class="user-details">
                <h4>{{ user?.full_name }}</h4>
                <p>{{ user?.email }}</p>
              </div>
            </div>
            <button @click="logout" class="dropdown-logout-btn">Logout</button>
          </div>
        </div>
      </div>
    </div>

    <div class="task-form">
      <h3>{{ editingTask ? 'Edit Task' : 'Add New Task' }}</h3>
      <form @submit.prevent="editingTask ? updateTask() : createTask()">
        <div class="form-row">
          <input v-model="taskForm.title" type="text" placeholder="Task Title" required>
          <textarea v-model="taskForm.description" placeholder="Task Description" required></textarea>
        </div>
        <div class="form-actions">
          <button type="submit" :disabled="loading">
            {{ loading ? 'Loading...' : (editingTask ? 'Update' : 'Create') }}
          </button>
          <button v-if="editingTask" type="button" @click="cancelEdit" class="cancel-btn">Cancel</button>
        </div>
      </form>
    </div>

    <div class="kanban-board">
      <div class="column" v-for="status in columns" :key="status.key">
        <div class="column-header">
          <h3>{{ status.title }} ({{ getTasksByStatus(status.key).length }})</h3>
        </div>
        <div 
          class="column-content"
          @drop="onDrop($event, status.key)"
          @dragover.prevent
          @dragenter.prevent
        >
          <div 
            v-for="task in getTasksByStatus(status.key)" 
            :key="task.id"
            class="task-card"
            :class="task.priority"
            draggable="true"
            @dragstart="onDragStart($event, task)"
          >
            <div class="task-header">
              <h4>{{ task.title }}</h4>
              <div class="task-actions">
                <button @click="editTask(task)" class="edit-btn">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" xml:space="preserve" class="action-icon"><path fill="#6E83B7" d="M464 502H10V48h246v48H58v358h358V256h48z"/><path fill="#6E83B7" d="m502 90-80-80-240 240v65.857l-20.071 20.072 14.142 14.142L196.143 330H262z"/><path fill="#FFE352" d="m351.497 80.502 27-27 79.999 80-27 27z"/></svg>
                </button>
                <button @click="deleteTask(task.id)" class="delete-btn">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" class="action-icon"><g data-name="58-Remove"><path fill="#ff4757" d="M16 0a16 16 0 1 0 16 16A16 16 0 0 0 16 0zm0 30a14 14 0 1 1 14-14 14 14 0 0 1-14 14z"/><path fill="#ff4757" d="M6 15h20v2H6z"/></g></svg>
                </button>
              </div>
            </div>
            <p class="task-description">{{ task.description }}</p>
            <div class="task-meta">
              <span class="priority" :class="task.priority">{{ task.priority }}</span>
              <span class="date">{{ formatDate(task.created_at) }}</span>
            </div>
          </div>

        </div>
      </div>
    </div>
    

  </div>
</template>

<script>
import { taskAPI, authAPI } from '../services/api'
import NotificationCenter from './NotificationCenter.vue'

export default {
  name: 'KanbanBoard',
  components: {
    NotificationCenter
  },
  emits: ['logout', 'show-notifications'],
  props: {
    user: Object
  },
  data() {
    return {
      tasks: [],
      taskForm: {
        title: '',
        description: '',
        is_done: false,
        priority: 'medium'
      },
      editingTask: null,
      loading: false,
      columns: [
        { key: 'pending', title: 'To Do' },
        { key: 'in_progress', title: 'Doing' },
        { key: 'completed', title: 'Done' }
      ],
      unreadCount: 0,
      showUserMenu: false
    }
  },
  async mounted() {
    await this.fetchTasks()
    this.updateUnreadCount()
    document.addEventListener('click', this.handleClickOutside)
  },
  
  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
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
    
    getTasksByStatus(status) {
      if (status === 'pending') {
        return this.tasks.filter(task => !task.is_done && task.status !== 'in_progress')
      } else if (status === 'in_progress') {
        return this.tasks.filter(task => !task.is_done && task.status === 'in_progress')
      } else if (status === 'completed') {
        return this.tasks.filter(task => task.is_done)
      }
      return []
    },
    
    onDragStart(event, task) {
      event.dataTransfer.setData('taskId', task.id.toString())
    },
    
    async onDrop(event, newStatus) {
      const taskId = parseInt(event.dataTransfer.getData('taskId'))
      const task = this.tasks.find(t => t.id === taskId)
      
      if (task) {
        try {
          let updateData = { ...task }
          
          if (newStatus === 'completed') {
            updateData.is_done = true
            updateData.status = 'completed'
          } else if (newStatus === 'in_progress') {
            updateData.is_done = false
            updateData.status = 'in_progress'
          } else {
            updateData.is_done = false
            updateData.status = 'pending'
          }
          
          await taskAPI.updateTask(taskId, updateData)
          Object.assign(task, updateData)
          
          // Add notification
          this.addNotification({
            type: 'task_updated',
            title: 'Task Status Updated',
            message: `Task "${task.title}" moved to ${newStatus.replace('_', ' ')}.`
          })
        } catch (error) {
          console.error('Failed to update task status:', error)
        }
      }
    },
    
    async createTask() {
      this.loading = true
      try {
        const taskData = { ...this.taskForm }
        const response = await taskAPI.createTask(taskData)
        this.resetForm()
        await this.fetchTasks()
        
        // Add notification
        this.addNotification({
          type: 'task_created',
          title: 'Task Created Successfully',
          message: `Task "${taskData.title}" has been created successfully.`
        })
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
        
        // Add notification
        this.addNotification({
          type: 'task_updated',
          title: 'Task Updated Successfully',
          message: `Task "${this.taskForm.title}" has been updated successfully.`
        })
      } catch (error) {
        console.error('Failed to update task:', error)
      } finally {
        this.loading = false
      }
    },
    
    async deleteTask(id) {
      if (confirm('Are you sure you want to delete this task?')) {
        const task = this.tasks.find(t => t.id === id)
        try {
          await taskAPI.deleteTask(id)
          await this.fetchTasks()
          
          // Add notification
          this.addNotification({
            type: 'task_deleted',
            title: 'Task Deleted Successfully',
            message: `Task "${task?.title}" has been deleted successfully.`
          })
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
        is_done: task.is_done,
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
        is_done: false,
        priority: 'medium'
      }
      this.editingTask = null
    },
    
    formatDate(dateString) {
      const date = new Date(dateString)
      const now = new Date()
      const diffTime = Math.abs(now - date)
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
      
      if (diffDays === 1) return '1 day ago'
      if (diffDays < 30) return `${diffDays} days ago`
      if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`
      return `${Math.floor(diffDays / 365)} years ago`
    },
    
    addNotification(notification) {
      if (this.$refs.notificationCenter) {
        this.$refs.notificationCenter.addNotification(notification)
      } else {
        // Store notification for when component is mounted
        const stored = localStorage.getItem('notifications') || '[]'
        const notifications = JSON.parse(stored)
        notifications.unshift({
          id: Date.now(),
          ...notification,
          read: false,
          created_at: new Date().toISOString()
        })
        localStorage.setItem('notifications', JSON.stringify(notifications))
      }
      this.updateUnreadCount()
    },
    
    updateUnreadCount() {
      const stored = localStorage.getItem('notifications')
      if (stored) {
        const notifications = JSON.parse(stored)
        this.unreadCount = notifications.filter(n => !n.read).length
      }
    },
    
    toggleUserMenu() {
      this.showUserMenu = !this.showUserMenu
    },
    
    handleClickOutside(event) {
      if (this.$refs.userProfile && !this.$refs.userProfile.contains(event.target)) {
        this.showUserMenu = false
      }
    },
    
    getUserInitials() {
      if (!this.user?.full_name) return 'U'
      return this.user.full_name.split(' ').map(name => name.charAt(0)).join('').substring(0, 2).toUpperCase()
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
.kanban-dashboard {
  background: linear-gradient(135deg, #20252A 0%, #1a1f24 100%);
  color: #FFD900;
  width: 100vw;
  height: 100vh;
  padding: 1rem;
  margin: 0;
  overflow-y: auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding: 1rem;
  border-bottom: 2px solid #FFD900;
}

.header h1 {
  margin: 0;
  color: #FFD900;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.notification-btn {
  background: #FFD900;
  color: #20252A;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  position: relative;
  margin-right: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.notification-icon {
  width: 20px;
  height: 20px;
}

.notification-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #dc3545;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  font-size: 0.7rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logout-btn {
  background: #FFD900;
  color: #20252A;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.task-form {
  background: linear-gradient(135deg, #2a3038 0%, #252a32 100%);
  padding: 2rem;
  border-radius: 12px;
  margin-bottom: 2rem;
  border: 2px solid #FFD900;
  box-shadow: 0 8px 32px rgba(255, 217, 0, 0.1);
}

.task-form h3 {
  color: #FFD900;
  margin-top: 0;
}

.form-row {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.form-row input, .form-row textarea, .form-row select {
  flex: 1;
  padding: 1rem;
  border: 2px solid #FFD900;
  border-radius: 8px;
  background: linear-gradient(135deg, #20252A 0%, #1a1f24 100%);
  color: #FFD900;
  transition: all 0.3s ease;
}

.form-row input:focus, .form-row textarea:focus, .form-row select:focus {
  outline: none;
  border-color: #FFA500;
  box-shadow: 0 0 0 3px rgba(255, 217, 0, 0.2);
}

.form-row input::placeholder, .form-row textarea::placeholder {
  color: #888;
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
  font-weight: bold;
}

.form-actions button[type="submit"] {
  background: linear-gradient(135deg, #FFD900 0%, #FFA500 100%);
  color: #20252A;
  transition: all 0.3s ease;
}

.form-actions button[type="submit"]:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(255, 217, 0, 0.4);
}

.cancel-btn {
  background: #666;
  color: white;
}

.kanban-board {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
  margin-top: 2rem;
}

.column {
  background: linear-gradient(135deg, #2a3038 0%, #252a32 100%);
  border-radius: 12px;
  border: 2px solid #FFD900;
  min-height: 500px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
  transition: transform 0.2s;
}

.column:hover {
  transform: translateY(-4px);
}

.column-header {
  padding: 1.5rem;
  border-bottom: 2px solid #FFD900;
  background: linear-gradient(135deg, #FFD900 0%, #FFA500 100%);
  color: #20252A;
  border-radius: 10px 10px 0 0;
  box-shadow: 0 4px 16px rgba(255, 217, 0, 0.3);
}

.column-header h3 {
  margin: 0;
  font-weight: bold;
}

.column-content {
  padding: 1rem;
  min-height: 450px;
}

.task-card {
  background: linear-gradient(135deg, #20252A 0%, #1a1f24 100%);
  border: 2px solid #FFD900;
  border-radius: 10px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  cursor: move;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.task-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #FFD900 0%, #FFA500 100%);
}

.task-card:hover {
  transform: translateY(-4px) scale(1.02);
  box-shadow: 0 12px 24px rgba(255, 217, 0, 0.3);
  border-color: #FFA500;
}

.task-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.5rem;
}

.task-header h4 {
  margin: 0;
  color: #FFD900;
  font-size: 1rem;
}

.task-actions {
  display: flex;
  gap: 0.5rem;
}

.edit-btn, .delete-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.edit-btn:hover {
  background: rgba(110, 131, 183, 0.2);
}

.delete-btn:hover {
  background: rgba(255, 71, 87, 0.2);
}

.action-icon {
  width: 16px;
  height: 16px;
}

.task-description {
  color: #ccc;
  margin: 0.5rem 0;
  font-size: 0.9rem;
}

.task-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
}

.priority {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: bold;
}

.priority.low { 
  background: #28a745; 
  color: white; 
}

.priority.medium { 
  background: #FFD900; 
  color: #20252A; 
}

.priority.high { 
  background: #dc3545; 
  color: white; 
}

.date {
  font-size: 0.8rem;
  color: #888;
}

.user-profile {
  position: relative;
  cursor: pointer;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: 2px solid #FFD900;
  background: linear-gradient(135deg, #FFD900 0%, #FFA500 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s;
}

.user-avatar:hover {
  transform: scale(1.1);
}

.avatar-text {
  color: #20252A;
  font-weight: bold;
  font-size: 14px;
}

.user-dropdown {
  position: absolute;
  top: 50px;
  right: 0;
  background: linear-gradient(135deg, #2a3038 0%, #252a32 100%);
  border: 2px solid #FFD900;
  border-radius: 12px;
  padding: 1rem;
  min-width: 250px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
  z-index: 1000;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #333;
}

.dropdown-avatar {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  border: 2px solid #FFD900;
  background: linear-gradient(135deg, #FFD900 0%, #FFA500 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.avatar-text-large {
  color: #20252A;
  font-weight: bold;
  font-size: 20px;
}

.user-details h4 {
  color: #FFD900;
  margin: 0 0 0.25rem 0;
  font-size: 1.1rem;
}

.user-details p {
  color: #ccc;
  margin: 0;
  font-size: 0.9rem;
}

.dropdown-logout-btn {
  width: 100%;
  background: linear-gradient(135deg, #FFD900 0%, #FFA500 100%);
  color: #20252A;
  border: none;
  padding: 0.75rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  transition: transform 0.2s;
}

.dropdown-logout-btn:hover {
  transform: translateY(-2px);
}
</style>