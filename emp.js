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

    document.getElementById('nemp').value = row.dataset.id;
    document.getElementById('ncin_mod').value = row.dataset.ncin;
    document.getElementById('nom_mod').value = row.dataset.nom;
    document.getElementById('tel_mod').value = row.dataset.tel;
    document.getElementById('mdp_mod').value = ''; 

    const sel = document.getElementById('adr_mod');
    for (let opt of sel.options) {
        opt.selected = (opt.value.toLowerCase() === row.dataset.adr.toLowerCase());
    }

    document.getElementById('btn-modifier').disabled = false;
    document.getElementById('btn-supprimer').disabled = false;
    document.getElementById('hint-selection').style.display = 'none';

    document.querySelectorAll('.ligne-employe').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
}