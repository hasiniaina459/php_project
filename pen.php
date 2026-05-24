<?php
session_start();
if (!isset($_SESSION['emp_id'])) {
    header("Location: form_pen.php"); // pas connecté → renvoi au login
    exit;
}
//  Connexion PDO 
require "connexion.php";
$message = '';

//$qc=query code
//  AJOUTER  
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom      = trim($_POST['nom']      ?? '');
    $date       = trim($_POST['date']       ?? '');
    if ($nom && $date) {
        $qc = $pdo->prepare(
            "INSERT INTO PENALITE(NOM_pen,DATE_pen)
            VALUES(?, ?)"
        );
        $qc->execute([$nom, $date]);
        $message = '<p class="msg success">&#10004; Client ajouté avec succès.</p>';
    } else {
        $message = '<p class="msg error">&#10008; Veuillez remplir tous les champs.</p>';
    }
}

//  MODIFIER (GET + modifier) 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['modifier'], $_GET['id'])) {
    $id        = (int)$_GET['id'];
    $nom      = trim($_GET['nom']      ?? '');
    $date       = trim($_GET['date']       ?? '');
    if ($id && $nom && $date) {
        $qc = $pdo->prepare(
            "UPDATE PENALITE SET NOM_pen=?,DATE_pen=?
            WHERE ID_pen=?"
        );
        $qc->execute([$nom, $date,$id]);
        $message = '<p class="msg success">&#10004; Client modifié avec succès.</p>';
    } else {
        $message = '<p class="msg error">&#10008; Veuillez remplir tous les champs.</p>';
    }
}

// SUPPRIMER (GET + supprimer) 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['supprimer'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id) {
        $qc = $pdo->prepare("DELETE FROM PENALITE WHERE ID_pen=?");
        $qc->execute([$id]);
        $message = '<p class="msg success">&#10004; Client supprimé avec succès.</p>';
    }
}

// LECTURE de tous les clients 
$clients = $pdo->query(
    "SELECT ID_pen,NOM_pen,DATE_pen 
    FROM PENALITE
    ORDER BY ID_pen ASC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client</title>
    <link rel="stylesheet" href="appstyle.css">
</head>

<body>
    <header id="entete">
        <a href="index.html">&#10096;</a>
        <h1>PENALITE</h1>
    </header>

    <?= $message ?>
    <section>
        <!-- ── TABLEAU D'AFFICHAGE ── -->
        <div class="affichage">
            <div class="affichage-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>Nom</th>
                            <th>date</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clients)): ?>
                            <tr>
                                <td colspan="3">
                                    Aucun client enregistré.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clients as $row): ?>
                                <tr class="ligne-client"
                                    data-id="<?= htmlspecialchars((string)($row['ID_pen']      ?? ''), ENT_QUOTES) ?>"
                                    data-nom="<?= htmlspecialchars((string)($row['NOM_pen']    ?? ''), ENT_QUOTES) ?>"
                                    data-date="<?= htmlspecialchars((string)($row['DATE_pen']  ?? ''), ENT_QUOTES) ?>">
                                    <td><?= htmlspecialchars((string)($row['ID_pen']      ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($row['NOM_pen']   ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($row['DATE_pen']    ?? '')) ?></td>
                                    <td>
                                        <button class="btn-select" onclick="selectionner(this)">Sélectionner</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── FORMULAIRES ── -->
        <div class="formulaire">

            <!-- Formulaire AJOUTER -->
            <form action="pen.php" method="post" id="form-ajouter">
                <h2 class="form-title">Ajouter</h2>

                <label for="ncin_add">Nom:</label>
                <input type="text" name="nom" id="nom_add" placeholder="201031054745">

                <label for="nom_add">date :</label>
                <input type="date" name="date">

                <button type="submit">Ajouter</button>
            </form>

            <!-- Formulaire MODIFIER / SUPPRIMER -->
            <form action="pen.php" method="get" id="form-modifier">
                <h2 class="form-title">Modifier / Supprimer</h2>

                <input type="hidden" name="id" id="id" value="">

                <label for="ncin_add">Nom:</label>
                <input type="text" name="nom" id="nom_mod" placeholder="201031054745">

                <label for="nom_add">date :</label>
                <input type="date" name="date" id="date_mod">

                <p id="hint-selection">&larr; Sélectionnez une ligne dans le tableau.</p>

                <div class="updet">
                    <button type="submit" name="modifier" id="btn-modifier" disabled>Modifier</button>
                    <button type="submit" name="supprimer" id="btn-supprimer" disabled>Supprimer</button>
                </div>
            </form>

        </div>
    </section>
    <script src="pen.js"></script>
</body>

</html>