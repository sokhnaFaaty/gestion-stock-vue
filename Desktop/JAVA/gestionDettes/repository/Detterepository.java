package repository;

import cores.DataBase;
import entity.Dette;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class Detterepository {

    public static void ajouterDette(Dette dette, int clientId) {
        int id = DataBase.executeUpdate(
            "INSERT INTO dettes (date, montant_dette, montant_paye, client_id) VALUES (?, ?, ?, ?)",
            dette.getDate(), dette.getMontantDette(), dette.getMontantPaye(), clientId
        );
        dette.setId(id);
        dette.setClientId(clientId);
    }

    public static void supprimerDette(Dette dette) {
        DataBase.executeUpdate("DELETE FROM dettes WHERE id = ?", dette.getId());
    }

    public static List<Dette> getDettes() {
        List<Dette> dettes = new ArrayList<>();
        ResultSet rs = DataBase.executeSelect("SELECT * FROM dettes");
        try {
            while (rs != null && rs.next()) {
                Dette d = new Dette(rs.getString("date"), rs.getDouble("montant_dette"));
                d.setId(rs.getInt("id"));
                d.setClientId(rs.getInt("client_id"));
                d.setMontantPaye(rs.getDouble("montant_paye"));
                dettes.add(d);
            }
        } catch (SQLException e) {
            System.out.println("Erreur getDettes : " + e.getMessage());
        }
        return dettes;
    }

    public static List<Dette> getDettesByClient(int clientId) {
        List<Dette> dettes = new ArrayList<>();
        ResultSet rs = DataBase.executeSelect(
            "SELECT * FROM dettes WHERE client_id = ?", clientId
        );
        try {
            while (rs != null && rs.next()) {
                Dette d = new Dette(rs.getString("date"), rs.getDouble("montant_dette"));
                d.setId(rs.getInt("id"));
                d.setClientId(clientId);
                d.setMontantPaye(rs.getDouble("montant_paye"));
                dettes.add(d);
            }
        } catch (SQLException e) {
            System.out.println("Erreur getDettesByClient : " + e.getMessage());
        }
        return dettes;
    }

    public static void mettreAJourMontantPaye(int detteId, double montantPaye) {
        DataBase.executeUpdate(
            "UPDATE dettes SET montant_paye = ? WHERE id = ?",
            montantPaye, detteId
        );
    }
}