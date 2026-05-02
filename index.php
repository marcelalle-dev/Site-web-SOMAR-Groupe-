<?php
session_start();
// Si déjà vérifié, rediriger
if(isset($_SESSION['visitor_verified']) && $_SESSION['visitor_verified'] === true) {
    header("Location: site/index.html");
    exit();
}
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOMAR Groupe - Accès Obligatoire</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
       
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #b5bdc5 0%, #3498db 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
       
        .access-container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.5s ease;
        }
       
        .access-header {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
        }
       
        .access-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
       
        .access-header p {
            opacity: 0.9;
            font-size: 16px;
        }
       
        .access-body {
            padding: 30px;
        }
       
        .error-message {
            background: #ffeaea;
            color: #e74c3c;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #e74c3c;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.3s;
        }
       
        .form-group {
            margin-bottom: 20px;
        }
       
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
        }
       
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            font-family: inherit;
        }
       
        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
       
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232c3e50' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 15px;
            padding-right: 40px;
        }
       
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
       
        .required {
            color: #e74c3c;
        }
       
        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
       
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
       
        .privacy-note {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 13px;
            color: #7f8c8d;
            text-align: center;
            border: 1px solid #e9ecef;
        }
       
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
       
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
       
        @media (max-width: 576px) {
            .access-container {
                border-radius: 10px;
            }
           
            .access-header {
                padding: 25px 20px;
            }
           
            .access-header h1 {
                font-size: 24px;
            }
           
            .access-body {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="access-container">
        <div class="access-header">
            <h1><i class="fas fa-graduation-cap"></i> SOMAR Groupe</h1>
            <p>Plateforme Éducative d'Excellence</p>
        </div>
       
        <div class="access-body">
            <?php if ($error === 'empty'): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Tous les champs obligatoires doivent être remplis.</span>
                </div>
            <?php elseif ($error === 'email'): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Veuillez entrer une adresse email valide.</span>
                </div>
            <?php endif; ?>
           
            <form action="verify.php" method="POST" id="accessForm">
                <div class="form-group">
                    <label for="nom">Nom complet <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" class="form-control"
                           placeholder="Ex: Jean Dupont" required
                           value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>">
                </div>
               
                <div class="form-group">
                    <label for="email">Adresse email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" class="form-control"
                           placeholder="exemple@email.com" required
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
               
                <div class="form-group">
                    <label for="telephone">Numéro de téléphone <span class="required">*</span></label>
                    <input type="tel" id="telephone" name="telephone" class="form-control"
                           placeholder="Ex: +229 XX XX XX XX" required
                           value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>">
                </div>
               
                <div class="form-group">
                    <label for="objectif">Pourquoi visitez-vous notre site ? <span class="required">*</span></label>
                    <select id="objectif" name="objectif" class="form-control" required>
                        <option value="">-- Sélectionnez votre objectif --</option>
                        <option value="cours" <?php echo (isset($_POST['objectif']) && $_POST['objectif'] == 'cours') ? 'selected' : ''; ?>>Consulter des cours en ligne</option>
                        <option value="epreuves" <?php echo (isset($_POST['objectif']) && $_POST['objectif'] == 'epreuves') ? 'selected' : ''; ?>>Télécharger des épreuves</option>
                        <option value="info" <?php echo (isset($_POST['objectif']) && $_POST['objectif'] == 'info') ? 'selected' : ''; ?>>Obtenir des informations</option>
                        <option value="orientation" <?php echo (isset($_POST['objectif']) && $_POST['objectif'] == 'orientation') ? 'selected' : ''; ?>>Demander une orientation</option>
                        <option value="tutorat" <?php echo (isset($_POST['objectif']) && $_POST['objectif'] == 'tutorat') ? 'selected' : ''; ?>>Prendre un tutorat</option>
                        <option value="autre" <?php echo (isset($_POST['objectif']) && $_POST['objectif'] == 'autre') ? 'selected' : ''; ?>>Autre raison</option>
                    </select>
                </div>
               
                <div class="form-group">
                    <label for="niveau">Votre niveau d'études</label>
                    <select id="niveau" name="niveau" class="form-control">
                        <option value="">-- Sélectionnez votre niveau --</option>
                        <option value="college" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] == 'college') ? 'selected' : ''; ?>>Collège (BEPC)</option>
                        <option value="lycee" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] == 'lycee') ? 'selected' : ''; ?>>Lycée (BAC)</option>
                        <option value="universite" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] == 'universite') ? 'selected' : ''; ?>>Université</option>
                        <option value="professionnel" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] == 'professionnel') ? 'selected' : ''; ?>>Professionnel</option>
                        <option value="autre" <?php echo (isset($_POST['niveau']) && $_POST['niveau'] == 'autre') ? 'selected' : ''; ?>>Autre</option>
                    </select>
                </div>
               
                <div class="form-group">
                    <label for="message">Message ou commentaire (optionnel)</label>
                    <textarea id="message" name="message" class="form-control"
                              placeholder="Votre message, suggestion ou question..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>
               
                <button type="submit" class="submit-btn">
                    <i class="fas fa-unlock-alt"></i> Accéder à la plateforme
                </button>
               
                <div class="privacy-note">
                    <i class="fas fa-shield-alt"></i>
                    Vos informations sont sécurisées et utilisées uniquement pour améliorer nos services.
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('accessForm');
           
            // Validation en temps réel
            form.addEventListener('submit', function(e) {
                let isValid = true;
               
                // Réinitialiser les styles
                document.querySelectorAll('.form-control').forEach(input => {
                    input.style.borderColor = '#e0e0e0';
                });
               
                // Vérifier les champs requis
                const requiredFields = form.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = '#e74c3c';
                        isValid = false;
                       
                        // Animation de secousse
                        field.animate([
                            { transform: 'translateX(0)' },
                            { transform: 'translateX(-5px)' },
                            { transform: 'translateX(5px)' },
                            { transform: 'translateX(0)' }
                        ], {
                            duration: 300
                        });
                    }
                });
               
                // Validation email
                const emailField = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailField.value && !emailRegex.test(emailField.value)) {
                    emailField.style.borderColor = '#e74c3c';
                    isValid = false;
                }
               
                // Validation téléphone (au moins 8 chiffres)
                const phoneField = document.getElementById('telephone');
                const phoneRegex = /[\d\s\+\(\)\-]{8,}/;
                if (phoneField.value && !phoneRegex.test(phoneField.value.replace(/\D/g, ''))) {
                    phoneField.style.borderColor = '#e74c3c';
                    isValid = false;
                }
               
                if (!isValid) {
                    e.preventDefault();
                   
                    // Afficher un message d'erreur
                    let errorMsg = document.querySelector('.error-message');
                    if (!errorMsg) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.innerHTML = '<i class="fas fa-exclamation-circle"></i><span>Veuillez corriger les erreurs dans le formulaire.</span>';
                        form.parentNode.insertBefore(errorMsg, form);
                    }
                   
                    // Scroll vers l'erreur
                    errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
           
            // Animation d'entrée des champs
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach((group, index) => {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
               
                setTimeout(() => {
                    group.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });
        });
    </script>
</body>
</html>
