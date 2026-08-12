<div class="flex justify-center mb-6" x-data="{ open: false }">
    <div class="relative">
        <button 
            @click="open = !open" 
            @click.outside="open = false"
            type="button"
            class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition border border-gray-200 dark:border-gray-700"
        >
            @if(app()->getLocale() === 'ar')
                <svg class="w-5 h-5 rounded-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#006c35" d="M0 0h640v480H0z"/>
                    <text x="320" y="200" font-family="Arial" font-size="48" fill="#fff" text-anchor="middle" direction="rtl">لا إله إلا الله</text>
                    <text x="320" y="260" font-family="Arial" font-size="48" fill="#fff" text-anchor="middle" direction="rtl">محمد رسول الله</text>
                    <path fill="#fff" d="M250 300h140v20H250z"/>
                </svg>
                <span>العربية</span>
            @else
                <svg class="w-5 h-5 rounded-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#bd3d44" d="M0 0h640v37H0zm0 74h640v37H0zm0 73h640v37H0zm0 73h640v37H0zm0 74h640v36H0zm0 73h640v37H0zm0 73h640v37H0z"/>
                    <path fill="#fff" d="M0 37h640v37H0zm0 73h640v37H0zm0 74h640v36H0zm0 74h640v37H0zm0 73h640v37H0zm0 73h640v37H0z"/>
                    <path fill="#192f5d" d="M0 0h260v259H0z"/>
                    <g fill="#fff"><g id="us-d3"><g id="us-c3"><g id="us-e3"><g id="us-b3"><path id="us-a3" d="m30 17 3 10h10l-8 6 3 9-8-6-8 6 3-9-8-6h10z"/><use href="#us-a3" y="42"/><use href="#us-a3" y="84"/></g><use href="#us-b3" y="126"/></g><use href="#us-a3" y="168"/></g><use href="#us-e3" x="42"/></g><use href="#us-c3" x="84"/><use href="#us-d3" x="126"/><use href="#us-c3" x="168"/><use href="#us-d3" x="210"/></g>
                </svg>
                <span>English</span>
            @endif
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute left-1/2 -translate-x-1/2 mt-2 w-40 origin-top rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 dark:bg-gray-800 dark:ring-white/10 z-50"
            style="display: none;"
        >
            <div class="py-1">
                <a 
                    href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}"
                    class="flex items-center gap-3 px-4 py-2 text-sm {{ app()->getLocale() === 'en' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/50 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}"
                >
                    <svg class="w-5 h-5 rounded-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#bd3d44" d="M0 0h640v37H0zm0 74h640v37H0zm0 73h640v37H0zm0 73h640v37H0zm0 74h640v36H0zm0 73h640v37H0zm0 73h640v37H0z"/>
                        <path fill="#fff" d="M0 37h640v37H0zm0 73h640v37H0zm0 74h640v36H0zm0 74h640v37H0zm0 73h640v37H0zm0 73h640v37H0z"/>
                        <path fill="#192f5d" d="M0 0h260v259H0z"/>
                        <g fill="#fff"><g id="d4"><g id="c4"><g id="e4"><g id="b4"><path id="a4" d="m30 17 3 10h10l-8 6 3 9-8-6-8 6 3-9-8-6h10z"/><use href="#a4" y="42"/><use href="#a4" y="84"/></g><use href="#b4" y="126"/></g><use href="#a4" y="168"/></g><use href="#e4" x="42"/></g><use href="#c4" x="84"/><use href="#d4" x="126"/><use href="#c4" x="168"/><use href="#d4" x="210"/></g>
                    </svg>
                    <span>English</span>
                    @if(app()->getLocale() === 'en')
                        <svg class="w-4 h-4 ml-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @endif
                </a>
                <a 
                    href="{{ request()->fullUrlWithQuery(['lang' => 'ar']) }}"
                    class="flex items-center gap-3 px-4 py-2 text-sm {{ app()->getLocale() === 'ar' ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/50 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700' }}"
                >
                    <svg class="w-5 h-5 rounded-sm" viewBox="0 0 640 480" xmlns="http://www.w3.org/2000/svg">
                        <path fill="#006c35" d="M0 0h640v480H0z"/>
                        <text x="320" y="200" font-family="Arial" font-size="48" fill="#fff" text-anchor="middle" direction="rtl">لا إله إلا الله</text>
                        <text x="320" y="260" font-family="Arial" font-size="48" fill="#fff" text-anchor="middle" direction="rtl">محمد رسول الله</text>
                        <path fill="#fff" d="M250 300h140v20H250z"/>
                    </svg>
                    <span>العربية</span>
                    @if(app()->getLocale() === 'ar')
                        <svg class="w-4 h-4 ml-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    @endif
                </a>
            </div>
        </div>
    </div>
</div>
