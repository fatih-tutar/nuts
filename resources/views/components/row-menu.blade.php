@props([
    'align' => 'left',
])

<div
    class="relative inline-flex"
    x-data="{
        open: false,
        style: {},
        init() {
            this._onOutside = (event) => {
                if (! this.open) return;
                if (this.$refs.button?.contains(event.target)) return;
                if (this.$refs.menu?.contains(event.target)) return;
                this.open = false;
            };
            this._onReposition = () => {
                if (this.open) this.place();
            };
            document.addEventListener('click', this._onOutside);
            window.addEventListener('scroll', this._onReposition, true);
            window.addEventListener('resize', this._onReposition);
        },
        destroy() {
            document.removeEventListener('click', this._onOutside);
            window.removeEventListener('scroll', this._onReposition, true);
            window.removeEventListener('resize', this._onReposition);
        },
        toggle() {
            this.open = ! this.open;
            if (this.open) {
                this.$nextTick(() => this.place());
            }
        },
        place() {
            const button = this.$refs.button;
            const menu = this.$refs.menu;
            if (! button || ! menu) return;

            const rect = button.getBoundingClientRect();
            const menuHeight = menu.offsetHeight || 96;
            const menuWidth = menu.offsetWidth || 176;
            const gap = 4;
            const openUp = (window.innerHeight - rect.bottom) < menuHeight + gap + 8;

            let top = openUp ? rect.top - menuHeight - gap : rect.bottom + gap;
            top = Math.max(8, Math.min(top, window.innerHeight - menuHeight - 8));

            let left = @js($align === 'right') ? rect.right - menuWidth : rect.left;
            left = Math.max(8, Math.min(left, window.innerWidth - menuWidth - 8));

            this.style = {
                position: 'fixed',
                top: `${top}px`,
                left: `${left}px`,
                zIndex: '60',
            };
        },
    }"
    @keydown.escape.window="open = false"
>
    <button
        type="button"
        x-ref="button"
        @click.stop="toggle()"
        class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        aria-label="İşlemler"
        :aria-expanded="open.toString()"
    >
        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 4.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3ZM10 11.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3ZM10 18.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3Z" />
        </svg>
    </button>

    <template x-teleport="body">
        <div
            x-ref="menu"
            x-show="open"
            x-cloak
            x-bind:style="style"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-44 origin-top-left rounded-md bg-white py-1 shadow-lg ring-1 ring-black/5"
            style="display: none;"
            @click="open = false"
        >
            {{ $slot }}
        </div>
    </template>
</div>
