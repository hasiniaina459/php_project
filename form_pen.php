<?php
require "connexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id         = (int)trim($_POST['nemp']       ?? 0);
    $nom        = trim($_POST['nomemp']           ?? '');
    $motdepasse = trim($_POST['motdepasse']       ?? '');

    if ($id && $nom && $motdepasse) {
        $cmd = $pdo->prepare(
            "SELECT N_emp FROM EMPLOYER
            WHERE N_emp = ? AND NOM_emp = ? AND motdepasse = ?"
        );
        $cmd->execute([$id, $nom, $motdepasse]);
        $employe = $cmd->fetch();

        if ($employe) {
            // Connexion réussie → redirection vers pen.php
            session_start();
            $_SESSION['emp_id'] = $id;
            header("Location: pen.php");
            exit;
        } else {
            $erreur = "Numéro, nom ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Pénalité</title>
    <link rel="stylesheet" href="pen.css">
</head>

<body>
    <form method="POST" action="form_pen.php">
        <h1>Connexion pénalité</h1>

        <?php if (!empty($erreur)): ?>
            <p style="color:red; text-align:center;"><?= htmlspecialchars($erreur) ?></p>
        <?php endif; ?>

        <label for="nemp">Numéro :</label>
        <input type="text" name="nemp" id="nemp">

        <label for="nomemp">Nom :</label>
        <input type="text" name="nomemp" id="nomemp">

        <label for="motdepasse">Mot de passe :</label>
        <input type="password" name="motdepasse" id="motdepasse" placeholder="********">

        <button type="submit">OK</button>
    </form>
</body>

</html>