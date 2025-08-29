{{-- resources/views/people/_form.blade.php --}}
@csrf

<div class="space-y-12">
    <div class="border-b border-gray-900/10 pb-12">
        <h2 class="text-base/7 font-semibold text-gray-900">Información de la Persona</h2>
        <p class="mt-1 text-sm/6 text-gray-600">Proporciona los detalles de la persona y selecciona los códigos SAT correspondientes.</p>

        <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
            {{-- Campo Razón Social --}}
            <div class="col-span-full">
                <label for="legalName" class="block text-sm/6 font-medium text-gray-900">
                    Razón Social <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <input id="legalName" type="text" name="legalName" value="{{ old('legalName', $person->legalName ?? '') }}" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" placeholder="Nombre completo o razón social" />
                </div>
                @error('legalName')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Email --}}
            <div class="sm:col-span-3">
                <label for="email" class="block text-sm/6 font-medium text-gray-900">
                    Email <span class="text-red-500">*</span>
                </label>
                <div class="mt-2">
                    <input id="email" type="email" name="email" value="{{ old('email', $person->email ?? '') }}" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" placeholder="correo@ejemplo.com" />
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Contraseña --}}
            <div class="sm:col-span-3">
                <label for="password" class="block text-sm/6 font-medium text-gray-900">
                    Contraseña @if(!isset($person))<span class="text-red-500">*</span>@endif
                </label>
                <div class="mt-2">
                    @if(!isset($person))
                        <input id="password" type="password" name="password" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" placeholder="Mínimo 8 caracteres" />
                    @else
                        <input id="password" type="password" name="password" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" placeholder="Dejar en blanco para no cambiar" />
                    @endif
                </div>
                @if(isset($person))
                    <p class="mt-1 text-sm text-gray-500">Deja en blanco si no quieres cambiar la contraseña actual</p>
                @endif
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo RFC --}}
            <div class="sm:col-span-3">
                <label for="tin" class="block text-sm/6 font-medium text-gray-900">RFC</label>
                <div class="mt-2">
                    <input id="tin" type="text" name="tin" value="{{ old('tin', $person->tin ?? '') }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" placeholder="ABC123456789" />
                </div>
                @error('tin')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Código Postal --}}
            <div class="sm:col-span-3">
                <label for="zipCode" class="block text-sm/6 font-medium text-gray-900">Código Postal</label>
                <div class="mt-2">
                    <input id="zipCode" type="text" name="zipCode" value="{{ old('zipCode', $person->zipCode ?? '') }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" placeholder="12345" />
                </div>
                @error('zipCode')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo Régimen de Capital --}}
            <div class="sm:col-span-3">
                <label for="capitalRegime" class="block text-sm/6 font-medium text-gray-900">Régimen de Capital</label>
                <div class="mt-2">
                    <input id="capitalRegime" type="text" name="capitalRegime" value="{{ old('capitalRegime', $person->capitalRegime ?? '') }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" placeholder="S.A. de C.V." />
                </div>
                @error('capitalRegime')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campo ID Fiscal API --}}
            <div class="sm:col-span-3">
                <label for="fiscalapiId" class="block text-sm/6 font-medium text-gray-900">ID Fiscal API</label>
                <div class="mt-2">
                    <input id="fiscalapiId" type="text" name="fiscalapiId" value="{{ old('fiscalapiId', $person->fiscalapiId ?? '') }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" placeholder="ID único de Fiscal API" />
                </div>
                @error('fiscalapiId')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dropdown SAT Régimen Fiscal --}}
            <x-dropdown-select
                name="satTaxRegimeId"
                label="Régimen Fiscal SAT"
                :options="$satTaxRegimes ?? []"
                :selected="old('satTaxRegimeId', $person->satTaxRegimeId ?? '')"
                :required="false"
                placeholder="Selecciona un régimen fiscal..."
            />

            {{-- Dropdown SAT Uso CFDI --}}
            <x-dropdown-select
                name="satCfdiUseId"
                label="Uso CFDI SAT"
                :options="$satCfdiUses ?? []"
                :selected="old('satCfdiUseId', $person->satCfdiUseId ?? '')"
                :required="false"
                placeholder="Selecciona un uso CFDI..."
            />

            {{-- Campo Contraseña Fiscal --}}
            <div class="sm:col-span-3">
                <label for="taxPassword" class="block text-sm/6 font-medium text-gray-900">Contraseña Fiscal</label>
                <div class="mt-2">
                    @if(!isset($person))
                        <input id="taxPassword" type="password" name="taxPassword" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" placeholder="Contraseña de certificados CSD" />
                    @else
                        <input id="taxPassword" type="password" name="taxPassword" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" placeholder="Dejar en blanco para no cambiar" />
                    @endif
                </div>
                @if(isset($person))
                    <p class="mt-1 text-sm text-gray-500">Deja en blanco si no quieres cambiar la contraseña fiscal actual</p>
                @endif
                @error('taxPassword')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-x-6">
    <a href="{{ route('people.index') }}" class="text-sm/6 font-semibold text-gray-900">Cancelar</a>
    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
        Guardar Persona
    </button>
</div>
