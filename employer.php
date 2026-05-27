<?php
session_start();
if (!isset($_SESSION['emp_id'])) {
    header("Location: form_pen.php"); // pas connecté → renvoi au login
    exit;
}
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

        $stmt = $pdo->prepare(
            "INSERT INTO EMPLOYER (NCIN_emp, NOM_emp, TEL_emp, ADR_emp, motdepasse)
            VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$ncin_emp, $nom_emp, $tel_emp, $adr_emp, $motdepasse]);
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
        <div class="tete">
            <div class="visible">
                <h1>ADMINISTRATEUR</h1>
                <a href="#" class="menu"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 5h18v2H3zm0 6h18v2H3zm0 6h18v2H3z"></path>
                    </svg></a>
            </div>
            <div id="pen">
                <a href="javascript:void(0)" onclick="closemenu(event)"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" viewBox="0 0 24 24">
                        <!--Boxicons v3.0.8 https://boxicons.com | License  https://docs.boxicons.com/free-->
                        <path d="M14.83 7.76 12 10.59 9.17 7.76 7.76 9.17 10.59 12l-2.83 2.83 1.41 1.41L12 13.41l2.83 2.83 1.41-1.41L13.41 12l2.83-2.83z"></path>
                        <path d="M12 2C9.33 2 6.82 3.04 4.93 4.93S2 9.33 2 12s1.04 5.18 2.93 7.07c1.95 1.95 4.51 2.92 7.07 2.92s5.12-.97 7.07-2.92S22 14.67 22 12s-1.04-5.18-2.93-7.07A9.93 9.93 0 0 0 12 2m5.66 15.66c-3.12 3.12-8.19 3.12-11.31 0-1.51-1.51-2.34-3.52-2.34-5.66s.83-4.15 2.34-5.66S9.87 4 12.01 4s4.15.83 5.66 2.34 2.34 3.52 2.34 5.66-.83 4.15-2.34 5.66Z"></path>
                    </svg></a>
                <a href="pen.php">ajouter pen</a>
            </div>
        </div>
    </header>

    <?= $message ?>
    <div class="stat">
        <div class="total">
            <label for="number_app">nombre total <br> d'administrateur </label>
            <label for="nbapp">
                <?php
                $cmd = $pdo->prepare(
                    "SELECT COUNT(*) FROM EMPLOYER"
                );
                $cmd->execute();
                echo $cmd->fetchColumn();
                ?>
            </label>
        </div>

    </div>
    <section>
        <!-- TABLEAU D'AFFICHAGE -->
        <div class="affichage">
            <div class="affichage-scroll">
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
                    <button type="submit" name="modifier" id="btn-modifier" disabled>Modifier</button>
                    <button type="submit" name="supprimer" id="btn-supprimer" disabled>Supprimer</button>
                </div>
            </form>

        </div>
    </section>

    <script src="emp.js"></script>
</body>

</html>