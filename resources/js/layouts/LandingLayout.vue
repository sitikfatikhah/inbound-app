<script setup lang="ts">
</script>

<template>
    <div>
        <header class="bg-surface border-b border-border shadow-sm sticky top-0 z-50">
            <nav class="flex justify-between items-center w-full px-margin-desktop max-w-container-max mx-auto h-20">
                <div class="flex items-center gap-8">
                    <span class="font-display text-headline-md font-bold text-primary">PT. CSA | Supply Chain
                        Tower</span>
                    <div class="hidden md:flex gap-6">
                        <a class="text-on-surface-variant font-medium hover:text-primary transition-colors duration-200"
                            href="#">Pengiriman</a>
                        <a class="text-on-surface-variant font-medium hover:text-primary transition-colors duration-200"
                            href="#">Armada</a>
                        <a class="text-on-surface-variant font-medium hover:text-primary transition-colors duration-200"
                            href="#">Analitik</a>
                        <a class="text-on-surface-variant font-medium hover:text-primary transition-colors duration-200"
                            href="#">Jaringan Global</a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <button
                            class="p-2 text-on-surface-variant hover:bg-surface-container-low rounded-full transition-colors">
                            <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
                        </button>
                        <button
                            class="p-2 text-on-surface-variant hover:bg-surface-container-low rounded-full transition-colors">
                            <span class="material-symbols-outlined" data-icon="help_outline">help_outline</span>
                        </button>
                    </div>
                    <div class="h-8 w-[1px] bg-border mx-2"></div>
                    <button
                        class="px-4 py-2 text-on-surface-variant font-medium hover:text-primary transition-colors">Hubungi
                        Admin</button>
                    <button
                        class="px-5 py-2.5 bg-primary text-on-primary font-semibold rounded-lg hover:opacity-90 active:opacity-80 transition-all">Login</button>
                </div>
            </nav>
        </header>
        <main class="flex-grow">
            <Button variant="outline">
                Button
            </Button>
            <slot />
        </main>
        <footer class="bg-surface-container-lowest border-t border-border mt-12">
            <div
                class="flex flex-col md:flex-row justify-between items-center w-full px-margin-desktop py-xl max-w-container-max mx-auto">
                <div class="mb-6 md:mb-0">
                    <span class="font-display text-headline-md font-bold text-primary block mb-2">PT. CSA Supply Chain
                        Tower</span>
                    <p class="text-label-md text-muted max-w-sm">Penyedia solusi logistik global terkemuka untuk
                        manajemen dan orkestrasi rantai pasok perusahaan.</p>
                </div>
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <div class="flex gap-6">
                        <a class="text-muted text-body-md hover:text-secondary transition-colors" href="#">Kebijakan
                            Privasi</a>
                        <a class="text-muted text-body-md hover:text-secondary transition-colors" href="#">Syarat &amp;
                            Ketentuan</a>
                        <a class="text-muted text-body-md hover:text-secondary transition-colors" href="#">Kepatuhan</a>
                    </div>
                    <div class="flex gap-4">
                        <button
                            class="w-10 h-10 rounded-full border border-border flex items-center justify-center hover:bg-surface-container-low transition-colors">
                            <span class="material-symbols-outlined text-primary text-[20px]"
                                data-icon="language">language</span>
                        </button>
                        <button
                            class="w-10 h-10 rounded-full border border-border flex items-center justify-center hover:bg-surface-container-low transition-colors">
                            <span class="material-symbols-outlined text-primary text-[20px]"
                                data-icon="mail">mail</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="border-t border-border/50 py-6 text-center">
                <p class="text-label-sm text-muted">© 2026 PT. CSA Supply Chain Tower. Hak cipta dilindungi
                    undang-undang.</p>
            </div>
        </footer>
    </div>
</template>