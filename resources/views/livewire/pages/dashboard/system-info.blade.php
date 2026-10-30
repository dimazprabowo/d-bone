    {{-- System Info (gated by configuration_view — internal app info) --}}
    @can('configuration_view')
    <div class="dash-enter bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6" style="animation-delay:760ms">
        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4">Informasi System</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="dash-card flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full"></span>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Laravel {{ app()->version() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Framework</p>
                </div>
            </div>
            <div class="dash-card flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full"></span>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">PHP {{ phpversion() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Runtime</p>
                </div>
            </div>
            <div class="dash-card flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full"></span>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ config('app.env') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Environment</p>
                </div>
            </div>
            <div class="dash-card flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <span class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full"></span>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ config('app.timezone') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Timezone</p>
                </div>
            </div>
        </div>
    </div>
    @endcan
