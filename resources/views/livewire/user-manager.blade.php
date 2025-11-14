<div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Gestion des utilisateurs</h2>

    <!-- Messages de notification -->
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
                    <option value="1">Secrétaire</option>
                    <option value="2">Médecin</option>
                    <option value="3">Propriétaire</option>
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

    <!-- Barre de recherche -->
    <div class="mb-4">
        <div class="relative rounded-md shadow-sm">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input wire:model.debounce.300ms="search" type="text" 
                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-3 py-2 sm:text-sm border-gray-300 rounded-md" 
                   placeholder="Rechercher un utilisateur...">
        </div>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('NomComplet')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        <div class="flex items-center">
                            Nom Complet
                            @if($sortField === 'NomComplet')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </div>
                    </th>
                    <th wire:click="sortBy('login')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        <div class="flex items-center">
                            Identifiant
                            @if($sortField === 'login')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $user->NomComplet }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $user->login }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($user->IdClasseUser == 1) bg-purple-100 text-purple-800
                                @elseif($user->IdClasseUser == 2) bg-blue-100 text-blue-800
                                @else bg-green-100 text-green-800
                                @endif">
                                @if($user->IdClasseUser == 1) Secrétaire
                                @elseif($user->IdClasseUser == 2) Médecin
                                @else Propriétaire
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button wire:click="toggleStatus({{ $user->Iduser }})" 
                                    wire:loading.attr="disabled"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if(!$user->ismasquer) bg-green-100 text-green-800 hover:bg-green-200
                                    @else bg-red-100 text-red-800 hover:bg-red-200
                                    @endif transition-colors duration-200">
                                @if(!$user->ismasquer) 
                                    <i class="fas fa-check-circle mr-1"></i> Actif
                                @else 
                                    <i class="fas fa-times-circle mr-1"></i> Inactif
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <button wire:click="openModal('edit', {{ $user->Iduser }})"
                                        wire:loading.attr="disabled"
                                        class="text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($user->IdClasseUser != 3 || $users->where('IdClasseUser', 3)->where('ismasquer', false)->count() > 1)
                                    <button wire:click="delete({{ $user->Iduser }})"
                                            wire:loading.attr="disabled"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')"
                                            class="text-red-600 hover:text-red-800 transition-colors duration-200">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            Aucun utilisateur trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>

    <!-- Modal de modification uniquement -->
    @if($showModal)
        <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="save">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="mb-4">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Modifier un utilisateur
                                </h3>
                            </div>

                            <div class="space-y-4">
                                <!-- Nom Complet -->
                                <div>
                                    <label for="nomComplet" class="block text-sm font-medium text-gray-700">Nom Complet</label>
                                    <input type="text" wire:model="nomComplet" id="nomComplet" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('nomComplet') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Login -->
                                <div>
                                    <label for="login" class="block text-sm font-medium text-gray-700">Identifiant</label>
                                    <input type="text" wire:model="login" id="login" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('login') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Mot de passe -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">
                                        Mot de passe (laisser vide pour ne pas modifier)
                                    </label>
                                    <input type="password" wire:model="password" id="password" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Rôle -->
                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700">Rôle</label>
                                    <select wire:model="role" id="role" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">Sélectionner un rôle</option>
                                        <option value="1">Secrétaire</option>
                                        <option value="2">Médecin</option>
                                        <option value="3">Propriétaire</option>
                                    </select>
                                    @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- Statut -->
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="isActive" id="isActive" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="isActive" class="ml-2 block text-sm text-gray-700">
                                        Compte actif
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Enregistrer
                            </button>
                            <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div> 