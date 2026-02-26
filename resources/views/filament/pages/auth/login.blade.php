<div>
    <div class="relative flex min-h-screen w-full flex-col items-center justify-center p-6 bg-cover bg-center"
        style="background-image: linear-gradient(rgba(15, 33, 35, 0.85), rgba(15, 33, 35, 0.95)), url('https://lh3.googleusercontent.com/aida-public/AB6AXuDsv4c3wc6dmZoCYlkV2XVf8YUudEwK919Vneoppn9JkVaQd-M_H_4t5zH0LYZDhzb3qkFoP0dXddH2tugSn8zyN06DLtIi4JBtHRFT9WwzCzO_-cJkT0NDBJyrn_ILDstmVmeqsOsCiyuaUTNiwAgyKVDy1t05jN-qcSuzCiOI6rGubsOeehxdw1rEQVL95y4ch7B78xOkRbCZ0jEppU6QDrQ8l6eQYrBWeAElMnExJIf77CXO1Wgd3xdePt6E62psbrJAoXjWwQ');">

        <div class="absolute top-8 left-8 flex items-center gap-2 text-primary cursor-pointer hover:opacity-80 transition"
            onclick="window.location='/'">
            <span class="material-symbols-outlined font-black">arrow_back</span>
            <span class="text-sm font-black uppercase tracking-widest italic">Back to Site</span>
        </div>

        <div
            class="w-full max-w-md glass rounded-3xl p-10 flex flex-col items-center border-primary/30 shadow-[0_0_40px_rgba(0,229,255,0.15)] bg-slate-900/40 backdrop-blur-xl">
            <div class="mb-10 flex flex-col items-center">
                <div class="bg-primary/20 p-5 rounded-full mb-4 shadow-[0_0_20px_rgba(6,224,249,0.2)] animate-pulse">
                    <span class="material-symbols-outlined text-primary text-5xl fill-1">diamond</span>
                </div>
                <h1 class="text-4xl font-black tracking-tighter text-white italic uppercase">Diamond<span
                        class="text-primary">OS</span></h1>
                <p class="text-primary font-black text-[10px] tracking-[0.4em] uppercase mt-2">Admin Command Center</p>
            </div>

            <form wire:submit="authenticate" class="w-full">
                <div class="w-full flex flex-col space-y-6">
                    <div class="fi-form-ctn w-full">
                        {{ $this->form }}
                    </div>

                    <div class="flex flex-col gap-6 pt-4">
                        <button type="submit"
                            class="w-full bg-accent hover:bg-accent/90 text-white font-black py-5 rounded-2xl shadow-[0_8px_20px_rgba(255,110,64,0.3)] transition-all text-sm uppercase tracking-[0.2em] active:scale-95">
                            Enter Dashboard
                        </button>

                        <div class="flex justify-center">
                            <a class="text-primary text-[10px] font-black uppercase tracking-widest hover:underline italic"
                                href="#">Loss of encryption key?</a>
                        </div>
                    </div>
                </div>
            </form>

            <div class="mt-12 flex flex-col items-center gap-4">
                <div class="flex items-center gap-2 text-slate-500 text-[8px] font-black uppercase tracking-widest">
                    <span class="material-symbols-outlined text-xs text-primary fill-1">security</span>
                    <span>Secure Broadcast Tunnel active</span>
                </div>
                <p
                    class="text-slate-600 text-[8px] font-medium text-center max-w-[200px] leading-relaxed uppercase tracking-widest">
                    Authorized Personnel Only. Biometric monitoring in effect.
                </p>
            </div>
        </div>

        <div class="mt-10 text-slate-700 text-[10px] font-black uppercase tracking-[0.5em] italic">
            © 2026 DiamondOS Global System
        </div>
    </div>


</div>