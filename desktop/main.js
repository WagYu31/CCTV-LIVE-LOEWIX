const { app, BrowserWindow, ipcMain, Notification, Menu } = require('electron');
const path = require('path');
const { spawn, execSync } = require('child_process');
const http = require('http');
const fs = require('fs');

let mainWindow = null;
let phpProcess = null;
let mediaMtxProcess = null;
const PHP_PORT = 28080;

// Ensure brew and local bins are in PATH for spawned processes on macOS
if (process.platform === 'darwin') {
  const extraPaths = ['/opt/homebrew/bin', '/usr/local/bin', '/usr/bin', '/bin'];
  process.env.PATH = extraPaths.concat(process.env.PATH ? process.env.PATH.split(':') : []).filter((v, i, a) => a.indexOf(v) === i).join(':');
}

// Determine project backend root directory
const isPackaged = app.isPackaged;
const backendRoot = isPackaged
  ? path.join(process.resourcesPath, 'app_backend')
  : path.resolve(__dirname, '..');

// Find PHP executable
function getPhpExecutable() {
  const isWin = process.platform === 'win32';
  const bundledPhpWin = path.join(__dirname, 'bin', 'php', 'php.exe');
  const packagedPhpWin = isPackaged ? path.join(process.resourcesPath, 'bin', 'php', 'php.exe') : null;

  if (isWin) {
    if (packagedPhpWin && fs.existsSync(packagedPhpWin)) return packagedPhpWin;
    if (fs.existsSync(bundledPhpWin)) return bundledPhpWin;
    return 'php';
  }

  // macOS / Linux common paths
  const candidatePaths = [
    '/opt/homebrew/bin/php',
    '/usr/local/bin/php',
    '/usr/bin/php'
  ];

  for (const p of candidatePaths) {
    if (fs.existsSync(p)) return p;
  }

  return 'php';
}

// Find MediaMTX executable
function getMediaMtxExecutable() {
  const isWin = process.platform === 'win32';
  const ext = isWin ? '.exe' : '';
  const candidates = [
    path.join(__dirname, 'bin', `mediamtx${ext}`),
    path.join(backendRoot, `mediamtx${ext}`),
    `/opt/homebrew/bin/mediamtx`,
    `/usr/local/bin/mediamtx`,
    `/home/loewix/mediamtx`
  ];

  for (const p of candidates) {
    if (fs.existsSync(p)) return p;
  }
  return null;
}

// Start PHP Built-in Web Server
function startPhpServer(callback) {
  const phpBin = getPhpExecutable();
  console.log(`[Loewix Desktop] Memulai PHP Server menggunakan: ${phpBin}`);
  console.log(`[Loewix Desktop] Backend Root: ${backendRoot}`);

  try {
    phpProcess = spawn(phpBin, [
      '-S', `127.0.0.1:${PHP_PORT}`,
      '-t', backendRoot
    ], {
      cwd: backendRoot,
      stdio: 'pipe'
    });

    phpProcess.stdout.on('data', (data) => {
      console.log(`[PHP stdout]: ${data}`);
    });

    phpProcess.stderr.on('data', (data) => {
      console.log(`[PHP stderr]: ${data}`);
    });

    phpProcess.on('error', (err) => {
      console.error('[PHP Error]:', err);
    });

    phpProcess.on('close', (code) => {
      console.log(`[PHP Process] Exited with code: ${code}`);
    });
  } catch (err) {
    console.error('[Loewix Desktop] Gagal spawn PHP:', err);
  }

  // Poll until PHP server responds
  let attempts = 0;
  const maxAttempts = 30;
  const interval = setInterval(() => {
    attempts++;
    http.get(`http://127.0.0.1:${PHP_PORT}/customer/index.php`, (res) => {
      clearInterval(interval);
      console.log(`[Loewix Desktop] PHP Server SIAP (Status: ${res.statusCode})`);
      if (callback) callback();
    }).on('error', (err) => {
      if (attempts >= maxAttempts) {
        clearInterval(interval);
        console.error('[Loewix Desktop] Timeout menunggu PHP server:', err.message);
        if (callback) callback();
      }
    });
  }, 300);
}

// Start Local MediaMTX if present
function startMediaMtx() {
  const mtxBin = getMediaMtxExecutable();
  const mtxConfig = path.join(backendRoot, 'mediamtx.yml');

  if (mtxBin && fs.existsSync(mtxConfig)) {
    console.log(`[Loewix Desktop] Memulai MediaMTX Lokal: ${mtxBin}`);
    try {
      mediaMtxProcess = spawn(mtxBin, [mtxConfig], {
        cwd: backendRoot,
        stdio: 'pipe'
      });
      mediaMtxProcess.on('error', (e) => console.warn('[MediaMTX Error]:', e.message));
    } catch (e) {
      console.warn('[Loewix Desktop] Skip local MediaMTX:', e.message);
    }
  } else {
    console.log('[Loewix Desktop] MediaMTX lokal tidak ditemukan, menggunakan remote stream.');
  }
}

// Create Main Application Window
function createMainWindow() {
  const iconPath = process.platform === 'win32'
    ? path.join(__dirname, 'assets', 'icon.ico')
    : path.join(__dirname, 'assets', 'icon.png');

  mainWindow = new BrowserWindow({
    width: 1360,
    height: 860,
    minWidth: 1024,
    minHeight: 700,
    title: 'Loewix CCTV Live & AI Face Recognition',
    icon: fs.existsSync(iconPath) ? iconPath : undefined,
    backgroundColor: '#0a0d14',
    webPreferences: {
      preload: path.join(__dirname, 'preload.js'),
      nodeIntegration: false,
      contextIsolation: true,
      webSecurity: false // Allow local stream playback without CORS blocking
    }
  });

  const appUrl = `http://127.0.0.1:${PHP_PORT}/customer/index.php`;
  mainWindow.loadURL(appUrl);

  mainWindow.webContents.on('did-fail-load', () => {
    setTimeout(() => {
      if (mainWindow) mainWindow.loadURL(appUrl);
    }, 1000);
  });

  // Setup Custom Native Application Menu
  setupAppMenu();

  mainWindow.on('closed', () => {
    mainWindow = null;
  });
}

function setupAppMenu() {
  const isMac = process.platform === 'darwin';
  const template = [
    ...(isMac ? [{
      label: 'Loewix CCTV',
      submenu: [
        { role: 'about', label: 'Tentang Loewix CCTV' },
        { type: 'separator' },
        { role: 'services' },
        { type: 'separator' },
        { role: 'hide', label: 'Sembunyikan' },
        { role: 'hideOthers', label: 'Sembunyikan Lainnya' },
        { role: 'unhide', label: 'Tampilkan Semua' },
        { type: 'separator' },
        { role: 'quit', label: 'Keluar' }
      ]
    }] : []),
    {
      label: 'File',
      submenu: [
        {
          label: 'Muat Ulang (Refresh)',
          accelerator: 'CmdOrCtrl+R',
          click: () => mainWindow && mainWindow.reload()
        },
        { type: 'separator' },
        isMac ? { role: 'close', label: 'Tutup Jendela' } : { role: 'quit', label: 'Keluar' }
      ]
    },
    {
      label: 'Tampilan',
      submenu: [
        { role: 'resetZoom', label: 'Ukuran Normal' },
        { role: 'zoomIn', label: 'Perbesar' },
        { role: 'zoomOut', label: 'Perkecil' },
        { type: 'separator' },
        { role: 'togglefullscreen', label: 'Layar Penuh (Fullscreen)' },
        { type: 'separator' },
        {
          label: 'Developer Tools',
          accelerator: 'F12',
          click: () => mainWindow && mainWindow.webContents.toggleDevTools()
        }
      ]
    }
  ];

  const menu = Menu.buildFromTemplate(template);
  Menu.setApplicationMenu(menu);
}

// IPC Handlers
ipcMain.handle('get-app-version', () => app.getVersion());
ipcMain.on('show-notification', (event, { title, body }) => {
  if (Notification.isSupported()) {
    new Notification({ title: title || 'Loewix CCTV', body: body || '' }).show();
  }
});

// App Lifecycle
app.whenReady().then(() => {
  startPhpServer(() => {
    startMediaMtx();
    createMainWindow();
  });

  app.on('activate', () => {
    if (BrowserWindow.getAllWindows().length === 0) {
      createMainWindow();
    }
  });
});

function cleanupProcesses() {
  if (phpProcess) {
    try {
      console.log('[Loewix Desktop] Mematikan PHP Server...');
      phpProcess.kill('SIGTERM');
    } catch (e) {}
    phpProcess = null;
  }
  if (mediaMtxProcess) {
    try {
      console.log('[Loewix Desktop] Mematikan MediaMTX...');
      mediaMtxProcess.kill('SIGTERM');
    } catch (e) {}
    mediaMtxProcess = null;
  }
}

app.on('before-quit', cleanupProcesses);
app.on('window-all-closed', () => {
  cleanupProcesses();
  if (process.platform !== 'darwin') {
    app.quit();
  }
});
