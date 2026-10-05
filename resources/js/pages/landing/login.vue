<script setup lang="ts">
import { ref } from 'vue';
</script>

<template>

    <body
        class="bg-background text-on-background font-body-md text-body-md min-h-screen flex items-center justify-center p-4 md:p-0">
        <main class="flex w-full max-w-[1440px] h-full md:h-[921px] glass-card rounded-xl overflow-hidden shadow-2xl">
            <!-- Left Side: Illustration & Branding -->
            <section class="hidden md:flex md:w-1/2 bg-primary relative overflow-hidden flex-col justify-between p-12">
                <!-- Animated Background Overlay -->
                <div class="absolute inset-0 z-0 opacity-20">

                </div>
                <!-- Content Top -->
                <div class="relative z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-lg">
                            <span class="material-symbols-outlined text-primary text-3xl"
                                style="font-variation-settings: 'FILL' 1;">hub</span>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="font-headline-md text-headline-md text-white font-bold leading-tight">PT. CSA
                            </h1>
                            <p class="font-label-md text-label-md text-primary-fixed opacity-80">Supply Chain Control
                                Tower</p>
                        </div>
                    </div>
                </div>
                <!-- Main Visual -->
                <div class="relative z-10 flex flex-col items-center">
                    <div
                        class="w-full aspect-video rounded-xl overflow-hidden shadow-2xl mb-8 transform hover:scale-[1.02] transition-transform duration-500">
                        <img alt="Supply Chain Logistics" class="w-full h-full object-cover"
                            data-alt="A professional, cinematic shot of a massive container ship docked at a futuristic automated port at dusk. High-density logistical activity is visible with glowing distribution trucks lining up in perfect rows under brilliant blue and white industrial lighting. The atmosphere is sophisticated and high-tech, utilizing a corporate color palette of deep navy blues and sharp whites to convey global reach and precision."
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAiNR87W3Wo952LDGzJVGfbO1kCiyvbcqcY8lNffkQj9bTXB0xeB8bdAGZkGaruNtnnwdWgYzr49WmAneStKIYOC29ATub-RuCvsoptKzOzjxvxmpRqiZs001tc8mokUfT2LW9I2B8gWUcg5h561Ki4vDwNaKNmVTGASmjXtuzTk8HyaYRwYTHUE8u_BepH8BnAbvk5qhdvY7G3EZPsaAPwYc6Va--PeHN2RcK1VU8iyDxrPje0oaYKYFZuUwA2e4PZZWy-x2_BLdfx">
                    </div>
                    <h2 class="font-headline-lg text-headline-lg text-white text-center mb-4">Supply Chain Visibility
                        for Better Decisions</h2>
                    <p class="font-body-lg text-body-lg text-primary-fixed text-center max-w-md opacity-90">
                        Optimalkan alur kerja logistik Anda dengan data real-time dan analitik prediktif dalam satu
                        platform terpadu.
                    </p>
                </div>
                <!-- Content Bottom -->
                <div class="relative z-10 flex justify-between items-center text-primary-fixed text-sm opacity-60">
                    <span>© 2024 PT. Cipta Sahabat Amarta</span>
                    <div class="flex gap-4">
                        <span class="hover:text-white cursor-pointer">Panduan Pengguna</span>
                        <span class="hover:text-white cursor-pointer">Bantuan</span>
                    </div>
                </div>
            </section>
            <!-- Right Side: Login Form -->
            <section class="w-full md:w-1/2 flex items-center justify-center p-8 md:p-16 bg-surface mesh-background">
                <div class="w-full max-w-md">
                    <!-- Mobile Logo (Visible only on small screens) -->
                    <div class="md:hidden flex flex-col items-center mb-8">
                        <div class="w-16 h-16 bg-primary rounded-xl flex items-center justify-center shadow-lg mb-4">
                            <span class="material-symbols-outlined text-white text-4xl"
                                style="font-variation-settings: 'FILL' 1;">hub</span>
                        </div>
                        <h1 class="font-headline-md text-headline-md-mobile text-primary font-bold">PT. CSA</h1>
                        <p class="font-label-md text-label-md text-muted">Supply Chain Control Tower</p>
                    </div>
                    <div class="mb-10">
                        <h2 class="font-headline-md text-headline-md text-on-background mb-2">Selamat Datang Kembali
                        </h2>
                        <p class="font-body-md text-body-md text-muted">Masuk ke akun Anda untuk mengelola rantai pasok.
                        </p>
                    </div>
                    <form class="space-y-6" onsubmit="event.preventDefault();">
                        <!-- Username Field -->
                        <div class="space-y-2">
                            <label class="font-label-md text-label-md text-on-surface-variant flex items-center gap-2"
                                for="username">
                                <span class="material-symbols-outlined text-[18px]">person</span>
                                Nama Pengguna
                            </label>
                            <div class="relative">
                                <input
                                    class="w-full h-12 px-4 bg-white border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none text-body-md"
                                    id="username" name="username" placeholder="Masukkan nama pengguna" type="text">
                            </div>
                        </div>
                        <!-- Password Field -->
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label
                                    class="font-label-md text-label-md text-on-surface-variant flex items-center gap-2"
                                    for="password">
                                    <span class="material-symbols-outlined text-[18px]">lock</span>
                                    Kata Sandi
                                </label>
                                <a class="font-label-sm text-label-sm text-secondary hover:underline transition-all"
                                    href="#">Lupa kata sandi?</a>
                            </div>
                            <div class="relative">
                                <input
                                    class="w-full h-12 px-4 bg-white border border-border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none text-body-md"
                                    id="password" name="password" placeholder="••••••••" type="password">
                                <button
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-primary transition-colors"
                                    type="button">
                                    <span class="material-symbols-outlined">visibility</span>
                                </button>
                            </div>
                        </div>
                        <!-- Remember Me -->
                        <div class="flex items-center gap-2">
                            <input class="w-4 h-4 text-primary border-border rounded focus:ring-primary" id="remember"
                                type="checkbox">
                            <label class="font-label-md text-label-md text-on-surface-variant" for="remember">Ingat saya
                                untuk 30 hari</label>
                        </div>
                        <!-- Submit Button -->
                        <button
                            class="w-full h-14 bg-primary text-white font-title-lg rounded-lg shadow-lg shadow-primary/20 hover:bg-primary-container hover:scale-[1.01] active:scale-95 transition-all flex items-center justify-center gap-2"
                            type="submit">
                            Masuk
                            <span class="material-symbols-outlined">login</span>
                        </button>
                    </form>
                    <!-- Alternative Actions -->
                    <div class="mt-10 pt-8 border-t border-border flex flex-col items-center gap-6">
                        <p class="font-label-md text-label-md text-muted">Belum memiliki akses? <a
                                class="text-secondary font-semibold hover:underline" href="#">Hubungi Admin IT</a></p>
                        <div class="flex gap-4">
                            <button
                                class="px-4 py-2 flex items-center gap-2 text-on-surface-variant bg-surface-container-low hover:bg-surface-container rounded-full transition-colors font-label-md">
                                <span class="material-symbols-outlined text-[18px]">translate</span>
                                Bahasa Indonesia
                            </button>
                            <button
                                class="px-4 py-2 flex items-center gap-2 text-on-surface-variant bg-surface-container-low hover:bg-surface-container rounded-full transition-colors font-label-md">
                                <span class="material-symbols-outlined text-[18px]">support_agent</span>
                                Dukungan
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </main>

    </body>
</template>

<style lang="postcss" scoped>
.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

.mesh-background {
    background-color: #F8FAFC;
    background-image:
        radial-gradient(at 0% 0%, rgba(29, 140, 248, 0.05) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(15, 76, 129, 0.05) 0px, transparent 50%);
}

.glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(226, 232, 240, 0.8);
}
</style>