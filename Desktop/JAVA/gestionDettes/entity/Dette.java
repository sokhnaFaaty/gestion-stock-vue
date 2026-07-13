package entity;

import java.util.ArrayList;

public class Dette {
    private int id;
    private String date;
    private double montantDette;
    private double montantPaye;
    private int clientId;
    private ArrayList<Paiement> listePaiements;

    public Dette(String date, double montantDette) {
        this.date = date;
        this.montantDette = montantDette;
        this.montantPaye = 0.0;
        this.listePaiements = new ArrayList<>();
    }

    public void enregistrerPaiement(Paiement paiement) {
        this.listePaiements.add(paiement);
        this.montantPaye += paiement.getMontant();
    }

    public double getMontantRestant() {
        return this.montantDette - this.montantPaye;
    }

    public int getId()                              { return id; }
    public String getDate()                         { return date; }
    public double getMontantDette()                 { return montantDette; }
    public double getMontantPaye()                  { return montantPaye; }
    public int getClientId()                        { return clientId; }
    public ArrayList<Paiement> getListePaiements()  { return listePaiements; }

    public void setId(int id)                       { this.id = id; }
    public void setClientId(int clientId)           { this.clientId = clientId; }
    public void setMontantPaye(double montantPaye)  { this.montantPaye = montantPaye; }
}