// Kurir application entry point
// Import ringtone functionality for new order notifications
import './services/ringtone';

// Import web push notification service
import './services/webPush';

// Import PWA install handler
import { setupPWAInstall } from './utils/serviceWorker';

// Setup PWA install prompt handler (hanya capture event, belum register SW)
setupPWAInstall();
