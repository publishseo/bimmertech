import { Page as InertiaPage } from '@inertiajs/vue3';

declare module '@inertiajs/vue3' {
    interface Page<P extends Record<string, any> = Record<string, any>> {
        flash?: {
            success?: string;
        };
    }
}
