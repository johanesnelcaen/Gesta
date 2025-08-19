<div class="p-6 bg-white rounded shadow">
  <!-- Titre dynamique -->
        <h3 id="monthTitle" class="text-lg font-semibold text-gray-700"></h3>


    <!-- Boutons de navigation -->
    <div class="mb-4 flex gap-4">
        <button id="prevBtn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Précédent</button>
        <button id="nextBtn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Suivant</button>
    
    </div>

    <!-- Le calendrier -->
    <div id="dp" style="width: 100%; height: 700px;"></div>

    <!-- Modal pour sous-tâches -->
<div id="subtaskModal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">

    <div class="bg-white p-6 rounded-lg w-[90%] md:w-[700px] max-h-[90vh] overflow-y-auto">
         <div class="mt-4 text-right">
            <button onclick="closeModal()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Fermer</button>
        </div>
        <h2 id="subtaskTitle" class="text-xl font-semibold mb-4 text-gray-700">Sous-tâches</h2>
        <div id="subtaskCalendar" class="w-full h-[500px]"></div>
       
    </div>
</div>

</div>

@push('scripts')
<script src="{{ asset('js/daypilot-all.min.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const dp = new DayPilot.Month("dp");
    dp.startDate = DayPilot.Date.today();
    dp.events.list = @json($tasks);

    dp.onEventClick = async function (args) {
        const taskId = args.e.id();
        const title = args.e.text();
        document.getElementById('subtaskTitle').innerText = `Sous-tâches de : ${title}`;

        const modal = document.getElementById('subtaskModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Charger les sous-tâches depuis l'API
       const res = await fetch(`/subtasks/${taskId}`);

if (!res.ok) {
  console.error("Erreur HTTP :", res.status);
  return;
}

const subtasks = await res.json();

        console.log("Sous-tâches récupérées :", subtasks);

        const subDp = new DayPilot.Calendar("subtaskCalendar");
        subDp.viewType = "Week";
        subDp.events.list = subtasks;
        subDp.init();
    };

    // Fonction globale pour fermer le modal
    function closeModal() {
        const modal = document.getElementById("subtaskModal");
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }
    window.closeModal = closeModal;

    dp.init();

    function updateMonthTitle() {
        const date = dp.startDate;
        const options = { month: 'long', year: 'numeric' };
        const formatted = new Date(date.toString()).toLocaleDateString('fr-FR', options);
        document.getElementById("monthTitle").textContent = "📅 " + formatted.charAt(0).toUpperCase() + formatted.slice(1);
    }

    updateMonthTitle();

    document.getElementById("prevBtn").addEventListener("click", function () {
        dp.startDate = dp.startDate.addMonths(-1);
        dp.update();
        updateMonthTitle();
    });

    document.getElementById("nextBtn").addEventListener("click", function () {
        dp.startDate = dp.startDate.addMonths(1);
        dp.update();
        updateMonthTitle();
    });
});

</script>

@endpush
