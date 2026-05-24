<?php
//  Connexion PDO 
require "connexion.php";
$message = '';

//  AJOUTER (POST) 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marque    = trim($_POST['marque']    ?? '');
    $libelle   = trim($_POST['libelle']   ?? '');
    $categorie = trim($_POST['categorie'] ?? '');
    $etat      = isset($_POST['etat']) ? (int)$_POST['etat'] : null;
    $prix      = trim($_POST['prix']      ?? '');

    if ($marque && $libelle && $categorie && $prix !== '' && $etat !== null) {
        $cmd = $pdo->prepare(
            "INSERT INTO APPAREIL (marque, LIB_app, categorie, ETAT_app, prix)
            VALUES (?, ?, ?, ?, ?)"
        );
        $cmd->execute([$marque, $libelle, $categorie, $etat, $prix]);
        $message = '<p class="msg success">&#10004; Appareil ajouté avec succès.</p>';
    } else {
        $message = '<p class="msg error">&#10008; Veuillez remplir tous les champs.</p>';
    }
}

//  MODIFIER (GET + modifier) 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['modifier'], $_GET['napp'])) {
    $id        = (int)$_GET['napp'];
    $marque    = trim($_GET['marque']    ?? '');
    $libelle   = trim($_GET['libelle']   ?? '');
    $categorie = trim($_GET['categorie'] ?? '');
    $etat      = isset($_GET['etat']) ? (int)$_GET['etat'] : null;
    $prix      = trim($_GET['prix']      ?? '');

    if ($id && $marque && $libelle && $categorie && $prix !== '' && $etat !== null) {
        $cmd = $pdo->prepare(
            "UPDATE APPAREIL
            SET marque=?, LIB_app=?, categorie=?, ETAT_app=?, prix=?
            WHERE CODE_app=?"
        );
        $cmd->execute([$marque, $libelle, $categorie, $etat, $prix, $id]);
        $message = '<p class="msg success">&#10004; Appareil modifié avec succès.</p>';
    } else {
        $message = '<p class="msg error">&#10008; Veuillez remplir tous les champs.</p>';
    }
}

//  SUPPRIMER (GET + supprimer) 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['supprimer'], $_GET['napp'])) {
    $id = (int)$_GET['napp'];
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM APPAREIL WHERE CODE_app=?");
        $stmt->execute([$id]);
        $message = '<p class="msg success">&#10004; Appareil supprimé avec succès.</p>';
    }
}

// LECTURE de tous les appareils 
$appareils = $pdo->query(
    "SELECT CODE_app, marque, LIB_app, categorie, ETAT_app, prix
    FROM   APPAREIL
    ORDER BY CODE_app ASC"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appareil</title>
    <script src="node_modules/chart.js/dist/chart.umd.js"></script>
    <link rel="stylesheet" href="appstyle.css">
</head>

<body>
    <header id="entete">
        <a href="index.html">&#10096;</a>
        <h1>APPAREIL</h1>
    </header>

    <?= $message ?>
    <div class="stat">
        <div class="total">
            <label for="number_app">nombre total <br> appareil</label>
            <label for="nbapp">
                <?php
                $cmd = $pdo->prepare(
                    "SELECT COUNT(*) FROM APPAREIL"
                );
                $cmd->execute();
                echo $cmd->fetchColumn();
                ?>
            </label>
        </div>
        <div class="total_bon">
            <label for="number_cli">nombre total <br> appareil en bon etat</label>
            <label for="nbapp">
                <?php
                $cmd = $pdo->prepare(
                    "SELECT COUNT(*) FROM APPAREIL WHERE ETAT_app>0"
                );
                $cmd->execute();
                echo $cmd->fetchColumn();
                ?>
            </label>
        </div>
        <div class="total_mauvais">
            <label for="number_cli">nombre total <br> appareil à reparer </label>
            <label for="nbapp">
                <?php
                $cmd = $pdo->prepare(
                    "SELECT COUNT(*) FROM APPAREIL WHERE ETAT_app < 1"
                );
                $cmd->execute();
                echo $cmd->fetchColumn();
                ?>
            </label>
        </div>
        <div class="graphe">
            <?php
            $cmd = $pdo->query(
                "SELECT categorie,COUNT(*) AS total
                    FROM APPAREIL
                    GROUP BY categorie
                    ORDER BY total DESC"
            );
            $rows = $cmd->fetchAll(PDO::FETCH_ASSOC);

            $labels = array_column($rows, 'categorie');
            $data = array_column($rows, 'total');
            $json_label = json_encode($labels, JSON_UNESCAPED_UNICODE);
            $json_data = json_encode($data);
            ?>
            <canvas id="grapheapp"></canvas>
            <script>
                const labels = <?= $json_label ?>;
                const data = <?= $json_data ?>;
                const con = document.getElementById('grapheapp');
                const chart = new Chart(con, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'appareil par categorie',
                            data: data,

                            backgroundColor: [
                                'rgb(231, 9, 146)',
                                'rgb(3, 1, 167)',
                                'rgba(231, 16, 9, 0.66)',
                            ],

                            borderColor:'black'
                        }]
                    },
                    options:{
                        responsive:true,
                        maintainAspectRadio:true,
                        plugins:{
                            legend:{
                                position:bottom
                            },
                            title:{
                                display:true,
                                text:'nombre par categorie'
                            }
                        }
                    }
                })
                chart.update();
            </script>
        </div>
    </div>

    <section>
        <!--  TABLEAU D'AFFICHAGE  -->
        <div class="affichage">
            <div class="affichage-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>CODE</th>
                            <th>Marque</th>
                            <th>Libellé</th>
                            <th>Catégorie</th>
                            <th>État</th>
                            <th>Prix</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appareils)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center;color:#888;padding:20px;">
                                    Aucun appareil enregistré.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($appareils as $row): ?>
                                <tr class="ligne-appareil"
                                    data-id="<?= $row['CODE_app'] ?>"
                                    data-marque="<?= htmlspecialchars((string)($row['marque']    ?? ''), ENT_QUOTES) ?>"
                                    data-libelle="<?= htmlspecialchars((string)($row['LIB_app']  ?? ''), ENT_QUOTES) ?>"
                                    data-categorie="<?= htmlspecialchars((string)($row['categorie'] ?? ''), ENT_QUOTES) ?>"
                                    data-etat="<?= (int)($row['ETAT_app'] ?? 0) ?>"
                                    data-prix="<?= htmlspecialchars((string)($row['prix'] ?? ''), ENT_QUOTES) ?>">
                                    <td><?= $row['CODE_app'] ?></td>
                                    <td><?= htmlspecialchars((string)($row['marque']    ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($row['LIB_app']  ?? '')) ?></td>
                                    <td><?= htmlspecialchars((string)($row['categorie'] ?? '')) ?></td>
                                    <td><?= ($row['ETAT_app'] ?? 0) ? 'Bon' : 'Mauvais' ?></td>
                                    <td><?= htmlspecialchars((string)($row['prix'] ?? '')) ?> Ar</td>
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

        <!--  FORMULAIRES  -->
        <div class="formulaire">

            <!-- Formulaire AJOUTER -->
            <form action="appareil.php" method="post" id="form-ajouter">
                <h2 class="form-title">Ajouter</h2>

                <label for="marque_add">Marque :</label>
                <input type="text" name="marque" id="marque_add" placeholder="Sony">

                <label for="libelle_add">Libellé :</label>
                <input type="text" name="libelle" id="libelle_add" placeholder="Appareil photo">

                <label for="categorie_add">Catégorie :</label>
                <select name="categorie" id="categorie_add">
                    <option value="audiovisuel">Audiovisuel</option>
                    <option value="informatique">Informatique</option>
                    <option value="divertissement">Divertissement</option>
                </select>

                <label>Etat :</label>
                <div class="etat">
                    <input type="radio" name="etat" value="1" id="good_add">
                    <label for="good_add">Bon</label>
                    <input type="radio" name="etat" value="0" id="bad_add">
                    <label for="bad_add">Mauvais</label>
                </div>

                <label for="prix_add">Prix (Ar) :</label>
                <input type="text" name="prix" id="prix_add" placeholder="15000">

                <button type="submit">Ajouter</button>
            </form>

            <!-- Formulaire MODIFIER / SUPPRIMER -->
            <form action="appareil.php" method="get" id="form-modifier">
                <h2 class="form-title">Modifier / Supprimer</h2>

                <!-- CODE_app cache de l'appareil selectionne -->
                <input type="hidden" name="napp" id="napp" value="">

                <label for="marque_mod">Marque :</label>
                <input type="text" name="marque" id="marque_mod">

                <label for="libelle_mod">Libelle :</label>
                <input type="text" name="libelle" id="libelle_mod">

                <label for="categorie_mod">Categorie :</label>
                <select name="categorie" id="categorie_mod">
                    <option value="audiovisuel">Audiovisuel</option>
                    <option value="informatique">Informatique</option>
                    <option value="divertissement">Divertissement</option>
                </select>

                <label>etat :</label>
                <div class="etat">
                    <input type="radio" name="etat" value="1" id="good_mod">
                    <label for="good_mod">Bon</label>
                    <input type="radio" name="etat" value="0" id="bad_mod">
                    <label for="bad_mod">Mauvais</label>
                </div>

                <label for="prix_mod">Prix (Ar) :</label>
                <input type="text" name="prix" id="prix_mod">

                <p id="hint-selection">
                    &larr; Selectionnez une ligne dans le tableau.
                </p>

                <div class="updet">
                    <button type="submit" name="modifier" id="btn-modifier" disabled>Modifier</button>
                    <button type="submit" name="supprimer" id="btn-supprimer" disabled>Supprimer</button>
                </div>
            </form>

        </div>
    </section>

    <script src="app.js">
    </script>
</body>

</html>