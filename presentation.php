<?php require_once __DIR__ . '/app/bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <header>
        <a href="index.php">Prezentare Proiect DAW</a>

        <div class="header-buttons">
            <?php if (is_logged_in()): ?>
            <a href="app/controller/logout.php" style="margin-left: 1rem; font-size: 1rem;">Deconectare</a>
            <?php else: ?>
            <a href="app/controller/login.php" style="margin-left: 1rem; font-size: 1rem;">Autentificare</a>
            <?php endif; ?>
        </div>
    </header>

    <section>
        <h2>Tema proiect: Activităţile unui hipermarket</h2>
        <p>Proiect realizat de: <b>Tudor Rares-Alexandru</b></p>
        <p>Grupa: 241</p>
    </section>

    <section>
        <h2>1. Descrierea arhitecturii aplicației</h2>
        <p>
            Aplicația web gestionează activitățile unui magazin online care vinde produse de papetărie. Componentele
            principale ale aplicației sunt:
            <b>Clienții</b> si <b>Produsele</b>.
        </p>

        <h3>Rolurile utilizatorilor aplicației</h3>
        <div class="role">
            <h3>Client</h3>
            <p>Clientul este utilizatorul principal al aplicației, care accesează magazinul online pentru a vizualiza
                produse, a le adăuga în coș și a plasa comenzi.</p>
            <div>
                <b>Acțiuni:</b>
                <ul>
                    <li> <b>Înregistrare cont:</b> Creează un cont nou completând numele, emailul și parola. </li>
                    <li> <b>Autentificare:</b> Se conectează în contul personal pentru a putea comanda produse. </li>
                    <li> <b>Vizualizare produse:</b> Poate vedea toate produsele disponibile, organizate pe categorii și
                        subcategorii. </li>
                    <li> <b>Căutare produse:</b> Poate filtra sau căuta articole după denumire sau categorie. </li>
                    <li> <b>Vizualizare detalii produs:</b> Accesează pagina unui produs pentru a vedea descrierea,
                        stocul și imaginea. </li>
                    <li> <b>Adăugare produse în coș:</b> Selectează articole și le adaugă într-un coș de cumpărături
                        temporar (memorat in sessionStorage). </li>
                    <li> <b>Modificare cantitate în coș:</b> Poate șterge, mări sau micșora cantitatea fiecărui produs
                        adăugat. </li>
                    <li> <b>Finalizare comandă (checkout):</b> Introduce adresa de livrare și metoda de plată pentru a
                        plasa comanda. </li>
                    <li> <b>Vizualizare comenzi anterioare:</b> Poate vedea istoricul comenzilor plasate anterior. </li>
                    <li> <b>Deconectare:</b> Închide sesiunea activă. </li>
                </ul>
            </div>
        </div>

        <div class="role">
            <h3>Administrator</h3>
            <p>Administratorul are rolul de a gestiona catalogul de produse, categoriile și comenzile din magazin.
                Are acces la un panou de administrare inaccesibil clienților obișnuiți.</p>
            <div>
                <b>Acțiuni:</b>
                <ul>
                    <li> <b>Autentificare:</b> Se conectează în panoul de administrare folosind cont de tip “admin”.
                    </li>
                    <li> <b>Vizualizare panou administrare:</b> Accesează interfața de control care afișează produsele,
                        comenzile și categoriile. </li>
                    <li> <b>Adăugare produs nou:</b> Completează formularul cu nume, descriere, preț, stoc și imagine.
                        Produsul este salvat în baza de date.</li>
                    <li> <b>Modificare produs existent:</b> Poate edita detaliile unui produs (preț, descriere, stoc).
                    </li>
                    <li> <b>Ștergere produs:</b> Elimină definitiv un produs din catalog.</li>
                    <li> <b>Adăugare categorie/subcategorie:</b> Creează noi categorii pentru organizarea produselor.
                    </li>
                    <li> <b>Ștergere categorie/subcategorie:</b> Elimină o categorie dacă nu mai conține produse active.
                    </li>
                    <li> <b>Vizualizare comenzi:</b> Poate vedea comenzile plasate de clienți, împreună cu statusul
                        fiecăreia.</li>
                    <li> <b>Actualizare status comandă:</b> Poate marca o comandă drept “paid” sau “shipped” după
                        procesare.</li>
                </ul>
            </div>
        </div>

        <h3>Structura bazei de date</h3>
        <p>Baza de date conține următoarele tabele, fiecare reprezentănd o entitate principală:</p>

        <table>
            <tr>
                <th>Tabel</th>
                <th>Descriere</th>
            </tr>
            <tr>
                <td><b>UTILIZATOR</b></td>
                <td>Datele utilizatorilor inregistrati (nume, email, rol).</td>
            </tr>
            <tr>
                <td><b>CATEGORIE</b></td>
                <td>Categoriile principale ale produselor.</td>
            </tr>
            <tr>
                <td><b>SUBCATEGORIE</b></td>
                <td>Subdiviziuni ale categoriilor, legate prin <i>category_id</i>.</td>
            </tr>
            <tr>
                <td><b>PRODUS</b></td>
                <td>Conține informațiile produselor: nume, descriere, stoc, imagine, subcategorie.</td>
            </tr>
            <tr>
                <td><b>COMANDA</b></td>
                <td>Conține comenzile și starea acestora (pending, paid, shipped).</td>
            </tr>
            <tr>
                <td><b>PRODUSE_COMANDA</b></td>
                <td>Conține detaliile fiecărui produs inclus într-o comandă.</td>
            </tr>
            <tr>
                <td><b>ADRESA</b></td>
                <td>Conține adresele salvate ale utilizatorilor.</td>
            </tr>
        </table>

        <h3>Relații între entități</h3>
        <ul>
            <li>Un <b>UTILIZATOR</b> poate avea mai multe <b>COMENZI</b> (1:N).</li>
            <li>Un <b>UTILIZATOR</b> poate salva mai multe <b>ADRESE</b> (1:N).</li>
            <li>O <b>COMANDA</b> conține mai multe <b>PRODUSE_COMANDA</b> (1:N).</li>
            <li>O <b>COMANDA</b> trebuie livrată la o anumită <b>ADRESA</b> (N:1).</li>
            <li>Un <b>PRODUS</b> aparține unei <b>SUBCATEGORII</b> (N:1).</li>
            <li>O <b>SUBCATEGORIE</b> aparține unei <b>CATEGORII</b> (N:1).</li>
            <li>Fiecare <b>PRODUSE_COMANDA</b> se referă la un singur <b>PRODUS</b> (N:1).</li>
        </ul>

        <p style="margin-top: 1rem">Tabele care reprezintă aceste entități sunt reprezentate in diagrama conceptuală a
            bazei de date din phpMyAdmin:</p>
        <img src="./assets/diagrama.png" alt="Diagrama bazei de date" class="diagram">
    </section>

    <section>
        <h2>2. Descrierea soluției de implementare</h2>

        <p>Fluxurile principale care descrie modul in care utilizatorii si administratorii interactioneaza cu magazinul
            online sunt descrise în urmatoarele diagramele UML:

        <h3>Fluxul utilizatorului (Client)</h3>
        <p>Următoarea diagramă de activitate ilustrează procesul de cumpărare al unui client si ilustrează parcursul
            complet al acestuia, de la accesarea site-ului, autentificare și navigarea prin produse, până la adăugarea
            articolelor în coș și finalizarea comenzii.</p>
        <img src="./assets/client.png" alt="Diagrama activitate client" class="diagram">

        <h3>Fluxul administratorului</h3>
        <p>Diagrama de mai jos prezintă pașii efectuați de un administrator si prezintă activitățile interne de
            gestionare ale magazinului: autentificarea în panoul de control, adăugarea și modificarea produselor,
            organizarea categoriilor și actualizarea comenzilor.</p>
        <img src="./assets/admin.png" alt="Diagrama activitate admin" class="diagram">

    </section>

</body>

</html>