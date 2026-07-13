package repository;

import cores.DataBase;
import entity.Paiement;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class Paiementrepository {

    public static void ajouterPaiement(Paiement paiement, int detteId) {
        int id = DataBase.executeUpdate(
            "INSERT INTO paiements (date, montant, dette_id) VALUES (?, ?, ?)",
            paiement.getDate(), paiement.getMontant(), detteId
        );
        paiement.setId(id);
        paiement.setDetteId(detteId);
    }

    public static void supprimerPaiement(Paiement paiement) {
        DataBase.executeUpdate("DELETE FROM paiements WHERE id = ?", paiement.getId());
    }

    public static List<Paiement> getPaiements() {
        List<Paiement> paiements = new ArrayList<>();
        ResultSet rs = DataBase.executeSelect("SELECT * FROM paiements");
        try {
            while (rs != null && rs.next()) {
                Paiement p = new Paiement(rs.getString("date"), rs.getDouble("montant"));
                p.setId(rs.getInt("id"));
                p.setDetteId(rs.getInt("dette_id"));
                paiements.add(p);
            }
        } catch (SQLException e) {
            System.out.println("Erreur getPaiements : " + e.getMessage());
        }
        return paiements;
    }

    public static List<Paiement> getPaiementsByDette(int detteId) {
        List<Paiement> paiements = new ArrayList<>();
        ResultSet rs = DataBase.executeSelect(
            "SELECT * FROM paiements WHERE dette_id = ?", detteId
        );
        try {
            while (rs != null && rs.next()) {
                Paiement p = new Paiement(rs.getString("date"), rs.getDouble("montant"));
                p.setId(rs.getInt("id"));
                p.setDetteId(detteId);
                paiements.add(p);
            }
        } catch (SQLException e) {
            System.out.println("Erreur getPaiementsByDette : " + e.getMessage());
        }
        return paiements;
    }
}