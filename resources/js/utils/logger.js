/**
 * Development-only console logger
 * Console logs hanya muncul di development environment (npm run dev)
 * Di production (npm run build), semua console log akan di-strip
 */

const isDev = import.meta.env.DEV;

export const logger = {
    log: (...args) => {
        if (isDev) {
            console.log(...args);
        }
    },

    warn: (...args) => {
        if (isDev) {
            console.warn(...args);
        }
    },

    error: (...args) => {
        if (isDev) {
            console.error(...args);
        }
    },

    info: (...args) => {
        if (isDev) {
            console.info(...args);
        }
    },

    debug: (...args) => {
        if (isDev) {
            console.debug(...args);
        }
    }
};
