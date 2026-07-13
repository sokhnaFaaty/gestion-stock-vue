package services;

import entity.Client;
import entity.Dette;
import entity.Paiement;
import repository.Clientrepository;
import repository.Detterepository;
import repository.Paiementrepository;

import java.util.ArrayList;
import java.util.List;

public class ClientService {

    // Option 1 : Ajouter un client (en base + en mémoire pour la session)
    public static boolean ajouterClient(Client nouveauClient) {
        // Règle : téléphone unique — on vérifie en base
        if (Clientrepository.getClientByTelephone(nouveauClient.getTelephone()) != null) {
            return false; // Ce numéro existe déjà
        }
        Clientrepository.ajouterClient(nouveauClient);
        return true;
    }

    // Option 2 : Récupérer tous les clients depuis la base
    public static List<Client> getTousLesClients() {
        return Clientrepository.getClients();
    }

    // Outil : chercher un client par téléphone (en base)
    public static Client rechercherParTelephone(String telephone) {
        return Clientrepository.getClientByTelephone(telephone);
    }

    // Option 3 : Ajouter une dette à un client
    public static boolean ajouterDetteAClient(String telephone, Dette nouvelleDette) {
        Client client = rechercherParTelephone(telephone);
        if (client != null) {
            Detterepository.ajouterDette(nouvelleDette, client.getId());
            return true;
        }
        return false;
    }

    // Option 4 : Lister les dettes d'un client
    public static ArrayList<Dette> getDettesClient(String telephone) {
        Client client = rechercherParTelephone(telephone);
        if (client != null) {
            return (ArrayList<Dette>) Detterepository.getDettesByClient(client.getId());
        }
        return null;
    }

    // Option 5 : Calculer le montant total dû par un client
    public static double calculerMontantTotalDu(String telephone) {
        ArrayList<Dette> dettes = getDettesClient(telephone);
        if (dettes == null) return -1.0;
        double totalDu = 0.0;
        for (Dette dette : dettes) {
            totalDu += dette.getMontantRestant();
        }
        return totalDu;
    }

    // Option 6 : Ajouter un paiement à une dette spécifique
    public static boolean ajouterPaiementADette(String telephone, int indexDette, Paiement nouveauPaiement) {
        ArrayList<Dette> dettes = getDettesClient(telephone);
        if (dettes != null && indexDette >= 0 && indexDette < dettes.size()) {
            Dette detteCible = dettes.get(indexDette);
            // Enregistrer en mémoire (pour mise à jour locale)
            detteCible.enregistrerPaiement(nouveauPaiement);
            // Persister le paiement en base
            Paiementrepository.ajouterPaiement(nouveauPaiement, detteCible.getId());
            // Mettre à jour le montant payé en base
            Detterepository.mettreAJourMontantPaye(detteCible.getId(), detteCible.getMontantPaye());
            return true;
        }
        return false;
    }

    // Option 7 : Lister les paiements d'une dette spécifique
    public static ArrayList<Paiement> getPaiementsDette(String telephone, int indexDette) {
        ArrayList<Dette> dettes = getDettesClient(telephone);
        if (dettes != null && indexDette >= 0 && indexDette < dettes.size()) {
            Dette dette = dettes.get(indexDette);
            return (ArrayList<Paiement>) Paiementrepository.getPaiementsByDette(dette.getId());
        }
        return null;
    }
}