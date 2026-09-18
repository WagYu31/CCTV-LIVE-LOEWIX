const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('electronAPI', {
  platform: process.platform,
  isDesktop: true,
  getVersion: () => ipcRenderer.invoke('get-app-version'),
  restartPhp: () => ipcRenderer.invoke('restart-php'),
  restartStream: () => ipcRenderer.invoke('restart-stream'),
  notify: (title, body) => ipcRenderer.send('show-notification', { title, body })
});
