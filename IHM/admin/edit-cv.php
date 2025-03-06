<?php
require_once 'Traitement/AdminTraitement.php';
require_once 'BD/CV.php';
requireAdminAuth();

$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);

// Get CV data
if (!isset($_GET['id'])) {
    header('Location: ?page=admin/dashboard');
    exit;
}

$cv = getCVById($_GET['id']);
if (!$cv) {
    $_SESSION['error'] = 'CV not found';
    header('Location: ?page=admin/dashboard');
    exit;
}

// Convert stored JSON data back to arrays
$formations = json_decode($cv['education'], true);
$experiences = json_decode($cv['experience'], true);
$competences = json_decode($cv['skills'], true);
$langues = json_decode($cv['languages'], true);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier CV - <?php echo htmlspecialchars($cv['first_name'] . ' ' . $cv['last_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="http://localhost/cvForm/IHM/public/style.css">
</head>
<body>
    <?php require_once 'IHM/components/navbar.php'; displayNavbar(); ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="?action=update-cv" enctype="multipart/form-data" class="cv-form">
        <input type="hidden" name="id" value="<?php echo $cv['id']; ?>">
        
        <h1>Modifier le CV</h1>

        <!-- Informations personnelles -->
        <div class="form-section">
            <h2>Informations personnelles</h2>
            <div class="form-group">
                <label for="photo" class="required">Photo</label>
                <?php if ($cv['photo_path']): ?>
                    <div class="mb-2">
                        <img src="<?php echo htmlspecialchars($cv['photo_path']); ?>" alt="Current photo" style="max-width: 100px;">
                    </div>
                <?php endif; ?>
                <input type="file" id="photo" name="photo" accept="image/*">
                <input type="hidden" name="current_photo" value="<?php echo htmlspecialchars($cv['photo_path']); ?>">
            </div>
            <div class="form-group">
                <label for="prenom" class="required">Prénom</label>
                <input type="text" id="prenom" name="prenom" required value="<?php echo htmlspecialchars($cv['first_name']); ?>">
            </div>
            <div class="form-group">
                <label for="nom" class="required">Nom de famille</label>
                <input type="text" id="nom" name="nom" required value="<?php echo htmlspecialchars($cv['last_name']); ?>">
            </div>
            <div class="form-group">
                <label for="titre" class="required">Titre</label>
                <input type="text" id="titre" name="titre" required value="<?php echo htmlspecialchars($cv['job_title']); ?>">
            </div>
            <div class="form-group">
                <label for="email" class="required">Adresse e-mail</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($cv['email']); ?>">
            </div>
            <div class="form-group">
                <label for="telephone" class="required">Numéro de téléphone</label>
                <input type="tel" id="telephone" name="telephone" required value="<?php echo htmlspecialchars($cv['phone']); ?>">
            </div>
            <div class="form-group">
                <label for="adresse" class="required">Adresse</label>
                <input type="text" id="adresse" name="adresse" required value="<?php echo htmlspecialchars($cv['address']); ?>">
            </div>
            <div class="form-group">
                <label for="code_postal" class="required">Code postal</label>
                <input type="text" id="code_postal" name="code_postal" required value="<?php echo htmlspecialchars($cv['postal_code']); ?>">
            </div>
            <div class="form-group">
                <label for="ville" class="required">Ville</label>
                <input type="text" id="ville" name="ville" required value="<?php echo htmlspecialchars($cv['city']); ?>">
            </div>
        </div>

        <!-- Profil -->
        <div class="form-section">
            <h2>Profil</h2>
            <div class="form-group">
                <label for="description" class="required">Description</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($cv['description']); ?></textarea>
            </div>
        </div>

        <!-- Formation -->
        <div class="form-section" id="formations-section">
            <h2>Formation</h2>
            <div id="formations-container">
                <?php foreach ($formations as $index => $formation): ?>
                <div class="formation-entry">
                    <div class="form-group">
                        <label class="required">Formation</label>
                        <input type="text" name="formations[<?php echo $index; ?>][titre]" required value="<?php echo htmlspecialchars($formation['titre']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="required">Établissement</label>
                        <input type="text" name="formations[<?php echo $index; ?>][etablissement]" required value="<?php echo htmlspecialchars($formation['etablissement']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="required">Ville</label>
                        <input type="text" name="formations[<?php echo $index; ?>][ville]" required value="<?php echo htmlspecialchars($formation['ville']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="required">Date de début</label>
                        <div class="date-selector">
                            <div class="month-selector">
                                <label>Mois</label>
                                <input type="range" name="formations[<?php echo $index; ?>][mois_debut]" min="1" max="12" value="<?php echo $formation['mois_debut']; ?>" class="month-slider">
                                <span class="month-value"></span>
                            </div>
                            <div class="year-selector">
                                <label>Année</label>
                                <input type="range" name="formations[<?php echo $index; ?>][annee_debut]" min="1980" max="2025" value="<?php echo $formation['annee_debut']; ?>" class="year-slider">
                                <span class="year-value"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required">Date de fin</label>
                        <div class="date-selector">
                            <div class="month-selector">
                                <label>Mois</label>
                                <input type="range" name="formations[<?php echo $index; ?>][mois_fin]" min="1" max="12" value="<?php echo $formation['mois_fin']; ?>" class="month-slider">
                                <span class="month-value"></span>
                            </div>
                            <div class="year-selector">
                                <label>Année</label>
                                <input type="range" name="formations[<?php echo $index; ?>][annee_fin]" min="1980" max="2025" value="<?php echo $formation['annee_fin']; ?>" class="year-slider">
                                <span class="year-value"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required">Description</label>
                        <textarea name="formations[<?php echo $index; ?>][description]" required><?php echo htmlspecialchars($formation['description']); ?></textarea>
                    </div>
                    <button type="button" class="btn-remove" onclick="removeEntry(this)">Supprimer cette formation</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn-add" onclick="addFormation()">Ajouter une formation</button>
        </div>

        <!-- Expérience professionnelle -->
        <div class="form-section" id="experiences-section">
            <h2>Expérience professionnelle</h2>
            <div id="experiences-container">
                <?php foreach ($experiences as $index => $experience): ?>
                <div class="experience-entry">
                    <div class="form-group">
                        <label class="required">Poste</label>
                        <input type="text" name="experiences[<?php echo $index; ?>][poste]" required value="<?php echo htmlspecialchars($experience['poste']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="required">Employeur</label>
                        <input type="text" name="experiences[<?php echo $index; ?>][employeur]" required value="<?php echo htmlspecialchars($experience['employeur']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="required">Ville</label>
                        <input type="text" name="experiences[<?php echo $index; ?>][ville]" required value="<?php echo htmlspecialchars($experience['ville']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="required">Date de début</label>
                        <div class="date-selector">
                            <div class="month-selector">
                                <label>Mois</label>
                                <input type="range" name="experiences[<?php echo $index; ?>][mois_debut]" min="1" max="12" value="<?php echo $experience['mois_debut']; ?>" class="month-slider">
                                <span class="month-value"></span>
                            </div>
                            <div class="year-selector">
                                <label>Année</label>
                                <input type="range" name="experiences[<?php echo $index; ?>][annee_debut]" min="1980" max="2025" value="<?php echo $experience['annee_debut']; ?>" class="year-slider">
                                <span class="year-value"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required">Date de fin</label>
                        <div class="date-selector">
                            <div class="month-selector">
                                <label>Mois</label>
                                <input type="range" name="experiences[<?php echo $index; ?>][mois_fin]" min="1" max="12" value="<?php echo $experience['mois_fin']; ?>" class="month-slider">
                                <span class="month-value"></span>
                            </div>
                            <div class="year-selector">
                                <label>Année</label>
                                <input type="range" name="experiences[<?php echo $index; ?>][annee_fin]" min="1980" max="2025" value="<?php echo $experience['annee_fin']; ?>" class="year-slider">
                                <span class="year-value"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required">Description</label>
                        <textarea name="experiences[<?php echo $index; ?>][description]" required><?php echo htmlspecialchars($experience['description']); ?></textarea>
                    </div>
                    <button type="button" class="btn-remove" onclick="removeEntry(this)">Supprimer cette expérience</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn-add" onclick="addExperience()">Ajouter une expérience</button>
        </div>

        <!-- Compétences -->
        <div class="form-section" id="competences-section">
            <h2>Compétences</h2>
            <div id="competences-container">
                <?php foreach ($competences as $index => $competence): ?>
                <div class="competence-entry">
                    <div class="form-group">
                        <label class="required">Compétence</label>
                        <input type="text" name="competences[<?php echo $index; ?>][nom]" required value="<?php echo htmlspecialchars($competence['nom']); ?>">
                        <div class="skill-level">
                            <label>Niveau</label>
                            <input type="range" name="competences[<?php echo $index; ?>][niveau]" min="0" max="100" step="10" value="<?php echo $competence['niveau']; ?>" class="skill-slider">
                            <span class="skill-value"></span>
                        </div>
                    </div>
                    <button type="button" class="btn-remove" onclick="removeEntry(this)">Supprimer cette compétence</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn-add" onclick="addCompetence()">Ajouter une compétence</button>
        </div>

        <!-- Langues -->
        <div class="form-section" id="langues-section">
            <h2>Langues</h2>
            <div id="langues-container">
                <?php foreach ($langues as $index => $langue): ?>
                <div class="langue-entry">
                    <div class="form-group">
                        <label class="required">Langue</label>
                        <input type="text" name="langues[<?php echo $index; ?>][nom]" required value="<?php echo htmlspecialchars($langue['nom']); ?>">
                        <div class="skill-level">
                            <label>Niveau</label>
                            <input type="range" name="langues[<?php echo $index; ?>][niveau]" min="0" max="100" step="10" value="<?php echo $langue['niveau']; ?>" class="skill-slider">
                            <span class="skill-value"></span>
                        </div>
                    </div>
                    <button type="button" class="btn-remove" onclick="removeEntry(this)">Supprimer cette langue</button>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn-add" onclick="addLangue()">Ajouter une langue</button>
        </div>

        <!-- Submit button -->
        <div class="form-actions">
            <button type="submit" class="btn-submit">Mettre à jour le CV</button>
        </div>
    </form>

    <script>
        // All the existing JavaScript from cv-form.php remains the same
    </script>
</body>
</html> 