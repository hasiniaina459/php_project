window.addEventListener("scroll", function () {
    const nav = document.getElementById("entete");
    if (window.scrollY > 50) {
        nav.classList.add("active");
    } else {
        nav.classList.remove("active");
    }
});

function selectionner(btn) {
    const row = btn.closest('tr');

    document.getElementById('id').value       = row.dataset.id;
    document.getElementById('nom_mod').value  = row.dataset.nom;
    document.getElementById('date_mod').value = row.dataset.date;

    document.getElementById('btn-modifier').disabled  = false;
    document.getElementById('btn-supprimer').disabled = false;
    document.getElementById('hint-selection').style.display = 'none';

    document.querySelectorAll('.ligne-client').forEach(row => row.classList.remove('selected'));
    row.classList.add('selected');
}