declare const assetUrl: string;
declare module "*.svg" {
    const content: string;
    export default content;
}

declare module '*.vue' {
    import { DefineComponent } from 'vue';
    /* eslint-disable @typescript-eslint/no-explicit-any */
    const component: DefineComponent<{}, {}, any>;
    export default component;
}

// global.d.ts
declare const Dropzone: typeof import('dropzone');
