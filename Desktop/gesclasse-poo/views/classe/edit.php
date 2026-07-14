<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une Classe</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f6f9; color: #333; }
        h1 { color: #2c3e50; }
        .form-container { background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); max-width: 500px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { display: inline-block; padding: 10px 15px; text-decoration: none; border-radius: 4px; color: white; font-weight: bold; border: none; cursor: pointer; }
        .btn-submit { background-color: #3498db; }
        .btn-back { background-color: #95a5a6; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; text-decoration: none; color: #34495e; font-weight: bold; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="<?= BASE_URL ?>/classes">Classes</a> | 
        <a href="<?= BASE_URL ?>/filieres">Filières</a> | 
        <a href="<?= BASE_URL ?>/niveaux">Niveaux</a>
    </div>

    <h1>Modifier la Classe</h1>

    <div class="form-container">
        <form action="<?= BASE_URL ?>/classes/edit?id=<?= $classe['id'] ?>" method="POST">
            <div class="form-group">
                <label for="nom">Nom de la classe</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($classe['nom']) ?>" required>
            </div>

            <div class="form-group">
                <label for="filiere_id">Filière</label>
                <select id="filiere_id" name="filiere_id" required>
                    <option value="">-- Sélectionner une filière --</option>
                    <?php foreach ($filieres as $filiere): ?>
                        <option value="<?= $filiere['id'] ?>" <?= $filiere['id'] == $classe['filiere_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($filiere['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="niveau_id">Niveau</label>
                <select id="niveau_id" name="niveau_id" required>
                    <option value="">-- Sélectionner un niveau --</option>
                    <?php foreach ($niveaux as $niveau): ?>
                        <option value="<?= $niveau['id'] ?>" <?= $niveau['id'] == $classe['niveau_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($niveau['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-submit">Modifier</button>
            <a href="<?= BASE_URL ?>/classes" class="btn btn-back">Retour</a>
        </form>
    </div>
</body>
</html>
