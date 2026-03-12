/// <reference types="vite/client" />

interface ImportMetaEnv {
    readonly VITE_API_URL?: string;
    readonly VITE_APP_NAME?: string;
    // tambahkan env lainnya di sini
  }
  
  interface ImportMeta {
    readonly env: ImportMetaEnv;
  }
  