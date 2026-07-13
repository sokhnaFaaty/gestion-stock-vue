package entity;

import java.util.ArrayList;

public class Client {
    private int id;
    private String nom;
    private String telephone;
    private String adresse;
    private ArrayList<Dette> listeDettes;

    public Client(String nom, String telephone, String adresse) {
        this.nom = nom;
        this.telephone = telephone;
        this.adresse = adresse;
        this.listeDettes = new ArrayList<>();
    }

    public void ajouterDette(Dette nouvelleDette) {
        this.listeDettes.add(nouvelleDette);
    }

    public int getId()                          { return id; }
    public String getNom()                      { return nom; }
    public String getTelephone()                { return telephone; }
    public String getAdresse()                  { return adresse; }
    public ArrayList<Dette> getListeDettes()    { return listeDettes; }

    public void setId(int id)                   { this.id = id; }
}