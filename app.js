// Remplit le formulaire Modifier/Supprimer au clic sur "Selectionner"
function selectionner(btn) {
    const row = btn.closest('tr');

    // Utilise CODE_app comme identifiant (data-id dans le HTML)
    document.getElementById('napp').value = row.dataset.id;
    document.getElementById('marque_mod').value = row.dataset.marque;
    document.getElementById('libelle_mod').value = row.dataset.libelle;
    document.getElementById('prix_mod').value = row.dataset.prix;

    // Categorie : normalise la casse pour comparer
    const sel = document.getElementById('categorie_mod');
    for (let opt of sel.options) {
        opt.selected = (opt.value.toLowerCase() === row.dataset.categorie.toLowerCase());
    }

    // etat radio
    document.getElementById('good_mod').checked = (row.dataset.etat === '1');
    document.getElementById('bad_mod').checked = (row.dataset.etat === '0');

    // Activer les boutons et cacher le hint
    document.getElementById('btn-modifier').disabled = false;
    document.getElementById('btn-supprimer').disabled = false;
    document.getElementById('hint-selection').style.display = 'none';

    // Surligner la ligne selectionnee
    document.querySelectorAll('.ligne-appareil').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
}