import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import os from "os";

function getLocalIP() {
  const interfaces = os.networkInterfaces();
  for (const name in interfaces) {
    for (const net of interfaces[name]) {
      if (net.family === "IPv4" && !net.internal) {
        return net.address;
      }
    }
  }
  return "localhost"; // fallback
}

const localIP = getLocalIP();

export default defineConfig({
  server: {
    host: "0.0.0.0", // ip host
    port: 5174,
    strictPort: true,
    https: false,
    hmr: {
      host: localIP, // ip may
      protocol: "ws", // 'wss' nếu dùng HTTPS
    },
    cors: true, // <-- Bắt buộc thêm dòng này
  },

  plugins: [
    laravel({
      input: [
        "resources/css/app.css",
        "resources/js/app.js",
        "resources/js/components.js",
      ],
      refresh: true,
    }),
  ],
});
