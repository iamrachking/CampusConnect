<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>CampusConnect – Interface Moderne</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('app.css') }}" />
</head>
<body>
  <div class="bg-animated"></div>

  <header class="app-header">
    <div class="brand">
      <span class="logo">⬢</span>
      <span class="title">CampusConnect</span>
    </div>
    <nav class="top-actions">
      <button id="logoutBtn" class="btn ghost" hidden>Se déconnecter</button>
    </nav>
  </header>

  <main class="container app-main">
    <section id="view-login" class="view active">
      <div class="card">
        <h1 class="card-title">Connexion</h1>
        <p class="muted">Accédez à votre espace personnalisé.</p>
        <form id="loginForm" class="form-grid">
          <label>
            <span>Email</span>
            <input type="email" name="email" placeholder="vous@exemple.com" required />
          </label>
          <label>
            <span>Mot de passe</span>
            <input type="password" name="password" placeholder="••••••••" required />
          </label>
          <button class="btn primary" type="submit">Se connecter</button>
          <div id="loginError" class="error" hidden></div>
        </form>
      </div>
    </section>

    <section id="view-admin" class="view">
      <div class="split">
        <aside class="sidebar">
          <h2>Administration</h2>
          <p class="muted">Gérez les demandes en attente.</p>
        </aside>
        <div class="content">
          <div class="card form-section">
            <h3 class="card-title">Réservations en attente</h3>
            <div id="adminPending" class="cards"></div>
          </div>
          <div class="card form-section">
            <h3 class="card-title">Ajouter une salle</h3>
            <form id="adminAddSalleForm" class="form-grid">
              <label>
                <span>Nom de la salle</span>
                <input type="text" name="nom_salle" required />
              </label>
              <label>
                <span>Capacité</span>
                <input type="number" name="capacite" min="1" placeholder="ex: 30" />
              </label>
              <label class="full">
                <span>Localisation</span>
                <input type="text" name="localisation" placeholder="Bâtiment, étage..." />
              </label>
              <button class="btn primary" type="submit">Créer la salle</button>
              <div id="adminAddSalleError" class="error" hidden></div>
            </form>
          </div>
          <div class="card form-section">
            <h3 class="card-title">Ajouter un matériel</h3>
            <form id="adminAddMaterielForm" class="form-grid">
              <label class="full">
                <span>Nom du matériel</span>
                <input type="text" name="nom_materiel" required />
              </label>
              <button class="btn primary" type="submit">Créer le matériel</button>
              <div id="adminAddMaterielError" class="error" hidden></div>
            </form>
          </div>
          <div class="card form-section">
            <h3 class="card-title">Liste des salles</h3>
            <div id="adminSallesList" class="list"></div>
          </div>
          <div class="card form-section">
            <h3 class="card-title">Liste des matériels</h3>
            <div id="adminMaterielsList" class="list"></div>
          </div>
        </div>
      </div>
    </section>

    <section id="view-teacher" class="view">
      <div class="split">
        <aside class="sidebar">
          <h2>Espace Enseignant</h2>
          <p class="muted">Vos réservations et nouvelles demandes.</p>
        </aside>
        <div class="content">
          <div class="grid-2">
            <div class="card form-section">
              <h3 class="card-title">Mes réservations</h3>
              <div id="teacherList" class="list"></div>
            </div>
            <div class="card form-section">
              <h3 class="card-title">Nouvelle demande</h3>
              <form id="teacherCreateForm" class="form-grid">
                <label>
                  <span>Type d'élément</span>
                  <select name="item_type">
                    <option value="salle">Salle</option>
                    <option value="materiel">Matériel</option>
                  </select>
                </label>
                <label>
                  <span>Élément</span>
                  <select name="item_id" id="teacherItemSelect" required>
                    <option value="" disabled selected>Choisir une salle ou un matériel</option>
                  </select>
                </label>
                <label>
                  <span>Date début</span>
                  <input type="datetime-local" name="date_debut" required />
                </label>
                <label>
                  <span>Date fin</span>
                  <input type="datetime-local" name="date_fin" required />
                </label>
                <label class="full">
                  <span>Motif (optionnel)</span>
                  <input type="text" name="motif" placeholder="Cours, examen, etc." />
                </label>
                <button class="btn primary" type="submit">Envoyer</button>
                <div id="teacherCreateError" class="error" hidden></div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="view-student" class="view">
      <div class="split">
        <aside class="sidebar">
          <h2>Espace Étudiant</h2>
          <p class="muted">Disponibilités et réservations.</p>
          <button id="studentRefreshBtn" class="btn ghost" type="button">Actualiser</button>
        </aside>
        <div class="content">
          <div class="grid-2">
            <div class="card">
              <h3 class="card-title">Salles</h3>
              <div id="studentSalles" class="list"></div>
            </div>
            <div class="card">
              <h3 class="card-title">Matériels</h3>
              <div id="studentMateriels" class="list"></div>
            </div>
            <div class="card full form-section">
              <h3 class="card-title">Demande de réservation</h3>
              <form id="studentCreateForm" class="form-grid">
                <label>
                  <span>Type d'élément</span>
                  <select name="item_type">
                    <option value="salle">Salle</option>
                    <option value="materiel">Matériel</option>
                  </select>
                </label>
                <label>
                  <span>Élément</span>
                  <select name="item_id" id="studentItemSelect" required>
                    <option value="" disabled selected>Choisir une salle ou un matériel</option>
                  </select>
                </label>
                <label>
                  <span>Date début</span>
                  <input type="datetime-local" name="date_debut" required />
                </label>
                <label>
                  <span>Date fin</span>
                  <input type="datetime-local" name="date_fin" required />
                </label>
                <label class="full">
                  <span>Motif (optionnel)</span>
                  <input type="text" name="motif" placeholder="Travail de groupe, etc." />
                </label>
                <button class="btn primary" type="submit">Envoyer</button>
                <div id="studentCreateError" class="error" hidden></div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="app-footer">
    <span>© CampusConnect</span>
  </footer>

  <script src="{{ asset('app.js') }}"></script>
</body>
</html>