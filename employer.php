<?php
//  Connexion PDO 
require "connexion.php";
$message = '';

// AJOUTER (POST) 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ncin_emp   = trim($_POST['NCIN_emp']   ?? '');
    $nom_emp    = trim($_POST['NOM_emp']    ?? '');
    $tel_emp    = trim($_POST['TEL_emp']    ?? '');
    $adr_emp    = trim($_POST['ADR_emp']    ?? '');
    $motdepasse = trim($_POST['motdepasse'] ?? '');

    if ($ncin_emp && $nom_emp && $tel_emp && $adr_emp && $motdepasse) {
        $hash = password_hash($motdepasse, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            "INSERT INTO EMPLOYER (NCIN_emp, NOM_emp, TEL_emp, ADR_emp, motdepasse)
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$ncin_emp, $nom_emp, $tel_emp, $adr_emp, $hash]);
        $message = '<p class="msg success">&#10004; Employé ajouté avec succès.</p>';
    } else {
        $message = '<p class="msg error">&#10008; Veuillez remplir tous les champs.</p>';
    }
}

//  MODIFIER 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['modifier'], $_GET['nemp'])) {
    $id         = (int)$_GET['nemp'];
    $ncin_emp   = trim($_GET['NCIN_emp']   ?? '');
    $nom_emp    = trim($_GET['NOM_emp']    ?? '');
    $tel_emp    = trim($_GET['TEL_emp']    ?? '');
    $adr_emp    = trim($_GET['ADR_emp']    ?? '');
    $motdepasse = trim($_GET['motdepasse'] ?? '');

    if ($id && $ncin_emp && $nom_emp && $tel_emp && $adr_emp) {
        if ($motdepasse !== '') {
            $hash = password_hash($motdepasse, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "UPDATE EMPLOYER
                SET NCIN_emp=?, NOM_emp=?, TEL_emp=?, ADR_emp=?, motdepasse=?
                WHERE N_emp=?"
            );
            $stmt->execute([$ncin_emp, $nom_emp, $tel_emp, $adr_emp, $hash, $id]);
        } else {
            $stmt = $pdo->prepare(
                "UPDATE EMPLOYER
                SET NCIN_emp=?, NOM_emp=?, TEL_emp=?, ADR_emp=?
                WHERE N_emp=?"
            );
            $stmt->execute([$ncin_emp, $nom_emp, $tel_emp, $adr_emp, $id]);
        }
        $message = '<p class="msg success">&#10004; Employé modifié avec succès.</p>';
    } else {
        $message = '<p class="msg error">&#10008; Veuillez remplir tous les champs.</p>';
    }
}

//  SUPPRIMER (GET + supprimer) 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['supprimer'], $_GET['nemp'])) {
    $id = (int)$_GET['nemp'];
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM EMPLOYER WHERE N_emp=?");
        $stmt->execute([$id]);
        $message = '<p class="msg success">&#10004; Employé supprimé avec succès.</p>';
    }
}

//  LECTURE de tous les employés 
$employes = $pdo->query(
    "SELECT N_emp, NCIN_emp, NOM_emp, TEL_emp, ADR_emp
    FROM   EMPLOYER
    ORDER BY N_emp ASC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>administration</title>
    <link rel="stylesheet" href="appstyle.css">
</head>
<body>
    <header>
        <a href="index.html">&#10096;</a>
        <h1>EMPLOYÉ</h1>
    </header>

    <?= $message ?>

    <section>
        <!-- TABLEAU D'AFFICHAGE -->
        <div class="affichage">
            <table>
                <thead>
                    <tr>
                        <th>N° Emp.</th>
                        <th>NCIN</th>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($employes)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:#888;padding:20px;">
                            Aucun employé enregistré.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($employes as $row): ?>
                    <tr class="ligne-employe"
                        data-id="<?= $row['N_emp'] ?>"
                        data-ncin="<?= htmlspecialchars($row['NCIN_emp'], ENT_QUOTES) ?>"
                        data-nom="<?= htmlspecialchars($row['NOM_emp'],   ENT_QUOTES) ?>"
                        data-tel="<?= htmlspecialchars($row['TEL_emp'],   ENT_QUOTES) ?>"
                        data-adr="<?= htmlspecialchars($row['ADR_emp'],   ENT_QUOTES) ?>">
                        <td><?= $row['N_emp'] ?></td>
                        <td><?= htmlspecialchars($row['NCIN_emp']) ?></td>
                        <td><?= htmlspecialchars($row['NOM_emp']) ?></td>
                        <td><?= htmlspecialchars($row['TEL_emp']) ?></td>
                        <td><?= htmlspecialchars($row['ADR_emp']) ?></td>
                        <td>
                            <button class="btn-select" onclick="selectionner(this)">Sélectionner</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- FORMULAIRES -->
        <div class="formulaire">

            <!-- Formulaire AJOUTER -->
            <!-- FIX: action was "admin.php", changed to "employer.php" -->
            <form action="employer.php" method="post" id="form-ajouter">
                <h2 class="form-title">Ajouter</h2>

                <label for="ncin_add">NCIN :</label>
                <input type="text" name="NCIN_emp" id="ncin_add" placeholder="201031054745">

                <label for="nom_add">Nom :</label>
                <input type="text" name="NOM_emp" id="nom_add" placeholder="Rakoto">

                <label for="tel_add">Téléphone :</label>
                <input type="text" name="TEL_emp" id="tel_add" placeholder="0341234567">

                <label for="adr_add">Adresse :</label>
                <select name="ADR_emp" id="adr_add">
                    <option value="antananarivo">Antananarivo</option>
                    <option value="toamasina">Toamasina</option>
                    <option value="mahajanga">Mahajanga</option>
                    <option value="fianarantsoa">Fianarantsoa</option>
                    <option value="toliara">Toliara</option>
                    <option value="diego">Diego</option>
                </select>

                <label for="mdp_add">Mot de passe :</label>
                <input type="password" name="motdepasse" id="mdp_add" placeholder="••••••••">

                <button type="submit">Ajouter</button>
            </form>

            <!-- Formulaire MODIFIER / SUPPRIMER -->
            <!-- FIX: action was "admin.php", changed to "employer.php" -->
            <form action="employer.php" method="get" id="form-modifier">
                <h2 class="form-title">Modifier / Supprimer</h2>

                <input type="hidden" name="nemp" id="nemp" value="">

                <label for="ncin_mod">NCIN :</label>
                <input type="text" name="NCIN_emp" id="ncin_mod">

                <label for="nom_mod">Nom :</label>
                <input type="text" name="NOM_emp" id="nom_mod">

                <label for="tel_mod">Téléphone :</label>
                <input type="text" name="TEL_emp" id="tel_mod">

                <label for="adr_mod">Adresse :</label>
                <select name="ADR_emp" id="adr_mod">
                    <option value="antananarivo">Antananarivo</option>
                    <option value="toamasina">Toamasina</option>
                    <option value="mahajanga">Mahajanga</option>
                    <option value="fianarantsoa">Fianarantsoa</option>
                    <option value="toliara">Toliara</option>
                    <option value="diego">Diego</option>
                </select>

                <label for="mdp_mod">Nouveau mot de passe :</label>
                <input type="password" name="motdepasse" id="mdp_mod" placeholder="Laisser vide = inchangé">

                <p id="hint-selection">&larr; Sélectionnez une ligne dans le tableau.</p>

                <div class="updet">
                    <button type="submit" name="modifier"  id="btn-modifier"  disabled>Modifier</button>
                    <button type="submit" name="supprimer" id="btn-supprimer" disabled>Supprimer</button>
                </div>
            </form>

        </div>
    </section>

    <script src="emp.js"></script>
</body>
</html>
