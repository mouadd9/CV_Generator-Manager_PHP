<?php
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']);
require_once 'IHM/components/navbar.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire CV</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="http://localhost/cvForm/IHM/public/style.css">
</head>
<body>
    <?php displayNavbar(); ?>  
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="?action=submit-cv" enctype="multipart/form-data" class="cv-form">
        <h1>Créer votre CV</h1>

        <!-- Informations personnelles -->
        <div class="form-section">
            <h2>Informations personnelles</h2>
            <div class="form-group">
                <label for="photo" class="required">Photo</label>
                <input type="file" id="photo" name="photo" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="prenom" class="required">Prénom</label>
                <input type="text" id="prenom" name="prenom" required placeholder="Votre prénom">
            </div>
            <div class="form-group">
                <label for="nom" class="required">Nom de famille</label>
                <input type="text" id="nom" name="nom" required placeholder="Votre nom">
            </div>
            <div class="form-group">
                <label for="titre" class="required">Titre</label>
                <input type="text" id="titre" name="titre" required placeholder="Ex: Développeur Web, Designer UX, etc.">
            </div>
            <div class="form-group">
                <label for="email" class="required">Adresse e-mail</label>
                <input type="email" id="email" name="email" required placeholder="votre.email@exemple.com">
            </div>
            <div class="form-group">
                <label for="telephone" class="required">Numéro de téléphone</label>
                <input type="tel" id="telephone" name="telephone" required placeholder="06 12 34 56 78">
            </div>
            <div class="form-group">
                <label for="adresse" class="required">Adresse</label>
                <input type="text" id="adresse" name="adresse" required placeholder="Votre adresse">
            </div>
            <div class="form-group">
                <label for="code_postal" class="required">Code postal</label>
                <input type="text" id="code_postal" name="code_postal" required placeholder="Votre code postal">
            </div>
            <div class="form-group">
                <label for="ville" class="required">Ville</label>
                <input type="text" id="ville" name="ville" required placeholder="Votre ville">
            </div>
        </div>

        <!-- Profil -->
        <div class="form-section">
            <h2>Profil</h2>
            <div class="form-group">
                <label for="description" class="required">Description</label>
                <textarea id="description" name="description" required placeholder="Présentez-vous en quelques lignes..."></textarea>
            </div>
        </div>

        <!-- Formation -->
        <div class="form-section" id="formations-section">
            <h2>Formation</h2>
            <div id="formations-container">
                <div class="formation-entry">
                    <div class="form-group">
                        <label class="required">Formation</label>
                        <input type="text" name="formations[0][titre]" required placeholder="Nom du diplôme ou de la formation">
                    </div>
                    <div class="form-group">
                        <label class="required">Établissement</label>
                        <input type="text" name="formations[0][etablissement]" required placeholder="Nom de l'établissement">
                    </div>
                    <div class="form-group">
                        <label class="required">Ville</label>
                        <input type="text" name="formations[0][ville]" required placeholder="Ville de l'établissement">
                    </div>
                    <div class="form-group">
                        <label class="required">Date de début</label>
                        <div class="date-selector">
                            <div class="month-selector">
                                <label>Mois</label>
                                <input type="range" name="formations[0][mois_debut]" min="1" max="12" value="1" class="month-slider">
                                <span class="month-value">Janvier</span>
                            </div>
                            <div class="year-selector">
                                <label>Année</label>
                                <input type="range" name="formations[0][annee_debut]" min="1980" max="2025" value="2020" class="year-slider">
                                <span class="year-value">2020</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required">Date de fin</label>
                        <div class="date-selector">
                            <div class="month-selector">
                                <label>Mois</label>
                                <input type="range" name="formations[0][mois_fin]" min="1" max="12" value="6" class="month-slider">
                                <span class="month-value">Juin</span>
                            </div>
                            <div class="year-selector">
                                <label>Année</label>
                                <input type="range" name="formations[0][annee_fin]" min="1980" max="2025" value="2023" class="year-slider">
                                <span class="year-value">2023</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required">Description</label>
                        <textarea name="formations[0][description]" required placeholder="Décrivez votre formation, les matières principales, etc."></textarea>
                    </div>
                    <button type="button" class="btn-remove" onclick="removeEntry(this)">Supprimer cette formation</button>
                </div>
            </div>
            <button type="button" class="btn-add" onclick="addFormation()">Ajouter une formation</button>
        </div>

        <!-- Expérience professionnelle -->
        <div class="form-section" id="experiences-section">
            <h2>Expérience professionnelle</h2>
            <div id="experiences-container">
                <div class="experience-entry">
                    <div class="form-group">
                        <label class="required">Poste</label>
                        <input type="text" name="experiences[0][poste]" required placeholder="Intitulé du poste">
                    </div>
                    <div class="form-group">
                        <label class="required">Employeur</label>
                        <input type="text" name="experiences[0][employeur]" required placeholder="Nom de l'entreprise">
                    </div>
                    <div class="form-group">
                        <label class="required">Ville</label>
                        <input type="text" name="experiences[0][ville]" required placeholder="Ville de l'entreprise">
                    </div>
                    <div class="form-group">
                        <label class="required">Date de début</label>
                        <div class="date-selector">
                            <div class="month-selector">
                                <label>Mois</label>
                                <input type="range" name="experiences[0][mois_debut]" min="1" max="12" value="1" class="month-slider">
                                <span class="month-value">Janvier</span>
                            </div>
                            <div class="year-selector">
                                <label>Année</label>
                                <input type="range" name="experiences[0][annee_debut]" min="1980" max="2025" value="2020" class="year-slider">
                                <span class="year-value">2020</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required">Date de fin</label>
                        <div class="date-selector">
                            <div class="month-selector">
                                <label>Mois</label>
                                <input type="range" name="experiences[0][mois_fin]" min="1" max="12" value="12" class="month-slider">
                                <span class="month-value">Décembre</span>
                            </div>
                            <div class="year-selector">
                                <label>Année</label>
                                <input type="range" name="experiences[0][annee_fin]" min="1980" max="2025" value="2023" class="year-slider">
                                <span class="year-value">2023</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required">Description</label>
                        <textarea name="experiences[0][description]" required placeholder="Décrivez vos responsabilités, réalisations, etc."></textarea>
                    </div>
                    <button type="button" class="btn-remove" onclick="removeEntry(this)">Supprimer cette expérience</button>
                </div>
            </div>
            <button type="button" class="btn-add" onclick="addExperience()">Ajouter une expérience</button>
        </div>

        <!-- Compétences -->
        <div class="form-section" id="competences-section">
            <h2>Compétences</h2>
            <div id="competences-container">
                <div class="competence-entry">
                    <div class="form-group">
                        <label class="required">Compétence</label>
                        <input type="text" name="competences[0][nom]" required placeholder="Nom de la compétence">
                        <div class="skill-level">
                            <label>Niveau</label>
                            <input type="range" name="competences[0][niveau]" min="0" max="100" step="10" value="50" class="skill-slider">
                            <span class="skill-value">50%</span>
                        </div>
                    </div>
                    <button type="button" class="btn-remove" onclick="removeEntry(this)">Supprimer cette compétence</button>
                </div>
            </div>
            <button type="button" class="btn-add" onclick="addCompetence()">Ajouter une compétence</button>
        </div>

        <!-- Langues -->
        <div class="form-section" id="langues-section">
            <h2>Langues</h2>
            <div id="langues-container">
                <div class="langue-entry">
                    <div class="form-group">
                        <label class="required">Langue</label>
                        <input type="text" name="langues[0][nom]" required placeholder="Nom de la langue">
                        <div class="skill-level">
                            <label>Niveau</label>
                            <input type="range" name="langues[0][niveau]" min="0" max="100" step="10" value="50" class="skill-slider">
                            <span class="skill-value">50%</span>
                        </div>
                    </div>
                    <button type="button" class="btn-remove" onclick="removeEntry(this)">Supprimer cette langue</button>
                </div>
            </div>
            <button type="button" class="btn-add" onclick="addLangue()">Ajouter une langue</button>
        </div>

        <!-- Submit button -->
        <div class="form-actions">
            <button type="submit" class="btn-submit">Générer mon CV</button>
        </div>
    </form>

    <script>
        // Utility functions
        function updateMonthValue(slider, valueSpan) {
            const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            valueSpan.textContent = months[slider.value - 1];
        }

        function updateYearValue(slider, valueSpan) {
            valueSpan.textContent = slider.value;
        }

        function updateSkillValue(slider, valueSpan) {
            valueSpan.textContent = slider.value + '%';
        }

        function setupSliders(container) {
            container.querySelectorAll('.month-slider').forEach(slider => {
                const valueSpan = slider.nextElementSibling;
                updateMonthValue(slider, valueSpan);
                slider.addEventListener('input', () => updateMonthValue(slider, valueSpan));
            });

            container.querySelectorAll('.year-slider').forEach(slider => {
                const valueSpan = slider.nextElementSibling;
                updateYearValue(slider, valueSpan);
                slider.addEventListener('input', () => updateYearValue(slider, valueSpan));
            });

            container.querySelectorAll('.skill-slider').forEach(slider => {
                const valueSpan = slider.nextElementSibling;
                updateSkillValue(slider, valueSpan);
                slider.addEventListener('input', () => updateSkillValue(slider, valueSpan));
            });
        }

        function removeEntry(button) {
            button.closest('[class$="-entry"]').remove();
        }

        function updateIndexes(container, type) {
            container.querySelectorAll(`[class$="-entry"]`).forEach((entry, index) => {
                entry.querySelectorAll('input, textarea').forEach(input => {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                });
            });
        }

        // Add new entries functions
        function addFormation() {
            const container = document.getElementById('formations-container');
            const template = container.querySelector('.formation-entry').cloneNode(true);
            const index = container.children.length;
            
            template.querySelectorAll('input, textarea').forEach(input => {
                input.value = '';
                input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
            });
            
            container.appendChild(template);
            setupSliders(template);
        }

        function addExperience() {
            const container = document.getElementById('experiences-container');
            const template = container.querySelector('.experience-entry').cloneNode(true);
            const index = container.children.length;
            
            template.querySelectorAll('input, textarea').forEach(input => {
                input.value = '';
                input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
            });
            
            container.appendChild(template);
            setupSliders(template);
        }

        function addCompetence() {
            const container = document.getElementById('competences-container');
            const template = container.querySelector('.competence-entry').cloneNode(true);
            const index = container.children.length;
            
            template.querySelectorAll('input').forEach(input => {
                input.value = input.type === 'range' ? '50' : '';
                input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
            });
            
            container.appendChild(template);
            setupSliders(template);
        }

        function addLangue() {
            const container = document.getElementById('langues-container');
            const template = container.querySelector('.langue-entry').cloneNode(true);
            const index = container.children.length;
            
            template.querySelectorAll('input').forEach(input => {
                input.value = input.type === 'range' ? '50' : '';
                input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
            });
            
            container.appendChild(template);
            setupSliders(template);
        }

        // Initialize all sliders
        document.addEventListener('DOMContentLoaded', function() {
            setupSliders(document);
        });
    </script>
</body>
</html> 