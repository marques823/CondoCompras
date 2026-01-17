<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nova Demanda') }} - {{ $condominio->nome }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($errors->any())
                        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('zelador.demandas.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="space-y-6">
                            <!-- Título -->
                            <div>
                                <x-input-label for="titulo" :value="__('Título da Demanda')" />
                                <x-text-input id="titulo" class="block mt-1 w-full" type="text" name="titulo" :value="old('titulo')" required autofocus placeholder="Ex: Manutenção do portão elétrico" />
                                <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                            </div>

                            <!-- Descrição -->
                            <div>
                                <x-input-label for="descricao" :value="__('Descrição Detalhada')" />
                                <textarea id="descricao" name="descricao" rows="6" required class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Descreva detalhadamente o serviço necessário...">{{ old('descricao') }}</textarea>
                                <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                            </div>

                            <!-- Urgência -->
                            <div>
                                <x-input-label for="urgencia" :value="__('Nível de Urgência')" />
                                <select id="urgencia" name="urgencia" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                    <option value="">Selecione a urgência</option>
                                    <option value="baixa" {{ old('urgencia') == 'baixa' ? 'selected' : '' }}>Baixa</option>
                                    <option value="media" {{ old('urgencia') == 'media' ? 'selected' : '' }}>Média</option>
                                    <option value="alta" {{ old('urgencia') == 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="critica" {{ old('urgencia') == 'critica' ? 'selected' : '' }}>Crítica</option>
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Selecione o nível de urgência da demanda</p>
                                <x-input-error :messages="$errors->get('urgencia')" class="mt-2" />
                            </div>

                            <!-- Anexos (Fotos) -->
                            <div>
                                <x-input-label for="anexos" :value="__('Anexar Fotos (opcional)')" />
                                <input type="file" 
                                       id="anexos" 
                                       name="anexos[]" 
                                       multiple 
                                       accept="image/*,.pdf"
                                       class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Você pode selecionar múltiplas fotos (JPG, PNG, GIF, WEBP) ou PDFs. Máximo 10MB por arquivo.</p>
                                <div id="anexos-preview" class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2"></div>
                                <x-input-error :messages="$errors->get('anexos.*')" class="mt-2" />
                            </div>

                            <!-- Botões -->
                            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('zelador.demandas.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                                    Cancelar
                                </a>
                                <x-primary-button>
                                    {{ __('Criar Demanda') }}
                                </x-primary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview de imagens anexadas
        document.getElementById('anexos').addEventListener('change', function(e) {
            const preview = document.getElementById('anexos-preview');
            preview.innerHTML = '';
            
            Array.from(e.target.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative';
                        div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-24 object-cover rounded border border-gray-300">
                            <button type="button" onclick="removerAnexo(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">×</button>
                        `;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                } else {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <div class="w-full h-24 bg-gray-200 rounded border border-gray-300 flex items-center justify-center">
                            <span class="text-xs text-gray-600">${file.name}</span>
                        </div>
                        <button type="button" onclick="removerAnexo(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">×</button>
                    `;
                    preview.appendChild(div);
                }
            });
        });

        function removerAnexo(index) {
            const input = document.getElementById('anexos');
            const dt = new DataTransfer();
            Array.from(input.files).forEach((file, i) => {
                if (i !== index) {
                    dt.items.add(file);
                }
            });
            input.files = dt.files;
            input.dispatchEvent(new Event('change'));
        }
    </script>
</x-app-layout>
