<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Gestion des utilisateurs</h2>

    @if (session()->has('message'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
    @endif

    <!-- Formulaire d'ajout d'utilisateur -->
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-green-800 mb-4">Ajouter un utilisateur</h3>
        <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label for="nomComplet" class="block text-sm font-medium text-gray-700 mb-1">Nom Complet *</label>
                <input type="text" wire:model.defer="nomComplet" id="nomComplet" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                       placeholder="Nom complet de l'utilisateur">
                @error('nomComplet') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="login" class="block text-sm font-medium text-gray-700 mb-1">Identifiant *</label>
                <input type="text" wire:model.defer="login" id="login" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                       placeholder="Nom d'utilisateur">
                @error('login') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe *</label>
                <input type="password" wire:model.defer="password" id="password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                       placeholder="Mot de passe">
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    Enregistrer
                </button>
            </div>
        </form>
        <!-- Champs supplémentaires -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Rôle *</label>
                <select wire:model.defer="role" id="role" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="">Sélectionner un rôle</option>
                    @foreach($roles as $roleItem)
                        <option value="{{ $roleItem['id'] }}">{{ $roleItem['Role'] }}</option>
                    @endforeach
                </select>
                @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
            <div class="flex items-center">
                <input type="checkbox" wire:model.defer="isActive" id="isActive" 
                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                <label for="isActive" class="ml-2 block text-sm text-gray-700">
                    Compte actif
                </label>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="mb-4 flex flex-col md:flex-row items-center gap-4">
        <div class="flex items-center gap-2">
            <label class="font-semibold text-sm">Filtrer par rôle :</label>
            <select wire:model="selectedRole" class="rounded border border-gray-300 px-3 py-2">
                <option value="">Tous</option>
                @foreach($roles as $roleItem)
                    <option value="{{ $roleItem['id'] }}">{{ $roleItem['Role'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <input type="text" wire:model="search" placeholder="Rechercher un utilisateur..." 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
        </div>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="mt-2 max-h-[60vh] overflow-y-auto">
        <div class="overflow-x-auto">
            <table class="min-w-full w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom Complet</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Identifiant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->NomComplet }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $user->login }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @php
                                    $roleText = match($user->IdClasseUser) {
                                        1 => 'Secrétaire',
                                        2 => 'Médecin',
                                        3 => 'Propriétaire',
                                        default => '-'
                                    };
                                @endphp
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($user->IdClasseUser == 1) bg-purple-100 text-purple-800
                                    @elseif($user->IdClasseUser == 2) bg-blue-100 text-blue-800
                                    @elseif($user->IdClasseUser == 3) bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $roleText }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <button wire:click="toggleStatus({{ $user->Iduser }})" 
                                        class="px-2 py-1 text-xs rounded-full 
                                        @if(!$user->ismasquer) bg-green-100 text-green-800 hover:bg-green-200
                                        @else bg-red-100 text-red-800 hover:bg-red-200
                                        @endif transition-colors">
                                    @if(!$user->ismasquer) Actif
                                    @else Inactif
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <button wire:click="openModal({{ $user->Iduser }})" 
                                        class="text-blue-600 hover:text-blue-800 mr-3">Modifier</button>
                                @if($user->IdClasseUser != 3 || $users->where('IdClasseUser', 3)->where('ismasquer', false)->count() > 1)
                                    <button wire:click="confirmDelete({{ $user->Iduser }})" 
                                            class="text-red-600 hover:text-red-800">Supprimer</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center text-gray-400">Aucun utilisateur trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Modal édition -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-8 relative">
                <div class="bg-primary text-white p-4 rounded-t-lg -mt-8 -mx-8 mb-6">
                    <h2 class="text-xl font-bold">Modifier un utilisateur</h2>
                </div>
                <button wire:click="closeModal" class="absolute top-4 right-4 text-gray-500 hover:text-red-600 text-2xl font-bold">&times;</button>
                <form wire:submit.prevent="save">
                    <div class="mb-4">
                        <label for="nomComplet" class="block text-sm font-medium text-gray-700">Nom Complet *</label>
                        <input type="text" wire:model.defer="nomComplet" id="nomComplet" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        @error('nomComplet') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="login" class="block text-sm font-medium text-gray-700">Identifiant *</label>
                        <input type="text" wire:model.defer="login" id="login" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        @error('login') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe (laisser vide pour ne pas modifier)</label>
                        <input type="password" wire:model.defer="password" id="password" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <label for="role" class="block text-sm font-medium text-gray-700">Rôle *</label>
                        <select wire:model.defer="role" id="role" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                            <option value="">Sélectionner un rôle</option>
                            @foreach($roles as $roleItem)
                                <option value="{{ $roleItem['id'] }}">{{ $roleItem['Role'] }}</option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model.defer="isActive" id="isActive" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                            <label for="isActive" class="ml-2 block text-sm text-gray-700">
                                Compte actif
                            </label>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal de suppression -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-8 relative">
                <div class="text-xl font-bold mb-4">Confirmer la suppression</div>
                <p class="mb-6">Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="deleteUser" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
                    <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Annuler</button>
                </div>
            </div>
        </div>
    @endif
</div>
