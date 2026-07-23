/** Données partagées Type → Catégorie → Sous-catégorie (parc + stock) */
window.categoriesData = {
    'Réseau': {
        'Connectivité & Transmission': ['Switches (L2/L3) 🖧', 'Routeurs 🛣️', 'Points d\'accès Wi-Fi / Contrôleurs Wi-Fi 📶', 'Modems 🌐', 'Convertisseurs Fibre (SFP, GBIC, Media converter) 🔄'],
        'Sécurité Réseau': ['Pare-feu (Firewall) 🛡️', 'UTM / Appliances de sécurité 🛡️', 'Passerelles VPN 🔐', 'IDS/IPS 🚨'],
        'Infrastructure & Support': ['Baies et armoires réseau 🗄️', 'Panneaux de brassage 🔌', 'Câblage RJ45 / Fibre optique 🔌', 'Onduleurs (UPS) ⚡', 'PDU (Multiprises intelligentes) 🔌']
    },
    'Électronique': {
        'Vidéosurveillance (CCTV)': ['Caméras IP (fixes, PTZ, dôme) 🎥', 'NVR / DVR 📼', 'Serveurs d\'archivage vidéos 🗄️', 'Moniteurs de contrôle 🖥️'],
        'Contrôle d\'accès': ['Badges / Lecteurs RFID 🪪', 'Serrures électroniques 🔐', 'Tourniquets / Portillons 🚪', 'Unités de contrôle et software 🧠'],
        'Systèmes d\'alarme': ['Alarmes anti-intrusion 🚨', 'Détecteurs de mouvement 🕵️', 'Détecteurs d\'ouverture 🚪', 'Centrale d\'alarme 🧠']
    },
    'Informatique': {
        'Postes Utilisateurs': ['Ordinateurs de bureau', 'Ordinateurs portables', 'Écrans', 'Claviers / Souris'],
        'Périphériques': ['Imprimantes 🖨️', 'Scanners 📠', 'Onduleurs individuels 🔋', 'Projecteurs / Écrans interactifs 📽️'],
        'Serveurs & Stockage': ['Serveurs physiques (Rack / Tour) 🖥️', 'NAS / SAN 🗄️', 'Baies de stockage 🧱', 'Solutions de backup (Tape library, disque dur externe) 💾'],
        'Matériel d\'Administration & Support': ['Outils de diagnostic (IT / Réseau) 🧰', 'KVM (Keyboard Video Mouse) 🖥️', 'Barras de test (câblage) 🧪', 'Logiciels systèmes et outils métiers 💻']
    },
    'Logiciel': {
        'Logiciels': ['Système d\'exploitation', 'Antivirus', 'Bureautique', 'Sécurité', 'Utilitaire', 'Métier']
    }
};

window.updateCategoriesParc = function () {
    const data = window.categoriesData;
    const type = document.getElementById('type').value;
    const categorieSelect = document.getElementById('categorie');
    const sousCategorieSelect = document.getElementById('sous_categorie');

    categorieSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
    sousCategorieSelect.innerHTML = '<option value="">-- Sélectionner la catégorie d\'abord --</option>';
    sousCategorieSelect.disabled = true;

    if (type && data[type]) {
        Object.keys(data[type]).forEach((cat) => {
            const option = document.createElement('option');
            option.value = cat;
            option.textContent = cat;
            categorieSelect.appendChild(option);
        });
        categorieSelect.disabled = false;
    } else {
        categorieSelect.disabled = true;
    }
};

window.updateSousCategoriesParc = function () {
    const data = window.categoriesData;
    const type = document.getElementById('type').value;
    const categorie = document.getElementById('categorie').value;
    const sousCategorieSelect = document.getElementById('sous_categorie');

    sousCategorieSelect.innerHTML = '<option value="">-- Sélectionner --</option>';

    if (type && categorie && data[type] && data[type][categorie]) {
        data[type][categorie].forEach((souscat) => {
            const option = document.createElement('option');
            option.value = souscat;
            option.textContent = souscat;
            sousCategorieSelect.appendChild(option);
        });
        sousCategorieSelect.disabled = false;
    } else {
        sousCategorieSelect.disabled = true;
    }
};
