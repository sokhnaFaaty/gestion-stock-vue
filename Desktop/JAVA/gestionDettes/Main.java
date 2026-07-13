import java.util.Scanner;
import views.ClientVue;

public class Main {
    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);
        int choix = 0;

        System.out.println("=== SYSTEME DE GESTION DES DETTES CLIENTS ===");

        // Boucle continue tant que l'utilisateur ne choisit pas de quitter (Option 8)
        while (choix != 8) {
            // 1. Affichage de la liste des 7 options du document
            ClientVue.afficherMenu();

            // 2. Récupération sécurisée du chiffre tapé
            if (scanner.hasNextInt()) {
                choix = scanner.nextInt();
                scanner.nextLine(); // Nettoie le tampon de saisie
            } else {
                System.out.println("Erreur : Veuillez saisir un nombre entre 1 et 8 !");
                scanner.nextLine(); // Vide la saisie invalide
                continue;
            }

            // 3. Traitement de l'action demandée
            switch (choix) {
                case 1:
                    ClientVue.ajouterClientFormulaire();
                    break;
                case 2:
                    ClientVue.afficherTousLesClients();
                    break;
                case 3:
                    ClientVue.ajouterDetteFormulaire();
                    break;
                case 4:
                    ClientVue.listerDettesClientFormulaire();
                    break;
                case 5:
                    ClientVue.afficherMontantTotalDuFormulaire();
                    break;

                case 6:
                    ClientVue.ajouterPaiementFormulaire();
                    break;
                case 7:
                    ClientVue.listerPaiementsDetteFormulaire();
                    break;
                case 8:
                    System.out.println("Fermeture du programme. À bientôt !");
                    break;
                default:
                    System.out.println("Option incorrecte. Choisissez une option valide.");
            }
        }

        scanner.close();
    }
}
