<?php
//  Connexion PDO 
require "connexion.php";
$message = '';
//$qc=query code
//  AJOUTER  
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ncin      = trim($_POST['ncin']      ?? '');
    $nom       = trim($_POST['nom']       ?? '');
    $prenom    = trim($_POST['prenom']    ?? '');
    $province  = trim($_POST['province']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $telephone = trim($_POST['telephone'] ?? '');

    if ($ncin && $nom && $prenom && $province && $email && $telephone) {
        $qc = $pdo->prepare(
            "INSERT INTO CLIENT (NCIN,NOM_cli,prenom,ADR_cli,email,TEL_cli)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $qc->execute([$ncin, $nom, $prenom, $province, $email, $telephone]);
        $message = '<p class="msg success">&#10004; Client ajouté avec succès.</p>';
    } else {
        $message = '<p class="msg error">&#10008; Veuillez remplir tous les champs.</p>';
    }
}

//  MODIFIER (GET + modifier) 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['modifier'], $_GET['ncli'])) {
    $id        = (int)$_GET['ncli'];
    $ncin      = trim($_GET['ncin']      ?? '');
    $nom       = trim($_GET['nom']       ?? '');
    $prenom    = trim($_GET['prenom']    ?? '');
    $province  = trim($_GET['province']  ?? '');
    $email     = trim($_GET['email']     ?? '');
    $telephone = trim($_GET['telephone'] ?? '');

    if ($id && $ncin && $nom && $prenom && $province && $email && $telephone) {
        $qc = $pdo->prepare(
            "UPDATE CLIENT
            SET NCIN=?, NOM_cli=?, prenom=?, ADR_cli=?, email=?, TEL_cli=?
            WHERE N_cli=?"
        );
        $qc->execute([$ncin, $nom, $prenom, $province, $email, $telephone, $id]);
        $message = '<p class="msg success">&#10004; Client modifié avec succès.</p>';
    } else {
        $message = '<p class="msg error">&#10008; Veuillez remplir tous les champs.</p>';
    }
}

// SUPPRIMER (GET + supprimer) 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['supprimer'], $_GET['ncli'])) {
    $id = (int)$_GET['ncli'];
    if ($id) {
        $qc = $pdo->prepare("DELETE FROM CLIENT WHERE N_cli=?");
        $qc->execute([$id]);
        $message = '<p class="msg success">&#10004; Client supprimé avec succès.</p>';
    }
}

// LECTURE de tous les clients 
$clients = $pdo->query(
    "SELECT N_cli, NCIN, NOM_cli, prenom, ADR_cli, email, TEL_cli
    FROM   CLIENT
    ORDER BY N_cli ASC"
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
        <h1>CLIENT</h1>
    </header>

    <?= $message ?>
    <div class="stat">
        <div class="total">
            <label for="number_app">nombre total <br> de client </label>
            <label for="nbcli">
                <?php
                $cmd = $pdo->prepare(
                "SELECT COUNT(*) FROM CLIENT"
                );
                $cmd->execute();
                echo $cmd->fetchColumn();
            ?>
            </label>
        </div>
    </div>
    <section>
        <!-- ── TABLEAU D'AFFICHAGE ── -->
        <div class="affichage">
            <div class="affichage-scroll">
                <table>
                <thead>
                    <tr>
                        <th>N° Client</th>
                        <th>CIN</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Province</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($clients)): ?>
                    <tr>
                        <td colspan="8">
                            Aucun client enregistré.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clients as $row): ?>
                    <tr class="ligne-client"
                        data-id="<?= $row['N_cli'] ?>"
                        data-ncin="<?= htmlspecialchars((string)($row['NCIN']      ?? ''), ENT_QUOTES) ?>"
                        data-nom="<?= htmlspecialchars((string)($row['NOM_cli']    ?? ''), ENT_QUOTES) ?>"
                        data-prenom="<?= htmlspecialchars((string)($row['prenom']  ?? ''), ENT_QUOTES) ?>"
                        data-province="<?= htmlspecialchars((string)($row['ADR_cli'] ?? ''), ENT_QUOTES) ?>"
                        data-email="<?= htmlspecialchars((string)($row['email']    ?? ''), ENT_QUOTES) ?>"
                        data-telephone="<?= htmlspecialchars((string)($row['TEL_cli'] ?? ''), ENT_QUOTES) ?>">
                        <td><?= $row['N_cli'] ?></td>
                        <td><?= htmlspecialchars((string)($row['NCIN']      ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($row['NOM_cli']   ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($row['prenom']    ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($row['ADR_cli']   ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($row['email']     ?? '')) ?></td>
                        <td><?= htmlspecialchars((string)($row['TEL_cli']   ?? '')) ?></td>
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
            <form action="client.php" method="post" id="form-ajouter">
                <h2 class="form-title">Ajouter</h2>

                <label for="ncin_add">Numéro CIN :</label>
                <input type="text" name="ncin" id="ncin_add" placeholder="201031054745">

                <label for="nom_add">Nom :</label>
                <input type="text" name="nom" id="nom_add" placeholder="Rakotondratsimba">

                <label for="prenom_add">Prénom :</label>
                <input type="text" name="prenom" id="prenom_add" placeholder="Tefihaja">

                <label for="province_add">Province :</label>
                <select name="province" id="province_add">
                    <option value="antananarivo">Antananarivo</option>
                    <option value="toamasina">Toamasina</option>
                    <option value="mahajanga">Mahajanga</option>
                    <option value="fianarantsoa">Fianarantsoa</option>
                    <option value="toliara">Toliara</option>
                    <option value="diego">Diego</option>
                </select>

                <label for="email_add">Email :</label>
                <input type="email" name="email" id="email_add" placeholder="Tefi09@gmail.com">

                <label for="telephone_add">Téléphone :</label>
                <input type="text" name="telephone" id="telephone_add" placeholder="0345063254">

                <button type="submit">Ajouter</button>
            </form>

            <!-- Formulaire MODIFIER / SUPPRIMER -->
            <form action="client.php" method="get" id="form-modifier">
                <h2 class="form-title">Modifier / Supprimer</h2>

                <input type="hidden" name="ncli" id="ncli" value="">

                <label for="ncin_mod">Numéro CIN :</label>
                <input type="text" name="ncin" id="ncin_mod">

                <label for="nom_mod">Nom :</label>
                <input type="text" name="nom" id="nom_mod">

                <label for="prenom_mod">Prénom :</label>
                <input type="text" name="prenom" id="prenom_mod">

                <label for="province_mod">Province :</label>
                <select name="province" id="province_mod">
                    <option value="antananarivo">Antananarivo</option>
                    <option value="toamasina">Toamasina</option>
                    <option value="mahajanga">Mahajanga</option>
                    <option value="fianarantsoa">Fianarantsoa</option>
                    <option value="toliara">Toliara</option>
                    <option value="diego">Diego</option>
                </select>

                <label for="email_mod">Email :</label>
                <input type="email" name="email" id="email_mod">

                <label for="telephone_mod">Téléphone :</label>
                <input type="text" name="telephone" id="telephone_mod">

                <p id="hint-selection">&larr; Sélectionnez une ligne dans le tableau.</p>

                <div class="updet">
                    <button type="submit" name="modifier"  id="btn-modifier"  disabled>Modifier</button>
                    <button type="submit" name="supprimer" id="btn-supprimer" disabled>Supprimer</button>
                </div>
            </form>

        </div>
    </section>
    <script src="cli.js"></script>
</body>
</html>
