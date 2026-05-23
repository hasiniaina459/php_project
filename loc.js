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

    document.getElementById('nreg').value = row.dataset.id;
    document.getElementById('montant_mod').value = row.dataset.montant;
    document.getElementById('datedebut_mod').value = row.dataset.datedebut;
    document.getElementById('datefin_mod').value = row.dataset.datefin;

    // Client select
    const select_Cli = document.getElementById('ncli_mod');
    for (let opt of select_Cli.options) {
        opt.selected = (opt.value === row.dataset.ncli);
    }

    // Appareil select
    const select_App = document.getElementById('codeapp_mod');
    for (let opt of select_App.options) {
        opt.selected = (opt.value === row.dataset.codeapp);
    }

    // Statut radio
    document.getElementById('encours_mod').checked = (row.dataset.etat === '1');
    document.getElementById('termine_mod').checked = (row.dataset.etat === '0');
    //activation des deux bouton
    document.getElementById('btn-modifier').disabled = false;
    document.getElementById('btn-supprimer').disabled = false;
    document.getElementById('hint-selection').style.display = 'none';

    document.querySelectorAll('.ligne-location').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
}