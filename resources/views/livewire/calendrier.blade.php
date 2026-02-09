<div class="p-6 bg-white rounded shadow">

    <!-- En-tête -->
    <div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h3 id="monthTitle" class="text-lg font-semibold text-gray-700"></h3>
        
        <div class="flex gap-2">
            <button id="viewDay" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-blue-500 hover:text-white transition">Jour</button>
            <button id="viewWeek" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-blue-500 hover:text-white transition">Semaine</button>
            <button id="viewMonth" class="px-4 py-2 bg-blue-500 text-white rounded">Mois</button>
        </div>
    </div>

    <!-- Sélecteur de date -->
    <div class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <div class="flex flex-col md:flex-row gap-4 items-start md:items-end">
            <div class="flex-1">
                <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                <input type="date" id="startDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">Date de fin (optionnel)</label>
                <input type="date" id="endDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex gap-2">
                <button id="applyDateBtn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">Appliquer</button>
                <button id="resetDateBtn" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 transition">Réinitialiser</button>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">💡 Laissez la date de fin vide pour afficher un seul jour</p>
    </div>

    <!-- Navigation -->
    <div class="mb-4 flex gap-4">
        <button id="prevBtn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">← <span id="prevLabel">Précédent</span></button>
        <button id="nextBtn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"><span id="nextLabel">Suivant</span> →</button>
    </div>

    <!-- Calendrier -->
    <div id="dp" style="width: 100%; height: 700px;"></div>

    <!-- Modal sous-tâches -->
    <div id="subtaskModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg w-[90%] md:w-[700px] max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h2 id="subtaskTitle" class="text-xl font-semibold text-gray-700">Sous-tâches</h2>
                <button id="closeModalBtn" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Fermer</button>
            </div>
            <div id="subtaskCalendar" class="w-full h-[500px]"></div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/daypilot-all.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let currentView = 'month';
    let dp = null;
    let subDp = null;
    const tasks = @json($tasks ?? []);

    // =======================
    // Initialisation calendrier
    // =======================
    function initCalendar(view) {
        const container = document.getElementById('dp');
        container.innerHTML = '';

        currentView = view;

        if (view === 'month') {
            dp = new DayPilot.Month("dp");
        } else {
            dp = new DayPilot.Calendar("dp");
            dp.viewType = view.charAt(0).toUpperCase() + view.slice(1);
        }

        dp.startDate = DayPilot.Date.today();
        dp.events.list = tasks;

        dp.onEventClick = async function(args) {
            const taskId = args.e.id();
            const title = args.e.text();

            document.getElementById('subtaskTitle').innerText = `Sous-tâches de : ${title}`;
            const modal = document.getElementById('subtaskModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            try {
                const res = await fetch(`/subtasks/${taskId}`);
                if (!res.ok) throw new Error(res.status);
                const subtasks = await res.json();

                if (!subDp) {
                    subDp = new DayPilot.Calendar("subtaskCalendar");
                    subDp.viewType = "Week";
                    subDp.init();
                }
                subDp.events.list = subtasks;
                subDp.update();

            } catch (error) {
                console.error("Erreur lors du chargement des sous-tâches:", error);
                alert("Une erreur est survenue");
            }
        };

        dp.init();
        updateTitle();
        updateNavigationLabels();
    }

    // =======================
    // Mise à jour titre
    // =======================
    function updateTitle() {
        const date = dp.startDate;
        let formatted = '';

        switch(currentView) {
            case 'day':
                formatted = new Date(date.toString()).toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                break;
            case 'week':
                const endOfWeek = date.addDays(6);
                formatted = `Semaine du ${new Date(date.toString()).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' })} au ${new Date(endOfWeek.toString()).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' })}`;
                break;
            case 'month':
                formatted = new Date(date.toString()).toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
                break;
        }

        document.getElementById("monthTitle").textContent = "📅 " + formatted.charAt(0).toUpperCase() + formatted.slice(1);
    }

    // =======================
    // Navigation
    // =======================
    function changeView(view) {
        document.querySelectorAll('#viewDay, #viewWeek, #viewMonth').forEach(btn => {
            btn.classList.remove('bg-blue-500','text-white');
            btn.classList.add('bg-gray-200','text-gray-700');
        });
        document.getElementById(`view${view.charAt(0).toUpperCase() + view.slice(1)}`).classList.remove('bg-gray-200','text-gray-700');
        document.getElementById(`view${view.charAt(0).toUpperCase() + view.slice(1)}`).classList.add('bg-blue-500','text-white');

        initCalendar(view);
    }

    function updateNavigationLabels() {
        const prevLabel = document.getElementById('prevLabel');
        const nextLabel = document.getElementById('nextLabel');
        switch(currentView) {
            case 'day': prevLabel.textContent = 'Jour précédent'; nextLabel.textContent = 'Jour suivant'; break;
            case 'week': prevLabel.textContent = 'Semaine précédente'; nextLabel.textContent = 'Semaine suivante'; break;
            case 'month': prevLabel.textContent = 'Mois précédent'; nextLabel.textContent = 'Mois suivant'; break;
        }
    }

    function goToDate(startDate, endDate = null) {
        const start = new DayPilot.Date(startDate);
        if (endDate) {
            const end = new DayPilot.Date(endDate);
            const daysDiff = Math.ceil((end.getTime() - start.getTime()) / (1000*60*60*24));
            if (daysDiff === 0) changeView('day');
            else if (daysDiff <= 7) changeView('week');
            else changeView('month');
        } else {
            changeView('day');
        }
        dp.startDate = start;
        dp.update();
        updateTitle();
    }

    // =======================
    // Event listeners
    // =======================
    document.getElementById('viewDay').addEventListener('click', () => changeView('day'));
    document.getElementById('viewWeek').addEventListener('click', () => changeView('week'));
    document.getElementById('viewMonth').addEventListener('click', () => changeView('month'));

    document.getElementById("prevBtn").addEventListener("click", () => {
        switch(currentView) {
            case 'day': dp.startDate = dp.startDate.addDays(-1); break;
            case 'week': dp.startDate = dp.startDate.addDays(-7); break;
            case 'month': dp.startDate = dp.startDate.addMonths(-1); break;
        }
        dp.update(); updateTitle();
    });

    document.getElementById("nextBtn").addEventListener("click", () => {
        switch(currentView) {
            case 'day': dp.startDate = dp.startDate.addDays(1); break;
            case 'week': dp.startDate = dp.startDate.addDays(7); break;
            case 'month': dp.startDate = dp.startDate.addMonths(1); break;
        }
        dp.update(); updateTitle();
    });

    document.getElementById("closeModalBtn").addEventListener("click", () => {
        const modal = document.getElementById("subtaskModal");
        modal.classList.add("hidden"); modal.classList.remove("flex");
    });

    document.getElementById("applyDateBtn").addEventListener("click", () => {
        const startDateInput = document.getElementById("startDate").value;
        const endDateInput = document.getElementById("endDate").value;
        if (!startDateInput) { alert("Veuillez sélectionner une date de début"); return; }
        goToDate(startDateInput, endDateInput || null);
    });

    document.getElementById("resetDateBtn").addEventListener("click", () => {
        document.getElementById("startDate").value = '';
        document.getElementById("endDate").value = '';
        changeView('month');
        dp.startDate = DayPilot.Date.today(); dp.update(); updateTitle();
    });

    // =======================
    // Initialisation
    // =======================
    initCalendar('month');
});
</script>
@endpush
