<template>
  <div class="notifications-page">
    <div class="header">
      <h1>📋 Notifications</h1>
      <button @click="$emit('back-to-dashboard')" class="back-btn">← Back to Dashboard</button>
    </div>
    
    <div class="notifications-container">
      <div v-if="notifications.length === 0" class="no-notifications">
        <div class="empty-icon">🔔</div>
        <h3>No notifications yet</h3>
        <p>You'll see notifications here when tasks are created, updated, or deleted.</p>
      </div>
      
      <div v-else class="notifications-list">
        <div 
          v-for="notification in notifications" 
          :key="notification.id"
          class="notification-card"
          :class="{ unread: !notification.read }"
        >
          <div class="notification-icon">
            <span v-if="notification.type === 'task_created'">✅</span>
            <span v-else-if="notification.type === 'task_updated'">✏️</span>
            <span v-else-if="notification.type === 'task_deleted'">🗑️</span>
          </div>
          
          <div class="notification-content">
            <h4>{{ notification.title }}</h4>
            <p>{{ notification.message }}</p>
            <span class="notification-time">{{ formatTime(notification.created_at) }}</span>
          </div>
          
          <div class="notification-actions">
            <button 
              v-if="!notification.read" 
              @click="markAsRead(notification.id)"
              class="mark-read-btn"
            >
              Mark as read
            </button>
            <button @click="deleteNotification(notification.id)" class="delete-btn">
              ✕
            </button>
          </div>
        </div>
      </div>
      
      <div v-if="notifications.length > 0" class="page-actions">
        <button @click="markAllAsRead" class="action-btn primary">
          Mark all as read
        </button>
        <button @click="clearAll" class="action-btn secondary">
          Clear all notifications
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotificationsPage',
  emits: ['back-to-dashboard'],
  data() {
    return {
      notifications: []
    }
  },
  mounted() {
    this.loadNotifications()
  },
  methods: {
    loadNotifications() {
      const stored = localStorage.getItem('notifications')
      if (stored) {
        this.notifications = JSON.parse(stored).sort((a, b) => 
          new Date(b.created_at) - new Date(a.created_at)
        )
      }
    },
    
    markAsRead(id) {
      const notification = this.notifications.find(n => n.id === id)
      if (notification) {
        notification.read = true
        this.saveNotifications()
      }
    },
    
    markAllAsRead() {
      this.notifications.forEach(n => n.read = true)
      this.saveNotifications()
    },
    
    deleteNotification(id) {
      this.notifications = this.notifications.filter(n => n.id !== id)
      this.saveNotifications()
    },
    
    clearAll() {
      if (confirm('Are you sure you want to clear all notifications?')) {
        this.notifications = []
        this.saveNotifications()
      }
    },
    
    saveNotifications() {
      localStorage.setItem('notifications', JSON.stringify(this.notifications))
    },
    
    formatTime(dateString) {
      const date = new Date(dateString)
      const now = new Date()
      const diffTime = Math.abs(now - date)
      const diffMinutes = Math.floor(diffTime / (1000 * 60))
      
      if (diffMinutes < 1) return 'Just now'
      if (diffMinutes < 60) return `${diffMinutes}m ago`
      if (diffMinutes < 1440) return `${Math.floor(diffMinutes / 60)}h ago`
      return `${Math.floor(diffMinutes / 1440)}d ago`
    }
  }
}
</script>

<style scoped>
.notifications-page {
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
  padding-bottom: 1rem;
  border-bottom: 2px solid #FFD900;
}

.header h1 {
  margin: 0;
  color: #FFD900;
  font-size: 2.5rem;
}

.back-btn {
  background: #FFD900;
  color: #20252A;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  transition: transform 0.2s;
}

.back-btn:hover {
  transform: translateY(-2px);
}

.notifications-container {
  max-width: 800px;
  margin: 0 auto;
}

.no-notifications {
  text-align: center;
  padding: 4rem 2rem;
  background: linear-gradient(135deg, #2a3038 0%, #252a32 100%);
  border-radius: 12px;
  border: 2px solid #FFD900;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.no-notifications h3 {
  color: #FFD900;
  margin-bottom: 1rem;
}

.no-notifications p {
  color: #ccc;
}

.notifications-list {
  space-y: 1rem;
}

.notification-card {
  display: flex;
  align-items: flex-start;
  background: linear-gradient(135deg, #2a3038 0%, #252a32 100%);
  border: 2px solid #333;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1rem;
  transition: all 0.3s ease;
}

.notification-card.unread {
  border-color: #FFD900;
  background: linear-gradient(135deg, rgba(255, 217, 0, 0.1) 0%, #2a3038 100%);
}

.notification-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(255, 217, 0, 0.2);
}

.notification-icon {
  font-size: 2rem;
  margin-right: 1rem;
  flex-shrink: 0;
}

.notification-content {
  flex: 1;
}

.notification-content h4 {
  margin: 0 0 0.5rem 0;
  color: #FFD900;
  font-size: 1.1rem;
}

.notification-content p {
  margin: 0 0 0.5rem 0;
  color: #ccc;
  line-height: 1.5;
}

.notification-time {
  font-size: 0.8rem;
  color: #888;
}

.notification-actions {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-left: 1rem;
}

.mark-read-btn, .delete-btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 0.8rem;
  transition: all 0.2s;
}

.mark-read-btn {
  background: #FFD900;
  color: #20252A;
  font-weight: bold;
}

.delete-btn {
  background: #ff4757;
  color: white;
}

.mark-read-btn:hover, .delete-btn:hover {
  transform: translateY(-1px);
}

.page-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #333;
}

.action-btn {
  padding: 1rem 2rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  transition: all 0.3s ease;
}

.action-btn.primary {
  background: linear-gradient(135deg, #FFD900 0%, #FFA500 100%);
  color: #20252A;
}

.action-btn.secondary {
  background: #666;
  color: white;
}

.action-btn:hover {
  transform: translateY(-2px);
}
</style>