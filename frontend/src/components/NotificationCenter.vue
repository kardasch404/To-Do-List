<template>
  <div class="notification-center">
    <div class="header">
      <h2>Notifications</h2>
      <button @click="$emit('close')" class="close-btn">✕</button>
    </div>
    
    <div class="notifications-list">
      <div v-if="notifications.length === 0" class="no-notifications">
        No notifications yet
      </div>
      
      <div 
        v-for="notification in notifications" 
        :key="notification.id"
        class="notification-item"
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
        
        <button 
          v-if="!notification.read" 
          @click="markAsRead(notification.id)"
          class="mark-read-btn"
        >
          Mark as read
        </button>
      </div>
    </div>
    
    <div class="notification-actions">
      <button @click="markAllAsRead" class="mark-all-read-btn">
        Mark all as read
      </button>
      <button @click="clearAll" class="clear-all-btn">
        Clear all
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotificationCenter',
  emits: ['close'],
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
        this.notifications = JSON.parse(stored)
      }
    },
    
    addNotification(notification) {
      const newNotification = {
        id: Date.now(),
        ...notification,
        read: false,
        created_at: new Date().toISOString()
      }
      
      this.notifications.unshift(newNotification)
      this.saveNotifications()
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
    
    clearAll() {
      this.notifications = []
      this.saveNotifications()
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
.notification-center {
  background-color: #20252A;
  color: #FFD900;
  width: 400px;
  max-height: 600px;
  border: 2px solid #FFD900;
  border-radius: 8px;
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 1000;
  display: flex;
  flex-direction: column;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #FFD900;
  background: #FFD900;
  color: #20252A;
  border-radius: 6px 6px 0 0;
}

.header h2 {
  margin: 0;
  font-weight: bold;
}

.close-btn {
  background: none;
  border: none;
  color: #20252A;
  font-size: 1.2rem;
  cursor: pointer;
  padding: 0.25rem;
}

.notifications-list {
  flex: 1;
  overflow-y: auto;
  max-height: 400px;
}

.no-notifications {
  text-align: center;
  padding: 2rem;
  color: #666;
  font-style: italic;
}

.notification-item {
  display: flex;
  align-items: flex-start;
  padding: 1rem;
  border-bottom: 1px solid #333;
  gap: 1rem;
}

.notification-item.unread {
  background: rgba(255, 217, 0, 0.1);
  border-left: 3px solid #FFD900;
}

.notification-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
}

.notification-content {
  flex: 1;
}

.notification-content h4 {
  margin: 0 0 0.5rem 0;
  color: #FFD900;
  font-size: 0.9rem;
}

.notification-content p {
  margin: 0 0 0.5rem 0;
  color: #ccc;
  font-size: 0.8rem;
}

.notification-time {
  font-size: 0.7rem;
  color: #888;
}

.mark-read-btn {
  background: #FFD900;
  color: #20252A;
  border: none;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.8rem;
  flex-shrink: 0;
}

.notification-actions {
  display: flex;
  gap: 1rem;
  padding: 1rem;
  border-top: 1px solid #333;
}

.mark-all-read-btn, .clear-all-btn {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}

.mark-all-read-btn {
  background: #FFD900;
  color: #20252A;
}

.clear-all-btn {
  background: #666;
  color: white;
}
</style>