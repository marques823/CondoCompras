<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Centro de Notificações</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Gerencie como o sistema se comunica com prestadores e administradores.</p>
                </div>
            </div>

            <form action="{{ route('notifications.settings.update') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    {{-- Painel WhatsApp --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">WhatsApp API</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach($settings['whatsapp'] ?? [] as $setting)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ $setting->description }}
                                    </label>
                                    @if($setting->type === 'boolean')
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="{{ $setting->key }}" value="1" class="sr-only peer" {{ $setting->value == '1' ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-500 dark:text-gray-400">Ativado</span>
                                        </label>
                                    @else
                                        <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @endif
                                </div>
                            @endforeach
                            <p class="text-xs text-gray-500 italic">Recomendamos o uso da Evolution API para integração direta.</p>
                        </div>
                    </div>

                    {{-- Painel E-mail --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">E-mail</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @foreach($settings['email'] ?? [] as $setting)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ $setting->description }}
                                    </label>
                                    @if($setting->type === 'boolean')
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="{{ $setting->key }}" value="1" class="sr-only peer" {{ $setting->value == '1' ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-500 dark:text-gray-400">Ativado</span>
                                        </label>
                                    @else
                                        <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @endif
                                </div>
                            @endforeach
                            <p class="text-xs text-gray-500">As configurações de servidor SMTP devem ser feitas no arquivo .env.</p>
                        </div>
                    </div>

                    {{-- Painel Gatilhos --}}
                    <div class="md:col-span-2 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Gatilhos de Notificação Automática</h3>
                        </div>
                        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($settings['triggers'] ?? [] as $setting)
                                <div class="flex items-start gap-3 p-3 rounded-lg border border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors">
                                    <div class="pt-0.5">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="{{ $setting->key }}" value="1" class="sr-only peer" {{ $setting->value == '1' ? 'checked' : '' }}>
                                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-amber-500"></div>
                                        </label>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 leading-tight">
                                            {{ str_replace('Notificar ', '', $setting->description) }}
                                        </p>
                                        <span class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ $setting->key }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                        Salvar Configurações
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
