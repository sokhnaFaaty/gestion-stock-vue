package cores;

import java.sql.*;

public class DataBase {

    private static final String URL      = "jdbc:postgresql://localhost:5432/gestiondettes";
    private static final String USER     = "postgres";
    private static final String PASSWORD = "ubuntu";

    private static Connection connection = null;

    public static Connection getConnection() {
        if (connection == null) {
            try {
                Class.forName("org.postgresql.Driver");
                connection = DriverManager.getConnection(URL, USER, PASSWORD);
                System.out.println("Connexion PostgreSQL etablie.");
            } catch (ClassNotFoundException e) {
                System.out.println("Driver introuvable : " + e.getMessage());
            } catch (SQLException e) {
                System.out.println("Erreur connexion : " + e.getMessage());
            }
        }
        return connection;
    }

    public static void closeConnection() {
        if (connection != null) {
            try {
                connection.close();
                connection = null;
                System.out.println("Connexion fermee.");
            } catch (SQLException e) {
                System.out.println("Erreur fermeture : " + e.getMessage());
            }
        }
    }

    // ======= SELECT : pour lire les donnees =======
    public static ResultSet executeSelect(String sql, Object... params) {
        try {
            PreparedStatement stmt = getConnection().prepareStatement(sql);
            remplirParams(stmt, params);
            return stmt.executeQuery();
        } catch (SQLException e) {
            System.out.println("Erreur executeSelect : " + e.getMessage());
            return null;
        }
    }

    // ======= UPDATE : pour ajouter, modifier, supprimer =======
    public static int executeUpdate(String sql, Object... params) {
        try (PreparedStatement stmt = getConnection().prepareStatement(sql, Statement.RETURN_GENERATED_KEYS)) {
            remplirParams(stmt, params);
            stmt.executeUpdate();
            ResultSet rs = stmt.getGeneratedKeys();
            if (rs.next()) return rs.getInt(1);
        } catch (SQLException e) {
            System.out.println("Erreur executeUpdate : " + e.getMessage());
        }
        return 0;
    }

    private static void remplirParams(PreparedStatement stmt, Object... params) throws SQLException {
        for (int i = 0; i < params.length; i++) {
            Object p = params[i];
            if      (p instanceof String)  stmt.setString(i + 1, (String) p);
            else if (p instanceof Integer) stmt.setInt(i + 1, (Integer) p);
            else if (p instanceof Double)  stmt.setDouble(i + 1, (Double) p);
            else if (p instanceof Long)    stmt.setLong(i + 1, (Long) p);
            else                           stmt.setObject(i + 1, p);
        }
    }
}