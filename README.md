# 📚 SOMAR Groupe – Plateforme Éducative d'Excellence

## 📋 Description du projet

**SOMAR Groupe** est une plateforme éducative innovante conçue pour révolutionner l'apprentissage. Elle offre une solution d'apprentissage complète combinant des **cours en ligne** et des **épreuves**, adaptée aux besoins des apprenants bénéficiant d'un système d'accès sécurisé.

**Fondateur** : Marcel ALLE (Développeur en formation à ESCAE-Bénin)

---

## 🎯 Objectif

Rendre l'éducation de qualité accessible à tous en proposant une plateforme d'apprentissage innovante et engageante qui :

- Libère le potentiel de chaque apprenant
- Accompagne les utilisateurs dans leur parcours scolaire
- Fournit des contenus pédagogiques adaptés
- Stimule la curiosité et favorise la réussite

---

## 🏢 À propos de SOMAR Groupe

- **Type** : Plateforme éducative en ligne
- **Fondateur** : Marcel ALLE
- **Localisation** : Bénin (ESCAE)
- **Email admin** : marcelalle3@gmail.com
- **Philosophie** : L'éducation est un droit fondamental

**Notre conviction** : Nous croyons que l'apprentissage doit être accessible, engageant et efficace pour tous, quels que soient l'âge, le niveau ou les circonstances.

---

## 💡 Mission & Vision

### Mission
Révolutionner l'apprentissage en proposant des solutions éducatives innovantes adaptées aux besoins des apprenants, avec un accent particulier sur l'accessibilité et l'efficacité pédagogique.

### Équipe
- Éducateurs passionnés
- Experts en pédagogie
- Développeurs engagés dans le développement de contenus et de méthodes d'apprentissage

---

## 📚 Services proposés

### 1. **Cours en ligne**
- Contenus éducatifs structurés et progressifs
- Apprentissage à son propre rythme
- Ressources multimédia

### 2. **Épreuves & Examens**
- Téléchargement d'épreuves
- Tests d'auto-évaluation
- Pratique pour les examens officiels

### 3. **Informations & Orientation**
- Conseils d'orientation scolaire
- Informations sur les cursus d'études
- Guidage pédagogique

### 4. **Tutorat**
- Accompagnement personnalisé
- Sessions de tutorat avec experts
- Support académique

---

## 🌐 Structure du site

### Architecture générale

```
SOMAR/
├── index.php              # Page d'accès (formulaire de vérification)
├── verify.php             # Traitement des données d'accès
├── admin.php              # Interface administrateur
├── logout_admin.php       # Déconnexion
├── download.php           # Gestion des téléchargements
├── logo.png               # Logo SOMAR
└── site/
    ├── index.html         # Page d'accueil principale
    ├── style.css          # Styles du site
    ├── script.js          # Scripts interactifs
    ├── logo.png           # Logo interne
    ├── eleve.webp         # Image d'étudiant
    ├── livre.webp         # Image de livre
    ├── cours/              # Dossier des cours
    ├── epreuves/           # Dossier des épreuves
    └── debug.html         # Page de débogage

```

### Sections du site

| Section | Contenu |
|---------|---------|
| **Accueil** | Bannière hero avec appel à l'action |
| **À propos** | Présentation de la mission et histoire |
| **Services** | Détail des services proposés |
| **Cours** | Ressources éducatives en ligne |
| **Épreuves** | Examens et tests à télécharger |
| **Contact** | Formulaire de contact |

---

## 🔐 Système d'accès sécurisé

### Processus d'accès

1. **Formulaire de vérification** : Les utilisateurs doivent remplir un formulaire avec leurs informations
   - Nom complet (requis)
   - Email (requis)
   - Téléphone (requis)
   - Objectif de visite (requis)
   - Niveau d'études (optionnel)
   - Message (optionnel)

2. **Validation** :
   - Validation email (format valide)
   - Validation téléphone (format international Bénin)
   - Anti-spam (délai minimum entre soumissions)
   - Tous les champs requis remplis

3. **Stockage des données** :
   - Données sauvegardées en JSON
   - Cookies de session pour accès ultérieurs
   - Durée de session : 30 jours

4. **Redirection** : Accès accordé à `site/index.html`

### Sécurité

- ✅ Validation côté serveur
- ✅ Nettoyage des données (trim, htmlspecialchars)
- ✅ Protection contre les attaques SQL
- ✅ Limitation de débit anti-spam
- ✅ Gestion sécurisée des cookies
- ✅ Vérification de session

---

## 🎨 Technologies utilisées

### Frontend
- **HTML5** : Structure sémantique
- **CSS3** : Design responsive et animations
- **JavaScript** : Interactivité et validation en temps réel
- **Font Awesome 6** : Icônes vectorielles

### Backend
- **PHP 7+** : Traitement des formulaires et sessions
- **Système de fichiers JSON** : Stockage des données visiteurs
- **Sessions PHP** : Gestion des accès

### Image & Média
- **WebP** : Images optimisées (eleve.webp, livre.webp)
- **PNG** : Logo et graphiques

---

## 🎯 Objectifs de visite disponibles

Le formulaire d'accès propose ces options :

1. **Consulter des cours en ligne**
2. **Télécharger des épreuves**
3. **Obtenir des informations**
4. **Demander une orientation**
5. **Prendre un tutorat**
6. **Autre raison**

---

## 🎓 Niveaux d'études supportés

- 🏫 Collège (BEPC)
- 🎒 Lycée (BAC)
- 🎓 Université
- 💼 Professionnel
- 📌 Autre

---

## 🚀 Installation & utilisation

### 1. **Configuration requise**

- PHP 7.0 ou supérieur
- Serveur web (Apache, Nginx, etc.)
- Support des sessions PHP
- Permissions d'écriture pour les dossiers `data` et `data/logs`

### 2. **Installation**

```bash
# Cloner ou télécharger le projet
git clone <url-du-repo>
cd SOMAR

# Créer les dossiers de données (si nécessaire)
mkdir -p data/logs

# Donner les permissions
chmod 755 data data/logs
```

### 3. **Configuration**

Éditer `verify.php` pour ajuster :
- Email admin
- Nom du site
- Durée des cookies
- Limite de visiteurs

```php
$CONFIG = [
    'admin_email' => 'votre@email.com',
    'site_name' => 'SOMAR Groupe',
    'enable_emails' => false, // Activer pour envoyer des emails
    'cookie_duration' => 30 * 24 * 60 * 60, // 30 jours
];
```

### 4. **Utilisation**

- **Pour les visiteurs** :
  1. Accéder à `index.php`
  2. Remplir le formulaire d'accès
  3. Accès accordé à la plateforme

- **Pour les administrateurs** :
  1. Accéder à `admin.php`
  2. Gestion des visiteurs
  3. Consultation des logs

### 5. **Déploiement**

Le site est prêt pour un déploiement sur :
- Hébergement partagé (OVH, 1&1, etc.)
- VPS (DigitalOcean, Linode, AWS EC2, etc.)
- Serveur dédié
- Docker (avec une configuration appropriée)

```bash
# Exemple avec Docker
docker build -t somar-groupe .
docker run -p 80:80 somar-groupe
```

---

## 📊 Gestion des données

### Format de stockage

Les données des visiteurs sont stockées au format JSON :

```json
{
  "visiteurs": [
    {
      "id": 1,
      "nom": "Jean Dupont",
      "email": "jean@example.com",
      "telephone": "+229XXXXXXXXXX",
      "objectif": "cours",
      "niveau": "lycee",
      "message": "Intéressé par les cours de mathématiques",
      "date": "2026-05-02 14:30:45",
      "ip": "192.168.1.1"
    }
  ]
}
```

### Logs

Les accès et événements sont loggés dans `data/logs/` pour audit et analyse.

---

## 🔒 Sécurité & Confidentialité

### Mesures de sécurité

- ✅ Validation stricte des données
- ✅ Échappement des caractères spéciaux
- ✅ Protection CSRF (si activée)
- ✅ Limitation de débit anti-spam
- ✅ Chiffrement recommandé en HTTPS
- ✅ Données hébergées en local (pas de partage tiers)

### Politique de confidentialité

Les informations des visiteurs sont :
- ✅ Utilisées uniquement pour améliorer les services
- ✅ Conservées de manière sécurisée
- ✅ Non partagées avec des tiers
- ✅ Soumises à la loi de protection des données

---

## 📞 Support & Contact

Pour toute question concernant SOMAR Groupe :

📧 **Email** : marcelalledev@gmail.com  
👤 **Fondateur** : Marcel ALLE  
📍 **Localisation** : Bénin

---

## 📝 Points importants

### À mettre à jour / À finaliser

- [ ] Intégration d'un vrai système d'emailing
- [ ] Contenu des cours à ajouter
- [ ] Épreuves à ajouter dans le dossier `epreuves/`
- [ ] Images réelles des étudiants
- [ ] Optimisation SEO complète
- [ ] Intégration Google Analytics
- [ ] Certificats SSL/HTTPS obligatoires
- [ ] Système de paiement (si cours payants)

### Améliorations futures

- [ ] Dashboard administrateur complet
- [ ] Système de notifications email
- [ ] Profil utilisateur et historique
- [ ] Progression des cours
- [ ] Système de certification
- [ ] Paiement en ligne
- [ ] Application mobile
- [ ] Système de notation et évaluations
- [ ] Chat/Support en direct
- [ ] Intégration LMS (Learning Management System)

---

## 🔄 Maintenance

### Tâches régulières

- ✅ Nettoyage des logs anciens
- ✅ Sauvegarde des données visiteurs
- ✅ Vérification des permissions fichiers
- ✅ Mise à jour des contenus
- ✅ Vérification de la sécurité

### Commandes utiles

```bash
# Voir les visiteurs enregistrés
cat data/visitors.json

# Consulter les logs
tail -f data/logs/access.log

# Vérifier les permissions
ls -la data/
```

---

## ⚖️ Licence

© 2026 **SOMAR Groupe** – Tous droits réservés.

Plateforme créée et maintenue par Marcel ALLE.

---

## 🎓 Ressources & Documentation

- **PHP Documentation** : https://www.php.net/docs.php
- **HTML5** : https://html.spec.whatwg.org/
- **CSS3** : https://www.w3.org/Style/CSS/
- **JavaScript** : https://developer.mozilla.org/en-US/docs/Web/JavaScript/

---

**Dernière mise à jour** : 2 mai 2026  
**Version** : 1.0  
**Créé avec** ❤️ **pour l'accès à une éducation de qualité**
