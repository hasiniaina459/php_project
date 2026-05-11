<?php
//  Connexion  
require "connexion.php";
$message = '';

//  AJOUTER 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ncli      = (int)($_POST['N_cli']     ?? 0);
    $code_app  = (int)($_POST['CODE_app']  ?? 0);
    $montant   = trim($_POST['montant']    ?? '');
    $datedebut = trim($_POST['datedebut']  ?? '');
    $etat      = isset($_POST['etat']) ? (int)$_POST['etat'] : null;
    $datefin   = trim($_POST['datefin']    ?? '');

    if ($ncli && $code_app && $montant && $datedebut && $etat !== null && $datefin) {
        $stmt = $pdo->prepare(
            "INSERT INTO ENREGISTREMENT (N_cli,CODE_app,MONTANT_reg,DATE_reg,statue_reg,DATE_ret)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$ncli, $code_app, $montant, $datedebut, $etat, $datefin]);
        $message = '<p class="msg success">&#10004; Location enregistrée avec succès.</p>';
    } else {
        $message = '<p class="msg error">&#10008; Veuillez remplir tous les champs.</p>';
    }
}

//  MODIFIER 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['modifier'], $_GET['nreg'])) {
    $id        = (int)$_GET['nreg'];
    $ncli      = (int)($_GET['N_cli']     ?? 0);
    $code_app  = (int)($_GET['CODE_app']  ?? 0);
    $montant   = trim($_GET['montant']    ?? '');
    $datedebut = trim($_GET['datedebut']  ?? '');
    $etat      = isset($_GET['etat']) ? (int)$_GET['etat'] : null;
    $datefin   = trim($_GET['datefin']    ?? '');

    if ($id && $ncli && $code_app && $montant && $datedebut && $etat !== null && $datefin) {
        $stmt = $pdo->prepare(
            "UPDATE ENREGISTREMENT
            SET N_cli=?, CODE_app=?, MONTANT_reg=?, DATE_reg=?, statue_reg=?, DATE_ret=?
            WHERE ID_reg=?"
        );
        $stmt->execute([$ncli, $code_app, $montant, $datedebut, $etat, $datefin, $id]);
        $message = '<p class="msg success">&#10004; Location modifiée avec succès.</p>';
    } else {
        $message = '<p class="msg error">&#10008; Veuillez remplir tous les champs.</p>';
    }
}

// SUPPRIMER 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['supprimer'], $_GET['nreg'])) {
    $id = (int)$_GET['nreg'];
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM ENREGISTREMENT WHERE ID_reg=?");
        $stmt->execute([$id]);
        $message = '<p class="msg success">&#10004; Location supprimée avec succès.</p>';
    }
}

// ── LECTURE de toutes les locations ───────────────────────────
// Jointure pour afficher nom client et libellé appareil
$locations = $pdo->query(
    "SELECT e.ID_reg, e.N_cli, c.NOM_cli, c.prenom,
            e.CODE_app, a.LIB_app, a.marque,
            e.MONTANT_reg, e.DATE_reg, e.statue_reg, e.DATE_ret
    FROM   ENREGISTREMENT e,CLIENT c,APPAREIL a
    WHERE c.N_cli     = e.N_cli
    AND a.CODE_app  = e.CODE_app
    ORDER BY e.ID_reg ASC"
)->fetchAll(PDO::FETCH_ASSOC);

// Listes pour les selects
$clients  = $pdo->query("SELECT N_cli, NOM_cli, prenom FROM CLIENT ORDER BY NOM_cli ASC")->fetchAll(PDO::FETCH_ASSOC);
$appareils = $pdo->query("SELECT CODE_app, marque, LIB_app FROM APPAREIL ORDER BY marque ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location</title>
    <link rel="stylesheet" href="appstyle.css">
</head>
<body>
    <header>
        <a href="index.html">&#10096;</a>
        <h1>LOCATION</h1>
    </header>

    <?= $message ?>

    <section>
        <!-- ── TABLEAU D'AFFICHAGE ── -->
        <div class="affichage">
            <table>
                <thead>
                    <tr>
                        <th>N° Enreg.</th>
                        <th>Client</th>
                        <th>Appareil</th>
                        <th>Montant</th>
                        <th>Date début</th>
                        <th>Statut</th>
                        <th>Date retour</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($locations)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center;color:#888;padding:20px;">
                            Aucune location enregistrée.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($locations as $row): ?>
                    <tr class="ligne-location"
                        data-id="<?= $row['ID_reg'] ?>"
                        data-ncli="<?= $row['N_cli'] ?>"
                        data-codeapp="<?= $row['CODE_app'] ?>"
                        data-montant="<?= htmlspecialchars((string)($row['MONTANT_reg'] ?? ''), ENT_QUOTES) ?>"
                        data-datedebut="<?= htmlspecialchars((string)($row['DATE_reg']  ?? ''), ENT_QUOTES) ?>"
                        data-etat="<?= (int)($row['statue_reg'] ?? 0) ?>"
                        data-datefin="<?= htmlspecialchars((string)($row['DATE_ret']   ?? ''), ENT_QUOTES) ?>">
                        <td><?= $row['ID_reg'] ?></td>
                        <td><?= htmlspecialchars((string)($row['NOM_cli'] ?? '') . ' ' . (string)($row['prenom'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($row['marque'] ?? '') . ' ' . (string)($row['LIB_app'] ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($row['MONTANT_reg'] ?? '')) ?> Ar</td>
                        <td><?= htmlspecialchars((string)($row['DATE_reg']   ?? '')) ?></td>
                        <td><?= ($row['statue_reg'] ?? 0) ? 'En cours' : 'Terminé' ?></td>
                        <td><?= htmlspecialchars((string)($row['DATE_ret']   ?? '')) ?></td>
                        <td>
                            <button class="btn-select" onclick="selectionner(this)">Sélectionner</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ── FORMULAIRES ── -->
        <div class="formulaire">

            <!-- Formulaire AJOUTER -->
            <form action="location.php" method="post" id="form-ajouter">
                <h2 class="form-title">Ajouter</h2>

                <label for="ncli_add">Client :</label>
                <select name="N_cli" id="ncli_add">
                    <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['N_cli'] ?>">
                        <?= htmlspecialchars((string)($c['NOM_cli'] ?? '') . ' ' . (string)($c['prenom'] ?? '')) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <label for="codeapp_add">Appareil :</label>
                <select name="CODE_app" id="codeapp_add">
                    <?php foreach ($appareils as $a): ?>
                    <option value="<?= $a['CODE_app'] ?>">
                        <?= htmlspecialchars((string)($a['marque'] ?? '') . ' – ' . (string)($a['LIB_app'] ?? '')) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <label for="montant_add">Montant (Ar) :</label>
                <input type="text" name="montant" id="montant_add" placeholder="24000">

                <label for="datedebut_add">Date début :</label>
                <input type="date" name="datedebut" id="datedebut_add">

                <label>Statut :</label>
                <div class="etat">
                    <input type="radio" name="etat" value="1" id="encours_add">
                    <label for="encours_add">En cours</label>
                    <input type="radio" name="etat" value="0" id="termine_add">
                    <label for="termine_add">Terminé</label>
                </div>

                <label for="datefin_add">Date retour :</label>
                <input type="date" name="datefin" id="datefin_add">

                <button type="submit">Ajouter</button>
            </form>

            <!-- Formulaire MODIFIER / SUPPRIMER -->
            <form action="location.php" method="get" id="form-modifier">
                <h2 class="form-title">Modifier / Supprimer</h2>

                <input type="hidden" name="nreg" id="nreg" value="">

                <label for="ncli_mod">Client :</label>
                <select name="N_cli" id="ncli_mod">
                    <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['N_cli'] ?>">
                        <?= htmlspecialchars((string)($c['NOM_cli'] ?? '') . ' ' . (string)($c['prenom'] ?? '')) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <label for="codeapp_mod">Appareil :</label>
                <select name="CODE_app" id="codeapp_mod">
                    <?php foreach ($appareils as $a): ?>
                    <option value="<?= $a['CODE_app'] ?>">
                        <?= htmlspecialchars((string)($a['marque'] ?? '') . ' – ' . (string)($a['LIB_app'] ?? '')) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <label for="montant_mod">Montant (Ar) :</label>
                <input type="text" name="montant" id="montant_mod">

                <label for="datedebut_mod">Date début :</label>
                <input type="date" name="datedebut" id="datedebut_mod">

                <label>Statut :</label>
                <div class="etat">
                    <input type="radio" name="etat" value="1" id="encours_mod">
                    <label for="encours_mod">En cours</label>
                    <input type="radio" name="etat" value="0" id="termine_mod">
                    <label for="termine_mod">Terminé</label>
                </div>

                <label for="datefin_mod">Date retour :</label>
                <input type="date" name="datefin" id="datefin_mod">

                <p id="hint-selection">&larr; Sélectionnez une ligne dans le tableau.</p>

                <div class="updet">
                    <button type="submit" name="modifier"  id="btn-modifier"  disabled>Modifier</button>
                    <button type="submit" name="supprimer" id="btn-supprimer" disabled>Supprimer</button>
                </div>
            </form>

        </div>
    </section>

    <script src="loc.js">
    </script>
</body>
</html>
