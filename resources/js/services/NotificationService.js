export default class NotificationService {
    static requestPermission() {
      return Notification.requestPermission();
    }
  
    static showDesktopNotification(title, options) {
      if (Notification.permission === 'granted') {
        const notification = new Notification(title, options);
        notification.onclick = () => {
          // Handle the click event (e.g., open the chat window)
          console.log('Notification clicked');
        };
      }
    }
    static showSidebarNotification(message, sidebarElement) {
        const sidebarNotification = document.createElement('div');
        sidebarNotification.className = 'sidebar-notification';
        sidebarNotification.textContent = message;
      
        sidebarElement.appendChild(sidebarNotification);
      
        // Automatically remove the notification after a few seconds
        setTimeout(() => {
          sidebarNotification.remove();
        }, 5000);
      }

  }