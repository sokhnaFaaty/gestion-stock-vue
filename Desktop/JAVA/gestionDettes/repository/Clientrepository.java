package repository;

import cores.DataBase;
import entity.Client;

import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

public class Clientrepository {

    public static void ajouterClient(Client client) {
        int id = DataBase.executeUpdate(
            "INSERT INTO clients (nom, telephone, adresse) VALUES (?, ?, ?)",
            client.getNom(), client.getTelephone(), client.getAdresse()
        );
        client.setId(id);
    }

    public static void supprimerClient(Client client) {
        DataBase.executeUpdate("DELETE FROM clients WHERE id = ?", client.getId());
    }

    public static List<Client> getClients() {
        List<Client> clients = new ArrayList<>();
        ResultSet rs = DataBase.executeSelect("SELECT * FROM clients");
        try {
            while (rs != null && rs.next()) {
                Client c = new Client(
                    rs.getString("nom"),
                    rs.getString("telephone"),
                    rs.getString("adresse")
                );
                c.setId(rs.getInt("id"));
                clients.add(c);
            }
        } catch (SQLException e) {
            System.out.println("Erreur getClients : " + e.getMessage());
        }
        return clients;
    }

    public static Client getClientByTelephone(String telephone) {
        ResultSet rs = DataBase.executeSelect(
            "SELECT * FROM clients WHERE telephone = ?", telephone
        );
        try {
            if (rs != null && rs.next()) {
                Client c = new Client(
                    rs.getString("nom"),
                    rs.getString("telephone"),
                    rs.getString("adresse")
                );
                c.setId(rs.getInt("id"));
                return c;
            }
        } catch (SQLException e) {
            System.out.println("Erreur getClientByTelephone : " + e.getMessage());
        }
        return null;
    }
}