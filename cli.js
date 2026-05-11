function selectionner(btn) {
    const row = btn.closest('tr');

    document.getElementById('ncli').value = row.dataset.id;
    document.getElementById('ncin_mod').value = row.dataset.ncin;
    document.getElementById('nom_mod').value = row.dataset.nom;
    document.getElementById('prenom_mod').value = row.dataset.prenom;
    document.getElementById('email_mod').value = row.dataset.email;
    document.getElementById('telephone_mod').value = row.dataset.telephone;

    const sel = document.getElementById('province_mod');
    for (let opt of sel.options) {
        opt.selected = (opt.value.toLowerCase() === row.dataset.province.toLowerCase());
    }

    document.getElementById('btn-modifier').disabled = false;
    document.getElementById('btn-supprimer').disabled = false;
    document.getElementById('hint-selection').style.display = 'none';

    document.querySelectorAll('.ligne-client').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
}