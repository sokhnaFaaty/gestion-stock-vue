package views;

import java.util.List;
import java.util.Scanner;
import entity.Client;
import entity.Dette;
import entity.Paiement;
import services.ClientService;

public class ClientVue {
    private static Scanner scanner = new Scanner(System.in);

    // Affiche les 7 options demandées dans l'exercice
    public static void afficherMenu() {
        System.out.println("\n--- GESTION DETTES ---");
        System.out.println("1. Ajouter un client dans un Tableau");
        System.out.println("2. Afficher les Clients du Tableau");
        System.out.println("3. Ajouter une Dette à un client");
        System.out.println("4. Lister dettes d'un client");
        System.out.println("5. Lister Montant total dû par client");
        System.out.println("6. Ajouter un Paiement a une Dette");
        System.out.println("7. Lister les Paiements d'une Dette d'un client");
        System.out.println("8. Quitter");
        System.out.print("Votre choix : ");
    }

    // Option 1 : Formulaire d'ajout client
    public static void ajouterClientFormulaire() {
        System.out.println("\n--- Nouveau Client ---");
        System.out.print("Nom : ");
        String nom = scanner.nextLine();
        System.out.print("Téléphone (Unique) : ");
        String tel = scanner.nextLine();
        System.out.print("Adresse : ");
        String adresse = scanner.nextLine();

        Client c = new Client(nom, tel, adresse);
        if (ClientService.ajouterClient(c)) {
            System.out.println("Succès : Client enregistré !");
        } else {
            System.out.println("Erreur : Ce numéro de téléphone existe déjà.");
        }
    }

    // Option 2 : Affichage de la liste du tableau
    public static void afficherTousLesClients() {
        System.out.println("\n--- Liste des Clients ---");
        List<Client> clients = ClientService.getTousLesClients();
 
        if (clients.isEmpty()) {
            System.out.println("Aucun client enregistré.");
            return;
        }
        for (Client c : clients) {
            System.out.println("Nom: " + c.getNom() + " | Tél: " + c.getTelephone() + " | Adresse: " + c.getAdresse());
        }
    }


    // Option 3 : Formulaire d'ajout de dette
    public static void ajouterDetteFormulaire() {
        System.out.println("\n--- Ajouter une Dette ---");
        System.out.print("Téléphone du client : ");
        String tel = scanner.nextLine();

        System.out.print("Date de la dette (JJ/MM/AAAA) : ");
        String date = scanner.nextLine();
        System.out.print("Montant de la dette : ");
        double montant = scanner.nextDouble();
        scanner.nextLine(); // Nettoyage mémoire

        Dette d = new Dette(date, montant);
        if (ClientService.ajouterDetteAClient(tel, d)) {
            System.out.println("Succès : Dette ajoutée au client !");
        } else {
            System.out.println("Erreur : Client introuvable.");
        }
    }

        // Option 4 : Formulaire pour l'affichage des dettes d'un client
    public static void listerDettesClientFormulaire() {
        System.out.println("\n--- Dettes d'un Client ---");
        System.out.print("Téléphone du client : ");
        String tel = scanner.nextLine();

        java.util.ArrayList<Dette> dettes = ClientService.getDettesClient(tel);

        if (dettes == null) {
            System.out.println("Erreur : Client introuvable.");
        } else if (dettes.isEmpty()) {
            System.out.println("Ce client n'a aucune dette enregistrée.");
        } else {
            for (Dette d : dettes) {
                System.out.println("Date: " + d.getDate() + 
                                   " | Total: " + d.getMontantDette() + " €" +
                                   " | Restant dû: " + d.getMontantRestant() + " €");
            }
        }
    }

    // Option 5 : Formulaire pour afficher le montant cumulé dû
    public static void afficherMontantTotalDuFormulaire() {
        System.out.println("\n--- Total Dû par un Client ---");
        System.out.print("Téléphone du client : ");
        String tel = scanner.nextLine();

        double total = ClientService.calculerMontantTotalDu(tel);

        if (total == -1.0) {
            System.out.println("Erreur : Client introuvable.");
        } else {
            System.out.printf("Le montant total restant dû par ce client est de : %.2f €\n", total);
        }
    }

        // Option 6 : Formulaire pour enregistrer un paiement
    public static void ajouterPaiementFormulaire() {
        System.out.println("\n--- Enregistrer un Paiement ---");
        System.out.print("Téléphone du client : ");
        String tel = scanner.nextLine();

        java.util.ArrayList<Dette> dettes = ClientService.getDettesClient(tel);

        if (dettes == null || dettes.isEmpty()) {
            System.out.println("Erreur : Client introuvable ou aucune dette à payer.");
            return;
        }

        // 1. On affiche la liste des dettes avec un numéro pour faire un choix
        System.out.println("Sélectionnez la dette à payer :");
        for (int i = 0; i < dettes.size(); i++) {
            Dette d = dettes.get(i);
            System.out.println("[" + i + "] Date: " + d.getDate() + " | Restant dû: " + d.getMontantRestant() + " €");
        }
        
        System.out.print("Votre choix (numéro entre crochets) : ");
        int indexDette = scanner.nextInt();
        scanner.nextLine(); // Nettoyage mémoire

        // 2. On saisit les informations du paiement
        System.out.print("Date du paiement (JJ/MM/AAAA) : ");
        String datePaiement = scanner.nextLine();
        System.out.print("Montant versé : ");
        double montantVerse = scanner.nextDouble();
        scanner.nextLine(); // Nettoyage mémoire

        // 3. On crée l'objet Paiement et on l'envoie au service
        Paiement p = new Paiement(datePaiement, montantVerse);
        boolean succes = ClientService.ajouterPaiementADette(tel, indexDette, p);

        if (succes) {
            System.out.println("Succès : Le paiement a été enregistré et déduit de la dette !");
        } else {
            System.out.println("Erreur : Saisie incorrecte, opération annulée.");
        }
    }

    public static void listerPaiementsDetteFormulaire() {
        System.out.println("\n--- Historique des Paiements ---");
        System.out.print("Téléphone du client : ");
        String tel = scanner.nextLine();

        java.util.ArrayList<Dette> dettes = ClientService.getDettesClient(tel);
        if (dettes == null || dettes.isEmpty()) {
            System.out.println("Erreur : Client introuvable ou aucune dette enregistrée.");
            return;
        }

        System.out.println("Sélectionnez la dette à consulter :");
        for (int i = 0; i < dettes.size(); i++) {
            System.out.println("[" + i + "] Date: " + dettes.get(i).getDate() + " | Total: " + dettes.get(i).getMontantDette() + " €");
        }
        System.out.print("Votre choix (index) : ");
        int indexDette = scanner.nextInt(); scanner.nextLine();

        java.util.ArrayList<Paiement> paiements = ClientService.getPaiementsDette(tel, indexDette);

        if (paiements == null) {
            System.out.println("Erreur : Choix invalide.");
        } else if (paiements.isEmpty()) {
            System.out.println("Aucun versement n'a encore été effectué sur cette dette.");
        } else {
            System.out.println("\nVersements reçus :");
            for (Paiement p : paiements) {
                System.out.println("- Date: " + p.getDate() + " | Montant: " + p.getMontant() + " €");
            }
        }


}
}
