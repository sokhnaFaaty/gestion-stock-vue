<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Classes</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f6f9; color: #333; }
        h1 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #34495e; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .btn { display: inline-block; padding: 8px 12px; text-decoration: none; border-radius: 4px; color: white; font-weight: bold; }
        .btn-add { background-color: #2ecc71; margin-bottom: 15px; }
        .btn-edit { background-color: #3498db; margin-right: 5px; }
        .btn-delete { background-color: #e74c3c; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; text-decoration: none; color: #34495e; font-weight: bold; }
        .nav a:hover { color: #2ecc71; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="<?= BASE_URL ?>/classes">Classes</a> | 
        <a href="<?= BASE_URL ?>/filieres">Filières</a> | 
        <a href="<?= BASE_URL ?>/niveaux">Niveaux</a>
    </div>

    <h1>Gestion des Classes</h1>
    <a href="<?= BASE_URL ?>/classes/create" class="btn btn-add">Ajouter une Classe</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Filière ID</th>
                <th>Niveau ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($classes)): ?>
                <?php foreach ($classes as $classe): ?>
                    <tr>
                        <td><?= htmlspecialchars($classe['id']) ?></td>
                        <td><?= htmlspecialchars($classe['nom']) ?></td>
                        <td><?= htmlspecialchars($classe['filiere_id']) ?></td>
                        <td><?= htmlspecialchars($classe['niveau_id']) ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/classes/edit?id=<?= $classe['id'] ?>" class="btn btn-edit">Modifier</a>
                            <a href="<?= BASE_URL ?>/classes/delete?id=<?= $classe['id'] ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette classe ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; color: #7f8c8d;">Aucune classe enregistrée.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
