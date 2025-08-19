<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accueil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
               <style>
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #fbc2eb 0%, #a6c1ee 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
   <div data-aos="zoom-in" class="bg-cover bg-center h-96" style='background-image: url("{{ asset('images/dash.jpg') }}");'
>
    <div class="container mx-auto px-4 py-12 max-w-4xl rounded-lg shadow-lg">
        <div class="p-8 sm:p-12">
            <div class="flex flex-col items-center text-center mb-12  bg-white/80">
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2">
                    Bienvenue sur 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 to-purple-600 ">GestA</span>
                </h1>
                <p class="text-gray-600 max-w-md  bg-white/50">
                    Organisez votre travail et collaborez avec votre équipe en toute simplicité.
                </p>
            </div>
            
        </div>
    </div>
</div>
<br>
    <div data-aos="fade-up" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                 <a href="{{ route('task-manager.index') }}" class="btn-primary text-white font-semibold py-4 px-6 rounded-xl flex items-center justify-center space-x-3">
    <i class="fas fa-list-check text-xl"></i>
    <span>Mes tâches</span>
</a>
 <a href="{{ route('groups.index') }}" class="btn-secondary text-white font-semibold py-4 px-6 rounded-xl flex items-center justify-center space-x-3">
    <i class="fas fa-users text-xl"></i>
    <span>Groupes</span>
</a>
</div>
            </div>
        </div>
    </div>
</x-app-layout>