package entity;

public class Paiement {
    private int id;
    private String date;
    private double montant;
    private int detteId;

    public Paiement(String date, double montant) {
        this.date = date;
        this.montant = montant;
    }

    public int getId()       { return id; }
    public String getDate()  { return date; }
    public double getMontant() { return montant; }
    public int getDetteId()  { return detteId; }

    public void setId(int id)           { this.id = id; }
    public void setDetteId(int detteId) { this.detteId = detteId; }
}